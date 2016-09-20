<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CoreStatus_form extends WForms_default {
function create(){
$this->elementClassPosition='F';
$this->content='<div name="joobi_status_div" id="joobi_status_div">';
$this->content .='<div class="appsInstall">';
$this->content .='<center><div name="jloader_status" id="jloader_status" class="jloader" style="display:none;"></div></center>';
$this->content .='<div name="joobi_status_install" id="joobi_status_install" class="joobi_status_install">';
$this->content .='</div>';
$this->content .='</div></div>';
return true;
}
function show(){
return $this->create();
}}