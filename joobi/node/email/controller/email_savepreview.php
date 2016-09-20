<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
 class Email_Savepreview_controller extends WController {
function savepreview(){
$this->save();
$previewClass=WClass::get('email.preview');
$previewClass->preview();
}
}