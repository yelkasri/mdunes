<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_Analyze_class extends WClasses {
	public function getTypeAndName($URL,&$type) {
				if ( strpos( $URL, 'youtube.com' ) ) {
			$type = 'youtube';
		} elseif ( strpos( $URL, 'vimeo.com' ) ) {
			$type = 'vimeo';
		} elseif ( strpos( $URL, 'livevideo.com' ) ) {
			$type = 'livevideo';
		} else {
			$message = WMessage::get();
			$message->adminW( 'Type of media not yet supported: ' . $URL );
		}
		return $this->_cleanURL( $type, $URL );
	}
	public function cleanURL(&$fileObject) {
		$fileObject->_name = $this->_cleanURL( $fileObject->_type, $fileObject->_name );
	}
	private function _cleanURL($type,$name) {
		switch ( $type ) {
			case 'youtube':
								$pos = strpos( $name, 'v=' );
				if ( $pos !== false ) {
					$newURL = substr( $name, $pos+2 );
					$newURL2 = explode( '&', $newURL );
					$name = $newURL2[0];
				}				break;
			case 'vimeo':
				http://vimeo.com/channels/staffpicks/107363044
				$vExA = explode( '/', $name );
				$name = array_pop( $vExA );
				break;
			case 'livevideo':
				$pos = strpos( $name, 'livevideo.com/video' );
				if ( $pos !== false ) {
					$newURL = substr( $name, $pos+20 );
					$newURL2 = explode( '/', $newURL );
					$name = $newURL2[0];
				}				break;
			case 'yahoovideo':
				break;
			case 'espn':
				break;
			case 'myvideo':
				break;
			case 'gametrailers':
				break;
			case 'myspace':
				break;
			case 'url':
				break;
			default:					break;
		}
		return $name;
	}
}