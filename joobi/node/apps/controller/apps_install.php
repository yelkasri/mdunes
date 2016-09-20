<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_install_controller extends WController {
public function install(){
$systemFolderC=WGet::folder();
$FOLDER=JOOBI_DS_USER.'installfiles';
if($systemFolderC->exist($FOLDER )){
$this->userN('1249562683MMMY',array('$FOLDER'=>$FOLDER));
return true;
}
WPage::addJSLibrary('rootscript');
WPage::addJSFile('js/install.1.1.js');
$finish=WGlobals::get('finish', 0 );
if(!empty($finish)){
$this->userS('1298294132BJZM');
return true;
}
$autotrigger=WGlobals::get('run', 0 );
if(!empty($autotrigger)){
$link=WPage::routeURL('controller=apps&task=instup','smart','popup');
$message=WText::t('1247466190MROL');
$javascript='jextinstup(\'BIGMSG['.$message.']\',\''.$link.'\',\'\');';
WPage::addJSScript($javascript);
}
}
}