<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Japps_Japps_main_view extends Output_Mlinks_class {
	function prepareQuery() {
		$freshInstall = WPref::load( 'PAPPS_NODE_FIRSTREFRESH' );
		if ( empty($freshInstall) ) {
			$refresh = WClass::get( 'apps.refresh' );
			$refresh->firstRefresh();
		}
		return true;
	}
	function prepareView() {
		if ( ! WExtension::exist('email.node') ) {
			$this->removeElements( 'japps_main_email' );
			$this->removeElements( 'japps_main_design' );
		}
		if ( ! WExtension::exist('theme.node') ) {
			$this->removeElements( 'japps_main_theme' );
		}
		if ( ! WExtension::exist('scheduler.node') ) {
			$this->removeElements( 'japps_main_scheduler' );
		}
		if ( ! WExtension::exist('events.node') ) {
			$this->removeElements( 'japps_main_events' );
		}
		if ( ! WExtension::exist('main.node') ) {
			$this->removeElements( 'japps_main_views' );
			$this->removeElements( 'japps_main_credentials' );
			$this->removeElements( 'japps_main_users' );
		}
		$usecms = WPref::load( 'PEMAIL_NODE_USECMS' );
		if ( $usecms != 9 ) $this->removeElements( 'japps_main_email_mailer' );
		if ( ! WExtension::exist('design.node') ) {
			$this->removeElements( 'japps_main_models' );
			$this->removeElements( 'japps_main_picklist' );
		}
		return true;
	}
}