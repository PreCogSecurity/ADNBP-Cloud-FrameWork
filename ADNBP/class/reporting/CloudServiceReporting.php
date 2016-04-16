<?php
// CloudSQL Class v10
if (!defined ("_CloudServiceReporting_CLASS_") ) {
    define ("_CloudServiceReporting_CLASS_", TRUE);
    
    class CloudServiceReporting {
        var $error = false;
        var $errorMsg = array();
        var $data = array();
        var $db = null;
        var $ds = null;
        var $super = null;
        var $queryResults = array();
        var $matrixReports = array();

        var $filters = array();
        
        function __construct() {
            global $adnbp;
            $this->super = &$adnbp;
            $this->super->initCache();
        }

        /**
         * Add filter for the report
         * @param $command
         * @param $var
         * @param null $info
         * @return null
         */
        function filter($command, $var, $info=null) {
            if(is_array($info) && isset($info['type']) &&  ($command == 'set' || $command=='add')) {
                // lets work on the diferent filters.
                $_filterCorrect = true;
                switch($info['type']) {
                    case "select":
                        if(!isset($info['data'])) $_filterCorrect = false;
                        else{
                            // Read Filters from session
                            if(!isset($_REQUEST['reset_filters']))
                                $info['value'] = $_SESSION['CloudServiceReportingFilter_'.$var];
                            // Value for field based in filter or default
                            if(isset($_REQUEST['filter_'.$var])) $info['value'] = $_REQUEST['filter_'.$var];

                            // Evalue default value if still it is empty
                            if(!strlen($info['value'])) $info['value'] = (isset($info['default']))?$info['default']:'';

                            if(strlen($info['value']) && !isset($info['data'][$info['value']])) $info['value'] ='';

                            // Required value
                            if($info['required'] && !strlen($info['value'])) $info['value'] = array_keys($info['data'])[0];

                            // SET from session
                            $_SESSION['CloudServiceReportingFilter_'.$var] = $info['value'];

                        }
                        break;
                    case "input":
                        if(isset($_REQUEST['filter_'.$var])) $info['value'] = $_REQUEST['filter_'.$var];
                        break;
                }

                if($_filterCorrect) {
                    if ($command == 'set') $this->filters = array($var => $info);
                    elseif ($command == 'add') $this->filters[$var] = $info;
                }
            }
            elseif($command == 'delete') { if(isset($this->filters[$var])) unset($this->filters[$var]);}
            else{
                // Current Value
                $value = isset($this->filters[$var]['value'])?$this->filters[$var]['value']:null;
                if($command == 'get') return (isset($this->filters[$var]))?$this->filters[$var]:null;
                elseif($command == 'getvalue') return ($value);
                elseif($command == 'getoption') return ($value!==null && isset($this->filters[$var]['data'][$value]))?$this->filters[$var]['data'][$value]:null;
                elseif($command == 'getdata') return (isset($this->filters[$var]['data']))?$this->filters[$var]['data']:null;
                else return null;
            }
        }


        /**
         * Excute an DB query
         * @param $cubeId string Id of the cube
         * @param $q string SQL query
         * @param $data array with the potential parameters %s of $q
         * @return bool
         */
        function createCubeFromQuery($cubeId, $q, $data=null) {return($this->DBquery($cubeId, $q, $data));}
        function createCubeFromDBQuery($cubeId, $q, $data=null) {return($this->DBquery($cubeId, $q, $data));}
        function createCubeFromDSQuery($cubeId, $q, $data=null) {return($this->DSquery($cubeId, $q, $data));}
        // Alias
        function DBquery($cubeId, $q, $data=null) {
            if($this->error) return false;
            $q = "SELECT ".$q;
            
            // Check cache
            if(!isset($_REQUEST['reload'])) {
                $this->queryResults[$cubeId]['data'] = $this->super->getCache('Reporting_'.$cubeId.'_'.md5($cubeId.$q.json_encode($data)));
                if(is_array($this->queryResults[$cubeId]['data'])) {
                      return true;
                }
            }
            
            // Db instance
            if($this->db===null) {
                $this -> super -> loadClass("db/CloudSQL");
                $this->db = new CloudSQL();
                $this->db->connect();
                if($this->db->error()) {
                     $this->addError($this->db->getError());
                    return false;
                }
            }
            
            // Query
            $ret = $this->db->getDataFromQuery($q,$data);
            $this->queryResults[$cubeId]['query'] = $this->db->getQuery();
            if(!$this->db->error()) {
                $this->queryResults[$cubeId]['data'] = $ret;
                $this->super->setCache('Reporting_'.$cubeId.'_'.md5($cubeId.$q.json_encode($data)),$ret);
                unset($ret);
                return true;
            } else {
                $this->addError($this->db->getError());
                $this->addError($this->db->getQuery()); //TODO: Securize this output
                return false;
            }
        }
        //Close the db connection of a query.
        function queryEnd() {
            if(is_object($this->db)) $this->db->close();
        }

        function DSquery($cubeId, $q, $data=null) {
            if($this->error) return false;
            if(!is_object($this->ds)) {
                $this->setError('No DataStore Object assigned. Use: reportObject->ds = &$DataStoreObject');
                return false;
            }
            $q = "SELECT ".$q;
            // Check cache
            if(!isset($_REQUEST['reload'])) {
                $this->queryResults[$cubeId]['data'] = $this->super->getCache('Reporting_'.$cubeId.'_'.md5($cubeId.$q.json_encode($data)));
                if(is_array($this->queryResults[$cubeId]['data'])) {
                    return true;
                }
            }



            // Query
            $ret = $this->ds->query($q,$data);
            _printe($ret);

            //$this->queryResults[$cubeId]['query'] = $this->ds->getQuery();
            if(!$this->ds->error()) {
                $this->queryResults[$cubeId]['data'] = $ret;
                $this->super->setCache('Reporting_'.$cubeId.'_'.md5($cubeId.$q.json_encode($data)),$ret);
                unset($ret);
                return true;
            } else {
                $this->addError($this->ds->getError());
                //$this->addError($this->db->getQuery()); //TODO: Securize this output
                return false;
            }
        }

        /**
         * Return a cube optionally reduced with $cond
         * @param $cubeId  string id of of data
         * @param $cond  array to reduce
         * @param $fields string fields to return separated by , or *
         * @return array|null
         */
        function getCube($cubeId, $fields="*",$cond=null ) {
            $ret = null;
            if (isset($this->queryResults[$cubeId]['data'])) {

                // Return if only want the Cube.
                if (!is_array($cond) && $fields == "*") {
                    $ret = $this->queryResults[$cubeId]['data'];
                 }
                // There is a $cond and/or $fields!='*'
                else {

                    $ret=array(); // $ret could be empty
                    // Fields to return
                    // $fields has to be an string with length
                    if(!is_string($fields) || trim($fields)=='') $fields='*';

                    // Get array of Fields if $fields!=*
                    if(trim($fields)!='*') $fields = array_map("trim",explode(',', $fields));

                    // Explore all rows
                    foreach ($this->queryResults[$cubeId]['data'] as $i=> $row) {
                        // Only include match elements
                        $inc = true;

                        // Apply condtion if it is an array
                        if(is_array($cond))
                        foreach ($cond as $key => $fieldCond) {

                            if (!is_array($fieldCond)) {
                                $fieldCond = array(  '=', $fieldCond );
                            }
                            if (!isset($row[$key])) $row[$key]='';
                            switch (strtolower($fieldCond[0])) {
                                case '=':
                                    if ( $row[$key] != $fieldCond[1]) $inc = false;
                                    break;
                                case '>':
                                case '>=':
                                case '<':
                                case '<=':
                                    $inc = false;
                                    $op = strtolower($fieldCond[0]);
                                    if(!strlen($row[$key])) $row[$key] = 0;
                                    $type =  ($fieldCond[2])?strtolower($fieldCond[2]):'int';

                                    if($op =='>')
                                        $inc = (trim($row[$key]) > trim($fieldCond[1]));
                                    else if($op =='>=')
                                        $inc = (trim($row[$key]) >= trim($fieldCond[1]));
                                    else if($op =='<')
                                        $inc = (trim($row[$key]) < trim($fieldCond[1]));
                                    else if($op =='<=')
                                        $inc = (trim($row[$key]) <= trim($fieldCond[1]));
                                    break;

                                case '!=':
                                    if ( trim($row[$key]) == trim($fieldCond[1])) $inc = false;

                                    break;
                                case 'like':
                                    if (stripos($row[$key],$fieldCond[1])===false) $inc = false;
                                    break;
                                case 'not like':

                                    if (stripos($row[$key],$fieldCond[1])!==false) $inc = false;
                                    break;
                            }

                            if(!$inc) break;  // if there is a condition with false, break the loop.
                        }

                        // Add Row if $inc is true
                        if($inc) {
                            if(is_array($fields)) {
                                $row = array();
                                foreach ($fields as $key=>$field) {
                                    $row[$field] = isset($this->queryResults[$cubeId]['data'][$i][$field])?$this->queryResults[$cubeId]['data'][$i][$field]:$field. ' does not exist';
                                }
                            }
                            $ret[] = $row;
                        }
                    }
                }
            }
            return $ret;
        }

        /**
         * Return a multipart info taking a cube.
         * @param $cubeId
         * @param $op string $op could be: select(default)|select distinct|fields|sum|count|count distinct
         * @param $fields string
         * @param $cond null|array
         * @return array|bool|int|string|null
         */
        function getCubeSubData($cubeId, $op='data', $fields = '*', $cond = null)
        {
            $data = $this->getCube($cubeId,$fields,$cond);
            if(!is_array($data)) return $data;

            switch (trim(strtolower($op))) {
                case'fields':
                    return array_keys($data[0]);
                    break;
                case'data':
                    return $data;
                    break;
                case'distinct data':
                    $distinct = array();
                    $distinctHash = array();

                    for ($i = 0, $tr = count($data); $i < $tr; $i++) {
                        if(!isset($distinctHash[sha1(serialize($data[$i]))])) {
                            $distinctHash[sha1(serialize($data[$i]))] = true;
                            $distinct = $data[$i];
                        }
                    }
                    return $distinct;
                    break;
                case'values':
                    $values = array();
                    for ($i = 0, $tr = count($data); $i < $tr; $i++) {
                        $values[] = array_values($data[$i]);
                    }
                    return $values;
                    break;
                case'distinct values':
                    $distinct = array();
                    $distinctHash = array();
                    for ($i = 0, $tr = count($data); $i < $tr; $i++) {
                        if(!isset($distinctHash[sha1(serialize($data[$i]))])) {
                            $distinctHash[sha1(serialize($data[$i]))] = true;
                            $distinct[] = array_values($data[$i]);
                        }
                    }
                    return $distinct;
                    break;
                case'group values':
                    $values = array();
                    for ($i = 0, $tr = count($data); $i < $tr; $i++) {
                        $values[] = implode(',',array_values($data[$i]));
                    }
                    return $values;
                    break;
                case'distinct group values':
                    $distinct = array();
                    $distinctHash = array();
                    for ($i = 0, $tr = count($data); $i < $tr; $i++) {
                        if(!isset($distinctHash[sha1(serialize($data[$i]))])) {
                            $distinctHash[sha1(serialize($data[$i]))] = true;
                            $distinct[] = implode(',',array_values($data[$i]));
                        }
                    }
                    return $distinct;
                    break;
                case'sum':
                    //TODO: fix sum option of getCubeSubData
                    $ret = 0;
                    for ($i = 0, $tr = count($data); $i < $tr; $i++) {
                        $ret += array_sum($data[$i]);
                    }
                    return $ret;
                    break;
                case'count distinct':
                    $distinct = array();
                    for ($i = 0, $tr = count($data); $i < $tr; $i++) {
                        $distinct[sha1(serialize($data[$i]))]+=1;
                    }
                    return(count($distinct));
                    break;
                case'count':
                    return(count($data));
                    break;
                default:
                    return('"'.$op. '" is not a valid parameter');
            }
        }
        /**
         * @param $cubeId
         * @param $op string (distinct|count|sum)
         * @param $field
         * @param null $row (string|array($field[,'order asc|desc'])
         * @param null $col (string|array($field[,'order asc|desc'])
         * @param null $cond
         * @return array|bool
         */
        function getCubeSubDataGroup($cubeId, $op, $field, $row=null, $col=null, $cond=null){
            $data = $this->getCube($cubeId,'*',$cond);
            if(!is_array($data)) return $data;

            $field = array($field,$op);
            if(!is_array($row)) $row = array((strlen($row))?$row:'_row_');
            if(!is_array($col)) $col = array((strlen($col))?$col:'_col_');

            $rowFieldContent = $row[0];
            $colFieldContent = $col[0];

            $ret = array();
            $retRows = array();
            $retCols = array();
            $retFields = array();
            $distinctFields = array();

            // Preparing data in the first Loop
            for ($i = 0, $tr = count($data); $i < $tr; $i++) {
                // ROW/COL for the field
                if($row[0]!='_row_')
                    $rowFieldContent = (isset($data[$i][$row[0]]) && strlen($data[$i][$row[0]]))?$data[$i][$row[0]]:'_empty_';

                if($col[0]!='_col_')
                    $colFieldContent = (isset($data[$i][$col[0]]) && strlen($data[$i][$col[0]]))?$data[$i][$col[0]]:'_empty_';

                $value = (isset($data[$i][$field[0]]) && strlen($data[$i][$field[0]]))?$data[$i][$field[0]]:'_empty_';

                switch ($field[1]) {
                    case 'count':
                        $ret[$rowFieldContent][$colFieldContent] += 1;
                        $retRows[$rowFieldContent]+= 1;
                        $retCols[$colFieldContent]+= 1;
                        $retFields['count_' .$field[0]]+=1;
                        break;
                    case 'sum':
                        $ret[$rowFieldContent][$colFieldContent] += $value;
                        $retRows[$rowFieldContent]+= $value;
                        $retCols[$colFieldContent]+= $value;
                        break;
                    case 'distinct':
                    default:
                        if(!isset($distinctFields[$value])) {
                            $ret[$rowFieldContent][$colFieldContent][] = $value;
                            $distinctFields[$value] += 1;
                            $retRows[$rowFieldContent][]= $value;
                            $retCols[$colFieldContent][]= $value;
                        }
                        break;
                }
            }

            // Transform return data based in the info collected.
            // Potential order
            if($col[0]!='_col_' && isset($col[1]) && stripos($col[1],'order ')!==false)
                if(stripos($col[1],' asc')!==false) ksort($retCols);
                else if(stripos($col[1],' desc')!==false) krsort($retCols);

            if($row[0]!='_row_' && isset($row[1]) && stripos($row[1],'order ')!==false)
                if(stripos($row[1],' asc')!==false) ksort($retRows);
                else if(stripos($col[1],' desc')!==false) krsort($retRows);

            $retGroup = ['data'=>[],'rows'=>[],'cols'=>[]];
            // Values Data
            if($row[0]!='_row_')
                $retGroup['rows'] = array_keys($retRows);
            if($col[0]!='_col_')
                $retGroup['cols'] = ($col[0]!='_col_')?array_keys($retCols):'';
            $i=0;
            foreach ($retRows as $row=>$rowValue) {
                foreach ($retCols as $col=>$colValue) {
                    if($row != '_row_') {
                        if($col !='_col_') {
                            $retGroup['data'][$row][$col] = $ret[$row][$col];
                        } else {
                            $retGroup['data'][$row] = $ret[$row][$col];
                        }
                    } elseif($col !='_col_') {
                        $retGroup['data'][$col] = $ret[$row][$col];
                    } else {
                        $retGroup['data'] = $ret[$row][$col];
                    }
                }
                $i++;
            }
            return($retGroup);

        }

        /**
         * @param $cubeId
         * @param $field
         * @param null $row
         * @param null $col
         * @param null $cond
         * @return array|bool
         */
        function queryDataExplore($cubeId, $field, $row=null, $col=null, $cond=null){
            $data = $this->getCube($cubeId,'*',$cond);
            if(!is_array($data)) return $data;

            if(!is_array($field)) $field = array($field,'distinct');
            if(!is_array($row)) $row = array((strlen($row))?$row:'_row_');
            if(!is_array($col)) $col = array((strlen($col))?$col:'_col_');

            $rowFieldContent = $row[0];
            $colFieldContent = $col[0];

            $ret = array();
            $retRows = array();
            $retCols = array();
            $retFields = array();
            $distinctFields = array();

            // Preparing data in the first Loop
            for ($i = 0, $tr = count($data); $i < $tr; $i++) {
                // ROW/COL for the field
                if($row[0]!='_row_')
                    $rowFieldContent = (isset($data[$i][$row[0]]) && strlen($data[$i][$row[0]]))?$data[$i][$row[0]]:'_empty_';

                if($col[0]!='_col_')
                    $colFieldContent = (isset($data[$i][$col[0]]) && strlen($data[$i][$col[0]]))?$data[$i][$col[0]]:'_empty_';

                $value = (isset($data[$i][$field[0]]) && strlen($data[$i][$field[0]]))?$data[$i][$field[0]]:'_empty_';

                switch ($field[1]) {
                    case 'distinct':
                        if(!isset($distinctFields[$value])) {
                            $ret[$rowFieldContent][$colFieldContent][] = $value;
                            $distinctFields[$value] += 1;
                            $retRows[$rowFieldContent][]= $value;
                            $retCols[$colFieldContent][]= $value;
                        }
                        break;
                    case 'count':
                        $ret[$rowFieldContent][$colFieldContent] += 1;
                        $retRows[$rowFieldContent]+= 1;
                        $retCols[$colFieldContent]+= 1;
                        $retFields['count_' .$field[0]]+=1;

                        break;
                    default:
                        $ret[$rowFieldContent][$colFieldContent] += $value;
                        $retRows[$rowFieldContent]+= $value;
                        $retCols[$colFieldContent]+= $value;
                        break;
                }
            }

            // Transform return data based in the info collected.
            // Potential order
            if($col[0]!='_col_' && isset($col[1]) && stripos($col[1],'order ')!==false)
                if(stripos($col[1],' asc')!==false) ksort($retCols);
                else if(stripos($col[1],' desc')!==false) krsort($retCols);

            if($row[0]!='_row_' && isset($row[1]) && stripos($row[1],'order ')!==false)
                if(stripos($row[1],' asc')!==false) ksort($retRows);
                else if(stripos($col[1],' desc')!==false) krsort($retRows);

            // Values Data
            $retGroup = array();
            if($row[0]!='_row_')
                $retGroup[1] = array_keys($retRows);
            if($col[0]!='_col_')
                $retGroup[2] = ($col[0]!='_col_')?array_keys($retCols):'';
            $i=0;
            foreach ($retRows as $row=>$rowValue) {
                foreach ($retCols as $col=>$colValue) {
                    if($row != '_row_') {
                        if($col !='_col_') {
                            $retGroup[0][$i][] = $ret[$row][$col];
                        } else {
                            $retGroup[0][] = $ret[$row][$col];
                        }
                    } elseif($col !='_col_') {
                        $retGroup[0][] = $ret[$row][$col];
                    } else {
                        $retGroup[0] = $ret[$row][$col];
                    }
                }
                $i++;
            }
            return($retGroup);

        }


        /**
         * @param $idMatix
         * @param $data array from with the following format: [0..n][field1=>value1,field2=>value2...]
         */
        function matrixReportInit($idMatix, $data) { $this->matrixReports =[$idMatix=>['data'=>$data,'rows'=>[],'cols'=>[],'values'=>[]]]; }
        function matrixReportSetData($idMatix, $data) {
            $this->matrixReports[$idMatix]['data'] = $data;
        }


        /**
         * @param $idMatix
         * @param $type string 'rows'|'cols' else ->'cols'
         * @param $field
         * @param array $props ['order'=>'asc'|'desc']
         */
        function matrixReportAdd($idMatix, $type, $field, $props=[]) {
            $this->_matrixReportData('add',$idMatix, $type, $field, $props);
        }
        function matrixReportSet($idMatix, $type, $field, $props=[]) {
            $this->_matrixReportData('set',$idMatix, $type, $field, $props);
        }
        function _matrixReportData($action,$idMatix, $type, $field, $props=[]) {

            // Normalizing $type names
            $type=strtolower($type).'s';
            if($type!='rows' && $type!='values' && $type!='data') $type='cols';

            // Reset Matrix
            if($action=='set')
                unset($this->matrixReports[$idMatix][$type]);

            $this->matrixReports[$idMatix][$type][$field] = $props;
        }


        // Recursive functions to explore data in matrixReportData;
        function recursiveCols(&$output,&$colData,&$props,$ncols,$nrows,$nvalues,$ncol=0,$x=0,$stringIndex='') {
            if($nvalues==0) $nvalues = 1;
            $first = ($x===0);

            // Sort Columns
            if(isset($props[$ncol]['order']))
                if(stripos($props[$ncol]['order'],'desc')!==false) krsort($colData);
                else ksort($colData);

            foreach ($colData as $key=>$item) {
                if($x==0)  for(;$x<$nrows;$x++) $output[$ncol][$x] = '';
                $output[$ncol][$x] = array_merge(array('value'=>$key,'stringIndex'=>((strlen($stringIndex))?$stringIndex.'|f|':'').$key),$props[$ncol]);
                $lastX=$x;
                if($ncol+1 < $ncols)
                    $x = $this->recursiveCols($output,$colData[$key],$props,$ncols,$nrows,$nvalues,$ncol+1,($first)?0:$x,((strlen($stringIndex))?$stringIndex.'|f|':'').$key);
                else $x+=$nvalues;
                if($x-$lastX>1) {
                    $output[$ncol][$lastX]['colspan'] = $x-$lastX;
                }
                $first = false;
            }
            return $x;
        }

        // Recursive functions to explore data in matrixReportData;
        function recursiveRows(&$output, &$rowData, &$props, $ncols, $nrows, $nvalues, $nrow=0, $y=0,$stringIndex='') {
            if($nvalues==0) $nvalues = 1;
            $first = ($y===0);

            // Sort Columns
            if(isset($props[$ncol]['order']))
                if(stripos($props[$nrow]['order'],'desc')!==false) krsort($rowData);
                else ksort($rowData);

            foreach ($rowData as $key=> $item) {
                if($y==0)  $y=$ncols; // Start the index under the cols
                if(isset($props[$nrow]['link'])) {
                    $props[$nrow]['link'] = $this->super->applyVarsSubsitutions($props[$nrow]['link'],array_map(urlencode,['self'=>$key]));
                }
                $output[$y][$nrow] = array_merge(array('value'=>$key,'stringIndex'=>((strlen($stringIndex))?$stringIndex.'|f|':'').$key),$props[$nrow]);
                $lastY=$y;
                if($nrow+1 < $nrows)
                    $y = $this->recursiveRows($output,$rowData[$key],$props,$ncols,$nrows,$nvalues,$nrow+1,($first)?0:$y,((strlen($stringIndex))?$stringIndex.'|f|':'').$key);
                else $y++;
                //
                if($y-$lastY>1) {
                    $output[$lastY][$nrow]['rowspan'] = $y-$lastY;
                    // Summarize Row is to add a new row
                    // Add a new Row if there is a summarize
                    if(isset($props[$nrow]['summarize'])) {
                        $output[$y][$nrows-1] = array('rowSummarize'=>$props[$nrow]['summarize'],'colspan'=>$nrows-$nrow,'value'=>'Total '.$key,'rowBold'=>'true','bold'=>'true','stringIndex'=>((strlen($stringIndex))?$stringIndex.'|f|':'').$key);
                        $y++;
                    }
                }
                $first = false;
            }

            return $y;
        }

        function matrixReportData($idMatix) {
            // Return if empty
            if(!isset($this->matrixReports[$idMatix]['data']) || !is_array($this->matrixReports[$idMatix]['data'])) return false;
            else if(!count($this->matrixReports[$idMatix]['data'])) return [];

            // Analyzing Rows && Cols
            if(!count($this->matrixReports[$idMatix]['cols'])) $this->matrixReports[$idMatix]['cols']['_col_'] = [];
            if(!count($this->matrixReports[$idMatix]['rows'])) $this->matrixReports[$idMatix]['rows']['_row_'] = [];
            if(!count($this->matrixReports[$idMatix]['values'])) $this->matrixReports[$idMatix]['values']['_value_'] = [];

            // Building basic structure:
            $propData = ['props'=>[],'cols'=>[],'rows'=>[],'values'=>[],'linkData'=>['cols'=>[],'rows'=>[],'values'=>[]]];
            $propData['props']['cols'] = array_values($this->matrixReports[$idMatix]['cols']);
            $propData['props']['rows'] = array_values($this->matrixReports[$idMatix]['rows']);
            $propData['props']['values'] = array_values($this->matrixReports[$idMatix]['values']);

            foreach ($this->matrixReports[$idMatix]['data'] as $index=>$dataRecord) {
                $rowName = '';
                // Cols & Rows & Values Structure
                // ROWS
                $rowNames = &$propData['rows'];
                $rowLinks = &$propData['linkData']['rows'];
                $lastRowName = '';
                foreach ($this->matrixReports[$idMatix]['rows'] as $rowfield=>$rowProps) {
                    $rowName='';
                    if($rowfield!='_row_') {
                        if (!array_key_exists($rowfield,$dataRecord)) $rowName = '_nofieldvalue_';
                        else $rowName = (strlen($dataRecord[$rowfield])) ? $dataRecord[$rowfield] : '_empty_';
                        if(!isset($rowNames[$rowName])) {
                            // Add the last record if we want to use smart-links
                            $rowLinks[$rowName] = json_encode($dataRecord);
                            // Initialize the row element as array
                            $rowNames[$rowName] = array();
                        }
                        $rowNames = &$rowNames[$rowName];
                    }

                    $lastRowName .= ((strlen($lastRowName))?'|f|':'').$rowName;
                    // COLS
                    $colNames = &$propData['cols'];
                    $colLinks = &$propData['linkData']['cols'];
                    $lastColName = '';
                    foreach ($this->matrixReports[$idMatix]['cols'] as $colField=>$colProps) {
                        $colName='';
                        if($colField!='_col_') {
                            if (!array_key_exists($colField,$dataRecord)) $colName = '_nofieldvalue_';
                            else $colName = (strlen($dataRecord[$colField])) ? $dataRecord[$colField] : '_empty_';

                            if (!isset($colNames[$colName])) {
                                // Add the last record if we want to use smart-links
                                $colLinks[$colName] = json_encode($dataRecord);
                                // Initialize the col element as array
                                $colNames[$colName] = array();
                            }
                            $colNames = &$colNames[$colName];
                        }
                        $lastColName .= ((strlen($lastColName))?'|f|':'').$colName;


                        $valDatas =  &$propData['values'];
                        $valLinks =  &$propData['linkData']['values'];
                        foreach ($this->matrixReports[$idMatix]['values'] as $valueField=>$valueProps) if($valueField!='_value_') {
                            if (!array_key_exists($valueField,$dataRecord)) $valData = '_nofieldvalue_';
                            else $valData = (strlen($dataRecord[$valueField])) ? $dataRecord[$valueField] : '_empty_';

                            // Add the last record if we want to use smart-links
                            $valLinks[$lastColName.'|_|'.$lastRowName][$valueField] = json_encode($dataRecord);
                            // Add list of values for each cell
                            $valDatas[$lastColName.'|_|'.$lastRowName][$valueField][] = $valData;
                        }
                    }
                }
            }



            // building output structure
            // Cols & Rows
            $ncols = (isset($this->matrixReports[$idMatix]['cols']['_col_']))?0:count($this->matrixReports[$idMatix]['cols']);
            $nrows = (isset($this->matrixReports[$idMatix]['rows']['_row_']))?0:count($this->matrixReports[$idMatix]['rows']);
            $nvalues = (isset($this->matrixReports[$idMatix]['values']['_value_']))?0:count($this->matrixReports[$idMatix]['values']);

            $matrixData = array();
            if($ncols) $this->recursiveCols($matrixData,$propData['cols'],$propData['props']['cols'],$ncols,$nrows,$nvalues);
            if($nrows) $this->recursiveRows($matrixData,$propData['rows'], $propData['props']['rows'],$ncols, $nrows,$nvalues);

            // Values
            if($nvalues) {
                // Get Keys combinations of cols&rows
                // Get Last level of Cols
                if($ncols>0) {
                    $colKeys = $matrixData[$ncols-1];
                    for($i=0;$i<$nrows;$i++) unset($colKeys[$i]);
                    foreach ($colKeys as $colKeyIndex=>$colKeyValues) {
                        $colKeys[$colKeyIndex] = $colKeyValues['stringIndex'];
                    }
                } else $colKeys = array($nrows=>'');


                // Get Last level of Rows
                if($nrows>0) {
                    for($i=$ncols,$tr=count($matrixData);$i<$tr;$i++)
                        $rowKeys[$i] = $matrixData[$i][$nrows-1]['stringIndex'];
                } else $rowKeys = array($ncols=>'');

                foreach ($rowKeys as $rowIndex=>$rowKey) {

                    // is this row a summary row
                    $rowSummary = false;
                    if(isset($matrixData[$rowIndex][1]['rowSummarize'])) {
                        $rowSummary = $matrixData[$rowIndex][1]['rowSummarize'];
                    }

                    // Fill matrix values cells
                    foreach ($colKeys as $colIndex=>$colKey) {
                        $i=0;
                        foreach ($this->matrixReports[$idMatix]['values'] as $valueField=>$valueProps) {

                            $summary = 'raw';
                            // Summary comes from rowSummary summarize property
                            if(false !== $rowSummary) {
                                if(is_array($rowSummary)) $summary = $rowSummary[$i];
                                else $summary = $rowSummary;

                            //  Summary comes value summarize property
                            } elseif(isset($valueProps['summarize'])){
                                $summary = $valueProps['summarize'];
                            }

                            if(!isset($valueProps['summarize'])) $valueProps['summarize'] = 'raw';

                            switch($summary) {
                                case "sum":
                                    if(!is_array($propData['values'][$colKey.'|_|'.$rowKey][$valueField]))
                                        $value='';
                                    else
                                        $value = array_sum($propData['values'][$colKey.'|_|'.$rowKey][$valueField]);
                                    break;
                                case "max":
                                case "min":
                                    if(!is_array($propData['values'][$colKey.'|_|'.$rowKey][$valueField]))
                                        $value='';
                                    else {
                                        if($summary=='max') rsort($propData['values'][$colKey . '|_|' . $rowKey][$valueField]);
                                        else sort($propData['values'][$colKey . '|_|' . $rowKey][$valueField]);
                                        $value = $propData['values'][$colKey . '|_|' . $rowKey][$valueField][0];
                                    }
                                    break;
                                case "count":
                                    $value = count($propData['values'][$colKey.'|_|'.$rowKey][$valueField]);
                                    break;
                                default:
                                    $value = $propData['values'][$colKey.'|_|'.$rowKey][$valueField];
                                    break;
                            }
                            $matrixData[$rowIndex][$colIndex+$i]['value'] = $value;
                            $matrixData[$rowIndex][$colIndex+$i] = array_merge($matrixData[$rowIndex][$colIndex+$i],$propData['props']['values'][$i]);

                            // If all the rowBold is
                            if($matrixData[$rowIndex][$nrows-1]['rowBold'])  $matrixData[$rowIndex][$colIndex+$i]['bold'] = true;

                            if($matrixData[$rowIndex][$colIndex+$i]['link']) {
                                $cellData = json_decode($propData['linkData']['values'][$colKey.'|_|'.$rowKey][$valueField],true);
                                if(!is_array($cellData))
                                    unset($matrixData[$rowIndex][$colIndex+$i]['link']);
                                else
                                    $matrixData[$rowIndex][$colIndex+$i]['link'] =  $this->super->applyVarsSubsitutions($matrixData[$rowIndex][$colIndex+$i]['link'],array_map(urlencode,$cellData));
                            }
                            $i++;
                        }
                    }
                }


            }

            return ($matrixData);
        }



        /**
         * @param $cubeId
         * @param $fields
         * @param $rows
         * @param $cols
         * @param $cond
         * @return array or false in error case.
         */
        function queryDataGroup($cubeId, $fields, $rows=null, $cols=null, $cond=null)
        {
            $data = $this->getCube($cubeId,$fields,$cond);
            if(!is_array($data)) return $data;

            if (!is_array($fields)) $fields = explode(",", trim($fields));
            if (!is_array($rows)) $rows = explode(",", trim($rows));
            if (!is_array($cols)) $cols = explode(",", trim($cols));

            if (!is_array($rows[0]) && !strlen($rows[0])) $rows = array('_row_');
            if (!is_array($cols[0]) && !strlen($cols[0])) $cols = array('_col_');

            $ret = array();
            $retRows = array();
            $retCols = array();
            $retFields = array();
            $retRowSummary = array();
            $retColSummary = array();

            // Analyzing Summaries when the cols or rows array arrays with properties
            foreach ($rows as $ind => $key) if(is_array($key))  {
                $retRowSummary[$key[0]] = $key[1];
                $rows[$ind] = $key[0];
            }
            foreach ($cols as $ind => $key) if(is_array($key))  {
                $colSummary[$key[0]] = $key[1];
                $cols[$ind] = $key[0];
            }

            // Preparing data in the first Loop
            for ($i = 0, $tr = count($data); $i < $tr; $i++) {
                $row = '';
                foreach ($rows as $ind => $key) {

                    $rowFieldContent = (isset($data[$i][$key])) ? $data[$i][$key] : $key;
                    $row .= ($row) ? '_' . $rowFieldContent : $rowFieldContent;
                    foreach ($fields as $ind2 => $field) {
                        if (!is_array($field)) $field = array(trim($field), 'sum');
                        $col = '';
                        foreach ($cols as $ind3 => $key3) {


                            $colFieldContent = (isset($data[$i][$key3])) ? $data[$i][$key3] : $key3;
                            $col .= ($col) ? '_' . $colFieldContent : $colFieldContent;
                            switch ($field[1]) {
                                case 'count':
                                    $ret[$row][$col]['count_' . $field[0]] += 1;
                                    $retRows[$row][$field[0]] += 1;
                                    $retCols[$col][$field[0]] += 1;
                                    $retFields['count_' . $field[0]] += 1;

                                    break;
                                case 'sum':
                                default:
                                    $ret[$row][$col][$field[0]] += $data[$i][$field[0]];
                                    $retRows[$row][$field[0]] += $data[$i][$field[0]];
                                    $retCols[$col][$field[0]] += $data[$i][$field[0]];
                                    $retFields[$field[0]] += $data[$i][$field[0]];
                                    break;
                            }
                        }
                    }

                }
            }

            // Building array to allow its representation
            $retGroup = array();
            $colSpan = count($retFields);
            foreach ($retRows as $row => $rowValue) {
                $currentRow = array();

                // Header of the report
                if (!count($retGroup)) {
                    if ($row != '_row_') $currentRow[] = '';
                    foreach ($retCols as $col => $colValue) if ($col != '_col_') {
                        $currentRow[] = array('value' => $col, 'colspan' => $colSpan, 'align' => 'center', 'bold' => true);
                    }
                    if (count($currentRow) > 1) {

                        $retGroup[] = $currentRow;
                    }
                    $currentRow = array();
                }

                // Row Content
                if ($row != '_row_') $currentRow[] = array('value' => $row, 'bold' => true);;
                foreach ($retCols as $col => $colValue) {
                    $i = 0;
                    foreach ($retFields as $field => $fieldValue) {
                        $currentRow[] = array('value' => $ret[$row][$col][$field], 'align' => 'right', 'currency' => (is_array($fields[$i])) ? $fields[$i][2] : '');
                        $i++;
                    }
                }
                $retGroup[] = $currentRow;
            }

            // Row Summary
            if (count($retColSummary)) {
                $currentRow = array(array(value=>''));
                foreach ($retCols as $col => $colValue) {
                    $i = 0;
                    foreach ($retFields as $field => $fieldValue) {
                        $currentRow[] = array('value' => $retCols[$col][$field], 'bold' => true, 'align' => 'right', 'currency' => (is_array($fields[$i])) ? $fields[$i][2] : '');
                        $i++;
                    }
                }
                $retGroup[] = $currentRow;

            }


            return($retGroup);
        }

        /**
         * @param $type
         * @param string $info
         */
        function add($type,$info='') {
            //if(is_array($info)) _printe((object)$info);
            switch ($type) {
                case 'row':
                case 'col':
                    if($info=='') $info=12;
                    if($info!='') $this->data[] = array('type'=>$type,'data'=> $info);
                    break;
                case 'pre':
                    $this->data[] = array('type'=>$type,'data'=> $info);
                    break;
                case 'markdown':
                    $this->data[] = array('type'=>$type,'data'=> $info);
                    break;
                default:
                    $this->data[] = array('type'=>$type,'data'=> is_array($info)?(object)$info:json_decode($info));
                    break;
            }
        }

        /**
         * return the HTML with the info printed closing opened database connections.
         */
        function output() {
        	global $adnbp;

            // Close Data base it it has been opened.
            $this->queryEnd();



            $controlVars = (object)array('dygraph'=>false,'tables'=>false,'reportNumber'=>0);
			$types = array('barcode'=>false);
			$_tables = false;
            $rows='';
            $cols='';
            $container=false;
            $lastColSize=12;
            ob_start();
            echo '<section id="widget-grid" >';
            if(count($this->filters)) {
                include __DIR__.'/templates/filters.php';
            }
            foreach ($this->data as $key => $data) {
                $controlVars->reportNumber++;
                $type = $data['type'];
                $data = $data['data'];
                if(isset($data->columns) && is_string($data->columns)) $data->columns = explode(',',$data->columns);
                if($type=='header')
                    include __DIR__.'/templates/header.php';
                elseif($type=='markdown') {
                    if (!class_exists('Parsedown')) {
                        include_once __DIR__.'/Parsedown.php';
                    }
                    echo Parsedown::instance()->text($data);
                }elseif($type=='table') {
                	$simple = false;
                    include __DIR__.'/templates/table.php';
					$_tables = true;
                }
                elseif($type=='simpleTable') {
                	$simple = true;
                    include __DIR__.'/templates/table.php';
					$_tables = true;
                }
                elseif($type=='tree') {
                    $simple = true;
                    include __DIR__.'/templates/tree.php';
                    $_tables = true;
                }elseif($type=='form') {
                    include __DIR__.'/templates/form.php';
                }
                elseif($type=='inlineChart') {
                    if(is_array($data->values)) $data->values = implode(',',array_values($data->values));
                    include __DIR__.'/templates/inline.php';
                }
				elseif($type=='barcode' ) {
					
					_printe($data->data);
					$barcode = array('element'=>'barcode'.$key);
					
                    include __DIR__.'/templates/barcode.php';
                }
                elseif($type=='btnInfo' || $type=='buttoninfo') {
                    if($list) echo "<li>";
                    include __DIR__.'/templates/btnInfo.php';
                    echo "</li>";
                }
                elseif($type=='container') {
                    if($container) include __DIR__.'/templates/container.php';

                    $container=false;
                    include __DIR__.'/templates/container.php';
                    $container=true;
                }
                elseif($type=='dygraph') {
                    if(is_array($data->rows)) {
                        if(!isset($data->rows[1]) || !is_array($data->rows[1])) $data->rows[1][]='Info';
                        if(!isset($data->rows[2]) ||!is_array($data->rows[2])) $data->rows[2][]=date('Ymd');
                        if(is_array($data->rows[0]) && !is_array($data->rows[1][0]) && !is_array($data->rows[2][0])) {
                            if(!is_array($data->rows[0][0])) $data->rows[0] = array($data->rows[0]);
                            include __DIR__ . '/templates/dygraph.php';

                        } else $this->addError('Wrong dygraph data');
                    }

                }
                elseif($type=='row') {
                    if($container) include __DIR__.'/templates/container.php';
                    $container=false;
                    echo $cols; $cols='';
                    echo $rows."<div class='row'>\n";
                    $rows="\n</div> <!-- row -->";
                    if($data=="center") {
                         echo "<center>";
                        $rows = "</center>".$rows;
                    }
                } elseif($type=='col') {
                    if($container) include __DIR__.'/templates/container.php';
                    $container=false;
                    echo $cols;
                    echo '    <div class="col-md-'.$data.'">';
                    $cols="\n    </div> <!-- col -->";
                    $lastColSize = $data;
                } elseif($type=='pre'){
                    echo "<pre>{$data}</pre>";
                }
            }
            if($container) include __DIR__.'/templates/container.php';
            echo $list.$cols.$rows;
            echo "</section>";
			//include __DIR__.'/templates/tablejsbottom.php';
            return ob_get_clean();

        }
        function formFieldHtml($data) {
            $ret = '';
            $extra='';
            switch(strtolower($data['type'])) {
                case 'text':
                    $data['value'] = htmlentities($data['value']);
                    $ret = "<input type='text' id='{$data['id']}' placeholder='{$data['placeholder']}' value='{$data['value']}'>";
                    break;
                case 'imageurl':
                    if(isset($data['width'])) $extra.= " width='{$data['width']}'";
                    if(isset($data['height'])) $extra.= " width='{$data['height']}'";
                    $ret = "<img id='{$data['id']}' src='{$data['value']}' {$extra}>";

                    break;

            }
            return $ret;
        }
        /**
         * @param $errorMsg error message to set.
         */
        function setError($errorMsg) {
            $this -> errorMsg = array();
            $this->addError($errorMsg);
        }

        /**
         * @param $errorMsg error messageto add.
         */
        function addError($errorMsg) {
            $this -> error = true;
            $this -> errorMsg[] = $errorMsg;            
        }

        /**
         * @param $errorMsg error messageto add.
         */
        function getArray($type,$data) {
            $ret = [];
            switch ($type) {
                case "dates":
                case "yearmonth":

                    if($type=='dates') $fortmat = ['date'=>'Y-m-d','inctype'=>'D'];
                    else if($type=='yearmonth') $fortmat = ['date'=>'Y-m','inctype'=>'M'];

                    $init = ($data['init'])?$data['init']:'now';
                    $end = ($data['end'])?$data['end']:'now';
                    $inc = ($data['inc'])?$data['inc']:1;
                    $value = (isset($data['asvalue']))?$data['asvalue']:null;

                    if($init=='now') $init = date('Y-m-d');
                    if($end=='now') $end = date('Y-m-d');

                    $init = new DateTime($init);
                    $i = new DateInterval("P{$inc}".$fortmat['inctype']);
                    $end = new DateTime($end);

                    // Get last day in the iteration
                    if($type=='dates')
                        $end->add($i);

                    $period = new DatePeriod(
                        $init,
                        $i ,
                        $end
                    );
                    foreach( $period as $date) {
                        if(null!== $value)
                            $ret[$date->format($fortmat['date'])] = $value;
                        else
                            $ret[] = $date->format($fortmat['date']);
                    }

                    // Order reverse if required
                    if(isset($data['reverse'])) {
                        if (null !== $value)
                            krsort($ret);
                        else
                            rsort($ret);
                    }
                    break;
            }
            return $ret;
        }


    }
} 