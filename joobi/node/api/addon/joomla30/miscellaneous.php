<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Joomla30_Miscellaneous_addon {
public function getAllWidgetsIDforFeatured(){
$allItemsA=array('mod_item_item_module','mod_product_product_module','mod_download_download_module','mod_auction_auction_module');
$joomlaModulesM=WModel::get('joomla.modules');
$joomlaModulesM->whereIn('module',$allItemsA );
$joomlaModulesM->select('id', 0, 'id');
$joomlaModulesM->select('title', 0, 'name');
$joomlaModulesM->select('module', 0, 'slug');
$joomlaModulesM->whereE('published', 1 );
$resultA=$joomlaModulesM->load('ol');
return $resultA;
}
public function getAllModule4Type($namekey){
$joomlaModulesM=WModel::get('joomla.modules');
$joomlaModulesM->whereE('module',$namekey );
$joomlaModulesM->select('id');
$joomlaModulesM->select('title');
$joomlaModulesM->select('module');
$joomlaModulesM->whereE('published', 1 );
$resultA=$joomlaModulesM->load('ol');
return $resultA;
}
public function renderModule($module){
$params=new stdClass();
$params->module=new stdClass();
$params->module->id=$module->id;
$params->module->title=$module->title;
$params->module->module=$module->module;
return WGet::startApplication('module','cart.cart.module',$params );
}
}