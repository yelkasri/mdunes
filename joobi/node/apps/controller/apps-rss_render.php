<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_rss_render_controller extends WController {
function render(){
$url=WGlobals::get('url');
if(!empty($url))$url=base64_decode($url );
switch ( JOOBI_FRAMEWORK_TYPE){
case 'joomla':
$headerSize='2';
break;
default:
$headerSize='3';
break;
}
$noAvailable=WText::t('1426726331GHCC');
if(empty($url)){
echo $noAvailable;
exit;
}
if(!class_exists('DOMDocument')){
$this->userN('1433250437OEYO');
return false;
}
$xmlDoc=new DOMDocument();
if(!$xmlDoc || ! method_exists($xmlDoc, 'load')) return '';
$xmlDoc->load($url );
if(empty($xmlDoc)){
echo $noAvailable;
exit;
}
$channel=$xmlDoc->getElementsByTagName('channel')->item(0);
if(empty($channel)){
echo $noAvailable;
exit;
}
$channel_title=$channel->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
$channel_link=$channel->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
$channel_desc=$channel->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
$html='';
$x=$xmlDoc->getElementsByTagName('item');
$maxFeed=6;
for($i=0; $i<=$maxFeed; $i++){
$item_title=$x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
$item_link=$x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
$item_desc=$x->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
$html .='<p><a href="'.$item_link.'" target="_blank"><h'.$headerSize.'><span style="background-color:#0C8EC2;font-size: 18px;line-height:25px;" class="badge">'.($i+1 ). '</span> '.$item_title.'</h'.$headerSize.'></a>';
$html .="<br>";
$item_desc=str_replace( array('<img src="','<img alt="" src="'), '<img width="200px;" style="padding:10px;" align="left" src="',$item_desc );
$item_desc=str_replace('<br /><a href=','<div style="clear:both;"></div><a class="btn btn-success" target="_blank" href=',$item_desc );
$item_desc=str_replace('<a href="','<a target="_blank" href="',$item_desc );
$html .=$item_desc . "</p>";
$html .='<div style="clear:both;"></div>';
if($i < $maxFeed)$html .='<hr style="margin-top: 20px;">';
}
echo $html;
exit;
}}