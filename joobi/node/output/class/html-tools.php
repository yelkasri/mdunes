<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WDiv extends WElement {
var $clear=false;  
public static function build($data,$float=null,$class=null,$id=null,$style=null){
$pane=new WDiv($data );
if(isset($float))$pane->float=$float;
if(isset($class))$pane->classes=$class;
if(isset($id))$pane->id=$id;
if(isset($style))$pane->style=$style;
$pane->make();
return $pane->display();
}
function create(){
if(empty($this->_data)) return false;
if($this->clear)$this->content .='<div style="clear:both;"></div>';
$this->content='<div' ;
if(!empty($this->float))$this->style .='float:'.$this->float.';';
if(!empty($this->width))$this->style .='width:'.$this->width.'em;';
if(!empty($this->height))$this->style .='height:'.$this->height.'em;';
if($this->id)$this->content .=' id="'.$this->id.'"';
if(isset($this->classes))$this->content .=' class="'.$this->classes.'"';
if($this->style)$this->content .=' style="'.$this->style.'"';
if(isset($this->onfocus))$this->content .=' onfocus="'.$this->onfocus.'"';
if(isset($this->onblur))$this->content .=' onblur="'.$this->onblur.'"';
if(isset($this->ondblclick))$this->content .=' ondblclick="'.$this->ondblclick.'"';
if($this->align)$this->content .=' align="'.$this->align.'"';
$this->content .=">". $this->_data .'</div>'.$this->crlf;
return true;
}
}
class WFieldset extends WElement {
var $clear=false;  
function create(){
if(empty($this->_data)) return false;
$this->content='';
if(!empty($this->legend))$this->content .='<h3 class="fieldsetLegend">'. $this->legend .'</h3>';$this->content .='<fieldset' ;
if($this->id)$this->content .=' id="'.$this->id.'"';
if($this->class)$this->content .=' class="'.$this->class.'"';
if($this->style)$this->content .=' style="'.$this->style.'"';
if($this->align)$this->content .=' align="'.$this->align.'"';
$this->content .=">" . $this->crlf;
$this->content .=$this->_data . $this->crlf;$this->content .='</fieldset>'.$this->crlf;
return true;
}
}
class WSpan extends WElement {
function create(){
if(empty($this->_data)) return false;
$this->content='<span' ;
if(isset($this->classes))$this->content .=' class="'.$this->classes.'"';
if(isset($this->title))$this->content .=' title="'.WGlobals::filter($this->title, 'string'). '"';
if($this->style)$this->content .=' style="'.$this->style.'"';
if($this->align)$this->content .=' align="'.$this->align.'"';
$this->content .='>'.$this->_data;$this->content .='</span>'.$this->crlf;
return true;
}
}
class WSelect {
public static function option($value,$text='',$value_name='value',$text_name='text',$extraProperty=null){
$option=new stdClass;
$option->$value_name=$value;
$option->$text_name=$text;
if(!empty($extraProperty)){
foreach($extraProperty as $prop=> $val){
$option->$prop=$val;
}}
return $option;
}
function createCollapsableTree($list,$tag_name,$tag_attribs=null,$propKey='value',$propText='text',$selected=null,$idtag=null,$translate=false,$arrayType=false,$onChange=false){
static $addJS=true;
static $count=1;
if(is_array($selected)){
if(empty($selected))$selected='';
else $selected=array_shift($selected );
}
$selected=(string)$selected;
$idtag=str_replace( array('[',']'), '_',$idtag );
$jstreeHidden=$idtag.'_hidden';
$js='jQuery( document ).ready(function(){';
$js .='jQuery("#'.$idtag.'").jstree({';
$js .='"core":{"data":[';
$js .=WGet::$rLine;
$parent='#';
$lastParentA=array();
$lastParentA[1]='#';
$previousParentA=array();
$previousParentA[-1]=1;
$previousParentA[0]=1;
foreach($list as $key=> $obj){
if( substr($obj->text, 0, 2 )=='¦'){
$spacer='¦&nbsp;&nbsp; ';
$count=0;$newText=str_replace('¦&nbsp;&nbsp; ','',$obj->text, $count );
$previousParentA[$key]=$count;
if($count > 1){
$part1=(!empty($previousParentA[$key])?$previousParentA[$key] : 0 );
$part2=(!empty($previousParentA[$key-1])?$previousParentA[$key-1] : 0 );
if($part1 !=$part2){
if($part1 > $part2){
$parent='a_'.($key-1 );
if(!isset($lastParentA[$count]))$lastParentA[$count]=$parent;
}else{
$parent=$lastParentA[$count];
}}}else{
$parent='#';
}}else{
$newText=$obj->text;
}
$defaultVal=($selected==$obj->value?',"state":{"opened":true,"selected":true }' : '');
$js .='{"id":"a_'.$key.'","parent":"'.$parent.'","text":"'.$newText.'","a_attr":{name:"'.$obj->value.'"}'.$defaultVal.'},';
$js .=WGet::$rLine;
}
$js .=']} });';
$js .='var hiddenJSTreeInput=jQuery("#'.$jstreeHidden.'");';
$js .='jQuery("#'.$idtag.'").on("click", "a", function(){
hiddenJSTreeInput.val(jQuery(this).attr("name"));
})';
$js .='});';
WPage::addJSLibrary('jquery');
WPage::addJSFile('js/jstree.js');
WPage::addCSSFile('css/tree.css');
WPage::addJS($js );
return '<input type="hidden" id="'.$jstreeHidden.'" name="'.$tag_name.'" value="'.$selected.'"><div id="'.$idtag.'"></div>';
}
function create($list,$tag_name,$tag_attribs=null,$propKey='value',$propText='text',$selected=null,$idtag=null,$translate=false,$arrayType=false,$onChange=false,$disabled=false){
static $addJS=true;
static $count=1;
$useFancy=( count($list) > 9?true : false);
$getOrderedList=WGlobals::get('getOrderedList', false, 'global');
WGlobals::set('getOrderedList', false, 'global');
if($getOrderedList && $useFancy){
$collapsable=WPref::load('PMAIN_NODE_CATEGORYCOLLAPSE');
if($collapsable){
$viewType=WGlobals::get('firstFormViewType', 0, 'global');
if($viewType==51 ) return $this->createCollapsableTree($list, $tag_name, $tag_attribs, $propKey, $propText, $selected, $idtag, $translate, $arrayType, $onChange );
}}
if(!empty($this->outype) && 1==$this->outype)$useFancy=false;
if($useFancy && $addJS){
WPage::addJSFile('js/chosen.jquery.js');
WPage::addCSSFile('css/chosen.css');
WPage::addJS('jQuery(function(){jQuery(".chosen-select").chosen()});');
$addJS=false;
}
$count++;
if(!isset($idtag))$idtag=WView::generateID('WSelect',$count );
$this->content=WGet::$rLine.'<select name="'.$tag_name.'"';
$this->content .=' id="'.$idtag.'"';
if($useFancy){
if(isset($this->classes)){
$this->content .=' class="'.$this->classes.' chosen-select"';
}else{
$this->content .=' class="chosen-select"';
}}else{
if(isset($this->classes))$this->content .=' class="'.$this->classes.'"';
}
if(!empty($this->style))$this->content .=' style="'.$this->style.'"';
if(!empty($this->align))$this->content .=' align="'.$this->align.'"';
if($onChange)$this->content .=' onchange="'.$onChange.'"';
if(!empty($tag_attribs))$this->content .=' '. $tag_attribs;
if($useFancy && !empty($this->outype) && ($this->outype==1 || $this->outype==8 )){
$this->content .=' multiple>';
}else{
$this->content .=' >';
}$this->content .=WGet::$rLine;
$this->content .=$this->_options($list, $propKey, $propText, $selected, $translate, $arrayType );
$this->content .='</select>';
$this->content .=WGet::$rLine;
return $this->content;
}
private function _options($list,$propKey='value',$propText='text',$selected=null,$translate=false,$arrayType=false){
if(empty($list)) return '';
$html='';
$optionGroup=false;
foreach($list as $key=> $value){
if($arrayType){$listKey=$key;
$listValue=$value;
}else{
$listKey=$value->$propKey;
$listValue=$value->$propText;
}
if( substr($listValue, 0, 2 )=='--'){
if($optionGroup){
$html .='</optgroup>';
$html .=WGet::$rLine;
}
$listValue=trim( substr($listValue, 2, strlen($listValue)-2 ));
$html .='<optgroup label="'.  $listValue .'">';
$optionGroup=true;
}elseif(substr($listValue, 0, 2 )=='++'){
$listValue=trim( substr($listValue, 2, strlen($listValue)-2 ));
$hyphen='';
for($i=0; $i <=2*strlen($listValue); $i++){
$hyphen.='-';
}
$html .='<optgroup label="'.$hyphen.'"></optgroup>' .
'<optgroup label="'.$listValue.'"></optgroup>' .
'<optgroup label="'.$hyphen.'"></optgroup>';
}elseif(substr($listValue, 0, 2 )=='**'){
$listValue=trim( substr($listValue, 2, strlen($listValue)-2 ));
$html .='<optgroup label="**'.$listValue.'**"></optgroup>';
}else{
$extra='';
if(is_array($selected )){
if( in_array($listKey, $selected ))  $extra .=' selected="selected" ';
}else{
$extra .=((!empty($selected) && (string)$listKey==(string)$selected )?' selected="selected" '  : '');
}if(isset($value->extras)){
$extra .=$value->extras;
}
if(!empty($value->style)){
$extra .=' style="'.$value->style.'"';
}
if(!empty($value->class)){
$extra .=' class="'.$value->class.'"';
}
$html .='<option value="'.$listKey.'"'.$extra.'>'.$listValue.'</option>';}
$html .=WGet::$rLine;
}
return $html;
}
}
class WRadio {
public $radioStyle='radioButton';
function create($list,$tag_name,$tag_attribs=null,$propKey='value',$propText='text',$selected=null,$idtag=null,$disable=false,$arrayType=false,$radioType=false,$colnb=0,$reqChecked=false){
$data=new stdClass;
$data->listA=$list;
$data->tagName=$tag_name;
$data->tagAttributes=$tag_attribs;
$data->tagID=$idtag;
$data->propertyKey=$propKey;
$data->propertyText=$propText;
$data->selected=$selected;
$data->disable=$disable;
$data->arrayType=$arrayType;
$data->radioType=$radioType;
$data->colnb=$colnb;
$data->radioStyle=$this->radioStyle;
$data->requiredCheked=$reqChecked;
return WPage::renderBluePrint('listradio',$data );
}
}
class WList {
public function create($list,$baseLink,$selected=null,$tag_attribs=null,$propKey='value',$propText='text'){
if(empty($list)) return '';
$classes=(isset($this->classes))?' class="'.$this->classes.'"' : '';
if(isset($this->style))$tag_attribs .=' style="'.$this->style.'"';
if(isset($this->align))$tag_attribs .=' align="'.$this->align.'"';
if(!is_array($selected))$selected=array($selected );
$html='<ul'.$classes .'>';
foreach($list as $key=> $value){
$listKey=$value->$propKey;
$listValue=$value->$propText;
if( substr($listValue, 0, 2 )=='--'){
}else{
$html .='<li>';
if(empty($listKey)){
$pos=strrpos($baseLink, '=');
$baseLinkNew=substr($baseLink, 0, $pos );
$link=WPage::link($baseLinkNew );
$html .='<a href="'.$link.'" '.$tag_attribs.'/>';
$html .='<i class="fa fa-times"></i>';
}elseif( in_array($listKey, $selected )){
if( strpos($baseLink, '!') !==false){
$baseLinkNew=str_replace('!'.$listKey, '',$baseLink );
}elseif( strpos($baseLink, '|') !==false){
$baseLinkNew=str_replace('|'.$listKey, '',$baseLink );
}
$link=WPage::link($baseLinkNew );
$html .='<a href="'.$link.'" '.$tag_attribs.'/>';
$html .='<i class="fa fa-check-square-o"></i>';
}else{
$link=WPage::link($baseLink . $listKey );
$html .='<a href="'.$link.'" '.$tag_attribs.'/>';
$html .='<i class="fa fa-square-o"></i>';
}$html .=$listValue.'</a>';
$html .='</li>';
$html .=WGet::$rLine;
}
}
$html .=WGet::$rLine;
$this->content=$html.'</ul>';
return $this->content;
}
}
class WForm extends WElement {
public $autoComplete=null;
public $enctype=null;
public $class='';
var $option=null; var $task=null;
var $name=null;var $id=null;var $action=null;var $method=null;var $_buttons=null;var $_hidden=array();var $_firstform=null;
var $useCookies=true;
private $_inputButtons=null;
private static $_idA=array();
private static $_ct=0;
function __construct($formName='',$params=null){
if(!empty($formName)){
$this->name=$formName;
$this->id=$formName;
}
self::$_ct++;
if(!isset(self::$_idA[$this->id])) self::$_idA[$this->id]=self::$_ct;
$this->crlf=WGet::$rLine;
$this->autoComplete=JOOBI_FORM_AUTOCOMPLETE;
}
public static function getPrev($map,$sid=0){
if(empty($map)) return;
if(!empty($sid)){
if(!in_array($sid, array('x','c')) && !is_numeric($sid))$sid=WModel::get($sid, 'sid');
$trk=WGlobals::get( JOOBI_VAR_DATA, array(), '','array');
$previous=(isset($trk[$sid]))?$trk[$sid] : array();
if(!empty($previous)){
if(isset($previous[$map])) return $previous[$map];
if( substr($map, 1, 1 )=='['){
$subMap=substr($map, 0, 1 );
$map=substr($map, 2, strlen($map) -3 );
if(isset($previous[$subMap][$map])) return $previous[$subMap][$map];
}}
$editorMap='zdtr_'.JOOBI_VAR_DATA.'_'.$sid.'__'.$map.'_';
$editor=WGlobals::getSession('formEditor',$editorMap );
if(!empty($editor)){
WGlobals::setSession('formEditor',$editorMap, null );
return $editor;
}else{
$editor=WGlobals::get($editorMap );
if(!empty($editor)) return $editor;
}}
if( substr($map, 1, 1 )=='['){
$subMap=substr($map, 0, 1 );
$map=substr($map, 2, strlen($map) -3 );
if(empty($map)) return;
$trk=WGlobals::get( JOOBI_VAR_DATA, array(), '','array');
if(isset($trk[$subMap][$map])) return $trk[$subMap][$map];
}
$maybe=WGlobals::get($map );
if(!empty($maybe )){
return $maybe;
}else{
$trk=WGlobals::get( JOOBI_VAR_DATA, array(), '','array');
$sid=(!empty($trk['s']['mid'])?$trk['s']['mid'] : 0 );
$previous=(isset($trk[$sid]))?$trk[$sid] : array();
$pattern='/[a-zA-Z0-9]+(?=\])/';
$st=preg_match($pattern, $map, $matches );
if($matches){
$ressss=WGlobals::get($matches[0] );
if(!empty($ressss)) return $ressss;
$letter=$map[0];
if(isset($previous[$letter][$matches[0]])){
return $previous[$letter][$matches[0]];
}elseif(!empty($trk)){
foreach($trk as $arrayTable){
if(isset($arrayTable[$letter][$matches[0]])){
return $arrayTable[$letter][$matches[0]];
}}}
}else{
if(isset($previous[$map])){
return $previous[$map];
}elseif(!empty($trk)){
foreach($trk as $arrayTable){
if(!empty($subMap)){
if(isset($arrayTable[$subMap][$map])){
return $arrayTable[$subMap][$map];
}}else{
if(isset($arrayTable[$map])){
return $arrayTable[$map];
}}}}}}
}
public function hidden($name,$value='',$overwrite=false,$addToSecurityCheck=false){
if(is_array($name)){
$this->_hidden=array_merge($this->_hidden, $name  );
return;
}
$len=strlen( JOOBI_VAR_DATA ) + 1;
if($addToSecurityCheck && substr($name, 0, $len )==JOOBI_VAR_DATA.'['){
$tempString=rtrim( substr($name, $len ), ']');
$tempStringA=explode('][',$tempString );
$securityForm=WGlobals::get('securityForm',array(), 'global');
$securityForm[(string)$tempStringA[0]][]=$tempStringA[1];
WGlobals::set('securityForm',$securityForm, 'global');
}
if(is_array($value)){
$i=0;
foreach($value as $oneValue){
$newName=$name.'['.$i.']';
if((!empty($newName) && !isset($this->_hidden[$newName])) || (!empty($newName) && $overwrite)){
$this->_hidden[$newName]=$oneValue;
WView::generateID('hidden',$newName . self::$_idA[$this->id] );
}$i++;
}}else{
if(!empty($name) && ( !isset($this->_hidden[$name]) || $overwrite ))$this->_hidden[$name]=$value;
WView::generateID('hidden',$name . self::$_idA[$this->id] );
}
}
public function hiddenRemove($name){
if(isset($this->_hidden[$name])) unset($this->_hidden[$name] );
}
public function hiddenDuplicate($name,$value=''){
$this->_hiddens[$name][]=$value;
}
public function sForm(){
$this->method=(isset($this->method))?$this->method : JOOBI_FORM_METHOD;
$h=$this->crlf;
$h .='<form role="form"';
if(!empty($this->action )){
$fAction=$this->action;
}else{
$option=$this->option;
if(empty($option) && !empty($this->_hidden[JOOBI_URLAPP_PAGE]))$option=$this->_hidden[JOOBI_URLAPP_PAGE];
$controller=(!empty($this->_hidden['controller'])?$this->_hidden['controller'] : '');
if( IS_ADMIN){
if( method_exists('WPage','linkNoSEF')){
$fAction=WPage::linkNoSEF('','standard');
}else{
$fAction='';
}
}else{
$fURL=WPage::formURL($option, $controller );
if(!empty($fURL ))$fAction=$fURL;
}}
if(!empty($fAction)){
$h .=' action="'.$fAction.'"';
}
if(!empty($this->method))$h .=' method="'.$this->method.'"';
$h .=(isset($this->enctype)?' enctype="multipart/form-data"' : '');
$h .=' id="'.$this->name.'"';
if(!empty($this->class))$h .=' class="'.$this->class.'"';
if($this->autoComplete)$h .=' autocomplete="on"';
$h .=' name="'.$this->name.'">'.$this->crlf;
$this->content .=$h;
return $h;
}
public function eForm(){
if(!IS_ADMIN){$itemId=WGlobals::get( JOOBI_PAGEID_NAME );
if(!empty($itemId))$this->hidden( JOOBI_PAGEID_NAME, $itemId );
}
$h='';
if(isset($this->_inputButtons )){
$size=sizeof($this->_inputButtons);
for($index=0; $index < $size; ++$index){
$value=(isset($this->_inputButtons[$index]->value))?' value="'.$this->_inputButtons[$index]->value.'" ' : '' ;
$name=(isset($this->_inputButtons[$index]->name))?' name="'.$this->_inputButtons[$index]->name.'" ' : '' ;
$extra=(isset($this->_inputButtons[$index]->extra))?' '.$this->_inputButtons[$index]->extra.' ' : '' ;
if($this->_inputButtons[$index]->type=='button'){
$label=$this->_inputButtons[$index]->label;
$inputButton[]='<button type="submit" '.$name . $value . $extra.' >'.$label.'</button>';
}else{
$inputButton[]='<input type="'. $this->_inputButtons[$index]->type .'" '.$name . $value . $extra.'/>';
}
}
$h .=( count($inputButton )?implode('',$inputButton ) : '');
$h .=$this->crlf;
}
$isPopUp=WGlobals::get('is_popup', false, 'global');
if($isPopUp)$this->hidden('isPopUp','true');
if(isset($this->_hidden)){
$keys=array_keys($this->_hidden);
foreach($keys as $key){
if($key==JOOBI_URLAPP_PAGE){
$idHidden='whdopt';
}else{
$idHidden=WView::retreiveID('hidden',$key . self::$_idA[$this->id] );
}
$h .='<input type="hidden"';
if(!empty($idHidden))$h .=' id="'.$idHidden.'"';
$h .=' name="'.$key.'" value="'.$this->_hidden[$key].'"/>'.$this->crlf;
}}
if(isset($this->hiddens)){
$i=1;
foreach($this->hiddens as $key=> $myArray){
if(!empty($myArray)){
foreach($myArray as $val){
$h .='<input type="hidden" id="'.$key.'" name="'.$key.'" value="'.$val.'"/>'.$this->crlf;
$i++;
}}}}
$h .='</form>'.$this->crlf;
$this->content .=$h;
return $h;
}
public function addIB($type='submit',$value=null,$name=null,$extra=null,$label=null){
$button=new stdClass;
$button->type=$type;
$button->value=$value;
$button->name=$name;
$button->extra=$extra;
$button->label=$label;
$this->_inputButtons[]=$button;
}
public function addContent($content){
$this->data=&$content;
}
public function make($notUsed=null,$notUsed2=null){
$this->sForm();
$this->content .=$this->data;
$this->eForm();
$h=$this->content;
$this->content='';
return $h;
}
}
class WLink extends WElement {
var $lien=null;var $title=null;var $target=null;var $extra=null;var $_data='';
var $_ssl=0;
function ssl($ssl=1){
$this->_ssl=$ssl;
}
function make($url='',$notUsed=null){
if(!IS_ADMIN && isset($this->_itemId))$url .=$this->_itemId;
$attribs='';
if($this->class)$attribs .=' class="'.$this->class.'"';
if($this->title)$attribs .=' title="'.WGlobals::filter($this->title, 'string'). '"';
if($this->target)$attribs .=' target="'.$this->target.'"';
if($this->extra)$attribs .=' '.$this->extra;
return '<a href="'.$url.'" '.$attribs.'>'.$this->_data.'</a>';
}
}