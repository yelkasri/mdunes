<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WRender_Form_class extends Theme_Render_class {
	public static $formDirection = null;
	public static $formTipStyle = null;
	public function render() {
		if ( !isset(self::$formDirection) ) {
			self::$formDirection = $this->value( 'form.direction' );
			self::$formTipStyle = $this->value( 'form.tooltip' );
		}
		return self::$formDirection;
	}
}
class WForms_default extends WForms_standard {
	function preCreate() {
		return true;
	}
	function preShow() {
		return true;
	}
	protected function wrapperCreate() {
		switch( $this->element->type ) {
			default:
				$html = '<div class="controls';
				if ( !empty($this->element->spantit) ) {
					$html .= ' smallControls';
				}
				$html .= '">' . $this->content . '</div>';
				break;
		}
		$this->content = $html;
	}
	protected function wrapperShow() {
		switch( $this->element->type ) {
			default:
				if ( !empty($this->element->spantit) ) {
					$this->content = '<div>' . $this->content . '</div>';
				} else {
					$this->content = '<div class="controls">' . $this->content . '</div>';
				}
				break;
		}
	}
}