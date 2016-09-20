<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Output_Breadcrumb_class {
  public function displayBreadCrumb($model,$eid='1',$controller,$task=''){
$typeCacheItem=false;
$lgid=WUser::get('lgid');
$cache=WCache::get();
if($model->_infos->type !=40){
$lastCategoryDispaly=0;
if(!empty($model->_infos->categorymodel)){
$modelName=$model->_infos->categorymodel;
$model=WModel::get($modelName );
if(empty($model)) return '';
$catid=WGlobals::get('catid');
if(empty($catid)){
$catid=$model->getItemDefaultCategory($eid );
}
if(empty($catid))$catid=1;
$cached=$cache->get('BreadCrumbs'.$modelName .'_item_'.$catid.'_'.$lgid, 'Views');
if(!empty($cached)) return $cached;
$typeCacheItem=true;
}else{
return '';
}
$linkCategory=WGlobals::get('breadcrumbsCAtegoryLink','','global');
}else{
$lastCategoryDispaly=1;
$cached=$cache->get('BreadCrumbs'.'catid'.$eid.'_'.$lgid, 'Views');
if(!empty($cached)) return $cached;
$catid=$eid;
$linkCategory='';
}
$thpath='';
if(empty($catid))$catid=1;
$branchesA=$model->getAllParents($catid );
if(!empty($branchesA)){
$mypkey=$model->getPK();
$pageT=WPage::theme('main');
if(!defined('JOOBI_URL_THEME_JOOBI')){
WView::definePath();
}
$newAllBranchesA=array();
foreach($branchesA as $key=> $parent){
$parentShow=$parent->name;
$url='controller='.$controller;
if($parentShow=='root' || empty($parentShow ) || $parent->namekey=='root' || $parent->catid==1){
WText::load('output.node');
$parentShow=WText::t('1206732431CQBG');
$linkCat=WPage::routeURL($url );
}else{
if(!empty($linkCategory))$url=$linkCategory;
elseif(!empty($task))$url .='&task='.$task;
$linkCat=WPage::routeURL($url . "&eid=" . $parent->$mypkey );
}
$oneBrancheO=new stdClass;
$oneBrancheO->name=$parentShow;
$oneBrancheO->link=$linkCat;
if(isset($branchesA[$key+$lastCategoryDispaly] )){
$oneBrancheO->addSeparator=true;
}
$newAllBranchesA[]=$oneBrancheO;
}
$bc=new stdClass;
$bc->type='catalog';
$bc->listA=$newAllBranchesA;
$thpath=WPage::renderBluePrint('breadcrumb',$bc );
}
if($typeCacheItem){
$cached=$cache->get('BreadCrumbs'.$modelName .'_item_'.$catid.'_'.$lgid, $thpath, 'Views');
}else{
$cache->set('BreadCrumbs'.'catid'.$catid.'_'.$lgid, $thpath, 'Views');
}
return $thpath;
  }
}
