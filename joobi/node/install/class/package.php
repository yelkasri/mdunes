<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Install_Package_class {
	var $errors = array();
	public $installUpdate = 1; 		public $newInstall = true;
	public $level = 0; 	var $auto = 3;
	public $main = true;
	public $previous_version = 0;
	public $packages = null;
	public $parent = null;
	public $list = null;
	public $packageId = 0;
	public $temp_folder = '';
	public static function installFilesFolder() {
		static $folder = null;
		if ( isset($folder) ) return $folder;
		$fileS = WGet::file();
		$systemFolderC = WGet::folder();
		if ( $fileS->exist( JOOBI_DS_NODE . 'install' . DS . 'class' . DS . 'processnew.php' )
		|| $systemFolderC->exist( JOOBI_DS_USER . 'installfiles' ) ) {
			$folder = JOOBI_DS_USER . 'installfiles';
		} else {
			$folder = JOOBI_DS_JOOBI . 'installfiles';
		}
self::logMessage( 'Defining installFilesFolder in Install_Package_class : ' . $folder, 'install' );
		return $folder;
	}
	public function processPackagesName() {
		$installCommonC = WClass::get( 'install.common' );
				$this->formated_packages = array();
		foreach( $this->packages as $k => $package ) {
			if ( is_array($package) ) {
				$package = $package[0];
			}			$this->formated_packages[] = $installCommonC->analyzePackageName( $package );
		}
	}
	public function packageName($package) {
		if (is_array($package)){
			$package=$package[0];
		}		$pieces = explode(DS,$package);
		$name = $pieces[count($pieces)-1];
		return $name;
	}
	public function loadXmlAndCheckDependencies() {
		if ( !$this->loadXml() ) return false;
		if ( $this->_checkFirstTimeInstalled() ) {
			if ( !$this->_checkDependencies() ) {
					return false;
				}			}
		return true;
	}
	public function loadXml() {
				$this->tables = $this->xml( $this->temp_folder );
self::logMessage( 'XML file loaded:' . $this->temp_folder , 'install' );
		if ( !$this->checkXmlData() ) return false;
		$this->ext =& $this->tables['extension_node'];
		$this->mainext =& $this->ext[0];
		$this->install_ext = new stdClass;
		$this->old_wid = $this->mainext->wid;
		$this->ext_version =& $this->tables['extension_version'][0];
		$this->current_version = $this->ext_version->version;
		$this->version = $this->current_version;
		return true;
	}
	public function checkXmlData() {
		if ( !is_array($this->tables) ) {
			return false;
		}
						if ( !array_key_exists( 'extension_node',$this->tables) || count($this->tables['extension_node'])<1 ) {
self::logMessage( 'The extension_node.xml file is missing in the folder '.$this->temp_folder,'install', false, 0, false );
				return false;
			}
			if ( !array_key_exists('extension_version',$this->tables) || count($this->tables['extension_version'])<1){
self::logMessage( 'The extension_version.xml file is missing in the folder '.$this->temp_folder, 'install', false, 0, false );
				return false;
			}
			return true;
	}
	function getOldVersion() {
		if ( empty($this->mainext->namekey) ) return 0;
		$extensionT = WTable::get( 'extension_node');
		$extensionT->whereE('namekey',$this->mainext->namekey);
		$extensionT->whereE('publish',1);
		return $extensionT->load('lr','version');
	}
	function triggerPreInstall(){
		if (!$this->initCustomInstance()){
			return false;
		}
self::logMessage( 'Trigger preinstall in triggerPreInstall() ','install', false, 0, false );
				if ( !$this->common->trigger( $this->install_ext, $this, 'preinstall') ) {
			return false;
		}self::logMessage( 'Pre-install triggered in triggerPreInstall() ','install', false, 0, false );
		return true;
	}
	public function initCustomInstance() {
self::logMessage( 'Initializing custom functions', 'install', false, 0, false );
self::logMessage( 'temp folder: ' . $this->temp_folder, 'install', false, 0, false );
		if ( isset($this->common) && is_object($this->common) ) {
			return true;
		}
		$this->common = WClass::get('install.common');
		$this->common->tables =& $this->tables;
		$this->common->setFolder( $this->temp_folder );
		$this->common->namekey = ( isset($this->mainext->namekey) ? $this->mainext->namekey : '' );
		if ( !$this->common->initCustom( $this->install_ext ) ) {
			return false;
		}
		if ( !isset($this->install_ext) ) $this->install_ext = new stdClass;
		$this->install_ext->newInstall = $this->newInstall;
self::logMessage( 'Custom functions initialized', 'install', false, 0, false );
		return true;
	}
	function getDestination($full=true) {
		if ( empty($this->mainext->folder) ) return '';
		$path = ($this->mainext->destination!= '' ? str_replace('|',DS,$this->mainext->destination).DS:'').$this->mainext->folder;
		if ($full){
			return JOOBI_DS_ROOT.JOOBI_FOLDER . DS.$path;
		}
		return $path;
	}
	function process() {
				if ( !$this->initCustomInstance() ) {
			return false;
		}
self::logMessage( 'Main install process started for ' . strtoupper(@$this->mainext->namekey) , 'install' );
				if ( empty($this->mainext) || empty($this->mainext->folder) || empty($this->mainext->namekey) ) {
self::logMessage( 'ERROR: missign data : ' . print_r(@$this->mainext) , 'install' );
			return true;
		}
		$this->common->folder = $this->mainext->folder;
		$this->common->destination = $this->mainext->destination;
		$this->common->namekey = $this->mainext->namekey;
		$this->common->type = $this->mainext->type;
		$this->common->level = $this->level;
		$this->common->core = false;
		$this->common->installUpdate = $this->installUpdate;
		$this->common->newInstall = $this->newInstall;
		$this->common->auto = $this->auto;
				$status = $this->common->process();
		$this->wid = $this->common->wid;
		$this->namekey = $this->common->namekey;
self::logMessage( 'Process finished for ' . strtoupper(@$this->mainext->namekey) , 'install' );
		return $status;
	}
	function triggerPostInstall(){
				$methods = $this->_getCustomFunctions();
self::logMessage( 'starting triggers for methods '.implode(',',$methods),'install', false, 0, false );
		if ( !$this->common->trigger( $this->install_ext, $this, $methods ) ) {
			return false;
		}
self::logMessage( 'triggers finished','install', false, 0, false );
		return true;
	}
	function customInstallMethods() {
		$custom = $this->_getCustomFunctions();
		$found = $this->common->getMethods( $this->install_ext, $this, $custom );
		return $found;
	}
	public function triggerMethods($methods) {
		if ( !$this->initCustomInstance() ) {
			return false;
		}
		self::logMessage( 'starting triggers for methods','install' );
self::logMessage( $methods ,'install' );
		if ( ! $this->common->trigger( $this->install_ext, $this, $methods ) ) {
			return false;
		}
self::logMessage( 'triggers finished','install', false, 0, false );
		return true;
	}
	function typeCustomFunction(){
		if ( !$this->initCustomInstance() ) {
			return false;
		}
		$this->common->type = $this->mainext->type;
		$this->common->installUpdate = $this->installUpdate;
		$this->common->newInstall = $this->newInstall;
self::logMessage( 'starting load Type Dependent Custom Function','install', false, 0, false );
		$this->common->loadTypeDependentCustomFunction( true );
		return true;
	}
	function hasCustomTypeFunction() {
		if ( ! $this->initCustomInstance() ) {
			return false;
		}
		$this->common->type = ( isset($this->mainext->type) ? $this->mainext->type : 0 );
self::logMessage( 'starting load Type Dependent Custom Function', 'install' );
		return $this->common->loadTypeDependentCustomFunction();
	}
	function hasCMSFunction() {
		if ( empty($this->mainext->type) ) return false;
				$wtype = WType::get( 'apps.type' );
		$addon = WAddon::get('install.'.JOOBI_FRAMEWORK);
		if (is_object($addon)){
self::logMessage( 'hasCMSFunction: cms addon loaded', 'install' );
			$type = str_replace(' ','', strtolower( $wtype->getName( $this->mainext->type) ) );
			if (method_exists($addon,$type)){
				return true;
			}		}		return false;
	}
	public function triggerCMS() {
				$addon = WAddon::get( 'install.' . JOOBI_FRAMEWORK );
		if ( is_object($addon) ) {
self::logMessage( 'triggerCMS: cms addon loaded', 'install' );
			$typeToUse = ( !empty($this->mainext->type) ? $this->mainext->type : 'application' );
			$wtype = WType::get( 'apps.type' );
			$type = str_replace( ' ', '', strtolower( $wtype->getName($typeToUse) ) );
			if ( 'node' == $type ) return true;
self::logMessage( 'Extension type: ' . $type , 'install' );
			if ( method_exists( $addon, $type ) ) {
				$addon->path = $this->getDestination();
				$addon->tables =& $this->tables;
self::logMessage( 'Triggering cms specific function for the ' . $type . ' ' . $this->mainext->namekey, 'install' );
				if ( ! $addon->$type() ) {
self::logMessage( 'ERROR: Triggering cms specific function', 'install' );
									} else {
self::logMessage( 'SUCCESS: Triggering cms specific function', 'install' );
				}
			}
		}
		return true;
	}
	function removeDependencyXml($temp_folder){
		if (isset($this->tables['extension_dependency'])){
			$file_handler = WGet::file();
			$file_handler->delete($temp_folder . DS . 'xml' . DS . 'extension_dependency.xml');
		}	}
	public function loadRealSQL() {
		$realFolder = rtrim( $this->temp_folder, DS ) . DS . 'database' . DS . 'real';
		$systemFolderC = WGet::folder();
		if ( $systemFolderC->exist( $realFolder ) ) {
self::logMessage( 'Tables location : ' . $realFolder, 'install' );
			$filesA = $systemFolderC->files( $realFolder );
			$newFilesA = array();
			foreach( $filesA as $oneFile ) {
				if ( 'index.html' == $oneFile ) continue;
				$newFilesA[] = $oneFile;
			}
			sort( $newFilesA );
self::logMessage( $newFilesA, 'install' );
			$installDatabaseC = WClass::get( 'install.database' );
			$installDatabaseC->setResaveItemMoveFile();
			$installDatabaseC->extension_namekey = $this->extension_namekey;
			$installDatabaseC->import( $newFilesA, $realFolder, 'real' );
		} else {
self::logMessage( 'No Table to import' ,'install' );
			return false;
		}
		return true;
	}
	public function activateExtensionVersionAndClean() {
self::logMessage( 'in function activateExtensionVersionAndClean', 'install' );
		if ( empty($this->wid) ) {
self::logMessage( 'ERROR: extension and wid not defined', 'install' );
			return false;
		}
				$extensionT = WTable::get( 'extension_node' );
		$extensionT->whereE( 'wid', $this->wid );
		$extensionT->update( array( 'publish'=>1, 'version'=>$this->current_version, 'lversion'=>$this->current_version, 'modified'=>time() ) );
				$extensionT->whereE( 'wid', $this->wid );
		$extensionT->whereE( 'created', 0 );
		$extensionT->setVal( 'created', time() );
		$extensionT->update();
				if ( !isset($_SESSION['joobi']['version_table_exist']) ) {
			$sql = WTable::get( 'extension_version');
			$_SESSION['joobi']['version_table_exist'] = $sql->tableExist();
		}
		if ( $_SESSION['joobi']['version_table_exist'] ) {
			$sql = WTable::get( 'extension_version' );
			$sql->whereE('wid',$this->wid);
			$sql->whereE('status',25);
			$sql->where( 'version', '!=', $this->current_version );
			$sql->update( array('status' => 0) );
			$sql = WTable::get( 'extension_version' );
			$sql->whereE( 'wid', $this->wid );
			$sql->whereE( 'status', 100 );
			$sql->where( 'version', '!=', $this->current_version );
			$sql->update( array( 'status' => 75 ) );
			$sql = WTable::get( 'extension_version' );
			$sql->whereE( 'wid', $this->wid );
			$sql->whereE( 'version', $this->current_version );
			$sql->update( array( 'status' => 25 ) );
		}
self::logMessage( 'Extension publishing finished', 'install' );
	}
	function isInstalled() {
		if ( empty($this->mainext) || empty($this->mainext->wid) || empty($this->mainext->namekey) ) return 0;
		$extensionT = WTable::get( 'extension_node' );
		if ( !empty($this->mainext->wid) ) $extensionT->whereE( 'wid', $this->mainext->wid );
		else $extensionT->whereE( 'namekey', $this->mainext->namekey );
		$extensionT->whereE( 'publish', 1 );
		$extensionT->whereE( 'version', $this->current_version );
		if ( empty($this->mainext->wid) ) self::logMessage( $this, 'package_isInstalled' );
		return $extensionT->load( 'lr', 'wid' );
	}
	public function extract($package) {
				$filehandler = WGet::file();
		$temp_folder = '';
		$useCMS = false;
				if ( ! $filehandler->extract( $package, $temp_folder ) ) {
self::logMessage( 'Package [' . $package . '] extraction failed into folder: ' . $temp_folder, 'install' );
			return false;
		}
self::logMessage( 'Package [' . $package . '] extracted', 'install' );
				$this->tables = $this->xml( $temp_folder );
self::logMessage( 'Temp folder location : ' . $temp_folder, 'install' );
				if ( !empty($this->tables['data'][0]['attributes']['type']) && strtolower($this->tables['data'][0]['attributes']['type']) == 'theme' ) {
						$installThemeC = WClass::get( 'install.theme' );
			$folderDestation = $installThemeC->installTheme( $this->tables['data'][0] );
			if ( empty($folderDestation) ) return false;
						$this->destination = JOOBI_DS_THEME . $folderDestation;
		} else {	
			if ( ! is_array($this->tables) ) return false;
			$ext =& $this->tables['extension_node'];
			$this->mainext =& $ext[0];
			$install_ext = new stdClass;
			$installCommonC = WClass::get('install.common');
			$installCommonC->namekey = $this->mainext->namekey;
			if ( !$installCommonC->initCustom( $install_ext ) ) {
				return false;
			}
self::logMessage( 'Custom functions loaded in extract()', 'install' );
						$dest = ( $this->mainext->destination != '' ? str_replace( '|', DS , $this->mainext->destination) . DS : '' ) . $this->mainext->folder;
			$this->destination = Install_Package_class::installFilesFolder() . DS . $dest;
						if ( ! $installCommonC->trigger( $install_ext, $this, 'preinstall') ) {
				return false;
			}
		}
self::logMessage( 'Pre-install triggered in extract()' ,'install' );
self::logMessage( $temp_folder ,'install' );
self::logMessage( $this->destination ,'install' );
		$systemFolderC = WGet::folder();
		$backup_type = 'add_over';
				if ( ! $systemFolderC->copy( $temp_folder, $this->destination, $backup_type ) ) {
self::logMessage( 'ERROR -- Files could not be copied from ' . $temp_folder . ' to ' . $this->destination, 'install' );
			return false;
		}
self::logMessage( 'Files copied from ' . $temp_folder . ' to ' . $this->destination, 'install' );
		$old_xml_folder = JOOBI_DS_JOOBI . $dest . DS . 'xml';
				if ( $systemFolderC->exist( $old_xml_folder ) ) {
self::logMessage( 'Deleting XML file ' . $old_xml_folder, 'install' );
			$systemFolderC->delete( $old_xml_folder );
		}
				$systemFolderC->delete( $temp_folder );
self::logMessage( 'Delete tmp folder ' . $temp_folder, 'install' );
		self::logMessage( 'DO NOT Delete package file '. $package, 'install' );
		return true;
	}
	function xml($folder) {
		$folder =	rtrim( $folder, DS );
				$cache_folder = JOOBI_DS_TEMP . 'cache' . DS . 'xml' . DS;
		$cache_file = $cache_folder . sha1($folder) . '.jxc';
		$filehandler = WGet::file();
		if ( $filehandler->exist($cache_file) ) {
			return unserialize($filehandler->read( $cache_file) );
		}
		$systemFolderC = WGet::folder();
		$metadataLocation = $folder . DS . 'metadata.xml';
self::logMessage( 'XML metadata file: ' . $metadataLocation, 'install' );
				$joobiWidgets = false;
		$joobiXml = $folder . DS . 'xml' . DS . 'extension_node.xml';
		if ( $filehandler->exist( $joobiXml ) ) $joobiWidgets = true;
						if ( $joobiWidgets || $filehandler->exist( $metadataLocation ) ) {
			if ( $joobiWidgets ) $metadataLocation = $joobiXml;
			$parser = WClass::get('library.parser');
			$xml = $parser->loadFile( $metadataLocation );
						if ( $xml[0]['nodename']=='data' && isset($xml[0]['attributes']['version']) ) {
								$data['data'] = $xml;
self::logMessage( 'Using metadata file', 'install', false, 0, false );
				foreach($xml[0]['children'] as $row) {
					if ( $row['nodename']=='extension') {
						$table = array();
						$obj = new stdClass;
						foreach($row['children'] as $column) {
							$key = strtolower($column['nodename']);
							if (in_array($key,array('namekey','version','name','type','folder','destination'))){
									if (array_key_exists('nodevalue',$column)){
										$obj->$key = $column['nodevalue'];
									}									else{
										$obj->$key = '';
									}							}
						}
												if (!isset($obj->lversion)) $obj->lversion=$obj->version;
						if (empty($obj->type) && !empty($obj->namekey) ) {
														$obj->type = array_pop(explode('.',$obj->namekey));
						} elseif (!is_numeric($obj->type)) {
														$typeHandler = WType::get( 'apps.type' );
							$obj->type = $typeHandler->getValue($obj->type,false);
						}
						if ( !isset($obj->folder) && !empty($obj->namekey) ){
														$parts = explode('.',$obj->namekey);
							if (count($parts)>2){
								$obj->folder = $parts[1];
							} else {
								$obj->folder = $parts[0];
							}
						}
						if (!isset($obj->destination) && !empty($obj->namekey) ) {
							$typeHandler = WType::get( 'apps.typefolder');
							$obj->destination = $typeHandler->getName($obj->type);
																					$parts = explode('.',$obj->namekey);
							if ( count($parts)>2 ) $obj->destination = 'node'.'|'.$parts[0].'|'.$obj->destination;
						}
						$obj->wid = 1;
						$data['extension_node'] = array($obj);
						$obj2 = new stdClass;
						$obj2->wid = 1;
						$obj2->version = $obj->version;
						$data['extension_version'] = array( $obj2 );
						break;
					}				}
								$filehandler->write( $cache_file,serialize($data), 'force' );
				return $data;
			}		}
		$xmlfolder = $folder . DS . 'xml' . DS;
				if ( !$systemFolderC->exist($xmlfolder) ) {
			self::logMessage( 'ERROR: The extension xml data folder is missing ' . $folder, 'install' );
			$this->_tryCMSPackageInstall( $folder );
			return false;
		}
		$files = $systemFolderC->files( $xmlfolder );
		if ( empty($files) ) {
self::logMessage( 'ERROR: The extension xml data files are missing in the folder ' . $folder, 'install' );
			return false;
		}
				foreach( $files as $k => $file ) {
			$name = explode('.',$file);
			$string = $filehandler->read($xmlfolder.$file);
			if ($string === false ) {
				continue;
			}
			$parser = WClass::get( 'library.parser' );
			$xml = $parser->parse( $string );
			if ( !is_array($xml) ) {
				self::logMessage( 'ERROR: Could not read the xml file '.$xmlfolder.$file,'install', false, 0, false );
				$this->_tryCMSPackageInstall( $folder );
				return false;
			}
			$table = array();
			if ( !empty($xml[0]['children']) ) {
				foreach( $xml[0]['children'] as $row ) {
					if ( !empty($row['children']) ) {
						$obj = new stdClass;
						foreach( $row['children'] as $column ) {
							$key = strtolower($column['nodename']);
							if (array_key_exists('nodevalue',$column)){
								$obj->$key = $column['nodevalue'];
							}							else{
								$obj->$key = '';
							}						}						$table[] = $obj;
					}
				}
			}
			$data[ $name[0] ] = $table;
		}
				$filehandler->write( $cache_file, serialize($data), 'force' );
		return $data;
	}
	private function _tryCMSPackageInstall($folder) {
self::logMessage( 'In tryCMSPackageInstall function  1', 'install' );
		$status = false;
		ob_start();
		switch( JOOBI_FRAMEWORK ) {
			case 'joomla30':
				break;
			default:
				break;
		}
		$data = ob_get_clean();
self::logMessage( 'In tryCMSPackageInstall function  2', 'install' );
self::logMessage($data, 'install' );
		if ( $status || !empty($data) ) {
			WPref::get( 'install.node' );
						$status = ( !empty( $_SESSION['joobi']['install_status'] ) ) ? $_SESSION['joobi']['install_status'] : 0;
			echo 'FINISH STATUS['.$status.'] BIGMSG[' . WText::t('1308836335LUUQ') . ' ... 1' . '<br/><br/>' . $data . ']';
			if (isset($this->packageId) && isset($this->list) && isset($this->parent)){
								unset($this->list[$this->packageId]);
self::logMessage( 'call updatePref 1', 'install' );
				$this->parent->updatePref( $_SESSION['joobi']['install_status'] );
				exit;
			}
			return true;
		}
		return false;
	}
	 function checkLicense() {
		$ext = WExtension::get( $this->mainext->namekey, 'data' );
		if ( $ext->level > 0 ) {
									$appsInfoC = WClass::get('apps.info');
			$reason = '';
			$licenceValid = $appsInfoC->checkValidity( $ext->wid, $reason, $ext->folder );
			if ( !$licenceValid ) self::logMessage( 'License not valid because: ' . $reason, 'install', false, 0, false );
			return true;
		}
				if ( $this->installUpdate==1 ) {
						$reQuestSite = WPref::load( 'PAPPS_NODE_REQUEST' );
			$process = WClass::get('install.process');
			if ( empty($reQuestSite) || ! $process->checkServerAvail( $reQuestSite ) ) {
				$message = WMessage::get();
				$EMAIL = 'support@joobi.co';
				$message->userN('1213794916KBVX');
				$message->userN('1213794916KBVY',array('$EMAIL'=>$EMAIL));
				return true;
			}
			if ( empty( $this->level ) ) {
								$wlidM = WModel::get( 'install.appslevel', 'object' );
				$wlidM->whereE( 'wid', $ext->wid );
								$wlidM->where( 'level', '!=', '0' );
				$wlidM->orderBy( 'level', 'DESC' );
				$level = $wlidM->load( 'lr' , 'level' );	
			} else $level = $this->level;
						if ( !empty($level) ) {
				$ltypeTolicence = 101;
				 if ( $status ) $this->level = $level;
			}
		}
		$this->_statistics();
		return true;
	}
	function needLicense() {
				return false;
		$wlidM = WTable::get( 'extension_level');
		$wlidM->whereE( 'wid', $this->wid );
				$wlidM->where( 'level', '!=', '0' );
		$result=$wlidM->load('lr' , 'lwid' );
		if ( empty($result) ) return false;
		return true;
	}
	public static function logMessage($message,$location='',$not1=null,$not2=null,$not3=null,$not4=null) {
		static $installDebug = null;
		if ( !isset($installDebug) ) {
			if ( !defined('PINSTALL_NODE_INSTALLDEBUG') ) {
				if ( class_exists( 'WPref') ) {
					WPref::get( 'install.node' );
				}			}			$installDebug = ( defined('PINSTALL_NODE_INSTALLDEBUG') ? PINSTALL_NODE_INSTALLDEBUG : false );
		}
		if ( $installDebug ) WMessage::log( $message, 'install' );	
	}
	private function _checkFirstTimeInstalled() {
self::logMessage( 'Checking already installed version: ' . $this->mainext->namekey . ' - ' . $this->current_version, 'install', false, 0, false );
			$table = WTable::get( 'extension_node', '', 'wid' );
			$table->whereE( 'namekey', $this->mainext->namekey );
			$table->whereE( 'publish', 1 );
			$table->where( 'version', '>=', $this->current_version );
			$this->goToNext = false;
			if ( $table->exist() ) {
				$this->goToNext = true;
				return false;
			}
			return true;
	}
	private function _checkDependencies() {
self::logMessage( ' Checking checkDependencies() start ', 'install', false, 0, false );
self::logMessage( ' with old wid: '.$this->old_wid,'install', false, 0, false );
			if ( !array_key_exists('extension_dependency', $this->tables ) || $this->mainext->namekey == 'install.node' ) {
				return true;
			}
					$dependency =& $this->tables['extension_dependency'];
		$joobiCoreA = Install_Node_install::accessInstallData( 'get', 'joobiCore' );
		foreach( $dependency as $dep ) {
										if ( $dep->wid != $this->old_wid ) {
						continue;
					}
										if ($dep->type!=0 && empty($dep->filter)){
						continue;
					}
										if (($dep->from != '' && $dep->from > $this->current_version) ||($dep->to != '' && $dep->to < $this->current_version)){
						continue;
					}
										$child = -1;
					foreach($this->ext as $k=> $e){
						if ($e->wid == $dep->ref_wid){
							$child = $k;
							break;
						}					}
										if ($child == -1){
						$name = $this->mainext->folder;
self::logMessage( 'Could not find the extension data for one of the extension child of the extension '.$this->mainext->namekey, 'install', false, 0, false );
						continue;
					}
					$child_ext = $this->ext[$child];
										if ( $child_ext->namekey == $this->mainext->namekey ) {
						continue;
					}
					$stop = false;
					switch( $dep->type ) {
						case 0:										break;
						case 25:									$extensionT = WTable::get( 'extension_node');
							$extensionT->whereE( 'destination', $child_ext->destination );
							$extensionT->whereE( 'publish', 1 );
							$db_ext = $extensionT->load('o','wid');
							if (is_object($db_ext))
								$stop = true;
							break;
					}
					if ( $stop ) continue;
										$extensionT = WTable::get( 'extension_node' );
					$extensionT->whereE( 'namekey', $child_ext->namekey );
					$extensionT->whereE('publish',1);
					$db_ext = $extensionT->load('o','version' );
					$key = -1;
										foreach($this->formated_packages as $k => $v) {
						if (str_replace('|','.',$child_ext->destination) == $v['destination'] && $v['name']==$child_ext->folder){
							$key = $k;
							break;
						}					}
					if (is_object($db_ext)){
												if ( $key != -1 && version_compare($db_ext->version,$this->formated_packages[$key]['version'],'<')){
							if ( ( $this->use_core_array && $joobiCoreA[$key]=='finished') || $this->list[$key]['install'] == 1 ) continue;
														$this->child_key = $key;
							$this->switch_child = true;
							return false;
						}						continue;
					}
										if ($key == -1){
						$name = $this->mainext->folder;
						$destination = $this->mainext->destination;
						$child_name = $child_ext->folder;
						$child_destination = $child_ext->destination;
						$myMess = 'The extension '.$name.' ('.$destination.') needs the extension '.$child_name.' ('.$child_destination.'), but this extension could not be found registered in the database, nor in the list of the packages which are to be installed now. Please install this extension first.';
self::logMessage($myMess, 'install', false, 0, false );
						return false;
					}
				if ( ( $this->use_core_array && $joobiCoreA[$key]=='finished') || $this->list[$key]['install'] == 1 ) continue;
												$this->child_key = $key;
				$this->switch_child = true;
				return false;
			}
		if ( !empty( $this->tables['extension_dependency'] ) ) {
				$extensionDepCache = Install_Node_install::accessInstallData( 'get', 'extensionDeps' );
				if ( empty( $extensionDepCache) ) $extensionDepCache = array();
				$extensions = $this->tables['extension_node'];
				$mainExt = $extensions[0];
				$deps = array();
				$numDeps = count( $extensions ) -1 ;
				for( $index=1; $index <= $numDeps; $index++ ) {
					$deps[] = $extensions[$index]->namekey;
				}
				$extensionDepCache[ $mainExt->namekey ] = $deps;
				Install_Node_install::accessInstallData( 'set', 'extensionDeps', $extensionDepCache, 'static' );
		}
self::logMessage( ' Checking checkDependencies() finished ', 'install', false, 0, false );
		return true;
	}
	private function _statistics() {
				$type = WExtension::get( $this->mainext->namekey , 'type' );
		if ( $type!=1 ) return true;
		$data = new stdClass;
		$data->key = 'avmio234nb3nhjfs554';
		$urlpos = strpos(JOOBI_SITE, '://');
		$url = substr( JOOBI_SITE, $urlpos+3 );
		$url = rtrim( $url, '/');
		if ( substr( $url, 0,9 )=='localhost'
				|| substr( $url, 0, 3 ) == '10.'
				|| substr( $url, 0, 8 ) == '192.168.'
				|| substr( $url, 0, 7 ) == '172.16.'
				|| substr( $url, 0, 4 ) == '127.'
			 ) {
			return false;
		} else {
			$data->url = JOOBI_SITE;
		}
				$extension = new stdClass;
		$extension->namekey = $this->mainext->namekey;
		$extension->status = $this->installUpdate;			$extension->auto = $this->auto;			$extension->level = $this->level;
		$data->extension = $extension;
				$website = new stdClass;
		$systemType = WType::get( 'apps.cms' );
		$cms = $systemType->getValue( JOOBI_FRAMEWORK, false );
		$website->cms = $cms;
		$website->cmsversion = WApplication::version();
		$dbTYpe = WType::get( 'library.sql');
		$website->dbtype = $dbTYpe->getValue( WGet::DBType() );			$website->lgid = WApplication::mainLanguage();
		$website->multilang = WApplication::availLanguages('lgid', 'all' );			$website->php = phpversion();
		$dbTest = WClass::get( 'library.sql' );
		$website->sqlversion = $dbTest->getVersion();
		$extHelper = WClass::get('apps.helper');
		$website->encoding = $extHelper->getEncoding();				$data->website = $website;
		$distributionSite = 'http://www.joobiserver.com';
		$netcom = WNetcom::get();
		$returned = $netcom->send( $distributionSite, 'license', 'stats_new', $data, false );
				if ( ! $returned ) {
						$netcom->protocol = 'simple';
			echo $netcom->send( $distributionSite, 'license', 'stats_new', $data );
		}
		return true;
	}
	private function _getCustomFunctions() {
								$listOfFunctionsToRun = array( 'install', JOOBI_FRAMEWORK, 'addExtensions' );
				if ( $this->installUpdate == 2 ) {
			$listOfFunctionsToRun[] = 'version';
		}
		return $listOfFunctionsToRun;
	}
}