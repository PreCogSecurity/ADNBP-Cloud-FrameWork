<?php
$api->checkMethod('GET');
if(!$api->error)
	$api->setReturnData(timezone_identifiers_list());