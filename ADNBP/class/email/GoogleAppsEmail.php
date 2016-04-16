<?php  
    ########################################################### 
    # Madrid Feb 3th 2014
    # ADNBP Cloud Frameworl  
    # http://www.adnbp.com (support@adnbp.com)
    ########################################################### 
/**
* Paquete de clases de utilidad general
* @version 1.0.0
* @author Hector López <hlopez@adnbp.com>
* @package com.adnbp.class.email
*/

require_once 'google/appengine/api/mail/Message.php';
use google\appengine\api\mail\Message;


if (!defined ("_GOOGLEAPPSEMAIL_CLASS_") ) {
    define ("_GOOGLEAPPSEMAIL_CLASS_", TRUE);

require_once(dirname(__FILE__) . '/mimePart.php');

    /**
    * @class GoogleAppsEmail
    * Let's send emails
    *
    */
   class GoogleAppsEmail{
       
       var $_data = array();
       var $_errorMsg = '';
       var $_error = false;
       var $_debug = false;
       var $_sengrid = null;
       var $_sengridmail = null;
       
       function GoogleAppsEmail ($from = '', $subject = '', $text = '', $thtml = '') {
            global $adnbp;
            
            if(is_array($from)) {
                $this->setFrom($from[0]);
                if(strlen($from[1]))
                    $this->setFromName($from[1]);
            } else if(!strlen($from) && is_object($adnbp)) $from = $adnbp->getConf("defaultEmailSender");
            else  $this->setFrom($from);
            
            $this->setSubject($subject);
            $this->setTextBody($text);
            $this->setHtmlBody($thtml);
            
       }
       
       function getError() { return($this->_errorMsg); }
       function isError() { return($this->_error === true); }
       function setError($msg) { $this->_errorMsg = $msg; $this->_error = true;}
       function setFrom($txt) { $this->_data['sender'] = $txt; }
       function setFromName($txt) { $this->_data['senderName'] = $txt; }
       function setSubject($txt) { $this->_data['subject'] = $txt; }
       function setTextBody($txt) { $this->_data['textBody'] = $txt; }
       function setText($txt) { $this->_data['textBody'] = $txt; }
       function setCc($cc) { $this->_data['cc'] = $cc; }
       function setBcc($bcc) { $this->_data['bcc'] = $bcc; }
       
       function setTo($txt) { $this->_data['to'] = $txt; }
       
       function setHtmlBody($txt) { $this->_data['htmlBody']= $txt; }
       function setHtml($txt) { $this->_data['htmlBody']= $txt; }
       
       function setHtmlTemplate($txt) {
            $this->_data['htmlTemplate']= $txt;
            if(is_file("./templates/$txt")) {
                $this->setHtmlBody(file_get_contents("./templates/$txt"));
            } else {
                $this->setError("Template $txt no found");
            }
       }
       
       function useSendGridCredentials($user='',$passw='') {
           global $adnbp;
           include_once(dirname(__FILE__) ."/sendgrid-google-php/SendGrid_loader.php");
           
           if(!strlen($user) && is_object($adnbp)) {
               $user = $adnbp->getConf("sendGridUser");
               $passw = $adnbp->getConf("sendGridPassword");
           }
           $_ret = true;
           if(!strlen($user) || !strlen($passw) ) {
               $_ret = false;
               $this->setError("No sendGridUser and/or sendGridPassword credentials");
           } else {
               $this->_sendgrid = new SendGrid\SendGrid($user, $passw);
               $this->_sendgridmail     = new SendGrid\Mail();
           }
           return($_ret);
           // $this->_sendgridmail->addCc($email);
           // $this->_sendgridmail->addBcc($email);
        }
       
       function checkValidEmail($email) {
           
           $_ret = true;
           if(!is_array($email)) $email = array($email);
               for($i=0,$tr=count($email);$i<$tr;$i++) {
                  if (! filter_var($email[$i], FILTER_VALIDATE_EMAIL) !== false) {
                  $_ret =  false;
                }             
           }
           return $_ret;
        }


       function setDebug($boolean) { $this->_debug= $boolean; }
       function isDebug() { return($this->_debug); }
              
       function send($to) {
           
           if($this->isError()) return(false);
           
           // Passing $to into an array
           if(!is_array($to) and strlen($to)) {
               if(!strpos($to, ",")) $to = array($to);
               else $to = explode(",",$to);
           }
           
           if(!$this->checkValidEmail($to)){
               $this->setError("Invalid email/s in 'To' email: ".print_r($to,true));
               return(false);
           }
           
           if(!$this->checkValidEmail($this->_data['sender'])){
               $this->setError("Error in 'From' email: ".$this->_data['sender']);
               return(false);
           }
           
           
           // Checking if everything is OK
           if(!strlen($this->_data['sender'])) {
               $this->setError("Sender missing. Use setFrom(email) method.");
               return(false);
           }
           
           if(!strlen($this->_data['textBody']) && !strlen($this->_data['htmlBody'])) {
               $this->setError("Text or HTML Body missing. Use setTextBody(txt) or setHtmlBody(html) methods.");
               return(false);
           }

           if(!strlen($this->_data['subject'])) {
               $this->setError("Subjectmissing. Use setSubject(txt) method.");
               return(false);
           }
           
           if(is_object($this->_sendgrid)) {
               
               $this->_sendgridmail->setFrom($this->_data['sender']);
               if(strlen($this->_data['senderName'])) $this->_sendgridmail->setFromName($this->_data['senderName']);
               $this->_sendgridmail->setSubject($this->_data['subject']);
               if(strlen($this->_data['htmlBody'])) $this->_sendgridmail->setHtml($this->_data['htmlBody']);
               if(strlen($this->_data['textBody'])) $this->_sendgridmail->setText($this->_data['textBody']);
               $this->_sendgridmail->setTos($to);
			   if(strlen($this->_data['cc'])) $this->_sendgridmail->setCc($this->_data['cc']);
			   if(strlen($this->_data['bcc'])) $this->_sendgridmail->setBcc($this->_data['bcc']);
               $res = $this->_sendgrid->send($this->_sendgridmail);

               if($res === FALSE) {
                   $this->setError("Probably wrong user and/or password");
                   return(false);
               } else {
                    $res = json_decode($res);
                    if($res->message == "success") 
                        return(true);
                    else {
                        $this->setError("<pre>".print_r($res,true)."</pre>");
                        return(false);
                    }
               }

                            
           } else {
               
               $message = new Message();
               $message->setSender($this->_data['sender']);
               $message->setSubject($this->_data['subject']);
               if(strlen($this->_data['htmlBody'])) $message->setHtmlBody($this->_data['htmlBody']);
               if(strlen($this->_data['textBody'])) $message->setTextBody($this->_data['textBody']);
               //if(strlen($this->_data['cc'])) $message->setCc($this->_data['cc']);
               //if(strlen($this->_data['bcc'])) $message->setBcc($this->_data['bcc']);
                              
               $message->addTo($to);
               
                try {
                    if($this->isDebug()) echo "<li> Email Debug: Sending message: <pre>".htmlentities(print_r($message,true))."</pre>";
                    $message->send();
                    if($this->isDebug()) echo "<li> Email Debug: OK";
                    return(true);
                } catch (InvalidArgumentException $e) {
                    $this->setError($e);
                    if($this->isDebug()) echo "<li> Email Debug: Error $e";
                    return(false);
                }               
           }


       }
    }
} 