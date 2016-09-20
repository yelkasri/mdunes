<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_View_tag {
function process($givenTagsA){
$replacedTagsA=array();
foreach($givenTagsA as $tag=> $myTagO){
if(empty($myTagO->name)) continue;
if(empty($myTagO->controller)) continue;
if(empty($myTagO->extension)) continue;
$controller=new stdClass;
$controller->controller=$myTagO->controller;
$controller->wid=WExtension::get($myTagO->extension, 'wid');
if(!empty($myTagO->model))$controller->sid=WModel::get($myTagO->model, 'sid');
$replaceBackEID=false;
$params=new stdClass;
if(!empty($myTagO->eid) && is_numeric($myTagO->eid )){
$params->_eid=$myTagO->eid; $replaceBackEID=true;
$eid=WGlobals::get('eid');
WGlobals::set('eid' , $myTagO->eid );
}
$view=WView::getHTML($myTagO->name, $controller, $params );
if(empty($view)){
$myTagO->wdgtContent='';
if($replaceBackEID ) WGlobals::set('eid' , $eid );
continue;
}
$myTagO->wdgtContent=$view->make();
$replacedTagsA[$tag]=$myTagO;
if($replaceBackEID ) WGlobals::set('eid' , $eid );
if(!empty($view->cssfile)){
$widCat=WExtension::get($view->folder.'.node','folder');
if(!empty($widCat)){
WPage::addCSSFile('tag/'.$widCat.'/css/style.css');
}
}
}
return $replacedTagsA;
}
 }