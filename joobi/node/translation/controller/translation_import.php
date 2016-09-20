<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Translation_import_controller extends WController {
function import(){
$fileTrucs=WGlobals::get( JOOBI_VAR_DATA, array(), 'FILES','array');
$file_tmp_name=$fileTrucs['tmp_name']['x']['file'];
$filetype=$fileTrucs['type']['x']['file'];
$file_name=$fileTrucs['name']['x']['file'];
$trk=WGlobals::get( JOOBI_VAR_DATA, array(), '','array');
$currentVal=current($trk );
$share=(bool)$currentVal['x']['participate'];
if( substr($file_name, -4) !='.ini'){
$message=WMessage::get();
$message->userW('1206732404ORZN');
return;
}
if(!is_uploaded_file($file_tmp_name)){
$mess=WMessage::get();
$mess->adminE('Bad request');
return;
}
$temp_name=md5($file_name.time());
$filestat=move_uploaded_file($file_tmp_name, JOOBI_DS_TEMP.$temp_name );
if($filestat===false){
$mess=WMessage::get();
$DESTINATION=JOOBI_DS_TEMP.$temp_name;
$SOURCE=$file_tmp_name;
$mess->adminE('Could not move the uploaded file '.$SOURCE. ' to the temporary file '.$DESTINATION );
return;
}
WPages::redirect('controller=translation&task=importedit&run='.$temp_name);
}
}
