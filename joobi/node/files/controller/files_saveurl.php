<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_saveurl_controller extends WController {
	function saveurl() {
		$trk = WGlobals::get( JOOBI_VAR_DATA );
		$uploadFile = $trk['x'];
		$message = WMessage::get();
		$filesM = WModel::get('files');
		$filesM->whereE('name', $uploadFile['url']);
		$filesM->whereE('type', 'url');
		$filesM->whereE('path', '');
		$exist = $filesM->load('o');
		if (empty($this->controller))$this->controller = 'files';
		if (empty($this->index)) $this->index = 'default';
		if ($exist) {
			$message->userW('1318337622EDNF');
			WPages::redirect( 'controller=' . $this->controller . '&task=upload' );
		} else {
			$uid = WUser::get('uid');
			$filesM->setVal('alias', $uploadFile['name2']);
			$filesM->setVal('name', $uploadFile['url']);
			$filesM->setVal('type','url');
			$filesM->setVal('modified',time());
			$filesM->setVal('uid', $uid );
			$uid = WUser::get('uid');
			$vendorHelperC = WClass::get( 'vendor.helper', null, 'class', false );
			if ( !empty($vendorHelperC) ) $vendid = $vendorHelperC->getVendorID( $uid );
			else $vendid = 1;
			$filesM->setVal('vendid', $vendid );
			$filesM->returnId();
			$status = $filesM->insert();
			$filid =  $filesM->filid;
			$name = $uploadFile['name2'];
			if (!empty($uploadFile['createitem2'])){					if ( WExtension::exist( 'download.node' ) ) {
					$downloadSampleData = WClass::get( 'download.sampledata' );
					$downloadSampleData->automatedDownload( $filid, $name );
				}			}
			if ($status) $message->userS('1318337622EDNG');
			else $message->userE('1315887072IZOO');
		}
		WPages::redirect('controller='.$this->controller.'&task=listing', '', $this->index);
		return true;
	}}