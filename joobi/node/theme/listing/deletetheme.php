<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('listing.butdelete');
class Theme_Deletetheme_listing extends WListing_butdelete {
function create(){
$core=$this->getValue('core');
if(!$core){
$link=WPage::routeURL('controller=theme&task=deleteall&eid='.$this->value);
$iconO=WPage::newBluePrint('icon');
$iconO->icon='delete';
$iconO->text=WText::t('1206732372QTKL');
$img=WPage::renderBluePrint('icon',$iconO );
$this->content='<a href="'.$link.'">'. $img.'</a>';
parent::create();
}else
$this->content='-';
return true;
}}