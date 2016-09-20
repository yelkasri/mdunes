<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Joomla_Easysocialwall_class extends WClasses {
	public function checkInstalled($post=null) {
		static $status = null;
		if ( !isset($status) ) {
			$jomSocial = WApplication::isEnabled( 'easysocial', true );
			if ( $jomSocial ) {
								$status = @require_once( JPATH_ROOT. DS . 'administrator' . DS . 'components' . DS . 'com_easysocial' . DS . 'includes' . DS . 'foundry.php' );
			} else {
				$status = false;
			}
		}
		return $status;
	}
	public function postWall($post=null) {
		$title = '';
		if ( empty($post->verb) ) $post->verb = 'create';
		if ( empty($post->eid) ) $post->eid = 1;
		$content = ( !empty($post->content) ? $post->content : ( !empty($post->description) ? $post->description : '' ) );
		if ( !empty( $post->image ) ) $content = $post->image . $content;
		$context = ( !empty($post->context) ? $post->context : ( !empty($post->model) ? $post->model : 'catalog' ) );
		if ( empty($post->uid) ) $post->uid = WUser::get('uid');
				$my = Foundry::user();
		$USERNAME = WUser::get( 'username' );
		$replaceA = array();
		$replaceA['{tag:actor}'] = $USERNAME;
		$replaceA['{multiple} {count} times{/multiple}'] = '';
		$replaceKeyA = array_keys( $replaceA );
		$content = str_replace( $replaceKeyA, $replaceA, $content );
		$post->title = str_replace( $replaceKeyA, $replaceA, $post->title );
				$stream	 = Foundry::stream();
				$template	= $stream->getTemplate();
						$template->setActor( $my->id, 'user' );
						$template->setContext( $post->eid, $context );
								$template->setVerb( $post->verb );
										$template->setType( 'full' );
								$template->setTitle( $post->title );
								$template->setContent( $content, 'catalog' );
				if ( isset($post->content) ) unset( $post->content );
		if ( isset($post->description) ) unset( $post->description );
		$template->setParams( $post );
				$stream->add( $template );
	}
}