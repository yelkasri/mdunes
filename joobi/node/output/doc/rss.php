<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Output_Doc_rss extends Output_Doc_Document {
var $endLine="\n";
public function renderContent(){
$rssOutput='';
$rss=$this->_createHeader($rssOutput);
$dataA=$this->htmlObj->_data;
$elementsA=$this->htmlObj->elements;
$mapListA=$this->htmlObj->mapListA;
if(!empty($dataA) && !empty($mapListA)){
$map_name=$mapListA['name'];
$map_desc=$mapListA['description'];
$map_created=$mapListA['created'];
$map_modified=$mapListA['modified'];
$map_pid=$mapListA['pid'];
$map_filid=$mapListA['filid'];
$imageM=WModel::get('files');
$entryO=new stdClass;
foreach($dataA as $ndxdata=> $Valdata){
$pid=$Valdata->$map_pid;
 $entryO->title=$Valdata->$map_name;
 $entryO->content=$Valdata->$map_desc;
 $entryO->published=$Valdata->$map_created;
 $entryO->updated=$Valdata->$map_modified;
 $entryO->filid=$Valdata->$map_filid;
$entryO->link=WPage::linkHome('controller=catalog&task=show&eid='. $pid, WPage::getPageId('catalog'));
$image=$imageM->getInfo($entryO->filid );
if(!empty($image)){
$image->path=$imageM->convertPath($image->path);
$urlID=$image->path.'/'.$image->name.'.'.$image->type;
$imagePath='<img src="'.JOOBI_URL_MEDIA . $urlID.'" width="100" alt="'.WGlobals::filter($image->name, 'string'). '" />';
}else{ $imagePath='';
}
$entryO->link=htmlentities($entryO->link );
 $this->_createEntry($rss, $entryO->title, $entryO->content, $entryO->link, $entryO->published, $entryO->updated, $imagePath);
 }
$rss.=$this->endLine.'</channel>'.$this->endLine.'</rss>';
$data=$rss;
$file='myrss.xml';
$fileHandler=WGet::file();
$fileHandler->write($file,$data, 'overwrite');
ob_clean();
header('content-type:application/atom+xml');
$fileHandler=WGet::file();
$data=$fileHandler->read($file);
echo $data;
exit();
}else{
$message=WMessage::get();
$message->userN('1263549766QJON');
return false;
}
}
private function _createHeader(&$rssOutput){
$catname='';
$xmlbase=JOOBI_SITE;
$titleheader=WGlobals::get('titleheader');
$catid='';
$catid=WGlobals::get('catid');
$productcatM=WModel::get('item.categorytrans','object');
$productcatM->select('name');
$productcatM->whereE('catid',$catid);
$catname=$productcatM->load('lr');
if(!empty($titleheader)){
$title=$titleheader;
}elseif($catname !=''){
$title=$catname;
}else{
$title=JOOBI_SITE;
}
$link=htmlentities( JOOBI_SITE . WView::getURI());
$author=JOOBI_SITE;
$updated=date('Y-m-d\TH:i:s\Z');
$rssOutput='<?xml version="1.0" encoding="UTF-8"?>'.$this->endLine;
$rssOutput .='<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'.$this->endLine;
$rssOutput .='<channel>'.$this->endLine;
$rssOutput .='<title>'.$title.'</title>'.$this->endLine;
if(!empty($description))$rssOutput .='<description><![CDATA['.$description.']]></description>'.$this->endLine;
$rssOutput .='<link>'.$link.'</link>'.$this->endLine;
$rssOutput .='<atom:link rel="self" type="application/rss+xml" href="'.$link.'"/>'.$this->endLine;
$rssOutput .='<lastBuildDate>' .$updated. '</lastBuildDate>'.$this->endLine;
$rssOutput .='<language>en-gb</language>'.$this->endLine;
$rssOutput .='<generator>'.$xmlbase.'</generator>';
return $rssOutput;
}
private function _createEntry(&$rssOutput,$etitle,$content,$link,$published,$updated,$imagePath2=null){
$created=date('Y-m-d',$published);
$published=date('Y-m-d\TH:i:s\Z',$published);
$updated=date('Y-m-d\TH:i:s\Z',$updated);
$rssOutput .=$this->endLine.'<item>'.$this->endLine;
$rssOutput .='<title>' .$etitle. '</title>'.$this->endLine;
$rssOutput .='<link>'.$link .'"</link>'.$this->endLine;
$rssOutput .='<guid isPermaLink="true">'.$link.'</guid>'.$this->endLine;
$rssOutput .='<pubDate>'.$published.'</pubDate>'.$this->endLine;
if(empty($content))$content='No description';
if(!empty($imagePath2))$content=$imagePath2.' '.$content;
$rssOutput .='<description><![CDATA['.$this->_xmlentities($content). ']]></description>'.$this->endLine;
$rssOutput .='</item>';
return $rssOutput;
}
private function _xmlentities($string){
return $string;
}
}
