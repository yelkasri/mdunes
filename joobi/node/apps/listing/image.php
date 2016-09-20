<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CoreImage_listing extends WListings_default{
function create(){
$wid=(int)$this->value;
$publish=$this->getValue('publish','apps');
$folder=WExtension::get($wid, 'folder', null, null, false);
$file=WGet::file();
if($publish || $file->exist( JOOBI_DS_NODE . $folder.DS.'images'.DS.$folder.'.png')){
$image=WPage::addImage( JOOBI_FOLDER.'/node/'.$folder.'/images/'.$folder.'-48.png','home');
$this->content='<img src="'.$image.'">';
}else{
$appsM=WModel::get('apps');
$appsM->whereE('wid',$wid );
$folder=$appsM->load('lr','folder');
$this->content='<img src="'.WPref::load('PLIBRARY_NODE_CDNSERVER'). '/joobi/user/media/apps/'.$folder.'-48.png'. '">';
}
$this->element->lien=JOOBI_URLAPP_PAGE.'='. WApplication::getAppLink($this->getValue('folder')).'&controller='.$this->getValue('folder');
return true;
}
}