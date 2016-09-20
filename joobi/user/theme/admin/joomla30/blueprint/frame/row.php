<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WPane_row extends WPane {
	private $_htmlContentA = array();
	public function startPane($params) {
		$this->_htmlContentA = array();
	}
	public function endPane() {
		if ( empty($this->_htmlContentA) ) {
			$this->content = '';
			return '';
		}
		$html = '<div class="clearfix">';	
		foreach( $this->_htmlContentA as $oneColumn ) {
			$html .= $oneColumn;
		}
		$html .= '</div>';
		$this->content = $html;
	}
	public function startPage($params) {
	}
	function add($content) {
		$this->_htmlContentA[] = $content;
	}
	public function endPage() {
	}
}
