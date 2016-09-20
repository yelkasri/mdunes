<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Ordering_picklist extends WPicklist {
function create(){
$trk=WGlobals::get( JOOBI_VAR_DATA );
  $wzmid=(!empty($trk['s']['mid'])?$trk['s']['mid'] : '');
if(!empty($this->_params->sid )){
$sid=$this->_params->sid;
}else{
$controller=WGlobals::get('controller','','','string');
$sid=WModel::get( str_replace('-','.',$controller), 'sid');
}
$eid=WGlobals::getEID();
if(empty($sid)) return '';
$sql=WModel::get($sid );
$transNameModel=$sql->getModelNamekey().'trans';
$transNameModelID=WModel::getID($transNameModel );
if(!empty($transNameModelID )){
$sql->makeLJ($transNameModel, $sql->getPK());
$sql->whereLanguage( 1 );
$modelTransM=WModel::get($transNameModel );
if($modelTransM->columnExists('name')){
$sql->select('name', 1 );}elseif($sql->columnExists('alias')){
$sql->select('alias', 0, 'name');
}else{
$this->codeE('There is no column to get the name from for the ordering picklist (a).');
return false;
}
}else{
if($sql->columnExists('name')){
$sql->select('name');}elseif($sql->columnExists('alias')){
$sql->select('alias', 0 ,'name');
}else{
$this->codeE('There is no column to get the name from for the ordering picklist (b).');
return false;
}
}
$item=Output_Forms_class::getItem($sql->getModelID(), $eid );
$groupMap=$sql->getParam('grpmap',$sql->getPK());
$groupValue=!empty($item->$groupMap)?$item->$groupMap : null;
if(empty($groupValue)){
$myPropertyNow=$groupMap.'_'.$sql->getModelID();
$groupValue=(!empty($item->$myPropertyNow)?$item->$myPropertyNow : '');
}
if(empty($groupValue))$groupValue=WForm::getPrev($groupMap);
$sql->select('ordering');
$sql->select($sql->getPK());  
if($groupMap!='publish')$sql->whereE($groupMap, $groupValue );
if($sql->columnExists('publish'))$sql->where('publish','>=', 0 );
$sql->orderBy('ordering');
$sql->setLimit( 10000 );
$list=$sql->load('ol');
if(!empty($list)){
$children=array();
$parent=array();
$parent['pkey']=$sql->getPK();
$parent['parent']='ordering';
$parent['name']='name';
$prt=$parent['parent'];
$i=1;
foreach($list as $itemList){
$this->addElement($itemList->$prt, $i.' - '.$itemList->name );
$i++;
}
}else{
$i=1;
}
if(empty($this->defaultValue ))  $this->defaultValue=$i;
$this->addElement($i , $i.' - '. WText::t('1206732428GQYF'));
}
}