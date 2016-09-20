<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_loginregister_controller extends WController {
function loginregister(){
$isRegistered=WUser::isRegistered();
if(!empty($isRegistered)){
$uid=WUser::get('uid');
$usersAddon=WAddon::get('users.'.WPref::load('PUSERS_NODE_FRAMEWORK_FE'));
$usersAddon->goProfile($uid );
}
return true;
}
}