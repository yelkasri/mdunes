<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Apps_Files_class extends WClasses {
var $chmodSet='0777';var $safeSet=false;var $use_same_rightsSet=true;
function createFolder($ffolder,$fpath='',$mainRep=null){
if($mainRep==null)$mainRep='node';
$folder=WGet::folder();
$path=JOOBI_DS_ROOT.'joobi'.DS.$mainRep.DS.$ffolder.DS.$fpath;
if(!$folder->exist($path)){
$folder->create($path, '',$this->safeSet, $this->chmodSet);
$file=WGet::file();
}
return true;
 }
 function deleteFile($ffolder,$filename,$fpath='',$mainRep=null){
 if($mainRep==null)$mainRep='node';
 $file=WGet::file();
$path=JOOBI_DS_ROOT.'joobi'.DS.$mainRep.DS.$ffolder.DS.$fpath;
$file->delete($path.DS.$filename);
 }
 function createFile($ffolder,$filename,$fileext,$type,$subfolder=true,$version='',$name='',$desc='',$modulesite='site',$params='',$mainRep=null){
 if($mainRep==null)$mainRep='node';
if($fileext=='php'){
 if($subfolder){
$path=JOOBI_DS_ROOT.'joobi'.DS.$mainRep.DS.$ffolder.DS.$type.DS.$filename.DS.$filename .'.'. $fileext;
 }else{
 $path=JOOBI_DS_ROOT.'joobi'.DS.$mainRep.DS.$ffolder.DS.$type.DS.$filename .'.'. $fileext;
 }$content=$this->_createIndex($name,$desc,$type,$ffolder,$filename);
 }elseif($fileext=='xml'){
 return true;
 }
 $file=WGet::file();
 if(!$file->exist($path)){
$file->write($path, $content, 'write',$this->chmodSet, $this->safeSet, $this->use_same_rightsSet);
}
return true;
 }
function createInstall($wid,$type,$name,$desc,$params){
$addTab="";
if(isset($params->wrapper)){
$addTab="\t\t";
}
$element=$addTab."\t".'<element>'.WGet::$rLine;
$element.=$addTab."\t\t".'<type>'.WGet::$rLine;
switch($type){
case 'forms':
switch($params->type){
case 'layout':
$element.=$addTab."\t\t\t".'<view>'.$params->typeNK.'</view>'.WGet::$rLine;
break;
case 'select':
$element.=$addTab."\t\t\t".'<select>'.$params->typeNK.'</select>'.WGet::$rLine;
break;
case 'multiselect':
$element.=$addTab."\t\t\t".'<multiselect>'.$params->typeNK.'</multiselect>'.WGet::$rLine;
break;
case 'customized':
$element.=$addTab."\t\t\t".'<customized>'.$params->typeNK.'</customized>'.WGet::$rLine;
break;
default:
$element.=$params->type;
}break;
case 'menus':
$element.=$params->type;
break;
case 'listings':
switch($params->type){
default:
$element.=$params->type;
}break;
}$element.=$addTab."\t\t".'</type>'.WGet::$rLine;
if(isset($params->map))$element.=$addTab."\t\t".'<map>'.$params->map.'</map>'.WGet::$rLine;
if(isset($params->area))$element.=$addTab."\t\t".'<area>'.$params->area.'</area>'.WGet::$rLine;
if(isset($params->sid))$element.=$addTab."\t\t".'<sid>'.$params->sid.'</sid>'.WGet::$rLine;
if(isset($params->action))$element.=$addTab."\t\t".'<action>'.$params->action.'</action>'.WGet::$rLine;
if(isset($params->icon))$element.=$addTab."\t\t".'<icon>'.$params->icon.'</icon>'.WGet::$rLine;
if(isset($params->spantit))$element.=$addTab."\t\t".'<spantit>true</spantit>'.WGet::$rLine;
if(isset($params->ordering))$element.=$addTab."\t\t".'<ordering>'.$params->ordering.'</ordering>'.WGet::$rLine;
$element.=$addTab."\t\t".'<name>'.$name.'</name>'.WGet::$rLine;
$element.=$addTab."\t\t".'<description>'.$desc.'</description>'.WGet::$rLine;
$element.=$addTab."\t".'</element>'.WGet::$rLine;
$language='en';
$content='<'.$type.'>'.WGet::$rLine;
$content.="\t".'<language>'.$language.'</language>'.WGet::$rLine;
$wrapperStart=$wrapperEnd='';
if(isset($params->wrapper)){
$wrapperStart="\t".'<element>'.WGet::$rLine;
$wrapperStart.=($params->wrapper->area)?"\t\t".'<area>'.$params->wrapper->area.'</area>'."\r\n":'';
$wrapperStart.=($params->wrapper->type)?"\t\t".'<type>'.$params->wrapper->type.'</type>'."\r\n":'';
$wrapperStart.=($params->wrapper->name)?"\t\t".'<name>'.$params->wrapper->name.'</name>'."\r\n":'';
$wrapperEnd="\t".'</element>'.WGet::$rLine;
}
$content.=$wrapperStart;
$content.=$element;
$content.=$wrapperEnd;
$content .="\t".'<insert>'.WGet::$rLine;
foreach($params->insert as $result){
$content .="\t\t".'<into u="true">'.$result.'</into>'.WGet::$rLine;
} $content .="\t".'</insert>'.WGet::$rLine;
$content .='</'.$type.'>'.WGet::$rLine;
$model=WModel::get('apps');
$model->whereE('wid',$wid);
$model->setVal('install' , $content );
$model->update();
}
private function _createIndex($name,$desc,$type,$ffolder,$filename){
$member=WUser::get();
$content='<?php
defined(\'JOOBI_SECURE\') or die(\'...J...\');
/**
* <p>Joobi application</p>
* @copyright Copyright (c) 2007-'.date('Y').' Joobi All rights reserved.
* @link joobi.info/license
* @author '.$member->username.' <'.$member->email.'>
*/
/**
*  '.$name.'
*  '.$desc.'
*/
';
switch($type)
{
case 'module' : $content .='class '.ucfirst($ffolder).'_'.ucfirst($filename).'_module extends WModule {';
break;
case 'plugin' : $content .='class '.ucfirst($ffolder).'_'.ucfirst($filename).'_plugin extends WPlugin {';
break;
case 'addon' : $content .='class '.ucfirst($ffolder).'_'.ucfirst($filename).'_addon extends WClasses {';
break;
case 'class' : $content .='class '.ucfirst($ffolder).'_'.ucfirst($filename).'_class extends WClasses {';
break;
case 'action' : $content .='class '.ucfirst($ffolder).'_'.ucfirst($filename).'_'.ucfirst($filename).'_action {';
break;
default : $content .='class To_Do {';
break;
}
$content .='
}//endclass';
 return $content;
}
}
