<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Joomla_Module_tag {
	function process($object) {
		$tags = array();
		foreach( $object as $tag => $arguments ) {
			if ( empty($arguments->id) ) {
				$message = WMessage::get();
				$message->userW('1380142885PHVI');
				$message->userW('1380142885PHVJ');
				continue;
			}
			$object[$tag]->id = (int) $arguments->id;
			$this->_modules[] = $object[$tag]->id;
		}
		$tags = $this->_replaceModuleJoomla( $object, $parameters );
		return $tags;
	}
	private function _replaceModuleJoomla($object,$parameters) {
		$tags = array();
		$message = WMessage::get();
		if ( empty($this->_modules) ) return $tags;
		$model=WModel::get('joomla.modules');
		$model->whereIn('id',$this->_modules);
		$myModules = $model->load( 'ol', array( 'id', 'title','module', 'position', 'content', 'showtitle', 'params') );
		foreach($myModules as $module){
			$module->user  	= substr( $module->module, 0, 4 ) == 'mod_' ?  0 : 1;
			$module->name = $module->user ? $module->title : substr( $module->module, 4 );
			$module->style = null;
			$module->module = preg_replace('/[^A-Z0-9_\.-]/i', '', $module->module);
			switch (JOOBI_FRAMEWORK) {
				case 'joomla30':
					$allModules[$module->id] = $this->_showModuleJoom15($module);
					break;
				default :
					$message->codeE( 'This tag system is still not developed for the CMS ' . JOOBI_FRAMEWORK );
			}
		}
		foreach( $object as $tag => $myTagO ) {
			if (isset($allModules[$myTagO->id])) $tags[$tag]->wdgtContent = $allModules[$myTagO->id];
			else{
				$ID = $myTagO->id;
				$tags[$tag]->wdgtContent = '';
				$message->userW('1380142885PHVK',array('$ID'=>$ID));
			}
		}
		return $tags;
	}
	function _showModuleJoom15($module) {
		$config = JFactory::getConfig();
		$secret = $config->get('config.secret');
		$URLModule = WPage::routeURL( 'controller=joomla&task=rendermodule&id='. $module->id . '&protect=' . time() . '&code='.$secret, 'home', 'popup', false. false );
		if ( strtolower($module->module) == 'mod_custom' ) {
			$done = false;
		} else {
			@ini_set( 'default_socket_timeout', 10 );
			@ini_set( 'user_agent', 'My-Application/2.5' );
			@ini_set( 'allow_url_fopen', 1 );
				$PATH = JPATH_ROOT. DS . 'modules' . DS .$module->module. DS .$module->module.'.php';
				if ( !file_exists($PATH ) ) {
					echo 'The system could not load the file '.$PATH.'<br>' .
							'<br>Make sure the module is not uninstalled!';
							'<br><br>Only Frontend Modules can be loaded based on your settings.<br>';
					return '';
				}
			$loadmethod = 'fileget';
			$done = false;
			if ( $loadmethod == 'fileget' || $loadmethod == 'filegetcontent' ) {
				if ( ini_get('allow_url_fopen') ) {
					$module->content = file_get_contents( $URLModule );
					$done = true;
				}
			}
			if ( !$done && $loadmethod == 'curl' ){
				if ( function_exists('curl_init') ) {
					$CURL = curl_init();
					curl_setopt( $CURL, CURLOPT_URL,$URLModule );
					curl_setopt( $CURL, CURLOPT_FAILONERROR, 1 );
					curl_setopt( $CURL, CURLOPT_RETURNTRANSFER, 1 );
					curl_setopt( $CURL, CURLOPT_TIMEOUT, 10) ;
					$module->content = curl_exec($CURL);
					curl_close( $CURL );
					$done = true;
				}
			}
		}
		if ( !$done ) {
			$lang =& JFactory::getLanguage();
			$lang->load( $module->module );
			$module->content= JModuleHelper::renderModule( $module, $module->params );
		}
		$LiveURL = str_replace( JOOBI_SITE, '', $URLModule );
		$module->content = str_replace( array($LiveURL,str_replace('&','&amp;',$LiveURL) ), 'index.php', $module->content );
		$module->content = preg_replace( "#(onclick|onfocus|onload|onblur) *= *\"(?:(?!\").)*\"#iU" , '',$module->content );
		$module->content =  preg_replace( "#< *script(?:(?!< */ *script *>).)*< */ *script *>#isU" , '', $module->content );
		return $module->content;
	}
}
