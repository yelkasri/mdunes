<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
if ( !class_exists( 'WRender_Progressbar_blueprint' ) ) {
class WRender_Progressbar_blueprint extends Theme_Render_class {
	public function render($data) {
		$renderType = ( !empty( $data->type ) ? $data->type : 'bar' );
		switch( $renderType ) {
			case 'error':
				return $this->_renderError( $data );
				break;
			case 'text':
				return  $this->_renderEstimate( $data );
				break;
			case 'bar':
			default:
				return $this->_renderBar( $data );
				break;
		}
	}
	private function _renderBar($data) {
		if ( empty($data->percentage) ) {
			if ( !empty($data->targetTotal) ) {
				if ( ! is_numeric($data->targetTotal) || ! is_numeric($data->target) ) return '';
				$data->percentage = ( $data->target / $data->targetTotal ) * 100;
			}
		}
		if ( $data->percentage > 100 ) $data->percentage = 100;
		$data->percentage = round( $data->percentage, 1 );
		if ( empty($data->labelMsg) ) $data->labelMsg = $data->percentage . '%';
		$style = ( !empty($data->labelStyle) ? ' style="' . $data->labelStyle . '"' : '' );
		$messageHTML = '<span' . $style . '>' . $data->labelMsg . '</span>';
		$extra = ' progress-bar-' . $data->color;
		if ( $data->striped ) $extra .= ' progress-bar-striped';
		if ( $data->animated ) $extra .= ' active';
		$progressbarHTML = '<div id="ProgressBar" class="progress">';
		$progressbarHTML .= '<div class="progress-bar ' . $extra .'" role="progressbar" aria-valuenow="' . $data->percentage . '" aria-valuemin="0" aria-valuemax="100" style="min-width: 3em; width: ' . $data->percentage . '%">';
		$progressbarHTML .= $messageHTML;
		$progressbarHTML .= '<span class="sr-only">' . $data->percentage . '% ' . WText::t('1338312640PTMH') . ' (' . $data->color . ')</span>';
		$progressbarHTML .= '</div></div>';
		return $progressbarHTML;
	}
	private function _renderError($data) {
		$html = '';
				if ( $data->showError ) {				$html .= '<div id="WAjxPaneWarning" style="display:none;" class="alert alert-warning" role="alert">' . $data->warningValue . '</div>';
			$html .= '<div id="WAjxPaneError" style="display:none;" class="alert alert-danger" role="alert">' . $data->errorValue . '</div>';
		}
		return $html;
	}
	private function _renderEstimate($data) {
		$html = '<div id="WAjxText" class="row">';
		if ( $data->showStatus ) {
			$html .= '<div class="form-group">';
			$html .= '<label class="col-sm-3 control-label">' . $data->statusText . ': </label>';
			$html .= '<div class="col-sm-9">' . $data->statusValue . '</div>';
			$html .= '</div>';
		}
		if ( $data->showDuration ) {
			$html .= '<div class="form-group">';
			$html .= '<label id="WAjxDurationText" class="col-sm-3 control-label">' . $data->durationText . ': </label>';
			if ( 100 == $data->percentage ) {
				$html .= '<div class="col-sm-9">' . $data->durationText . '</div>';
			} else {
				$html .= '<div class="col-sm-9">' . $data->durationValue . '</div>';
			}			$html .= '</div>';
		}
		if ( $data->showTime ) {
			$html .= '<div class="form-group">';
			$html .= '<label class="col-sm-3 control-label">' . $data->timeText . ': </label>';
			$html .= '<div class="col-sm-9">' . $data->timeValue . '</div>';
			$html .= '</div>';
		}
		if ( $data->showDetails ) {
			$html .= '<div class="form-group">';
			$html .= '<label class="col-sm-3 control-label">' . $data->detailsText . ': </label>';
			$html .= '<div class="col-sm-9">' . $data->detailsValue . '</div>';
			$html .= '</div>';
		}
		$html .= '</div>';
		return $html;
	}
}}
class WRender_Progressbar_class extends WRender_Progressbar_blueprint {
}