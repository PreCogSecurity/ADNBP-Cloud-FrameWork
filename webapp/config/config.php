<?php

// ADNBP Methodology  - May 2014
// Keep here variables with URLs o Passwords or other critical information
// Try to keep safe this file and don't share with anyone
// Use $this->setConf("<var>",<value>) for all config vars.

// CloudServiceUrl. Default is: /api. Uncomment to point local server
// $this->setConf("CloudServiceUrl","/api");

// $this->setConf("GoogleMapsAPI",true);
// $this->setConf("GooglePublicAPICredential",getenv('GOOGLE_PUBLIC_API_CREDENTIAL')); 


$this->setConf("portalHTMLTop",'CloudFrameWorkTop.php');
$this->setConf("portalHTMLBottom",'CloudFrameWorkBottom.php');
$this->setConf("portalTitle",'ADNBP Cloud FrameWork '.date("Y"));
$this->setConf("portalDescription",'Cloud Framwork to develop Cloud Solutions.');
$this->setConf("portalNavColor",'navbar-inverse');

// VERSION
$this->setConf("CloudFrameWorkVersion",$this->_version);

// AUTH API KEYs
$this->setConf("API_KEY_APIKEYTEST",array('organization'=>'Test Organization','allowed_domains'=>array('localhost*')));

// Google Single Sign on: https://console.developers.google.com/project/apps~bloombees-web-v1/apiui/credential
// $this->setConf("GooglePublicAPICredential","{Write here your Key}"); 
// $this->setConf("GoogleServerAPICredential","{Write here your Key}"); 

// API HEADER AUTH FOR TESTING
$this->setConf('CloudServiceId','test'); // Password to manage Security Calls
$this->setConf('CloudServiceSecret','$RHThr;We*Ef'); // Password to manage Security Calls

$this->setConf('CLOUDFRAMEWORK-ID-test',array("secret"=>'$RHThr;We*Ef','data'=>'for test'));
$this->setConf('API_KEY-test',array('allowed_referers'=>array('localhost','cloudframework.adnbp.com')));

?>