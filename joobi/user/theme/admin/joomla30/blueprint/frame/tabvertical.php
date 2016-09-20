<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WPane_tabvertical extends WPane {
	public $fade = true; 	
	private $_navTabHTMLA = array();
	private $_tabPaneHTMLA = array();
	private static $_paneIcon = null;
	private $_leftTabs = true;
	private $_sideways = false;
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
		WPage::addCSSFile( 'css/vertical-tabs.css' );
		$sideways = ( $this->_sideways ? ' sideways' : '' );
		if ( $this->_leftTabs ) {
			$this->content = '<div class="col-xs-3">';
			$this->content .= '<ul class="nav nav-tabs tabs-left' . $sideways . '">';
			$this->content .= implode( '', $this->_navTabHTMLA );
			$this->content .= '</ul>';
			$this->content .= '</div>';
			$this->content .= '<div class="col-xs-9">';
			$this->content .= '<div class="tab-content">';
			$this->content .= implode( '', $this->_tabPaneHTMLA );
			$this->content .= '</div>';
			$this->content .= '</div>';
		} else {
			$this->content = '<div class="col-xs-3">';
			$this->content .= '<div class="tab-content">';
			$this->content .= implode( '', $this->_tabPaneHTMLA );
			$this->content .= '</div>';
			$this->content .= '</div>';
			$this->content .= '<div class="col-xs-9">';
			$this->content .= '<ul class="nav nav-tabs tabs-right' . $sideways . '">';
			$this->content .= implode( '', $this->_navTabHTMLA );
			$this->content .= '</ul>';
			$this->content .= '</div>';
		}
		return $this->content;
	}
	public function startPage($params) {
		$this->content = '';
	}
	public function endPage($params) {
		if ( empty($this->content) ) {
			return '';
		}		$this->fade = false;
		static $count = 0;
		$count++;
		static $active = true;
		if ( $active ) {
			$activeClassNav = ' class="active"';
			$activeClassPane = ' active';
		} else {
			$activeClassNav = '';
			$activeClassPane = '';
		}
		if ( $active ) $active = false;
		$myID = $params->id . '_' . $count;
		$navTabHTML = '<li' . $activeClassNav . '><a href="#' . $myID . '" data-toggle="tab">';
		if ( self::$_paneIcon && !empty($params->faicon) ) $navTabHTML .= '<i class="fa ' . $params->faicon . '"></i>';
		$navTabHTML .= $params->text;			$navTabHTML .= '</a></li>';
		$tabPaneHTML = '<div class="tab-pane' . $activeClassPane . '" id="' . $myID . '">' . $this->content . '</div>';
				$this->content = '';
		$this->_navTabHTMLA[] = $navTabHTML;
		$this->_tabPaneHTMLA[] = $tabPaneHTML;
		return '';
	}
}