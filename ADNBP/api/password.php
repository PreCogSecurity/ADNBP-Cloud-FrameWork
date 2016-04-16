<?php
$api->checkMethod('POST');

if(!$api->error) {
	switch ($api->params[0]) {
            case 'crypt':
				switch ($api->params[1]) {
					case 'validate':
		                if( strlen($api->formParams['password']) && strlen($api->formParams['password_crypt']) ) {
		                	$api->addReturnData(
		                	    array(
		                	         'validated'=>$this->checkPassword($api->formParams['password'],$api->formParams['password_crypt'])
		                	        ,'password'=>$api->formParams['password']
		                	        ,'password_crypt'=>$api->formParams['password_crypt']
									)
							);
		                } else $api->setError('Required form-params: password and password_crypt');
		                break;						
					
					default:
						if(!strlen($api->params[1])) {
			                if( strlen($api->formParams['password']) )  $api->addReturnData(array('password'=>$api->formParams['password'],'crypt'=>$this->crypt($api->formParams['password'])));
			                else $api->setError('Required form-param  is not received.');
						} else					
							$api->setError('url params allowed: crypt and crypt/validate');
						break;
				}
				break;

            default:
				$api->setError('url params allowed: crypt');
                break;
        }
}