<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Api_Wp4_Admin_menu_class extends WClasses {
public function wpRun($appName=''){
$this->_showFrontEndMenus();
if(empty($appName )){
return false;
}
$this->_createAppBEmenu($appName, $appName.'_main', WExtension::get($appName.'.application','name'), JOOBI_SITE . JOOBI_FOLDER.'/node/'.$appName.'/images/'.$appName.'.png');
}
private function _createAppBEmenu($appName,$menuSlug='',$applicationName='',$iconURL=''){
$menuO=WView::get($menuSlug, 'html', null, null, false);
if(empty($menuO)) return false;
$capability='wzrole_'.$menuO->rolid;
$function='page_'.$appName;
WApplication_wp4::createClass($function, 'controller',$function );
WApplication_wp4::createClass($appName, 'controller',$function );
add_menu_page($applicationName,
$applicationName,
$capability,
$appName,
$function,
$iconURL );
$allSubMenusA=$menuO->elements;
if(!empty($allSubMenusA)){
if( JOOBI_MAIN_APP !=$appName && 'jvendor' !=$appName){
WText::load('api.node');
$parentSlug=$appName;
$link=$appName.'&task=welcome';
$ctrl='controller_'.str_replace( array('=','&'), array('_','__'), $link );
$function=$appName.'__'.$ctrl;
$action=$appName.'&controller='.$link;
$edit=add_submenu_page($parentSlug,
$appName.'_welcome',
WText::t('1206961889EEYJ'),
'wzrole_'.WRole::getRole('allusers','rolid'),
$action,
$function );
}else{
$mySpace=WGlobals::getSession('page','space', null );
if('vendors'==$mySpace ) WGlobals::setSession('page','space','admin');
}
$parentMenuA=array();
foreach($allSubMenusA as $subMenu){
if( IS_ADMIN && 'jvendor'== $appName){
if( in_array($subMenu->namekey, array('vendors_horizontalmenu_fe_logout','vendors_horizontalmenu_fe_backtocatalog')) ) continue;
}
if(!empty($subMenu->parent)){
continue;
}else{
$parentSlug=$appName;
$parentMenuA[$subMenu->mid]=$subMenu->namekey;
}
$link=$subMenu->action;
$subMenu->action=str_replace( array('-'), '_',$subMenu->action );
$ctrl='controller_'.str_replace( array('=','&'), array('_','__'), $subMenu->action );
$function=$appName.'__'.$ctrl;
$action=$appName.'&controller='.$link;
$edit=add_submenu_page($parentSlug,
$subMenu->namekey,
$subMenu->name,
'wzrole_'.$subMenu->rolid,
$action,
$function 
);
}
}
}
private function _showFrontEndMenus(){
static $onlyOnce=false;
if(!IS_ADMIN ) return false;
if($onlyOnce ) return true;
$onlyOnce=true;
if( WExtension::exist('users.node') && WRole::hasRole('register') && ! WRole::hasRole('manager')){
$this->_createUsersMenu();
}
if( WExtension::exist('vendors.node') && WRole::hasRole('vendor') && ! WRole::hasRole('storemanager')){
$this->_createVendorMenu();
}
}
private function _createUsersMenu(){
WText::load('api.node');
$appName=JOOBI_MAIN_APP;
$icon=JOOBI_SITE . JOOBI_FOLDER.'/node/'.$appName.'/images/'.$appName.'.png';
$this->_createAppBEmenu( JOOBI_MAIN_APP, 'users_node_horizontalmenu_fe', WText::t('1208359284QWNS'), $icon );
}
private function _createVendorMenu(){
WText::load('api.node');
$appName='jmarket';
$icon=JOOBI_SITE . JOOBI_FOLDER.'/node/'.$appName.'/images/'.$appName.'.png';
$this->_createAppBEmenu('jvendor','vendors_node_horizontalmenu_fe', WText::t('1221228435BYUA'), $icon );
}
}
