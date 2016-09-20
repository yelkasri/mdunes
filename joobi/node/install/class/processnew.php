<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Install_Processnew_class {
	var $list = array();
	var $installing = array();
	var $mode = 'extract';	
	var $params_name = array( 'list', 'installing', 'mode' );
	var $_catchMessage = true;
	private $_returnedMessageA = array();
	private $_packageToInstallA = array();
	private function _setMessage($action,$message='',$status=null) {
		$obj = new stdClass;
		$obj->action = $action;
		$obj->message = $message;
		if ( !empty($status) ) $obj->status = $status;
		$this->_returnedMessageA[] = $obj;
	}
	private function _getMessage() {
		$messA = $this->_returnedMessageA;
		$this->_returnedMessageA = array();
		return $messA;
	}
 	public function getListOfPackagesStep() {
 		$status = $this->_getListOfPackages();
 		if ( $status ) $this->removeXMLCacheFolder();
 		return $this->_getMessage();
 	}
 	public function getNumberPackage() {
Install_Processnew_class::logMessage( 'List of package retreived:' );
Install_Processnew_class::logMessage( $this->_packageToInstallA );
 		return count( $this->_packageToInstallA );
 	}
 	public function getLastExtension() {
 		$count = count( $this->_packageToInstallA );
 		$lastExtensionO = $this->_packageToInstallA[$count-1];
 		return $lastExtensionO->namekey;
 	}
 	public function getDownloadPackages($nb) {
 		$this->_downloadPackages( $nb );
 		return $this->_getMessage();
 	}
 	public function installLanguages($nb) {
 		$this->_downloadLanguages( $nb );
 		return $this->_getMessage();
 	}
 	public function finalizeInstall() {
 		$this->finish_transinstall();
 		$this->finish_install();
 		 		$this->_createCMSMenu();
 		return $this->_getMessage();
 	}
 	public function extractPackages($nb) {
 		$this->_extractPackage( $nb );
 		return $this->_getMessage();
 	}
 	public function moveFiles() {
 		$this->finish_extract();
 		return $this->_getMessage();
 	}
 	public function createTables($nb) {
 		$this->_createTables( $nb );
 		return $this->_getMessage();
 	}
 	public function customFunction($nb) {
 		$this->_customFunction( $nb );
 		return $this->_getMessage();
 	}
	function updatePref($install_status) {
		$array_of_params = array();
		foreach( $this->params_name as $name ) {
			if ( isset($this->$name) ) $array_of_params[$name] = $this->$name;
		}
		$array_of_params[0] = 'install_params_version_2';
		$install_params = serialize($array_of_params);
		if ( !class_exists( 'Install_Node_install' ) ) WLoadFile( 'install.install.install');
		Install_Node_install::accessInstallData( 'set', 'installParams', $install_params );
	}
	function getPref() {
		$this->_packageToInstallA = Install_Node_install::accessInstallData( 'get', 'installExtension' );
	}
 	function finish_extract() {
self::logMessage( 'Beginning of moving files : ' . time(), 'install', false, 1, true, true);
		 		$this->_overwriteEntryPoint();
				$systemFolderC = WGet::folder();
		$foldersA = $systemFolderC->folders( JOOBI_DS_USER . 'installfiles' );
		foreach( $foldersA as $folder ) {
Install_Processnew_class::logMessage( 'Moving folder: ' . JOOBI_DS_USER . 'installfiles' . DS . $folder, 'install' );
			if ( $systemFolderC->exist( JOOBI_DS_USER . 'installfiles' . DS . $folder ) ) {
				$systemFolderC->copy( JOOBI_DS_USER . 'installfiles' . DS . $folder, JOOBI_DS_JOOBI . $folder, 'add_over' );
			}
			if ( $systemFolderC->exist( JOOBI_DS_USER . 'installfiles' . DS . $folder ) ) {
				$systemFolderC->delete( JOOBI_DS_USER . 'installfiles' . DS . $folder );
			}
		}
self::logMessage( 'All files moved : ' . time(), 'install', false, 1, true, true );
		$this->_setMessage( 'html', WText::t('1427652812DTKK') );
		return true;
 	}
 	 function finish_copy() {
 self::logMessage( 'All files moved','install' );
		$this->mode='createtable';
		if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
		$message = 'All files moved';
				$systemFolderC = WGet::folder();
		$systemFolderC->delete( JOOBI_DS_USER . 'installfiles' );
		return 'STATUS['.@$_SESSION['joobi']['install_status'].'] BIGMSG['.$message.'] ';
 	}
 	function finish_createtable() {
self::logMessage( 'All tables extracted','install' );
		$this->mode='install';
		if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
		$message = 'All tables created';
		$customInstallA = array();
		Install_Node_install::accessInstallData( 'set', 'joobiCustomInstall', $customInstallA );
		return 'STATUS['.@$_SESSION['joobi']['install_status'].'] BIGMSG['.$message.'] ';
 	}
 	function finish_install() {
 self::logMessage( 'All packages installed', 'install' );
		$this->mode = 'customtype';
				$installCommonC = WClass::get( 'install.common' );
		$installCommonC->publishEnglishLanguage( false );
self::logMessage( '-- populatePreferences --', 'install' );
		$installCommonC->populatePreferences();
self::logMessage( '-- populateEnglish --', 'install' );
		$installCommonC->populateEnglish();
self::logMessage( '-- Start: addUpdateDependencies --', 'install' );
				$this->addUpdateDependencies();
self::logMessage( '-- Finish: addUpdateDependencies --', 'install' );
		return true;
 	}
 	function finish_customtype(){
 self::logMessage( 'All type custom install functions run', 'install' );
		$this->mode='cmsinstall';
		if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
		$message = 'All type custom install functions run';
		return 'STATUS['.@$_SESSION['joobi']['install_status'].'] BIGMSG['.$message.'] ';
 	}
 	function finish_cmsinstall(){
 self::logMessage( 'All CMS links created','install' );
		$this->mode='custominstall';
		if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
		$message = 'All CMS links created';
		return 'STATUS['.@$_SESSION['joobi']['install_status'].'] BIGMSG['.$message.'] ';
 	}
 	function finish_translation(){
 self::logMessage( 'All translations retrieved' , 'install' );
		$this->mode='transinstall';
				WClass::get( 'translation.process' );
		$_SESSION['joobi']['import_step'] = POPULATION_STEP;
		$importLangs = Install_Node_install::accessInstallData( 'get', 'importLangs' );
		foreach( $importLangs as $key => $lang ){
			if ( empty( $lang->fileFound ) && $lang->code!='en'){
				unset( $importLangs[ $key ] );
			}		}
		Install_Node_install::accessInstallData( 'set', 'importLangs', $importLangs );
				if ( empty( $importLangs ) ) {
			$installWidget = WGlobals::getSession( 'webapps', 'widgetinstall', null );
			if ( !empty($installWidget) && $installWidget !== true ) {
				$installWidget = WText::t('1260151166CSBV') . '<br/>' .
						'<a href="' . WPage::routeURL( 'controller=translation&task=listing', 'smart', 'default', false, false, JOOBI_MAIN_APP ) . '">' . WText::t('1260151166CSBW') . '</a>';
				WGlobals::setSession( 'webapps', 'widgetinstall', $installWidget );
			}
			return $this->finish_transinstall();
		}
		if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
		$message = WText::t('1395716602EKEW');
		if ( empty($message) ) {
			$message = 'All translations retrieved!';
		}
		return 'STATUS['.@$_SESSION['joobi']['install_status'].'] BIGMSG['.$message.'] ';
 	}
 	function finish_transinstall() {
self::logMessage( 'real table files versions file saved','install' );
				$cache = WCache::get();
		$cache->resetCache( 'Packages' );
				$modelToReloadA = array( 'library.controller', 'library.view', 'library.model', 'library.picklist', 'install.apps' );
		foreach( $modelToReloadA as $modelName ) {
			$controllerM = WModel::get( $modelName,'object' );
			$controllerM->setVal( 'reload', 1 );
			$controllerM->update();
		}
				$this->removeXMLCacheFolder();
				$this->_removePackagesInTempFolder();
self::logMessage( 'Downloaded packages removed', 'install' );
				$translationHelperC = WClass::get( 'translation.helper', null, 'class', false );
		if ( !empty($translationHelperC) ) {
			$translationHelperC->updateLanguages();
self::logMessage( 'Updated the list of translations', 'install' );
		}
						$appToSetup = $this->_searchPreferencesPageForNewlyInstalledApps();
		return true;
 	}
	public function finishDownloadPackages() {									$this->updatePref( 2 );
						$langM = WClass::get('translation.helper');
			$langM->updateLanguages();
				$systemFolderC = WGet::folder();
		$folder = JOOBI_DS_USER . 'installfiles';
		if ( $systemFolderC->exist($folder) ) $systemFolderC->delete( $folder );
		return true;
	}
 	function getLanguages() {
 		$fields = array( 'name','code','lgid' );
 		$langs = WApplication::availLanguages( $fields, 'all' );
 		if ( !empty($langs) ) {
			foreach($langs as $key => $item){
				$name = strtolower( str_replace( array( ',', '(',')', ' ' ), '_', $item->name ) );
				$langs[$key]->name = $name;
			}
			Install_Node_install::accessInstallData( 'set', 'importLangs', $langs );
 		}
		return $langs;
 	}
 	public function removeXMLCacheFolder() {
 		$systemFolderC = WGet::folder();
		$systemFolderC->delete( JOOBI_DS_TEMP.'cache' . DS . 'xml' );
self::logMessage( 'XML cache folder cleaned', 'install' );
 	}
 	private function _removePackagesInTempFolder() {
 		foreach( $this->list as $items ) {
 			foreach( $items as $package ) {
 				if ( is_array($package) && isset($package['filename']) ) {
 					$path = $package['filename'];
self::logMessage( 'Remove downloaded files', 'install' );
self::logMessage( $path, 'install' );
 				} 			} 		}
 		return true;
 	}
 	public function install(&$return,&$v,&$v2,&$packages,$k,$k2) {
 		if ( empty($v['level']) ) $v['level'] = 0;
		$pieces = explode(DS,$v2['filename']);
		$package = $pieces[count($pieces)-1];
		$install = WClass::get('install.package');
		$install->level = (int)$v['level'];
		$install->packages =& $packages;
		$install->main = true;
		if ( isset($v2['folder']) ) {
			$install->temp_folder = $v2['folder'];
		}
		$install->processPackagesName();
		$install->parent =& $this;
		$install->list =& $this->list[$k];
		$install->packageId = $k2;
				if ( !$install->loadXmlAndCheckDependencies() ) {
			if ( !empty($install->goToNext) ) {
self::logMessage( $package . ' already installed ! Going to the next one' , 'install', false, 0, false );
				$this->list[$k][$k2]['install'] = 1;
				$return = 'continue';
				return true;
			}
			if ( isset($install->switch_child) && $install->switch_child ) {
									$this->list[$k][$k2]['folder'] = $install->temp_folder;
				$child = $this->list[$k][$install->child_key];
				$this->list[$k][$install->child_key] = $this->list[$k][$k2];
				$this->list[$k][$k2] = $child;
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
								$parts = explode( DS, $child['filename'] );
				$child_package =$parts[count($parts)-1];
				$parts=explode('_',$child_package);
				if ($parts[0]=='lib'){
					$parts[1]='lib';
				}				$child_package = $parts[1];
				self::logMessage( 'The package '.$package.' requires first the install of the package '.$child['filename'],'install', false, 0, false );
				$namekey_info = '<div style="display:none;">'.$install->mainext->namekey.'</div>';
				$return = 'STATUS['.@$_SESSION['joobi']['install_status'].'] JSUCCESS['.$v['wid'].$v['level'].']';
				$return .= ' BIGMSG['.'The extension '.$install->mainext->name.' requires first the install of the package '.$child_package.$namekey_info.'] ';
				return true;
			}		}
		if ( $install->isInstalled() ) {
self::logMessage( $package . ' already installed ! Going to the next one', 'install', false, 0, false );
			$this->list[$k][$k2]['install'] = 1;
			$return = 'continue';
			return true;
		}
		$version = $install->getOldVersion();
		$installWidget = WGlobals::getSession( 'webapps', 'widgetinstall', null );
		if ( !empty($version) ) {
			$this->list[$k][$k2]['oldVersion'] = $version;
			$install->installUpdate = 2;
			$install->previous_version = $version;
			$install->newInstall = false; 
			if ( $install->mainext->type==1 ) {
				$wid = WExtension::get( $install->mainext->namekey, 'wid' );
				$found = false;
				foreach( $this->installing as $key => $app ) {
					if ( $app->wid==$wid ){
						$found=$key;
						break;
					}
				}
				if (is_int($found)){
					$this->installing[$key]->newInst=false;
				}
			}
		} elseif ( !empty($installWidget) ) {
			$obj = new stdClass;
			$obj->wid = $install->mainext->namekey;
			$obj->newInst = true;
			$this->installing=array($obj);
		}
		ob_start();
 		if ( !$install->process() ) {
 			$errors = ob_get_clean();
			$mess = WMessage::get();
			$errors .= $mess->getM();
self::logMessage( 'An error occurred during the installing process of the package '."\r\n".$errors,'install', false, 0, false );
			$this->updatePref( 0 );
			$return = 'FINERROR STATUS['.@$_SESSION['joobi']['install_status'].'] JERROR['.$v['wid'].$v['level'].'] BIGMSG[';
			$return .= 'An error occurred during the installing process of the package:' . ' '.$package;
			$return .= '<br/>' . $errors.'] ';
			return true;
		}
		if ( !$install->hasCMSFunction() ) {
			$this->list[$k][$k2]['cmsinstall'] = 1;
		}
		$methods = $install->customInstallMethods();
		if ( empty($methods) ) {
			$this->list[$k][$k2]['custominstall'] = 1;
		}		else{
			$customInstallA = Install_Node_install::accessInstallData( 'get', 'joobiCustomInstall' );
			$customInstallA[$k][$k2] = $methods;
			Install_Node_install::accessInstallData( 'set', 'joobiCustomInstall', $customInstallA );
		}
self::logMessage( 'Calling activateExtensionVersionAndClean 2', 'install' );
		$install->activateExtensionVersionAndClean();
		if ( !$install->needLicense() ) {
			$this->list[$k][$k2]['checklicense'] = 1;
		}
		if ( $install->mainext->type != 1 ) {
			$this->list[$k][$k2]['translation'] = 1;
		} else {
			$this->list[$k][$k2]['namekey'] = $install->mainext->namekey;
			$this->list[$k][$k2]['langDownloaded'] = array();
		}
		if ( !$install->hasCustomTypeFunction() ) {
			$this->list[$k][$k2]['customtype'] = 1;
		}
		$this->list[$k][$k2]['install'] = 1;
self::logMessage( 'The package "' . $package . '" was installed.', 'install', false, 0, false );
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
		$EXTENSION = $install->mainext->name;
		$message = 'The extension "' . $EXTENSION . '" was installed.';
		$namekey_info = '<div style="display:none;">'.$install->mainext->namekey.'</div>';
		$version_info = ( !empty($v['wid']) ? $v['wid'] : 9999 ) . (!empty($v['level']) ? $v['level'] : 0 );
		$return = 'STATUS[' . @$_SESSION['joobi']['install_status'] . '] JSUCCESS[' . $version_info . '] BIGMSG[' . $message . $namekey_info . '] ';
		return true;
 	}
 	function copy(&$return,&$v,&$v2,&$packages,$k,$k2) {
		$pieces = explode( DS, $v2['filename'] );
		$package = $pieces[ count($pieces)-1 ];
		$real_folder = rtrim( $v2['folder'], DS );
		$dest = rtrim( $v2['destination'], DS );
		$tmp_folder = JOOBI_DS_USER . 'installfiles' . DS . $dest;
self::logMessage( 'Copy files from installfiles folder to final joobi folder: ' . $real_folder, 'install' );
		$systemFolderC = WGet::folder();
		if ( $systemFolderC->exist( $tmp_folder ) ) {
			$systemFolderC->copy( $tmp_folder, $real_folder, 'add_over' );
						$themeFolder = $real_folder . DS . 'theme';
			if ( $systemFolderC->exist( $themeFolder ) ) {
								$systemFolderC->copy( $themeFolder, JOOBI_DS_THEME, 'add_over' );
				$systemFolderC->delete( $themeFolder );
			}
			$systemFolderC->delete( $tmp_folder );
		} else {
			$return = 'continue';
			$this->list[$k][$k2]['copy'] = 1;
			return true;
		}
		$this->list[$k][$k2]['copy'] = 1;
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
				$parts = explode( '_', $package );
		if ( $parts[0]=='lib' ) {
			$parts[1]='lib';
		}
		if ( $parts[1] == 'node' ) {
			$EXTENSION = $parts[0];
		} else {
			$EXTENSION = $parts[1];
		}
		if ( !empty($EXTENSION) ) {
			$message = 'The files of the extension '.$EXTENSION.' were copied.';
		} else {
			$message = 'The files were copied.';
		}
		if ( empty($v['wid']) ) {
		}
		$namekey_info = '<div style="display:none;">' . $package . '</div>';
		$return = 'STATUS[' . @$_SESSION['joobi']['install_status'] . '] JSUCCESS[' . @$v['wid'] . @$v['level'] . '] BIGMSG[' . $message . $namekey_info . '] ';
		return true;
 	}
 	function cmsinstall(&$return,&$v,&$v2,&$packages,$k,$k2) {
		$pieces = explode(DS,$v2['filename']);
		$package = $pieces[count($pieces)-1];
		$install = WClass::get('install.package');
		if ( empty($v['level']) ) $v['level'] = 0;
		$install->level = (int)$v['level'];
		$install->packages =& $packages;
		$install->main = true;
		$install->parent =& $this;
		$install->list =& $this->list[$k];
		$install->packageId = $k2;
		if (isset($v2['folder'])){
			$install->temp_folder = $v2['folder'];
		}
		$install->processPackagesName();
		$install->loadXml();
		$install->triggerCMS();
self::logMessage( 'The CMS links for the package '.$package.' were created.', 'install' );
		$EXTENSION = $install->mainext->name;
		$message = 'The CMS links of the extension '.$EXTENSION.' were created.';
		$namekey_info = '<div style="display:none;">'.$install->mainext->namekey.'</div>';
		$return = 'STATUS['.@$_SESSION['joobi']['install_status'].'] JSUCCESS['.$v['wid'].$v['level'].'] BIGMSG['.$message.$namekey_info.'] ';
		return true;
 	}
 	public function custominstall(&$return,&$v,&$v2,&$packages,$k,$k2) {
		$pieces = explode(DS,$v2['filename']);
		$package = $pieces[count($pieces)-1];
self::logMessage( 'starting custom install for '.$package,'install', false, 0, false );
		if ( empty($v['level']) ) $v['level'] = 0;
		$install = WClass::get( 'install.package' );
		$install->level = (int)$v['level'];
		$install->packages =& $packages;
		$install->main = true;
		$install->parent =& $this;
		$install->list =& $this->list[$k];
		$install->packageId = $k2;
		if (isset($v2['folder'])){
			$install->temp_folder = $v2['folder'];
		}
		$install->processPackagesName();
		$install->loadXml();
		if ( isset($v2['oldVersion']) ) {
			$install->installUpdate = 2;
			$install->previous_version = $v2['oldVersion'];
			$install->newInstall = false; 		}
		$customInstallA = Install_Node_install::accessInstallData( 'get', 'joobiCustomInstall' );
		$install->triggerMethods( $customInstallA[$k][$k2] );
		$this->list[$k][$k2]['custominstall'] = 1;
self::logMessage( 'The custom install for the package '. $package. ' was run.', 'install', false, 0, false );
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
		$EXTENSION = $install->mainext->name;
		$message = 'The custom install of the extension ' . $EXTENSION . ' was run.';
		$namekey_info = '<div style="display:none;">' . $install->mainext->namekey . '</div>';
		$return = 'STATUS['.@$_SESSION['joobi']['install_status'].'] JSUCCESS['.$v['wid'].$v['level'].'] BIGMSG['.$message.$namekey_info.'] ';
		return true;
 	}
 	function customtype(&$return,&$v,&$v2,&$packages,$k,$k2) {
 		$pieces = explode(DS,$v2['filename']);
		$package = $pieces[count($pieces)-1];
		if ( empty($v['level']) ) $v['level'] = 0;
		$install = WClass::get('install.package');
		$install->level = (int)$v['level'];
		$install->packages =& $packages;
		$install->main = true;
		$install->parent =& $this;
		$install->list =& $this->list[$k];
		$install->packageId = $k2;
		if (isset($v2['folder'])){
			$install->temp_folder = $v2['folder'];
		}
		$install->processPackagesName();
		$install->loadXml();
		if ( isset($v2['oldVersion']) ) {
			$install->installUpdate = 2;
			$install->previous_version = $v2['oldVersion'];
			$install->newInstall = false; 		}
		$install->typeCustomFunction();
		$this->list[$k][$k2]['customtype'] = 1;
self::logMessage( 'The type custom install for the package '.$package.' was run.','install', false, 0, false );
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
		$EXTENSION = $install->mainext->name;
		$message = 'The type custom install of the extension '.$EXTENSION.' was run.';
		$namekey_info = '<div style="display:none;">'.$install->mainext->namekey.'</div>';
		$return = 'STATUS['.@$_SESSION['joobi']['install_status'].'] JSUCCESS['.$v['wid'].$v['level'].'] BIGMSG['.$message.$namekey_info.'] ';
		return true;
 	}
 	function checklicense(&$return,&$v,&$v2,&$packages,$k,$k2) {
 		return true;
		$pieces = explode(DS,$v2['filename']);
		$package = $pieces[count($pieces)-1];
		if ( empty($v['level']) ) $v['level'] = 0;
		$install = WClass::get('install.package');
		$install->level = (int)$v['level'];
		$install->packages =& $packages;
		$install->main = true;
		if (isset($v2['folder'])){
			$install->temp_folder = $v2['folder'];
		}
		$install->processPackagesName();
		$install->loadXml();
 		if ( isset($v2['oldVersion']) ) {
			$install->installUpdate = 2;
			$install->previous_version = $v2['oldVersion'];
			$install->newInstall = false; 		}
		$this->list[$k][$k2]['checklicense'] = 1;
self::logMessage( 'The license of the package '.$package.' was validated.','install', false, 0, false );
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
		$EXTENSION = $install->mainext->name;
		$message = str_replace(array('$EXTENSION'), array($EXTENSION),WText::t('1221227936IUXD'));
				if ( empty($message) ) {
			$message = 'The license of the extension '.$EXTENSION.' was validated.';
		}
		$namekey_info = '<div style="display:none;">'.$install->mainext->namekey.'</div>';
		$return = 'STATUS['.@$_SESSION['joobi']['install_status'].'] JSUCCESS['.$v['wid'].$v['level'].'] BIGMSG['.$message.$namekey_info.'] ';
		return true;
 	}
	function transinstall(&$return,&$v,&$v2,&$packages,$k,$k2) {
		if (isset($_SESSION['joobi']['import_step'])){
			$translationProcessC = WClass::get('translation.process');
			$translationProcessC->setDontForceInsert( true );
			$translationProcessC->setFinishTag(false);
			$translationProcessC->importexec();
		}
		$return = 'continue';
		return true;
	}
	function translation(&$return,&$v,&$v2,&$packages,$k,$k2) {
		$namekey = $v2['namekey'];
		$importLangs = Install_Node_install::accessInstallData( 'get', 'importLangs' );
		foreach( $importLangs as $key => $lang) {
			if ( !empty( $v2['langDownloaded'] ) && in_array($lang->code,$v2['langDownloaded'])){
				continue;
			}
						$this->list[$k][$k2]['langDownloaded'][] = $lang->code;
						if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
						if ($lang->code=='en'){
				continue;
			}
						$parts = explode('.',$namekey);
			$APPLICATION = $parts[0];
			$LANGUAGE = $lang->name;
						$senddata = new stdClass;
			$senddata->namekey = $namekey;
			$senddata->language = strtolower( $lang->name );
			$netcom = WNetcom::get();
			$data = $netcom->send( $this->_getDistribSite() , 'repository', 'getDic', $senddata );
						if ( is_array( $data ) ) $data = base64_decode( $data[1] );
			if ( is_string( $data ) ) {
self::logMessage( $lang->name.' language data downloaded for ' . $namekey , 'install', false, 0, false );
				$translationHandler = WClass::get( 'translation.importlang' );
				$translationHandler->auto = 1;
				$translationHandler->setForceInsert( true );
				$translationHandler->importDictionary( $data );	
				$importLangs[$key]->fileFound = true;
				Install_Node_install::accessInstallData( 'set', 'importLangs', $importLangs );
				$message = str_replace(array('$LANGUAGE','$APPLICATION'), array($LANGUAGE,$APPLICATION),WText::t('1227579828FTWS'));
								if ( empty($message) ) {
					$message = 'The '.$LANGUAGE. ' translations were retrieved for the application '.$APPLICATION;
				}
								if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
			} else {
				$messageHandler = WMessage::get();
				$messageHandler->log( $messageHandler->getM(),'install' );
				$message = str_replace(array('$LANGUAGE','$APPLICATION'), array($LANGUAGE,$APPLICATION),WText::t('1227579828FTWT'));
								if ( empty($message) ) {
					$message = $LANGUAGE. ' translations not available for the application '.$APPLICATION;
				}
			}
self::logMessage( $message,'install', false, 0, false );
			$return = 'STATUS['.@$_SESSION['joobi']['install_status'].'] JSUCCESS['.$v['wid'].$v['level'].'] BIGMSG['.$message.'] ';
			return true;
		}
		$this->list[$k][$k2]['translation'] = 1;
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
		$parts = explode('.',$namekey);
		$APPLICATION = $parts[0];
		$message = str_replace(array('$APPLICATION'), array($APPLICATION),WText::t('1227579828FTWU'));
				if ( empty($message) ) {
			$message = 'All translations retrieved for the application '.$APPLICATION;
		}
self::logMessage( $message, 'install', false, 0, false );
		$return = 'STATUS['.@$_SESSION['joobi']['install_status'].'] JSUCCESS['.$v['wid'].$v['level'].'] BIGMSG['.$message.'] ';
		return true;
	}
	function clean($removeFolder=true) {
		$addon = WAddon::get( 'install.'.JOOBI_FRAMEWORK );
		if ( is_object($addon) && method_exists( $addon, 'clean' ) ) {
			if ( !$addon->clean( $removeFolder ) ) return false;
		}
		if ( !class_exists('Install_Node_install') ) WLoadFile( 'install.install.install', JOOBI_DS_NODE );
				Install_Node_install::accessInstallData( 'delete', 'installparams' );
		Install_Node_install::accessInstallData( 'delete', 'importlangs' );
				$folderC = WGet::folder();
		$folderC->delete( JOOBI_DS_ROOT . 'tmp' . DS . 'cache' );			$folderC->create( JOOBI_DS_ROOT . 'tmp' . DS . 'cache' );
				$folderC->delete( JOOBI_DS_USER . 'progress' );			
				$cC = WCache::get();
		$cC->resetCache();
		return true;
	}
	function checkServerAvail($server='',$force=false) {
		static $available=array();
		if ( empty($server) ) {
			$server = $this->_getDistribSite();
		}
										if (!isset($available[$server]) && isset($_SESSION['joobi']['server_available']) ) {
			$available[$server] = $_SESSION['joobi']['server_available'];
		}
		if ( !isset($available[$server])  || $force) {
			$netcom = WNetcom::get();
			$response = $netcom->send( $server, 'netcom', 'ping',false );
			$available[$server] = ($response === true) ? true : false;
			if (isset($_SESSION)) {
				$_SESSION['joobi']['server_available'][$server] = $available[$server];
			}		}
		if ( !$available[$server] ) {
			$message = WMessage::get();
			$EMAIL = 'support@joobi.co';
			$message->userN('1213794916KBVX');
			$message->userN('1213794916KBVY',array('$EMAIL'=>$EMAIL));
		}
		return $available[$server];
	}
 	function extract(&$return,&$v,&$v2,&$packages,$k,$k2) {
		$pieces = explode(DS,$v2['filename']);
		$package = $pieces[count($pieces)-1];
		if ( empty($v['level']) ) $v['level'] = 0;
		$installPackageC = WClass::get('install.package');
		$installPackageC->level = (int)$v['level'];
		$installPackageC->parent =& $this;
		$installPackageC->list =& $this->list[$k];
		$installPackageC->packageId = $k2;
		self::logMessage( 'Extracting the package '.$package, 'install', false, 0, false );
		if ( $this->_catchMessage ) ob_start();
		$status = $installPackageC->extract( $v2['filename'] );
		if ( $this->_catchMessage ) {
			$errors = ob_get_contents();
			ob_end_clean();
			$mess = WMessage::get();
			$errors .= $mess->getM();
		} else {
			$errors = '';
		}
		if ( !$status ) {
self::logMessage( 'An error occurred during the extracting process of the package '."\r\n".$errors,'install', false, 0, false );
			$this->updatePref(0);
			$return = 'FINERROR STATUS['.@$_SESSION['joobi']['install_status'].'] JERROR['.$v['wid'].$v['level'].'] BIGMSG[';
			$return .= 'An error occurred during the extracting process of the package:' . ' '.$package;
			$return .= '<br/>'.$errors.'] ';
			return true;
		}
		$this->list[$k][$k2]['folder'] = $installPackageC->getDestination();
		$this->list[$k][$k2]['destination'] = $installPackageC->getDestination(false);
		$this->list[$k][$k2]['extract'] = 1;
self::logMessage( 'The folder  ' . $this->list[$k][$k2]['folder'] ,'install', false, 0, false );
self::logMessage( 'The destination  ' . $this->list[$k][$k2]['destination'] ,'install', false, 0, false );
		if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
				$parts=explode('_',$package);
		if ($parts[0]=='lib'){
			$parts[1]='lib';
		}		$PACKAGE =$parts[0] . ' ' . $parts[1];
		$message = str_replace(array('$PACKAGE'), array($PACKAGE),WText::t('1215002067FIWP'));
				if ( empty($message) ) {
			$message = 'The package '.$PACKAGE.' was extracted.';
		}
				$namekey_info = '<div style="display:none;">'.$installPackageC->mainext->namekey.'</div>';
		$return = 'STATUS['.@$_SESSION['joobi']['install_status'].'] BIGMSG['.$message.$namekey_info.'] ';
		return true;
 	}
 	public function installPackages($catchMessage=true) {
 		$this->_catchMessage = $catchMessage;
 				$this->getPref();
								if ( !in_array( $this->mode, array( 'install',' createtable' ) ) ) {
			WPref::get('install.node');
		}
self::logMessage( 'installPackages function, list of packages' ,'install', false, 0, false );
		if ( !empty($this->list) ) {
									foreach( $this->list as $k => $onePackageInfoA ) {
				$packages = $this->_getPackages( $onePackageInfoA );
				foreach( $onePackageInfoA as $property => $value ) {
					if ( in_array( (string)$property, array('level','wid') ) ) {
							continue;					}
					if ( empty($value) ) continue;
					$mode = $this->mode;
										if ( !empty($value[$mode]) ) {
						continue;
					}
					$return = null;
					$this->$mode( $return, $onePackageInfoA, $value, $packages, $k, $property );
					if ( is_string($return) ) {
																								if ( $return == 'continue' || 'createtable' == $mode ) {
							continue;
						} else {
							return $return;
						}
					}
				}
			}
		}
				$function = 'finish_' . $this->mode;
self::logMessage( 'installPackages function  finish_' . $this->mode , 'install', false, 0, false );
		return $this->$function();
 	}
	function addUpdateDependencies() {
		$extensionDepCache = Install_Node_install::accessInstallData( 'get', 'extensionDeps' );
		if ( empty( $extensionDepCache ) ) return true;
		$appsM = WTable::get( 'extension_node', 'main_library', 'wid' );
		$appsDepM = WTable::get( 'extension_dependency', 'main_library', 'exdpid' );
		foreach( $extensionDepCache as $key => $deps ) {
			$mainWid = WExtension::get( $key, 'wid' );
			if ( empty($mainWid) ) continue;
						$appsDepM->whereE( 'wid', $mainWid );
			$appsDepM->delete();
			if ( empty( $deps ) ) continue;
						$appsM->select( $mainWid, 0, 'wid', 'val' );
			$appsM->select( 'wid', 0, 'ref_wid' );
			$appsM->whereIn( 'namekey', $deps );
			$depWids = $appsM->printQ( 'load' );
			$appsDepM->setIgnore();
			$appsDepM->insertSelect( array( 'wid', 'ref_wid' ), $depWids );
			$appsM->resetAll();
			$appsDepM->resetAll();
		}
		Install_Node_install::accessInstallData( 'delete', 'extensionDeps' );
		return true;
	}
	public static function logMessage($message,$location='install',$not1=null,$not2=null,$not3=null,$not4=null) {
		static $installDebug = null;
		if ( !isset($installDebug) ) {
			if ( !defined('PINSTALL_NODE_INSTALLDEBUG') ) {
				if ( class_exists( 'WPref') ) {
					WPref::get( 'install.node' );
				}			}			$installDebug = ( defined('PINSTALL_NODE_INSTALLDEBUG') ? PINSTALL_NODE_INSTALLDEBUG : false );
		}
		if ( $installDebug ) {
			WMessage::log( $message, $location );			}
	}
	private function _getLinkToAutomaticalyRedirect() {
		$returnObj = new stdClass;
		reset( $this->list );
		$subArry = current( $this->list );
				if ( !empty($subArry['wid']) ) $wid = $subArry['wid'];
		else {
						$SID = WModel::get( 'install.apps', 'sid' );
			$trk = WGlobals::get( JOOBI_VAR_DATA );
			$wid = $trk[$SID]['wid'];
		}
		$folder = WExtension::get( $wid, 'folder' );
		$folder = strtolower($folder);
		if ( empty($folder) ) {
self::logMessage( ' $wid= '.print_r($wid, true),  'error_' . __FUNCTION__ );
self::logMessage( ' $folder= '.print_r($folder, true),  'error_' . __FUNCTION__ );
			return false;
		}
				$returnObj->link = $folder;
		if ( 'apps' == $folder ) {
			$url =  WPage::routeURL( 'controller=' . $folder, 'smart', 'default', false, false, JOOBI_MAIN_APP );
		} else {
			$url =  WPage::routeURL( 'controller=' . $folder, 'smart', 'default', false, false, $folder );
		}
				$returnObj->url = $url;
				WGlobals::setSession( 'installRedirectInfo', 'alex' , $returnObj->url );
		return $returnObj;
	}
 	private function _overwriteEntryPoint() {
 		if ( JOOBI_FRAMEWORK_TYPE == 'joomla' ) {
			$cms_addon = JOOBI_DS_USER . 'installfiles' . DS . 'node'. DS . 'install' . DS . 'addon' . DS . JOOBI_FRAMEWORK . DS . JOOBI_FRAMEWORK . '.php';
			$entry_file = JOOBI_DS_ROOT . 'administrator' . DS . 'components' . DS . 'com_' . JOOBI_MAIN_APP . DS . JOOBI_MAIN_APP . '.php';
			if ( file_exists( $cms_addon ) ) {
self::logMessage( 'Overwrite entry point file ' . $entry_file . ' using cms addon file ' . $cms_addon, 'install', false, 0, false  );
				require_once( $cms_addon );
				$fileHandler = WGet::file();
				$className = 'Install_' . ucfirst( JOOBI_FRAMEWORK ) . '_addon';
				switch( $className ) {
					default:
						$fileHandler->write( $entry_file, $className::magicFile(), 'force' );
						break;
				}
			} else {
self::logMessage( 'skipping entry point overwrite, no cms addon file at ' . $cms_addon, 'install', false, 0, false  );
			}
		}
 	}
 	private function _getDistribSite() {
		$netcomServerC = WClass::get('netcom.server');
		return $netcomServerC->checkOnline( true );
 	}
	private function _getListOfPackages() {
		$app2InstallA = null;
		$alreadyInstalledA = null;
		$mess = $this->_getSelectedApplications( $app2InstallA, $alreadyInstalledA );
		if ( !empty($mess) ) {
			return $mess;
		}
		self::logMessage( 'Send list request to '. $this->_getDistribSite() ,'install', false, 0, false );
		$netcom = WNetcom::get();
		$sentData = new stdClass;
		$sentData->site = JOOBI_SITE;
		$sentData->cms = JOOBI_FRAMEWORK;
		$sentData->cmsFramework = JOOBI_FRAMEWORK_TYPE;
		$sentData->cms_version = WApplication::version( 'short' );
		$sentData->database_name = WGet::DBType(); 			if ( defined('JOOBI_DB_VERSION') ) $sentData->database_version = JOOBI_DB_VERSION;
		$sentData->php_version = phpversion();
		$sentData->ip = @gethostbyname( parse_url( JOOBI_SITE, PHP_URL_HOST ) );
		$appsInfoC = WClass::get( 'apps.info' );
		$sentData->url = $appsInfoC->myURL();
		$what2Update = WGlobals::getSession( 'installProcess', 'what', 'single' );
		$getPossibleCode = true;
		if ( 'single' == $what2Update ) {				$extensionNamekeySent = $app2InstallA[0]->namekey;
			$getPossibleCode = $extensionNamekeySent;
			$token = WExtension::get( $extensionNamekeySent, 'token' );
			if ( !empty($token) ) $sentData->token = $token;
			$sentData->type = 'single';			} else {
			if ( $what2Update == 'multiple' ) {
				$sentData->type = 'multiple';									$appsM = WModel::get( 'install.apps' );
				$appsM->whereE( 'publish', 1 );
				$appsM->whereE( 'type', 1 );
				$app2InstallA = $appsM->load( 'ol', 'namekey' );
			} else {
				$sentData->type = 'single';				}
		}
		if ( empty($sentData->token) ) {
						$status = $appsInfoC->getPossibleCode( $getPossibleCode, 'token' );				if ( $status === false ) {
self::logMessage( 'There is no valid token to do this update!' ,'install' );
				return $this->_finishWithError( WText::t('1427652813TJJQ') );
			}			$sentData->token = $status;
		}
				$newData = $this->_optmizedSendData( $alreadyInstalledA );
		$sentData->alreadyInstalled = serialize( $newData );	
		$sentData->desiredApp = serialize( $app2InstallA );	
		if ( !empty( $_SESSION['joobi']['repositoryid'] ) ) $sentData->reposid = $_SESSION['joobi']['repositoryid'];
self::logMessage( 'Getting list of packages' ,'install' );
		$receivedData = $netcom->send( $this->_getDistribSite(), 'repository', 'getOptimizedList', $sentData );
self::logMessage( 'Received list of packages' ,'install' );
		if ( is_object( $receivedData ) ) {
self::logMessage( 'Receive the package is object' ,'install' );
			if ( $receivedData->type == 'success' ) {
self::logMessage( 'Successfully receive the packages' ,'install' );
				$tempArary = array();
				$unseraibleMe = unserialize( $receivedData->data );
				if ( empty($unseraibleMe) ) {
					self::logMessage( 'Response succesful but no packages was returned', 'install' );
					$this->_setMessage( 'html', WText::t('1427986835NTMI'), 'failed' );
					return false;
				}
				if ( is_array($unseraibleMe) ) {
															$extensionReceviedA = array();
					foreach( $unseraibleMe as $oneUnseraibleMe ) {
						$extensionReceviedA[$oneUnseraibleMe->e] = $oneUnseraibleMe;
					}
					$existingExtensionA = array();
										if ( !empty($existingExtensionA) ) {
						foreach( $existingExtensionA as $oneExisting ) {
							$newVErsion = $extensionReceviedA[$oneExisting->namekey]->n;								if ( $newVErsion <= $oneExisting->version ) {
																unset($extensionReceviedA[$oneExisting->namekey]);
							}						}					}
					$sortedExtA = array();
										foreach( $alreadyInstalledA as $oneApp ) $sortedExtA[$oneApp->namekey] = $oneApp->version;
					foreach( $extensionReceviedA as $onePack ) {
						$obj = new stdClass;
						$obj->namekey = $onePack->e;
						$obj->version = $onePack->n;
						$obj->vsid = $onePack->v;
						if ( isset($sortedExtA[$onePack->e]) ) $obj->oldVersion = $sortedExtA[$onePack->e];
						if ( !empty($onePack->u) ) $obj->url = $onePack->u;
						$this->_packageToInstallA[] = $obj;
					}
					if ( !empty($this->_packageToInstallA) ) {
						$status = Install_Node_install::accessInstallData( 'set', 'installExtension', $this->_packageToInstallA );
						if ( ! $status ) {
							$this->_setMessage( 'html', 'Failed to save list of extension...', 'failed' );
						}					} else {
						$this->_setMessage( 'html', 'No package to install...', 'failed' );
					}
				} else {
					self::logMessage( 'The distribution site could not provide the list of extensions for repository: ' . $receivedData->reposid, 'install', false, 0, false );
					self::logMessage( $receivedData, 'install', false, 0, false );
					self::logMessage( $sentData, 'install', false, 0, false );
					return $this->_finishWithError( 'Wrong format of packages.' );
				}
			} elseif ( $receivedData->type == 'error2' ) {
self::logMessage( 'Fail to receive the packages' ,'install' );
				return $this->_dealWithTokenIssue( $receivedData );
			} elseif ( $receivedData->type == 'error' ) {
self::logMessage( 'Fail to receive the packages' ,'install' );
self::logMessage( 'The distribution site could not provide the list of extensions: ', 'install' );
self::logMessage( $receivedData, 'install' );
				return $this->_finishWithError( $receivedData->message );
			}
		} else {
self::logMessage( 'Could not retrieve the list of packages: ' . $receivedData->reposid, 'install', false, 0, false );
self::logMessage( $receivedData, 'install', false, 0, false );
			$this->_setMessage( 'html', WText::t('1236536983NQDJ'), 'failed' );
			return $this->_finishWithError( WText::t('1236536983NQDJ') );
		}
		$this->_setMessage( 'html', WText::t('1427652813TJJR') );
	}
	private function _getSelectedApplications(&$app2InstallA,&$alreadyInstalledA) {
self::logMessage( 'Check selected applications' );
						$extsid = WModel::get( 'install.apps', 'sid' );
			$lwidtab = 'wid_' . $extsid;
			$to_install = WGlobals::get( $lwidtab );
						if ( empty( $to_install ) ) {
				$wid = WForm::getPrev( 'wid' );
								if ( empty($wid) ) $wid = WExtension::get( JOOBI_MAIN_APP . '.application', 'wid' );
				$to_install = array( $wid );
			}
			if ( !isset($to_install) ) {
				$this->_setMessage( 'html', WText::t('1213107629LYER'), 'failed' );
				return false;
			}
self::logMessage( 'Get info on each extension'  );
self::logMessage( $to_install );
			$app2InstallA = $this->_getApplicationsInfo( $to_install );
			if ( is_string($app2InstallA) ) {
				return $app2InstallA;
			}
self::logMessage( 'Get the packages already installed' );
						$alreadyInstalledA = $this->_alreadyInstalled();
		return '';
	}
	private function _alreadyInstalled() {
		$sql = WTable::get( 'extension_node', 'main_library', 'wid' );
		$sql->whereE( 'publish', 1 );
		$sql->whereIn( 'type' , array( 1, 150 ) );
		return $sql->load( 'ol', array( 'destination', 'folder', 'namekey', 'version' ) );
	}
	private function _addLevelToList(&$app2InstallA){
		$i = 0;
		foreach( $app2InstallA as $k ) {
						$levelToInstall=WGlobals::get( 'levelInstall' );
			if ( ! $levelToInstall ) {
				$sql = WModel::get( 'install.apps', 'object' );
				$sql->makeLJ( 'install.appslevel', 'wid' );
				$sql->whereE( 'folder', $k->folder );
				$sql->whereE( 'destination', $k->destination );
				$sql->select( 'level', 1, 'level', 'MAX' );
				$sql->select( 'wid' );
				$sql->groupBy( 'wid', 1 );
				$levelToInstall = $sql->load( 'o' );
				if ( ! is_object( $levelToInstall ) ) {
					continue;
				}
			}
			$this->list[$i]['level'] = $levelToInstall->level;
			$this->list[$i]['wid'] = $levelToInstall->wid;
			$i++;
		}
	}
	private function _getApplicationsInfo(&$to_install) {
		$app2InstallA = array();
		$sql = WTable::get( 'extension_node', 'main_library', 'wid' );
		if ( !empty($to_install) ) {
						foreach( $to_install as $k ) {
				$sql->whereE( 'wid', $k );
				$data = $sql->load( 'o', array( 'folder', 'destination', 'namekey' ) );
				if ( !is_object( $data ) ){
					return $this->_finishWithError( 'Could not get any information for the package ID ' .$k );
				}
				$extension = new stdClass;
				$extension->wid = $k;
				$extension->newInst = true;
				$this->installing[] = $extension;
				$app2InstallA[] = $data;
			}
		} else {
			return $this->_finishWithError( 'No application selected in _getApplicationsInfo' );
		}
		return $app2InstallA;
	}
	private function _extractPackage($nb) {
				$this->getPref();
		$extensionNb = $nb - 1;
		if ( !isset($this->_packageToInstallA[$extensionNb]) ) {
			Install_Processnew_class::logMessage( 'ERROR: -- the package is not available', 'install' );			
			return false;
		}
		$extensionInfoO = $this->_packageToInstallA[$extensionNb];
		if ( empty($extensionInfoO) ) {
Install_Processnew_class::logMessage( 'ERROR: -- The package information was not found when extracting...', 'install' );			
			$this->_setMessage( 'html', 'The package information was not found when extracting...', 'failed' );
			return false;
		}
		$PACKAGE_NAME = $extensionInfoO->namekey . ' ' . $extensionInfoO->version;
Install_Processnew_class::logMessage( 'Extracting package ' . $PACKAGE_NAME, 'install' );		
				$filename = JOOBI_DS_USER . 'downloads' . DS . 'packages' . DS . str_replace( '.', '_', $extensionInfoO->namekey ) . '_' . $extensionInfoO->version . '.tar.gz';
		$installPackageC = WClass::get( 'install.package' );
		$status = $installPackageC->extract( $filename );
		if ( $status ) {
			$this->_setMessage( 'append', '<br>' . str_replace(array('$PACKAGE_NAME'), array($PACKAGE_NAME),WText::t('1427652813TJJS')) );
Install_Processnew_class::logMessage( 'Extracting package result: ' . "The package $PACKAGE_NAME was successfully extracted.", 'install' );			
		} else {
			$this->_setMessage( 'append', '<br>' . str_replace(array('$PACKAGE_NAME'), array($PACKAGE_NAME),WText::t('1427652813TJJT')), 'failed' );
Install_Processnew_class::logMessage( 'Extracting package result: ' . "The package $PACKAGE_NAME could not be extracted.", 'install' );			
		}		
		return $status;
	}
	private function _downloadLanguages($nb) {
				$this->getPref();
		$extensionNb = $nb - 1;
		$extensionInfoO = $this->_packageToInstallA[$extensionNb];
		if ( empty($extensionInfoO) ) {
			$this->_setMessage( 'html', 'The package information was not found when extracting...', 'failed' );
			return false;
		}
		$extA = explode( '.', $extensionInfoO->namekey );
		if ( $extA[1] != 'application' ) return true;
		$namekey = $extensionInfoO->namekey;
		$PACKAGE_NAME = $extensionInfoO->namekey;
self::logMessage( 'Download Language for ' . $PACKAGE_NAME , 'install' );
		$explodeKeyA = explode( '.', $extensionInfoO->namekey );
self::logMessage( 'Package type : ' . $explodeKeyA[1], 'install' );
		switch( $explodeKeyA[1] ) {
			case 'node':
			case 'application':
				$folder = 'node';
				break;
			case 'includes':
			default:
				return true;
				break;
		}
		$importLangs = Install_Node_install::accessInstallData( 'get', 'importLangs' );
self::logMessage( 'Downloading language for extension' . $extensionInfoO->namekey , 'install' );
		$status = false;
		foreach( $importLangs as $key => $lang ) {
						if ( $lang->code=='en' ) continue;
						$parts = explode( '.', $namekey );
			$APPLICATION = $parts[0];
			$LANGUAGE = $lang->name;
self::logMessage( 'Request Language for ' . $PACKAGE_NAME , 'install' );
						$senddata = new stdClass;
			$senddata->namekey = $namekey;
			$senddata->language = strtolower( $lang->name );
			$netcom = WNetcom::get();
			$data = $netcom->send( $this->_getDistribSite() , 'repository', 'getDic', $senddata );
self::logMessage( 'Received Language for ' . $PACKAGE_NAME , 'install' );
			if ( is_array( $data ) ) $data = base64_decode( $data[1] );
			if ( is_string( $data ) ) {
self::logMessage( $lang->name . ' language data downloaded for ' . $namekey , 'install' );
				$translationHandler = WClass::get( 'translation.importlang' );
				$translationHandler->auto = 1;
				$translationHandler->setForceInsert( true );
				$translationHandler->importDictionary( $data );	
				$message = 'The '.$LANGUAGE. ' translations were retrieved for the application '.$APPLICATION;
				$status = true;
			} else {
				$messageHandler = WMessage::get();
				$messageHandler->log( $messageHandler->getM() ,'install' );
self::logMessage( 'Language file not available for the extension ' . $extensionInfoO->namekey , 'install' );
				$status = true;				}
self::logMessage( $status, 'install' );
		}
		if ( $status ) {
			$this->_setMessage( 'append', '<br>' . str_replace(array('$PACKAGE_NAME'), array($PACKAGE_NAME),WText::t('1427652814RAOH')) );
		} else {
			$this->_setMessage( 'append', '<br>' . str_replace(array('$PACKAGE_NAME'), array($PACKAGE_NAME),WText::t('1427652814RAOI')), 'failed' );
		}
		return $status;
	}
	private function _createCMSMenu() {
self::logMessage( 'starting CMS menu creation', 'install' );
				$this->getPref();
		foreach( $this->_packageToInstallA as $key => $extensionInfoO ) {
			$PACKAGE_NAME = $extensionInfoO->namekey . ' ' . $extensionInfoO->version;
			$install = WClass::get( 'install.package' );
			$explodeKeyA = explode( '.', $extensionInfoO->namekey );
self::logMessage( '999 . CMS menu creation for ' . $PACKAGE_NAME, 'install' );
self::logMessage( $explodeKeyA , 'install' );
			if ( ! in_array( $explodeKeyA[1], array('application', 'widget', 'pack' ) ) )continue;
			switch( $explodeKeyA[1] ) {
				case 'application':
					$folder = 'node';
					break;
				case 'pack':
					$folder = 'pack';
					break;
				case 'node':
				case 'includes':
				default:
					continue;
					break;
			}
self::logMessage( 'starting CMS menu creation for ' . $PACKAGE_NAME, 'install' );
			$install->temp_folder = JOOBI_DS_NODE . $explodeKeyA[0];
			$install->extension_namekey = $extensionInfoO->namekey;
			$install->loadXml();
self::logMessage( 'Call triggerCMS', 'install' );
			$status = $install->triggerCMS();
self::logMessage( $extensionInfoO, 'install' );
					if ( empty( $extensionInfoO->oldVersion ) ) {
				$wid = WExtension::get( $extensionInfoO->namekey, 'wid' );
self::logMessage( $wid, 'install' );
								$extensionT = WTable::get( 'extension_node' );
				$extensionT->whereE( 'wid', $wid );
				$extensionT->whereE( 'created', 0 );
				$extensionT->setVal( 'created', time() );
				$extensionT->update();
			}
self::logMessage( 'The CMS menu for the package '. $PACKAGE_NAME . ' was created.', 'install' );
			if ( $status ) {
				$this->_setMessage( 'append', '<br>' . str_replace(array('$PACKAGE_NAME'), array($PACKAGE_NAME),WText::t('1427739324NDEY')) );
			} else {
				$this->_setMessage( 'append', '<br>' . str_replace(array('$PACKAGE_NAME'), array($PACKAGE_NAME),WText::t('1427739324NDEZ')), 'failed' );
			}
		}
		return true;
	}
	private function _customFunction($nb) {
				$this->getPref();
		$extensionNb = $nb - 1;
		$extensionInfoO = $this->_packageToInstallA[$extensionNb];
		if ( empty($extensionInfoO) ) {
			$this->_setMessage( 'html', 'The package information was not found when run custom functions...', 'failed' );
			return false;
		}
		$PACKAGE_NAME = $extensionInfoO->namekey . ' ' . $extensionInfoO->version;
self::logMessage( 'starting custom install for ' . $PACKAGE_NAME, 'install' );
		$install = WClass::get( 'install.package' );
		$explodeKeyA = explode( '.', $extensionInfoO->namekey );
self::logMessage( 'Package type : ' . $explodeKeyA[1], 'install' );
		switch( $explodeKeyA[1] ) {
			case 'node':
			case 'application':
				$folder = 'node';
				break;
			case 'includes':
			default:
				return true;
				break;
		}
				$install->temp_folder = JOOBI_DS_NODE . $explodeKeyA[0];
		$install->extension_namekey = $extensionInfoO->namekey;
		$install->loadXml();
		if ( !empty( $extensionInfoO->oldVersion ) ) {
			$install->installUpdate = 2;
			$install->previous_version = $extensionInfoO->oldVersion;
			$install->newInstall = false;
		} else {
			$install->newInstall = true;
		}
		$install->initCustomInstance();
		$methods = $install->customInstallMethods();
		$status = $install->triggerMethods( $methods );
self::logMessage( 'The custom install for the package '. $PACKAGE_NAME . ' was run.', 'install', false, 0, false );
		if ( $status ) {
			$this->_setMessage( 'append', '<br>' . str_replace(array('$PACKAGE_NAME'), array($PACKAGE_NAME),WText::t('1427652814RAOJ')) );
		} else {
			$this->_setMessage( 'append', '<br>' . str_replace(array('$PACKAGE_NAME'), array($PACKAGE_NAME),WText::t('1427652814RAOK')), 'failed' );
		}
		return $status;
	}
	private function _createTables($nb) {
				$this->getPref();
		$extensionNb = $nb - 1;
		$extensionInfoO = $this->_packageToInstallA[$extensionNb];
		if ( empty($extensionInfoO) ) {
			$this->_setMessage( 'html', 'The package information was not found when creating tables...', 'failed' );
			return false;
		}
		$PACKAGE_NAME = $extensionInfoO->namekey . ' ' . $extensionInfoO->version;
		$install = WClass::get( 'install.package' );
		$explodeKeyA = explode( '.', $extensionInfoO->namekey );
self::logMessage( 'Create tables extension ' . $extensionInfoO->namekey, 'install' );
self::logMessage( $explodeKeyA, 'install' );
		switch( $explodeKeyA[1] ) {
			case 'node':
			case 'application':
				$hasTables = true;
				$base = JOOBI_DS_NODE;
				break;
			case 'includes':					$hasTables = false;
				$base = JOOBI_DS_INC;
				break;
			default:
self::logMessage( 'Tables NOT created for extension ' . $extensionInfoO->namekey, 'install' );
				return true;
				break;
		}
		$status = true;
		if ( $hasTables ) {
						$install->temp_folder = JOOBI_DS_NODE . $explodeKeyA[0];
			$install->extension_namekey = $extensionInfoO->namekey;
			$install->loadXml();
self::logMessage( 'Create tables for the package ' . $extensionInfoO->namekey, 'install' );
			$status = $install->loadRealSQL();
		}
		$file = $base . $explodeKeyA[0] . DS . 'database' . DS . 'data' . DS . $extensionInfoO->namekey . '_data_mysql.sql';
		$fileS = WGet::file();
		if ( ! $fileS->exist( $file ) ) {
self::logMessage( 'No SQL file found : ' . $file, 'install' );
			return true;
		}
		$dbHandler = WClass::get( 'install.database' );
		$dbHandler->setResaveItemMoveFile();
		$status = $dbHandler->importFile( $file );
		if ( $status ) {
self::logMessage( 'The tables were created for the package ' . $extensionInfoO->namekey );
			$this->_setMessage( 'append', '<br>' . str_replace(array('$PACKAGE_NAME'), array($PACKAGE_NAME),WText::t('1427652814RAOL')) );
		} else {
self::logMessage( 'No tables to create for the package ' . $extensionInfoO->namekey );
					}
		return $status;
	}
	private function _downloadPackages($nb) {
				$this->getPref();
		$extensionNb = $nb - 1;
		if ( !isset($this->_packageToInstallA[$extensionNb]) ) {
			$this->_setMessage( 'html', 'The package was not found in the list...', 'failed' );
			return false;
		}
		$extensionInfoO = $this->_packageToInstallA[$extensionNb];
		if ( empty($extensionInfoO) ) {
			$this->_setMessage( 'html', 'The package information was not found when downloading packages...', 'failed' );
			return false;
		}
		$PACKAGE_NAME = $extensionInfoO->namekey . ' ' . $extensionInfoO->version;
				$file = WGet::file();
		$filename = JOOBI_DS_USER . 'downloads' . DS . 'packages' . DS . str_replace( '.', '_', $extensionInfoO->namekey ) . '_' . $extensionInfoO->version . '.tar.gz';
self::logMessage( 'Location to write the package : ' . $filename , 'install' );
		if ( $file->exist($filename) ) {
self::logMessage( 'Package already available for : ' . $extensionInfoO->namekey . '_' . $extensionInfoO->version , 'install' );
			$this->_setMessage( 'append', '<br>' . str_replace(array('$PACKAGE_NAME'), array($PACKAGE_NAME),WText::t('1427652814RAON')) );
			return true;
		}
		if ( !empty($extensionInfoO->url) ) {
					$netcomRestC = WClass::get( 'netcom.rest' );
			$filecontent = $netcomRestC->downloadFileFromURL( $this->_getDistribSite() . strtolower( $extensionInfoO->url ) );
		}
		if ( empty($filecontent) ) {
						$extHelper = WClass::get( 'apps.helper' );
			$sentData = new stdClass;
			$sentData->vsid = $extensionInfoO->vsid;
			$appsInfoC = WClass::get( 'apps.info' );
			$sentData->url = $appsInfoC->myURL();
			if ( !empty( $_SESSION['joobi']['repositoryid'] ) ) $sentData->reposid = $_SESSION['joobi']['repositoryid'];
self::logMessage( 'Downloading : ' . $extensionInfoO->namekey . '_' . $extensionInfoO->version , 'install' );
self::logMessage( $sentData, 'install' );
			$netcom = WNetcom::get();
			$receivedData = $netcom->send( $this->_getDistribSite(), 'repository', 'packageOnePackage', $sentData );
			if ( $receivedData == null || $receivedData->type == 'error' ) {
self::logMessage( 'Package download problem: communication error', 'install' );
self::logMessage( $receivedData, 'install' );
				return $this->_dealWithTokenIssue( $receivedData );
			} elseif ( $receivedData->type == 'error2' ) {
				return $this->_dealWithTokenIssue( $receivedData );
			}
			$resp = $receivedData->data;
						$filecontent = base64_decode( $resp[3][1] );
		} else {
self::logMessage( 'The packge downloaded with URL', 'install' );
		}
		if ( empty( $filecontent ) ) {
self::logMessage( 'The packge was empty and could not be downloaded', 'install' );
			$this->_setMessage( 'append', '<br>' . str_replace(array('$PACKAGE_NAME'), array($PACKAGE_NAME),WText::t('1428371836FLXB')) );
			return false;
		}
		if ( ! $file->write( $filename, $filecontent, 'force' ) ) {
self::logMessage( 'ERROR writing the file : ' . $filename, 'install' );
						$PATH = $filename;
			$this->_setMessage( 'html', WText::t('1298294193EGSU') . $PATH, 'failed' );
			return false;
		}
self::logMessage( 'Package ' . $PACKAGE_NAME . ' downloaded successfully', 'install', false, 0, false );
		$this->_setMessage( 'append', '<br>' . str_replace(array('$PACKAGE_NAME'), array($PACKAGE_NAME),WText::t('1395716605OVVZ')) );
		return true;
	}
	private function _finishWithError($message) {
		$this->_setMessage( 'html', $message, 'failed' );
		self::logMessage( 'Finish with errors: ' . $message, 'install' );
		return false;
	}
	private function _dealWithTokenIssue($receivedData) {
self::logMessage( 'Package download problem:', 'install', false, 0, false );
self::logMessage( $receivedData, 'install', false, 0, false );
				if ( !empty($receivedData->errorCode) ) {
			$mainMessage = WText::t('1389701766DFGM');
			switch($receivedData->errorCode) {
				case 'tokenExpired':
					if ( !empty($receivedData->requested) && 'freeDemo' == $receivedData->requested ) $receivedData->message = WText::t('1389965492NTIZ');
					else $receivedData->message = WText::t('1389965492NTJA');
					break;
				case 'tokenNotPublished':
					$receivedData->message = WText::t('1389965492NTJB');
					break;
				case 'tokenNotExist':
					$receivedData->message = WText::t('1389965492NTJC');
					break;
				case 'tokenUnknownError':
					$receivedData->message = WText::t('1389965492NTJD');
					break;
				case 'tokenNotValid':
					$receivedData->message = WText::t('1389965492NTJE');
					break;
			}
					} elseif ( !empty($receivedData->code) ) {
			$mainMessage = WText::t('1389988802RQBQ');
			switch($receivedData->code) {
				case 'REPOPACK404':
					$receivedData->message = WText::t('1389988802RQBR') . '<br />' . $receivedData->message;
					break;
				default:
					break;
			}		}
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
		$LINK = '<a target="_blank" href="http://joobi.info/support">' . WText::t('1389701766DFGL') . '</a>';
		$myMess = '<span style="color:red;">'. $mainMessage . '</span><br>';
		if ( !empty( $receivedData->message ) ) {
			$myMess .= ' ' . $receivedData->message . ' ';
		}		if ( !empty( $receivedData->token ) ) {
			$myMess .= '<br> ' . WText::t('1389275714NKEJ') . ': ' . $receivedData->token . '<br> ';
		}		$myMess .= '<br>' . $LINK . '<br>';
		$mess = WText::t('1427652814RAOO');
		$mess .= '<br>' . $LINK;
		$mess .= '<br>' . $mainMessage;
		if ( !empty( $receivedData->message ) ) $mess .= '<br>' . $receivedData->message;
		if ( !empty( $receivedData->token ) ) $mess .= '<br>' . WText::t('1389275714NKEJ') . ': ' . $receivedData->token;
		$this->_setMessage( 'html', $mess, 'failed' );
		return $myMess;
	}
 	private function _searchPreferencesPageForNewlyInstalledApps() {
 		$appToSetup='';
self::logMessage( $this->installing, 'install', false, 0, false );
		if ( count($this->installing) >0 ) {
			foreach($this->installing as $extapp){
								if ( $extapp->newInst ) {
					$application=WExtension::get( $extapp->wid,'data');
										WPref::get($application->namekey);
										$constant='P'.strtoupper(str_replace( '.', '_', $application->namekey)).'_INSTALLED';
self::logMessage( 'checking constant '.$constant,'install', false, 0, false );
					if (defined($constant)){
						$myConstant = constant($constant);
self::logMessage( 'constant '.$constant.' exists','install', false, 0, false );
												if ( $myConstant<1 ) {
							$mycontroller = explode( '.',$application->namekey );
							$url = WPage::routeURL('controller='.$application->folder.'&task=setup','smart','default',false,true,$application->folder);
							$_SESSION['joobi']['config_urls'][] = $url;
														if (empty($appToSetup)){
self::logMessage( 'redirecting to '.$url,'install', false, 0, false );
																$appToSetup='SETUPPAGE['.$url;
							}
						}					}				}
			}		}
				if ( !empty($appToSetup) ) $appToSetup.=']';
		return $appToSetup;
 	}
	private function _getPackages(&$packagesA) {
		$list = array();
		foreach( $packagesA as $p ) {
			if ( !is_array($p) || empty( $p['filename'] ) ) continue;	
			if ( !empty( $p['installed'] ) ) {
				$list[] = array( $p['filename'], 'installed' );
				continue;
			}
			$list[] = $p['filename'];
		}
		return $list;
	}
private function _optmizedSendData($data) {
	if ( empty($data) ) return $data;
	$newData = array();
	foreach( $data as $oneData ) {
		$obj = new stdClass;
		$obj->namekey = $oneData->namekey;
		$obj->version = $oneData->version;
		$newData[] = $obj;
	}
	return $newData;
}
}