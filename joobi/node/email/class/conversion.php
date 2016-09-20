<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Email_Conversion_class extends WClasses {
public function smartHTMLSize($html,$charLimit=null,$onlyText=false,$keepLinks=true,$makeReturnLine=true,$removeAllReturnLine=false){
if(empty($charLimit)){
return $onlyText?$this->HTMLtoText($html, false, $makeReturnLine, $removeAllReturnLine ) : $html;
}else{
if( strlen($html) > $charLimit){
if($onlyText){
$newString=$this->HTMLtoText($html, $keepLinks, $makeReturnLine, $removeAllReturnLine );
$html=$this->cleanShortenHTML($newString, $charLimit, '...', false, false);
}else{
$html=$this->cleanShortenHTML($html, $charLimit );
}
}else{
$html=($onlyText?$this->HTMLtoText($html, false, $makeReturnLine, $removeAllReturnLine ) : $html );
}
}
return $html;
}
public function HTMLtoText($html,$keepLinks=true,$makeReturnLine=false,$removeAllReturnLine=false){
if($removeAllReturnLine)$html=str_replace( array( "\n", "\r", "\t" ), '',$html );
if($makeReturnLine){
$html=str_replace( array('<br>','<br />','<br/>',  '<BR>','<BR />','<BR/>'), "\n\r<br />", $html );
$html=str_replace( array('<p','<P'), "\n\r<p", $html );
$html=str_replace( array('<li','<LI'), "\n\r<li", $html );
}
$removeImgLinks="#< *a[^>]*> *< *img[^>]*> *< *\/ *a *>#isU";
$removeJS="#< *script(?:(?!< */ *script *>).)*< */ *script *>#isU";
$removeCSS="#< *style(?:(?!< */ *style *>).)*< */ *style *>#isU";
$removeTags='#< *strike(?:(?!< */ *strike *>).)*< */ *strike *>#iU';
$replaceHTags='#< *(h1|h2)[^>]*>#Ui';
$replaceBullets='#< *li[^>]*>#Ui';
$replaceTag1='#< */ *(li|td|tr|div|p)[^>]*> *< *(li|td|tr|div|p)[^>]*>#Ui';
$replaceTag2='#< */? *(br|p|h1|h2|legend|h3|li|ul|h4|h5|h6|tr|td|div)[^>]*>#Ui';
$replaceLinks='/< *a[^>]*href *=*"([^#][^"]*)"[^>]*>(.*)< *\/ *a *>/Uis';
$linkConversion=($keepLinks )?'${2} (${1} )' : '${2}';
$text=preg_replace(
array($removeImgLinks, $removeJS, $removeCSS, $removeTags, $replaceHTags, $replaceBullets, $replaceTag1, $replaceTag2, $replaceLinks),
array('','','','',"\n\n","\n* ","\n","\n", $linkConversion ), $html );
$text=strip_tags($text );
$text=str_replace( array(" ","&nbsp;"), ' ',$text );
$text=@html_entity_decode($text, ENT_QUOTES, 'UTF-8');
$text=trim($text);
if($makeReturnLine){
$text=nl2br($text );
}elseif($removeAllReturnLine){
$text=str_replace( array( "\n\r", "\n", "\r", "\r\n", '<br />','<br/>','<br>'), ' ',$text );
}
return $text;
}
function cleanShortenHTML ($text,$length=100,$ending='...',$exact=false,$considerHtml=true){
if(empty($text) || empty($length)) return $text;
if( strlen($text) < $length ) return $text;
$text=str_replace( array('<br>','<br />','<br/>',  '<BR>','<BR />','<BR/>'), "HHH", $text );
if( strpos($text, 'HHH') !==false)$exact=false;
if($considerHtml){
if(strlen(preg_replace('/<.*?>/','',$text)) <=$length){
return $text;
}
preg_match_all('/(<.+?>)?([^<>]*)/s',$text, $lines, PREG_SET_ORDER);
$total_length=strlen($ending);
$open_tags=array();
$truncate='';
foreach($lines as $line_matchings){
if(!empty($line_matchings[1])){
if(preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is',$line_matchings[1])){
}elseif(preg_match('/^<\s*\/([^\s]+?)\s*>$/s',$line_matchings[1], $tag_matchings)){
$pos=array_search($tag_matchings[1], $open_tags);
if($pos !==false){
unset($open_tags[$pos]);
}
}elseif(preg_match('/^<\s*([^\s>!]+).*?>$/s',$line_matchings[1], $tag_matchings)){
array_unshift($open_tags, strtolower($tag_matchings[1]));
}
$truncate .=$line_matchings[1];
}
$content_length=strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i',' ',$line_matchings[2]));
if($total_length+$content_length> $length){
$left=$length - $total_length;
$entities_length=0;
if(preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i',$line_matchings[2], $entities, PREG_OFFSET_CAPTURE)){
foreach($entities[0] as $entity){
if($entity[1]+1-$entities_length <=$left){
$left--;
$entities_length +=strlen($entity[0]);
}else{
break;
}
}
}
$truncate .=substr($line_matchings[2], 0, $left+$entities_length);
break;
}else{
$truncate .=$line_matchings[2];
$total_length +=$content_length;
}
if($total_length>=$length){
break;
}
}
}else{
if( strlen($text) <=$length){
return $text;
}else{
$truncate=substr($text, 0, $length - strlen($ending));
}
}
if(!$exact){
$spacepos=strrpos($truncate, ' ');
if(isset($spacepos)){
$truncate=substr($truncate, 0, $spacepos );
}
}
$truncate .=$ending;
if($considerHtml){
foreach($open_tags as $tag){
$truncate .='</'.$tag.'>';
}
}
$truncate=str_replace( "HHH", '<br />',$truncate );
return $truncate;
}
}
