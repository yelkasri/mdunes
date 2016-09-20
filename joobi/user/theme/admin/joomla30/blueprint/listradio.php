<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WRender_Listradio_class extends Theme_Render_class {
	public function render($data) {	
		static $count = 1;
		if ( empty($data->listA) ) return '';
		if ( empty( $data->tagID ) ) $data->tagID = $data->tagName;
		$html = '';
		$optionGroup = false;
		if ( $data->colnb > 0 ) {
			$columnCount = true;
		} else {
			$columnCount = false;
		}
		if ( isset($this->classes) ) $data->tagAttributes .= ' class="'.$this->classes.'"';
		if ( isset($this->style) ) $data->tagAttributes .= ' style="'.$this->style.'"';
		if ( isset($this->align) ) $data->tagAttributes .= ' align="'.$this->align.'"';
		$typeSlect = 'checked="checked"';
		if ( $data->radioType ) {
				$typeHTML = 'checkbox';
				$nameHTML = $data->tagName . '[]';
		} else {
				$typeHTML = 'radio';
				$nameHTML = $data->tagName;
		}
		$icol = 1;
		if ( $data->radioStyle=='checkBox' && $columnCount ) {
			$html .= '<div class="controls">';
			$html .= '<div class="checkBoxAlign">';
			$isCheckBox = true;
		} else {
			$isCheckBox = false;
		}
		if ( !empty($data->requiredCheked) ) {
			$myText = WGlobals::get( 'requireCheckedText', '', 'global' );
			$onClick = 'onclick="' . WView::checkTerms( count($data->listA), $myText ) .'"';
		} else {
			$onClick = '';
		}
		$color2Use = ( count($data->listA) > 2 ? 'primary' : 'default' );
		foreach( $data->listA as $key => $value ) {
			$tagIDVal = $data->tagID . '_' . $key;
			if ( $data->arrayType ) {
				$listKey = $key;
				$listValue = $value;
			} else {
				$valKey = $data->propertyKey;
				$listKey = $value->$valKey;
				$valVal = $data->propertyText;
				$listValue = $value->$valVal;
			}
			if ( substr( $listValue, 0, 2 ) == '--' ) {
				if ( $optionGroup ) {
					$html .= '</fieldset>' . $this->crlf ;
				}
				$listValue = trim( substr( $listValue, 2, strlen($listValue)-2 ) );
				$html .= '<fieldset id="' . $data->tagID . '" class="'.$classes.'"><legend>'.$listValue.'</legend>';
				$optionGroup = true;
			} else {
				$extra = '';
				$selected = false;
				if ( is_array( $data->selected ) ) {
					if ( in_array( $listKey, array_values($data->selected) ) ) {
						$selected = true;
					}
				} else {
					if ( $listKey == $data->selected ) {
						$selected = true;
					}
				}
				if ( $selected ) $extra .= $typeSlect;
				if ( $data->disable ) $extra .= ' disabled';
				if ( $data->radioStyle == 'radioButton' ) {
					if ( $color2Use == 'primary') {
						if ( $selected ) $classLabel = 'btn btn-primary';	
						else $classLabel = 'btn';
					} else {
						if ( $listKey == 0 ) {
							if ( $selected ) $classLabel = 'btn btn-danger';	
							else $classLabel = 'btn';
						} elseif ( $listKey == 1 ) {
							if ( $selected ) $classLabel = 'btn btn-success';	
							else $classLabel = 'btn';
						}
					}
				}
				if ( $data->radioStyle == 'checkBoxMultipleSelect' ) {
					$html .= '<div class="multiRadio">';
				}
				if ( $data->radioStyle=='checkBox' ) $html .= '<div>';
				$html .= '<input type="'.$typeHTML.'" name="' . $nameHTML . '" id="' . $tagIDVal . '" value="' . $listKey . '" '.$data->tagAttributes.' ' . $extra . $onClick . ' />';
				$html .= '<label';
				if ( !empty($classLabel) ) $html .= ' class="' . $classLabel . '"';
				$html .= ' for="' . $tagIDVal . '">'. $listValue .'</label>' . WGet::$rLine;
				if ( $data->radioStyle=='checkBox' ) $html .= '</div>';
				if ( $data->radioStyle == 'checkBoxMultipleSelect' ) {
					$html .= '</div>';
				}
				$html .= WGet::$rLine;
			}
			if ( $data->radioStyle=='checkBox' || !$data->radioType ) {
				if ( $columnCount ) {
					if ( $data->colnb != $icol && intval($icol/$data->colnb) === $icol/$data->colnb ) {
						if ( $data->radioStyle=='checkBox' ) {
							$html .= '</div><div class="checkBoxAlign">';
						} else {
							$html .= '</div><div class="radioAlign">';
						}
					}
					$icol++;
				}
			} else {
				if ( $columnCount ) {
					$icol++;
				}
			}
		}
		if ( $isCheckBox ) $html .= '</div></div>';
		$html .= WGet::$rLine;
		$html = ( $optionGroup ) ? '</fieldset>' . $html : $html;
		switch( $data->radioStyle ) {
			case 'checkBox':
				break;
			case 'checkBoxMultipleSelect':
				if ( $data->colnb < 4 ) $data->colnb = 4;
				$heightBigBox = $data->colnb * 26;
				$html = '<div class="radioAlign">' . $html . '</div>';
				$html = '<div class="checkBoxMultipleSelect" style="height:' . $heightBigBox . 'px;">' . $html . '</div>';
				break;
			case 'radioButton':
			default:
				$extraDisable = ( $data->disable ? ' disabled' : '' );
				if ( $color2Use == 'primary') {
					$html = '<fieldset id="' . $data->tagID . '" class="radio btn-group' . $extraDisable . '">' . $html . '</fieldset>';
				} else {
					$html = '<fieldset id="' . $data->tagID . '" class="radio btn-group btn-group-yesno' . $extraDisable . '">' . $html . '</fieldset>';
				}
				break;
		}
		return $html;
	}
}
