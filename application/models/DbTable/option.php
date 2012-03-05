<?php
/**
 * option
 * 
 * @author pagliaccio
 * @version 
 */
require_once 'Zend/Db/Table/Abstract.php';
class Model_option extends Zend_Db_Table_Abstract
{
    /**
     * The default table name 
     */
    protected $_name = 'option';
    private $id = 0;
    private $type;
    /**
     * db
     * @var Zend_Db_Table
     */
    private $db;
    function __construct ($id, $type = "user")
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $this->db = $db;
        $this->id = $id;
        $this->type = $type;
        if ($type == "user")
            $opt = $db->fetchAll(
            "SELECT * 
					FROM `" . OPTION_TABLE . "` 
					WHERE `user_id`='" . $id . "'");
        else
            $opt = $db->fetchAll(
            "SELECT * 
				FROM `" . OPTION_CIV_TABLE . "` 
				WHERE `civ_id`='" . $id . "'");
        for ($i = 0; $opt[$i]; $i ++) {
            $var = $opt[$i]['option'];
            $this->$var = $opt[$i]['value'];
        }
    }
    public function get ($name, $default = 0)
    {
        if (isset($this->$name))
            $default = $this->$name;
        return $default;
    }
    public function del ($name)
    {
        if (isset($this->$name)) {
            if ($this->type == "user")
                $this->db->query(
                "
				DELETE FROM `" . OPTION_TABLE . "` 
				WHERE `user_id` = '" . $this->id . "' 
				AND `option` = '" . $name . "' LIMIT 1");
            else
                $this->db->query(
                "DELETE FROM `" . OPTION_CIV_TABLE . "` 
					WHERE `civ_id` = '" . $this->id . "' 
					AND `option` = '" . $name . "' LIMIT 1");
            unset($this->$name);
        }
    }
    public function set ($name, $value)
    {
        if ($this->type == "user")
            $this->db->query(
            "INSERT INTO `" . OPTION_TABLE . "` 
				SET `value`='" . $value . "' ,
				 `option`='" . $name . "' , 
				 `user_id`='" . $this->id .
             "' ON DUPLICATE KEY UPDATE `value`='" . $value . "'");
        else
            $this->db->query(
            "INSERT INTO `" . OPTION_CIV_TABLE . "` 
					SET `value`='" . $value . "' ,
					`option`='" . $name .
             "' ,
					`civ_id`='" .
             $this->id . "' ON DUPLICATE KEY UPDATE `value`='" . $value . "'");
        $this->$name = $value;
    }
    
}
?>