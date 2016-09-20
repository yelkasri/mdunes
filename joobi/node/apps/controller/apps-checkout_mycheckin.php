<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_checkout_mycheckin_controller extends WController {
function mycheckin(){
$checkoutC=WClass::get('apps.checkout');
$uid=WUser::get('uid');
$status=$checkoutC->memberCheckin($uid );
$sid=WModel::get('checkout','sid');
return $this->showM($status ,  'checkin', 1,$sid );
}}