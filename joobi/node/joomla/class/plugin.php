<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Joomla_Plugin_class extends WClasses {
	public function myItems($profileID=null) {
		if ( !isset($profileID) ) $profileID = WGlobals::get( 'userid' );
		$uid = WUser::get( 'uid', $profileID, false );	
		if ( !empty( $uid ) ) {
			$vendorHelperC = WClass::get( 'vendor.helper', null, 'class', false );
			if ( !empty($vendorHelperC) ) $vendid = $vendorHelperC->getVendorID( $uid );
			else $vendid = 1;
		}
 		if ( empty( $vendid ) || empty( $uid ) ) return false;
		WGlobals::set( 'integrationmemberid', $uid );
		WGlobals::set( 'extensionKEY', 'item.node', 'global' );
		WText::load( 'joomla.node' );
		$integrationV = WView::get( 'item_vendor_products_widget' );
		if ( empty($integrationV) ) return WText::t('1404396090RVSP');
		$HTMLoutput = $integrationV->make();
		if ( empty($HTMLoutput) ) return WText::t('1404396090RVSP');
		return $HTMLoutput;
	}
	public function myOrders($profileID=null) {
		if ( empty($profileID) ) return '';
		$memberId = WUser::get( 'uid', $profileID, false );
		$LogMemberId = WUser::get('uid');
				if ( empty($memberId) || $memberId!=$LogMemberId ) return '';
				WGlobals::set( 'integrationmemberid', $memberId );
		WGlobals::set( 'extensionKEY', 'order.node', 'global' );
		WText::load( 'joomla.node' );
				$integrationV = WView::get( 'order_widget_integration' );
		if ( empty($integrationV) ) return WText::t('1404396090RVSQ');
		$HTMLoutput = $integrationV->make();
		if ( empty($HTMLoutput) ) return WText::t('1404396090RVSQ');
		return $HTMLoutput;
	}
	public function mySubscriptionOnProfileDisplay($profileID=null) {
				$memberId = WUser::get( 'uid', $profileID, false );
				if ( empty($memberId) ) return '';
		$LogMemberId = WUser::get('uid');
				if ( empty($memberId) || $memberId != $LogMemberId ) return '';
				WGlobals::set( 'integrationmemberid', $memberId );
		WGlobals::set( 'extensionKEY', 'subscription.node', 'global' );
		WText::load( 'joomla.node' );
				$integrationV = WView::get( 'subscription_widget_integration' );
		if ( empty($integrationV) ) return '';
		$HTMLoutput = $integrationV->make();
			return $HTMLoutput;
	}
	public function mySubscriptionOnUserRegisterFormDisplay($subonregform,$subtitle,$paid) {
				if (!$subonregform) return;
		$selected = WPref::load( 'PSUBSCRIPTION_NODE_SELECTED' );
		if ( empty($selected) ) return '';
	  	$subHelp = WClass::get( 'subscription.helper', null, 'class', false );
		$pids= $subHelp->getSelectedSubscription( $selected );
		$html = '';
						if ( !empty($pids) ) {
			$html .= '<table align="center">';
									if (!empty($subtitle)) {
								$html .= '<tr><td class="titleCell" colspan="2"><h4>'. $subtitle .'</h4></td></tr>';
			}
			$i=0;
						$this->_checkVal( $paid );
			$subpid=WGlobals::get('pid');
			if ( empty($subpid) ) $subpid = $paid;
			foreach( $pids as $pid ) {
				$checked='';
				$link=$this->_getlink($pid->pid);
				if ($subpid==$pid->pid) $checked='checked';
				$html .= '<tr>';
								$text = "\n".'<td><a href="'.$link.'"><input type="checkbox" name="subscription['.$pid->pid.']" value="'.$pid->pid.'"'.$checked.' onclick="window.location=\''.$link.'\';"> </a>'.$pid->name.'<br></td>';
				$html .= $text;
								$html .= '</tr>';
				$i++;
			}
		}
		$html .= '</table>';
				echo $html;
	}
	public function mySubscriptionOnSystemStart($pluginObj) {
				$view = WGlobals::get( 'view' );
		$task = WGlobals::get( 'task' );
				$subscriptionCheckC = WClass::get( 'subscription.check' );
				switch( $view ) {
		case 'groups':
					switch( $task ) {
						case 'create':
								$creategrouprest = $pluginObj->params->get('creategrouprest', '');
				if ($creategrouprest==0) return true;
								$subscriptionCheckC->restriction( 'joomsocial-groupcreate', '' ,'You cannot create a Group. You need to subscribe first.');
			break;
						case 'addnews':
								$bulletinrest = $pluginObj->params->get('bulletinrest', '');
				if ($bulletinrest==0) return true;
								$subscriptionCheckC->restriction( 'joomsocial-createbulletin', '' ,'You cannot create announcement/bulletin. You need to subscribe first.');
			break;
						case 'adddiscussion':
								$discussionrest = $pluginObj->params->get('discussionrest', '');
				if ($discussionrest==0) return true;
								$subscriptionCheckC->restriction( 'joomsocial-creatediscussion', '' ,'You cannot create discussion. You need to subscribe first.');
			break;
			case 'joingroup':
							$joingrouprest = $pluginObj->params->get('joingrouprest', '');
			if ($joingrouprest==0) return true;
			$gid= WGlobals::get( 'groupid' );
						$subscriptionCheckC->restriction( 'joomsocial-groupjoin', 'groupid='.$gid ,'You cannot join the Group. You need to subscribe first.');
			break;
			case 'viewgroup':
							$viewgrouprest = $pluginObj->params->get('viewgrouprest', '');
			if ($viewgrouprest==0) return true;
			$gid= WGlobals::get( 'groupid' );
						$subscriptionCheckC->restriction( 'joomsocial-groupview', 'groupid='.$gid ,'You cannot view the Group. You need to subscribe first.');
			break;
			default:
			break;
		}
		break;
		case 'photos':
			switch($task) {
				case 'newalbum':
									$createalbum = $pluginObj->params->get('createalbum', '');
				if ($createalbum==0) return true;
								$subscriptionCheckC->restriction( 'joomsocial-createalbum', '' ,'You cannot create New Album. You need to subscribe first.');
				break;
				case 'uploader':
									$uploader = $pluginObj->params->get('uploader', '');
				if ($uploader==0) return true;
								$subscriptionCheckC->restriction( 'joomsocial-uploader', '' ,'You cannot upload photos. You need to subscribe first.');
				break;
				default:
				break;
			}
		break;
		case 'inbox':
			switch( $task ) {
				case 'write':
									$writemessage = $pluginObj->params->get('writemessage', '');
				if ($writemessage==0) return true;
								$subscriptionCheckC->restriction( 'joomsocial-writemessage', '' ,'You cannot write message. You need to subscribe first.');
				break;
				default:
				break;
			}
		break;
		case 'videos':
			switch($task) {
				case 'link':
									$uploadvideo = $pluginObj->params->get('uploadvideo', '');
				if ($uploadvideo==0) return true;
								$subscriptionCheckC->restriction( 'joomsocial-uploadvideo', '' ,'You cannot upload video. You need to subscribe first.');
				break;
				case 'upload':
									$uploadvideo = $pluginObj->params->get('uploadvideo', '');
				if ($uploadvideo==0) return true;
								$subscriptionCheckC->restriction( 'joomsocial-uploadvideo', '' ,'You cannot upload video. You need to subscribe first.');
				break;
				case 'video':
									$viewvideo = $pluginObj->params->get('viewvideo', '');
				if ($viewvideo==0) return true;
								$subscriptionCheckC->restriction( 'joomsocial-viewvideo', '' ,'You cannot view video. You need to subscribe first.');
				break;
				default:
				break;
			}
		break;
		case 'register':
			switch($task) {
				case 'registerSucess':
				$session = JFactory::getSession();
				$password = WGlobals::getSession( 'jsubpass', 'jsubpass');
	  		 		  			$user	= $session->get('tmpUser');
	  			$user->password_clear = $password;
	  			$this->_automaticLogin( $user );
	  			$pid=$session->get('pid', '','jSubscription');
				if (!empty($pid)) {
					$myLink = ( WPref::load( 'PCART_NODE_USENEWCART' ) ? 'controller=cart&task=additem' : 'controller=basket&task=addbasket' );
					WPages::redirect( 'index.php?option=com_jsubscription&' . $myLink . '&eid=' . $pid );
				}								$session->clear( 'pid','jSubscription' );
				break;
				default:
				break;
			}
		break;
		default:
		break;
		}
	}
	private function _automaticLogin($user) {
		$usersM = WModel::get( 'users');
				$usersM->whereE( 'username', $user->username );
		$usersM->setVal( 'confirmed', 1 );
		$usersM->setVal( 'block', 0 );
		$usersM->update();
				$db = JFactory::getDBO();
		$db->setQuery( 'UPDATE `#__users` SET `block` = 0 WHERE `username`= "'.$user->username.'"' );
		$db->loadResult();
		$usersCredentialC = WUser::credential();
		$usersCredentialC->automaticLogin( $user->username, $user->password_clear );
	}
	private function _checkVal($paid) {
						$pid = WGlobals::get( 'pid' );
			if (empty($pid)) $pid = $paid;
			$vall = WGlobals::get( 'vall' );
			if ( !empty($pid) ) {
								$session = JFactory::getSession();
  		 		$session->set('pid', $pid,'jSubscription');
			}
	}
	private function _getLink($i) {
				$urlInstances = JURI::getInstance();
		if ( isset($urlInstances->_uri) && !empty($urlInstances->_uri) ){
			$vall = WGlobals::get( 'vall' );
			$url = $urlInstances->_uri;
			if ( !empty( $vall ) ) $url = substr( $url, 0, '-'.$vall );
			$urlExtra = "&pid=". $i ."&vall=";
			$urlExtraMain = $urlExtra;
						$urlExtraLength = strlen( $urlExtra );
						$urlExtra .= $urlExtraLength;
						$urlExtraLength = strlen( $urlExtra );
						$url .= $urlExtraMain.$urlExtraLength;
			return $url;
		} else {
			$url = "Link Error";
			return '';
		}
	}
}