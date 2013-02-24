<?php
/**
 * event
 * 
 * @author pagliaccio
 * @version 
 */
require_once 'Zend/Db/Table/Abstract.php';
class Model_event extends Zend_Db_Table_Abstract
{
    /**
     * The default table name 
     */
    protected $_name = EVENTS_TABLE;
    private $events = array();
    private $time;
    private $id;
    /**
     * 
     * Enter description here ...
     * @var Zend_Log_Writer_Abstract
     */
    protected $log;
    public $now;
    /**
     * 
     * @var Zend_Translate_Adapter
     */
    public $t;
    /**
     * legge gli eventi da processare
     */
    function __construct ($fetch = true)
    {
        parent::__construct();
        $this->db = $this->getDefaultAdapter();
        $this->log = Zend_Registry::get("log");
        $this->t = Zend_Registry::get("translate");
    }
    
    /**
     * ritorna gliveventi
     * @param array $where
     */
    public function getEvent ($where = null, $order = array('time ASC','id ASC'))
    {
        return $this->fetchAll($where, $order);
    }
    /**
     * mette in coda le truppe
     * @param int $num
     * @param int $tid
     * @param int $time
     * @param int $vid
     * @param int $cid
     */
    public function addtrain ($num, $tid, $time, $vid = 0, $cid = 0)
    {
        $civ = Model_civilta::getInstance();
        for ($j = 0; $civ->training[$j]; $j ++);
        if ($j)
            $time += $civ->training[$j - 1]['time'];
        else
            $time += mktime();
        if (! $vid)
            $vid = $civ->getCurrentVillage();
        if (! $cid)
            $cid = $civ->cid;
        $param = serialize(
        array('village_id' => $vid, 'num' => $num, 'trooper_id' => $tid, 
        'civ_id' => $cid));
        $this->insert(
        array('type' => TRAINING_EVENT, 'time' => $time, 'params' => $param));
    }
  
}
