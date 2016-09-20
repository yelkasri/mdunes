<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WListing_Coreyesno extends WListings_default{
protected $oneDropA=array();
var$class ='';
var$nameTag='';
var$order=0;
var$valueTo=0;
var $elementType='';
var $classLegend=array();
public function createHeader(){
if(empty($this->element->align))$this->element->align='center';
if(empty($this->element->width))$this->element->width='30px';
return false;
}
public function create(){
if(!empty($this->value)){
$this->class='yes';
$this->nameTag=WText::t('1206732372QTKI');
$this->order=1;
$this->valueTo='0';
}else{
$this->class='cancel';
$this->nameTag=WText::t('1206732372QTKJ');
$this->order=2;
$this->valueTo='1';
}
$this->elementType='yesno';
$this->classLegend=array('yes','cancel');
$this->content=$this->createHTML();
return true;
}
protected function createHTML(){
$html='';
$script=WPref::load('PLIBRARY_NODE_SCRIPTTYPE');
if(isset($this->element->noscript))$script=false; 
$extra='';
if(isset($this->element->style))$style='style="'. $this->element->style .'" ';
else $style='';
$pkeyMap=$this->pkeyMap;
if(!isset($this->element->infonly) && (is_array($pkeyMap) || strpos($pkeyMap,',')))$this->element->infonly=1;
if(empty($this->element->infonly) && empty($this->element->lien)){
$param=new stdClass;
$id=$this->element->sid.'_'.$this->element->map.'_'.$this->data->$pkeyMap;
$aid='a'.$id;
$msg=(!empty($this->element->confirmmsg )?$this->element->confirmmsg : WText::t('1327354737PCAU'));
if($script){
$link='controller='.$this->controller;
$param->jsButton=array('ajaxToggle'=>1, 'ajxUrl'=> $link );
$pkeyMap=$this->pkeyMap;
$eid=$this->data->$pkeyMap;
$extra="{'em':'em". $this->line."','zval':".$this->valueTo.",'divId':'".$id."','title':'". $this->nameTag . "','elemType':'".$this->elementType."','myId':'". $eid."'";
if(isset($this->element->confirm)){
$extra.=",'confirm':1";
$extra.=",'confirmmsg':'".$msg."'";
}$extra.="}";
}
$data=new stdClass;
$data->image=$this->class;
$data->text=$this->nameTag;
$data->group='publish';
$data->order=$this->order+10;
$data->ID=$id;$imageHTML=WPage::renderBluePrint('legend',$data );
$onclick=$this->elementJS($extra, $param );
if(isset($this->element->confirm))$onclick='if(confirm(\''.$msg.'\')){ return '.$onclick.'}';
$this->content='<a style="cursor:pointer" id="'.$aid.'" onclick="'.$onclick.'" title="'.  $this->nameTag.' '. WText::t('1206732372QTKR').'">';
$this->content .=$imageHTML;
$this->content .='</a>';
}else{
$data=new stdClass;
$data->image=$this->class;
$data->text=$this->nameTag;
$data->group='publish';
$data->order=$this->order+10;
$this->content=WPage::renderBluePrint('legend',$data );
}
return $this->content;
}
public function advanceSearch(){
$lid=$this->element->lid;
if(empty($this->oneDropA)){
$this->oneDropA[]=WSelect::option( 0, WText::t('1206732410ICCJ'));
$this->oneDropA[]=WSelect::option( 10, WText::t('1206732372QTKJ'));
$this->oneDropA[]=WSelect::option( 11, WText::t('1206732372QTKI'));
}
$HTMLDrop=new WRadio();
$mapField='advsearch['.self::$complexMapA[$lid] .']';
$defaultValue=WGlobals::getUserState( self::$complexSearchIdA[$lid] , self::$complexMapA[$lid], 0, 'array','advsearch');
$HTMLDrop->classes='simpleselect';
$this->content=$HTMLDrop->create($this->oneDropA, $mapField, null, 'value','text',$defaultValue, Output_Doc_Document::$advSearchHTMLElementIdsA[$lid] );
return true;
}
public function searchQuery(&$model,$element,$searchedTerms=null,$operator=null){
$lid=$element->lid;
$this->createComplexIds($lid, $element->map.'_'.$element->sid );
Output_Doc_Document::$advSearchHTMLElementIdsA[$lid]='srchwz_'.$lid;
if(!empty($searchedTerms)){
$defaultValue=$searchedTerms;
}else{
$defaultValue=WGlobals::getUserState( self::$complexSearchIdA[$lid], self::$complexMapA[$lid], 0, 'array','advsearch');
}
if(!empty($defaultValue) && is_numeric($defaultValue)){
if(empty($searchedTerms))$defaultValue -=10;
$model->whereSearch($element->map, $defaultValue, $element->asi, '=',$operator );
}
}
}