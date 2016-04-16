<?php

// This logic example requires the template: $this->getCloudServiceResponse("getTemplate/Email.htm");

$this->setConf("pageCode","email");

    if(strlen($_POST['from'])) $_from = $_POST['from'];
    else $_from = $this->getConf("defaultEmailSender");
            
        
    if(strlen($_POST['txtMsg'])) $_txtMsg = $_POST['txtMsg'];
    else $_txtMsg = "Text body message to see it works.";
    
    if(strlen($_POST['htmlMsg'])) $_htmlMsg = $_POST['htmlMsg'];
    else $_htmlMsg = "<html><body><h1>Html body message to see it works.</h1></body></html>";
    
    if(strlen($_POST['subject'])) $_subject = $_POST['subject'];
    else $_subject = "Testing Email";
    
    if(strlen($_POST['to'])) $_to = $_POST['to'];
    else $_to = '';
    
    if(strlen($_POST['sendGridUser']) && $_POST['sendGridUser'] != "sendGridUser") $_sendgridUser = $_POST['sendGridUser'];
    else $_sendgridUser = $this->getConf("sendGridUser");
    
    if(strlen($_POST['sendGridPassword']) && $_POST['sendGridPassword'] != "sendGridPassword") $_sendgridPassword = $_POST['sendGridPassword'];
    else $_sendgridPassword = $this->getConf("sendGridPassword");
    
    if(strlen($_POST['useSendGrid'])) $_useSendGrid = $_POST['useSendGrid'];
    else $_useSendGrid = 0;    

if(!strlen($this->getConf("defaultEmailSender"))) {
    
    $_msg = 'Error: please set $this->setConf("defaultEmailSender","{Write the emailbydefault}") in /config/config.php';
    
} else {
    if($_POST['send']=='1') {
        
        $this->loadClass("email/GoogleAppsEmail");
        $_objEmail = new GoogleAppsEmail($_from);  // if you don't specify $sender the object takes it from $this->getConf("defaultEmailSender")
        
          $_objEmail->setSubject($_subject);
          $_objEmail->setTextBody($_txtMsg);
          $_objEmail->setHtmlBody($_htmlMsg);
        
        if(strlen($_GET['emailDebug'])) $_objEmail->setDebug(true);
        
        if($_useSendGrid) {
            if(!$_objEmail->useSendGridCredentials($_sendgridUser,$_sendgridPassword)) {
                $_msg = "Error: ".$_objEmail->getError();
            } 
        } 
        
        if(!$_objEmail->isError()) {
            if($_objEmail->send($_to)) {
                $_msg = "OK. Message sent to: ".print_r($_to,true);
            } else {
                $_msg = "Error sending to: ".print_r($_to,true)." (".$_objEmail->getError().")";
            }        
        }
        $_to = '';
        
    
    }
}

// Hidding real sendGridUser & sendGridPassword
if($_sendgridUser == $this->getConf("sendGridUser")) $_sendgridUser  = 'sendGridUser';
if($_sendgridPassword == $this->getConf("sendGridPassword")) $_sendgridPassword  = 'sendgridPassword';

?>