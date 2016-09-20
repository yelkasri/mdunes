<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WListing_Coretext extends WListings_default{
function create(){
if($this->searchOn && !empty($this->mywordssearched)){
$this->value=preg_replace('#('.str_replace('#','\#',implode('|',$this->mywordssearched)).')#i','<span class="search-highlight">$0</span>',$this->value );
}
if(!empty($this->element->truncate)){
if( strlen($this->value) > $this->element->truncate){
$strlengt=(!empty($this->element->trunchar)?strlen($this->element->trunchar) : 0 );
$this->value=substr( strip_tags($this->value), 0, $this->element->truncate-$strlengt);
if(!empty($this->element->trunchar))$this->value .=$this->element->trunchar;
}
}
$status=parent::create();
if(empty($this->element->indentationDone) && empty($this->element->ovly)){
$this->element->indentationDone=true;
if(!empty($this->data->indentTreeNumber ) && !empty($this->element->treeindent )){
$indentationWidth=25 * ($this->data->indentTreeNumber - 1 );
if(!empty($indentationWidth))$html='<div style="width:'.$indentationWidth.'px;float:left;">&nbsp;</div>';
$this->content=$html . $this->content;
}else{
$depth=$this->getValue('depth');
if(!empty($depth)){
$closeDiv=true;
$indentWidth=20;
$indentationWidth=$indentWidth * ($depth - 1 );
if(!empty($indentationWidth)){
$contentImg='<div style="width:'.$indentationWidth.'px;float:left;">&nbsp;</div>';
$contentImg .='<div style="float:left;">';
$contentImg .=$this->content;
$contentImg .='</div>';
$this->content=$contentImg;
}
}
}
}
return $status;
}
}
