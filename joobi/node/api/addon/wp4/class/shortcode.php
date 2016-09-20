<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Api_Wp4_Shortcode_class extends WClasses {
private $appName=null;
public function wpRun($array){
}
public function joobipage($attributes=null){
if(empty($attributes)) return;
$pageRendered=WGlobals::get('pageRendered', false, 'global');
if(!empty($pageRendered)) return '';
$defaults_array=array('id');
shortcode_atts($defaults_array, $attributes );
if(empty($attributes['id'])) return;
$id=$attributes['id'];
if( strpos($id, '__') !==false){
$explodeIDA=explode('__',$id );
if(!empty($explodeIDA)){
foreach($explodeIDA as $oneP){
if($oneP==JOOBI_PREFIX ) continue;
$explodePA=explode('_',$oneP );
if( count($explodePA) > 1){
if($explodePA[0]=='ctrl')$explodePA[0]='controller';
WGlobals::set($explodePA[0], $explodePA[1] );
}
}
}
}else{
$tag='{widget:alias|'.$id.'}';
$tagProcessC=WClass::get('output.process');
$tagProcessC->replaceTags($tag );
$css=JoobiWP::renderCSS();
$js=JoobiWP::renderJS();
echo $css . $js . $content;
return;
}
$params=null;
$namekey='';
WGlobals::set('resetForm','yes','global');
$content=WGet::startApplication('application',$namekey, $params );
$css=JoobiWP::renderCSS();
$js=JoobiWP::renderJS();
echo $css . $js . $content;
return;
}
public function joobiwidget($attributes=null){
if(empty($attributes)) return;
$defaults_array=array('id');
shortcode_atts($defaults_array, $attributes );
if(empty($attributes['id'])) return;
$exit=WLoadFile( "api.addon." . JOOBI_FRAMEWORK . ".widget", JOOBI_DS_NODE );
$id=$attributes['id'];
$tag='{widget:alias|'.$id.'}';
$tagProcessC=WClass::get('output.process');
$tagProcessC->replaceTags($tag );
$css=JoobiWP::renderCSS();
$js=JoobiWP::renderJS();
return $css . $js . $tag;
}
}