<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('listing.butedit');
class Theme_Editicon_listing extends WListing_butedit {
function create(){
if($this->getValue('core')){
WPage::renderBluePrint('legend','show');
$this->content='<span class="jpng-16-show" alt="Show" title="View" border="0"></span>';
return true;
}else{
return parent::create();
}
}}