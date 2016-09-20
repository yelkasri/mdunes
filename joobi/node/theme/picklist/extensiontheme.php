<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_Extensiontheme_picklist extends WPicklist {
function create(){
$themeM=WModel::get('theme');
$themeM->makeLJ('apps','wid');
$themeM->whereE('publish',1,1);
$themeM->setDistinct();
$themeM->select('name',1);
$themeM->select('type',1);
$themeM->select('wid');
$themeM->setLimit(500);
$themeM->orderBy('type','ASC',1);
$themeM->orderBy('name','ASC',1);
$results=$themeM->load('ol');
if(empty($results)) return false;
foreach($results as $myResult){
$this->addElement($myResult->wid , $myResult->name);
}
return true;
}}