<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WRender_Badge_classData {
public $type='default';
public $itemsA=array();
public $params=null;
public $class='';
public $nodeName='';
public $hasImage=false; 
}
class WRender_Badge_blueprint extends Theme_Render_class {
private static $countID=0;
private static $rowNb=0;
private static $columnNb=0;
public $nodeName=null;
public $hasImage=null;
public function render($data){
if(empty($data->params->widgetSlugID)){
static $ctID=0;
$ctID++;
$data->params->widgetSlugID=$data->params->widgetSlug.'_'.$ctID;
}
$this->nodeName=$data->nodeName;
$this->hasImage=$data->hasImage;
if(empty($data->params->display)){
$data->params->display='vertical';
$data->type='vertical';
}
if(empty($data->params->layout))$data->params->layout='badge';
switch($data->params->layout){
case 'badgebig':
$data->params->layoutClass='ListView';
break;
case 'badgemini':
$data->params->layoutClass='MiniView';
break;
case 'table':
$data->params->layoutClass='TableView';
break;
case 'badge':
default:
$data->params->layoutClass='GridView';
break;
}
if(!empty($data->params->mouseOver->secondimage) && !empty($data->params->mouseOver->imagezoomeffect)){
unset($data->params->mouseOver->imagezoomeffect);
}elseif(!empty($data->params->mouseOver->imagezoomeffect)){
$this->_zoomEffect();
}
$html='';
switch($data->type){
case 'carrousel':
if(empty($data->params->layout) || $data->params->layout=='default')$data->params->layout='badgebig';
$html=$this->_displayCarrousel($data->itemsA, $data->params, 'carrousel');
$html='<div class="carrouselPanel">'.$html.'</div>';
break;
case 'slider':
if(empty($data->params->layout) || $data->params->layout=='default')$data->params->layout='badgemini';
$html=$this->_displayCarrousel($data->itemsA, $data->params, 'slider');
break;
case 'accordion-vertical':if(empty($data->params->layout) || $data->params->layout=='default')$data->params->layout='badgebig';
$html=$this->_displayAccordionVertical($data->itemsA, $data->params );
break;
case 'accordion-horizontal':if(empty($data->params->layout) || $data->params->layout=='default')$data->params->layout='badgebig';
$html=$this->_displayAccordionHorizontal($data->itemsA, $data->params );
break;
case 'vertical':
case 'horizontal':
default:
if(empty($data->params->layout) || $data->params->layout=='default')$data->params->layout='badge';
if(empty($data->params->display))$data->params->display='vertical';
switch($data->params->display){
case 'vertical':
case 'horizontal':
default:
$data->class='separatorStandard';
break;
}
if(!empty($data->params->organizeTree )){
$html=$this->_renderSubCategoryBadge($data->itemsA, $data->params, $data->class );
}else{
$html=$this->_renderStandardBadge($data->itemsA, $data->params, $data->class );
}
$extraClass=(!empty($data->params->mouseOver->imagezoomeffect)?' class="zoomEffect"' : '');
$html='<div id="'.$data->params->widgetSlugID.'"'.$extraClass.'>'.$html.'</div>';
break;
}
return $html;
}
private function _renderStandardBadge($productA,$tagParams,$className=''){
if(!empty($tagParams->classSuffix))$className .=' '.$tagParams->classSuffix;
$html='<div class="displayGrid '.$tagParams->layoutClass.'">';
$html .='<div class="container-fluid"><div class="row">';
$totalItems=count($productA);
$count=1;
foreach($productA as $key=> $product){
$product->nb=$count;
if(empty($tagParams->showNoImage)) Output_Theme_class::setImageSize($product, $tagParams, $tagParams->widgetSlugID );$html .=Output_Theme_class::callTheme($product, $tagParams );
if(!empty($tagParams->layoutNbColumn)){
if($count % $tagParams->layoutNbColumn==0 && $totalItems !=$count){
$html .='<div class="clearfix"></div>';
}}$count++;
}
$html .='</div></div>';
$html .='</div>';
return $html;
}
private function _displayCarrousel($productsA,$tagParams,$typeDisplay='carrousel'){
static $alreadyLoaded=array();
static $keyIndex=1;
if(!defined('T3_TEMPLATE')) WPage::addJSFile('js/extrascript.js');
if(!empty($tagParams->layoutNbColumn) || !empty($tagParams->layoutNbRow)){
if(empty($tagParams->layoutNbColumn) || $tagParams->layoutNbColumn < 1)$tagParams->layoutNbColumn=1;
if(empty($tagParams->layoutNbRow) || $tagParams->layoutNbRow < 1)$tagParams->layoutNbRow=1;
$toalItems=$tagParams->layoutNbColumn * $tagParams->layoutNbRow;
$slidesofItemsA=array();
$count=0;
$index=0;
foreach($productsA as $oneProduct){
$count++;
$slidesofItemsA[$index][]=$oneProduct;
if($count >=$toalItems){
$index++;
$count=0;
}}
$HTMLSlidesA=array();
foreach($slidesofItemsA as $oneSlides){
$HTMLSlidesA[]=$this->_renderStandardBadge($oneSlides, $tagParams );
}}
if(!empty($tagParams->widgetSlugID )){
$key=$tagParams->widgetSlugID;
}else{
$key=$this->nodeName.'-'.$keyIndex;
$keyIndex++;
$typeDisplayAgain=($typeDisplay=='carrousel')?'Carrousel' : 'Slider' ;
$key .=$typeDisplayAgain;
}
$key=str_replace('|','',$key );
$alreadyLoaded[$key]=true;
$carrouselSpeed=WPref::load('PCATALOG_NODE_CARROUSELSPEED');
$html='';
$active=' active';
$indicatorsHTML='';
$indice=0;
if(!empty($HTMLSlidesA)){
$productsA=$HTMLSlidesA;
$showSlidesB=true;
}else{
$showSlidesB=false;
}
$count=0;
foreach($productsA as $product){
$html .='<div class="item'.$active.'">';
if($showSlidesB){
$html .=$product;
}else{
$count++;
$product->nb=$count;
if($this->hasImage && empty($tagParams->showNoImage)) Output_Theme_class::setImageSize($product, $tagParams, $tagParams->widgetSlugID );
$html .=Output_Theme_class::callTheme($product, $tagParams );
}$html .='</div>';
$indicatorsHTML .='<li data-target="#'.$key.'" data-slide-to="'.(string)$indice.'"';
if(!empty($active))$indicatorsHTML .=' class="active"';
$indicatorsHTML .='></li>';
$indice++;
$active='';
}
if(!empty($tagParams->showtitle ))$tagParams->showTitle=$tagParams->showtitle;
if(empty($tagParams->showTitle)){
$controlHTML='<a class="left carousel-control" href="#'.$key.'" data-slide="prev">
<span class="fa fa-chevron-left"></span>
</a>
<a class="right carousel-control" href="#'.$key.'" data-slide="next">
<span class="fa fa-chevron-right"></span>
</a>';
}else{
$colorBtn=(!empty($catalogCarrouselControlColor)?' btn-'.$catalogCarrouselControlColor : '');
$catalogCarrouselControlColor=$this->value('catalog.carrouselcontrolcolor');
$controlHTML='<div class="controls pull-right hidden-xs">
<a class="fa fa-chevron-left btn'.$colorBtn.'" data-slide="prev" href="#'.$key.'"></a>
<a class="right fa fa-chevron-right btn'.$colorBtn.'" data-slide="next" href="#'.$key.'"></a>
</div>';
}
$indicatorsHTML='<ol class="carousel-indicators">'.$indicatorsHTML.'</ol>';
$extraClass=(!empty($tagParams->mouseOver->imagezoomeffect)?' zoomEffect' : '');
$finalHTML='<div class="carousel slide" data-ride="carousel" id="'.$key.'">';$finalHTML .='<div class="carousel-inner">';
$finalHTML .=$html;
$finalHTML .='</div>';
if(empty($tagParams->showTitle) && !empty($tagParams->carrouselArrow ))$finalHTML .=$controlHTML;
$finalHTML .='</div>';
if(!empty($tagParams->showtitle )){
WGlobals::set('widgetBoxHeaderRight',$controlHTML, 'global');
WGlobals::set('widgetBoxClass','carrouselWidget','global');
return '<div id="'.$key.'Panel" class="displayCarrousel '.$tagParams->layoutClass . $extraClass.'">'.$finalHTML.'</div>';
}
$data=WPage::newBluePrint('panel');
$data->id=$key.'Panel';if(!empty($tagParams->faicon))$data->faicon=$tagParams->faicon;
if(!empty($tagParams->color))$data->color=$tagParams->color;
if(!empty($tagParams->showTitle))$data->header=$tagParams->title;
if(!empty($tagParams->showTitle))$data->headerRightA[]=$controlHTML;
$data->class='carrouselWidget';
$data->body='<div class="displayCarrousel '.$tagParams->layoutClass . $extraClass.'">'.$finalHTML.'</div>';
return WPage::renderBluePrint('panel',$data );
}
private function _displayAccordionVertical($productsA,$tagParams){
static $count=0;
if(!empty($productsA)){
if(empty($tagParams->id)){
if(!empty($tagParams->idLabel))$tagParams->id=$tagParams->idLabel;
else $tagParams->id='acordion'.time();
}
WLoadFile('blueprint.frame', JOOBI_DS_NODE.'output'.DS );
WLoadFile('blueprint.frame', JOOBI_DS_THEME_JOOBI );
WLoadFile('blueprint.frame.sliders', JOOBI_DS_THEME_JOOBI );
$tagParams->animate=true;
$tagParams->delay=2500;
$frame=new WPane_sliders($tagParams );
$frame->startPane($tagParams );
$count=0;
foreach($productsA as $product){
if(is_array($product))$product=array_shift($product );
$count++;
$product->nb=$count;
$frame->startPage($tagParams );
if($this->hasImage && empty($tagParams->showNoImage)) Output_Theme_class::setImageSize($product, $tagParams, $tagParams->widgetSlugID );
$html=Output_Theme_class::callTheme($product, $tagParams );
$frame->content=$html;
$paramO=new stdClass;
$paramO->text=$product->name;
if(empty($paramO->parent))$paramO->parent=$tagParams->id;
$frame->endPage($paramO );
}
$html=$frame->endPane($tagParams );
}else{
$html='';
}
$extraClass=(!empty($tagParams->mouseOver->imagezoomeffect)?' zoomEffect' : '');
if(empty($tagParams->showTitle)){
return '<div id="'.$tagParams->widgetSlugID.'" class="accordionVertical'.$extraClass.'">'.$html.'</div>';
}else{
$data=WPage::newBluePrint('panel');
$count++;
$data->id='accordionVertical'.$count;
if(!empty($tagParams->faicon))$data->faicon=$tagParams->faicon;
if(!empty($tagParams->color))$data->color=$tagParams->color;
if(!empty($tagParams->showTitle))$data->header=$tagParams->title;
$data->body='<div class="accordionVertical '.$tagParams->layoutClass . $extraClass.'">'.$html.'</div>';
return WPage::renderBluePrint('panel',$data );
}
}
private function _displayAccordionHorizontal($productsA,$tagParams){
static $loadedJS=null;
if(!$loadedJS){
$browser=WPage::browser();
if($browser->name=='msie'){
WPage::addCSSScript('div#itemAccordeonWrapper div.itemAccordeonTitleWrapper{text-indent:15px;left:17px;width:265px;height:36px!important;top:-8px!important;}');
}
WPage::addJSLibrary('jquery');
WPage::addCSSFile('css/accordion-horizontal.css');
WPage::addJSFile('js/accordion-horizontal.js');
$loadedJS=true;
}
$key=$this->nodeName;
if(isset($alreadyLoaded[$key])){
$key=$this->nodeName.'-'.$keyIndex;
$keyIndex++;
}
$height=230;
$width=600;
$subHeight=$height-67;
$subWidth=$width-83;
$numberPanes=count($productsA);
$alreadyLoaded[$key]=true;
$JScode='var $jq=jQuery.noConflict();';
$JScode .='var itemAccordeonmodule_counter="'.$numberPanes.'";';
$JScode .='var itemAccordeonurl="'. JOOBI_URL_THEME_JOOBI .'images/accordion/";';
$JScode .='var itemAccordeonspeed="900";';
$JScode .='var itemAccordeontransition="3500";';
$JScode .='var itemAccordeoncycle="yes";';
$JScode .='var itemAccordeondef_slide="1";';
$JScode .='$jq(function(){
var itemWidth=jQuery(\'#itemAccordeonWrapperWidth\').width();
var itemSlideWidth=itemWidth - (( itemAccordeondef_slide - 1) * 40);
var itemSusWidth=itemWidth - 141;
var num=itemAccordeondef_slide;
if(num=="1"){
itemAccordeonopen(1);
}else{
if(document.getElementById("itemAccordeon"+num)){
eval("itemAccordeonopen("+num+");");
}
}
$jq("div.accordeonMain").addClass("accordeonMain-js");
$jq("div#itemAccordeonWrapper").addClass("itemAccordeonWrapper-js");
$jq("div#itemAccordeonWrapper").css("width", itemWidth+\'px\');
$jq("div.accordeonMain").css("width", itemSlideWidth+\'px\');
$jq("div.itemAccordeonContent").css("width", itemSusWidth+\'px\');
window.setTimeout("itemAccordeonrotate_slides()",itemAccordeontransition);
});';
WPage::addJSFile('main/js/jquery_easing.js','inc');
WPage::addJSScript($JScode, 'default', false);
$height=$height.'px';
$width=$width.'px';
$html='<div style="display:none">';
foreach($productsA as $product)$html .='<img alt="icon" src="'.JOOBI_URL_THEME_JOOBI .'images/accordion/slide.png">';
$html .='</div>';
$extraCSS=(empty($tagParams->showTitle)?'margin-bottom:20px;' : '');
$html .='<div id="itemAccordeonWrapperWidth" style="width:100%;"></div><div id="itemAccordeonWrapper" onclick="itemAccordeondisable()" onblur="itemAccordeonenable()" style="border-top:none; border-left:none; border-bottom:none; border-right:none; padding:0px; margin:0px; height:'.$height.'; width:'.$width.';'.$extraCSS.'">';
$slideWidth=$width - (($numberPanes - 1 ) * 40 );
$i=0;
foreach($productsA as $product){
if(is_array($product))$product=array_shift($product );
$i++;
$product->nb=$i;
$html .='<div id="itemAccordeon'.$i.'" onclick="itemAccordeonopen('.$i.')" class="accordeonMain" style="padding:0px; margin:0px; height:'. $height .'px;">';
$html .='<div id="accordeonLinkWrap'.$i.'" style="width:40px; height:'.$height.';">';
$html .='<div style="margin:0px; height:'.$subHeight.'px;" class="itemAccordeonBody">';
$html .='<div style="margin:0px" class="itemAccordeonTitleWrapper">';
$html .=str_replace(' ','&nbsp;',$product->name );
$html .='</div></div>';
$html .='</div>';
$html .='<div class="itemAccordeonContent" style="margin:0px; width:'.$subWidth.'px;">';
if($this->hasImage && empty($tagParams->showNoImage)) Output_Theme_class::setImageSize($product, $tagParams, $tagParams->widgetSlugID );
$html .=Output_Theme_class::callTheme($product, $tagParams );
$html .='</div></div>';
}
$html .='</div>';
$extraClass=(!empty($tagParams->mouseOver->imagezoomeffect)?' zoomEffect' : '');
if(empty($tagParams->showTitle)){
return '<div id="'.$tagParams->widgetSlugID.'" class="accordionHorizontal'.$extraClass.'">'.$html.'</div>';
}else{
$data=WPage::newBluePrint('panel');
$data->id='accordionHorizontal';
if(!empty($tagParams->faicon))$data->faicon=$tagParams->faicon;
if(!empty($tagParams->color))$data->color=$tagParams->color;
if(!empty($tagParams->showTitle))$data->header=$tagParams->title;
$data->body='<div class="accordionHorizontal '.$tagParams->layoutClass . $extraClass.'">'.$html.'</div>';
return WPage::renderBluePrint('panel',$data );
}
}
private function _renderSubCategoryBadge($productA,$tagParams,$className){
if(!empty($tagParams->megaMenu)){
WPage::addCSSFile('node/catalog/css/megamenu.css');
$tagParams->subCatStyle=1;
$classType='catPopover';
}
if(!isset($tagParams->subCatStyle))$tagParams->subCatStyle=0;
$classType='';
switch($tagParams->subCatStyle){
case 5:
$classType='catCollapse';
break;
case 7:
$classType='catTree';
break;
case 0:
$classType='catGrid';
case 1:
default:
if(empty($classType))$classType='catPopover';
break;
}
$mainParent=key($productA);
$initialLevel=$productA[$mainParent][0]->depth;
$firstLevelA=$productA[$mainParent];
unset($productA[$mainParent] );
$reversedA=array_reverse($productA, true);
$childHMTLA=array();
$tagParamsChild=clone $tagParams;
$tagParamsChild->containerClass='categoryChild '.$classType;
$tagParamsChild->borderShow=false;
$tagParamsChild->borderColor='none';
$nb=0;
$colNbClass='';
foreach($reversedA as $manySubProduct){
$htmlSubCategory='<div>';
if(!empty($tagParams->megaMenuColumn)){
if($tagParams->megaMenuColumn > 6)$tagParams->megaMenuColumn=6;
$boostrapColumn=floor( 12 / $tagParams->megaMenuColumn );
$tagParams->colNbClass=' colNb'.$tagParams->megaMenuColumn;
}
$countElmenent=0;
$addRow=false;
foreach($manySubProduct as $oneProduct){
$oneProduct->level=$oneProduct->depth - $initialLevel;
$tagParamsChild->containerClass='subCat '.'categoryChild '.$classType.' subCat'.$oneProduct->level;
$nb++;
$oneProduct->nb=$nb;
if($classType=='catTree' || $classType=='catCollapse'){
$oneProduct->isSubCategory=true; }
$oneProduct->subCategoryStyle=$classType;
if(isset($childHMTLA[$oneProduct->catid])){
$htmlChild=$childHMTLA[$oneProduct->catid];
unset($childHMTLA[$oneProduct->catid] );
}else{
$htmlChild='';
}
if(empty($oneProduct->htmlChild) && !empty($htmlChild)){
$oneProduct->htmlChild=$htmlChild;
if($classType=='catCollapse'){
$oneProduct->addSpanIcon=true;
}}
if(!empty($tagParams->megaMenuColumn) && 1==$oneProduct->level){
$countElmenent++;
if( 1==$countElmenent){
$addRow=true;
$htmlSubCategory .='<div class="row">';
}
$tagParamsChild->containerClass .=' col-md-'.$boostrapColumn;
}
if(empty($tagParams->showNoImage)) Output_Theme_class::setImageSize($oneProduct, $tagParamsChild, $tagParams->widgetSlugID, $oneProduct->depth - $initialLevel + 1 );
$htmlSubCategory .=Output_Theme_class::callTheme($oneProduct, $tagParamsChild );
if(!empty($tagParams->megaMenuColumn) && 1==$oneProduct->level){
if($countElmenent==$tagParams->megaMenuColumn){
$countElmenent=0;
$addRow=false;
$htmlSubCategory .='</div>';
}}
if(isset($childHMTLA[$oneProduct->catid])){
$htmlSubCategory .=$childHMTLA[$oneProduct->catid];
}
}
if($addRow){
$addRow=false;
$htmlSubCategory .='</div>';
}
$htmlSubCategory .='</div>';
$childHMTLA[$oneProduct->parent]=$htmlSubCategory;
}
if(!empty($tagParams->classSuffix))$className .=$tagParams->classSuffix;
$extraClass=(!empty($tagParams->display) &&  'menu'==$tagParams->display )?' menu' : '';
$html='';
if(!empty($tagParams->megaMenu)){
if(!empty($tagParams->menuExpand)){
$html .='<div class="megaMenuWrapper">'; }else{
$html .='<div class="megaMenuWrapper expandLeft">'; }
$html .='<div class="megaMenu"><i class="fa fa-bars"></i> '.WText::t('1206732411EGQO'). '</div>'; WPage::addJSFile('node/catalog/js/megamenu.js'); $html .='<div class="displayCategory '.$tagParams->layoutClass.'">';
}else{
$html .='<div class="displayCategory '.$tagParams->layoutClass.'">';
}
$html .='<div class="container-fluid"><div class="row">';
$totalItems=count($initialLevel);
$count=4;
$styleApplied=(!empty($tagParams->containerStyle)?$tagParams->containerStyle : '');$styleApplied='';
unset($tagParams->containerStyle );
foreach($firstLevelA as $key=> $product){
if(empty($tagParams->showNoImage)) Output_Theme_class::setImageSize($product, $tagParams, $tagParams->widgetSlugID );
if(isset($childHMTLA[$product->catid])){
$htmlChild=$childHMTLA[$product->catid];
}else{
$htmlChild='';
}
$styleAppliedClass=(!empty($htmlChild)?$styleApplied.' class="parentMenu"' : $styleApplied );
if(empty($product->htmlChild) && !empty($htmlChild))$product->htmlChild=$htmlChild;
if(!empty($classType) && $classType=='catPopover'){
if(!empty($product->htmlChild )){
$product->htmlID='PopOver_';
$product->subClass=true;
}WPage::addJSFile('js/popover.js');
}elseif(!empty($classType) && $classType=='catCollapse'){
$product->addAnotherClass=true;
if(!empty($product->htmlChild)){
$product->subCollapse=true;
}
WPage::addJSFile('js/collapse.js');
}
$product->subCategoryStyle=$classType;
if(!isset($product->subCategoryStyle))$product->subCategoryStyle='';
$parentCat=Output_Theme_class::callTheme($product, $tagParams );
$html .=$parentCat;
$count++;
}
$html .='</div>';
$html .='<div class="clearfix"></div>';
$html .='</div>';
$html .='</div>';
if(!empty($tagParams->megaMenu)){
$html .='</div>';
}
return $html;
}
private function _zoomEffect(){static $onlyOnce=false;
if($onlyOnce ) return true;
$css='.zoomEffect .siteImage img:hover{';
$css .='transform:scale(1.15)rotate(-1.5deg);
-webkit-transform:scale(1.1)rotate(-1.5deg);
-moz-transform:scale(1.1)rotate(-1.5deg);
-ms-transform:scale(1.1)rotate(-1.5deg);
-o-transform:scale(1.1)rotate(-1.5deg);}';
WPage::addCSS($css );
$onlyOnce=true;
}
}