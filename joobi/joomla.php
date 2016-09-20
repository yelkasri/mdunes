<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WFramework_Load_Config {
	public function loadConfig() {
		require( JOOBI_DS_ROOT . JOOBI_FRAMEWORK_CONFIG_FILE );
		$joomlaConfig = new JConfig;
		$liveSite = $joomlaConfig->live_site;
		if ( trim($liveSite) == '' ) {
								if ( isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off') ) {
					$https = 's://';
				} else {
					$https = '://';
				}
				if ( !empty($_SERVER['PHP_SELF']) && !empty($_SERVER['REQUEST_URI']) ) {
															$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				} else {
					$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
										if ( isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ) {
						$theURI .= '?' . $_SERVER['QUERY_STRING'];
					}				}
								$liveSite = str_replace( array( "'", '"', '<', '>' ), array("%27", "%22", "%3C", "%3E" ), $theURI );
		} else {
			$theURI = rtrim( $liveSite, "/" );
			if ( !empty($_SERVER['REQUEST_URI']) ) $theURI .= $_SERVER['REQUEST_URI'];
			else {
				if ( !empty($_SERVER['SCRIPT_NAME']) ) $theURI .= $_SERVER['SCRIPT_NAME'];
				else {
					if ( !empty($_SERVER['PHP_SELF']) ) $theURI .= $_SERVER['PHP_SELF'];
				}			}			$liveSite = str_replace( array( "'", '"', '<', '>' ), array("%27", "%22", "%3C", "%3E" ), $theURI );
		}
		$urlA = parse_url( $liveSite );
		$folderPos = strpos( $urlA['path'], JOOBI_FOLDER );
		if ( false === $folderPos ) {
						$pathExplodeA = explode( '/', $urlA['path'] );
			define( 'JOOBI_INDEX', array_pop($pathExplodeA) );
			define( 'JOOBI_SITE_PATH', implode( '/', $pathExplodeA ) . '/' );
			define( 'JOOBI_SITE' , $urlA['scheme'] . '://' . $urlA['host'] . JOOBI_SITE_PATH );
		} else {
			define( 'JOOBI_SITE_PATH', substr( $urlA['path'], 0, $folderPos ) . JOOBI_FOLDER . '/' );
			define( 'JOOBI_INDEX', substr( $urlA['path'], $folderPos + strlen( JOOBI_FOLDER ) + 1 ) );
			define( 'JOOBI_SITE' , $urlA['scheme'] . '://' . $urlA['host'] . str_replace( JOOBI_FOLDER . '/' . JOOBI_INDEX, '', $urlA['path'] ) );
		}
		define( 'JOOBI_INDEX2', JOOBI_INDEX );
		if ( !defined('IS_ADMIN') ) define( 'IS_ADMIN', 0 );
				if ( !defined('URL_NO_FRAMEWORK') ) define( 'URL_NO_FRAMEWORK', '&tmpl=component' );
				define( 'JOOBI_DEBUGCMS' , $joomlaConfig->debug );
		if ( !defined('JOOBI_DB_TYPE') ) define( 'JOOBI_DB_TYPE', 'mysqli' );			define( 'JOOBI_DB_PREFIX', $joomlaConfig->dbprefix );
		define( 'JOOBI_DB_NAME', $joomlaConfig->db );
		define( 'JOOBI_DB_HOSTNAME', $joomlaConfig->host );
		define( 'JOOBI_DB_USER', $joomlaConfig->user );
		define( 'JOOBI_DB_PASS', $joomlaConfig->password );
		define( 'JOOBI_LIST_LIMIT', $joomlaConfig->list_limit );
				define('JOOBI_FORM_NAME', 'adminForm' );
				if ( !defined('JOOBI_SESSION_LIFETIME') ) define( 'JOOBI_SESSION_LIFETIME', $joomlaConfig->lifetime );
		if ( !defined('_JEXEC') ) define('_JEXEC', true );
	}
}