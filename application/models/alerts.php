<?php
/**
 *
 * @author pagliaccio
 * @version
 */
require_once 'Zend/Db/Table/Abstract.php';
class Model_alerts extends Zend_Db_Table_Abstract
{
	public static  $data;
	protected $_name=ALERTS_TABLE;
	function __construct() {
		$auth=Zend_Auth::getInstance();
		parent::__construct();
		self::$data=$this->getAdapter()->fetchAll(
				"SELECT *
				FROM `" . ALERTS_TABLE ."`
				WHERE `aid`!=ALL (
					SELECT `id` 
					FROM `" .ALERTS_READ . "` 
					WHERE `user`='" . $auth->getIdentity()->user_id . "'
				)"
		);
	}
	/**
	 * @return array
	 */
	static function getAlerts() {
		$a=new Model_alerts();
		return self::$data;
	}
}
?>