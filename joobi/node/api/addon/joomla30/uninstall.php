<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Joomla30_Uninstall_addon extends WClasses {
public function unInstallOneExtension($wid=0){
if(empty($wid)){
$appsM=WModel::get('apps');
 $appsM->whereE('type', 1 );
 $appsM->whereE('publish', 1 );
 $widA=$appsM->load('lra','wid');
}else{
$widA=array($wid );
}
if(!empty($widA)){
foreach($widA as $oneWid){
$this->_oneExtension($oneWid );
}}
if(empty($wid)){
$joomlaExtensionsM=WModel::get('joomla.extensions');
$joomlaExtensionsM->whereSearch('manifest_cache','joobi');
$extA=$joomlaExtensionsM->load('ol',array('extension_id','type','folder','element'));
if(!empty($extA)){
$extensionIDA=array();
$moduleA=array();
foreach($extA as $oneExt){
$mod=$this->_oneSubExtension($oneExt );
if(!empty($mod))$moduleA[]=$mod;
$extensionIDA[]=$oneExt->extension_id;
}$joomlaExtensionsM->whereIn('extension_id',$extensionIDA );
$joomlaExtensionsM->delete();
if(!empty($moduleA)){
$joomlaModulesM=WModel::get('joomla.modules');
$joomlaModulesM->whereIn('module',$moduleA );
$joomlaModulesM->delete();
}
}
}
}
private function _oneSubExtension($oneExt=null){
$folderS=WGet::folder();
$status='';
switch($oneExt->type){
case 'module':
$path=JOOBI_DS_ROOT.'modules'.DS.$oneExt->element;
$status=$oneExt->element;
break;
case 'plugin':
$path=JOOBI_DS_ROOT.'plugins'.DS.$oneExt->folder.DS.$oneExt->element;
break;
default:
return false;
break;
} $folderS->delete($path );
 return $status;
}
private function _oneExtension($wid=0){
$folder=WExtension::get($wid, 'folder');
$component='com_'.$folder;
$joomlaExtensionsM=WModel::get('joomla.extensions');
$joomlaExtensionsM->whereE('type','component');
$joomlaExtensionsM->whereE('element',$component );
$id=$joomlaExtensionsM->load('lr','extension_id');
$joomlaExtensionsM=WModel::get('joomla.extensions');
$joomlaExtensionsM->whereE('extension_id',$id );
$joomlaExtensionsM->delete();
$joomlaMenuM=WModel::get('joomla.menu');
$joomlaMenuM->whereE('component_id',$id );
$joomlaMenuM->delete();
$joomlaMenuM=WModel::get('joomla.menu');
$joomlaMenuM->where('link',' LIKE ','%option='.$component .'%');
$joomlaMenuM->delete();
$assetsT=WTable::get('assets');
$assetsT->whereE('name',$component );
$assetsT->delete();
$folderS=WGet::folder();
 $folderS->delete( JOOBI_DS_ADMIN . $component );
 $folderS->delete( JOOBI_DS_ROOT.'components'.DS.$component );
}
}