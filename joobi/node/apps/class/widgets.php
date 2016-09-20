<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Apps_Widgets_class extends WClasses {
function publish($widgetID,$state=false){
$APLLICATION='';
if(!$this->_checkWidget($APLLICATION,$state,$widgetID)){
$message=WMessage::get();
$message->userN('1418159318SHOQ',array('$APLLICATION'=>$APLLICATION));
return false;
}
$widgetID=WExtension::get($widgetID,'wid');
$this->_publishElements($widgetID, $state );
$appsDependencyM=WModel::get('install.appsdependency');
$appsDependencyM->select('ref_wid');
$appsDependencyM->whereE('wid',$widgetID );
$arrayWIDS=$appsDependencyM->load('lra');
$modelExt=WModel::get('apps.userinfos');
$modelExt->setVal('wid',$widgetID);
$modelExt->setVal('enabled',$state);
$modelExt->setIgnore();
$modelExt->insert();
if(!$modelExt->affectedRows()){
$modelExt->whereE('wid',$widgetID);
$modelExt->setVal('enabled',$state);
$modelExt->update();
}
foreach($arrayWIDS as $wid){
$extension=WExtension::get($wid,'data');
switch($extension->type){
case 25 : $this->_publishMP($extension->namekey,'module',$state);
break;
case 50 : $this->_publishMP($extension->namekey,'plugin',$state);
break;
case 60 : $this->_publishAction($wid,$state);
default:
break;
}}
if(!$state)
{
$message=WMessage::get();
$message->userS('1215710386RMJC');
}else{
$message=WMessage::get();
$message->userS('1215710386RMJD');
}
return true;
}
function _publishElements($wid,$state){
$sql=WModel::get('apps','object');
$sql->whereE('wid',$wid);
$ext=$sql->load('o');
if(empty($ext)) return false;
if(empty($ext->install)){
$path=JOOBI_DS_JOOBI . str_replace('|',DS,$ext->destination).DS.$ext->folder.DS.'data.xml';
$handler=WGet::file();
if($handler->exist($path)){
$ext->install=$handler->read($path);
}}
if(empty($ext->install)){
return true;
}
$xml=WClass::get('install.xml');
$xml->setParent('role', 1 );
$xml->setParent('languages', 1 );
$xml->setParent('extension',$wid);
$xml->setMode(!$state);
return $xml->parse($ext->install);
}
function _publishMP($namekey,$type,$state){
$object=new stdClass;
$object->name=$namekey;
$object->type=$type;
$object->joobi=true;
$object->publish=$state;
WApplication::setWidget($object);
return true;
}
function _publishAction($wid,$state){
$action=WModel::get('library.action','object');
$action->select('actid');
$action->whereE('wid',$wid );
$actids=$action->load('lra');
if(empty($actids)){
WMessage::log('Could not find any actions for the extension '.$wid,'actions.publish');
return true;
}
$action->setVal('publish',$state);
$action->whereIn('actid',$actids);
$action->update();
$actiontrigger=WModel::get('library.controlleraction','object');$actiontrigger->setVal('publish',$state);
$actiontrigger->whereIn('actid',$actids);
$actiontrigger->update();
return true;
}
private function _checkWidget(&$component,$state,$widgetID){
$extension=WExtension::get($widgetID,'data');
if(empty($extension)) return false;
$parts=explode('.',$extension->namekey,2);
$class=ucfirst($parts[0]).'_Widget_install';
if(!class_exists($class)){
$file=JOOBI_DS_NODE . $extension->folder.DS.'install'.DS.'install.php';
$fileHandler=WGet::file();
if($fileHandler->exist($file)){
include_once($file );
}}
if(class_exists($class)){
$obj=new $class();
if($state==true){
$name='checkPublish';
}else{
$name='checkUnpublish';
}
if(method_exists($obj,$name)){
return $obj->$name($component);
}
}
return true;
}
}
