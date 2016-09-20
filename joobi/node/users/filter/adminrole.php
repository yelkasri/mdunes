<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Adminrole_filter {
function create(){
if( WRole::hasRole('sadmin')) return false;
return true;
}
}