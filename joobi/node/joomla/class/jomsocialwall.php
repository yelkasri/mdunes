<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Joomla_Jomsocialwall_class extends WClasses {
	public function checkInstalled($post=null) {
		static $status = null;
		if ( !isset($status) ) {
			$jomSocial = WApplication::isEnabled( 'community', true );
			if ( $jomSocial && file_exists( JPATH_ROOT. DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' ) ) {
				$status = include_once( JPATH_ROOT. DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
			} else {
				$status = false;
			}
		}
		return $status;
	}
	public function postWall($post=null) {
		$act = new stdClass;
				$my = JFactory::getUser();
		$act->cmd = $post->callingFunction;
		$act->actor = $my->id;
		$act->target = 0; 		$act->title = JText::_('{actor} write on wall.');
		$act->content = ( !empty($post->image) ? $post->image : '' );
		$act->content .= ( !empty($post->content) ? $post->content : ( !empty($post->description) ? $post->description : '' ) );
		$act->app = 'wall';
		$act->cid = 0;
		if ( !empty($post) ) {
			foreach( $post as $prop => $value ) {
				if ( $prop == 'title' ) $value = JText::_( str_replace( '{tag:actor}', '{actor}', $value ) );
				$act->$prop = $value;
			}		}
		CFactory::load('libraries', 'activities');
		CActivityStream::add( $act );
	}
}