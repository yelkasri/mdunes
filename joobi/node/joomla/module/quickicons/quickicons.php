<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Joomla_CoreQuickicons_module extends WModule {
	public function create() {
		$this->createNew();
	}
	public function createNew() {
		WPage::addCSSFile( 'css/menudashboard.css' );
		if ( JOOBI_FRAMEWORK == 'joomla30' ) WPage::addCSSFile( 'css/joomla30.css' );
				$appsM = WModel::get( 'apps' );
		$appsM->remember( 'QuickMenuIcons', true, 'Views' );
		$appsM->whereE( 'publish', 1 );
		$appsM->remember( 'available_apps' );
		$appsM->whereE( 'type', 1 );
		$appsM->orderBy( 'name' );
		$allAppsA = $appsM->load( 'ol', array( 'wid', 'name', 'namekey' ) );
		if ( empty($allAppsA) ) return '';
		$html2 = '<div id="cpanel"><div class="clearfix">';
		foreach( $allAppsA as $oneApp ) {
			if ( JOOBI_MAIN_APP . '.application' == $oneApp->namekey && ! WApplication::isEnabled( JOOBI_MAIN_APP ) ) {
				continue;
			}
			$controllerA = explode( '.', $oneApp->namekey );
			$link = 'index.php?option=com_' . $controllerA[0] . '&controller=' . $controllerA[0] . '';
			$html2 .= '<div style="float:left;">
<div class="icon">
<a href="' . $link . '">
<img src="' . JOOBI_SITE . 'joobi/node/' . $controllerA[0] . '/images/' . $controllerA[0] . '-48.png">
<span>' . $oneApp->name . '</span>
</a>
</div>
</div>';
		}
		$html2 .= '</div></div>';
		$this->content = $html2;
	}
	public function createOld() {
		$controller = new stdClass;
		$controller->wid = WExtension::get( 'joomla.node', 'wid' );
		$params = new stdClass;
		WGlobals::set( 'quickIconsImg', true, 'global' );
		$form = WView::getHTML( 'joomla_quickicons' , $controller, $params );
		if ( !empty($form) ) $this->content = '<div id="cpanel">' . $form->make() . '</div>';
	}
}