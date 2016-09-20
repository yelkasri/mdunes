<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Node_install extends WInstall {
private $_allThemeA=null;
public function install(&$object){
if(!empty($this->newInstall ) || (property_exists($object, 'newInstall') && $object->newInstall)){
WText::load('output.node');
$installWidgetsC=WClass::get('install.widgets');
$installWidgetsC->installWidgetType(
  'output.area'
  , "Tag use to replace a theme area"
  , WText::t('1434028549ILUR')
  , WText::t('1377041362DLCF')
  , 117);
$installWidgetsC->installWidgetType(
  'output.define'
  , "Tag to use constant"
  , WText::t('1379006374DSAU')
  , WText::t('1379006374DSBC')
  , 117);
$installWidgetsC->installWidgetType(
  'output.param'
  , "Tag to replace parameters"
  , WText::t('1377041362DLBN')
  , WText::t('1377041362DLCE')
  , 117);
$installWidgetsC->installWidgetType(
  'output.view'
  , "View's widget"
  , WText::t('1380567318QTUF')
  , WText::t('1379039059BHZA')
  , 117);
}
return true;
}
}