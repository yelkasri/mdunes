<?php 


* @license GNU GPLv3 */

class Users_CoreNewaccount_form extends WForms_default {
function create(){
$objButtonO=WPage::newBluePrint('button');
$objButtonO->text=$this->element->name;
$objButtonO->color='success';
$objButtonO->icon='fa-user';
$objButtonO->link=WPage::routeURL('controller=users&task=register');
$this->content=WPage::renderBluePrint('button',$objButtonO );
return true;
}}