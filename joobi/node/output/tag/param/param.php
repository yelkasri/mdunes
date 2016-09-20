<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Output_Param_tag {
function process($object){
$replacedTagsA=array();
foreach($object as $tag=> $myTagO){
if(empty($myTagO->name))$myTagO->name=$myTagO->_type;
$name=$myTagO->name;
if(isset($this->params->$name)){
if( is_string($this->params->$name) || is_numeric($this->params->$name) || is_bool($this->params->$name)){
$myTagO->wdgtContent=$this->params->$name;
}else{
$message=WMessage::get();
$message->codeE('Your param tag "'.$name.'" is not a string, please change it!',array(), 0 );
$myTagO->wdgtContent=serialize($this->params->$name );
}
}else{
$myTagO->wdgtContent='';
}
$replacedTagsA[$tag]=$myTagO;
}
return $replacedTagsA;
}
}