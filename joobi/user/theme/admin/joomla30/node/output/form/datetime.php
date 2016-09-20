<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_datetime extends WForm_Coredatetime {
	function create() {
		if ( !empty($this->element->readonly) ) return $this->show();
		$class = ( ( !empty( $this->element->classes ) ) ? $this->element->classes : 'inputbox' );
				$formats = array();
		$formats['dateonly'] = array( 'php' => "Y-m-d", 'js' => "yyyy-mm-dd", 'default' => "0000-00-00", 'hour' => 'false' );
		$formats['datetime'] = array( 'php' => "Y-m-d H:i", 'js' => "yyyy-mm-dd hh:ii", 'default' => "0000-00-00 00:00", 'hour' => 'true' );
										if ( !empty($this->element->formatdate) && !empty($formats[$this->element->formatdate]) ) $format = $this->element->formatdate;
		else $format = 1;
		if ( !empty($this->value) ) {
						if ( is_numeric($this->value ) ) {
												$this->value = WApplication::date( $formats[$this->dateFormat]['php'], $this->value );
			}		} else {
			$this->value = $formats[$this->dateFormat]['default'];
		}
		if ( '0000-00-00 00:00' == $this->value || '0000-00-00' == $this->value ) {
			$dateFormat = WApplication::date( $formats[$this->dateFormat]['php'], time() + WUsers::timezone() );
		} else {
			$dateFormat = $this->value;
		}		
		$disabled = ( empty($this->element->disabled) ?  '' : ' disabled="disabled"' );
		$readonly = empty( $this->element->readonly ) ? '' : ' readonly';
				$html = '<div id="' . $this->idLabel . '" class="input-append date form_datetime" data-date="' . $dateFormat . '" data-date-format="' . $formats[$this->dateFormat]['js'] . '">';
		$html .= '<input size="16" type="text" name="' . $this->map .'" value="' . $this->value . '"' . $readonly . $disabled . '>';
		if ( empty($readonly) ) $html .= '<span class="add-on"><i class="fa fa-times"></i></span><span class="add-on"><i class="fa fa-calendar"></i></span></div>';
		$use24Format = WPref::load( 'PMAIN_NODE_DATEFORMAT' );
		$use24Hour = ( !empty($use24Format) ? '0' : '1' );
				$minimumDate = '1980-01-01 00:01';
		$jsString = "jQuery('#". $this->idLabel . "').datetimepicker({
format:'". $formats[$this->dateFormat]['js'] ."',
startDate:'". $minimumDate ."',
todayBtn:1,";
if ( $this->dateFormat == 'dateonly' ) $jsString .= "minView:2,";
$jsString .= "todayHighlight:true,
weekStart:1,
autoclose:1,
startView:2,
forceParse:0,
showMeridian:" . $use24Hour . "
});";
		WPage::addJSFile( 'js/bootstrap-datetimepicker.js' );
		WPage::addCSSFile( 'css/bootstrap-datetimepicker.css' );
		WPage::addJSScript( $jsString );
		$this->content = $html;
		return true;
	}
}