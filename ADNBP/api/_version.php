<?php
$api->checkMethod('GET');
if(!$api->error) {
	$api->setReturnData(array('_version'=>$this->version()));
	$api->addReturnData(array('output_format'=>$this->_format));

	if(isset($_GET['date'])) {
		$api->addReturnData(array('_timeZoneSystemDefault'=>$this->_timeZoneSystemDefault));
		$api->addReturnData(array('timeZone'=>$this->_timeZone));
		
	}
	
	// CLIENT AUTH	
	if(isset($_GET['security'])) {
		$data['CloudServiceClientParams']['conf var: CloudServiceUrl']=$this->getConf("CloudServiceUrl");
		$data['CloudServiceClientParams']['conf var: CloudServiceId']=$this->getConf("CloudServiceId");
		if(strlen($this->getConf("CloudServiceId")))
			$data['CloudServiceClientParams']['conf var: CloudServiceSecret'] =(strlen($this->getConf("CloudServiceSecret")))?'******':'missing' ;
		
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
		$data['CloudServiceServerIds'] = $serverHeaders;
		$data['API-SERVER-KEYS'] = $apiKeys;
		$api->addReturnData($data);
		
	}


	if(isset($_GET['fingerprint']))
		$api->addReturnData(array('fingerprint'=>$this->getRequestFingerPrint()));

	if(isset($_GET['vars']))
		$api->addReturnData(array('vars'=>$_SERVER));

}