<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
WView::includeElement('form.yesno');
class Users_Watermarksetting_form extends WForm_yesno {
function create(){
parent::create();
$this->content .='<br /><a style="color:green;" target="_blank" href="'. WPage::routeURL('controller=apps&task=preferences').'">'. WText::t('1338404831MNYL').'</a>';
return true;
}}