<?php 


* @license GNU GPLv3 */

class Users_System_plugin extends WPlugin {
function onAfterInitialise(){
$usersAddon=WAddon::get('api.'. JOOBI_FRAMEWORK.'.user');
$usersAddon->onAfterInitialise();
}
}