<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Node_install extends WInstall {
public function install(&$object){
try{
if(!empty($this->newInstall ) || (property_exists($object, 'newInstall') && $object->newInstall)){
$this->_defaultimage();
WText::load('users.node');
$installWidgetsC=WClass::get('install.widgets');
$installWidgetsC->installWidgetType(
  'users.user'
  , "User widget"
  , WText::t('1229653442FEQZ')
  , WText::t('1433381333KDQJ')
  , 7);
$CMSaddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.import');
$CMSaddon->importCMSUsers(false);
$prefM=WPref::get('users.node');
$prefM->updatePref('registrationrole', WRole::get('registered'));
$prefM->updatePref('frameworkrole', WRole::get('registered'));
if('joomla'==JOOBI_FRAMEWORK_TYPE){
$exist=WApplication::isEnabled('community', true);
if($exist){
$prefM->updatePref('framework_fe','jomsocial');
}}
}
} catch (Exception $e){
WMessage::log( "\n install <br> " . $e->getMessage(), 'users_install');
}
return true;
}
public function addExtensions(){
$extension=new stdClass;
$extension->namekey='users.user.plugin';
$extension->name='Joobi - User synchronization';
$extension->folder='user';
$extension->type=50;
$extension->publish=1;
$extension->certify=1;
$extension->destination='node|users|plugin';
$extension->core=1;
$extension->params='publish=1';
$extension->description='';
if($this->insertNewExtension($extension ))$this->installExtension($extension->namekey );
$extension=new stdClass;
$extension->namekey='users.system.plugin';
$extension->name='Joobi - Users Edit';
$extension->folder='system';
$extension->type=50;
$extension->publish=1;
$extension->certify=1;
$extension->destination='node|users|plugin';
$extension->core=1;
$extension->params='publish=1';
$extension->description='';
if($this->insertNewExtension($extension ))$this->installExtension($extension->namekey );
}
private function _defaultimage(){
try{
$imageM=WModel::get('files');
$imageM->whereE('name','userx');
$imageID=$imageM->load('lr','filid');
$usersM=WModel::get('users');
if(empty($imageID)){
$usersM->noValidate();
$usersM->saveItemMoveFile( JOOBI_DS_NODE.'users'.DS.'install'.DS.'images'.DS.'userx.png','', true, null, false);
$imageM=WModel::get('files');
$imageM->whereIn('name',array('userx'));
$imageM->setVal('core', 1 );
$imageM->update();
}
$usersM->where('email','=','');
$usersM->delete();
} catch (Exception $e){
WMessage::log( "\n _defaultimage <br> ".$e->getMessage(), 'users_install');
}
}
}