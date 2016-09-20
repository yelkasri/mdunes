<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('listing.datetime');
class WListing_CoreDateonly extends WListing_datetime {
protected $dateFormat='dateonly';
function create(){
$this->noTimeZone=true;
return parent::create();
}}
