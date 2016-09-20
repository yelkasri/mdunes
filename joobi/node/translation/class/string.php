<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Translation_String_class extends WClasses {
private $_maxLenght=1800;
private $_BR_replacement='%br%';
private $_singleTagsA=array(
0=> '$',
1=> '&amp',
2=> '</a>', 
3=> '{tag:actor}',
4=> '{multiple}',
5=> '{/multiple}',
6=> '{single}',
7=> '{/single}',
8=> '{count}'
);
private $_memoryA=array();
private $_counter=0;
private $_transAddon=null;
private $_lgFrom=0;
private $_lgTo=0;
private $_engineO=null;
private $_showMessage=false;
private $_isHTML=false;
private $_validTranslator=false;
public function encodeTranslation($content){
$content=$this->_encodePregMatch($content, "/(?s){widget(.+?)\}/" );
$content=$this->_encodePregMatch($content, "/(?s)<a (.+?)\>/" );
$content=$this->_encodePregMatch($content, "/(?s)<img (.+?)\>/" );
$content=$this->_encodePregMatch($content, "/(?s)<input (.+?)\>/" );
$content=$this->_encodePregMatch($content, "/(?s)http(.+?)\ /" );
$content=$this->_encodePregMatch($content, "/(?s)index.php(.+?)\ /" );
$content=$this->_encodePregMatch($content, "/(?s){tag(.+?)\}/" );
$content=$this->_replaceSingelString($content );
return $content;
}
public function decodeTranslation($string){
$content=htmlspecialchars_decode($string);
$content=html_entity_decode($content, ENT_QUOTES );
foreach($this->_memoryA as $key=> $val){
$key=trim($key);
$content=str_replace($key, $val, $content );
}
$content=str_replace('</ span>','</span>',$content );
$content=str_replace('</ ul>','</ul>',$content );
$content=str_replace('</ li>','</li>',$content );
$content=str_replace('</ LI>','</li>',$content );
$content=str_replace('</ UL>','</ul>',$content );
$content=str_replace('</ SPAN>','</span>',$content );
return $content;
}
public function setTranslationInformation($transAddon,$lgFrom,$lgTo,$engineO){
$this->_transAddon=$transAddon;
$this->_lgFrom=$lgFrom;
$this->_lgTo=$lgTo;
$this->_engineO=$engineO;
if(empty($this->_transAddon) || empty($this->_lgFrom) || empty($this->_lgTo) || empty($this->_engineO)){
$this->_validTranslator=false;
return false;
}
$this->_maxLenght=$this->_transAddon->getMaxString();
$this->_validTranslator=true;
return true;
}
public function convertHTML($html,$maxLenght=1000,$showMessage=false){
if(empty($html) || is_numeric($html)) return $html;
if(empty($this->_transAddon) || empty($this->_lgFrom) || empty($this->_lgTo) || empty($this->_engineO)){
return $html;
}
$this->_showMessage=$showMessage;
if($maxLenght < $this->_maxLenght)$this->_maxLenght=$maxLenght;
$stringLengthAgain=strlen($html );
if($stringLengthAgain <=$this->_maxLenght){
$translatedText=$this->_transAddon->translateText($html, $this->_lgFrom, $this->_lgTo, $this->_engineO );
return $translatedText;
}
$subHTML=$this->_getSubHTML($html );
$data=$this->_parseHTML($subHTML );
if(is_array($data)){
$data=$this->_reintegrateTags($data, $this->_maxLenght );
$translatedHTML='';
foreach($data as $val)
{
switch($val['type'])
{
case 'open':
if($val['tag'] !=='INFO')
{
$translatedHTML .='<'.strtolower($val['tag']);
if(array_key_exists('attributes',$val))
{
foreach($val['attributes'] as $key=> $att)
{
$translatedHTML .=' '.strtolower($key). '="'.$att.'"';
}
}
$translatedHTML .='>';
}
if( array_key_exists('value',$val)){
if(isset($val['value']))
$translatedHTML .=$this->_translate($val['value'] );
}
break;
case 'close':
if($val['tag'] !=='INFO')
{
$translatedHTML .='</'.strtolower($val['tag']). '>';
}
break;
case 'cdata':
if(isset($val['value']))
$translatedHTML .=$this->_translate($val['value']);
break;
case 'complete':
if($val['tag'] !=='INFO')
{
$translatedHTML .='<'.strtolower($val['tag']);
if(array_key_exists('attributes',$val))
{
foreach($val['attributes'] as $key=> $att)
{
$translatedHTML .=' '.strtolower($key). '="'.$att.'"';
}
}
$translatedHTML .='>';
}
if(array_key_exists('value',$val))
{
if(isset($val['value']))
$translatedHTML .=$this->_translate($val['value']);
}
if($val['tag'] !=='INFO')
{
$translatedHTML .='</'.strtolower($val['tag']). '>';
}
}
}
}else{
$data=str_replace(array('<info>','</info>'), '',$data);
$translatedHTML=$this->_translate($data);
}
return $translatedHTML;
}
private function _externalTranslation($val){
if(empty($val) || strlen($val) < 2 || is_numeric($val)) return $val;
if(!$this->_validTranslator){
return $val;
}
$val=html_entity_decode($val, ENT_QUOTES );
return $this->_transAddon->translateText($val, $this->_lgFrom, $this->_lgTo, $this->_engineO );
}
private function _replaceSingelString($content){
$newContent=$content;
foreach($this->_singleTagsA as $match){
$pos=strpos($newContent, $match );
$len=strlen($match );
$first=(( substr($newContent, $pos-1, 1 ) !=' ')?true : false);
$last=(( substr($newContent, $pos+$len, 1 ) !=' ')?true : false);
$encoded=$this->_addMemory($match, $first, $last );
$newContent=str_replace($match, $encoded, $newContent );
}
return $newContent;
} 
private function _encodePregMatch($content,$pregMatch){
preg_match_all($pregMatch, $content, $matches, PREG_PATTERN_ORDER );
$newContent=$content;
foreach($matches[0] as $match){
$pos=strpos($newContent, $match );
$len=strlen($match );
$first=(( substr($newContent, $pos-1, 1 ) !=' ')?true : false);
$last=(( substr($newContent, $pos+$len, 1 ) !=' ')?true : false);
$encoded=$this->_addMemory($match, $first, $last );
$newContent=str_replace($match, $encoded, $newContent );
}
return $newContent;
} 
private function _addMemory($string,$first=false,$last=false){
if($first)$startTag=' ABCM';
else $startTag='ABCM';
if($last)$endTag='XYZ ';
else $endTag='XYZ';
$this->_counter++;
$key=$startTag . $this->_counter . $endTag;
$this->_memoryA[$key]=$string;
return $key;
}
private function _getSubHTML($html){
$posOpen=stripos($html, '<body>');
$posClosed=stripos($html, '</body>');
$returnHTML=$html;
if($posOpen !==FALSE)
{
if($posClosed !==FALSE)
{
$returnHTML=substr($html, $posOpen, ($posClosed + strlen('</body>')) - $posOpen );
}
}
return $returnHTML;
}
private function _parseHTML($html){
$html=preg_replace('/<\s*br\s*\/?\s*>/i',$this->_BR_replacement, $html );
$html=preg_replace('/&([a-zA-Z]{2,6};|#[0-9]{3};)/','{$1}',$html);
$html='<info>'. $html.'</info>';
$parser=xml_parser_create();
$vals=null;
if( xml_parse_into_struct($parser, $html, $vals )===1){
$this->_isHTML=( strpos($html, '</' !==false)?true : false);
xml_parser_free($parser );
return $vals;
}else{
if($this->_showMessage)$this->userW('1403539630BAFA');
xml_parser_free($parser );
return $html;
}
}
private function _reintegrateTags($arr,$len)
{
$topLevel=1;
$elem='';
$previousLevel='';
foreach($arr as $val)
{
$topLevel=$topLevel < $val['level']?$val['level'] : $topLevel;
}
for($i=$topLevel; $i > 1; $i--)
{
foreach($arr as $key=> $val)
{
$previousLevel=$val['level']===$i - 1?$key : $previousLevel;
if($val['level'] >=$i)
{
switch($val['type'])
{
case 'open':
$canCombine=array_key_exists($key-1, $arr)?$arr[$key-1]['level'] < $i?TRUE : FALSE : TRUE;
$elem .='<'.strtolower($val['tag']);
if(array_key_exists('attributes',$val))
{
foreach($val['attributes'] as $attKey=> $att)
{
$elem .=' '.strtolower($attKey). '="'.$att.'"';
}
}
$elem .='>';
$elem .=array_key_exists('value',$val)?$val['value'] : '';
break;
case 'cdata':
$elem .=$val['value'];
break;
case 'complete':
$canCombine=array_key_exists($key-1, $arr)?$arr[$key-1]['level'] < $i?TRUE : FALSE : TRUE;
$elem .='<'.strtolower($val['tag']);
if(array_key_exists('attributes',$val))
{
foreach($val['attributes'] as $attKey=> $att)
{
$elem .=' '.strtolower($attKey). '="'.$att.'"';
}
}
$elem .='>';
$elem .=array_key_exists('value',$val)?$val['value'] : '';
case 'close':
$elem .='</'.strtolower($val['tag']). '>';
if($canCombine)
{
if(array_key_exists('value',$arr[$previousLevel]))
{
if((strlen($arr[$previousLevel]['value']) + strlen($elem)) <=$len)
{
$arr[$previousLevel]['value'] .=$elem;
for($j=$previousLevel + 1; $j <=$key; $j++)
{
unset($arr[$j]);
}
}
}
else
{
if(strlen($elem) <=$len)
{
$arr[$previousLevel]['value']=$elem;
for($j=$previousLevel + 1; $j <=$key; $j++)
{
unset($arr[$j]);
}
}
}
}
$elem='';
}
}
}
$arr=array_values($arr);
}
return $arr;
}
private function _translate($text){
if( "\n"==$text){
return $text;
}
$text=preg_replace('/'.$this->_BR_replacement.'/','<br />',$text );
$text=preg_replace('/\{([a-zA-Z]{2,6};|#[0-9]{3};)\}/','&$1',$text );
$len=strlen($text );
if($len <=$this->_maxLenght){
$reult=$this->_externalTranslation($text );
return $reult;
}
if($this->_isHTML && $this->_showMessage)$this->userN('1403539630BAFB');
$subTextA=array();
$subSeperatorA=array();
do {
$lstPun=$this->_lastPunctuation($text );
if( substr($text, $lstPun ) >=$this->_maxLenght){
$wordwrap=wordwrap($text, $this->_maxLenght-1, '|H|');
$wordwrapA=explode('|H|',$wordwrap );
foreach($wordwrapA as $oneLine){
$subTextA[]=$oneLine;
$subSeperatorA[]='W';
}
$text=substr($text, $lstPun + 1 );
$lenRemaining=strlen($text );
if($this->_showMessage)$this->userN('1403539630BAFC');
}else{
$nextChar=substr($text, $lstPun+1, 1 );
if(false===$nextChar){
$subTextA[]=substr($text, 0, $lstPun );
$subSeperatorA[]='<';
$text=substr($text, $lstPun + 1 );
$lenRemaining=strlen($text );
}elseif($nextChar==' '){
$subTextA[]=substr($text, 0, $lstPun + 1 );
$subSeperatorA[]='_';
$text=substr($text, $lstPun + 2 );
$lenRemaining=strlen($text );
}elseif($nextChar=='\\'){
$subTextA[]=substr($text, 0, $lstPun+1 );
$subSeperatorA[]='<';
$text=substr($text, $lstPun + 1 );
$lenRemaining=strlen($text );
}elseif($nextChar=='<'){
$subTextA[]=substr($text, 0, $lstPun+1 );
$subSeperatorA[]='<';
$text=substr($text, $lstPun + 1 );
$lenRemaining=strlen($text );
}else{
$subTextA[]=substr($text, 0, $lstPun );
$subSeperatorA[]='<';
$text=substr($text, $lstPun + 1 );
$lenRemaining=strlen($text );
}
}
} while($lenRemaining > 0 );
$retunedString='';
foreach($subTextA as $key=> $val){
$retunedString .=$this->_externalTranslation($val );
if('_'==$subSeperatorA[$key])$retunedString .=' ';
}
return $retunedString;
}
private function _lastPunctuation($text){
$len=strlen($text );
if($len > $this->_maxLenght){
preg_match_all('/\.|!|\?/ui',$text, $matches, PREG_OFFSET_CAPTURE );
foreach($matches[0] as $key=> $val){
if($val[1] >=$this->_maxLenght ) return ($key==0?$matches[0][$key][1] : $matches[0][$key-1][1] );
}
}
return $len;
}
}
