<?php
/**
 * Common PHP functions.
 * TODO: document them
 */

// ------------------------------------------------------- //
// ------------- General purpose functions --------------- //
// ------------------------------------------------------- //
function getrandmd5($salt='37bcv%))(bvcb7456%$)^t342fv7;e4t\'\'4') {
	return md5(mt_rand(1,4523).$salt.log(mt_rand(1,184953)+mt_rand(8,51681)).(mt_rand(1,46873)+9657).'r6b#rwy\';'.mt_rand(1,100000).time().$salt);
}

function redirect($url='') {
	global $config;
	if (isset($_GET['ajax'])) {
		if (preg_match('/\?[^?]*$/',$url)) {$url.='&ajax';}
		else {$url.='?ajax';}
	}
	header('Location: '.(preg_match('/^[a-zA-Z]:\/\//',$url)?'':WEBROOT).$url);die();
}

function ehtml($str) {
	return htmlentities($str,ENT_COMPAT,'UTF-8');
}

function eurl($str) {
	return rawurlencode($str);
}
	
function ejs1($str) { // escape JavaScript single-quotes
	return addcslashes($str,'\'');
}

function ip2ulong($ip) {
	return sprintf("%u",ip2long($ip));
}

function mysqldate($timestamp = null) {
	return date('Y-m-d h:i:s', $timestamp);
}

/**
 * Normalizes an array, transforming its numeric values to associative ones.
 * @param  array $array Source array.
 * @param  array $values Where to extract values for numeric keys.
 * @return array Resulting normalized array.
 */
function normalize_array($array, $values = null) {
	$result = array();
	foreach ($array as $k=>$v) {
		if (is_numeric($k)) {
			if (isset($values)) 
				$result[$v] = $values[$v];
			else $result[$v] = $v;
		} else $result[$k] = $v;
	}
	return $result;
}

/**
 * Converts a given time field from string to an associative array containing its components.
 * The components will be in the 24 hour format.
 * Formats accepted: HH:MM, HH:MM:SS, HH:MM AM/PM
 * HH can be either one digit or two, MM must be 2 digits.
 * 
 * @param  string $timeStr The string to parse.
 * @return array|null The resulting time components (h, m and s).
 */
function parseTime($timeStr) {
	if (preg_match('/^([0-9]{1,2})\:([0-9]{2})(?:\:([0-9]{2}))?( ?am|pm)?$/i', $timeStr, $match)) {
		$time = array(
				'h' => (int)$match[1],
				'm' => (int)$match[2],
			);
		if (!empty($match[4])) {
			if (strtolower($match[4])=='pm')
				$time['h'] += 12;
		}
		if (!empty($match[3])) 
			$time['s'] = (int)$match[3];
		return $time;
		
	} elseif (preg_match('/^([0-9]{2})([0-9]{2})$/', $timeStr, $match)) {
		$time = array(
			'h' => (int)$match[1],
			'm' => (int)$match[2],
		);
		return $time;
		
	} else return null;
}

// ------------------------------------------------------- //
// ------------------- Mail functions -------------------- //
// ------------------------------------------------------- //
function site_mail($to, $subject, $contents, $attachments = array()) {
	global $config;
	/* mail($to, $subject, $contents,
		"From: ".$config['MAIL_FROM']."\n".
		"Reply-to: ".$config['MAIL_REPLYTO']."\n".
		"Content-type: text/html; charset=utf-8\n"
	); */
	
	if (isset($config['MAIL_SMTP'])) {
		$smtp_port = 25;
		$smtp_type = null;
		if (isset($config['MAIL_SMTP_TYPE']))
			$smtp_type = $config['MAIL_SMTP_TYPE'];
		if (isset($config['MAIL_SMTP_PORT']))
			$smtp_port = $config['MAIL_SMTP_PORT'];
		
		$transport = Swift_SmtpTransport::newInstance()
			->setHost($config['MAIL_SMTP']);
		$transport->setPort($smtp_port);
		if ($smtp_type) 
			$transport->setEncryption($smtp_type);
		$transport
			->setUsername($config['MAIL_SMTP_USER'])
			->setPassword($config['MAIL_SMTP_PASS']);
	} else {
		// default to mail()
		$transport = Swift_MailTransport::newInstance();
	}
	$mailer = Swift_Mailer::newInstance($transport);
	$message = Swift_Message::newInstance()
		->setSubject($subject)
		->setFrom($config['MAIL_FROM'])
		->setTo($to)
		->setBody($contents, 'text/html')
		;
	if (!empty($attachments))
		foreach ($attachments as $attachment) {
			$message->attach($attachment);
		}
	$sent = $mailer->send($message);
	return $sent;
}

function site_mail_tpl($to, $tplfile, $vars, $retmsg=false) {
	global $tpl_name, $config;
	
	$mailtpl = new PSTTemplate();
	$mailtpl->config['dir_templates'] = SITE_ROOT.'/tpl/emails/';
	$mailtpl->config['dir_cache'] = SITE_ROOT.'/tmp/';
	
	$mailtpl->setref('config',$config);
	foreach ($vars as $vname=>$vval) {$mailtpl->set($vname,$vval);}
	
	$message = $mailtpl->getresult($tplfile);
	$subject = $mailtpl->get('subject');
	$attachments = $mailtpl->get('attachments');
	
	if ($retmsg) {return $message;}
	return site_mail($to, $subject, $message, $attachments);
}

// ------------------------------------------------------- //
// ----------------- Template functions ------------------ //
// ------------------------------------------------------- //
function print_srcpages($numrows,$srchpage,$numperpage,$actionpageurl,$onclick='') {
	$srchpstr='';
	$pagini_afisate=10;
	if (!preg_match('/__I__/',$actionpageurl)) {$actionpageurl.='__I__';}
	$maxpages=floor($numrows/$numperpage);
	if (($numrows%$numperpage)!=0) {$maxpages++;}
	$sepstart=1;$sepend=$maxpages;
	if ($srchpage>1) {$srchpstr.=' <a href="'.preg_replace('/__I__/',$srchpage-1,$actionpageurl).'"'.(!empty($onclick)?' onclick="'.preg_replace('/__I__/',$srchpage-1,$onclick).'"':'').' class="text">&laquo;</a> ';}
	if ($maxpages>$pagini_afisate) {
		$sepstart=$srchpage-floor(($pagini_afisate-1)/2);if ($sepstart<1) {$sepstart=1;}
		if ($sepstart>1) {$srchpstr.='<span>...</span>';}
		$sepend=$srchpage+floor(($pagini_afisate-1)/2);
		if ($sepend>$maxpages) {$sepend=$maxpages;}
	}
	for ($i=$sepstart;$i<=$sepend;$i++) {
		$srchpstr.=' <a href="'.preg_replace('/__I__/',$i,$actionpageurl).'"'.(!empty($onclick)?' onclick="'.preg_replace('/__I__/',$i,$onclick).'"':'').($srchpage==$i?' class="selectedpage"':'').'>'.$i.'</a>';
	}
	if (($maxpages>$pagini_afisate)&&($sepend<$maxpages)) {$srchpstr.=' <span>...</span>';}
	if ($srchpage<$maxpages) {$srchpstr.=' <a href="'.preg_replace('/__I__/',$srchpage+1,$actionpageurl).'"'.(!empty($onclick)?' onclick="'.preg_replace('/__I__/',$srchpage+1,$onclick).'"':'').' class="text">&raquo;</a> ';}
	return $srchpstr;
}
function pag_calclimit($page,$rpp) {
	return (((int)$page-1)*$rpp).','.$rpp;
}

function print_selectoptions($options,$selectedid=0,$keyasval=false) {
	$retstr='';
	foreach ($options as $key=>$value) {
		if ($keyasval) $key=$value;
		$retstr.='<option value="'.ehtml($key).'"'.($key==$selectedid?' selected="selected"':'').'>'.ehtml($value).'</option>';
	}
	return $retstr;
}
