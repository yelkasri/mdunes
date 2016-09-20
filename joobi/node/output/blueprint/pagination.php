<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WRender_Pagination_blueprint extends Theme_Render_class {
  public function render($data){
  if(empty($data->pageNumbersA)) return '';
$pageLinks='';
  foreach($data->pageNumbersA as $onePage){
  $pageLinks .=$this->_oneLink($onePage );
  }
  $paginationHTMLMAIN='<ul';
  if( defined('T3_TEMPLATE'))$paginationHTMLMAIN .=' class="pagination"';
  $paginationHTMLMAIN .='>'.$pageLinks.'</ul>';
if(!empty($data->pageOf) && 'wordpress' !=JOOBI_FRAMEWORK_TYPE)$paginationHTML='<p class="counter">'.$data->pageOf.'</p>';
else $paginationHTML='';
if( APIPage::isRTL()){
$html=$paginationHTMLMAIN . $paginationHTML;
}else{
$html=$paginationHTML . $paginationHTMLMAIN;
}
$return='<div class="pagination">'.$html.'</div>';
$return='<div class="pagination-wrap">'.$return.'</div>';
return $return;
  }
  private function _oneLink($onePage){
  if(empty($onePage)) return '';
$addtionalClass=(!empty($onePage->class)?' class="pagination-'.$onePage->class.'"' : '');
if(!empty($onePage->current)){
$html=WGet::$rLine.'<li'.$addtionalClass.'><span class="pagenav">'.$onePage->text.'</span></li>';
return $html;
}else{
$return=WGet::$rLine.'<li'.$addtionalClass.'><a';
$return .=' class="pagenav"';
if(!empty($onePage->linkOnClick))$return .=' onclick="'.$onePage->linkOnClick.'"';
$return .=' href="#">'.$onePage->text.'</a></li>';
return $return;
$link='<span class="pagiCenter"><a href="#" class="page" title="'.$onePage->text .
'" onclick="'.$onePage->linkOnClick.'">'.$onePage->text.'</a></span>';
$test='<span class="pagiCenter">'.$onePage->text.'</span>';
if(empty($onePage->off))$onePage->off='';
$return='<span class="'.$onePage->classTwo. $onePage->off .'"><span class="'.$onePage->classOne.'">';
$return .=( trim($onePage->off )=='disabled')?$test : $link;
$return .='</span></span>';
}
return $return;
}
}
