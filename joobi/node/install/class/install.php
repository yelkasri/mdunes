<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Install_Install_class {
	public function install($old_param=null) {
		$installProcessC = WClass::get('install.process');
		return $installProcessC->instup();
	}
	public function checkServerAvail($server,$force) {
		$installProcessC = WClass::get( 'install.process' );
		return $installProcessC->checkServerAvail( $server, $force );
	}
	function processPackagesName() {
	}
	public function getModuleInitialParams($namekey) {
		$extensionO = WExtension::get( $namekey, 'data' );
		if ( empty($extensionO) || empty($extensionO->widgetView) ) return '';
		$sid = WView::get( $extensionO->widgetView, 'sid', null, false );
		$viewFormM = WModel::get( 'library.viewforms' );
		$viewFormM->makeLJ( 'library.view', 'yid' );
		$viewFormM->whereE( 'sid', $sid, 1 );
		$viewFormM->where( 'initial', '!=', '' );
		$allItianalParamsA = $viewFormM->load( 'ol', array( 'initial', 'map' ) );
		if ( empty($allItianalParamsA) ) return '';
		$finalParamsA = array();
		foreach( $allItianalParamsA as $oneInitial ) {
			$cleanMap = substr( $oneInitial->map, 2, -1 );
			if ( !empty($cleanMap) ) $finalParamsA[] = $cleanMap . '=' . $oneInitial->initial;
		}
		return implode( "\n", $finalParamsA );
	}
}
class WInstall {
	public $newInstall = false;
	public static function get($path,$showMessage=true) {
		$pathA = explode( '_', $path );
		$myFuntion = $pathA[0];
		$exists = WLoadFile( $myFuntion . '.install.install', JOOBI_DS_NODE, true, false );
		if ( !$exists ) {
			$tmp = null;
			return $tmp;
		}
		$className = ucfirst( $pathA[0] ) . '_' . ucfirst( $pathA[1] ) . '_install';
		if ( !empty($className) && class_exists( $className ) ) {
			$newClass = new $className();
			$newClass->node = $myFuntion;
		} else {
			$tmp = null;
			return $tmp;
		}
		return $newClass;
	}
	public function addWidgets() {
		return array();
	}
	protected function insertNewExtension($extension) {
		$appsM = WTable::get( 'extension_node', 'main_library', 'wid' );
		$appsM->whereE( 'namekey', $extension->namekey );
		$exist = $appsM->exist();
		if ( $exist ) {
			return false;
		} else {
			if ( isset($extension->description) ) {
				$description = $extension->description;
				unset($extension->description);
			}
			foreach( $extension as $prop => $oneExt ) {
				$appsM->setVal( $prop, $oneExt );
			}
			return $appsM->insertIgnore();
		}
	}
	protected function installExtension($namekey) {	
		if ( empty($namekey) ) return false;
		$class = 'Install_' . JOOBI_FRAMEWORK . '_addon' ;
		if ( !class_exists($class) ) $addon = WAddon::get( 'install.' . JOOBI_FRAMEWORK );
		else $addon = new $class();
		if ( is_object($addon) ) {
			$namekeyExplodeA = explode( '.', $namekey );
			$type = $namekeyExplodeA[2];
			$type = str_replace( ' ', '', strtolower( $type ) );
			if ( method_exists( $addon, $type ) ) {
				$addon->setExtensionInfo( $namekey );	
WMessage::log( 'Triggering cms specific function for the ' . $type .' and extension: ' . $namekey, 'install' );
				if ( ! $addon->$type() ) {
				}
			} else {
			}
		}
		return false;
	}
}