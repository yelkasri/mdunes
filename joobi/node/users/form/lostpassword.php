<?php 


* @license GNU GPLv3 */

class Users_Lostpassword_form extends WForms_default {
function create(){
$objButtonO=WPage::newBluePrint('button');
$objButtonO->text=$this->element->name;
$objButtonO->id='lostpwd';
$objButtonO->color='warning';
$objButtonO->icon='fa-lock';
$objButtonO->link=WPage::routeURL('controller=users&task=requestpwd');
$this->content=WPage::renderBluePrint('button',$objButtonO );
return true;
}
}