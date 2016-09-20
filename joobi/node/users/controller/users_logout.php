<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_logout_controller extends WController {
function logout(){
$usersAddon=WAddon::get('users.'.JOOBI_FRAMEWORK );
return $usersAddon->goLogout();
}
}