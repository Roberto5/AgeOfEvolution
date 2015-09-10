<?php

/**
 * params
 * 
 * @author pagliaccio
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Model_params extends Zend_Db_Table_Abstract {

	/**
	 * The default table name 
	 */
	protected $_name=PARAMS_TABLE;
	protected $_primary="name";
	static $instance;
	
	function __construct() {
		parent::__construct();
		self::$instance=$this;
	}
	/**
	 * @return Model_params
	 */
	static function getInstance() {
		if (!self::$instance) self::$instance= new Model_params();
		return self::$instance;
	}
	/**
	 * 
	 * @return array
	 */
	function getall() {
		$par=$this->fetchAll()->toArray();
		foreach ($par as $key=>$value) {
			$this->$key=$value;
		}
		return $par;
	}
/**
     * se la variabile non è presente in memoria la estrae dal DB
     * @param String $name nome del parametro
     * @param Mixed $default valore di default
     * @return mixed
     */
    function get($name,$default=0,$refresh=false)
    {
        
        if (isset ($this->$name)&&!$refresh) $default=$this->$name;
        else {
            $r=$this->getDefaultAdapter()->fetchOne("SELECT `value` FROM `".PARAMS_TABLE."` WHERE `name`='".$name."'");
            if ($r) {
                $default=$r;
            }
            $this->$name=$default;
        }
        return $default;
    }
    /**
     * setta le variabili
     * @param String $name
     * @param mixed $value
     */
    function set($name,$value)
    {
        $this->$name=$value;
        $this->getDefaultAdapter()->delete(PARAMS_TABLE,"`name`='".$name."'");
        $this->getDefaultAdapter()->insert(PARAMS_TABLE,array("name"=>$name , "value"=>$value));
    }
}
