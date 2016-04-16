<?php
// adnbp_framework_config.php
// Basic file for portal configuration.
// Please be carefull with this file because it affects to the whole site.

$this->setConf('CloudFrameWorkWebApp', '{CloudFrameWorkWebApp}');
$this->setConf('CloudFrameWorkConfigPassword', '{CloudFrameWorkConfigPassword}'); 

// Assign webapp
if(strlen($this->getConf('CloudFrameWorkWebApp'))) $this->setWebApp('/'.$this->getConf('CloudFrameWorkWebApp').'_'.webapp);

/* // You can use more advanced rewritting allowing support for several apps depending of the domain
 
	if (strpos($_SERVER['HTTP_HOST'],'domain.com') !== false ) {
		$this->setConf('CloudFrameWorkWebApp', 'other_webapp');
	} 
 
*/
// END-TEMPLATE. Write your personal code below this line. Do not delete or modify this line
