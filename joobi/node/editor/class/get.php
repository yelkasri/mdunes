<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Editor_Get_class extends WClasses {
	var $content = '';
	var $name = '';
	var $id = '';
	var $width = '300px';
	var $height = '100px';
	var $cols = 60;
	var $rows = 6;
	var $editorName = null;
	var $element = null;
	var $cms = false;
	var $class = 'textarea';
	var $changeVar = true;
	var $_extensionDestination = 'node|editor|addon';
	public function loadEditor($editorName,$showButtons=false) {
		$editorLoad = explode('.',$editorName);
		if ( empty($editorName) || strtolower($editorName) =='default' ) {
			$editor = new Editor_Get_class;
		} else {
			$EDITORADDON = 'editor.' . $editorLoad[0];
			$editor = WAddon::get( $EDITORADDON );
			if ( !method_exists( $editor, 'display' ) ) {
				$message = WMessage::get();
				$message->codeE('Can not load your editor '.$EDITORADDON.'. Please select an other one in your General Preferences',array(),0);
				return '';
			}
		}
		$editor->content = $this->content;
		if ( !empty($editor->changeVar) ) {
			$newName = 'zdtr_'  . str_replace( array( '[', ']', '.' ), '_', $this->name );
			$editor->name = $newName;
			$currentForm = WView::form( $this->formName );
			$currentForm->hidden( JOOBI_VAR_DATA . 'zt[edt][' . $editorName . '][' . $newName . ']', $this->name );			
		} else {
			$editor->name = $this->name;
		}
		$editor->id = $this->id;
		$editor->element = $this->element;
		if ( !empty($editorLoad[1]) ) $editor->editorName = $editorLoad[1];
		if ( !empty($this->element->width) ) $editor->width = $this->element->width;
		if ( !empty($this->element->height) ) $editor->height = $this->element->height;
		if ( !empty($this->element->cols) ) $editor->cols = $this->element->cols;
		if ( !empty($this->element->rows) ) $editor->rows = $this->element->rows;
		$editor->load();
		$editor->showButtons = $showButtons;
		return $editor->display();
	}
	function display() {
		if ( !empty( $this->width ) || !empty( $this->height ) ) {
			if ( !empty( $this->width ) && is_numeric( $this->width ) ) {
				$this->width .= 'px';
			}
			if ( !empty( $this->height ) && is_numeric( $this->height ) ) {
				$this->height .= 'px';
			}
			$style = 'style="';
			if ( !empty( $this->width ) ) $style .= 'width:' . $this->width . ';';
			if ( !empty( $this->width ) ) $style .= 'height:' . $this->height . ';';
			$style .= '" ';
		} else {
			$style = '';
		}
		if ( empty($this->cols) ) $this->cols = 50;
		if ( empty($this->rows) ) $this->rows = 10;
		$content = ' <textarea ' . $style . 'id="' . $this->id . '"';
		$content .= ' name="' . $this->name .'"';
		$content .= ' rows="' . $this->rows . '" cols="' . $this->cols . '"';
		if ( !empty( $this->class ) ) $content .= ' class="' . $this->class . '"';
		if ( isset( $this->element->wrap ) ) $content .=  ' warp=OFF'; 
		if ( isset( $this->element->disabled ) ) $content .=	' disabled';
		if ( !empty($this->element->readonly) ) $content .= ' readonly' ;
		$content .= ' >' ;
		$content .=  $this->content .'</textarea>' ;
		return $content;
	}
	function extra() {
		$extra = '<script language="javascript" type="text/javascript">
function insertWidget(namekey){
	var editorName = "' . $this->editorName . '";
	var dropText = "{widget:alias|name="+namekey+" }";
	var droparea = document.getElementById("' . $this->id . '");
	var range1 = droparea.selectionStart;
	var range2 = droparea.selectionEnd;
	var val = droparea.val();
	var str1 = val.substring(0,range1);
	var str3 = val.substring(range1,val.length);
	droparea.val(str1 + dropText + str3);
}
</script>';
		return $extra;
	}
	public function getAllEditorsName() {
		$myEditors = array();
		$oneEditor = new stdClass();
		$oneEditor->name = WText::t('1378320294IHEE');
		$oneEditor->folder = 'nicedit';
		$myEditors[] = $oneEditor;
		$oneEditor = new stdClass();
		$oneEditor->name = WText::t('1378320294IHEF');
		$oneEditor->folder = 'framework';
		$myEditors[] = $oneEditor;
		$oneEditor = new stdClass();
		$oneEditor->name = WText::t('1237260381RHQN');
		$oneEditor->folder = 'textarea';
		$myEditors[] = $oneEditor;
		$oneEditor = new stdClass();
		$oneEditor->name = WText::t('1384345472OOFE');
		$oneEditor->folder = 'ckeditor';
		$myEditors[] = $oneEditor;
		foreach( $myEditors as $editor ) {
			$class = WAddon::get('editor.'.$editor->folder);
			if ( !method_exists( $class,'getEditorName') ) continue;
			$editorsName = $class->getEditorName();
			if ( empty($editorsName) ) continue;
			$editorObj = new stdClass;
			$editorObj->name = $editor->name;
			$editorObj->cms = $class->cms;
			$editorObj->editors = $editorsName;
			$editors[] = $editorObj;
		}
		return $editors;
	}
	function load() {
	}
	function getEditorName() {
		return array( 'default' => WText::t('1237260381RHQN') );
	}
	function getEditorContent($editorName,$fieldName) {
		$editorLoad = explode( '.', $editorName );
		$EDITORADDON = 'editor.' . $editorLoad[0];
		$editor = WAddon::get( $EDITORADDON );
		if ( !method_exists( $editor, 'getContent' ) ) {
			$message = WMessage::get();
			$message->codeE( 'Can not load your editor ' . $EDITORADDON . '. Please select an other one in your General Preferences',array(),0 );
			return '';
		}
		if ( !empty($editorLoad[1]) ) $editor->editorName = $editorLoad[1];
		return $editor->getContent( $fieldName );
	}
	function getContent($fieldName) {
		return WGlobals::get( $fieldName, '', 'POST' );	
	}
	function getId() {
		return $this->id ;
	}
	function getFormName() {
	}
	function getName() {
		return $this->name ;
	}
}