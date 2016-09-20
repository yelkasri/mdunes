<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_CoreStatus_form extends WForms_default {
function create(){
$basscript='<script type="text/javascript">var jslide=new Fx.Slide(\'joobi_status_div\');jQuery(\'#joobi_status_toggle\').on(\'click\',function(e){e=new Event(e);jslide.toggle();e.stop();})</script>';
WGlobals::set('addFooter',$basscript, 'global','append');
WText::load('apps.node');
$this->content='<div name="joobi_status_div" id="joobi_status_div"><fieldset>';
$this->content .='<legend>'.WText::t('1206732392OZVH').'</legend>';
$this->content .='<center><div name="jloader_status" id="jloader_status" class="jloader"></div></center>';
$this->content .='<div name="joobi_status_install" id="joobi_status_install" class="joobi_status_install">';
$this->content .='</div></fieldset></div><a id="joobi_status_toggle" href="#">';
$this->content .=WText::t('1206732398LZKD');
$this->content .='</a>';
return true;
}
function show(){
return $this->create();
}
}