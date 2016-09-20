<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Users_show_view extends Output_Forms_class {
function prepareView(){
$approvalRequired=WGlobals::get('approval');
if(empty($approvalRequired)){
$this->removeMenus( array('users_show_disapproval','users_show_approval','users_show_approval_divider'));
}
return true;
}}