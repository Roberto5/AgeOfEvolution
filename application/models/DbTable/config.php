<?php
/**
 * config
 * 
 * @author pagliaccio
 * @version 
 */
require_once 'Zend/Db/Table/Abstract.php';
class Model_config extends Zend_Db_Table_Abstract
{
    /**
     * The default table name 
     */
    protected $_name = CONFIG_TABLE;
    
    static function get($name,$default=false) {
    	$value=Zend_Db_Table::getDefaultAdapter()->fetchOne("SELECT `value` FROM `".CONFIG_TABLE."` WHERE `option`='$name'");
    	return ($value ? $value : $default);
    }
    static function set($name,$value) {
    	Zend_Db_Table::getDefaultAdapter()->delete(CONFIG_TABLE,"`option`='$name'");
    	Zend_Db_Table::getDefaultAdapter()->insert(CONFIG_TABLE, array('option'=>$name,'value'=>$value));
    }
	static function del($name) {
    	Zend_Db_Table::getDefaultAdapter()->delete(CONFIG_TABLE,"`option`='$name'");
    }
}
