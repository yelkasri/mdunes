<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_Helper_class extends WClasses {
public function destfolder($type=49){
if(empty($type))$destfolder='node';
else {
$themeTypefolderT=WType::get('theme.typefolder');
$destfolder=$themeTypefolderT->getName($type );
if(empty($destfolder))$destfolder='node';
}
return $destfolder;
}
public function getCol($tmid,$col){
if(empty($tmid) || empty($col)) return null;
static $themes=array();
if(empty($themes[$tmid])){
$themeM=WModel::get('theme');
$themeM->whereE('tmid',$tmid);
$themes[$tmid]=$themeM->load('o');
}
return $themes[$tmid]->$col;
}
public function getFiles($tmid,$filetype='',$recuriveFolder=true){
if(!is_string($filetype)) return false;
switch($filetype){
case 'html':
$folderThatWEDontShow=array('blueprint','js','css','fonts','images','database','xml');
$acceptedTypesA=array('html');
break;
case 'js':
$folderThatWEDontShow=array('blueprint','css','fonts','images','database','xml');
$acceptedTypesA=array('js');
break;
case 'css':
$folderThatWEDontShow=array('blueprint','js','fonts','images','database','xml');
$acceptedTypesA=array('css');
break;
case 'view':
default:
$acceptedTypesA=array('php');
$folderThatWEDontShow=array('blueprint','css','js','fonts','images','database','xml');
break;
}
$type=$this->getCol($tmid,'type');
$folder=$this->getCol($tmid,'folder');
$isCore=$this->getCol($tmid,'core');
$destfolder=$this->destfolder($type );
$srcfolder=JOOBI_DS_THEME . $destfolder.DS.$folder;
$fileClass=WGet::file();$systemFolderC=WGet::folder();
if(!$systemFolderC->exist( JOOBI_DS_THEME . $destfolder.DS.$folder )) return '';
$controllercopy='controller=theme&task=copythemefile&eid='.$tmid.'&type='.$type . (!empty($filetype )?'&filetype='.$filetype : '');
$iconO=WPage::newBluePrint('icon');
$iconO->icon='copy';
$iconO->text=WText::t('1206732372QTKK');
$imgcopy=WPage::renderBluePrint('icon',$iconO ). ' ';
if(!$systemFolderC->exist($srcfolder)) return '';
$files=$systemFolderC->files($srcfolder, '',$recuriveFolder, false, $folderThatWEDontShow );
$objData=array();
if(!empty($files)){
sort($files);
foreach($files as $one_file){
if($one_file=='index.html') continue;
$fileNameA=explode('.',$one_file );
$fileExtension=array_pop($fileNameA );
if(!in_array($fileExtension, $acceptedTypesA )) continue;
$objElement=new stdClass;
$objElement->filename=$one_file;
$linkToShowFile=WPage::routeURL('controller=theme&task=showfile&eid='.$tmid.'&filetype='.$filetype.'&file='.base64_encode(serialize($one_file)).'&titleheader='.$one_file);
$HTMLShow='<a href="'. $linkToShowFile .'">';
$HTMLShow .=$one_file;
$HTMLShow .="</a>";
if($isCore==0){
$controllercopy .="&filename=".base64_encode(serialize($one_file)).'&titleheader='.$one_file;
$linkcopy=WPage::linkPopUp($controllercopy );
$linkEdit=WPage::routeURL('controller=theme&task=editfile&eid='.$tmid.'&filetype='.$filetype.'&file='.base64_encode(serialize($one_file)).'&titleheader='.$one_file);
$iconO=WPage::newBluePrint('icon');
$iconO->icon='edit';
$iconO->text=WText::t('1206732361LXFE');
$imgEdit=WPage::renderBluePrint('icon',$iconO ). ' ';
$objElement->filename=WPage::createPopUpLink($linkcopy, $imgcopy, 600, 250 );
$objElement->filename .='&nbsp;&nbsp;&nbsp;<a href="'. $linkEdit .'">';
$objElement->filename .=$imgEdit;
$objElement->filename .="</a>";
$objElement->filename .='&nbsp;&nbsp;&nbsp;';
$objElement->filename .=$HTMLShow;
}else{
$objElement->filename=$HTMLShow;
}
$objData[]=$objElement;
}
}
return $objData;
}
public function overwriteThemeFile($onlyApply=false){
$titleheader=WGlobals::get('titleheader');
$fileEncoded=WController::getFormValue('file');
$file=unserialize( base64_decode($fileEncoded ));
$eid=WController::getFormValue('eid');
$filetype=WController::getFormValue('filetype');
$content=WController::getFormValue('content','x', null, 'html');
WGlobals::set('eid',$eid );
WGlobals::set('file',$file );
WGlobals::set('filetype',$filetype );
$fileClass=WGet::file();
$type=$this->getCol($eid,'type');
$folder=$this->getCol($eid,'folder');
$destfolder=$this->destfolder($type);
if($filetype=='main')  $path=JOOBI_DS_THEME.$destfolder.DS.$folder.DS.$file;
else $path=JOOBI_DS_THEME.$destfolder.DS.$folder. DS.DS.$file;
$fileClass->write($path, $content, 'overwrite');
$message=WMessage::get();
$message->userS('1298294183ETWL',array('$file'=>$file));
if($onlyApply){
WPages::redirect('controller=theme&task=editfile&eid='.$eid.'&filetype='.$filetype.'&file='.$fileEncoded.'&titleheader='. $titleheader);
}else{
WPages::redirect('controller=theme&task=show&eid='.$eid );
}
return true;
}
public function setPremium($tmid){
if(empty($tmid)) return false;
$themeM=WModel::get('theme');
$themeM->setVal('premium', 1 );
$themeM->whereE('tmid',$tmid );
$themeM->update();
return true;
}
public function unPremium($tmid){
if(empty($tmid)) return false;
$type=$this->getCol($tmid, 'type');
$themeM=WModel::get('theme');
$themeM->setVal('premium', 0 );
$themeM->whereE('type',$type );
$themeM->update();
return true;
}
public function getFileContent($tmid='',$filetype='main',$file='index.html'){
$type=$this->getCol($tmid, 'type');
$folder=$this->getCol($tmid,'folder');
$destfolder=$this->destfolder($type);
if($filetype=='main'){
$path=JOOBI_DS_THEME.$destfolder.DS.$folder.DS.'index.html';
}else{
$path=JOOBI_DS_THEME.$destfolder.DS.$folder.DS.$file;}
$fileClass=WGet::file();
if($fileClass->exist($path )){
$size=$fileClass->size($path );
if($size >=0 && $size <=1048576){
$content=$fileClass->read($path );}else{
$FILENAME=str_replace( JOOBI_DS_JOOBI, '',$file );
$this->userE('1316671955LOQA',array('$FILENAME'=>$FILENAME));
return false;
}}else{
$FILENAME=str_replace( JOOBI_DS_JOOBI, '',$file );
$this->userE('1420549276ICAG',array('$FILENAME'=>$FILENAME));
return false;
}
return $content;
}
}