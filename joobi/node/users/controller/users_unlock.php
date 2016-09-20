<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_unlock_controller extends WController {
function unlock(){
$eid=WGlobals::getEID(true);
$userLockC=WClass::get('users.api');
$status=$userLockC->blockUser( 0, $eid );
$TOTAL=count($eid );
if($TOTAL > 1)$this->userS('1401465958GTFM',array('$TOTAL'=>$TOTAL));
else {
$this->userS('1401465958GTFN');
}
return $status;
}}