<?php 


* @license GNU GPLv3 */

class Output_tour_controller extends WController {
function tour(){
$trid=WGlobals::get('trid');
$action=WGlobals::get('tract');
$yid=WGlobals::get('yid');
if(!empty($trid )){
$designTourusersM=WModel::get('output.tourusers');
$designTourusersM->whereE('trid',$trid );
$designTourusersM->whereE('yid', 0 );
$designTourusersM->whereE('uid', WUsers::get('uid'));
$designTourusersM->setVal('status', 9 );
$designTourusersM->update();
}elseif(!empty($yid )){
$designTourusersM=WModel::get('output.tourusers');
$designTourusersM->whereE('yid',$yid );
$designTourusersM->whereE('trid', 0 );
$designTourusersM->whereE('uid', WUsers::get('uid'));
$designTourusersM->setVal('status', 9 );
$designTourusersM->update();
}
exit;
}
}