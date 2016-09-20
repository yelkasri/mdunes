<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Framework_Page_addon {
private $_metatagA=array();
private $_sep='';
private $_debug=false;
public function createPage($html,$headerA,$debug=false){
$this->_debug=$debug;
$this->_createMetaTag($headerA );
$lang=(!empty($headerA['lang'] )?$headerA['lang'] : 'en-GB');
$dir=(!empty($headerA['dir'] )?$headerA['dir'] : 'ltr');
$this->_debug=(( JOOBI_DEBUGCMS || WPref::load('PLIBRARY_NODE_DBGERR'))?true : false);
if($this->_debug)$this->_sep="\n";
$page='<!doctype html>';
$page .=$this->_sep.'<html dir="'.$dir.'" lang="'.$lang.'">';
$page .=$this->_sep.'<head>';
$title=(!empty($headerA['title'])?$headerA['title'] : JOOBI_SITE_NAME );
$page .=$this->_sep.'<title>'.$title.'</title> ';
if(!empty($this->_metatagA )){
$page .=$this->_sep . implode($this->_sep, $this->_metatagA );
}
$page .=$this->_sep.'</head>';
$page .=$this->_sep.'<body>';
$page .=$html;
$page .=$this->_sep.'</body>';
$page .=$this->_sep.'</html>';
return $page;
}
private function _createMetaTag($headerA){
$this->_metatagA[]='<meta charset="utf-8">';
$this->_metatagA[]='<meta http-equiv="X-UA-Compatible" content="IE=edge">';
if(!WUser::isRegistered()){
if(!empty($headerA['desc']))$this->_metatagA[]=$this->_addMetagTag('description',$headerA['desc'] );
if(!empty($headerA['others'])){
foreach($headerA['others'] as $key=> $content){
$this->_metatagA[]=$this->_addMetagTag($key, $content );
}}
if(!empty($headerA['generator'])){
$gen=$headerA['generator'];
}else{
$gen='Joobi '.WExtension::get( JOOBI_MAIN_APP.'.application','userversion');
}$this->_metatagA[]=$this->_addMetagTag('generator',$gen );
}
if(!empty($headerA['js'] )){
foreach($headerA['js'] as $js=> $notUsed){
$this->_metatagA[]=$this->_sep.'<script src="'.str_replace('&','&amp;',$js ). '" type="text/javascript"></script>';
}}
if(!empty($headerA['css'] )){
foreach($headerA['css'] as $css=> $notUsed){
$this->_metatagA[]=$this->_sep.'<link href="'.str_replace('&','&amp;',$css ). '" rel="stylesheet">';
}}
if(!empty($headerA['css_sc'] )){
$cssString=implode($this->_sep, array_keys($headerA['css_sc']));
if(!$this->_debug){
$cssString=str_replace( array( "\t", '  ') , ' ',$cssString );
}$this->_metatagA[]=$this->_sep.'<style media="screen" type="text/css">'.$cssString.'</style>';
}
if(!empty($headerA['js_sc'] )){
$jsString=implode($this->_sep, array_keys($headerA['js_sc']));
if(!$this->_debug){
$jsString=str_replace( array( "\t", '  ') , ' ',$jsString );
}$this->_metatagA[]=$this->_sep.'<script type="text/javascript">'.$jsString.'</script>';
}
if(!empty($headerA['link'])){
foreach($headerA['others'] as $obj){
if(!empty($obj->extraA )){
$extra=' ';
foreach($obj->extraA as $key=> $content){
$extra=' '.$key.'="'.$content.'"';
}}else{
$extra='';
}$this->_metatagA[]='<link '.$obj->relType.'="'.$obj->relation.'" href="'.$obj->link.'"'.$extra.' />';
}}
$this->_metatagA[]=$this->_addMetagTag('viewport','width=device-width, initial-scale=1');
}
private function _addMetagTag($name,$content){
return '<meta name="'.$name.'" content="'.$content.'" />';
}
function __destruct(){
return true;
}
}