<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_Export_class extends WClasses {
var $_transSQL=null;
var $wid=0;
function transExtension($dbtid,$main_pkey,$ids){static $instance=null;
if(!isset($instance)){
$instance=WModel::get('translation.en');
}$this->_transSQL=&$instance;
if(!empty($ids)){
foreach( array_keys(get_object_vars(reset($ids))) as $field){
if($field=='lgid'||$field==$main_pkey)continue;
foreach($ids as $id){
$trID=null;
$transate=$id->$field;
if($this->_searchExistingTranslation($transate, $trID )){
}else{
$trID=$this->_addToVocabulary($transate );
}$this->_insertCross($trID, $dbtid, $id->$main_pkey, $field );
}
}}
}
function _addToVocabulary($text){
$key=$this->genVocabKey($text );
$this->_transSQL->setVal('text',$text);
$this->_transSQL->setVal('imac',$key);
$this->_transSQL->insert();
$object=new stdClass;
$object->imac=$key;
return $object;
}
private function _toAlphaNumber($num){
$anum='';
while($num >=1){
$num=$num - 1;
$anum=chr(($num % 26)+65).$anum;
$num=$num / 26;
}
return $anum;
}
function genVocabKey($text){
static $time=0;
static $count=0;
if( time()!=$time){
$time=time();
$count=mt_rand(15601, 358800);}
$count++;
return $time . $this->_toAlphaNumber($count);
}
function _searchExistingTranslation($transMe,&$trid){
$this->_transSQL->whereE('text',$transMe );
$trid=$this->_transSQL->load('o','imac');
return (!empty($trid))?true : false;
}
function _insertCross($transMe,$dbtid,$eid,$map){
static $instance=null;
if(empty($transMe)) return false;
if(!isset($instance)){
$instance=WModel::get('translation.populate');
}
if(!isset($instance->_dbcid[$dbtid][$map])){
$columnModel=WModel::get('library.columns');
$columnModel->whereE('dbtid',$dbtid);
$columnModel->setLimit(50000);
$results=$columnModel->load('ol',array('dbcid','name'));
foreach($results as $oneResult){
$instance->_dbcid[$dbtid][$oneResult->name]=$oneResult->dbcid;
}
}
if(!isset($instance->_dbcid[$dbtid][$map])){
$message=WMessage::get();
$message->codeE("Could not find the column for the table $dbtid and the map $map");
return false;
}
$instance->dbcid=$instance->_dbcid[$dbtid][$map];
$instance->eid=$eid;
$instance->wid=$this->wid;
$instance->imac=$transMe->imac;
$instance->setReplace();
return $instance->insert();
}
function _searchExistingKey($transMe){
$this->_transSQL->whereE('imac',$transMe );
return $this->_transSQL->exist();
}
function _nl2br2($string){
$string=str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
return $string;
}
function cleanLayout($yid){
}
}
