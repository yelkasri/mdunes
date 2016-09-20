<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_logs_download_controller extends WController {
function download(){
$filename=WGlobals::get('file');if(empty($filename)) return true;
$path=JOOBI_DS_USER.'logs'.DS.$filename;
$this->_downloadlog($path );
return true;
}
private function _downloadlog($logFilePath){
$fileHandler=WGet::file();
$data=$fileHandler->read($logFilePath );
if(!$data){
$mess=WMessage::get();
$mess->pop();
$mess->userE('1213107637HXCS');
return true;
}
@ob_clean();
header( "content-type: text/plain" );
header( "content-disposition: attachment; filename=" . basename($logFilePath). ";" );
header( "content-transfer-encoding: binary" );
header( "content-length: " . strlen($data));
header('Cache-control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header( "Expires: 0" );
echo $data;
exit;
}
}