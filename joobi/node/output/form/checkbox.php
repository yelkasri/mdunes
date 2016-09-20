<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_Corecheckbox extends WForms_default {
function create(){
$class=(isset($this->element->classes ))?$this->element->classes.' checkBox' : 'checkBox';
$this->content='<input type="checkbox" id="'.$this->idLabel.'" name="'.$this->map .'" class="'.$class.'"';
if($this->value)$this->content .=' checked ' ;
$this->content .=' />' ;
return true;
}
}
