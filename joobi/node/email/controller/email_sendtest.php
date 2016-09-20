<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Email_Sendtest_controller extends WController {
function sendtest(){
$previewClass=WClass::get('email.preview');
$previewClass->preview();
$previewClass->sendTestEmail();
}
}