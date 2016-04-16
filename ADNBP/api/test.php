<?php
// Automatically you have $api accesible as an object of ADBP/class/api/RESTful.php
// This object is created by ADNBP/logic/api.php

// allowed methods to receive GET,POST etc..
$api->checkMethod('GET,POST'); 				

// Activating Auth mode with a namespace specific for API_KEY
if(!$api->error) 
   $this->activeAuth(strlen($_REQUEST['API_KEY'])?'API_KEY':''); 	

// Detecting extra security
if(!$api->error && strlen($this->getHeader('X-CLOUDFRAMEWORK-SECURITY'))) 
	if(!$this->checkCloudFrameWorkSecurity(3600,'test'))  $api->setError($this->getLog(),401);
 

 // Auth privileges for api/test/checkauth
if(!$api->error && $api->params[0]=='checkauth') {
	if(strlen($this->getHeader('X-CLOUDFRAMEWORK-TOKEN')) || strlen($_REQUEST['API_KEY'])) 
		$this->authToken('check');
	if(!$this->isAuth())  $api->setError($this->getLog(),401);
}

// Checking POST api/test/..
if(!$api->error && $api->method =='POST' ) switch ($api->params[0]) {
	case 'auth':
		$api->checkMandatoryFormParam(array('id','user','password','clientfingerprint'));
		break;
	default:
		$api->setError('POST only admits: test/auth');
		break;
}

// API LOGIC
if(!$api->error) {
	// Adding Return data
		$api->setReturnData(array('method'=>$api->method)); 
		if(strlen($this->getHeader('X-CLOUDFRAMEWORK-SECURITY')))
			$api->setReturnData(array('X-CLOUDFRAMEWORK-SECURITY'=>$this->getHeader('X-CLOUDFRAMEWORK-SECURITY'))); 

		switch ($api->method) {
		case 'GET': 
			switch ($api->params[0]) {
				case 'checkauth': // GET api/test/checkauth
					if($this->isAuth())
						$api->addReturnData(array('userData'=>$this->getAuthUserData()));
					break;					

				case 'source': // GET api/test/source
					echo file_get_contents(__FILE__);
					die();
					break;					
				
				default:
					if(strlen($api->params[0])) $this->setError('unknown call');
					break;
			}
			break;
		case 'POST':
			switch ($api->params[0]) {
				case 'auth': // POST api/test/auth
					$this->setAuth(false);
					if(!strlen($api->formParams['user']) || !strlen($api->formParams['password']) || !strlen($api->formParams['id'])) {
						$api->setError('User not found. id,user and password can no be empty.',404);
					} elseif($this->authToken('generate',array($api->formParams['id'],$api->formParams['clientfingerprint']))) {
						$api->addReturnData(array('userAuthData'=>$this->getAuthUserData()));
					} else {
						$api->setError($this->getLog(),401);
					}
					break;					
			}
			break;	
	}
	$api->addReturnData(array('auth'=>$this->isAuth()));
	if($this->isAuth()) {
		$api->addReturnData(array('NEW-CLOUDFRAMEWORK-SECURITY'=>$this->generateCloudFrameWorkSecurityString('test')));
		$api->addReturnData(array('seconds_to_expire'=>3600));
	}
}

// Method to return the data
$api->setReturnFormat('JSON');
