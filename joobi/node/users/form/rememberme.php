<?php 


* @license GNU GPLv3 */

WView::includeElement('form.checkbox');
class Users_Rememberme_form extends WForm_checkbox {
function create(){
parent::create();
$this->content='<div style="text-align:left;">'.$this->content;
$this->content .='  '.$this->element->name;
$this->content .='</div>';
return true;
}}