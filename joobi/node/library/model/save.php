<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WModel_Save {
var $_existingExternalFile=null;
public function save($validate=true,&$localObj){
if(!$validate)$localObj->_validate=false;
$pKey=$localObj->getPK();
$message=WMessage::get();
if(!isset($localObj->_new)){
if($localObj->multiplePK()){
$expolodePK=$localObj->getPKs();
if(!empty($expolodePK)){
$localObj->_new=false;
foreach($expolodePK as $onePK){
if(empty($localObj->$onePK ))$localObj->_new=true;
}}else{
$localObj->_new=true;
}}else{
$localObj->_new=(!empty($localObj->$pKey ))?false : true ;
}}
$status=true;
if(isset($localObj->_parentKey) && isset($localObj->_parentId)){
$parentKey=$localObj->_parentKey;
$localObj->$parentKey=$localObj->_parentId;
}if($localObj->_keepAttributesOnSave && !$localObj->_new){
$sql=WModel::get($localObj->sid,'object');
$data=$sql->load($localObj->$pKey, $localObj->getPublicProperties(false, false, true));
$localObj->addProperties($data, '_');
}
$modelNamkey=$localObj->getModelNamekey();
$modelNamkeyType=$modelNamkey.'.type';
if( WModel::modelExist($modelNamkeyType)){
$modelTypeM=WModel::get($modelNamkeyType );
$modelTypeM->addPredefined($localObj );
}
if($localObj->_validate){
if($localObj->_new){
$fctValidateObj='addValidate';
$fctExtraObj='addExtra';
}else{
$fctValidateObj='editValidate';
$fctExtraObj='editExtra';
}
if($localObj->validate()){
if(!$localObj->$fctValidateObj()){
return false;
}}else{
return false;
}}
if(isset($localObj->_fileInfo )){
$tempM=WModel::get('files');
foreach($localObj->_fileInfo as $filidK=> $filidV){
if((!empty($localObj->$filidK ) && !is_numeric($localObj->$filidK)) || !empty($localObj->x['mdpthtp'][$filidK]['name'])){
$tempM->_fileInfo=$localObj->_fileInfo;
if(!empty($localObj->x['mdpthtp'][$filidK]['name']))$tempM->_mdpthtp[$filidK]=$localObj->x['mdpthtp'][$filidK];
$myfileinfo=new stdClass;
$filidValue=(isset($localObj->$filidK)?$localObj->$filidK : '');
$ID2Return=$this->_createWFile($tempM, $filidValue, '', false, $filidK );
$myfileinfo->map=$ID2Return;
if(!empty($tempM->wfiles[$ID2Return]) && is_array($tempM->wfiles[$ID2Return]))$getKeyOfArray=key($tempM->wfiles[$ID2Return]);
else $getKeyOfArray=0;
if(empty($tempM->wfiles[$ID2Return][$getKeyOfArray])){
$IDtoRemove=!empty($localObj->x['mdpthtp'][$filidK]['filid_remove'])?$localObj->x['mdpthtp'][$filidK]['filid_remove'] : 0;
$removeType=!empty($localObj->x['mdpthtp'][$filidK]['type'])?$localObj->x['mdpthtp'][$filidK]['type'] : '';
if(empty($removeType) || (!empty($IDtoRemove) && $removeType !='file')){
$localObj->$filidK=0;
$this->_deleteOldFile($localObj, $filidK, false, $IDtoRemove );
}continue;
}
foreach($tempM->wfiles[$ID2Return][$getKeyOfArray] as $tempMK=> $tempMV){
$myfileinfo->$tempMK=$tempMV;
}
$localObj->$filidK=$this->_saveFiles($myfileinfo, $tempM );
if(!$localObj->_new && !empty($localObj->$filidK) && empty($this->_existingExternalFile) && !$localObj->_infos->mpk){
$this->_deleteOldFile($localObj, $filidK );
}
}elseif(isset($localObj->x['mdpthtp'][$filidK]['type']) && empty($localObj->x['mdpthtp'][$filidK]['type']) && !empty($localObj->x['mdpthtp'][$filidK]['filid_remove'])){
$this->_deleteOldFile($localObj, $filidK, false, $localObj->x['mdpthtp'][$filidK]['filid_remove'] );
$localObj->$filidK=0;
}
}
}
if(isset($localObj->wfiles )){
$filesIDs=array();
if(!empty($localObj->wfiles)){
foreach($localObj->wfiles as $arrKey=> $arrVal){
foreach($arrVal as $arrKey2=> $arrVal2){
if(!empty($arrVal2->name)){
$rlt=$this->_saveFiles($arrVal2, $localObj );
if(false===$rlt){
unset($arrVal[$arrKey2] );
}else{
$filesIDs[$arrKey][]=$rlt;
}}
}
if(!$localObj->_new && !empty($filesIDs[$arrKey]) && ! $localObj->_infos->mpk){
$this->_deleteOldFile($localObj, $arrKey );
}}
}else{ echo 'There is something seriously wrong in there';
return false;
}
unset($localObj->wfiles);
if($localObj->getType()==30){
$childName='C'.$localObj->getModelID();
if(empty($filesIDs)) return true;
foreach($filesIDs as $filesIDsKey=> $filesIDsVal){
if(empty($filesIDsVal)) continue;
$strangeArray=new stdClass;
$strangeArray->$filesIDsKey=$filesIDsVal;
$childModel=&$this->_getChildInstance($childName, $strangeArray, $localObj );
if(!$childModel->isReady()){
$mess=WMessage::get();
$mess->codeE('Multiple picture save error '.(isset($localObj->name)?'of the object '.(isset($localObj->prefix)?$localObj->prefix.'_' : ''). $localObj->name :''). ' created an error at saving. Name: '.$childName );
return false;
}
$mychildPK=array_diff($childModel->getPKs(), array($filesIDsKey));
sort($mychildPK);
$childProperty=array();
$localObj->_pubProperties=$this->_getPropertiesAndChild($childProperty, $localObj );
if(!empty($localObj->_pubProperties)){
foreach($localObj->_pubProperties as $prop){
$childModel->$prop=$localObj->$prop;
}}else{
return false;
}$childModel->_childRemoveNotPresent=false;
$childModel->_childOnlyAddNew=false;
$localObj->_new=true;
$prpS=(!empty($mychildPK[0])?$mychildPK[0] : '');
if(isset($localObj->$prpS)){
$this->_saveChild($childModel, $childName, false, $localObj->$prpS, $strangeArray, array($filesIDsKey), $localObj );
}
}
if(!empty($filesIDs)){
$i=0;
foreach($filesIDs as $filesIDsKey=> $filesIDsVal){
$huii=$filesIDsKey[$i];
$localObj->$huii=$filesIDsVal;
$i++;
}}
return true;
}else{
if(!empty($filesIDs)){
foreach($filesIDs as $filesIDsKey=> $filesIDsVal){
if(!empty($filesIDsKey)){
if(is_array($filesIDsVal)){
$myFileKeyNow=key($filesIDsVal);
$localObj->$filesIDsKey=$filesIDsVal[$myFileKeyNow];
}else{
$localObj->$filesIDsKey=$filesIDsVal;
}}
}
}}
}
if(!empty($localObj->p )){
if(is_array($localObj->p )){
if(isset($localObj->params)){
$txt=explode( "\n" , $localObj->params );
}else{
$txt=array();
}
foreach($localObj->p as $k=>$v){
$res='';
if(is_array($v)){
$res="$k=";
end($v);
$vend=key($v);
foreach($v as $vkey=> $vx){
if($vkey !=$vend)$res .="$vx,";
else $res.="$vx";
}}else{
$v=str_replace( "\r\n", "\r", $v );
$v=str_replace( "\n", "\r", $v );
if(!empty($v) && is_string($k) && ( is_string($v) || is_numeric($v)))$res="$k=$v";
}
if(!empty($res))$txt[]=$res;
}$localObj->params=implode( "\n" , $txt );
$localObj->_p=$localObj->p;unset($localObj->p );
}}
if(!empty($localObj->j )){
if(is_array($localObj->j )){
if(isset($localObj->predefined)){
$jsonObj=json_decode($localObj->predefined );
}else{
$jsonObj=new stdClass;
}
foreach($localObj->j as $k=>$v){
$jsonObj->$k=$v;
}
$localObj->predefined=json_encode($jsonObj );
$localObj->_j=$localObj->j;unset($localObj->j );
}}
if(!empty($localObj->f )){
foreach($localObj->f as $fileType=> $content){
$propertiesF=get_object_vars($localObj );
$fileObject=new stdClass; foreach($propertiesF as $keyF=> $valF){
if($keyF[0]!='_')$fileObject->$keyF=$valF;
}$trk=WGlobals::get( JOOBI_VAR_DATA );
if(!empty($trk['x']))$fileObject->x=$trk['x'];
WLoadFile('design.system.class', JOOBI_DS_NODE );
$extFileA=WAddon::get('design.'.$fileType );
if(!empty($extFileA))$extFileA->save($content, $fileObject );
}
unset($localObj->f );}
if(!empty($localObj->m ) && is_array($localObj->m)){
$txt=array();
foreach($localObj->m as $k=>$v){
$localObj->$k=implode('|_|' , $v );
}unset($localObj->m);
}
if(!empty($localObj->_mlt_s_extra)){
foreach($localObj->_mlt_s_extra as $mSID=> $oneMulSelectMap){
if(isset($oneMulSelectMap)){
foreach($oneMulSelectMap as $oneMultVal){
if(isset($localObj->$oneMultVal) && is_array($localObj->$oneMultVal )){
if(!empty($localObj->$oneMultVal))$localObj->$oneMultVal='|_|'.implode('|_|',$localObj->$oneMultVal ). '|_|';
}}}}}
if(!empty($localObj->x['zwother'])){
foreach($localObj->x['zwother'] as $otherProp){
$othrsDefaultVal=(!empty($localObj->x['zwother_dft_'.$otherProp ])?$localObj->x['zwother_dft_'.$otherProp ] : '');
if(empty($othrsDefaultVal)){
continue;}
$othrsNewVal=(!empty($localObj->x['zwother_'.$otherProp ])?$localObj->x['zwother_'.$otherProp ] : '');
if(isset($localObj->$otherProp) && !empty($othrsNewVal) && $othrsDefaultVal==$localObj->$otherProp){
$localObj->$otherProp=$othrsNewVal;
if(isset($localObj->x['zwother_crt'][$otherProp])){
if($localObj->x['zwother_crt'][$otherProp] !=$othrsNewVal){
$this->_deletePicklistChildValue($localObj, $otherProp );
}
}}}
}
if(!empty($localObj->x['zwother_crt'])){
foreach($localObj->x['zwother_crt'] as $oneKMzap=> $oneVMzap){
if(isset($localObj->$oneKMzap) && $localObj->$oneKMzap !=$oneVMzap){
$this->_deletePicklistChildValue($localObj, $oneKMzap );
}}}
if(isset($localObj->x)){
$localObj->_x=$localObj->x;unset($localObj->x);
}
$childProperty=array();
$localObj->_pubProperties=$this->_getPropertiesAndChild($childProperty, $localObj );
if(!empty($childProperty))$localObj->returnId(true);
if($localObj->_new && $localObj->getParam('verifyUnique', false)){
$namekeyEID=array();
if(!$this->_checkUK($localObj->_new, true, $localObj, $namekeyEID )){
return true;
}}
$user=WUser::get();
if($localObj->getParam('autofld', false)){
static $allAutoFieldsA=array();
$tableID=$localObj->getTableId();
if(!isset($allAutoFieldsA[$tableID] )){
$modelPK=WModel::get('library.columns');
$modelPK->whereE('dbtid',$tableID );
$modelPK->whereE('checkval', 1 );
$modelPK->setLimit( 300 );$allAutoFields=$modelPK->load('ol',array('name','columntype'));
$allAutoFieldsA[$tableID]=$allAutoFields;
}else{
$allAutoFields=$allAutoFieldsA[$tableID];
}
$autoFields=new stdClass;
if(!empty($allAutoFields)){
foreach($allAutoFields as $oneField){
$nameColumn=$oneField->name;
if(!isset($autoFields->$nameColumn))$autoFields->$nameColumn=new stdClass;
$autoFields->$nameColumn->columnType=$oneField->columntype;
if(isset($localObj->$nameColumn)){
if(empty($oneField->columntype)) continue;
if($oneField->columntype==1 && !is_numeric($localObj->$nameColumn)){
$newVal=WApplication::stringToTime($localObj->$nameColumn );
if(!empty($newVal) && strlen($localObj->$nameColumn) > 11)$newVal=$newVal - WUser::timezone();
$localObj->$nameColumn=$newVal;
}}}
}
if($localObj->_new){
if(isset($autoFields->author) && empty($localObj->author ))$localObj->author=$user->uid;
if(isset($autoFields->uid) && $localObj->getPK() !='uid' && empty($localObj->uid ))$localObj->uid=$user->uid;
if(isset($autoFields->namekey) && empty($localObj->namekey))$localObj->namekey=$localObj->genNamekey();
if(isset($autoFields->created) && empty($localObj->created))$localObj->created=time();
} else unset($localObj->created);
if(isset($autoFields->modifiedby) && ( !isset($localObj->modifiedby ) || $localObj->modifiedby<1 ))$localObj->modifiedby=$user->uid;
if(isset($autoFields->modified) && ( !isset($localObj->modified ) || $localObj->modified<1 ))$localObj->modified=time();
if(!empty($localObj->_infos->fields)){
$this->_processCustomFields($localObj );
}
}
if(!empty($localObj->namekey)){
$localObj->namekey=WGlobals::filter($localObj->namekey, 'namekey');
}if(isset($localObj->namekey)){
$localObj->namekey=trim($localObj->namekey );
if(empty($localObj->namekey))$localObj->genNamekey();
}
if(isset($localObj->alias) && empty($localObj->alias)){
$SIDmap=WModel::get($localObj->getModelNamekey(). 'trans','sid', null, false);
if(!empty($SIDmap)){
$sidTransC='C'.$SIDmap;
if(isset($localObj->$sidTransC->name ))$localObj->alias=$localObj->$sidTransC->name;
}else{
if(isset($localObj->name ))$localObj->alias=$localObj->name;
}}
$removeBRinEditorA=array('description','wdescription','introduction');
foreach($removeBRinEditorA as $oneTextArea){
if(!empty($localObj->$oneTextArea)){
$localObj->$oneTextArea=trim($localObj->$oneTextArea );if( substr($localObj->$oneTextArea, strlen($localObj->$oneTextArea)-4 )=='<br>')$localObj->$oneTextArea=substr($localObj->$oneTextArea, 0, strlen($localObj->$oneTextArea)-4 );if( substr($localObj->$oneTextArea, strlen($localObj->$oneTextArea)-6 )=='<br />')$localObj->$oneTextArea=substr($localObj->$oneTextArea, 0, strlen($localObj->$oneTextArea)-6 );}}
if($localObj->getType()==20 && (empty($localObj->lgid) || $localObj->lgid<1 )){
$useMultipleLang=defined('PLIBRARY_NODE_MULTILANG')?PLIBRARY_NODE_MULTILANG : 0;
if($useMultipleLang){
$localObj->lgid=$user->lgid;
}else{
$useMultipleLangENG=defined('PLIBRARY_NODE_MULTILANGENG')?PLIBRARY_NODE_MULTILANGENG : 1;
$localObj->lgid=($useMultipleLangENG?1: WApplication::userLanguage());
}
}
$skipQuery=false;
if(!$localObj->_new && !$localObj->multiplePK()){
$propertiesToUpdate=array_diff($localObj->_pubProperties, $localObj->getPKs());
if(empty($propertiesToUpdate))$skipQuery=true;
}
if($localObj->getParam('verifyFields', false)){
$constraints=$localObj->getFields();
$messageConstraints='';
if(!empty($constraints)){
if(!$this->_verifyData($constraints, $messageConstraints, $localObj->_new, $localObj )){
$mess=WMessage::get();
return $mess->historyE($messageConstraints );
}}}
if($localObj->getAudit()){
static $count=0;
$count++;if(!isset($securityAuditA))$securityAuditA=WClass::get('security.audit', null, 'class', false);
if(!empty($securityAuditA)){
$securityAuditA->beforeSave($localObj->getModelID(), $count, $localObj );
}}
if(isset($localObj->wfiles)) unset($localObj->wfiles );
if($skipQuery || $status=$localObj->store()){
if($localObj->getAudit() && $status){
if(!empty($securityAuditA))$securityAuditA->afterSave($count );
}
$localObj->_saveState=true;
if(isset($localObj->ordering))$localObj->setModelSaveOrder();
if(!empty($childProperty)){
foreach($childProperty as $childKey=> $childVal){
if( ctype_upper($childVal[0]) && ctype_alpha($childVal)){
$childNamekey=strtolower($localObj->_myFct.'.'.$childVal );
$childSID=WModel::get($childNamekey, 'sid');
if($childSID){
$objTmp=$localObj->$childVal;
unset($localObj->$childVal);
$childVal='C'.$childSID;
$localObj->$childVal=$objTmp;
}
}
$model=null;
$childValKey=0;
if(is_array($localObj->$childVal)  && !empty($localObj->$childVal)){
if(!empty($localObj->$childVal)){
$strangeArray=$localObj->$childVal;
if( is_object($strangeArray[0])){
foreach($localObj->$childVal as $childValKey=> $childValVal){
$model=$this->_getChildInstance($childVal, $strangeArray[0], $localObj  );
if(!$model->isReady()){
$mess=WMessage::get();
$mess->codeE('One of the sub-model '.(isset($localObj->name)?'of the object '.(isset($localObj->prefix)?$localObj->prefix.'_' : ''). $localObj->name :''). ' created an error at saving. Name: '.$childVal. ' , value :'. $childValKey );
return false;
}
if($model->_samePrimaryKey){if(!empty($localObj->$pKey))$model->_forceInsert=true;
}
if(isset($localObj->$pKey))$model->$pKey=$localObj->$pKey;
$model->_new=$localObj->_new;
$arrayInValue=$model->addProperties($childValVal, '', true);
$oneMoreChild=false;
foreach($arrayInValue as $gggggKey=> $ckeckarrayInValue){
if(ctype_upper($ckeckarrayInValue[0])){
$oneMoreChild=true;
$model->$ckeckarrayInValue=$childValVal->$ckeckarrayInValue;
}}
if($oneMoreChild){
$model->save();
}else{
$this->_saveChild($model, $childVal, $childValKey, $localObj->$pKey, $childValVal, $arrayInValue, $localObj );
if($model->multiplePK()){
$mpKey=$model->getPK();
$myPK=$model->getConstraints('pk');
if($model->getType()==20){
$myPK=array_diff($myPK, array('lgid'));
$mpKey=$myPK[0];
}
}else{
$mpKey=$model->getPK();
}
unset($model->$mpKey );
}}}}
}elseif( is_object($localObj->$childVal) && !empty($localObj->$childVal)){
$model=$this->_getChildInstance($childVal, $localObj->$childVal, $localObj  );
if(!$model->isReady()){
$mess=WMessage::get();
$mess->codeE('One of the sub-model 2 '.(isset($localObj->name)?'of the object '.(isset($localObj->prefix)?$localObj->prefix.'_' : ''). $localObj->name :''). ' created an error at saving. Name: '.$childVal. ' , value :'. $childValKey );
return false;
}
if(isset($localObj->$pKey))$model->$pKey=$localObj->$pKey;
$model->_new=$localObj->_new;
if(isset($model->_samePrimaryKey) && $model->_samePrimaryKey){if(!empty($localObj->$pKey))$model->_forceInsert=true;
}
$arrayInValue=$model->addProperties($localObj->$childVal, '', true);
$arrayInValue=array_diff($arrayInValue, array('x','c','p','u','wfiles','m'));
$allCrossValues=null;
$keytoUse=array();
if(!empty($arrayInValue)){
if(!empty($arrayInValue)){
$keytoUse=$arrayInValue[0];
$allCrossValues=$localObj->$childVal->$keytoUse;
}
}elseif($model->multiplePK() && $model->getType() !=20){
$subKeys=array_diff($model->getPKs(), array($localObj->getPK()) );
$arrayInValue=$subKeys;
sort($arrayInValue);
if( count($arrayInValue) < 2){
$FileinfoA=(!empty($model->_fileInfo))?array_keys($model->_fileInfo) : array();
$prp=$arrayInValue[0];
if(!in_array($arrayInValue[0], $FileinfoA ) && empty($localObj->$childVal->$prp)){
continue;
}
if(isset($model->_fileInfo )){
$tempM=WModel::get('files');
foreach($model->_fileInfo as $filidK=> $filidV){
if(!empty($localObj->$childVal->x['mdpthtp'][$filidK]['name'])){
$tempM->_fileInfo=$model->_fileInfo;
if(!empty($localObj->$childVal->x['mdpthtp'][$filidK]['name']))$tempM->_mdpthtp[$filidK]=$localObj->$childVal->x['mdpthtp'][$filidK];
$myfileinfo=new stdClass;
$filidValue=(isset($model->$filidK))?$model->$filidK : '';
$ID2Return=$this->_createWFile($tempM, $filidValue, '', false, $filidK );
$myfileinfo->map=$ID2Return;
if(!empty($tempM->wfiles[$ID2Return]) && is_array($tempM->wfiles[$ID2Return]))$getKeyOfArray=key($tempM->wfiles[$ID2Return]);
else $getKeyOfArray=0;
if(empty($tempM->wfiles[$ID2Return][$getKeyOfArray])){
$IDtoRemove=!empty($localObj->$childVal->x['mdpthtp'][$filidK]['filid_remove'])?$localObj->$childVal->x['mdpthtp'][$filidK]['filid_remove'] : 0;
$removeType=!empty($localObj->$childVal->x['mdpthtp'][$filidK]['type'])?$localObj->$childVal->x['mdpthtp'][$filidK]['type'] : '';
if(empty($removeType) || (!empty($IDtoRemove) && $removeType !='file')){
$localObj->$filidK=0;
$this->_deleteOldFile($model, $filidK, false, $IDtoRemove );
}continue;
}
foreach($tempM->wfiles[$ID2Return][$getKeyOfArray] as $tempMK=> $tempMV){
$myfileinfo->$tempMK=$tempMV;
}
$model->$filidK=$this->_saveFiles($myfileinfo, $tempM );
if(!empty($model->$filidK )){
$localObj->$childVal->$filidK=$model->$filidK;
}
if(!$model->_new && !empty($localObj->$filidK) && empty($this->_existingExternalFile) && !$model->_infos->mpk){
$this->_deleteOldFile($localObj, $filidK );
}
}elseif(isset($localObj->$childVal->x['mdpthtp'][$filidK]['type']) && empty($localObj->$childVal->x['mdpthtp'][$filidK]['type']) && !empty($localObj->$childVal->x['mdpthtp'][$filidK]['filid_remove'])){
$this->_deleteOldFile($localObj, $filidK, false, $localObj->$childVal->x['mdpthtp'][$filidK]['filid_remove'] );
$localObj->$filidK=0;
}
}
}
}}
if(!empty($allCrossValues)){
if( key($allCrossValues) !=0){
$model->whereE($pKey, $model->$pKey );
$model->select($keytoUse );
$allResults=$model->load('lra');
$myValueToInsert=array();
$myValueToDelete=array();
$allCrossValuesReversed=array();
foreach($allCrossValues as $keyID=> $truefalse){
if($truefalse)$myValueToInsert[]=$keyID;
else $myValueToDelete[]=$keyID;
}
$myValueToInsert=array_diff($myValueToInsert, $allResults );
$myValueToDelete=array_intersect($myValueToDelete, $allResults );
}else{
$allValuesToProcess=$allCrossValues;
$myValueToInsert=$allCrossValues;
$model->whereE($pKey, $model->$pKey );
$model->select($keytoUse );
$model->setLimit( 5000 );
$allResults=$model->load('lra');
$myValueToDelete=array();
$myValueToDelete=array_diff($allResults, $allValuesToProcess );
$myValueToInsert=array_diff($myValueToInsert, $allResults);
$myValueToInsert=array_diff($myValueToInsert, $myValueToDelete);
}
if(!empty($myValueToDelete )){
$model->whereE($pKey, $localObj->$pKey );
$model->whereIn($keytoUse, $myValueToDelete );
unset($model->$keytoUse);
$datas=array();
foreach($myValueToDelete as $key=> $goodID){
if(!isset($datas[$key]))$datas[$key]=new stdClass;
$datas[$key]->$pKey=$localObj->$pKey;
$datas[$key]->$keytoUse=$goodID;
}$model->_manyValues=$datas;
$model->delete();
}
if(!empty($myValueToInsert )){
$datas=array();
foreach($myValueToInsert as $key=> $goodID){
$datas[$key]=new stdClass;
$datas[$key]->$pKey=$localObj->$pKey;
$datas[$key]->$keytoUse=$goodID;
}
if($model->_validate){
$model->_manyValues=$datas;
$fctValidateObjBis='addValidate';
$fctExtraObjBis='addExtra';
if($model->validate()){
if(!$model->$fctValidateObjBis()){
$message->codeE('Model : ' .$model->getModelNamekey(). '.The validate save return FALSE for the following funtion: '. $fctValidateObjBis .'()',null, false);
return false;
}
}else{
$message->codeE('Model : ' .$model->getModelNamekey(). '.The validate save return FALSE for the following funtion: validate()',null, false);
return false;
}}
$myvalue=$model->_manyValues[key($model->_manyValues)];
$selects=array();
foreach($myvalue as $key=> $value){
$selects[]=$key;
}
$model->setIgnore();
$model->insertMany($selects, $model->_manyValues );
$model->setIgnore(false);
if($model->_validate){
if($model->$fctExtraObjBis() ){
$extraStatus=$model->extra();
}else{
$message->codeW('The extra save return FALSE for the following model '. $model->_infos->namekey.' and funtion:'.$fctExtraObjBis .'() a');
return false;
}if(!$extraStatus){
$message->codeW('The extra save return FALSE for the following model '. $model->_infos->namekey.' and funtion extra() b');
return false;
}}
}
}elseif(is_array($allCrossValues) && $model->multiplePK() && $model->getType() !=20){
$model->whereE($pKey, $localObj->$pKey );
$model->delete();
}else{
if(isset($allCrossValues) && is_array($allCrossValues))$arrayInValue=$allCrossValues;
if(isset($localObj->$childVal->wfiles)){
$model->wfiles=$localObj->$childVal->wfiles;
}
if(isset($localObj->$childVal->m)){
$model->m=$localObj->$childVal->m;
}
$this->_saveChild($model, $childVal, false, $localObj->$pKey, $localObj->$childVal, $arrayInValue, $localObj );
$childKey=$model->getPK();
if(isset($model->$childKey) && !$model->multiplePK()){
$localObj->$childVal->$childKey=$model->$childKey;
}
}
}
}
}
if($localObj->_validate){
if($localObj->$fctExtraObj() ){
$extraStatus=$localObj->extra();
}else{
$message->codeW('The extra save return FALSE for the following model '. $localObj->_infos->namekey.' and funtion:'.$fctExtraObj .'()c');
return false;
}
if(!$extraStatus){
$message->codeW('The extra save return FALSE for the following model '. $localObj->_infos->namekey.' and funtion extra().d');
return false;
}}
if(!empty($localObj->_infos->outgoing)){
$this->_sendOutGoingData($localObj );
}
}else{
$localObj->_saveState=true;
$className=get_class($localObj );
$serialzieObj=serialize($localObj );
$message->codeE('The main save returned FALSE for :'.$className , null, false);
return false;
}
if($status){
if(!empty($localObj->_infos->cachedata) && !$localObj->_new){
$cache=WCache::get();
$pkey=$localObj->getPK();
$nameTrans=WModel::modelExist($localObj->_infos->namekey.'trans');
if($nameTrans){
$lgid=WUser::get('lgid');
$key='lg-'.$localObj->$pkey.'-'.$lgid;
}else{
$key=$localObj->$pkey;
}
$rememberID='md-'.$localObj->_infos->dbtid.'-id-'.$key;
$cache->resetCache('Model_'.$localObj->_infos->tablename, $rememberID );
if(!empty($localObj->namekey)){
if($nameTrans){
$lgid=WUser::get('lgid');
$key='lg-'.$localObj->namekey.'-'.$lgid;
}else{
$key=$localObj->namekey;
}$rememberID='md-'.$localObj->_infos->dbtid.'-id-'.$key;
$cache->resetCache('Model_'.$localObj->_infos->tablename, $rememberID );
}
}
}
if($localObj->getParam('ordrg',false)){
$localObj->setModelSaveOrder();
}
return $status;
}
public function saveFileLib(&$localObj,$source='',$destination='',$validate=true,$property=null,$optimize=null){
$ID2Return=$this->_createWFile($localObj, $source, $destination, $validate, $property );
$localObj->returnId();
$localObj->setIgnore();
if( is_bool($optimize))$localObj->optimizeImage($optimize );
$status=$localObj->save($validate );
$return=(($status)?($localObj->getType()==30?true : (isset($localObj->$ID2Return)?$localObj->$ID2Return : false)) : false);
return $return;
}
public function copy($eid=null,&$localObj){
$localObj->setParam('uniqueCorrect', true);
$pkey=$localObj->getPK();
if(is_array($eid)){
$localObj->whereE($pkey, $eid[0] );
}else{
$localObj->whereE($pkey, $eid );
}$data=$localObj->load('o');
$localObj->resultToObj(true); 
if(!$localObj->copyValidate()){
return false;
}
unset($localObj->$pkey);  
$localObj->noVerify();
$namekeyEID=array();
if(!$this->_checkUK(true, true, $localObj, $namekeyEID, true)){
return false;
}
return ($localObj->save() && $localObj->copyExtra());
}
public function copyAll($eid=0,&$localObj){
if( @is_array($eid )){
foreach($eid as $id){
$retunredID=$localObj->copyAll($id );
if(!$retunredID ) return false;
}return $retunredID;
}elseif( is_numeric($eid) && $eid !=0){
$pkey=$localObj->getPK();
$localObj->whereE($pkey, $eid );
$data=$localObj->load('o');
$parent=false;$namekeyEID=array();
$obj=$this->_getChildsCopy($localObj->getTableId(), $eid, $parent, $namekeyEID, $pkey );
if(!empty($obj ))$localObj->addProperties($obj );
foreach($obj as $midCHild=> $midVal){
if(empty($localObj->$midCHild)) continue;
if(is_array($localObj->$midCHild)){
$newChildArray=array();
foreach($localObj->$midCHild as $childObjectNow){
if(!empty($childObjectNow->$pkey)) unset($childObjectNow->$pkey);
$newChildArray[]=$childObjectNow;
}$localObj->$midCHild=$newChildArray;
}
}
if(!$localObj->copyValidate()) return false;
$pkey=$localObj->getPK();
unset($localObj->$pkey);  
$localObj->noVerify();
$localObj->_propagateToChild=true;
$localObj->setParam('verifyUnique', true);
$localObj->setParam('uniqueCorrect', true);
if(!$this->_checkUK(true, true, $localObj, $namekeyEID, true)){
return false;
}
if(!$this->_checkUKForChildModel(true, true, $localObj, $namekeyEID )){
return false;
}
$localObj->returnId();
if(isset($localObj->core )){
if( substr($localObj->_infos->namekey, 0, 8 ) !='library.'
){
$localObj->core=0;
}}
if(!$localObj->save()) return false;
if($parent){
$this->_parentSync($namekeyEID, $localObj );
}
$localObj->copyExtra();
$pKey=$localObj->getPK();
return $localObj->$pKey;
}
return true;
}
public function genNamekey($suffix='',$maxsize=100,$prefix='',$style='alphanumeric'){static $numberA=array();
static $uid=null;
if(!isset($uid))$uid=WUser::get('uid');
if(!isset($numberA[$uid]))$numberA[$uid]=0;
switch($style){
case 'numeric':
$string=time() - 1232420000;
break;
case 'alpha':
$time=time() - 1232420000; $addedNumber=(empty($numberA[$uid]))?'' : 'a'.base_convert($numberA[$uid], 10, 36 );
$string=strtolower($prefix . base_convert(($time ),10,25).'z'. base_convert(($uid ),10,26). $addedNumber . $suffix );
$string=WGlobals::filter($string, 'word');
break;
case 'alphanumeric':
default:
$time=time() - 1232420000; $addedNumber=(empty($numberA[$uid]))?'' : 'a'.base_convert($numberA[$uid], 10, 36 );
$string=strtolower($prefix . base_convert(($time ),10,35).'z'. base_convert(($uid ),10,36). $addedNumber . $suffix );
$string=WGlobals::filter($string, 'alnum');
break;
}
$numberA[$uid]++;
if( strlen($string) > $maxsize){
$string=substr($string, 0, $maxsize );
}
return $string;
}
private function _deletePicklistChildValue($localObj,$otherProp){
if(empty($localObj->x['zwother_prts'][$otherProp] )) return false;
$did=$localObj->x['zwother_prts'][$otherProp];
if(empty($localObj->x['zwother_chlds'][$did])) return false;
$childProperty=$localObj->x['zwother_chlds'][$did];
$localObj->$childProperty='';
$this->_deletePicklistChildValue($localObj, $childProperty );
}
private function _getChildsCopy($dbtid,$eid,&$parent,&$namekeyEID,$parentPK){
static $myGoodPK=null;
static $storeFK=array();
if(!isset($storeFK[$dbtid])){
$sql=WModel::get('library.foreign');
$sql->remember('foreignKey_OnUpdate_'.$dbtid, true, 'Model');
$sql->makeLJ('library.columns','feid','dbcid');
$sql->makeLJ('library.model','dbtid');
$sql->whereE('ref_dbtid',$dbtid );
$sql->whereE('onupdate', 3 );$sql->groupBy('dbtid');
$sql->whereE('publish', 1 );
$sql->whereE('publish', 1, 2 );
$sql->select('name',1, 'map');
$sql->select('sid', 2 );
$sql->select('dbtid');
$sql->setLimit( 100 );
$fks=$sql->load('ol');
$storeFK[$dbtid]=$fks;
}else{
$fks=$storeFK[$dbtid];
}
$childObj=new stdClass;
foreach($fks as $fk){
$sql=WTable::get($fk->dbtid );
if(!$sql->getParam('dbname', false)){
 $mess=WMessage::get();
 $mess->codeE('The table for the foreign key does not exists, check your table :  '. $fk->dbtid, array(), 'query');
 continue;
}
$sql->whereE($fk->map, $eid );
$sql->setLimit( 1000 );
if($sql->multiplePK()){
$myPK=$sql->getConstraints('pk');
if(empty($myPK)){
continue;
}
if($sql->getType()==20){$myPK=array_diff($myPK, array('lgid'));
$myGoodPK=$myPK[0];
$sql->whereE('lgid', 1 );
}else{
if( in_array($parentPK, $myPK )){
$myGoodPK=$parentPK;
}else{
exit;
}
}
$childKeys=$sql->load('ol');
}else{
$myPK=array();
$myPK[]=$sql->getPK();
$childKeys=$sql->load('ol');
$myGoodPK=$sql->getPK();
}
if(!empty($childKeys)){
$childName='C'.$fk->sid;
$makeInstanceChildM=WModel::get($fk->sid );
$obj=array();
$typeTable=$sql->_infos->type;
foreach($childKeys as $ckey=> $childKey){
$oneObject=$childKey;
if(!isset($oneObject->$myGoodPK)) continue;
$eidValue=$oneObject->$myGoodPK;
if(isset($oneObject->parent ) && !empty($oneObject->namekey)){
$parent=true;
$namekeyEID[$childName][$oneObject->$myGoodPK]=$oneObject->namekey;
}
if(isset($oneObject->$myGoodPK )) unset($oneObject->$myGoodPK );
if(isset($oneObject->modified )) unset($oneObject->modified );
if(isset($oneObject->created )) unset($oneObject->created );
if(isset($oneObject->uid )) unset($oneObject->uid );
$myobj=$this->_getChildsCopy($makeInstanceChildM->getTableId(), $eidValue, $parent, $namekeyEID, $parentPK );
if(!empty($myobj)){
foreach($myobj as $okey=> $oVak){
$oneObject->$okey=$oVak;
}
$obj[]=$oneObject;
}
}
if(!empty($obj)){
$childObj->$childName=$obj;
}else{
if( 30==$typeTable){
$childObj->$childName=array($oneObject );
}else{
$childObj->$childName=$oneObject;
}
}
}
}
return  $childObj;
}
private function _saveFiles($myfileinfo,&$localObj){
static $files=null;
if(empty($myfileinfo->name)) return false;
if(empty($myfileinfo->map)) return 0;
$message=WMessage::get();
$mapID=$myfileinfo->map;unset($myfileinfo->map);
$files=WModel::get('files');
if( is_bool($localObj->_optimizeImg ))$files->optimizeImage($localObj->_optimizeImg );
$files->returnId();
if(!empty($myfileinfo->externalFile)){
$files->_folder='';
$files->_path=$myfileinfo->name;
$files->_fileType='external';
$files->secure=false;
}else{
$transferVariableA=array();
$transferVariableA['folder']='_folder';
$transferVariableA['folder']='folder';
$transferVariableA['path']='_path';
$transferVariableA['secure']='secure';
$transferVariableA['format']='_format';
$transferVariableA['maxSize']='_maxSize';
$transferVariableA['storage']='_storage';
$transferVariableA['thumbnail']='thumbnail';
$transferVariableA['maxHeight']='_maxHeight';
$transferVariableA['maxWidth']='_maxWidth';
$transferVariableA['maxTHeight']='_maxTHeight';
$transferVariableA['maxTWidth']='_maxTWidth';
$transferVariableA['watermark']='_watermark';
$transferVariableA['fileType']='_fileType';
foreach($transferVariableA as $origin=> $destination){
if(isset($localObj->_fileInfo[$mapID]->$origin ))$files->$destination=$localObj->_fileInfo[$mapID]->$origin;
}
if(isset($myfileinfo->uploadFile ))$files->_uploadFile=$myfileinfo->uploadFile;
}
if(!empty($myfileinfo)){
foreach($myfileinfo as $key=>$val){
$myKey='_'.$key;
$files->$myKey=$val;
}}
if(!empty($files->_type) && $files->_type !='files'){
$filesCleanC=WClass::get('files.analyze');
$filesCleanC->cleanURL($files );
}
$this->_existingExternalFile=null;
if(!$files->save()){
$FILENAME=$myfileinfo->name;
$message->userW('1298350424IMTJ',array('$FILENAME'=>$FILENAME));
if(empty($files->filid )){
return false;
}return false;
}
$this->_existingExternalFile=$files->_currentFileID;
return $files->filid;
}
private function _createWFile(&$localObj,$source='',$destination='',$validate=true,$map=null){
if(!empty($map) && !empty($localObj->_mdpthtp[$map])){
$obj=new stdClass;
$obj->name=$localObj->_mdpthtp[$map]['name'];
$obj->type=$localObj->_mdpthtp[$map]['type'];
if(empty($obj->type)) return false;
$obj->externalFile=true;
$obj->tmp_name='';
$obj->error=0;
$obj->size=0;
$obj->uploadFile=false;
$obj->map=key($localObj->_fileInfo );
$localObj->wfiles[$obj->map][]=$obj;
$ID2Return=$obj->map;
}else{
if(!isset($localObj->_fileInfo ) || (!empty($map) && !isset($localObj->_fileInfo[$map] ))){
$message=WMessage::get();
$message->codeE('The model is not well defined and we could not find the file specification, please complete $_fileInfo property for the model: '.$localObj->getModelNamekey());
WMessage::log($map, 'file-issue-model');
WMessage::log($localObj, 'file-issue-model');
WMessage::log( debugB( 89563401), 'file-issue-model');
return false;}
$fileC=WGet::file();
if(empty($source) || ! $fileC->exist($source )){
$message=WMessage::get();
$FILEPATH=$source;
if( is_string($FILEPATH))$message->userE('1305518119BKOP',array('$FILEPATH'=>$FILEPATH));
return false;}
$ID2Return=(!empty($map))?$map : 'filid';
$obj=new stdClass;
$obj->externalFile=false;
$obj->tmp_name=$source;
$nameA=explode( DS, $source );
$obj->name=array_pop($nameA );
$obj->error=0;
$obj->size=filesize($source);
$obj->uploadFile=false;
if( count($localObj->_fileInfo ) < 2){
$obj->map=key($localObj->_fileInfo );
$localObj->wfiles[$obj->map][]=$obj;
$ID2Return=$obj->map;
}elseif(!empty($map)){
$obj->map=$ID2Return;
$localObj->wfiles[$map][]=$obj;
$ID2Return=$map;
}else{
$message=WMessage::get();
$NAME=$localObj->getModelNamekey();
$message->codeE('There are several files ID on this model: '.$NAME.', please specify which property need to be used.');
return false;
}
}
return $ID2Return;
}
private function &_getChildInstance($childName,$childObj,&$localObj){
$classType=get_class($childObj );
if( substr($classType, strlen($classType ) - 6, 6)=='_model'){
$model=&$childObj;
}else{
$paramsName='_Params_'.$childName;
$childValParams=(isset($localObj->$paramsName))?$localObj->$paramsName : null;
$childNameNumber=substr($childName, 1 );
if( is_numeric($childNameNumber)){
  $model=WModel::get($childNameNumber, 'objectfile',$childValParams );
}else{
$className=$localObj->_namekey.'.'.$childName;$model=WModel::get($className, 'objectfile',$childValParams );
}}
if(!is_object($model)){
$mess=WMessage::get();
$mess->codeE('The model was not found. Name: '.$className );
return false;
}
if($localObj->_propagateToChild){
$model->setParam('uniqueCorrect',$localObj->getParam('uniqueCorrect',false));
$model->setParam('uniqueUpdate',$localObj->getParam('uniqueUpdate',false));
$model->setParam('uniqueSilent',$localObj->getParam('uniqueSilent',false));
}
return $model;
}
private function _saveChild(&$model,$childName,$index=false,$parentId,$chidlVal,$arrayInValue,&$localObj){
$model->returnId($localObj->_returnId );
if(!$localObj->_validate)$model->_validate=false;
$model->setIgnore();
if(empty($arrayInValue ) || !is_array($arrayInValue) || isset($model->wfiles )){
if(!$model->multiplePK()){
$pKey=$localObj->getPK();
$model->$pKey=$localObj->$pKey;
}
$model->_new=$localObj->_new ;
if($model->save()){
if($localObj->_getChildIds){
$localObj->_getChildIds=$model->_getChildIds;
$pkmap=$model->getPK();
$localObj->_childID->$childName=$model->$pkmap;
if(isset($model->_childID ))$localObj->_childID->_childID=$model->_childID;
}
}
}else{
$model->_new=$localObj->_new;
$otherMap=$arrayInValue[0];
$childValues=(isset($chidlVal->$otherMap)?$chidlVal->$otherMap : null );
if(!is_array($childValues))$childValues=array($childValues );
if($model->_childRemoveNotPresent || $model->_childOnlyAddNew){
if($model->_childRemoveNotPresent){
if(!empty($childValues)){
$model->whereE($localObj->getPK(), $parentId );
if(!is_array($childValues))$childValues=array($childValues );
$model->whereIn($otherMap, $childValues, 0, true);
$model->delete();
}}
if($model->_childOnlyAddNew){
$model->whereE($localObj->getPK(), $parentId );
$allChildren=$model->load('lra',$otherMap );
$childValues=(!empty($childValues))?array_diff($childValues, $allChildren ) : $childValues;
$model->setParam('verifyUnique', true);
$model->_new=true;
}
}
if(empty($childValues ) || ( !is_object($childValues) && !is_array($childValues)) ) return;
foreach($childValues as $values){
if(empty($values)) continue;
$model->$otherMap=$values;
if($model->save()){
if($localObj->_getChildIds){
$localObj->_getChildIds=$model->_getChildIds;
$pkmap=$model->getPK();
$localObj->_childID->$childName=$model->$pkmap;
if(isset($model->_childID ))$localObj->_childID->_childID=$model->_childID;
}
}}}
$model->setIgnore(false);
}
private function _verifyData($constraintsA,&$mess,$new,&$localObj){
foreach($constraintsA as $key=> $constraint){
$map=$constraint->name;
if(isset($localObj->$map)){
$val=$localObj->$map;
$min=$max=$minU=0;
if(empty($val)) break;switch($constraint->type){
case '14':
case '15':
$max=($constraint->size < 255 )?$constraint->size : 255;
if($constraint->size>0 && strlen($val) > $max){
if($localObj->getParam('fieldsCorrect',false)){
$localObj->$map=substr($val, 0, $max );
}  else {
$modus=$localObj->_infos->namekey;
$mess=" You have too many charaters for the model: $modus and the field $map the maximum character you can enter is: " . $max ;
return false;
}
}
break;
case '4':
$val=(int)$val;
$maxU=4294967295;
$minS=-2147483648;
$maxS=2147483647;
if(!$this->_isSize($val , $constraint->attributes, $min, $max, $minU, $maxU, $minS, $maxS)){
$modus=$localObj->_infos->namekey;
$mess="1. Wrong value for the model : $modus and field $map : $val. The value needs to be between $min and $max" ;
return false;
}break;
case '1':
$val=(int)$val;
$maxU=255;
$minS=-128;
$maxS=127;
$status=$this->_isSize($val, $constraint->attributes, $min, $max, $minU, $maxU, $minS, $maxS );
if(!$status){
$modus=$localObj->_infos->namekey;
$mess="2. Wrong value for the model : $modus and field $map : $val. The value needs to be between $min and $max" ;
return false;
}break;
case '2':
$val=(int)$val;
$maxU=65535;
$minS=-32768;
$maxS=32767;
if(!$this->_isSize($val , $constraint->attributes, $min, $max, $minU, $maxU, $minS, $maxS)){
$modus=$localObj->_infos->namekey;
$mess="3. Wrong value for the model : $modus and field $map : $val. The value needs to be between $min and $max" ;
return false;
}break;
case '3':
$val=(int)$val;
$maxU=16777215;
$minS=-8388608;
$maxS=8388607;
if(!$this->_isSize($val , $constraint->attributes, $min, $max, $minU, $maxU, $minS, $maxS)){
$modus=$localObj->_infos->namekey;
$mess="4. Wrong value for the model : $modus and field $map : $val. The value needs to be between $min and $max" ;
return false;
}break;
case '10':
if($val=='0000-00-00 00:00:00') break;$datebis=explode(' ',$val);
$date=explode('-',$datebis[0]);
if(isset($datebis[1]))$time=explode(':',$datebis[1]);
if(!checkdate($date[1], $date[2], $date[0] )){
$mess='The value entered is not in the date and time format';
return false;
}elseif(isset($time) && ($time[0]>23 OR $time[1]>59 OR $time[2]>59)){
$mess='The value entered is not in the date and time format';
return false;
}break;
case '9':
if('0000-00-00'==$val || '0000-00-00 00:00'==$val ) break;$date=explode('-',$val );
if(!checkdate($date[1], $date[2], $date[0] )){
$mess='The value entered is not a valid formated date for '.$map;
return false;
}break;
case '14':
case '15':
case '16':
case '18':
case '19':
case '22':
case '23':
case '20':
if( substr($localObj->$map, strlen($localObj->$map)-4 )=='<br>')$localObj->$map=substr($localObj->$map, 0, strlen($localObj->$map)-4 );if( substr($localObj->$map, strlen($localObj->$map)-6 )=='<br />')$localObj->$map=substr($localObj->$map, 0, strlen($localObj->$map)-6 );$max=pow(2, 16);if( strlen($val) > $max){
$modus=$localObj->_infos->namekey;
$mess=" You have too many charaters for the model: $modus and the field $map the maximum character you can enter is: " .$max ;
return false;
}
break;
case '21':
case '17':
$max=pow(2, 8);if( strlen($val) > $max){
$modus=$localObj->_infos->namekey;
$mess=" You have too many charaters for the model: $modus and the field $map the maximum character you can enter is: " .$max ;
return false;
}
break;
case '12':
$time=explode(':',$val );
if($time[0]>23 OR $time[1]>59 OR $time[2]>59){
$mess='The value entered is not a date for '.$map;
return false;
}break;
case '8':
$val=str_replace(',','.',$val );
break;
case '5':
$maxU=18446744073709551615;
$minS=-9223372036854775808;
$maxS=9223372036854775807;
if(!$this->_isSize($val , $constraint->attributes, $min, $max, $minU, $maxU, $minS, $maxS)){
$modus=$localObj->_infos->namekey;
$mess="5. Wrong value for the model : $modus and field $map : $val. The value needs to be between $min and $max" ;
return false;
}}
}
}
return true;
}
private function _checkUK($new,$reset=false,&$localObj,&$namekeyEID,$fromCopy=false){
static $countFail=0;
if( in_array($localObj->_infos->type, array( 20, 30 )) ) return true;
if($reset)$countFail=0;
$countFail++;
if($countFail > 50){
$mess=WMessage::get();
return $mess->historyE('1410373252KGXR');
return false;
}
$pKey=$localObj->getPK();
$uniquesA=($localObj->multiplePK())?$localObj->getConstraints('ukpk',$localObj->_pubProperties ) : $localObj->getConstraints('uk',$localObj->_pubProperties );
if(empty($uniquesA) || ! is_array($uniquesA)){
return true;
}
foreach($uniquesA as $ulid=> $uniqueKeys){
if(!$fromCopy){
$sql=WModel::get($localObj->getModelID());
$NOPickListValues=false;
foreach($uniqueKeys as $keyUK=> $sizeUK){
if(isset($localObj->$keyUK)){
$sql->whereE($keyUK, $localObj->$keyUK );
}else{
$pKeyChildA=$sql->getPKs();
if( in_array($keyUK, $pKeyChildA )) break;
$NOPickListValues=true;
}}
if($NOPickListValues){
continue;
}
if(!$new){
if(isset($localObj->$pKey) && ! $localObj->_model->multiplePK())$sql->where($pKey, '!=',$localObj->$pKey );}
if($localObj->getParam('uniqueUpdate', false)){
$valStatus=$sql->existId();
$status=($valStatus > 0 )?$valStatus : false;
}else{
$status=$sql->exist();
}
}else{
$status=true;
}
if($status){
if($localObj->getParam('uniqueCorrect')){
asort($uniqueKeys);
end($uniqueKeys);
$biggestMap=key($uniqueKeys);
if(isset($localObj->$biggestMap)){
$biggestSize=current($uniqueKeys);
$biggestValue=$localObj->$biggestMap;
if(!is_numeric($biggestValue) && $biggestSize > 0){
$len=strlen($biggestValue);
if($len==$biggestSize){
if($biggestSize > 9 )
{
$biggestValueNew=substr($biggestValue, 0, $len - 9). '_copy'.time();
}else{
$biggestValueNew=substr($biggestValue, 0 , $len - 1). time();
}
}
elseif($biggestSize - $len > 9){
$biggestValueNew=$biggestValue.'_copy'.time();
}
else
{
$biggestValueNew=substr($biggestValue, 0, $biggestSize - 9). '_copy'.time();
}
$localObj->$biggestMap=$biggestValueNew;
}else{
$localObj->$biggestMap=$biggestValue + time();
}
if('namekey'==$biggestMap){
$this->_replaceProperNamekey($namekeyEID, $biggestValue, $localObj->$biggestMap );
}
$status=$this->_checkUK($new, false, $localObj, $namekeyEID );
if(!$status){
return false;
}
}else{
$PKsChildA=$localObj->getPKs();
if( in_array($biggestMap , $PKsChildA )) return true;
}
}else{
if($localObj->getParam('uniqueSilent', false)){
return false;
}elseif($localObj->getParam('uniqueUpdate', false)){
if($localObj->multiplePK()){
foreach($uniqueKeys as $keyUK=>$sizeUK){
if(isset($localObj->$keyUK))$sql->whereE($keyUK, $localObj->$keyUK );
else {
$mess=WMessage::get();
$mess->codeE('Error saving model'.$localObj->getModelNamekey(). '  no value defined for the following column (and unique key): '. $keyUK );
return false;
}}
}else{
$localObj->$pKey=$valStatus;
}
$localObj->_new=false;
}else{
$mess=WMessage::get();
$message='An entry with the same value(s) for the combination of fields (';
$message .=implode(',', array_keys($uniqueKeys));
$message .=') already exists. Please choose another value(s).';
return $mess->historyE($message );
}
}
}
}
return true;
}
private function _replaceProperNamekey(&$namekeyEID,$oldNamekey,$newNamekey){
if($oldNamekey==$newNamekey || empty($namekeyEID)) return true;
foreach($namekeyEID as $oneModelKey=> $oneModelVal){
foreach($oneModelVal as $oneNamekeyKey=> $oneNamekeyVal){
if($oneNamekeyVal==$oldNamekey){
$namekeyEID[$oneModelKey][$oneNamekeyKey]=$newNamekey;
return true;
}}
}
}
private function _checkUKForChildModel($new,$reset=false,&$localObj,&$namekeyEID){
$foundChildsA=$this->_getAllChilds($localObj );
if(empty($foundChildsA)){
return true;
}
foreach($foundChildsA as $CName=> $oneChild){
if(empty($localObj->$CName)) continue;
$childInstanceI=WModel::get($oneChild );
$childInstanceI->setParam('uniqueCorrect', true);
if(is_array($localObj->$CName)){
$newArrayREsultA=array();
foreach($localObj->$CName as $indexID=> $oneData){
foreach($oneData as $key=> $value){
$childInstanceI->$key=$value;
}
$status=$this->_checkUK($new, $reset, $childInstanceI, $namekeyEID );
if(!$status ) continue;
$newObject=new stdClass;
foreach($oneData as $key=> $value){
if(isset($childInstanceI->$key))$newObject->$key=$childInstanceI->$key;
}
$newArrayREsultA[$indexID]=$newObject;
}
$localObj->$CName=$newArrayREsultA;
}else{
}
}
return true;
}
private function _getAllChilds($object){
if(!empty($object)){
$propeetiesA=get_object_vars($object );
if(empty($propeetiesA)) return false;
$doundChildsA=array();
foreach($propeetiesA as $oneProp=> $oneVal){
if($oneProp[0]=='C'){
$modelID=substr($oneProp, 1 );
$doundChildsA[$oneProp]=$modelID;
}}
return $doundChildsA;
}
return false;
}
private function _isSize($val,$sign,&$min,&$max,$minU,$maxU,$minS,$maxS){
if($sign=='UNSIGNED' || $sign=='UNSIGNED ZEROFILL' || $sign==1 || $sign==2){
$min=$minU;
$max=$maxU;
return ( is_int($val )?(($val>=$min)?(($val<=$max)?true : false) : false) : false); }else{
$min=$minS;
$max=$maxS;
return (((string)$val===(string)(int)$val)?(($val>=$min)?(($val<=$max)?true : false) : false) : false);}}
private function _deleteOldFile($localObj,$arrKey,$standardFile=true,$eid=null){
static $alreadyDone=array();
$mySpecialPK=$localObj->_infos->pkey;
$key=$localObj->_infos->sid.'-'.$mySpecialPK.'-'.$localObj->$mySpecialPK;
if(!isset($alreadyDone[$key])){
if($standardFile){
$tempSameM=WModel::get($localObj->_infos->sid );
$tempSameM->whereE($mySpecialPK, $localObj->$mySpecialPK );
$myFilID=$tempSameM->load('lr',$arrKey );
if(!empty($myFilID)){$tempFileM=WModel::get('files');
$tempFileM->_basePath=JOOBI_DS_USER . $localObj->_fileInfo[$arrKey]->folder . DS;
$tempFileM->delete($myFilID );
}
}else{$tempFileM=WModel::get('files');
if(!empty($eid))$tempFileM->delete($eid );
}
$alreadyDone[$key]=true;
}
}
private function _getPropertiesAndChild(&$tellChild,&$localObj){
$vars=array();
$ObjVar=get_object_vars($localObj );
foreach($ObjVar as $key=> $val){
$letter=substr($key, 0, 1 );
if($letter !='_'){
if( strtolower($letter)===$letter){
$vars[]=$key;
}elseif( strtoupper($letter)===$letter){
$tellChild[]=$key;
}
}
}
return $vars;
}
private function _parentSync($nkEID,&$localObj){
foreach($nkEID as $Csid=> $neid){
$sid=substr($Csid,1);
$modelEm=WModel::get($sid);
$updateThem=array();
$corres=array();
foreach($localObj->$Csid as $element){
if($element->parent!=0){
if(isset($neid[$element->parent])){
if(!isset($corres[$element->parent])){
$modelEm->whereE('namekey',$neid[$element->parent] );
$modelEm->select($modelEm->getPK());
$corres[$element->parent]=$modelEm->load('lr');
}
$updateThem[$corres[$element->parent]][]=$element->namekey;
}}}
if(!empty($updateThem)){
foreach($updateThem as $newFid=> $arrayNamekeys){
$modelEm->whereIn('namekey',$arrayNamekeys);
$modelEm->setVal('parent',$newFid);
$modelEm->update();
}
}
}
}
private function _sendOutGoingData($model){
$libraryControllerM=WModel::get('library.controller');
$libraryControllerM->remember('outgoing'.$model->_infos->namekey, true, 'Outoging');
$libraryControllerM->whereE('app','model-outgoing');
$libraryControllerM->whereE('task',$model->_infos->namekey );
$ctrid=$libraryControllerM->load('lr','ctrid');
if(empty($ctrid)){
$cacheC=WCache::get();
$cacheC->resetCache('Outoging');
$libraryControllerM->app='model-outgoing';
$libraryControllerM->task=$model->_infos->namekey;
$libraryControllerM->namekey='model-outgoing.'.$model->_infos->namekey;
$libraryControllerM->premium=0;
$libraryControllerM->admin=1;
$libraryControllerM->publish=1;
$libraryControllerM->core=0;
$libraryControllerM->reload=0;
$libraryControllerM->trigger=50;
$libraryControllerM->rolid=WRoles::get('allusers','rolid');
$libraryControllerM->returnId();
$libraryControllerM->save();
if(!empty($libraryControllerM->ctrid))$ctrid=$libraryControllerM->ctrid;
}
if(!empty($ctrid)){
$modelInfo=clone $model;
$modelInfo->ModelNew=$model->_new;
$modelInfo->ModelName=$model->_infos->namekey;
foreach($modelInfo as $key=> $val){
$firstKey=substr($key, 0, 1 );
if('_'==$firstKey){
unset($modelInfo->$key );
}elseif('C'==$firstKey){
$modelID=substr($key, 1 );
$modelNamekey=WModel::get($modelID, 'namekey');
$modelInfo->$modelNamekey=$val;
unset($modelInfo->$key );
}}
WController::trigger('model-outgoing',$model->_infos->namekey, $modelInfo );
}
}
private function _processCustomFields(&$modelObj){
$modelFieldsM=WModel::get('design.modelfields','object', null, false);
if(!empty($modelFieldsM)){
$modelFieldsM->makeLJ('design.fields','fieldid');
$modelFieldsM->whereE('sid',$modelObj->_infos->sid );
$modelFieldsM->select('column');
$modelFieldsM->select('namekey', 1 );
$myFieldsA=$modelFieldsM->load('ol');
if(!empty($myFieldsA)){
foreach($myFieldsA as $oneField){
if($oneField->namekey=='output.datetime' || $oneField->namekey=='output.dateonly'){
$modelObj->validateDate($oneField->column );
}}}
}
}
}