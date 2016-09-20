<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_Sanitize_class extends WClasses {
	public static $optmImage = false;
	private $_fileName = '';
	private $_blockPage = true;
	private static $doneA = array();
	public function validateFile($fileName,$fileType,$fileTmpPath,$blockPage=true) {	
				$this->_fileName = $fileName;
		$this->_blockPage = $blockPage;
		if ( empty($this->_fileName) || empty( $fileType ) || empty($fileTmpPath) ) return false;
				if ( !empty(self::$doneA[$fileTmpPath]) ) return true;
		self::$doneA[$fileTmpPath] = true;
				if ( strstr( $this->_fileName, "\u0000" ) ) {
			return $this->_corruptFile();
		}
				$nameA = explode( '.', strtolower( str_replace( array( '(', ')' ), '', $this->_fileName ) ) );
		$nameA = array_reverse( $nameA );
		$count = count( $nameA );
				if ( $count > 1 ) {				$phpNic = substr( $nameA[0], 3 );
			if ( 'php' == substr( $nameA[0], 0, 3 ) && ( empty($phpNic) || is_numeric($phpNic) ) ) {
				return $this->_corruptFile();
			}		}
				if ( $count > 2 ) {				$phpNic = substr( $nameA[1], 3 );
			if ( 'php' == substr( $nameA[1], 0, 3 ) && ( empty($phpNic) || is_numeric($phpNic) ) ) {
				return $this->_corruptFile();
			}		}		
				if ( $count > 3 ) {				$phpNic = substr( $nameA[2], 3 );
			if ( 'php' == substr( $nameA[2], 0, 3 ) && ( empty($phpNic) || is_numeric($phpNic) ) ) {
				return $this->_corruptFile();
			}		}		
		if ( $count > 4 ) {				$phpNic = substr( $nameA[3], 3 );
			if ( 'php' == substr( $nameA[3], 0, 3 ) && ( empty($phpNic) || is_numeric($phpNic) ) ) {
				return $this->_corruptFile();
			}		}		
				$fp = @fopen( $fileTmpPath, 'r' );
		if ( false != $fp ) {
						$data = '';
			$shortTagExtA = array( 'inc', 'phps', 'class', 'php3', 'php4', 'txt', 'dat',  'tpl', 'tmpl' );
			$fileType = strtolower( $fileType );
			$hasShortTag = in_array( $fileType, $shortTagExtA );
						while ( ! feof($fp) ) {
								$tmp = @fread( $fp, 131072 );
				$data .= $tmp;
								if ( stristr( $tmp, '<?php') ) return $this->_corruptFile();
								if ( $hasShortTag ) {
										if ( strstr($tmp, '<?') ) return $this->_corruptFile();
				}		
								$data = substr( $data, -4 );
																																																			}		
			fclose( $fp );
		}		
				if ( WPref::load( 'PSECURITY_NODE_OPTIMIZEIMAGE' ) && in_array( $fileType, array( 'png', 'jpg', 'gif', 'jpeg' ) ) ) {
			$filesResizeC = WClass::get( 'images.resize' );
			self::$optmImage = true;				if ( ! $filesResizeC->optimzeImg( $fileTmpPath ) ) {
				return $this->_corruptFile();
			}		}		
		return true;
	}
	private function _corruptFile() {
		if ( WExtension::exist( 'security.node' ) ) {
			$CAUSE = WText::t('1457643304PHYB');
			$details = 'A corrupt file was blocked:<br>File name: ' . $this->_fileName;
			$securityReportC = WClass::get( 'security.report' );
			$securityReportC->setIP();
			$securityReportC->blockPage( 'shieldfile', $CAUSE, $details, false, 0, $this->_blockPage );			
		}
		return false;
	}	
}