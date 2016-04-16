<?php
$api->checkMethod('GET,PUT');

$template = null;
$lang = null;

// Error Control end-points.
if(!$api->error && strlen($api->params[0])) {
	if(strpos($api->params[0], '..')) 
		$api->setError('{templateRoute} doesn\'t allow ".." in the route. Important security issued have been reported.',403);
	else {
		$template = $api->params[0];
		if(strpos($template,'.htm') === false) $template.='.htm';
		$lang = $api->params[1];
	}
}

// Sending response
if(!$api->error) {
// List templates
	if(!strlen($api->params[0])) {
        // Paths to search for the template
        $templates=array();
        $paths= array('/ADNBP/templates/CloudFrameWork');
        $files = scandir($this->_rootpath."/ADNBP/templates/CloudFrameWork");
        foreach ($files as $key => $content) if(strpos($content,'.htm')) $templates[] = str_replace( '.htm' ,  '', $content);


		//Getting templates from $this->_webapp."/templates/CloudFrameWork"
        if(is_dir($this->_webapp."/templates/CloudFrameWork"))  {
        	$paths[] = '/{webapp}/templates/CloudFrameWork';
            $files = scandir($this->_webapp."/templates/CloudFrameWork");
            foreach ($files as $key => $content) if(strpos($content,'.htm')) $templates[] = str_replace( '.htm' ,  '', $content);
		}
		
		//Getting templates from ApiTemplatesPath conf-var
        if(strlen($this->getConf("ApiTemplatesPath"))) {
        	$paths[] = $this->getConf("ApiTemplatesPath");
			if(preg_match("/^http/", $this->getConf("ApiTemplatesPath"))){
				$api->addReturnData(array('warning'=>"We can not get templates list from a remote URL: ".$this->getConf("ApiTemplatesPath")));
			} elseif(!is_dir($this->getConf("ApiTemplatesPath"))) {
				$api->setError("Error. The following path does not exist: ".$this->getConf("ApiTemplatesPath"),503);
            } else {
            	$files = scandir($this->getConf("ApiTemplatesPath"));
                foreach ($files as $key => $content) if(strpos($content,'.htm')) $templates[] = str_replace( '.htm' ,  '', $content);
            }
		}

		$api->addReturnData(array('api_paths'=>$paths));
		$api->addReturnData(array('templates'=>$templates));
		unset($value);
		unset($templates);
		
// Check the template passed does not have ..
	} else {
		$api->setReturnFormat('HTML');
			
		// Allow include the templates from GoogleCloudStoreBucket
		if(strlen($this->getConf("ApiTemplatesPath"))) {
			if(preg_match("/^http/", $this->getConf("ApiTemplatesPath"))){
				$value = file_get_contents ($this->getConf("ApiTemplatesPath")."/".$template );
				if($value!==false) $found = true;
			} elseif(is_file($this->getConf("ApiTemplatesPath")."/".$template)) {
               $value = file_get_contents ($this->getConf("ApiTemplatesPath")."/".$template );
               $found = true;
			} else $found = false;
		} 
		
        // If not lets try from webapp or Cloud FramWork
        if(!$found)
		if(is_file($this->_webapp."/templates/CloudFrameWork/".$template)) {
           $value = file_get_contents ( $this->_webapp."/templates/CloudFrameWork/".$template );
           $found = true;
        } elseif(is_file($this->_rootpath."/ADNBP/templates/CloudFrameWork/".$template)) {
           $value = file_get_contents ( $this->_rootpath."/ADNBP/templates/CloudFrameWork/".$template );
           $found = true;
        }
        
        // If not Found error
        if(!$found) {
        	$api->setError("<html><body>template not found</body></html>",404);
            
        // Else apply subsititutions
        } else {
            // Do substitutions if lang is passed
        	if(strlen($lang))
				$value = $this->applyTranslations($value,$lang); // substitute {{lang:xxxx }}
			
			$value = $this->applyVarsSubsitutions($value,$api->formParams);
			$api->setReturnData($value);
			unset($value);
        }        
	}
} 