<?php
// CORS Control from a potential Cross-Reference.
$db_keys = [$api->formParams['test_assign_web_key'],$api->formParams['test_assign_web_referers_allowed']];
if($this->checkWebKey($db_keys)) {
    $api->sendCorsHeaders('GET');
}

$api->checkMethod('GET');
$data = ['vars.for.testing'=>[
    'test_assign_basic_user'=>'User to send in a basic Auth',
    'test_assign_basic_password'=>'password to send in a basic Auth. Can be empty',
    'test_assign_web_client_key'=>'api_key to require a X-CLOUDFRAMEWORK-API-KEY header',
    'test_assign_client_referrers_allowed'=>'which domains will be allowed: *,localhost*, etc..',
    'test_assign_server_key_id'=>'key_id to require a X-CLOUDFRAMEWORK-SECURITY header'

]];
if($api->params[0]=='test') {

    if(strlen($api->formParams['test_assign_basic_user'])) {
        $data['Basic.Auth'] = ['method' => '$passed =  $this->checkBasicAuth(\''.$api->formParams['test_assign_basic_user'].'\',\''.$api->formParams['test_assign_basic_password'].'\')'];
        $data['Basic.Auth']['notes'] = 'you have also: (bool)$this->existBasicAuth() and (array)$this->getBasicAuth()';
        $data['Basic.Auth']['passed'] = $this->checkBasicAuth($api->formParams['test_assign_basic_user'], $api->formParams['test_assign_basic_password']);


        if (!$data['Basic.Auth']['passed']) $data['Basic.Auth']['message'] = "Error. Send Basic Auth with the following info: user={$api->formParams['test_assign_basic_user']} and password={$api->formParams['test_assign_basic_password']}";
    }

    if(strlen($api->formParams['test_assign_web_key'])) {
        $data['Web.Client.Auth'] = ['method'=>'$passed = $this->checkWebKey([\''.$api->formParams["test_assign_web_key"].'\',\''.$api->formParams["test_assign_web_referers_allowed"].'\')]'];
        $data['Web.Client.Auth']['notes'] = 'you have also: (bool)$this->existWebKey() and (array)$this->getWebKey()';
        $data['Web.Client.Auth']['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
        $data['Web.Client.Auth']['passed'] = $this->checkWebKey($db_keys);
        if (!$data['Web.Client.Auth']['passed']) {
            if(!strlen($_SERVER['HTTP_REFERER']))
                $data['Web.Client.Auth']['message'][] = "Missing HTTP_REFERER: only *.rule referers_allowed";
            $data['Web.Client.Auth']['message'][] = "Error. Send X-CLOUDFRAMEWORK-WEB-KEY header or _GET['web_key']  with the following info: "
                . $api->formParams['test_assign_web_key'];
        }

    }

    if(strlen($api->formParams['test_assign_server_key'])) {
        $data['Web.Server.Auth'] = ['method'=>'$passed = $this->checkServerKey([\''.$api->formParams["test_assign_server_key"].'\',\''.$api->formParams["test_assign_ips_allowed"].'\')]'];
        $data['Web.Server.Auth']['notes'] = 'you have also: (bool)$this->existServerKey() and (array)$this->getServerKey()';
        $data['Web.Server.Auth']['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
        $data['Web.Server.Auth']['passed'] = $this->checkServerKey([$api->formParams["test_assign_server_key"],$api->formParams["test_assign_ips_allowed"]]);
        if (!$data['Web.Server.Auth']['passed']) {
            if(strlen($_SERVER['REMOTE_ADDR']) < 10)
                $data['Web.Server.Auth']['message'][] = "REMOTE_ADD short. Maybe only *.rule ips_allowed will work";
            $data['Web.Server.Auth']['message'][] = "Error. Send X-CLOUDFRAMEWORK-SERVER-KEY header]  with the following info: "
                . $api->formParams['test_assign_server_key'];
        }

    }


    if(strlen($api->formParams['test_assign_security_token_id'])) {
        $secret='WerErty';
        $data['SecurityToken.Auth'] = ['method'=>'$passed = $this->checkCloudFrameWorkSecurity(3600,\''.$api->formParams["test_assign_security_token_id"].'\',$secret)]'];
        $data['SecurityToken.Auth']['notes'] = 'The key can be generated using: $this->generateCloudFrameWorkSecurityString($api->formParams[\'test_assign_security_token_id\'],[\'\'|{current_time}],$secret)';
        $data['SecurityToken.Auth']['passed'] = $this->checkCloudFrameWorkSecurity(3600,$api->formParams['test_assign_security_token_id'],$secret);
        if (!$data['SecurityToken.Auth']['passed'])
            $data['SecurityToken.Auth']['message'] = "Error. Send X-CLOUDFRAMEWORK-SECURITY header with the following info: "
                  .$this->generateCloudFrameWorkSecurityString($api->formParams["test_assign_security_token_id"],'',$secret);

    }

}
$api->addReturnData($data);
