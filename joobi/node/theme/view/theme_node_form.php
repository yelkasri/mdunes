<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_Theme_node_form_view extends Output_Forms_class {
function prepareView(){
$allowTheme=WPref::load('PVENDORS_NODE_ALLOWTHEME');
if( WRoles::isNotAdmin('storemanager') && empty($allowTheme )) return false;
$type=$this->getValue('type');
if(!in_array($type, array( 1, 201, 50 )))$this->removeElements( array('theme_node_form_tab_catalog'));
$skinsextraExist=WExtension::exist('skinsextra.application');
if(empty($skinsextraExist)){
$this->removeElements( array('theme_node_form_image_skin'));
}
return true;
}
}