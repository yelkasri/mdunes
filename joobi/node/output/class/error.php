<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Output_Error_class {
public function manageMissingTheme(){
$THEMENAME=WView::getDefaultTheme();
if(empty($THEMENAME)){
$myMess=WText::t('1398304276OSSP');
$this->_messageExit($myMess );
}else{
$myMess='<br/>';
$myMess .=str_replace(array('$THEMENAME'), array($THEMENAME),WText::t('1398304276OSSQ'));
$myMess .='<br/>';
$myMess .=WText::t('1398304276OSSR');
$myMess .='<br/>';
$myMess .='<br/>';
$themeM=WModel::get('theme');
$type=( IS_ADMIN?2 : 1 );
$themeM->whereE('type',$type );
$themeM->whereE('publish', 1 );
$total=$themeM->total();
if($total > 1){
$themeM->whereE('type',$type );
$themeM->whereE('publish', 1 );
$themeM->orderBy('premium','DESC');
$currentThemeO=$themeM->load('lr','tmid');
$themeM->whereE('type',$type );
$themeM->where('tmid','!=',$currentThemeO );
$themeM->whereE('publish', 1 );
$themeM->orderBy('premium','DESC');
$possibleNewThemeO=$themeM->load('lr','tmid');
if(!empty($possibleNewThemeO)){
$themeM->whereE('type',$type );
$themeM->setVal('premium', 0 );
$themeM->update();
$themeM->whereE('tmid',$possibleNewThemeO );
$themeM->setVal('premium', 1 );
$themeM->update();
$cache=WCache::get();
$cache->resetCache();
$myMess .=WText::t('1398304276OSSS');
$myMess .='<br/>';
}
}
$this->_messageExit($myMess );
}
return false;
}
private function _messageExit($myMess){
echo $myMess;
$myMess .='<br/>';
$myMess .='<br/>';
$myMess .=WText::t('1398304276OSST');
$myMess .='<br/>';
WMessage::log('theme is missing','theme-missing');
exit;
}
}
