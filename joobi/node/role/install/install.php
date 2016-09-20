<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Role_Node_install extends WInstall {
public function install(&$object)  {
if(!empty($this->newInstall ) || (property_exists($object, 'newInstall') && $object->newInstall)){
$installWidgetsC=WClass::get('install.widgets');
$installWidgetsC->installTable('role',$this->_installValuesA());
$installWidgetsC->installTable('roletrans',$this->_installValues2A());
}
return true;
}
public function addExtensions(){
if( JOOBI_FRAMEWORK_TYPE !='joomla') return true;
$extension=new stdClass;
$extension->namekey='role.content.plugin';
$extension->name='Joobi - Role Restriction to Joomla Articles';
$extension->folder='content';
$extension->type=50;
$extension->publish=1;
$extension->certify=1;
$extension->destination='node|role|plugin';
$extension->core=1;
$extension->params='publish=0';
$extension->description='';
if($this->insertNewExtension($extension ))$this->installExtension($extension->namekey );
$extension=new stdClass;
$extension->namekey='role.system.plugin';
$extension->name='Joobi - Role Restriction for Joomla Components';
$extension->folder='system';
$extension->type=50;
$extension->publish=1;
$extension->certify=1;
$extension->destination='node|role|plugin';
$extension->core=1;
$extension->params='publish=0';
$extension->description='';
if($this->insertNewExtension($extension ))$this->installExtension($extension->namekey );
}
private function _installValuesA(){
return array(
  array('rolid'=>'1','parent'=>'0','lft'=>'1','rgt'=>'70','core'=>'1','joomla'=>'28','j16'=>'1','namekey'=>'allusers','color'=>'blue','type'=>'1','depth'=>'0'),
  array('rolid'=>'2','parent'=>'1','lft'=>'2','rgt'=>'71','core'=>'1','joomla'=>'29','j16'=>'1','namekey'=>'visitor','color'=>'aqua','type'=>'1','depth'=>'1'),
  array('rolid'=>'3','parent'=>'2','lft'=>'2','rgt'=>'69','core'=>'1','joomla'=>'18','j16'=>'2','namekey'=>'registered','color'=>'navy','type'=>'1','depth'=>'1'),
  array('rolid'=>'4','parent'=>'3','lft'=>'3','rgt'=>'22','core'=>'1','joomla'=>'19','j16'=>'3','namekey'=>'author','color'=>'teal','type'=>'1','depth'=>'1'),
  array('rolid'=>'5','parent'=>'4','lft'=>'4','rgt'=>'13','core'=>'1','joomla'=>'20','j16'=>'4','namekey'=>'editor','color'=>'silver','type'=>'1','depth'=>'2'),
  array('rolid'=>'6','parent'=>'5','lft'=>'5','rgt'=>'12','core'=>'1','joomla'=>'21','j16'=>'5','namekey'=>'publisher','color'=>'maroon','type'=>'1','depth'=>'3'),
  array('rolid'=>'7','parent'=>'6','lft'=>'6','rgt'=>'11','core'=>'1','joomla'=>'23','j16'=>'6','namekey'=>'manager','color'=>'orange','type'=>'1','depth'=>'4'),
  array('rolid'=>'8','parent'=>'7','lft'=>'7','rgt'=>'10','core'=>'1','joomla'=>'24','j16'=>'7','namekey'=>'admin','color'=>'fuchsia','type'=>'1','depth'=>'5'),
  array('rolid'=>'9','parent'=>'8','lft'=>'8','rgt'=>'9','core'=>'1','joomla'=>'25','j16'=>'8','namekey'=>'sadmin','color'=>'red','type'=>'1','depth'=>'6'),
  array('rolid'=>'11','parent'=>'3','lft'=>'23','rgt'=>'24','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'customer','color'=>'green','type'=>'1','depth'=>'1'),
  array('rolid'=>'12','parent'=>'4','lft'=>'14','rgt'=>'19','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'supplier','color'=>'olive','type'=>'1','depth'=>'2'),
  array('rolid'=>'13','parent'=>'12','lft'=>'15','rgt'=>'18','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'vendor','color'=>'purple','type'=>'1','depth'=>'3'),
  array('rolid'=>'14','parent'=>'3','lft'=>'25','rgt'=>'28','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'affiliate','color'=>'teal','type'=>'1','depth'=>'1'),
  array('rolid'=>'15','parent'=>'14','lft'=>'26','rgt'=>'27','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'reseller','color'=>'black','type'=>'1','depth'=>'2'),
  array('rolid'=>'16','parent'=>'3','lft'=>'29','rgt'=>'34','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'agent','color'=>'teal','type'=>'1','depth'=>'1'),
  array('rolid'=>'17','parent'=>'30','lft'=>'30','rgt'=>'31','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'supportmanager','color'=>'orange','type'=>'1','depth'=>'0'),
  array('rolid'=>'18','parent'=>'3','lft'=>'35','rgt'=>'40','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'mailauthor','color'=>'teal','type'=>'1','depth'=>'1'),
  array('rolid'=>'19','parent'=>'18','lft'=>'34','rgt'=>'39','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'maileditor','color'=>'silver','type'=>'1','depth'=>'2'),
  array('rolid'=>'20','parent'=>'19','lft'=>'35','rgt'=>'38','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'mailpublisher','color'=>'maroon','type'=>'1','depth'=>'3'),
  array('rolid'=>'21','parent'=>'13','lft'=>'16','rgt'=>'17','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'storemanager','color'=>'red','type'=>'1','depth'=>'4'),
  array('rolid'=>'30','parent'=>'16','lft'=>'30','rgt'=>'33','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'moderator','color'=>'black','type'=>'1','depth'=>'1'),
  array('rolid'=>'31','parent'=>'3','lft'=>'41','rgt'=>'48','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'groupmoderator','color'=>'teal','type'=>'1','depth'=>'1'),
  array('rolid'=>'32','parent'=>'31','lft'=>'42','rgt'=>'47','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'groupmanager','color'=>'maroon','type'=>'1','depth'=>'2'),
  array('rolid'=>'33','parent'=>'32','lft'=>'43','rgt'=>'46','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'communitymoderator','color'=>'orange','type'=>'1','depth'=>'3'),
  array('rolid'=>'34','parent'=>'33','lft'=>'44','rgt'=>'45','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'communitymanager','color'=>'red','type'=>'1','depth'=>'4'),
  array('rolid'=>'35','parent'=>'3','lft'=>'49','rgt'=>'54','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'articleauthor','color'=>'teal','type'=>'1','depth'=>'1'),
  array('rolid'=>'36','parent'=>'35','lft'=>'50','rgt'=>'53','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'articleeditor','color'=>'silver','type'=>'1','depth'=>'2'),
  array('rolid'=>'37','parent'=>'36','lft'=>'51','rgt'=>'52','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'articlepublisher','color'=>'maroon','type'=>'1','depth'=>'3'),
  array('rolid'=>'38','parent'=>'20','lft'=>'36','rgt'=>'37','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'mailmanager','color'=>'orange','type'=>'1','depth'=>'4'),
  array('rolid'=>'39','parent'=>'3','lft'=>'55','rgt'=>'56','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'listmanager','color'=>'black','type'=>'1','depth'=>'1'),
  array('rolid'=>'43','parent'=>'4','lft'=>'20','rgt'=>'21','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'sales_support','color'=>'black','type'=>'1','depth'=>'2'),
  array('rolid'=>'45','parent'=>'43','lft'=>'21','rgt'=>'20','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'sales_agent','color'=>'purple','type'=>'1','depth'=>'3'),
  array('rolid'=>'46','parent'=>'45','lft'=>'20','rgt'=>'21','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'sales_manager','color'=>'red','type'=>'1','depth'=>'4'),
  array('rolid'=>'48','parent'=>'3','lft'=>'57','rgt'=>'62','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'developer','color'=>'purple','type'=>'1','depth'=>'2'),
  array('rolid'=>'49','parent'=>'48','lft'=>'58','rgt'=>'61','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'developer_manager','color'=>'orange','type'=>'1','depth'=>'3'),
  array('rolid'=>'50','parent'=>'49','lft'=>'59','rgt'=>'60','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'developer_architec','color'=>'red','type'=>'1','depth'=>'4'),
  array('rolid'=>'51','parent'=>'3','lft'=>'63','rgt'=>'68','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'project_member','color'=>'purple','type'=>'1','depth'=>'2'),
  array('rolid'=>'52','parent'=>'51','lft'=>'64','rgt'=>'67','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'project_manager','color'=>'orange','type'=>'1','depth'=>'3'),
  array('rolid'=>'53','parent'=>'52','lft'=>'65','rgt'=>'66','core'=>'1','joomla'=>'0','j16'=>'0','namekey'=>'project_admin','color'=>'red','type'=>'1','depth'=>'4')
);
}
private function _installValues2A(){
return array(
  array('rolid'=>'1','lgid'=>'1','name'=> WText::t('1211280059QYRD') ,'description'=> WText::t('1212510541LVCO'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'2','lgid'=>'1','name'=> WText::t('1211280059QYRF') ,'description'=> WText::t('1212510541LVCO'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'3','lgid'=>'1','name'=> WText::t('1206732411EGSE') ,'description'=> WText::t('1212510541LVCP'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'4','lgid'=>'1','name'=> WText::t('1206732400OWZO') ,'description'=> WText::t('1212510541LVCQ'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'5','lgid'=>'1','name'=> WText::t('1211280059QYRJ') ,'description'=> WText::t('1298350313CJWK'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'6','lgid'=>'1','name'=> WText::t('1211280059QYRL') ,'description'=> WText::t('1298350313CJWL'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'7','lgid'=>'1','name'=> WText::t('1211280059QYRN') ,'description'=> WText::t('1213093169PGHQ'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'8','lgid'=>'1','name'=> WText::t('1206961998KTYJ') ,'description'=> WText::t('1212510541LVCU'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'9','lgid'=>'1','name'=> WText::t('1211280059QYRQ') ,'description'=> WText::t('1212510541LVCV'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'11','lgid'=>'1','name'=> WText::t('1206961912MJPB') ,'description'=> WText::t('1298350313CJWM'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'12','lgid'=>'1','name'=> WText::t('1298350313CJWO') ,'description'=> WText::t('1298350313CJWN'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'13','lgid'=>'1','name'=> WText::t('1221228435BYUA') ,'description'=> WText::t('1298520787FNQP'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'14','lgid'=>'1','name'=> WText::t('1240888717QTMV') ,'description'=> WText::t('1298958645SVBQ'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'15','lgid'=>'1','name'=> WText::t('1298958645SVCF') ,'description'=> WText::t('1298958645SVBR'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'16','lgid'=>'1','name'=> WText::t('1298958645SVCG') ,'description'=> WText::t('1298958645SVBS'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'17','lgid'=>'1','name'=> WText::t('1298958645SVCH') ,'description'=> WText::t('1298958645SVBT'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'18','lgid'=>'1','name'=> WText::t('1298958645SVCI') ,'description'=> WText::t('1298958645SVBU'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'19','lgid'=>'1','name'=> WText::t('1298958645SVCE') ,'description'=> WText::t('1298958645SVBP'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'20','lgid'=>'1','name'=> WText::t('1298958645SVCD') ,'description'=> WText::t('1298958645SVBO'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'21','lgid'=>'1','name'=> WText::t('1298350804GFLD') ,'description'=> WText::t('1298520787FNQQ'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'30','lgid'=>'1','name'=> WText::t('1298958645SVCK') ,'description'=> WText::t('1298958645SVBW'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'31','lgid'=>'1','name'=> WText::t('1298958645SVCJ') ,'description'=> WText::t('1298958645SVBV'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'32','lgid'=>'1','name'=> WText::t('1298958645SVCL') ,'description'=> WText::t('1298958645SVBX'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'33','lgid'=>'1','name'=> WText::t('1298958645SVCM') ,'description'=> WText::t('1298958645SVBY'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'34','lgid'=>'1','name'=> WText::t('1298958645SVCN') ,'description'=> WText::t('1298958645SVBZ'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'35','lgid'=>'1','name'=> WText::t('1298958645SVCO') ,'description'=> WText::t('1298958645SVCA'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'36','lgid'=>'1','name'=> WText::t('1298958645SVCP') ,'description'=> WText::t('1298958645SVCB'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'37','lgid'=>'1','name'=> WText::t('1298958645SVCQ') ,'description'=> WText::t('1298958645SVCC'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'38','lgid'=>'1','name'=> WText::t('1300366053JXYR') ,'description'=> WText::t('1300366052PZRW'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'39','lgid'=>'1','name'=> WText::t('1300773989BZML') ,'description'=> WText::t('1300773988BYXD'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'43','lgid'=>'1','name'=> WText::t('1360107595TFTS') ,'description'=> WText::t('1360107595TFTR'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'45','lgid'=>'1','name'=> WText::t('1358040638TETR') ,'description'=> WText::t('1358040638TETP'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'46','lgid'=>'1','name'=> WText::t('1358040638TETS') ,'description'=> WText::t('1358040638TETQ'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'48','lgid'=>'1','name'=> WText::t('1360925653IBLM') ,'description'=> WText::t('1360925653IBLJ'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'49','lgid'=>'1','name'=> WText::t('1360925653IBLN') ,'description'=> WText::t('1360925653IBLK'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'50','lgid'=>'1','name'=> WText::t('1360925653IBLL') ,'description'=> WText::t('1360925653IBLI'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'51','lgid'=>'1','name'=> WText::t('1373207701KOTA') ,'description'=> WText::t('1373207700DBFX'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'52','lgid'=>'1','name'=> WText::t('1373207701KOTB') ,'description'=> WText::t('1373207700DBFY'),'auto'=>'1','fromlgid'=>'0'),
  array('rolid'=>'53','lgid'=>'1','name'=> WText::t('1410185242NOSJ') ,'description'=> WText::t('1410185242NOSI'),'auto'=>'1','fromlgid'=>'0')
);
}
}