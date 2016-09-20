<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
 class Output_Area_tag {
function process($object){
$replacedTagsA=array();
if(empty($this->params->container) || !is_array($this->params->container)){
return $replacedTagsA;
}
foreach($object as $tag=> $myTagO){
$area=$myTagO->name;
if(isset($this->params->container[$area])){
$myTagO->wdgtContent=$this->params->container[$area];
}else{
$myTagO->wdgtContent='';
}
$replacedTagsA[$tag]=$myTagO;
}
return $replacedTagsA;
}
}