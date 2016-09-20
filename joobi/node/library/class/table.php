<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class WTable extends WQuery {
var $_ignore=false;var $_replace=false;public $_returnId=false;
var $_new=true;
var $_forceInsert=false;
var $_query=null;
private $_identify=false;private $_selectA=array();var $_from=null;var $_as_cd=array();
 public $_leftjoin=null;   
public $_whereValues=null;private $_groups=null ;  
private $_orderby=null;private $_groupby=array();var $_update=null;var $_limitl=null;var $_limith=null;var $_total=null;
private $_seachedWords=array();
var $_result=null;
private $_noReset=false; private $_queryError=false;
public $_printOnly=null;
var $_whereOnValues=array();
protected $_remember=false;protected $_rememberCache='static';protected $_rememberID=0;
protected $_rememberQuery=false;protected $_rememberFolder='QueryData';
private $_indexResult=false; 
protected $_hasModelAndConnection=false;
protected $_failedQuery=false;
protected $_checkMaxElements=false;
public static $addDBName=true;
private static $_cacheC=null;
private $_queryType=false;
function __construct($tablename='',$dbname='',$pkey=null,$params=null,$tablePrefix=null){
  if(!empty($tablename) && is_numeric($tablename)){
  $caching=WPref::load('PLIBRARY_NODE_CACHING');
$caching=($caching > 0 )?'cache' : 'static';
if(!isset($params))$params=new stdClass;
$params->_infos=WCache::getObject($tablename, 'Table',$caching, true);
  if(empty($params->_infos))$params->_infos=WCache::getObject($tablename, 'Table','static', true);
$pkey=$params->_infos->pkey;
}
WTable::$addDBName=false;
parent::__construct($params, false);
if(!empty($pkey)){
if( strpos($pkey,',')){
$this->_infos->primaryKeys=explode(',',$pkey);
$this->_infos->mpk=true;
}else{
$this->_infos->pkey=$pkey;
$this->_infos->primaryKeys=array($pkey);
$this->_infos->mpk=false;
}}if(isset($tablePrefix)){
$this->_infos->tableprefix=$tablePrefix;
}
if(!empty($tablename) && !is_numeric($tablename)){
$this->_infos->tablename=$tablename;
}
if(!empty($this->_infos->tablename))$this->getAs($this->_infos->tablename );
else $this->getAs($tablename );
if(empty($this->_infos->addon))$this->_infos->addon=WGet::DBType(); 
if(!isset($this->_db))$this->_db=WQuery::getDBConnector($this->_infos->addon, $this->_infos );
if(empty($this->_db))$this->_failedQuery=true;
if(!empty($this->_infos))$this->_hasModelAndConnection=true;
}
public static function get($tableName='',$dbName='',$pKey=null,$params=null,$tablePrefix=''){
$instance=new WTable($tableName, $dbName, $pKey, $params, $tablePrefix );
return $instance;
}
public function isReady(){
return $this->_hasModelAndConnection;
}
public function cancelQuery(){
$this->_failedQuery=true;
WMessage::log($this, 'failed-query');
return false;
}
public function getSQL($tablename,$showMessage=true){
  $tableModel=WModel::get('library.table');
$tableModel->whereE('dbtid',$tablename );
$tableModel->select( array('name','prefix','pkey','dbtid','type','domain','export','exportdelete'), 0, array('tablename','tableprefix'));
$tableInfo=$tableModel->load('o');
$tableInfo->id=$tablename;
return $tableInfo;
}
public function remember($id=0,$cache=false,$folder='QueryData'){
if(empty($id )){
$this->_remember=false;
return false;
}$this->_remember=true;$this->_rememberID=$id;
$this->_rememberQuery=false;
$this->_rememberCache=$cache;
$this->_rememberFolder=$folder;
}
public function rememberQuery($cache=false,$folder='QueryData'){
$this->_remember=true;$this->_rememberCache=$cache;
$this->_rememberQuery=true;
$this->_rememberFolder=$folder;
}
public function load($type=null,$selects=null){
  if(!$this->_hasModelAndConnection ) return false;
if(!isset($type)){
if(!$this->setQuery()){
$this->resetAll();
return false;
}return $this->_result;
}
if($type=='q' || $type=='qr'){
$status=$this->setQuery($type, $selects );
$this->resetAll();
return $status;
}
$this->select($selects );
$remember=false;
$rememberQuery=false;
  if($this->_remember){
  $remember=true;
  if($this->_rememberQuery){
  $rememberQuery=true;
  $propertyA=array('_as_cd','_selectA','_whereValues','_leftjoin','_groupby','_orderby','_whereOnValues','_seachedWords','_limith','_limitl');
  $key='';
  foreach($propertyA as $oneProp){
  if(!empty($this->$oneProp )){
  $key .=serialize($this->$oneProp );
  }  $key .='|';
  }
  $rememberID=md5($key );
$caching=($this->_rememberCache )?'cache' : 'static';
if(!isset(self::$_cacheC)) self::$_cacheC=WCache::get();
$data=self::$_cacheC->get($rememberID, $this->_rememberFolder, $caching );
if(!empty($data)){
$this->_remember=false;
$this->_rememberQuery=false;
$this->resetAll();
return $data;
}  }else{
    if(empty($this->_rememberID )){
  $remember=false;
  $rememberQuery=false;
  }else{
  $caching=($this->_rememberCache )?'cache' : 'static';
if(!isset(self::$_cacheC)) self::$_cacheC=WCache::get();
$tableID=(empty($this->_infos->dbtid)?$this->_infos->tablename : $this->_infos->dbtid );
$rememberID='md-'.$tableID.'-id-'.$this->_rememberID;
$data=self::$_cacheC->get($rememberID, $this->_rememberFolder, $caching );
if(!empty($data)){
$this->_remember=false;
$this->_rememberQuery=false;
$this->resetAll();
return $data;
}  }  }
  }
$selectCount=count($this->_selectA );
if(is_array($type)){
$this->whereIn($this->getPK(), $type );
if($selectCount>1 || $selectCount==0){
$status=$this->setQuery('ol');
if(!empty($this->_checkMaxElements)){
if( count($status) > ($this->_checkMaxElements * 0.95 )){$MODELNAME=WModel::get($this->_infos->sid, 'namekey');
$message=WMessage::get();
$message->adminE('The system is getting very close to the maximum number of '.$MODELNAME.', please increase the limit if appropriate.');
} 
}
}else{
$status=$this->setQuery('lra');
}}elseif( is_numeric($type)){
$this->whereE($this->getPK() , $type );
if($selectCount>1 || $selectCount==0){
$status=$this->setQuery('o');
}else{
$status=$this->setQuery('lr');
}}else{
if(!$this->setQuery($type))$status=false;
else $status=true;
if( in_array($type, array('o','lr')))$this->resultToObj();
}
if(!$status){
$this->resetAll();
return false;
}
if($remember){
  self::$_cacheC->set($rememberID, $this->_result, $this->_rememberFolder, $caching );
}
$this->resetAll();
return $this->_result;
}
  public function save($key=null,$value=null){
  if(!$this->multiplePK()){
$pKey=$this->getPK();
$this->_new=(isset($this->$pKey) && !empty($this->$pKey ))?false : true ;
  }
return $this->store();
  }
public function existId($eid=null){
if(isset($eid )){
  $this->resetAll();
  if($this->multiplePK()){
  if(!is_array($eid)){
  $this->adminE('Table with multiple PK need an array as input.');
  return false;
  }  $PKA=explode(',',$this->getPKs());
  foreach($PKA as $k=> $v)$this->whereE($v, $eid[$k] );
  }else{
  $this->whereE($this->getPK(), $eid );
  } 
}
if($this->multiplePK()){
  $this->select($this->getPKs());
}else{
  $this->select($this->getPK());
}
if(!$this->setQuery('lr')) return false;
return $this->_result;
  }  
  public function exist($eid=null){
  if(empty($this->_db)) return $this->cancelQuery();
    if(isset($eid )){
  if($this->multiplePK()){
  $this->adminE('Table with multiple PK can be tested.');
  return false;
  }  
  $this->resetAll();
  $this->whereE($this->getPK(), $eid );
  }  
  $leftJoin=$this->_makeLeftJoin();
  $select=(empty($leftJoin))?'' : 'A.';
  if($this->multiplePK()){
  $pk=$this->getPKs();
  $satus=$this->setQuery('lr','SELECT !ISNULL('.$select.$this->_db->nameQuote($pk[0]). ') FROM '.$this->makeT(). $leftJoin . $this->_makeWhere(). ' LIMIT 1');
  }else{
  $satus=$this->setQuery('lr','SELECT !ISNULL('.$select.$this->_db->nameQuote($this->getPK()). ') FROM '.$this->makeT(). $leftJoin . $this->_makeWhere(). ' LIMIT 1');
  }  
      if(!$satus ) return false;
  return (bool)$this->_result;
  } 
public function makeT($as=0,$quotes=true){
if(empty($this->_db)) return $this->cancelQuery();
$function=$quotes?'nameQuote' : 'isSecure';
$tableName=$this->getTableName();
if(empty($tableName)) return $this->cancelQuery();
$tbName=$this->_db->$function($tableName );
if( WTable::$addDBName)$tb=$this->_db->$function($this->getDBName()). '.'.$tbName.' ';
else $tb=$tbName.' ';
if($this->_identify){
$tb .=' AS '.$this->_convertAs($as ).' ';
}
return $tb;
}
public function resetAll(){
if(empty($this->_noReset)){
$this->_identify=false;
$this->_update=array();
$this->_total=null;
$this->_as_cd=array(0); $this->_limith=null;
$this->_limitl=null;
$this->_groupby=array();
$this->_orderby=null;
$this->_selectA=null;
$this->_whereValues=null;
$this->_whereOnValues=array();
$this->_search=null;
$this->_searchables=null;
$this->_seachedWords=array();
$this->_indexResult=false;
$this->_leftjoin=null;
$this->_printOnly=null;
$this->_remember=false;
$this->_rememberQuery=false;
}
}
public function delete($id=null){
if(!empty($id)){
    $this->_query=' DELETE FROM '.$this->makeT();
  if(is_array($id)){
  $this->whereIn($this->getPK(), $id );
  $qLimit='';
  }else{
  $this->whereE($this->getPK(), $id );
  $qLimit=' LIMIT 1';
  }
  $this->_query .=$this->_makeWhere(). $qLimit;   return $this->setQuery('',$this->_query );
}else{
    $where=$this->_makeWhere();
  if(!empty($where)){
  if(!empty($this->_leftjoin)){
  $this->_query=' DELETE A';  }else{
  $this->_query=' DELETE '.$this->makeT();
  }
$this->_query .= ' FROM '.$this->makeT();
if(!empty($this->_leftjoin))$this->_query .=$this->_makeLeftJoin();
$this->_query .=$where;
$start=($this->_limitl)?$this->_limitl.',' : '';
$limittag=($this->_limith)?' LIMIT '.$start . $this->_limith : '';
return $this->setQuery('',$this->_query . $limittag );
  }
  $mess=WMessage::get();
  $mess->codeE(' You cannot delete the entire table, make sure the where clause is not missing, for model:"'.$this->makeT().'"!',array(),'query');
  return false;
}
}
public function returnId($set=true){
$this->_returnId=$set;
}
public function indexResult($map=false){
$this->_indexResult=$map;
}
public function multiplePK(){
return (isset($this->_infos->mpk)?$this->_infos->mpk : false);
}
public function getPK(){
if(isset($this->_infos->pkey)) return $this->_infos->pkey;
return null;
}
public function getPKs(){
return isset($this->_infos->primaryKeys)?$this->_infos->primaryKeys : null;
}
public function total(){
  $limith=$this->_limith;
$limitl=$this->_limitl;
$this->_limith=null;
$this->_limitl=null;
if(empty($this->_selectA)){
$pksA=$this->getPKs();
if(empty($pksA)) return false;
foreach($pksA as $one){
$this->select($one );
}$removeSelect=true;
}else{
$removeSelect=false;
}
$this->_makeQuery();
$this->setQuery('lr','SELECT COUNT(*) FROM ('.$this->_query.') AS X');
if($removeSelect ) unset($this->_selectA);
$this->_limith=$limith;
$this->_limitl=$limitl;
return $this->_result;
}
public function insertSelect($select,$query,$updateSelect=false,$insertIgnore=false){
if(is_array($select)){
  foreach($select as $map){
$update=new stdClass;
$update->champ=$map;
$update->value='';
$this->_update[]=$update;
  }}else{
  $update=new stdClass;
  $update->champ=$select;
  $update->value='';
  $this->_update[]=$update;
}
if($insertIgnore)$this->_ignore=true;
return ($updateSelect?$this->_update($query ) : $this->insert($query ));
}
public function union($type,$model,$returnString=false){
if(empty($this->_db)) return $this->cancelQuery();
  $query2=$model->printQ('load','ol',null,true);
  $query1=$this->printQ('load','ol',null,true);
  if($returnString){
  return $this->_db->union($query1, $query2 );
  }else{
    $this->setQuery($type, $this->_db->union($query1, $query2 ));
  return $this->_result;
  }
}
public function setIgnore($status=true){
$this->_ignore=$status;
}
public function setReplace($status=true){
$this->_replace=$status;
}
public function updateInsert(){
$temp2=$this->_update;
$status=$this->update();
if(empty($status )) return $status;
$nb=$this->affectedRows();
if(empty($nb)){
$this->_update=$temp2;
return $this->insertIgnore();
}
 return $status;
}
public function updatePlus($column,$values=1){
$updtO=new stdClass;
$updtO->champ=$column;
$updtO->operation=($values >=0?'+' : '-');
$updtO->value=abs((real)$values );
$this->_update[]=$updtO;
}
public function setCalcul($value1,$operator,$value2,$as1=null,$as2=null){
$calO=new stdClass;
$calO->value1=$value1;
$calO->operator=$operator;
$calO->value2=$value2;
$calO->as1=$as1;
$calO->as2=$as2;
return $calO;
}
public function setVal($column,$values,$as1=0,$as2=null,$special=null){
$key=$column.'|'.$as1.'|'.$special;  $updtO=new stdClass;
$updtO->champ=$column;
$updtO->value=$values;
$updtO->champAS=$as1;
$updtO->valueAS=$as2;
if(isset($special)){
  $updtO->special=strtolower($special);
}
$this->_update[$key]=$updtO;
if(empty($as1 ) && $as2==null && $special==null)$this->$column=$values;
}
public function update($values=null){
if(!empty($values) && (is_array($values ) || is_object($values))){
  foreach($values as $key=> $val){
$this->setVal($key , $val );
  }
}
return $this->_update();
}
public function romoveSelect($what2Remove=''){
if(empty($what2Remove))$this->_selectA=array();
elseif('special'==$what2Remove){
foreach($this->_selectA as $key=> $oneS){
if(!empty($oneS->special)) unset($this->_selectA[$key]);
}}}
public function select($column,$as=0,$alias=null,$special=0,$multipleKey=false,$externalSite=null){
if(!empty($special)){
static $count=0;
static $setAliasA=array();
if(isset($setAliasA[$alias])){
$alias .='_'.(string)$count;
}$count++;
$setAliasA[$alias]=true;
}
if(!isset($this->_selectA))$this->_selectA=array();
if(!is_array($column)){
if(!is_object($column)){
$column=trim($column );
if( strlen($column) > 0){
$key=$column.'-'.$as.'-'.$alias.'-'.$special.'-'.$externalSite.'-'.$multipleKey;
if(isset($this->_selectA[$key])) return true;
$sltO=new stdClass;
$sltO->map=$column;
$sltO->as1=$as;
$sltO->alias=$alias;
$sltO->special=$special;
$sltO->site=$externalSite;
$this->_selectA[$key]=$sltO;
}
}else{
static $nb=1;
$key='calval'.$nb;
$nb++;
$sltO=new stdClass;
$sltO->map=$column;
$sltO->as1=$as;
$sltO->alias=$alias;
$sltO->special=$special;
$sltO->site=$externalSite;
$this->_selectA[$key]=$sltO;
}
return $alias;
}
foreach($column as $arrayKey=> $value){
if(is_array($alias)){
if(!empty($alias[$arrayKey])){
$myAlias=$alias[$arrayKey];
}else{
$myAlias=null;
}}else{
$myAlias=null;
}
$key=$value.'-'.$as.'-'.$myAlias.'-'. $externalSite;
if(isset($this->_selectA[$key])) continue;
$sltO=new stdClass;
$sltO->map=$value;
$sltO->as1=$as;
$sltO->alias=$myAlias;
$sltO->special=$special;
$sltO->site=$externalSite;
$this->_selectA[$key]=$sltO;
}
}
public function selectPK(){
if($this->_mpk){
foreach($this->_primaryKeys as $key=> $val ){
$this->select($val );
}}else{
$this->select($this->_pkey );
}  }
public function where($column,$cond,$value,$as1=0,$as2=null,$bkbefore=0,$bkafter=0,$operator=0,$type='default',$externalSiteA=null,$externalSiteB=null){
if(!empty($column) && !empty($cond)){
if(is_array($value)){
  $mess=WMessage::get();
  $mess->adminN('A query input was detected as an array instead of a string and thus, was intercepted before execution. Please notify the support of the application. This message is only displayed to administrators for debug purpose.',array(),'query');
$this->_queryError=true;
return true;
}elseif( is_object($value)){
  $mess=WMessage::get();
  $mess->adminN('A query input was detected as an object instead of a string and thus, was intercepted before execution. Please notify the support of the application. This message is only displayed to administrators for debug purpose.',array(),'query');
$this->_queryError=true;
return true;
}
$where=new stdClass;
$where->champ=$column;
$where->cond=( strlen($cond)>10 )?'=' : $cond;$where->value=$value;
$where->asi1=$as1;
$where->asi2=$as2;
$where->bkbefore=$bkbefore;
$where->bkafter=$bkafter;
$where->operator=$operator;
$where->type=$type;
if(isset($externalSiteA))$where->extstida=$externalSiteA;
if(isset($externalSiteB))$where->extstidb=$externalSiteB;
$this->_whereValues[]=$where;
}
}
public function whereOn($column,$cond='=',$value,$as=1,$as2=null,$bkbefore=0,$bkafter=0,$operator=0){
  $where=new stdClass;
  $where->champ=$column;
  $where->cond=( strlen($cond)>4 )?'=' : $cond;  $where->value=$value;
  $where->asi1=$as;
  $where->asi2=$as2;
  $where->bkbefore=$bkbefore;
  $where->bkafter=$bkafter;
  $where->operator=$operator;
  $where->type='default';
  $this->_whereOnValues[$as][]=$where;
}
public function whereLanguage($as=1,$lgid=null,$whereOn=true){
if(empty($lgid)){
$useMultipleLang=defined('PLIBRARY_NODE_MULTILANG')?PLIBRARY_NODE_MULTILANG : 0;
if($useMultipleLang){
$lgid=WUser::get('lgid');
if(empty($lgid))$lgid=1;
}else{ $useMultipleLangENG=defined('PLIBRARY_NODE_MULTILANGENG')?PLIBRARY_NODE_MULTILANGENG : 1;
$lgid=($useMultipleLangENG?1 : WApplication::userLanguage());
}}
if($whereOn && !empty($as)){$this->whereOn('lgid','=',$lgid, $as );
}else{
$this->whereE('lgid',$lgid, $as );
}
}
public function whereE($column,$value,$as1=0,$as2=null,$bkbefore=0,$bkafter=0,$operator=0,$type='default'){
$this->where($column, '=',$value, $as1, $as2, $bkbefore, $bkafter, $operator, $type );
}
public function whereNotIn($column,$values,$as=0,$bkbefore=0,$bkafter=0,$operator=0,$dontFilter=false,$externalSiteA=null,$externalSiteB=null){
  $this->whereIn($column , $values, $as , true, $bkbefore, $bkafter, $operator, $dontFilter, $externalSiteA, $externalSiteB );
}
public function whereIn($column,$values,$as=0,$notIn=false,$bkbefore=0,$bkafter=0,$operator=0,$dontFilter=false,$externalSiteA=null,$externalSiteB=null){
  $where=new stdClass;
$where->champ=$column;
if(empty($values)){
$message=WMessage::get();
  $message->codeE('The WHERE IN could not be executed because the value passed is empty for the map : '. $column,array(),'query');
  $this->_queryError=true;
return false;
}
if(is_array($values)){
  $where->values=$values;
}elseif(!$dontFilter){
$message=WMessage::get();
$message->codeE('The WHERE IN could not be executed because the value passed is not an array for the map : '. $column,array(),'query');
$this->_queryError=true;
return false;
}
if($dontFilter){
$where->values=trim($values, ';');}else{
}
$where->asi=$as;
$where->notIn=$notIn;
$where->bkbefore=$bkbefore;
$where->bkafter=$bkafter;
$where->operator=$operator;
$where->type='in';
$where->filter=$dontFilter;
if(isset($externalSiteA))$where->extstida=$externalSiteA;
if(isset($externalSiteB))$where->extstidb=$externalSiteB;
$this->_whereValues[]=$where;
}
public function whereSearch($column,$value,$as=0,$sign=null,$operator=null,$doNotEscape=false){
if(empty($column) || empty($value)) return;
if(isset($operator)){
$OR_operator=( strtoupper($operator)=='AND'?false : true);
}
if(is_array($column)){
foreach($column as $oneColum){
$seachedWords=new stdClass;
$seachedWords->map=$oneColum;
$seachedWords->asi=$as;
$seachedWords->noEsc=$doNotEscape;
$seachedWords->value=(is_array($value)?$value : array($value));if(!empty($sign) && is_string($sign))$seachedWords->sign=$sign;
if(isset($OR_operator))$seachedWords->opr=$OR_operator;
$this->_seachedWords[]=$seachedWords;
}}else{
$seachedWords=new stdClass;
$seachedWords->map=$column;
$seachedWords->asi=$as;
$seachedWords->noEsc=$doNotEscape;
$seachedWords->value=(is_array($value)?$value : array($value));
if(!empty($sign) && is_string($sign))$seachedWords->sign=$sign;
if(isset($OR_operator))$seachedWords->opr=$OR_operator;
$this->_seachedWords[]=$seachedWords;
}
}
public function isNull($map,$condition=true,$as=0,$bkbefore=0,$bkafter=0,$operator=0){
$isNullObj=new stdClass;
$isNullObj->champ=$map;
$isNullObj->condition=$condition;
$isNullObj->asi=$as;
$isNullObj->bkbefore=$bkbefore;
$isNullObj->bkafter=$bkafter;
$isNullObj->operator=$operator;
$isNullObj->type='isnull';
$this->_whereValues[]=$isNullObj;
}
public function orderBy($column,$direction='ASC',$as=0,$useAlias=false,$alias=null){
$key=is_object($column)?serialize($column). $as : $column . $as;
if(!empty($this->_orderby[$key] )){
return true;
}
$order=new stdClass;
$order->champ=$column;
$order->direction=strtoupper($direction);$order->asi=$as;$order->useAlias=$useAlias;
$order->alias=$alias;
$this->_orderby[$key]=$order;
}
public function groupBy($column,$as=0){
$groupBy=new stdClass;
$groupBy->champ=$column;
$groupBy->asi=$as;$this->_groupby[]=$groupBy;
}
public function setLimit($limit,$start=null){
if($start){
if($start < 0){
$start=0;
}$this->_limitl=(int)$start;
}
if($limit < 1){
return false;
}
$this->_limith=(int)$limit;
}
public function setReset($bool=false){
$this->_noReset=!$bool;
}
public function setFrom($column){
$this->_from=$column;
}
public function setDistinct(){
$this->_distinct=true;
}
public function getAs($sid=0){
if(empty($sid)) return false;
  if(isset($this->_as_cd[$sid])) return $this->_as_cd[$sid];
return $this->setAs($sid );
}
public function setAs($sid,$val=0){
if(empty($val)){
    $val=sizeof($this->_as_cd );
}$this->_as_cd[$sid]=$val;
return $val;
}
public function getSIDFromAs($as){
$temp=array_flip($this->_as_cd);
return $temp[$as];
}
public function addTable($tablename,$dbname=''){
$table=new stdClass;
$table->as=$this->getAs($tablename);
$table->tablename=$tablename;
$table->dbname=$dbname;
$this->_addTable[$tablename]=$table;
$this->_identify=true;
}
public function makeLJ($tablename,$dbname='',$cond1='',$cond2='',$as1=0,$as2=null,$firstOperator=''){
  $myTable=WTable::get($tablename, $dbname );
  $tablename=$myTable->getTableName();
if(empty($tablename)){
$this->_failedQuery=true;
return false;
}
$dbname=$myTable->getDBName();
  if(empty($cond1))$cond1=$myTable->multiplePK()?$this->getPK() : $myTable->getPK();
  $key=$tablename . $dbname . $as1 . $cond1 . $as2 . $cond2;
if(isset($this->_leftjoin[$key])) return;
$join=new stdClass;
$join->tablename=$tablename;
$join->dbname=$dbname;
$join->as1=$as1;
$join->as2=$as2;
$join->cond1=$cond1;
$join->cond2=$cond2 ;
$join->Vopr1=$firstOperator;
$this->_leftjoin[$key]=$join;
$this->_identify=true;
}
public function printQ($task='',$qType='ol',$params=null,$noSemiColumn=false,$multipleDB=true){
$this->_printOnly=true;
$this->_multipleDB=$multipleDB;
$this->_noCheckLimit=true;
if(!empty($task)){
  if(isset($params)){
if(!is_array($params)){
  if( is_object($params)){
$message=WMessage::get();
$message->codeE('When you use the printQ function the params need to be an array!');
return false;
  }else{
$params[0]=$params;
  }}call_user_func_array(array(&$this,$task), $params);
  }else{
$this->$task();
  }}else{
if(!$this->setQuery($qType )) return false;
}return $this->_query . (($noSemiColumn )?'' : ';');
}
public function setQueryType($status=false){
$this->_queringType=$status;
}
protected function setQuery($qType='ol',$query=null){
if($this->_failedQuery){$this->_result=null;
return false;
}if($this->_queringType=== false) return false;
$this->_result=false;
if(!$this->_limith && ( in_array($qType, array('o','object','lr','r','result')) )){
  $this->setLimit( 1 );
}
if(!isset($query)){
$this->_replacePrefix=false;
$this->_makeQuery();
$query=$this->_query;
}else{
$this->_replacePrefix=true;
$this->_query=$query;
}
if(!empty($this->_printOnly)){
  $this->_printOnly=null;
  return $query;
}
return parent::setQuery($qType, $query );
  }
public function showQ($title='',$status=true){
$this->_debugTitle=$title;
$this->_debug=$status;
return $this->_query;
}
public function resultToObj($addslashes=false){
if(!$this->_result){
return true;
}
if( is_object($this->_result)){
$properties=get_object_vars($this->_result );
}elseif(is_array($this->_result)){
$properties=$this->_result;
}else{
return true;
}
if(!empty($properties)){
if($addslashes){
foreach($properties as $key=> $value){
if(is_object($this->_result)){
$this->$key=$this->_db->escape($this->_result->$key );
}else{
$this->$key=$this->_db->escape($this->_result[$key] );
}
}}else{
foreach($properties as $key=> $value){
if( is_object($this->_result)){
$this->$key=$this->_result->$key;
}else{
$this->$key=$this->_result[$key];
}
}}}}
public function insertIgnore($query=null,$ValueToInsert=null){
$this->_ignore=true;
return $this->insert($query, $ValueToInsert );
}
public function replace($query=null,$ValueToInsert=null){
$this->_replace=true;
return $this->insert($query, $ValueToInsert );
}
public function insert($query=null,$ValueToInsert=null){
$insName=array();
$insVal=array();
if($this->_queringType=== false) return false;
if(empty($this->_db)) return $this->cancelQuery();
if(!empty($this->_update )){
foreach($this->_update as $key=> $val){
  if(!isset($val->special)){
  $insVal[]=$this->valueQuote($val->value );
  }else{
  if($val->special=='ip' && !empty($val->value)){
  $insVal[]=$this->_db->insertInetAton($this->valueQuote($val->value ));
  }else{
    continue;
  }  }  
  $makeC=$this->_makeC($val->champ );
  if(empty($makeC)) continue; 
  $insName[]=$makeC;
}
}else{
foreach( get_object_vars($this ) as $k=> $v){
  if(!isset($this->$k) || is_array($v) || is_object($v) or $k[0]=='_' || (strtoupper($k[0])===$k[0])) continue;
if($k=='ip'){
if(!empty($v)){
$insVal[]=$this->_db->insertInetAton($this->valueQuote($v ));
}else{
continue;
}  $makeC=$this->_makeC($k );
  if(empty($makeC)) continue;
  $insName[]=$makeC;
}else{
  $makeC=$this->_makeC($k );
  if(empty($makeC)) continue;
  $insName[]=$makeC;
$insVal[]=$this->valueQuote($v );
}}}
$inserttag=' ('.(count($insName )? implode(',',$insName ) : ''). ')';
if(isset($query)){
  $inserttag .=$query;
}elseif(!isset($ValueToInsert)){
  if(empty($insVal)){return true;
  }  $inserttag .=' VALUES ('. (count($insVal )? implode(',',$insVal ) : '').')';
}else{
  if(!is_array($ValueToInsert )) return true;
  $inserttag .=' VALUES '. implode(',',$ValueToInsert ).';';
}
$ign=(!empty($this->_ignore))?' IGNORE' : '' ;$this->_query=(!empty($this->_replace )?'REPLACE' : 'INSERT'.$ign ). ' INTO '.$this->makeT();
$this->_query .=$inserttag;
$myStatus=$this->setQuery('',$this->_query );
if($myStatus && $this->_returnId){
foreach($this->getPKs() as $myPkey){
if(empty($this->$myPkey)){
$this->$myPkey=$this->lastId();
}}}
return $myStatus;
  }
  public function replaceMany($selects=array(),$datas){
  return $this->insertMany($selects, $datas, true);
  }
public function insertMany($selects=array(),$datas,$replace=false){
if(empty($datas) && !is_array($datas)) return true;
if(empty($selects) || !is_array($selects)){
$firstElement=current($datas);
  if( is_object($firstElement)){
$selects=array_keys(get_object_vars($firstElement));
  }else{
return false;
  }
}
foreach($selects as $select){
  $updtO=new stdClass;
  $updtO->champ=$select;
  $updtO->value=2;  $this->_update[]=$updtO;
}
$forInsert=array();
foreach($datas as $data){
  if( is_object($data)){
$insVal=array();
foreach($selects as $select){
  $insVal[]=$this->valueQuote($data->$select );
}$forInsert[]='('.implode(',',$insVal).')';
  }elseif(is_array($data)){
$insVal=array();
foreach($data as $donnee){
  $insVal[]=$this->valueQuote($donnee );
}$forInsert[]='('.implode(',',$insVal).')';
  }
}
if($replace)$this->_replace=true;
return $this->insert( null, $forInsert );
  }
public function emptyTable(){
$this->setQuery('',$this->_db->emptyTable($this->makeT()) );
return $this->_result;
}
public function optimizeTable(){
return $this->load('q',$this->_db->optimizeTable($this->makeT()) );
}
public function repairTable(){
return $this->load('q',$this->_db->repairTable($this->makeT()) );
}
public function deleteTable($ifExist=false){
return $this->load('q',$this->_db->deleteTable($this->makeT(), $ifExist ));
}
public function createTable($engine='INNODB'){
return $this->setQuery('q',$this->_db->createTable($this->makeT(), $engine ));
}
public function showTable(){
$this->setQuery('o',$this->_db->showTable($this->makeT()) );
 return $this->_result;
}
public function tableExist(){
$this->whereE('Name',$this->getTableName());
$result=$this->showDBTables(false);
return empty($result)?false : true;
}
public function showDBTables($fullInfo=false,$database=''){
$result=($fullInfo)?'ol' : 'lra';
if(empty($database))$database=$this->getDBName();
$query=$this->_db->showDBTables($database, $fullInfo );
if(!empty($this->_infos->tablename)){
$prefix=empty($this->_infos->tableprefix )?JOOBI_DB_PREFIX : $this->_infos->tableprefix;
$query .=' LIKE '.$this->_db->valueQuote($prefix . $this->_infos->tablename );
}
$this->setQuery($result, $query );
return $this->_result;
}
public function showColumns(){
$realFields=array();
$table=$this->makeT();
if(empty($table)) return $realFields;
if(!$this->setQuery('ol',$this->_db->showColumns($table )) ) return false;
if(empty($this->_result)) return $realFields;
foreach($this->_result as $field){
$type=$field->Type;
$size=0;
$pos=strpos($type, '(');
if((int)$pos > 0 ){
  $type=substr($field->Type,0,$pos);
  $pregResult=preg_match('#\(.*\)#',$field->Type,$resultats);
  $size=str_replace(array('(',')'),'',$resultats[0]);
}
$attribute='';
$pos=strpos($field->Type,' ');
if((int)$pos>0){
  $attribute=trim(strstr($field->Type,' '));
}
$map=strtolower($field->Field);
$realFields[$map]['name']=$map;
$realFields[$map]['type']=strtoupper($type);
$realFields[$map]['size']=(int)$size;
$realFields[$map]['attributes']=strtoupper($attribute);
$realFields[$map]['mandatory']=$field->Null=='NO'?true : false;
$realFields[$map]['default']=(string)$field->Default;
$realFields[$map]['autoinc']=$field->Extra=='auto_increment'?true : false;
  }
return $realFields;
}
public function changeColumn($next,$previous=null){
if(empty($previous)){
$actualTable=WTable::get($this->_infos->dbtid );
$columnsA=$actualTable->showColumns();
if(!empty($columnsA[$next['name']])) return false;
}
$q=$this->_db->changeColumn($this->makeT(), $next, $previous );
$status=$this->load('q',$q );
if(empty($status)) return $status;
else return $q;
}
public function dropColumn($columnName){
  return $this->load('q',$this->_db->dropColumn($this->makeT(), $columnName ));
}
public function redoPK($arrayPK){
$result=true;
$indexes=$this->showIndexes();
if(!empty($indexes->pkey)){
$result=$this->load('q',$this->_db->dropPK($this->makeT()));
}
$result=$this->load('q',$this->_db->addPK($this->makeT(),$arrayPK)) && $result;
return $result;
}
public function dropKey($uniqueKey){
return $this->load('q',$this->_db->dropKey($this->makeT(),$uniqueKey));
}
public function dropIndex($indexName){
return $this->load('q',$this->_db->dropIndex($this->makeT(),$indexName));
}
public function addKey($uniqueKey,$uniqueFields){
$query=$this->_db->addKey($this->makeT(), $uniqueKey, $uniqueFields );
return $this->load('q',$query );
return $query;
}
public function addIndex($indexName,$indexFields){
$query=$this->_db->addIndex($this->makeT(), $indexName, $indexFields );
$this->load('q',$query );
return $query;
}
 public function showIndexes(){
 if(!$this->setQuery('ol',$this->_db->showIndexes($this->makeT()) )) return false;
 $result=$this->_result;
 $indexes=new stdClass;
 if(empty($result)) return $indexes;
 foreach($result as $unique){
if(!empty($unique->Non_unique)){
 if($unique->Index_type=='FULLTEXT'){
$indexes->fulltext[$unique->Key_name][$unique->Seq_in_index]=$unique->Column_name;
continue;
 }
 $indexes->index[$unique->Key_name][$unique->Seq_in_index]=$unique->Column_name;
 continue;
}
if($unique->Key_name=='PRIMARY'){
 $indexes->pkey[$unique->Seq_in_index]=$unique->Column_name;
 continue;
}
$indexes->unique[$unique->Key_name][$unique->Seq_in_index]=$unique->Column_name;
 }
 return $indexes;
}
public function getType(){
return (isset($this->_infos->type)?$this->_infos->type : null );
}
public function getDomain(){
return $this->_infos->domain;
}
public function getDBId(){
return (isset($this->_infos->dbid)?$this->_infos->dbid : 0 );
}
public function getTableId(){
return (isset($this->_infos->dbtid)?$this->_infos->dbtid : 0 );}
public function getSQLConstraints($keyID){
$explodeA=explode('-',$keyID );
$tableID=$explodeA[0];
$pubPropertiesA=unserialize($explodeA[1] );
$model=WModel::get('library.constraints');
$model->remember('Const'.$keyID, true, 'Model_dataset_tables');
  $model->makeLJ('library.constraintsitems','ctid');
  $model->makeLJ('library.columns','dbcid','dbcid',1,2);
if(!empty($pubPropertiesA))$model->whereIn('name',$pubPropertiesA, 2 );
$model->select('type');
$model->select('ctid',1);
$model->select('name', 2);
$model->select('size', 2);
$model->orderBy('type','ASC');
$model->orderBy('ordering','ASC',1);
$model->whereE('dbtid',$tableID );
$model->setLimit( 100 );
$constraints=$model->load('ol');
$constraintsO=new stdClass;
$constraintsO->data=$constraints;
$constraintsO->id=$keyID;
return $constraintsO;
}
public function getConstraints($type='all',$pubPropertiesA=null){
$caching=WPref::load('PLIBRARY_NODE_CACHING');
$caching=($caching > 0 )?'cache' : 'static';
$tableID=$this->getTableId();
$keyID=$tableID.'-'.serialize($pubPropertiesA );
$constraintsO=WCache::getObject($keyID, 'Table',$caching, true, true, 'Constraints');
if(empty($constraintsO))$constraintsO=WCache::getObject($keyID, 'Table','static', true, true, 'Constraints');
$constraints=$constraintsO->data;
switch ($type){
  case'all':
return $constraints;
break;
  case'uk':
$myConstraints=array();
if(!empty($constraints)){
  foreach($constraints as $constraint){
if($constraint->type==1)$myConstraints[$constraint->ctid][$constraint->name]=$constraint->size;
  }  return $myConstraints;
}break;
  case'pk':
$myConstraints=array();
if(!empty($constraints)){
  foreach($constraints as $constraint){
if($constraint->type==3)$myConstraints[]=$constraint->name;
  }  return $myConstraints;
}break;
  case'ukpk':
$myConstraints=array();
if(!empty($constraints)){
  foreach($constraints as $constraint){
if($constraint->type==3 || $constraint->type==1)$myConstraints[$constraint->ctid][$constraint->name]=$constraint->size;
  }  return $myConstraints;
}break;
  case'ix':
$myConstraints=array();
if(!empty($constraints)){
  foreach($constraints as $constraint){
if($constraint->type==2)$myConstraints[$constraint->ctid][$constraint->name]=$constraint->size;
  }  return $myConstraints;
}break;
  default:
return $constraints;
break;
}
}
public function setIdentify($value=true){
$this->_identify=$value;
}
public function valueQuote($str,$dontAddQuote=false){
if(empty($this->_db)) return $this->cancelQuery();
if( method_exists($this->_db,'valueQuote')){
  return $this->_db->valueQuote($str, $dontAddQuote );
}
return "'" . $this->_db->escape($str). "'";
}
public function openBracket($num=1){
$object=new stdClass;
$object->open=$num;
$this->_groups[count($this->_whereValues)][]=$object;
}
public function closeBracket($num=1){
$object=new stdClass;
$object->close=$num;
$this->_groups[count($this->_whereValues)-1][]=$object;
}
public function operator($op='OR'){
$object=new stdClass;
$object->operator=strtoupper($op);
$list=array('AND','OR','NOR','XOR');
if(!in_array($object->operator, $list )){
$mess=WMessage::get();
$mess->codeE('You used the operator "'.$op.'". Please specify a valid operator from the list: '.implode(',',$list), array(), 'query');
$this->_queryError=true;
return false;
}
$tc=count($this->_whereValues);$this->_operators[$tc]=$object;
return true;
}
public function store(){
$status=true;
if(isset($this->wfiles)) unset($this->wfiles );
if($this->_forceInsert && !$this->_new)$this->_forceInsert=false;
$useMultipleLang=defined('PLIBRARY_NODE_MULTILANG')?PLIBRARY_NODE_MULTILANG : 0;
if(!$useMultipleLang && $this->getType()==20 && empty($this->lgid)){
$useMultipleLangENG=defined('PLIBRARY_NODE_MULTILANGENG')?PLIBRARY_NODE_MULTILANGENG : 1;
$this->lgid=($useMultipleLangENG?1: WApplication::userLanguage());
}
if(!$this->multiplePK()){  $pkey=$this->getPK();
  if(!empty($this->$pkey )  && !($this->_forceInsert || $this->_new)){
$this->whereE($pkey, $this->$pkey );
$this->setLimit(1);
$temp=$this->$pkey;
unset($this->$pkey );
$status=$this->_update();
$this->$pkey=$temp;
  }else{
$status=$this->insert();
  }
  if(!empty($pkey) && empty($this->$pkey))$this->$pkey='';
}else{
$goodPrimaryKeys=true;
foreach($this->getPKs() as $primK){
if(empty($this->$primK )){
$goodPrimaryKeys=false;
break;
}}
if($goodPrimaryKeys && !($this->_forceInsert || $this->_new)){
$this->setIgnore();
$status=$this->insert();
if($status){
$affectedRows=$this->affectedRows();
if(empty($affectedRows)){
$tempArray=array();
$numberOfPK=0;
foreach($this->getPKs() as $primK){
$this->whereE($primK, $this->$primK );
$tempArray[$primK]=$this->$primK;
$numberOfPK++;
}
if(count($this->getPublicProperties()) > $numberOfPK){
if(!empty($this->lgid) && $this->getType()=='20'){
$this->auto=2;
}
$this->_update();
}
}
}
}else{$status=$this->insert();
}
if($useMultipleLang && !empty($this->lgid) && $this->getType()=='20'){
$allLgids=array_diff( WTable::getAvailableLanguages(), array($this->lgid ));
if(!empty($allLgids)){
$modelCopyM=WModel::get($this->getModelID());
if(!empty($affectedRows) || !$goodPrimaryKeys || $this->_forceInsert || $this->_new){
$insertValues=array();
foreach($allLgids as $onelgid){
$insertedObject=new stdClass;
foreach($this->getPublicProperties() as $myProp){
$insertedObject->$myProp=$this->$myProp;
}$insertedObject->auto=-1;
$insertedObject->fromlgid=$this->lgid;
$insertedObject->lgid=$onelgid;
$insertValues[$onelgid]=$insertedObject;
}
$modelCopyM->setIgnore();
$modelCopyM->insertMany( array(), $insertValues );
}else{ $insertedObject=new stdClass;
foreach($this->getPublicProperties() as $myProp){
$insertedObject->$myProp=$this->$myProp;
}
unset($insertedObject->auto );
unset($insertedObject->fromlgid );
unset($insertedObject->lgid );
if(!empty($insertedObject)){
$mainPKs=array_diff($this->getPKs(), array('lgid'));
$errorQuery=false;
foreach($mainPKs as $mainPK){
if(!isset($this->$mainPK)){
$errorQuery=true;
$message=WMessage::get();
$message->codeE('One of the PK is missing to be able to update the other translations : '.$mainPK, array(), 0 );
break;
}else{
$modelCopyM->whereE($mainPK, $this->$mainPK );
unset($insertedObject->$mainPK );
}}
if(!$errorQuery){
$modelCopyM->whereE('fromlgid',$this->lgid );
$modelCopyM->whereE('auto','-1');
$modelCopyM->update($insertedObject );
}}}
}}
if(!empty($this->lgid) && $this->getType()=='20' && empty($this->_infos->export)){
$proertitesA=array();
$removeProA=array();
$goodPH=null;
foreach($this->getPKs() as $onePK){
if($onePK !='lgid')$goodPH=$onePK;
$removeProA[]=$onePK;
}
if(!empty($goodPH)){
$removeProA[]='auto';
foreach($this->getPublicProperties() as $myProp){
if( in_array($myProp, $removeProA )){
continue;
}$proertitesA[]=$myProp;
}
$populateM=WModel::get('translation.populate');
$populateM->makeLJ('library.columns','dbcid','dbcid');
$populateM->makeLJ('library.table','dbtid','dbtid', 1, 2 );
$populateM->whereE('eid',$this->$goodPH );
$populateM->whereE('dbtid',$this->_infos->dbtid, 2 );
$populateM->select('name', 1 );
$allIMacA=$populateM->load('ol',array('imac'));
$code=WLanguage::get($this->lgid, 'code');
$dictionaryM=WModel::get('translation.'.$code, 'object', null, false);
if(!empty($dictionaryM)){
foreach($allIMacA as $imac){
$map=$imac->name;
if(!isset($this->$map) || empty($this->$map)) continue;
$dictionaryM->setVal('auto', 5 );$dictionaryM->setVal('text',$this->$map );
$dictionaryM->whereE('imac',$imac->imac );
$dictionaryM->update();
}
}
}
}
}
return $status;
  }
public static function getAvailableLanguages(){
static $result=null;
if($result !==null) return $result;
$languageM=WTable::get('language_node','main_userdata','lgid');
if(!$languageM->tableExist() || ! $languageM->isReady())$languageM=WTable::get('joobi_languages','main_userdata','lgid');
if(empty($languageM)){
$message=WMessage::get();
$message->codeE('Model languages not found');
return $result;
}
$languageM->whereE('publish', 1 );
$languageM->setLimit( 500 );
$result=$languageM->load('lra',array('lgid'));
return $result;
}
public function showCreateTable($table='',$DB=null){
if(empty($table)){
if(!$this->tableExist()) return false;
$table=$this->makeT();
}else{
$table=$this->nameQuote($table );
}
if(!empty($DB))$table=$this->nameQuote($DB ). '.'.$table;
$this->setQuery('o','SHOW CREATE TABLE '.$table );
return $this->_result;
}
public function setPrint($print=true){
$this->_printOnly=$print;
}
public function getPublicProperties($child=false,$assoc=false,$onlyRealProperty=false){
$vars=array();
$ObjVar=get_object_vars($this );
foreach($ObjVar as $key=> $val){
$letter=substr($key, 0, 1 );
if($letter !='_'){
if($child){
if( strtoupper($letter)===$letter){
$vars[0][]=$key;
$vars[1][$key]=$val;
}}else{
if( strtolower($letter)===$letter){
$vars[0][]=$key;
$vars[1][$key]=$val;
}}
}}
if($onlyRealProperty){
if($assoc){
unset($vars[1]['u']);
unset($vars[1]['x']);
unset($vars[1]['c']);
unset($vars[1]['wfiles']);
}else{
$vars[0]=array_flip($vars[0] );
unset($vars['u']);
unset($vars['x']);
unset($vars['c']);
unset($vars[0]['wfiles']);
$vars[0]=array_flip($vars[0] );
}}
return $vars[$assoc?1 : 0];
}
public function nameQuote($column){
return $this->_db->nameQuote($column );
}
private function _makeC($column,$as=0,$special=0){
    if( is_object($column)){
  return $this->_interpCalcul($column );
  }
if($column=='*'){
$columnModif=$column;
}else{
if(empty($this->_db)) return $this->cancelQuery();
$columnModif=$this->_db->nameQuote($column );
}
if($this->_identify && $as !==null){
$asLetter=$this->_convertAs($as ). '.';
$columnModif=' '.$asLetter.$columnModif.' ';
}
switch( substr( strtolower($special), 0, 10 )){
  case'0':
return $columnModif;
  case'-1':
  case'min':
return ' MIN('.$columnModif.')';
  case'1':
  case'max':
return ' MAX('.$columnModif.')';
  case'2':
  case'count':
return ' COUNT('.$columnModif.')';
  case'4':
  case'avg':
return ' AVG('.$columnModif.')';
  case'5':
  case'sum':
return ' SUM('.$columnModif.')';
  case'6':
  case'ip':
  return ' INET_NTOA('.$columnModif.')';
  case'9':
  case'val':
  return $column;
  case'fmunixtime':
return ' FROM_UNIXTIME('.$columnModif.', \'' .substr($special, 10).'\')';
  case'fmunixweek':
return ' WEEK(FROM_UNIXTIME('.$columnModif.', \'' .substr($special, 10).'\'))';  case 'dateformat':
  return ' DATE_FORMAT('.$columnModif.', \'' .substr($special, 10).'\')';  case 'dtfrmtweek':
  return ' WEEK(DATE_FORMAT('.$columnModif.', \'' .substr($special, 10).'\'))';
  default :
  return $columnModif;
}
}
private function _makeQuery(){
$fromTag=' FROM '.$this->makeT();
$grouptag='';
$ordertag='';
$limittag='';
$leftJoin='';
$selectTag=(isset($this->_distinct))?' SELECT DISTINCT ' : ' SELECT ';
if(!empty($this->_selectA)){
$select=array();
  if($this->_indexResult){
  $this->select($this->_indexResult );
$this->_db->indexResult($this->_indexResult );
  }else{  $this->_db->indexResult(false);
  }
  $count=0;
  foreach($this->_selectA as $selectKey=> $oneSelect){
  $count++;
$as=(isset($oneSelect->alias)?' AS '. $this->_db->valueQuote($oneSelect->alias ). '' : '');
if(!empty($oneSelect->special)){
$makeC=$this->_makeC($oneSelect->map, $oneSelect->as1, $oneSelect->special ). $as;
if(empty($makeC)) continue;
$select[]=$makeC;
}else{
$makeC=$this->_makeC($oneSelect->map, $oneSelect->as1 ). $as;
if(empty($makeC)) continue;
$select[]=$makeC;
}
}
$selectag=(count($select )?$selectTag. implode(',',$select ) : '');
}else{
$selectag=$selectTag.'* ';
if($this->_indexResult)$this->_db->indexResult($this->_indexResult );
}
if(!empty($this->_groupby)){
  $myGrouBy=array();
  foreach($this->_groupby as $grouby){
$makeC=$this->_makeC($grouby->champ, $grouby->asi );
if(empty($makeC)) continue;
$myGrouBy[]=$makeC;
  }  if(!empty($myGrouBy))$grouptag=' GROUP BY '. trim( implode(',',$myGrouBy), ',');
}
  if(!empty($this->_orderby)){
$orderby=array();
  foreach($this->_orderby as $myOrder){
  if( in_array( strtoupper($myOrder->direction ), array('ASC','DESC'))){
  $checkedOrderBy=$myOrder->direction;
  }elseif($myOrder->direction=='RAND'){
  $orderby[]='RAND()';
  continue;
  }else{
  $checkedOrderBy='ASC';
  }
  if(!empty($myOrder->useAlias)){
  if(!empty($myOrder->alias))$orderby[]=$myOrder->alias .' '.$checkedOrderBy;
else {
$orderby[]=$this->valueQuote($myOrder->champ, true).'_'. $this->getSIDFromAs($myOrder->asi).' '.$checkedOrderBy ;}}else{
$makeC=$this->_makeC($myOrder->champ, $myOrder->asi ).' '. $checkedOrderBy;
if(empty($makeC)) continue;
$orderby[]=$makeC;
}
  }
  $ordertag=( count($orderby )?' ORDER BY '. implode(',',$orderby ) : '');
}
$start=($this->_limitl)?$this->_limitl.',' : '';
$limittag=($this->_limith)?' LIMIT '.$start . $this->_limith : '';
 if($this->_identify){
  if($this->_leftjoin)$leftJoin=$this->_makeLeftJoin();  else {
$leftJoin=$this->_addExtraTable();
  }}
$tempMakeWhere=$this->_makeWhere();
$this->_query=$selectag . $fromTag . $leftJoin . $tempMakeWhere . $grouptag . $ordertag . $limittag;
unset($this->_distinct );
}
private function _makeLeftJoin(){
if(empty($this->_leftjoin)) return '';
$whereOn=array();
foreach($this->_leftjoin as $ljkey=> $ljvalue){
$modelLeftJoin=WTable::get($ljvalue->tablename, $ljvalue->dbname );
$modelLeftJoin->_identify=true;
$as1=$ljvalue->as1;
$as2=(!empty($ljvalue->as2)?$ljvalue->as2 : $this->getAs($ljvalue->tablename));
$this->_leftjoin[$ljkey]->as2=$as2;
if(empty($ljvalue->cond2))$ljvalue->cond2=$ljvalue->cond1;
$leftjoin[]=' LEFT JOIN '.$modelLeftJoin->makeT($as2).' ON '.$this->_makeC($ljvalue->cond1, $as1 ). (!empty($ljvalue->Vopr1)?$ljvalue->Vopr1 : '='). $this->_makeC($ljvalue->cond2, $as2 );
if(!empty($this->_whereOnValues[$as2])){
foreach($this->_whereOnValues[$as2] as $whereOn){
$argu2=isset($whereOn->asi2)?$this->_makeC($whereOn->value , $whereOn->asi2 ) : $this->_db->valueQuote($whereOn->value );
$leftjoin[]=' AND '.$this->_makeC($whereOn->champ , $whereOn->asi1 ). $whereOn->cond . $argu2;
}}
}
return ( count($leftjoin )?' '. implode(' ' , $leftjoin ) : '');
}
private function _makeWhere(){
$whereA=array();
$first=true;
if(!empty($this->_whereValues)){
$opwhere='';
foreach($this->_whereValues as $numWhere=> $myWhere){
if(!$first){
if(!empty($this->_operators[$numWhere] )){
$opwhere .=' '.$this->_operators[$numWhere]->operator.' ';
}else{
switch($myWhere->operator){
  case'1':
$opwhere .=' OR ';
break;
  case '0':
  default:
$opwhere .=' AND ';
break;
}}}
if(isset($this->_groups[$numWhere])){
foreach($this->_groups[$numWhere] as $group){
if(!empty($group->open)){
for($index2=0; $index2 < $group->open; $index2++)$opwhere .='(';
}}}
if($myWhere->bkbefore>0 ) for($index2=0; $index2 < $myWhere->bkbefore; $index2++)$opwhere .='(';
switch($myWhere->type){
case 'isnull':
$opwhere .=$this->_makeWhereIsNull($myWhere );
  break;
case 'in':
  $whereINValues=$this->_makeWhereIn($myWhere );
  if($whereINValues===false) return '';
  $opwhere .=$whereINValues;
  break;
    case 'ip':
$myWhere->value='INET_ATON('.$this->valueQuote($myWhere->value ).')';
  $opwhere .=$this->_makeWhereDefault($myWhere, true);
break;
  default :
  $opwhere .=$this->_makeWhereDefault($myWhere);
break;
}
if($myWhere->bkafter>0 ) for($index2=0; $index2 < $myWhere->bkafter; $index2++)$opwhere .=')';
if(isset($this->_groups[$numWhere])){
foreach($this->_groups[$numWhere] as $group){
if(!empty($group->close)){
for($index2=0; $index2 < $group->close; $index2++)$opwhere .=')';
}}}
$first=false;
}$whereA[]=$opwhere;
}
if(!empty($this->_seachedWords)){
$termsSeachedIn=array();
$termsSeachedInAddedA=array();
foreach($this->_seachedWords as $oneTerm){
if(!empty($oneTerm->sign)){
$searchObj=new stdClass;
if(isset($oneTerm->opr))$searchObj->typeLike=$oneTerm->opr;
else $searchObj->typeLike=true;
$searchObj->map=$oneTerm->map;
$searchObj->asi=$oneTerm->asi;
$myseachVal=$this->_db->valueQuote( trim($oneTerm->value[0]));
$searchObj->searched=$this->_db->escape( trim($oneTerm->sign)). $myseachVal;
$termsSeachedInAddedA[]=$searchObj;
}else{
foreach($oneTerm->value as $oneValue){
if(empty($oneValue)) continue;
if( substr($oneValue, 0, 1)=='-'){
  $myLike=' NOT LIKE ' ;
  $oneValue=substr($oneValue, 1 );
  $typeLIKE=(isset($oneTerm->opr)?$oneTerm->opr : false);
}else{
  $myLike=' LIKE ' ;
  $typeLIKE=true;
  $typeLIKE=(isset($oneTerm->opr)?$oneTerm->opr : true);
}if($oneTerm->noEsc){
$myseachVal=trim($oneValue);
}else{
$myseachVal=$this->_db->escape( trim($oneValue));
}$searchTermsKey=( PLIBRARY_NODE_LWRSEARCH ) ?$myLike.' \'%'.strtolower($myseachVal ). '%\'' : $myLike.' \'%'.$myseachVal.'%\'';
$searchObj=new stdClass;
$searchObj->typeLike=$typeLIKE;
$searchObj->map=$oneTerm->map;
$searchObj->asi=$oneTerm->asi;
$searchObj->searched=$searchTermsKey;
$termsSeachedIn[$searchTermsKey][]=$searchObj;
}}
}
if(!empty($termsSeachedIn) || !empty($termsSeachedInAddedA)){
$searchTempAddedA=array();
if(!empty($termsSeachedInAddedA )){
foreach($termsSeachedInAddedA as $addObjsA){
$makeC=$this->_makeC($addObjsA->map, $addObjsA->asi ). $addObjsA->searched;
if(empty($makeC)) continue;
$searchTempAddedA[]=$makeC;
}}
if(!empty($termsSeachedIn)){
foreach($termsSeachedIn as $ssTerm=> $ssObjsA){
$searchTemp=array();
foreach($ssObjsA as $ssObj){
$makeC=$this->_makeC($ssObj->map, $ssObj->asi );
if(empty($makeC)) continue;
if( PLIBRARY_NODE_LWRSEARCH)$searchTemp[]=' LOWER('.$makeC.') '.$ssObj->searched;
else $searchTemp[]=$makeC . $ssObj->searched;
}
if(!empty($searchTempAddedA ))$searchTemp=array_merge($searchTemp, $searchTempAddedA );
$operation=($ssObjsA[0]->typeLike)?' OR ' : ' AND ';
$subWhere[]=' ('.(count($searchTemp )?' '. implode($operation, $searchTemp ) : ''). ')';
}}else{
$operation=(!empty($this->_seachedWords[0]->opr)?' OR ' : ' AND ');
$subWhere[]=' ('.(count($searchTempAddedA )?' '. implode($operation, $searchTempAddedA ) : ''). ')';
}
$whereA[]=(!empty($whereA)?' AND ' : ''). (count($subWhere )?' '. implode(' AND ',$subWhere ) : '');
}
}
$resultReturned=( count($whereA)>0?' WHERE '. implode(' ',$whereA ) : '');
return $resultReturned;
}
private function _makeWhereDefault($myWhere,$noQuotes=false){
  if(isset($myWhere->asi2)){  if($this->_identify){
  $asLetter=$this->_convertAs($myWhere->asi2 ).'.';
  $value=' '. $asLetter . (($noQuotes )?$myWhere->value : $this->_db->nameQuote($myWhere->value )).' ';
  }else{
  $value=' '. (($noQuotes )?$myWhere->value : $this->_db->nameQuote($myWhere->value )).' ';
  }
}else{
$value=($noQuotes )?$myWhere->value : $this->valueQuote($myWhere->value );
}
$makeC=$this->_makeC($myWhere->champ, $myWhere->asi1 );
if(empty($makeC)) return '';
return $makeC . $myWhere->cond. ' '.$value;
}
private function _makeWhereIn($myWhere){
if(empty($myWhere->filter )){
$whereIN='';
foreach($myWhere->values as $val){
if(!empty($whereIN))$whereIN .=',';
$whereIN .=$this->valueQuote($val ) ;
}if(empty($whereIN)){
return false;
}}else{$whereIN=(is_array($myWhere->values))?$myWhere->values[0] : $myWhere->values;
}
$makeC=$this->_makeC($myWhere->champ, $myWhere->asi );
if(empty($makeC)) return '';
$column=' '.$makeC.' ';
$notIn=($myWhere->notIn )?' NOT IN (' : ' IN (' ;
return $column.$notIn. $whereIN .') ';
}
private function _makeWhereIsNull($myWhere){
$makeC=$this->_makeC($myWhere->champ, $myWhere->asi );
if(empty($makeC)) return '';
return $makeC . (($myWhere->condition )?' IS NULL ' : ' IS NOT NULL ');
}
private function _interpCalcul($calcul){
  $listop=array('+','-','*','%','/');
$value1=$this->_calculValueToString($calcul->value1, $calcul->as1 );
$value2=$this->_calculValueToString($calcul->value2, $calcul->as2 );
$calculString='(';
if( in_array($calcul->operator, $listop )){
$calculString .=$value1.' '.$calcul->operator.' '.$value2;
}else{
$message=WMessage::get();
$message->codeE('The operator is not found, please choose one in the following list : '.implode(',',$listop),array(),'query');
return '';
}
return $calculString .=')';
}
private function _calculValueToString($value='',$as=null){
    if( is_object($value)) return $this->_interpCalcul($value );
if(isset($as)) return $this->_makeC($value, $as );
return $this->_db->valueQuote($value );
}
private function _update(){
$update=array();
if(!empty($this->_update)){
foreach($this->_update as $updateObj){
  $as1=(isset($updateObj->champAS))? $updateObj->champAS: 0;
  $column=$this->_makeC($updateObj->champ, $as1 );
  if(empty($column)) continue;
  if(isset($updateObj->valueAS)){
  $makeC=$this->_makeC($updateObj->value, $updateObj->valueAS );
  if(empty($makeC)) continue;
$update[]=$column .'='.$makeC;
  }else{
  if( is_object($updateObj->value)){
$updatedValue=$this->_interpCalcul($updateObj->value );
if(empty($updatedValue)){
$this->codeE('the calculation returned zero!');
continue;
}}else{
if(!empty($updateObj->special) && 'ip'==$updateObj->special){
$updatedValue=$this->valueQuote($updateObj->value);
$updatedValue=$this->_db->insertInetAton($this->valueQuote($updateObj->value ));
}else{
$updatedValue=$this->valueQuote($updateObj->value);
}
}
$update[]=$column.'='.(isset($updateObj->operation)?$column.' '.$updateObj->operation.' ': ''). $updatedValue;
  }}
}else{
  foreach(get_object_vars($this ) as $k=> $v){
  if(is_array($v) or is_object($v) or $k[0]=='_' or  (strtoupper($k[0])===$k[0])) continue;
if($k=='ip'){
if(!empty($v)){
$makeC=$this->_makeC($k );
if(empty($makeC)) continue;
$update[]=$makeC .'='. $this->_db->insertInetAton($this->valueQuote($v ));
}else{
continue;
}}else{
$makeC=$this->_makeC($k );
if(empty($makeC)) continue;
$update[]=$makeC .'='.$this->valueQuote($v);
}}
}
if(!count($update )){
$this->_queryError=true;
return false;
}
$updatetag=' SET '.implode(',',$update );
$this->_query=' UPDATE ';
if(!empty($this->_ignore))$this->_query .='IGNORE ';
$this->_query .=$this->makeT();
if($this->_identify){
if($this->_leftjoin)$this->_query .=$this->_makeLeftJoin();
else $this->_query .=$this->_addExtraTable();
}
$this->_query .=$updatetag;
$start=($this->_limitl?$this->_limitl.',' : '');
$limittag=($this->_limith?' LIMIT '.$start . $this->_limith : '');
$this->_query .=$this->_makeWhere(). $limittag;
$whereMemory=$this->_whereValues;
$status=$this->setQuery('',$this->_query );
if($status){
if(!empty($this->_infos->cachedata)){
$pkey=$this->getPK();
if(!empty($this->$pkey )){
$id=$this->$pkey;
}else{
if(!empty($whereMemory)){
foreach($whereMemory as $w){
if($pkey==$w->champ){
if(empty($w->value) || ! is_string($w->value)){
continue;
}$id=$w->value;
}elseif('namekey'==$w->champ){
if(empty($w->value) || ! is_string($w->value)){
continue;
}$namekey=$w->value;
}}}}
if(!empty($id ) && is_numeric($id)){
$cache=WCache::get();
$nameTrans=WModel::modelExist($this->_infos->namekey.'trans');
if($nameTrans){
$lgid=WUser::get('lgid');
$key='lg-'.$id.'-'.$lgid;
}else{
$key=$id;
}
if(!empty($namekey)){
$usedNamekey=$namekey;
}elseif(!empty($this->namekey))  {
$usedNamekey=$this->namekey;
}else{
if(!empty($this->_infos->sid))$usedNamekey=WModel::getElementData($this->_infos->sid, $id, 'namekey');
}
$tableID=(empty($this->_infos->dbtid)?$this->_infos->tablename : $this->_infos->dbtid );
$rememberID='md-'.$tableID.'-id-'.$key;
$cache->resetCache('Model_'.$this->_infos->tablename, $rememberID );
if(!empty($usedNamekey)){
if($nameTrans){
$lgid=WUser::get('lgid');
$key='lg-'.$usedNamekey.'-'.$lgid;
}else{
$key=$usedNamekey;
}$tableID=(empty($this->_infos->dbtid)?$this->_infos->tablename : $this->_infos->dbtid );
$rememberID='md-'.$tableID.'-id-'.$key;
$cache->resetCache('Model_'.$this->_infos->tablename, $rememberID );
}
}
}
}
return $status;
  }
private function _addExtraTable(){
$fromTag='';
if(!empty($this->_addTable)){
foreach($this->_addTable as $table){
$myTable=WTable::get($table->tablename, $table->dbname );
$myTable->setIdentify();
$fromTag .=','.$myTable->makeT($table->as );
}}
return $fromTag;
}
private function _convertAs($as){
if(!is_numeric($as)) return strtoupper($as);
else return chr($as+65);
}
}