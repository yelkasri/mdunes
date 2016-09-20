<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WListing_CoreButcustom extends WListings_default{
public function createHeader(){
if(empty($this->element->align))$this->element->align='center';
if(empty($this->element->width))$this->element->width='30px';
return false;
}
function create(){
if(empty($this->value)) return false;
if(!isset($this->buttonNameSepcial)){
$this->buttonNameSepcial=$this->element->map;
}
switch($this->buttonNameSepcial){
case 'copy':
$text=WText::t('1206732372QTKK');
break;
case 'delete':
$text=WText::t('1206732372QTKL');
$ACTION=$text;
if( WPref::load('PLIBRARY_NODE_AJAXPAGE')){
$paramsO=WObject::newObject('output.jsaction');
$paramsO->confirm=true;
$paramsO->confirmName=$ACTION;
$outputLinkC=WClass::get('output.link');
$outputLinkC->wid=$this->nodeID;
$link=$outputLinkC->convertLink($this->element->lien, $this->data, '',$this->modelID, $this->mapList, false);
$linkA=explode('&',$link );
$task2Use='delete';
$valueA=array();
if(!empty($linkA)){
foreach($linkA as $lk){
$exA=explode('=',$lk );
if(empty($exA[0]) || empty($exA[1])) continue;
if('task'==$exA[0]){
$task2Use=$exA[1];
continue;
}$valueA[$exA[0]]=$exA[1];
}}
if(!empty($this->element->nestedView)){
$valueA['vWjx']=$this->yid;
$valueA['fRmjx']=$this->formName;
}
$this->element->lienValidation='onclick="'.WPage::jsAction($task2Use, $paramsO, $valueA ). '"';
$this->element->lienAjax=true;
}else{
$this->element->lienValidation='onclick="return confirm(\''.str_replace(array('$ACTION'), array($ACTION),WText::t('1233626551NWXV')). '\')"';
}
break;
case 'edit':
$text=WText::t('1206732361LXFE');
break;
case 'show':
$text=WText::t('1206732372QTKM');
break;
default :
$text=$this->element->name;
}
$data=new stdClass;
$data->image=$this->buttonNameSepcial;
$data->text=$text;
$img[$this->buttonNameSepcial]=WPage::renderBluePrint('legend',$data );
$this->content=$img[$this->buttonNameSepcial];
return true;
}
public function advanceSearch(){
return false;
}
public function searchQuery(&$model,$element,$searchedTerms=null,$operator=null){
}
}