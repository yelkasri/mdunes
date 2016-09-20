<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_CoreShow_profile_button extends WButtons_external {
function create(){
$usersAddon=WAddon::get('users.'.WPref::load('PUSERS_NODE_FRAMEWORK_FE'));
$link=$usersAddon->showUserProfile( WUser::get('uid'), true);
if(empty($link )) return false;
$link=str_replace('controller=','',$link );
$this->setAction($link );
return true;
}}