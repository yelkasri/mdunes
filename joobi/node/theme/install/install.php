<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_Node_install extends WInstall {
public function install(&$object){
if(!empty($this->newInstall ) || ( property_exists($object, 'newInstall') && $object->newInstall)){
$themeM=WModel::get('theme');
$themeM->setVal('premium', 1 );
$themeM->whereE('core', 1 );
$themeM->update();
$imageM=WModel::get('files');
$imageM->whereE('name','default_theme');
$imageID=$imageM->load('lr','filid');
if(!empty($imageID)) return true;
$filehandler=WGet::file();
$src=JOOBI_DS_NODE .'theme'.DS.'install'.DS.'image'.DS.'default_theme.png';
if(!$filehandler->exist($src )) return true;$dest='images'.DS.'theme' ;
$content=$filehandler->read($src );
$filehandler->copy($src, JOOBI_DS_MEDIA . $dest.DS.'thumbnails'.DS.'default_theme.png');
$imagesM=WModel::get('files');
if($imagesM->instanceExist()){
$imagesM->saveOneFile($content, 'default_theme.png',$dest, JOOBI_DS_MEDIA, true); 
$imagesM->setVal('core', 1 );
$imagesM->setVal('storage','0');
$imagesM->setVal('width', 80 );
$imagesM->setVal('twidth', 80 );
$imagesM->setVal('height', 120 );
$imagesM->setVal('theight', 120 );
$imagesM->setVal('thumbnail', 1 );
$imagesM->whereE('name','default_theme');
$imagesM->whereE('type','png');
$imagesM->update();
}
$themeInstallC=WClass::get('theme.install');
$themeInstallC->installDefaultTheme( 106, 'none','Default Mail','The default newsletter theme.');
}return true;
}
public function addExtensions(){
$extension=new stdClass;
$extension->namekey='theme.system.plugin';
$extension->name='Joobi - Bootstrap Skins for Theme';
$extension->folder='system';
$extension->type=50;
$extension->publish=1;
$extension->certify=1;
$extension->destination='node|theme|plugin';
$extension->core=1;
$extension->params='publish=1';
$extension->description='Overwrite the boostrap.css file with a skin file.';
if($this->insertNewExtension($extension ))$this->installExtension($extension->namekey );
}
}