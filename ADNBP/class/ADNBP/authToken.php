<?php
// we receive: $command and $data
switch ($command) {
	case 'check':
	case 'API_KEY':
	case 'CLOUDFRAMEWORK':
		$type = false;
		if(($command=='CLOUDFRAMEWORK' || $command=='check') && strlen($this->getHeader('X-CLOUDFRAMEWORK-TOKEN'))) $type ='CLOUDFRAMEWORK';
		if(($command=='API_KEY' || $command=='check') && strlen($_REQUEST['API_KEY'])) $type = 'API_KEY';
		if(!$type) {
			$this->setAuth(false);
			$this->addLog( 'we spect a X-CLOUDFRAMEWORK-TOKEN or API_KEY form-param. User session has been destroyed.');	
			return false;		
		}
		switch ($type) {
				case 'CLOUDFRAMEWORK':
					list($_id,$_token) = explode('__', $this->getHeader('X-CLOUDFRAMEWORK-TOKEN'),2);
					
				    // Every request has a Request Fingerprint
					$_hasFingerPrint = sha1(json_encode($this->getRequestFingerPrint()));
					
					if($this->getConf('CLOUDFRAMEWORK-ID-'.$_id) ===null) {
						$this->addLog('Missing conf-var CLOUDFRAMEWORK-ID-'.$_id);
					} elseif(!$this->isAuth()) {
							$this->addLog( "Token not created or has expired!.Get a new token.");	
					} elseif(!strlen($this->getAuthUserData('token')) || $this->getAuthUserData('token') != $this->getInputHeader('X-CLOUDFRAMEWORK-TOKEN')) {
							$this->addLog("Token '".$this->getInputHeader('X-CLOUDFRAMEWORK-TOKEN')."' does not match.");
					} elseif($this->getAuthUserData('tokenData')->hash_fingerprint != $_hasFingerPrint) {
							$this->addLog("Fingerprint doesn't match. Security violation. This call will generate security protocol to evaluate an attack.");
					} else {
                        
						return(true);
					}
					$this->setAuth(false);
					return false;
					break;
					
				case 'API_KEY':
					$referer = 	$this->_referer;
					if(!strlen($referer)) {
						$this->addLog("HTTP_REFERER unknown. Pass a HTTP_REFERER form-var to evaluate");
					} else {
						$key = $_REQUEST['API_KEY'];
						$_api_key_conf = $this->getConf('API_KEY-'.$key);
						if(!strlen($key)) {
							$this->addLog("API_KEY form-var is missing");
						} elseif($this->getConf('API_KEY-'.$key) === null || !is_array($_api_key_conf) || !is_array($_api_key_conf['allowed_referers'])) {
							$this->addLog('Missing array conf-var API_KEY-'.$key.' with  allowed_referers');
						} else {

							$dataToken = $this->getConf('API_KEY-'.$key);
							$finger_print = $this->getRequestFingerPrint();
							$dataToken_hash = sha1(json_encode($dataToken).json_encode($finger_print));
							
							// If is auth and API_KEY match then return true
							if($this->isAuth()) {
								$userData = $this->getAuthUserData('API_KEY');
								if(is_array($userData) && $userData['key']==$key && $userData['hash']==$dataToken_hash)
									return true;
							}

							// Restart auth session
							$this->setAuth(false);
							$api_key_data['key'] = $key;
							$api_key_data['hash'] = $dataToken_hash;
							$api_key_data['date'] = date('Y-m-d h:i:s');
							$api_key_data['fingerprint'] = $finger_print;
							$api_key_data['data'] = $dataToken;
							foreach ($_api_key_conf['allowed_referers'] as $index => $content) 
								if($content=="*" || strpos($referer,$content)!==false) {
									$this->setAuthUserData('API_KEY',$api_key_data);
									return(true);
								}
							$this->addLog("HTTP_REFERER '$referer' does not match with allowed_referers.");
						}
					}
					$this->setAuth(false);
					return(false);		
					break;
				default:
					$this->addLog("Method $type no supported");
					return(false);		
					break;
			}
		return null;
		break;
		case 'generate':
			$this->setAuth(false);
			$id = $data[0];
			$clientfingerprint = (isset($data[1]))?$data[1]:null;

			if(empty($id)) {
				$this->addLog('invalid id and token in setAuthToken function',503);
			} elseif($this->getConf('CLOUDFRAMEWORK-ID-'.$id) === null) {
				$this->addLog('Missing conf-var CLOUDFRAMEWORK-ID-'.$id);
			} else {
				$dataToken['fingerprint'] = $this->getRequestFingerPrint();
				$dataToken['hash_fingerprint'] = sha1(json_encode($dataToken['fingerprint']));
				$dataToken['clientfingerprint'] = $clientfingerprint;
				$dataToken['id'] = $id;
				$dataToken['created'] = date('Y-m-d h:i:s');
				$token = sha1(json_encode($dataToken));
				$dataToken['token'] = $id.'__'.$token;
				$dataToken =json_encode($dataToken);
				
				$this->setAuthUserData('token',$id.'__'.$token);
				$this->setAuthUserData('tokenData',json_decode($dataToken));
			}
			
			return($this->isAuth());
		break;
}
return false;