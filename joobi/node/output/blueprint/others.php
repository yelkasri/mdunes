<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WRender_Others_blueprint extends Theme_Render_class {
  public function render($data){
  if(empty($data) || empty($data->type)){
  $this->codeE('WRender_Others_class data incomplete!');
  return false;
  }
  switch($data->type){
  case 'editView':
  $html=$this->_editView($data );
  break;
  case 'translationView':
  $html=$this->_translationView($data );
  break;
  case 'editModule':
  $html=$this->_editModule($data );
  break;
  case 'editWidget':
  $html=$this->_editWidget($data );
  break;
  default:
  $html='';
  break;
  }
return $html;
  }
private function _editView($data){
$image='<i class="fa fa-edit fa-lg text-danger"></i>';
if(empty($data->studio))$controller='main-'. $data->controller;
else {
if('picklist'==$data->controller)$controller='view-'. $data->controller;
else $controller='view-'. $data->controller.'s';
}
$link=WPage::linkPopUp('controller='. $controller.'&task=edit&eid='.$data->eid );
$linkHTML=WPage::createPopUpLink($link, $image, '80%','90%','','',$data->text );
$content='';
if(!empty($data->look))$data->controller=$data->look;
switch($data->controller){
case 'form':
$content=$linkHTML;
break;
case 'form-layout':
$content='<div class="editElement editFormLayout">'.$linkHTML.'</div>';
break;
case 'listing':
$content='<div class="editElement editListing">'.$linkHTML.'</div>';
break;
case 'menu':
$content='<div class="editElement editMenu">'.$linkHTML.'</div>';
break;
case 'view':
$content='<div class="editElement editView">'.$linkHTML.'</div>';
break;
default:
$content='<div class="editElement">'.$linkHTML.'</div>';
break;
}
return $content;
}
private function _translationView($data){
$image='<i class="fa fa-language fa-lg text-info"></i>';
$lgid=WUser::get('lgid');
$link=WPage::linkPopUp('controller=main-translate&task=edit&type='. $data->controller .'&eid='.$data->eid.'&text='.$data->text.'&lgid='.$lgid, false);
$linkHTML=WPage::createPopUpLink($link, $image, '80%', 200, 'translation','',$data->text );
$content='';
switch($data->controller){
case 'form':
$content=$linkHTML;
break;
case 'form-layout':
$content='<div class="editElement translateFormLayout">'.$linkHTML.'</div>';
break;
case 'listing':
$content='<div class="editElement translateListing">'.$linkHTML.'</div>';
break;
case 'menu':
$content='<div class="editElement translateMenu">'.$linkHTML.'</div>';
break;
case 'view':
$content='<div class="editElement translateView">'.$linkHTML.'</div>';
break;
default:
$content='<div class="editElement">'.$linkHTML.'</div>';
break;
}
return $content;
}
private function _editModule($data){
$content='<div class="btn-group" role="group">';
$btnO=WPage::newBluePrint('button');
$btnO->text=WText::t('1206732361LXFE');
$btnO->title=WText::t('1206732361LXFE'). ' '.$data->title;
$btnO->id='mdledt'.$data->moduleID;
$btnO->type='infoLink';
$btnO->icon='fa-cog';
$btnO->link=WPage::linkPopUp('controller=main-widgets-preference&task=edit&id='.$data->moduleID.'&goty=com_modules');
$btnO->popUpIs=true;
$btnO->popUpWidth='80%';
$btnO->popUpHeight='80%';
$btnO->color='success';
$content .=WPage::renderBluePrint('button',$btnO );
$content .='</div>';
return $content;
}
private function _editWidget($data){
$content='<div class="btn-group" role="group">';
$btnO=WPage::newBluePrint('button');
$btnO->text=WText::t('1206732361LXFE');
$btnO->title=WText::t('1206732361LXFE'). ' '.$data->title;
$btnO->id='wdtedt'.$data->widgetID;
$btnO->type='infoLink';
$btnO->link=WPage::linkPopUp('controller=main-widgets&task=edit&eid='.$data->widgetID );
$btnO->popUpIs=true;
$btnO->icon='fa-cog';
$btnO->popUpWidth='80%';
$btnO->popUpHeight='80%';
$btnO->color='success';
$content .=WPage::renderBluePrint('button',$btnO );
$content .='</div>';
return $content;
}
}