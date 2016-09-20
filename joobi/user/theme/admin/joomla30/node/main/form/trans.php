<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_trans extends WForm_Coretrans {
	protected function renderTransCreate() {
		$buttonO = WPage::newBluePrint( 'button' );
		$buttonO->type = 'infoLink';
		$buttonO->tooltips = $this->infoBubble;
		$buttonO->link = $this->url;
		$buttonO->useTitle = false;
		$buttonO->text = '<i class="fa fa-language"></i>';
		$buttonO->id = 'tr_' . $this->idLabel;
		$buttonO->popUpIs = true;
		$buttonO->popUpWidth = '600';
		$buttonO->popUpHeight = '250';
		$myBotton = WPage::renderBluePrint( 'button', $buttonO );
		$html = '<div class="input-append">';
		$html .= $this->content;
		$html .= $myBotton;
		$html .= '</div>';
		$this->content = $html;
	}
}