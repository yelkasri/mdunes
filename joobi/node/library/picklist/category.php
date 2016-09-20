<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Library_Category_picklist extends WPicklist {
function create(){
$sql=WModel::get($this->sid ); 
if($this->onlyOneValue()){
$pkey=$sql->getPK();
$transNameModel=$sql->getModelNamekey(). 'trans';
if( WModel::modelExist($transNameModel)){
$sql->makeLJ($transNameModel, $pkey);
$sql->whereLanguage( 1 );
$sql->select('name',1 );
}else{
$sql->select('name');
}
$sql->whereE($pkey,$this->defaultValue );  
$sql->rememberQuery();
$result=$sql->load('o',array($pkey));
if(!empty($result)){
$this->addElement($result->$pkey, $result->name);
}else{
$this->addElement($this->defaultValue, '');
}
return true;
}
$parent=array();
$parent['pkey']=$sql->getPK();
$parent['parent']='parent';
$parent['name']='name';
$transNameModel=$sql->getModelNamekey().'trans';
$modelRef=WModel::get('library.model','object'  );
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
$sql->setLimit( 500 );
$myitems=$sql->load('ol');
$childOrderParent=array();
$list=WOrderingTools::getOrderedList($parent, $myitems, 1, false, $childOrderParent );
foreach($list as $itemList){
$pkey=$parent['pkey'];
$name=$parent['name'];
$this->addElement($itemList->$pkey, $itemList->$name );
}
}
}