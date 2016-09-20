<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WListing_Corepublish extends WListings_default{
public function createHeader(){
if(empty($this->element->align))$this->element->align='center';
if(empty($this->element->width))$this->element->width='30px';
return false;
}
function create(){
static $addLegend=array();
static $classCSSA=array();
$script=WPref::load('PLIBRARY_NODE_SCRIPTTYPE');
$extra='';
switch($this->value){
case'1':$class='publish';
$nameTag=WText::t('1207306697RNMA');
$order=1;
$this->valueTo=0;
break;
case'0':$class ='unpublish';
$nameTag=WText::t('1242282416HAQR');
$order=2;
$this->valueTo='1';
break;
case'2':$class ='pending';
$task ='toggle';$nameTag=WText::t('1206732372QTKP');
$order=3;
$this->valueTo=0;
break;
case'-1':$class ='archive';
$task ='toggle';$nameTag=WText::t('1209746189NUCP');
$order=4;
$this->valueTo=0;
break;
case'-2':$class ='disabled';
$task ='toggle';$nameTag=WText::t('1219769904NDIM');
$order=5;
$this->valueTo=0;
break;
default:
if($this->value>2){
$class ='unpublish';
$nameTag=WText::t('1242282416HAQR');
$order=1;
$this->valueTo=0;
}else{
$class ='publish';
$nameTag=WText::t('1207306697RNMA');
$order=2;
$this->valueTo='1';
}break;
}
if(isset($this->element->style))$style='style="'.$this->element->style.'" ';
else $style='';
$pkeyMap=$this->pkeyMap;
$eid=( is_string($pkeyMap) && (isset($this->data->$pkeyMap ))?$this->data->$pkeyMap : null );
if(!isset($this->element->infonly) && is_array($pkeyMap))$this->element->infonly=1;
if(!isset($this->element->infonly) && !isset($this->element->lien)){
$param=new stdClass;
$id=$this->element->sid.'_'.$this->element->map.'_'.$this->data->$pkeyMap;
$aid="a" . $id;
$msg=isset($this->element->confirmmsg )?$this->element->confirmmsg : WText::t('1327354737PCAU');
$data=new stdClass;
$data->image=$class;
$data->text=$nameTag;
$data->group='publish';
$data->order=$order+5;
$data->ID=$id;
$imageHTML=WPage::renderBluePrint('legend',$data );
if($script){
$link='controller='.$this->controller;
$param->jsButton=array('ajxUrl'=> $link );
if(empty($this->element->noscript))$param->jsButton['ajaxToggle']=1; 
$extra="{'em':'em". $this->line . "','zval':" . $this->valueTo . ",'divId':'".$id."','title':'". $nameTag."','elemType':'publish','myId':'" . $eid . "'";
if(isset($this->element->confirm)){
$extra .=",'confirm':1";
$extra .=",'confirmmsg':'" . $msg . "'";
}$extra .="}";
$onclick=$this->elementJS($extra, $param );
if(!empty($this->element->confirm ))$onclick='if(confirm(\''.$msg.'\')){return '.$onclick.'}';
$this->content='<a style="cursor:pointer" id="'.$aid.'" onclick="'.$onclick.'" title="'. $nameTag.' '.WText::t('1206732372QTKR').'">';
}else{
$paramA=array();
$paramA['zsid']=$this->element->sid;
$paramA['zmap']=$this->element->map;
$zsc=WTools::secureMe($paramA );
$link=WPage::link('controller='.$this->controller.'&task=toggle&eid='.$eid.'&zmap=publish&zsid='.$this->element->sid.'&zval='.$this->valueTo.'&zsc='.$zsc  );
$this->content='<a href="'.$link.'" id="'.$aid.'" title="'.  $nameTag.' '.WText::t('1206732372QTKR').'">';
}
$this->content .=$imageHTML;
$this->content .='</a>';
}else{
$data=new stdClass;
$data->image=$class;
$data->text=$nameTag;
$data->group='publish';
$data->order=$order+5;
$imageHTML=WPage::renderBluePrint('legend',$data );
$this->content .=$imageHTML;
}
return true;
}
public function advanceSearch(){
$lid=$this->element->lid;
$oneDropA=array();
$oneDropA[]=WSelect::option( 0, WText::t('1206732410ICCJ'));
$oneDropA[]=WSelect::option( 10, WText::t('1242282416HAQR'));
$oneDropA[]=WSelect::option( 11, WText::t('1207306697RNMA'));
$HTMLDrop=new WRadio();
$mapField='advsearch['.self::$complexMapA[$lid] .']';
$defaultValue=WGlobals::getUserState( self::$complexSearchIdA[$lid] , self::$complexMapA[$lid], 0, 'array','advsearch');
$HTMLDrop->classes='simpleselect';
$this->content=$HTMLDrop->create($oneDropA, $mapField, null, 'value','text',$defaultValue, Output_Doc_Document::$advSearchHTMLElementIdsA[$lid] );
return true;
}
public function searchQuery(&$model,$element,$searchedTerms=null,$operator=null){
$lid=$element->lid;
$this->createComplexIds($lid, $element->map.'_'.$element->sid );
Output_Doc_Document::$advSearchHTMLElementIdsA[$lid]='srchwz_'.$lid;
if(!empty($searchedTerms)){
$defaultValue=$searchedTerms;
}else{
$defaultValue=WGlobals::getUserState( self::$complexSearchIdA[$lid] , self::$complexMapA[$lid], 0, 'array','advsearch');
}
if(!empty($defaultValue) && is_numeric($defaultValue)){
if(empty($searchedTerms))$defaultValue -=10;
$model->whereSearch($element->map, $defaultValue, $element->asi, '=',$operator );
}
}
}