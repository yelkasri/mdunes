<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WRender_Menuview_blueprint extends Theme_Render_class {
private $_data=null;
private $_roleHelper=null;
private $_colorDefault='';
  public function render($data){
  $html='';
$this->_data=$data;
$toolbarGroup=$this->value('toolbar.group');
$this->_toolbarColor=$this->value('toolbar.color');
$this->_toolbarIcon=$this->value('toolbar.icon');
$this->_toolbarPosition=$this->value('toolbar.position');
$this->_toolbarIconSize=$this->value('toolbar.iconsize');
$this->_toolbarIconColored=$this->value('toolbar.iconcolored');
$this->_colorDefault=$this->value('toolbar.colordefault');
$this->_roleHelper=WRole::get();
$countBtn=count($data->elements);
if($toolbarGroup && $countBtn > 1)$html='<div class="btn-group">';
foreach($data->elements as $oneMenu){
  if(!$this->_roleHelper->hasRole($oneMenu->rolid )) continue;
  if($oneMenu->type==90){
    if($toolbarGroup)$html .='</div><div class="btn-group">';
  continue;
  }
  $html .=$this->_makeOneButton($oneMenu );
}if($toolbarGroup && $countBtn > 1)$html .='</div>';
$html='<div class="btn-toolbar" role="toolbar">'.$html.'</div>';
return $html;
  }
  private function _makeOneButton($oneMenu){
  static $menuFileA=array();
WTools::getParams($oneMenu );
if(!empty($oneMenu->requirednode)){
$nodeExist=WExtension::exist($oneMenu->requirednode );
if(empty($nodeExist)) return '';
}
  $objButtonO=WPage::newBluePrint('button');
if(!empty($oneMenu->themepref )){
$explodeA=explode('.',$oneMenu->themepref );
$objButtonO=WPage::newBluePrint('prefcatalog');
$objButtonO->type=$explodeA[1];
}else{
$objButtonO->type='button';}
  if($oneMenu->type > 1 && $oneMenu->type < 18 && (empty($oneMenu->faicon) || empty($oneMenu->color))){
$this->_predefinedButton($oneMenu );
  }
$buttonObject=Output_Mlinks_class::loadButtonFile($oneMenu );
if( null===$buttonObject){
$obj=null;
return $obj;
}
$buttonObject->initialiseMenu($this->_data );
$status=$buttonObject->make();
if(false===$status ) return null;
if(!empty($status) && true !==$status ) return $status;
foreach($buttonObject->buttonO as $key=> $val ) if(!isset($oneMenu->$key) || $oneMenu->$key !=$val)$oneMenu->$key=$val;
  $objButtonO->text=$oneMenu->name;
  $objButtonO->float='right';
  if($this->_toolbarColor){
if(!empty($oneMenu->color ))$objButtonO->color=$oneMenu->color;
  }
  if($this->_colorDefault)$objButtonO->colorDefault=$this->_colorDefault;
  if($this->_toolbarIconColored)$objButtonO->coloredIcon=true;
  if($this->_toolbarIconSize)$objButtonO->iconSize=$this->_toolbarIconSize;
  if($this->_toolbarIcon && !empty($oneMenu->faicon )){
$objButtonO->icon=$oneMenu->faicon;
  }else{
    }
    if(!empty($oneMenu->popheight) || !empty($oneMenu->popheight)){
  $oneMenu->isPopUp=true;
$objButtonO->popUpWidth=(!empty($oneMenu->popwidth)?$oneMenu->popwidth : '80%');
$objButtonO->popUpHeight=(!empty($oneMenu->popheight)?$oneMenu->popheight : '80%');
$objButtonO->popUpIs=true;
  }
if(!empty($buttonObject->link)){
$objButtonO->link=$buttonObject->link;$objButtonO->typeReplacement=$objButtonO->type;
$objButtonO->type='link';
}elseif(!empty($objButtonO->link)){
}elseif(!empty($oneMenu->isPopUp)){
$objButtonO->link=WPage::linkPopUp($oneMenu->action );
$objButtonO->popUpIs=true;
$objButtonO->popUpWidth=(!empty($oneMenu->popwidth)?$oneMenu->popwidth : '80%');
$objButtonO->popUpHeight=(!empty($oneMenu->popheight)?$oneMenu->popheight : '80%');
}elseif($oneMenu->type < 50 || !empty($buttonObject->buttonO->buttonJS )){
$objButtonO->link='#';}else{
$objButtonO->link=WPage::link($oneMenu->action );
}
if(!empty($this->_toolbarIconPosition )){
$objButtonO->iconPosition=$this->_toolbarIconPosition;
}
if(!empty($buttonObject->buttonO->buttonJS )){
$objButtonO->linkOnClick=$buttonObject->buttonO->buttonJS;
$objButtonO->loading=true;}
if(empty($objButtonO->id )){
$objButtonO->id=WView::generateID('menu',$oneMenu->mid );
}
if(!empty($oneMenu->themepref)){
$html=WPage::renderBluePrint('prefcatalog',$objButtonO );
}else{
$html=WPage::renderBluePrint('button',$objButtonO );
}
  return $html;
  }
  private function _predefinedButton(&$button){
  $button->faicon='fa-';
  switch($button->type){
case 2:$button->faicon .='pencil-square-o';
$button->color='primary';
break;
case 3:$button->faicon .='plus';
$button->color='success';
break;
case 4:$button->faicon .='check';
$button->color='success';
break;
case 5:$button->faicon .='times';
$button->color='danger';
break;
case 6:$button->faicon .='floppy-o';
$button->color='info';
break;
case 7:case 8:$button->faicon .='trash-o';
$button->color='danger';
break;
case 9:$button->faicon .='step-backward';
$button->color='info';
break;
case 10:$button->faicon .='files-o';
$button->color='info';
break;
case 11:$button->faicon .='plus-square-o';
$button->color='success';
break;
case 12:$button->faicon .='minus-square-o';
$button->color='danger';
break;
case 13:$button->faicon .='cogs';
$button->color='info';
break;
case 14:$button->faicon .='chevron-left';
$button->color='info';
break;
case 15:$button->faicon .='chevron-right';
$button->color='info';
break;
case 16:$button->faicon .='life-ring';
$button->color='primary';
break;
case 17:$button->faicon .='magic';
$button->color='primary';
break;
  }
  }
}