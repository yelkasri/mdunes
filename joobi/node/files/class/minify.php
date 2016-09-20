<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_Minify_class extends WClasses {
	public function compressFileCSS($path) {
		return $this->_compressFile( $path, 'CSS' );
	}
	public function compressFileJS($path) {
		return $this->_compressFile( $path, 'JS' );
	}
	private function _compressFile($path,$type) {
				$endOfFile = '.min.' . strtolower($type);
		$lg = strlen( $path ) - strlen( $endOfFile );
		if ( substr( $path, $lg ) == $endOfFile ) {
			return false;
		}
		$fileS = WGet::file();
		$content = $fileS->read( $path );
		if ( empty($content) ) {
			$this->userE('1395437800NHYK');
			return false;
		}
		$functionName = '_minify' . $type;
		$result = $this->$functionName( $content, $path );
		if ( !empty($result) ) {
			$this->_saveFile( $path, $result );
		} else {
			return false;
		}
	}
	private function _minifyCSS($content,$path) {
				$url = 'http://cssminifier.com/raw';
		$netcomRestC = WClass::get( 'netcom.rest', null, 'class', false );
		$data = array( 'input' => $content );
		$minified = $netcomRestC->send( $url, $data );
		return $minified;
	}
	private function _minifyJS($content,$path) {
				$url = 'http://javascript-minifier.com/raw';
		$netcomRestC = WClass::get( 'netcom.rest', null, 'class', false );
		$data = array( 'input' => $content );
		$minified = $netcomRestC->send( $url, $data );
		if ( substr( $minified, 0, 9 ) == "// Error:" ) {
			$FINENAME = $path;
			$this->userE('1395437800NHYL',array('$FINENAME'=>$FINENAME));
			$this->userE( $minified );
						$minified = $content;
		}
		return $minified;
	}
	private function _saveFile($path,$result) {
				$piecesA = explode( '.', $path );
		$extension = array_pop( $piecesA );
		$newFile = implode( '.', $piecesA );
		$newFile .= '.min.' . $extension;
		$fileS = WGet::file();
		return $fileS->write( $newFile, $result );
	}
}