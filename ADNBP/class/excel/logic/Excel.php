<?php

$this->loadClass("excel/PHPExcel");
$_objExcel = new PHPExcel();

$_objExcel->getProperties()
   ->setCreator('Hector López')
   ->setTitle('PHPExcel Demo')
   ->setLastModifiedBy('Hector López')
   ->setDescription('ADNBP CloudFrameWork demo to show how to create Excel files')
   ->setSubject('CloudFrameWork Excel export')
   ->setKeywords('excel php office phpexcel ')
   ->setCategory('CloudFrameWork Excel')
   ;
   
$ews = $_objExcel->getSheet(0);
$ews->setTitle('Page Output');

if(isset($_POST['export']) && strlen($_POST['filename']) && strlen($_POST['data'])) {
    $lines = explode("\n", $_POST['data']);
    $dataExcel= array();
    for ($i=0,$tr=count($lines); $i < $tr; $i++) {
        $dataExcel[] = explode(',',trim($lines[$i])); 
    }
    
    //Header
    $ews->fromArray($dataExcel, ' ', 'A1');
    $header = 'a1:'.(chr((ord('a')+count($dataExcel[0])-1))).'1'; // a1:c1
    
    $ews->getStyle($header)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00ffff00');
    $style = array(
        'font' => array('bold' => true,),
        'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
        );
    $ews->getStyle('a1:c1')->applyFromArray($style);
    
    
    // We'll be outputting an excel file
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="'.$_POST['filename'].'"');
    $objWriter = PHPExcel_IOFactory::createWriter($_objExcel, 'Excel2007');
    $objWriter->save('php://output');
    die();
}

