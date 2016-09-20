<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_Email_mail_preview_view extends Output_Forms_class {
function prepareView(){
$emailType=WPref::load('PEMAIL_NODE_TYPE');
switch ($emailType){
case 'html':
$this->removeElements('mail_preview_mailtrans_ctext');
break;
case 'text':
$this->removeElements('mail_preview_mailtrans_chtml');
break;
default:
break;
}
return true;
}}