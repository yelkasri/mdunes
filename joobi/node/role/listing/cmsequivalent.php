<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Role_CoreCmsequivalent_listing extends WListings_default{
function create(){
$roleAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.role');
$column=$roleAddon->getColumnName();
$roleID=$this->getValue($column );
$this->content=$roleAddon->getRoleName($roleID );
return true;
}}