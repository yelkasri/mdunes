<?php 


* @license GNU GPLv3 */

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