<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or defined('_JEXEC') or die('J....');
$cms_config_file=JOOBI_DS_ROOT . JOOBI_FRAMEWORK_CONFIG_FILE;
if(!file_exists($cms_config_file)){
echo 'Could not find the CMS configuration file';
exit;
}
$Itemid=null;
require_once($cms_config_file);
function jimport($path){
return;
}
class JLoader {
function import()
{
return '';
}
}
class JURI {
function base()
{
return '';
}
function root()
{
return '';
}
}
class JUtility {
function getToken(){
return '';
}
}
class JFactory {
function getApplication(){
return new WJoomla30_mainframe();
}
function getDocument(){
return new JDocument();
}
function getLanguage(){
return new JLanguage();
}
function getSession(){
return new JSession();
}
function getUser(){
return new JUser();
}
}
class JDocument {
function setTitle($title){
return true;
}
function addScript($header,$type){
return true;
}
function addStyleSheet($header,$type,$media,$attributes){
return true;
}
function addStyleDeclaration($header,$type){
return true;
}
function addJS($header,$type){
return true;
}
function addScriptDeclaration($header,$type){
return true;
}
function getCharset(){
return 'UTF-8';
}
}
class JSession {
function getId(){
return '';
}
}
class JVersion {
var $RELEASE='1.6';
var $DEV_LEVEL='0';
function getLongVersion(){
return $this->RELEASE.'.'.$this->DEV_LEVEL;
}
function getShortVersion(){
return $this->RELEASE;
}
}
$version=new JVersion();
define('JVERSION',$version->DEV_LEVEL);
define('JPATH_BASE',JOOBI_DS_ROOT);
class JLanguage {
function isRTL(){
return false;
}
function getTag(){
return 'en';
}
function getKnownLanguages(){
return array('en'=>'english');
}
}
class JPluginHelper {
function importPlugin($group,$action,$arguments){
return false;
}
}
class WJoomla30_mainframe {
var $_session=null;
function isAdmin(){
return false;
}
function WJoomla30_mainframe(){
$this->_session=new stdClass;
$this->_session->userid=0;
}
function getCfg($var){
static $array=array();
if(!class_exists('JConfig'))
{
return false;
}
$array=get_class_vars('JConfig');
return $array[$var];
}
function getTemplate(){
return '';
}
function getDocument(){
return new JDocument();
}
function login($credentials,$options){
return false;
}
function logout(){
return false;
}
}
class JUser{
function getParam($param){
return false;
}
}
class JUserHelper{
function genRandomPassword($length){
return '';
}
function getCryptedPassword($password,$salt){
return '';
}
}
class JDispatcher{
function trigger($action,$args){
return false;
}
function attach($obj){
return false;
}
function getInstance(){
return new JDispatcher();
}
}