<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_Extensions_picklist extends WPicklist {
function create(){
if($this->onlyOneValue()){
if($this->defaultValue !=0){
$extensionM=WModel::get('apps');
$extensionM->rememberQuery();
$extensionM->whereE('wid',$this->defaultValue );
$result=$extensionM->load('o',array('wid','name'));
$this->addElement($result->wid, $result->name);
}else{
$this->addElement( 0, 'No Parent');
}
return true;
}
$myFilter=(isset($this->wval1))?$this->wval1 : 0;
$appsM=WModel::get('apps');
$appsM->select(array('name','type','wid'));
$appsM->whereE('publish' , 1 );
$showLibrary=true;
$types=array('150','1');
$appsM->whereIn('type',$types );
$appsM->orderBy('type');
$appsM->orderBy('name');
$appsM->setLimit( 500 );
$components=$appsM->load('ol');
$types=WType::get('apps.type');
$typePrev='';
if(!empty($components)){
foreach($components as $component)  {
if($typePrev!=$component->type ){
$typePrev=$component->type;
$this->addElement( 0 , '--'. $types->getName($component->type));
}
$this->addElement($component->wid , $component->name.' '. $types->getName($component->type));
$used[$component->wid]=1;
}
if(is_numeric($this->defaultValue) && empty($used[$this->defaultValue])){
$this->addElement( 0 , '--Other Extension');
$this->addElement($this->defaultValue,'Actual Extension : '.$this->defaultValue);
}
}
}}