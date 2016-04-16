<?php
$api->checkMethod('GET');
if(!$api->error) {
	$api->setReturnData(array('_version'=>$this->version()));
	$api->addReturnData(array('_timeZoneSystemDefault'=>$this->_timeZoneSystemDefault));
	$api->addReturnData(array('timeZone'=>$this->_timeZone));
	$api->addReturnData(array('output_format'=>$this->_format));
	
	// CLIENT AUTH				
	$api->addReturnData(array('API-CLIENT-HEADER(CloudServiceUrl)'=>$this->getConf("CloudServiceUrl")));
	$api->addReturnData(array('API-CLIENT-HEADER(CloudServiceId)'=>$this->getConf("CloudServiceId")));
	if(strlen($this->getConf("CloudServiceId")))
		$api->addReturnData(array('API-CLIENT-HEADER(CloudServiceSecret)'=>(strlen($this->getConf("CloudServiceSecret")))?'******':'missing' )); 
	
	// API-SERVER-HEADERS
	$serverHeaders = null;
	$apiKeys = null;
	foreach ($this -> _conf as $key => $value) {
		if(strpos($key, 'CLOUDFRAMEWORK-ID-')===0) {
			list($foo,$foo,$id) = explode("-",$key,3);
			$secArr = $this->getConf('CLOUDFRAMEWORK-ID-'.$id);
			$serverHeaders[$id] = (strlen($secArr['secret']))?'*****':'SECRET missing';
		}elseif(strpos($key, 'API_KEY-')===0) {
			list($foo,$id) = explode("-",$key,3);
			$secArr = $this->getConf('API_KEY-'.$id);
			$apiKeys[] = $this->getConf('API_KEY-'.$id);
		} 
	}
	$api->addReturnData(array('API-SERVER-HEADER(CLOUDFRAMEWORK-ID-*)'=>$serverHeaders));
	$api->addReturnData(array('API-SERVER-KEYS(API_KEYS-*)'=>$apiKeys));
	$api->addReturnData(array('fingerprint'=>$this->getRequestFingerPrint()));
}