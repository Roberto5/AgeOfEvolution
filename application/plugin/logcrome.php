<?php

include_once APPLICATION_PATH.'/plugin/ChromePhp.php';

class logcrome_write extends Zend_Log_Writer_Abstract {
	/* (non-PHPdoc)
	 * @see Zend_Log_Writer_Abstract::_write()
	 */
	protected function _write($event) {
		ChromePhp::log($event);
	}

	/* (non-PHPdoc)
	 * @see Zend_Log_FactoryInterface::factory()
	 */
	public static function factory($config) {
		
	}

	
}

?>