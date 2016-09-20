<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_removefile_controller extends WController {
function removefile(){
$id=WGlobals::get('id');
$controller=WGlobals::get('controllerback');
$model=WGlobals::get('model');
$pk=WGlobals::get('pk');
$filid=WGlobals::get('filid');
$map=WGlobals::get('map');
if(empty($map))$map='filid';
if(empty($id) || empty($pk) || empty($model)) return false;
$modelM=WModel::get($model, 'object');
$modeltype=WModel::get($model, 'type');
$modelM->whereE($pk, $id);
if($modeltype==30){
$modelM->whereE($map, $filid);
$modelM->delete();
}else{
$modelM->setVal($map, 0);
$modelM->update();
}
$msg=WMessage::get();
$msg->userS('1317297448JZYW');
WPages::redirect('controller='.$controller.'&task=edit&eid='.$id);
return true;
}}