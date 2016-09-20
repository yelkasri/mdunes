<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or define( 'JOOBI_SECURE', true );
if( !defined('DS') ) define('DS', DIRECTORY_SEPARATOR);
if ( !defined('JOOBI_FRAMEWORK') ) {
		require_once( dirname(__FILE__) . DS . 'discover.php' );
	WDiscoverEntry::discover();
	if (!defined('JOOBI_FRAMEWORK')) {
		echo 'The entry point file did not provide a CMS name. Please contact the support.';
		exit();
	}}
if (!defined('JOOBI_FOLDER')) define('JOOBI_FOLDER', 'joobi' );
if (!defined('JOOBI_DS_ROOT')) define( 'JOOBI_DS_ROOT', dirname(dirname((__FILE__))) . DS );
if (!defined('JOOBI_DS_CONFIG')) define( 'JOOBI_DS_CONFIG', dirname(__FILE__) . DS );
if ( !defined('JOOBI_DS_NODE') ) define( 'JOOBI_DS_NODE' , dirname(__FILE__) . DS .'node' . DS ); if ( !defined('JOOBI_LIB_CORE') ) define( 'JOOBI_LIB_CORE' , JOOBI_DS_NODE . 'library' . DS ); 
require_once( JOOBI_DS_NODE . 'api' . DS . 'addon' . DS . JOOBI_FRAMEWORK . DS . JOOBI_FRAMEWORK . '.php' );
if ( defined( 'IS_ADMIN' ) && IS_ADMIN && !empty($_SESSION['joobi']['installwithminilib']) ) {
	if ( !empty($_GET['stopinstall']) ) {
		unset($_GET['stopinstall']);
		unset($_SESSION['joobi']['install_status']);
		unset($_SESSION['joobi']['installwithminilib']);
	} else {
		require_once( JOOBI_LIB_CORE . 'define.php' );
		require_once( JOOBI_DS_NODE . 'api' . DS . 'addon' . DS . JOOBI_FRAMEWORK . DS . 'api.php' );
		$process = WClass::get('install.process');
		$process->instup();
	}
}
$appName = 'WApplication_'. JOOBI_FRAMEWORK;
if ( class_exists($appName) ) {
	if ( !isset($params) ) $params = null;
		if ( isset($module) ) $params->module = $module;
		$app = new $appName();
		if ( !isset($joobiEntryPoint) ) $joobiEntryPoint='';
		$html = $app->make( $joobiEntryPoint, $params );
} else {
	echo 'JOOBI ERROR 56399. Please contact support.';		exit;
}
if ( !empty($html)) echo $html;
