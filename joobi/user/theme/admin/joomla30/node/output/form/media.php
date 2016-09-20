<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WForm_media extends WForm_Coremedia {
		protected $inputType = 'file';
	private $_maxFileSizeShow = 0;
	private $_allowedFormatsA = array();
	private $_maxFiles = 1;
	function create() {
		if ( ! WPref::load( 'PLIBRARY_NODE_ALLOW_FILE_UPLOAD' ) ) return false;
		$countUploadsN = WTools::count( 'ctUpld' );
				if ( !(bool) ini_get('file_uploads') ) {
			$message = WMessage::get();
			$message->adminW( 'You need to enable file upload in your PHP configuration to upload files.' );
			$this->content = '';
			return false;
		}
		static $file = null;
		$media = false;
		$onlyURL = WGlobals::get( 'mediaUploadOnlyURL_' . $this->element->namekey, false, 'global' );
		$formObject = WView::form( $this->formName );
		$formObject->enctype = true;
				if ( in_array( $this->element->typeName, array( 'media' ) ) ) {				 $this->element->typeName = 'file';
			 $media = true;
		}
		$this->_maxFiles = ( empty($this->element->maxnbupload) || $this->element->maxnbupload < 1 ) ? 1 : ( $this->element->maxnbupload > PLIBRARY_NODE_FANCYUPLOADMAX ? PLIBRARY_NODE_FANCYUPLOADMAX : $this->element->maxnbupload );
		if ( isset($this->onlyinputClass) && $this->onlyinputClass ) return;
						$mediaMapId = 'mediamap_'.$this->element->sid .'_'.$this->element->map.'_'.$countUploadsN;
		$mediaMapName = JOOBI_VAR_DATA . '['.$this->element->sid .'][x][mdpthtp]['.$this->element->map.']';
		if ( !$onlyURL ) {
			if ( is_numeric($this->value) ) {
								$this->content .='<input type="hidden" id="'.$mediaMapId .'old_'.$this->idLabel.'" name="'.$mediaMapName .'[filid_remove]" value="'.$this->value.'" />';
			}
						WText::load( 'output.node' );
			$maxFileSizeShowHTML = $this->_maxFileUpload();
			if ( !empty( $this->element->standardupload) ) {
				$fancyFileUpload = false;
			} else {
				$filesFancyuploadC = WClass::get( 'files.fancyupload' );
				$fancyFileUpload = $filesFancyuploadC->check();
			}
			if ( $fancyFileUpload ) {
								$mediaMapId = WGlobals::filter( 'mediaMap_'.$this->element->sid .'_'.$this->element->map."_".$countUploadsN, 'alnum' );
				$fielUploadURL = WPage::linkAjax( 'controller=output&task=uploadfile&wajx=1' );
				WText::load( 'output.node' );
	  			$mytranslation = array();
				$mytranslation['selectfile'] = WText::t('1206732376KOVE');
				$mytranslation['add_files'] =  WText::t('1242282432ARKM');
				$mytranslation['start_upload'] = WText::t('1359740894RBVZ');
				$mytranslation['remove_all'] = WText::t('1359740894RBWA');
				$mytranslation['close'] = WText::t('1228820287MBVC');
				$mytranslation['error_method_doesnot_exists_1'] = 'Method';
				$mytranslation['error_method_doesnot_exists_2'] = 'does not exist on jQuery.ajaxupload';
				$mytranslation = WGlobals::filter( $mytranslation, 'safejs' );
				$fileID = $this->element->sid . '_' . $this->element->map;
				$mytranslation_json = json_encode($mytranslation);
								$JScode = "var mytranslation='". $mytranslation_json."';
jQuery(document).ready(function() {
jQuery('#".$mediaMapId."').ajaxupload({
url:'".$fielUploadURL."',
autoStart:  true,
foraxfiles: '".$fileID."',
dropArea:'#drophere_".$countUploadsN."',";
				if ( !empty($this->element->sid) ) {
										$usedModelM = WModel::get( $this->element->sid );
					if ( !empty( $this->_allowedFormatsA ) ) {
						if ( !is_array($this->_allowedFormatsA) ) $this->_allowedFormatsA = explode( ',', $this->_allowedFormatsA );
						$acceptedFormat = implode( "','", $this->_allowedFormatsA );
						$acceptedFormat = strtolower($acceptedFormat);
						$JScode .= " allowExt:['" . $acceptedFormat . "'],";
					}				}
				$JScode .= " maxFiles:'" . $this->_maxFiles . "',";
				$JScode .= " form:'#".$this->formName."',";
				$JScode .= " maxFileSize:'" . $this->_maxFileSizeShow . "'});});";
				WPage::addJSLibrary( 'jquery' );
				WPage::addJSFile( 'js/ajaxupload.js' );
				WPage::addJSScript( $JScode, 'default', false );
				WPage::addCSSFile( 'css/ajaxupload.css' );
				$this->content = '<div id="'.$mediaMapId.'" class="clearfix"> </div>';
				$this->content .= '<div  class="drophere" id="drophere_'.$countUploadsN.'">' . WText::t('1418159379KSUW') . '<br/><i class="fa fa-cloud-download fa-4x"></i>';
				if ( !empty( $maxFileSizeShowHTML ) ) $this->content .= '<br/><p class="text-warning">' . $maxFileSizeShowHTML . '</p>';
				$this->content .= '</div>';
			} else {
				parent::create();
				if ( !empty( $maxFileSizeShowHTML ) ) $this->content .= '<br>' . $maxFileSizeShowHTML;
			}
			$eid = WGlobals::getEID();
						if ( ! $this->element->fdid && !empty($eid) && $media ) {	
				$map = $this->element->map;
				$model = $this->element->sid;
				if ( !empty($this->value) && $this->_maxFiles < 2 ) {
					$textLink = WText::t('1206732361LXFE');
					$iconO = WPage::newBluePrint( 'icon' );
					$iconO->id = 'edit' . $this->element->fid;
					$iconO->icon = 'edit';
					$iconO->text = WText::t('1206732361LXFE');
				} else {
					$iconO = WPage::newBluePrint( 'icon' );
					$iconO->icon = 'attachment';
					$iconO->id = 'attch' . $this->element->fid;
					$iconO->text = WText::t('1327712854HKDV');
					$textLink = WText::t('1327712854HKDV');
				}
					$attach = WPage::renderBluePrint( 'icon', $iconO ) . $textLink;
				$objButtonO = WPage::newBluePrint( 'button' );
				$objButtonO->text = '<div style="display:block;padding-top:3px;vertical-align:middle;">'. $attach.'</div>';
				$objButtonO->type = 'standard-showAll';
				$objButtonO->id = 'btnMdttch' . $this->element->fid;
				$objButtonO->wrapperDiv = 'mediaButtonBord';
				$objButtonO->link = WPage::linkPopUp( 'controller=files-attach&task=listing&pid=' . $this->eid.'&map='.$map . '&model=' . $model );
				$objButtonO->popUpIs = true;
				$objButtonO->popUpHeight = '90%';
				$objButtonO->popUpWidth = '90%';
				$attachButton = WPage::renderBluePrint( 'button', $objButtonO );
				$this->content .= '<span class="mediaWrapButton">' . $attachButton;
				if ( !empty($this->value) && $this->_maxFiles < 2 ) {	
										$iconO = WPage::newBluePrint( 'icon' );
					$iconO->icon = 'delete';
					$iconO->text = WText::t('1225790126CCSR');
					$attach = WPage::renderBluePrint( 'icon', $iconO );
					$controller = WGlobals::get('controller');
					$textLink = WText::t('1225790126CCSR');
					$objButtonO = WPage::newBluePrint( 'button' );
					$objButtonO->text = '<div style="display:block;padding-top:3px;vertical-align:middle;">' . $attach . '</div>';
					$objButtonO->type = 'standard';
					$objButtonO->wrapperDiv = 'mediaButtonBord';
					$deleteIcon = WPage::renderBluePrint( 'button', $objButtonO );
										$mainModel = WModel::get($this->element->sid, 'object' );
					$pk = $mainModel->getPK();
										$model = WModel::get($this->element->sid, 'namekey' );
					$deleteLink = WPage::routeURL('controller=output&task=removefile&filid='.$this->value.'&map='.$map.'&pk='.$pk.'&model='.$model.'&controllerback='.$controller.'&id='.$this->eid);
					$text= WText::t('1369750048LRQB');
					$this->content .= '<a style="cursor: pointer" onclick=\'if (confirm("'.$text.'")) location.href="'.$deleteLink.'";\' >'.$deleteIcon.'</a>';
				}
				$this->content .= '</span>';
			}
		}
		$myFileInfoO = new stdClass;
				$filesMediaC = WClass::get( 'files.media' );
		if ( !empty($this->value) ) {
			$filesHelperC = WClass::get( 'files.helper' );
			$myFileInfoO = $filesHelperC->getInfo( $this->value );
		}
				if ( !empty($this->element->mpicklist) ) {
			$nameValue = ''; 			if ( !empty($myFileInfoO->type) ) {
								$mediatypes = WView::picklist( 'files_type' );
				if ( !$mediatypes->inValues( $myFileInfoO->type ) ) {
					$myFileInfoO->icontype = $myFileInfoO->type;
				}				if ( empty($myFileInfoO->icontype) ) {
					$mediaIconTypes = WType::get( 'files.icontypes');
					if ( !$mediaIconTypes->inValues( $myFileInfoO->type ) ) {
						$myFileInfoO->icontype = 'file';
					} else {
						$myFileInfoO->icontype = $mediaIconTypes->getName( $myFileInfoO->type );
					}				}			}						$params = new stdClass;
			$params->default = !empty($myFileInfoO->type) ? $myFileInfoO->type : '';
			$params->outputType = 0; 			$params->nbColumn = 1; 			$params->map = $mediaMapName.'[type]'; 			$params->idlabel = $mediaMapId.'_type'; 
						$fileTypePicklist = WGlobals::get( 'mediaUploadFileType_' . $this->element->namekey, '', 'global' );
			WGlobals::set( 'mediaUploadFileTypePicklist', $fileTypePicklist, 'global' );
			$mediaPickList = WView::picklist( 'files_media_types', null, $params );
			$this->content .= $mediaPickList->display();
						$this->content .= '<input class="inputbox" type="text" size="50" id="'.$mediaMapId.'_name" name="'.$mediaMapName.'[name]" value="'.$nameValue.'">';
		}
		if ( $this->_maxFiles < 2 ) {
					  	if ( !empty($myFileInfoO) ) {
								if ( empty($myFileInfoO->mime) ) {
					$mimeA = array( 'unkown' );
				} else {
					$mimeA = explode( '/', $myFileInfoO->mime );
				}
				if ( $mimeA[0]=='image' && !$myFileInfoO->secure ) {
					if ( $myFileInfoO->thumbnail ) {
						$myWidth = ( !empty($myFileInfoO->twidth) ) ? $myFileInfoO->twidth : ( ( $myFileInfoO->width > 48 ) ? 48 : $myFileInfoO->width);
						$myHeight = ( !empty($myFileInfoO->theight) ) ? $myFileInfoO->theight : ( ( $myFileInfoO->height > 48 ) ? 48 : $myFileInfoO->height);
					} else {
						$thumnailPath='';
						$myWidth = ( $myFileInfoO->width > 48 ) ? 48 : $myFileInfoO->width;
						$myHeight = ( $myFileInfoO->height > 48 ) ? 48 : $myFileInfoO->height;
					}
					$myNewImageO = WObject::get( 'files.file' );
					$myNewImageO->name = $myFileInfoO->name;
					$myNewImageO->type = $myFileInfoO->type;
					$myNewImageO->basePath = JOOBI_URL_MEDIA;
					$myNewImageO->folder = ( empty($myFileInfoO->folder) ? 'media' : $myFileInfoO->folder );
					$myNewImageO->path = $myFileInfoO->path;
					$myNewImageO->fileID = $myFileInfoO->filid;
					$myNewImageO->thumbnail = $myFileInfoO->thumbnail;
					$myNewImageO->storage = $myFileInfoO->storage;
					$myNewImageO->secure = $myFileInfoO->secure;
					$url = $myNewImageO->fileURL( $myFileInfoO->thumbnail );
					$this->content .= '<div style="float:left; clear:both;">';
					$this->content .= '<div><strong>' . WText::t('1298350386KTQN') . '</strong></div>';
					if ( $myNewImageO->thumbnail || $myNewImageO->isImage() ) {
						$this->content .= '<a id="link' . $this->idLabel . '" href="#" onclick="window.open(\'' . $url . '\', \'win2\', \'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=' . $myFileInfoO->width . ',height=' . $myFileInfoO->height . ',directories=no,location=no\');return false;">';
						$data = WPage::newBluePrint( 'image' );
						$data->location = $url;
						$data->text = $myFileInfoO->filename;
						$data->width = $myWidth;
						$data->height = $myHeight;
						$this->content .= WPage::renderBluePrint( 'image', $data );
						$this->content .= '</a>';
					} else {
						$this->content .= '<a target="_blank" href="' . $url . '"><span class="label label-success">' . WText::t('1446651300CLYK') . '</span></a>';
					}
					$this->content .= '<div style="padding: 5px 0 10px;">' . $myFileInfoO->filename . '</div>';
					$this->content .= '</div>';
				} else {
					if ( !empty($myFileInfoO->type) ) {
						$this->content .= '<div class="clearfix"><strong>' . WText::t('1206732375LZCF') . '</strong></div>';							$filesMediaC = WClass::get( 'files.media' );
						$this->content .= $filesMediaC->renderHTML( $this->value, $this->element );
					}
				}
			}
		}
		$formObject = WView::form( $this->formName );
		$formObject->hidden( 'laod-fild[' . $this->element->sid. '][' . WTools::count( 'laod-fild' . $this->element->sid ) . ']', $this->element->map );
		$listingSecurity = WGlobals::get( 'securityForm', array(), 'global' );
		$listingSecurity['sec'][] = $this->element->sid .'_' . $this->element->map;
		WGlobals::set( 'securityForm', $listingSecurity, 'global' );
		return true;
	}
}