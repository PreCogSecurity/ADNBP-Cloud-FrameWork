<?php
require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
use google\appengine\api\cloud_storage\CloudStorageTools;

$api->checkMethod('GET,POST');

if(!$api->error) {
	switch ($api->method) {
		case 'GET':
			if(!strlen($this->getConf('uploadDir'))) $api->checkMandatoryFormParam('upload_dir');
			if(!$api->error) {
				$options = array( 'gs_bucket_name' => (strlen($this->getConf('uploadDir')))?$this->getConf('uploadDir'):$api->formParams['upload_dir'] );
				$upload_url = CloudStorageTools::createUploadUrl('/api/upload', $options);
				$options['url_to_upload'] = $upload_url;
				$api->addReturnData($options);
			}
			break;
		
		case 'POST':
			$api->addReturnData($_FILES);
			break;
	}
}
