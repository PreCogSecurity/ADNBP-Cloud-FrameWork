<?php
global $__p;
$api->checkMethod('GET');

if(!isset($_GET['only']) || $_GET['only']=='db')
if($this->getConf("dbName")) {
	$__p->init('test','db/CloudSQL connect');
	$this->loadClass("db/CloudSQL");
	$db = new CloudSQL();
	$db->connect();
	if(!$db->error()) $db->close();
	$notes = array($db->getError());
	/*
	$notes[] = ['dbServer'=>(strlen($this->getConf("dbServer")))?substr($this->getConf("dbServer"),0,2).'***':'None'];
	$notes[] = ['dbSocket'=>(strlen($this->getConf("dbSocket")))?'***':'None'];
	$notes[] = ['dbUser'=>(strlen($this->getConf("dbUser")))?substr($this->getConf("dbUser"),0,2).'***':'None'];
	$notes[] = ['dbPassword'=>(strlen($this->getConf("dbPassword")))?'***':'None'];
	$notes[] = ['dbName'=>(strlen($this->getConf("dbName")))?'***':'None'];
	$notes[] = ['dbPort'=>(strlen($this->getConf("dbPort")))?'***':'None'];
	*/
	$__p->end('test','db/CloudSQL connect',!$db->error(),$notes);
} else {
	$api->addReturnData(array('db/CloudSQL connect'=>'no DB configured'));
}

if(!isset($_GET['only']) || $_GET['only']=='localizr')
if(strlen($this->getConf("LocalizePath"))) {
	  $__p->init('test','LocalizePath scandir');
		$errMsg='';
	  try {
		  $ret = scandir($this->getConf("LocalizePath"));
	  } catch (Exception $e) {
		  $errMsg = 'Error reading ' . $this->getConf("LocalizePath") . ': ' . $e->getMessage() . ' ' . error_get_last();
	  }
	  $__p->end('test','LocalizePath scandir',is_array($ret),$this->getConf("LocalizePath").': '.$errMsg);
}

if(!isset($_GET['only']) || $_GET['only']=='cloud')
	if(strlen($this->getCloudServiceUrl())) {
		  $__p->init('test','Cloud Service Stream Url');
		  $ret = $this->getCloudServiceResponse('_version');

		  $retOk = !$this->error;
		  $retErr = $this->errorMsg;
		  if($retOk) {
			$ret = json_decode($ret);
			$retOk = $ret->success;
			if(!$retOk) $retErr = json_encode($ret);
		  }
		  $__p->end('test','Cloud Service Stream Url',$retOk,$this->getCloudServiceUrl('_version').' '.$retErr);
	} else {
		$api->addReturnData(array('Cloud Service Stream Url'=>'no CloudServiceUrl configured'));
	}

if(!isset($_GET['only']) || $_GET['only']=='cloud')
	if(strlen($this->getCloudServiceUrl())) {
		$__p->init('test','Cloud Service Curl Url');
		$ret = $this->getCloudServiceResponseCurl('_version');

		$retOk = !$this->error;
		$retErr = $this->errorMsg;
		if($retOk) {
			$ret = json_decode($ret);
			$retOk = $ret->success;
			if(!$retOk) $retErr = json_encode($ret);
		}
		$__p->end('test','Cloud Service Curl Url',$retOk,$this->getCloudServiceUrl('_version').' '.$retErr);
	} else {
		$api->addReturnData(array('Cloud Service Url'=>'no CloudServiceUrl configured'));
	}

if(is_file($this->_webapp.'/logic/_test.php')) include($this->_webapp.'/logic/_test.php');

if(isset($__p->data['init']))
	$api->addReturnData($__p->data['init']['test']);

