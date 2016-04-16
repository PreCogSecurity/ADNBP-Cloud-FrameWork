<?php
if( isset($_POST['CloudFrameWorkWebApp'])
and isset($_POST['CloudFrameWorkConfigPassword'])) {
	$template = file_get_contents(__DIR__.'/../../templates/configuration/adnbp_framework_config.php');
	
	 $_POST['CloudFrameWorkWebApp'] = trim($_POST['CloudFrameWorkWebApp']);
	 
	// Encrypt Password
	if($this->getConf('CloudFrameWorkConfigPassword')!=$_POST['CloudFrameWorkConfigPassword'])
	 	$_POST['CloudFrameWorkConfigPassword'] = $this->crypt($_POST['CloudFrameWorkConfigPassword']);
	
	$template = str_replace('{CloudFrameWorkWebApp}', $_POST['CloudFrameWorkWebApp'], $template);
	$template = str_replace('{CloudFrameWorkConfigPassword}', $_POST['CloudFrameWorkConfigPassword'], $template);
	
	$towrite = '';
	if(is_file($this->_rootpath.'/adnbp_framework_config.php')) {
		$tmp = file($this->_rootpath.'/adnbp_framework_config.php');
		$endtemplate = false;
		foreach ($tmp as $key => $value) {
			if($endtemplate) $template.="\n".$value;
			if(strpos($value, 'END-TEMPLATE')) $endtemplate=true;
		}
	}
	
	if(file_put_contents($this->_rootpath.'/adnbp_framework_config.php', $template))  {
		$dir = $this->_rootpath.'/'. $_POST['CloudFrameWorkWebApp'].'_webapp';
		if(!is_dir($dir)) mkdir($dir);
		if(is_dir($dir)) {
			if(!is_dir($dir.'/logic')) mkdir($dir.'/logic');
			if(!is_dir($dir.'/templates')) mkdir($dir.'/templates');
			if(!is_dir($dir.'/config')) mkdir($dir.'/config');
		}
		$this->urlRedirect($this->_url.'?saveadnbpframeworkfile=ok');
	}
}