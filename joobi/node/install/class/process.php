<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
if ( !function_exists( 'WLoadFile' ) ) {
	function WLoadFile($filePath,$base=null,$expand=true,$showMessage=true) {
		return wimport( $filePath, $base, $expand, $showMessage );
	}}
class Install_Process_class {
	var $list = array();
	var $installing = array();
	var $mode = 'extract';		
	var $params_name = array( 'list', 'installing', 'mode' );
	var $_catchMessage = true;
	private $_returnedMessageA = array();
	public static function installFilesFolder() {
		static $folder = null;
		if ( isset($folder) ) return $folder;
		$folder = JOOBI_DS_JOOBI . 'installfiles';
self::logMessage( 'Defining installFilesFolder in Install_Process_class : ' . $folder, 'install' );
		return $folder;
	}
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
 		$this->_getListOfPackages();
 		$this->removeXMLCacheFolder();
 		return $this->_getMessage();
 	}
 	public function getNumberPackage() {
 		return count($this->list[0]);
 	}
 	public function getDownloadPackages() {
 		$this->_downloadPackages();
 		return $this->_getMessage();
 	}
	function instup() {
		WTools::increasePerformance();
		if ( !defined('JOOBI_INSTALLING') ) define( 'JOOBI_INSTALLING', 1 );
		if ( !class_exists( 'Install_Node_Install' ) ) require_once( JOOBI_DS_NODE . 'install' . DS . 'install' . DS . 'install.php' );
self::logMessage( 'Install preferences loaded' );
				if ( empty($_SESSION['joobi']['sleep_value'] ) ) sleep(1);
		else sleep(5);
		WPref::get( 'install.node' );
		WText::load( 'install.node' );
				@error_reporting( E_ALL );
		@ini_set('display_errors',true);
		@ini_set('display_startup_errors',true);
			if ( empty($_SESSION['joobi']['install_status'] ) ) $_SESSION['joobi']['install_status'] = 0;
self::logMessage( 'Install status:' . $_SESSION['joobi']['install_status'] );
			switch( (int)$_SESSION['joobi']['install_status'] ) {
				case 0 :
self::logMessage( 'Status 0 : Initialization' );
										$cacheC = WCache::get();
					$cacheC->resetCache();
										$mess =  'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG['.WText::t('1303104133RCSA').'] ';
					$this->updatePref( 10 );
					break;
				case 1:		self::logMessage( 'Download handler', 'install' );
					$mess = $this->_downloadPackages();
					break;
				case 2:		self::logMessage( 'install handler', 'install' );
					$mess = $this->installPackages();
										WGlobals::get( 'userchoicedone', false, 'global' );
					break;
				case 10:		self::logMessage( 'List retreiving handler', 'install' );
					$mess = $this->_getListOfPackages();
self::logMessage( 'Calling XML cache folder cleaner, case 10', 'install' );
										$this->removeXMLCacheFolder();
					break;
				case 49:
										$userchoiceContinue = WGlobals::get( 'continue_status', false, 'request' );
					$_SESSION['joobi']['continue_status'] = true;	
										$this->getPref();
					$this->updatePref(50);
					$mess =  'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG['.WText::t('1307005747JWMB').'] ';
					break;
				case 50:	self::logMessage( 'User choice handler','install' );
					$mess = $this->_handleUserChoice();
					break;
				default:
self::logMessage( 'Error PINSTALL_NODE_INSTALL_STATUS does not work', 'install' );
					return $this->_finishWithError( WText::t('1213020853MLHP') );
					break;
			}
		$finalMessage = '<div style="padding: 50px; background-color: rgb(43, 163, 212); color: white; text-align: center; line-height: 1.8em;"><br>If you see this message it means that a joobi installation process was stopped in the middle of the processing. <br>It usually happen when you go to another page while the install is running. <br>If you click HERE, the install mode will be disabled and you will be able to access your website again.<br>However, you may have to re-install the application again for it to function properly.<br>Only do that if you are sure the installation was finished. <br>The install stopped here: ' . $mess . '<br></div>';
		$finalMessage = preg_replace( '#\r?\n#', '<br />', $finalMessage );
		if ( defined('JOOBI_CHARSET') && JOOBI_CHARSET !='UTF-8' ) {
			$finalMessage = WPage::changeEncoding( $finalMessage, 'UTF-8', JOOBI_CHARSET );
		}
self::logMessage( 'Echo message: ' . $finalMessage, 'install' );
		echo $finalMessage;
		exit;
	}
	function updatePref($install_status) {
		$_SESSION['joobi']['install_status'] = $install_status;
		$array_of_params = array();
		foreach( $this->params_name as $name ) {
			if ( isset($this->$name) ) $array_of_params[$name] = $this->$name;
		}
		$array_of_params[0] = 'install_params_version_2';
		$install_params = base64_encode( serialize($array_of_params) );
		if ( !class_exists( 'Install_Node_install' ) ) WLoadFile( 'install.install.install');
		Install_Node_install::accessInstallData( 'set', 'installParams', $install_params );
	}
	function getPref() {
		$installParamsA = Install_Node_install::accessInstallData( 'get', 'installParams' );
		$params = unserialize( base64_decode( $installParamsA ) );
		if ( is_array($params) && isset($params[0]) && is_string($params[0]) && $params[0]=='install_params_version_2' ) {
			foreach( $params as $key => $param ){
				if ( in_array($key,$this->params_name) ) {
					$this->$key = $param;
				}			}			return;
		}
		$this->list = $params;
	}
 	function finish_extract() {
self::logMessage( 'All packages extracted', 'install', false, 1, true, true);
		$this->mode='copy';
		if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref($_SESSION['joobi']['install_status']);
		 		$this->_overwriteEntryPoint();
		$message = WText::t('1397671837IMCS');
				$folders = array(
			'node' . DS . 'api',
			'node' . DS . 'install',
			'node' . DS . 'library',
			'node' . DS . 'output'
		);
				$systemFolderC = WGet::folder();
		$installfiles = Install_Process_class::installFilesFolder();
		foreach( $folders as $folder ) {
			if ( $systemFolderC->exist( $installfiles . DS . $folder ) ) {
				$systemFolderC->copy( $installfiles . DS . $folder, JOOBI_DS_JOOBI . $folder, 'add_over' );
			}		}
		$_SESSION['joobi']['installwithminilib'] = true;
		 		foreach( $folders as $folder ) {
			if ( $systemFolderC->exist( $installfiles . DS . $folder ) ) {
				$systemFolderC->delete( $installfiles . DS . $folder );
			}		}
		return 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG['.$message.'] ';
 	}
 	 function finish_copy() {
 self::logMessage( 'All files moved','install' );
		$this->mode='createtable';
		if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
		$message = 'All files moved';
				$systemFolderC = WGet::folder();
		$installfiles = Install_Process_class::installFilesFolder();
		$systemFolderC->delete( $installfiles );
		return 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG['.$message.'] ';
 	}
 	function finish_createtable() {
self::logMessage( 'All tables extracted','install' );
		$this->mode='install';
		if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
		$message = 'All tables created';
		$customInstallA = array();
		Install_Node_install::accessInstallData( 'set', 'joobiCustomInstall', $customInstallA );
		return 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG['.$message.'] ';
 	}
 	function finish_install() {
 self::logMessage( 'All packages installed', 'install' );
		$this->mode = 'customtype';
		if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
				$installCommonC = WClass::get('install.common');
		$installCommonC->publishEnglishLanguage(false);
		$installCommonC->populatePreferences();
		$installCommonC->populateEnglish();
		$message = 'All packages installed';
				$this->addUpdateDependencies();
		return 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG['.$message.'] ';
 	}
 	function finish_customtype(){
 self::logMessage( 'All type custom install functions run', 'install' );
		$this->mode='cmsinstall';
		if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref($_SESSION['joobi']['install_status']);
		$message = 'All type custom install functions run';
		return 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG['.$message.'] ';
 	}
 	function finish_cmsinstall(){
 self::logMessage( 'All CMS links created','install' );
		$this->mode='custominstall';
		if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref($_SESSION['joobi']['install_status']);
		$message = 'All CMS links created';
		return 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG['.$message.'] ';
 	}
 	function finish_custominstall() {
 self::logMessage( 'All custom install functions run','install' );
		$this->mode='checklicense';
		if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref($_SESSION['joobi']['install_status']);
		$message = 'All custom install functions run';
		return 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG['.$message.'] ';
 	}
 	function finish_checklicense(){
		$this->mode = 'translation';
				$languagesFound = $this->getLanguages();
				if (empty($languagesFound)){
			return $this->finish_transinstall();
		}
		if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref($_SESSION['joobi']['install_status']);
		$message = WText::t('1389108457QWKA');
		if ( empty($message) ) {
			$message = 'Translation checked';
		}
		return 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG['.$message.'] ';
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
		return 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG['.$message.'] ';
 	}
 	function finish_transinstall() {
 				$this->updatePref(0);
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
				$translationHelperC = WClass::get('translation.helper', null, 'class', false );
		if ( !empty($translationHelperC) ) {
			$translationHelperC->updateLanguages();
self::logMessage( 'Updated the list of translations', 'install' );
		}
						$appToSetup = $this->_searchPreferencesPageForNewlyInstalledApps();
self::logMessage( 'Application Installation Complete. In fct finish_transinstall', 'install' );
				unset($_SESSION['joobi']['installwithminilib']);
				Install_Node_install::accessInstallData( 'delete', 'installParams' );
		$installWidget = WGlobals::getSession( 'webapps', 'widgetinstall', null );
		if ( !empty($installWidget) && $installWidget===true ) {
			$message = WText::t('1308836335LUUQ') . ' ... 2';
			WGlobals::setSession( 'webapps', 'widgetinstall', null );
		}elseif ( !empty($installWidget) ) {
			$message = $installWidget;
			WGlobals::setSession( 'webapps', 'widgetinstall', null );
		} else {
						$uid = WUser::get( 'uid' );
			if ( empty($uid) ) {
								WUser::get( null, 'reset' );
				$usersSessionC = WUser::session();
				$usersSessionC->resetUser();
			}
			$link = WPage::routeURL( 'controller=apps','smart','default',false,false, 'jcenter' );				$message = WText::t('1227579827CIXW');
			$goBackMessage = '<a href="'.$link.'">'.$message.'</a>';
						$objRedirectAuto = $this->_getLinkToAutomaticalyRedirect();
			if ( !is_object($objRedirectAuto) ) {
				$goBackMessage2 = '';
			} else {					$goBackMessage2 = '<a id="alex_redirect_link" href="'.$objRedirectAuto->url.'">'.$objRedirectAuto->link.'</a>';
			}
			$message = WText::t('1213107630MBVS').'<br/>'.$goBackMessage.'<br/>'.$goBackMessage2;
			$messageM = WMessage::get();
			$messageM->userB( 'finish' );
		}
		$message = 'FINISH BIGMSG['.$message.'] '.$appToSetup;
				$cC = WCache::get();
		$cC->resetCache();
		unset( $_SESSION['JoobiUser'] );
		return $message;
 	}
	public function finishDownloadPackages() {									$this->updatePref( 2 );
						$langM = WClass::get('translation.helper');
			$langM->updateLanguages();
				$systemFolderC = WGet::folder();
		$installfiles = Install_Process_class::installFilesFolder();
		if ( $systemFolderC->exist($installfiles) ) $systemFolderC->delete( $installfiles );
		return true;
	}
 	function getLanguages() {
 		$fields = array( 'name','code','lgid' );
 		$langs = WApplication::availLanguages($fields,'all');
 		if (!empty($langs)){
			foreach($langs as $key => $item){
				$name = strtolower( str_replace(array(',','(',')',' '), '_', $item->name) );
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
				$return = 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] JSUCCESS['.$v['wid'].$v['level'].']';
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
			$return = 'FINERROR STATUS[' . @$_SESSION['joobi']['install_status'] .'] JERROR['.$v['wid'].$v['level'].'] BIGMSG[';
			$return .= 'An error occurred during the installing process of the package:' . ' '.$package;
			$return .= '<br/>'.$errors.'] ';
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
self::logMessage( 'Calling activateExtensionVersionAndClean 1', 'install' );
		$install->activateExtensionVersionAndClean();
		if ( ! $install->needLicense() ) {
			$this->list[$k][$k2]['checklicense'] = 1;
		}
			if ( @$install->mainext->type != 1 ) {
				$this->list[$k][$k2]['translation'] = 1;
			} else {
				$this->list[$k][$k2]['namekey'] = @$install->mainext->namekey;
				$this->list[$k][$k2]['langDownloaded'] = array();
			}
		if ( !$install->hasCustomTypeFunction() ) {
			$this->list[$k][$k2]['customtype'] = 1;
		}
		$this->list[$k][$k2]['install'] = 1;
self::logMessage( 'The package "' . $package . '" was installed.', 'install', false, 0, false );
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
		$EXTENSION = @$install->mainext->name;
		$message = 'The extension "' . $EXTENSION . '" was installed.';
		$namekey_info = '<div style="display:none;">' . @$install->mainext->namekey . '</div>';
		$version_info = ( !empty($v['wid']) ? $v['wid'] : 9999 ) . (!empty($v['level']) ? $v['level'] : 0 );
		$return = 'STATUS[' . $_SESSION['joobi']['install_status'] . '] JSUCCESS[' . $version_info . '] BIGMSG[' . $message . $namekey_info . '] ';
		return true;
 	}
 	function createtable(&$return,&$v,&$v2,&$packages,$k,$k2) {
		$pieces = explode(DS,$v2['filename']);
		$package = $pieces[count($pieces)-1];
		if ( empty($v['level']) ) $v['level'] = 0;
		$install = WClass::get('install.package');
		$install->level = (int)$v['level'];
		$install->packages =& $packages;
		$install->parent =& $this;
		$install->list =& $this->list[$k];
		$install->packageId = $k2;
		$install->main = true;
		if ( isset( $v2['folder']) ) {
			$install->temp_folder = $v2['folder'];
		}		$install->processPackagesName();
		$install->extension_namekey = $package;
		if ( ! $install->loadRealSQL() ) {
			$this->list[$k][$k2]['createtable'] = 1;
			$return = 'continue';
			return true;
		}
		$this->list[$k][$k2]['createtable'] = 1;
self::logMessage( 'The tables were created for the package '.$package,'install', false, 0, false );
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
				$parts=explode('_',$package);
		array_pop( $parts );
		$EXTENSION = implode( ' ', $parts );
		$message = 'The tables of the extension '.$EXTENSION.' were created.';
		$namekey_info = '<div style="display:none;">'.$package.'</div>';
		$version_info = (!empty( $v['wid'] ) ? $v['wid'] : 9999 ) . (!empty( $v['level']) ? $v['level'] : 0);
		$return = 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] JSUCCESS['.$version_info.'] BIGMSG['.$message.$namekey_info.'] ';
		return true;
 	}
 	function copy(&$return,&$v,&$v2,&$packages,$k,$k2) {
		$pieces = explode( DS, $v2['filename'] );
		$package = $pieces[ count($pieces)-1 ];
		$real_folder = rtrim( $v2['folder'], DS );
		$dest = rtrim( $v2['destination'], DS );
		$installfiles = Install_Process_class::installFilesFolder();
		$tmp_folder = $installfiles . DS . $dest;
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
		$return = 'STATUS[' . $_SESSION['joobi']['install_status'] . '] JSUCCESS[' . @$v['wid'] . @$v['level'] . '] BIGMSG[' . $message . $namekey_info . '] ';
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
		$this->list[$k][$k2]['cmsinstall'] = 1;
self::logMessage( 'The CMS links for the package '.$package.' were created.','install', false, 0, false );
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref($_SESSION['joobi']['install_status']); 
		$EXTENSION = $install->mainext->name;
		$message = 'The CMS links of the extension '.$EXTENSION.' were created.';
		$namekey_info = '<div style="display:none;">'.$install->mainext->namekey.'</div>';
		$return = 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] JSUCCESS['.$v['wid'].$v['level'].'] BIGMSG['.$message.$namekey_info.'] ';
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
		$return = 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] JSUCCESS['.$v['wid'].$v['level'].'] BIGMSG['.$message.$namekey_info.'] ';
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
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref($_SESSION['joobi']['install_status']);
		$EXTENSION = $install->mainext->name;
		$message = 'The type custom install of the extension '.$EXTENSION.' was run.';
		$namekey_info = '<div style="display:none;">'.$install->mainext->namekey.'</div>';
		$return = 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] JSUCCESS['.$v['wid'].$v['level'].'] BIGMSG['.$message.$namekey_info.'] ';
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
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref($_SESSION['joobi']['install_status']);
		$EXTENSION = $install->mainext->name;
		$message = str_replace(array('$EXTENSION'), array($EXTENSION),WText::t('1221227936IUXD'));
				if ( empty($message) ) {
			$message = 'The license of the extension '.$EXTENSION.' was validated.';
		}
		$namekey_info = '<div style="display:none;">'.$install->mainext->namekey.'</div>';
		$return = 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] JSUCCESS['.$v['wid'].$v['level'].'] BIGMSG['.$message.$namekey_info.'] ';
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
						if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref($_SESSION['joobi']['install_status']);
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
			$return = 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] JSUCCESS['.$v['wid'].$v['level'].'] BIGMSG['.$message.'] ';
			return true;
		}
		$this->list[$k][$k2]['translation'] = 1;
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref($_SESSION['joobi']['install_status']);
		$parts = explode('.',$namekey);
		$APPLICATION = $parts[0];
		$message = str_replace(array('$APPLICATION'), array($APPLICATION),WText::t('1227579828FTWU'));
				if ( empty($message) ) {
			$message = 'All translations retrieved for the application '.$APPLICATION;
		}
self::logMessage( $message, 'install', false, 0, false );
		$return = 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] JSUCCESS['.$v['wid'].$v['level'].'] BIGMSG['.$message.'] ';
		return true;
	}
	public function clean($removeFolder=true) {
		$addon = WAddon::get( 'install.'.JOOBI_FRAMEWORK );
		if ( is_object($addon) && method_exists( $addon, 'clean' ) ) {
			if ( !$addon->clean( $removeFolder ) ) return false;
		}
		if ( !class_exists('Install_Node_install') ) WLoadFile( 'install.install.install', JOOBI_DS_NODE );
		Install_Node_install::accessInstallData( 'delete', 'joobiCore' );
		Install_Node_install::accessInstallData( 'delete', 'joobiCorePackages' );
		Install_Node_install::accessInstallData( 'delete', 'joobiCmsInstall' );
		Install_Node_install::accessInstallData( 'delete', 'joobiCustomInstall' );
		Install_Node_install::accessInstallData( 'delete', 'packagelist' );
				Install_Node_install::accessInstallData( 'delete', 'installparams' );
		Install_Node_install::accessInstallData( 'delete', 'importlangs' );
				$folderC = WGet::folder();
		$folderC->delete( JOOBI_DS_ROOT . 'tmp' );			$folderC->create( JOOBI_DS_ROOT . 'tmp' );
		$folderC->delete( JOOBI_DS_USER . 'progress' );	
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
			$return = 'FINERROR STATUS[' . @$_SESSION['joobi']['install_status'] .'] JERROR['.$v['wid'].$v['level'].'] BIGMSG[';
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
		$return = 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG['.$message.$namekey_info.'] ';
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
			$url =  WPage::routeURL( 'controller=' . $folder, 'smart', 'default', false, false, 'jcenter' );			} else {
			$url =  WPage::routeURL( 'controller=' . $folder, 'smart', 'default', false, false, $folder );
		}
				$returnObj->url = $url;
				WGlobals::setSession( 'installRedirectInfo', 'alex' , $returnObj->url );
		return $returnObj;
	}
 	private function _overwriteEntryPoint() {
 		if ( JOOBI_FRAMEWORK_TYPE == 'joomla' ) {
 			$installfiles = Install_Process_class::installFilesFolder();
			$cms_addon = $installfiles . DS . 'node'. DS . 'install' . DS . 'addon' . DS . JOOBI_FRAMEWORK . DS . JOOBI_FRAMEWORK . '.php';
			$entry_file = JOOBI_DS_ROOT . 'administrator' . DS . 'components' . DS . 'com_' . 'jcenter' . DS . 'jcenter' . '.php';	
			if ( file_exists( $cms_addon ) ) {
self::logMessage( 'overwrite entry point file ' . $entry_file . ' using cms addon file ' . $cms_addon, 'install', false, 0, false  );
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
		$netcomServerC = WClass::get( 'netcom.server' );
		return $netcomServerC->checkOnline( true );
 	}
	private function _getListOfPackages($already=false) {
				$systemFolderC = WGet::folder();
		$systemFolderC->delete( JOOBI_DS_USER . 'logs' );
		$app2InstallA = null;
		$data = null;
		$mess = $this->_getSelectedApplications( $already, $app2InstallA, $data );
		if ( !empty($mess) ) return $mess;
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
						$status = $appsInfoC->getPossibleCode( $getPossibleCode, 'token' );				if ( $status === false ) return $this->_finishWithError( WText::t('1427652813TJJQ') );
			$sentData->token = $status;
		}
				$newData = $this->_optmizedSendData( $data );
		$sentData->alreadyInstalled = serialize( $newData );			$sentData->desiredApp = serialize( $app2InstallA );	
		if ( !empty( $_SESSION['joobi']['repositoryid'] ) ) $sentData->reposid = $_SESSION['joobi']['repositoryid'];
		if ( $already ) $sentData->updateOtherApps = true;
		$receivedData = $netcom->send( $this->_getDistribSite(), 'repository', 'getOptimizedList', $sentData );
				if ( is_array( $receivedData ) ) {
									$this->list = $receivedData;			} elseif ( is_object( $receivedData ) ) {
			if ( $receivedData->type == 'success' ) {
				$this->list = array();
				$tempArary = array();
				$unseraibleMe = unserialize( $receivedData->data );
				if ( is_array($unseraibleMe) ) {
															$extensionReceviedA = array();
					foreach( $unseraibleMe as $oneUnseraibleMe ) {
						$extensionReceviedA[$oneUnseraibleMe->e] = $oneUnseraibleMe;
					}
					$existingExtensionA = array();
										if ( !empty($existingExtensionA) ) {
						foreach( $existingExtensionA as $oneExisting ) {
							$newVErsion = $extensionReceviedA[$oneExisting->namekey]->n;
							if ( $newVErsion <= $oneExisting->version ) {
																unset($extensionReceviedA[$oneExisting->namekey]);
							}						}					}
					foreach( $extensionReceviedA as $onePack ) {
						$tempArary[] = $onePack->v;												}					$this->list[] = $tempArary;
				} else {
					self::logMessage( 'The distribution site could not provide the list of extensions for repository: ' . $receivedData->reposid, 'install', false, 0, false );
					self::logMessage( $receivedData, 'install', false, 0, false );
					self::logMessage( $sentData, 'install', false, 0, false );
					return $this->_finishWithError( 'Wrong format of packages.' );
				}
			} elseif ( $receivedData->type == 'error2' ) {
				return $this->_dealWithTokenIssue( $receivedData );
			} elseif ( $receivedData->type == 'error' ) {
self::logMessage( 'The distribution site could not provide the list of extensions: ' . $receivedData->reposid, 'install', false, 0, false );
				return $this->_finishWithError( $receivedData->message );
			}
		} else {
self::logMessage( 'Could not retrieve the list of packages: ' . $receivedData->reposid, 'install', false, 0, false );
self::logMessage( $receivedData, 'install', false, 0, false );
			$this->_setMessage( 'html', WText::t('1236536983NQDJ'), 'failed' );
			return $this->_finishWithError( WText::t('1236536983NQDJ') );
		}
				if ( $this->list[0]===true ) {
self::logMessage( 'list available but other apps need an update','install', false, 0, false );
			return $this->_refreshDownloadListWithPackages( $app2InstallA, $data );
		}
				$this->_addLevelToList( $app2InstallA );
				if (isset($this->list[0][0]) && isset($this->list[0][1]) && $this->list[0][0] == '_array_' && empty($this->list[0][1])){
			if (!empty($this->list[0]['wid'])){
				$ext = WModel::get( 'install.apps', 'object' );
				$ext->whereE( 'wid', $this->list[0]['wid'] );
				$ext->setVal( 'publish', 1 );
				$ext->setVal( 'version', 'lversion', 0, 0 );
				$ext->update();
				$ext = WModel::get( 'install.appsinfo', 'object' );
				$ext->whereE( 'wid', $this->list[0]['wid'] );
				$ext->setVal( 'userversion', 'userlversion', 0, 0 );
				$ext->update();
			}
			$this->updatePref(0);
self::logMessage( 'Application Installation Complete. In fct: _getListOfPackages', 'install', false, 0, false );
						if (isset($_SESSION['joobi']['installwithminilib'])) unset($_SESSION['joobi']['installwithminilib']);
			$message = 'FINISH BIGMSG['.WText::t('1213107630MBVS').'] ';
			$this->_setMessage( 'html', WText::t('1213107630MBVS'), 'complete' );
			$messageM = WMessage::get();
			$messageM->userB( 'finish' );
						$cC = WCache::get();
			$cC->resetCache();
			unset( $_SESSION['JoobiUser'] );
			return $message;
		}
		$this->_setMessage( 'html', WText::t('1213020853MLHV') );
				$this->updatePref(1);
		return 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG['.WText::t('1213020853MLHV').'] ';
	}
	private function _addLevelToList(&$app2InstallA){
		$i = 0;
		foreach( $app2InstallA as $k ) {
						$levelToInstall=WGlobals::get( 'levelInstall' );
			if ( !$levelToInstall ){
				$sql = WModel::get( 'install.apps', 'object' );
				$sql->makeLJ( 'install.appslevel', 'wid' );
				$sql->whereE( 'folder', $k->folder );
				$sql->whereE( 'destination', $k->destination );
				$sql->select( 'level', 1, 'level', 'MAX');
				$sql->select( 'wid' );
				$sql->groupBy( 'wid', 1 );
				$levelToInstall = $sql->load( 'o' );
				if (!is_object( $levelToInstall ) ){
					continue;
				}			}
			$this->list[$i]['level'] =$levelToInstall->level;
			$this->list[$i]['wid'] = $levelToInstall->wid;
			$i++;
		}
	}
	private function _refreshDownloadListWithPackages(&$app2InstallA,&$data) {
		$apps = '';
		foreach($this->list[1] as $app){
			$obj = new stdClass;
			$obj->folder = $app[1];
			$obj->destination = $app[2];
			$app2InstallA[]=$obj;
						$sql = WModel::get( 'install.apps', 'object' );
			$sql->whereE( 'folder', $obj->folder );
			$sql->whereE( 'destination', $obj->destination );
			$wid = $sql->load( 'lr', 'wid' );
			if ( !empty( $wid ) ){
				$extension = new stdClass;
				$extension->wid = $wid;
				$extension->newInst = false;
				$this->installing[] = $extension;
			} else {
self::logMessage( 'the install needs to update  '.$obj->destination.'|'.$obj->folder.' but we couldn\'t find it in the DB !','install', false, 0, false );
self::logMessage( $this->list,'install', false, 0, false );
			}
						$apps .= $app[0] . ', ';
		}
		$apps = rtrim( $apps, ', ' );
self::logMessage( 'the install needs to update as well '.$apps,'install', false, 0, false );
		$this->list[0] = array( $app2InstallA, $data );
		$this->updatePref(49);
		$updateNeededMessage = 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG[Updating...] ';
		$this->_setMessage( 'html', 'Updating...' );
		return $updateNeededMessage;
	}
	private function _getSelectedApplications($already,&$app2InstallA,&$data) {
		if ( !$already ) {
self::logMessage( 'Check selected applications' );
						$extsid = WModel::get( 'install.apps', 'sid' );
			$lwidtab = 'wid_' . $extsid;
			$to_install = WGlobals::get( $lwidtab );
						if ( empty( $to_install ) ) {
				$wid = WForm::getPrev( 'wid' );
								if ( empty($wid) ) $wid = WExtension::get( 'jcenter' . '.application', 'wid' );					$to_install = array( $wid );
			}
			if ( !isset($to_install) ) {
				return 'FINERROR BIGMSG['. WText::t('1213107629LYER') .']';
			}
self::logMessage( 'Get info on each extension'  );
self::logMessage( $to_install );
			$app2InstallA = $this->_getApplicationsInfo( $to_install );
			if ( is_string($app2InstallA) ) {
				return $app2InstallA;
			}
self::logMessage( 'Get the packages already installed' );
						$data = $this->_alreadyInstalled();
		} else {
self::logMessage( 'retrieve info from DB' );
			$this->getPref();
			$app2InstallA = $this->list[0][0];
			$data = $this->list[0][1];
		}
		return '';
	}
	private function _getApplicationsInfo(&$to_install) {
		$app2InstallA = array();
		$sql = WTable::get( 'extension_node', 'main_library', 'wid' );
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
		return $app2InstallA;
	}
 	private function _handleUserChoice(){
 		$userChoiceOnce = WGlobals::get( 'userchoicedone', false, 'global' );
 		if ( !empty( $userChoiceOnce ) || $userChoiceOnce !== false ) {
 			 			exit;
 		}
 		$result = $_SESSION['joobi']['continue_status'];
		if ($result){
self::logMessage( 'user wants to continue','install', false, 0, false );
			return $this->_getListOfPackages(true);
		}		else{
self::logMessage( 'installation stopped','install', false, 0, false );
			$this->updatePref(0);
			return 'FINISH BIGMSG['.WText::t('1211280055AYDS').']';
		} 	}
	private function _downloadPackages() {
				$this->getPref();
		$netcom = WNetcom::get();
		foreach( $this->list as $k => $v ) {
			foreach( $v as $k2 => $v2 ) {
				if ( in_array( (string)$k2, array('level','wid') ) ) {
					continue;				}				
				if ( empty($v2) ) continue;
								if ( is_array($v2) && !empty($v2['downloaded']) ) continue;
self::logMessage( 'downloading package:' ,'install', false, 0, false );
self::logMessage( $v2, 'install', false, 0, false );
								$extHelper = WClass::get( 'apps.helper' );
				$sentData = new stdClass;
				$sentData->vsid = (int)$v2;
				$appsInfoC = WClass::get( 'apps.info' );
				$sentData->url = $appsInfoC->myURL();
				if ( !empty( $_SESSION['joobi']['repositoryid'] ) ) $sentData->reposid = $_SESSION['joobi']['repositoryid'];
self::logMessage( $sentData, 'install', false, 0, false );
				$receivedData = $netcom->send( $this->_getDistribSite(), 'repository', 'packageOnePackage', $sentData );
												if ( $receivedData == null || $receivedData->type == 'error' ) {
self::logMessage( 'Package download problem: communication error', 'install', false, 0, false );
self::logMessage( $receivedData, 'install', false, 0, false );
					return $this->_dealWithTokenIssue( $receivedData );
				} elseif ( $receivedData->type == 'error2' ) {
					return $this->_dealWithTokenIssue( $receivedData );
				}
												$resp = $receivedData->data;
								$file = WGet::file();
				$filename = JOOBI_DS_USER . 'downloads' . DS . 'packages' . DS . $resp[0];
				$filecontent = base64_decode( $resp[3][1] );
				if ( ! $file->write( $filename, $filecontent, 'force' ) ) {
										if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref($_SESSION['joobi']['install_status']);
					$PATH = $filename;
					$this->_setMessage( 'html', WText::t('1298294193EGSU') . $PATH, 'failed' );
					return 'FINERROR STATUS[' . @$_SESSION['joobi']['install_status'] .'] JERROR['.$v['wid'].$v['level'].'] BIGMSG['.WText::t('1298294193EGSU').$PATH.'.] ';
				}
				if ( !empty( $_SESSION['joobi']['repositoryid'] ) )  {
										$sentData = new stdClass;
					$sentData->vsid =  (int) $v2;
					$sentData->status =2; 					$sentData->reposid = $_SESSION['joobi']['repositoryid'];
				}
				$this->list[$k][$k2] = array('id'=>$v2);
				$this->list[$k][$k2]['filename'] = $filename;
				$this->list[$k][$k2]['downloaded'] = 1;
				if ( isset($_SESSION['joobi']) && isset($_SESSION['joobi']['install_status']) ) $this->updatePref( $_SESSION['joobi']['install_status'] );
				$package = $resp[0];
self::logMessage( 'Package ' .$package. ' downloaded successfully', 'install', false, 0, false );
				$namekey_info = '<div style="display:none;">' . $package . '</div>';
				$parts=explode( '_', $package );
				if ($parts[0]=='lib'){
					$parts[1]='lib';
				}
				$PACKAGE_NAME = $parts[0] . ' ' . $parts[1];
				$this->_setMessage( 'html', str_replace(array('$PACKAGE_NAME'), array($PACKAGE_NAME),WText::t('1395716605OVVZ')) );
				return 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] JSUCCESS['.$v['wid'].$v['level'].'] BIGMSG[' . str_replace(array('$PACKAGE_NAME'), array($PACKAGE_NAME),WText::t('1395716605OVVZ')) . $namekey_info .'] ';
			}
		}
		$this->finishDownloadPackages();
		$this->_setMessage( 'html', WText::t('1397671839JQGR'), 'complete' );
				return 'STATUS[' . @$_SESSION['joobi']['install_status'] .'] BIGMSG[' . WText::t('1397671839JQGR') . ']';
	}
	private function _finishWithError($message) {
		$this->_setMessage( 'html', $message, 'failed' );
		$returnMessage = 'FINERROR BIGMSG[<span style="color:red;">'. $message .'</span>';
		$LINK = '<a target="_blank" href="http://joobi.info/support">' . WText::t('1389701766DFGL') . '</a>';
		$returnMessage .= '<br/><br/>' . $LINK . '<br/>';
		$returnMessage .= ']';
		$returnMessage .= 'FINISH[]';
		return $returnMessage;
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
		$myMess =  'FINERROR STATUS[' . $_SESSION['joobi']['install_status'] . '] JERROR[' . $receivedData->message . ']';
		$LINK = '<a target="_blank" href="http://joobi.info/support">' . WText::t('1389701766DFGL') . '</a>';
		$myMess .= ' BIGMSG[<span style="color:red;">'. $mainMessage . '</span><br>';
		if ( !empty( $receivedData->message ) ) {
			$myMess .= ' ' . $receivedData->message . ' ';
		}		if ( !empty( $receivedData->token ) ) {
			$myMess .= '<br> ' . WText::t('1389275714NKEJ') . ': ' . $receivedData->token . '<br> ';
		}		$myMess .= '<br>' . $LINK . '<br>';
		$myMess .= '] ';
		$myMess .= 'FINISH[]';
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
	private function _alreadyInstalled() {
		$sql = WTable::get( 'extension_node', 'main_library', 'wid' );
		$sql->whereE( 'publish' , 1 );
		$sql->whereIn( 'type' , array( 1, 150 ) );
		return $sql->load( 'ol', array( 'destination', 'folder', 'namekey', 'version' ) );
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