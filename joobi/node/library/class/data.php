<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_data_class extends WClasses {
public function processSubmittedData(){
$trk=WGlobals::get( JOOBI_VAR_DATA, array(), '','array');
$dprocessA=WGlobals::get( JOOBI_VAR_DATA.'zt');
if(!empty($dprocessA)){
foreach($dprocessA as $type=> $valuesA){
if('edt'==$type){
$editorClass=WClass::get('editor.get');
foreach($valuesA as $editorName=> $fieldInfos){
foreach($fieldInfos as $fieldName=> $realField){
$fieldArgs=explode('[', str_replace(']','',$realField ));
if(count($fieldArgs)==4){
$trk[$fieldArgs[1]][$fieldArgs[2]][$fieldArgs[3]]=$editorClass->getEditorContent($editorName, $fieldName );
}else{
$trk[$fieldArgs[1]][$fieldArgs[2]]=$editorClass->getEditorContent($editorName, $fieldName );
}}}
continue;
}
if(empty($valuesA) || !is_array($valuesA)) continue;
$this->_loadFormElement();
$instance=WClass::get($type, null, 'form');
if(!empty($instance) && method_exists($instance, 'preSaveValidate')){
foreach($valuesA as $k=> $map){
$first=substr($map, 0, 1 );
if('c'==$first){
$prefA=explode('][', substr($map, 2, -1 ));
if(isset($trk['c'][$prefA[0]][$prefA[1]] )){
$trk['c'][$prefA[0]][$prefA[1]]=$instance->preSaveValidate($trk['c'][$prefA[0]][$prefA[1]] );
}}elseif( is_numeric($first)){
$prefA=explode('|',$map );
$trk[$prefA[0]][$prefA[1]]=$instance->preSaveValidate($trk[$prefA[0]][$prefA[1]] );
}
}
}
}
}
return $trk;
}
private function _loadFormElement(){
static $onlyOnce=false;
if($onlyOnce ) return true;
if(!defined('JOOBI_DS_THEME_JOOBI')) WView::definePath();
WLoadFile('theme.class.render');
WLoadFile('output.class.forms');
WLoadFile('blueprint.form', JOOBI_DS_THEME_JOOBI, true, false);
$onlyOnce=true;
}
}