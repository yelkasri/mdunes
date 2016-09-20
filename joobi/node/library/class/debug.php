<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Library_debug_class {
var $xmlDepth=array();
var $xmlCData;
var $xmlSData;
var $xmlDData;
var $xmlCount=0;
var $xmlAttrib;
var $xmlName;
var $title;
var $arrType=array("array","object","resource","boolean");
var $_html='';
var $speed=null;
var $speedQuery='';
var $backTrace=false;
private $_HTMLLook=true;
private $_lineReturn="\r\n";
function initializeDebug($var,$forceType="",$title='',$nb='',$time=null,$exactTime=null,$notrace=false,$backTrace=false,$textOnlyTrace=false){
static $perfValue=null;
$this->_HTMLLook=!$textOnlyTrace;
if(!WTools::checkMemory()) return '';
if(isset($time )){
$perfValueDelta=$time;
}else{
if(!isset($perfValue) && defined('JOOBI_PERF_START_TIME')){
$perfValueDelta=round((microtime(true) - JOOBI_PERF_START_TIME ) * 1000 , 3 );
}else{
$perfValueDelta=round((microtime(true) - $perfValue ) * 1000, 3);
}
$perfValue=microtime(true);
}
if($notrace ) return ;
$arrAccept=array("array","object","xml", "resource", "boolean", "query", "backtrace"); 
$this->title=$title;
$this->nombre=$nb;
$this->backTrace=$backTrace;
if(in_array($forceType,$arrAccept)){
$this->_html=$this->{"varIs".ucfirst($forceType)}($var);
 }else{
$this->_html=$this->checkType($var );
 }
return $this->_html;
}
function time(){
return $this->speed;
}
public function display(){
return $this->_html;
}
function makeTableHeader($type,$header,$colspan=2){
if($this->_HTMLLook){
return "<table cellspacing=\"2\" cellpadding=\"3\" class=\"dBug_".$type."\"><tr><td class=\"dBug_".$type."Header\" colspan=\"".$colspan."\" style=\"cursor:hand\" onClick='dBug_toggleTable(this)'>".$header."</td></tr>";
}else{
$text=(!empty($colspan))?'=>' : '';
$text .="--" . $type.'--';
$text .=$this->_lineReturn . $this->_addIndent($colspan ). "{";
$text .=$this->_lineReturn;
return $text;
}
}
private function _makeTDHeader($type,$header,$indent=0){
if($this->_HTMLLook){
return "<tr><td valign=\"top\" onClick='dBug_toggleRow(this)' style=\"cursor:hand\" class=\"dBug_".$type."Key\">".$header."</td><td>";
}else{
if(!empty($header)) return $this->_addIndent($indent ). $header;
}
}
private function _closeTDRow(){
if($this->_HTMLLook){
return "</td></tr>";
}
}
function error($type){
$error="Error: Variable is not a";
if(in_array(substr($type,0,1),array("a","e","i","o","u","x")))
$error.="n";
return ($error." ".$type." type");
}
function checkType($var,$indent=0){
static $endOfTrace=false;
if(!WTools::checkMemory()){
if(!$endOfTrace){
$endOfTrace=true;
return 'Out of Memory - Last trace!';
}
return '';
}
switch(gettype($var)){
case "resource":
$html=$this->varIsResource($var, $indent );
break;
case "object":
$html=$this->varIsObject($var, $indent );
break;
case "array":
$html=$this->varIsArray($var, $indent );
break;
case "boolean":
$html=$this->_varIsBoolean($var);
break;
case "string":
case "integer":
case "real":
case "float":
$html=$this->varIsString($var);
break;
default:
$var=($var=="")?"[empty string]" : $var;
$html="<table cellspacing=0><tr>\n<td>".$var."</td>\n</tr>\n</table>\n";
break;
}
return $html;
}
private function _varIsBoolean($var){
if($this->_HTMLLook){
$html=($var )?'<b><span style="color: rgb(0, 153, 0);">True</span></b><br>' : '<b><span style="color: rgb(255, 0, 0);">False</span></b><br>';
}else{
$html='=>';
$html .=($var )?'True' : 'False';
$html .=$this->_lineReturn;
}
return $html;
}
function varIsString($var){
$html='';
$var=htmlentities($var, null, JOOBI_CHARSET );
if($this->_HTMLLook){
$var='<span style="color: rgb(51, 51, 255);"><b>String: '.((!empty($this->nombre))?$this->nombre : '').' | <u>'.$this->title.'</u> :</b>  '.$var.'</span><br>';
if(isset($this->speed))$var.='<br><span style="color: rgb(51, 102, 255);">'.$this->speed.'</span>'; ;
$html .=$var.'<br />';
}else{
$var='String: '.((!empty($this->nombre))?$this->nombre : '').' '.$this->title.' : '.$var;
$html .=$var . $this->_lineReturn;
}
return $html;
}
function varIsQuery($var){
$html='';
$title='Query:'.(!empty($this->nombre))?$this->nombre : '';
$title .=': '.$this->title;
if(isset($this->speed))$title.=' <span style="color: rgb(51, 102, 255);"> '.$this->speed.'</span>'; ;
if(isset($this->speedQuery))$title.=' <span style="color: rgb(0, 255, 81);"> '.$this->speedQuery.'</span>'; ;
$html .=$this->makeTableHeader("resource", $title, 1);
if($this->_HTMLLook){
$html .="<td>" . $var."</td>\n</tr>\n";
$html .="</table>";
}else{
$html .=$var . $this->_lineReturn;
}
return $html;
}
function varIsArray($var,$indent=0){
$html='';
$nombre=$this->nombre;
$title='array:'.((!empty($nombre))?$nombre : '');
$title .=(!empty($this->title))? ': '.$this->title : '' ;
if(isset($this->speed))$title.=' <span style="color: rgb(51, 102, 255);"> '.$this->speed.'</span>';
$indent++;
if($this->_HTMLLook)$indent=2;
$html .=$this->makeTableHeader("array", $title, $indent );
if(is_array($var)){
foreach($var as $key=> $value){
if($this->backTrace && in_array($key, array('object','type','args'))){
continue;
}
if($this->_HTMLLook){
$html .=$this->_makeTDHeader( "array", $key );
}else{
$html .=$this->_addIndent($indent ). '['.$this->_makeTDHeader( "array", $key, 0 ).']';
}
if( in_array(gettype($value), $this->arrType ))$html .=$this->checkType($value, $indent );
else {
$value=(trim($value)=="")?"[empty string]" : $value;
if($this->_HTMLLook){
$html .=$value."</td>\n</tr>\n";
}else{
$html .='=>'.$value.  $this->_lineReturn;
}
}
}
}else{
if($this->_HTMLLook){
$html .="<tr><td>".$this->error("array").$this->_closeTDRow();
}else{
$html .=$this->error("array");
}
}
if($this->_HTMLLook){
$html .="</table>";
}else{
$html .=$this->_addIndent($indent ). "}" . $this->_lineReturn;
}
$this->nombre++;
return $html;
}
function varIsObject($var,$indent=0){
$html='';
$nombre=$this->nombre;
$title='object: '.((!empty($nombre))?$nombre : get_class($var));
$title .=(!empty($this->title))? ': '.$this->title : '' ;
if(isset($this->speed))$title.=' <span style="color: rgb(51, 102, 255);"> '.$this->speed.'</span>'; ;
$indent++;
if($this->_HTMLLook)$indent=2;
$html .=$this->makeTableHeader("object", $title, $indent);
$arrObjVars=get_object_vars($var);
$parentClass=get_parent_class($var );
if( in_array($parentClass, array('WModel','WCategories'))){
$haveModel=true;
}else{
$haveModel=false;
}
$haveModel=false;
if( is_object($var)){
foreach($arrObjVars as $key=> $value){
if($haveModel && substr($key, 0, 1)=='_') continue;
$value=( is_string($value) && trim($value)=="")?"[empty string]" : $value;
if($this->_HTMLLook){
$html .=$this->_makeTDHeader( "object", $key );
}else{
$html .=$this->_addIndent($indent ). '('.$this->_makeTDHeader( "object", $key, 0 ).')';
}
if( in_array( gettype($value), $this->arrType )){
$html .=$this->checkType($value, $indent );
}else{
if($this->_HTMLLook){
$html .=$value . $this->_closeTDRow();
}else{
$html .='=>'.$value.  $this->_lineReturn;
}
}
}
}else{
$html .="<tr><td>".$this->error("object").$this->_closeTDRow();
}
if($this->_HTMLLook){
$html .="</table>";
}else{
$html .=$this->_addIndent($indent ). "}" . $this->_lineReturn;
}
$this->nombre++;
return $html;
}
function varIsBacktrace($var){
$html=$this->makeTableHeader("resource", 'Backtrace', 5 );
$type="resource";
$html="<table cellspacing=2 cellpadding=3 class=\"dBug_".$type."\">
<tr>
<td class=\"dBug_".$type."Header\" style=\"cursor:hand\" onClick='dBug_toggleTable(this)'>Class</td>
<td class=\"dBug_".$type."Header\" style=\"cursor:hand\" onClick='dBug_toggleTable(this)'>Function</td>
<td class=\"dBug_".$type."Header\" style=\"cursor:hand\" onClick='dBug_toggleTable(this)'>Arguments</td>
<td class=\"dBug_".$type."Header\" style=\"cursor:hand\" onClick='dBug_toggleTable(this)'>File</td>
<td class=\"dBug_".$type."Header\" style=\"cursor:hand\" onClick='dBug_toggleTable(this)'>Line</td>
</tr>";
$html .="</table>";
return $html;
}
function varIsResource($var,$indent=0){
$html='';
$html .=$this->makeTableHeader( "resourceC", "resource", 1 );
$html .="<tr>\n<td>\n";
switch( get_resource_type($var)){
case "fbsql result":
case "mssql result":
case "msql query":
case "pgsql result":
case "sybase-db result":
case "sybase-ct result":
case "mysql result":
case "mysqli result":
$db=current( explode( " ", get_resource_type($var)) );
$html .=$this->varIsDBResource($var, $db );
break;
case "gd":
$html .=$this->_varIsGDResource($var);
break;
case "xml":
$html .=$this->_varIsXmlResource($var);
break;
default:
$html .=get_resource_type($var). $this->_closeTDRow();
break;
}
$html .=$this->_closeTDRow(). "</table>\n";
return $html;
}
function varIsXml($var){
$this->_varIsXmlResource($var);
}
private function _varIsXmlResource($var){
$xml_parser=xml_parser_create();
xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
xml_set_element_handler($xml_parser,array(&$this,"xmlStartElement"),array(&$this,"xmlEndElement"));
xml_set_character_data_handler($xml_parser,array(&$this,"xmlCharacterData"));
xml_set_default_handler($xml_parser,array(&$this,"xmlDefaultHandler"));
$html .=$this->makeTableHeader("xml","xml document",2);
$html .=$this->_makeTDHeader("xml","xmlRoot");
$bFile=(!($fp=@fopen($var,"r")))?false : true;
if($bFile){
while($data=str_replace("\n","",fread($fp,4096)))
$this->xmlParse($xml_parser,$data,feof($fp));
}
else {
if(!is_string($var)){
$html .=$this->error("xml").$this->_closeTDRow()."</table>\n";
return;
}
$data=$var;
$this->xmlParse($xml_parser,$data,1);
}
$html .=$this->_closeTDRow()."</table>\n";
return $html;
}
function xmlParse($xml_parser,$data,$bFinal){
if(!xml_parse($xml_parser,$data,$bFinal)){
die(sprintf("XML error: %s at line %d\n",
xml_error_string(xml_get_error_code($xml_parser)),
xml_get_current_line_number($xml_parser)));
}
}
function xmlStartElement($parser,$name,$attribs){
$html='';
$this->xmlAttrib[$this->xmlCount]=$attribs;
$this->xmlName[$this->xmlCount]=$name;
$this->xmlSData[$this->xmlCount]='$html .=$this->makeTableHeader("xml","xml element",2);';
$this->xmlSData[$this->xmlCount].='$html .=$this->_makeTDHeader("xml","xmlName");';
$this->xmlSData[$this->xmlCount].='$html .="<strong>'.$this->xmlName[$this->xmlCount].'</strong>".$this->_closeTDRow();';
$this->xmlSData[$this->xmlCount].='$html .=$this->_makeTDHeader("xml","xmlAttributes");';
if(count($attribs)>0)
$this->xmlSData[$this->xmlCount].='$this->varIsArray($this->xmlAttrib['.$this->xmlCount.']);';
else
$this->xmlSData[$this->xmlCount].='$html .="&nbsp;";';
$this->xmlSData[$this->xmlCount].='$html .=$this->_closeTDRow();';
$this->xmlCount++;
return $html;
}
function xmlEndElement($parser,$name){
for($i=0;$i<$this->xmlCount;$i++){
$html .=$this->_makeTDHeader("xml","xmlText");
$html .=(!empty($this->xmlCData[$i]))?$this->xmlCData[$i] : "&nbsp;";
$html .=$this->_closeTDRow();
$html .=$this->_makeTDHeader("xml","xmlComment");
$html .=(!empty($this->xmlDData[$i]))?$this->xmlDData[$i] : "&nbsp;";
$html .=$this->_closeTDRow();
$html .=$this->_makeTDHeader("xml","xmlChildren");
unset($this->xmlCData[$i],$this->xmlDData[$i]);
}
$html .=$this->_closeTDRow();
$html .="</table>";
$this->xmlCount=0;
return $html;
}
function xmlCharacterData($parser,$data){
$count=$this->xmlCount-1;
if(!empty($this->xmlCData[$count]))
$this->xmlCData[$count].=$data;
else
$this->xmlCData[$count]=$data;
}
function xmlDefaultHandler($parser,$data){
$data=str_replace(array("&lt;!--","--&gt;"),"",htmlspecialchars($data));
$count=$this->xmlCount-1;
if(!empty($this->xmlDData[$count]))
$this->xmlDData[$count].=$data;
else
$this->xmlDData[$count]=$data;
}
function varIsDBResource($var,$db="mysql"){
$numrows=call_user_func($db."_num_rows",$var);
$numfields=call_user_func($db."_num_fields",$var);
$html .=$this->makeTableHeader("resource",$db." result",$numfields+1);
$html .="<tr><td class=\"dBug_resourceKey\">&nbsp;</td>";
for($i=0;$i<$numfields;$i++){
$field[$i]=call_user_func($db."_fetch_field",$var,$i);
$html .="<td class=\"dBug_resourceKey\">".$field[$i]->name."</td>";
}
$html .="</tr>";
for($i=0;$i<$numrows;$i++){
$row=call_user_func($db."_fetch_array",$var,constant(strtoupper($db)."_ASSOC"));
$html .="<tr>\n";
$html .="<td class=\"dBug_resourceKey\">".($i+1)."</td>";
for($k=0;$k<$numfields;$k++){
$tempField=$field[$k]->name;
$fieldrow=$row[($field[$k]->name)];
$fieldrow=($fieldrow=="")?"[empty string]" : $fieldrow;
$html .="<td>".$fieldrow."</td>\n";
}
$html .="</tr>\n";
}
$html .="</table>";
if($numrows > 0 )
call_user_func($db."_data_seek",$var,0);
return $html;
}
private function _varIsGDResource($var){
$html='';
$html .=$this->makeTableHeader("resource","gd",2);
$html .=$this->_makeTDHeader("resource","Width");
$html .=imagesx($var).$this->_closeTDRow();
$html .=$this->_makeTDHeader("resource","Height");
$html .=imagesy($var).$this->_closeTDRow();
$html .=$this->_makeTDHeader("resource","Colors");
$html .=imagecolorstotal($var).$this->_closeTDRow();
$html .="</table>";
return $html;
}
private function _addIndent($indent){
if(empty($indent)) return;
$tab='';
for($i=0; $i < $indent; $i++)$tab .="\t";
return $tab;
}
}
