<?php
// CloudFrameWork Class v1q
if (!defined ("_CFDB_CLASS_") ) {
    define ("_CFDB_CLASS_", TRUE);
	
	class CloudFrameWork {
		
        // Base variables
		var $db = null;
		var $memCache = null;
		var $super = null;
		var $objects = null;
		
		var $urlObject = null;
		var $selectedObject = null;
	
	
		// Construct
		function CloudFrameWork(&$super) {
			$this->super = &$super;
			$this->memCache = new Memcache;
	        $this->super->loadClass("db/CloudSQL");
			list($foo,$script,$service,$this->urlObject) = split('/',$super->_url,4);
		}
		
		// Init objects		
		function init() {
	        $this->db = new CloudSQL();
			$this->db->connect();
			if(isset($_GET['dbdebug']) ) $this->db->_debug=1;	
			$this->db->connect();
			if($this->db->error()) {
				$this->super->setError($this->db->getError());
				return false;
			} else {
				return true;
			}
		}
		
		// End CF closing DB.
		function end() {
			$this->db->close();
		}
		
		// Read menu of objects
		function readMenu() {
			
		 		// Let's cache a DB content very frecuently 
		        $tmpZip = $this->memCache->get("CFEditObjects".$this->super->getOrganizationDefault());
			    if(!strlen($tmpZip) || isset($_REQUEST['nocache'])) {
				    unset($_CloudFrameWorkData);
					if($this->super->getOrganizationDefault() == 1) {
			    		$_CloudFrameWorkData['DirectoryObjects'] = "%"; 
						if(isset($_GET['dbdebug'])) _print("INIT QUERY to retrieve Directory Objects: Calling query from menuObjects.php");  
					    $tmp = $this->db->CloudFrameWork("getRecords",$_CloudFrameWorkData);
					} else {      
						if(isset($_GET['dbdebug'])) _print("INIT QUERY  to retrieve Directory Objects: Calling query from menuObjects.php");  
			    		$_CloudFrameWorkData['DirectoryOrganization_Id'] = $this->super->getOrganizationDefault();   
					    $tmp = $db->CloudFrameWork("getRecords",$_CloudFrameWorkData,'Rel_DirectoryObjects_DirectoryOrganizations');
					}
					$this->memCache->set("CFEditObjects".$this->super->getOrganizationDefault(),gzcompress(serialize($tmp)));
			    } else {
			    	$tmp = unserialize(gzuncompress($tmpZip));
			    }
				
				$this->objects['_title_'] = 'Select an object to Edit'; // Default title for objets
				
				$_found = false;
		        for ($i=0,$j=0,$tr=count($tmp); $i < $tr; $i++) {
		            $key = $tmp[$i]['DirectoryObject_Name'];
		
					// If the urlObject is include in the current objects
		            if($this->urlObject == $key) {
		                $_found=true;
		                $this->objects['_title_'] = $tmp[$i]['DirectoryObject_LangEN'];
						
						// get from caché las version
						$tmpZip = $this->memCache->get("CFEditObject_".$tmp[$i]['DirectoryObject_LangEN'].'_'.$this->super->getOrganizationDefault());
						if(!strlen($tmpZip) || isset($_REQUEST['nocache'])) {
							$this->selectedObject =$tmp[$i];
							
							//Read from DB Field Types
							$this->selectedObject['fieldDBTypes'] = $this->db->CloudFrameWork("getFieldTypes",array($this->urlObject=>"%"));
							$this->processObjecFields();
							//_printe($this->selectedObject);
							
							//Split Labels for the object 
							
							//Put in cachñe the results..
							$this->memCache->set("CFEditObject_".$tmp[$i]['DirectoryObject_LangEN'].'_'.$this->super->getOrganizationDefault(),gzcompress(serialize($this->selectedObject)));
						} else {
							$this->selectedObject = unserialize(gzuncompress($tmpZip));
						}
						
		                $_active = true;
		            } else  $_active = false;
		            
		            // If level 0            
		            if( !strlen($tmp[$i]['DirectoryObject_DirectoryObject_Id'])
		               || (strlen($tmp[$i]['DirectoryObject_DirectoryObject_Id']) && $tmp[$i]['DirectoryObject_DirectoryObject_Id'] == $tmp[$i]['DirectoryObject_Id']) ) {
		
		                $this->objects['_keys_'][] = $key;                   
		                $this->objects[$key]['id'] = $key;
		                $this->objects[$key]['name'] = $tmp[$i]['DirectoryObject_LangEN'];
		                $this->objects[$key]['groupname'] = $tmp[$i]['DirectoryObjectsGroup_Name']; 
		                if(!$this->objects[$key]['active'])
		                    $this->objects[$key]['active'] = $_active;
		                $j++;  
		            // level 1 asociated to   DirectoryObject_DirectoryObject_Id     
		            } else {
		                $this->objects[$tmp[$i]['DirectoryObject_DirectoryObject_Name']]['submenu'][] = array("id"=>$key
		                                                                                        ,"name"=>$tmp[$i]['DirectoryObject_LangEN']
		                                                                                        ,"groupname"=>$tmp[$i]['DirectoryObjectsGroup_Name']
		                                                                                        ,"active"=>$_active
		                                                                                        ,"parent"=>$tmp[$i]['DirectoryObject_DirectoryObject_Name']
		                                                                                        );
		                if($_active) $this->objects[$tmp[$i]['DirectoryObject_DirectoryObject_Name']]['active'] = $_active;
		                
		            }
		        }
				
				// Deativate $this->urlObject if not found
				if(!$_found ) $this->urlObject = null;
				unset($tmp);	
						
			
		}
	
		// End CF closing DB.
		function processObjecFields() {
			
			if($this->selectedObject === null) return;
			
			// Process List Fields and tis views
			if(strlen($this->selectedObject['DirectoryObject_ListFields'])) {
	        	$tmp = explode("\n",$this->selectedObject['DirectoryObject_ListFields']);
				for ($j=0,$i=0,$tr=count($tmp); $i < $tr; $i++) if(strlen(trim($tmp[$i]))) {
					$tmp[$i] = trim($tmp[$i]);
				 	if(strpos($tmp[$i], 'View=') !== false) {
				 		if(strlen($_views[$j]['fields'])) {
                            if(!strlen($_views[$j]['title'])) $_views[$j]['title'] = 'General';
				 		    $j++;
                        }
				 		$_views[$j]['title'] = str_replace('View=', '', $tmp[$i]);
						$_rViews[$_views[$j]['title']] = $j;
						continue;
				 	} 
				 	$_views[$j]['fields'] .= trim($tmp[$i]);
					
					$key = trim(str_replace(',', '', $tmp[$i]));
					$_label[$key] = $key;
					$_rLabel[$key] = $key;
					$this->selectedObject['fieldDBTypes'][$key]['label'] = $key;
					
					//if(strpos($tmp[$i], ',') === false) $j++;
				}
	
	        }    // If the DirectoryObject_ListFields is empty we take the fields form fieldDBTypes
	        else if(is_array($this->selectedObject['fieldDBTypes']))  foreach ($this->selectedObject['fieldDBTypes'] as $key => $value) {
	        	$_views[0]['fields'] .= (strlen($_views[0]['fields']))?','.$key:$key;
				$this->selectedObject['fieldDBTypes'][$key]['label'] = $key;
				$_label[$key] = $key;
				$_rLabel[$key] = $key;
	        }
			if(!strlen($_views[0]['title'])) {
				 $_views[0]['title'] = 'General';
				 $_rViews['General'] = 0;
			} 

			// $cfFilters is used also in the top menu
	        if(($tr = count($_views))>0) {
			 	for ($i=0,$cffilter=''; $i < $tr; $i++) {
					
			        $cfFilters[$i]['id'] = 'javascript:document.ExploreObjects.view.value=\''.$i.'\';document.ExploreObjects.submit();';
			        $cfFilters[$i]['name'] = 'View: '.$_views[$i]['title'];
					$cfFilters['_keys_'][]= $i;
					if($_currentView== $i) {
						$_fields = $_views[$i]['fields'];
					}
				}   
				if(!strlen($_fields)) 	$_fields =  $_views[0]['fields'];
	        }	
						
			$this->selectedObject['views'] = $_views;
			$this->selectedObject['rViews'] = $_rViews;
			$this->selectedObject['filterViews'] = $cfFilters;
			
			
			// Let's explore DirectoryObject_Params divided by views 0..n
			
			
			if(strlen($this->selectedObject['DirectoryObject_Params']))   {
				// split in lines..
	        	$tmp = explode("\n",$this->selectedObject['DirectoryObject_Params']);
				
				// Start analyzing fields separated by views
				$_paramView='fieldDBTypes';
				for ($j=0,$i=0,$tr=count($tmp); $i < $tr; $i++) if(strlen(trim($tmp[$i])) && strpos($tmp[$i], '--') === false) { // avoid comments
				 	
					// $key is the field
	                list($_field,$value) = explode("=",$tmp[$i],2);
	                $key = trim($_field); $value = trim($value);	
					
					// View controler
				 	if($_field == 'View') {
				 		$_paramView = 'fieldDBTypes_'.$value; continue;
				 	}
					
					// virtual Fields
	                if(strripos($value, "virtualField") !== false) {
	                    unset($matchs);
	                    preg_match('/virtualField\[([^\]]*)\]/', $value,$matchs);
						
	                    if(strlen($matchs[1])) {
	                    	
	                    	$this->selectedObject[$_paramView][$key]['type'] = 'virtual';
							$this->selectedObject[$_paramView][$key]['value'] = str_replace('query=', '', $matchs[1]);
							 
							/*
							if(strpos($_fields, ','.$key) !== false) {
	                        	$_fields = str_replace(','.$key, '', $_fields); // Extract this field 
	                        	$_qfields = str_replace(','.$key, ',('.$this->selectedObject['virtualField'][$key].') '.$key,$_qfields); // Extract this field 
							} else {
	                            $_fields = str_replace($key.',', '', $_fields);
	                        	$_qfields = str_replace($key.',', '('.$this->selectedObject['virtualField'][$key].') '.$key.',',$_qfields); // Extract this field 
							}
							 * 
							 */
	                    }
	                } 
	                	
	                // Columns Calc
	                if(strripos($value, "sumColumn") !== false)  $this->selectedObject[$_paramView][$key]['calcColumn'] = 'sum';
	                
					// List Order
			        if(strripos($value, "order") !== false) {
			        	if(strlen($this->selectedObject[$_paramView]['_order_'])) $this->selectedObject[$_paramView]['_order_'].=',';
						$this->selectedObject[$_paramView]['_order_'].= $key;
			        	if(strpos($value, "order DESC") !== false) $this->selectedObject[$_paramView]['_order_'].= ' DESC';
			        }
					
					// Filter Dynamic from select
			        if(strripos($value, "selectDynamic") !== false && !strlen($_GET['force_'.$key]))  $this->selectedObject[$_paramView][$key]['filterDynamic'] = true;
			        
					// Read only								
			        if(strripos($value, "readonly") !== false)   $this->selectedObject[$_paramView][$key]['readOnly'] = true;

					// Allow System
			        if(strripos($value, "allowSystem") !== false)   $this->selectedObject[$_paramView][$key]['allowSystem'] = true;

	                // External URL 
	                $_format[$key] = '';
	                if(strpos($value, "externalURL") !== false) {
	                    $_format[$key] = "externalURL";
	                    unset($matchs);
	                    preg_match('/externalURL\[([^\]]*)\]/', $value,$matchs);
	                    if(strlen($matchs[1])) {
	                    	$this->selectedObject[$_paramView][$key]['externalURL'] = $matchs[1];
	                    }
	                } 
					
	                // Formats 
					if(strripos($value, "currency") !== false) {
			            $this->selectedObject[$_paramView][$key]['format'] = "currency";
			        } else if(strripos($value, "date") !== false) {
	                    $this->selectedObject[$_paramView][$key]['format'] = "date";
	                } 
					
					// Convert a text field in autoselect			
			        if(strripos($value, "autoselect") !== false) {
			        	$this->selectedObject[$_paramView][$key]['autoSelect'] = true;
			            $_autoSelect[$key] = true;
						// $db->setAutoSelectField($key);
						//$_filterDynamic[$key]=true;
			        } 
						

					// To define external fields instead of <external_field>_Name			
			        if(strpos($value, "referalFields") !== false) {
			        	unset($matchs);
			        	preg_match('/referalFields\[([^\]]*)\]/', $value,$matchs);
						if(strlen($matchs[1])) {
							$this->selectedObject[$_paramView][$key]['referalFields'] = $matchs[1];
				            // $_referalFields[$key] = $matchs[1];
							// $db->setReferalField($key,$this->strCFReplace($matchs[1]));
						}
			        } 	
					
					// Force a where value for this field
			        if(strpos($value, "forceWhere") !== false) {
			        	unset($matchs);
			        	preg_match('/forceWhere\[([^\]]*)\]/', $value,$matchs);
						if(strlen($matchs[1])) {
							$this->selectedObject[$_paramView][$key]['forceWhere'] = $matchs[1];
							//$db->addWhereField($key,$this->strCFReplace($matchs[1]));
				            //$_forceWhere[$key] = $this->strCFReplace($matchs[1]);
						}
			        } 
					
					// Get the value to field from a external list
			        if(strpos($value, "pickExternalValue") !== false) {
			        	unset($matchs);
			        	preg_match('/pickExternalValue\[([^\]]*)\]/', $value,$matchs);
						if(strlen($matchs[1])) {
							$this->selectedObject[$_paramView][$key]['pickExternalValue'] =$matchs[1];
							/*
				            $_pickExternalField[$key] = $this->strCFReplace($matchs[1]);
							if(strpos( $_pickExternalField[$key],'?')) $_pickExternalField[$key].='&';
							else $_pickExternalField[$key].='?';
							$_pickExternalField[$key].='noheader&pickExternal';
							 * 
							 */
						}
			        } 
					
					// Modify the filter for this field only for Where values
			        if(strpos($value, "filterWhere") !== false) {
			        	unset($matchs);
			        	preg_match('/filterWhere\[([^\]]*)\]/', $value,$matchs);
						if(strlen($matchs[1])) {
							$this->selectedObject[$_paramView][$key]['filterWhere'] = $matchs[1];
							
							//$db->addFilterWhereField($key,$this->strCFReplace($matchs[1]));
				            //$_filterWhere[$key] = $this->strCFReplace($matchs[1]);
						}
			        } 
					
					// The value to show DependOf other values
			        if(strpos($value, "dependOf") !== false) {
			        	unset($matchs);
			        	preg_match('/dependOf\[([^\]]*)\]/', $value,$matchs);
						if(strlen($matchs[1]))
							$this->selectedObject[$_paramView][$key]['dependOf'] = $matchs[1];
			            
			            // $_dependOf[$key] = (strlen($matchs[1]))?$this->strCFReplace($matchs[1]):false;
			        } 
	
				    // Asign label[XXX] tag
			        if(!isset($_GET['showFields']) && strpos($value, "label") !== false) {
			        	unset($matchs);
			        	preg_match('/label\[([^\]]*)\]/', $value,$matchs);
						if(strlen(trim($matchs[1]))) {
							$this->selectedObject[$_paramView][$key]['label'] = $matchs[1];
							$this->selectedObject[$_paramView]['_reverseLabels_'][$matchs[1]] = $key;
						} else
							$this->selectedObject[$_paramView]['_reverseLabels_'][$key] = $key;
						
			        } else
						$this->selectedObject[$_paramView]['_reverseLabels_'][$key] = $key;
					
				    // Required field?
			        if(strpos($value, "required") !== false) {
			        	$this->selectedObject[$_paramView][$key]['required'] = true;
			            // $_required[$key] = true;
			        } 	
	
			        if(strpos($value, "default") !== false) {
			        	unset($matchs);
			        	preg_match('/default\[([^\]]*)\]/', $value,$matchs);
						$this->selectedObject[$_paramView][$key]['default'] = $matchs[1];
			            // $_default[$key] = $this->strCFReplace($matchs[1]);					
			        } 
				}        	
	        }

			
		}		

		// Read records
		function readRecords() {
			if($this->urlObject === null) return false;
			
			// Analyze selected Object field
			$_where = '1=1';
			unset($_CloudFrameWorkData);
			$_CloudFrameWorkData[$this->urlObject]  = $_where;
			if(isset($_GET['dbdebug'])) _print("INIT QUERY to Edit Record for '.$this->urlObject.': Calling query from EditObjects.php");  
            $recordsForEdit = $this->db->CloudFrameWork("getRecordsForEdit",$_CloudFrameWorkData,$this->urlObject,$_order,$_qfields,$_GET['page']);
			_print("getRecordsForEdit",$_CloudFrameWorkData,$this->urlObject,$_order,$_qfields,$_GET['page']);
			_print($this->db->getError(),$this->db->getQuery());
			//_print($recordsForEdit);
			
			$filters = array();
			if(!$this->db->error()) {
				
				// Create filter & complete missing fields
	            for ($i=0,$tr=count($recordsForEdit['fields']); $i < $tr; $i++) {
	                $key =  $recordsForEdit['fields'][$i];
					
					if($_addVisibleFields && isset($fieldTypes[$key])) $_visibleFields[] = $key;
					if($_addInsertFields && isset($fieldTypes[$key])) $_insertFields[] = $key;
					
	                if($recordsForEdit[$key]['type'] == "rel" || $this->selectedObject['fieldDBTypes'][$key]['autoSelect']) {
	                	
	                	if($this->selectedObject['fieldDBTypes'][$key]['filterDynamic']) $filters[$key]['type'] = 'selectDynamic';
						else if(strlen($this->selectedObject[$_paramView][$key]['pickExternalValue'])) $filters[$key]['type'] = 'pickExternalValue';
					   	else $filters[$key]['type'] = 'select'; 
	                    $filters[$key]['data']=$recordsForEdit[$key]['relData'];
	                }                    
	            } 
				$this->selectedObject['filters'] = $filters;
			}
			
		}


	}
}