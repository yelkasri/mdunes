<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Translation_languages_picklist extends WPicklist {
function create(){
$model=WModel::get('library.languages');
$langClient=WApplication::mainLanguage('lgid', false, array(), 'admin');
$model->whereE('publish', 1);
$model->setLimit( 500 );
$results=$model->load('ol', array (
'lgid',
'name' 
));
foreach($results as $result){
if($langClient==$result->lgid)$this->defaultValue=$result->lgid;
$this->addElement($result->lgid, $result->name );
}
$sid=WGlobals::get('sid');
$map=WGlobals::get('map');
$model=WModel::get($sid);
$pks=$model->_primaryKeys;
unset($pks[array_search('lgid',$pks)]);
$pk=$pks[0];
$eid=WGlobals::get($pk);
$gettransurl=WPage::linkPopUp('controller=translation&task=gettrans');
$scipriot="\n".'jCore.getTrans=function(sele){' ."\n".
'var vars=new Object();' .
'vars.map='.$map.';' .
'vars.sid='.$sid.';' .
'vars.eid='.$eid.';' .
'vars.lgid=sele;' .
'var ajax=new Ajax(\''.$gettransurl.'\',{postBody:vars,onComplete: function(ajax){alert(jCore.messageMatch(ajax,\'RESPONSE\',\'any\'));}}).request();' .
'};' .
'jCore.messageMatch=function(mess,tag,type){
switch(type)
{
case \'any\':
type="(?:(?!\\\]).)*";
break;
case \'number\':
type="[0-9]+";
break;
}
mess=mess.replace(/([^>])\\n/g, \'$1<br />\');
regex=new RegExp(tag+"\\[("+type+")\\]");//,"g");
Args=mess.match(regex);
if(Args)
{
return Args[1];
}
return \'\';
}' ."\n";
WPage::addJSScript($scipriot ,'getTrans');
$this->onChange="jCore.getTrans(this.value);";
}
}