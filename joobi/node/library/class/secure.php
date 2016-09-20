<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Library_Secure_class {
public function encrypt($object,$serialize=false){
if($serialize)$string=serialize($object );
elseif( is_string($object)){
$string=$object;
}else{
$message=WMessasge::get();
$message->codeE('Could not secure the object!');
return false;
}
if( function_exists('mcrypt_encrypt')){
$key=JOOBI_SITE_TOKEN;
$encrypted=base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key)) ));
}else{
$encrypted=base64_encode($string );
}
return $encrypted;
}
public function decrypt($string,$unserialize=false){
if( function_exists('mcrypt_encrypt')){
$key=JOOBI_SITE_TOKEN;
$decrypted=rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "\0" );
}else{
$decrypted=base64_decode($string );
}
if($unserialize)$object=unserialize($decrypted );
else $object=$decrypted;
return $object;
}
}