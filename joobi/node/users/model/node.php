<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Users_Node_model extends WModel {
protected $_updateCMS=true;
protected $_emailNewPassword=false;
protected $_syncContacts=true;
protected $_checkEmailValidity=true;
private $_password=null;
private $_roleC=null;
function __construct(){
$myImageO=new stdClass;
$myImageO->fileType='images';
$myImageO->folder='media';
$myImageO->path='images'.DS.'users';
$myImageO->secure=false;
$prodPref=WPref::load('PUSERS_NODE_IMGFORMAT');
if(!empty($prodPref)){
$imgFormat=explode(',',$prodPref );
}
$myImageO->format=(!empty($imgFormat))?$imgFormat : array('jpg','png','gif','jpeg');
$myImageO->thumbnail=1;
$myImageO->maxSize=WPref::load('PUSERS_NODE_IMGMAXSIZE') * 1028;
$myImageO->maxHeight=WPref::load('PUSERS_NODE_MAXPH');
if(empty($myImageO->maxHeight))$myImageO->maxHeight=50;
$myImageO->maxWidth=WPref::load('PUSERS_NODE_MAXPW');
if(empty($myImageO->maxWidth))$myImageO->maxWidth=50;
$myImageO->maxTHeight=WPref::load('PUSERS_NODE_SMALLIH');
if(empty($myImageO->maxTHeight))$myImageO->maxTHeight=20;
$myImageO->maxTWidth=WPref::load('PUSERS_NODE_SMALLIW');
if(empty($myImageO->maxTWidth))$myImageO->maxTWidth=20;
$myImageO->watermark=WPref::load('PUSERS_NODE_WATERMARKITEM');
$myImageO->storage=WPref::load('PUSERS_NODE_FILES_METHOD_PHOTOS');
$this->_fileInfo['filid']=$myImageO;
parent::__construct();
}
function validate(){
$usersEmailC=WClass::get('users.email');
if($this->_checkEmailValidity && !empty($this->email) && !$usersEmailC->validateEmail($this->email )){
$message=WMessage::get();
return $message->historyE('1298350399EIUD');
}
if(!empty($this->x['password'])){
if($this->x['password'] !=$this->x['password_confirmed']){
$message=WMessage::get();
return $message->historyE('1401465958GTFQ');
}
if(empty($this->rolid)){
if(empty($this->uid )){
$rolid=WPref::load('PUSERS_NODE_REGISTRATIONROLE');
}else{
$rolid=WUser::get('rolid',$this->uid );
}
}else{
$rolid=$this->rolid;
}
if(!isset($this->_roleC))$this->_roleC=WRole::get();
$isManager=$this->_roleC->compareRole($rolid, 'manager');
$strenght=($isManager?WPref::load('PUSERS_NODE_PWD_STRENGTH_ADMIN') : WPref::load('PUSERS_NODE_PWD_STRENGTH_REGISTER'));
$usersRegsiterC=WClass::get('users.register');
$error=$usersRegsiterC->checkPassword($this->x['password'], $strenght );
if(!empty($error)){
$message=WMessage::get();
return $message->historyE($error );
}
$this->password=$usersRegsiterC->generateHashPassword($this->x['password'] );
$this->_password=$this->x['password'];
}
if(isset($this->name))$this->name=str_replace( array('<>','"'), '',$this->name );
WController::trigger('users','onvalidate',$this );
return true;
}
function addValidate(){
if(empty($this->email )){
return false;
}
$sidV=WModel::get('vendors','sid');
$propUsed='C'.$sidV;
unset($this->$propUsed );
$sidV=WModel::get('vendorstrans','sid');
$propUsed='C'.$sidV;
unset($this->$propUsed );
$this->validateDate('registerdate');
if(empty($this->registerdate))$this->registerdate=time();
if(empty($this->username )){
$PUSERS_NODE_USERNAMEDEFAULT=WPref::load('PUSERS_NODE_USERNAMEDEFAULT');
if('email'==$PUSERS_NODE_USERNAMEDEFAULT)$this->username=$this->email;
else {
$count=0;
do {
$count++;
$this->username=WTools::randomString( 10, false);
$unique=$this->_uniqueUsername($this->username );
} while ( !$unique && $count < 10 );
if($count==10)$this->username .='_'.time();
}
}else{
$usersM=WModel::get('users');
$usersM->whereE('username',$this->username );
$existinUID=$usersM->load('lr','uid');
if(!empty($existinUID)){
$USERNAME=$this->username;
return $this->historyE('1424728189PIMT',array('$USERNAME'=>$USERNAME));
}
}
if(empty($this->name ))$this->name=$this->username;
if( WExtension::exist('contacts.node') && WPref::load('PCONTACTS_NODE_SPLITNAME')){
$name='';
if(!empty($this->x['firstname'])){
$name .=$this->x['firstname'];
}
if( WPref::load('PCONTACTS_NODE_MIDDLENAME') && !empty($this->x['middlename'])){
if(!empty($name))$name .=' ';
$name .=$this->x['middlename'];
}
if(!empty($this->x['lastname'])){
if(!empty($name))$name .=' ';
$name .=$this->x['lastname'];
}
if(!empty($name)){
$this->name=$name;
}
}
$onRegsitration=WGlobals::set('userOnRegister', false, 'global');
if($onRegsitration && !isset($this->rolid)){
$this->rolid=WPref::load('PUSERS_NODE_REGISTRATIONROLE');
}
if(empty($this->rolid )){
$this->rolid=WRole::getRole('visitor');
}
if(!isset($this->_roleC))$this->_roleC=WRole::get();
$isRegister=$this->_roleC->compareRole($this->rolid, 'registered');
if($isRegister){
$this->registered=1;
if(empty($this->password )){
$usersRegsiterC=WClass::get('users.register');
$password=WTools::randomString( 14, true);
$this->password=$usersRegsiterC->generateHashPassword($password );
}
}
if(!isset($this->confirmed))$this->confirmed=0;
$activationmethod=WPref::load('PUSERS_NODE_ACTIVATIONMETHOD');
switch ($activationmethod){
case 'admin':
if(isset($this->blocked))$this->blocked=1;
break;
case 'self':
if(isset($this->blocked))$this->blocked=1;
break;
default:
if(isset($this->blocked))$this->blocked=0;
break;
}
$memberSessionC=WUser::session();
$ip=$memberSessionC->getIP();
if(!empty($ip )){
if(!isset($this->ip))$this->ip=$ip;
$ipLookupC=WClass::get('security.lookup', null, 'class', false);
if(!empty($ipLookupC)){
$localization=$ipLookupC->detectIP($ip );
if(!isset($this->timezone) || $this->timezone==999){
if(isset($localization->country->timezone)){
$this->timezone=$localization->country->timezone;
}
}
if(!isset($this->ctyid)){
if(!empty($localization->country->ctyid))$this->ctyid=$localization->country->ctyid;
}
}
}
$this->returnId();
return true;
}
function editValidate(){
if( WExtension::exist('contacts.node') && WPref::load('PCONTACTS_NODE_SPLITNAME')){
$name='';
$firstName=$this->getChild('contacts.details','first_name');
if(!empty($firstName)){
$name .=$firstName;
}
$middleName=$this->getChild('contacts.details','middle_name');
if( WPref::load('PCONTACTS_NODE_MIDDLENAME') && !empty($middleName)){
if(!empty($name))$name .=' ';
$name .=$middleName;
}
$lastName=$this->getChild('contacts.details','last_name');
if(!empty($lastName)){
if(!empty($name))$name .=' ';
$name .=$lastName;
}
if(!empty($name)){
$this->name=$name;
}
}
if(empty($this->name)){
$this->name='';
}
return true;
}
function addExtra(){
if($this->_syncContacts){
if( WExtension::exist('contacts.node')){
$contactsDetailsM=WModel::get('contacts.details');
$contactsDetailsM->whereE('uid',$this->uid );
$existContact=$contactsDetailsM->load('lr','uid');
if(empty($existContact)){
$contactsDetailsM->setVal('uid',$this->uid );
}else{
$contactsDetailsM->whereE('uid',$this->uid );
}
$firstName='';
$middleName='';
$lastName='';
if( WPref::load('PCONTACTS_NODE_SPLITNAME')){
if(!empty($this->_x['firstname'])){
$firstName=$this->_x['firstname'];
}
if( WPref::load('PCONTACTS_NODE_MIDDLENAME') && !empty($this->_x['middlename'])){
$middleName=$this->_x['middlename'];
}
if(!empty($this->_x['lastname'])){
$lastName=$this->_x['lastname'];
}
}else{
if(!empty($this->name)){
$nameA=explode(' ',$this->name );
$count=count($nameA );
if($count <=1){
$lastName=$nameA[0];
}elseif($count <=2){
$firstName=$nameA[0];
$lastName=$nameA[1];
}else{
$firstName=array_shift($nameA);
$middletName=array_shift($nameA);
$lastName=implode(' ',$nameA );
}
}
}
if(!empty($firstName))$contactsDetailsM->setVal('first_name',$firstName );
if(!empty($middletName))$contactsDetailsM->setVal('middle_name',$middletName );
if(!empty($lastName))$contactsDetailsM->setVal('last_name',$lastName );
if(empty($existContact)){
$contactsDetailsM->insertIgnore();
}else{
$contactsDetailsM->update();
}
}
}
$usersRegisterC=WClass::get('users.register');
if(!empty($this->registered )){
if(!empty($this->_x['password']))$password=$this->_x['password'];
else $password='';
$frameworkFE=WPref::load('PUSERS_NODE_FRAMEWORK_FE');
if( WRoles::isNotAdmin('admin') && in_array($frameworkFE, array('users','contacts'))){
$activationmethod=WPref::load('PUSERS_NODE_ACTIVATIONMETHOD');
switch ($activationmethod){
case 'admin':
$usersRegisterC=WClass::get('users.register');
if(!empty($this->uid )){
$usersRegisterC->sendAdminApproval($this->uid );
$emailpwd=WPref::load('PUSERS_NODE_EMAILPWD');
if(!empty($emailpwd) || $this->_emailNewPassword){
$usersRegisterC->emailPassword($this->uid, $password );
}
}
break;
case 'self':
$usersRegisterC=WClass::get('users.register');
if(!empty($this->uid )){
$usersRegisterC->sendSelfApproval($this->uid, $password );
if($this->_emailNewPassword)$usersRegisterC->emailPassword($this->uid, $password );
}
break;
default:
if(empty($this->blocked )){
if(!empty($password)){
if(!empty($this->uid ))$usersRegisterC->emailPassword($this->uid, $password );
else $this->userE('1401818484FRIA');
}
}
break;
}
}
}
$usersRegisterC->notifyAdmin($this->uid );
$this->_updateCMSUser();
return true;
}
function editExtra(){
if(!empty($this->id)){
$id=$this->id;
}else{
$id=WUser::get('id',$this->uid );
}
$this->_updateCMSUser($id );
return true;
}
public function setEmailValidation($validate=true){
$this->_checkEmailValidity=$validate;
}
public function updateFrameworkUser($update=false){
$this->_updateCMS=$update;
}
public function emailNewPassword($pwd=false){
$this->_emailNewPassword=$pwd;
}
function extra(){
WController::trigger('users','onExtra',$this );
if(!empty($this->password)){
if( WExtension::exist('security.node')){
$period=WPref::load('PSECURITY_NODE_USER_PWD_PERIOD');
if($period){
$usersDetailsM=WModel::get('users.details');
$usersDetailsM->whereE('uid',$this->uid );
$usersDetailsM->setVal('datepwd', time());
$usersDetailsM->update();
if(!$usersDetailsM->affectedRows()){
$usersDetailsM->setVal('uid',$this->uid );
$usersDetailsM->setVal('datepwd', time());
$usersDetailsM->insertIgnore();
}
}}}
return true;
}
function deleteValidate($eid=0){
if($eid==WUser::get('uid')){
$this->userE('1402327860NWWM');
return false;
}
$this->_deleteInfoO=WUser::get('data',$eid );
WController::trigger('users','deleteValidate',$this );
return true;
}
function deleteExtra($eid=0){
$usersAddon=WAddon::get('api.'.JOOBI_FRAMEWORK.'.user');
if(!empty($this->_deleteInfoO))$status=$usersAddon->deleteUser($this->_deleteInfoO );
WController::trigger('users','deleteExtra',$this );
return true;
}
private function _updateCMSUser($id=0){
if(!$this->_updateCMS ) return true;
$framworkRole=WPref::load('PUSERS_NODE_FRAMEWORKROLE');
if(empty($framworkRole)){
$framworkRole='registered';
}
$rolid=(!empty($this->rolid)?$this->rolid : WUser::get('rolid'));
if(!isset($this->_roleC))$this->_roleC=WRole::get();
$isRegister=$this->_roleC->compareRole($rolid, $framworkRole );
if($isRegister){
WGlobals::set('syncUserFlag', true, 'global');
$extraPrams=new stdClass;
if(isset($this->lgid))$extraPrams->lgid=$this->lgid;
if(isset($this->blocked))$extraPrams->blocked=$this->blocked;
if(isset($this->rolid))$extraPrams->rolid=$this->rolid;
if(isset($this->activation))$extraPrams->activation=$this->activation;
if(isset($this->registerdate))$extraPrams->registerdate=$this->registerdate;
if(!empty($id))$extraPrams->id=$id;
$usersAddon=WAddon::get('users.'.JOOBI_FRAMEWORK );
$id=$usersAddon->ghostAccount($this->email, $this->_password, $this->name, $this->username, false, false, false, $extraPrams );
if(!empty($id)){
$usersM=WModel::get('users');
$usersM->whereE('uid',$this->uid );
$usersM->setVal('id',$id );
$usersM->update();
}
}
}
private function _uniqueUsername($username){
$uid=WUser::get('uid',$username );
return $uid;
}}