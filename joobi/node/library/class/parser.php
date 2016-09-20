<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Parser_class extends WClasses {
var $arrOutput=array();
var $resParser;
var $strXmlData;
var $firstChild=false;
var $count=-1;
var $tag_lower_case=false;
function __construct($tfile=''){
if(trim($tfile) !='')$this->loadFile($tfile);
}
function loadArray($array){
$this->arrOuput=$array;
}
public function loadFile($tfile){
$this->thefile=$tfile;
$file=WGet::file();
return $this->parse($file->read($tfile));
}
public function parse($strInputXML,$firstChild=false){
if(!is_string($strInputXML)) return false;
$this->firstChild=$firstChild;
$this->resParser=xml_parser_create();
xml_set_object($this->resParser, $this );
xml_set_element_handler($this->resParser, 'tagOpen','tagClosed');
xml_parser_set_option($this->resParser, XML_OPTION_CASE_FOLDING, 0 );
 xml_set_character_data_handler($this->resParser, 'tagData');
$this->strXmlData=xml_parse($this->resParser, $strInputXML );
if(!$this->strXmlData){
$ERROR=xml_error_string( xml_get_error_code($this->resParser ));
$LINE=xml_get_current_line_number($this->resParser );
$this->userN('1213020890DAKY',array('$ERROR'=>$ERROR,'$LINE'=>$LINE));
return false;
}
xml_parser_free($this->resParser );
return $this->arrOutput;
}
function tagOpen($parser,$name,$attrs){
$tag=array('nodename'=>$name, 'attributes'=>$attrs );
array_push($this->arrOutput, $tag );
}
function tagData($parser,$tagData){
if( trim($tagData) !=''){
if(isset($this->arrOutput[count($this->arrOutput)-1]['nodevalue'])){
$this->arrOutput[count($this->arrOutput)-1]['nodevalue'] .=$this->_parseXMLValue($tagData);
}else{
$this->arrOutput[count($this->arrOutput)-1]['nodevalue']=$this->_parseXMLValue($tagData);
}
}
}
function tagClosed($parser,$name){
$this->arrOutput[count($this->arrOutput)-2]['children'][]=$this->arrOutput[count($this->arrOutput)-1];
if($this->firstChild && count ($this->arrOutput[count($this->arrOutput)-2]['children'] )==1){
$this->arrOutput[count($this->arrOutput)-2]['firstchild']=&$this->arrOutput[count($this->arrOutput)-2]['children'][0];
}
array_pop($this->arrOutput);
}
private function _parseXMLValue($tvalue){
$tvalue=html_entity_decode($tvalue );
return $tvalue;
}
function toArray($xml='')
{
if(count($this->arrOuput)==0)
{
if($xml !='')
{
$this->parse($xml);
}
}
return $this->arrOuput;
}
private function _toXML($tob=null,$nr=true){
$result='';
if($tob==null)$tob=$this->arrOutput;
if(!isset($tob)){
$message=WMessage::get();
$message->codeN('No data available for XML creation.');
return null;
}
$this->count++;
$nb_tob=count($tob);
$c=0;
foreach($tob as $key=> $t){
$tab=$this->tab();
$nodename=($this->tag_lower_case?strtolower($t['nodename']) : $t['nodename']);
if($c!=0)
if($nr)$result .="\r\n";
$result .=$tab.'<'.$nodename;
if(isset($t['attributes']) && is_array($t['attributes'])){
foreach($t['attributes'] as $key=> $value){
if('children'==$key){
$result .='> '.$this->_toXML($value );
$result .='</'.$nodename.'> ';
}else{
$result .=' '.($this->tag_lower_case?strtolower($key) : $key ). '="'.$this->_parseXMLValue($value). '"';
}
}}
if(isset($t['nodevalue'])){
$result .='>';
$val=$t['nodevalue'];
$found=false;
if(is_string($val) && (strpos($val, '<') !==false  || strpos($val, '&') !==false))
{
$found=true;
}
if($found)
{
$result .='<![CDATA['.$val.']]>';
}else{
$result .=$val;
}
$result .='</'.$nodename . ">";
}elseif(isset($t['children']) && is_array($t['children']) && count($t['children']) > 0){
$result .='>';
$calc=$this->_toXML($t['children'] );
if($nr)$result .="\r\n";
$result .=$calc .$tab;
$result .='</'.$nodename . ">";
}else{
$result .='/>';
}
$c++;
}
$result .="\r\n";
$this->count--;
return $result;
}
function lowerCaseTagNames($bool=true){
$this->tag_lower_case=$bool;
}
function tab($tab=' '){
return str_repeat($tab,$this->count);
}
public function getXML($tob=null,$version='1.0',$encoding='UTF-8',$doctype=''){
return '<?xml version="'. $version.'" encoding="'.$encoding.'"?>'."\r\n" . ($doctype!=''?$doctype . "\r\n" : ''). $this->_toXML($tob);
}
function unichr($dec){
if($dec < 128){
$utf=chr($dec);
}elseif($dec < 2048){
$utf=chr(192 + (($dec - ($dec % 64)) / 64));
$utf .=chr(128 + ($dec % 64));
}else {
$utf=chr(224 + (($dec - ($dec % 4096)) / 4096));
$utf .=chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
$utf .=chr(128 + ($dec % 64));
}
return $utf;
}
}