<?php
$api->checkMethod('GET');

if(!$api->error) switch ($this->getAPIMethod()) {
    case 'GET':
		$api->addReturnData(array($this->getGeoData('',$_SERVER['REMOTE_ADDR'])));
        break;
}