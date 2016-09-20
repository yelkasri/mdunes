<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Api_Joomla30_Content_addon {
public function processContent(&$content){
if( IS_ADMIN ) return ;
WGlobals::set('view','article');
$item=new stdClass;
$item->text=&$content;
$item->id=0;
$item->catid=0;
$app=JFactory::getApplication('site');
$params=$app->getParams();
JPluginHelper::importPlugin('content');
$dispatcher=JDispatcher::getInstance();
$limitstart=0;
$dispatcher->trigger('onContentPrepare',array('item.description', &$item, &$params, $limitstart));
$results=$dispatcher->trigger('onContentAfterTitle',array('item.description.', &$item, &$params, $limitstart));
if(empty($item->event))$item->event=new stdClass;
$item->event->AfterDisplayTitle=trim( implode("\n", $results));
$results=$dispatcher->trigger('onContentBeforeDisplay',array('item.description.', &$item, &$params, $limitstart));
$item->event->BeforeDisplayContent=trim(implode("\n", $results));
$results=$dispatcher->trigger('onContentAfterDisplay',array('item.description.', &$item, &$params, $limitstart));
$item->event->AfterDisplayContent=trim(implode("\n", $results));
}
public function replaceThemeTag($theme){
$matchesA=array();
$finalTheme=$theme;
if(preg_match_all('#<jdoc:include\ type="([^"]+)"(.*)\/>#iU',$theme, $matchesA))
{
$template_tags_first=array();
$template_tags_last=array();
for($i=count($matchesA[0]) - 1; $i >=0; $i--)
{
$type=$matchesA[1][$i];
$attribs=empty($matchesA[2][$i])?array() : JUtility::parseAttributes($matchesA[2][$i]);
$name=isset($attribs['name'])?$attribs['name'] : null;
if($type=='module' || $type=='modules')
{
$template_tags_first[$matchesA[0][$i]]=array('type'=> $type, 'name'=> $name, 'attribs'=> $attribs);
}
else
{
$template_tags_last[$matchesA[0][$i]]=array('type'=> $type, 'name'=> $name, 'attribs'=> $attribs);
}
}
$template_tags_first=array_reverse($template_tags_first);
if(!empty($template_tags_first)){
$allTagsA=array();
foreach($template_tags_first as $oneTag){
$allTagsA[]=$oneTag['name'];
}}
$attribs=null;
$toReplaceA=array();
foreach($matchesA[2] as $key=> $position){
$allA=JModuleHelper::getModules($allTagsA[$key] );
$htmlMod='';
if(!empty($allA)){
foreach($allA as $oneMod){
$htmlMod .=JModuleHelper::renderModule($oneMod, $attribs );
}}
$moduleTag=str_replace(' style="no"','',$position );$toReplaceA[$moduleTag]=$htmlMod;
}
if(!empty($toReplaceA)){
$finalTheme=str_replace('<jdoc:include type="modules" ',' ',$theme );
$finalTheme=str_replace(' style="no" />',' ',$finalTheme );
foreach($toReplaceA as $Akey=> $Aval)$finalTheme=str_replace($Akey, $Aval, $finalTheme );
}
}
return $finalTheme;
}
}