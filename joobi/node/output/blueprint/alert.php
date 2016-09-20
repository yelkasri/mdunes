<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WRender_Alert_blueprint extends Theme_Render_class {
  public function render($newArraySort){
  $extraSound='';
$html='<div id="WMessage">';
foreach($newArraySort as $type=> $allMessages){
if(empty($allMessages)) continue; 
if($type=='beep'){
if( WPref::load('PLIBRARY_NODE_ENABLESOUND')){
$browser=WPage::browser('namekey');
$extension=($browser=='safari' || $browser=='msie')?'mp3' : 'ogg';
$URLBeep=PLIBRARY_NODE_CDNSERVER.'/joobi/user/media/sounds/'.$allMessages[0]->message.'.'. $extension;
$extraSound .='<audio autoplay="true" src="'.$URLBeep.'" preload="auto" autobuffer></audio>';
}
continue;
}
switch ($type){
case 'success':
$alertClas='success';
$faicon='fa-thumbs-up';
break;
case 'error':
$alertClas='danger';
$faicon='fa-times';
break;
case 'warning':
$alertClas='warning';
$faicon='fa-exclamation-triangle';
break;
case 'notice':
case 'text':
default:
$alertClas='info';
$faicon='fa-exclamation-circle';
break;
}
$childMsgs='';
$alertCollapse=$this->value('alert.collapse');
$alertDismiss=$this->value('alert.dismiss');
$count=0;
foreach($allMessages as $oneM){
$count++;
$msgid='msg_'.rand(0,99). '_'.$count;
$onClick=' onclick="'.JOOBI_JS_APP_NAME.'.flip(\''.$msgid.'\', 1);"';
if(!is_string($oneM->message))$oneM->message='<pre>'.var_dump($oneM->message, true). '</pre>';
if(!empty($oneM->variable)){
$oneM->message=str_replace( array_keys($oneM->variable), array_values($oneM->variable), $oneM->message );
}
$oneM->message=str_replace('<a ','<a class="alert-link" ',$oneM->message );
$HMessage=( JOOBI_FRAMEWORK !='netcom')?$oneM->message : 'XML-RPC SERVER MESSAGE: '.$oneM->message;
$childMsgs .='<div class="alert alert-'.$alertClas.'';
if($alertDismiss){
$childMsgs .=' alert-dismissable';
}
$childMsgs .='">';
if($alertDismiss)$childMsgs .='<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
if($alertCollapse){
$childMsgs .='<dl class="dl-horizontal">';
$childMsgs .='<dt>';
$childMsgs .='<i class="fa '.$faicon.' fa-lg messageIcons"'.$onClick.'></i>';
$childMsgs .='</dt>';
$childMsgs .='<dd>';
}
$childMsgs .='<div id="'.$msgid.'" class="message-contain">';
$childMsgs .=$HMessage;
$childMsgs .='</div>';
if($alertCollapse){
if( JOOBI_FRAMEWORK !='netcom'){
$childMsgs .='<div id="msg'.$msgid.'" class="message-flip" style="display:none;" '.$onClick.'>';
$MESSAGETYPE=$type;
$childMsgs .=str_replace(array('$MESSAGETYPE'), array($MESSAGETYPE),WText::t('1299166217IYHU'));
$childMsgs .='</div>';
}
$childMsgs .='</dd>';
$childMsgs .='</dl>';
}
$childMsgs .='</div>';
}
$html .=$childMsgs;
}
$html .=$extraSound;
$html .='</div>';
return $html;
  }
}