<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WForm_CoreText extends WForms_default {
protected $inputType=null;
public function create(){
static $countMe=0;
if(!isset($this->element))$this->element=new stdClass;
$countMe++;
if(is_array($this->value)) return false;
 $text=WGlobals::filter((string)$this->value, 'string');
if(!empty($this->layout) && !empty($this->layout->_extras['fsize'][$this->element->map]))$fsize=$this->layout->_extras['fsize'][$this->element->map];
if(!isset($this->element->width ) || isset($fsize ))$this->element->width=(isset($fsize ))?$fsize : '35';
if(!isset($this->inputClass ))$this->inputClass='inputbox';
$this->_class=(isset($this->element->classes ))?$this->element->classes : $this->inputClass;
if(empty($this->inputType)){
if(!empty($this->element->typeName)){
$this->inputType=( in_array($this->element->typeName, array('checkbox','file','hidden','image','radio','reset'))?$this->element->typeName : 'text');
if( in_array($this->element->typeName, array('hidden','image')))$this->inputType='hidden';
}else{
$this->inputType='text';
}
if(isset($this->overType))$this->inputType=$this->overType;
if($this->inputType=='inputbox')$this->inputType='text';
}
if(!empty($this->addPostText) || !empty($this->addPreText))$this->_class .=' form-control';
$this->content='<input type="'.$this->inputType.'" id="'.$this->idLabel.'" name="'.$this->map .'"';
$this->content .=(!empty($this->element->align ))?' align="'.$this->element->align .'"' : '';
$this->content .=' class="'. $this->_class .'"';
if(!empty($this->element->width )){
if( is_numeric($this->element->width)){
$this->content .=' size="'.$this->element->width.'"';
}else{
if(empty($this->element->style))$this->element->style='';
$this->element->style .='width:'.$this->element->width.';';
}
}
$this->content .=(!empty($this->element->style ))?' style="'.$this->element->style.'"' : '';
if(isset($this->element->maxlgt ))$this->content .= ' maxlength="'.$this->element->maxlgt .'"';
$this->content .=' value="'.$text.'"';
if(!empty($this->element->disabled ))$this->content .=' disabled';
if(!empty($this->element->readonly ))$this->content .=' readonly';
if($this->inputType !='button' && !empty($this->element->required))  $this->content .=' required="required"';
if(empty($this->autofocus) && $countMe==1)$this->element->autofocus=true;
if(!empty($this->element->autofocus))  $this->content .=' autofocus';
if(!empty($this->element->autocomplete))  $this->content .=' autocomplete="off"';
if(!empty($this->element->yid) && 'placeHolder'==WView::$titleDisplay[$this->element->yid]){
$this->content .=' placeholder="'.$this->element->name.'"';
}
if(!empty($this->extras ))$this->content .=$this->extras;
$this->content .=' />';
if(!empty($this->addPreText) || !empty($this->addPostText)){
$html='<div class="input-group">';
if(!empty($this->addPreText))$html .='<span class="input-group-addon">'.$this->addPreText.'</span>';
$html .=$this->content;
if(!empty($this->addPostText))$html .='<span class="input-group-addon">'.$this->addPostText.'</span>';
$html .='</div>';
$this->content=$html;
}
return true;
}
}
