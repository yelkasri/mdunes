<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
if ( !class_exists('Install_Common_class') ) {
class Install_Common_class {
	var $folder = '';
	var $destination = '';
	var $real_folder = '';
	var $level = 0;
	var $wid = 0;
	var $core = false;
	var $installUpdate = 0;
	var $auto = 0;
	public $namekey = '';	
	function process() {
self::logMessage( '--Extension name -- ' . $this->_getExtensionIdentifier(), 'install', false, 0, false );
self::logMessage( 'Process Data SQL file', 'install', false, 0, false);
WMessage::log( 'process Import data  _processDataSQLFiles 2', 'install' );
		if ( $this->_processDataSQLFiles() ) {
			return false;
		}
self::logMessage( 'Reset cache','install', false, 0, false);
		$cC = WCache::get();
		$cC->resetCache();
		$sql = WTable::get( 'extension_node', '', 'wid' );
		$sql->whereE( 'namekey', $this->namekey );
		$this->wid = $sql->load( 'lr', 'wid');
		return true;
	}
	function loadTypeDependentCustomFunction($process=false) {
		if (!isset($this->type)){
			return false;
		}
		static $types = array();
		if (!isset($types[$this->type])){
			$type = WType::get( 'apps.type' );
			if (!is_object($type)){
				return false;
			}
			$name = $type->getName($this->type);
			if (empty($name)){
				return false;
			}
			$types[$this->type] = 'type'.ucfirst($name);
		}
		$function = $types[$this->type];
		if (method_exists($this,$function)){
			if ($process){
				if (!$this->$function()){
					$extension = $this->_getExtensionIdentifier();
self::logMessage( '++An error occurred in the type method '.$function.' for the extension '.$extension,'install', false, 0, false );
				}
			}
			return true;
		}
		return false;
	}
	function publishEnglishLanguage($unpublishOther=false) {
		$languageM = WTable::get( 'language_node', 'main_userdata', 'lgid' );
		if ( ! $languageM->tableExist() || ! $languageM->isReady() ) $languageM = WTable::get( 'joobi_languages', 'main_userdata', 'lgid' );
 		$languageM->whereE( 'code', 'en' );
 		$languageM->update( array( 'publish' =>1 ) );
 		if ( $unpublishOther ) {
	 		$languageM->where( 'code','!=','en' );
	 		$languageM->update( array( 'publish'=>0 ) );
 		}
 	}
 	public function populatePreferences(){
 	}
 	public function populateEnglish() {
 		$obj =  new stdClass;
 		$obj->lgid = 1;
 		$obj->name = 'english';
 		$obj->code = 'en';
		$translationProcessC = WClass::get( 'translation.process' );
		$translationProcessC->setDontForceInsert( true );
		$translationProcessC->setTag( false );
		$translationProcessC->setStep( POPULATION_STEP );
		$translationProcessC->setFinishTag( false );
		$translationProcessC->setHandleMessage( false );
		$translationProcessC->setImportLang( array( $obj ) );
		$translationProcessC->importexec();
 	}
	public function analyzePackageName($package) {
		$attributes = array();
		$pieces = explode( DS, $package );
		$parts = explode( '_', $pieces[count( $pieces ) - 1] );
		$nb = count( $parts );
		if ( $parts[0] == 'lib' && $nb == 3 ) {
			$attributes['destination'] = '';
			$attributes['name'] = $parts[0];
			$attributes['pack_start'] = $attributes['name'].'_';
		} else {
			$attributes['destination'] = $parts[0];
			$namenb = $nb-2;
			$attributes['name'] = Install_Common_class::getName( $namenb, $parts );
			$attributes['pack_start'] = $attributes['destination'].'_'.$attributes['name'].'_';
		}
		$attributes['version'] = str_replace( '.tar.gz', '', $parts[ $nb - 1] );	
		return $attributes;
	}
	function getName($nb,$parts) {
		$name = '';
		for( $i = 1; $i < $nb; $i++ ) {
			$name.='_'.$parts[$i];
		}
		return substr( $name, 1 );
	}
	public static function writeFrameworkDefaultConfigFile() {
		$folder = JOOBI_DS_ROOT . JOOBI_FOLDER . DS;
		if ( defined('JOOBI_DS_JOOBI') ) $folder = JOOBI_DS_JOOBI;
		$path = $folder.'config.php';
		if ( !file_exists($path) ) {
			$content = '<?php
defined(\'JOOBI_SECURE\') or die(\'J....\');
class Joobi_Config{
public $model = array(
\'tablename\'=>\'model_node\'
);
public $table = array(
\'tablename\'=>\'dataset_tables\'
);
public $db = array(
\'tablename\'=>\'dataset_node\'
);
public $multiDB = false;
public $secret = \'' . WTools::randomString( 29, true ) . '\';
}//endclass';
			file_put_contents( $path, $content );
		}
	}
	function trigger(&$install_ext,&$install,$method='install'){
		if ( is_array( $method ) ){
			foreach( $method as $k => $v ) {
				if ( Install_Common_class::trigger( $install_ext, $install, $v ) ===false ){
					return false;
				}
			}
		} elseif ( $method=='version' ) {
			if ( Install_Common_class::trigger( Install_Common_class::getVersionMethod( $install_ext, $install->previous_version, $install->version ) ) === false ) {
				return false;
			}
		} elseif ( is_object( $install_ext ) ) {
			if ( method_exists( $install_ext, $method ) ) {
self::logMessage( 'triggering custom function ' . $method, 'install', false, 0, false );
				if ( $install_ext->$method( $install ) === false ) return false;
			} else {
self::logMessage( 'Method ' . $method . ' could not be found in the custom install object', 'install', false, 0, false);
			}
		} else {
self::logMessage( 'custom install object could not be loaded', 'install', false, 0, false );
		}
		return true;
	}
	public function getMethods(&$install_ext,&$install,$method='install') {
		if ( is_array($method) ) {
			$exist_methods = array();
			foreach( $method as $k => $v ) {
				$exist_method = Install_Common_class::getMethods( $install_ext, $install, $v);
				if ( is_array( $exist_method ) ){
					if ( !empty( $exist_method ) ){
						foreach( $exist_method as $em ){
							$exist_methods[] = $em;
						}
					}
				}elseif ( $exist_method ){
					$exist_methods[] = $exist_method;
				}
			}
			if (!empty($exist_methods)){
				return $exist_methods;
			}
		} elseif ( $method=='version' ) {
			$installedVersion = ( !empty($install->version) ? $install->version : 0 );
			$versions_methods = Install_Common_class::getVersionMethod( $install_ext, $install->previous_version, $installedVersion );
			return Install_Common_class::getMethods( $install_ext, $install, $versions_methods );
		} elseif ( is_object($install_ext) ) {
			if ( method_exists($install_ext, $method) ) {
				return $method;
			}
		}
		return false;
	}
	private static function getVersionMethod(&$install_ext,$previous_version,$current_version) {
		$version_methods = array();
		if (!is_object($install_ext)){
			return $version_methods;
		}
		$methodsA = get_class_methods( $install_ext );
		foreach( $methodsA as $method ) {
			if ( strpos( $method, 'version_' ) !==0 ) {
				continue;
			}
			$method_version = str_replace('_','.',str_replace('version_','',$method));
			if ( version_compare( $method_version, $previous_version, '<=' ) || version_compare( $method_version, $current_version, '>' ) ) {
				continue;
			}
			$version_methods[$method_version] = $method;
		}
		if ( count($version_methods)>1 ){
			uksort( $version_methods, 'version_compare' );
		}
		return $version_methods;
	}
	public function initCustom(&$install_ext) {
		require_once( JOOBI_DS_JOOBI . DS . 'node' . DS . 'install' . DS.'class' . DS . 'install.php' );
		$inst_file = $this->real_folder . DS . 'install' . DS . 'install.php';
		$proc = WGet::file();
		if ( !$proc->exist( $inst_file ) ) return true;
		$class_name = $this->getClassName();
		if ( !class_exists($class_name) )  require_once( $inst_file );
		if ( class_exists($class_name) ) {
			$install_ext = new $class_name();
		} else {
			return true;
		}
		return true;
	}
	function setFolder($folder){
		return $this->real_folder = rtrim($folder,DS);
	}
	function getClassName($type='install') {
		static $classNames = array();
		if (!isset($this->namekey)){
			$sql= WTable::get( 'extension_node','','wid');
			$sql->whereE('wid',$this->wid);
			$this->namekey = $sql->load('lr','namekey');
		}
		if (!isset($classNames[$this->namekey])){
			$parts = explode('.',$this->namekey);
			foreach($parts as $k => $part){
				$parts[$k] = ucfirst($part);
			}
			$classNames[$this->namekey] = implode('_',$parts);
		}
		return $classNames[$this->namekey].'_'.$type;
	}
	function typeWidget() {
		if ( isset($this->tables['data']) ) {
			$xml = WClass::get('install.xml');
			$xml->setParent('role',1);
			$xml->setParent('languages',1);
			$xml->importData($this->tables['data']);
			if ($this->newInstall){
				$model = WTable::get( 'extension_node', 'main_library', 'wid' );
				$model->whereE( 'namekey', $this->namekey );
				$this->wid = $model->load( 'lr', 'wid' );
				$handler = WClass::get('apps.widgets');
				$handler->publish( $this->wid , true );
			}
		}
		return true;
	}
	public static function logMessage($message,$location='',$not1=null,$not2=null,$not3=null,$not4=null) {
		static $installDebug = null;
		if ( !isset($installDebug) ) {
			if ( !defined('PINSTALL_NODE_INSTALLDEBUG') ) {
				if ( class_exists( 'WPref') ) {
					WPref::get( 'install.node' );
				}
			}
			$installDebug = ( defined('PINSTALL_NODE_INSTALLDEBUG') ? PINSTALL_NODE_INSTALLDEBUG : false );
		}
		if ( $installDebug ) WMessage::log( $message, 'install' );	
	}
	private function _processDataSQLFiles() {
WMessage::log( 'process Import data  _processDataSQLFiles 9', 'install' );
		$dbHandler = WClass::get( 'install.database' );
		$folder = rtrim( $this->real_folder, DS ) . DS . 'database';
		$file = $folder . DS . 'data' . DS . $this->namekey . '_data_mysql.sql';
		$dbHandler->setResaveItemMoveFile();
		$status = $dbHandler->importFile( $file );
WMessage::log( 'process Import data  _processDataSQLFiles status: ' . $status, 'install' );
		return true;
	}
	private function _getExtensionIdentifier() {
		if ( !empty($this->folder) ) return $this->folder . ' (' . $this->destination . ') ';
		return $this->real_folder;
	}
}
}