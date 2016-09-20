<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_Theme_node_show_view extends Output_Forms_class {
function prepareView(){
$type=$this->getValue('type');
if( 106==$type){
$this->removeMenus( array('theme_node_show_edit','theme_node_show_add_file','theme_node_show_upload_file','theme_node_show_2'));
}else{
$this->removeMenus( array('theme_node_show_edit_newsletter'));
}
return true;
}
}