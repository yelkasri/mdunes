<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WRender_Prefcatalog_classData {
public $type='buttonAddToCart';
public $version=2;
}
class WRender_Prefcatalog_blueprint extends Theme_Render_class {
  public function render($data){
  static $count=0;
  $html='';
  $objButtonO=WPage::newBluePrint('button');
  foreach($data as $key=> $value)$objButtonO->$key=$value;
  $type2Use=(!empty($data->typeReplacement)?$data->typeReplacement : $data->type );
  $count++;
  if(empty($objButtonO->id))$objButtonO->id='btnid'.$count;
  switch($type2Use){
case 'buttonViewCartInCatalogPage':
$objButtonO->type='infoLink';
$objButtonO->icon=$this->value('catalog.carticon');
$objButtonO->size=$this->value('catalog.cartsize');
$objButtonO->color=$this->value('catalog.cartcolor');
$html=WPage::renderBluePrint('button',$objButtonO );
  break;
  case 'buttonAddToCartInCatalogPage':
$objButtonO->type='infoLink';
$objButtonO->size=$this->value('catalog.cartsize');
$objButtonO->color=$this->value('catalog.cartcolor');
$html=WPage::renderBluePrint('button',$objButtonO );
  break;
  case 'buttonReviewInCatalogPage':
$objButtonO->type='infoLink';
$objButtonO->icon=$this->value('catalog.reviewicon');
$objButtonO->size=$this->value('catalog.reviewsize');
$objButtonO->color=$this->value('catalog.reviewcolor');
$html=WPage::renderBluePrint('button',$objButtonO );
  break;
 case 'buttonQuestionInCatalogPage':
$objButtonO->type='infoLink';
$objButtonO->size=$this->value('catalog.questionsize');
$objButtonO->color=$this->value('catalog.questioncolor');
$html=WPage::renderBluePrint('button',$objButtonO );
  break;
  case 'buttonAddToCartInItemPage':
$objButtonO->type='infoLink';
$objButtonO->icon=$this->value('catalog.addcarticon');
$objButtonO->size=$this->value('catalog.addcartsize');
$objButtonO->color=$this->value('catalog.addcartcolor');
$html=WPage::renderBluePrint('button',$objButtonO );
  break;
  case 'buttonDetailsInCatalogPage':
$objButtonO->type='infoLink';
$objButtonO->icon=$this->value('catalog.detailicon');
$objButtonO->size=$this->value('catalog.detailsize');
$objButtonO->color=$this->value('catalog.detailcolor');
$html=WPage::renderBluePrint('button',$objButtonO );
  break;
  case 'buttonViewAllInCatalogPage':
$objButtonO->type='infoLink';
$objButtonO->icon=$this->value('catalog.viewallicon');
$objButtonO->size=$this->value('catalog.viewallsize');
$objButtonO->color=$this->value('catalog.viewallcolor');
$html=WPage::renderBluePrint('button',$objButtonO );
  break;
  case 'buttonBasketUpdateCart':
$objButtonO->icon=$this->value('catalog.cartupdateicon');
$objButtonO->size=$this->value('catalog.cartupdatesize');
$objButtonO->color=$this->value('catalog.cartupdatecolor');
$html=WPage::renderBluePrint('button',$objButtonO );
  break;
  case 'buttonBasketCheckoutPrevious':
$objButtonO->icon=$this->value('catalog.cartpreviousicon');
$objButtonO->size=$this->value('catalog.cartprevioussize');
$objButtonO->color=$this->value('catalog.cartpreviouscolor');
$objButtonO->iconPosition=$this->value('catalog.cartpreviousiconposition');
$html=WPage::renderBluePrint('button',$objButtonO );
  break;
  case 'buttonBasketCheckoutNext':
$objButtonO->icon=$this->value('catalog.cartnexticon');
$objButtonO->size=$this->value('catalog.cartnextsize');
$objButtonO->color=$this->value('catalog.cartnextcolor');
$objButtonO->iconPosition=$this->value('catalog.cartnexticonposition');
$html=WPage::renderBluePrint('button',$objButtonO );
  break;
  case 'buttonVendorsRegister':
$objButtonO->type='infoLink';
$objButtonO->icon=$this->value('catalog.vendorsregistericon');
$objButtonO->size=$this->value('catalog.vendorsregistersize');
$objButtonO->color=$this->value('catalog.vendorsregistercolor');
$html=WPage::renderBluePrint('button',$objButtonO );
  break;
  case 'buttonViewMap':
$objButtonO->type='infoLink';
$objButtonO->icon=$this->value('catalog.viewmapicon');
$objButtonO->size=$this->value('catalog.viewmapsize');
$objButtonO->color=$this->value('catalog.viewmapcolor');
$html=WPage::renderBluePrint('button',$objButtonO );
  break;
  case 'buttonEditAddress':
$objButtonO->type='infoLink';
$objButtonO->icon=$this->value('catalog.editaddressicon');
$objButtonO->size=$this->value('catalog.editaddresssize');
$objButtonO->color=$this->value('catalog.editaddresscolor');
$html=WPage::renderBluePrint('button',$objButtonO );
  break;
  case 'itemAddPhoto':
$objButtonO->type='button';
if($this->value('toolbar.icon'))$objButtonO->icon='fa-download';
$objButtonO->size=$this->value('catalog.editaddresssize');
if($this->value('toolbar.color')){
$objButtonO->color='info';
}else{
$objButtonO->color='default';
}
$html=WPage::renderBluePrint('button',$objButtonO );
  break;
  case 'showAllLink':
  $html='<div class="showAll">'.$data->html.'</div>';
  break;
  case 'addJS4Stars':
$changeStar='window.onload=function(){document.getElementById("jrate_rats").value=\'\';};';
$changeStar .='function changeStar(val){
document.getElementById("jrate_rats").value=val;
for(var x=1;x<='.$data->maxStar.';x++)
{
if(x <=val){name="fa-star";}
else{name="fa-star-o";}
document.getElementById("star_"+x).className="fa " + name + " text-warning fa-lg"
}
return false;
}';
WPage::addJS($changeStar, 'text/javascript', true);
  break;
  default:
  break;
  }
  return $html;
  }
}
