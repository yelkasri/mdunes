<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Translation_Exportlang_class extends WClasses {
 var $codeOrigin='';
 var $codeDest='en';
 var $path=''; var $content=array(); var $fileContent=''; var $count=0; var $fileHeader=''; var $version='1.0.0'; var $header=true;
 var $method=0;
 var $extensionName='';
private $_TR=null;
public $maxStringLenght=0;
 function __construct($lineJump="\r\n"){
$this->lineJump=$lineJump;
if(!isset($this->_TR))$this->_TR=WGlobals::get('translationConstantPrefix','TR','global');
 }
public function getText2Translate($originlgid,$destinationlgid,$wid=0,$onlyNotTranslated=true,$limit=10000){
static $allwedWID=array();
static $languageInformationsA=array();
WTools::increasePerformance();
if(empty($originlgid) || empty($destinationlgid)) return false;
if(!is_numeric($originlgid))$originlgid=WLanguage::get($originlgid, 'lgid');
if(!is_numeric($destinationlgid))$destinationlgid=WLanguage::get($destinationlgid, 'lgid');
if(empty($wids)){
if(empty($wid)) return false;
else $wids=is_array($wid)?$wid : array($wid );
}
$this->_getDependancies($wids );
$selectedImacsA=$this->_getImacs($wids );
$mySepcialKey=$originlgid.'-'.$destinationlgid;
if(empty($languageInformationsA[$mySepcialKey])){
$languageModel=WModel::get('library.languages');
$languageModel->whereIn('lgid',array($originlgid, $destinationlgid));
$languageModel->setLimit(2);
$languageInformationsA[$mySepcialKey]=$languageModel->load('ol',array('lgid','code','name'));
}
foreach($languageInformationsA[$mySepcialKey] as $lang){
$languageInfos[$lang->lgid]=$lang;
$languageInfos[$lang->lgid]->namekey='translation.'.$languageInfos[$lang->lgid]->code;
}
$model=WModel::get('library.model','object');
$model->whereE('namekey',$languageInfos[$destinationlgid]->namekey);
$exist=$model->exist();
$exportdata=array();
if($exist && $onlyNotTranslated && $destinationlgid !=$originlgid){
$translationChecktableC=WClass::get('translation.checktable');
$transExist=$translationChecktableC->transTableExist( str_replace('translation.','',$languageInfos[$destinationlgid]->namekey ));
$transModelOr=WModel::get($languageInfos[$originlgid]->namekey );
$transModelOr->select( array('imac','text'));
if(!empty($selectedImacsA))$transModelOr->whereIn('imac',$selectedImacsA );
if($transExist){
$transModelOr->makeLJ($languageInfos[$destinationlgid]->namekey,'imac');
$transModelOr->isNull('imac', true, 1 );
}
$transModelOr->setLimit($limit );
$exportdata=$transModelOr->load('ol');
}else{
$transModel=WModel::get('translation.en');
$transModel->makeLJ($languageInfos[$originlgid]->namekey, 'imac');
$transModel->select('text', 1, 'original');
if(!empty($selectedImacsA))$transModel->whereIn('imac',$selectedImacsA );
$exportdata=$transModel->load('ol',array('imac','text'));
if(!empty($exportdata)){
foreach($exportdata as $key=> $oneWord){
if(!empty($oneWord->original))$exportdata[$key]->text=$oneWord->original;
}}
}
unset($selectedImacsA );
$this->setContent($exportdata );
$this->setDestCode($languageInfos[$destinationlgid]->code );
if($destinationlgid !=$originlgid)$this->setOriginCode($languageInfos[$originlgid]->code );
if(!empty($wid))$this->extensionName=WExtension::get($wid,'name');
$this->wid=$wid;
return !empty($exportdata)?true : false;
}
 function setFileHeader($content){
 if(!empty($content))$this->fileHeader=$content;
 }
 function setApplicationName($content){
 if(!empty($content))$this->extensionName=$content;
 }
 function setContent($content){
 $this->content=$content;
 }
 function setVersion($content){
 if(!empty($content))$this->version=$content;
 }
 function setDestCode($destCode){
 $this->codeDest=$destCode;
 }
 function setOriginCode($origCode){
 $this->codeOrigin=$origCode;
 }
 function setPath($path){
 $this->path=$path;
 }
 function setHeader($bool=true){
 $this->header=$bool;
 }
 function createLanguageString(){
if(empty($this->content)) return '';
 $this->fileContent='';
 foreach($this->content as $myContentO){
 $this->fileContent .=$this->_TR . $myContentO->imac. '='. $myContentO->text . $this->lineJump;
 }
 return $this->fileContent;
 }
 function createAutomaticLanguageString($maxCharacter2Translate){
   $count=0;
 $oneLineContent='';
 $contentfile="<table S1T2A3R4T border=1 BORDERCOLOR=RED>";
if(empty($this->content)) return '';
$i=0; foreach($this->content as $actualLine){
$i++;
 if($maxCharacter2Translate==0 || $count < $maxCharacter2Translate){
 $contentfile .=$oneLineContent;
 $oneLineContent="<tr R1E1V>"; $oneLineContent .="<td R1E1V>"; $oneLineContent .=$actualLine->imac;  $oneLineContent .="</td R1E1V>"; $oneLineContent .="<td R1E1V>";
    $content=$actualLine->text;
 preg_match_all("/(?s){widget(.+?)\}/", $content, $matches, PREG_PATTERN_ORDER );
  foreach($matches[0] as $match){
 $encoded=base64_encode($match);
  $encoded=str_replace('+','ABC000123XYZ',$encoded);
 $encoded=str_replace('/','XYZ000123ABC',$encoded);
 $encoded='R1Y2A3N'.$encoded.'R1Y2A3N';
 $content=str_replace($match,$encoded,$content);
 }
 $content=str_replace('$','123XYZ',$content);
  preg_match_all("/(?s)<a (.+?)\>/", $content, $match, PREG_PATTERN_ORDER );
 foreach($match[0] as $link){
 $encoded=base64_encode($link);
  $encoded=str_replace('+','ABC000123XYZ',$encoded);
 $encoded=str_replace('/','XYZ000123ABC',$encoded);
 $encoded='L1I2N3K'.$encoded.'L1I2N3K';
 $content=str_replace($link,$encoded,$content);
 }
    $content=str_replace('</a>','E1N2D3',$content);
  preg_match_all("/(?s)http(.+?)\ /", $content, $match, PREG_PATTERN_ORDER);
 foreach($match[0] as $link){
 $encoded=base64_encode($link);
  $encoded=str_replace('+','ABC000123XYZ',$encoded);
 $encoded=str_replace('/','XYZ000123ABC',$encoded);
 $encoded='U1R2L3'.$encoded.'U1R2L3';
 $content=str_replace($link,$encoded,$content);
 }
 preg_match_all("/(?s)index.php(.+?)\ /", $content, $match, PREG_PATTERN_ORDER);
 foreach($match[0] as $link){
 $encoded=base64_encode($link);
  $encoded=str_replace('+','ABC000123XYZ',$encoded);
 $encoded=str_replace('/','XYZ000123ABC',$encoded);
 $encoded='I1N1D1'.$encoded.'I1N1D1';
 $content=str_replace($link,$encoded,$content);
 }
 preg_match_all("/(?s)index2.php(.+?)\ /", $content, $match, PREG_PATTERN_ORDER);
 foreach($match[0] as $link){
 $encoded=base64_encode($link);
 $encoded='I2N2D2'.$encoded.'I2N2D2';
 $content=str_replace($link,$encoded,$content);
 }
 htmlspecialchars_decode($content);
 $oneLineContent .=$content;
 $oneLineContent .="</td R1E1V>"; $oneLineContent .="</tr R1E1V>";
  $lineSize=strlen($oneLineContent );
  if($i==1 && $maxCharacter2Translate > 0 && $lineSize > 3000){
 $maxCharacter2Translate=4900; break; } $count=$count + $lineSize;
 }else{
 break;
 }
 }
  if($maxCharacter2Translate==0 || $count < $maxCharacter2Translate ){
 $contentfile .=$oneLineContent;
 $this->count=$count;
 }else{
 $this->count=$count - $lineSize;
 }
 $contentfile .="</table S1T2A3R4T>"; $this->fileContent=$contentfile;
 return $this->fileContent;
 }
 function getRowContent(){
 return $this->content;
 }
 function generateIniFile(){
 $contentfile='';
 if($this->header)$contentfile .=$this->_generateINIHeader();
$contentfile .=$this->createLanguageString();
$this->_generateFile($contentfile );
 }
 public function generateManualContent($content,$wid,$destlgid=1){
$languageModel=WModel::get('library.languages');
$languageModel->whereE('lgid',$destlgid );
$languageInfo=$languageModel->load('o',array('lgid','code','name'));
 $languageInfo->namekey='translation.'.$languageInfo->code;
 $this->setApplicationName( WExtension::get($wid, 'name'));
 $this->setDestCode($languageInfo->code );
 $contentfile='';
 if($this->header)$contentfile .=$this->_generateINIHeader();
  if( strpos($content, '# TR') !==false){
 $content=htmlspecialchars_decode($content );
 $content=str_replace( "# TR", "TR", $content );
 } 
$contentfile .=$content;
return $contentfile;
 }
 function generateHtmlFile(){
 $maxString=0;
$contentfile=$this->createAutomaticLanguageString($maxString );
return $this->_generateFile($contentfile, 'html');
 }
 private function _getDependancies(&$wids){
static $alreadyGot=array();
$key=serialize($wids);
if(!isset($alreadyGot[$key] )){
$appsDependencyM=WModel::get('install.appsdependency');
for($i=0;$i<10;$i++){
$appsDependencyM->whereIn('wid',$wids);
$appsDependencyM->whereIn('ref_wid',$wids,0,true);
$appsDependencyM->makeLJ('apps','ref_wid','wid');
$appsDependencyM->where('type','!=',1,1);
$appsDependencyM->setDistinct();
$newWids=$appsDependencyM->load('lra',array('ref_wid'));
if(empty($newWids)) break;
$wids=array_merge($wids, $newWids );
}$alreadyGot[$key]=$wids;
}else{
$wids=$alreadyGot[$key];
}
 }
 private function _getDependanciesRecursive(&$wids){
static $alreadyGot=array();
$key=serialize($wids);
if(!isset($alreadyGot[$key] )){
$appsDependencyM=WModel::get('install.appsdependency');
for($i=0;$i<10;$i++){
$appsDependencyM->whereIn('wid',$wids);
$appsDependencyM->whereIn('ref_wid',$wids,0,true);
$appsDependencyM->makeLJ('apps','ref_wid','wid');
$appsDependencyM->where('type','!=',1,1);
$appsDependencyM->setDistinct();
$newWids=$appsDependencyM->load('lra',array('ref_wid'));
if(empty($newWids)) break;
$wids=array_merge($wids, $newWids );
}$alreadyGot[$key]=$wids;
}else{
$wids=$alreadyGot[$key];
}
 }
 function getImacs4OneApplication($wid){
 if(empty($wid)) return array();
 $wids=array($wid );
 $this->_getDependancies($wids );
 return $this->_getImacs($wids );
 }
 private function _getImacs($widsA){
if(empty($widsA)) return array();
$populateModel=WModel::get('translation.populate');
$populateModel->whereIn('wid',$widsA );
$populateModel->select('imac');
$referenceModel=WModel::get('translation.reference');
$referenceModel->whereIn('wid',$widsA );
$referenceModel->select('imac');
return $referenceModel->union('lra',$populateModel );
 }
private function _generateFile($contentfile,$format='ini'){
 if(!empty($this->path)){
 $file_handler=WGet::file();
return $file_handler->write($this->path, $contentfile, 'force');
 }
  @ob_clean();
 header("Pragma: public");
header("Expires: 0"); header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
$fileName="language.".$this->codeDest;
if(!empty($this->wid))$fileName .='_'.WExtension::get($this->wid, 'namekey');
else $fileName .='_joobi';
$fileName .='.'.$format;
header("Content-Disposition: attachment; filename=".$fileName.";");
header("Content-Transfer-Encoding: binary");
header( "Content-Length: ".strlen($contentfile));
echo $contentfile;
exit();
}
 private function _fileHeader(){
 $content="# ####################################".$this->lineJump;
$content .="#  Joobi Application".$this->lineJump;
$content .="# --------------------------------------------------------".$this->lineJump;
$content .="# Published, licensed and distributed by Joobi".$this->lineJump;
$content .="# Copyright (c) 2008-".date('Y')." Joobi".$this->lineJump;
$content .="# All rights reserved.".$this->lineJump;
$content .="# https://joobi.co".$this->lineJump;
$content .="# --------------------------------------------------------".$this->lineJump;
$content .="# The installation and use of this software implies legal".$this->lineJump;
$content .="# acknowledgment and acceptance of licensing terms.".$this->lineJump;
$content .="# ####################################".$this->lineJump;
 return $content;
 }
 private function _generateINIHeader(){
 if(empty($this->fileHeader)){
 $this->fileHeader=$this->_fileHeader();
 } $content=$this->fileHeader;$content .="-- --------------------------------------------------------".$this->lineJump;
if(!empty($this->extensionName))$content .="-- Application: $this->extensionName ".$this->lineJump;
$content .="-- Language: $this->codeDest ".$this->lineJump;
if(!empty($this->codeOrigin))$content .="-- Origin: $this->codeOrigin ".$this->lineJump;
$content .="-- Version: ".$this->version."".$this->lineJump;
$methodUsed=($this->method )?'Automatic' : 'Manual';
$content .="-- Method: $methodUsed ".$this->lineJump;
$content .="-- Collation: UTF-8".$this->lineJump;
$content .="-- --------------------------------------------------------".$this->lineJump;
$content .="#######Note to Translators  ####### ".$this->lineJump;
$content .="# If you wish to translate the language file to your own language, please feel free to do so. ".$this->lineJump;
$content .="# We would appreciate if you could send your translation to translation@joobi.co ".$this->lineJump;
$content .="# so that other people could benefit from your contribution ".$this->lineJump;
$content .="# If you feel that the file is too long feel free to do as much as you want and probably ".$this->lineJump;
$content .="# someone else will be happy to pick up were you stop. ".$this->lineJump;
$content .="# If you update your translation please send us the update as well. ".$this->lineJump;
$content .="#  ".$this->lineJump;
$content .="# IMPORTANT: make sure to respect the punctuation , cases and spacing as much as possible ".$this->lineJump;
$content .="# sometimes you might see HTML tags or {widget: ... } in the definition, please leave it the way it is. ".$this->lineJump;
$content .="# Do NOT translate what is between comments with ## it is there to give you some context of the translation. ".$this->lineJump;
$content .="#  ".$this->lineJump;
$content .="# VERY IMPORTANT: the file needs to be saved in UTF-8 format. ".$this->lineJump;
$content .="#  ".$this->lineJump;
$content .="# Here is an example: ".$this->lineJump;
$content .="# 1206732343MNFC EQUAL SIGN Members ".$this->lineJump;
$content .="# Please do not touch the first string before the equal sign, it will result in translation not working. ".$this->lineJump;
$content .="# The text to be translated is everything after the equal sign.".$this->lineJump ;
$content .="# ".$this->lineJump ;
$content .="#########  The text to translate start here after  #########".$this->lineJump.$this->lineJump;
return $content;
 }
 }