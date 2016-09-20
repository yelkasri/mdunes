<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Upload_class extends WClasses {
public function secureFileUpload($files,$extA,$fieldName=''){
$hasUploadFile=false;
$name2Check='';
$filesFancyuploadC=WClass::get('files.fancyupload');
$fancyFileUpload=$filesFancyuploadC->check();
if($fancyFileUpload){
$map='x'.$fieldName;
if(!empty($files['tmp_name'][0][0][$map])){
$fileLocation=$files['tmp_name'][0][0][$map];
$csvFile=$files['name'][0][0][$map];
$error=$files['error'][0][0][$map];
$filetype=$files['type'][0][0][$map];
$name2Check=$fileLocation;
$hasUploadFile=true;
}else{
return false;
}
}else{
$trk=WGlobals::get( JOOBI_VAR_DATA , '','files');
if(!empty($trk['tmp_name']['x'][$fieldName])){
$fileLocation=$trk['tmp_name']['x'][$fieldName];
$csvFile=$trk['name']['x'][$fieldName];
$error=$trk['error']['x'][$fieldName];
$filetype=$trk['type']['x'][$fieldName];
$name2Check=$csvFile;
$hasUploadFile=true;
}
}
if(!$hasUploadFile){
$this->userE('1369750880LIYV');
return false;
}
if($error > 0){
$this->userE('1441611733AERD');
$this->userE('1441611733AERE');
$this->_removeFile($fileLocation );
return false;
}
$pos=strrpos($name2Check, '.');
if(false===$pos){
$this->userE('1441611733AERD');
$this->userE('1441611733AERE');
$this->_removeFile($fileLocation );
return false;
}
if(empty($extA))$extA=array('png','jpg','jpg');
else {
if( is_string($extA))$extA=array($extA );
}
$ext=substr($name2Check, $pos+1 );
if(!in_array($ext, $extA )){
$VALID_EXTENSION=implode(',',$extA );
if( count($extA)==1){
$this->userE('1443227611IILP',array('$VALID_EXTENSION'=>$VALID_EXTENSION));
}else{
$this->userE('1443227611IILQ',array('$VALID_EXTENSION'=>$VALID_EXTENSION));
}$this->_removeFile($fileLocation );
return false;
}
$r=array('location'=> $fileLocation, 'name'=> $csvFile, 'type'=> $ext );
return $r;
}
private function _removeFile($path=''){
$fileC=WGet::file();
if(!empty($path)) return $fileC->delete($path );
return false;
}
}