<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_query_execute_controller extends WController {
function execute(){
$showResult=WController::getFormValue('result');
$queries=WController::getFormValue('query');
$queries=html_entity_decode($queries, ENT_QUOTES );
if(empty($queries)){
$this->userE('1425662174IZVW');
return true;
}
$sqlHandler=WClass::get('library.sql');
$queriesA=$sqlHandler->splitSql($queries );
$message=WMessage::get();
if(empty($queriesA)){
$queriesA[]=rtrim($queries, ';'). ';';
}
$dbHandler=WTable::get();
$status=true;
$queryResults=array();
foreach($queriesA as $oneQuery){
$oneQuery=trim($oneQuery);
if(!empty($oneQuery)){
$result=array(); 
$oneQuery=preg_replace('/#database_[0-9]{0,4}#/',$dbHandler->getDBName(), str_replace('#__',$dbHandler->getTablePrefix(), $oneQuery));
if(!empty($oneQuery) && strlen($oneQuery) > 5){
if($showResult)$result=$dbHandler->load('qr',$oneQuery );
else $result=$dbHandler->load('q',$oneQuery );
}
if($result===false){
$status=false;
$messageTxt=$dbHandler->getErrorMsg(). ' '.$oneQuery;
$message->userE($messageTxt );
continue;
}
}
if($showResult){
if($result && !empty($dbHandler->_result ))$result=$dbHandler->_result;
$queryResults[$oneQuery]=$result;
}
}
if(!empty($queryResults)){
foreach($queryResults as $query=> $result){
if($result!==true){
$txt=$query.'<br /><br />'.$this->_explodeObject($result );
$message->userS($txt );
}
}
}
if($status){
$message->userS('1229651871ADBG');
}else{
$message->userW('1229651871ADBH');
}
return true;
}
private function _explodeObject($object){
if(!empty($object ) && ( is_object($object ) || is_array($object ))){
$string='';
foreach($object as $name=> $property){
$string .='<tr border="1" style="border-bottom: 1px solid grey; "><td >'.$name.'</td><td>'.$this->_explodeObject($property ).'<tr/>';
}
return '<table cellpadding="2px" style="border: 1px solid grey; ">'.$string.'</table>';
}
return (string)$object;
}
}