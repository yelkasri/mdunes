<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
if ( !function_exists( 'WLoadFile' ) ) {
	function WLoadFile($filePath,$base=null,$expand=true,$showMessage=true) {
		return wimport( $filePath, $base, $expand, $showMessage );
	}}
class Install_Joomla30_addon {	
	public $apiVersion = 'joomla30';
		public $client = 0;
	public $position = 'position-7';
	public $publish = 0;
	public $access = 1;
	var $path = '';
	var $tables = array();
	var $same;
	var $level = 0;
	var $languageFiles = array();
	var $params = null;
	var $name = null;
		protected $path_xml = '';
	function addMenu($vars=null,$parent=1,$option=null) {
		if ( !isset($option) ) $option = JOOBI_MAIN_APP;
		$e = new Exception;
		if ( empty($this->parent) ) $this->parent = $parent;
		$menuM = WModel::get( 'joomla.menu' );
		if ( $parent==1 ) {
			$menuM->returnId( true );
		}
		foreach( $vars as $k => $v ) {
			$menuM->$k = $v;
		}
				unset( $menuM->ordering );
		if ( empty($menuM->menutype) ) $menuM->menutype = 'menu';
		if ( empty($menuM->type) ) $menuM->type = 'component';
		$menuM->published = 1;
		if ( !isset($menuM->client_id) ) $menuM->client_id = 1;
		$menuM->parent_id = $this->parent;
		if ( empty($menuM->component_id) ) {
			$extensionM = WTable::get( 'extensions', '', 'extension_id' );
			$extensionM->whereE( 'element', $menuM->title );
			$menuM->component_id = $extensionM->load( 'lr', 'extension_id' );
		}
		$menuM->img = ( empty($menuM->img) ? 'js/ThemeOffice/component.png' : $menuM->img );
		$menuM->language = '*';
				$menuCheckM = WModel::get('joomla.menu');
		$menuCheckM->whereE( 'client_id', $menuM->client_id );
		$menuCheckM->whereE( 'parent_id', $menuM->parent_id );
		$menuCheckM->whereE( 'language', $menuM->language );
		$menuCheckM->whereE( 'alias', $menuM->alias );
		$id = $menuCheckM->load( 'lr' , 'id' );
		if ( empty($id) ) {
						$status = $menuM->save();
		} else {
			$status = true;
			$menuM->id = $id;
		}
		if ( $parent ) {
			if ( !empty($menuM->id) ) return $menuM->id;
			else return 0;
		}
		return $status;
	}
	public function application() {
				$extensionO = $this->init();
				if ( empty($extensionO) ) return false;
		$this->publish = 1;
		$namekeyA = explode( '.', $extensionO->namekey );
		$namekey = 'com_' . $namekeyA[0];
				$this->_insertExtensionDB( $extensionO->name, $namekey, 'component' );
						$name = $extensionO->name;
		if ( ! $this->setXmlAndMagicFile( 'component', $name ) ) {
			$mess = WMessage::get();
			$mess->codeW('Could not create the joomla xml file for the application '.$name);
			return false;
		}					
				$status = $this->refreshMenus( $namekeyA[0] );
		if (!$status) return false;
		$this->_createRouteFile();
		return true;
	}	
	public function module() {
		$extensionO = $this->init();
		if ( empty($extensionO) ) return false;
		$namekeyA = explode( '.', $extensionO->namekey );
		$namekey = 'mod_' . str_replace( '.', '_',$extensionO->namekey );
				$this->_insertExtensionDB( $extensionO->name, $namekey, 'module' );
		$name = $extensionO->name;
		if ( ! $this->setXmlAndMagicFile( 'module', $name ) ) {
			$mess = WMessage::get();
			$mess->codeW( 'Could not create the joomla xml file for the module ' . $name );
			return false;
		}	
		$client = $this->client == 'admin' ? 1 : 0;
						$sql = WModel::get('joomla.modules');
		$sql->whereE( 'module', $this->same );
		$sql->whereE( 'client_id', $this->client );
		$result = $sql->load( 'o', 'id' );
		if ( !empty($result) ) return true;
				$sql->whereE( 'position', $this->position );
		$sql->select('ordering',0, null, 1);
		$order = $sql->load('lr');
				$sql->title = $this->title;
		$sql->module = $this->same;
		$sql->ordering = $order+1;
		$sql->position = $this->position;
		$sql->showtitle = '1';
		$sql->published = $this->publish;
				$sql->client_id = $this->client;
		$sql->access = $this->access; 		$sql->params = $this->xml_params;
		$sql->returnId();
		$sql->insert();
				if ( !empty($sql->id) ) {
			$model= WTable::get( 'modules_menu','','moduleid,menuid');
			$model->whereE('moduleid',$sql->id);
			$res=$model->exist();
			if (!$res){
				$model->moduleid=$sql->id;
				$model->menuid=0;
				$model->insert();
			}		}	
		return true;
		}	
		public function plugin() {
			$extensionO = $this->init();
			if ( empty($extensionO) ) return false;
			$namekeyA = explode( '.', $extensionO->namekey );
			$namekey = str_replace( '.', '_',$extensionO->namekey );							if ( 'button' == $namekeyA[1] ) $namekeyA[1] = 'editors-xtd';
			$this->_insertExtensionDB( $extensionO->name, $namekey, 'plugin', $namekeyA[1] );
			$name = $extensionO->name;
			if ( !$this->setXmlAndMagicFile( 'plugin', $name ) ) {
				$mess = WMessage::get();
				$mess->codeW('Could not create the joomla xml file for the plugin '.$name);
				return false;
			}	
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
										if ( 'button' == $extensionO->folder ) $extensionO->folder = 'editors-xtd';
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
		private function _createRouteFile() {
			$ext = $this->init();
			$folder = $ext->folder;
			$path = JOOBI_DS_ROOT.'components' . DS . 'com_'.$folder . DS . 'router.php';
			$phpcode = '<?php
if ( defined(\'_JEXEC\') && !defined(\'JOOBI_SECURE\') ) define( \'JOOBI_SECURE\', true );
defined(\'JOOBI_SECURE\') or die(\'J....\');
/**
* @copyright Copyright (C) 2006-'.date('Y').' Joobi All rights reserved.
* This file is released under the GPLv3
*/
function '.ucfirst($folder).'BuildRoute(&$query){
if(!class_exists("WPage")){
$status=include(JPATH_ROOT.DIRECTORY_SEPARATOR."joobi".DIRECTORY_SEPARATOR."entry.php");
if (!$status) return;
}
return WPage::buildURL($query);
}//endfct
function '.ucfirst($folder).'ParseRoute($segments){
if (!class_exists("WPage")){
$status=include(JPATH_ROOT.DIRECTORY_SEPARATOR."joobi".DIRECTORY_SEPARATOR."entry.php");
if (!$status) return;
}
return WPage::interpretURL($segments);
}//endfct
';
			$file_handler = WGet::file();
			$file_handler->write($path,$phpcode,'force');
		}	
		function getMainTag(){
			return 'extension';
		}	
		function getIconLink($icon,$folder,$app=false) {
			if ( $app ) {
																								return '../joobi/node/'.$folder.'/images/'.$icon.'.png';
				} else {
					return '../administrator/components/com_' . $folder . '/images/'. $icon . '.png'; 	
				}			}	
			public function setXmlAndMagicFile($type,$extension_name) {
				$array = array();
				$group = '';
												$this->loadXmlFile();
				$ext = $this->init();
								if ( $this->generate ) $this->generateXml( $ext, $type );
				$path_xml = '';
				$path_php = '';
				switch ( $type ) {
										case 'component':
						$this->xmldata[0]['children'][] = array('nodename'=>'uninstallfile','attributes'=>array(),'nodevalue'=>'uninstall.php');
												$this->getComponentPaths( $ext->folder );
						break;
											case 'plugin':
												$name = $this->getStandardName( 'plugins' ,$ext->namekey );
						$this->xml_params = '';
												$this->group = $ext->folder;
						$this->name = $ext->name;	
						if ( $this->generate ) {
							$this->xmldata[0]['attributes']['type'] = 'plugin';
							$this->xmldata[0]['attributes']['group'] = $this->group;
														$this->xmldata[0]['children'][] = array('nodename'=>'files','attributes'=>array(),'children'=>array(array('nodename'=>'filename','attributes'=>array('plugin'=>$name),'nodevalue'=>$name.'.php')));
							$fielsetsA = array();
														if ( !empty( $this->content ) ) {
								$fielsetsA[] = array( 'nodename'=>'fieldset','attributes'=>array( 'name'=>'basic', 'addfieldpath' =>'/components/com_' . JOOBI_MAIN_APP . '/fields' ),'children'=> $this->content );
							}	
							if ( !empty($fielsetsA) ) {
								$XMLParams = array('nodename'=>'fields','attributes'=>array('name'=>'params'),'children'=> $fielsetsA );
								$this->xmldata[0]['children'][] = array('nodename'=>'config','attributes'=>array(),'children'=> array($XMLParams) );
							}	
						} else {
														$this->xml_params = $this->getParams();
														$uninstallfile_found=false;
							foreach($this->content[0]['children'] as $k => $mainnode){
								if ($mainnode['nodename'] == 'name'){
									$this->name = $mainnode['nodevalue'];
																																			}							}	
																											}	
												$folder = JOOBI_DS_ROOT . 'plugins' . DS . $this->group;
						$systemFolderC = WGet::folder();
						if (!$systemFolderC->exist($folder) && !$systemFolderC->create($folder,'',true)){
							return false;
						}	
												$this->same = $name;
						$same =$folder . DS . $name . DS . $name;
						$this->path_xml = $same.'.xml';
						$this->path_php = $same.'.php';
																		break;
											case 'module':
												$name = $this->getStandardName($type,$ext->namekey);
						$this->title = $ext->name;													
						$this->xml_params = '';	
						if ( $this->generate ) {
														$this->xmldata[0]['attributes']['client'] = $this->client;
														$this->xmldata[0]['children'][] = array('nodename'=>'files','attributes'=>array(),'children'=>array(array('nodename'=>'filename','attributes'=>array('module'=>$name),'nodevalue'=>$name.'.php')));
							if ( is_string($this->content) ) $this->content = trim( $this->content );
							$fielsetsA = array();
														if ( !empty( $this->content ) ) {
								$fielsetsA[] = array( 'nodename'=>'fieldset','attributes'=>array( 'name'=>'basic'),'children'=> $this->content  );
							} else {
																																$myContetA = array();
								$myContetA[] = array( 'nodename'=>'field','attributes'=>array( 'name'=>'preferences', 'label'=>'Preferences', 'description'=>'Click on the link to edit the preferences of the extension', 'type'=>'preferences', 'id'=>'preferences' ) );
								$myContetA[] = array( 'nodename'=>'field','attributes'=>array( 'name'=>'moduleclass_sfx', 'label'=>'COM_MODULES_FIELD_MODULECLASS_SFX_LABEL', 'description'=>'COM_MODULES_FIELD_MODULECLASS_SFX_DESC', 'type'=>'text' ) );
								$cacheA = array();
								$cacheA['name'] = 'cache';
								$cacheA['type'] = 'list';
								$cacheA['default'] = '0';
								$cacheA['label'] = 'COM_MODULES_FIELD_CACHING_LABEL';
								$cacheA['description'] = 'COM_MODULES_FIELD_CACHING_DESC';
								$cacheA['children'] = array();									$cacheA['children'][0]['nodename'] = 'option';
								$cacheA['children'][0]['attributes']['value'] = '1';
								$cacheA['children'][0]['nodevalue'] = 'JGLOBAL_USE_GLOBAL';
								$cacheA['children'][1]['nodename'] = 'option';
								$cacheA['children'][1]['attributes']['value'] = '0';
								$cacheA['children'][1]['nodevalue'] = 'COM_MODULES_FIELD_VALUE_NOCACHING';
								$myContetA[] = array( 'nodename'=>'field','attributes'=>$cacheA );
								$fielsetsA[] = array( 'nodename'=>'fieldset','attributes'=>array( 'name'=>'basic'),'children'=> $myContetA );
							}	
						 $XMLParams = array('nodename'=>'fields','attributes'=>array( 'name'=>'params', 'addfieldpath' =>'/components/com_' . JOOBI_MAIN_APP . '/fields' ), 'children'=> $fielsetsA );
						 $this->xmldata[0]['children'][] = array('nodename'=>'config','attributes'=>array(),'children'=> array($XMLParams) );
						 						} else {
														$uninstallfile_found = false;
							foreach($this->content[0]['children'] as $k => $mainnode){
								if ($mainnode['nodename'] == 'name'){
									$this->title = $mainnode['nodevalue'];
																																			}							}	
														$this->xml_params = $this->getParams();
						}	
												$this->same = $name;
						$same = $this->getModuleFolder($name);
						$this->path_xml = $same.'.xml';
												$this->path_php = $same.'.php';
																		break;
				}	
								return $this->writeFiles( $type, $extension_name, $ext->namekey );
			}	
			public function refreshMenus($namekey,$name='') {
				$this->parentRefreshMenus( $namekey, '' );
				$links_path = JOOBI_DS_ROOT.'components' . DS . 'com_'.$namekey. DS . 'views';
				$systemFolderC = WGet::folder();
				if ( $systemFolderC->exist($links_path) ) {
														}	
								if ( $this->loadYIDforJoomlaMenus( $namekey, true ) ) {
					$list = $this->getSubMenusList();
					if ( !empty($list) ) {
						$parser = WClass::get( 'library.parser' );
						$filehandler = WGet::file();
						foreach( $list as $row ) {
							$trans = $this->getMenuTranslation( $row->mid, 1, array('name','description') );
							$foldername = strtolower(str_replace(' ', '_', $trans->name ));
							$xml_path = $links_path. DS .$foldername. DS . 'tmpl' . DS . 'default.xml';
							$php_path = $links_path. DS .$foldername. DS . 'tmpl' . DS . 'default.php';
							$parts = explode('&',$row->action);
							$params= array();
							foreach($parts as $part){
								$info = explode('=',$part,2);
								if (count($info)==2){
									$params[]=array(
											'nodename'=>'field',
											'attributes'=>array(
													'name'=>$info[0],
													'type'=>'text',
													'default'=>$info[1],
													'label'=>$info[0],
													'description'=>''
											)
									);
								}
							}	
							$xmldata = array(
									array(
											'nodename'=>'metadata',
											'children'=>array(
													array(
															'nodename'=>'layout',
															'attributes' => array( 'title' => $trans->name ),
															'children'=>array(
																	array(
																			'nodename'=>'message',
																			'nodevalue'=>$trans->description,
																	)
															)
													),
													array(
															'nodename'=>'fields',
															'attributes' => array( 'name' => 'request' ),
															'children'=>array(
																	array(
																			'nodename'=>'fieldset',
																			'attributes' => array(
																					'name' => 'request',
																					'addfieldpath' => 'components/com_'.$namekey
																			),
																			'children'=> $params
																	)
															)
													)
											)
									)
							);
							$phpcode = '<?php ';
														if ( !$this->_createMetadataXML( $links_path, $foldername, $trans->name, $trans->description ) ) return false;
																					if (!$filehandler->write( $xml_path, $parser->getXML($xmldata),'force') ) {
								return false;
							}	
														if ( !$filehandler->write( $php_path, $phpcode, 'force' ) ) {
								return false;
							}	
						}					}				}	
				return true;
			}	
			function parentRefreshMenus($namekey,$notUsed) {
								if ( $this->loadYIDforJoomlaMenus( $namekey ) ) {
										$this->deleteMenus( $namekey, '' );
				} else {
					return true;
				}	
								$status = $this->addMenus( $namekey );
				if ( $status ) $this->updateMenuLinks();
				return $status;
			}	
			function addMenus($app) {
			 								if ( $this->hideJcenterMenuIfOfflinePackage($app) ) return true;
				$ext = $this->init();
				if ( !$this->addMainMenu( $app, $ext ) ) return true;
								$this->lgid = WApplication::mainLanguage( 'lgid', false, array(), 'admin' );
				$this->code = WApplication::mainLanguage( 'code', false, array(), 'admin' );
								$list = $this->getSubMenusList();
				if ( empty($list) ) return true;
				if ( JOOBI_MAIN_APP != $app ) {
					$rowWelcome = new stdClass;
					$rowWelcome->mid = 9999999;
					$rowWelcome->action = $app . '&task=welcome';
					$rowWelcome->icon = '';
					$this->addSubMenuFromObject( $rowWelcome, $app );
				}	
								foreach( $list as $row ) {
										$this->addSubMenuFromObject( $row, $app );
				}	
				return true;
			}	
			function addMainMenu($app,&$ext) {
				$vars = array( 'title'=>'com_'.$app,'alias'=>$ext->name,
						'path'=>$ext->name,
						'link'=>'index.php?option=com_'.$app.'&controller='.$app,
						'img'=> $this->getIconLink( $app, $app, true )
				);
				$this->parent = $this->addMenu( $vars, 1 );
				return true;
			}	
			function buildMenuElement($app,$name,$row) {
				$icon = ( $row->icon == '' ? '' : $this->getIconLink( $row->icon, $app ) );
				return array('title'=>'com_'.$app,
						'alias'=>$name,
						'path'=>$app.'/'.$name,
						'link'=>'index.php?option=com_'.$app.'&controller='.$row->action,
						'img'=>$icon
				);
			}	
			function updateMenuLinks(){
			}	
			function deleteMenus($namekey,$notUsed) {
				$menuM = WModel::get('joomla.menu');
				$menuM->whereE('title', 'com_'.$namekey );
				$menuM->setDistinct( 'component_id' );
				$this->deletedComponentIds = $menuM->load( 'lra', 'component_id' );
								$menuM->whereE('title', 'com_'.$namekey );
				return $menuM->delete();
			}	
			function menuExist($name,$menuType) {
								$CMSMenuTypeTable = WTable::get( 'menu_types','','id');
				$CMSMenuTypeTable->whereE( 'menutype', $menuType );
				$exist = $CMSMenuTypeTable->load('lr', 'id' );
				if ( empty($exist) ) return false;
				$menuM = WModel::get('joomla.menu');
								$menuM->whereE( 'menutype', $menuType);
				$menuM->openBracket();
				$menuM->whereE('alias', strtolower( str_replace(' ', '-', $name) ) );
				$menuM->operator('OR');
				$menuM->whereE( 'title', $name );
				$menuM->closeBracket();
				return $menuM->load('lr', 'id' );
			}	
				function writeFiles($type,$extension_name,$namekey) {
										if ( !$this->setMagicFile( '', $type, $extension_name) ) {
						return true;
					}	
										if ( ! $this->setUninstallFile($namekey) ) {
						return true;
					}	
					if ( $this->generate ) {
						$xmldata = 'xmldata';
					} else {
						$xmldata = 'content';
					}	
					$myXML = $this->$xmldata;
					if ( !isset($myXML[0]['attributes']['version']) ) {
						$myXML[0]['attributes']['version'] = '1.6';
						$myXML[0]['attributes']['method'] = 'upgrade';
					}	
										$parser = WClass::get('library.parser');
					$filehandler = WGet::file();
					$parsedXML = $parser->getXML( $myXML );
					$filehandler->write( $this->path_xml, $parsedXML, 'force' );
					return true;
				}	
				function getComponentPaths($folder) {
					$folder_path = JOOBI_DS_ROOT.'administrator' . DS . 'components' . DS . 'com_'.$folder.DS;
					$this->path_xml = $folder_path.$folder.'.xml';
					$this->path_php = array($folder_path. $folder.'.php',JOOBI_DS_ROOT.'components' . DS . 'com_'.$folder. DS .$folder.'.php');
					$this->path_uninstall = $folder_path . 'uninstall.php';
					$this->path_install = $folder_path . 'install.php';
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
				public function loadXmlFile() {
					$this->generate = true;
					$ext = $this->init();
															$extensionM = WTable::get( 'extension_node', 'main_library', 'wid' );
					$extensionM->whereE( 'namekey', $ext->namekey );
					$install = $extensionM->load( 'lr', 'install' );
					if ( empty($install) ) {
						$this->content = '';
						return true;
					}	
					$this->content = @unserialize( $install );
					if ( empty($this->content) || !is_array($this->content) ) {
						return false;
					}	
										foreach( $this->content as $key => $oneParam ) {
						if ( $this->content[$key]['nodename'] =='param' ) $this->content[$key]['nodename'] = 'field';
					}	
				}	
				function clean($removeFolder=true) {
					$sql = WModel::get('joomla.extensions');
					$sql->where('element', 'LIKE', '%com_joobiinstaller%');
					$sql->delete();
										$dbHandler = WTable::get();
					$dbHandler->load( 'q', "DELETE FROM `#__assets` WHERE `name` like '%com_joobiinstaller%';" );
					$dbHandler->load( 'q', "DELETE FROM `#__menu` WHERE `link` like '%com_joobiinstaller%';" );
										$installerName = defined('INSTALLER_FILE_NAME') ? INSTALLER_FILE_NAME : 'joobiinstaller';
					if ( $removeFolder ) {
						$adminFolder = JOOBI_DS_ROOT.'administrator' . DS . 'components' . DS . 'com_' . $installerName;
						$siteFolder = JOOBI_DS_ROOT.'components' . DS . 'com_' . $installerName;
						$systemFolderC = WGet::folder();
												$systemFolderC->delete( $adminFolder );
						$systemFolderC->delete( $siteFolder );
					}	
					return true;
				}	
				function getPluginTable(){
					return 'joomla.extensions';
				}	
				public function setUninstallFile($namekey) {
																				$status = false;
					$fileHandler = WGet::file();
					$uninstallfile = JOOBI_DS_NODE . 'api' . DS . 'addon' . DS . $this->apiVersion . DS.'uninstall.php';
					$explodeNamekeyA = explode( '.', $namekey );
					$namekeyFCT = '_' . strtoupper( $explodeNamekeyA[0] );
					if ( !empty($this->path_uninstall) ) $status = $fileHandler->write( $this->path_uninstall, str_replace(array( '__NAMEKEY__', '__NAMEKEYFCT__', '__JOOBI__' ),array( $namekey, $namekeyFCT, JOOBI_FOLDER ), $fileHandler->read($uninstallfile)),'force');
					if ( $status ) {
						$uninstallfile = JOOBI_DS_NODE . 'api' . DS . 'addon' . DS . $this->apiVersion . DS . 'install.php';
						$explodeNamekeyA = explode( '.', $namekey );
						$namekeyFCT = '_' . strtoupper( $explodeNamekeyA[0] );
						if ( !empty($this->path_install) ) $status = $fileHandler->write( $this->path_install, str_replace( array( '__NAMEKEY__', '__NAMEKEYFCT__', '__JOOBI__' ), array( $namekey, $namekeyFCT, JOOBI_FOLDER ), $fileHandler->read( $uninstallfile) ), 'force');
					}	
					return true;
					return $status;
				}	
								protected function _insertExtensionDB($name,$namekey,$type='component',$folder=null) {
					if ( strlen($name) > 100 ) {
						$messageM = WMessage::get();
						$NAME = $name;
						$messageM->userE('1298294123FNDM',array('$NAME'=>$NAME));
					}					$extensionsM = WModel::get('joomla.extensions');
															$extensionsM->whereE( 'element', $namekey );
					$extensionsM->whereE( 'type', $type );
					$exist = $extensionsM->load( 'lr', 'extension_id' );
					if ( !$exist ) {
						$extensionO = $this->init();
						$myManifest = array();
						$myManifest['legacy'] = false;
						$myManifest['name'] = $name;
						$myManifest['type'] = 'component';
						$myManifest['creationDate'] = WApplication::date('F Y', ( empty($extensionO->modified) ? time(): $extensionO->modified ) );
						$myManifest['author'] = 'Joobi';
						$myManifest['copyright'] = '(C) 2006-'.date('Y').' Joobi. All rights reserved.';
						$myManifest['authorEmail'] = 'support@joobi.co';
						$myManifest['authorUrl'] = 'https://joobi.co';
						$version = $this->getVersion( $extensionO->wid );
						$myManifest['version'] = ( empty($version) ) ? '1.0' : $version;
						$lgid = WLanguage::get( 'en', 'lgid');
						$myManifest['description'] = $this->getDescription( $lgid, $extensionO->wid );
						$extensionsM->setVal( 'type', $type );
						$extensionsM->setVal( 'name', $name );
						$extensionsM->setVal( 'element', $namekey );
						$extensionsM->setVal( 'client_id', $this->client );
																		switch ( $type ) {
							case 'plugin':
								$extensionsM->setVal( 'enabled', $this->publish );
								break;
							case 'module':
								$extensionsM->setVal( 'enabled', 1 );
								break;
							case 'component':
							default:
								$extensionsM->setVal( 'enabled', 1 );
								break;
						}													
						$extensionsM->setVal( 'access', $this->access );
						$extensionsM->setVal( 'protected', '0' );
						if ( function_exists('json_encode') ) $extensionsM->setVal( 'manifest_cache', json_encode($myManifest) );
						if ( isset($folder) ) $extensionsM->setVal( 'folder', $folder );
						$extensionsM->returnId();
						$id = $extensionsM->insertIgnore();
					} else {
						$id = $exist;
					}	
										if ( empty($id) ){
						$extensionsM->whereE( 'element', $namekey );
						$extensionsM->whereE( 'type', $type );
						$id = $extensionsM->load('lr', 'extension_id' );
					}	
					$this->componentID = $id;
					return true;
				}	
				protected function _createMetadataXML($linksPath,$folderName,$title,$description) {
					$xml_path = $linksPath . DS . $folderName . DS . 'metadata.xml';
					$xmldata = null;
					$xmldata = array(
							array(
									'nodename'=>'metadata',
									'children'=>array(
											array(
													'nodename'=>'view',
													'attributes' => array( 'title' => $title ),
													'children'=>array(
															array(
																	'nodename'=>'message',
																	'nodevalue'=> $description
															)
													)
											)
									)
							)
					);
										$filehandler = WGet::file();
					$parser = WClass::get('library.parser');
					if (!$filehandler->write( $xml_path, $parser->getXML( $xmldata ), 'force' ) ) {
						return false;
					}	
					return true;
				}	
				function addSubMenuFromObject(&$row,$app) {
					$this->parentAddSubMenuFromObject( $row, $app );
					$list = $this->getMenuTranslation( $row->mid );
					if ( empty($list) || ! is_array($list) ) return;
					foreach( $list as $item ) {
						$code = WLanguage::get( $item->lgid, 'code' );
						$this->languageFiles[$code][$row->mid] = $item->name;
					}	
				}	
				function parentAddSubMenuFromObject(&$row,$app) {
										$name = $this->getMenuTranslation( $row->mid, $this->lgid );
					if ( $name===false && $this->lgid != $this->en->lgid ) {
						$name = $this->getMenuTranslation( $row->mid, $this->en->lgid );
					}	
					if ( $name===false ) {
						$lgid = $this->lgid;
						$mess = WMessage::get();
						$mess->codeE('Could not get the submenu name translation for the application '.$app.' neither in the language with the id '.$lgid.' nor in english.');
						$name = 'No trans found for menu id ' . $row->mid;
					}	
										$vars = $this->buildMenuElement( $app, $name, $row );
					$this->addMenu( $vars );
										$this->copyMenuIcon( $app, $row->icon );
					return true;
				}	
				function copyMenuIcon($folder='',$icon='') {
					$images = $this->getThemeFolder();
					$filehandler = WGet::file();
					$icon = $icon . '.png';
					if ( $filehandler->exist( $images . $icon ) ) {
						$destfolder = $this->iconFolderDestination() . 'com_' . $folder . DS . 'images' . DS;
						$filehandler->copy( $images . $icon, $destfolder . $icon, 'force' ); 					}	
					return true;
				}	
				protected function loadYIDforJoomlaMenus($app,$front=false) {
															$name = $app.'_main';
					if ( $front ) {
						$name .= '_fe';
					}															$this->yid = WView::get( $name, 'yid', null, null, false, true );
					return true;
				}	
				function getThemeFolder(){
					return JOOBI_DS_THEME.'admin' . DS . 'joomla30' . DS . 'images' . DS . 'app' . DS . '16'.DS; 				}	
				function getSubMenusList() {
					if ( empty( $this->yid ) ) {
						return false;
					}	
										$namekey = WView::get( $this->yid, 'namekey' );
					$libraryViewMenusM = WModel::get( 'library.viewmenus', 'object' );
										$libraryViewMenusM->whereE( 'yid', $this->yid );
										$libraryViewMenusM->where( 'type', '!=', 55 );
					$libraryViewMenusM->whereE( 'publish', 1 );
					$libraryViewMenusM->whereE( 'parent', 0 );
					$libraryViewMenusM->orderBy( 'ordering', 'ASC' );
					$libraryViewMenusM->setLimit( 500 );
					$list = $libraryViewMenusM->load( 'ol' );
					if ( empty($list) )  return false;
					return $list;
				}	
				function trim($object){
					$object->name = trim($object->name);
					return $object;
				}	
				private function getMenuTranslation($mid,$lgid=null,$info='name') {
					if ( 9999999 == $mid ) {
						WText::load( 'install.node' );
						return WText::t('1206961889EEYJ');
					}	
					$libraryWiewmenustransM = WModel::get( 'library.viewmenustrans', 'object' );
					$libraryWiewmenustransM->whereE('mid',$mid);
					if ( isset($lgid) ) {
						$libraryWiewmenustransM->whereE( 'lgid', $lgid );
						$name = $libraryWiewmenustransM->load( 'o', $info );
												if ( empty($name) ) {
							$libraryWiewmenustransM->whereE('mid',$mid);
							$libraryWiewmenustransM->whereE( 'lgid', 1 );								$name = $libraryWiewmenustransM->load( 'o', $info );
						}	
						if (!is_object($name)) return false;
						if (is_array($info)) return $name;
						return trim($name->$info);
					} else {
						if (!is_array($info)){
							$info = array( $info );
						}	
						$info[]='lgid';
						$libraryWiewmenustransM->setLimit( 5000 );
						$libraryWiewmenustransM->orderBy( 'lgid', 'ASC' );
						$names = $libraryWiewmenustransM->load( 'ol', $info );
						if (!is_array($names) || count($names)==0) return false;
						return array_map( array($this,'trim'), $names );
					}	
				}	
				function getType(&$type) {
					if ($type=='plugin'){
						$type=$this->getPluginTable();
					}				}	
				function getModulesFolder(){
										if ( $this->client ) {
						return JOOBI_DS_ROOT.'administrator' . DS . 'modules'.DS;
					}					else{
						return JOOBI_DS_ROOT.'modules'.DS;
					}				}	
				protected function hideJcenterMenuIfOfflinePackage($app) {
															return false;
										if ( $app == JOOBI_MAIN_APP && file_exists( JOOBI_DS_INSTALLFOLDER . 'lib_packages.txt' ) ) {
						return true;
					} else {
					}	
					return false;
				}	
				function iconFolderDestination() {
					return JOOBI_DS_ROOT . 'administrator' . DS . 'components' . DS;
				}	
				protected function getModuleFolder($name){
					return $this->getModulesFolder() . $name . DS . $name;
				}	
				public function generateXml(&$ext,$type) {
					$this->version = $this->getVersion( $ext->wid );
										$desc = '';
					if ( isset($this->tables) ) {
						$lgid = 0;
						if ( array_key_exists( 'language_node', $this->tables ) ) {								foreach($this->tables['language_node'] as $row) {									if ($row->code == 'en'){
									$lgid=$row->lgid;
									break;
								}							}						}	
						if (array_key_exists('extension_trans',$this->tables) && $lgid != 0){
							foreach($this->tables['extension_trans'] as $row){
								if ($row->wid==$ext->wid && $row->lgid == $lgid){
									$desc=$row->description;
									break;
								}							}						}					} else {
						$lgid = WLanguage::get( 'en', 'lgid');
						if ($lgid){
							$desc = $this->getDescription( $lgid, $ext->wid );
						} else {
							$desc = '';
						}					}	
										$desc = '
<table style="text-align: left; width: 500px; margin-left: auto; margin-right: auto; background-color: rgb(12, 142, 194); color: white;" border="0" cellpadding="10" cellspacing="10">
<tbody>
<tr align="center">
<td colspan="2" rowspan="1" style="vertical-align: top;"><big><big><span style="font-weight: bold;">Joobi Apps</span></big></big><br></td>
</tr>
<tr>
<td style="text-align: center;"><big><span style="font-weight: bold;">Documentation</span></big><br>
<br>
<small>Find and share ideas with other Joobi users around the world.</small><br>
<br><a style="color: white;"
href="https://joobi.co/r.php?l=documentation" target="_blank">Read
Tutorials</a><br>
</td>
<td style="text-align: center;"><big style="font-weight: bold;">Ticket System</big><br>
<small>Do you have a complex problem or question? Our friendly and knowledgeable technical staff is always ready to help!</small><br>
<br>
<a style="color: white;" href="http://joobi.info/support" target="_blank">Submit a Ticket</a><br>
</td>
</tr>
<tr>
<td style="text-align: center;"><big style="font-weight: bold;">Community Forum</big><br>
<br>
<small>Other Places You\'d Find Answers</small><br>
<br>
<a style="color: white;" href="https://joobi.co/r.php?l=forum" target="_blank">Ask a Question</a><br>
</td>
<td style="text-align: center;"><big><span style="font-weight: bold;">Live Chat</span></big><br>
<small>Do you have a presale and product related questions? Our caring and dedicated support agent is ready with answers!</small><br>
<br>
<a style="color: white;" href="http://joobi.info/support" target="_blank">Talk to Us</a><br>
</td>
</tr>
</tbody>
</table>
';
					if ( empty($ext->modified) ) $ext->modified = time();
										$options = array( 'name'=>$ext->name, 'creationDate'=>date('F Y',$ext->modified) , 'version'=>$this->version, 'description'=> $desc );
										$sql = WModel::get('apps.info');
					$sql->whereE('wid',$ext->wid);
					$data_infos = $sql->load('o');
					if ( !empty($data_infos) ) {
						$options['author']=$data_infos->author;
						$options['authorname']=$data_infos->author;
						if ( !empty($data_infos->author) ) $options['copyright'] = '(C) 2006-'.date('Y') . ' ' . $data_infos->author . '. All rights reserved.';
						$options['authorEmail']='';
						$options['authorUrl']=$data_infos->homeurl;
						$options['version']=$data_infos->userversion;
					}	
					if ( empty($options['author']) ) $options['author'] = 'Joobi';
					if ( empty($options['authorname']) ) $options['authorname'] = 'Joobi';
					if ( empty( $options['copyright'] ) ) $options['copyright'] = '(C) 2006-'.date('Y').' Joobi. All rights reserved.';
					if ( empty($options['authorEmail']) ) $options['authorEmail'] = 'wecare@joobi.co';
					if ( empty($options['authorUrl']) ) $options['authorUrl'] = 'https://joobi.co';
					if ( empty($options['license']) ) $options['license'] = 'GNU GLP v3';
					if ( empty($options['version']) ) $options['version'] = '1.0';
										foreach( $options as $k => $v ) {
						$array[]=array('nodename'=>$k,'attributes'=>array(),'nodevalue'=>$v);
					}	
					$this->xmldata = array(
							array(
									'nodename'=>$this->getMainTag(),
									'attributes'=>array(
											'type'=>$type
									),
									'children'=>$array
							)
					);
				}	
	function getVersion($wid) {
		static $versionA =array();
		if ( !isset($versionA[$wid]) ) {
			$sql = WModel::get( 'apps.info' );
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
					}					return $descA[$key];
				}	
				public static function magicFile($type='',$extension_name='') {
					$beforeLoad = '';
					$afterLoad = '';
					if ($type=='plugins') $type = 'plugin';
					if (in_array($type,array('plugin'))){
						$beforeLoad = 'if (!isset($_SESSION[\'joobi\'][\'first_install\'])){ ';
						$afterLoad = '}';
					}	
					return '<?php
/**
* @copyright Copyright (C) 2006-'.date('Y').' Joobi All rights reserved.
* This file is released under the GPL v3
*/
'.$beforeLoad.'
$joobiEntryPoint = __FILE__ ;
$status = @include(JPATH_ROOT.DIRECTORY_SEPARATOR. \''. JOOBI_FOLDER .'\'.DIRECTORY_SEPARATOR.\'entry.php\');
if (!$status && !defined(\'JOOBI_DS_INSTALLFOLDER\'))
echo "We were unable to load Joobi library for the '.$type.' '.$extension_name.'. If you removed the joobi folder, please also remove this '.$type.' from the Joomla '.$type.' manager.";
'.$afterLoad;
				}	
				function setMagicFile($file='',$type='',$extension_name=''){
					if (empty($file)){
						if (!isset($this->path_php) || empty($this->path_php)){
							return true;
						}						else{
							$file =& $this->path_php;
						}					}	
					if (is_array($file)){
						foreach($file as $f){
							if (!$this->setMagicFile($f,$type,$extension_name)){
								return false;
							}						}						return true;
					}	
					$content = self::magicFile( $type, $extension_name );
					$filehandler = WGet::file();
					return $filehandler->write( $file, $content, 'force' );
				}	
				function getParams() {
					$string_params ='';
										if ( @is_array($this->content) && array_key_exists(0,$this->content) && array_key_exists('children',$this->content[0])){
						foreach( $this->content[0]['children'] as $child ) {
							if ($child['nodename'] == 'administration' && array_key_exists('children',$child) && count($child['children'])>0){
								foreach($child['children'] as $subchild){
									if ($subchild['nodename'] == 'params' && array_key_exists('children',$subchild) && count($subchild['children'])>0){
										$string_params .= $this->_getParamsXML($subchild);
									}								}							}							elseif ($child['nodename'] == 'params' && array_key_exists('children',$child) && count($child['children'])>0){
								$string_params.= $this->_getParamsXML($child);
							}						}					}	
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
						}					}	
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
									}								}								else{
									$message = WMessage::get();
									$notTRanslatedYet = 'The XML file has a "param" tag without a "name" attribute.';
									$message->userN($notTRanslatedYet);
								}							}							else{
								$message = WMessage::get();
								$notTRanslatedYet = 'The XML file has a "param" tag without attributes.';
								$message->userN($notTRanslatedYet);
							}						}						else{
							$message = WMessage::get();
							$notTRanslatedYet = 'The XML file has an unknown child tag in the "params" tag.';
							$message->userN($notTRanslatedYet);
						}					}					return $string_params;
				}	
}