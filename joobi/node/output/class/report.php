<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Report_class {
public static function reportQuery($task,&$model,$noQuery=true,$map='',$sid='',$orderDate='',$yid='',$firstFormName='',$reportnosetinterval=false){
$dateNumber='DateNumber';
static $startTime=0;
static $endTime=0;
$trk=WGlobals::get( JOOBI_VAR_DATA );
$interval=(!empty($trk['x']['interval'] )?$trk['x']['interval'] : WGlobals::get('interval'));
if(empty($interval)){
$interval=WGlobals::getSession('graphFilters','interval');
if(empty($interval))$currentPresetDate=15;}
WGlobals::setSession('graphFilters','interval',$interval );
$currentInterval=$interval;
$currentPresetDate=(!empty($trk['x']['presetdate'] )?$trk['x']['presetdate'] : 0 );
if(empty($currentPresetDate)){
$currentPresetDate=WGlobals::getSession('graphFilters','presetdate');
if(empty($currentPresetDate))$currentPresetDate=30;}WGlobals::setSession('graphFilters','presetdate',$currentPresetDate );
if($task=='generate' && (!empty($trk['x']['startdate']) || !empty($trk['x']['enddate'] ))){
$trk=WGlobals::get( JOOBI_VAR_DATA );
$start=$trk['x']['startdate'];
$end=$trk['x']['enddate'];
if($start !='0000-00-00' && $end !='0000-00-00'){$startTime=strtotime($start);
$endTime=strtotime($end);
}elseif($start !='0000-00-00'){$startTime=strtotime($start);
$endTime=time();
}else{$message=WMessage::get();
$message->userW('1256627078COLC');
$startTime='0000-00-00';$endTime='0000-00-00';
}}else{
$endTime=$currDate=time() - WApplication::dateOffset() * 3600;switch($currentPresetDate){
case '2': $startTime=mktime(0, 0, 0, date('m'), date('d'), date('Y'));
$endTime=mktime(23, 59, 59, date('m'), date('d'), date('Y'));
break;
case '4': $startTime=mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));
$endTime=mktime(23, 59, 59, date('m'), date('d')-1, date('Y'));
break;
case '6':$startTime=mktime(0, 0, 0, date('n'), date('j'), date('Y')) - ((date('N')-1)*3600*24);
break;
case '12':$startTime=mktime(0, 0, 0, date('n'), date('j')-6, date('Y')) - ((date('N'))*3600*24);
$endTime=mktime(23, 59, 59, date('n'), date('j'), date('Y')) - ((date('N'))*3600*24);
break;
case '24':$startTime=mktime(0, 0, 0, date('n'), date('j')-13, date('Y')) - ((date('N'))*3600*24);
$endTime=mktime(23, 59, 59, date('n'), date('j'), date('Y')) - ((date('N'))*3600*24);
break;
case '36':$startTime=strtotime((date('m')-1).'/01/'.date('Y'), $currDate );
$endTime=strtotime('-1 second', strtotime( date('m').'/01/'.date('Y').' 00:00:00',$startTime));
break;
case '42':$startTime=strtotime('01/01/'.date('Y'), $currDate);
break;
case '48':$startTime=mktime(0,0,0,1,1,date('Y')-1);$endTime=mktime(23,59,59,12,31,date('Y')-1);
break;
case '54':$startTime=mktime(0,0,0,1,1,2000);$endTime=time();
break;
case '51': if($currentInterval=='33'){$startTime=mktime(0,0,0,1,1,date('Y')-2);}else{
$endTime=mktime(23,59,59,12,31,date('Y')-2);$startTime=mktime(0,0,0,1,1,date('Y')-2);}break;
case '53': if($currentInterval=='33'){$startTime=mktime(0,0,0,1,1,date('Y')-3);}else{
$endTime=mktime(23,59,59,12,31,date('Y')-3);$startTime=mktime(0,0,0,1,1,date('Y')-3);}break;
case '30':default:
$startTime=strtotime(date('m').'/01/'.date('Y'), $currDate);
break;
}}
if($startTime !='0000-00-00' && $endTime !='0000-00-00' && $startTime > 0 && $endTime > 0){
if($currentPresetDate==2 || $currentPresetDate==4){
$reportHTML=WApplication::date( WTools::dateFormat('date'), $startTime );
}else{
$reportHTML=WApplication::date( WTools::dateFormat('date'), $startTime ).' to '. WApplication::date( WTools::dateFormat('date'), $endTime );}
static $alreadyLoaded=false;
$showTitleHeader=WGlobals::get('showTitleHeader', true, 'global');
if($showTitleHeader){
$previousName=WGlobals::get('titleheaderreport');
if(!empty($previousName) && !$alreadyLoaded){
$reportHTML=$previousName .' ('. $reportHTML.')';
if(!empty($firstFormName)){
$formInt=WView::form($firstFormName );
$formInt->hidden('titleheaderreport',$previousName );
}}}
if(!$alreadyLoaded && $showTitleHeader){
$alreadyLoaded=true;
WGlobals::set('titleheader',$reportHTML, '');
}
}
if($map !='registerdate'){switch($currentInterval){
case '33':$unixTime4OrderDate='fmunixtime%Y';
$unixTime4DateNumber='fmunixtime%Y%m%d';
break;
case '15':$unixTime4OrderDate='fmunixtime%M %d, %Y';
$unixTime4DateNumber='fmunixweek%Y-%m-%d';break;
case '7':$unixTime4OrderDate='fmunixtime%M %d, %Y';
$unixTime4DateNumber='fmunixtime%Y%m%d';
break;
case '23':default:
$unixTime4OrderDate='fmunixtime%M, %Y';
$unixTime4DateNumber='fmunixtime%Y%m%d';
break;
}}else{
switch($currentInterval){
case '33':
$unixTime4OrderDate='dateformat%Y';
$unixTime4DateNumber='dateformat%Y%m%d';
break;
case '15':$unixTime4OrderDate='dateformat%M %d, %Y';
$unixTime4DateNumber='dtfrmtweek%Y-%m-%d';break;
case '7':$unixTime4OrderDate='dateformat%M %d, %Y';
$unixTime4DateNumber='dateformat%Y%m%d';
break;
case '23':default:
$unixTime4OrderDate='dateformat%M, %Y';
$unixTime4DateNumber='dateformat%Y%m%d';
break;
}}
if($map=='registerdate' && $startTime !='0000-00-00'){$startTime=date('Y-n-j',$startTime);
$endTime=date('Y-n-j',$endTime);
}
if($noQuery){
WGlobals::set('interval',$currentInterval );
WGlobals::set('presetdate',$currentPresetDate );
WGlobals::set('end',$endTime );
WGlobals::set('start',$startTime );
}else{
$as=$model->getAs($sid);
if($map=='created' || $map=='startime' || $map=='registerdate' || $map=='modified'){
$aliasOrderDate=$model->select($map, $model->getAs($sid), $orderDate, $unixTime4OrderDate );
$alias=$model->select($map, $model->getAs($sid), $dateNumber, $unixTime4DateNumber );
$model->where($map, '>=',$startTime, $as );
$model->where($map, '<',$endTime, $as );
if(!$reportnosetinterval){
if($currentInterval==15){$model->groupBy($alias, null );
}else{
$model->groupBy($aliasOrderDate, null );}}
$model->orderBy($alias, 'ASC', null );
switch ($map){
case 'startime' :
$model->orderBy('startime','ASC',$as,null );
break;
case 'registerdate' :
$model->orderBy('registerdate','ASC',$as, null );
break;
case 'modified' : $model->orderBy('modified','ASC',$as, null );
break;
default :
$model->orderBy('created','ASC',$as, null );
break;
}
}else{$model->select($map, $model->getAs($sid), $orderDate );
}}
return true;
}
}
