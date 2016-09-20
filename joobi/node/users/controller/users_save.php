<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_save_controller extends WController {
function save(){
$status=parent::save();
if( WRoles::isAdmin('manager')){
$this->setView('users_listings');
return  $status;
}else{
$this->setView('users_dashboard');
return $status;
}
}}