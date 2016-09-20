<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('form.checkbox');
class Users_Rememberme_form extends WForm_checkbox {
function create(){
parent::create();
$this->content='<div style="text-align:left;">'.$this->content;
$this->content .='  '.$this->element->name;
$this->content .='</div>';
return true;
}}