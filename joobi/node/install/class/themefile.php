<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Install_Themefile_class extends WClasses {
 	public function addThemeFile($type,$filesA) {
	 		$themeM = WModel::get( 'theme' );
	 		$themeM->whereE( 'type', $type );
	 		$themeM->orderBy( 'tmid', 'ASC' );
	 		$this->_allThemeA = $themeM->load( 'ol', array( 'tmid', 'namekey', 'premium', 'core', 'folder' ) );
 		if ( empty($this->_allThemeA) ) return false;
 		$coreThemeO = null;
 		$hasClone = false;
 		foreach( $this->_allThemeA as $oneTheme ) {
 			if ( !empty($oneTheme->core) ) {
 				if ( empty($coreThemeO) ) $coreThemeO = $oneTheme;
 			} else {
 				$hasClone = true;
 			}
 		}
 		if ( empty($coreThemeO) || empty($hasClone) ) return false;
 		$fileS = WGet::file();
 		$folderS = WGet::folder();
		foreach( $this->_allThemeA as $oneTheme ) {
			if ( !empty($oneTheme->core) ) continue;
			$basePath = JOOBI_DS_THEME;
			$namekey = $coreThemeO->namekey;
			$namekeyA = explode( '.', $namekey );
			array_pop( $namekeyA );
			$location = array_pop( $namekeyA );
			$original = array_pop( $namekeyA );
			$baseOrignal = $basePath . $location . DS . $original . DS;
			$baseDestination = $basePath . $location . DS . $oneTheme->folder . DS;
			foreach( $filesA as $oneFile ) {
				$oneFile = str_replace( '/', DS, $oneFile );
				if ( strpos( $oneFile, '.' ) === false ) {
					if ( $folderS->exist( $baseOrignal . $oneFile ) ) {
						$folderS->copy( $baseOrignal . $oneFile, $baseDestination . $oneFile, true );
					}
				} else {
					if ( $fileS->exist( $baseOrignal . $oneFile ) ) {
						$fileS->copy( $baseOrignal . $oneFile, $baseDestination . $oneFile, true );
					}
				}
			}
		}
 	}
 	public function updateThemeFile($type,$filesA) {
 		$themeM = WModel::get( 'theme' );
 		$themeM->whereE( 'type', $type );
 		$themeM->orderBy( 'tmid', 'ASC' );
 		$this->_allThemeA = $themeM->load( 'ol', array( 'tmid', 'namekey', 'premium', 'core', 'folder' ) );
 		if ( empty($this->_allThemeA) ) return false;
 		$coreThemeO = null;
 		$hasClone = false;
 		foreach( $this->_allThemeA as $oneTheme ) {
 			if ( !empty($oneTheme->core) ) {
 				if ( empty($coreThemeO) ) $coreThemeO = $oneTheme;
 			} else {
 				$hasClone = true;
 			}
 		}
 		if ( empty($coreThemeO) || empty($hasClone) ) return false;
 		$fileS = WGet::file();
 		foreach( $this->_allThemeA as $oneTheme ) {
 			if ( !empty($oneTheme->core) ) continue;
 			$basePath = JOOBI_DS_THEME;
 			$namekey = $coreThemeO->namekey;
 			$namekeyA = explode( '.', $namekey );
 			array_pop( $namekeyA );
 			$location = array_pop( $namekeyA );
 			$original = array_pop( $namekeyA );
 			$baseOrignal = $basePath . $location . DS . $original . DS;
 			$baseDestination = $basePath . $location . DS . $oneTheme->folder . DS;
 			foreach( $filesA as $oneFile ) {
 				$oneFile = str_replace( '/', DS, $oneFile );
 				if ( $fileS->exist( $baseOrignal . $oneFile ) ) {
 					if ( $fileS->exist( $baseDestination . $oneFile ) ) {
 						$extensionA = explode( '.', $oneFile );
 						$ext = array_pop( $extensionA );
 						$file = implode( '.', $extensionA );
 						$newName = $file . '.' . time() . '.bak.' . $ext;
 						$fileS->move( $baseDestination . $oneFile, $baseDestination . $newName, true );
 					}
 					$fileS->copy( $baseOrignal . $oneFile, $baseDestination . $oneFile, true );
 				}
 			}
 		}
 	}
}
