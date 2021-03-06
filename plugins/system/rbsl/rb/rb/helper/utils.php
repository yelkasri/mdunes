<?php
/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		RB Framework
* @subpackage	Frontend
* @contact 		shyam@readybytes.in
*/
if(defined('_JEXEC')===false) die('Restricted access' );

class Rb_HelperUtils
{
	/**
	 * Clonning function Due to bug in utf8_ireplace function
	 */
	static public function str_ireplace($search, $replace, $str, $count = NULL)
	{
		return str_ireplace($search, $replace, $str, $count = NULL);
	}
	
	// Filter a string so it can be used as id
	public static function jsCompatibleId($str='', $new='')
	{
		return str_replace(array('[',']',' ','-'), array($new, $new), $str);
	}
		
	public static function jQueryCompatibleSelector($str='')
	{
		return str_replace(array('[',']'), array('\\[','\\]'), $str);
	}
	
	public static function fixJSONDates($json)
	{
		$pattern = '/\"Date\((.*)\)\"/';
		$replace = 'new Date(\1)';
    	return preg_replace($pattern, $replace, $json);
	}
	
	public static function getKeyFromId($id)
	{
		return Rb_Factory::getEncryptor()->encrypt($id);
	}
	
	public static function getIdFromKey($key)
	{
		return Rb_Factory::getEncryptor()->decrypt($key);
	}
	
	static public function getMethodsDefinedByClass($class)
	{
	    $rClass = new ReflectionClass($class);
	    $array = array();
	    foreach ($rClass->getMethods() as $rMethod)
	    {
	        try
	        {
	            // check whether method is explicitly defined in this class
	            if ($rMethod->getDeclaringClass()->getName()
	                == $rClass->getName())
	            {
	                // Convert task into lowercase because in controller's execute function task will be check in lower case only
	                $array[] =  strtolower($rMethod->getName());
	            }
	        }
	        catch (exception $e)
	        {    /* was not in parent class! */    }
	    }
	   
	    return $array;
	}
	
	static $tables = null;
	public static function isTableExist($tableName, $prefix='#__', $refresh=false)
	{
		
		$db		 	   = Rb_Factory::getDBO();

		// load tables if required
		if(self::$tables == null || $refresh == true){
			self::$tables	= $db->getTableList();
		}


		//if table name consist #__ replace it.
		$tableName = str_replace($prefix, $db->getPrefix(), $tableName);

		//check if table exist
		return in_array($tableName,self::$tables ) ? true : false;
	}
}