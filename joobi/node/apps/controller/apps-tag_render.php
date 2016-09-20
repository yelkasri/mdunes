<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_tag_render_controller extends WController {
function render(){
$id=WGlobals::get('id','','','namekey');
if(!empty($id)){
if( is_numeric($id)){
$widgetO=WGlobals::getSession('renderWidget','wdgt'.$id, null );
if(!empty($widgetO)){
$outputwidgetsC=WClass::get('output.widgets');
$html=$outputwidgetsC->renderWidget($widgetO->widgetID, $widgetO->nodeID, $widgetO->formName, $widgetO->yid, $widgetO->widgetSlug, 0, false);
echo $html;
return true;
}else{
$tag='{widget:alias|'.$id.'}';
}
}else{
$tag=$id;
}
}
if(empty($tag)){
WMessage::log('could not render the tag with id='.$id, 'widget-ajaxrender-error');
exit;
}
$html='';
$tagClass=WClass::get('output.process');
$tagClass->replaceTags($tag );
$html=$tag;
echo $html;
return true;
}
}