<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WRender_Countdown_classData {
public $location='';
public $icon='';
public $text='';
public $size='';
public $color='';
public $animation='';
}
class WRender_Countdown_blueprint extends Theme_Render_class {
  public function render($object){
  static $onlyOnce=true;
  static $idCount=0;
  if($onlyOnce){
WPage::addJSFile('js/countdown.js');
  }
  $idCount++;
  $divId='countDownIdWeb'.$idCount;
  $date=date('Y-m-d H:i:s',$object->time );
  $variableName='ctWeb'.$idCount;
  unset($object->type );
  $translation=WGlobals::filter($object, 'safejs');
  $translation_json=json_encode($translation);
  $JScode=$variableName . "=new dateTimeDownWeb('". $this->_datediff($date ). "','" . $divId . "','".($translation_json)."');";
  $JScode .=$variableName.'.do_cd('.$object->precision.');';
  WPage::addJSScript($JScode );
  return '<div id="'.$divId.'"></div>';
  }
private function _datediff($finalTime,$nowtime=0){
if($nowtime==0){
$datefrom=date("Y-m-d H:i:s"); 
}else{
$datefrom=$nowtime;
}
$datefrom=WApplication::stringToTime($datefrom, 0 );
$dateto=WApplication::stringToTime($finalTime, 0 );
$difference=$dateto - $datefrom; 
if($difference < 0)$difference=$difference * -1;
return $difference;
}
}