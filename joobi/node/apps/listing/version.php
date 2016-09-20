<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CoreVersion_listing extends WListings_default{
function create(){
$wid=$this->getValue('wid');
$this->content='<div id="status_'.$wid.'">';
if($this->getValue('type')==350){
static $helper=null;
if(!isset($helper))$helper=WClass::get('apps.helper');
$CURRENT_VERSION=$helper->getCMSModuleVersionUsingWid($wid );
$PUBLISH=0;
if($CURRENT_VERSION){
$PUBLISH=1;
}
$LEVEL=0;
$v1=(int)str_replace(array('.'),array('0'), $CURRENT_VERSION );
}else{
$CURRENT_VERSION=$this->getValue('userversion');
$LEVEL=$this->getValue('level');
$PUBLISH=$this->getValue('publish');
$v1=$this->getValue('version');
}
if(empty($CURRENT_VERSION ))$CURRENT_VERSION=$this->getValue('version');
$v2=$this->getValue('lversion');
$NEW_VERSION=$this->getValue('userlversion');
$CURRENT_VERSION='<b>'.$CURRENT_VERSION.'</b>';
$NEW_VERSION='<b>'.$NEW_VERSION.'</b>';
if($PUBLISH==1){
$currentversion=WText::t('1228709213SHFK'). ' <span class="badge">'.$CURRENT_VERSION.'</span>';
if( version_compare($v1, $v2, ">=" )){
$this->content .='<small>'.$currentversion.'</small>';
}elseif(!empty($wid)){
if($LEVEL){
$sx1Type=(int)$this->getValue('ltype');
}
$this->content .='<div class="clearfix"><small>'.$currentversion.'</small></div>';
$this->content .='<div class="newVersion clearfix">'.WText::t('1206732400OXCC'). ' <span class="badge">'.$NEW_VERSION.'</span></div>';
}
}
$this->content .='</div>';
return true;
}}