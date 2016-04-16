<?php
$__p = Performance::getInstance();
$__p->init('include_logic',$this->_url);
$this->loadClass("api/RESTful");
$api = new RESTful();
if(!strlen($api->service)) {
    $this->setConf("notemplate",false);
    include_once $this->_rootpath."/ADNBP/logic/apiDoc.php";
    if(is_file($this->_webapp."/logic/api/apiDoc.php"))  include_once $this->_webapp."/logic/api/apiDoc.php";
} elseif(!$api->error) {

	// This allows to create your own services in each WebServer
    $__includePath ='';
    
    // If ApiPath is defined, normally pointing into a bucket..
    if(strlen($this->getConf("ApiPath"))) 
        if(is_file($this->getConf("ApiPath").'/'.$api->service.".php"))
            $__includePath = $this->getConf("ApiPath").'/'.$api->service.".php";
        elseif(is_file($this->_rootpath."/ADNBP/api/".$api->service.".php"))
            $__includePath = $this->_rootpath."/ADNBP/api/".$api->service.".php";
    
    //  If there is no path found lets try under logic/api
    if($__includePath=='')
       if(is_file($this->_webapp."/api/".$api->service.".php"))
            $__includePath = $this->_webapp."/api/".$api->service.".php";
	   elseif(is_file($this->_webapp."/logic/api/".$api->service.".php"))
            $__includePath = $this->_webapp."/logic/api/".$api->service.".php";
       elseif(is_file($this->_rootpath."/ADNBP/api/".$api->service.".php"))
            $__includePath =  $this->_rootpath."/ADNBP/api/".$api->service.".php";
    
    //Now include the file or show the error
    if(strlen($__includePath)) {
    	$__p->init('include_logic',$__includePath);
        include_once $__includePath;
    	$__p->end('include_logic',$__includePath);
    } else {
    	if(strlen($this->getConf("ApiPath")))
    		$api->setError('Unknow file '.$api->service.' in bucket '.$this->getConf("ApiPath"),404);
		else 
			$api->setError('Unknow file '.$api->service.' in '.$this->_url,404);
		
    }

    // Compatibility until migration to $api
    $ret = array();
    $ret['success'] = ($api->error)?false:true;
    $ret['status'] = $api->getReturnCode();
    $ret['url']=(($_SERVER['HTTPS']=='on')?'https://':'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $ret['method'] = $api->method;
    if(strlen($__includePath)) $ret['time']=$__p->data['init']['include_logic'][$__includePath]['time'];

	// Debug params
	if(isset($api->formParams['debug'])) {
		$ret['header'] = $api->getResponseHeader();
		$ret['session'] = session_id();
		$ret['ip']=$this->_ip;
        $ret['user_agent']=($this->userAgent!=null)?$this->userAgent:$api->requestHeaders['User-Agent'];
		$ret['urlParams']=$api->params;
		$ret['form-raw Params']=$api->formParams;
	}
	
    if($api->error) {
            $ret['error']['message']=$api->errorMsg;
    }

	// If I have been called from a queue the response has to be 200 to avoid…
	if(isset($api->formParams['cloudframework_queued'])) {
		if($api->error) {
			$ret['queued_return_code'] = $api->error;
			$api->error = 0;
			$api->ok = 200;
		}
	}


	// Send Logs APILog
	if($api->service != 'logs' && strlen($this->getConf("ApiLogsURL"))) {
		if(isset($_REQUEST['addLog']) && !isset($_REQUEST['test'])) {
			// $logParams['test_mode'] = 'on';
			$logParams['title'] = 'API '.$this->_url;
			$logParams['text'] = json_encode($ret);
			$urlLog = $this->getConf("ApiLogsURL").'/Logs/';
			$urlLog .= ($api->error)?'Error':'Success';
			
			$retLog = json_decode($this->getCloudServiceResponse($urlLog,$logParams));
			
			if(is_object($retLog) && isset($retLog->success) && $retLog->success) $ret['log_saved'] = true;
			else {
				$ret['log_saved'] = false;
				$ret['log_message'] = json_encode($retLog);
			}
		} else {
			if(isset($_REQUEST['addLog'])) {
				$ret['log_ignored'] = true;
			    $ret['log_message'] = 'test form-var has been passed';
			}
		}
	}

	if(is_array($api->returnData)) $ret = array_merge($ret,$api->returnData);

	// Sending info.
	$api->sendHeaders();
	// Output Value
	__p('END logic/api ');
	$__p->end('include_logic',$this->_url);
    switch ($api->contentTypeReturn) {
        case 'JSON':
  			if(isset($api->formParams['__p']))
				$ret['__p'] = __p();
			if($this->error)
				$ret['errors'] = $this->errorMsg;
            
            $ret['_totTime'] = $__p->data['init']['include_logic'][$this->_url]['time'];

			if(count($api->rewrite)) $ret = $api->rewrite;
            die(json_encode($ret));    
			               
            break;
        default:
            if($api->error) die($api->errorMsg);
			else die($api->returnData['data']);
            break;
    }
}

?>