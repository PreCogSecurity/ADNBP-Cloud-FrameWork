<?php
$data['method'] = $api->method;
$data['headers'] = $this->getHeaders();
$data['formParams'] = $api->formParams;
$api->addReturnData($data);