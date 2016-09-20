<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Joomla_CoreShortmenu_module extends WModule {
	public function create() {
				if ( 'joomla30' != JOOBI_FRAMEWORK ) return false;
				$appsM = WModel::get( 'apps' );
		$appsM->whereE( 'publish', 1 );
		$appsM->remember( 'QuickMenuIcons', true, 'Views' );
		$appsM->remember( 'available_apps' );
		$appsM->whereE( 'type', 1 );
		$appsM->orderBy( 'name' );
		$allAppsA = $appsM->load( 'ol', array( 'wid', 'name', 'namekey' ) );
		if ( empty($allAppsA) ) return '';
		$html2 = '<ul id="menuJoobi" class="nav ">';
		$html2 .= '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Joobi <span class="caret"></span></a>
<ul class="dropdown-menu">';
		foreach( $allAppsA as $oneApp ) {
			if ( JOOBI_MAIN_APP . '.application' == $oneApp->namekey && ! WApplication::isEnabled( JOOBI_MAIN_APP ) ) {
				continue;
			}
			$controllerA = explode( '.', $oneApp->namekey );
			$link = 'index.php?option=com_' . $controllerA[0] . '&controller=' . $controllerA[0] . '';
			$html2 .= '<li><a href="' . $link . '" style="background-image: url(../joobi/node/' . $controllerA[0] . '/images/' . $controllerA[0] . '.png); background-repeat: no-repeat; background-position: 4px 4px; "><span style="padding-left:4px;">' . $oneApp->name . '</span></a></li>';
		}
		$html2 .= '</ul></li>';
		$html2 .= '</ul>';
		$this->content = $html2;
	}
}