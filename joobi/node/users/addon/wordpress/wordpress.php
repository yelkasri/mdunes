<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WLoadFile('users.class.parent', JOOBI_DS_NODE );
class Users_Wordpress_addon extends Users_Parent_class {
function getPicklistElement(){
$wordpress=new stdClass;
$wordpress->option='wordpress';
$wordpress->label='wordpress';
$wordpress->extension='wordpress';
return $wordpress;
}
}