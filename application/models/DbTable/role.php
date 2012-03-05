<?php

/**
 * role
 * 
 * @author pagliaccio
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Model_role extends Zend_Db_Table_Abstract {

	/**
	 * The default table name 
	 */
	protected $_name=ROLE_TABLE;
	static public $role=false;
	
	static function getRole() {
		$auth=Zend_Auth::getInstance();
		if (!self::$role)
		self::$role=Zend_Db_Table::getDefaultAdapter()
			->fetchOne(
				"SELECT `role` FROM `" . ROLE_TABLE . "` 
				WHERE `user_id`='" . $auth->getIdentity()->user_id . "'");
		//Zend_Registry::get("log")->debug(self::$role);
		if (!self::$role) {
			self::$role="user";
			//Zend_Registry::get("log")->debug("non c'e per id ".$auth->getIdentity()->user_id);
		}
		return self::$role;
	}

}
