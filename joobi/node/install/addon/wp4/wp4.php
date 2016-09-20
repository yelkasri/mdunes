<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Install_Wp4_addon {
	public $apiVersion = 'wp4';
		public $client = 0;
	public $position = '';
	public $publish = 0;
	public $access = 1;
	var $path = '';
	var $tables = array();
	var $same;
	var $level = 0;
	var $languageFiles = array();
		protected $path_xml = '';
	public function getSubMenusList() {
		if ( empty( $this->yid ) ) {
			return false;
		}
				$namekey = WView::get( $this->yid, 'namekey' );
		$libraryViewMenusM = WModel::get( 'library.viewmenus', 'object' );
		$libraryViewMenusM->makeLJ( 'library.viewmenustrans', 'mid' );
		$libraryViewMenusM->whereLanguage();
		$libraryViewMenusM->rememberQuery( true );
		$libraryViewMenusM->whereE( 'yid', $this->yid );
		$libraryViewMenusM->where( 'type', '!=', 55 );
		$libraryViewMenusM->whereE( 'publish', 1 );
		$libraryViewMenusM->orderBy( 'ordering', 'DESC' );
		$libraryViewMenusM->setLimit( 500 );
		$list = $libraryViewMenusM->load( 'ol' );
		if ( empty($list) )  return false;
		return $list;
	}
	function application() {
		$extensionO = $this->init();
		if ( empty($extensionO) ) return false;
		$this->publish = 1;
		$namekeyA = explode( '.', $extensionO->namekey );
		$pluginSlup = $namekeyA[0];
		$version = $extensionO->version;
		$appsM = WModel::get('apps','object');
		$appsM->makeLJ( 'appstrans', 'wid' );
		$appsM->whereLanguage();
		$appsM->makeLJ( 'apps.info', 'wid' );
		$appsM->select( array( 'wid', 'version', 'lversion', 'folder', 'namekey', 'type', 'name' ) );
		$appsM->select( 'description', 1 );
		$appsM->select( array( 'userversion' ), 2 );
		$appsM->whereE( 'namekey', $extensionO->namekey );
		$extensionCompleteO = $appsM->load( 'o' );
		if ( !empty($extensionCompleteO) ) {
			foreach( $extensionCompleteO as $p => $v ) if ( empty($extensionO->$p) ) $extensionO->$p = $v;
			$extensionO->wid = $extensionCompleteO->wid;
		}
		$version = $extensionO->userversion;
		$description = $extensionO->description;
		$this->_createRouteFile( $extensionO, $pluginSlup, $version, $description );
				$this->_insertExtensionDB( $pluginSlup, $version );
		return true;
	}
	public function refreshMenus($namekey,$name='') {
	}
	public function module() {
				$extensionO = $this->init();
		if ( empty($extensionO) ) return false;
		WTools::getParams( $extensionO );
		$newWidgetSlug = $extensionO->namekey;
		$newWidgetName = $extensionO->name;
		$optionName = ( !empty($extensionO->client) ? '_admin_widgets' : '_site_widgets' );
		if ( !empty($newWidgetSlug) ) {
				$widgetsListA = get_option( JOOBI_PREFIX . $optionName );
				if ( !empty($widgetsListA) ) {
					$newA = $widgetsListA;
					foreach( $widgetsListA as $slug => $cName ) {
						if ( $slug == $newWidgetSlug ) continue;
						$newA[$newWidgetSlug] = $newWidgetName;
					}					update_option( JOOBI_PREFIX . $optionName, $newA );
				} else {
					update_option( JOOBI_PREFIX . $optionName, array( $newWidgetSlug => $newWidgetName ) );
				}
			}
		return true;
	}
	public function plugin() {
		return true;
	}
	function init() {
		static $namekey = null;
		static $extensionO = null;
		if ( !empty($this->tables) ) {
			if ( empty( $this->tables['extension_node'][0] ) ) return false;
			$myExtension = $this->tables['extension_node'][0];
						if ( $namekey != $myExtension->namekey ) {
				$namekey = $myExtension->namekey;
				$extensionO = $this->tables['extension_node'][0];
								if ( !empty($extensionO->params) ) {
					$this->_convertParameters( $extensionO->params );
				}
			}
			return $extensionO;
		} else {
						$this->_convertParameters( $this->params );
			return $this;
		}
	}
	public function setExtensionInfo($namekey) {
		$installAppsM = WTable::get( 'extension_node', 'main_library', 'wid' );
		$installAppsM->whereE( 'namekey', $namekey );
		$extension = $installAppsM->load( 'o' );
		if ( empty($extension) ) {
			return false;
		}
		$explodeNamekeyA = explode( '.', $namekey );
								$path = 'node' . DS . $explodeNamekeyA[0] . DS . $explodeNamekeyA[1];
		$this->path = JOOBI_DS_ROOT . JOOBI_FOLDER . DS . $path;
		$name = $extension->name;
		$namekeyExplodeA = explode( '.', $namekey );
		$extension->folder = $namekeyExplodeA[1];
				$this->tables['extension_node'][0] = $extension;
		$extension = new stdClass;
		$extension->description = $name;
		$this->tables['extension_trans'][0] = $extension;
	}
	private function _createRouteFile($extensionO,$pluginSlup,$version,$description) {
		$appURL = 'https://joobi.co?l=home_' . $pluginSlup . '.application';
		$phpcode = '<?php
/*
Plugin Name: ' . $extensionO->name . '
Plugin URI: ' . $appURL . '
Description: ' . $description . '
Author: Joobi
Author URI: https://joobi.co
Version: ' . $version . '
*/
/* START_OF_FILE */
if((defined("ABSPATH")) && !defined("JOOBI_SECURE")) define("JOOBI_SECURE",true);
defined("JOOBI_SECURE") or define( "JOOBI_SECURE", true );
register_activation_hook( __FILE__, "' . $pluginSlup . '_activate" );
register_deactivation_hook( __FILE__, "' . $pluginSlup . '_deactivate" );
add_action( "admin_init", "install_' . $pluginSlup . '" );
add_action( "template_redirect", "joobiIsPopUp" );
if(!empty( $_POST["page"])) $_GET["page"]=$_POST["page"];
function ' . $pluginSlup . '_pluginActionLinks_WP( $links ) {
return WApplication_wp4::renderFunction( "install",  "pluginActionLinks", array( "' . $pluginSlup . '", $links ) );
}
function ' . $pluginSlup . '_installNotice_WP() {
ob_start();
$html = WApplication_wp4::renderFunction( "install",  "installNotice", "' . $pluginSlup . '" );
ob_end_clean();
echo $html;
}
function ' . $pluginSlup . '_activate() {
if(version_compare(phpversion(),"5.3","<")){echo "PHP 5.3 is required for for the plugin to work!";exit;}
add_option( "' . $pluginSlup . 'Activated_Plugin", "' . $pluginSlup . '" );
}
function ' . $pluginSlup . '_deactivate() {
add_option( "' . $pluginSlup . 'DeActivated_Plugin", "' . $pluginSlup . '" );
}
function install_' . $pluginSlup . '() {
if( is_admin() ) {
if ( get_option( "' . $pluginSlup . 'Activated_Plugin" ) == "' . $pluginSlup . '" ) {
delete_option( "' . $pluginSlup . 'Activated_Plugin" );
include( dirname(__FILE__) . DIRECTORY_SEPARATOR . "install.php" );
$joobiInstaller = new install_joobi ;
if(class_exists( "install_joobi" )){
$joobiInstaller->setCMS("wordpress","' . $pluginSlup . '");
$joobiInstaller->installJoobi();
}
WApplication_wp4::renderFunction("install","install","' . $pluginSlup . '");
} elseif ( get_option( "' . $pluginSlup . 'DeActivated_Plugin" ) == "' . $pluginSlup . '" ) {
delete_option( "' . $pluginSlup . 'DeActivated_Plugin" );
WApplication_wp4::renderFunction("install","uninstall","' . $pluginSlup . '");
}
}
}
$joobiEntryPoint = __FILE__ ;
$status = @include(ABSPATH."joobi".DIRECTORY_SEPARATOR."entry.php");
';
		$path = WP_PLUGIN_DIR . DS . $pluginSlup . DS;
		$pathFull = $path . $pluginSlup . '.php';
		$fileS = WGet::file();
		$fileS->write( $pathFull, $phpcode, 'force' );
				$fileS->copy( dirname(__FILE__) . DS . 'tar.php', $path . 'tar.php', 'force' );
				$status = $fileS->copy( dirname(__FILE__) . DS . 'install.php', $path . 'install.php', 'force' );
		return true;
	}
	function getMainTag(){
		return 'extension';
	}
	function getIconLink($icon,$folder,$app=false) {
			return '../joobi/node/'.$folder.'/images/'.$icon.'.png';
	}
 	function getStandardName($type,$namekey){
 		switch($type){
 			case 'module':
 				$type = 'mod_';
 				break;
 			default;
 				$type = '';
 				break;
  		}
 				return $type . str_replace('.','_',$namekey);
 	}
	public function setUninstallFile($namekey) {
		return true;
		$status = true;
		$fileHandler = WGet::file();
		$uninstallfile = JOOBI_DS_NODE.'api' . DS . 'addon' . DS . $this->apiVersion . DS.'uninstall.php';
		$explodeNamekeyA = explode( '.', $namekey );
		$namekeyFCT = '_' . strtoupper( $explodeNamekeyA[0] );
		$status = $fileHandler->write( $this->path_uninstall, str_replace(array( '__NAMEKEY__', '__NAMEKEYFCT__', '__JOOBI__' ),array( $namekey, $namekeyFCT, JOOBI_FOLDER ),$fileHandler->read($uninstallfile)),'force');
		if ( $status ) {
			$uninstallfile = JOOBI_DS_NODE.'api' . DS . 'addon' . DS . $this->apiVersion . DS.'install.php';
			$explodeNamekeyA = explode( '.', $namekey );
			$namekeyFCT = '_' . strtoupper( $explodeNamekeyA[0] );
			$status = $fileHandler->write( $this->path_install, str_replace( array( '__NAMEKEY__', '__NAMEKEYFCT__', '__JOOBI__' ), array( $namekey, $namekeyFCT, JOOBI_FOLDER ), $fileHandler->read( $uninstallfile) ), 'force');
		}
		return $status;
	}
	protected function _insertExtensionDB($pluginSlup,$version) {
	$plugin = $pluginSlup . '/' . $pluginSlup . '.php';
		$network_wide = false;
	$current = get_site_option( 'active_sitewide_plugins', array() );
	if ( is_multisite() && ( $network_wide || is_network_only_plugin($plugin) ) ) {
		$network_wide = true;
		$current = get_site_option( 'active_sitewide_plugins', array() );
		$_GET['networkwide'] = 1; 	} else {
		$current = get_option( 'active_plugins', array() );
	}
	if ( $network_wide ) {
		$current[$plugin] = time();
		update_site_option( 'active_sitewide_plugins', $current );
	} else {
				if ( !in_array( $plugin, $current ) ) {
			$current[] = $plugin;
			sort($current);
			update_option( 'active_plugins', $current );
		}	}
	$transient_plugin_slugs = get_option( '_transient_plugin_slugs', array() );
		if ( !in_array( $plugin, $transient_plugin_slugs ) ) {
		$transient_plugin_slugs[] = $plugin;
		sort($transient_plugin_slugs);
		update_option( '_transient_plugin_slugs', $transient_plugin_slugs );
	}
		$site_transient_update_plugins = get_option( '_site_transient_update_plugins', array() );
	if ( !empty($site_transient_update_plugins->checked) ) {
		$checked = $site_transient_update_plugins->checked;
				if ( !in_array( $plugin, array_keys($checked) ) ) {
			$checked[$plugin] = $version;
			asort($checked);
			$site_transient_update_plugins->checked = $checked;
			update_option( '_site_transient_update_plugins', $site_transient_update_plugins );
		}	}
		return false;
	}
	protected function loadYIDforJoomlaMenus($app,$front=false) {
	}
	function getMenuTranslation($mid,$lgid=null,$info='name') {
	}
	protected function hideJcenterMenuIfOfflinePackage($app) {
return false;
				if ( $app == JOOBI_MAIN_APP && file_exists( JOOBI_DS_INSTALLFOLDER . 'lib_packages.txt' ) ) {
			return true;
		} else {
		}
		return false;
	}
	function getVersion($wid) {
		static $versionA =array();
		if (!isset($versionA[$wid])){
			$sql = WModel::get('apps.info');
			$sql->whereE('wid',$wid );
			$versionA[$wid] = $sql->load('lr','userversion');
		}
		return $versionA[$wid];
	}
	function getDescription($lgid,$wid) {
		static $descA =array();
		$key = $lgid . '-'.$wid;
		if ( !isset( $descA[$key] ) ) {
			$sql = WModel::get('appstrans');
			$sql->whereE('wid',$wid);
			$sql->whereE('lgid',$lgid);
			$descA[$key] = $sql->load('lr','description');
		}		return $descA[$key];
	}
	public static function magicFile($type='',$extension_name='') {
	}
	function setMagicFile($file='',$type='',$extension_name=''){
		if (empty($file)){
			if (!isset($this->path_php) || empty($this->path_php)){
				return true;
			}			else{
				$file =& $this->path_php;
			}		}
		if (is_array($file)){
			foreach($file as $f){
				if (!$this->setMagicFile($f,$type,$extension_name)){
					return false;
				}			}			return true;
		}
		$content = self::magicFile($type,$extension_name);
		$filehandler = WGet::file();
		return $filehandler->write($file,$content,'force');
	}
	function getParams(){
		$string_params ='';
				if (@is_array($this->content) && array_key_exists(0,$this->content) && array_key_exists('children',$this->content[0])){
			foreach($this->content[0]['children'] as $child){
				if ($child['nodename'] == 'administration' && array_key_exists('children',$child) && count($child['children'])>0){
					foreach($child['children'] as $subchild){
						if ($subchild['nodename'] == 'params' && array_key_exists('children',$subchild) && count($subchild['children'])>0){
							$string_params.= $this->_getParamsXML($subchild);
						}					}				}				elseif ($child['nodename'] == 'params' && array_key_exists('children',$child) && count($child['children'])>0){
					$string_params.= $this->_getParamsXML($child);
				}			}		}
				rtrim( $string_params, "\n" );
		return $string_params;
	}
	private function _convertParameters($allParams) {
		$myParams = explode( "\n", $allParams );
				if ( !empty($myParams) ) {
			foreach( $myParams as $myParam ) {
				if ( empty($myParam  ) ) continue;
				$position = strpos( $myParam, '=');
				if ($position === false) continue;
				$propertyName = substr( $myParam, 0, $position );
								$this->$propertyName = trim(substr( $myParam, $position+1 ) );
			}		}
	}
	private function _getParamsXML($subchild){
		$string_params = '';
		foreach($subchild['children'] as $param){
			if ($param['nodename'] == 'param'){
				if (count($param['attributes'])>0){
					if (isset($param['attributes']['name'])){
						$string_params.= $param['attributes']['name'];
						if (isset($param['attributes']['default'])){
							$string_params.= '=' . $param['attributes']['default'] . "\n";
						}					}					else{
						$message = WMessage::get();
						$notTRanslatedYet = 'The XML file has a "param" tag without a "name" attribute.';
						$message->userN($notTRanslatedYet);
					}				}				else{
					$message = WMessage::get();
					$notTRanslatedYet = 'The XML file has a "param" tag without attributes.';
					$message->userN($notTRanslatedYet);
				}			}			else{
				$message = WMessage::get();
				$notTRanslatedYet = 'The XML file has an unknown child tag in the "params" tag.';
				$message->userN($notTRanslatedYet);
			}		}
		return $string_params;
	}
}