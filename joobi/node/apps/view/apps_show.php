<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_Apps_show_view extends Output_Forms_class {
protected function prepareView(){
$distribserver=WPref::load('PAPPS_NODE_DISTRIBSERVER');
if($distribserver==11){
$this->removeMenus( array('apps_show_custom_install','apps_show_custom_reinstall','apps_show_divider_install'));
}elseif($distribserver==99){
$message=WMessage::get();
$message->userN('1338581058MKNK');
}else{
}
$wid=WGlobals::getEID();
$appsShowC=WClass::get('apps.show');
$info=$appsShowC->checkInstalled($wid );
if(empty($info)){
}elseif($info->version !=$info->lversion){
$appsInfoC=WCLass::get('apps.info');
$status=$appsInfoC->possibleUpdate($wid );
if(!$status){
$this->removeMenus('apps_show_custom_install_now');
return true;
}
$this->changeElements('apps_show_custom_install_now','name', WText::t('1227580898LIDX'));
}
return true;
}
}