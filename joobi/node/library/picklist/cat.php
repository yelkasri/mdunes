<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Cat_picklist extends WPicklist {
function create(){
static $myPicklist=array();
$sql=WModel::get($this->sid ); 
if(empty($sql)){
$sid=$this->sid;
$message=WMessage::get();
$message->codeE('The model '.$sid.' was not found when trying to create picklist for library.cat');
return;
}
$transNameModel=$sql->getModelNamekey().'trans';
if($this->onlyOneValue()){
$transNameModel=$sql->getModelNamekey(). 'trans';
if( WModel::modelExist($transNameModel)){
$sql->makeLJ($transNameModel, $sql->getPK());
$sql->whereLanguage( 1 );
$sql->select('name',1 );
}else{
$sql->select('name');
}
if(is_array($this->defaultValue)){
$sql->whereIn($sql->getPK(),$this->defaultValue );  
}else{
$sql->whereE($sql->getPK(),$this->defaultValue );  
}
$sql->rememberQuery();
$result=$sql->load('o',array('rolid'));
if(!empty($result)){
$this->addElement($result->rolid, $result->name );
}else{
$this->addElement($this->defaultValue, '');
}
return true;
}
$parent=array();
$parent['pkey']=$sql->getPK();
$parent['parent']='parent';
$parent['name']='name';
if(!isset($myPicklist[$transNameModel])){
$modelRef=WModel::get('library.model','object');
$modelRef->whereE('namekey',$transNameModel );
if($modelRef->exist()){
$sql->makeLJ($transNameModel, $sql->getPK());
$sql->whereLanguage( 1 );
$sql->select($parent['name'], 1);
}else{
$sql->select($parent['name']);
}
$sql->select($parent['parent']);
$sql->orderBy($parent['parent'] );
$sql->select($sql->getPK());  
$sql->whereE('type','1'); 
$sql->setLimit( 500 );
$myitems=$sql->load('ol');
$childOrderParent=array();
$list=WOrderingTools::getOrderedList($parent, $myitems, 1, false, $childOrderParent  );
$myPicklist[$transNameModel]=$list;
}
$pkey=$parent['pkey'];
$name=$parent['name'];
foreach($myPicklist[$transNameModel] as $itemList){
if(!empty($itemList->parent)){
$this->addElement($itemList->$pkey, $itemList->$name );
}
}
}
}