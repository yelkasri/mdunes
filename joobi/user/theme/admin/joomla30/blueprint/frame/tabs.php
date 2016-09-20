<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WPane_tabs extends WPane {
	public $fade = true; 	
	private $_navTabHTMLA = array();
	private $_tabPaneHTMLA = array();
	private static $_paneIcon = null;
	private $_params = null;
	public function startPane($params) {
		$this->_params = $params;
		if ( !isset( self::$_paneIcon ) ) {
		  	self::$_paneIcon = WPage::renderBluePrint( 'initialize', 'pane.icon' );
		}
		$this->_navTabHTMLA = array();
		$this->_tabPaneHTMLA = array();
	}
	public function endPane() {
		static $count = 0;
		$count++;
		$id = ( !empty( $this->_params->idText ) ? $this->_params->idText : 'EnapBat' . $count );
		$this->content = '<ul id="' . $id . '" class="nav nav-tabs">';
		$this->content .= implode( '', $this->_navTabHTMLA );
		$this->content .= '</ul>';
		$this->content .= '<div class="tab-content">';
		$this->content .= implode( '', $this->_tabPaneHTMLA );
		$this->content .= '</div>' . $this->crlf;
		return $this->content;
	}
	public function startPage($params) {
		$this->content = '';
	}
	public function endPage($params) {
		if ( empty($this->content) ) {
			return '';
		}
		static $active = true;
																						$js  = '';
										$js .= 'window.WApps.helpers.makeTabActive("' . $params->idText . '");' . WGet::$rLine;
										$js .= 'window.WApps.helpers.setToCookieActiveTab("' . $params->idText . '");' . WGet::$rLine;
					WPage::addJSScript( $js,'default', false );
		if ( $active ) {
			$activeClassNav = ' class="active"';
			$activeClassPane = ' active';
			if ( $this->fade ) $activeClassPane = ' fade in' . $activeClassPane;
		} else {
			$activeClassNav = '';
			if ( $this->fade ) $activeClassPane = ' fade';
			else $activeClassPane = '';
		}
		if ( $active ) $active = false;
		$navTabHTML = '<li' . $activeClassNav . '><a href="#' . $params->id . '" data-toggle="tab">';
		if ( self::$_paneIcon && !empty($params->faicon) ) $navTabHTML .= '<i class="fa ' . $params->faicon . '"></i>';
		$navTabHTML .= '<h3 class="panel-title">' . $params->text . '</h3>';			$navTabHTML .= '</a></li>';
		$tabPaneHTML = '<div class="tab-pane' . $activeClassPane . '" id="' . $params->id . '">' . $this->content . '</div>';
				$this->content = '';
		$this->_navTabHTMLA[] = $navTabHTML;
		$this->_tabPaneHTMLA[] = $tabPaneHTML;
		return '';
	}
}