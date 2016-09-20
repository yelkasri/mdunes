<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Editor_Codemirror_addon extends Editor_Get_class {
	function load() {
?>
<script type="text/javascript">
Joomla.submitbutton = function(task) {
}
</script>
<?php
		WPage::addCSSFile( 'codemirror/css/codemirror.css', 'inc' );
		WPage::addScript( JOOBI_URL_INC . 'codemirror/js/codemirror.js' );
		WPage::addJSFile( 'codemirror/js/codemirror.js', 'inc' );
		$filetype = WGlobals::get('filetype');
				$parserFile = 'parsexml.js';
		$styleSheet = 'xmlcolors.css';
		$compressed = '';
		switch($filetype){
			case 'main':
				break;
			case 'view':
				break;
			case 'css':
				$parserFile = 'parsecss.js';
				$styleSheet = 'csscolors.css';
				break;
			case 'js':
				$parserFile = array( 'tokenizejavascript.js', 'parsejavascript.js' );
				$styleSheet = 'jscolors.css';
				break;
			default:
				break;
		}
		$options = new stdClass;
		$options->basefiles		= array( 'basefiles' . $compressed . '.js' );
		$options->path	= JOOBI_URL_INC . 'codemirror' . DS . 'js'. DS;
		$options->parserfile = $parserFile;
		$options->stylesheet = JOOBI_URL_INC . 'codemirror' . DS . 'css'. DS . $styleSheet;
		$options->height = '350px';			$options->width	= '100%';
		$options->continuousScanning = 500;
		$options->lineNumbers	= true;
		$options->textWrapping	= true;
		$options->tabMode = 'shift';
										$id = $this->id;
				$version = phpversion();
						if ( $version >= '5.2.0' ) {			$addjs = '(function() {';
			$addjs .= 'var editor = new CodeMirror.fromTextArea("' . $id . '", ' . json_encode($options) . ');';
			$addjs .= '})()';
			WPage::addJSScript($addjs);
		}
	}
	function getEditorName() {
		$codeMirror['codemirror'] = 'Code Mirror';
		return $codeMirror;
	}
}