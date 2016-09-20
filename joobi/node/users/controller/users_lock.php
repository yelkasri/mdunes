<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_lock_controller extends WController {
function lock(){
$eid=WGlobals::getEID(true);
$userLockC=WClass::get('users.api');
$status=$userLockC->blockUser( 1, $eid );
$TOTAL=count($eid );
if($TOTAL > 1)$this->userS('1401465958GTFO',array('$TOTAL'=>$TOTAL));
else {
$this->userS('1401465958GTFP');
}
return $status;
}
}