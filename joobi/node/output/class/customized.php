<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Customized_class extends WView {
function create(){
$pageT=WPage::theme($this->namekey, 'html');
$pageT->type=49;
$pageT->htmlfile=1;
$pageT->folder=$this->folder;
$pageT->wid=$this->wid;
$pageT->file=$this->namekey.'.php';
$this->content=$pageT->display();
return parent::create();
}
}