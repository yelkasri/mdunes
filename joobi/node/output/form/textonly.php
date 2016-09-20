<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_Coretextonly extends WForms_default {
function create(){
if(isset($this->element->link) && !isset($this->element->spanval)){
$link=new WLink($this->value );
$this->content=$link->make( WPage::routeURL($this->element->link ));
return true;
}else{
$status=parent::create();
return $status;
}
}
}
