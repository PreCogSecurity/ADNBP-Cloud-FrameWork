<?php
/* @var $this ADNBP */

// Task to use in background
use google\appengine\api\taskqueue\PushTask;

$_url = str_replace('queue/', '', urldecode($this -> _url));
unset($api->formParams['_raw_input_']);

$api->formParams['cloudframework_queued'] = true;
$api->formParams['cloudframework_queued_id'] = uniqid('queue',true);
$api->formParams['cloudframework_queued_ip'] = $this->_ip;
$api->formParams['cloudframework_queued_fingerprint'] = json_encode($this->getRequestFingerPrint(),JSON_PRETTY_PRINT);

$headers = $this->getHeaders();

$value['url_queued'] =$_url;
$value['method'] =$api->method;
$value['data_sent'] = $api->formParams;

// CALL URL and wait until the response is received
if(isset($api->formParams['interative'])) {
	// Requires to create a complete URL
	$_url = (($_SERVER['HTTPS']=='off')?'http':'https').'://'.$_SERVER['HTTP_HOST'].$_url;
	$value['url_queued'] =$_url;
	$value['interative'] = true;
	$value['headers'] = $this->getHeaders();
	$value['data_received'] = $this->getCloudServiceResponse($_url,$api->formParams,$api->method,$this->getHeaders());
	if($value['data_received']===false) $value['data_received'] = $this->errorMsg;
	else $value['data_received'] = json_decode($value['data_received']);
} 
// RUN THE TASK
else {
	$options = array('method'=>$api->method);
	foreach ($headers as $key => $value2) if(strpos($key, 'CONTENT_')===false) {
		$options['header'] .= $key.': '.$value2. "\r\n";
	}
	$value['options'] = $options;
	$task = new PushTask($_url, $api->formParams,$options);
	$task_name = $task->add();	
}

$api->addReturnData($value);