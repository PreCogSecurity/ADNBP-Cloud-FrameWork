<?php

class MobilePush {
	public $error = false;
	public $errorMessage = '';
	private $apns = array();
	private $gcm = array();
	public $apnConnection = null;
	public $lastGCMResult=null;
	
	
	function __construct(){
		
	}
	
	function setAPNS($phrase,$cert,$url='tls://gateway.sandbox.push.apple.com:2195') {
		if(is_file($cert)) {
			$this->apns['phrase'] = $phrase;
			$this->apns['cert'] = $cert;
			$this->apns['url'] = $url;
			if($apnConnection) fclose($this->apnConnection);
			$ctx = stream_context_create();
			stream_context_set_option($ctx, 'ssl', 'local_cert', $this->apns['cert']);
			stream_context_set_option($ctx, 'ssl', 'passphrase', $this->apns['phrase']); 
            //stream_context_set_option($ctx, 'ssl', 'cafile', __DIR__.'/entrust_2048_ca.cer'); 
            
			
			try {
				$this->apnConnection = stream_socket_client(
											$this->apns['url'], $err,
											$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
				if (!$this->apnConnection) {
					$this->error = true;
					$this->errorMessage = "Failed to connect: $err $errstr";
				}
			} catch(Exception $e) {
				$this->error = true;
				$this->errorMessage = $e->getMessage();
			}
			return(!$this->error);
		} else {
			$this->error = true;
			$this->errorMessage .= $cert.' does not exist'."\n";
			return(false);
		}
	}

	function setGCM($key,$url='https://android.googleapis.com/gcm/sen') {
		$this->gcm['key'] = $key;
		$this->gcm['url'] = $url;
		return(true);
	}
	
	// Close all open connections
	function closeAPNS() {
		if($this->apnConnection) fclose($this->apnConnection);
	}
	
	/**
	 * Send a message to the the APNS with $deviceToken destination 
	 * Based on: http://www.tagwith.com/question_138013_ssl-connect-to-apns-server-in-local-environment-stream-socket-client-failed
	 * Also.. http://stackoverflow.com/questions/28995197/apns-php-stream-socket-client-failed-to-enable-crypto
	 */
	function _sendAPNSMessage($type,$deviceToken, $txt, $badge){
		if($this->error) return(false);
		if(!$this->apnConnection) {
			$this->error = true;
			$this->errorMessage = 'Use setAPNS($phrase,$cert[,$url]) before to call this method.';
		}
		
		if (!$this->apnConnection) {
			$this->error = true;
			$this->errorMessage = "Failed to connect: $err $errstr";
		} else { 
			// Create the payload body
			if($type=='msg') {
				$body['aps'] = array(
						'alert' => array('body' => $txt),
						'sound' => 'default',
						'badge' => $badge
				);
			} else {
				$body['aps'] = array(
						'alert' => array('loc-key' => $txt),
						'sound' => 'default',
						'badge' => $badge
				);					
			}
			// Encode the payload as JSON
			$payload = json_encode($body);
			//echo $payload;
			// Build the binary notification
			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
            
            // Sending trying again if it fails.
			$maxtries = 2;
            $tries = 0;
            $sleep=1;
            do {
                $this->error = false;
                if($tries>0) sleep(1); // Wait one second to try more thant 1 time
    			// Send it to the server
    			try {
    			    $result = fwrite($this->apnConnection, $msg, strlen($msg));
    			} catch(Exception $e) {
                    $this->error = true;
                    $this->errorMessage = $e->getMessage().' sending '.$msg.' ('.strlen($msg).')';
                }
                $tries++;
            } while(($this->error || !$result) && $tries < $maxtries);
			
			if (!$this->error && !$result) {
				$this->error = true;
				$this->errorMessage = 'Message not delivered';
			} 
			
		}
		return(!$this->error);
	}
	
	function sendAPNSMessage($deviceToken, $mssg, $badge){
		return($this->_sendAPNSMessage('msg',$deviceToken, $mssg, $badge));
	}
	function sendAPNSLocKey($deviceToken, $locKey, $badge){
		return($this->_sendAPNSMessage('locKey',$deviceToken, $mssg, $badge));
	}
	
	/**
	 * Android Messages
	 *
	 */	
	function sendGCMMessage($regIds, $pushMssg){
		if($this->error) return(false);
		
		if( strlen($this->gcm['url']) && strlen($this->gcm['key'])) {
			if(!is_array($regIds)) $regIds = array($regIds);
			$fields = array('registration_ids'  => $regIds,"collapse_key" => $pushMssg,'data'=> array( "message" => $pushMssg ));

			
			$headers  = 'Authorization: key=' . $this->gcm['key']. "\r\n";
			$headers .= 'Content-Type: application/json'. "\r\n";
			$headers .= 'Connection: close' . "\r\n";
			
			// use key 'http' even if you send the request to https://...
			$options = array(
			    'http' => array(
			        'header'=> $headers ,
			        'method'  => 'POST',
			        'content' => json_encode($fields),
			    ),
			);
			//_printe($options);
			$context  = stream_context_create($options);
			$result = @file_get_contents($this->gcm['url'], false, $context);
			if($result===false) {
				$this->error=true;
				$this->errorMessage = error_get_last();
			} else {
				$result = json_decode($result);
				$this->lastGCMResult = $result;
				if(!$result->success) {
					$this->error=true;
				} 
			}
			
			// var_dump($result);	
		}
		
		return(!$this->error);
	}	
}