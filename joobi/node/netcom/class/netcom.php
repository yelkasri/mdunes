<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
WLoadFile('netcom.class.client');
class Netcom_Netcom_class extends Netcom_Client_class {
public $APIVersion='1.0';
public $APIUserID=0;
public $servicesCredentials=array(
'ping'=> true
);
function ping($data){
WMessage::log($data, 'netcom-ping-data');
if(empty($data)) return true;
return $data;
}
function dictionaryTransFile($data=null){
if(!is_object($data )){
$reply=null;
$reply->error='File data must be in object form.';
return $reply;
}
$filePath=JOOBI_DS_USER.'temp'.DS.'dictionarytrans'.DS.$data->filename;
$filehandler=WGet::file();
$filehandler->write($filePath, base64_decode($data->content ), 'force');
return JOOBI_FOLDER.'/user/temp/dictionarytrans/'.$data->filename;
}
}
