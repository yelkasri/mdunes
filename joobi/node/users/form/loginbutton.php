<?php 


* @license GNU GPLv3 */

WView::includeElement('main.form.submit');
class Users_Loginbutton_form extends WForm_submit {
public function create(){
$status=parent::create();
$objButtonO=WPage::newBluePrint('button');
$objButtonO->text=$this->content;
$objButtonO->type='standard';
$objButtonO->float='right';
$objButtonO->icon='next';
$this->content=WPage::renderBluePrint('button',$objButtonO );
return $status;
}
}