<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_Type_picklist extends WPicklist {
function create(){
$wid=WGlobals::get('wid');
$themeM=WModel::get('theme');
$themeM->select('type');
if($wid !=0)$themeM->whereE('wid',$wid );
$themeM->setDistinct();
$themeM->orderBy('type');
$themeM->setLimit('50');
$resultsA=$themeM->load('lra');
if(empty($resultsA) || sizeof($resultsA)<=1 ) return false;
if(!empty($resultsA)){
foreach($resultsA as $result){
switch ($result){
case 49:
$name=WText::t('1206732400OXBR');
break;
case 101:
$name=WText::t('1399505622LUTN');break;
case 106:
$name=WText::t('1206732400OWZW');
break;
case 102:$name=WText::t('1373210597CPQC');break;
case 107:
$name=WText::t('1206961936HCWP');
break;
case 105:
$name=WText::t('1251697929ABZD');
break;
case 1:
$name=WText::t('1416822016EIWM');
break;
case 2:
$name=WText::t('1416822016EIWN');
break;
case 3:
$name=WText::t('1396670883SAXN');
break;
case 50:
$name=WText::t('1382364838SGIL');
break;
case 201:
$name=WText::t('1399595651JURP');
break;
default:
$name=''; 
}
$this->addElement($result , $name );
}
}
return true;
}
}