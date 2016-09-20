<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Email_CoreTag_form extends WForms_default {
private static $_pregMatch=null;
function create(){
if(empty($this->value)){
return true;
}
$this->content=str_replace( array( "\n" ), '<br/>',$this->value );
return true;
}
function show(){
$this->create();
return true;
}}