<?php
/**
 * user
 * 
 * @author pagliaccio
 * @version 
 */
require_once 'Zend/Db/Table/Abstract.php';
class Model_user extends Zend_Db_Table_Abstract
{
    /**
     * The default table name 
     */
    protected $_name = USERS_TABLE;
    protected $_primary = 'ID';
    /**
     * db
     * @var Zend_Db_Adapter_Abstract
     */
    private $db;
    /**
     * model_option
     * @var Model_option
     */
    public $option = null;
    public $data = null;
    public $ID;
    /**
     * @param int $ID
     */
    function __construct ($ID = 1)
    {
    	parent::__construct();
        $ID = intval($ID);
        $this->ID=$ID;
        $this->db=Zend_Db_Table::getDefaultAdapter();
        //$this->setDefaultAdapter($db);
        //$this->db=$db;
        //$row = $db->fetchRow("SELECT * FROM `".$this->_name."` WHERE `ID`='$ID'");
        //$w=$this->select()->where("`ID`='$ID'");
        $row= $this->fetchRow("`ID`='$ID'");
        if (! $row) {
            throw new Exception("Could not find row $ID");
        }
        $this->data = $row;
        $this->option=new Model_option($ID);
    }
    /**
     * registra un utente, ritorna true se la registrazione &egrave; andata bene.
     * @param Array $vect indice per il campo e valore come valore 
     * @return bool
     */
    function register ($data)
    {
        if ($data) {
        	$this->getDefaultAdapter()->insert(USERS_TABLE,$data);
            return true;
        } else {
            return false;
        }
    }
    /**
     * modifica i valori dell'utente
     * @param Array $data indice per il campo e valore come valore 
     * @return bool 
     */
    function updateU ($data)
    {
        if ($data) {
        	$this->update($data,"`ID`='$this->ID'");
            return true;
        }
        return false;
    }
    /**
     * ritorna la lista dei server in cui è iscritto un utente
     */
    function getServerSubscrive() {
    	$sel=$this->getDefaultAdapter()->select();
        $sel->from(RELATION_USER_CIV_TABLE)->where("user_id = ?",$this->ID);
    	return $this->getDefaultAdapter()->fetchAll($sel);
    }
    
    function getCiv($server) {
    	$sel=$this->db->select();
    	$where="`user_id`='".$this->ID."' AND `server`='".$server."'";
        $sel->from(RELATION_USER_CIV_TABLE)->where($where);
        $cid=$this->db->fetchRow($sel);
    	return $cid;
    }
}
