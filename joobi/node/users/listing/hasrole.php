<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_CoreHasrole_listing extends WListings_default{
function create(){
if($this->value){
$text=WText::t('1206732372QTKI');
$color='green';
$action='1';
}else{
$text=WText::t('1206732372QTKJ');
$color='red';
$action='2';
}
$eid=WGlobals::getEID();
$linkO=new WLink('<span style="color:'.$color.';font-weight:bold;">'.$text.'</span>');
$rolid=$this->getValue('rolid');
$link=WPage::routeURL('controller='. $this->controller .'&task=assign&act='.$action.'&eid='.$eid.'&rolid='.$rolid );
$this->content=$linkO->make($link);
return true;
}}