<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_Skin_picklist extends WPicklist {
function create(){
$themeM=WModel::get('theme');
$themeM->makeLJ('themetrans','tmid');
$themeM->whereLanguage();
$themeM->whereE('type', 101 );
$themeM->whereE('publish', 1 );
$themeM->checkAccess();
$themeM->select('name', 1 );
$themeM->select('namekey');
$themeM->setLimit(500);
$themeM->orderBy('name','ASC', 1 );
$results=$themeM->load('ol');
if(empty($results)) return false;
$this->addElement('',' -- '.WText::t('1206732425HINT'). ' -- ');
foreach($results as $myResult){
$this->addElement($myResult->namekey , $myResult->name );
}
return true;
}
}