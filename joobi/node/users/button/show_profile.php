<?php 


* @license GNU GPLv3 */

class Users_CoreShow_profile_button extends WButtons_external {
function create(){
$usersAddon=WAddon::get('users.'.WPref::load('PUSERS_NODE_FRAMEWORK_FE'));
$link=$usersAddon->showUserProfile( WUser::get('uid'), true);
if(empty($link )) return false;
$link=str_replace('controller=','',$link );
$this->setAction($link );
return true;
}}