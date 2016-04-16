<?php
$res='';			
$_url = $this->getCloudServiceURL($rute);
$options = array(
    'http' => array(
    'method'  => $verb,
    'ignore_errors'  => '1'
	
        )
 );

if($data !== null && is_array($data)) {
    $options['http']['header'] =  "Content-type: application/x-www-form-urlencoded\r\n";
    $options['http']['content'] =  http_build_query($data);
}
if($headers!=null) $options['http']['header'] =$headers;

$context  = stream_context_create($options);

$res = @file_get_contents($_url,false,$context);

if ($res === false) {
		throw new Exception("$verb $rute failed: $php_errormsg");
}

switch ($format) {
case 'json':
  $res = json_decode($res);
  if ($res === null) {
    throw new Exception("failed to decode $res as json");
  }
  break;

case 'xml':
  $res = simplexml_load_string($res);
  if ($res === null) {
    throw new Exception("failed to decode $res as xml");
  }
  break;
}
?>