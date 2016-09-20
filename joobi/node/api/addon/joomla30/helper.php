<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Joomla30_Helper_addon {
public function createMenu($name,$menuParent,$link,$option,$client=1,$access=0,$level=0,$ordering=0,$param=null){
$addonI=WAddon::get('install.joomla30');
$exist=$addonI->menuExist($name, $menuParent );
if(!empty($exist)) return false;
$alias=strtolower( str_replace(' ','-',$name));
$vars=array(
'title'=> $name,
'menutype'=> $menuParent,
'client_id'=> $client,
'access'=> $access,
'level'=> $level,
'ordering'=> $ordering,
'alias'=> $alias,
'path'=> $alias,
'link'=>'index.php?option=com_'. $option .'&view='.strtolower( str_replace(' ','_',$name)).'&'. $link,
'img'=>''
);
if($param=='frontmenu'){
$vars['params']='{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}';
}
if(empty($vars['component_id'] )){
$ext=WApplication::getComponents($option, 'id');
if(!empty($ext) && !empty($ext[0]->id))$vars['component_id']=$ext[0]->id;
else $vars['component_id']=0;
}
$this->parent=$addonI->addMenu($vars, 1, $option );
}
}