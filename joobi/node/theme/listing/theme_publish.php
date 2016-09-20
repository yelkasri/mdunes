<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('listing.publish');
class Theme_Theme_publish_listing extends WListing_publish {
function create(){
if($this->getValue('premium')==1){
$this->element->infonly=1;
}else{
unset($this->element->infonly);
}
return parent::create();
}}