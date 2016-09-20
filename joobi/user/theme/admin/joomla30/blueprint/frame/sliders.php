<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WPane_sliders extends WPane {
	private $_paneID = null;
	private $_pageTabHTMLA = array();
	private static $_countA = array();
	private static $_paneIcon = null;
	private static $_paneColor = null;
	public function startPane($params) {
  		if ( !isset( self::$_paneIcon ) ) {
  			$wowA = WPage::renderBluePrint( 'initialize', array( 'pane.icon', 'pane.color' ) );
  			self::$_paneIcon = $wowA['pane.icon'];
  			self::$_paneColor = $wowA['pane.color'];
  		}
		$this->_pageTabHTMLA = array();
		$this->_paneID = $params->id;
		WPage::addJSLibrary( 'jquery' );
		$js = 'jQuery(\'#' . $this->_paneID . ' .accordion-toggle\').click(function (e){var chevState = jQuery(this).find("i").toggleClass(\'fa-chevron-down fa-chevron-right\');});';
		$js .= '
jQuery( document ).ready(function() {
if ( typeof window.alradyloaded == "undefined") {
window.alradyloaded = true;
sessionStorage.activeTabTwo = typeof sessionStorage.activeTabTwo != "undefined" ? sessionStorage.activeTabTwo : "";
if ( sessionStorage.activeTabTwo.length ) {
var idsArr = sessionStorage.activeTabTwo.split(",");
jQuery.each(idsArr, function (index, id) {
var elementDiv = jQuery("#"+id);
var panelDiv = elementDiv.parent("div.panel");
var panelGroupDiv = panelDiv.parent("div.panel-group");
var index = panelGroupDiv.children().index( panelDiv )
if ( index !== 0  ) {
panelDiv.find(".accordion-toggle").click();
}
});
}
var divs = jQuery(".panel-default div.panel-collapse");
divs.on("hidden.bs.collapse", function () {
var id =  jQuery(this).attr("id");
if ( sessionStorage.activeTabTwo.length ) {
var idsArr = sessionStorage.activeTabTwo.split(",");
idsArr = jQuery.grep(idsArr, function(value) {
return value != id;
});
var newIdsStr = idsArr.join();
sessionStorage.activeTabTwo = newIdsStr;
}
jQuery(this).parent("div").find("i")
.removeClass("fa-chevron-down").addClass("fa-chevron-right");
});
divs.on("shown.bs.collapse", function () {
var id =  jQuery(this).attr("id");
if ( sessionStorage.activeTabTwo.length ) {
var idsArr = sessionStorage.activeTabTwo.split(",");
if ( jQuery.inArray( id, idsArr ) == -1 ) {
idsArr.push(id);
}
} else {
var idsArr = new Array();
idsArr.push(id);
}
var newIdsStr = idsArr.join();
sessionStorage.activeTabTwo = newIdsStr;
jQuery(this).parent("div").find("i")
.removeClass("fa-chevron-right").addClass("fa-chevron-down");
});
}
});
';
  		WPage::addJSScript( $js );
		return '';
	}
	public function endPane() {
		$this->content = '<div class="panel-group" id="' . $this->_paneID . '">' . $this->crlf;
		$this->content .= implode( '', $this->_pageTabHTMLA );
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
		$id = $params->parent;
  		if ( empty(self::$_countA[$id]) ) self::$_countA[$id] = 1;
  		else self::$_countA[$id]++;
  		$html = '<div class="panel';
  		if ( self::$_paneColor && !empty($params->color) ) $html .= ' panel-' . $params->color;
  		else $html .= ' panel-default';
  		$html .= '">';
  		$idName = $id . '_' . self::$_countA[$id];
  		if ( !empty($params->text) ) {
	  		$html .= '<div class="panel-heading">';
	  		$html .= '<h4 class="panel-title">';	
	  		if ( self::$_paneIcon && !empty($params->faicon) ) $html .= '<i class="fa ' . $params->faicon . '"></i>';
	  		$html .= '<a class="accordion-toggle" data-toggle="collapse" data-parent="#' . $this->_paneID . '" href="#' . $idName . '">';
	  		$html .= $params->text;
	  		if ( self::$_countA[$id] == 1 ) $html .= '<i class="fa fa-chevron-down fa-lg pull-right"></i>';
	  		else $html .= '<i class="fa fa-chevron-right fa-lg pull-right"></i>';
	  		$html .= '</a></h4>';
	  		$html .= '</div>';
  		}
  		$addIn = ( self::$_countA[$id] <= 1 ? ' in' : '' );
  		$html .= '<div id="' . $idName . '" class="panel-collapse collapse' . $addIn . '">';
  		$html .= '<div class="panel-body">';
  		$html .= $this->content;
  		$html .= '</div>';
  		$html .= '</div>';
		$this->content = '';
  		$html .= '</div>';
		$this->_pageTabHTMLA[] = $html;
	}
}
