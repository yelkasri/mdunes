<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
 class Apps_Netcomcode_class extends WClasses {
public function convertAnswers($code){
switch($code){
case 'TOKENVALID':
$this->adminS('The API key is valid!');
return true;
case 'TOKENEXPIRED':
$this->adminE('The API key is expired!');
return false;
case 'TOKENNOTEXIST':
$this->adminE('The API key was not found, please check it again!');
return false;
case 'TOKENMISMATCH':
$this->adminE('The API key mismatch!');
return false;
case 'TOKENNOTPOSSIBLERESULT':
$this->adminE('API key error!');
return false;
case 'TOKENUNKNOWNERROR':
$this->adminE('The API key provided is unknown, please check with support!');
return false;
case 'NO_URL_PROVIDED':
$this->adminE('No URL provided?!..');
return false;
case 'NO_URL_VALID':
$this->adminE('URL no valid!');
return false;
case 'NO_FREE_TRIAL':
$this->adminE('Could not get a free trial!');
return false;
default:WMessage::log('Code not defined: '.$code, 'Apps_Netcomcode_class');
$this->adminE('API key error or not defined!');
return false;
}
return true;
 }
}
