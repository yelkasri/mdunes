<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Doc_csv extends Output_Doc_Document {
public function renderContent(){
$dataA=$this->htmlObj->_data;
$elementsA=$this->htmlObj->elements;$mapListA=$this->htmlObj->mapListA;$titleHeader=WGlobals::get('titleheader');$viewTitle=$this->htmlObj->name;
$dataValues=array();
$mapName=null;
$baseValue=null;
$dataName=array();
$elementsASize=sizeof($elementsA);
$ctr=0;
$header=array();
$datas=array();
$this->numberRows=count($dataA);
$this->allDataRowsA=$dataA;
$headerTable='';
$dataTable='';
foreach($dataA as $ndxDataA=> $valueDataA){
$this->rowNumber=$ndxDataA;
foreach($elementsA as $elementsndx=> $element){
$this->complexMap=$complexMap=$element->map.'_'.$element->sid;
$this->cellValue=$valueDataA->$complexMap;
$header[]=$headerTemp=$element->name; if($ctr==0){
if(!empty($headerTemp))$headerTable .=$headerTemp;}elseif($ctr <=$elementsASize && $elementsndx !=0){
$headerTable .=", ". $headerTemp;}
$data=$this->callListingElement($element , $valueDataA );
if($element->map=='created' || $element->map=='startime' || $element->map=='registerdate' || $element->map=='modified'){
$data='"'.$data.'"';}if($elementsndx==0){
$dataTable .=str_replace(',',' ',$data);
}else{
$dataTable .=", ".str_replace(',',' ',$data);
}
$ctr++;
}$dataTable .="\n";
}
if(!empty($dataTable)){
$html=$headerTable . "\n" . $dataTable;
$fileNameTemp=$viewTitle.' from '.$titleHeader;
$fileName=str_replace(' ','_',$fileNameTemp);
ob_end_clean();
ob_start();
header('Content-type: application/vnd.ms-excel');
header('Content-disposition: attachment; filename='.$fileName.'.csv');
print $html;
exit();
}else{
$message=WMessage::get();
$html=$message->userW('1262788661GLZO');
}
return $html;
}
}
