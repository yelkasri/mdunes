<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Output_Space_class extends WClasses {
public function findSpace(){
static $spaceO=null;
if(isset($spaceO)) return $spaceO;
$mySpace=WGlobals::get('space');
if(empty($mySpace))$mySpace=WGlobals::getSession('page','space','');
if(!empty($mySpace)){
WGlobals::setSession('page','space',$mySpace );
}else{
$mySpace='site';
}
$spaceM=WModel::get('space');
$spaceO=$spaceM->loadMemory($mySpace );
if(!IS_ADMIN && empty($spaceO)){
$this->adminN('There is no space defined!');
}
if(!empty($spaceO->theme )){
$explodeA=explode('.',$spaceO->theme );
$spaceO->themeFolder=array_shift($explodeA );
$spaceO->themeTheme=array_shift($explodeA );
WLoadFile('library.class.theme');
$spaceO->themeType=WTheme::get($spaceO->theme, 'type');
if(empty($spaceO->frameworktheme)){
WGlobals::set('tmpl','component');
}
}else{
$spaceO=new stdClass;
$spaceO->themeFolder='joomla30';
$spaceO->themeTheme='site';
$spaceO->themeType=1;
$spaceO->controller='';
if('site' !=$mySpace){
WGlobals::set('tmpl','component');
}
}
WGlobals::setSession('page','space',$mySpace );
if(!empty($spaceO->controller))$spaceO->controller=str_replace('controller=','',$spaceO->controller );
return $spaceO;
}
}