<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Translation_Importlang_class extends WClasses {
  var $_numberMaxValues=200;
 var $_publish=true;
 public $auto=0;
 private $_forceInsert=false;
public function setForceInsert($bool=true){
$this->_forceInsert=$bool;
}
 function setNoPublish(){
 $this->_publish=false;
 }
 function getLanguage(){
 return $this->language;
 }
 public function importDictionary($text,$userInsert=false){ 
 if(!is_string($text)){
 WMessage::log('import data is not a string:','translation.import');
 WMessage::log($text, 'translation.import');
 return false;
 }
 $lines=preg_split( "#\r?\n|\r#", $text );
 $helper=WClass::get('translation.helper');
 $results=$helper->processINI($lines, true);
   foreach($results->metaData as $key=> $val){
 $mykey=strtolower($key);
 $this->$mykey=strtolower($val);
 }
 if(empty($this->language)){
 WMessage::log('Could not find the language tag in the data:','translation.import');
 WMessage::log($results, 'translation.import');
 return false;
 }
 $langsM=WModel::get('library.languages');
 $langsM->whereE('code',$this->language );
 $exists=$langsM->exist();
 if(!$exists){
 WMessage::log('Could not find the language in joobi languages','translation.import');
 WMessage::log($results, 'translation.import');
 $this->message=WText::t('1313753405ECNO'). $this->language;
 return false;
 }
 $translationChecktableC=WClass::get('translation.checktable');
if(!$translationChecktableC->createTransTable($this->language )){
WMessage::log('Could not create the dictionnary table for the language '.$this->language, 'translation.import');
return false;
}
if(empty($results->transData)) return true;
 $namekey='translation.'.trim($this->language );
  $transModel=WModel::get($namekey, 'object', null, false);
 if(empty($transModel)) return true;
   $transModel->whereIn('imac', array_keys($results->transData ));
 $allImacsA=$transModel->load('ol',array('imac','auto'));
  $newIMAC_AutoA=array();
 foreach($allImacsA as $oneImac){
 $newIMAC_AutoA[$oneImac->imac]=$oneImac->auto;
 }
 $valueToForceInsertA=array();
 $valueToNotForceInsertA=array();
 $i=0;
 if(empty($this->auto)){
  if(!empty($this->method) && $this->method=='Automatic'){
 $auto=100;
 }else{
  $auto=2;
 } }else{
  $auto=$this->auto;
 }
  $priority2directedit=true;
$_forceInsert=true;
 foreach($results->transData as $imac=> $text){
 if(!isset($newIMAC_AutoA[$imac] )){
  $_forceInsert=true;
 }else{
 if($newIMAC_AutoA[$imac] > 1){
  if($userInsert)$_forceInsert=true;
 else $_forceInsert=false;
 }else{
 $_forceInsert=true;
 } }
 if(!empty($newIMAC_AutoA[$imac])){
 if(!$userInsert && $newIMAC_AutoA[$imac]==2 ) continue;
 }
  if($i++ > $this->_numberMaxValues){
  $transModel=WModel::get($namekey, 'object', null, false);
 $transModel->setReplace();
 if(!empty($valueToForceInsertA))$transModel->insertMany( array('imac','text','auto'), $valueToForceInsertA );
  $transModel=WModel::get($namekey, 'object', null, false);
 $transModel->setIgnore();
 if(!empty($valueToNotForceInsertA))$transModel->insertMany( array('imac','text','auto'), $valueToNotForceInsertA );
 $valueToForceInsertA=array();
 $valueToNotForceInsertA=array();
 $i=0;
 }
 if($_forceInsert)$valueToForceInsertA[]=array('imac'=> $imac, 'text'=> $text , 'auto'=> $auto );
 else $valueToNotForceInsertA[]=array('imac'=> $imac, 'text'=> $text , 'auto'=> $auto );
 }
   $transModel->setReplace();
 if(!empty($valueToForceInsertA))$transModel->insertMany( array('imac','text','auto'), $valueToForceInsertA );
  $transModel->setIgnore();
 if(!empty($valueToNotForceInsertA))$transModel->insertMany( array('imac','text','auto'), $valueToNotForceInsertA );
 if($this->_publish){
$lang=WModel::get('library.languages');
$lang->setVal('publish', 1 );
$lang->whereE('code',$this->language );
if(!empty($this->_forceInsert))$lang->where('publish','!=', -1 );
$lang->update();
}
    if(empty($this->origin) || ($this->origin==$this->language)){
 WMessage::log('no origin language','translation.import');
 return true;
 }
$namekeyOrigin='translation.'.$this->origin;
   if($this->origin !='en'){
   $myModel=WModel::get('library.model');
 $myModel->whereE('namekey',$namekeyOrigin );
 if(!$myModel->exist()){
  WMessage::log('No model defined for the origin language '.$this->origin, 'translation.import');
return true;
 } }
 $modelOrigin=WModel::get($namekeyOrigin );
 if(empty($modelOrigin)){
 WMessage::log('Unknown model: '.$modelOrigin , 'translation-import-failed'); 
 return true;
 } $tableModelOrigin=$modelOrigin->makeT();
 if(empty($tableModelOrigin)){
 WMessage::log('Unknown table for : '.$modelOrigin , 'translation-import-failed');
 return true;
 }  
 $tableTransModel=$transModel->makeT();
 if(empty($tableTransModel)){
 WMessage::log('Unknown table for : '.$namekey , 'translation-import-failed');
 return true;
 }  
 $query='DELETE '.$tableTransModel.'.* FROM '.$tableTransModel;
 $query .=' LEFT JOIN '.$tableModelOrigin.' ON '.$tableTransModel.'.imac='.$tableModelOrigin.'.imac';
 $query .=' WHERE '.$tableTransModel.'.text='.$tableModelOrigin.'.text';
 $query .=' AND '.$tableTransModel.'.auto < 2;';
 $requete=WTable::get();
 $requete->load('q',$query );
 return true;
 }
 public function insertTranslatedString($string,$imac,$lgid){
 if(empty($lgid)) return false;
$code=WLanguage::get($lgid, 'code');
$translationChecktableC=WClass::get('translation.checktable');
 if(!$translationChecktableC->createTransTable($code)) return false;
$languageM=WModel::get('translation.'.$code );
if(empty($languageM)){
$message=WMessage::get();
$MODEL='translation.'.$code;
$message->userE('1299160210HQTQ',array('$MODEL'=>$MODEL));
return false;
}
if(!is_array($string)){
$string=array($imac=> $string );
}
foreach($string as $oneIMac=> $oneString){
  $languageM->where('auto','>=', 1 ); $languageM->whereE('imac',$oneIMac );
 $found=$languageM->load('lr','imac');
  if(!empty($found)){
WMessage::log($oneIMac.' : '.$oneString  , 'skipped-translation');
 continue;  }else{
 $languageM->setVal('auto', 1 );
 $languageM->setVal('imac',$oneIMac );
$languageM->setVal('text',$oneString );
$languageM->insertIgnore();
$found=false;
 }
}
return true;
 }
public function importFromAutomatic($content2Process,$lgid,$originalStringsA=array()){
$compareImacA=array();
if(!empty($originalStringsA)){
foreach($originalStringsA as $oneImac){
$compareImacA[]=$oneImac->imac;
}}
@ini_set('pcre.backtrack_limit','10000000');
if( preg_match('#<table S1T2A3R4T\b[^>]*>(?:.*?)</table S1T2A3R4T>#s',$content2Process, $match )){ $content=$match[0];
}else{
$message=WMessage::get();
$message->userE('1302662638LHBZ');
Wmessage::log('Import Failed. Wrong format of the import text.','importFromAutomatic-translation');
return true;
}
$code=WLanguage::get($lgid, 'code');
$translationChecktableC=WClass::get('translation.checktable');
 if(!$translationChecktableC->createTransTable($code)) return false;
$languageM=WModel::get('translation.'.$code );
if(empty($languageM)){
$message=WMessage::get();
$MODEL='translation.'.$code;
$message->userE('1299160210HQTQ',array('$MODEL'=>$MODEL));
return false;
}
htmlspecialchars_decode($content);
$content=str_replace('</ span>','</span>',$content);
$content=str_replace('</ ul>','</ul>',$content);
$content=str_replace('</ li>','</li>',$content);
$content=str_replace('</ LI>','</li>',$content);
$content=str_replace('</ UL>','</ul>',$content);
$content=str_replace('</ SPAN>','</span>',$content);
$content=str_replace('ABC000123XYZ','+',$content);
$content=str_replace('XYZ000123ABC','/',$content);
$content=html_entity_decode($content,ENT_QUOTES);
$content=str_replace('123XYZ','$',$content);
$content=str_replace('R1Y2A3N R1Y2A3N','R1Y2A3N',$content );
$content=str_replace( array('=R1Y2A3N','=R1Y2A3N','=R1Y2A3N','==R1Y2A3N','==R1Y2A3N','==R1Y2A3N'), '=R1Y2A3N',$content );
preg_match_all( "/(?s)R1Y2A3N(.+?)R1Y2A3N/", $content, $strings, PREG_PATTERN_ORDER );
foreach($strings[1] as $str){
$content=str_replace($str, base64_decode($str), $content);
}
$content=str_replace('R1Y2A3N','',$content);
 preg_match_all("/(?s)L1I2N3K(.+?)L1I2N3K/", $content, $strings, PREG_PATTERN_ORDER);
 foreach($strings[1] as $str){
$content=str_replace($str,base64_decode($str),$content);
}
$content=str_replace('L1I2N3K','',$content);
$content=str_replace('E1N2D3','</a>',$content);
preg_match_all("/(?s)U1R2L3(.+?)U1R2L3/", $content, $strings, PREG_PATTERN_ORDER);
foreach($strings[1] as $str){
$content=str_replace($str,base64_decode($str),$content);
}
$content=str_replace('U1R2L3','',$content);
preg_match_all("/(?s)I1N1D1(.+?)I1N1D1/", $content, $strings, PREG_PATTERN_ORDER);
foreach($strings[1] as $str){
$content=str_replace($str,base64_decode($str),$content);
}
$content=str_replace('I1N1D1','',$content);
preg_match_all("/(?s)I2N2D2(.+?)I2N2D2/", $content, $strings, PREG_PATTERN_ORDER);
foreach($strings[1] as $str){
$content=str_replace($str,base64_decode($str),$content);
}
$content=str_replace('I2N2D2','',$content);
preg_match_all("/(?s)<tr R1E1V>(.+?)<\/tr R1E1V>/", $content, $matches, PREG_PATTERN_ORDER ); 
if(empty($matches[0])){
WMessage::log($content2Process,  'importlang-error');
$mail=WMail::get();
$subject='The import of languaged failed on the preg_match: ';
$body="Hello translator,\n\r The import of languaged failed on the preg_match ";
$body .="\n\r The text to be translated whcih failed is : \n\r" ;
$body .=$content2Process;
$body .="\n\r\n\r ----End of text.";
$mail->sendTextAdmin($subject, $body );
return false;
}foreach($matches[0] as $result){
preg_match_all("/(?s)<td R1E1V>(.+?)<\/td R1E1V>/", $result, $match, PREG_PATTERN_ORDER);
$imac=trim($match[1][0] );
$notAccepted=preg_replace('/([A-Z0-9]+)/i','',$imac); $imac=preg_replace('/([' .$notAccepted.'+)])/i','',$imac ); 
 if(!empty($compareImacA)){
 if(!in_array($imac, $compareImacA )){
 return false;
 } }
if(!empty($match[1][1])){
$text=trim($match[1][1] );
}else{
return false;
}
  $languageM->where('auto','>=', 1 ); $languageM->whereE('imac',$imac );
 $found=$languageM->load('lr','imac');
 if($found){
 continue;  }else{
$languageM->setVal('auto', 1 );
 $languageM->setVal('imac',$imac );
$languageM->setVal('text',$text );
$languageM->replace();
$found=false;
 }}
return true;
}
}