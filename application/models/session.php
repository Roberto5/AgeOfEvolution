<?php
/**
 * @property int $ID
 * @method get($k,$dv=FALSE)
 */
class Sessions implements Zend_Auth_Storage_Interface
{
    var $ID = '';
    var $log;
    /**
     * db adapter
     * @var Zend_Db_Adapter_Pdo_Mssql
     */
    private $db;
    /**
     * @param int $id
     * @param zend_db_adapter
     */
    function __construct ($ID = false, $db)
    {
        
        $this->db = $db;
        //$this->log=Zend_Registry::get("log");
        $this->db->delete(SESSIONS_TABLE, 
        "`last_activity`<'" . strtotime("-336 hours") . "'");
        if (!$_COOKIE['ev_login']) {
        	Zend_Db_Table::setDefaultAdapter($db);
        	$ev=Model_config::get("lastckid",1);
        	Model_config::set("lastckid", $ev+1);
        	setcookie("ev_login",$ev,mktime()+604800,"/");
        	$_COOKIE['ev_login']=$ev;
        }
        else setcookie("ev_login",$_COOKIE['ev_login'],mktime()+604800,"/");
        if ($ID == false) {
            $this->ID = md5(
            $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] .
             $_COOKIE['ev_login']);
        } else {
            $this->ID = $ID;
        }
        $row = $this->db->fetchAll(
        "SELECT * FROM " . SESSIONS_TABLE . " WHERE `ID`='" . $this->ID .
         "' AND `validate`='1'");
        if ($row[0]['last_activity'] < strtotime("-15 minutes")) {
            $data = array('validate' => '0');
            $where = array("`ID`='" . $this->ID . "'", "`validate`='1'");
            $this->db->update(SESSIONS_TABLE, $data, $where);
            for ($i = 0; $row[$i]; $i ++) { //      	
                $data = array('last_activity' => mktime(), 
                'ID' => $row[$i]['ID'], 'var_name' => $row[$i]['var_name'], 
                'var_value' => $row[$i]['var_value'], 'create' => mktime(), 
                'user_id' => $row[$i]['user_id']);
                $this->db->insert(SESSIONS_TABLE, $data);
            }
        } else
            $this->db->update(SESSIONS_TABLE, 
            array('last_activity' => mktime()), 
            array("`ID`='" . $this->ID . "'", "`validate`='1'"));
        foreach ($row as $value) {
            $this->$value['var_name'] = unserialize($value['var_value']);
        }
    }
    /**
     * Returns true if and only if storage is empty
     * @see Zend_Auth_Storage_Interface::isEmpty()
     */
    function isEmpty ()
    {
        return ! isset($this->user_id);
    }
    public function read ()
    {
        return $this;
    }
    public function write ($contents)
    {
        $contents = (array) $contents;
        foreach ($contents as $key => $value) {
        	if ($key=="ID") $key="user_id";
            $this->set($key, $value);
        }
    }
    /**
     *
     * @param String $k
     * @param mixed $dv
     * @return mixed
     */
    function get ($k, $dv = FALSE)
    {
        if (! isset($this->$k)) {
            return $dv;
        } else {
            return $this->$k;
        }
    }
    /**
     *
     * @param String $k
     * @param mixed $v
     */
    function set ($k, $v = NULL)
    {
    	$log=Zend_Registry::get("log");
        if (! isset($this->$k)) {
            $uid = ($k == "user_id" ? $v : "" );
            $data = array('var_name' => $k, 'var_value' => serialize($v), 
            'ID' => $this->ID, 'create' => mktime(), 'last_activity' => mktime(), 
            'user_id' => $uid);
            $this->db->insert(SESSIONS_TABLE, $data);
            $this->$k = $v;
        } else {
            $where = array("`ID`='" . $this->ID . "'", "`validate`='1'", 
            "`var_name`='" . $k . "'");
            if ($k == "user_id")
                $where = array_merge($where, array("`user_id`='" . $v . "'"));
            $this->db->update(SESSIONS_TABLE, 
            array('var_value' => serialize($v), 'last_activity' => mktime()), 
            $where);
            $this->$k = $v;
        }
    }
    /**
     *
     * @param String $k
     */
    function delete ($k)
    {
        $this->db->delete(SESSIONS_TABLE, 
        array("`ID`='" . $this->ID . "'", "`var_name`='" . $k . "'"));
        unset($this->$k);
    }
    /**
     * distrugge la sessione 
     */
    function clear ()
    {
        $this->db->query(
        "UPDATE `" . SESSIONS_TABLE . "` SET `validate`='0' , `last_activity`='" .
         mktime() . "' WHERE `ID`='" . $this->ID . "' AND `validate`='1'");
    }
    /**
     * distrugge le sessioni di un user account
     * @param int $user_id 
     */
    function destroyAll ($user_id)
    {
        global $fw;
        $ID = $this->db->fetchAll(
        "SELECT `ID` FROM `" . SESSIONS_TABLE .
         "` WHERE `var_name`='user_id' AND `var_value`='" . serialize($user_id) .
         "' AND `validate`='1'");
        print_r($ID);
        for ($i = 0; $ID[$i]; $i ++)
            $this->db->update(SESSIONS_TABLE, array('validate' => '0'), 
            array("`ID`='" . $ID[$i] . "'", "`validate`='1'"));
    }
}
?>