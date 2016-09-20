<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Role_System_plugin extends WPlugin {
function onAfterRoute(){
if( JOOBI_FRAMEWORK_TYPE !='joomla') return;
 if(!WExtension::exist('subscription.node')) return;
$option=WApplication::name('com');
$roleSectionsM=WModel::get('joomla.extensions');
$roleSectionsM->makeLJ('role.components','extension_id','id');
$roleSectionsM->whereE('element',$option, 0 );
$roleSectionsM->select( array('rolid','site','admin'), 1 );
$result=$roleSectionsM->load('o');
if(!empty($result)){
if( WRoles::isAdmin()){
if(empty($result->admin)) return;
}else{
if(empty($result->site)) return;
}
$rolid=WUser::roles();
if(!in_array($result->rolid, $rolid )){
$message=WMessage::get();
$message->userW('1206732348RCNT');
$redirect=WPref::load('PSUBSCRIPTION_NODE_COMPREDIRECTLINK');
if(true){
$url=(!empty($redirect)?$redirect : WPage::linkHome('controller=subscription&task=possible&rolid='. $result->rolid ));
}else{
$url=WPages::linkHome('controller=subscription&task=invalid', WPages::getPageId('catalog'));
}WPages::redirect( ltrim($url, '/'));
}
}
}
}