<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Wp4_Helper_addon {
public function createMenu($name,$menuParent,$link,$option,$client=1,$access=0,$level=0,$ordering=0,$param=null){
if( in_array($menuParent, array('top','mainmenu','usermenu')) ) return true;
if('wordpress'==JOOBI_FRAMEWORK_TYPE){
if( strpos($link, '&eid=') !==false || strpos($link, '&type=') !==false) return true;
}
$namekey=JoobiWP::linkToSlug($link, $option );
$content='['.JOOBI_PREFIX.'page id="'.apply_filters($namekey.'_shortcode_tag',$namekey ). '"]';
if(!empty($menuParent)){
$postParent=apply_filters($menuParent, get_option($menuParent ));
}else{
$postParent='';
}
$optionWP=$this->_createPage($link, $option, $name, $content, $postParent );
flush_rewrite_rules();
return $optionWP;
}
private function _createPage($link,$option='',$page_title='',$page_content='',$postParent=0){
global $wpdb;
$postStatus='publish';
$linkA=explode('&',$link );
if(!empty($linkA)){
foreach($linkA as $onel){
$onelA=explode('=',$onel );
if('controller'==$onelA[0]){
$nodeA=explode('-',$onelA[1] );
$node=$nodeA[0].'.node';
if(!WExtension::exist($node )){
$node=$nodeA[0].'.application';
if(!WExtension::exist($node )){
$postStatus='draft';
}
}
break;
}}}
$optionWP=JoobiWP::linkToOption($link, strtolower($option));
$option_value=get_option($optionWP );
if($option_value > 0 && get_post($option_value )){
return -1;
}
$page_found=null;
$slug=str_replace( array('controller=','&task=','&','='), array('','_','_','_'), $link );
if( substr($slug, 0, 1 )=='j')$slug=substr($slug, 1 );
if( strlen($page_content ) > 0){
$page_found=$wpdb->get_var($wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_type='page' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ));
}else{
$page_found=$wpdb->get_var($wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_type='page' AND post_name=%s LIMIT 1;", $slug ));
}
if($page_found){
if(!$option_value )
update_option($optionWP, $page_found );
return $optionWP;
}
$author=WUser::get('id');
$page_data=array(
'post_status'=> $postStatus,
'post_type'=> 'page',
'post_author'=> $author,
'post_name'=> $slug,
'post_title'=> $page_title,
'post_content'=> $page_content,
'post_parent'=> $postParent,
'comment_status'=> 'closed'
);
$page_id=wp_insert_post($page_data );
if(!empty($postParent)){
$permalinks=get_option( JOOBI_PREFIX.'_permalinks');
if(empty($permalinks)){
$permalinks=array();
}$permalinks[$page_id]=$slug;
update_option( JOOBI_PREFIX.'_permalinks',$permalinks );
}
if($optionWP ) update_option($optionWP, $page_id );
return $optionWP;
}
}