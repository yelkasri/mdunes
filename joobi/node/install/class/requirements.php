<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Install_Requirements_class {
	private static $_requirementsA = array();
	private $_panesA = array( 'server', 'communication', 'php', 'sql', 'folder', 'framework' );
	private $_finalStatus = 'success';
	private $_Yes = null;
	private $_No = null;
	public function displayRequirements() {
		if ( !empty(self::$_requirementsA) ) return self::$_requirementsA;
		$this->_setup();
		foreach( $this->_panesA as $area ) {
			$tab = new stdClass;
			$tab->name = $this->_paneName( $area );
			$fct = '_' . $area . 'Display';
			$paneA = $this->$fct();
			if ( !empty($paneA) ) $tab->paneA = $paneA;
			self::$_requirementsA[] = $tab;
		}
		return self::$_requirementsA;
	}
	public function checkRequirements() {
		if ( 'fail' == $this->_finalStatus ) return false;
		else return true;
	}
	private function _setup() {
		$this->_Yes = WText::t('1206732372QTKI');
		$this->_No = WText::t('1206732372QTKJ');
	}
	private function _addElement($map,$name,$value,$status,$description='',$type='') {
		if ( empty($type) ) $type = 'output.text';
		$reqO = new stdClass;
		$reqO->name = $name;
		$reqO->description = $description;
		$reqO->map = $map;
		switch( $status ) {
			case 'success':
				$color = 'success';
				break;
			case 'fail':
				$color = 'danger';
				$this->_finalStatus = 'fail';
				break;
			case 'warning':
				$color = 'warning';
				break;
			default:
				$color = 'info';
				break;
		}
		$reqO->value = '<span class="label label-' . $color . '">' . $value . '</span>';
		$reqO->type = $type;
		$typeSplitA = explode( '.', $reqO->type );
		$reqO->typeNode = $typeSplitA[0];
		$reqO->typeName = $typeSplitA[1];
		return $reqO;
	}
	private function _serverDisplay() {
		$elementA = array();
		$phpVersion = phpversion();
		if ( version_compare( $phpVersion, '5.0', '>=' ) ) {
			$status = 'success';
		} else {
			$status = 'fail';
		}
		$elementA[] = $this->_addElement( 'p[phpversion]', WText::t('1242282433QMCF'), $phpVersion, $status );
		$sqlVersion = $this->_getMysqlVers();
		if ( version_compare( $sqlVersion, '4.2.1', '>=' ) ) {
			$status = 'success';
		} else {
			$status = 'fail';
		}
		$elementA[] = $this->_addElement( 'p[sqlversion]', WText::t('1317823749MRTP'), $sqlVersion, $status );
		if ( function_exists('gd_info')) $gd = gd_info();
		else $gd = array();
		$desc = WText::t('1432423407JSWF');
		if ( isset($gd["GD Version"]) ) {
			$status = 'success';
			$text = $this->_Yes;
		} else {
			$status = 'warning';
			$text = $this->_No;
		}
		$elementA[] = $this->_addElement( 'p[gd]', WText::t('1432423407JSWG'), $text, $status, $desc );
		if ( version_compare( $phpVersion, '5.3', '>=' ) && class_exists( 'PharData' ) ) {
			$status = 'success';
			$text = $this->_Yes;
		} else {
			$status = 'warning';
			$text = $this->_No;
		}
		$elementA[] = $this->_addElement( 'p[phar]', 'Phar ' . WText::t('1432423407JSWL'), $text, $status, $desc );
		if ( class_exists( 'DOMDocument' ) ) {
			$status = 'success';
			$text = $this->_Yes;
		} else {
			$status = 'warning';
			$text = $this->_No;
		}
		$elementA[] = $this->_addElement( 'p[domdocument]', 'DOMDocument ' . WText::t('1433250455EAMT'), $text, $status, $desc );
		return $elementA;
	}
	private function _phpDisplay() {
		$elementA = array();
		$memory = @ini_get('memory_limit');
		$limit = WTools::returnBytes($memory);
		if ( $memory == 0 || $limit >= WTools::returnBytes('32M') ) {
			$status = 'success';
		} else {
			$status = 'fail';
		}
		$elementA[] = $this->_addElement( 'p[memory_limit]', WText::t('1298350274FGVE'), $memory, $status );
		$memory = @ini_get('max_execution_time');
		if ( $memory == 0 || $memory >= 30 ) {
			$status = 'success';
		} else {
			$status = 'fail';
		}
		if ( empty($memory) ) $memory = WText::t('1206961954EAMU');
		else $memory .= ' ' . WText::t('1360366414NJAJ');
		$elementA[] = $this->_addElement( 'p[maximum_execution_time]', WText::t('1432423407JSWM'), $memory, $status );
		$memory = @ini_get('post_max_size');
		$limit = WTools::returnBytes($memory);
		if ( $memory == 0 || $limit >= WTools::returnBytes('8M') ) {
			$status = 'success';
		} else {
			$status = 'fail';
		}
		$elementA[] = $this->_addElement( 'p[post_max_size]', WText::t('1432423407JSWN'), $memory, $status );
		$memory = @ini_get('max_input_time');
		if ( $memory == 0 || $memory >= 30 ) {
			$status = 'success';
		} else {
			$status = 'fail';
		}
		$memory .= ' ' . WText::t('1360366414NJAJ');
		$elementA[] = $this->_addElement( 'p[max_input_time]', WText::t('1432423407JSWO'), $memory, $status );
		$memory = @ini_get('upload_max_filesize');
		$limit = WTools::returnBytes($memory);
		if ( $memory == 0 || $limit >= WTools::returnBytes('1M') ) {
			$status = 'success';
		} else {
			$status = 'warning';
		}
		$elementA[] = $this->_addElement( 'p[upload_max_filesize]', WText::t('1432423407JSWP'), $memory, $status );
		if ( function_exists( 'mb_strtolower' ) ) {
			$memory = WText::t('1206732397TAXY');
			$status = 'success';
		} else {
			$memory = WText::t('1460394209KOEB');
			$status = 'warning';
		}
		$elementA[] = $this->_addElement( 'p[mbstring]', WText::t('1460394209KOEC'), $memory, $status );	
		if ( extension_loaded( 'imap' ) ) {
			$memory = WText::t('1206732397TAXY');
			$status = 'success';
		} else {
			$memory = WText::t('1460394209KOEB');
			$status = 'warning';
		}
		$elementA[] = $this->_addElement( 'p[imap]', 'IMAP', $memory, $status );
		return $elementA;
	}
	private function _frameworkDisplay() {
		$elementA = array();
		switch( JOOBI_FRAMEWORK_TYPE ) {
			case 'joomla':
				$app = JFactory::getApplication();
				if ( ! $app->getCfg('ftp_enable') ) {
					$status = 'success';
					$value = $this->_Yes;
				} else {
					$status = 'warning';
					$value = $this->_No;
				}
				$elementA[] = $this->_addElement( 'p[ftp_enable]', WText::t('1432423407JSWQ'), $value, $status );
				break;
			case 'wordpress':
				break;
		}
		return $elementA;
	}
	private function _communicationDisplay() {
		$elementA = array();
		$nameServerStatus = WPref::load( 'PLIBRARY_NODE_SERVER_STATUS' );
		if ( $nameServerStatus ) {
			$status = 'success';
			$text = WText::t('1241506531DZAM');
		} else {
			$text = WText::t('1219769904NDIM');
			$status = 'fail';
		}
		$elementA[] = $this->_addElement( 'p[external_communication]', WText::t('1298350446IEXL'), $text, $status );
		$desc = WText::t('1432423407JSWH');
		if ( ini_get( 'allow_url_fopen' ) ) {
			$status = 'success';
			$text = $this->_Yes;
		} else {
			$status = 'fail';
			$text = $this->_No;
		}
		$elementA[] = $this->_addElement( 'p[fopen]', 'allow_url_fopen ' . WText::t('1432423407JSWI'), $text, $status, $desc );
		$hasOne = false;
		$desc = WText::t('1249442156QUCO');
		if ( function_exists('fsockopen') ) {
			$status = 'success';
			$text = $this->_Yes;
			$hasOne = true;
		} else {
			$status = 'warning';
			$text = $this->_No;
		}
		$elementA[] = $this->_addElement( 'p[fsockopen]', 'fsockopen ' . WText::t('1432423407JSWK'), $text, $status, $desc );
		$desc = WText::t('1249442156QUCO');
		if ( function_exists('curl_init') ) {
			$status = 'success';
			$text = $this->_Yes;
			$hasOne = true;
		} else {
			$status = 'warning';
			$text = $this->_No;
		}
		$elementA[] = $this->_addElement( 'p[curl]', 'cURL ' . WText::t('1432423407JSWJ'), $text, $status, $desc );
		if ( ! $hasOne ) {
			$protocol = WText::t('1444070873QVSA');
			$elementA[] = $this->_addElement( 'p[com_protocol]', WText::t('1444070873QVSB'), $protocol, 'fail' );
		}
		$timeOut = WPref::load( 'PLIBRARY_NODE_SERVER_TIMEOUT' );
		if ( $timeOut >= 30 ) {
			$status = 'success';
		} elseif ( $timeOut < 10 ) {
			$status = 'fail';
		} else {
			$status = 'warning';
		}
		$elementA[] = $this->_addElement( 'p[communication_timeout]', WText::t('1444070844HEHJ'), $timeOut, $status );
		if ( $nameServerStatus ) {
			$testString = 'Test Requirements: connection to ' . JOOBI_SITE;
			$netcom = WNetcom::get();
			$netcom->setTimeout( 10 );
			$netcomServerC = WClass::get('netcom.server');
			$myDistribServer = $netcomServerC->checkOnline( true );
			$returnedPing = $netcom->send( $myDistribServer, 'netcom', 'ping', $testString );
			if ( $testString == $returnedPing ) {
				$status = 'success';
				$text = WText::t('1206961996STAF') . ': ' . $myDistribServer;
			} else {
				$text = WText::t('1444070873QVSC') . ': ' . $myDistribServer;
				$status = 'fail';
			}
			$elementA[] = $this->_addElement( 'p[distrib_server]', WText::t('1338573341FLOL'), $text, $status );
		}
		return $elementA;
	}
	private function _sqlDisplay() {
	}
	private function _folderDisplay() {
		$elementA = array();
		$foldersA = array();
		$foldersA[] = '';
		switch( JOOBI_FRAMEWORK_TYPE ) {
			case 'joomla':
				$foldersA[] = 'administrator/components';
				$foldersA[] = 'components';
				$foldersA[] = 'cache';
				$foldersA[] = 'plugins';
				$foldersA[] = 'modules';
				$foldersA[] = 'tmp';
				break;
			case 'wordpress':
				$foldersA[] = trim( str_replace( site_url(), '', plugins_url() ) , '/' );
				break;
		}
		$count=0;
		foreach( $foldersA as $folder ) {
			$path = JOOBI_DS_ROOT . str_replace( '/', DS, $folder );
			$count++;
			if ( @is_writable( $path ) ) {
				$status = 'success';
				$value = WText::t('1242282417SQLA');
			} else {
				$status = 'fail';
				$value = WText::t('1242282416HAQS');
			}
			if ( empty($folder) ) $text = '/';
			else $text = $folder;
			$elementA[] = $this->_addElement( 'p[path' . $count . ']', $text, $value, $status );
		}
		return $elementA;
	}
	private function _paneName($pane) {
		switch( $pane ) {
			case 'server':
				return WText::t('1432423407JSWR');
				break;
			case 'php':
				return WText::t('1432423407JSWS');
				break;
			case 'sql':
				return WText::t('1432423407JSWT');
				break;
			case 'folder':
				return WText::t('1432423407JSWU');
				break;
			case 'communication':
				return WText::t('1255339744MGTH');
				break;
			case 'framework':
				switch( JOOBI_FRAMEWORK_TYPE ) {
					case 'joomla':
						return 'Joomla';
						break;
					case 'wordpress':
						return 'WordPress';
						break;
				}
				break;
			default:
				return 'Requirements Not Defined';
				break;
		}
	}
	private function _getMysqlVers() {
		if( $connect = @mysqli_connect( JOOBI_DB_HOSTNAME, JOOBI_DB_USER, JOOBI_DB_PASS) ) {
			$versA = array( mysqli_get_server_info( $connect ), mysqli_get_client_info() );
		} else {
			if( $connect = @mysql_connect( JOOBI_DB_HOSTNAME, JOOBI_DB_USER, JOOBI_DB_PASS) ) {
				$versA = array( mysql_get_server_info($connect), mysql_get_client_info() );
			} else {
				$versA = false;
			}
		}
		if ( !empty( $versA[0] ) ) return $versA[0];
		else return false;
	}
}
