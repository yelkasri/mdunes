<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Role_Content_plugin extends WPlugin {
public function onBeforeDisplayContent(&$article,&$params,$limitstart){
if( WRoles::isAdmin()) return '';
if(!WExtension::exist('subscription.node')) return '';
static $artcileView=null;
static $level=null;
if(!isset($artcileView)){
$artcileView=( WGlobals::get('view')=='article')?true : false;
}
$articleID=!empty($article->id)?$article->id : 0;
$sectionID=!empty($article->sectionid)?$article->sectionid : 0;
$catID=!empty($article->catid)?$article->catid : 0;
$rolid=WUser::roles();
 if(!($rolid))$rolid=array(1,2);
if($artcileView){
if($this->_checkContentAccess('content',$article, $level, $artcileView, $rolid, $articleID )) return '';
if($this->_checkContentAccess('categories',$article, $level, $artcileView, $rolid, $catID )) return '';
if($this->_checkContentAccess('sections',$article, $level, $artcileView, $rolid, $sectionID )) return '';
}else{
if($this->_checkContentAccess('sections',$article, $level, $artcileView, $rolid, $sectionID )) return '';
if($this->_checkContentAccess('categories',$article, $level, $artcileView, $rolid, $catID )) return '';
if($this->_checkContentAccess('content',$article, $level, $artcileView, $rolid, $articleID )) return '';
}
return '';
}
public function onContentBeforeDisplay($context,&$article,&$params,$limitstart){
if( WRoles::isAdmin()) return '';
if(!WExtension::exist('subscription.node')) return;
static $artcileView=null;
static $level=null;
if(!isset($artcileView)){
$artcileView=( WGlobals::get('view')=='article')?true : false;
}
$articleID=!empty($article->id)?$article->id : 0;
$catID=!empty($article->catid)?$article->catid : 0;
$rolid=WUser::roles();
 if(!($rolid))$rolid=array(1,2);
if($artcileView){
if($this->_checkContentAccess('content',$article, $level, $artcileView, $rolid, $articleID )) return '';
if($this->_checkContentAccess('categories',$article, $level, $artcileView, $rolid, $catID )) return '';
}else{
if($this->_checkContentAccess('categories',$article, $level, $artcileView, $rolid, $catID )) return '';
if($this->_checkContentAccess('content',$article, $level, $artcileView, $rolid, $articleID )) return '';
}
return '';
}
private function _checkContentAccess($type,&$article,$level,$artcileView,$rolid,$ID){
static $ACL=array();
$index=$type . $ID;
if(!isset($ACL[$index])){
$roleSectionsM=WModel::get('role.'.$type );
$roleSectionsM->whereE('id',$ID );
$ACL[$index]=$roleSectionsM->load('o',array('introrolid','rolid'));
if(empty($ACL[$index]))$ACL[$index]='';
}
if(!empty($ACL[$index])){
if(!in_array($ACL[$index]->rolid, $rolid )){
$this->_restrictMe($article, $level, $artcileView, $ACL[$index]->introrolid, $ACL[$index]->rolid );
return true;
}
}
return false;
}
private function _restrictMe(&$article,$level,$artcileView,$intro,$rolid){
$text='text';
if(!$artcileView)$text='introtext';
$urolid=WUser::roles();
if(!($urolid))$urolid=array(1,2);
$content='';
if($artcileView){
if( in_array($intro, $urolid )){
$article->$text=$article->introtext;
}else{
$article->$text=$content;
}
}else{
if(!in_array($intro, $urolid )){
$article->$text=$content;
}
}
$iconO=WPage::newBluePrint('icon');
$iconO->icon='lock';
$iconO->align='middle';
$iconO->text=WText::t('1206732411EGRI');
$image=WPage::renderBluePrint('icon',$iconO );
WText::load('role.node');
$uid=WUser::get('uid');
if($uid>0){ 
$route=WPref::load('PSUBSCRIPTION_NODE_REDIRECTLINK');
if(!empty($route))$link=WPage::linkHome($route );
else {
$link=WPages::linkHome('controller=subscription&task=possible&rolid='. $rolid, WPages::getPageId('catalog'));
}
$message=WPref::load('PSUBSCRIPTION_NODE_RESTMESSAGE');
if(!empty($message) && 'Subscribe to Read!' !=$message)$span='<span class="restricted"><a href="'.$link.'">'.$message.'</a></span>';
 else $span='<span class="restricted"><a href="'.$link.'">'.WText::t('1236926015ATTV'). '</a></span>';
$position=WPref::load('PSUBSCRIPTION_NODE_RESTMESSAGEPOS');
if(!empty($position))$article->$text=$article->$text . $image . $span;
else  $article->$text=$image . $span . $article->$text;
}else{ 
$route=WPref::load('PSUBSCRIPTION_NODE_VREDIRECTLINK');
if(!empty($route))$link=WPage::link($route );
else {
$link=WPages::linkHome('controller=subscription&task=possible&rolid='. $rolid, WPages::getPageId('catalog'));
}
$message=WPref::load('PSUBSCRIPTION_NODE_VRESTMESSAGE');
if(!empty($message) && 'Register or Log-in to Read!' !=$message)$span='<span class="restricted"><a href="'.$link.'">'.$message.'</a></span>';
 else $span='<span class="restricted"><a href="'.$link.'">'.WText::t('1236926015ATTV'). '</a></span>';
$position=WPref::load('PSUBSCRIPTION_NODE_VRESTMESSAGEPOS');
if(!empty($position))$article->$text=$article->$text . $image . $span;
else  $article->$text=$image . $span . $article->$text;
}
}
function onPrepareContent(&$article,&$params,$limitstart){
return '';
}
function onAfterContentSave(&$article,$isNew){
return true;
}
}