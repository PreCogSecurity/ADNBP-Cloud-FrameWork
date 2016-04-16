<?php
// Temporary bug workaround
// https://code.google.com/p/googleappengine/issues/detail?id=11695#c6
if($this->is("production")) {
     apc_delete('_ah_app_identity_:https://www.googleapis.com/auth/devstorage.read_only');
     apc_delete('_ah_app_identity_:https://www.googleapis.com/auth/devstorage.read_write');
}

// stream_context_set_default($this->system['stream_context_default']);
// When you use getCloudServiceResponse the system exec file_get_contents using a context.
// This default options has been optimez for Google App engine.
// More info: 
// Workaround to avoid EOF: https://code.google.com/p/googleappengine/issues/detail?id=11772&q=certificate%20invalid%20or%20non-existent&colspec=ID%20Type%20Component%20Status%20Stars%20Summary%20Language%20Priority%20Owner%20Log
// GAE gives error with ssl=>(allow_self_signed' => true)
$this->system['stream_context_default'] = array('ssl'=>array('verify_peer' => false));

// Avoid long waits in a connection
$this->system['stream_context_default']['http']['ignore_errors'] ='1';
$this->system['stream_context_default']['http']['header'] = 'Connection: close' . "\r\n";

// $this->setConf('activeCache',true); // Active cache.
// $this->setConf('LocalizePath','gs://my-mucket/localize');

// About Buckets
// -- it is used in {bucket:<bucketname-path>} tags.
$this->setConf('BucketPrefix','gs://');  // Change to $_SERVER['DOCUMENT_ROOT'].'{dirpath}' for development writable/readble dirs
$this->setConf('pathPrefix','gs:/');
$this->setConf('urlPrefix','https://storage.googleapis.com');
