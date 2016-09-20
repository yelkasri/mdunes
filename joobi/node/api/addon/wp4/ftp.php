<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Wp4_Ftp_addon {
private static $instance=null;
public function create($path='',$mode=0755){
}
public function delete($path,$file=false){
}
public function content($path='',$filter='',$recursive=false,$full=false,$type=0,$exclude=array()){
}
public function move($src,$dst){
}
public function upload($src,$dst){
}
public function copy($src,$dst,$file=false){
}
public function read($path=''){
}
public function write($path,$content){
}
}