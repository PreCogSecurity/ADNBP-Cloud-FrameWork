<?php
// API Documentation Module APIARY compatible
        if(is_file($this->_webapp."/localize/api.txt")) $file = $this->_webapp."/localize/api.txt";
        else $file = $this->_rootpath."/ADNBP/localize/api.txt";
        
        $apiFile = file($file);
        $api = array();
        $lastCommand = '';
        $currentService = 0;
        $currentGroup = 0;
        $currentSubgroup = 0;
        $currentCalls = 0;
        
        $lastId = 0;
        for($i=0,$tr=count($apiFile);$i<$tr;$i++) {
            
            list($param1,$param2, $param3) = explode(' ',trim($apiFile[$i]),3); 
            
            //echo "<li>".$command;
            switch (strtoupper($param1)) {
                case 'HOST:':
                    $host = $param2;
                    $lastCommand = 'HOST';
                    break;
                
                case '#':
                    if(strtoupper($param2) != 'GROUP') {
                        $currentService++;
                        $lastCommand ='service';
                        $lastId = $currentService;
                        $api[$currentService][$lastCommand][$lastId]['title']=$param2;
                        
                    } else {
                        $currentGroup++;
                        $lastId = $currentGroup;
                        $lastCommand =$currentService.'_group';
                        $api[$currentService][$lastCommand][$lastId]['title']=$param3;
                    }
                    break;
                case '##':
                    $currentSubgroup++;
                    $lastId = $currentSubgroup;
                    list($param3,$call) = explode('[', $param3);
                    $lastCommand =$currentService.'_'.$currentGroup.'_subgroup';
                    $api[$currentService][$lastCommand][$lastId]['title']=$param2.' '.$param3;
                    $api[$currentService][$lastCommand][$lastId]['call']=str_replace(']', '', $call);
                    break;

                case '###':
                    $currentCalls++;
                    $lastId = $currentCalls;
                    list($param3,$method,$url) = explode('[', $param3);
                    $lastCommand =$currentService.'_'.$currentGroup.'_'.$currentSubgroup.'_methods';
                    $api[$currentService][$lastCommand][$lastId]['title']=$param2.' '.$param3;
                    $api[$currentService][$lastCommand][$lastId]['method']=str_replace(']', '', $method);
                    $api[$currentService][$lastCommand][$lastId]['url']=str_replace(']', '', $url);
                    break;

                default:
					if(strlen(ltrim($apiFile[$i]))) $apiFile[$i] = ltrim($apiFile[$i]);
                    if($currentService)
                       $api[$currentService][$lastCommand][$lastId]['description'].=$apiFile[$i];
                   
                break;
            }
            
        }
        //_print($api,'exit');


	   // Api Fields
		$apiFields['templateName']['paramName'] = 'templateName';
		$apiFields['templateName']['paramDescription'] = 'String templateName of the Template to get to use.';
		$apiFields['templateName']['paramDetails'] = 'string, required, example: intro';	   


		$docServicesTitle = "API";
		$docServicesSubtitle = "Write the descriptions of your API using ADNBP/logic/apiDoc.php as a template and write it in: <webapp>/logic/api/apiDoc.php";
		

		
		$docServices['templates']['title']='Templates';
		$docServices['templates']['description']='Retrieve templates available on the server';
		$docServices['templates']['api'][0]['title'] = 'Templates Collection';		
		$docServices['templates']['api'][0]['api'] = '/templates';	
		$docServices['templates']['api'][0]['GET']='Retrieve all templates available';
		$docServices['templates']['api'][0]['GET-response-header']="200 (OK)\nContent-Type: application/json";
		$docServices['templates']['api'][0]['GET-response-content']= json_encode(array('intro','File','..'));


		$docServices['templates']['api'][1]['title'] = 'Retreive a Template';		
		$docServices['templates']['api'][1]['api'] = '/templates/{templateName}';	
		$docServices['templates']['api'][1]['GET']='Retrieve the template defined by string';
		$docServices['templates']['api'][1]['GET-response-header']="200 (OK)\nContent-Type: text/html";
		$docServices['templates']['api'][1]['GET-response-content']=htmlentities("<html>\n<header>..</header>\n<body>..</body>\n</html>");
		$docServices['templates']['api'][1]['example']='/templates/intro';
		$docServices['templates']['api'][1]['params'][0] = $apiFields['templateName'];

		$docServices['checkAPIAuth']['title']='CheckApiAuth';
		$docServices['checkAPIAuth']['api'][0]['title'] = 'Check Api Version';		
		$docServices['checkAPIAuth']['api'][0]['api'] = '/checkAPIAuth';	
		$docServices['checkAPIAuth']['api'][0]['GET']='Show current version of the API';
		$docServices['checkAPIAuth']['api'][0]['GET-response-header']="200 (OK)\nContent-Type: text/html";
		$docServices['checkAPIAuth']['api'][0]['GET-response-content']= 'Your current version is: 2014Jun.17';
		
		
		$docServices['version']['title']='FraWork Version';
		$docServices['version']['api'][0]['title'] = 'Check Api Version';		
		$docServices['version']['api'][0]['api'] = '/version';	
		$docServices['version']['api'][0]['GET']='Show current version of the API';
		$docServices['version']['api'][0]['GET-response-header']="200 (OK)\nContent-Type: text/html";
		$docServices['version']['api'][0]['GET-response-content']= 'Your current version is: 2014Jun.17';


		$docServices['myip']['title']='Show My IP';
		$docServices['myip']['api'][0]['title'] = 'Return IP of the Client-Side';		
		$docServices['myip']['api'][0]['api'] = '/myip';	
		$docServices['myip']['api'][0]['GET']='Return IP of the Client-Side';
		$docServices['myip']['api'][0]['GET-response-header']="200 (OK)\nContent-Type: text/html";
		$docServices['myip']['api'][0]['GET-response-content']= '192.168.1.1';


		$docServices['genPassword']['title']='genPassword';
		$docServices['genPassword']['description']='Return the source code of a template';
		$docServices['fetchURL']['title']='fetchURL';
		$docServices['fetchURL']['description']='Return the source code of a template';
	
?>