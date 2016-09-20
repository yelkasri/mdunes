<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Translation_Edit_controller extends WController {
function edit(){
$sid=WGlobals::get('sid');
$title=WGlobals::get('title');
$name=WGlobals::get('name');
$this->controller='translation';
$this->task='edit';
$this->wid=WExtension::get('translation','wid');
$this->sid=$sid;
$this->layout->sid=$sid;
$params=new stdClass;
$params->dynamicForm=true;
$this->layout=WView::get('translation_edit_trans','html',$params, $this );
$this->layout->name=$title.' '.$name;
$this->content=$this->layout->make();
return true;
}
}