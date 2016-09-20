<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WRender_Menustandard_blueprint extends Theme_Render_class {
private $_roleHelper=null;
private $_menuHTMLleftA=array();
private $_menuHTMLrightA=array();
private $_wid=0;
private $_navShowIcon=null;
private $_outputLinkC=null;
  public function render($data){
  static $count=0;
  $navUseLogo=$this->value('nav.uselogo');
  $navBrand=$this->value('nav.brand');
  $navLogoName=$this->value('nav.logoname');
  $this->_navShowIcon=$this->value('nav.showicon');
  $this->_hint=$this->value('hint.remove');
  $this->_wid=$data->wid;
    $parentA=array('pkey'=>'mid','parent'=>'parent','name'=>'name');
  $childOrderParent=array();
$data->elements=WOrderingTools::getOrderedList($parentA, $data->elements, 2, false, $childOrderParent );
$parentOrderedListA=array();
foreach($data->elements as $oneMenu){
$parentOrderedListA[$oneMenu->parent][]=$oneMenu;
}$parentOrderedListA=array_reverse($parentOrderedListA, true);
$this->_outputLinkC=WClass::get('output.link');
$this->_roleHelper=WRole::get();
$menuLeft='';
  $menuRight='';
foreach($parentOrderedListA as $parent=> $menuSetA){
$this->_createMenus($menuSetA, $menuLeft, $menuRight, $parent );
$this->_menuHTMLleftA[$parent]=$menuLeft;
$this->_menuHTMLrightA[$parent]=$menuRight;
}
$menu='';
if(!empty($this->_menuHTMLleftA[0])){
$menu .='<ul class="nav navbar-nav">';
$menu .=$this->_menuHTMLleftA[0];
$menu .='</ul>';
}if(!empty($this->_menuHTMLrightA[0])){
$menu .='<ul class="nav navbar-nav navbar-right">';
$menu .=$this->_menuHTMLrightA[0];
$menu .='</ul>';
}
$html='<nav class="navbar navbar-default" role="navigation">';
$html .='<div class="container-fluid">';
$count++;
$id='zHozMenu'.$count;
if($navUseLogo){
if( IS_ADMIN){
$brand='<i class="fa app-joobi-logo"></i>';WPage::addCSSFile('fonts/app/css/app.css');
}else{
if('app-joobi-logo'==$navLogoName){
WPage::addCSSFile('fonts/app/css/app.css');
}$brand='<i class="fa '.$navLogoName.'"></i>';
}}else{
$brand=$navBrand;
}
$html .='<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#'.$id.'">
<span class="sr-only">'.WText::t('1397594079OVQR'). '</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="#">'.$brand.'</a>
</div>';
$html .='<div class="collapse navbar-collapse" id="'.$id.'">';
$html .=$menu;
$html .='</div>';$html .='</div>';$html .='</nav>';
return $html;
  }
  private function _createMenus($menuSetA,&$menuLeft,&$menuRight,$parent=0){
  $menuLeft='';
  $menuRight='';
foreach($menuSetA as $oneMenu){
if(!$this->_roleHelper->hasRole($oneMenu->rolid )) continue;
WTools::getParams($oneMenu );
if(!empty($oneMenu->requirednode)){
$nodeExist=WExtension::exist($oneMenu->requirednode );
if(empty($nodeExist)) continue;
}
if(!empty($oneMenu->bfloat) && 'right'==$oneMenu->bfloat){
$menuRight .=$this->_makeOneLink($oneMenu );
}else{
$menuLeft .=$this->_makeOneLink($oneMenu );
}
}
if(!empty($parent)) return;
if(!( WRoles::isNotAdmin('manager') && ! WPref::load('PLIBRARY_NODE_WIZARDFE'))){
$oneMenu=new stdClass;
$oneMenu->name=WText::t('1206732391QBUR');
$oneMenu->type=17;
$oneMenu->mid='btnWzrd';
$oneMenu->action='controller=output&task=wizard';
$oneMenu->faicon='fa-magic';$menuRight .=$this->_makeOneLink($oneMenu );
}
return;
  }
  private function _makeOneLink($oneMenu){
  $menu='';
if( 102==$oneMenu->type){
$buttonObject=Output_Mlinks_class::loadButtonFile($oneMenu );
if( null===$buttonObject){
$obj=null;
return $obj;
}
$buttonObject->initialiseMenu($this->_data );
$status=$buttonObject->make();
if(false===$status ) return '';
foreach($buttonObject as $key=> $val)$oneMenu->$key=$val;
}
  if($this->_navShowIcon && !empty($oneMenu->faicon )){
  $oneMenu->name='<i class="fa '.$oneMenu->faicon.'"></i>'.$oneMenu->name;
  }
  $target='';
$this->_outputLinkC->wid=$this->_wid;
if(!isset($oneMenu->level))$oneMenu->level=0;
$level=WGlobals::getCandy();
if($oneMenu->level > $level){
$oneMenu->action='';
$oneMenu->name .=' <span class="label label-warning">'.WApplication::renderLevel($oneMenu->level, false). '</span>';
}
if(!empty($oneMenu->action)){
if( 102==$oneMenu->type){
if(!empty($oneMenu->link)){
$link=$oneMenu->link;
}elseif( strpos($oneMenu->action, 'index.php') !==false){
$link=$oneMenu->action;
}else{
if( substr($oneMenu->action, 0, 11 ) !='controller=')$oneMenu->action='controller='.$oneMenu->action;
$link=$this->_outputLinkC->convertLink($oneMenu->action, '', null );
}}elseif( 55==$oneMenu->type){
$link=$oneMenu->action;
$target=' target="_blank"';
}else{
if( substr($oneMenu->action, 0, 11 ) !='controller=')$oneMenu->action='controller='.$oneMenu->action;
$link=$this->_outputLinkC->convertLink($oneMenu->action, '', null );
}
}else $link='#';
  if(!empty($this->_menuHTMLleftA[$oneMenu->mid]) || !empty($this->_menuHTMLrightA[$oneMenu->mid])){
  if($oneMenu->indentTreeNumber < 1){
    $menu .='<li class="dropdown"><a'.$target.' href="'.$link.'" class="dropdown-toggle"';
    $menu .=' data-toggle="dropdown"';
  $menu .='>';
  $menu .=$oneMenu->name;
  $menu .='<b class="caret"></b></a><ul class="dropdown-menu">';
  if(!empty($this->_menuHTMLleftA[$oneMenu->mid]))$menu .=$this->_menuHTMLleftA[$oneMenu->mid];
  if(!empty($this->_menuHTMLrightA[$oneMenu->mid]))$menu .=$this->_menuHTMLrightA[$oneMenu->mid];
  $menu .='</ul></li>';
  }else{
  $menu .='<li class="dropdown-submenu"><a'.$target.' href="'.$link.'">';
  if(!empty($oneMenu->description)){
  $menu .=$oneMenu->name;
  }else{
  $menu .=$oneMenu->name;
  }  $menu .='</a><ul class="dropdown-menu">';
  if(!empty($this->_menuHTMLleftA[$oneMenu->mid]))$menu .=$this->_menuHTMLleftA[$oneMenu->mid];
  if(!empty($this->_menuHTMLrightA[$oneMenu->mid]))$menu .=$this->_menuHTMLrightA[$oneMenu->mid];
  $menu .='</ul></li>';
  }
  }else{
  $menu .='<li>';
  $noLink=(empty($link) || '#'==$link?' class="noLink"' : '');
  $menu .='<a'.$target . $noLink .  ' href="'.$link.'">';
  if(!$this->_hint && !empty($oneMenu->description)){
  $menu .='<span>'.$oneMenu->name.'</span>';
  $menu .='<span class="hint">'.$oneMenu->description.'</span>';
  }else{
  $menu .=$oneMenu->name;
  }  $menu .='</a>';
  $menu .='</li>';
  }
  return $menu;
  }
}