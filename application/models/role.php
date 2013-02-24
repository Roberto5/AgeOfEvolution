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
	static public $r=false;
	public $role;
	/**
	 * 
	 * @var Zend_Acl
	 */
	private $acl;
	private $id;
	static function getRole() {
		$auth=Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			if (!self::$r) 	self::$r=Zend_Db_Table::getDefaultAdapter()
			->fetchOne(
				"SELECT `role` FROM `" . ROLE_TABLE . "` 
				WHERE `uid`='" . $auth->getIdentity()->user_id . "'");
			if (!self::$r) self::$r='user';
		}
		else self::$r="guest";
		return self::$r;
	}
	function __construct($id) {
		$this->_name=PREFIX.'role';
		parent::__construct();
		$id = intval($id);
		$this->id=$id;
		$data=$this->fetchRow("`uid`='$id'");
		if ($data) $this->role=$data['role'];
		include_once (APPLICATION_PATH . "/models/acl.php");
		$this->acl=$acl;
	}
	public function setRole($role) {
		if (in_array($role,$this->acl->getRoles())) {
			if ($this->role) $this->update(array('role'=>$role), "`uid`='".$this->id."'");
			else $this->insert(array('role'=>$role,'uid'=>$this->id));
			$this->role=$role;
			return true;
		}
		else return false;
	}
}
?>