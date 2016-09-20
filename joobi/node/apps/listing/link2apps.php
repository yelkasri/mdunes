<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CoreLink2apps_listing extends WListings_default{
function create(){
$namekey=$this->getValue('namekey');
$nA=explode('.',$namekey );
$folder=$nA[0];
$this->content='<a href="'.JOOBI_INDEX.'?'.JOOBI_URLAPP_PAGE.'='.WApplication::getAppLink($folder ).'&controller='.$folder.'">'.$this->value.'</a>';
return true;
}}