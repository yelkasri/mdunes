<?php 


* @license GNU GPLv3 */

class Users_add_controller extends WController {
function add(){
$usersAddon=WAddon::get('users.'.WPref::load('PUSERS_NODE_FRAMEWORK_BE'));
if(!empty($usersAddon))$usersAddon->addUserRedirect();
return parent::add();
}
}