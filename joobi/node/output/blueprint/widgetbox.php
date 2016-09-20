<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WRender_Widgetbox_classData {
public $title='';
public $content='';
public $id=null;
public $headerRightA=array();
public $headerCenterA=array();
public $bottomRightA=array();
public $bottomCenterA=array();
public $faicon=null;
public $color=null;
public $class=null;
}
class WRender_Widgetbox_blueprint extends Theme_Render_class {
private static $_paneIcon=null;
private static $_paneColor=null;
  public function render($data){
  if(empty($data->content) || '<div></div>'==$data->content ) return '';
$headerRight=WGlobals::get('widgetBoxHeaderRight','','global');
if(!empty($headerRight)){
$data->headerRightA[]=$headerRight;
WGlobals::set('widgetBoxHeaderRight','','global');
}
$widgetBoxClass=WGlobals::get('widgetBoxClass','','global');
if(!empty($widgetBoxClass)){
if(!empty($data->class))$data->class .=' ';
$data->class .=$widgetBoxClass;
WGlobals::set('widgetBoxClass','','global');
}
if(!empty($data->element->fid)){
if('edit'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$editButton=$outputDirectEditC->editView('form',$this->yid, $data->element->fid, 'form-layout');
if(!empty($editButton))$data->params->text=$editButton . $data->params->text;
}elseif('translate'==WPref::load('PMAIN_NODE_DIRECT_MODIFY')){
$outputDirectEditC=WClass::get('output.directedit');
$editButton=$outputDirectEditC->translateView('form',$this->yid, $data->element->fid, $data->params->text );
if(!empty($editButton))$data->params->text=$editButton . $data->params->text;
}
$nagivation=WGlobals::get('paginationFormElementNav'.$data->element->fid, '','global');
WGlobals::set('paginationFormElementNav'.$data->element->fid, '','global');
$NbDisplay=WGlobals::get('paginationFormElementDisplay'.$data->element->fid, '','global');
WGlobals::set('paginationFormElementDisplay'.$data->element->fid, '','global');
}
$panel=WPage::newBluePrint('panel');
$panel->type=$this->value('catalog.container');
$panel->id=(!empty($data->id)?$data->id : (!empty($data->params->idText)?$data->params->idText : null ));
$panel->faicon=(!empty($data->faicon)?$data->faicon : (!empty($data->element->faicon)?$data->element->faicon : null ));
$panel->color=(!empty($data->color)?$data->color : (!empty($data->element->color)?$data->element->color : null ));
if(empty($data->element->notitle ) && empty($data->element->spantit ) && !empty($data->params->text)){
$panel->header=$data->params->text;
}else{
$panel->header=$data->title;
}
if(!empty($data->class)){
if(!empty($panel->class))$panel->class .=' ';
$panel->class .=$data->class;
}
if(!empty($data->element->class)){
if(!empty($panel->class))$panel->class .=' ';
$panel->class .=$data->element->class;
}
if(empty($panel->class))$panel->class='widgetBox';
$panel->body=$data->content;
if(!empty($data->headerRightA))$panel->headerRightA=$data->headerRightA;
if(empty($data->element->spantit ) && !empty($nagivation)){
$panel->headerRightA[]=$nagivation;
}
if(!empty($data->headerCenterA))$panel->headerCenterA=$data->headerCenterA;
if(!empty($data->bottomRightA))$panel->bottomRightA=$data->bottomRightA;
if(!empty($data->bottomCenterA))$panel->bottomCenterA=$data->bottomCenterA;
return WPage::renderBluePrint('panel',$panel );
  }
}