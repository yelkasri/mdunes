<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CoreVersion_form extends WForms_default {
function show(){
$wid=WGlobals::getEID();
$extensionM=WModel::get('apps');
$extensionM->whereE('wid',$wid);
if($extensionM->load('lr','type')==350){
$helper=WClass::get('apps.helper');
$this->content=$helper->getCMSModuleVersionUsingWid($wid);
}else{
$this->content=$this->value;
}
return true;
}}