<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Install_Node_install { 
	private static $languagesUsed = array();
	function preinstall(&$extension) {
		self::logMessage( 'Starting preinstall of INSTALL.NODE', 'install', false, 1, true, true );
		$this->_updateSystemLayerCompatibility();
		$this->_updateInstallProcessCompatibility2();
		$this->_writeConfigFileIfNotExists();
		$this->_removeOldXmlFiles();
		$this->_improveInstallProcess();
				$dest = '';
		if ( isset($extension->destination) ) {
			$dest = $extension->destination;
		}
		if ( !Install_Node_install::moveRootFiles($dest) ) {
			return false;
		}
		return true;
	}
	public function install($object) {
self::logMessage( 'Starting install() of INSTALL.NODE','install', false, 1, true, true );
		if ( !empty( $this->newInstall ) || (property_exists($object, 'newInstall') && $object->newInstall) ) {
self::logMessage( ' new install for INSTALL.NODE','install', false, 1, true, true );
						Install_Node_install::moveRootFiles();
self::logMessage( ' finished new install for INSTALL.NODE','install', false, 1, true, true );
		}
self::logMessage( 'Clear cache for INSTALL.NODE','install', false, 1, true, true );
				$cache = WCache::get();
		$cache->resetCache( 'Model' );
		$cache->resetCache( 'Preference' );
		return true;
	}
	public static function first_install(&$installerO) {
		$installerO->log( 'first_install() Beginning' );
				$startTime = time();
		$maxTime = ( $installerO->isOffline() ? 5 : 15 );
		$minTime = $maxTime;
		$maxExecution = @ini_get('max_execution_time');
		if ( empty($maxExecution) ) $maxExecution = $maxTime;
		if ( $maxExecution > $maxTime ) $maxExecution = $maxTime;
		if ( $maxExecution < $minTime ) $maxExecution = $minTime;
		$maxExecution = $maxExecution * 0.8;
		Install_Node_install::generateFirstDefines();
				$installerO->log( 'first_install() stage: ' . $_SESSION['joobi']['first_install'] );
				if ( class_exists('WMessage') ) {
			self::logMessage( 'first_install() status ','install', false, 1, false, false );
			self::logMessage( $_SESSION['joobi']['first_install'] ,'install', false, 1, false, false );
		}
				if ( $_SESSION['joobi']['first_install'] == 20 && !empty($_SESSION['joobi']['stepDone']['20']) ) {
			$_SESSION['joobi']['first_install'] = 25;
		}
		if ( $_SESSION['joobi']['first_install'] == 20 ) {
			$installerO->log( 'first_install stage TRACE 2: ' . $_SESSION['joobi']['first_install'] );
			$installerO->log( 'first_install stage TRACE 2: ' . print_r( $_SESSION['joobi'], true ) );
		}
		$myReturn = false;
		$forceStopRefresh = false;			do {
			$stepName = (int)$_SESSION['joobi']['first_install'];
			switch( (int)$stepName ) {
				case 0:
				case 1:						$stepName = 'Extra Core Packages';
					$installerO->loadTar();
						$myReturn = Install_Node_install::extractCorePackages( $installerO );
					$installerO->log( 'First stage of the install $return: ' . $myReturn );
					if ( $myReturn === false ) {
						return false;
					} elseif ( is_string($myReturn) ) {
						Install_Node_install::messageToUser( $myReturn );
					}
					Install_Node_install::accessInstallData( 'set', 'doneTables', array() );
										return $myReturn;
					break;
				case 5:						$stepName = 'Create Core Tables';
					Install_Node_install::loadLib();
					$myReturn = Install_Node_install::createTablesCorePackages( $installerO );
					break;
				case 20:						$stepName = 'Installation of Core Packages';
					$_SESSION['joobi']['stepDone']['20'] = 21;
					$installerO->log( 'first_install stage TRACE 3: ' . print_r( $_SESSION['joobi']['stepDone'], true ) );
					Install_Node_install::loadLib();
					$myReturn = Install_Node_install::first_install_process( $installerO );
					break;
				case 25: 					$stepName = 'Run Custom Install';
					Install_Node_install::loadLib();
					$myReturn = Install_Node_install::first_install_custom( $installerO );
					break;
				case 30:						$stepName = 'Install English Languages';
					Install_Node_install::loadLib();
					$myReturn = Install_Node_install::first_install_lang_list( $installerO );
					break;
				case 40:
					$stepName = 'Install First Language';
					Install_Node_install::loadLib();
					$myReturn = Install_Node_install::first_install_lang( $installerO );
					break;
				case 45:
					$stepName = 'Populate Translation tables';
					Install_Node_install::loadLib();
					$myReturn = Install_Node_install::first_install_populate( $installerO );
					break;
				case 50:
					$stepName = 'Final installation procedure!';
					Install_Node_install::loadLib();
					$myReturn = Install_Node_install::first_install_final( $installerO );
					$installerO->mode( 4 );						$forceStopRefresh = true;
					break;
				default:
					$installerO->log( 'default case not supported it is an error.' );
					$forceStopRefresh = true;
					break;
			}
			$logMessage = ' END  Switch: ' . time() . ' step : ' . $stepName;
			if ( $myReturn !== true ) $logMessage .= ' $myReturn: ' . $myReturn;
			$installerO->log( $logMessage );
						if ( $myReturn === false ) return false;
			if ( $myReturn === true ) $myReturn = $stepName;
			if ( $forceStopRefresh ) {
				$installerO->renderMessageOnPage( $myReturn );
			}
		} while ( ( ( time() - $startTime ) < $maxExecution ) );
		return $myReturn;
	}
	public static function first_install_process(&$installerO) {
self::logMessage( 'Install the packages.', 'install', false, 1, true, true);
		$customInstallA = Install_Node_install::accessInstallData( 'get', 'joobiCustomInstall' );
		$cmsInstallA = Install_Node_install::accessInstallData( 'get', 'joobiCmsInstall' );
		if ( empty($customInstallA ) ) $customInstallA = array();
		if ( empty($cmsInstallA ) ) $cmsInstallA = array();
		$joobiCoreA = Install_Node_install::accessInstallData( 'get', 'joobiCore' );
		$joobiCorePackagesA = Install_Node_install::accessInstallData( 'get', 'joobiCorePackages' );
		WMessage::log( 'before moving theme', 'install-user-theme' );
		foreach( $joobiCoreA as $k => $folder ) {
			if ( is_string($folder) && $folder=='finished' ) continue;
			$installerO->calculateProgress( 'installPackage' );
			$package = WClass::get('install.package');
			$package->use_core_array = true;
			$package->temp_folder = rtrim($folder,DS);
			$package->packages = $joobiCorePackagesA;
			$package->processPackagesName();
self::logMessage( 'Now processsing extension: ' . $package->temp_folder , 'install', false, 1, true, true);
						$systemFolderC = WGet::folder();
			$themeFolder = $package->temp_folder . DS . 'theme';
			if ( $systemFolderC->exist($themeFolder) ) {
self::logMessage( 'Moving theme folder: ' . $themeFolder , 'install', false, 1, true, true);
WMessage::log( 'now moving theme', 'install-user-theme' );
				$explodePathA = explode( DS, $package->temp_folder );
				$extensionName = array_pop($explodePathA);
				$realThemeFolder = JOOBI_DS_THEME;
				$systemFolderC->copy( $themeFolder, $realThemeFolder, 'add_over' );
				$systemFolderC->delete( $themeFolder );
			}
			if ( !$package->loadXmlAndCheckDependencies() ) {
				if ( isset($package->goToNext) && $package->goToNext ) {
self::logMessage($package->mainext->name.'already installed ! Going to the next one','install', false, 1, true, true);
					$joobiCoreA[$k] = 'finished';
					Install_Node_install::accessInstallData( 'set', 'joobiCore', $joobiCoreA );
					continue;
				}
				if ( isset($package->switch_child) && $package->switch_child ) {
					$child = $joobiCoreA[$package->child_key];
					$joobiCoreA[$k] = $child;
					$joobiCoreA[$package->child_key] = $folder;
					$temp_info = $joobiCorePackagesA[$k];
					$joobiCorePackagesA[$k] = $joobiCorePackagesA[$package->child_key];
					$joobiCorePackagesA[$package->child_key] = $temp_info;
self::logMessage( 'Package '.$folder.' ('.$k.') switched with package '.$child.' ('.$package->child_key.')','install', false, 1, true, true);
					$child_package_name = $package->packageName($child);
					Install_Node_install::accessInstallData( 'set', 'joobiCore', $joobiCoreA );
					Install_Node_install::accessInstallData( 'set', 'joobiCorePackages', $joobiCorePackagesA );
					Install_Node_install::messageToUser('The extension '. $package->mainext->name. ' requires first the install of the package '.$child_package_name.'<div style="display:none;">'.$package->mainext->namekey.'</div>');
					return true;
				}
			}
			if ( !$package->triggerPreInstall() ) {
				return false;
			}
			if ( !$package->process() ) {
				Install_Node_install::errorMessage($package);
				return false;
			}
			if ( $package->hasCMSFunction() ) {
				$cmsInstallA[$k] = $folder;
			}
			$methods = $package->customInstallMethods();
			if ( !empty($methods) ) {
				$customInstallA[$k] = array( 'folder'=> $folder, 'methods'=> $methods );
			}
			self::logMessage( 'Calling activateExtensionVersionAndClean 3', 'install' );
			$package->activateExtensionVersionAndClean();
Install_Node_install::errorMessages();
			$namekey = $package->mainext->namekey;
			$name = $package->mainext->name;
self::logMessage( 'Processing of extension' . strtoupper($namekey) . ' finished' ,'install' );
			$namekey_info = '<div style="display:none;">'.$namekey.'</div>';
			$joobiCoreA[$k] = 'finished';
		}
		WMessage::log( 'after moving theme', 'install-user-theme' );
		Install_Node_install::accessInstallData( 'set', 'joobiCore', $joobiCoreA );
		Install_Node_install::accessInstallData( 'set', 'joobiCustomInstall', $customInstallA );
		Install_Node_install::accessInstallData( 'set', 'joobiCmsInstall', $cmsInstallA );
		Install_Node_install::messageToUser( 'Extension '.$name.' successfully installed!'. $namekey_info );
		Install_Node_install::accessInstallData( 'set', 'joobiCore', $joobiCoreA );
				$installCommonC = WClass::get( 'install.common' );
				$installCommonC->populatePreferences();
				$installCommonC->publishEnglishLanguage(true);
				$installCommonC->populateEnglish();
self::logMessage( 'All extensions installed!', 'install', false, 1, false, false );
		$step = new stdClass;
		$step->install = "finish";
		Install_Node_install::messageToUser( 'All extensions installed!', false, $step );
		$_SESSION['joobi']['first_install'] = 25;
				Install_Node_install::_cleanAndUpdateParentOfChild();
		$process = WClass::get( 'install.process' );
		$process->addUpdateDependencies();
		return true;
	}
	public static function first_install_custom(&$installerO) {
self::logMessage( 'Custom install of the packages', 'install' );
		$customInstallA = Install_Node_install::accessInstallData( 'get', 'joobiCustomInstall' );
		$cmsInstallA = Install_Node_install::accessInstallData( 'get', 'joobiCmsInstall' );
		if ( !empty($cmsInstallA) ) {
			$joobiCorePackagesA = Install_Node_install::accessInstallData( 'get', 'joobiCorePackages' );
			foreach( $cmsInstallA as $k => $cms ) {
				$status = true;
				if (is_string($cms) && $cms=='finished') continue;
				$installerO->calculateProgress( 'installCustom' );
				$package = WClass::get('install.package');
				$package->temp_folder = rtrim( $cms, DS );
				$package->packages = $joobiCorePackagesA;
				$package->processPackagesName();
				if ( !$package->loadXml() ) {
					Install_Node_install::errorMessage($package);
					$status = false;
				} else {
					if ( ! $package->triggerCMS() ) {
						Install_Node_install::errorMessage($package);
						$status = false;
											}
				}
				$namekey = $package->mainext->namekey;
self::logMessage( 'cms install of '.$namekey.' started','install', false, 1, true, true);
								$strtolowerNamekey = strtolower($namekey);
				if ( $strtolowerNamekey != JOOBI_MAIN_APP . '.application' && strpos( $strtolowerNamekey, '.application') )
				{
					WGlobals::setSession( 'installRedirectInfo', 'redirectApp', $strtolowerNamekey );
				}
				Install_Node_install::errorMessages();
				$name = $package->mainext->name;
self::logMessage( 'cms install of '.$namekey.' finished','install', false, 1, true, true);
				$namekey_info = '<div style="display:none;">'.$namekey.'</div>';
				$cmsInstallA[$k] = 'finished';
				Install_Node_install::accessInstallData( 'set', 'joobiCmsInstall', $cmsInstallA );
				Install_Node_install::messageToUser( 'Extension '.$name.'\'s CMS link done'.$namekey_info);
				return $status;
			}
		}
		if ( !empty($customInstallA) ) {
			$joobiCorePackagesA = Install_Node_install::accessInstallData( 'get', 'joobiCorePackages' );
			foreach($customInstallA as $k => $custom) {
				if (is_string($custom) && $custom=='finished') continue;
				$folder = $custom['folder'];
				$methods = $custom['methods'];
				$package = WClass::get('install.package');
				$package->temp_folder = rtrim($folder,DS);
				$package->packages = $joobiCorePackagesA;
				$package->processPackagesName();
				if ( !$package->loadXml() ) return false;
				$namekey = $package->mainext->namekey;
								$strtolowerNamekey = strtolower($namekey);
				if ($strtolowerNamekey != JOOBI_MAIN_APP . '.application' && strpos( $strtolowerNamekey, '.application') ) {
					WGlobals::setSession( 'installRedirectInfo', 'redirectApp', $strtolowerNamekey );
				}self::logMessage( 'Custom install of '. strtoupper($namekey) .' started','install', false, 1, true, true );
				if ( !$package->triggerMethods($methods) ) {
														}
				Install_Node_install::errorMessages();
				$name = $package->mainext->name;
self::logMessage( 'Custom install of '. strtoupper($namekey) .' finished','install', false, 1, true, true);
				$namekey_info = '<div style="display:none;">'.$namekey.'</div>';
				$customInstallA[$k] = 'finished';
				Install_Node_install::accessInstallData( 'set', 'joobiCustomInstall', $customInstallA );
				Install_Node_install::messageToUser( 'Extension '.$name.'\'s custom install done!' . $namekey_info );
			}
		}
		$step = new stdClass;
		$step->custom = 'finish';
		Install_Node_install::messageToUser('All extensions\' custom install process done!', false, $step );
		$_SESSION['joobi']['first_install'] = 30;
		return true;
	}
	public static function first_install_lang_list(&$installerO) {
		$installerO->calculateProgress( 'languageEnglish' );
self::logMessage( 'chmod the joobi folder if needed','install', false, 1, true, true);
				if ( !Install_Node_install::_chmodJoobiFolder() ) {
			Install_Node_install::messageToUser( 'Could not set the permissions for the joobi folder',false,$step);
self::logMessage( 'Could not set the permissions for the joobi folder.','install', false, 1, true, true);
			return false;
		}
				$installHandler = WClass::get('install.process');
self::logMessage( 'Calling XML cache folder cleaner, first_install_lang_list', 'install' );
				$installHandler->removeXMLCacheFolder();
				self::$languagesUsed = $installHandler->getLanguages();
		if ( !empty(self::$languagesUsed) ) {
			$step = new stdClass;
			$step->language = 'search';
			Install_Node_install::messageToUser( 'Searching for translations',false,$step);
			$_SESSION['joobi']['languagesDone'] = array();
			$_SESSION['joobi']['first_install'] = 40;
			$countLanguages = count( self::$languagesUsed );
			$installerO->calculateProgress( $countLanguages, 'updateLanguages' );
			return true;
		}
		$step = new stdClass;
		$step->language = 'notrans';
		Install_Node_install::messageToUser( 'No translation needed or available for your website languages', false, $step );
		$_SESSION['joobi']['first_install'] = 50;
		return true;
	}
	public static function first_install_lang(&$installerO) {
		if ( !empty(self::$languagesUsed) ) {
self::logMessage( 'importing languages','install', false, 1, true, true);
			$netcom = WNetcom::get();
			$translationHandler = WClass::get( 'translation.importlang' );
			$translationHandler->auto = 1;
			$translationHandler->setForceInsert( true );
						if ( ! WPref::load( 'PINSTALL_NODE_DISTRIB_WEBSITE' ) ) {
				$atInstallDistribsite = joobiinstaller::getConfiguration( 'distrib_server5' );
				if ( empty( $atInstallDistribsite ) ) {
					$atInstallDistribsite = 'http://www.joobiserver.com';
				}			} else {
				$atInstallDistribsite = WPref::load( 'PINSTALL_NODE_DISTRIB_WEBSITE' );
			}
			$distribserver = WPref::load( 'PAPPS_NODE_DISTRIBSERVER' );
			if ( empty($distribserver) ) {
				$distribserver = 1;
			}
			if ( $distribserver == 11 ) {
				$message = WMessage::get();
				$message->userW('1338581028LZCR');
				return false;
			}
						if ( $distribserver == 99 ) {
				$myDistribServer = WPref::load( 'PINSTALL_NODE_DISTRIB_WEBSITE_BETA' );
			} elseif ( $distribserver == 54 ) {
			$myDistribServer = WPref::load( 'PINSTALL_NODE_DISTRIB_WEBSITE_DEV' );
			} else {
				$myDistribServer = $atInstallDistribsite;
			}
			foreach( self::$languagesUsed as $key => $lang ) {
				$installerO->calculateProgress( 'languageOthers' );
				if ( isset($_SESSION['joobi']['languagesDone'][$lang->code]) ) {
					continue;
				}
								$_SESSION['joobi']['languagesDone'][$lang->code] = $lang->code;
								if ( $lang->code=='en' ) {
					continue;
				}
								$LANGUAGE = $lang->name;
				$data = new stdClass;
				$data->namekey = JOOBI_MAIN_APP . '.application';
				$data->language = $LANGUAGE;
				WPref::get('install.node');
				$data = $netcom->send( $myDistribServer, 'repository', 'getDic', $data );
				if ( is_string($data) ) {
self::logMessage( $LANGUAGE.' language data downloaded for Main App','install', false, 1, true, true);
					$translationHandler->importDictionary( $data );
					$message = 'The ' . $LANGUAGE . ' translations were retrieved for the application '.$APPLICATION;
				} else {
					$message = $LANGUAGE . ' translations not available for the application '.$APPLICATION;
										unset( self::$languagesUsed[$key] );
				}
self::logMessage($message,'install', false, 1, true, true);
				Install_Node_install::messageToUser($message);
				return true;
			}		}
self::logMessage( 'all languages imported','install', false, 1, true, true);
		$step = new stdClass;
		$step->language = 'imported';
		Install_Node_install::messageToUser('Translations imported.',false,$step);
		$_SESSION['joobi']['first_install'] = 45;
		return true;
	}
	public static function first_install_populate(&$installerO) {
self::logMessage( 'populating languages','install', false, 1, true, true);
		$translationProcessC = WClass::get('translation.process');
		$translationProcessC->setDontForceInsert( true );
				$translationProcessC->setStep( POPULATION_STEP );
		$translationProcessC->setHandleMessage( false );
		$translationProcessC->importexec();
		$installerO->calculateProgress( 'languagePopulate' );
		$step = new stdClass;
		$step->language = 'finish';
		Install_Node_install::messageToUser('Translations populated.',false,$step);
		$_SESSION['joobi']['first_install'] = 50;
		return true;
	}
	public static function first_install_final(&$installerO) {
self::logMessage( 'reset the joobi cache','install', false, 1, true, true);
				$cC = WCache::get();
		$cC->resetCache();
				$modelToReloadA = array( 'library.controller', 'library.view', 'library.model', 'library.picklist', 'install.apps' );
		foreach( $modelToReloadA as $modelName ) {
			$controllerM = WModel::get( $modelName, 'object' );
			$controllerM->setVal( 'reload', 1 );
			$controllerM->update();
		}
		unset( $_SESSION['JoobiUser'] );
		WUser::get( 'lgid' );
				$_SESSION['joobi']['install_process_mode'] = 'extracting';
		$step = new stdClass;
		$step->install = 'finish';
		Install_Node_install::messageToUser( 'End of the install. Congratulations!', true, $step );
		$installerO->calculateProgress( 'languagePopulate' );
		return true;
	}
	function second_install($installerO) {
		ob_start();
		if ( !defined('JOOBI_INSTALLING') ) define('JOOBI_INSTALLING', 1 );
		$joobiEntryPoint = 'install';
		require( dirname(dirname(__FILE__) ) . DS . 'entry.php');
		self::logMessage( ob_get_clean() );
		self::logMessage( 'library loaded' );
				Install_Node_install::errorMessages();
		self::logMessage( 'error messages handling' );
		return true;
	}
	public static function third_install($installerO) {
		Install_Node_install::generateFirstDefines();
		Install_Node_install::loadLib();
		$file_handler = WGet::file();
		$user = JOOBI_DS_ROOT.JOOBI_FOLDER . DS.'user'.DS;
		$file_handler->write( $user.'safe' . DS . '.htaccess', 'Deny from all', 'force' );
		$file_handler->write( $user.'logs' . DS . '.htaccess', 'Deny from all', 'force' );
				self::logMessage( 'Joobi installer Removed' );
	}
	public static function messageToUser($mess=null,$status=false,$step=null) {
		static $message = '';
		static $static_status = false;
		if ( !empty($mess) ) {
			if ( is_string($mess) ) {
				$message = $mess;
			} else {
				return $message;
			}		}
		if ( $status ) {
			$static_status = $status;
		}
		return $static_status;
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
	private static function createTablesCorePackages(&$installerO) {
		$folder_handler = WGet::folder();
self::logMessage( 'load real tables','install' );
		$doneTablesA = Install_Node_install::accessInstallData( 'get', 'doneTables' );
		$joobiCoreA = Install_Node_install::accessInstallData( 'get', 'joobiCore' );
		$joobiCorePackagesA = Install_Node_install::accessInstallData( 'get', 'joobiCorePackages' );
		$countCorePackagesN = count( $joobiCorePackagesA );
		$countCoreTablesN = count( $joobiCoreA );
		$divided = ceil( $countCoreTablesN / $countCorePackagesN );
		$curentCount = 0;
				foreach( $joobiCoreA as $k => $folder ) {
			$curentCount++;
			if ( !($curentCount % $divided) ) $installerO->calculateProgress( 'createTable' );
			if ( in_array( $k, $doneTablesA ) ) continue;
			$doneTablesA[] = $k;
			$real_folder = rtrim( $folder,DS ) . DS . 'database' . DS . 'real';
			if ( ! $folder_handler->exist( $real_folder ) ) {
				continue;
			}
			$sql = WClass::get( 'install.database' );
			$sql->setResaveItemMoveFile();
			$files = $folder_handler->files( $real_folder );
			$sql->extension_namekey = $real_folder;
			if ( ! $sql->import( $files, $real_folder, 'real' ) ) {
				self::logMessage( 'Could not create the library tables : '.$real_folder,'install', false, 1, true, true);
			}
			$attr = Install_Node_install::analyzePackageName( $joobiCorePackagesA[$k][0] );
			$message = 'Tables created for the package '.$attr['name'];
self::logMessage( $message,'install' );
			Install_Node_install::accessInstallData( 'set', 'doneTables', $doneTablesA );
			Install_Node_install::messageToUser( $message.'<div style="display:none;">'.$k . time().'</div>' );
			return true;
		}
		$_SESSION['joobi']['first_install'] = 20;
		$step = new stdClass;
		$step->table = 'finish';
		Install_Node_install::messageToUser( 'All tables created in the database.', false, $step );
self::logMessage( 'All tables created in the database.' ,'install', false, 1, false, false );
		return true;
	}
	private static function extractCorePackages(&$install) {
				$install->definePackagesFolder();
		$tmpfolder = PACKAGE_FOLDER . DS;
		$joobiCorePackagesA = Install_Node_install::accessInstallData( 'get', 'joobiCorePackages' );
		if ( empty($joobiCorePackagesA) ) {
			$joobiCorePackagesA = Install_Node_install::accessInstallData( 'get', 'packagelist' );
		}
		$encoding_value = 0;
		$joobiCoreA = Install_Node_install::accessInstallData( 'get', 'joobiCore' );
		foreach( $joobiCorePackagesA as $i => $filename ) {
			if (isset($filename['extracted']) && $filename['extracted']) continue;
			$attributes = Install_Node_install::analyzePackageName($filename[0]);
			$name = $attributes['name'];
			$enc = $attributes['enc'];
			$path = $tmpfolder . $filename[0];
			$folder = JOOBI_DS_ROOT. JOOBI_FOLDER . DS . ( $filename[1] != '' ? str_replace( '.', DS, trim( $filename[1] ) ) . DS : '' ) . $name;
			$joobiCoreA[$i] = $folder;
			if ( !file_exists( $path ) ) {
				echo 'ERRORS'.'Could not find the package ' . $path;
				return false;
			}
			if ( !Install_Node_install::extractAnyPackage( $folder, $path ) ) {
				echo 'ERRORS'.'Could not extract the package ' . $path . ' to the folder ' . $folder;
				return false;
			}
			$install->log( "===== Extracting to $folder \n file: $path" );
			$joobiCorePackagesA[$i]['extracted'] = true;
						@unlink($path);
			Install_Node_install::accessInstallData( 'set', 'joobiCore', $joobiCoreA );
			Install_Node_install::accessInstallData( 'set', 'joobiCorePackages', $joobiCorePackagesA );
		}
		Install_Node_install::accessInstallData( 'set', 'joobiCore', $joobiCoreA );
				Install_Node_install::_loadCommon();
		Install_Common_class::writeFrameworkDefaultConfigFile();
		$_SESSION['joobi']['first_install'] = 5;
		$step = new stdClass;
		$step->extract = 'finish';
		Install_Node_install::messageToUser( 'All packages successfully extracted!<br />Now installing packages...', false, $step );
		return true;
	}
	public static function loadLib() {
		Install_Node_install::messageToUser( 'Framework API loaded: '. (bool)class_exists( 'APIPage') );
						require_once( JOOBI_LIB_CORE . 'define.php' );
		require_once( JOOBI_DS_NODE . 'api' . DS . 'addon' . DS . JOOBI_FRAMEWORK . DS . 'api.php' );
				@error_reporting( E_ALL );
		@ini_set( 'display_errors', true );
		@ini_set( 'display_startup_errors', true );
		if ( !defined( 'JOOBI_CHARSET' ) ) define( 'JOOBI_CHARSET', WPage::encoding() );
		if ( !defined( 'PLIBRARY_NODE_CACHING' ) ) define( 'PLIBRARY_NODE_CACHING', 5 );
		Install_Node_install::_setUser();
	}
	function errorMessage(&$package) {
		Install_Node_install::errorMessages();
		$namekey = $package->mainext->namekey;
		$name = $package->mainext->name;
self::logMessage( 'error in the processing of '.$namekey,'install', false, 1, true, true);
		$namekey_info = '<div style="display:none;">'.$namekey.'</div>';
echo 'error in the processing of '.$namekey;
		$step = new stdClass;
		$step->install = 'fail';
		$step->message = $namekey;
		Install_Node_install::messageToUser('error in the processing of '.$name.$namekey_info,false,$step);
	}
	private static function errorMessages() {
		if ( class_exists('WMessage') && WExtension::exist( 'main.node' ) ) {
			$mess = WMessage::get();
			if ( !is_object($mess) ) {
				self::logMessage( 'Errors can be displayed because WMessage::get is not working','install', false, 1, true, true);
				return true;
			}
															$html = $mess->getM();
									if (trim($html) != '') {
				echo 'ERRORS'.$html;
				self::logMessage( $html, 'install', false, 1, true, true );
				return false;
			}
		}		return true;
	}
	public static function extractAnyPackage($folder,$path) {
		if ( $folder != null ) {
			if ( !Install_Node_install::mkdir($folder) ) {
				return false;
			}
			if ( ! class_exists('Archive_Tar') ) return false;
			$tar_object = new Archive_Tar( $path, 'gz' );
			$old_mask = @umask(0);
			$status = $tar_object->extract( $folder );
			@umask($old_mask);
			return $status;
		}
		return true;
	}
	public static function mkdir($folder) {
		if (!is_dir($folder)) {
			if (!Install_Node_install::mkdir(dirname($folder))) {
				return false;
			}
			$old_mask = @umask(0);
			if (!@mkdir($folder,0755)) {
				@umask($old_mask);
				echo 'ERRORS'.'Could not create the folder '.$folder;
				return false;
			}			@umask($old_mask);
		}
		@touch(rtrim($folder,DS). DS . 'index.html');
		return true;
	}
	private static function _setUser() {
		if ( !isset($_SESSION['JoobiUser']) ) {
			$myID = WUser::cmsMyUser( 'id' );
			$_SESSION['JoobiUser'] = new stdClass;
			$_SESSION['JoobiUser']->lgid = 1;
			$_SESSION['JoobiUser']->uid = 0;
			$_SESSION['JoobiUser']->id = $myID;
			$_SESSION['JoobiUser']->rolid = 8;
			$_SESSION['JoobiUser']->rolids = array(1,2,3,4,5,6,7,8);
		}
	}
	function xml($folder) {
self::logMessage( 'Loading the XML file :' . $folder, 'install', false, 1, true, true );
		$xmlfolder = $folder . DS . 'xml'.DS;
		$systemFolderC = WGet::folder();
		if ( !$systemFolderC->exist($xmlfolder) ) {
			echo 'ERRORS'.'The extension xml data files are missing in the folder '.$folder;
			return false;
		}
		$filehandler = WGet::file();
		$files = $systemFolderC->files( $xmlfolder );
		$data = array();
				foreach( $files as $k => $file ) {
			$name = explode('.',$file);
			switch($name[0]) {
								default:
					$parser= WClass::get('library.parser');
					$xml = $parser->loadFile($xmlfolder.$file);
					if (!is_array($xml)) {
						echo 'ERRORS'.'Could not read the xml file '.$xmlfolder.$file;
						return false;
					}
					$table = array();
					foreach($xml[0]['children'] as $row) {
						$obj = new stdClass;
						foreach($row['children'] as $column) {
							$key = strtolower($column['nodename']);
							if (array_key_exists('nodevalue',$column))
								$obj->$key = $column['nodevalue'];
							else
								$obj->$key = '';
						}
						$table[] = $obj;
					}
					$data[$name[0]] = $table;
					break;
			}		}
				if ( !array_key_exists('extension_node',$data) || !array_key_exists( 'extension_version', $data) ) {
self::logMessage( 'Error: The extension xml data is missing in the folder '.$xmlfolder,'install', false, 1, true, true);
			echo 'ERRORS'.'The extension xml data is missing in the folder '.$xmlfolder;
			return false;
		}
self::logMessage( 'XML info retrieved','install', false, 1, true, true );
				$version = $data['extension_version'][0]->version;
				$data['extension_node'][0]->version = $version;
		$data['extension_node'][0]->lversion = $version;
		$namekey = $data['extension_node'][0]->namekey;
		$type = $data['extension_node'][0]->type;
		$name = $data['extension_node'][0]->name;
		return array( $folder, $version, $namekey, $type, $name );
	}
	function getCorePackages(&$packages) {
		$core = array();
		foreach($packages as $k => $v) {
			if (trim($v[2])=='') {
				continue;
			}			unset($packages[$k]);
			$core[$k][]=$v;
		}
		ksort($core);
		$list = array();
		foreach($core as $v) {
			foreach($v as $w) {
				$list[] = $w;
			}		}
		return $list;
	}
	function getParent($dest) {
		$parts = explode('|',$dest);
		$fold = array_pop($parts);
		while(count($parts)>0) {
			$dest = implode('|',$parts);
			if ($parts[0]=='lib' && count($parts)==1) {
				$fold = 'lib';
				$dest = '';
			}
			$sql = WTable::get('extension','node','wid');
			$sql->whereE('folder',$fold);
			$sql->whereE('destination',$dest);
			$wid = $sql->load('o','wid');
			if (is_object($wid)) {
				return $wid->wid;
			}			$fold = array_pop($parts);
		}
		return 0;
	}
	public static function moveRootFiles($destination='') {
				$install_folder = dirname(__FILE__);
		$joobi_root_files = $install_folder . DS . 'files' . DS;
				$joobi_joobi = JOOBI_DS_JOOBI;
		if ( !empty($destination) ) {
			$joobi_joobi = dirname( dirname($destination) ) . DS;
		}
self::logMessage( 'moving entry files from '. $joobi_root_files. ' to ' . $joobi_joobi, 'install', false, 1, true, true);
		$folder_handler = WGet::folder();
		if ( $folder_handler->exist( $joobi_root_files ) ) {
			self::logMessage( 'entry files\' folder exists','install', false, 1, true, true);
			$fileHandlerC = WGet::file();
			$files = $folder_handler->files( $joobi_root_files );
			foreach( $files as $file ) {
								if ( !$fileHandlerC->move( $joobi_root_files . $file, $joobi_joobi . $file, 'force' ) ) {
self::logMessage( 'ERROR ERROR ERROR ERROR the file '. $joobi_root_files . $file . ' could not be moved to ' . $joobi_joobi . $file, 'install' , false, 1, true, true );
					return false;
				}			}
			$folder_handler->delete( $joobi_root_files );
		}
		return true;
	}
	private static function _loadCommon() {
		if ( !class_exists('Install_Common_class') ) require_once( dirname(dirname(__FILE__) ) . DS . 'class' . DS . 'common.php' );
	}
	public static function generateFirstDefines() {
				if ( !defined('JOOBI_DS_NODE') ) define( 'JOOBI_DS_NODE', JOOBI_DS_ROOT.JOOBI_FOLDER. DS . 'node'.DS );
		if ( !defined('JOOBI_LIB_CORE') ) define( 'JOOBI_LIB_CORE' , JOOBI_DS_NODE . 'library' . DS ); 		if ( !defined('JOOBI_INSTALLING') ) define( 'JOOBI_INSTALLING', 1 );
	}
	public static function analyzePackageName($package) {
		$attributes = array();
		$pieces = explode(DS,$package);
		$parts = explode('_',$pieces[count($pieces)-1]);
		$nb = count($parts);
		if ($parts[0] == 'lib' && $nb == 3) {
			$attributes['destination'] = '';
			$attributes['name'] = $parts[0];
			$attributes['enc'] = $parts[1];
			$attributes['pack_start'] = $attributes['name'].'_';
		} else {
			$attributes['destination'] = $parts[1];
			$attributes['enc'] = 0;
						if ($nb==3 || $parts[2] == 'theme' ){
												$attributes['name'] =$parts[0];
				$attributes['pack_start'] = $attributes['destination'].'_'.$attributes['name'].'_';
			} else {
								$attributes['name'] =$parts[1];
				$attributes['pack_start'] = $attributes['destination'].'_'.$attributes['name'].'_';
			}
		}
		return $attributes;
	}
	private static function _cleanAndUpdateParentOfChild() {
				$cache = WCache::get();
		$cache->resetCache( 'Model' );
		$apps = WTable::get( 'extension_node', 'main_library', 'wid' );
		$apps->whereE( 'parent', 0 );
		$apps->whereIn( 'type', array(25, 50, 75, 100) ); 		$needsUpdate = $apps->load( 'lra', 'namekey' );
				foreach( $needsUpdate as $ext ){
			$parts = explode( '.', $ext );
			$key = $parts[0] . '.node';
			$wid = WExtension::get( $key, 'wid' );
						$apps->resetAll();
			$apps->setVal( 'parent', $wid );
			$apps->whereE( 'namekey', $ext );
			$apps->update();
		}
	}
	private static function loadFromFileCache($action,$location,$data=null) {
				if ( !defined('JOOBI_DS_ROOT') ) define( 'JOOBI_DS_ROOT', dirname(JOOBI_DS_JOOBI) . DS );
		$filePath = JOOBI_DS_ROOT . 'tmp' . DS . strtolower($location) . '.jti';
		if ( $action == 'exist' ) {
			return is_file( $filePath );
		} elseif ( $action == 'delete' ) {
			if ( is_file( $filePath ) ) return unlink( $filePath );
			else return true;
		} elseif ( $action == 'set' ) {
			if ( ! is_file( JOOBI_DS_ROOT . 'tmp' ) ) {
				@mkdir( JOOBI_DS_ROOT . 'tmp' );
			}			return @file_put_contents( $filePath, serialize($data) );
		} else {				$exists = Install_Node_install::loadFromFileCache( 'exist', $location );
			if ( !$exists ) return null;
			$data = file_get_contents( $filePath );
			return unserialize( $data );
		}
	}
	public static function accessInstallData($action,$location,$data=null,$type='cache') {
		$cache = Install_Node_install::loadFromFileCache( $action, $location, $data );
		return $cache;
	}
	private function _improveInstallProcess(){
self::logMessage( 'In improveInstallProcess','install', false, 1, true, true );
			$installCommonC = WClass::get('install.process');
			if ( method_exists( $installCommonC,'copy') ) {
				return true;
			}
self::logMessage( 'In improveInstallProcess 2', 'install', false, 1, true, true );
			$file = dirname( dirname(__FILE__) ) . DS . 'class' . DS . 'process.php';
			$new_file = JOOBI_DS_JOOBI . 'node' . DS . 'install' . DS . 'class' . DS . 'process.php';
			$filehandler = WGet::file();
			$filehandler->copy( $file, $new_file, 'force' );
			return true;
	}
	private function _removeOldXmlFiles(){
		$xml_file = JOOBI_DS_JOOBI . 'node' . DS . 'files' . DS . 'xml' . DS . 'extension_dependency.xml';
		$xml_file2 = JOOBI_DS_JOOBI . 'node' . DS . 'captcha' . DS . 'addon' . DS . 'image' . DS . 'xml' . DS . 'extension_dependency.xml';
		$filehandler = WGet::file();
		if ($filehandler->exist($xml_file)&&$filehandler->exist($xml_file2)){
			$old_install_file = JOOBI_DS_JOOBI . 'node' . DS . 'install' . DS . 'class' . DS . 'package.php';
			$new_install_file = dirname(dirname(__FILE__)). DS . 'class' . DS . 'package.php';
			$filehandler->move($old_install_file,$old_install_file.'.old.php', 'force');
			$filehandler->move($new_install_file,$old_install_file, 'force');
		}
		return true;
	}
	private function _writeConfigFileIfNotExists() {
self::logMessage( 'in _writeConfigFileIfNotExists','install', false, 1, true, true);
		Install_Common_class::writeFrameworkDefaultConfigFile();
		return true;
	}
	private function _updateInstallProcessCompatibility2(){
self::logMessage( 'In updateInstallProcessCompatibility2','install', false, 1, true, true);
		$installCommonC = WClass::get('install.common');
		if ( method_exists( $installCommonC, 'writeFrameworkDefaultConfigFile' ) ) {
			return true;
		}
self::logMessage( 'In updateInstallProcessCompatibility2 1','install', false, 1, true, true);
		$folder = dirname(dirname(__FILE__));
		$new_folder = JOOBI_DS_JOOBI . 'node' . DS . 'install';
		$systemFolderC = WGet::folder();
		$systemFolderC->copy( $folder, $new_folder, 'force' );
		$this->_checkStatusAndParams();
		echo 'Install processus updated';
		exit;
	}
	private function _updateSystemLayerCompatibility(){
self::logMessage( 'in updateSystemLayerCompatibility','install', false, 1, true, true);
		$filehandler = WGet::file();
				if (method_exists($filehandler,'exist')){
			return true;
		}
self::logMessage( 'updating system layer','install', false, 1, true, true);
		$systemFolderC = WGet::folder();
		$files = $systemFolderC->files(JOOBI_DS_TEMP);
		sort($files);
		foreach($files as $file){
			if (strpos($file,'node_system_')===0){
				$dest = JOOBI_DS_JOOBI . 'node' . DS . 'system';
				$filehandler->extract(JOOBI_DS_TEMP.$file, $dest);
			}
			elseif (strpos($file,'node.system.addon_hdd_')===0){
				$dest = JOOBI_DS_JOOBI . 'node' . DS . 'system' . DS . 'addon' . DS . 'hdd';
				$filehandler->extract(JOOBI_DS_TEMP.$file, $dest);
			}		}
self::logMessage( 'System layer updated','install', false, 1, true, true);
		echo 'System layer updated';
		exit;
	}
	private function _checkStatusAndParams() {
		$installParamsA = Install_Node_install::accessInstallData( 'get', 'installParams' );
		if (!isset($_SESSION['joobi']['install_status']) || empty($installParamsA)){
			if (defined('PINSTALL_NODE_INSTALL_PARAMS')){
				$_SESSION['joobi']['install_status'] = PINSTALL_NODE_INSTALL_STATUS;
				Install_Node_install::accessInstallData( 'set', 'installParams', PINSTALL_NODE_INSTALL_PARAMS );
			}		}		return true;
	}
	private static function serverInSession() {
		if (isset($_SESSION['joobi']['config']['distrib_server']) && isset($_SESSION['joobi']['config']['distrib_server'])){
			return $_SESSION['joobi']['config']['distrib_server'];
		}		return false;
	}
	private static function _chmodJoobiFolder(){
		$systemFolderC = WGet::folder();
		$systemFolderC->changeAccess( JOOBI_DS_JOOBI );
		return true;
	}
}