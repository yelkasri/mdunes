<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_Apps_user_logdetails_view extends Output_Listings_class {
function prepareQuery(){
$file=WGlobals::get('file');  
$path=JOOBI_DS_USER.'logs'.DS.$file;
$fileClass=WGet::file();
$size=$fileClass->size($path);
if($size!=0 && $size <=1048576){
$data_in_the_file=$fileClass->read($path );
$row_data=explode( "## End ##\r\n" , $data_in_the_file );
$objData=array();
if(!empty($row_data)){
$i=0;
foreach($row_data as $single_data){
$single_data=trim($single_data);
if(!empty($single_data)){
$i++;
$databox=explode(' ***/',$single_data, 2 );
$header=str_replace( array('/*** Start',' * ', "\r\n" ), array('','', "<br />" ), $databox[0] );
$date='';
$datePos=strpos($header, '( date:');
if($datePos){
$date=substr($header, $datePos, strpos($header, ' '.')')-$datePos );
$header=str_replace($date.' '.')','',$header );
$date=substr($date, 7 );
}
$objTest=new stdClass;
$objTest->nb=$i;
$objTest->date=$date;
$objTest->location=$header; 
if(!empty($databox[1]))$objTest->details=str_replace("\r\n", "<br />", $databox[1] );else $objTest->details='';
$objData[]=$objTest;
}
}
}
$this->addData($objData );
}else{
$warnings=WMessage::get();
$warnings->userE('1260443578NNAV');
WPages::redirect('controller=apps-logs&task=listing');
}
return true;
}}