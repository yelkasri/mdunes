<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_uploadfile_controller extends WController {
function uploadfile(){
if(!WPref::load('PLIBRARY_NODE_ALLOW_FILE_UPLOAD')) exit;
error_reporting( E_ALL ^ E_NOTICE ); 
$fileName=WGlobals::get('ax-file-name');
$currByte=WGlobals::get('ax-start-byte');
$maxFileSize=WGlobals::get('ax-maxFileSize');
$html5fsize=WGlobals::get('ax-fileSize');
$isLast=WGlobals::get('isLast');
$thumbHeight=WGlobals::get('ax-thumbHeight');
$thumbWidth=WGlobals::get('ax-thumbWidth');
$thumbPostfix=WGlobals::get('ax-thumbPostfix');
$thumbPath=WGlobals::get('ax-thumbPath');
$thumbFormat=WGlobals::get('ax-thumbFormat');
$TYTYTY=WGlobals::get('ax-allow-ext');
$allowExt=(empty($TYTYTY ))?array() : explode('|',$TYTYTY );
$randomKey=WGlobals::getSession('upld','rdmKy');
if(empty($randomKey)){
$randomKey='z'.rand( 10000, 99999 );
WGlobals::setSession('upld','rdmKy',$randomKey );
}$uploadPath=JOOBI_DS_TEMP.'uploads'.DS.$randomKey . DS;
$dirSys=WGet::folder();
if(!$dirSys->exist($uploadPath )){
$dirSys->create($uploadPath );
}
$thumbPath='';
$axFilesA=WGlobals::get('ax-files', null, 'files');
if(isset($axFilesA)){
foreach($axFilesA['error'] as $key=> $error){
if($error==UPLOAD_ERR_OK){
$newName=(!empty($fileName)?$fileName:$axFilesA['name'][$key] );
$fullPath=Output_uploadfile_controller::_checkFilename($allowExt, $uploadPath, $maxFileSize, $newName, $axFilesA['size'][$key] );
if($fullPath){
move_uploaded_file($axFilesA['tmp_name'][$key], $fullPath );
WText::load('output.node');
$fileUPdaedText=WText::t('1360160364KSSI');
echo json_encode( array('name'=>basename($fullPath), 'size'=> filesize($fullPath), 'status'=>'uploaded','info'=> $fileUPdaedText ));
die;
}}  else {
echo json_encode( array('name'=>basename($axFilesA['name'][$key]), 'size'=>$axFilesA['size'][$key], 'status'=>'error','info'=>$error));
die;
}}
}elseif(isset($fileName)){
$fullPath=(($currByte!=0 )?$uploadPath . $fileName : Output_uploadfile_controller::_checkFilename($allowExt, $uploadPath, $maxFileSize, $fileName, $html5fsize ));
if($fullPath){
$flag=($currByte==0 )?0 : FILE_APPEND;
$receivedBytes=file_get_contents('php://input');
while( @file_put_contents($fullPath, $receivedBytes, $flag )===false){
usleep(50);
}
if($isLast=='true'){
}
WText::load('output.node');
$fileUPdaedText=WText::t('1360160364KSSJ');
echo json_encode( array('name'=>basename($fullPath), 'size'=>$currByte, 'status'=>'uploaded','info'=> $fileUPdaedText ));
die;
}
}
return true;
}
private function _checkFilename($allowExt,$uploadPath,$maxFileSize,$fileName,$size,$newName=''){
$maxsize_regex=preg_match("/^(?'size'[\\d]+)(?'rang'[a-z]{0,1})$/i", $maxFileSize, $match);
$maxSize=4*1024*1024;if($maxsize_regex && is_numeric($match['size'])){
switch (strtoupper($match['rang'])){ case 'K': $maxSize=$match[1]*1024; break;
case 'M': $maxSize=$match[1]*1024*1024; break;
case 'G': $maxSize=$match[1]*1024*1024*1024; break;
case 'T': $maxSize=$match[1]*1024*1024*1024*1024; break;
default: $maxSize=$match[1];}
}
if(!empty($maxFileSize) && $size>$maxSize){
WText::load('output.node');
$fileUPdaedText=WText::t('1360160364KSSK');
echo json_encode(array('name'=>$fileName, 'size'=>$size, 'status'=>'error','info'=> $fileUPdaedText ));
die;
}
$windowsReserved=array('CON','PRN','AUX','NUL','COM1','COM2','COM3','COM4','COM5','COM6','COM7','COM8','COM9',
'LPT1','LPT2','LPT3','LPT4','LPT5','LPT6','LPT7','LPT8','LPT9');
$badWinChars=array_merge(array_map('chr', range(0,31)), array("<", ">", ":", '"', "/", "\\", "|", "?", "*"));
$fileName=str_replace($badWinChars, '',$fileName);
$fileInfo=pathinfo($fileName);
$fileExt=$fileInfo['extension'];
$fileBase=$fileInfo['filename'];
if(in_array($fileName, $windowsReserved))
{
echo json_encode(array('name'=>$fileName, 'size'=>0, 'status'=>'error','info'=>'File name not allowed. Windows reserverd.'));
die;
}
if(!in_array( strtolower($fileExt), $allowExt ) && count($allowExt)){WText::load('output.node');
$FILE_EXTENSION=$fileExt;
$fileUPdaedText=str_replace(array('$FILE_EXTENSION'), array($FILE_EXTENSION),WText::t('1443227509HADC'));
echo json_encode(array('name'=>$fileName, 'size'=>0, 'status'=>'error','info'=> $fileUPdaedText ));
die;
}
$fullPath=$uploadPath . $fileName;
$c=0;
while(file_exists($fullPath)){
$c++;
$fileName=$fileBase."($c).".$fileExt;
$fullPath =$uploadPath.$fileName;
}
return $fullPath;
}
}