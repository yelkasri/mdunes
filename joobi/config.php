<?php
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.
* @license GNU GPLv3 */

//defined('JOOBI_SECURE') or define( 'JOOBI_SECURE', true );

class Joobi_Config{
public $model = array(
'tablename'=>'model_node'
);
public $table = array(
'tablename'=>'dataset_tables'
);
public $db = array(
'tablename'=>'dataset_node'
);
public $multiDB = false;
public $secret = 'JoobiDev';
}//endclass