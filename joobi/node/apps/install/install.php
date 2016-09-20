<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_Node_install extends WInstall {
public function install($object){
$pref=WPref::get('install.node');
$appsUserinfosM=WModel::get('apps.userinfos');
$appsUserinfosM->makeLJ('apps','wid');
$appsUserinfosM->where('expire','>', time());
$appsUserinfosM->whereE('enabled', 1 );
$appsUserinfosM->whereE('type', 1, 1 );
$appsUserinfosM->where('namekey','!=','jiptracker.application', 1 );$appsUserinfosM->select('name', 1 );
$appsUserinfosM->orderBy('name','ASC', 1 );
$allAppsA=$appsUserinfosM->load('lra');
if(!empty($allAppsA)){
$appsInstallC=WClass::get('apps.install');
$count=1;
foreach($allAppsA as $oneApp){
$appsInstallC->createDashboardMenu($oneApp, $count );
$count++;
}
}
if(!empty($this->newInstall) || (property_exists($object, 'newInstall') && $object->newInstall)){
$this->_checkMultiLanguageSite();
$this->_insertDefaultPreferences4Themes();
}else{
$folderS=WGet::folder();
$folderA=$folderS->folders( JOOBI_DS_USERS );
if(!empty($folderA)){
foreach($folderA as $folder){
$num=substr($folder, 1 );
if( is_numeric($num)) continue;
$uid=WUsers::get('uid',$folder );
if(empty($uid)) continue;
$folderS->move( JOOBI_DS_USERS . $folder, JOOBI_DS_USERS.'u'.$uid );
}}
}
$appsM=WModel::get('apps');
$appsM->where('type','>=', 60 );
$appsM->where('type','<=', 80 );
$appsM->deleteAll();
}
public function addExtensions(){
$extension=new stdClass;
$extension->namekey='apps.system.plugin';
$extension->name='Joobi - Debug Traces';
$extension->folder='system';
$extension->type=50;
$extension->publish=1;
$extension->certify=1;
$extension->destination='node|apps|plugin';
$extension->core=1;
$extension->params='publish=1';
$extension->description='This is a plugin to see all the debug traces at the bottom of the page.';
if($this->insertNewExtension($extension ))$this->installExtension($extension->namekey );
}
private function _checkMultiLanguageSite(){
$languagesCMS=APIApplication::cmsAvailLang();
WMessage::log('_checkMultiLanguageSite 1','check-Multi-LanguageSite');
WMessage::log($languagesCMS, 'check-Multi-LanguageSite');
$languagesA=WApplication::availLanguages('lgid','all');
WMessage::log($languagesA, 'check-Multi-LanguageSite');
if( count($languagesCMS ) > 1){
$pref=WPref::get('library.node');
$pref->updatePref('multilang', 1 );
WMessage::log('_checkMultiLanguageSite 2','check-Multi-LanguageSite');
$cache=WCache::get();
$cache->resetCache('Preference');
}
}
private function _insertDefaultPreferences4Themes(){
$joomla30='font_awesome=1
image_responsive=1
image_style=rounded
nav_logoname=app-joobi-logo
nav_brand=Nav
nav_uselogo=1
pagination=11
nav_showicon=1
form_tabfade=1
pane_color=1
pane_icon=1
tooltip_html=1
alert_dismiss=1
alert_collapse=1
button_icon=1
button_color=1
view_icon=1
wizard_color=1
toolbar_color=1
toolbar_group=1
toolbar_icon=1
table_maxlist=20
table_hover=1
table_striped=1
table_columniconcolor=1
table_buttonicon=1
table_buttontext=1';
$themeM=WModel::get('theme');
$themeM->whereIn('namekey',array('joomla30.admin.theme','wp40.admin.theme'));
$themeM->setVal('params',$joomla30 );
$themeM->update();
}
}