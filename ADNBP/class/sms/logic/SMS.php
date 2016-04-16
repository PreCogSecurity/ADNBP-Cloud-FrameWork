<?php

    if(strlen($_POST['txtMsg'])) $_txtMsg = $_POST['txtMsg'];
    else $_txtMsg = "Text body message to see it works.";
    
    if(strlen($_POST['to'])) $_to = $_POST['to'];
    else $_to = '';
	
if($_POST[send]) {
	if(strlen($_to) && strlen($_txtMsg) && strlen($this->getConf("twilioAccountSid")) && strlen($this->getConf("twilioAuthToken")) && strlen($this->getConf("twilioNumber"))) {
		$this->loadClass("sms/twilio/Twilio");
	
		try { 
		$client = new Services_Twilio($this->getConf("twilioAccountSid"), $this->getConf("twilioAuthToken")); 
		 
		 
		$client->account->messages->create(array(  
			'From' => $this->getConf("twilioNumber"),    
			'To' => $_to,    
			'Body' => $_txtMsg,    
		));	
		$_msg = "msg sent to: ".$_to;
		} catch(Exception $e) {
			 $_msg = $e;
			
		}
		
	} else {
		$_msg = "Missing right parameters";
	}
} 


?>