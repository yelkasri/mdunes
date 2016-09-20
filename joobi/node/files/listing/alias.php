<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_CoreAlias_listing extends WListings_default{
function create() {
if ( empty($this->value) ) $this->value = $this->getValue('name', 'files') . '.' . $this->getValue('type');
return parent::create();
}}