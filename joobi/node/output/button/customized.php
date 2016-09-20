<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WButton_Corecustomized extends WButtons_default {
function create(){
if(!empty($this->buttonO->filef )){
if(!empty($this->buttonO->currentClassName)){
$extnalFucntion=new $this->buttonO->currentClassName;
}else{
$mwid=WView::get($this->buttonO->yid, 'wid');
if(empty($mwid)) return false;
$nodeName=WExtension::get($mwid, 'folder');
$elementName=$nodeName.'.button.'.$this->buttonO->filef;
WView::includeElement($elementName, null, true);
$clNAme=ucfirst($nodeName). '_'.$this->buttonO->filef.'_button';
$extnalFucntion=new $clNAme;
}
} else return false;
if(!is_object($extnalFucntion) || empty($extnalFucntion)){
return false;
}
if( method_exists($extnalFucntion,'create')){
$extnalFucntion->buttonO=&$this->buttonO;
$extnalFucntion->viewInfoO=&$this->viewInfoO;
$extnalFucntion->noJSonButton=&$this->noJSonButton;
if(!empty($this->viewInfoO->formID)){
$formObj=WView::form($this->viewInfoO->formID );
$extnalFucntion->formName=$formObj->name;
}$extnalFucntion->content='';
$contentHTML=$extnalFucntion->create();
if($contentHTML===false){
$this->content='';
return false;
}elseif($contentHTML===true){
$this->content=$extnalFucntion->content;
}else{
$this->content=(!empty($contentHTML))?$contentHTML : $extnalFucntion->content;
}
}
return true;
}
}
class WButtons_external extends WElement {
public $buttonO=null;public $viewInfoO=null; 
public $noJSonButton=false;
var $_hide=false;
var $content='';
var $_onClick='';
public $isPopUp=false;
function __construct(){
}
public function getData(){
return $this->buttonO;}
protected function getValue($columnName,$modelName=null){
return WView::retreiveOneValue($this->viewInfoO->elementsData, $columnName, $modelName );
}
function setOnclickJS($myJS){
$this->buttonO->buttonJS=$myJS; }
protected function setCSS($CSS){
$this->buttonO->cssImgLocation=$CSS;
}
function setTitle($title){
$this->buttonO->name=$title;
}
function setIcon($myIcon){
$this->buttonO->icon=$myIcon;
}
function setAction($myAction){
$this->buttonO->action=$myAction;
}
function setFullDisable($fDisable=true){
$this->buttonO->fullDisable=$fDisable;
}
function setAddress($myAdress,$external=false){
if($external){
$this->buttonO->href=$myAdress;
}else{
$this->buttonO->href=WPage::routeURL($myAdress );
}
$this->noJSonButton=true;
}
function confirmAction($confirm=true){
$this->buttonO->confirm=$confirm;
}
function setPopup(){
$this->buttonO->isPopUp=true;
}
function requireSelection($select=true){
$this->buttonO->lslct=$select;
}
function unClickable(){
$this->buttonO->noClick=true;
$this->setOnclickJS('return false;');
}
function hide($hidden=true){
$this->buttonO->hide=$hidden;
}
}