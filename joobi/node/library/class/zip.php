<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Zip_class {
  private static function folderToZip($folder,&$zipFile,$exclusiveLength){
$handle=opendir($folder);
while (false !==$f=readdir($handle)){
  if($f !='.' && $f !='..'){
$filePath="$folder/$f";
$localPath=substr($filePath, $exclusiveLength);
if(is_file($filePath)){
  $zipFile->addFile($filePath, $localPath);
}elseif(is_dir($filePath)){
    $zipFile->addEmptyDir($localPath);
  self::folderToZip($filePath, $zipFile, $exclusiveLength);
}
  }}
closedir($handle);
  }
  public function zipDir($sourcePath,$outZipPath){
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
$fileA=pathInfo($outZipPath );
$folderName=$fileA['filename'];
$temp=JOOBI_DS_TEMP.'zip'.DS.time().DS.$folderName;
$folderS=WGet::folder();
$folderS->copy($sourcePath, $temp );
$pathInfo=pathInfo($temp );
$parentPath=$pathInfo['dirname'];
$dirName=$pathInfo['basename'];
$z=new ZipArchive();
$z->open($outZipPath, ZIPARCHIVE::CREATE );
$z->addEmptyDir($dirName );
self::folderToZip($temp, $z, strlen( "$parentPath/" ));
$z->close();
return true;
  }
}