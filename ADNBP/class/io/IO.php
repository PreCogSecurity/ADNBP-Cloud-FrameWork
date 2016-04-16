<?php
require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
use google\appengine\api\cloud_storage\CloudStorageTools;

// CloudSQL Class v10
if (!defined ("_bucket_CLASS_") ) {
    define ("_bucket_CLASS_", TRUE);
    
    class IO {
		
    	var $folder = '';
    	var $folderPref = '';
		var $error = false;
		var $errorMsg = array();
		var $max = array();
		var $uploadedFiles = array();
		var $isThereUploads = false;
		
   		function IO($folder='') {
   			global $adnbp;
   			if(strlen($folder)) {
   				if(strpos($folder, 'gs://')!==false) $this->folderPref ='gs://';
   				$this->folder = str_replace('gs://', '', $folder);
			} else {
				$this->folderPref = 'gs://';
				$this->folder = CloudStorageTools::getDefaultGoogleStorageBucketName();
			}
			
			$this->vars['upload_max_filesize'] = ini_get('upload_max_filesize');
			$this->vars['max_file_uploads'] = ini_get('max_file_uploads');
			$this->vars['file_uploads'] = ini_get('file_uploads');
			$this->vars['default_bucket'] = $this->folder;
			$this->vars['retUploadUrl'] = $adnbp->_url;
			
			
			if(count($_FILES)) {
				foreach ($_FILES as $key => $value) {
					if(is_array($value['name'])) {
						for($j=0,$tr2=count($value['name']);$j<$tr2;$j++) {
							foreach ($value as $key2 => $value2) {
								$this->uploadedFiles[$key][$j][$key2] = $value[$key2][$j];
							}
							if(!$this->uploadedFiles[$key][$j]['error']) $this->isThereUploads = true;
						}
					} else {
						$this->uploadedFiles[$key][0] = $value;
						if(!$value['error']) $this->isThereUploads = true;
					}
				}
			}
   		}
		
		function getUploads($form_field='',$pos='',$field=''){
			if(!strlen($form_field)) return($this->uploadedFiles);
			elseif(!strlen($pos)) return($this->uploadedFiles[$form_field]);
			elseif(!strlen($field)) return($this->uploadedFiles[$form_field][$pos]);
			else return($this->uploadedFiles[$form_field][$pos][$field]);
		}
		function getExtenstion($filename){
			return(pathinfo($filename, PATHINFO_EXTENSION));
		}
		function deleteUploadFiles() {
			if(strlen($_FILES['uploaded_files']['tmp_name'])) unlink($_FILES['uploaded_files']['tmp_name']);
		}

		function saveUploadFile($form_field,$pos,$filename='',$path='',$public=true) {
			$ret = false;
			if(is_array($this->uploadedFiles[$form_field][$pos])) {
					
				// Context	
				$context = array('gs'=>array('Content-Type' =>$this->uploadedFiles[$form_field][$pos]['type']));
				if($public)  $context['gs']['acl'] = 'public-read';
				stream_context_set_default($context);
				
				// Filename
				if(!strlen($filename)) $filename = $this->uploadedFiles[$form_field][$pos]['name'];
				$dest = $this->folderPref.$this->folder.$path.'/'.$filename;
				$value = $this->uploadedFiles[$form_field][$pos];
				try {
					if(copy($value['tmp_name'],$dest)) {
						$this->uploadedFiles[$form_field][$pos]['movedTo'] = $dest;
						if($public)
							$this->uploadedFiles[$form_field][$pos]['publicUrl'] = $this->getPublicUrl($dest);
						$ret = true;
					} else {
						$this->addError(error_get_last());
						$this->uploadedFiles[$form_field][$pos]['error'] = $this->errorMsg;
					}
				} catch(Exception $e) {
						$this->addError($e->getMessage());
						$this->addError(error_get_last());
						$this->uploadedFiles[$form_field][$pos]['error'] = $this->errorMsg;
				}
			} 
			return $ret;
		}
		
		function manageUploadFiles($dest_bucket='',$public=true) {
			// $gs_name = $_FILES['uploaded_files']['tmp_name'];
			// move_uploaded_file($gs_name, 'gs://my_bucket/new_file.txt');
			if($this->isThereUploads)  {
				foreach ($this->uploadedFiles as $key => $files) {
					for($i=0,$tr=count($files);$i<$tr;$i++) {
						$value = $files[$i];
						if(!$value['error']) 
							$this->saveUploadField($key,$i,'',$dest_bucket,$public);
					}
				}
			}
			return($this->uploadedFiles);
		}
		
		function getPublicUrl($filename='', $path='', $https = true) {
			global $adnbp;
			
			if(strpos($filename,'gs://')!==0 ) {
				if(strlen($filename))
					$filename = $this->folderPref.$this->folder.$path.'/'.$filename;
				else 
					$filename = $this->folderPref.$this->folder.$path;
			}
			
			if(strpos($filename,'gs://')!==0 ) {
				$ret  = $adnbp->url['host_url'].str_replace($_SERVER['DOCUMENT_ROOT'], '',$filename);
			} else {
				$ret =  CloudStorageTools::getPublicUrl($filename,$https);
			}

			return $ret;
		}
		
		function scan($path='') {
			$ret = array();
			$tmp = scandir($this->folderPref.$this->folder.$path);
			foreach ($tmp as $key => $value) {
				$ret[$value] = array('type'=>(is_file($this->folderPref.$this->folder.$path.'/'.$value))?'file':'dir');
				if(isset($_REQUEST['__p'])) __p('is_dir: '.$this->folderPref.$this->folder.$path.'/'.$value);
			}
			return($ret);
		}
		function fastScan($path='') {
			return(scandir($this->folderPref.$this->folder.$path));
		}
		
		function deleAllFiles($path='') { return($this->deleteFiles('*',$path));}
		function deleteFile($filename,$path='') { return($this->deleteFiles(array($filename),$path)); }
		function deleteFiles($filenames,$path='') {
			if(is_array($filenames)) $files=$filenames;	
			else if($filenames == '*') $files = $this->fastScan($path);
			else $files[] = $filenames;
			
			$ret = array();
			foreach ($files as $key => $value) {
				if(strlen($path)) $value = $path.'/'.$value;
				else $value = $this->folderPref.$this->folder.$path.'/'.$value;
				try {
					$ret[$value] = 'ignored';
					if(is_file($value)) {
						$ret[$value] = 'deleting: '.unlink($value);
					}
				}catch(Exception $e) {
						$this->addError($e->getMessage());
						$this->addError(error_get_last());
				}
			}
			return($ret);
		}
		
		function rmdir($path='')  {
			$value = $this->folderPref.$this->folder.$path;
			$ret = false;
			try {
				$ret = rmdir($value);
			} catch(Exception $e) {
					$this->addError($e->getMessage());
					$this->addError(error_get_last());
			}
			return $ret;
		}

		function mkdir($path='')  {
			$value = $this->folderPref.$this->folder.$path;
			$ret = false;
			try {
				$ret = @mkdir($value);
			} catch(Exception $e) {
					$this->addError($e->getMessage());
					$this->addError(error_get_last());
			}
			return $ret;
		}
		
		function isDir($extrapath='')  {
			$value = $this->folderPref.$this->folder.$extrapath;
			return(is_dir($value));
		}
		
		function isFile($file,$extrapath='')  {
			$value = $this->folderPref.$this->folder.$extrapath.'/'.$file;
			return(is_file($value));
		}
		
		function isMkdir($path='')  {
			$value = $this->folderPref.$this->folder.$path;
			$ret = is_dir($value);
			
			if(!$ret) try {
				if(!strlen($path)) {
					$subfolders = explode('/', $this->folder);
					if($this->folderPref=='gs://') {
						$value = 'gs://'.$subfolders[0];
					} else {
						$value ='';				
					}
					$ret=true;
					for($i,$tr=count($subfolders);$i<$tr && $ret;$i++) if(strlen($subfolders[$i])) {
						$value.='/'.$subfolders[$i];
						if(!is_dir($value)) {
							try {
								$ret = @mkdir($value);
							} catch(Exception $e) {
								$this->addError($e->getMessage());
								$this->addError(error_get_last());
							}
						}
					}

				} else {
					$subfolders = explode('/', $path);
					$value = $this->folderPref.$this->folder;
					$ret=true;
					for($i=1,$tr=count($subfolders);$i<$tr && $ret;$i++) if(strlen($subfolders[$i])) {
						$value.='/'.$subfolders[$i];
						if(!is_dir($value)) {
							try {
								$ret = @mkdir($value);
							} catch(Exception $e) {
								$this->addError($e->getMessage());
								$this->addError(error_get_last());
							}
						}
					}
				}
				$ret = is_dir($value);
				
			} catch(Exception $e) {
					$this->addError($e->getMessage());
					$this->addError(error_get_last());
			}
			return $ret;
		}

		function saveFromSource($source, $filename, $path='',$public=true) {
			$ok = false;
			try{
				$ret=false;
				$error='';
				if(strpos($source, 'data:')===0) {
					list($header,$source) = explode(",",$source,2);
					$header = str_replace('data:', '', $header);
					list($ctype,$encoded) = explode(";",$header,2);
					if($encoded == 'base64')
						$ret = @base64_decode($source);
					else {
						$error = 'data: with no base64 format';
					}
				} else {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $source);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$ret = curl_exec ($ch);
					$error = curl_error($ch); 
					curl_close ($ch);
					$ctype = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
				}
				if($ret=== false) {
					$this->addError($error);
				} else {
					$ok = $this->putContents($filename,$ret,$path,$public,$ctype);
				}
			} catch(Exception $e) {
					$this->addError($e->getMessage());
					$this->addError(error_get_last());
			}
			return $ok;
		}
				
		function putContents($filename, $data, $path='',$public=true,$ctype = '' ) {
			
			// $ctype could be: text/plain f.e.
			$options = array();
			if(strlen($ctype)) $options['gs']['Content-Type'] = $ctype;
			if($public)	$options['gs']['acl'] = 'public-read';
			$ctx = stream_context_create($options);
			
			$ret = false;
			try{
				if(@file_put_contents($this->folderPref.$this->folder.$path.'/'.$filename, $data,0,$ctx) === false) {
					$this->addError(error_get_last());
				} else $ret = true;
			} catch(Exception $e) {
					$this->addError($e->getMessage());
					$this->addError(error_get_last());
			}
			return($ret);
		}

		function getContents($file,$path='') {
			$ret = '';
			try{
				$ret = @file_get_contents($this->folderPref.$this->folder.$path.'/'.$file);
				if($ret=== false) {
					$this->addError(error_get_last());
				}
			} catch(Exception $e) {
					$this->addError($e->getMessage());
					$this->addError(error_get_last());
			}
			return($ret);
		}
			
		function getUploadUrl() {
			$options = array( 'gs_bucket_name' => $this->folder );
			$upload_url = CloudStorageTools::createUploadUrl($this->vars['retUploadUrl'], $options);
			return($upload_url);
		}	
		

		function setError($msg) {
			$this->errorMsg = array();
			$this->addError($msg);
		}
		function addError($msg) {
			$this->error = true;
			$this->errorMsg[] = $msg;
		}
	}
} 