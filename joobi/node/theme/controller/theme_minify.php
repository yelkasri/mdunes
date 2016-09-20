<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_minify_controller extends WController {
function minify(){
$eid=WGlobals::getEID();
$themeM=WModel::get('theme');
$themeM->whereE('tmid',$eid );
$themeO=$themeM->load('o',array('tmid','folder','wid'));
if(empty($themeO)){
$this->userW('1395437861EIXK');
WPages::redirect('controller=theme');
return false;
}
$path='';
return false;
$folderS=WGet::folder();
$allFilesA=$folderS->files($path, '', true, false, array('index.html'));
if(!empty($allFilesA)){
$filesMinifyC=WClass::get('files.minify');
foreach($allFilesA as $oneFile){
$piecesA=explode('.',$oneFile );
$extension=array_pop($piecesA );
if('css'==$extension){
$filesMinifyC->compressFileCSS($path . $oneFile );
}elseif('js'==$extension){
$filesMinifyC->compressFileJS($path . $oneFile );
}
}
}
WPages::redirect('controller=theme');
return true;
}}