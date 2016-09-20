<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Theme_class extends WClasses {
public $header=null;
public $hasImage=false; 
public $nodeName='';public $layoutPrefix='';
private static $countID=0;
private static $rowNb=0;
private static $columnNb=0;
public function createLayout(&$productA,$tagParams=null){
static $count=0;
if(empty($productA)) return '';
$count++;
if(empty($this->nodeName) && !empty($tagParams->nodeName))$this->nodeName=$tagParams->nodeName;
if(!empty($tagParams->imageWidth) || !empty($tagParams->imageHeight))$this->hasImage=true;
if(!empty($tagParams->layoutname))$tagParams->layout=$tagParams->layoutname;
if(empty($tagParams->layout)){
$html=$this->_createBadge($productA, $tagParams );
}else{
$typeLayout=strtolower(substr($tagParams->layout, 0, 5 ));
if($typeLayout=='badge'){
$html=$this->_createBadge($productA, $tagParams );
}elseif($typeLayout=='table'){
$html=$this->_createTable($productA, $tagParams );
}else{$html=$this->_createBadge($productA, $tagParams );
}}
if(!empty($tagParams->showAllLink)){
$objButtonO=WPage::newBluePrint('prefcatalog');
if(!empty($objButtonO->version) && $objButtonO->version > 1){
$objButtonO->type='showAllLink';
$objButtonO->html=$tagParams->showAllLink;
$html .=WPage::renderBluePrint('prefcatalog',$objButtonO );
}else{
$html .='<div class="showAll">'.$tagParams->showAllLink.'</div>';
}}
return $html;
}
private function _createBadge(&$productA,$tagParams=null){
self::$rowNb=0;
self::$columnNb=0;
self::$countID=0;
if(!empty($tagParams->organizeTree )){
$productA=$this->_processSubCategories($productA );
}
if(!empty($tagParams->layoutNbColumn)){
if($tagParams->layoutNbColumn==1){
$percent=98;
}else{
$dived=( 100 - ($tagParams->layoutNbColumn * 2.8 )) / $tagParams->layoutNbColumn;
$percent=round($dived * 0.98, 1 );}$tagParams->containerStyle=' style="width:'.$percent.'%;"';
}
if(empty($tagParams->layoutNbColumn) || $tagParams->layoutNbColumn < 2)$tagParams->layoutNbColumn=1;
if($tagParams->layoutNbColumn > 12)$tagParams->layoutNbColumn=12;
$indice=floor( 12 / $tagParams->layoutNbColumn );
if($indice > 6)$tagParams->containerClass='col-xs-'.$indice;
elseif($indice >=4)$tagParams->containerClass='col-xs-12 col-sm-6 col-md-'.$indice;
else $tagParams->containerClass='col-xs-12 col-sm-6 col-md-3 col-lg-'.$indice;
$html='';
if(empty($tagParams->display))$tagParams->display='vertical';
$data=WPage::newBluePrint('badge');
$data->type=$tagParams->display;
$data->nodeName=$this->nodeName;
$data->hasImage=$this->hasImage;
$data->itemsA=$productA;
$tagParams->layoutPrefix=$this->layoutPrefix;
$tagParams->nodeName=$this->nodeName;
$tagParams->header=$this->header;
switch($tagParams->display){
case 'vertical':
case 'horizontal':
default:
$className='separatorStandard';
break;
}
if( count($productA ) < 2){
$tagParams->display='normalSeparator';
}
$data->params=$tagParams;
$data->class=$className;
$html=WPage::renderBluePrint('badge',$data );
return $html;
}
private function _createTable(&$productA,$tagParams=null){
$tagParams->layoutPrefix=$this->layoutPrefix;
$tagParams->nodeName=$this->nodeName;
$tagParams->header=$this->header;
if($this->hasImage && empty($tagParams->showNoImage)){
$newProducA=array();
foreach($productA as $product){
Output_Theme_class::setImageSize($product, $tagParams, $tagParams->widgetSlugID );
$newProducA[]=$product;
}
}else{
$newProducA=$productA;
}
$html=Output_Theme_class::callTheme($newProducA, $tagParams, true);
return $html;
}
public static function setImageSize(&$product,$tagParams,$tagID,$globalRatio=1){
static $imageSize=array();
if(!empty($tagParams->imageWidth) || !empty($tagParams->imageHeight)){
$product->imageWidth=$product->originWidth;
$product->imageHeight=$product->originHeight;
}
if(!isset($imageSize[$tagID])){
if(!empty($tagParams->imageWidth)){
$width=$tagParams->imageWidth;
}else{
$typeLayout=strtolower(substr($tagParams->layout, 0, 5 ));
if($typeLayout=='table'){
$width=40;
}elseif($typeLayout=='badge'){
$sizeLayout=strtolower(substr($tagParams->layout, 5, 3 ));
if($sizeLayout=='big'){
$width=150;
}elseif($sizeLayout=='min'){
$width=50;
}else{
$width=100;
}}else{
$width=30;
}}
if(!empty($tagParams->imageHeight)){
$height=$tagParams->imageHeight;
}else{
$typeLayout=strtolower(substr($tagParams->layout, 0, 5 ));
if($typeLayout=='table'){
$height=40;
}elseif($typeLayout=='badge'){
$sizeLayout=strtolower(substr($tagParams->layout, 5, 3 ));
if($sizeLayout=='big'){
$height=150;
}elseif($sizeLayout=='min'){
$height=50;
}else{
$height=100;
}}else{
$height=30;
}}
$objSize=new stdClass;
$objSize->width=$width;
$objSize->height=$height;
$imageSize[$tagID]=$objSize;
}
$width=$imageSize[$tagID]->width;
$height=$imageSize[$tagID]->height;
$product->imageWidth=(!empty($product->originWidth))?$product->originWidth : $width;
$product->imageHeight=(!empty($product->originHeight))?$product->originHeight : $height;
if($product->imageHeight > $height){
$ratio=$height / $product->imageHeight;
$product->imageWidth=defined('PHP_ROUND_HALF_DOWN')?round($product->imageWidth * $ratio, 0, PHP_ROUND_HALF_DOWN ) : round($product->imageWidth * $ratio, 0 );
$product->imageHeight=$height;
}
if($product->imageWidth > $width){
$ratio=$width / $product->imageWidth;
$product->imageHeight=defined('PHP_ROUND_HALF_DOWN')?round($product->imageHeight * $ratio, 0, PHP_ROUND_HALF_DOWN ) : round($product->imageHeight * $ratio, 0 );
$product->imageWidth=$width;
}
if($globalRatio > 1){
$product->imageWidth=( 1 - $globalRatio * 0.15 ) * $product->imageWidth;
$product->imageHeight=( 1 - $globalRatio * 0.15 ) * $product->imageHeight;
}
return;
}
public function mouseOverParams(&$tagParams){
static $loadMeB=true;
if(!empty($tagParams->mouseOver )){
$mouseOVerA=WTools::preference2Array($tagParams->mouseOver );
if(!empty($mouseOVerA)){
$newMouseOverO=new stdClass;
foreach($mouseOVerA as $oneMouseOver){
$newMouseOverO->$oneMouseOver=true;
}
$tagParams->mouseOver=$newMouseOverO;
if($loadMeB){
WPage::addJSLibrary('jquery');
WPage::addJSFile('js/vignette.js');
}
}
}
}
public static function callTheme($product,$tagParams,$isTable=false){
$usedLayout='';
if(!empty($tagParams->layoutPrefix))$usedLayout .=$tagParams->layoutPrefix;
$usedLayout .=$tagParams->layout;
if( is_object($product)){
if(!empty($tagParams->containerStyle))$product->containerStyle=$tagParams->containerStyle;
else $product->containerStyle='';
}
if(empty($tagParams->themeType))$tagParams->themeType='node';
if(!in_array($tagParams->themeType, array('node')))$tagParams->themeType='node';
$theme=WPage::theme($tagParams->themeType.'-'. $usedLayout );
if(empty($product->classSuffix))$theme->setContent('classSuffix', !empty($tagParams->classSuffix)?$tagParams->classSuffix : '');
$theme->setContent('containerClass', !empty($tagParams->containerClass)?$tagParams->containerClass : '');
if(!isset($tagParams->nodeName)){
$message=WMessage::get();
$message->codeE('The nodeName is not defined in your class!');
return '';
}
$theme->type=49;
$productID=WExtension::get($tagParams->nodeName.'.node','wid');
$theme->wid=$productID;
if($isTable){
$theme->setContent('elements',$product );
if(!empty($tagParams->header))$theme->setContent('header',$tagParams->header );
}
if(!empty($tagParams->widgetSlugID)){
if(!empty($tagParams->layoutNbColumn)){
if( self::$countID % $tagParams->layoutNbColumn){
self::$columnNb++;
}else{
self::$rowNb++;
self::$columnNb=1;
}
self::$countID++;
$tagParams->uniqueID=WView::generateID($tagParams->widgetSlugID, self::$rowNb.'_'.self::$columnNb );
}else{
$tagParams->uniqueID=WView::generateID($tagParams->widgetSlugID, 'ct_'.self::$countID );
}
}else{
if(!empty($tagParams->widgetSlug)){
if(!empty($tagParams->widgetID )){
$tagParams->uniqueID=WView::generateID($tagParams->widgetSlug . $tagParams->widgetID, 'ct_'.self::$countID );
}}
}
$theme->setContent('elementParams',$tagParams );
$theme->file=$usedLayout.'.php';
$theme->htmlfile=true;
$html=$theme->display($product );
static $CSSExist=array();
$folder=!empty($path)?$path : $tagParams->nodeName;
$key=$tagParams->nodeName.'-'.$tagParams->themeType;
if(!isset($CSSExist[$key] )){
$file=WGet::file();
$themeHTMLPath=JOOBI_DS_THEME_JOOBI . $tagParams->themeType .DS . $folder.DS.'css'.DS.'style.css';
if($file->exist($themeHTMLPath )){
$CSSExist[$key]=$file->read($themeHTMLPath );if($tagParams->themeType=='node'){
WPage::addCSSFile('node/'.$folder.'/css/style.css');}else{
WPage::addCSSFile('tag/'.$folder.'/css/style.css');}
}else{
$CSSExist[$key]=false;
}
}
return $html;
}
private function _processSubCategories($categoryA){
if(empty($categoryA)) return $categoryA;
$parentMain=$categoryA[0]->parent;
$orginazedCategoryA=array();
foreach($categoryA as $cat){
$orginazedCategoryA[$cat->parent][]=$cat;
}
return $orginazedCategoryA;
}
}