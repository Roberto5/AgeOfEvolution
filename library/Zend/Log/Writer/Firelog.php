<?php
/** Zend_Log */
require_once 'Zend/Log.php';

/** Zend_Log_Writer_Abstract */
require_once 'Zend/Log/Writer/Abstract.php';
/** firelog library */

require_once 'includes/firelogger.php';

class Zend_Log_Writer_firelog extends Zend_Log_Writer_Abstract
{
	private $flog;
	protected $priority = array(Zend_Log::EMERG  =>"error",
			Zend_Log::ALERT  => "error",
			Zend_Log::CRIT   => "critical",
			Zend_Log::ERR    => "error",
			Zend_Log::WARN   => "warning",
			Zend_Log::NOTICE => "warning",
			Zend_Log::INFO   => "info",
			Zend_Log::DEBUG  => "debug");
	public function __construct($name='logger', $style=null)
	{
		$this->flog=new FireLogger($name,$style);
	}
	 
	public static function factory($config)
	{
		if ($config instanceof Zend_Config) {
			$config = $config->toArray();
		}
		if (!is_array($config)) {
			throw new Exception(
					'factory expects an array or Zend_Config instance'
			);
		}
		 
		$default = array(
				'name' => null,
				'style' => null,
		);
		$config = array_merge($default, $config);
		 
		return new self(
				$config['name'],
				$config['style']
		);
	}
	/**
	 * @param array $event ['message'] ['priority']
	 * (non-PHPdoc)
	 * @see Zend_Log_Writer_Abstract::_write()
	 */
	protected function _write($event) {
		$this->flog->log($this->priority[$event['priority']],$event['message']);
	}
}
?>