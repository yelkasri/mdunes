<?php 


* @license GNU GPLv3 */

class Users_home_controller extends WController {
function home(){
$isRegistered=WUser::isRegistered();
if(!empty($isRegistered)){
$itemId=WPage::getPageId('users','dashboard');
WPages::redirect('controller=users&task=dashboard',$itemId );
}
if(!defined('PUSERS_NODE_FRAMEWORK_FE')) WPref::get('users.node', false, true, false);
$usersAddon=WAddon::get('users.'.PUSERS_NODE_FRAMEWORK_FE );
$usersAddon->goLogin();
return true;
}
}