<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WButton_CoreLink extends WButtons_default {
protected $noJSonButton=true;
function create(){
static $redirect=null;
$this->buttonO->action=str_replace( JOOBI_INDEX.'?','',$this->buttonO->action );
$actions=explode('&',$this->buttonO->action );
$content='';
$extra='';
$option=false;
foreach($actions as $action){
$action=str_replace("amp;", '',$action);
$propVal=explode('=',$action);
switch($propVal[0]){
case JOOBI_URLAPP_PAGE:
$option=str_replace('com_','',$propVal[1] );
break;
case 'controller':
$content=$propVal[0].'='.$propVal[1];
break;
default:
$extra.='&'.$action;
}}
$content.=$extra;
if($option){
$this->buttonO->href=WPage::routeURL($content,'home','default',false,true,$option );
}else{
if(!isset($redirect)){
$redirect='&returnid='.base64_encode( WView::getURI());
}$this->buttonO->action=str_replace('(returnid)',$redirect, $this->buttonO->action );
$this->buttonO->href=WPage::routeURL($this->buttonO->action );
}
$this->link=$this->buttonO->href;
return true;
}
}