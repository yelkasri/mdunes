<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_checkout_checkin_controller extends WController {
function checkin(){
$checkoutM=WModel::get('checkout');
$checkoutM->emptyTable();
$message=WMessage::get();
$message->userS('1209746216SXQF');
return true;
}}