<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Install_Theme_class {
	public function installTheme($xmlData) {
				$extensionObjHome = $this->_convertXML2Object( $xmlData['children'][0] );
		$extensionObj = $extensionObjHome->extension;
		$themeTypeT = WType::get( 'theme.typefolder' );
				$allTuypesA = $themeTypeT->allNames();
		if ( !in_array( $extensionObj->type, $allTuypesA) ) {
			$message = WMessage::get();
			$message->userE('1298350310EWVG');
			return false;
		}
		$publish = isset( $extensionObj->publish ) ? $extensionObj->publish : 1;
		$core = isset( $extensionObj->core ) ? $extensionObj->core : 0;
		$type = $themeTypeT->getValue( $extensionObj->type );
		$parent = isset( $extensionObj->parent ) ? WExtension::get( $extensionObj->parent, 'wid' ) : 0;
		if ( !empty($extensionObj->namekey) ) {
			$namekey = $extensionObj->namekey;
		} else {
						if ( $type==49 ) {					$parentNameA = explode( '.',  $extensionObj->parent );
				$namekey = $parentNameA[0] . '.' . $extensionObj->folder; 			} else {
				$namekey = $extensionObj->folder . '.' . $extensionObj->type;
			}		}
		$appsM = WModel::get( 'install.apps' );
				$appsM->whereE( 'namekey', $namekey . '.' . 'theme' );
		$existWID = $appsM->load('lr', 'wid');
		if ( !empty($existWID) ) $appsM->wid = $existWID;
		$appsM->namekey = $namekey . '.' . 'theme';
		$appsM->parent = $parent;
		$appsM->folder = $extensionObj->folder;
		$appsM->name = $extensionObj->name;
		$appsM->version = $extensionObj->version;
		$appsM->lversion = $extensionObj->version;
		$appsM->type = 77;
		$appsM->core = $core;
		$appsM->publish = $publish;
		if ( !empty($extensionObj->trans->description) ) {
			$lgid = WLanguage::get( $extensionObj->trans->language, 'lgid' );
			if ( empty($lgid) ) $lgid = 1;
			$appsM->setChild( 'appstrans', 'lgid', $lgid );
			$appsM->setChild( 'appstrans', 'description', $extensionObj->trans->description );
		}
		if ( !empty($extensionObj->info->author) ) $appsM->setChild( 'apps.info', 'author', $extensionObj->info->author );
		if ( !empty($extensionObj->info->url) ) $appsM->setChild( 'apps.info', 'homeurl', $extensionObj->info->url );
		$appsM->returnId();
		$status = $appsM->save();
		if ( $status && empty($existWID) ) {	
						$themeM = WModel::get('theme');
						$themeM->type = $type;
			$themeM->namekey = $namekey;
			$themeM->alias = $extensionObj->name;
			$themeM->folder = $extensionObj->folder;
			$themeM->setChild( 'themetrans', 'lgid', $lgid );
			$themeM->setChild( 'themetrans', 'name', $extensionObj->name );
			$themeM->setChild( 'themetrans', 'description', $extensionObj->trans->description );
			$themeM->wid = $appsM->wid;
			$themeM->publish = $publish;
			$themeM->core = $core;
			$themeM->save();
		} else {
			if ( $status ) {
				$message = WMessage::get();
				$message->userS('1298350310EWVH');
			} else {
				$message = WMessage::get();
				$message->userE('1298350310EWVI');
				return false;
			}		}
				return $extensionObj->type . DS . $extensionObj->folder;
	}
	private function _convertXML2Object($extensionA) {
		$obj = new stdClass;
		$property = '';
		foreach( $extensionA as $key => $value ) {
			if ( is_numeric($key) ) {
				$prop = $value['nodename'];
				$children = !empty($value['children']) ? $value['children'] : '';
				if ( !empty($children) ) $obj->$prop = $this->_convertXML2Object( $children );
				else $obj->$prop = $value['nodevalue'];
			} elseif ( $key=='nodename') $property = $value;
			elseif ( $key=='nodevalue' && !empty($property) ) {
				$obj->$property = $value;
			} elseif ( $key=='children' && !empty($property) ) {
				$obj->$property = $this->_convertXML2Object( $value );
			}
		}
		return $obj;
	}
}