<?php 


* @license GNU GPLv3 */

class Api_Mobilev1_Miscellaneous_addon {
public function getAllWidgetsIDforFeatured(){
return array();
}
public function getAllModule4Type($namekey){
$resultA=array();
return $resultA;
}
public function renderModule($module){
$params=new stdClass();
$params->module=new stdClass();
$params->module->id=$module->id;
$params->module->title=$module->title;
$params->module->module=$module->module;
return WGet::startApplication('module','cart.cart.module',$params );
}
}