<?php 


* @license GNU GPLv3 */

class Netcom_Dispatcher_class {
public function receiver($addonName){
$addonName=strtolower($addonName );
$namekey='netcom.'.$addonName.'.addon';
$error=null;
WLoadFile('netcom.parent.class');
$netcomProtocolN=WAddon::get('netcom.'.$addonName );
if(!is_object($netcomProtocolN)){
echo 'REQUESTEDPROTOCOLNOTAVAILABLE'; exit;
}
$netcomProtocolN->receiver($error );
return;
}
public static function clean($namekey){
return str_replace('|', DS, preg_replace('#[^|a-zA-Z_.0-9]#','',$namekey ));
}
public static function getDisplayedData(){
$txt=ob_get_contents();
ob_end_clean();
return $txt;
}
}