<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WListing_Coredynarea extends WListings_default{
function create(){
static $onlyOnce=true;
$nice=WGlobals::get('jnicedit');
if(!$nice){
WPage::addJSFile('main/nicedit/nicEdit.js','inc');
WGlobals::set('jnicedit',true);
}
$linepropID=$this->name.$this->line;
$divID1='dyn_'.$linepropID;
$divID2='Ndyn_'.$linepropID;
$inputId='jdynarea'.$linepropID;
$path=JOOBI_URL_INC.'main/nicedit/niceicons.gif';
$ondblclick='wzflipIpt(\''.$divID1.'\',\''.$divID2.'\')';
$div1=new WDiv($this->value );
$div1->classes='jdynarea';
$div1->ondblclick=$ondblclick;
$div1->id=$divID1;
$part1=$div1->make();
if($onlyOnce){
$flipFunc='';
$debug=JOOBI_DEBUGCMS || WPref::load('PLIBRARY_NODE_DBGERR');
if($debug)$flipFunc .='/* function to flip the dynamic imput */'.$this->crlf;
$flipFunc .='wzflipIpt=function(d1,d2){'.$this->crlf .
'document.getElementById(d1).style.display=\'none\';'.$this->crlf .
'document.getElementById(d2).style.display=\'block\';'.$this->crlf .
'}';
$onlyOnce=false;
WPage::addJS($flipFunc);
}
$pkeyMap=$this->pkeyMap;
$eid=$this->data->$pkeyMap;
$extras="{'em':'em". $this->line."','zval':document.getElementById('".$inputId."').value";
$script=WPref::load('PLIBRARY_NODE_SCRIPTTYPE');
$param=new stdClass;
$aid='';
if($script){
$link='controller='.$this->controller;
$param->jsButton=array('ajaxToggle'=> 1, 'ajxUrl'=> $link );
$extras.=",'divId':'". $divID1."','elemType':'dyninput','myId':'". $eid."'";
}$extras.="}";
$onclick=$this->elementJS($extras, $param );
$inputBox='<textarea id="'.$inputId.'" class="jdyninput">'.$this->value.'</textarea>';
$inputBox.='<a href="#" value="submit" onclick="'.$onclick.'"><span class="jpng-16-save" style="float:left;">&nbsp;&nbsp;&nbsp;&nbsp;</span></a>';
$div2=new WDiv($inputBox );
$div2->style='display:none;';
$div2->id=$divID2;
$part2=$div2->make();
$this->content=$part1.$part2;
return true;
}}
