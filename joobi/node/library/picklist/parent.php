<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Parent_picklist extends WPicklist {
function create(){
$trk=WGlobals::get( JOOBI_VAR_DATA );
  $wzmid=$trk['s']['mid'];
  if(empty($this->sid)){
$controller=WGlobals::get('controller','','','string');
$sid=((!empty($wzmid))?$wzmid: WModel::get( str_replace('-','.',$controller), 'sid'));
  }else{
  $sid=$this->sid;
}
$eid=WGlobals::getEID();
if(empty($sid)) return '';
$sql=WModel::get($sid );
$transNameModel=$sql->getModelNamekey().'trans';
$modelRef=WModel::get('library.model','object'  );
$modelRef->whereE('namekey',$transNameModel );
if($modelRef->exist()){
$sql->makeLJ($transNameModel, $sql->getPK());
$sql->whereLanguage( 1 );
$sql->select('name', 1 );
}else{
$sql->select('name');
}
$item=Output_Forms_class::getItem($sql->getModelID(), $eid );
$groupMap=$sql->getParam('grpmap',$sql->getPK());
$sql->select('parent');
$sql->select($sql->getPK());  
$groupValue=!empty($item->$groupMap)?$item->$groupMap : null;
if(empty($groupValue)){
$myPorpertyNow=$groupMap.'_'.$sql->getModelID();
$groupValue=(isset($item->$myPorpertyNow)?$item->$myPorpertyNow : '');
}
if(empty($groupValue))$groupValue=WGlobals::get($groupMap);
$sql->whereE($groupMap, $groupValue );
$sql->where('publish','!=', -2 );
if($eid>0)$sql->where($sql->getPK(), '!=',$eid ); 
if($sql->columnExists('parent'))$sql->orderBy('parent');
if($sql->columnExists('ordering'))$sql->orderBy('ordering');
$sql->setLimit( 10000 );
$sql->groupBy($sql->getPK());
$elements=$sql->load('ol');
$children=array();
$parent=array();
$parent['pkey']=$sql->getPK();
$parent['parent']='parent';
$parent['name']='name';
if($elements){
$childOrderParent=array();
$list=WOrderingTools::getOrderedList($parent, $elements, 1, false, $childOrderParent );
}else{
$list=array();
}
$this->addElement('0', WText::t('1206732429GMSU'));
$kpek=$parent['pkey'];
foreach($list as $itemList){
$this->addElement($itemList->$kpek, '.  '.$itemList->name );
}
}
}