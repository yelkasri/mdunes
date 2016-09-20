<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Translation_Importedit_controller extends WController {
function importedit(){
WPage::addJSLibrary('rootscript');
WPage::addJSFile('js/install.1.1.js');
$finish=WGlobals::get('finish', 0);
if(!empty($finish)){
$message=WMessage::get();
$message->userS('1239875557LNEW');
return true;
}
$autotrigger=WGlobals::get('run', 0);
if(!empty($autotrigger)){
$link=WPage::routeURL('controller=translation&task=importexec&run='.$autotrigger , 'smart','popup');
$message=WText::t('1227580123BQIA');
$javascript='jextinstup(\'BIGMSG['.$message.']\',\''.$link.'\',\'\');';
WPage::addJSScript($javascript, 'jquery');
}
return true;
}
}