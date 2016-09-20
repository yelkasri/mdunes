<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_Coreaccess extends WForms_default {
function create(){
$roleM=WModel::get('role','object'); 
$parent=array();
$parent['pkey']='rolid';
$parent['parent']='parent';
$parent['name']='name';
$roleM->makeLJ('roletrans','rolid');
$roleM->whereLanguage(1);
$roleM->select('name', 1);
$roleM->orderBy('lft','ASC');
$roleM->select('rolid');  $roleM->select('parent');
$roleM->where('type','!=','2');$roleM->setLimit( 300 );
$myitems=$roleM->load('ol');
$childOrderParent=array();
$list=WOrderingTools::getOrderedList($parent, $myitems, 1, false, $childOrderParent );
$oneDrop=array();
foreach($list as $role){
$oneDrop[]=WSelect::option($role->rolid, $role->name );
}
$HTMLDrop=new WSelect();
if( substr($this->element->map, 1, 1 )=='['){
$case=substr($this->element->map, 0, 1 );
$mapField=JOOBI_VAR_DATA.'['.$case.']'.substr($this->element->map, 1, -1 ). ']';
}else{
$mapField=JOOBI_VAR_DATA.'['.$this->element->sid.']['.$this->element->map.']';
}
$HTMLDrop->classes='simpleselect';
$this->content=$HTMLDrop->create($oneDrop, $mapField, null, 'value','text',$this->value );
return true;
}
function show(){
$roleM=WModel::get('role','object');
$roleM->makeLJ('roletrans','rolid');
$roleM->whereLanguage(1);
$roleM->whereE('rolid',$this->value );
$roleM->select('name',1);
$roleM->select('color');
$results=$roleM->load('o');
if(!empty($results)){
$this->content='<span style="white-space: nowrap; color: '. $results->color .';">';
$this->content .=$results->name .'</span>';
}else{
$this->content='';
}return true;
}
}
