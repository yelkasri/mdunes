<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_System_plugin extends WPlugin {
function onAfterInitialise(){
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
$usersAddon->onAfterInitialise();
}
}