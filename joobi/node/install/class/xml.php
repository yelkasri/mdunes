<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
WLoadFile( 'library.class.parser');
class Install_Xml_class extends Library_Parser_class {
	var $columnsInfo = array();
	var $foreignsInfo = array();
	var $constraintsInfo = array();
	var $parents = array();
	var $stack = array();
	var $showMessage = false;
	var $test = false;
	var $mode = false;
	var $number= 0;
	var $queries = array();
	var $toInsert = array();
	var $version = 0;
	var $install = false;
	var $currentVersion = "1";
	function loadFile($tfile){
		$parent=null;
		return $this->importData(parent::loadFile($tfile),$parent);
	}
	function setMode($mode=true){
		$this->mode = $mode;
	}
	function setTest($mode=true){
		$this->test = $mode;
	}
	function setParent($model,$value,$field=0) {
		$modelObject = WModel::get( $model );
		if ( !empty($modelObject) ){
			$this->parents[$modelObject->getTableId()][$field]=$value;
			return true;
		}
		return false;
	}
	function showMessage($bool=true){
		$this->showMessage = $bool;
	}
	function parse($data){
		$parent=null;
		return $this->importData(parent::parse($data),$parent);
	}
	function importData(&$data){
		if (empty($data)){
			return false;
		}
		$parent = null;
		if (empty($data[0]['children'])){
			if ($this->showMessage){
				$TAG = $data[0]['nodename'];
WMessage::log( 'Warning: No children found for the main tag '.$TAG, 'install-xml' );
			}
			return false;
		}
		if (isset($data[0]['attributes']['version'])){
		 $this->version = $data[0]['attributes']['version'];
		 if (version_compare($this->version,$this->currentVersion,'>')){
		 	if ($this->showMessage){
					$tag = $data[0]['nodename'];
					$version = $this->version;
					$currentVersion = $this->currentVersion;
WMessage::log( 'Warning: The current parser ( version '.$currentVersion.' )	cannot handle the xml data because it was written for a newer version ( '.$version.' ) of the parser', 'install-xml' );
		 	}
		 	return false;
		 }
		} else {
			if ($this->showMessage){
					$mess = WMessage::get();
					$tag = $data[0]['nodename'];
					$version = $this->currentVersion;
WMessage::log( 'Warning: The version attribute is missing for the main tag '.$tag.'. The current version of the parser is '.$version, 'install-xml' );
			}
			return false;
		}
		$this->stack[]=$data[0]['nodename'];
		foreach($data[0]['children'] as $element){
			$this->stack[]=$element['nodename'];
			$this->_importChild($element,$parent);
			array_pop($this->stack);
		}
		array_pop($this->stack);
		if (!empty($this->queries)){
			$queries = '<br/>'.implode('<br/><br/>',$this->queries);
WMessage::log( 'Notice:  '.$queries, 'install-xml' );
		}
		return true;
	}
	function _importChild(&$element,&$parent){
		if (empty($element['children'])){
			if ($this->showMessage){
				$PATH = implode('->',$this->stack);
WMessage::log( 'Warning: The tag '.$PATH.' does not have any children. The tag is skipped.', 'install-xml' );
			}
			return true;
		}
		$user = false;
		if (isset($element['attributes']['user'])){
			$user=true;
		}
		$modelName = $element['nodename'];
		$status = $this->_getModelName($modelName,$user,$parent);
		$model = WModel::get( $modelName );
		if (!$model){
			if ($this->showMessage){
				$PATH = implode('->',$this->stack);
WMessage::log( 'Warning:  The model '.$modelName.' for the tag '.$PATH.' could not be found in the database. The tag is skipped.', 'install-xml' );
			}
			return true;
		}
		if (isset($element['attributes']['version'])){
		 $model->_modelVersion = $element['attributes']['version'];
		}
		$dbtid = $model->getTableId();
		$model->_usedFK = array();
		$model->_nodeName = $element['nodename'];
		$model->_number = 0;
		$this->_loadColumns($dbtid);
		$this->_loadForeigns($dbtid);
		$this->_addChildren($element['children'],$model,$dbtid);
		$this->_checkConstraints($model,$dbtid,$modelName);
		$this->_checkForeigns($model,$dbtid);
		if (!is_null($parent)){
			$parent->_childNodes[]=$model;
			if (!$status){
				$parent->_forwardPK[]=count($parent->_childNodes)-1;
			}
		} else {
			$this->process($model,$modelName);
		}
		return true;
	}
	function process(&$model,$modelName) {
		$this->_converVirtualValueFK($model);
		if ($this->test){
			$model->setPrint();
		}elseif (!$this->mode){
			$model->returnId();
		}
		$model->_xmlProcess = true;
		if (!$this->showMessage){
			$model->_noCheckMessage = true;
		}
		$status=true;
		if (!isset($model->_skipProcess) || !$model->_skipProcess){
			if ($this->showMessage){
			}
			if ($this->mode){
				$model->_noCoreCheck=true;
				$status = $model->delete();
			} else {
				$status = $model->save();
			}
			if ($this->test ){
				$this->number++;
				$model->_number = $this->number;
				$txt = $this->number.' : ['.implode('->',$this->stack).']<br/>'.$status;
				$this->queries[]=$txt;
				$status=true;
			}
		}
		if ( $status && !empty($model->_childNodes) ) {
			$dbtid = $model->getTableId();
			$pkey = $model->getPK();
			if (empty($model->$pkey)){
				if ($this->test){
					$model->$pkey = '@'.$pkey.'_'.$this->number;
				} else {
					if ($this->showMessage){
						$PATH = implode('->',$this->stack);
WMessage::log( 'Warning: The tag '.$PATH.' seems to have been successfully processed. However, we couldn\'t get the resulting ID for it. Thus, we won\'t be able to process its children tags.', 'install-xml' );
					}
					return false;
				}
			}
			foreach($model->_childNodes as $key => $node){
				$ref_dbtid = $node->getTableId();
				if (isset($this->foreigns[$ref_dbtid][$dbtid])){
					$foreign = $this->foreignsInfo[$ref_dbtid][$this->foreigns[$ref_dbtid][$dbtid][0]];
					$child_fkey=$foreign->columnName;
				} else {
					$child_fkey=$pkey;
				}
				if (!empty($node->_forwardPK)){
					if (empty($model->_forwardPK)){
						$node->_forwardFK = $child_fkey;
					} else {
						$node->_forwardFK = $model->_forwardFK;
					}
				}
				$node->$child_fkey=$model->$pkey;
				if (!empty($model->_forwardPK) && in_array($key,$model->_forwardPK)){
					$fkey = $model->_forwardFK;
					$node->$fkey=$model->$fkey;
				}
				$this->stack[]=$node->_nodeName;
				if ( !$this->process( $node, $node->getModelNamekey() ) ) {	
					$status = false;
				}
				array_pop($this->stack);
			}
		}
		return $status;
	}
	function _converVirtualValueFK(&$model){
		$properties = get_object_vars($model);
		foreach($properties as $key => $val){
			if ($key[0]!='_' && is_object($val)){
				if ($this->test){
					$model->$key = '@'.$val->getPK().'_'.$val->_number;
				} else {
					$name = $val->getPK();
					$model->$key = $val->$name;
				}
			}
		}
	}
	function _checkConstraints(&$model,$dbtid,$modelName){
		if (!isset($this->constraintsInfo[$dbtid])){
			$this->constraintsInfo[$dbtid] = $model->getConstraints('uk');
		}
		if (empty($this->constraintsInfo[$dbtid])){
			return true;
		}
		foreach($this->constraintsInfo[$dbtid] as $constraints){
			$model2 = WModel::get( $modelName );
			$columns = array();
			foreach($constraints as $contraint=>$uselessValue){
				if (isset($model->$contraint)){
					$model2->whereE($contraint,$model->$contraint);
				} else {
					continue 2;
				}
			}
			$pkeys = $model2->getPKs();
			$result = $model2->load('o',$pkeys);
			if (!empty($result)){
				foreach($pkeys as $pkey){
					if ($this->mode){
						$model->whereE($pkey,$result->$pkey);
					}
					$model->$pkey = $result->$pkey;
					$constraints[$pkey]='';
				}
				$this->_needProcess($model,$constraints,$dbtid);
				return true;
			} else {
				$UKcolumns = array_keys($constraints);
				if (count($UKcolumns)==1){
					$found = $UKcolumns[0];
					$this->toInsert[$dbtid][$found][$model->$found] =& $model;
					if ($this->mode){
						$model->whereE($found,$model->$found);
						return true;
					}
				}
			}
		}
		return true;
	}
	function _needProcess(&$model,&$constraints,$dbtid){
		$found = false;
		foreach(array_keys($this->columns[$dbtid]) as $column){
			if (isset($model->$column)){
				$isAConstraint=false;
				foreach($constraints as $contraint=>$uselessValue){
					if ($column==$contraint){
						$isAConstraint=true;
						break;
					}
				}
				if (!$isAConstraint){
					$found=true;
				}
			}
		}
		if (!$found){
			$model->_skipProcess=true;
		}
	}
	function _checkForeigns(&$model,$dbtid){
		if (empty($this->foreignsInfo[$dbtid])){
			return true;
		}
		foreach($this->foreignsInfo[$dbtid] as $foreign){
			$map = $foreign->columnName;
			if (!isset($model->$map)){
				$key=false;
				if (isset($this->parents[$foreign->ref_dbtid][$map])){
					$key = $map;
				}elseif (isset($this->parents[$foreign->ref_dbtid][0])){
					$key = 0;
				}
				if ($key!==false){
					$model->$map = $this->parents[$foreign->ref_dbtid][$key];
				}
			}
		}
		return true;
	}
	function _getModelName(&$modelName,$user,&$parent){
		if (!empty($parent->_nodeName) && $parent->_nodeName==$modelName){
			$modelName = $parent->getModelNamekey();
			return false;
		}
		if (in_array($modelName,array('form','menu','listing','value','language'))){
			$modelName.='s';
		}
		if ($modelName=='view'){
			if ($user){
				$modelName='views';
			} else {
				$modelName='library.view';
			}
		}elseif (in_array($modelName,array('picklist','view.picklist','picklist.values','model','modeltrans'))){
			$modelName = 'library.' . str_replace('.','',$modelName);
		}elseif ($modelName=='trans'){
			$modelName = $parent->getModelNamekey().$modelName;
		}elseif (in_array($modelName,array('forms','menus','listings'))){
			if (is_null($parent)){
				if ($user){
					$parentName='views';
				} else {
					$parentName='library.view';
				}
			} else {
				$parentName = $parent->getModelNamekey();
			}
			if ($parentName=='views'){
				$modelName = $parentName.'.'.$modelName;
			}elseif ($parentName=='library.view'){
				$modelName = $parentName.$modelName;
			}
		}elseif ($modelName=='values' && $parent->getModelNamekey()=='library.picklist'){
			$modelName ='library.picklistvalues';
		}
		return true;
	}
	function _addChildren(&$elements,&$model,$dbtid){
		foreach($elements as $child){
			$name = $child['nodename'];
			$value = '';
			$children = array();
			if (isset($child['nodevalue'])){
				$value = $child['nodevalue'];
			}elseif (isset($child['children'])){
				$children = $child['children'];
			}
			$this->stack[]=$name;
			if (!empty($children)){
				$isModel=false;
				if (!isset($this->columns[$dbtid][$name])){
					$isModel = true;
				}elseif (empty($value)){
					$user = false;
					if (isset($child['attributes']['user'])){
						$user = true;
					}
					$modelName = $name;
					$this->_getModelName($modelName,$user,$model);
					$submodel = WModel::get( $modelName,'objectfile', null, false );
					if ($submodel){
						$isModel = true;
					}
				}
				if ($isModel){
					$this->_importChild($child,$model);
					array_pop($this->stack);
					continue;
				}
			}
			if (isset($child['attributes']['column'])){
				$user = false;
				if (isset($child['attributes']['user'])){
					$user = true;
				}
				$column = $child['attributes']['column'];
				if ($this->showMessage && !isset($this->columns[$dbtid][$column])){
					$PATH = implode('->',$this->stack);
					$MODEL = $model->getModelNamekey();
WMessage::log( 'Warning:  In the foreign tag '.$PATH . ' you specified to use the column '.$column.' which is not in the list of columns of the the model '.$MODEL.'. It is highly possible that the query using this tag will fail.', 'install-xml' );
				}
				$this->_handleFK($model,$dbtid,$user,$name,$column,$value);
			}elseif (!empty($child['attributes']['type'])){
				$model->$name = $this->_convertType($child['attributes']['type'],$value);
			} else {
				$this->_handleNormalTag($dbtid,$name,$child,$value,$children,$model);
			}
			array_pop($this->stack);
		}
	}
	function _handleFK(&$model,$dbtid,$user,$modelName,$column,$value){
		$this->_getModelName($modelName,$user,$model);
		$foreignModel = WModel::get($modelName,'objectfile',null,false);
		if (is_null($foreignModel)){
		} else {
			$ref_dbtid = $foreignModel->getTableId();
			$usable = false;
			if (isset($this->foreigns[$dbtid][$ref_dbtid])&&count($this->foreigns[$dbtid][$ref_dbtid])){
				$found=false;
				foreach($this->foreigns[$dbtid][$ref_dbtid] as $fkKey){
					$foreign = $this->foreignsInfo[$dbtid][$fkKey];
					$chosenFk = $dbtid.'_'.$fkKey;
					if ($foreign->columnName==$column){
						$found=true;
						if (in_array($chosenFk,$model->_usedFK)){
							if ($this->showMessage){
								$PATH = implode('->',$this->stack);
								$PARENT = $model->getModelNamekey();
								$FOREIGN = $column.'->'.$this->foreignsInfo[$dbtid][$fkKey]->refColumnName;
WMessage::log( 'Warning:  In order to fill the foreign tag . '.$PATH . ' we searched for a foreign key between '.$PARENT.' and '.$modelName.'. We found the foreign key '.$FOREIGN.'.'.
									 'The problem is that this foreign key was already used for a previous tag of the same model so this tag will be skipped.', 'install-xml' );
							}
						} else {
							$model->_usedFK[]=$chosenFk;
							$usable=true;
						}
						break;
					}
				}
				if (!$found&&$this->showMessage){
					$PATH = implode('->',$this->stack);
					$PARENT = $model->getModelNamekey();
WMessage::log( 'Warning: In order to fill the foreign tag . '.$PATH . ' we searched for a foreign key between '.$PARENT.' and '.$modelName.'. We couldn\'t find it for the column '.$column.' so this tag will be skipped.', 'install-xml' );
				}
			}
			if ($usable){
				if (is_numeric($value)){
					$model->$column = $value;
				} else {
					$model->$column =& $this->_convertForeign($modelName,$value);
				}
			}
		}
	}
	function _handleNormalTag($dbtid,$name,&$child,$value,&$children,&$model){
		if (!isset($this->columns[$dbtid][$name])){
			$parent = null;
			$user = false;
			if (isset($child['attributes']['user'])){
				$user = true;
			}
			$modelName = $name;
			$this->_getModelName($modelName,$user,$parent);
			$foreignModel = WModel::get($modelName,'objectfile',null,false);
			if (is_null($foreignModel)){
				if ($this->showMessage && !in_array($name,array_keys($this->columns[$dbtid])) && ($name!='enablewidget')){
					$PATH = implode('->',$this->stack);
					$MODEL = $model->getModelNamekey();
WMessage::log( 'Warning:  We couldn\'t find the model '.$modelName.' in the database for the tag	'.$PATH . ' and the tag is not in the list of columns of the the model '.$MODEL.'. It is highly possible that the query using this tag will fail.', 'install-xml' );
				}
			} else {
				$ref_dbtid = $foreignModel->getTableId();
				if (isset($this->foreigns[$dbtid][$ref_dbtid])){
					$convertKey = false;
					$messageOk=false;
					if (count($this->foreigns[$dbtid][$ref_dbtid])>1){
						$maps = array();
						foreach($this->foreigns[$dbtid][$ref_dbtid] as $fkKey){
							if (!in_array($dbtid.'_'.$fkKey,$model->_usedFK)){
								$foreign = $this->foreignsInfo[$dbtid][$fkKey];
								$maps[$foreign->columnName]=$fkKey;
							}
						}
						if (empty($maps)){
							if ($this->showMessage){
								$PATH = implode('->',$this->stack);
								$PARENT = $model->getModelNamekey();
								$FOREIGN = $this->foreignsInfo[$dbtid][$fkKey]->columnName.'->'.$this->foreignsInfo[$dbtid][$fkKey]->refColumnName;
								$msgText = 'In order to fill the foreign tag . '.$PATH . ' we searched for a foreign key between '.$PARENT.' and '.$modelName.'. However we found several of them, so we chose the foreign key '.$FOREIGN.' based on the ordering of the foreign keys of the models.';
								$msgText .=' The problem is that this foreign key was already used for a previous tag of the same model so this tag will be skipped. Remember that you can use the attribute "column" in order to specify directly the column of the model to set';
WMessage::log( 'Warning:  '.$msgText, 'install-xml' );
								$messageOk = true;
							}
						} else {
							$fkKey = reset($maps);
							$model->_usedFK[] = $dbtid.'_'.$fkKey;
							if ($this->showMessage){
								$PATH = implode('->',$this->stack);
								$PARENT = $model->getModelNamekey();
								$FOREIGN = $this->foreignsInfo[$dbtid][$fkKey]->columnName.'->'.$this->foreignsInfo[$dbtid][$fkKey]->refColumnName;
								$msgText = 'In order to fill the foreign tag . '.$PATH . ' we searched for a foreign key between '.$PARENT.' and '.$modelName.'.';
								$msgText .= 'However we found several of them. We will choose the foreign key '.$FOREIGN.' based on the ordering of the foreign keys of the models. Remember that you can use the attribute "column" in order to specify directly the column of the model to set';
WMessage::log( 'Warning:  '.$msgText, 'install-xml' );
								$messageOk = true;
							}
							$convertKey = true;
						}
					} else {
						$fkKey = $this->foreigns[$dbtid][$ref_dbtid][0];
						$foreign = $this->foreignsInfo[$dbtid][$fkKey];
						$chosenFk = $dbtid.'_'.$fkKey;
						if (in_array($chosenFk,$model->_usedFK)){
							if ($this->showMessage){
								$PATH = implode('->',$this->stack);
								$PARENT = $model->getModelNamekey();
								$FOREIGN = $this->foreignsInfo[$dbtid][$fkKey]->columnName.'->'.$this->foreignsInfo[$dbtid][$fkKey]->refColumnName;
								$msgText = 'In order to fill the foreign tag . '.$PATH . ' we searched for a foreign key between '.$PARENT.' and '.$modelName.'. We found the foreign key '.$FOREIGN.'.';
								$msgText .= ' The problem is that this foreign key was already used for a previous tag of the same model so this tag will be skipped. Remember that you can use the attribute "column" in order to specify directly the column of the model to set';
WMessage::log( 'Warning:  '.$msgText, 'install-xml' );
								$messageOk = true;
							}
						} else {
							$model->_usedFK[]=$chosenFk;
							$convertKey = true;
						}
					}
					if ($convertKey){
						$foreign = $this->foreignsInfo[$dbtid][$fkKey];
						$name = $foreign->columnName;
						if (!is_numeric($value)){
							$value =& $this->_convertForeign($foreignModel,$value);
						}
					}elseif ($this->showMessage && !$messageOk){
						$PATH = implode('->',$this->stack);
						$MODEL = $model->getModelNamekey();
WMessage::log( 'Warning: We couldn\'t identify the tag	'.$PATH . ' as a foreign key tag and the tag is not in the list of columns of the the model '.$MODEL.'. It is highly possible that the query using this tag will fail.', 'install-xml' );
					}
				}
			}
		}
		$this->_checkParams($name,$value,$children);
		if (isset($this->columns[$dbtid][$name])){
			$columnKey = $this->columns[$dbtid][$name];
			$type = $this->columnsInfo[$dbtid][$columnKey]->type;
			$this->_checkType($type,$value,$name);
		}
		$model->$name =& $value;
	}
	function _checkParams($name,&$value,&$children){
		if ($name=='params' && !empty($children)){
			$value = '';
			foreach($children as $param){
				$value.=$param['nodename'].'='.$param['nodevalue']."\n";
			}
			$value = rtrim($value,"\n");
		}
	}
	function _checkType($type,&$value,$name){
		if (($type >=1 && $type <=5 ) || $type == 25){
			if (!is_numeric($value)){
				$parent = $this->stack[count($this->stack)-2];
				$value = $this->_convertType($parent.'.'.$name,$value);
			}
		}
	}
	function &_convertForeign($model,$value){
		if (is_object($model)){
			$modelName = $model->getModelNamekey();
		} else {
			$modelName = $model;
			$model = WModel::get($model,'objectfile',null,false);
		}
		if ($modelName=='model'){
			$parent=null;
			$this->_getModelName($value,false,$parent);
		}
		if ($model){
			$dbtid = $model->getTableId();
			if (!isset($this->constraintsInfo[$dbtid])){
				$this->constraintsInfo[$dbtid] = $model->getConstraints('uk');
			}
			$found=false;
			foreach($this->constraintsInfo[$dbtid] as $constraint){
				if (count($constraint)==1){
					$columnName = key($constraint);
					$found = true;
					break;
				}
			}
			if ($found){
				$model->whereE($columnName,$value);
				$realValue = $model->load('lr',$model->getPK());
				if ($realValue){
					$value = $realValue;
				}elseif (isset($this->toInsert[$dbtid][$columnName][$value])){
					$value =& $this->toInsert[$dbtid][$columnName][$value];
				}elseif ($this->showMessage){
					$PATH = implode('->',$this->stack);
WMessage::log( 'Warning: No entry found for the value '.$value.' of the '.$columnName.' of the model '.$modelName.' in order to fill the foreign tag '.$PATH, 'install-xml' );
				}
			}elseif ($this->showMessage){
				$PATH = implode('->',$this->stack);
WMessage::log( 'Warning:  No suitable (on one column) unique key found for the model '.$modelName.' in order to fill the foreign tag '.$PATH.' with the value '.$value, 'install-xml' );
			}
		}elseif ($this->showMessage){
			$PATH = implode('->',$this->stack);
WMessage::log( 'Warning:  The foreign model '.$modelName.' could not be found for the tag '.$PATH.' with the value $value', 'install-xml' );
		}
		return $value;
	}
	function _convertType($type,$value){
		$typeHandler = WType::get($type,false);
		if ($typeHandler){
			$value = $typeHandler->getValue($value,false);
		}elseif ($this->showMessage){
			$PATH = implode('->',$this->stack);
WMessage::log( 'Notice:  The type of the tag '.$PATH.' could not be found. Thus, the tag value won\'t be converted.', 'install-xml' );
		}
		return $value;
	}
	function _loadColumns($dbtid){
		if (isset($this->columnsInfo[$dbtid])){
			return true;
		}
		$columnModel = WModel::get('library.columns');
		$columnModel->whereE('dbtid', $dbtid);
		$this->columnsInfo[$dbtid] = $columnModel->load('ol');
		if (empty($this->columnsInfo[$dbtid])){
			if ($this->showMessage){
				$PATH = implode('->',$this->stack);
WMessage::log( 'Notice:  Although we found the model of the tag '.$PATH.' in the database, we couldn\'t retreive the list of columns of the model. This may result in unexpected errors.', 'install-xml' );
			}
			return false;
		}
		$this->columns[$dbtid] = array();
		foreach($this->columnsInfo[$dbtid] as $key => $column){
			$this->columns[$dbtid][$column->name] = $key;
		}
		return true;
	}
	function _loadForeigns($dbtid){
		if (isset($this->foreignsInfo[$dbtid])) return true;
		$foreignModel = WModel::get('library.foreign');
		$foreignModel->makeLJ('library.table','dbtid'); 
		$foreignModel->makeLJ('library.columns','feid','dbcid'); 
		$foreignModel->makeLJ('library.table','ref_dbtid','dbtid'); 
		$foreignModel->makeLJ('library.columns','ref_feid','dbcid'); 
		$foreignModel->whereE('dbtid',$dbtid);
		$foreignModel->whereE('publish',1);
		$foreignModel->select(array('dbtid','ref_dbtid','feid','ref_feid','ondelete','onupdate'));
		$foreignModel->select(array('name','prefix','dbid','export'),1,array('tableName','tablePrefix','DBID','export'));
		$foreignModel->select(array('name','type'),2,array('columnName','columnType'));
		$foreignModel->select(array('name','prefix','dbid','export'),3,array('refTableName','refTablePrefix','refDBID','refExport'));
		$foreignModel->select(array('name'),4,array('refColumnName'));
		$foreignModel->orderBy('ordering');
		$this->foreignsInfo[$dbtid] = $foreignModel->load('ol');
		$this->foreigns[$dbtid] = array();
		if (empty($this->foreignsInfo[$dbtid])){
			return true;
		}
		foreach($this->foreignsInfo[$dbtid] as $key => $foreign){
			if (!isset($this->foreigns[$dbtid][$foreign->ref_dbtid])){
				$this->foreigns[$dbtid][$foreign->ref_dbtid] = array();
			}
			$this->foreigns[$dbtid][$foreign->ref_dbtid][] = $key;
		}
		return true;
	}
}