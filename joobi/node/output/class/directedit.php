<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Directedit_class {
private static $_onlyOnce=true;
public function editView($controller,$yid,$eid,$look=null){
$access=WPref::load('PMAIN_NODE_DIRECT_ACCESS');
if($access < 1 ) return '';
if(!WRole::hasRole($access )) return '';
$userEdit=WView::get($yid, 'useredit');
if(empty($userEdit)) return '';
switch($controller){
case 'form':
$imageIcon='edit-purple';
break;
case 'form-layout':
$imageIcon='edit-purple';
break;
case 'listing':
$imageIcon='edit-purple';
break;
case 'menu':
$imageIcon='edit-orange';
break;
case 'view':
$imageIcon='edit-green';
break;
default:
$imageIcon='edit-purple';
break;
}
$viewToIgnore=array('main_direct_translate_form','main_view_form','main_viewform_form','main_viewlisting_form','main_viewmenu_form');
$namekeyView=WView::get($yid, 'namekey');
if( in_array($namekeyView, $viewToIgnore)) return '';
$this->_addCSS();
$text=WText::t('1206732361LXFE'). ' '.ucfirst($controller );
$data=new stdClass;
$data->type='editView';
$data->icon=$imageIcon;
$data->text=$text;
$data->eid=$eid;
$data->look=$look;
$data->controller=$controller;
return WPage::renderBluePrint('others',$data );
}
public function translateView($controller,$yid,$eid,$text){
$access=WPref::load('PMAIN_NODE_DIRECT_ACCESS');
if($access < 1 ) return '';
if(!WRole::hasRole($access )) return '';
$viewToIgnore=array('main_direct_translate_form','main_view_form','main_viewform_form','main_viewlisting_form','main_viewmenu_form');
$namekeyView=WView::get($yid, 'namekey');
if( in_array($namekeyView, $viewToIgnore)) return '';
$this->_addCSS();
$textIcon=WText::t('1242822624LPVJ'). ' '.ucfirst($controller );
switch($controller){
case 'form':
$imageIcon='trans-purple';
break;
case 'form-layout':
$imageIcon='trans-purple';
break;
case 'listing':
$imageIcon='trans-purple';
break;
case 'menu':
$imageIcon='trans-orange';
break;
case 'view':
$imageIcon='trans-green';
break;
default:
$imageIcon='trans-purple';
break;
}
$data=new stdClass;
$data->type='translationView';
$data->icon=$imageIcon;
$data->text=$text;
$data->textIcon=$textIcon;
$data->eid=$eid;
$data->controller=$controller;
return WPage::renderBluePrint('others',$data );
}
public function editModule($moduleID,$title=''){
if( WRoles::isAdmin()) return '';
if(!WRole::hasRole('manager')) return '';
$data=new stdClass;
$data->type='editModule';
$data->title=$title;
$data->moduleID=$moduleID;
$button=WPage::renderBluePrint('others',$data );
return $button;
}
public function editWidget($widgetID,$title=''){
if( WRoles::isAdmin()) return '';
if(!WRole::hasRole('manager')) return '';
$data=new stdClass;
$data->type='editWidget';
$data->title=$title;
$data->widgetID=$widgetID;
$button=WPage::renderBluePrint('others',$data );
return $button;
}
private function _addCSS(){
if( self::$_onlyOnce){
if(!defined('JOOBI_URL_THEME_JOOBI')){
WView::definePath();
}
WPage::addCSSFile('css/directedit.css');
self::$_onlyOnce=false;
}
}
}