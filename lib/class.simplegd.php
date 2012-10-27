<?php
/*
	PHP Simple Graphics Draw Class v0.0.999 HACKED for ASTROFOTO

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

	WARNING: THIS IS AN ALPHA VERSION! THIS MEANS THAT NOT ALL FEATURES HAVE BEEN
	IMPLEMENTED AND/OR IT WASN'T FULLY TESTED!

*/
	$simpleGD_errors=array(
		'INVALID_FILE'=>'The specified file is not a valid image!',
		'CANNOT_WRITE'=>'Can\'t save the image file to the specified dir. Check permissions!',
		'INVALID_ARGS'=>'Invalid arguments specified!',
		'INVALID_RESIZE_ARGS'=>'Invalid resize arguments specified!',
		'FILE_SIZE_EXCEEDED'=>'maximum allowed file size exceeded!',
	);

	class simpleGD {
		var $opts=null;
		var $img=null;
		var $imgsize=null;
		
		function simpleGD($opts=null) {
			$this->opts=array(
				'url_args'=>(isset($opts['url_args'])?$opts['url_args']:array(''=>'sgd', 'mode'=>'mode', 'file'=>'file', 'text'=>'text', 'args'=>'args')),
				'url_rw_resize'=>(isset($opts['url_rw_resize'])?$opts['url_rw_resize']:'?%1$s&%2$s=resize&%3$s=%5$s&%4$s=%6$s'),
				'url_rw_text'=>(isset($opts['url_rw_resize'])?$opts['url_rw_resize']:'?%1$s&%2$s=resize&%3$s=%5$s&%4$s=%6$s'),
				'resize_modes'=>true, // allowed resize modes, boolean true for all
				'output_type'=>'png',
				'cache'=>true,
			);
			if (isset($_GET[$this->opts['url_args']['']])) {
				$mode=$_GET[$this->opts['url_args']['mode']];
				switch ($mode) {
					case 'resize':
						// HACK: disk caching
						$cache_dir = SITE_ROOT.'/tmp/thumbs/';
						$cache_file = md5($_GET[$this->opts['url_args']['file']].'_'.
								$_GET[$this->opts['url_args']['args']]).'.jpg';
						
						if (file_exists($cache_dir.$cache_file) && 
								(filemtime($cache_dir.$cache_file) > 
								filemtime($_GET[$this->opts['url_args']['file']]))) {
							$this->open($cache_dir.$cache_file);
							$this->output();
							die();
						}
						
						$this->open($_GET[$this->opts['url_args']['file']]);
						$this->resize($_GET[$this->opts['url_args']['args']]);
						$this->output($cache_dir.$cache_file);
						$this->output();
						die();
						break;
					case 'text':
						$this->text($_GET[$this->opts['url_args']['text']],$_GET[$this->opts['url_args']['args']]);
						$this->output();
						die();
						break;
					case 'captcha':
						$this->captcha();
						$this->output(null,null,true);
						die();
						break;
				}
			}
		}
		
		function url($mode, $args0=null, $args1=null) {
			switch ($mode) {
				case 'resize':
					if (!is_array($args0)) $args0=array('file'=>$args0,'args'=>$args1);
					return sprintf($this->opts['url_rw_resize'], $this->opts['url_args'][''], $this->opts['url_args']['mode'], $this->opts['url_args']['file'], $this->opts['url_args']['args'], $this->_eurl($args0['file']), $this->_eurl($args0['args']));
					break;
				case 'text':
					if (!is_array($args0)) $args0=array('text'=>$args0,'resize'=>$args1);
					return sprintf($this->opts['url_rw_resize'], $this->opts['url_args'][''], $this->opts['url_args']['mode'], $this->opts['url_args']['file'], $this->opts['url_args']['args'], $this->_eurl($args0['file']), $this->_eurl($args0['args']));
					break;
				case 'captcha':
					if (!is_array($args0)) $args0=array('text'=>$args0,'resize'=>$args1);
					return sprintf($this->opts['url_rw_captcha'], $this->opts['url_args'][''], $this->opts['url_args']['mode'], $this->opts['url_args']['args'], $this->_eurl($args0['args']));
					break;
			}
			return '';
		}
		
		function open($file) {
			if (!file_exists($file)) return false;
			$size=getimagesize($file);
			$this->imgsize=array($size[0],$size[1]);
			switch ($size[2]) {
				case IMAGETYPE_PNG: $this->img=imagecreatefrompng($file);return true;
				case IMAGETYPE_JPEG: $this->img=imagecreatefromjpeg($file);return true;
				case IMAGETYPE_GIF: $this->img=imagecreatefromgif($file);return true;
			}
			$this->img=null;
			$this->imgsize=null;
			return false;
		}

		function captcha() {
			$code = $this->generateCode($characters);
			/* font size will be 75% of the image height */
			$font_size = $height * 0.75;
			$image = imagecreate($width, $height) or die('Cannot initialize new GD image stream');
			/* set the colours */
			$background_color = imagecolorallocate($image, 255, 255, 255);
			$text_color = imagecolorallocate($image, 20, 40, 100);
			$noise_color = imagecolorallocate($image, 100, 120, 180);
			/* generate random dots in background */
			for( $i=0; $i<($width*$height)/3; $i++ ) {
				imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
			}
			/* generate random lines in background */
			for( $i=0; $i<($width*$height)/200; $i++ ) {
      	   imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
			}
			/* create textbox and add text */
			$textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
			$x = ($width - $textbox[4])/2;
			$y = ($height - $textbox[5])/2;
			imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $code) or die('Error in imagettftext function');
			/* output captcha image to browser */
			header('Content-Type: image/jpeg');
			imagejpeg($image);
			imagedestroy($image);
			$_SESSION['sgd_captcha'] = $code;
		}
		
		function watermark($source) {
			$wtgd=new simpleGD();
			$wtgd->open($source);
			
		}
		
		function upload($source,$dest,$args,$watermark='') {
			/*
				Example for $args: M4MBR1024x1024, M4MB, R800x600sl
				Arguments (CASE-SENSITIVE AND IN THIS ORDER, ALL ARE OPTIONAL):
					E: check extension to be jpe?g/png/gif
					U: check if is_uploaded_file()
					M[maxsize]: maximum size, returns error if exceeded. Must have suffix B,KB,MB,GB
					R[resizeargs]: Call the resize function and pass these arguments
			*/
			$args=str_replace(' ','',$args);
			if (!file_exists($source)) {return 'INVALID_FILE';}
			if (!preg_match('/^([EU]*)(?:M([0-9]+)(B|MB|KB|GB))?(?:R(.+))?$/',$args,$match)) {return 'INVALID_ARGS';}
			if ((strpos($match[1],'E')!==false)&&(!preg_match('/\.(?:png|jpe?g|gif)$/i',$source))) {return 'INVALID_FILE';}
			if ((strpos($match[1],'U')!==false)&&(!is_uploaded_file($source))) {return 'INVALID_FILE';}
			if (!empty($match[2])) {
				$size=(int)$match[2];
				if ($match[3]=='KB') {$size*=1024;}
				if ($match[3]=='MB') {$size*=1024*1024;}
				if ($match[3]=='GB') {$size*=1024*1024*1024;}
				if (filesize($source)>$size) return 'FILE_SIZE_EXCEEDED';
			}
			if (!$this->open($source)) {return 'INVALID_FILE';}
			if (!empty($match[4])) if (!$this->resize($match[4])) {return 'INVALID_RESIZE_ARGS';}
			if (!$this->output($dest)) {return 'CANNOT_WRITE';}
			$this->destroy();
			return true;
		}
		
		function calcsize($args) {
			$args=$this->_open_args($args);
			if (!$this->img) return false;
			if (!preg_match('/^([0-9]+)x([0-9]+)([dlsc_\]\[-]*)(#[0-9a-fA-F]{6}|#[0-9a-fA-F]{3})?$/i',$args,$pargs)) {return false;}
			$cw=null;$ch=null;
			if (($pargs[1]==0)&&($pargs[2]==0)) return false;
			$or=($this->imgsize[1]==0?0:$this->imgsize[0]/$this->imgsize[1]);
			if ($or==0) return false;
			if (strpos($pargs[3],'d')!==false) { // deformation mode
				if ($pargs[1]==0) {$pargs[1]=$this->imgsize[0];}
				if ($pargs[2]==0) {$pargs[2]=$this->imgsize[1];}
				$width=$pargs[1];
				$height=$pargs[2];
			} elseif ((strpos($pargs[3],'c')!==false)&&(strpos($pargs[3],'s')!==false)) { // cut mode
				if (($pargs[1]==0)||($pargs[2]==0)) return false;
				if ($pargs[1]>$this->imgsize[0]) {
					$width=$this->imgsize[0];
					$height=$this->imgsize[1];
				} else {
					$width=$pargs[1];
					$height=$width/$or;
					if ($height<$pargs[2]) {
						$height=$pargs[2];
						$width=$pargs[2]*$or;
					}
				}
			} else {
				if ($pargs[2]==0) {
					if (strpos($pargs[3],'s')!==false) return false;
					if (($pargs[1]<$this->imgsize[0])||(strpos($pargs[3],'l')!==false)) {
						$width=$pargs[1];
						$height=$width/$or;
					} else {
						$width=$this->imgsize[0];
						$height=$this->imgsize[1];
					}
				} elseif ($pargs[1]==0) {
					if (strpos($pargs[3],'s')!==false) return false;
					if (($pargs[2]<$this->imgsize[1])||(strpos($pargs[3],'l')!==false)) {
						$height=$pargs[2];
						$width=$height*$or;
					} else {
						$height=$this->imgsize[1];
						$width=$this->imgsize[0];
					}
				} else {
					if (($pargs[1]>$this->imgsize[0]) && ($pargs[2]>$this->imgsize[1])) {
						$width=$this->imgsize[0];
						$height=$this->imgsize[1];
					} elseif ($pargs[1]>$this->imgsize[0]) {
						$height=$pargs[2];
						$width=$height*$or;
					} else {
						$width=$pargs[1];
						$height=$width/$or;
						if ($width>($pargs[2]*$or)) {
							$height=$pargs[2];
							$width=$pargs[2]*$or;
						}
					}
				}
			}
			$cw=$width=(int)$width;
			$ch=$height=(int)$height;
			if (strpos($pargs[3],'s')!==false) {
				$width=$pargs[1];
				$height=$pargs[2];
			}
			return array((int)$width, (int)$height, (int)$cw, (int)$ch);
		}
		
		function resize($args) {
			/*
				'80x80s#fff'
				$args: WxHdls_-[]#rrggbb
				Arguments (CASE-SENSITIVE):
					WxH: width and height, may be 0 for auto (required, must be first, everything else is optional)
					d: deformation, do not retain W/H proportions (lsc_-# has no effect)
					l: enlarge if smaller than the specified W/H (works with sc-_#)
					s: strict resize to W and H, fill empty space with #rgb (works with lc-_#)
					c: cut from image instead of adding background in strict mode (works with sl and -_# if image W/H smaller than the resize W/H, if l not set)
					_: vertical align bottom in strict mode
					-: vertical align middle in strict mode (works with slc#, default is top)
					[: horizontal align left
					]: horizontal align right (default is center)
					#rrggbb|#rgb: set background color (must be the last argument, defaults to #fff)
				
				THIS IS ALPHA AND NOT EVERYTHING HAS BEEN IMPLEMENTED (l not implemented)
			*/
			$args=$this->_open_args($args);
			if (!$this->img) return false;
			if (is_array($this->opts['resize_modes'])&&!in_array($args,$this->opts['resize_modes'])) return false;
			if (!preg_match('/^([0-9]+)x([0-9]+)([dlsc_\[\]-]*)(#[0-9a-fA-F]{6}|#[0-9a-fA-F]{3})?$/i',$args,$pargs)) {return false;}
			if (!($imgsize=$this->calcsize($args))) return false;
			$newimg=imagecreatetruecolor($imgsize[0],$imgsize[1]);
			$rgb=sscanf(preg_replace('/^#([a-zA-Z0-9])([a-zA-Z0-9])([a-zA-Z0-9])$/i','#\1\1\2\2\3\3',$pargs[4]),'#%2x%2x%2x');
			$bgcolor=imagecolorallocate($newimg,(int)$rgb[0],(int)$rgb[1],(int)$rgb[2]);
			imagefill($newimg,0,0,$bgcolor);
			$x=0;$y=0;
			if ($imgsize[0]!=$imgsize[2]) {
				if (strpos($pargs[3],'[')!==false) {
					$x=0;
				} elseif (strpos($pargs[3],']')!==false) {
					$x=$imgsize[0]-$imgsize[2];
				} else {
					$x=floor(($imgsize[0]-$imgsize[2])/2);
				}
			}
			if ($imgsize[1]!=$imgsize[3]) {
				if (strpos($pargs[3],'-')!==false) {
					$y=floor(($imgsize[1]-$imgsize[3])/2);
				} elseif (strpos($pargs[3],'_')!==false) {
					$y=$imgsize[1]-$imgsize[3];
				} else {
					$y=0;
				}
			}
			if (!imagecopyresampled($newimg,$this->img,$x,$y,0,0,$imgsize[2],$imgsize[3],$this->imgsize[0],$this->imgsize[1])) return false;
			imagedestroy($this->img);
			$this->img=$newimg;
			$this->imgsize=array($imgsize[0],$imgsize[1]);
			return true;
		}
		
		function text() {
			// not implemented yet
		}
		
		function output($where=null,$output_type=null,$nocache=null) {
			if (!isset($output_type)) $output_type=$this->opts['output_type'];
			if (!$this->img) return false;
			if ($this->opts['cache'] && (!$nocache)) {
				header('Expires: '.gmdate('D, d M Y H:i:s', time()+3600*24*30).'GMT');
			} elseif ($nocache) {
				header("Cache-Control: no-cache, must-revalidate");
				header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			}
			if ($where) {$output_type=preg_replace('/^.*\./','',$where);}
			switch ($output_type) {
				case 'png': 
					if (!$where) header('Content-type: image/png');
					imagepng($this->img,$where);
					break;
				case 'jpg':
				case 'jpeg':
					if (!$where) header('Content-type: image/jpeg');
					imagejpeg($this->img,$where);
					break;
				case 'gif':
					if (!$where) header('Content-type: image/gif');
					imagegif($this->img,$where);
					break;
				default:return false;
			}
			$this->reset();
			return true;
		}
		
		function destroy() {
			if (!$this->img) return false;
			imagedestroy($this->img);
			$this->img=null;
			$this->imgsize=null;
		}
		function reset() {
			$this->destroy();
		}
		
		function _open_args($args) {
			if (!preg_match('/^(.*)\?(.*)$/',$args,$match)) {
				return $args;
			};
			$this->reset();
			if ($this->open($match[1])) {return $match[2];}
			return false;
		}
		function _eurl($url) {
			return preg_replace('/%2F/i','/',rawurlencode($url));
		}
	}
?>
