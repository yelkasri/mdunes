<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WButton_CoreExtlink extends WButtons_default {
protected $noJSonButton=true;
var $_target='_blank';
function create(){
$this->buttonO->href=$this->buttonO->action;
return true;
}
}
