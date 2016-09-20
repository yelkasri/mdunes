<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_login_controller extends WController {
function login(){
$uid=WUser::get('uid');
$usersAddon=WAddon::get('users.'.WPref::load('PUSERS_NODE_FRAMEWORK_FE'));
if(empty($uid)){
$allowLogin=WPref::load('PUSERS_NODE_LOGINALLOW');
if(empty($allowLogin)){
$this->userW('1402327860NWWL');
return false;
}else{
$usersAddon->goLogin();
}
}else{
$usersAddon->goProfile($uid );
}
return true;
}
}