<?php
/*
	PHP ShortTags Template Engine v0.1.7

	Copyright (C) 2007 Stancu Florin <niflostancu@gmail.com>

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

	Requirements: PHP 4.3.3, read-write perms on $this->config['dir_cache']

*/

// Default settings
define('PSTT_DEFTEMPLATESDIR','templates/'); // default templates directory, make sure that the web server does not serve it's contents to the clients
define('PSTT_DEFCACHEDIR','cache/templates/'); // default cache directory, stores compiled templates (needs write access)
define('PSTT_DEFCACHEEXT','.compiled'); // default cached file extension
define('PSTT_DEFDIEONERR',true); // default "die on error?"

define('PSTT_ERR_FOPENTPL','Cannot open template file for reading!');
define('PSTT_ERR_FWRITECH','Cannot open cache file for writing!');

class PSTTemplate {
	/*** variable declarations ***/
	var $config=array();
	var $vars=array();
	var $lasterror='';

	/**
	 * Class Constructor
	 */
	function PSTTemplate() {
		$this->config['dir_templates']=PSTT_DEFTEMPLATESDIR;
		$this->config['dir_cache']=PSTT_DEFCACHEDIR;
		$this->config['cache_ext']=PSTT_DEFCACHEEXT;
		$this->config['die_on_error']=PSTT_DEFDIEONERR;
	}


	/******************************* EXPORTED FUNCTIONS *******************************/

	/**
	 * Parses&executes a template file, printing the result
	 *
	 * @param string $template Template file
	 * @param bool [$absolute] Ignore dir_templates option, $template is an absolute path
	 */
	function display($_pstt_template,$_pstt_absolute=false) {
		extract($this->vars,EXTR_SKIP);
		if ($_pstt_absolute) {$_pstt_savedpath=$this->config['dir_templates'];$this->config['dir_templates']='';}
		if (file_exists($_pstt_filename=$this->_cache_compile($_pstt_template))) include($_pstt_filename);
		if ($_pstt_absolute) {$this->config['dir_templates']=$_pstt_savedpath;}
	}
	/**
	 * Parses&executes a template file, returning the result
	 *
	 * @param string $template Template file
	 * @param bool [$absolute] Ignore dir_templates option, $template is an absolute path
	 * @return string The result of template's execution
	 */
	function getresult($template,$absolute=false) {
		ob_start();
		$this->display($template,$absolute);
		$result=ob_get_contents();
		ob_end_clean();
		return $result;
	}
	/**
	 * Sets the value of a variable
	 *
	 * @param string $varname Variable's name
	 * @param string $value New value
	 */
	function set($varname,$value) {
		$this->vars[$varname]=$value;
	}
	/**
	 * Sets $variable as reference to $reference
	 *
	 * @param string $varname Variable's name
	 * @param string $reference Variable to be referenced
	 */
	function setref($varname,&$reference) {
		$this->vars[$varname]=&$reference;
	}
	/**
	 * Gets the value of a variable
	 *
	 * @param string $varname Variable's name
	 * @return string The variable's value
	 */
	function get($varname) {
		return $this->vars[$varname];
	}


	/******************************* INTERNAL FUNCTIONS *******************************/

	/**
	 * Returns the absolute path of the template
	 *
	 * @param string $template Template filename
	 * @return string Absolute path
	 */
	function _locatetpl($templatename) {
		if (empty($this->config['dir_templates'])) {return $templatename;}
		if (is_array($this->config['dir_templates'])) {
			foreach ($this->config['dir_templates'] as $i=>$val) {
				if ($val[strlen($val)-1]!='/') {$this->config['dir_templates'][$i].='/';$val.='/';}
				if (file_exists($val.$templatename)) {return $val.$templatename;}
			}
		} else {
			if ($this->config['dir_templates'][strlen($this->config['dir_templates'])-1]!='/') {$this->config['dir_templates'].='/';}
			if (file_exists($this->config['dir_templates'].$templatename)) {return $this->config['dir_templates'].$templatename;}
		}
		return 'NOTFOUND';
	}

	/**
	 * Returns the absolute path of the cached file
	 *
	 * @param string $template Template filename
	 * @return string Absolute path of the [to be] cached file
	 */
	function _locatecache($templatename) {
		if (empty($this->config['dir_cache'])) {$this->config['dir_cache']='./';}
		elseif ($this->config['dir_cache'][strlen($this->config['dir_cache'])-1]!='/') {$this->config['dir_cache'].='/';}
		return $this->config['dir_cache'].basename($templatename).'.'.strtolower(substr(md5($this->_locatetpl($templatename)),0,20)).$this->config['cache_ext'];
	}

	/**
	 * Parses and compiles a template file, saving the result to the cache folder
	 *
	 * @param string $template Template filename
	 * @return string Cache file name
	 */
	function _cache_compile($template) {
		if (($funcres=$this->_cache_compiled($template))!==false) {return $funcres;}
		$cachefname=$this->_locatecache($template);
		$tplfname=$this->_locatetpl($template);
		if (file_exists($cachefname)) {unlink($cachefname);}
		//-- read the template file --//
		$tcontents='';
		if (function_exists('file_get_contents')) {
			$tcontents=@file_get_contents($tplfname);
			if ($tcontents===false) {$this->_lasterror(PSTT_ERR_FOPENTPL);return false;}
		} else {
			$tf=@fopen($tplfname,'r');
			if (!$tf) {$this->_lasterror(PSTT_ERR_FOPENTPL);return false;}
			$tcontents=fread($tf,filesize($tplfname));
			if ($tcontents===false) {$this->_lasterror(PSTT_ERR_FOPENTPL);return false;}
			fclose($tf);
		}
		//-- parse & compile --//
		$offset=0;
		while (($pos=strpos($tcontents,'<?',$offset))!==false) {
			$i=0;
			while (($pos2=strpos($tcontents,'?>',$i+$pos))!==false) {
				$i=$pos2+2;
				if (!$this->_tpl_allclosed(substr($tcontents,$pos,$pos2-$pos))) {break;}
			}
			if ($pos2===false) {$pos2=strlen($tcontents);$tcontents.='?>';}
			if (preg_match('/^<\?[+-]/',substr($tcontents,$pos,$pos2-$pos),$match)<1) {
				$insert=preg_replace(
					array('/^<\?(?!=)(php)?/i','/^<\?=/i'),
					array('<?php ','<?php echo '),
					substr($tcontents,$pos,$pos2-$pos));
				$tcontents=substr($tcontents,0,$pos).$insert.substr($tcontents,$pos2);
			} else {
				if (preg_match('/^<\?-([^()]+)(\(.+\))?$/',substr($tcontents,$pos,$pos2-$pos),$match_ftags)) {
					$insert='<?php $_pstt_ftagcontent=ob_get_contents();ob_end_clean();echo '.$match_ftags[1].(empty($match_ftags[2])?'($_pstt_ftagcontent); ':preg_replace('/([^a-zA-Z0-9$])OUTPUT([^a-zA-Z0-9])/','\1$_pstt_ftagcontent\2',$match_ftags[2]));
					$tcontents=substr($tcontents,0,$pos).$insert.substr($tcontents,$pos2);
				} elseif (preg_match('/^<\?+(.+)$/',substr($tcontents,$pos,$pos2-$pos),$match_ftags)) {
					$insert='<?php ob_start(); ';
					$tcontents=substr($tcontents,0,$pos).$insert.substr($tcontents,$pos2);
				}
			}
			$offset=$pos+strlen($insert)+2;
		}
		//-- write result to the cache --//
		$cf=fopen($cachefname,'w');
		if (!$cf) {$this->_lasterror(PSTT_ERR_FWRITECH);return false;}
		if (fwrite($cf,$tcontents,strlen($tcontents))===false) {$this->_lasterror(PSTT_ERR_FWRITECH);return false;}
		fclose($cf);
		touch($cachefname,filemtime($tplfname));

		return $cachefname;
	}
	/**
	 * Checks the template if it's been compiled
	 *
	 * @param string $template Template filename
	 * @return Cache file name / false
	 */
	function _cache_compiled($template) {
		$cachefname=$this->_locatecache($template);
		$tplfname=$this->_locatetpl($template);
		if (!file_exists($cachefname)) {return false;}
		clearstatcache();
		if (filemtime($tplfname)!=filemtime($cachefname)) {return false;}
		return $cachefname;
	}
	/**
	 * Sets/displays (based on class config) the last error
	 *
	 * @param string $errormsg Error text
	 */
	function _lasterror($errormsg) {
		$this->lasterror=$errormsg;
		if ($this->config['die_on_error']) {die($errormsg);}
	}

	/**
	 * Verifies if there are any unclosed quotes/comments in the string
	 *
	 * @param string $text Input text
	 * @return bool True/false
	 */
	function _tpl_allclosed($text) {
		$quotetype=-1; // 0-simple;1-double;2-blockcomm;3-inlinecomm;4-HEREDOC
		$quotename='';
		$text=' '.$text.' ';
		for ($i=0;$i<(strlen($text)-1);$i++) {
			//if (($quotetype<0)&&($text[$i]=='/')&&($text[$i+1]=='/')) {$quotetype=3;}
			if (($quotetype<0)&&($text[$i]=='/')&&($text[$i+1]=='*')) {$quotetype=2;}
			elseif (($quotetype<0)&&($text[$i]=='\'')) {$quotetype=0;}
			elseif (($quotetype<0)&&($text[$i]=='"')) {$quotetype=1;}
			elseif (($quotetype<0)&&(preg_match('/^<<<([a-zA-Z0-9_]+?)[\n\r]/',$text,$rgmatch,null,$i))) {$quotetype=4;$quotename=$rgmatch[1];}
			elseif (($quotetype==0)&&($text[$i]=='\'')&&($text[$i-1]!="\\")) {$quotetype=-1;}
			elseif (($quotetype==1)&&($text[$i]=='"')&&($text[$i-1]!="\\")) {$quotetype=-1;}
			elseif (($quotetype==2)&&($text[$i]=='*')&&($text[$i+1]=="/")) {$quotetype=-1;}
			elseif (($quotetype==4)&&(preg_match('/^'.preg_quote($quotename).'/i',$text,$rgmatch,null,$i))) {$quotetype=-1;}
			//elseif (($quotetype==3)&&(($text[$i]=="\n")||($text[$i]=="\r"))) {$quotetype=-1;}
		}
		return ($quotetype>-1);
	}
}
?>
