<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Editor_Ckeditor_addon extends Editor_Get_class {
	function load() {
						WPage::addJSFile( 'main/ckeditor/ckeditor.js', 'inc', '', 'ForceFalse' );
	}
	function display() {
		static $onlyOnce = true;
				$content = parent::display();
						$extra = '<script language="javascript" type="text/javascript">
function insertWidget(namekey){
var editorName = "' . $this->editorName . '";
var dropText = "{widget:alias|name="+namekey+" }";
CKEDITOR.instances["' . $this->id . '"].insertText(dropText);
}
</script>';
		$toolbar = $this->editorName;
				$name = parent::getName();
		$id = parent::getId();
		if ( $onlyOnce ) {
			$ckString = 'CKEDITOR.disableAutoInline=true;';
			WPage::addJS( $ckString, 'text/javascript', true );
			$onlyOnce = false;
		}
		$placeholderStr = WText::t('1460990092NBVH');
		$html = ' <div class="ckEditor">' . $content . '</div> ';
		$script = '<script language="javascript" type="text/javascript">';
		if ($toolbar === 'inline') {
			$script .= 'CKEDITOR.inline( "' . $name . '", {toolbar: "' . $toolbar . '" , uiColor: "#DDE8F2", placeholder: "' . $placeholderStr . '"} );';
		} else {
			$script .= 'CKEDITOR.replace( "' . $name . '", {toolbar: "' . $toolbar . '" , uiColor: "#DDE8F2", placeholder: "' . $placeholderStr . '"});';
		}
		$script .= '
var onLoadValue = document.getElementById( \'' . $id . '\' ).value;
for (var i in CKEDITOR.instances) {
	CKEDITOR.instances[i].on(\'change\', function() {
		CKEDITOR.instances.' . $id . '.updateElement();
	});
}
</script>';
		return  $html . $script . $extra;
	}
	function getEditorName() {
		$ckEditor['ckeditor.ckbasic'] = 'CKEditor - Basic';
		$ckEditor['ckeditor.ckstandard'] = 'CKEditor - Standard';
		$ckEditor['ckeditor.ckfull'] = 'CKEditor - Full';
		$ckEditor['ckeditor.inline'] = 'CKEditor - Inline';
		return $ckEditor;
	}
}