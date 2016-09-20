<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WListing_Coreemail extends WListings_default{
function create(){
$eids=$this->eid();
$email=(is_array($eids)?implode($eids) : $eids );
if(empty($this->element->truncate))$this->element->truncate=0;
$outputEMailC=WClass::get('output.emailclock');
$this->value=$outputEMailC->cloakmail($email, $email, $this->value, $this->element->truncate );
return parent::create();
}
}
