<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
if(!defined('FILE_APPEND')) define('FILE_APPEND', 8 );
class Library_Storage_class {
protected $message=true;
protected $fileInfoO=null;
protected $storageID=null;
public function setStorageID($storageID){
$this->storageID=$storageID;
}
 public function displayMessage($bool=true){
 $this->message=$bool;
 }
 public function setFileInformation($fileInfoO){
 $this->fileInfoO=$fileInfoO;
 }
public function fileURL($thumbnail=false){
}
public function download(){
}
public function checkExist(){
return true;
}
public function exist($path,$file=false){
return true;
}
public function writable($fold,$file=false){
return true;
}
  public function makePath($folder,$path=''){
 return false;
 }
public function delete($path,$file=false){
return true;
}
public function changeAccess($path,$chmod){
return true;
}
public function copy($src,$dst,$file=false,$filter=null,$backup=false,$exclude=array()){
  return true;
}
public function move($src,$dst){
return true;
}
public function fileSize($file){
return 0;
}
  public function upload($src,$dst,$file=true){
return true;
  }
  public function read($path){
  return '';
  }
  public function write($path,$content,$append=false,$chmod='0644'){
  return 0;
  }
  public function safe($folder=''){
  return true;
  }
  public function create($path,$chmod='0755',$file=false,$safe=false){
return true;
  }
  public function content($path='',$filter=null,$recursive=false,$full=false,$type=0,$exclude=array()){
  return array();
  }
public function freeSpace($folder){
return 0;
}
public function totalSpace($folder){
return 0;
}
}