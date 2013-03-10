<?php
/**
 * @todo qui non funziona, mettere apposto confrontando con login
 * @author pagliaccio
 *
 */
class Plugin_Logweb extends Zend_Log_Writer_Abstract {
	private  static  $mess=array();
	function __construct() {
	}
	function write($event) {
		$this->_write($event);
	}
	protected function _write($event) {
		$event['message']=json_encode($event['message']);
		self::$mess[]=$event;
	}
	static function factory($config) {
		$web=new Plugin_Logweb();
		return $web;
	}
	static function get() {
		return self::$mess;
	}
}

?>