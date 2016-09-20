<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WRender_Menupanel_blueprint extends Theme_Render_class {
  public function render($data){
  WPage::addCSSFile('css/menudashboard.css');
$commonCSS=array();
$html='<div class="clearfix">';
foreach($data->elements as $oneIcon){
if(!empty($oneIcon->parent)) continue;
if( 102==$oneIcon->type){
$buttonObject=Output_Mlinks_class::loadButtonFile($oneIcon );
if( null===$buttonObject){
continue;
}
$buttonObject->initialiseMenu($data );
$status=$buttonObject->make();
if(false===$status ) continue;
}
WTools::getParams($oneIcon );
if(!empty($oneIcon->requirednode)){
$nodeExist=WExtension::exist($oneIcon->requirednode );
if(empty($nodeExist)) continue;
}
if(empty($oneIcon->faicon))$oneIcon->faicon='fa-question';
if(!empty($data->nestedView)){
if( substr($oneIcon->action, 0, 1 )=='/'
|| substr($oneIcon->action, 0, 4 )=='http'
|| ( JOOBI_INDEX !='' && substr($oneIcon->action, 0, strlen( JOOBI_INDEX ))== JOOBI_INDEX )
){
$link=WPage::link($oneIcon->action );
}else{
$link=WPage::link('controller='.$oneIcon->action );
}
}else{
$link=WPage::routeURL('controller='.$oneIcon->icon, '', false, false, false, $oneIcon->icon );
}
$html .='<div style="float:left;">
<div class="icon">
<a href="'.$link.'">
<i class="fa '.$oneIcon->faicon.' fa-4x"></i>
<span>'.$oneIcon->name.'</span>
</a>
</div>
</div>';
}
$html .='</div>';
return $html;
  }
}