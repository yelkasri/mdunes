<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WListing_Coreaccess extends WListings_default{
public function createHeader(){
if(empty($this->element->align))$this->element->align='center';
if(empty($this->element->width))$this->element->width='60px';
return false;
}
function create(){
static $roles=null;
if(!isset($roles)){
$rolemodel=WModel::get('role','object');
$rolemodel->remember('AllRoles', true, 'Model_role_node');
$rolemodel->makeLJ('roletrans','rolid');
$rolemodel->whereLanguage(1);
$rolemodel->select('name',1);
$rolemodel->select('rolid');
$rolemodel->select('color');
$rolemodel->setLimit( 500 );
$results=$rolemodel->load('ol');
foreach($results as $role){
$roles[$role->rolid]=array('name'=> $role->name, 'color'=> $role->color);
}}
$nocolor=(isset($this->nocolor) && !empty($this->nocolor))?true : false;
if(is_array($this->value)){
$this->content='';
foreach($this->value as $role){
if(isset($roles[$role])){
if(!$nocolor)$this->content .='<span style="white-space: nowrap; color: '.$roles[$role]['color'].';">';
$this->content .=$roles[$role]['name'];
if(!$nocolor)$this->content .='</span>';
$this->content .='<br/>';
}}
}elseif(isset($roles[$this->value])){
if(!$nocolor)$this->content='<span style="white-space: nowrap; color: '.$roles[$this->value]['color'].';">';
$this->content .=$roles[$this->value]['name'];
if(!$nocolor)$this->content .='</span>';
}
return true;
}
public function advanceSearch(){
$lid=$this->element->lid;
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
$oneDrop[]=WSelect::option( 0, WText::t('1206732410ICCJ'));
foreach($list as $role){
$oneDrop[]=WSelect::option($role->rolid, $role->name );
}
$HTMLDrop=new WSelect();
$mapField='advsearch['.self::$complexMapA[$lid] .']';
$defaultValue=WGlobals::getUserState( self::$complexSearchIdA[$lid] , self::$complexMapA[$lid], '','array','advsearch');
$HTMLDrop->classes='simpleselect';
$this->content=$HTMLDrop->create($oneDrop, $mapField, null, 'value','text',$defaultValue, Output_Doc_Document::$advSearchHTMLElementIdsA[$lid] );
return true;
}
public function searchQuery(&$model,$element,$searchedTerms=null,$operator=null){
$lid=$this->element->lid;
$this->createComplexIds($lid, $element->map.'_'.$element->sid );
Output_Doc_Document::$advSearchHTMLElementIdsA[$lid]='srchwz_'.$lid;
if(!empty($searchedTerms)){
$defaultValue=$searchedTerms;
}else{
$defaultValue=WGlobals::getUserState( self::$complexSearchIdA[$lid], self::$complexMapA[$lid], 0, 'array','advsearch');
}
if(!empty($defaultValue) && is_numeric($defaultValue)){
$model->whereSearch($element->map, $defaultValue, $element->asi, '=',$operator );
}
}
}