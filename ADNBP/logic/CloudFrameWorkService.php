<?php

    //$headers = apache_request_headers(); // Store all headers
    $isSuperAdmin = (strlen($this->getHeader('X-Adnbp-Superuser')))?$this->checkPassword($this->getHeader('X-Adnbp-Superuser'),$this->getConf("adminPassword")):false;
	$apiMethod = $this->getAPIMethod(); // GET , PUT, UPDATE, DELETE, COPY...
    
// This service has to be implemented in your <document_root>/logic/CloudFrameWorkService.php
    list($foo,$script,$service,$params) = explode('/',$this->_url,4);
    switch ($service) {
        case 'keepSession':
            // This method will never exist because it is implemented in ADNBP.class
            break;
        case 'template':
        case 'templates':
        case 'getTemplate':
			if(strlen($params)) {
				header("Content-type: text/html");
	            $template = $params;
				if(strpos($template,'.') === false) $template.='.htm';
	            if(is_file($this->_rootpath."/ADNBP/templates/CloudFrameWork/".$template)) {
	               echo(file_get_contents ( $this->_rootpath."/ADNBP/templates/CloudFrameWork/".$template ));
	            } else if(is_file($this->_webapp."/templates/CloudFrameWork/".$template)) {
	               echo(file_get_contents ( $this->_webapp."/templates/CloudFrameWork/".$template ));
	            } else echo("<html><body>template not found</body></html>");   
			} else {
				$ret=array();
				if(is_dir($this->_webapp."/templates/CloudFrameWork")) {
					$files = scandir($this->_webapp."/templates/CloudFrameWork");
					foreach ($files as $key => $value) if(strpos($value,'.htm')) $ret[] = str_replace( '.htm' ,  '', $value);
				}
				$files = scandir($this->_rootpath."/ADNBP/templates/CloudFrameWork");
				foreach ($files as $key => $value) if(strpos($value,'.htm')) $ret[] = str_replace( '.htm' ,  '', $value);
				
				
				header("Content-type: application/json");
				die(json_encode($ret));
			}
			die();  
            break;
        case 'checkAPIAuth':
		case 'auth':
        			if(strlen($params)) {
				echo '<h1>Server Side</h1>';
				echo '<li>conf-var: CloudServiceToken-'.$params.': '.(strlen($this->getConf("CloudServiceToken-".$params))?'exist. OK':'missing. ERROR').'</li>';
				echo '<h1>Client Side</h1>';
				echo '<li>header: X-Cloudservice-Id '.(strlen($this->getHeader('X-Cloudservice-Id'))?'exist. OK':'missing. ERROR').'</li>';
				echo '<li>header: X-Cloudservice-Date '.(strlen($this->getHeader('X-Cloudservice-Date'))?'exist. OK':'missing. ERROR').'</li>';
				echo '<li>header: X-Cloudservice-Signature '.(strlen($this->getHeader('X-Cloudservice-Signature'))?'exist. OK':'missing. ERROR').'</li>';
				$msg = '';
				if(strlen($this->getConf("CloudServiceToken-".$params)))
				if($this->checkAPIAuth($msg)){
					echo "<li>API Auth OK";
				} else echo "<li>API Auth Error: ".$msg;
				
			} else {
				echo "A Id param is required: ../checkAPIAuth/{Id}";
			}
			die();
            break;
        case 'checkVersion':
		case 'version':
            if(!strlen($params)) echo "Your current version is: ".$this->version();
            else echo(($this->version() == $params)?"OK $params":"Warning. Your version  ".htmlentities($params)." is different of current version:".$this->version);
            die();
            break;
        case 'myIP':
        case 'myip':
            echo $_SERVER[REMOTE_ADDR];
            die();
		    break;
		case 'genPassword':
			if(strlen($params)) echo $this->crypt($params);
			else echo "A string param is required: ../getPassword/{yourPassword}";
            die();
		    break;			
        case 'fetchURL':
            if(strpos($_GET[url], 'http') !== false) {
                echo @file_get_contents($_GET[url]);
            } else {
                echo "You have to provide 'url' GET variable: .../fetchURL?url={encodedURL} ";
            }

            die();
            break;
        default:
			// This allow to create own services in each WebServer
			if(strlen($service) && is_file($this->_webapp."/logic/CloudFrameWorkService/".$service.".php")) {
				$_found = true;
				include_once $this->_webapp."/logic/CloudFrameWorkService/".$service.".php";
			} elseif(strlen($service) && strlen($this->getConf("ExtraLogicPath")) && is_file($this->getConf("ExtraLogicPath")."/CloudFrameWorkService/".$service.".php")) {
				$_found = true;
				include_once $this->getConf("ExtraLogicPath")."/CloudFrameWorkService/".$service.".php";
			} else {
				$this -> setConf("templateVarContent", "output");
				$output = '<h1 align="center">Service not found.</h1>';
			}
            break;
    }
?>