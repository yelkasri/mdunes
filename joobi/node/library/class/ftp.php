<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WLoadFile('library.class.hdd', JOOBI_DS_NODE );
class Library_Ftp_class extends Library_Hdd_class {
private $errorMessage='';
protected $fileInfoO=null;
private $ftpInstance=null;
 function __construct(){
  $this->ftpInstance=WAddon::get('api.'. JOOBI_FRAMEWORK.'.ftp');
 }
  public function create($path,$chmod='0755',$file=false,$safe=false){
return $this->ftpInstance->create($path, $chmod );
  }
  public function delete($path,$file=false){
  return $this->ftpInstance->delete($path, $file );
  }
  public function content($path='',$filter=null,$recursive=false,$full=false,$type=0,$exclude=array()){
  return $this->ftpInstance->delete($path, $filter, $recursive, $full, $type, $exclude );
  }
  public function move($src,$dst){
  return $this->ftpInstance->move($src, $dst );
  }
  public function upload($src,$dst){
      $filesFancyuploadC=WClass::get('files.fancyupload');
$fancyFileUpload=$filesFancyuploadC->check();
  if($fancyFileUpload ) return $this->move($src, $dst );
  if(!$this->_checkConditions()) return false;
  $status=$this->ftpInstance->move($src, $dst );
return $status;
  }
public function copy($src,$dst,$file=false,$filter=null,$backup=false){
return $this->ftpInstance->copy($src, $dst, $file );
  }
  public function read($path){
  return $this->ftpInstance->read($path );
  }
  public function write($path,$content,$append=false,$chmod='0644'){
  return $this->ftpInstance->write($path, $content );
  }
}