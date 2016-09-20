<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('listing.yesno');
class Theme_Theme_default_listing extends WListing_yesno {
function create(){
if($this->getValue('publish')==0){
$this->element->infonly=1;
}
return parent::create();
}
}