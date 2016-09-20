<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class Install_Widgets_class {
	public function installWidgetType($namekey,$alias,$name,$description,$groupId,$rolid=null,$publish=1) {
		$widgetypeM = WModel::get( 'install.widgetype' );
		$widgetypeM->whereE( 'namekey', $namekey );
		if ( $widgetypeM->exist() ) return true;
		$widgetypeM->namekey = $namekey;
		$widgetypeM->core = 1;
		$widgetypeM->alias = $alias;
		$widgetypeM->setChild( 'install.widgetypetrans', 'name', $name );
		$widgetypeM->setChild( 'install.widgetypetrans', 'description', $description );
		$widgetypeM->groupid = $groupId;
		if ( empty($rolid) ) $rolid = 'allusers';
		$widgetypeM->rolid = WRole::getRole( $rolid, 'rolid' );
		$widgetypeM->publish = $publish;
		$widgetypeM->created = time();
		$widgetypeM->save();
		return true;
	}
	public function installTable($modelName,$valuesA) {
		$selectA = array();
		foreach( $valuesA[0] as $key => $val ) {
			$selectA[] = $key;
		}
		$totalData = count($valuesA);
		$maxQuery = 20;
		$count = 0;
		do {
			$modelM = WModel::get( $modelName );
			if ( empty( $modelM ) ) break;
			$datosA = array();
			for( $i = 0; $i < $maxQuery; $i++ ) {
				$datosA[] = $valuesA[$count];
				$count++;
				if ( $count >= $totalData ) break;
			}
			$modelM->setIgnore();
			$status = $modelM->insertMany( $selectA, $datosA );
		} while( $count < $totalData );
	}
	public function installWidgets($extensionNamekey) {
		if ( empty($extensionNamekey) ) return false;
		$allWidgetsA = $this->_loadWidgetsA( $extensionNamekey );
		if ( empty($allWidgetsA) ) return false;
		foreach( $allWidgetsA as $oneWidget ) {
			$this->_installWidget( $oneWidget );
		}
		return true;
	}
	public function installViewWidgets($yid) {
		$viewNamekey = WView::get( $yid, 'namekey' );
		if ( empty($viewNamekey) ) return false;
		$wid = WView::get( $yid, 'wid' );
		$allWidgetsA = $this->_loadWidgetsA( $wid );
		if ( empty($allWidgetsA) ) return false;
		$installedForms = false;
		foreach( $allWidgetsA as $oneWidget ) {
			if ( !empty($oneWidget->view) && $viewNamekey != $oneWidget->view ) continue;
			if ( !empty($oneWidget->formElement) ) {
				$formElement = $oneWidget->formElement;
				unset( $oneWidget->formElement );
			} else {
				$formElement = null;
			}
			$oneWidget->widgetid = $this->_installWidget( $oneWidget, $yid );
			if ( !empty($formElement) ) {
				$this->_installFormElement( $oneWidget, $yid, $formElement );
				$installedForms = true;
			}
		}
		if ( $installedForms ) {
			$extensionHelperC = WCache::get();
			$extensionHelperC->resetCache( 'Views' );
		}
		return true;
	}
	private function _loadWidgetsA($extensionID) {
		if ( empty($extensionID) ) return false;
		$folder = WExtension::get( $extensionID, 'folder' );
		if ( empty($folder) ) return false;
		if ( !class_exists('Install_Node_install') ) WLoadFile( 'install.class.install' );
		$className = ucfirst( $folder ) . '_Node_install';
		if ( ! class_exists($className) ) {
			WLoadFile( $folder . '.install.install' );
			if ( !class_exists( $className ) ) return false;
		}
		WText::load( $extensionID );
		$installClassC = new $className();
		if ( empty($installClassC) ) return false;
		if ( ! method_exists( $installClassC, 'addWidgets' ) ) return false;
		$allWidgetsA = $installClassC->addWidgets();
		$this->_checkViewExist( $allWidgetsA );
		return $allWidgetsA;
	}
	private function _checkViewExist($allWidgetsA) {
		if ( empty( $allWidgetsA ) ) return false;
		foreach( $allWidgetsA as $widget ) {
			if ( !empty($widget->view) ) {
				if ( ! WView::get( $widget->view, 'yid', null, null, false ) ) $this->_createView( $widget );
			}
		}
	}
	private function _createView($view) {
		$namekey = $view->view;
		$mainViewM = WModel::get( 'library.view' );
		$mainViewM->whereE( 'namekey', $namekey );
		if ( $mainViewM->exist() ) return true;
		if ( empty($view->viewName) ) $view->viewName = 'View: ' . $namekey;
		if ( empty($view->viewAlias) ) $view->viewAlias = 'View: ' . $namekey;
		$mainViewM->namekey = $namekey;
		$mainViewM->wid = WExtension::get( 'catalog.node' );
		$mainViewM->setChild( 'library.viewtrans', 'name', $view->viewName );
		if ( !empty($view->viewDesc ) ) $mainViewM->setChild( 'library.viewtrans', 'description', $view->viewDesc );
		$mainViewM->alias = $view->viewAlias;
		$mainViewM->type = 109;
		$mainViewM->frontend = 1;
		$mainViewM->widgets = 1;
		$mainViewM->useredit = 1;
		$mainViewM->rolid = WRoles::getRole( 'allusers' );
		$mainViewM->core = 0;
		$mainViewM->publish = 1;
		$mainViewM->save();
	}
	private function _installFormElement($oneWidget,$yid,$formElement) {
		$mainViewFormM = WModel::get( 'design.viewforms' );
		$parent = 0;
		$namekeyStart = WView::get( $yid, 'namekey' )  . '_' . $oneWidget->namekey;
		$ordering = ( !empty($formElement->ordering) ? $formElement->ordering : 1 );
		if ( !empty($formElement->container) ) {
			$namekeyContainer = $namekeyStart . '_container';
			if ( ! ( $parent = $this->_checkFromAlreadyInstalled( $namekeyContainer ) ) ) {
				$mainViewFormM->setChild( 'design.viewformstrans', 'name', $oneWidget->name );
				if ( !empty($oneWidget->description) ) $mainViewFormM->setChild( 'main.viewformtrans', 'description', $oneWidget->description );
				$mainViewFormM->yid = WView::get( $yid, 'yid' );
				$mainViewFormM->type = $formElement->container;
				if ( !empty($formElement->position) )$mainViewFormM->area = $formElement->position;
				$mainViewFormM->ordering = $ordering;
				$mainViewFormM->namekey = $namekeyContainer;
				$mainViewFormM->core = 0;
				$mainViewFormM->publish = 1;
				$mainViewFormM->returnId();
				$mainViewFormM->save();
				$parent = $mainViewFormM->fid;
			} else {
			}
			$ordering += 500;
		}
		$namekeyWidget = $namekeyStart . '_widget';
		$myWidgetParams = 'notitle=1
spantit=1
widgetid=' . $oneWidget->widgetid;
		if ( ! ( $fid = $this->_checkFromAlreadyInstalled( $namekeyWidget ) ) ) {
			$mainViewFormM->fid = null;
			$mainViewFormM->setChild( 'design.viewformstrans', 'name', $oneWidget->name );
			$mainViewFormM->yid = WView::get( $yid, 'yid' );
			$mainViewFormM->type = 'main.widget';
			if ( !empty($formElement->position) )$mainViewFormM->area = $formElement->position;
			$mainViewFormM->ordering = $ordering;
			$mainViewFormM->namekey = $namekeyWidget;
			$mainViewFormM->parent = $parent;
			$mainViewFormM->core = 0;
			$mainViewFormM->publish = 1;
			$mainViewFormM->params = $myWidgetParams;
			$mainViewFormM->save();
		} else {
			$mainViewFormM->whereE( 'fid', $fid );
			$mainViewFormM->setVal( 'params', $myWidgetParams );
			$mainViewFormM->update();
		}
	}
	private function _checkFromAlreadyInstalled($namekey) {
		$libraryViewFormM = WModel::get( 'library.viewforms' );
		$libraryViewFormM->whereE( 'namekey', $namekey );
		return $libraryViewFormM->load( 'lr', 'fid' );
	}
	private function _installWidget($oneWidget,$yid=0) {
		static $mainWidgetC = null;
		static $allusers = 0;
		if ( empty($allusers) ) {
			$allusers = WRole::getRole( 'allusers' );
		}
		$widgetM = WModel::get( 'main.widget' );
		$widgetM->whereE( 'namekey', $oneWidget->namekey );
		$widgetid = $widgetM->load( 'lr', 'widgetid' );
		if ( !empty($widgetid) ) return $widgetid;
		$widgetM->namekey = $oneWidget->namekey;
		$widgetM->alias = $oneWidget->alias;
		$widgetM->framework_type = ( !empty($oneWidget->framework_type) ? $oneWidget->framework_type : 5 );
		if ( empty( $yid ) && !empty($oneWidget->view) ) {
			$yid = WView::get( $oneWidget->view, 'yid' );
		}
		$widgetM->framework_id = $yid;
		$widgetM->core = 0;	
		$widgetM->publish = ( isset($oneWidget->publish) ? $oneWidget->publish : 1 );
		if ( empty($mainWidgetC) ) $mainWidgetC = WClass::get( 'main.widget', null, 'class', false );
		$widgetM->wgtypeid = $mainWidgetC->getWidgetTypeID( $oneWidget->widgetType );
		if ( empty($widgetM->wgtypeid) ) return false;
		$widgetM->setChild( 'main.widgettrans', 'name', $oneWidget->name );
		if ( !empty($oneWidget->description) ) $widgetM->setChild( 'main.widgettrans', 'description', $oneWidget->description );
		$widgetM->rolid = ( !empty($oneWidget->rolid) ? $oneWidget->rolid : $allusers );
		if ( !empty($oneWidget->params) ) $widgetM->params = $oneWidget->params;
		$widgetM->returnId();
		$widgetM->setIgnore();
		$status = $widgetM->save();
		if ( $status ) return $widgetM->widgetid;
		else return false;
	}
}
