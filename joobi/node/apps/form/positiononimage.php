<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CorePositiononimage_form extends WForms_default {
function create(){
$this->content='<table style="border-style: groove; text-align: center; width: 130px;" border="1" cellpadding="2" cellspacing="0" rules="none"><tbody><tr>';
$this->content .='<td width="10" height="40"><input id="'. $this->idLabel .'1" name="'.$this->map.'" value="1" ' .(($this->value==1)?'checked="checked" ' : ''). 'type="radio"></td>';
$this->content .='<td width="10" height="40"><input id="'. $this->idLabel .'2" name="'.$this->map.'" value="2" ' .(($this->value==2)?'checked="checked" ' : ''). 'type="radio"></td>';
$this->content .='<td width="10" height="40"><input id="'. $this->idLabel .'3" name="'.$this->map.'" value="3" ' .(($this->value==3)?'checked="checked" ' : ''). 'type="radio"></td>';
$this->content .='<tr></tr>';
$this->content .='<td width="10" height="40"><input id="'. $this->idLabel .'4" name="'.$this->map.'" value="4" ' .(($this->value==4)?'checked="checked" ' : ''). 'type="radio"></td>';
$this->content .='<td width="10" height="40"><input id="'. $this->idLabel .'5" name="'.$this->map.'" value="5" ' .(($this->value==5)?'checked="checked" ' : ''). 'type="radio"></td>';
$this->content .='<td width="10" height="40"><input id="'. $this->idLabel .'6" name="'.$this->map.'" value="6" ' .(($this->value==6)?'checked="checked" ' : ''). 'type="radio"></td>';
$this->content .='<tr></tr>';
$this->content .='<td width="10" height="40"><input id="'. $this->idLabel .'7" name="'.$this->map.'" value="7" ' .(($this->value==7)?'checked="checked" ' : ''). 'type="radio"></td>';
$this->content .='<td width="10" height="40"><input id="'. $this->idLabel .'8" name="'.$this->map.'" value="8" ' .(($this->value==8)?'checked="checked" ' : ''). 'type="radio"></td>';
$this->content .='<td width="10" height="40"><input id="'. $this->idLabel .'9" name="'.$this->map.'" value="9" ' .(($this->value==9)?'checked="checked" ' : ''). 'type="radio"></td>';
$this->content .='</tr></tbody></table>';
return true;
}}