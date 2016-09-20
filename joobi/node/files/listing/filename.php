<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_CoreFilename_listing extends WListings_default{
function create() {
$myType = $this->getValue('type');
$this->value = $this->getValue('name', 'files');
if ( $myType != 'url' ) {
	$this->value .= '.' . $this->getValue('type');
}
return parent::create();
}}