<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_Apps_update_all_view extends Output_Forms_class {
protected function prepareView(){
$distribserver=WPref::load('PAPPS_NODE_DISTRIBSERVER');
if($distribserver==11){
$this->removeMenus( array('apps_update_all_install','apps_update_all_reinstall','apps_update_all_divider_install'));
}elseif($distribserver==99){
$message=WMessage::get();
$message->userN('1338581058MKNK');
}else{
}
$eid=WGlobals::getEID();
$appsInfoC=WCLass::get('apps.info');
$status=$appsInfoC->possibleUpdate($eid );
if(!$status){
$this->removeMenus( array('apps_update_all_install','apps_update_all_reinstall','apps_update_all_divider_install'));
}
return true;
}}