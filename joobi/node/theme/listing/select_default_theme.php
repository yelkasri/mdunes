<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('listing.radio');
class Theme_Select_default_theme_listing extends WListing_radio {
function create(){
if($this->getValue('premium')==1){
$this->checked=true ;
}
parent::create();
return true;
}}