<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WButton_CoreJscancel extends WButtons_default {
protected $noJSonButton=true;
function create(){
$this->buttonO->buttonJS='history.back();';
return true;
}
}
