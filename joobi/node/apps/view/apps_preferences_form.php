<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_Apps_preferences_form_view extends Output_Forms_class {
protected function prepareView(){
$emailExist=WExtension::exist('email.node');
if(!$emailExist){
$this->removeMenus('apps_preferences_form_sendtest');
$this->removeElements( array('apps_preferences_form_mail','apps_preferences_form_email_node_adminnotif'));
}
if(!WExtension::exist('scheduler.node')){
$this->removeElements('apps_preferences_form_scheduler');
}
if(!WExtension::exist('main.node')){
$this->removeElements( array('apps_preferences_form_fieldset_captcha','apps_preferences_form_fieldset_watermark','apps_preferences_form_fieldset_storage','apps_preferences_form_views_chart_dimmensions','apps_preferences_form_views_custom_field','apps_preferences_form_library_node_useminify','apps_preferences_form_library_node_cdnuse','apps_preferences_form_library_node_cdnserver'));
}
$cronType=WPref::load('PLIBRARY_NODE_CRON');
if(empty($cronType)){
$this->removeElements( array('apps_preferences_forms_scheduler_node_cronfrequency',
 'apps_preferences_forms_scheduler_node_timelimit',
 'apps_preferences_forms_scheduler_node_reportemail',
 'apps_preferences_forms_scheduler_node_maxtasks',
'apps_preferences_forms_scheduler_node_maxprocess',
 'apps_preferences_forms_scheduler_node_usepwd',
 'apps_preferences_forms_scheduler_node_password',
'apps_preferences_forms_scheduler_node_report'));
}
if( 15 !=$cronType){
$this->removeElements( array('apps_preferences_forms_scheduler_node_cron_url'));
}
if( WPref::load('PINSTALL_NODE_DISTRIB_EDIT')){
$this->removeElements('apps_preferences_form_install_node_distrib_website');
}else{
$this->removeElements('apps_preferences_form_install_node_distrib_website_edit');
}
return true;
}}