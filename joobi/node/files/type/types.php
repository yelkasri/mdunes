<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_Types_type extends WTypes {
public $types = array(
		'' => 'None',
		'url' => 'URL',
		'file' => 'File',			'x1x1' => '--Video Sites',
		'youtube' => 'Youtube',
		'vimeo' => 'Vimeo',
		'livevideo' => 'Live Video',
		'yahoovideo' => 'Yahoo Video',
		'espn' => 'ESPN Sports',
		'x2x2' => '--Social Network Sites',
		'myvideo' => 'MyVideo',
		'gametrailers' => 'Game Trailers'
	);
}