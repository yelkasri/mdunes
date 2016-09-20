<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Api_Wp4_Widget_addon {
public function renderWidget($argumentsA,$instance,$id_base){
static $onlyOnce=true;
if(empty($id_base)) return '';
if($onlyOnce){
if(!defined('JOOBI_URL_THEME_JOOBI')) WView::definePath();
WPage::addCSSFile('css/style.css');
WPage::addJSLibrary('rootscript');
$onlyOnce=false;
}
extract($argumentsA );
$len=strlen( JOOBI_PREFIX ) + 1;
$namekey=substr($id_base, $len );
$extensionO=new stdClass;
$extensionO->namekey=str_replace('_','.',$namekey );
if(empty($extensionO)) return '';
$moduleHTML='';
$params=new stdClass;
$params->widget_id=$widget_id;
$modLayout=WExtension::module($extensionO->namekey, $params );
if($modLayout){
$modLayout->wid=WExtension::get($extensionO->namekey, 'wid');
$moduleHTML=$modLayout->make();
}
if(empty($moduleHTML)) return '';
$title=apply_filters('widget_title', (empty($instance['title'] )?'' : $instance['title'] ), $instance, $id_base );
$html='<div class="oneWidget">';
if($title)$html .='<h3 class="g-title">'.$title.'</h3>'; $html .=$moduleHTML;
$html .='</div>';
$css=JoobiWP::renderCSS();
$js=JoobiWP::renderJS();
return $css . $js . $html;
}
}
class WModule extends WElement {
public function __construct($params){
$this->module=new stdClass;
if(!empty($params)){
foreach($params as $k=> $v)$this->$k=$v;
}
WGlobals::set('pageHasWidgets', false, 'global');
$eWidgetO=$this->_loadWidget($this->widget_id );
if(empty($eWidgetO->widgetid)){
$libraryCMSMenuC=WAddon::get('api.'.JOOBI_FRAMEWORK.'.cmsmenu');
$status=$libraryCMSMenuC->editExtensionPreferences($this->widget_id );
$eWidgetO=$this->_loadWidget($this->widget_id );
}
if(!empty($eWidgetO->params)){
$this->params=$eWidgetO->params;
WTools::getParams($this );
}
if(!isset($this->module ))$this->module=new stdClass;
if(!empty($eWidgetO->namekey))$widget_idA=explode('-',$eWidgetO->namekey );
else $widget_idA=explode('-',$this->widget_id );
if(!empty($eWidgetO->widgetid))$this->module->id=$eWidgetO->widgetid;
else $this->module->id=(!empty($widget_idA[1])?$widget_idA[1] : $widget_idA[0] );
$this->module->module=$widget_idA[0];
$this->suffix=(isset($this->moduleclass_sfx)?$this->moduleclass_sfx : ''  );
$this->classes='WPwidget';
$themeExist=WView::initializeTheme();
if(true !==$themeExist ) return $themeExist;
}
public function make($notUsed1=null,$notUsed2=null){
$this->create();
$direct_edit_modules=WPref::load('PMAIN_NODE_DIRECT_EDIT_MODULES');
if(!empty($direct_edit_modules )){
$directEditClass=WClass::get('output.directedit');
$this->content=$directEditClass->editModule($this->module->id ). $this->content;
}
if(!empty($this->content)){
$className='stdWidget';
if(!empty($this->module->name))$className .=' '.$this->module->name;
if(!empty($this->module->id))$className .=' wdgt_'.$this->module->id;
$this->content='<div class="'.$className.'">'.$this->content .'</div>'; }
$status=$this->display();
WPage::declareJS();
return $status;
}
private function _loadWidget($moduleId){
$mainWidgetM=WModel::get('library.widget');
$widgetO=$mainWidgetM->loadMemory($moduleId, null, false);
return $widgetO;
}
}