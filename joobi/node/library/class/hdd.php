<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
if(!defined('FILE_APPEND')) define('FILE_APPEND', 8 );
class Library_Hdd_class {
private $message=true;
protected $fileInfoO=null;
 public function displayMessage($bool=true){
 $this->message=$bool;
 }
public function checkExist(){
return true;
}
public function exist($path,$file=false){
if($file ) return @is_file($path );
return @is_dir($path);
}
 public function makePath($folder,$path=''){
 return JOOBI_DS_USER . $folder . (!empty($path)?DS . $path : '');
 }
public function download(){
$libraryDownload=WClass::get('main.download');
return $libraryDownload->download($this->fileInfoO );
}
 public function setFileInformation($fileInfoO){
 $this->fileInfoO=$fileInfoO;
 }
 public function fileURL($thumbnail=false){
if($this->fileInfoO->type=='url'){
return $this->fileInfoO->name;
}else{
$URL=(!empty($this->fileInfoO->basePath)?$this->fileInfoO->basePath : '');
$URL .=str_replace('|','/',$this->fileInfoO->path );
if(!empty($this->fileInfoO->thumbnail) && $thumbnail)$URL .='/thumbnails';
$URL .='/';
$URL .=$this->fileInfoO->name;
$URL .='.';
$URL .=$this->fileInfoO->type;
return $URL;
}
}
public function writable($fold){
return is_writable($fold );
}
public function fileSize($file){
clearstatcache();
return $this->_runCriticalFunction('filesize',$file );
}
 public function compress($file,$compression='gz',$force=''){
 $dst=$file.'.'.$compression;
if($this->exist($dst, true)){
if($force !='force'){
if($this->message){
$mess=WMessage::get();
$FOLDER=$dst;
$mess->codeE('The target archive ('.$FOLDER.') of the compression process already exists.');
}
return false;
}
if(!$this->delete($dst, true)){
return false;
}
}
$ext=$compression;
$type='tar';
switch ($ext){
case 'bz2':
case 'gz':
break;
case 'bz':
$ext='bz2';
break;
case 'zip':
$type='zip';
break;
default:
$ext=null;
break;
}
if('tar'==$type){
WExtension::includes('lib.archivetar');
if(!class_exists('Archive_Tar')) return false;
$arch=new Archive_Tar($dst, $ext );
$folder=dirname($file );
$status=$arch->createModify( array($file), '',$folder );
}elseif('zip'==$type){
if( version_compare( phpversion(), '5.2','<')){
$message=WMessage::get();
$message->adminE('PHP 5.2 is required for ZipArchive module to function!');
return false;
}
if(!class_exists('ZipArchive')){
$message=WMessage::get();
$message->adminE('ZipArchive is not installed on the server!');
return false;
}
$zip=new ZipArchive;
$zip->open($dst, ZipArchive::CREATE );
$zip->addFile($file);
$zip->close();
$status=true;
}else{
$status=false;
}
if($status){
$this->changeAccess($file, '0755');
}
return $status;
}
  public function create($path,$chmod='0755',$file=false,$safe=false){
static $open_basedir;
if($file ) return true;
rtrim($path, DS);
$mask=@umask(0);
if( is_dir($path)) return true;
if( is_string($chmod)){
  $chmod=intval($chmod, 8 );
  }
$status=$this->_runCriticalFunction('mkdir',$path, $chmod );
umask($mask);
if($safe)$status=$this->safe($path );
return $status;
  }
  public function delete($path,$file=false){
  if($file) return $this->_runCriticalFunction('unlink',$path );
if($dirHandle=@opendir($path)){
$old_cwd=getcwd();
chdir($path);
$path=rtrim($path,DS);
while ($name=@readdir($dirHandle)){
if($name=='.' || $name=='..') continue;
if(is_dir($path.DS.$name)){
  if(!$this->delete($path.DS.$name)) return false;
}else{
  if(!$this->delete($path.DS.$name,true)) return false;
}
}
closedir($dirHandle);
@chdir($old_cwd);
return $this->_runCriticalFunction('rmdir',$path);
}
return false;
  }
  public function content($path='',$filter=null,$recursive=false,$full=false,$type=0,$excludeA=array(),$baseFolder=''){
  if(is_array($path)){
$dir=implode( DS, $path );
if(!$this->exist($dir)){
echo '<br>Error 891:Could not list the folder '.$dir;
return false;
}}else{
$dir=$path;
}
$dirHandle=$this->_runCriticalFunction('opendir',$dir );
if(!$dirHandle ) return false;
$array=array();
$excludeMe=array('.','..','.svn','CVS','.project','CVS','.DS_Store','__MACOSX');if(!empty($excludeA)){
if(is_array($excludeA)){
$excludeMe=array_merge($excludeMe, $excludeA );
}elseif( is_string($excludeA)){
$excludeMe[]=$excludeA;
}}
$old_cwd=getcwd();
chdir($dir);
while ($name=@readdir($dirHandle)){
if(isset($this->checkindex)){
if(!is_dir($name) && $name !='index.html') continue;
}else{
if( in_array($name, $excludeMe)) continue;
if(!empty($filter)){
$filter=rtrim($filter, DS );
if($type !=2 || !is_dir($name)){
if( substr($filter,0,2)=='!!'){
if(!preg_match('#'.substr($filter,2).'#',$name)) continue;
}elseif( preg_match('#'.$filter.'#',$name)){
continue;
}}}
}
if( is_dir($name)){
if($type < 2){
  $array[]=$name;
}
if($recursive){
if(!empty($baseFolder))$baseFolder=$baseFolder.DS.$name;
else $baseFolder=$name;
  if(is_array($path)){
  $result=$this->content( array($path[0],$path[1].DS.$name), $filter, $recursive, $full, $type, $excludeA, $baseFolder );
  }else{
  $result=$this->content( array($path, $name), $filter, $recursive, $full, $type, $excludeA, $baseFolder );
  }
  if(!empty($result) && !empty($name) && empty($excludeA)){
  $newA=array();
  foreach($result as $k=> $p)$newA[$k]=$name.DS.$p;
  $result=$newA;
  }
    if(!empty($excludeA)){
$newA=array();
  if(is_array($excludeA)){
  foreach($result as $k=> $p){
  $valueFinal=$name.DS.$p;
  $dontAdd=false;
  foreach($excludeA as $exVal){
  if( strpos($valueFinal, $exVal ) !==false){
  $dontAdd=true;
  }  }  if(!$dontAdd)$newA[$k]=$valueFinal;
  }  }else{
  $newA=array();
  foreach($result as $k=> $p){
  $valueFinal=$name.DS.$p;
  if( strpos($valueFinal, $excludeA )===false){
  $newA[$k]=$valueFinal;
  }  }
  }  $result=$newA;
  }
  if($result !==false)$array=array_merge($array, $result );
}}elseif($type !=1){$array[]=$name;
}
}
closedir($dirHandle );
chdir($old_cwd );
return $array;
  }
  public function move($src,$dst){
  return $this->_runCriticalFunction('rename',$src, $dst );
  }
  public function upload($src,$dst){
      $filesFancyuploadC=WClass::get('files.fancyupload');
$fancyFileUpload=$filesFancyuploadC->check();
  if($fancyFileUpload ) return $this->move($src, $dst );
  if(!$this->_checkConditions()) return false;
$status=$this->_runCriticalFunction('move_uploaded_file',$src, $dst );
if($status){
$status=$this->changeAccess($dst, '0644');
return $status;
}
return false;
  }
public function copy($src,$dst,$file=false,$filter=null,$backup=false,$exclude=array()){
if($file){
$status=$this->_runCriticalFunction('copy',$src, $dst );
if($status)$this->changeAccess($dst, '0644');
return $status;
}
if(!$backup && $this->create($dst,'0755', false, true)===false){
  return false;
}
$array=$this->content($src, $filter, true, false, 1, $exclude );
if(is_array($array)){
  foreach($array as $fold){
  if($backup && $this->exist($dst.DS.$fold )) continue;
if($this->create($dst.DS.$fold, '0755', false, true)===false){
return false;
}  }}
$array=$this->content($src, $filter, true, false, 3, $exclude );
if(!empty($array) && is_array($array)){
  foreach($array as $myfile){
  $src_file=$src.DS.$myfile;
  $dst_file=$dst.DS.$myfile;
  if($backup===2){
    $tmp=explode('.',$myfile );
$ext=array_pop($tmp );
$original_file='';
if($ext!='xml'){
$tmp[]='original';
$tmp[]=$ext;
$tmp=implode('.',$tmp);
$original_file=$dst.DS.$tmp;
if(!$this->exist($original_file, true)){
if($this->copy($src_file,$original_file,true)===false){
return false;
}if($this->copy($src_file,$dst_file,true)===false){
return false;
}continue;
}}  }
if($backup && $this->exist($dst_file, true)){
if($backup===3){
if(!$this->delete($dst_file, true)){
WMessage::log('Copy function backup mode 3, the file could not be deleted','error-filesystem');
WMessage::log($dst_file, 'error-filesystem');
return false;
}
}elseif($backup===2){
if(empty($original_file)){
if(!$this->delete($dst_file,true)){
WMessage::log('Copy function backup mode 2, the file could not be deleted','error-filesystem');
WMessage::log($dst_file, 'error-filesystem');
return false;
}
}else{
$md5_original=md5_file($original_file );
$md5_dst=md5_file($dst_file );
if($md5_original==$md5_dst){
if(!$this->delete($dst_file,true)){
WMessage::log('Copy function backup mode 2 with md5, the file could not be deleted','error-filesystem');
WMessage::log($dst_file, 'error-filesystem');
return false;
}
if($this->copy($src_file, $dst_file, true)===false){
WMessage::log('Copy function backup mode 2 with md5, the file could not be copied','error-filesystem');
WMessage::log('From: '.$src_file, 'error-filesystem');
WMessage::log('To: '.$dst_file, 'error-filesystem');
return false;
}}
$dst_file=$original_file;
}
}else{
$backup_file=str_replace( DS.DS, DS, rtrim( JOOBI_DS_USER.'backup', DS ).DS . ltrim( substr($dst.DS.$myfile, strlen(JOOBI_DS_ROOT)), DS ));
    if($this->exist($backup_file,true))
  {
  if(!$this->delete($backup_file,true))
  {
  $FILE=$backup_file;
  WMessage::log('Could not remove the old backup file '.$FILE  , 'error-filesystem');
return false;
  }
  }
    $parent=dirname($backup_file);
  if(!$this->exist($parent))
  {
  $folder_handler=WGet::folder();
  if(!$folder_handler->create($parent,'',true)){
  WMessage::log('Could not backup the file '.$FILE.' because the folder ' .$parent.' could not be created','error-filesystem');
  return false;
  }  }
    if(!$this->move($dst.DS.$myfile, $backup_file )){
  $FILE=$dst_file;
  $BACKUP_FILE=$backup_file;
  WMessage::log('Could not backup the file '.$FILE.' to the file ' .$BACKUP_FILE , 'error-filesystem');
return false;
  }
$FILE=$dst_file;
$BACKUP_FILE=$backup_file;
}}
if($this->copy($src_file, $dst_file, true)===false){
WMessage::log('File could not be copied in the folder copy function!','error-filesystem');
WMessage::log('From: '.$src_file, 'error-filesystem');
WMessage::log('To: '.$dst_file, 'error-filesystem');
return false;
}
  }
}
return true;
  }
  public function read($path){
return file_get_contents($path );
  }
  public function write($path,$content,$append=false,$chmod='0644'){
if(!is_string($content))$content=(string)$content;
if($append) return $this->_runCriticalFunction('file_put_contents',$path, $content, FILE_APPEND );
$status=$this->_runCriticalFunction('file_put_contents',$path, $content );
if($status){
$this->changeAccess($path, $chmod );
}return $status;
  }
  public function changeAccess($path,$chmod){
  if( is_string($chmod))$chmod=intval($chmod, 8 );
  return $this->_runCriticalFunction('chmod',$path, $chmod );
  }
  public function safe($folder=''){
if($folder=='')$folder=$this->folder;
$file=$folder.DS.'index.html';
if($this->exist($file, true)) return true;
return $this->write($file, '<html><body bgcolor="#FFFFFF"></body></html>');
  }
public function freeSpace($folder){
return $this->_runCriticalFunction('disk_free_space',$folder );
}
public function totalSpace($folder){
return $this->_runCriticalFunction('disk_total_space',$folder );
}
private function _correctParentFolderPermissions($path){
$path=dirname($path);
if(is_writable($path) && is_readable($path)) return true;
if( function_exists('posix_getuid')){
if( @fileowner($path)===posix_getuid()){
if($this->changeAccess($path, '0755')){
return true;
}}}
return false;
}
  private function _runCriticalFunction($function,$arg1=null,$arg2=null,$arg3=null){
  ob_start();
  if(isset($arg3)){
  $status=@$function($arg1, $arg2, $arg3 );
if(!$status )  {
if($this->_correctParentFolderPermissions($arg1))$status=@$function($arg1,$arg2,$arg3);
}
  }elseif(isset($arg2)){
$status=@$function($arg1, $arg2 );
if(!$status){
if( in_array($function, array('rename','copy','move_uploaded_file'))){
$status=$this->_correctParentFolderPermissions($arg2 );
}else{
$status=$this->_correctParentFolderPermissions($arg1 );
}if($status)$status=@$function($arg1, $arg2 );
}  }else{
$status=@$function($arg1);
  }
if($status===false){
$error=ob_get_clean();
if(!empty($error)){
$regex='(?:(?!'.$function.'\(\)).)*'.$function.'\(\)\s(?:\[(?:(?!]).)*\]:)?\s?((?:(?!(on line|<br ?\/>)).)*)';
$matches=null;
if( preg_match_all('#'.$regex.'#',$error, $matches))$error=implode('<br/>'."\r\n",$matches[1]);
if($this->message){
$message=WMessage::get();
$message->codeE($error );
WMessage::log('Error in _runCriticalFunction()','error-filesystem');
WMessage::log($error, 'error-filesystem');
}else{
echo 'Error message file system: ';
echo $error;
}
}} else ob_end_clean();
return $status;
  }
  protected function _checkConditions(){
if(!(bool) ini_get('file_uploads')){
$mess=WMessage::get();
$mess->userW('1212843257NVLH');
return false;
}  return true;
  }
}