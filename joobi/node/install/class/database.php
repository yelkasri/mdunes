<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Install_Database_class extends WQuery {
	var $_removeFile = false;
	var $types = array( 'table', 'real' );
	var $basedir = '';
	public $extension_namekey = null;
	public function setResaveItemMoveFile($remove=true){
		$this->_removeFile = $remove;
	}
	function getResaveItemMoveFile() {
		return $this->_removeFile;
	}
	public function import($file,$basedir='',$compat=false,$update=null) {
		try {
			$this->_displayStartingMessage();
			$this->basedir = rtrim( $basedir, DS ) . DS;
			if ( $basedir === true ) {
				$query = trim( $file );
				if ( !empty($query) && substr( $query, 0, 1 ) != '#' ) {
					$sqlInstanceQ = WQuery::getDBConnector( WGet::DBType() );	
					$sqlInstanceQ->setQuery( $query );
					$sqlInstanceQ->query();
					if ( $sqlInstanceQ->getErrorNum() ) $status = false;
				}
			} else {	
				$status = true;
				if ( is_array( $file ) ) {
					if ( in_array( $compat, $this->types ) ) {
						$tmp = array();
						foreach( $file as $f ) {
							$parts = explode( '+', $f );
							switch( $compat ) {
								case 'table':
									$need = 3;
									break;
								case 'real':
									$need = 2;
									break;
							}
							if ( count( $parts ) != $need ) {
								continue;
							}
							$version = array_shift( $parts );
							if ( $compat == 'table' )  $order = array_shift( $parts );
							if ( !isset( $tmp[$version] ) ) $tmp[$version] = array();
							switch( $compat ) {
								case 'table':
									$tmp[$version][$order] = $f;
									break;
								case 'real':
									$tmp[$version][] = $f;
									break;
							}
						}
						static $sorter = null;
						if ($sorter == null) $sorter = WClass::get( 'library.sql' );
						uksort( $tmp, array( $sorter, 'sorter' ) );
						$file = array();
						foreach( $tmp as $f ) {
							if ( $compat == 'table' ) ksort($f);
							foreach($f as $v)
								$file[]=$v;
						}
					}
					if ( $compat=='layout' ) sort( $file );
					foreach( $file as $single ) {
						if ( $compat == 'layout' ) {
							if ( $update == 2 ) {
								$array = explode( '.', $single );
								array_pop( $array );
								array_pop( $array );
								array_pop( $array );
								$you = array_pop( $array );
								if ( $you == 'you' ) {
									continue;
								}
							}
						}
						if ( ! $this->_checkAndLoadFile( $single, $compat ) ) $status = false;
					}
					if ( $this->getResaveItemMoveFile() ) {
						$systemFolderC = WGet::folder();
						$systemFolderC->delete( $this->basedir );
					}
				}
			}
			return $status;
		} catch (Exception $e) {
			WMessage::log( $e->getMessage(), 'Install_Database_class_import' );
			WMessage::log( $e->getTraceAsString(), 'Install_Database_class_import' );
			return true;
		}
	}
	function checkFile($file,$compat) {
		static $methods = array();
		if ( !isset( $methods[$compat] ) ){
			$methods[$compat] = false;
			$name = 'check' . ucfirst( $compat );
			if ( method_exists( $this, $name ) ){
				$methods[$compat] = $name;
			}
		}
		if ( $methods[$compat] ){
			$name = $methods[$compat];
			return $this->$name( $file );
		}
		return true;
	}
	function checkConfig(){
		static $exists = null;
		if ( !isset( $exists ) ){
			$exists = false;
			$sql = WTable::get( 'dataset_node' );
			if ( $sql->tableExist() ){
				$exists = true;
			}
		}
		return $exists;
	}
	public function importFile($file,$split=true) {
		try {
			$filehandler = WGet::file();
			$data = $filehandler->read( $file );
			if ( empty($data) ) return false;
			$handler = WClass::get( 'library.sql' );
			$this->_importQueries( $handler->splitSql( $data, ';', null, false ) );
			if ( $this->getResaveItemMoveFile() ) {
				$filehandler->delete( $file );
			}
		} catch (Exception $e) {
			WMessage::log( $e->getMessage(), 'Install_Database_class_importfile' );
			WMessage::log( $e->getTraceAsString(), 'Install_Database_class_importfile' );
			return true;
		}
		return true;
	}
	private function _importQueries($array) {
		$status=true;
		if ( empty($array) ) return true;
		try {
			if ( !is_array( $array ) ) $array = array( $array );
			if ( count($array) > 0 ) {
				foreach( $array as $query ) {
					$query = trim($query);
					if ( !empty($query) ) {
						if ( '/* CONDITION' == substr( $query, 0, 12 ) ) {
							$exQueryA = explode( ' */', substr( $query, 3 ) );
							$conditionA = explode( '||', $exQueryA[0] );
							if ( 'CONDITIONCOLUMN' == $conditionA[0] ) {
								$libraryModelM = WTable::get( $conditionA[1] );
								if ( !empty( $libraryModelM ) ) {
									$columnsA = $libraryModelM->showColumns();
								} else {
									$columnsA = array();
								}
								if ( isset( $columnsA[$conditionA[2]] ) ) {
									continue;
								} else {
									$query = $exQueryA[1];
								}
							} elseif ( 'CONDITIONINDEX' == $conditionA[0] ) {
								$libraryModelM = WTable::get( $conditionA[1] );
								if ( !empty( $libraryModelM ) ) {
									$indexO = $libraryModelM->showIndexes();
								} else {
									$indexO = null;
								}
								if ( is_array($indexO->index) && array_key_exists( $conditionA[2], $indexO->index ) ) {
									continue;
								} else {
									$query = $exQueryA[1];
								}
							} elseif ( 'CONDITIONTABLE' == $conditionA[0] ) {
								$allTablesA = explode( ',', $conditionA[1] );
								if ( !empty($allTablesA) ) {
									$tableExist = true;
									foreach( $allTablesA as $oTable ) {
										$libraryModelM = WTable::get( $oTable );
										if ( empty( $libraryModelM ) || ! $libraryModelM->tableExist() ) {
											$tableExist = false;
										}
									}
									if ( ! $tableExist ) continue;
								} else {
									continue;
								}
							} else {
								continue;
							}
						}
						$query = preg_replace( '/#database_[0-9]{0,4}#/', $this->getDBName(), $query );
						$error = $this->load( 'q', $query );
						$codeError = $this->getErrorNum();
						if ( ! $error && !empty( $codeError ) ) {
							$status = false;
						}
					}
				}
			}
			if ( $status ) $this->setResaveItemMoveFile( true );
			return $status;
		} catch (Exception $e) {
			WMessage::log( $e->getMessage(), 'Install_Database_class_importQueries' );
			WMessage::log( $e->getTraceAsString(), 'Install_Database_class_importQueries' );
			return true;
		}
	}
	private function _displayStartingMessage() {
		if (isset( $this->extension_namekey ) ) {
			static $mess = false;
			if ( !$mess ){
				$mess = true;
			}
		}
	}
	private function _checkAndLoadFile($file,$compat) {
		if ( $this->checkFile( $file, $compat ) ) {
			return $this->importFile( $this->basedir . $file, false );
		}
		return true;
	}
}