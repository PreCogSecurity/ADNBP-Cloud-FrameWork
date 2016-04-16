<?php

	// This service has to be implemented in your <document_root>/logic/CloudFrameWorkAuth.php to autenticate
	if($_POST['CloudUser'] == "admin" && $_POST['CloudPassword'] == "admin" ) {
	        $this->setAuthUserData("name","User Admin");  
	} else if($this->getConf("AllowOauth") && is_array($_SESSION['opauth']) && strlen($_SESSION['opauth']['auth']['info']['name'])) {  
	    /*   		
		// You have to use 	$this->setAuthUserData() to tell the system the user is autenticated.	
	    $_CloudFrameWorkData[DirectoryUsersOauth_Id] = $_SESSION[opauth][auth][uid];
	    $_CloudFrameWorkData[DirectoryUsersOauth_Strategy] = $_SESSION[opauth][auth][provider];
	    $_CloudFrameWorkData[DirectoryUsersOauth_Domain] = $_SERVER[SERVER_NAME];
	    $_CloudFrameWorkData[DirectoryUsersOauth_IP] = $_SERVER[REMOTE_ADDR];
	    $_CloudFrameWorkData[DirectoryUsersOauth_FullName] = $_SESSION[opauth][auth][info][name];
	    $_CloudFrameWorkData[DirectoryUsersOauth_Email] = $_SESSION[opauth][auth][info][email];
	    $_CloudFrameWorkData[DirectoryUsersOauth_SerializedData] = serialize($_SESSION[opauth]);
	
	    $this->loadClass("db/CloudSQL");
	    $db = new CloudSQL();
	    
	    if($db->connect()) {
	        $db->CloudFrameWork("replace",$_CloudFrameWorkData);
	        $db->close();
	    } 
	    
	    if($db->error()) {
	        $output =  "<li>ERROR: ".$db->getError();
	            die($output);
	    }
	    
	    $this->setAuthUserData("name",$_SESSION[opauth][auth][info][name]);
	        $this->setAuthUserData("opauth",$_SESSION);
	        unset($_SESSION[opauth]);
	        
	    }   
	     * 
	     */    
	     }
?>