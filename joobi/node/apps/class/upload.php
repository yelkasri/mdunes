<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_upload_class extends WClasses {
function uploadINstallPackage(){
$trk=WGlobals::get( JOOBI_VAR_DATA, array(), '','array'); $url=$trk['x']['url'];
$status=false;
if(empty($url) || $url=='http://'){
$folder=$trk['x']['folder'];
if(empty($folder) || $folder==JOOBI_DS_ROOT){
$status=$this->_handleFile();
}else{
$status=$this->_handleFolder($folder);
}
}else{
$status=$this->_handleUrl($url);
}
return $status;
}
private function _handleFolder($folder){
$folder=rtrim($folder,'/\\');
$installPackageC=WClass::get('install.package');
$installPackageC->tables=$installPackageC->xml($folder);
if(!$installPackageC->tables){
$mess=WMessage::get();
$FOLDER=$folder;
$mess->userE('1248948187IFCW',array('$FOLDER'=>$FOLDER));
return false;
}
if(!$installPackageC->checkXmlData())  return false;
$ext=&$installPackageC->tables['extension_node'][0];
$destination=JOOBI_DS_JOOBI.(empty($ext->destination)?'':str_replace('|',DS,$ext->destination).DS).$ext->folder;
$type='add_over';
if(in_array($ext->type, array(100,101,102))){
$type='user_backup';
}
$systemFolderC=WGet::folder();
$systemFolderC->copy($folder, $destination, $type );
$systemFolderC->delete($folder);
$installProcessC=WClass::get('install.process');
$installProcessC->list=array( array( array('filename'=> $destination, 'folder'=> $destination, 'extract'=> 1 ), 'level'=>0 ));
$installProcessC->mode='createtable';
$installProcessC->finishDownloadPackages();
return true;
}
private function _handleUrl($url){
$helper=WClass::get('netcom.rest', null, 'class', false);
$helper->_method='GET';
$data=$helper->send($url );
if(!$data)  return false;
$temp_name=md5($name.time());
$fileHandler=WGet::file();
if(!$fileHandler->write(JOOBI_DS_TEMP.$temp_name,$data)) return false;
$installProcessC=WClass::get('install.process');
$installProcessC->list=array(array(array('filename'=> JOOBI_DS_TEMP.$temp_name),'level'=>0));
$installProcessC->finishDownloadPackages();
return true;
}
private function _handleFile(){
$fileTrucs=WGlobals::get( JOOBI_VAR_DATA, array(), 'FILES','array');
if(empty($fileTrucs['tmp_name']['x']['file']) || empty($fileTrucs['name']['x']['file'])){
$message=WMessage::get();
$message->userW('1248835367AECZ');
return false;
}
$filename=$fileTrucs['tmp_name']['x']['file'];
$name=$fileTrucs['name']['x']['file'];
$EXTENSIONS=array('gz','bz','bz2','tgz');
switch (JOOBI_FRAMEWORK){
case 'joomla30':
$EXTENSIONS[]='tar';
$EXTENSIONS[]='zip';
$EXTENSIONS[]='tbz2';
$EXTENSIONS[]='bzip2';
break;
default:
break;
}
$FILE_EXTENSION=substr($name, strrpos($name,'.')+1);
if(!in_array($FILE_EXTENSION, $EXTENSIONS )){
$message=WMessage::get();
$EXTENSIONS=implode(',',$EXTENSIONS);
$message->userW('1249567958KVAQ',array('$FILE_EXTENSION'=>$FILE_EXTENSION,'$EXTENSIONS'=>$EXTENSIONS));
return false;
}
if(!is_uploaded_file($filename)){
$mess=WMessage::get();
$mess->adminE('Bad request');
return false;
}
$temp_name=md5( time()). preg_replace('#[^a-z0-9\._-]#','',$name );
$filestat=move_uploaded_file($filename, JOOBI_DS_TEMP.$temp_name);
if($filestat===false){
$mess=WMessage::get();
$DESTINATION=JOOBI_DS_TEMP.$temp_name;
$SOURCE=$filename;
$mess->adminE('Could not move the uploaded file '.$SOURCE. ' to the temporary file '.$DESTINATION);
return false;
}
$installProcessC=WClass::get('install.process');
$installProcessC->list=array(array(array('filename'=> JOOBI_DS_TEMP.$temp_name),'level'=>0));
$installProcessC->finishDownloadPackages();
return true;
}
}