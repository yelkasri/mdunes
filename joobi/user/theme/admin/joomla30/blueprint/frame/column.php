<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WPane_column extends WPane {
	private $_htmlContentA = array();
	private $_columnWidthA = array();
	private $_isRTL = null;
	public function startPane($params) {
		$this->_isRTL = WPage::isRTL();
		$this->_htmlContentA = array();
	}
	public function endPane() {
		if ( empty($this->_htmlContentA) ) {
			$this->content = '';
			return '';
		}
		$total = count( $this->_htmlContentA );
		if ( $total > 12 ) $total = 12;
		$totalUsed = 0;
		$totalExitCol = 0;
		if ( !empty($this->_columnWidthA) ) {
			foreach( $this->_columnWidthA as $key => $col ) {
				if ( !empty($col) ) {
					$new = floor( 12 * $col / 100 );
					$this->_columnWidthA[$key] = $new;
					$totalUsed += $new;
					$totalExitCol++;
				}
			}
		}
		$totalLEft = $total - $totalExitCol;
		if ( $totalLEft > 0 ) $indiceRef = floor( (12-$totalUsed) / ( $totalLEft ) );
		else $indiceRef = 0;
		$html = '<div class="container-fluid"><div class="row">';
		foreach( $this->_htmlContentA as $key => $oneColumn ) {
			if ( !empty( $this->_columnWidthA[$key] ) ) $indice = $this->_columnWidthA[$key];
			else $indice = $indiceRef;
			$pushRight = ( $this->_isRTL ? ' col-md-push-' . $indice : '' );
			$html .= '<div class="col-md-' . $indice . $pushRight . '">' . $oneColumn . '</div>';
		}
		$html .= '</div></div>';
		$this->content = $html;
	}
	public function startPage($params) {
	}
	function add($content) {
		$width = 0;
		if ( !empty( $this->_data->width ) ) {
			if ( strpos( $this->_data->width, '%' ) !== false ) {
				$width = str_replace( array(' ', '%'), '', $this->_data->width );
			}
		}
		$this->_htmlContentA[] = $content;
		$this->_columnWidthA[] = $width;
	}
	public function endPage() {
	}
}
