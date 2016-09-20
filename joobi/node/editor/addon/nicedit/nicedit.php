<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Editor_Nicedit_addon extends Editor_Get_class {
	public $editorName = 'media';
	public function load() {
		$editorConfiguration['basic'] = "buttonList : ['undo','redo','bold','italic','underline','strikethrough','forecolor','ol','ul','indent','outdent'";
		$editorConfiguration['advanced'] = $editorConfiguration['basic'].",'fontSize','fontFormat','bgcolor','left','center','right','justify'";
		$editorConfiguration['media'] = $editorConfiguration['advanced'].",'hr','link','unlink','image'";
		$editorConfiguration['full'] = 'fullPanel:true';
		$params = $editorConfiguration[$this->editorName];
		if ( in_array( $this->editorName, array('basic','advanced','media') ) ) $params .= ']';
		$path = JOOBI_URL_INC .'main/nicedit/nicEditorIcons.gif';
		$params .= ',iconsPath:\'' . $path . '\'';
		static $onlyOnce=true;
		WPage::addJSLibrary( 'jquery' );
		$js = 'new nicEditor({' . $params . '}).panelInstance(\'' . $this->id . '\');' . WGet::$rLine;
		if ( $onlyOnce ) {
			$js .= 'jQuery(\'.nicEdit-panelContain\').parent().width(\'100%\');' . WGet::$rLine;
			$js .= 'jQuery(\'.nicEdit-panelContain\').parent().next().width(\'98%\');' . WGet::$rLine;
			$onlyOnce = false;
		}
		WPage::addJSScript( $js, 'nicedit' );
		$this->content = str_replace( "\n", '', $this->content );
	}
	public function display() {
		$content = parent::display();
		$extra = parent::extra();
		if ( empty( $this->width ) ) $this->width = '300px';
		$html = '<div id="Box' . $this->id . '"';
		$html .= ' style="width:auto;min-width:' . $this->width . ';"';
		$html .= ' class="niceEditContain">';
		$html .= '<div class="niceEditor">' . $content . '</div>' . $extra . '</div>';
		return $html;
	}
	public function getEditorName() {
		$nicEdit['nicedit.basic'] = 'NicEdit - Basic';
		$nicEdit['nicedit.advanced'] = 'NicEdit - Advanced';
		$nicEdit['nicedit.media'] = 'NicEdit - Links and Media';
		$nicEdit['nicedit.full'] = 'NicEdit - Complete';
		return $nicEdit;
	}
}