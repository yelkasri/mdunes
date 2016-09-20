<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Theme_CoreTag_listing  extends WListings_default{
function create(&$listing){
$map=$this->mapList['map'];
$mapName=$this->data->$map;
$tag='widget:area|name='.$mapName.'';
return $tag;
}
}
