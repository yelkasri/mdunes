<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WRender_Socialicons_classData {
public $type='';
public $text='';
public $link='';
public $count=0;
public $onClick='';
public $color='primary';
public $wrapper=true;
}
class WRender_Socialicons_blueprint extends Theme_Render_class {
private static $_showText=null;
private static $_showCount=null;
private static $_size=null;
private static $_color=null;
private static $_group=null;
private $_noButton=false;
  public function render($data){
  if(!isset( self::$_group )) self::$_group=$this->value('socialicon.group');
  if( is_string($data))return self::$_group;
  if(empty($data->type)){
  $this->codeE('The type of social icon is not specified!');
  return '';
  }
  if(!isset(self::$_showText)){
  self::$_showText=$this->value('socialicon.showtext');
  self::$_showCount=$this->value('socialicon.showcount');
  self::$_size=$this->value('socialicon.size');
  self::$_color=$this->value('socialicon.color');
  }
  if(!empty($data->noButton) && self::$_group)$this->_noButton=true;
  $extraHTML='';
  switch($data->type){
  case 'favorite':
  $data->color=$this->value('socialicon.colorfavorite');
  $html=$this->_renderIcons($data, 'fa-heart');
  break;
  case 'wish':
  $data->color=$this->value('socialicon.colorwish');
  $html=$this->_renderIcons($data, 'fa-gift');
  break;
  case 'watch':
  $data->color=$this->value('socialicon.colorwatch');
  $html=$this->_renderIcons($data, 'fa-eye');
  break;
  case 'like':
  $data->color=$this->value('socialicon.colorlike');
  $html=$this->_renderIcons($data, 'fa-thumbs-up');
  break;
  case 'dislike':
  $data->color=$this->value('socialicon.colordislike');
  $html=$this->_renderIcons($data, 'fa-thumbs-down');
  break;
  case 'views':
  $data->color=$this->value('socialicon.colorviews');
  $html=$this->_renderIcons($data, 'fa-globe');
  break;
  case 'sharewall':
  $data->color=$this->value('socialicon.colorsharewall');
  $data->type='link';
  $html=$this->_renderIcons($data, 'fa-share-square-o');
  break;
  case 'print':
  $data->color=$this->value('socialicon.colorprint');
  $data->type='link';
  $html=$this->_renderIcons($data, 'fa-print');
  break;
  case 'email':
$data->type='link';
  $data->color=$this->value('socialicon.coloremail');
  $html=$this->_renderIcons($data, 'fa-envelope');
  break;
  case 'likeDislike':
$html='<div class="btn-group">'.$data->text.'</div>';
  break;
  case 'facebook':
  $html='';
static $alreadyDoneFB=false;
static $hasCredentialFB=false;
if(!$alreadyDoneFB){
$extraHTML='<div id="fb-root"></div>';
static $appID=null;
if(!isset($appID)){
$mainCredentialsC=WClass::get('main.credentials');
$appID=$mainCredentialsC->loadFromType('facebook','username');
}
if(empty($appID )){
$fb=WPage::getHTTP(). 'joobi.info/facebook-api';
$FACEBOOKLINK='<a href="'.$fb.'" target="_blank">credentials menu</a>';
$this->adminW('Facebook share require an App ID, please enter your App ID in the '.$FACEBOOKLINK.'.');
}else{
$hasCredentialFB=true;
}
if($hasCredentialFB){
$lang=WLanguage::get( WUser::get('lgid'), 'code');
if( strlen($lang) > 2){
$language=str_replace('-','_',$lang );
}else{
$language=$lang.'_'.strtoupper($lang );
}
WPage::addScript( WPage::getHTTP(). 'connect.facebook.net/en_US/all.js');
$js='
FB.init({appId: \''.$appID.'\',status:true,cookie:true,xfbml:true});';
WPage::addJSLibrary('jquery');
WPage::addJSScript($js );
$alreadyDoneFB=true;
}
}
if($hasCredentialFB){
static $FBcount=0;
$FBcount++;
$id='facebook_button'.$FBcount;
$js='
jQuery("#facebook_button'.$FBcount.'").on("click", function (e){
e.preventDefault();
FB.ui({
method: "feed",
name: "'.$data->itemName.'",
link: "'.$data->itemURL.'",
caption: "'.$data->itemName.'",
description: "'.$data->itemName.'",
message: ""
});
});';
WPage::addJSScript($js );
$data->color=$this->value('socialicon.colorfacebookshare');
$data->type='link';
$data->id=$id;
$data->text='Facebook';
$data->target='_parent';
$data->link='javascript: void(0);';
$html .=$this->_renderIcons($data, 'fa-facebook');
}
  break;
  case 'twitter':
  $extraHTML='<div id="tw-root"></div>';
  WPage::addJSFile('//platform.twitter.com/widgets.js','none','//platform.twitter.com/widgets.js', false);
$data->color=$this->value('socialicon.colortwittershare');
$data->type='link';
$data->id='twitter_button';
$data->text='Twitter';
$data->target='_parent';
$data->link='http://twitter.com/intent/tweet?url='.$data->itemURL.'&amp;text='.urlencode($data->itemName ). '&amp;count=none';
$html=$this->_renderIcons($data, 'fa-twitter');
  break;
  case 'googleplus':
  $extraHTML='<div id="googleplus-root"></div>';
$data->color=$this->value('socialicon.colortgoogleshare');
$data->type='link';
$data->id='googleplus_button';
$data->text='Google+';
$data->target='_parent';
$data->onClick='javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;';
$data->link='https://plus.google.com/share?url='.$data->itemURL;
$html=$this->_renderIcons($data, 'fa-google-plus');
  break;
  case 'addthis':
WPage::addScript('//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-522e10a348d020ee');
$html='<a href="http://www.addthis.com/bookmark.php" class="addthis_button" style="text-decoration:none;"><img src="http://s7.addthis.com/static/btn/sm-plus.gif" width="16" height="16" border="0" alt="Share" /> '.$data->text.'</a>';
  break;
  default:
  $html='';
  break;
  }
  if(!empty($data->noButton)){
  return $html;
  }
  if(!self::$_group && $data->wrapper){
  if( self::$_showText){
  if(isset($data->id) && !empty($data->id))$html='<div id='.$data->id.' class="socialText">'.$html.'</div>';
  else $html='<div class="socialText">'.$html.'</div>';
  }else{
  $html='<div class="socialIcon">'.$html.'</div>';
  }
  }
  WGlobals::set('socialIconsExtraHTML',$extraHTML, 'global');
return $html;
  }
  private function _renderIcons($data,$icons){
  $button=WPage::newBluePrint('button');
  $button->type=($data->type=='link'?'link' : 'button');
  $button->icon=$icons;
  if(!empty($data->title))$button->title=$data->title;
  $button->color=(!empty(self::$_color)?self::$_color : $data->color );
  if(!empty(self::$_size))$button->size=self::$_size;
  if(!empty($data->id))$button->id=$data->id;
  if(!empty($data->target))$button->target=$data->target;
  if(!empty($data->onClick))$button->linkOnClick=$data->onClick;
  if(!empty($data->link))$button->link=$data->link;
  if(!empty($data->extraClasses))$button->extraClasses=$data->extraClasses;
  if(!empty($data->extraTags))$button->extraTags=$data->extraTags;
  if(!empty($data->popUpIs)){
  $button->popUpIs=true;
  if(!empty($data->popUpWidth))$button->popUpWidth=$data->popUpWidth;
  if(!empty($data->popUpHeight))$button->popUpHeight=$data->popUpHeight;
  }
  if( self::$_showText){
  if(!empty($data->text)){
  $button->text=$data->text;
  if( self::$_showCount && !empty($data->count) && $data->count > 0){
  $button->text .=' <span class="badge">'. $data->count .'</span>';
  }
  }
  }else{
  if( self::$_showCount && !empty($data->count) && $data->count > 0){
  $button->text=$data->count;
  }
  }
  if($this->_noButton){
  $returnHTML='';
  if(!empty($button->icon))$returnHTML .='<i class="fa '.$button->icon.'"></i>';
  return $returnHTML . $button->text;
  }
  $html=WPage::renderBluePrint('button',$button );
  return $html;
  return $html;
  }
}