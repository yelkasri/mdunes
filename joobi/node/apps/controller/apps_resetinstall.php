<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_resetinstall_controller extends WController {
function resetinstall(){
$install=WClass::get('install.processnew');
if(!class_exists('Install_Node_install')) WLoadFile('install.install.install', JOOBI_DS_NODE );
$install->clean();
$_SESSION['joobi']['sleep_value']=5;
$message=WMessage::get();
$message->userS('1359218479BLAZ');
WPages::redirect('controller=apps');
return true;
}
}