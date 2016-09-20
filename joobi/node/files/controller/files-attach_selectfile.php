<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_attach_selectfile_controller extends WController {
function selectfile() {
	$filid = WGlobals::get('filid');
	$pid = WGlobals::get('pid');
	$attach = WGlobals::get('attach');
	$map = WGlobals::get('map');
	if (empty($map)) $map = 'filid';
	$model= WGlobals::get('model');
	if (empty($model)) $model = 'item';
	$downloadM = WModel::get($model);
	if ( $downloadM->getType() == 30 ) {
		if ($attach) {
			$downloadM->setVal( 'pid', $pid );
			$downloadM->setVal( $map, $filid );
			$downloadM->insertIgnore();
		} else {
			$downloadM->whereE( 'pid', $pid );
			$downloadM->whereE( $map, $filid );
			$downloadM->delete();
		}
	} else {
		$downloadM->whereE('pid', $pid);
		if ($attach)$downloadM->setVal($map, $filid);
		else $downloadM->setVal($map, 0);
		$downloadM->update();
	}
	WPages::redirect('controller=files-attach&task=listing&pid='.$pid .'&map='.$map .'&model='.$model );
}}