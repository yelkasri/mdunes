<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_CorePrevtheme_listing extends WListings_default{
function create(){
$type=$this->getValue('type');
if($type==101 ) return false;
if($type !=101 ) return true;
$link=WPage::routeURL('controller=theme&task=preview&eid='.$this->value.'&titleheader='.$this->getValue('name'));
$iconO=WPage::newBluePrint('icon');
$iconO->icon='preview';
$iconO->text=WText::t('1211811587EZNL');
$img=WPage::renderBluePrint('icon',$iconO );
$this->content='<a href="'.$link.'">'. $img.' '.WText::t('1211811587EZNL').'</a>';
return true;
}}