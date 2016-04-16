<?php
$api->checkMethod('GET,POST,DELETE');

if(!$api->error && $api->method=='GET' && $api->params[0]=='source') die(file_get_contents(__FILE__));

// Only works in development for security reasons.
// if(!$api->error && !$this->is('development')) $api->setError('this test function only works in development',401);
if(!$api->error) {
	$this->loadClass("io/Bucket");
	$_bucketPath = 'adnbp-cloud-framwork-public/upload';
	$bucket = new Bucket($_bucketPath);
	
	switch ($api->method) {
		case 'GET':
			if(!strlen($api->params[0])) {
				$api->addReturnData($bucket->vars);
			} else switch ($api->params[0]) {
				case 'url':
					$api->addReturnData(array('bucket'=>$bucket->bucket,'url_to_upload'=>$bucket->getUploadUrl()));
					break;
				case 'fastscan':
					$api->addReturnData(array('bucket'=>$bucket->bucket,'fastScan'=>$bucket->fastScan()));
					break;
				case 'scan':
					$api->addReturnData(array('bucket'=>$bucket->bucket,'scan'=>$bucket->scan()));
					break;
				default:
					$api->setError('user api/_file/[url,scan] to get a url of uploading.');
					break;
			}
			break;
		case 'POST':
			if(!$bucket->uploaded) {
				$api->setError('No file uploaded');
			} else {
				$bucket->manageUploadFiles();
				$api->addReturnData($bucket->uploadedFiles);
			}
			break;
		case 'DELETE':
			$api->addReturnData($bucket->deleAllFiles());
			break;
	}
}