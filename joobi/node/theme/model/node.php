<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_Node_model extends WModel {
function addValidate(){
if(empty($this->namekey))$this->genNamekey();
return true;
}
function extra(){
$cache=WCache::get();
$cache->resetCache();
return true;
}
function copyValidate(){
$trk=WGlobals::get( JOOBI_VAR_DATA );
$folder=$trk['x']['foldername'];
$folder=strtolower($folder);
$folder=str_replace(' ','_',$folder);
$themeC=WClass::get('theme.helper');
$themeC->unPremium($this->tmid );
$this->core=0;
$this->premium=0;
$this->created=time();
$this->alias='Clone of: '.$this->alias.'  : '.$folder;
$this->setChild('themetrans','name',$this->alias );
$this->folder=$folder;
$this->premium=1;
if($this->type==101){
$explodeA=explode('.',$this->namekey );
$this->namekey=$folder.'.'.$explodeA[1];
$this->_originalSkin=$explodeA[0];
}
return true;
}
function copyExtra(){
$tmid=WGlobals::getEID();
$themeC=WClass::get('theme.helper');
if($this->type==101){
$explodeNamekeyA=explode('.',$this->namekey );
$systemFolderC=WGet::file();
$sourceLocation=JOOBI_DS_THEME.'skin'.DS.$this->_originalSkin.DS.'css'.DS.$explodeNamekeyA[1].'.css';
$path='skin'.DS.$explodeNamekeyA[0].DS.'css'.DS.$explodeNamekeyA[1].'.css';
if($systemFolderC->exist($sourceLocation))$systemFolderC->copy($sourceLocation, JOOBI_DS_THEME . $path, true);
$sourceLocation=JOOBI_DS_THEME.'skin'.DS.$this->_originalSkin.DS.'css'.DS.$explodeNamekeyA[1].'.min.css';
$path='skin'.DS.$explodeNamekeyA[0].DS.'css'.DS.$explodeNamekeyA[1].'.min.css';
if($systemFolderC->exist($sourceLocation))$systemFolderC->copy($sourceLocation, JOOBI_DS_THEME . $path, true);
}else{
$destfolder=$themeC->destfolder($this->type );
$srcfolder=$themeC->getCol($tmid, 'folder') ;
$path=$destfolder.DS.$this->folder;
$fileC=WClass::get('apps.files');
$fileC->createFolder('theme',$path, 'user');
$sourceLocation=JOOBI_DS_THEME . $destfolder.DS.$srcfolder;
$systemFolderC=WGet::folder();
$systemFolderC->copy($sourceLocation, JOOBI_DS_THEME . $path, true);
}
$cache=WCache::get();
$cache->resetCache('Theme');
return true;
}
function deleteValidate($eid=0){
$this->_x=$this->load($eid );
return true;
}
function deleteExtra($eid=0){
$systemFolderC=WGet::folder();
$themeC=WClass::get('theme.helper');
if(!empty($this->_x->type) && $this->_x->type !=201){
$destfolder=$themeC->destfolder($this->_x->type );
$path=$destfolder.DS.$this->_x->folder;
if(!empty($path)){
$systemFolderC->delete( JOOBI_DS_JOOBI.'user'.DS.'theme'.DS.$path );
}
if(!empty($this->_x->type) && !empty($this->_x->premium)){
$themeM=WModel::get('theme');
$themeM->whereE('type',$this->_x->type );
$themeM->whereE('wid',$this->_x->wid );
$themeM->whereE('core', 1 );
$themeM->setVal('premium', 1 );
$themeM->update();
}
}
$cache=WCache::get();
$cache->resetCache('Theme');
return true;
}}