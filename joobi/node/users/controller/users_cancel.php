<?php 


* @license GNU GPLv3 */

class Users_cancel_controller extends WController {
function cancel(){
$status=parent::cancel();
if( WRoles::isAdmin('manager')) return  $status;
else {
$this->setView('users_dashboard');
return $status ;
}
}}