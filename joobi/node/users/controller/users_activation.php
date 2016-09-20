<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_activation_controller extends WController {
function activation(){
$activation=WGlobals::get('active');
$uid=WGlobals::get('id');
$usersM=WModel::get('users');
$usersM->whereE('uid',$uid );
$usersM->whereE('activation',$activation );
$exist=$usersM->load('lr','uid');
if(empty($exist)){
$this->userE('1407807686JAZD');
return false;
}
$usersM->whereE('uid',$uid );
$usersM->setVal('activation','');
$usersM->setVal('blocked', 0 );
$usersM->setVal('confirmed', 1 );
$usersM->update();
$usersAddon=WClass::get('users.api');
$usersAddon->blockUser( 0, $uid );
$subscribeEvent='register';
$obj=new stdClass;
$obj->event=$subscribeEvent;
WEvent::get('users.registration',$uid, $obj );
$this->userS('1407807686JAZE');
WPages::redirect('controller=users&task=dashboard');
return true;
}
}