<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WPane_list extends WPane {
	private $htmlContent = '';
	private $htmlCell = '';
	function __construct($params=array()) {
		parent::__construct($params);
		$this->startPane( $params );
	}
	public function startPane($params) {
		$this->htmlContent = '';
	}
	public function endPane() {
		$this->htmlContent .= '';
	}
	public function startPage() {
		$this->htmlContent .= WGet::$rLine . '<div class="control-group">' . WGet::$rLine;
	}
	private function endPage() {
		$this->htmlContent .= WGet::$rLine . '</div>' . WGet::$rLine;
	}
	function cell($content,$position='') {
		$this->htmlCell .= $content;
	}
	public function line() {
		$this->startPage();
		$this->htmlContent .= $this->htmlCell;
		$this->endPage();
		$this->htmlCell = '';
	}
	public function add($content) {
		$this->htmlContent .= $content;
	}
	public function body() {
		$this->endPane();
		$this->content = $this->htmlContent;
		$this->htmlContent = '';
	}
	public function miseEnPageTwo(&$params,$value) {
		$name = $params->name;
		$title = '';
		if ( 'edit' == WPref::load( 'PMAIN_NODE_DIRECT_MODIFY' ) ) {
			$outputDirectEditC = WClass::get( 'output.directedit' );
			$editButton = $outputDirectEditC->editView( 'form', $params->yid, $params->fid );
			if ( !empty($editButton) ) $this->cell( $editButton , 'edit' );
		} elseif ( 'translate' == WPref::load( 'PMAIN_NODE_DIRECT_MODIFY' ) ) {
			$outputDirectEditC = WClass::get( 'output.directedit' );
			$editButton = $outputDirectEditC->translateView( 'form', $params->yid, $params->fid, $name );
			if ( !empty($editButton) ) $this->cell( $editButton , 'edit' );
		}
		$tip = $params->description;
		$required = $params->required;
		$notitle = 0;
		if ( isset($params->notitle) ) $notitle = $params->notitle;
		if ( isset($params->flip) ) $flip=$params->flip;
		if ( isset($params->lbreak) ) $lbreak=$params->lbreak;
		$tipsType = ( !empty( $params->tipstyle ) ? $params->tipstyle : false );
		if ( empty($tipsType) && isset(WRender_Form_class::$formTipStyle) ) $tipsType = WRender_Form_class::$formTipStyle;
		if ( $notitle == 0 ) {	
			$req = '<span class="star"> *</span>';
			if ( $tip && ! $tipsType ) {	
					$toolTipsO = WPage::newBluePrint( 'tooltips' );
					$toolTipsO->tooltips = $tip;
					if ( $required==1 && $params->editItem ) $toolTipsO->text = $name . $req;
					else $toolTipsO->text = $name;
					$toolTipsO->title = $name;
					$toolTipsO->id = $params->idLabel;
					$toolTipsO->bubble = true;
					if ( $required==1 ) $toolTipsO->class = 'required';
					$s = WPage::renderBluePrint( 'tooltips', $toolTipsO );
				$title.= $s;
			} else {
				if ( $required==1 && $params->editItem ) $titleNude = $name . $req;
				else $titleNude = $name;
				$Labelclass = '';
				$title .= WGet::$rLine . '<label' . $Labelclass . ' for="' . $params->idLabel . '">' . $titleNude . '</label>' . WGet::$rLine;
			}
			$title = '<div class="control-label">' . $title . '</div>' . WGet::$rLine;
		} else {
			$title = '<div class="col-sm-2">&nbsp;</div>';
		}
		if ( $tipsType ) {
			switch( $tipsType ) {
				case 'abovefield':
					$value = '<div class="controls text-muted tipsAbove">' . $tip . '</div>' . $value;
					break;
				case 'belowfield':
					$value .= '<div class="controls text-muted tipsBelow">' . $tip . '</div>';
					break;
				default:
					break;
			}
		}
		$this->td_c = 'key' . $params->spantit;
		if ( !empty($flip) ) {
			if ( ! $params->spantit ) {
				if ( ! $params->spanval ) {
					$this->cell( $value, 'right' );
					$this->cell( $title, 'left' );
				} else {
					$this->cell( $title, 'left' );
				}
			}else {
				$this->cell( $value, 'right' );
			}
		} else {
			if (!$params->spantit){
				if (!$params->spanval){
					$this->cell($title, 'left' );
					$this->cell($value, 'right' );
				} else {
					$this->cell($title, 'left' );
				}
			}else {
				$this->cell($value, 'right' );
			}
		}
		if ( isset($params->extracol) ) $this->cell( $tip, 'third' );
		 return $this->line();
	}
}