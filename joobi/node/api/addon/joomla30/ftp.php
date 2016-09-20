<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Api_Joomla30_Ftp_addon {
private static $instance=null;
public function create($path='',$mode=0755){
jimport('joomla.filesystem.folder');
return JFolder::create($path='',$mode );
}
public function delete($path,$file=false){
if($file){
jimport('joomla.filesystem.file');
JFile::delete($path );
}else{
jimport('joomla.filesystem.folder');
JFolder::delete($path );
}
}
public function content($path='',$filter='',$recursive=false,$full=false,$type=0,$exclude=array()){
jimport('joomla.filesystem.folder');
return JFolder::files($path, $filter, $recursive, $full, $exclude );
}
public function move($src,$dst){
jimport('joomla.filesystem.file');
return JFile::delete($src, $dst );
}
public function upload($src,$dst){
jimport('joomla.filesystem.file');
return JFile::upload($src, $dst );
}
public function copy($src,$dst,$file=false){
if($file){
jimport('joomla.filesystem.file');
JFile::copy($src, $dst );
}else{
jimport('joomla.filesystem.folder');
JFolder::copy($src, $dst );
}
}
public function read($path=''){
jimport('joomla.filesystem.file');
return JFile::read($path );
}
public function write($path,$content){
jimport('joomla.filesystem.file');
return JFile::write($path, $content );
}
}