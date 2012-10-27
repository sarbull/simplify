<?php
/*
	PHP Simple Database Class v0.3.1 [mysql]

	Copyright (C) 2008 Stancu Florin <niflostancu@gmail.com>

	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.

	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public
	License along with this library; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301 USA

*/
	class simpleDB_mysql {
		var $link=null;
		var $err_display=true;
		var $err_backtrace=true;
		var $err_die=true;
		var $opts=null;
		
		function simpleDB_mysql($host=null,$user=null,$pass=null,$db=null,$persistent=null) {
			if (isset($host)) {$this->connect($host,$user,$pass,$db,$persistent);}
		}
		
		function setopts($host,$user=null,$pass=null,$db=null,$persistent=null) {
			if (!is_array($host)) {
				$opts=array('host'=>$host,'user'=>$user,'pass'=>$pass,'db'=>$db,'persistent'=>$persistent);
			} else {$opts=$host;}
			$this->opts=array(
				'host'=>(string)$opts['host'],
				'user'=>(string)$opts['user'],
				'pass'=>(string)$opts['pass'],
				'db'=>(string)$opts['db'],
				'persistent'=>(isset($opts['persistent'])?(bool)$opts['persistent']:false),
				'charset'=>(isset($opts['charset'])?(string)$opts['charset']:''),
				'newlink'=>(isset($opts['newlink'])?(string)$opts['newlink']:true),
				'lazy'=>(isset($opts['lazy'])?(string)$opts['lazy']:true),
				'fetch_mode'=>(isset($opts['fetch_mode'])?(int)$opts['fetch_mode']:MYSQL_BOTH),
			);
		}
		
		function connect($host=null,$user=null,$pass=null,$db=null,$persistent=null) {
			if ((!isset($host))&&(!isset($this->opts))) {return false;}
			elseif (isset($host)) {$this->setopts($host,$user,$pass,$db,$persistent);}
			$this->link=null;
			if (($this->opts['lazy'])&&(isset($host))) {return -1;}
			
			if ($this->opts['persistent']) {$this->link=mysql_pconnect($this->opts['host'],$this->opts['user'],$this->opts['pass']);}
			else {$this->link=mysql_connect($this->opts['host'],$this->opts['user'],$this->opts['pass'],$this->opts['newlink']);}
			if (!$this->link)
				return $this->_error('<b>mysql_'.($this->opts['persistent']?'p':'').'connect</b>: '.$this->error());
			
			if (!empty($this->opts['charset'])) {$this->set_charset($this->opts['charset']);}
			if (!empty($this->opts['db'])) {$this->select_db($this->opts['db']);}
		}
		
		function set_charset($charset) {
			$this->opts['charset']=$charset;
			if (!$this->link) {$this->connect();return -1;}
			if (function_exists('mysql_set_charset')) {
				return @mysql_set_charset($charset,$this->link) or $this->error('<b>mysql_set_charset</b>: '.$this->error());
			} else {
				return $this->query('SET NAMES '.$charset);
			}
		}

		function select_db($db) {
			$this->opts['db']=$db;
			if (!$this->link) {$this->connect();return -1;}
			if (!(@mysql_select_db($db,$this->link))) {
				return $this->_error('<b>mysql_select_db</b>: '.$this->error());
			}
			return true;
		}

		function query($query) {
			if (!$this->link) $this->connect();
			$qres=@mysql_query($query,$this->link);
			if (!$qres) {
				return $this->_error('<b>mysql_query</b>: '.$this->error().' (query: <i>'.htmlspecialchars($query).'</i>)');
			}
			return $qres;
		}
		function sql($query) {
			return $this->query($query);
		}
		
		function escape($str) {
			if (!$this->link) $this->connect();
			if ($this->link) return mysql_real_escape_string($str,$this->link);
			return mysql_escape_string($str);
		}

		//Custom queries:
		function qupdate($table,$values,$where,$params='',$endparams='') {
			if (empty($values)||empty($table)) return false;
			$qvalues='';
			if (is_string($values)) {
				$qvalues=$values;
			} else foreach ($values as $k=>$v) {
				$qvalues.=(!empty($qvalues)?', ':'').'`'.$k.'`=\''.(is_array($v)?$v[0]:$this->escape($v)).'\'';
			}
			$qres=$this->query("UPDATE $params `$table` SET $qvalues ".(!empty($where)?" WHERE ".(is_array($where)?implode(' AND ',$where):$where).' ':'').$endparams);
			return $qres;
		}
		function qinsert($table,$values,$params='') {
			if (empty($values)||empty($table)) return false;
			$arrvalues=array();
			foreach ($values as $k=>$v) {
				if (is_array($v)) {$arrvalues[$k]=$v[0];}
				else {$arrvalues[$k]=$this->escape($v);}
			}
			$qres=$this->query("INSERT $params INTO `$table` (`".implode('`, `',array_keys($arrvalues))."`) VALUES ('".implode('\', \'',$arrvalues)."')");
			if (!$qres) return false;
			return $qres;
		}
		function qdelete($table,$where,$params='') {
			return $this->query("DELETE $params FROM `$table`".(empty($where)?'':" WHERE ".(is_array($where)?implode(' AND ',$where):$where)));
		}

		function fetch_array($qres,$type=null) {
			$free=false;
			if (is_string($qres)) {$qres=$this->query($qres);$free=true;}
			if (!$qres) {
				return $this->_error('<b>mysql_fetch_array</b>: Invalid Resource (failed query?)');
			}
			$row=mysql_fetch_array($qres,(isset($type)?$type:$this->opts['fetch_mode']));
			if ($free) $this->free($qres);
			return $row;
		}
		function fetch($qres,$type=null) {return $this->fetch_array($qres,$type);}
		function fetch_one($qres) {
			$farr=$this->fetch_array($qres,MYSQL_NUM);
			if (!$farr) return false;
			return reset($farr);
		}
		function fetch_all($qres,$type=null) {
			if (is_string($qres)) {$qres=$this->query($qres);}
			$arr=array();
			while ($farr=$this->fetch_array($qres,$type)) {$arr[]=$farr;}
			$this->free($qres);
			return $arr;
		}

		function num_rows($qres) {
			return mysql_num_rows($qres);
		}
		function affected_rows() {
			return mysql_affected_rows($this->link);
		}
		function insert_id() {
			return (int)mysql_insert_id($this->link);
		}
		
		function free($qres) {
			return @mysql_free_result($qres) or $this->error('<b>mysql_free_result</b>: Invalid resource!');
		}
		function close() {
			if ($this->link) return mysql_close($this->link);
			return false;
		}
		
		function error() {
			if ($this->link) return mysql_error($this->link);
			return mysql_error();
		}
		
		// class internals
		function _error($errorstr) {
			if ($this->err_display) echo $errorstr."<br />\r\n";
			if ($this->err_backtrace) {
				$trace=debug_backtrace();
				$i=0;
				while ($trace[$i]['file']==__FILE__) $i++;
				$trace=$trace[$i];
				if (is_array($trace['args'])) {$trace['args']=implode(', ',$trace['args']);}
				if ($trace) {echo "{$trace['class']}{$trace['type']}{$trace['function']}() called at [{$trace['file']}:{$trace['line']}]<br />\r\n";}
			}
			if ($this->err_die) {die();}
			return false;
		}
	}
?>
