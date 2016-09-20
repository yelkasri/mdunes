<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Folder_class extends WClasses {
public $system='hdd';
 private $message=true;
 private static $hddFileC=array();
 function __construct($storageType='hdd',$showMessage=true){
 if(empty($storageType))$storageType='hdd';
 $this->message=$showMessage;
  if( is_numeric($storageType)){
$storageID=$storageType;
$mainCredentialsC=WClass::get('main.credentials');
$storageType=$mainCredentialsC->loadFromID($storageType, 'typeNamekey');
 }else{
 $storageID=0;
 }
  if('local'==$storageType){
 $storageType='hdd';
 }
 $this->system=$storageType;
if(!isset(self::$hddFileC[$this->system])){
if( in_array($this->system, array('hdd','ftp'))){
self::$hddFileC[$this->system]=WClass::get('library.'.$this->system, null, 'class', false);
}else{
self::$hddFileC[$this->system]=WClass::get('main.'.$this->system, null, 'class', false);
if(!empty($storageID)) self::$hddFileC[$this->system]->setStorageID($storageID );
}
}
 }
 public function makePath($folder,$path=''){
 if(empty(self::$hddFileC[$this->system])) return false;
 return self::$hddFileC[$this->system]->makePath($folder, $path );
 }
 public function displayMessage($bool=false){
 $this->message=$bool;
 if(!empty(self::$hddFileC[$this->system]) && method_exists( self::$hddFileC[$this->system], 'messages')){
 self::$hddFileC[$this->system]->displayMessage($bool );
 } }
  public function setDisplayMessages($bool=true){
  $this->displayMessage($bool );
  }
public function create($FOLDER='',$base=null,$safe=true,$chmod='0755'){
  if(is_array($FOLDER)){
 $status=true;
 foreach($FOLDER as $fold){
 if(!$this->create($fold, $base, $safe, $chmod ))$status=false;
 } return $status;
 }
  if(!empty($base)){
 $base=rtrim($base, DS );
 $FOLDER=$base.DS.rtrim($FOLDER, DS );
 }else{
 $FOLDER=rtrim($FOLDER, DS );
 }
if(empty($FOLDER)) return false;
if($this->exist($FOLDER)){
return self::$hddFileC[$this->system]->safe($FOLDER);
}
$parent=dirname($FOLDER);
if($this->exist($parent) || $this->create($parent, '',$safe, $chmod)){
return self::$hddFileC[$this->system]->create($FOLDER, $chmod, false, $safe );
}
if($this->message){
$mess=WMessage::get();
$mess->userN('1212843291IDWF',array('$FOLDER'=>$FOLDER));
$FOLDER=$parent;
$mess->userN('1212843292GOIU',array('$FOLDER'=>$FOLDER));
}
return false;
 }
public function checkExist(){
return self::$hddFileC[$this->system]->checkExist();
}
 public function exist($folder='',$base=null){
 if(!empty($base))$base=rtrim($base, DS);
$foler=($base !=''?$base . DS : ''). $folder;
 $base=rtrim($base, DS);
return self::$hddFileC[$this->system]->exist($base . $folder );
 }
 public function delete($folder='',$base=null){
 if(is_array($folder)){
 foreach($folder as $fold){
 if(!$this->delete($fold, $base ))$status=false;
 } return $status;
 }
 if(!empty($base))$base=rtrim($base, DS);
$foler=($base !=''?$base.DS: '').$folder;
if($this->exist($folder )){
if( self::$hddFileC[$this->system]->delete($folder)) return true;
}
return false;
 }
public function copy($src,$dst,$force=false,$filter=null,$exclude=array()){
return $this->_transfer($src, $dst, null, $force, $filter, 'copy',$exclude );
}
public function move($src,$dst,$force=false,$filter=null,$exclude=array()){
return $this->_transfer($src, $dst, null, $force );
}
public function changeAccess($folder,$chmod='0755',$recursive=true,$files=true,$chmodfiles='0644'){
$status=true;
if($this->exist($folder)){
$status=self::$hddFileC[$this->system]->changeAccess($folder,$chmod);
if($status && $recursive){
$function='folders';
if($files){
$function='content';
}
$subfolders=$this->$function($folder,'',true,true);
foreach($subfolders as $subfolder){
if(!$this->exist($subfolder)){
$chmodToUse=$chmodfiles;
}else{
$chmodToUse=$chmod;
}
if(!self::$hddFileC[$this->system]->changeAccess($subfolder,$chmodToUse)){
$status=false;
}
}
}
}return $status;
}
public function files($path='',$filter=null,$recursive=false,$full=false,$exclude=array()){
return $this->content($path, $filter, $recursive, $full, 2, $exclude );
}
public function folders($path='',$filter=null,$recursive=false,$full=false,$exclude=array()){
return $this->content($path, $filter, $recursive, $full, 1, $exclude );
}
public function content($path='',$filter=null,$recursive=false,$full=false,$type=0,$exclude=array()){
if(is_array($path)){
 $return=array();
 foreach($path as $fold){
 $return[]=$this->content($path, $filter, $recursive, $full, $type, $exclude );
 } return $return;
 }
 $path=rtrim($path, DS );
if($this->exist($path)){
$resultA=self::$hddFileC[$this->system]->content($path, $filter, $recursive, $full, $type, $exclude );
if($full ) foreach($resultA as $k=> $p)$resultA[$k]=$path.DS.$p;
return $resultA;
}
if($this->message){
$mess=WMessage::get();
if(empty($path)){
$mess->userN('1213107678KHAX');
}
$FOLDER=$path;
$mess->userN('1212843292GOIY',array('$FOLDER'=>$FOLDER));
}
return false;
}
 public function compress($folder,$dst='',$force='',$exclude=null){
 if(empty($dst))$dst=$folder.'.zip';
 if(!$this->exist($folder)){
 if($this->message){
 $mess=WMessage::get();
 $FOLDER=$folder;
 $mess->userE('1212843292GOIV',array('$FOLDER'=>$FOLDER));
 } return false;
 }
 $folder=rtrim($folder, DS );
 $file=WGet::file();
 $file->system=$this->system;
 $list=null;
 if(!empty($exclude)){
  $list=$this->files($folder, '', true);
 if(!empty($list)){
 foreach($exclude as $exc){
 $exc=ltrim($exc,DS);
 $path=$folder.DS.$exc;
 if($this->exist($path)){
  array_walk($list, array('Library_Folder_class','_match'), rtrim($exc,DS). DS );
 $keys=$this->_match(true);
 foreach($keys as $key){
 unset($list[$key] );
 }
 } elseif($file->exist($path)){
  $key=array_search($exc,$list);
 if($key){
 unset($list[$key]);
 }
 } } } }
$parent=dirname($dst);
if(!$this->exist($parent)){
if(!$this->create($parent, '', true)) return false;
}else{
if($file->exist($dst) && $force!='add'){
if($force!='force'){
if($this->message){
$mess=WMessage::get();
$FOLDER=$dst;
$mess->codeE('The target archive ('.$FOLDER.') of the compression process already exists.');
}
return false;
}if(!$file->delete($dst)){
return false;
}}}
$type='tar';
$ext=strtolower( substr($dst, (strrpos($dst, '.'))+1 ));
switch($ext){
case 'bz2':
case 'gz':
break;
case 'tgz':
$ext='gz';
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
if($this->message){
}
$fold=$folder;
if(empty($list)){
if(!is_dir($folder)){
$fold=dirname($fold);
}}else{
$folder=array();
foreach($list as $file){
$folder[]=$fold.DS.$file;
}}
return $arch->createModify(array($folder),'',$fold);
}elseif('zip'==$type){
$zipC=WClass::get('library.zip');
return $zipC->zipDir($folder, $dst );
}else{
return false;
}
}
public function space($folder=null,$option='free'){
if(empty($folder))$folder=rtrim( JOOBI_DS_ROOT, DS );
if(!$this->exist($folder)){
if($this->message){
$mess=WMessage::get();
$mess->codeE('The folder '.$folder. 'does not exists. Thus, the space information for this folder could not be retrieved.');
}
return false;
}
switch($option){
case 'used':
return self::$hddFileC[$this->system]->totalSpace($folder) - self::$hddFileC[$this->system]->freeSpace($folder);
case 'free':
return self::$hddFileC[$this->system]->freeSpace($folder);
case 'total':
return self::$hddFileC[$this->system]->totalSpace($folder);
}return false;
}
 public function tempFolder($action='create',$i=0){
 switch($action){
 case 'create':
 case 'generate':
 $returnpath=false;
 if($i===true){
 $returnpath=true;
 }
 $i=0;
$safe=20;
do {
$i=md5( mt_rand().date('Y-m-d H:m:s'));
$exists=$this->exist( JOOBI_DS_TEMP . $i );
$safe--;
} while($exists && $safe !=0);
if($safe==0){
if($this->message){
$mess=WMessage::get();
$FOLDER=JOOBI_DS_TEMP;
$mess->userE('1212843292GOIZ',array('$FOLDER'=>$FOLDER));
}
return false;
}
$tempfolder=JOOBI_DS_TEMP . $i . DS;
$current=$this->_temp($tempfolder );
if($action=='generate'){
if($returnpath){
return $tempfolder;
}
return $current;
}
$status=$this->create($tempfolder, '', true);
if($status)
{
if($returnpath)
{
return $tempfolder;
}
return $current;
}
if($this->message){
$mess=WMessage::get();
$FOLDER=JOOBI_DS_TEMP;
$mess->userE('1212843292GOJA',array('$FOLDER'=>$FOLDER));
}
return false;
case 'delete':
if(!is_int($i) && $i !=(int)$i)
{
$tempfolders=$this->_temp();
$i=array_search($i,$tempfolders);
if($i===false)
{
return false;
}
}
$status=$this->delete($this->_temp($i));
return $status;
case 'clean':
$tempfolders=$this->_temp();
foreach($tempfolders as $k=> $tempfolder)
{
$status=$this->delete($tempfolder);
if($status)
{
$this->_temp($k);
}
}
$tempfolders=array();
break;
case 'path':
$tempfolders=$this->_temp();
if(array_key_exists($i,$tempfolders))
return $tempfolders[$i];
return false;
case 'list':
break;
 } }
 private function _temp($i=null){
 static $tempfolders=array();
 if( is_int($i)){
 $tempfolder=$tempfolders[$i];
 unset($tempfolders[$i]);
 return $tempfolder;
 }elseif(is_string($i)){
 $tempfolders[]=$i;
 return count($tempfolders)-1;
 }
 return $tempfolders;
 }
private function _transfer($src,$dst,$base=null,$force=false,$filter=null,$function='move',$exclude=array()){
$dst=rtrim($dst, DS );
 if(is_array($src)){
 $return=true;
 foreach($src as $fold){
 if(!$this->_transfer($fold, $dst, $base, $force, $filter, $function, $exclude ))$return=false;
 } return $return;
 }
 if(!empty($base)){
 $base=rtrim($base, DS );
 $src=(!empty($base)?$base . DS : ''). rtrim($src, DS );
 $dst=(!empty($base)?$base . DS : ''). $dst;
 }
 $src=rtrim($src, DS );
if(!$this->exist($src)){
if($this->message){
$mess=WMessage::get();
$FOLDER=$src;
$mess->userN('1212843292GOIV',array('$FOLDER'=>$FOLDER));
}return false;
}
if(!$this->exist($dst)){
if($function=='move'){
$parent=dirname($dst);
if(!$this->exist($parent)){
if(!$this->create($parent,'',true)) return false;
}
}elseif(!$this->create($dst, '', true)){
return false;
}
}elseif($function=='copy' && is_string($force) && $force=='backup'){
return self::$hddFileC[$this->system]->$function($src, $dst, false, $filter, true, $exclude );
}elseif($function=='copy' && is_string($force) && $force=='user_backup'){
return self::$hddFileC[$this->system]->$function($src, $dst, false, $filter, 2, $exclude );
}elseif($function=='copy' && is_string($force) && $force=='add_over'){
return self::$hddFileC[$this->system]->$function($src, $dst, false, $filter, 3, $exclude );
}elseif($force || $function=='move'){
if(!$this->delete($dst)){
if($this->message){
$mess=WMessage::get();
$FOLDER=$src;
$mess->userN('1212843292GOIW',array('$FOLDER'=>$FOLDER));
}return false;
}
}else{
if($this->message){
$mess=WMessage::get();
$FOLDER=$src;
$mess->userN('1212843292GOIX',array('$FOLDER'=>$FOLDER));
}return false;
}
if($function=='move'){
return self::$hddFileC[$this->system]->$function($src, $dst );
}else{
return self::$hddFileC[$this->system]->$function($src, $dst, false, $filter, false, $exclude );
}
}
private function _match($value,$key='',$exclude=''){
static $keys=array();
if($value===true){
return $keys;
}if(strpos($value,$exclude)===0){
$keys[]=$key;
}}
}