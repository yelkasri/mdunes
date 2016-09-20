<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Api_Joomla30_Editor_addon extends WClasses {
public $cols=80;
public $rows=10;
public function display(){
$editor=JFactory::getEditor($this->editorName );
$editor->initialise();
$html=$editor->display($this->name, $this->content ,$this->width, $this->height, $this->cols, $this->rows, $this->showButtons );
return $html;
}
public function getEditorName(){
static $joomEditors=null;
if(!isset($joomEditors)){
$plugin=WModel::get('joomla.extensions');
$plugin->whereE('folder','editors');
$plugin->whereE('type','plugin');
$plugin->whereE('enabled',1);
$plugin->orderBy('ordering');
$plugin->orderBy('name');
$plugin->setLimit( 100 );
$joomEditors=$plugin->load('ol',array('element','name'));
}
$editors=array();
$editors['framework.']=WText::t('1352226844OYVM');
if(!empty($joomEditors)){
foreach($joomEditors as $myEditor){
$editName=str_replace( array('_','plg','editors'), array(' ','', WText::t('1211280059QYRJ')), $myEditor->name );
$editors['framework.'.$myEditor->element]=$editName;
}
}
return $editors;
}
}