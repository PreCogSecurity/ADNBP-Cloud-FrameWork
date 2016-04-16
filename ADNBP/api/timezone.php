<?php
$date = date("Y-m-d H:i:s");
$value=array('default_timezone'=>date_default_timezone_get(),'datetime'=>$date);
// GET , POST, PUT, UPDATE, DELETE, COPY...
switch ($api->method) {
    case 'GET':
        list($continent,$city,$date) = explode('/',$params);
        if(!strlen($continent) || $continent=='db') {
			if($continent=='db') {
			   	if(!is_object($db)){
	                $this->loadClass("db/CloudSQL");
	                $db = new CloudSQL();
	            }
				$db->connect();
				$_CloudFrameWorkData['SystemTimeZone_Id'] = '%';
				$ret = $db->cloudFrameWork("getDistinctRecords",$_CloudFrameWorkData);
				if($db->error()) {
					 $api->error = 503;
	                 $api->errorMsg = $db->getError();
				} else {
					for ($i=0,$tr=count($ret); $i < $tr && !$db->error(); $i++) {
						$value['db_timezones'][] = $ret[$i]['SystemTimeZone_Id'];
					}
					if($i==0) $value['db_timezones'][] = 'No timezones loaded. Use: [POST] timezone/updatedb';
				} 
				$db->close();
			} else  
				$value['timezones'] =  timezone_identifiers_list();
			
        } else {
            date_default_timezone_set($continent.'/'.$city);
            $value['new_timezone']=date_default_timezone_get();
            $date = date("Y-m-d H:i:s");
            $value['new_datetime']=$date;
        }
        break;
	case 'POST':
		    list($command) = explode('/',$params);
			if($command != 'updatedb') {
					 $api->error = 400;
	                 $api->errorMsg = 'required parameter: updatedb';
			} else {
			    if(!is_object($db)){
	                $this->loadClass("db/CloudSQL");
	                $db = new CloudSQL();
	            }
				$db->connect();
				$ret =  timezone_identifiers_list();
				$db->command("DELETE FROM CF_SystemTimeZones");
				for ($i=0,$tr=count($ret); $i < $tr && !$db->error(); $i++) { 
					$_CloudFrameWorkData['SystemTimeZone_Id'] =$ret[$i];
					$_CloudFrameWorkData['SystemTimeZone_Name'] =$ret[$i];
					$db->cloudFrameWork("insert",$_CloudFrameWorkData);
					$value['db_timezones'][] = $ret[$i];
				}
				if($db->error()) {
					 $api->error = 503;
	                 $api->errorMsg = $db->getError();
				} 
				$db->close();
			}
			
			
        break;
    default:
        $api->error=400;
        break;
} 

// Compatibility until migration
$error = $api->error;
$errorMsg = $api->errorMsg;
?>