<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Output_Rating_class extends WClasses {
public $type=1;
public function createHTMLRating(&$form,$onlyShow=false){
if(!isset($this->rating)) return false;
if(!$onlyShow && !isset($this->restriction) && !isset($this->rateController)) return false;
if(!isset($this->option))$this->option=0;
if(!isset($this->colorPref))$this->colorPref='yellow';
if(!isset($this->maxNbStar))$this->maxNbStar=5;
if(!isset($this->picWidth))$this->picWidth=22.4;
if($this->colorPref=='mixed')$this->colorPref='red';
if($this->option==1 && !isset($this->primaryId)) return false;
$picWidth=$this->picWidth;
$maxNbStar=$this->maxNbStar;
$color=$this->colorPref;
$ratingO=new stdClass;
$ratingO->type=$this->type;
$ratingO->maxNbStar=$this->maxNbStar;
$ratingO->makeHalfStar=(isset($this->makeHalfStar)?$this->makeHalfStar : true);
$ratingO->rating=$this->rating;
$ratingO->colorPref=$this->colorPref;
$ratingO->picWidth=$this->picWidth;
$ratingO->rateController=(!empty($this->rateController)?$this->rateController : '');
$ratingO->primaryId=(!empty($this->primaryId)?$this->primaryId : 0 );
$ratingO->restriction=(!empty($this->restriction)?$this->restriction : '');
$myHtml=WPage::renderBluePrint('rating',$ratingO );
if(empty($myHtml)){
$myHtml=$this->_colourC();
}
if($this->type==1){
$jsStar=new stdClass;
$jsStar->type='addJS4Stars';
$jsStar->color=$this->colorPref;
$jsStar->maxStar=$this->maxNbStar;
WPage::renderBluePrint('prefcatalog',$jsStar );
$myHtml.='<input style="display:none;" type="text" id="jrate_rats" name="'.$form->map.'" value=""></input>';
}elseif(!empty($form->map))$myHtml.='<input style="display:none;" type="text" id="jrate_rats" name="'.$form->map.'" value="'.$this->rating.'"></input>';
return $myHtml;
}
private function _colourC(){
$this->maxNbStar=$this->maxNbStar * 1;
$this->makeHalfStar=false;
$myHtml='';
$this->starRate=0;
for($i=1; $i <=$this->maxNbStar; $i++){
$condition=1;
if($i < $this->rating){
$this->starRate=$i;
$myHtml .=$this->_createStar();
}else{
$iHalfStar=$i-1;
$endFull=$i+0.25;
$iStart=$iHalfStar+0.25;
$iEnd=$iHalfStar+0.75;
if($iStart<=$this->rating && $this->rating<=$iEnd){
$this->makeHalfStar=true;
$this->starRate=$i;
$myHtml .=$this->_createStar();
$this->makeHalfStar=false;
$condition=0;
}elseif($iEnd < $this->rating && $this->rating<$endFull){
$this->starRate=$i;
$myHtml .=$this->_createStar();
$condition=0;
}
}
if($i>$this->rating && $condition==1){
$this->starRate=$i;
$myHtml .=$this->_createStar('white');
}
}
return $myHtml;
 }
private function _createStar($color=''){
$myHtml='';
if(empty($color))$color=$this->colorPref;
if($this->makeHalfStar==true){
$colorStar='star-half';
}else{
if($color=='white'){
$colorStar='star-blank';
}else{
$colorStar='star';
}
}
if(!defined('JOOBI_URL_THEME_JOOBI')) WView::definePath();
if($this->type==1){
$myHtml .='<a href="#" onClick="return changeStar('. $this->starRate .');">';
$iconO=WPage::newBluePrint('icon');
$iconO->location='star/';
$iconO->id='star_'.$this->starRate;
$iconO->icon=$colorStar;
$myHtml .=WPage::renderBluePrint('icon',$iconO );
$myHtml .='</a>';
}elseif($this->type==2){
$link=WPage::routeURL('controller='.$this->rateController.'&task=rate&starRate='.$this->starRate.'&primaryId='.$this->primaryId.'&restriction='.$this->restriction );
$myHtml .='<a href="'. $link .'">';
$iconO=WPage::newBluePrint('icon');
$iconO->location='star/';
$iconO->icon=$colorStar;
$myHtml .=WPage::renderBluePrint('icon',$iconO );
$myHtml .='</a>';
}else{
$iconO=WPage::newBluePrint('icon');
$iconO->location='star/';
$iconO->icon=$colorStar;
$myHtml .=WPage::renderBluePrint('icon',$iconO );
}
return $myHtml;
}
}