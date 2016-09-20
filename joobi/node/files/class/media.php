<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_Media_class extends WClasses {
	var $videoWidth = 250; 	var $videoHeight =  200; 	var $audioWidth = 200; 	var $audioHeight =  25; 	var $transparency = 'transparent';
	var $autoplay = 'true';
	var $bgcolor = '838B8B';	
	var $fileType = ''; 
	private $_myNewImageO = null;
	private $_myFileInfoO = null;
	private static $IDCount = 0;
	private static $loadJMPlayer = true;
	private static $mediaID = array();
	private $_linkText = '';
	private $_addStyle = 'success';
	function __construct() {
		$this->autoplay = ( WPref::load( 'PFILES_NODE_AUTOPLAY' ) ? 'true' : 'false' );
	}	
	public function getMediaID($filid) {
		if ( !empty( self::$mediaID[$filid] ) ) return self::$mediaID[$filid];
		return null;
	}
	public function getPlayerType() {
		$useJWPlayer = WPref::load( 'PFILES_NODE_JWPLAYER' );
		if ( $useJWPlayer ) return 'jwplayer';
		else return 'standard';
	}
	public function renderHTML($filid,$element=null,$linkText='',$addStyle='success') {
		if ( !isset($element) ) $element = new stdClass;
		if ( !isset($element->fileicon) ) $element->fileicon = null;
		if ( !isset($element->advmedia) ) $element->advmedia = true;
		$this->_linkText = $linkText;
		$this->_addStyle = $addStyle;
		WText::load( 'files.node' );
		$HTML = '';
		$filesHelperC = WClass::get( 'files.helper' );
		$this->_myFileInfoO = $filesHelperC->getInfo( $filid );
				if ( empty($this->_myFileInfoO) ) return '';
		$this->_myFileInfoO->filename = $this->_myFileInfoO->name . '.' . $this->_myFileInfoO->type;
		if ( $this->_myFileInfoO->secure == 1 ) {
			$HTML .= $this->_myFileInfoO->filename.' <strong><em>('.WText::t('1298350386KTQO'). ') </em></strong>';
			return $HTML;
		} else {
			$url = $this->_getFileURL();
		}
				if ( empty($element->advmedia) ) {
			if ( empty($this->_linkText) ) $this->_linkText = $this->_myFileInfoO->name;
			if ( !empty($this->_addStyle) ) $this->_linkText = '<span class="label label-' . $this->_addStyle . '">' . $this->_linkText . '</span>';
						if ( false && $this->_myFileInfoO->type != 'url' ) $HTML .= '<a href="'.$url.'">' . $this->displayIcon( $this->_myFileInfoO ) . $this->_linkText . '</a>'; 			else $HTML .= '<a href="' . $this->_myFileInfoO->name . '" onclick="window.open(\''.$this->_myFileInfoO->name.'\', \'win2\', \'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes\')">' . $this->_linkText . ' </a>';
			return $HTML;
		}
						if (in_array( $this->_myFileInfoO->type, array( 'jpg', 'jpeg', 'gif', 'png', 'bmp' ) ) ) {
			$this->fileType = 'image';
			$HTML .= $this->image( null, $element );
		} elseif ( in_array( $this->_myFileInfoO->type, array( 'mp3', 'ma4', 'wav', 'aiff', 'wma' ) ) ) { 
			$this->fileType = 'audio';
			$HTML .= $this->_audio();
		} elseif ( in_array( $this->_myFileInfoO->type, array( 'vimeo', 'youtube', 'livevideo', 'yahoovideo', '3gp','flv', 'divx', 'mov', 'mp4', 'm4v', 'mov', 'swf', 'wmv') ) ) {
						$this->fileType = 'video';
			$HTML .= $this->video();
		} else {	
			$HTML = $this->_renderExternalFile( $this->_myFileInfoO );
		}
		return $HTML;
	}
	public function setMediaStyle($width,$height,$autoplay=null,$backGroundColor=null) {
		if ( !empty($width) ) $this->videoWidth = (int)$width;
		if ( !empty($height) ) $this->videoHeight = (int)$height;
		if ( isset($autoplay) ) $this->autoplay = (string)$autoplay;
		if ( isset($backGroundColor) ) $this->bgcolor = $backGroundColor;
	}
	public function renderExternalFile($name,$path='',$type='') {
		if ( empty($type) && empty($path) ) {
						$filesAnalyseC = WClass::get( 'files.analyze' );
			$name = $filesAnalyseC->getTypeAndName( $name, $type );
		}
		$myFileInfoO = new stdClass();
		$myFileInfoO->filid = rand( 100, 999 );			$myFileInfoO->name = $name;
		if ( empty($type) ) {
			$myFileInfoO->type = 'mp4';
		} else {
			$myFileInfoO->type = $type;
		}
		$myFileInfoO->path = $path;
		$myFileInfoO->folder = '';
		$myFileInfoO->storage = 0;
		$myFileInfoO->secure = 1;
		$HTML = $this->_renderExternalFile( $myFileInfoO );
		return $HTML;
	}
	private function _renderExternalFile($myFileInfoO) {
		$HTML = '';
				$mediatypes = WView::picklist( 'files_type' );
		if ( $myFileInfoO->type != 'url' && $mediatypes->inValues( $myFileInfoO->type ) ) {
			$HTML .= $this->video( $myFileInfoO );
		} else {
			if ( !empty( $myFileInfoO->type ) ) {
				$extensionType = $myFileInfoO->type;
			} else {
				$getExtensionA = explode( '.', $myFileInfoO->name );	
				$extensionType = array_pop( $getExtensionA );
			}			
			$extensionType = trim($extensionType);
			if (in_array( $extensionType, array( 'jpg', 'jpeg', 'gif', 'png', 'bmp' ) ) ){
				$this->fileType = 'image';
				$HTML .= $this->image();
			}
						elseif ( in_array( $extensionType, array( 'mp3', 'm4a', 'wav', 'aiff', 'wma' ) ) ){
				$this->fileType = 'audio';
				$HTML .= $this->_audio();
			}
						elseif ( in_array( $extensionType, array( '3gp','flv', 'divx', 'mov', 'mp4', 'mov', 'swf', 'wmv') ) ) {
				$this->fileType = 'video';
				$HTML .= $this->video( $myFileInfoO, $extensionType );
			} else {	
				if ( empty($this->_linkText) ) $this->_linkText = WText::t('1305603045PMTM');
				if ( !empty($this->_addStyle) ) $this->_linkText = '<span class="label label-' . $this->_addStyle . '">' . $this->_linkText . '</span>';
				if ( $myFileInfoO->type != 'url' ) {
					$myLink = JOOBI_URL_MEDIA;
					$myLink .= $this->_convertPath( $myFileInfoO->path, 'url' );
					$myLink .= '/' . $myFileInfoO->name . '.' . $myFileInfoO->type;
					WPage::addJSLibrary( 'joobibox' );
					$HTML .= '<a target="_blank" href="'. $myLink .'">';
					$HTML .= $this->_linkText;
					$HTML .= '</a>';
					if ( WRoles::isAdmin( 'manager' ) ) $HTML .= '<div style="padding: 5px 0 10px;">' . $myFileInfoO->name . '.' . $myFileInfoO->type . '</div> ';
				} else {
										$HTML .= WPage::createPopUpLink( $myFileInfoO->name, $this->_linkText, 800, 450 );
				}				
			}
		}
		return $HTML;
	}
	public function video($myFileInfoO=null,$extensionType=null) {
		if ( !empty($myFileInfoO) ) $this->_myFileInfoO = $myFileInfoO;
		if ( empty( $this->_myFileInfoO ) ) return '';
		$useJWPlayer = WPref::load( 'PFILES_NODE_JWPLAYER' );
				if ( $useJWPlayer ) {
			$mediaScriptsT = WType::get( 'files.scriptsjwplayer' );
			if ( self::$loadJMPlayer ) {
				WPage::addJSFile( 'main/mediaplayer/jwplayer/jwplayer.js', 'inc' );
				$useJWPlayerKey = PFILES_NODE_JWPLAYERKEY;
				if ( !empty($useJWPlayerKey) ) WPage::addJS( 'jwplayer.key="'.$useJWPlayerKey.'";' );
				self::$loadJMPlayer = false;
			}		} else {
			$mediaScriptsT = WType::get( 'files.scripts' );
		}
		self::$IDCount++;
		if ( $this->_myFileInfoO->type != 'url' ) {
			$url = $this->_getFileURL();
		} else {
			$url = $this->_myFileInfoO->name;
			if ( !empty($extensionType) ) $this->_myFileInfoO->type = $extensionType;
		}
		$videoScript = $mediaScriptsT->getName( $this->_myFileInfoO->type );
		self::$mediaID[$this->_myFileInfoO->filid] = $this->_myFileInfoO->name . '-' . self::$IDCount;
		$tags = array();
		$tags['{NAME}'] = $this->_myFileInfoO->name;
		$tags['{ID}'] = $this->_myFileInfoO->name . '-' . self::$IDCount;
		$tags['{SITEURL}'] = JOOBI_SITE;
		$tags['{URLINC}'] = JOOBI_URL_INC;
		$tags['{TEXTLOADING}'] = WText::t('1352764977FLOB');
		$tags['{TRANSPARENCY}'] = $this->transparency;
		$tags['{WIDTH}'] = $this->videoWidth;
		$tags['{HEIGHT}'] = $this->videoHeight;
		$tags['{BACKGROUND}'] = $this->bgcolor;
		$tags['{AUTOPLAY}'] = $this->autoplay;
		$tags['{FILESOURCE}'] = $url;
		$tags['{FILEID}'] = $this->_myFileInfoO->filid;
		$tags['{MEDIAID}'] = $this->_myFileInfoO->name;
		$tags['{BACKGROUNDQT}'] = $this->bgcolor;
				$HTML = str_replace( array_keys($tags), array_values($tags), $videoScript );
				if ( $this->_myFileInfoO->type == 'wmv'){
			WPage::addJSFile( 'main/mediaplayer/wmvplayer/wmvplayer.js', 'inc' );
			WPage::addJSFile( 'main/mediaplayer/wmvplayer/silverlight.js', 'inc' );
		}elseif ( in_array( $this->_myFileInfoO->type, array('mov','3gp','mp4') ) ) {
			WPage::addJSFile( 'main/mediaplayer/quicktimeplayer/ac_quicktime.js', 'inc' );
		}
		return $HTML;
	}
	public function image($myFileInfoO=null,$element=null) {
		if ( !empty($myFileInfoO) ) $this->_myFileInfoO = $myFileInfoO;
		if ( empty( $this->_myFileInfoO ) ) return '';
		if ( $this->_myFileInfoO->type == 'url' ) {
			$width = 150;
			$heigth = 150;
			$folder_url = $this->_myFileInfoO->name;
			$folder_ds = '';
			$fileName = '';
			$url = $folder_url;
			if ( empty($element) ) {
				$element = new stdClass;
				$element->imgfull = $this->_myFileInfoO->name;
				$this->idLabel = 'imagepreview';
			}
		} else {
			$width = $this->_myFileInfoO->width;
			$heigth = $this->_myFileInfoO->height;
			$url = $this->_getFileURL();
			$fileName = $this->_myFileInfoO->name;
		}
				if ( !empty( $element->thumb) ) {
			$width = $this->_myFileInfoO->twidth;
			$heigth = $this->_myFileInfoO->theight;
			$thumbnail_url = $this->_getFileURL( true );
		} else {
			$thumbnail_url = $url;
		}
						if ( !empty($element->imageWidth) && !empty($element->imageHeight) ) {
						if ( $heigth > $element->imageHeight ) {
				$ratio =  $element->imageHeight / $heigth;
				$width = defined('PHP_ROUND_HALF_DOWN') ? round( $width * $ratio, 0, PHP_ROUND_HALF_DOWN ) : round( $width * $ratio, 0 );
				$heigth = $element->imageHeight;
			}
						if ( $width > $element->imageWidth ) {
				$ratio =  $element->imageWidth / $width;
				$heigth = defined('PHP_ROUND_HALF_DOWN') ? round( $heigth * $ratio, 0, PHP_ROUND_HALF_DOWN ) : round( $heigth * $ratio, 0 );
				$width = $element->imageWidth;
			}
		}
		$data = WPage::newBluePrint( 'image' );
		$data->location = $thumbnail_url;
		$data->width = $width;
		$data->height = $heigth;
		$data->text = $fileName;
		$data->align = 'left';
		$contentImg = WPage::renderBluePrint( 'image', $data );
		$class = ( !empty( $element->classes ) ) ? $element->classes : 'a';
		if ( !isset($element->link) && !isset($element->imgfull) ) {
			$HTML =	$contentImg;
		} elseif ( !isset($element->link) && isset($element->imgfull) ) {
			$HTML = WPage::createPopUpLink( $url, $contentImg, ( $this->_myFileInfoO->width*1.15 ), ( $this->_myFileInfoO->height*1.15 ), $class, $this->idLabel );
		} else {
			if ( empty($this->idLabel) ) $this->idLabel = 'mediaID-' . time();
						$HTML = '<a id="'.$this->idLabel.'" class="'.$class.'" href="' . WPage::routeURL( $element->link ) . '">';
			$HTML .= $contentImg.'</a>';
		}
		return $HTML;	
	}
	private function _convertPath($path='',$type='url') {
		if ( $type == 'hdd' ) {
			$type = DS;
		} elseif ($type=='url' ) {
			$type = '/';
		}		
		return str_replace( array( '|', '\\', '/' ), $type, $path );
	}
	function displayIcon($myFileInfoO=null) {
		return true;
		$html = '';
		if ( !empty($myFileInfoO) ) {
			$iconType = $myFileInfoO->type;
			$iconImg = $this->iconImages( $iconType );
			$data = WPage::newBluePrint( 'image' );
			$data->location = $iconImg;
			$data->width = 32;
			$data->height = 32;
			$data->text = $myFileInfoO->filename;
			$data->align = 'middle';
			$html .= WPage::renderBluePrint( 'image', $data );
		}
		return $html;
	}
	private function _getFileURL($thumbnail=false) {
		if ( !isset($this->_myNewImageO) ) $this->_myNewImageO = WObject::get( 'files.file' );
		$this->_myNewImageO->name = $this->_myFileInfoO->name;
		$this->_myNewImageO->type = $this->_myFileInfoO->type;
		$this->_myNewImageO->folder = ( empty($this->_myFileInfoO->folder) ? 'media' : $this->_myFileInfoO->folder );
		$this->_myNewImageO->basePath = JOOBI_URL_MEDIA;
		$this->_myNewImageO->path = $this->_myFileInfoO->path;
		$this->_myNewImageO->fileID = $this->_myFileInfoO->filid;
		$this->_myNewImageO->thumbnail = $thumbnail;
		$this->_myNewImageO->storage = $this->_myFileInfoO->storage;
		$this->_myNewImageO->secure = $this->_myFileInfoO->secure;
		return $this->_myNewImageO->fileURL( $thumbnail );
	}
	private function _audio() {
		if ( empty( $this->_myFileInfoO ) ) return '';
		if ( $this->_myFileInfoO->type == 'url' ) {
			$path = $this->_myFileInfoO->name;
		} else {
			$path = $this->_getFileURL();
		}
				$useJWPlayer = WPref::load( 'PFILES_NODE_JWPLAYER' );
				if ( $useJWPlayer ) {
			$mediaScriptsT = WType::get( 'files.scriptsjwplayer' );
			if ( self::$loadJMPlayer ) {
								WPage::addJSFile( 'main/mediaplayer/jwplayer/jwplayer.js', 'inc' );
				$useJWPlayerKey = PFILES_NODE_JWPLAYERKEY;
				if ( !empty($useJWPlayerKey) ) WPage::addJS( 'jwplayer.key="'.$useJWPlayerKey.'";' );
				self::$loadJMPlayer = false;
			}
			$videoScript = $mediaScriptsT->getName( $this->_myFileInfoO->type );
			self::$IDCount++;
			$tags = array();
			$tags['{NAME}'] = $this->_myFileInfoO->name;
			$tags['{ID}'] = $this->_myFileInfoO->name . '-' . self::$IDCount;
			$tags['{SITEURL}'] = JOOBI_SITE;
			$tags['{URLINC}'] = JOOBI_URL_INC;
			$tags['{TEXTLOADING}'] = WText::t('1352764977FLOB');
$this->audioHeight = 70;			
			$tags['{TRANSPARENCY}'] = $this->transparency;
			$tags['{WIDTH}'] = $this->audioWidth;
			$tags['{HEIGHT}'] = $this->audioHeight;
			$tags['{BACKGROUND}'] = $this->bgcolor;
			$tags['{AUTOPLAY}'] = $this->autoplay;
			$tags['{FILESOURCE}'] = $path;
			$tags['{FILEID}'] = $this->_myFileInfoO->filid;
			$tags['{MEDIAID}'] = $this->_myFileInfoO->name;
			$tags['{BACKGROUNDQT}'] = $this->bgcolor;
						$HTML = str_replace( array_keys($tags), array_values($tags), $videoScript );
		} else {
			$browser = WPage::browser( 'namekey' );
			if ( $browser != 'msie' ) {
				$mediaPlayer = JOOBI_URL_INC . 'main/mediaplayer/audioPlayer.swf';
				$HTML = '<object classid="audioPlayer" width="'. $this->audioWidth .'" height="' . $this->audioHeight .'" id="flashContent">
					 <param name="movie" value="'.$mediaPlayer.'" />
					 <param name="FlashVars" value="playerID=1&noinfo=yes&bg=ffffff&soundFile=' . $path . '" />
					 <param name="wmode" value="transparent" />
					 <!--[if !IE]>-->
						<object type="application/x-shockwave-flash" data="'.$mediaPlayer.'" width="177" height="20">
						<param name="FlashVars" value="playerID=1&noinfo=yes&bg=ffffff&soundFile=' . $path . '" />
						<param name="wmode" value="transparent" />
					<!--<![endif]-->
					<a href="' . $path . '" class="text_bodytiny">Play Audio</a>
					<!--[if !IE]>-->
					</object>
					<!--<![endif]-->
					</object>';
			} else {
				$HTML = '<embed height="' . $this->videoHeight . 'px" width="' . $this->videoWidth . 'px" src="' . $path . '" />';
			}
		}
		return $HTML;
	}
	function iconImages($type) {			if ( empty($type) ) $type= '';
		$iconTypes = WType::get( 'files.icontypes');
		$image = $iconTypes->getName($type);
		if (empty($image)) $image = 'default';
		$iconImg = JOOBI_URL_JOOBI_IMAGES . 'mime/' . $image . '.png';
		return $iconImg;
	}
	function uploadDirectory($uploadFiles) {
		$message = WMessage::get();
		 if (empty($uploadFiles->directory)){
		 	 $message->historyN('1315887069HOQD');
		 	 return true;
		 }
		 $uploadFiles->directory = JOOBI_DS_ROOT . $uploadFiles->directory . DS;
		 		 $uploadFiles->directory =  preg_replace('#[/\\\\]+#', DS, $uploadFiles->directory);
		 $found = strpos($uploadFiles->extension, '.');
		 if ($found){
		 	$uploadFiles->filename=$uploadFiles->extension;
		 	$uploadFiles->extension = '';
		 }
		 $uploadFiles->extension = $uploadFiles->extension ? explode( ',', $uploadFiles->extension ) : array();
		 $folder = WGet::folder();
		 $filenames = array();
		 if ( !empty($uploadFiles->filename) ) $filenames[0] = $uploadFiles->filename;
		 else $filenames = $folder->files($uploadFiles->directory);
		 $path =  $uploadFiles->secure ?  '': 'download';
		 $folders = $uploadFiles->secure ? 'safe' : 'media';
		 $directoryto = JOOBI_DS_USER . $folders . DS . $path;
				if ( ! $folder->exist($directoryto) ) {
			if ( ! $folder->create($directoryto) ) {
				$message->adminE( 'Unable to create folders' );
				return true;
			}		}
		$files = WGet::file();
		$filesM = WModel::get('files');
		$status = false;
		$messageA = array();
		$count = 0;
		$timeout = WTools::increasePerformance();
		$newtimeout = $timeout - ($timeout / 10);
		$time = time();
		$timeCheck = $time + $newtimeout;
		if ( $filenames ) {
			for( $n=0; $n<count($filenames); $n++ ) {
				$fileSpecificName = $uploadFiles->directory . $filenames[$n];
				$ext = strtolower( substr( $fileSpecificName, (strrpos($fileSpecificName, '.'))+1 ) );
				if ( in_array($ext , $uploadFiles->extension) || empty($uploadFiles->extension)) {
					$source = $uploadFiles->directory . $filenames[$n];
					if ( !file_exists($source) ) {
						$message->adminN('The file '.$source.' doesn\'t exist');
						$status = false;
						continue;
					}
					$filename = $filenames[$n];
					$destination = $directoryto . DS . $filename;
					$filesM = WModel::get('files');
					if (!empty($uploadFiles->filid))	$filesM->filid = $uploadFiles->filid;
					$filesM->alias = (!empty($uploadFiles->name))? $uploadFiles->name: $filename;
					$filesM->type = $ext;
					$filesM->thumbnail = 0;
					$filesM->secure = $uploadFiles->secure;
					$filesM->_name = $filename;
					$filesM->_tmp_name = $source;
					$filesM->_error = 0;
					$filesM->_size = $files->size($source);
					$filesM->_path =  $path;
					$filesM->_folder = $folders;
					$filesM->_format = WPref::load( 'PITEM_NODE_DWLDFORMAT' );						$filesM->_maxSize = WPref::load( 'PITEM_NODE_DWLDMAXSIZE' ) * 1028;	
					$imageExt = array('jpg', 'jpeg', 'png', 'gif');
					$filesM->_fileType = (in_array($ext, $imageExt))? 'images': 'files';
					if ($uploadFiles->keepfile) $filesM->_copy = true; 					else $filesM->_uploadFile = false; 
					$filesM->returnId();
					$status = $filesM->save();
					if ( $status ) {
						if ( $count < 10 ) {
							if ( !empty($uploadFiles->showUploadedFiles) ) {
								$messageA[$count]= 'Successfully Uploaded '.$filesM->name. '.' .$ext;
							}						}						$count++;
					}
					$time = time();
					if ($time >= $timeCheck) {
						$message->adminN('Upload Stopped because the session is about to expire.');
						$message->adminN('Stopped at '.$filesM->name. '.' .$ext);
						break;
					}
				}			}
			if ($status){
				$message->userS('1315887070LGQT',array('$count'=>$count));
				if ($count < 10){
					if (!empty($messageA)){
						foreach($messageA as $msg){
							$message->userS($msg);
						}					}				}			}			else {
				if ($count == 0){
					$text = implode(' , ', $uploadFiles->extension);
					if (empty($text))$message->adminN('No Files on directory');
					else $message->adminN('No Files found that have '.$text.' extension');
				}else $message->adminN('There\'s a problem on uploading the files.');			}		} else {
			$message->adminN('No Files on directory');
		}
		return $status;
	}
}