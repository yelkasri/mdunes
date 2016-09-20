<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Mobilev1_Page_addon {
private $_metatagA=array();
private $_sep='';
private $_debug=false;
public function createPage($html,$headerA,$debug=false){
$this->_debug=$debug;
if( JOOBI_FRAMEWORK_FORMAT_OUPUT !='browser')$html=$this->_formatLinks4Mobile($html );
$lang=(!empty($headerA['lang'] )?$headerA['lang'] : 'en-GB');
$dir=(!empty($headerA['dir'] )?$headerA['dir'] : 'ltr');
if($this->_debug){
$this->_sep="\n";
}
$page='';
if( JOOBI_FRAMEWORK_FORMAT_OUPUT=='browser'){
if(!empty($this->_metatagA )){
$page .=$this->_sep . implode($this->_sep, $this->_metatagA );
}}
$page .=$html;
return  $page ;
}
public function createHead($headerA,$debug=false){
$this->_debug=$debug;
$this->_createMetaTag($headerA );
if($this->_debug){
$this->_sep="\n";
}
$headhtml='';
if(!empty($this->_metatagA )){
$headhtml=$this->_sep . implode($this->_sep, $this->_metatagA );
}return $headhtml;
}
private function _formatLinks4Mobile($html){
$html=str_replace( array( JOOBI_SITE . JOOBI_INDEX.'?', JOOBI_INDEX.'?'), '',$html );
preg_match_all( "/(?s)<a (.+?)\>/", $html, $matchesA, PREG_PATTERN_ORDER );
$newContent=$html;
foreach($matchesA[1] as $onelink){
$posStart=strpos($onelink, 'href="');
if($posStart===false) continue;
$posEnd=strpos($onelink, '"',$posStart+7 );
$myLink=substr($onelink, $posStart+6, $posEnd-$posStart-6 );
if( substr($myLink, 0, 11 )=='controller='){
$myLink="'" .$myLink. "'";
$newLink=substr($onelink, 0, $posStart). 'on-tap="goTo('.$myLink.')" href="javascript::'.substr($onelink, $posEnd );
$classStart=strpos($newLink, 'class="');
if($classStart===false){
$addedClass='class="gzyw"';
}else{
$addedClass='';
$classEnd=strpos($newLink, '"',$classStart+7 );
$newLink=substr($newLink, 0, $classEnd ). ' gzyw'.substr($newLink, $classEnd );
}
$search='<a '.$onelink.'>';
$replace='<a '.$newLink. '>' ;
$newContent=str_replace($search, $replace, $newContent );
}
}
return $newContent;
}
private function _createMetaTag($headerA){
if(!empty($headerA['js'] )){
if( JOOBI_FRAMEWORK_FORMAT_OUPUT=='browser'){
$newJSA=array('media/jui/js/jquery.min.js');
}else{
$newJSA=array('js/jquery-2.1.1.min.js');
}
foreach($headerA['js'] as $js=> $NotUSed)$newJSA[]=$js;
$dontAddA=array('menu.js','extrascript.js');
foreach($newJSA as $js){
$pos=strrpos($js, '/');
$file=substr($js, $pos+1 );
if( in_array($file, $dontAddA )) continue;
if( strlen(JOOBI_SITE_PATH) > 1  && ( ! JOOBI_FRAMEWORK_FORMAT_OUPUT || JOOBI_FRAMEWORK_FORMAT_OUPUT=='mobile'))$js=str_replace( JOOBI_SITE_PATH, '',$js );
if('/'==$js[0])$js=substr($js, 1 );
$this->_metatagA[]=$this->_sep.'<script src="'.str_replace('&','&amp;',$js ). '"></script>';
}
}
if(!empty($headerA['css'] )){
foreach($headerA['css'] as $css=> $NotUSed)$newCSSA[]=$css;
$dontAddA=array('app.css');
foreach($newCSSA as $css){
$pos=strrpos($css, '/');
$file=substr($css, $pos+1 );
if( in_array($file, $dontAddA )) continue;
if( strlen(JOOBI_SITE_PATH) > 1  && ( ! JOOBI_FRAMEWORK_FORMAT_OUPUT || JOOBI_FRAMEWORK_FORMAT_OUPUT=='mobile'))$css=str_replace( JOOBI_SITE_PATH, '',$css );
if('/'==$css[0])$css=substr($css, 1 );
$this->_metatagA[]=$this->_sep.'<link href="'.str_replace('&','&amp;',$css ). '" rel="stylesheet">';
}}
if(!empty($headerA['css_sc'] )){
$cssString=implode($this->_sep, array_keys($headerA['css_sc']));
if(!$this->_debug){
$cssString=str_replace( array( "\t", '  ') , '',$cssString );
}$this->_metatagA[]=$this->_sep.'<style media="screen" type="text/css">'.$cssString.'</style>';
}
if(!empty($headerA['js_sc'] )){
$jsString=implode($this->_sep, array_keys($headerA['js_sc']));
if(!$this->_debug){
$jsString=str_replace( array( "\t", '  ') , '',$jsString );
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
}
private function _addMetagTag($name,$content){
return '<meta name="'.$name.'" content="'.$content.'" />';
}
function __destruct(){
return true;
}
}