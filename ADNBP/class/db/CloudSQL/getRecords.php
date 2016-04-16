<?php

if(!strlen($value['selectWhere'])) $value['selectWhere'] = "1=1";

if(!strlen($table)) $table = $key;
if(strlen($order)) $order = " ORDER BY ".$order;
if($action == "getRecords") {
    $_q = "select $selectFields from $table main where ".$value['selectWhere'].$order." limit ".$this->_limit;
   return($this->getDataFromQuery($_q,$value['values']));
   
} else if($action == "getDistinctRecords") {
    $_q = "select distinct $selectFields from $table main where ".$value['selectWhere'].$order." limit ".$this->_limit;
   return($this->getDataFromQuery($_q,$value['values']));
   
} else {

   // Eplore types
   for($k=0,$tr3=count($types);$k<$tr3;$k++) {
   	   	
   	   if(preg_match("/(int|numb|deci)/i", $types[$k]['Type']))
           $_ret[$types[$k]['Field']]['type'] = 'text';
	   else if(preg_match("/(text)/i", $types[$k]['Type']))
	       $_ret[$types[$k]['Field']]['type'] = 'textarea';
	   else
	   	   $_ret[$types[$k]['Field']]['type'] = 'text';
       
       list($foo,$field,$rels) = explode("_", $types[$k]['Field'],3);
       
       if(($field=="Id" && $rels=="" && !$_relTable) || ($_relTable && $foo=="Id")) 
           $_ret[$types[$k]['Field']]['type'] = "key";
       else if($rels=='Id'   || ($_relTable && strlen($field))) {
       	
	   // Getting Rel data to this field                               	
           $_ret[$types[$k]['Field']]['type'] = "rel";
		   
           if($_relTable) {
               $reltable=$foo."s";
			   $_f= $foo;
           } else {
               $reltable=$field."s";
           	   $_f= $field;
           }
		   
		   // Fields dependences and WhereConditions
		   $_fqWhere = '';
		   if(($dependences = $this->getFieldDependence($types[$k]['Field'])) !== false)  $_fqWhere .=  ' (R.'.$dependences.')';
		   
		   if(($fieldwheres = $this->getWhereField($types[$k]['Field'])) !== false) {
		   	if(strlen($_fqWhere)) $_fqWhere .= ' AND ';
		   	$_fqWhere .=  ' ('.$fieldwheres.')';
		   }

		   if(($fieldwheres = $this->getFilterWhereField($types[$k]['Field'])) !== false) {
		   	if(strlen($_fqWhere)) $_fqWhere .= ' AND ';
		   	$_fqWhere .=  ' ('.$fieldwheres.')';
		   }
		   
		   $_refField = str_replace('_Id', '_Name', $types[$k]['Field']);
		   if($this->getReferalField($types[$k]['Field']) !==false ) {
		   	   $selectFields .=',CONCAT_WS(" - ",'.$this->getReferalField($types[$k]['Field']).') '.$_refField;
		       $_refFields = 'CONCAT_WS(" - ",R.'.str_replace(',', ',R.', $this->getReferalField($types[$k]['Field'])).') Name';
		   } else {
		   	   $selectFields .=','.$_f.'_Name '.$_refField;
		   	   $_refFields = 'R.'.$_f.'_Name Name';
		   }
		   // include all referal Fields in the query.
		   //$_refFields = 'R.'.str_replace(',', ',R.', $this->getReferalField($types[$k]['Field']));
		   
		   
		   
		   $_fn = 'R.'.$_f.'_Id Id,'.$_refFields;
		   if(!strlen($_fqWhere )) $_fqWhere .=  '1=1';
		   // $_fq = " SELECT DISTINCT $_fn FROM  $table R  WHERE $_fqWhere ";
		   $_fq = " SELECT DISTINCT $_fn FROM  $reltable R LEFT JOIN  $table P ON (R.".$_f."_Id = P.".$types[$k]['Field'].") WHERE $_fqWhere ";
		   
		   if($this->isAvoidFilterCalculation($types[$k]['Field'])) {
		   		$_ret[$types[$k]['Field']]['relData'] = array();
		   } else {
               $relData = $this->getDataFromQuery($_fq); 
			   if($this->error()) return false;
               $_ret[$types[$k]['Field']]['relData'] =$relData;
           }
		   
       } else if($this->isAutoSelectField($types[$k]['Field'])) {
       	   $_fqWhere = '';
		   if(($dependences = $this->getFieldDependence($types[$k]['Field'])) !== false)  $_fqWhere .=  ' ('.$dependences.')';
       	   $_fn = $types[$k]['Field'].' AS Id,'.$types[$k]['Field'].' AS Name';
       	   if(!strlen($_fqWhere )) $_fqWhere .=  '1=1';
       	   
       	   $_fq = " SELECT DISTINCT $_fn FROM  $table  WHERE $_fqWhere ";
           $relData = $this->getDataFromQuery($_fq); 
		   if($this->error()) return false;
           $_ret[$types[$k]['Field']]['relData'] =$relData;
       	
       }

       // add where to Global Query: 
	   if(($fieldwheres = $this->getWhereField($types[$k]['Field'])) !== false) {
			$value['selectWhere'] .= ' AND   ('.$fieldwheres.')';
	   }	

   } 
   // Let see how many rows it has
   $nrows = $this->getDataFromQuery("select count(1) TOT from $table main where ".$value['selectWhere'],$value['values']);
   if($this->error()) return false;
   $_ret['totRows'] = $nrows[0]['TOT'];
 
   
   if($action == "getRecordsForEdit") $this->_limit = 50;  
   
   $_ret['totPages'] = round($nrows[0]['TOT']/$this->_limit,0);
   if($_ret['totPages']*$this->_limit < $nrows[0]['TOT']) $_ret['totPages']++;
   
   if($page >= $_ret['totPages'] ) $page = $_ret['totPages']-1 ;
   if($page < 0 ) $page=0;
   
   $_ret['currentPage'] = $page;
   $_ret['totRowsInPage'] = ($this->_limit < $_ret['totRows'])?$this->_limit:$_ret['totRows'];
   $_ret['offset'] = $page * $this->_limit;
                            
   $data = $this->getDataFromQuery("select $selectFields from $table main where ".$value['selectWhere'].$order." limit ".$_ret['offset'].','.$this->_limit,$value['values']);
   if($this->error()) return false;
   $_ret['fields'] = array_keys($fieldTypes);
   for($i=0,$tr=count($data);$i<$tr;$i++)
      $data[$i]['_hash'] = $this->getHashFromArray($data[$i]);
	

   $_ret['data'] = $data;
   
   unset($data);
   return($_ret);
}
?>