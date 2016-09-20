<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_Render_class extends WClasses {
static private $_preferencesO=null;
protected $_hint=false;
function __construct(){
if(!class_exists('WThemeHTML')) WLoadFile('library.class.theme');
$params=WThemeHTML::$themeParams;
if(!empty($params )){
self::$_preferencesO=new stdClass;
self::$_preferencesO->params=$params;
WTools::getParams( self::$_preferencesO );
}
if(empty( self::$_preferencesO )){
self::$_preferencesO=WObject::get('theme.preferences');
}
}
public function newObject($data=null){
$className=get_class($this ). 'Data';if( class_exists($className)) return new $className;
else {
$className=get_class($this ). 'Object';
if( class_exists($className)) return new $className;
else return new stdClass;
}}
protected function value($property,$default=null){
if( is_string($property)){
$property=str_replace('.','_',$property );
if(isset( self::$_preferencesO->$property )) return self::$_preferencesO->$property;
else return $default;
}elseif(is_array($property)){
$returnA=array();
foreach($property as $oneP){
$onePDOT=str_replace('.','_',$oneP );
if(isset( self::$_preferencesO->$onePDOT ))$returnA[$oneP]=self::$_preferencesO->$onePDOT;
else $returnA[$oneP]=null;
}return $returnA;
}
return $default;
}
protected function overwriteThemePreferences($themeCustomO){
if(empty($themeCustomO)) return false;
foreach($themeCustomO as $key=> $value){
self::$_preferencesO->$key=$value;
}
}
}