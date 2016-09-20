<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WTableau extends WElement {
var $_body=null; var $_line=null;var $_cell=null;
var $t_c=null;
var $t_s=null;
var $t_w=null;
var $t_p=null;
var $t_spc=null;
var $t_b=null;
var $tr_c=null;
var $tr_s=null;
var $tr_script=null;
var $tr_a=null;
var $tr_va=null;
var $th_c=null;
var $th_s=null;
var $th_w=null;
var $th_h=null;
var $th_a=null;
var $th_va=null;
var $th_colspan=null;
var $th_rowspan=null;
var $th_nowrap=null;
var $td_c=null;
var $td_s=null;
var $td_a=null;
var $td_h=null;
var $td_va=null;
var $td_nowrap=null ; var $td_colspan=null;
var $td_rowspan=null;
var $oneCellContent=null;
var $tip=null;
function __construct($obj=null,$sTable=false){
$this->t_c=(isset($obj->t_c))?$obj->t_c : '';$this->t_w=(isset($obj->t_w))?$obj->t_w : ((defined('JOOBI_T_W'))?JOOBI_T_W : null );
$this->t_p=(isset($obj->t_p))?$obj->t_p : ((defined('JOOBI_T_P'))?JOOBI_T_P : null );
$this->t_b=(isset($obj->t_b))?$obj->t_b : ((defined('JOOBI_T_B'))?JOOBI_T_B : null );
if($sTable)$this->_sTable();
$this->crlf=WGet::$rLine;
}
function clearTD(){
$this->td_c=null;
$this->td_s=null;
$this->td_a=null;
$this->td_w=null;
$this->td_h=null;
$this->td_va=null;
$this->td_colspan=null;
$this->td_rowspan=null;
$this->td_nowrap=false;
}
private function _sTable(){
$h=$this->crlf;
$h .='<table' ;
if(!empty($this->t_c))$h .=' class="'.$this->t_c.'"';
if(!empty($this->t_s))$h .=' style="'.$this->t_s.'"';
if(!empty($this->t_w))$h .=' width="'.$this->t_w.'"';
if(!empty($this->t_p))$h .=' cellspadding="'.$this->t_p.'"';
if(!empty($this->t_spc))$h .=' cellspacing="'.$this->t_spc.'"';
if(!empty($this->t_b))$h .=' border="'.$this->t_b.'"';
$h .='>'.$this->crlf;
return $this->content=$h;
}
private function _eTable(){
$this->content .= '</table>'.$this->crlf;
return $this->content;
}
function body($type='body'){
$body='';
$previousBody=(isset($this->_body))?$this->_body : '';
if(!empty($this->_line)){
$body='  <t'.$type.'>';if(!empty($this->_line))$body .=$this->crlf . $this->_line . $this->crlf;
$body .='  </t'.$type.'>'.$this->crlf;
}
$this->_body=$previousBody . $body;
$this->_line=null;
return $body;
}
function line($params=''){
$d=(isset($this->_cell)?$this->_cell : '');
$line='';
$line .=$this->_sLineTable();
$line .=$d;
$line .=$this->_eLineTable();
$line .=$this->crlf;
$previousLine=(isset($this->_line))?$this->_line : '';
$this->_cell=null;
$this->_line=$previousLine . $line;
return $line;
}
private function _sLineTable(){
$h='<tr';
if(!empty($this->tr_c))$h .=' class="'.$this->tr_c.'"';
if(!empty($this->tr_script))$h .=' onClick="'.$this->tr_script.'"';
if(!empty($this->tr_id))$h .=' id="'.$this->tr_id.'"';
if(!empty($this->tr_s))$h .=' style="'.$this->tr_s.'"';
if(!empty($this->tr_a))$h .=' align="'.$this->tr_a.'"';
if(!empty($this->tr_va))$h .=' valign="'.$this->tr_va.'"';
$h .='>'.$this->crlf;
return $h;
}
private function _eLineTable(){
return '</tr>';}
function cell($d,$header=false){
if( is_string($d) && strlen($d)>0){
$cell=$this->_sCellTable($header );
$cell .=$d;
$cell .=$this->_eCellTable($header );
}else{
$cell=($header)? '<th></th>' : '<td></td>';
$cell .=$this->crlf;
}
return $this->_cell .=$cell;
}
private function _sCellTable($header=false){
$h=$this->crlf;
if($header){
$h .='<th';
if(!empty($this->th_c))$h .=' class="'.$this->th_c.'"';
if(!empty($this->th_s))$h .=' style="'.$this->th_s.'"';
if(!empty($this->th_a) &&($this->th_a !='center'))$h .=' align="'.$this->th_a.'"';
if(!empty($this->th_w))$h .=' width="'.$this->th_w.'"';
if(!empty($this->th_h))$h .=' height="'.$this->th_h.'"';
if(!empty($this->th_va))$h .=' valign="'.$this->th_va.'"';
if(!empty($this->th_colspan))$h .=' colspan="'.$this->th_colspan.'"';
if(!empty($this->th_rowspan))$h .=' rowspan="'.$this->th_rowspan.'"';
if(!empty($this->th_nowrap))$h .=' nowrap';
$h .='>';
}else{
$h .= '<td';
if(!empty($this->td_c))$h .=' class="'.$this->td_c.'"';
if(!empty($this->td_a)){
switch($this->td_a){
case 'right':
if(!isset($this->td_s))$this->td_s='';
$this->td_s .=' text-align:right;';
break;
case 'left':
if(!isset($this->td_s))$this->td_s='';
$this->td_s .=' text-align:left;';
break;
case 'center' :
break;
default:
$h .=' align="'.$this->td_a.'"';
break;
}}
if(isset($this->td_s)){
$h .=' style="'.$this->td_s.'"';
}
if(!empty($this->td_id))$h .=' id="'.$this->td_id.'"';
if(!empty($this->td_w))$h .=' width="'.$this->td_w.'"';
if(!empty($this->td_h))$h .=' height="'.$this->td_h.'"';
if(!empty($this->td_va))$h .=' valign="'.$this->td_va.'"';
if(!empty($this->td_colspan))$h .=' colspan="'.$this->td_colspan.'"';
if(!empty($this->td_rowspan))$h .=' rowspan="'.$this->td_rowspan.'"';
if(!empty($this->td_nowrap))$h .=' nowrap="nowrap"';
$h .='>';
if(!empty($this->td_a) && $this->td_a=='center')$h  .='<center>';
$this->td_colspan=null;
}
return $h;
}
private function _eCellTable($header=false){
if(isset($this->td_a) && $this->td_a=='center')$center='</center>'; else $center='';
$h=($header)?$center.'</th>' : $center.'</td>';
return $h;
}
function miseEnPageTwo(&$params,$value){
$name=$params->name;
$title='';
$tip=$params->description;
$required=$params->required;
$notitle=0;
if(isset($params->notitle))$notitle=$params->notitle;
if(isset($params->flip))$flip=$params->flip;
if(isset($params->lbreak))$lbreak=$params->lbreak;
if($notitle==0){
$req='<b class="required" title="'.WText::t('1206732369EREV').'">*</b>';
if($tip){
$toolTipsO=WPage::newBluePrint('tooltips');
$toolTipsO->tooltips=$tip;
$toolTipsO->title=$name;
$toolTipsO->text=$name;
$toolTipsO->id='tip_'.$params->idLabel;
$toolTipsO->bubble=true;
$s=WPage::renderBluePrint('tooltips',$toolTipsO );
if($required==1 && $params->editItem)$title .=$s . $req;
else $title.=$s;
}else{
if($required==1 && $params->editItem)$title .=$name . $req;
else $title.=$name ;
}
}
$this->td_c='key'.$params->spantit;
if(!empty($flip)){
if(!$params->spantit){
if(!$params->spanval){
$this->td_va='top';
$this->cell($value);
$this->td_c='';
$this->cell($title);
}else{
$this->td_colspan=2;
$this->td_va='top';
$this->cell($title);
}}else{
$this->td_colspan=2;
$this->td_va='top';
$this->cell($value);
}
}else{
if(!$params->spantit){
if(!$params->spanval){
$this->td_va='top';
$this->cell($title);
$this->td_c='';
$this->cell($value);
}else{
$this->td_colspan=2;
$this->td_va='top';
$this->cell($title);
}}else{
$this->td_colspan=2;
$this->td_va='top';
$this->cell($value);
}}
if(isset($params->extracol))$this->cell($tip);
return $this->line();
}
function miseEnPageOne(&$params,$value){
$name=$params->name;
$tip=$params->description;
$required=$params->required;
$notitle=0;
if(isset($params->notitle))$notitle=$params->notitle;
if(isset($params->flip))$flip=$params->flip;
if(isset($params->lbreak))$lbreak=$params->lbreak;
$title='<div class="jcaption"><div class="jtcaption">';
if($notitle==0){
$req='<b class="required" title="'.WText::t('1206732369EREV').'">*</b>';
if($tip){
$toolTipsO=WPage::newBluePrint('tooltips');
$toolTipsO->tooltips=$tip;
$toolTipsO->title=$name;
$toolTipsO->text=$name;
$toolTipsO->id='tip_'.$params->idLabel;
$toolTipsO->bubble=true;
$s=WPage::renderBluePrint('tooltips',$toolTipsO );
if($required==1 && $params->editItem)$title.=$s.$req;
else $title=$s;
}else{
if($required==1 && $params->editItem)$title .= $name.$req ;
else $title.=$name ;
}}$title.='</div></div>';
$this->td_c='key'.$params->spantit;
$this->td_va='top';
$this->td_s='text-align:left;';
if(isset($flip) && $flip==1){
if(isset($lbreak) && $lbreak==1)$this->cell($value.$title);
else $this->cell($value.$title);
}else{
if(isset($lbreak) && $lbreak==1)$this->cell($title.$value);
else $this->cell($title.$value);
}
return $this->line();}
function miseEnPageFlat(&$params,$value){
$name=$params->name;
$tip=$params->description;
$required=$params->required;
$notitle=0;
if(isset($params->notitle))$notitle=$params->notitle;
if(isset($params->flip))$flip=$params->flip;
if(isset($params->lbreak))$lbreak=$params->lbreak;
$title='';
if($notitle==0){
$req='<b class="required" title="'.WText::t('1206732369EREV').'">*</b>';
if($tip){
$toolTipsO=WPage::newBluePrint('tooltips');
$toolTipsO->tooltips=$tip;
$toolTipsO->title=$name;
$toolTipsO->text=$name;
$toolTipsO->id='tip_'.$params->idLabel;
$toolTipsO->bubble=true;
$s=WPage::renderBluePrint('tooltips',$toolTipsO );
if($required==1 && $params->editItem)$title.=$s. $req;
else $cell=$s;
}else{
if($required==1 && $params->editItem)$title .= $name.$req ;
else $title.=$name ;
}}
$this->td_c='key'.$params->spantit;
$this->td_s='text-align:left;';
if(isset($flip) && $flip==1){
if(isset($lbreak) && $lbreak==1)$this->cell(''. $value .'<br />'.$title);
else $this->cell(''. $value.$title);
}else{
if(isset($lbreak) && $lbreak==1)$this->cell(''. $title .'<br />'.$value);
else $this->cell(''. $title.$value);
}}
function miseEnPageBr(&$params,$value){
$name=$params->name;
$tip=$params->description;
$required=$params->required;
$notitle=0;
if(isset($params->notitle))$notitle=$params->notitle;
if(isset($params->flip))$flip=$params->flip;
if(isset($params->lbreak))$lbreak=$params->lbreak;
$title='';
if($notitle==0){
$req='<b class="required" title="'.WText::t('1206732369EREV').'">*</b>';
if($tip){
$toolTipsO=WPage::newBluePrint('tooltips');
$toolTipsO->tooltips=$tip;
$toolTipsO->title=$name;
$toolTipsO->text=$name;
$toolTipsO->id='tip_'.$params->idLabel;
$toolTipsO->bubble=true;
$s=WPage::renderBluePrint('tooltips',$toolTipsO );
if($required==1 && $params->editItem)$title.=$s. $req;
else $cell=$s;
}else{
if($required==1 && $params->editItem)$title .= $name.$req ;
else $title.=$name;
}}else $title.='';
if(isset($flip) && $flip==1){
if(isset($lbreak) && $lbreak==1)$cont=''. $value .'<br />'.$title;
else $cont=''. $value.$title;
}else{
if(isset($lbreak) && $lbreak==1)$cont=''. $title .'<br />'.$value;
else $cont=''. $title.$value;
}
$cont .='<br />'.$this->crlf;
return $cont;}
function create(){
$this->_sTable();
$this->content .=(isset($this->_body))?$this->_body : $this->_line;
$this->_eTable();
$this->_body=null;
$this->_cell=null;
$this->_line=null;
return $this->content;
}
}
