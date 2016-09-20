<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
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