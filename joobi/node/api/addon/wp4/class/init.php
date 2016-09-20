<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Api_Wp4_Init_class extends WClasses {
private $appName=null;
public function wpRun($appName=''){
static $onlyOnce=false;
$this->appName=(!empty($appName)?$appName : '');
if($onlyOnce ) return true;
$onlyOnce=true;
$this->_enqueueJS_AND_CSS();
if(!IS_ADMIN){
$this->_createShortCodes();
}else{
$settings=WGlobals::get('settings-updated');
if(!empty($settings)){
WApplication_wp4::renderFunction('install','createRewriteRules');
}
if( WRole::hasRole('manager')){
WApplication_wp4::createFunction( JOOBI_PREFIX.'_dashboard_widget','init','registerAdminWidgets');
add_action('wp_dashboard_setup', JOOBI_PREFIX.'_dashboard_widget');
}
WApplication_wp4::createFunction( JOOBI_PREFIX.'_button_popup','init','createPopupWidget');
add_action('edit_page_form', JOOBI_PREFIX.'_button_popup');
add_action('edit_form_advanced', JOOBI_PREFIX.'_button_popup');
WApplication_wp4::createFunction( JOOBI_PREFIX.'_button_widget','init','showButtonWidget');
add_action('media_buttons', JOOBI_PREFIX.'_button_widget');
WApplication_wp4::createFunction( JOOBI_PREFIX.'_save_one_page','init','saveOnePage');
add_action('save_post', JOOBI_PREFIX.'_save_one_page');
WApplication_wp4::createFunction( JOOBI_PREFIX.'_delete_one_page','init','deleteOnePage');
add_action('delete_post', JOOBI_PREFIX.'_delete_one_page', 10 );
$controller=WGlobals::get('controller');
if(!empty($controller)) wp_deregister_script('heartbeat');
WApplication_wp4::createFunction( JOOBI_PREFIX.'_upgrade_process','init','upgradeProcess');
add_action('upgrader_process_complete', JOOBI_PREFIX.'_upgrade_process', 10, 2 );
}
WApplication_wp4::createFunction( JOOBI_PREFIX.'_handle_ajax','init','handleAjax');
add_action('wp_ajax_'.'jbiwpx', JOOBI_PREFIX.'_handle_ajax');
$this->_userHooks();
$beforeHeader=WGlobals::get('bfrhead');
if(!empty($beforeHeader)) return $this->_executeBeforeHeader();
}
public function handleAjax(){
WGet::startApplication('application','', null );
}
public function wpLoadedPlugin($namekey){
$instance=WExtension::plugin($namekey );
if(!empty($instance))$instance->onAfterRoute();
}
public function wpShutDownPlugin($namekey){
$instance=WExtension::plugin($namekey );
if(!empty($instance))$instance->onAfterRender();
}
public function registerAdminWidgets(){
$widgetsListA=get_option( JOOBI_PREFIX.'_admin_widgets');
if(empty($widgetsListA)) return '';
foreach($widgetsListA as $slug=> $name){
$slug=str_replace('.','_',$slug );
$functionName=JOOBI_PREFIX.'_create_dashboard_widget_'.$slug;
WApplication_wp4::createFunction($functionName, 'init','createAdminWidgets',$slug );
wp_add_dashboard_widget( JOOBI_PREFIX.'_'.$slug, $name, $functionName );
}
}
public function createAdminWidgets($namekey){
if(empty($namekey)) return '';
$I=WAddon::get('api.'.JOOBI_FRAMEWORK.'.widget');
$extensionO=new stdClass;
$extensionO->namekey=str_replace('_','.',$namekey );
$moduleHTML='';
$params=new stdClass;
$params->widget_id=$namekey;
$modLayout=WExtension::module($extensionO->namekey, $params );
if($modLayout){
$modLayout->wid=WExtension::get($extensionO->namekey, 'wid');
$moduleHTML=$modLayout->make();
}
if(!empty($moduleHTML)){
WPage::addCSSFile('css/bootstrap.css');
echo $moduleHTML;
}
return '';
}
public function upgradeProcess($upgrader,$options){
if(isset($options['type']) && in_array($options['type'], array('plugin','theme'))){
$cacheC=WCache::get();
$cacheC->resetCache();
}
}
public function saveOnePage($post_id){
$cache=WCache::get();
$cache->resetCache('post_content');
$post=get_post($post_id, 'object');
if(!in_array($post->post_type, array('page')) ) return;
$permalinks=get_option( JOOBI_PREFIX.'_permalinks');
if(empty($permalinks)){
$permalinks=array();
}
$permalinks[$post_id]=$post->post_name;
update_option( JOOBI_PREFIX.'_permalinks',$permalinks );
$joobiPage=null;
$shortCode=joobiGetShortCodeFromPage($post->post_content, $joobiPage );
if(empty($shortCode)) return;
$specialOption='joobipg|'.str_replace('__','|',$shortCode );
update_option($specialOption, $post_id );
WApplication_wp4::renderFunction('install','createRewriteRules');
}
public function deleteOnePage($post_id){
$post=get_post($post_id, 'object');
if(!in_array($post->post_type, array('page')) ) return;
$permalinks=get_option( JOOBI_PREFIX.'_permalinks');
if(empty($permalinks)){
return;
}
unset($permalinks[$post_id] );
update_option( JOOBI_PREFIX.'_permalinks',$permalinks );
WApplication_wp4::renderFunction('install','createRewriteRules');
}
public function createPopupWidget(){
echo JoobiWP::renderPopUpButton();
$html='<div id="wzpOpUp" class="modal fade">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-body">
<iframe width="99.6%" height="400px" frameborder="0" src=""></iframe>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times-circle"></i>'.WText::t('1228820287MBVC'). '</button>
</div>
</div>
</div>
</div>';
echo $html;
}
public function showButtonWidget($editor_id){
static $instance=0;
$instance++;
$pagePHP=WGlobals::get('PHP_SELF','','server');
if( strpos($pagePHP, 'post-new.php') !==false){
$post_type=WGlobals::get('post_type');
if(!empty($post_type) && $post_type !='page') return false;
}elseif( strpos($pagePHP, 'post.php') !==false){
$id=WGlobals::get('post');
$post=get_post($id, 'object');
if(!in_array($post->post_type, array('post','page')) ) return false;
}
WPage::addCSSFile('fonts/app/css/app.css');
$css='.joobi-media{background-color:#2BA3D4 !important;color:white !important;} .joobi-media i{padding:5px}';
echo  WGet::$rLine.'<style type="text/css" media="screen">'.$css.'</style>'.WGet::$rLine;
echo JoobiWP::renderCSS();
$js="function insertWidget(name,tag){";
$js .="window.send_to_editor('['+name+' id=\"'+tag+'\"]');";
$js .="jQuery('#wzpOpUp').modal('toggle');";
$js .="}";
echo WGet::$rLine.'<script type="text/javascript">'.$js.'</script>'.WGet::$rLine;
$link='admin.php?page='.JOOBI_MAIN_APP.'&controller=main-widgets-tag&isPopUp=true'.URL_NO_FRAMEWORK;
$link=str_replace('&','&amp;',$link );
$img='<i class="fa app-joobi-logo"></i>';
$linkExtra=' data-pheight="75" data-pwidth="80" data-toggle="modal" data-target="#wzpOpUp" href="'.$link.'"';
$id_attribute=$instance===1?' id="joobi-media-button"' : ' id="joobi-media-button'.$instance.'"';
printf('<a '.$linkExtra.' %s class="button add_media joobi-media" data-editor="%s" title="%s">%s</a>',
$id_attribute,
esc_attr($editor_id ),
esc_attr__('Use this button to insert any Joobi Widgets'),
$img . WText::t('1426644982FUIK')
);
}
public function registerSiteWidgets(){
if( WRoles::isAdmin('manager')
&& ( strpos( WGlobals::get('PHP_SELF','','server'), 'widgets.php') !==false
|| strpos( WGlobals::get('PHP_SELF','','server'), 'customize.php') !==false)
){
$html='<div id="wzpOpUp" class="modal fade">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-body">
<iframe width="99.6%" height="400px" frameborder="0" src=""></iframe>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times-circle"></i>'.WText::t('1228820287MBVC'). '</button>
</div>
</div>
</div>
</div>';
echo $html;
}
$allWidgetsA=get_option( JOOBI_PREFIX.'_site_widgets');
if(empty($allWidgetsA)) return;
foreach($allWidgetsA as $oneWidget=> $widgetName){
$className=$this->_createWidgetClass($oneWidget, $widgetName );
register_widget($className );
}
}
public function addEarlyCSS(){
if(!empty( APIPage::$cssFileA )){
foreach( APIPage::$cssFileA as $name=> $header){
wp_enqueue_style($name, $header );
}
}
APIPage::$cssLoaded=true;
}
public function registerPlugins(){
$allWidgetsA=$this->_loadExtensionsA();
if(empty($allWidgetsA)) return true;
foreach($allWidgetsA as $oneWidget){
if($oneWidget->type !=50 ) continue;
$namekeyA=explode('.',$oneWidget->namekey );
$type=$namekeyA[1];
$functionNamekey=str_replace('.','_',$oneWidget->namekey );
$functionName='_'.$functionNamekey;
switch($type){
case 'system':
$instance=WExtension::plugin($oneWidget->namekey );
if(!empty($instance))$instance->onAfterInitialise();
add_action('wp_loaded', JOOBI_PREFIX . $functionName.'_system_load');
WApplication_wp4::createFunction( JOOBI_PREFIX . $functionName.'_system_load','init','wpLoadedPlugin',$oneWidget->namekey );
add_action('shutdown', JOOBI_PREFIX . $functionName.'_system_shutdown');
WApplication_wp4::createFunction( JOOBI_PREFIX . $functionName.'_system_shutdown','init','wpShutDownPlugin',$oneWidget->namekey );
add_action('wp_login', JOOBI_PREFIX . $functionName.'_users_login', 10, 2 );
WApplication_wp4::createFunction( JOOBI_PREFIX . $functionName.'_users_login','users','loginUserPlugin',$oneWidget->namekey );
add_action('wp_logout', JOOBI_PREFIX . $functionName.'_users_logout');
WApplication_wp4::createFunction( JOOBI_PREFIX . $functionName.'_users_logout','users','logoutUserPlugin',$oneWidget->namekey );
add_action('wp_login_failed', JOOBI_PREFIX . $functionName.'_users_login_failed', 10, 1 );
WApplication_wp4::createFunction( JOOBI_PREFIX . $functionName.'_users_login_failed','users','loginUserFailedPlugin',$oneWidget->namekey );
break;
case 'user':
add_action('user_register', JOOBI_PREFIX . $functionName.'_users_add', 10, 1 );
WApplication_wp4::createFunction( JOOBI_PREFIX . $functionName.'_users_add','users','addUserPlugin',$oneWidget->namekey );
add_action('profile_update', JOOBI_PREFIX . $functionName.'_users_edit', 10, 2 );
WApplication_wp4::createFunction( JOOBI_PREFIX . $functionName.'_users_edit','users','editUserPlugin',$oneWidget->namekey );
add_action('delete_user', JOOBI_PREFIX . $functionName.'_users_delete');
WApplication_wp4::createFunction( JOOBI_PREFIX . $functionName.'_users_delete','users','deleteUserPlugin',$oneWidget->namekey );
add_action('wp_login', JOOBI_PREFIX . $functionName.'_users_login', 10, 2 );
WApplication_wp4::createFunction( JOOBI_PREFIX . $functionName.'_users_login','users','loginUserPlugin',$oneWidget->namekey );
add_action('wp_logout', JOOBI_PREFIX . $functionName.'_users_logout');
WApplication_wp4::createFunction( JOOBI_PREFIX . $functionName.'_users_logout','users','logoutUserPlugin',$oneWidget->namekey );
add_action('wp_login_failed', JOOBI_PREFIX . $functionName.'_users_login_failed', 10, 1 );
WApplication_wp4::createFunction( JOOBI_PREFIX . $functionName.'_users_login_failed','users','loginUserFailedPlugin',$oneWidget->namekey );
break;
default:
continue;
break;
}
}
}
private function _loadExtensionsA(){
static $allWidgetsA=null;
if(isset($allWidgetsA)) return $allWidgetsA;
$installAppsA=WModel::get('apps', null, false);
if(empty($installAppsA)) return false;
$installAppsA->makeLJ('appstrans','wid');
$installAppsA->whereLanguage();
$installAppsA->whereIn('type',array( 50 ));
$installAppsA->whereE('publish', 1 );
$installAppsA->select( array('description'), 1 );
$installAppsA->select( array('name','namekey','type'));
$installAppsA->remember('all_wordpress_widget_plugins', true);
$allWidgetsA=$installAppsA->load('ol');
return $allWidgetsA;
}
private function _executeBeforeHeader(){
$page=WApplication::getApp();
$content=JoobiWP::slugToApp('page_'.$page );
echo $content;
exit;
}
private function _createWidgetClass($namekey,$name){
$namekey=WGlobals::filter($namekey );
$name=WGlobals::filter($name );
$cleanNameKey=str_replace('.','_',$namekey );
$className=JOOBI_PREFIX.'_'.$cleanNameKey;
$description=WModel::getElementData('apps',$namekey, 'description');
$classCode='class '.$className .' extends WP_Widget{
public function __construct(){
$namekey="'.$className.'";
$name="'.$name.'";
$optionsA=array("classname"=>"'.$className.'","description"=>"'.$description.'");
parent::__construct($namekey,$name,$optionsA);
$this->settings=array(
"title"=>array(
"type"=>"text",
"std"=>"'.$name.'",
"label"=>"'.WText::t('1206732412DAGC') .'"),
"link"=>array(
"type"=>"link",
"std"=>"'.$name.'",
"label"=>"'.WText::t('1416248538ILNE') .'")
);
}
public function widget($a,$i){
$I=WAddon::get("api.".JOOBI_FRAMEWORK.".widget");
echo $I->renderWidget($a,$i,$this->id_base);
}
public function form($instance){
if(empty($this->settings)) return "";
foreach($this->settings as $k=>$S){
$value=isset($instance[$k])?$instance[$k]:$S["std"];
switch($S["type"]){
case "text":
?><p>
<label for="<?php echo $this->get_field_id($k); ?>"><?php echo $S["label"]; ?></label>
<input class="widefat" id="<?php echo esc_attr($this->get_field_id($k)); ?>" name="<?php echo $this->get_field_name($k); ?>" type="text" value="<?php echo esc_attr($value ); ?>" />
</p><?php
break;
case "link":
$l=WPages::linkPopUp("controller=main-widgets-preference&task=edit&id=".$this->id."&title=".esc_attr($value ));
$HTML=WPage::createPopUpLink($l,$S["label"],"80%","80%","button-primary",esc_attr($this->get_field_id($k)),$S["label"]);
$pH=JoobiWP::renderWidget();
echo $pH;
?>
<p><?php echo $HTML; ?></p>
<?php
break;
}
}
}
}
';
eval($classCode );
return $className;
}
private function _userHooks(){
add_action('user_register', JOOBI_PREFIX.'_users_add', 10, 1 );
WApplication_wp4::createFunction( JOOBI_PREFIX.'_users_add','users','addUser');
add_action('profile_update', JOOBI_PREFIX.'_users_edit', 10, 2 );
WApplication_wp4::createFunction( JOOBI_PREFIX.'_users_edit','users','editUser');
add_action('delete_user', JOOBI_PREFIX.'_users_delete');
WApplication_wp4::createFunction( JOOBI_PREFIX.'_users_delete','users','deleteUser');
}
private function _enqueueJS_AND_CSS(){
static $onlyOnce=true;
if($onlyOnce){
$onlyOnce=false;
add_action('wp_loaded', JOOBI_PREFIX.'_add_early_CSS');
WApplication_wp4::createFunction( JOOBI_PREFIX.'_add_early_CSS','init','addEarlyCSS');
}
}
private function _createShortCodes(){
WApplication_wp4::createFunction( JOOBI_PREFIX.'_joobipage_shortcode','shortcode','joobipage');
add_shortcode('joobipage', JOOBI_PREFIX.'_joobipage_shortcode');
WApplication_wp4::createFunction( JOOBI_PREFIX.'_joobiwidget_shortcode','shortcode','joobiwidget');
add_shortcode('joobiwidget', JOOBI_PREFIX.'_joobiwidget_shortcode');
return true;
}
}
class WPlugin extends WObj {
public function __construct($path=''){
}
function onAfterInitialise(){
}
function onAfterRoute(){
}
function onAfterRender(){
}
function onUserAuthenticate($a,$b,$c){
return true;
}
function onUserBeforeSave($a,$b,$c){
return true;
}
function onUserAfterSave($a,$b,$c,$d){
return true;
}
function onUserBeforeDelete($a){
return true;
}
function onUserAfterDelete($a,$b,$c){
return true;
}
function onUserLogin($a,$b){
return true;
}
function onUserLogout($a,$b){
return true;
}
function onUserLoginFailure($m){
return true;
}
function onUserBeforeDeleteGroup($a){
return true;
}
function onUserAfterDeleteGroup($a,$b,$c){
return true;
}
function onUserBeforeSaveGroup($a){
return true;
}
function onUserAfterSaveGroup($a){
return true;
}
function onDisplay($a){
return null;
}
}