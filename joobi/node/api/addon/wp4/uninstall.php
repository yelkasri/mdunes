<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Wp4_Uninstall_addon extends WClasses {
public function unInstallOneExtension($wid=0,$everything=false){
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
}
private function _oneExtension($wid=0){
$pluginSlup=WExtension::get($wid, 'folder');
delete_option($pluginSlup . "DeActivated_Plugin" );
WApplication_wp4::renderFunction( "install", "uninstall", $pluginSlup );
$plugin=$pluginSlup.'/'.$pluginSlup.'.php';
$network_wide=false;
$currentA=get_site_option('active_sitewide_plugins',array());
if( is_multisite() && ($network_wide || is_network_only_plugin($plugin))){
$network_wide=true;
$currentA=get_site_option('active_sitewide_plugins',array());
}else{
$currentA=get_option('active_plugins',array());
}
if($network_wide){
$currentA[$plugin]=time();
update_site_option('active_sitewide_plugins',$currentA );
}else{
$key=array_search($plugin, $currentA );
if(!empty($key)){
unset($currentA[$key] );
sort($currentA);
update_option('active_plugins',$currentA );
}}
$transient_plugin_slugs=get_option('_transient_plugin_slugs',array());
$key=array_search($plugin, $transient_plugin_slugs );
if(!empty($key)){
unset($transient_plugin_slugs[$key] );
sort($transient_plugin_slugs);
update_option('_transient_plugin_slugs',$transient_plugin_slugs );
}
$site_transient_update_plugins=get_option('_site_transient_update_plugins',array());
if(!empty($site_transient_update_plugins->checked)){
$checked=$site_transient_update_plugins->checked;
if( in_array($plugin, array_keys($checked))){
unset($checked[$plugin] );
asort($checked);
$site_transient_update_plugins->checked=$checked;
update_option('_site_transient_update_plugins',$site_transient_update_plugins );
}}
$folderS=WGet::folder();
$path=WP_PLUGIN_DIR.DS.$pluginSlup;
 $folderS->delete($path );
}
}