<?php
/**
 * research
 * 
 * @author pagliaccio
 * @version 
 */
require_once 'Zend/Db/Table/Abstract.php';
class Model_research extends Zend_Db_Table_Abstract
{
    /**
     * The default table name 
     */
    protected $_name = RESEARCH_TABLE;
    public $data = array();
    public $cid = 0;
    function __construct ($cid)
    {
        parent::__construct();
        $this->log=Zend_Registry::get("log");
        $this->cid = $cid;
        $this->data = $this->getDefaultAdapter()->fetchAssoc(
        "SELECT `rid`,`liv` FROM `" . RESEARCH_TABLE . "` WHERE `civ_id`='$cid'");
    }
    /**
     * fa l'uprgade della ricerca
     * @param int $rid
     */
    function upgrade ($rid)
    {
        $liv = $this->data[$rid]['liv'];
        if ($liv)
            $this->update(array('liv' => $liv + 1), 
            "`civ_id`='" . $this->cid . "' AND `rid`='$rid'");
        else
            $this->insert(array('civ_id' => $this->cid, 'liv' => 1, 
            'rid' => $rid));
    }
    /**
     * restituisce le ricerche disponibili
     * @return array
     */
    function dispRes ()
    {
        global $research_array;
        $disp = array();
        $civ=Model_civilta::getInstance();
        foreach ($research_array as $type=>$res) {
            $bool = true;
            if ($res::$require) {
            	if ($civ->getAge()<$res::$age) $bool=false;
                if (($res::$require['research'])&&($bool))
                    foreach ($res::$require['research'] as $value) {
                        if ($this->data[$value['type']]['liv'] < $value['liv'])
                            $bool = false;
                    }
                if (($res::$require['build'])&&($bool))
                    foreach ($res::$require['build'] as $value) {
                    	$b=$civ->village->building[$civ->getCurrentVillage()];
                    	$pos=$b->getBildForType($value['type']);
                        if ($b->getLiv($pos) < $value['liv'])
                            $bool = false;
                    }
                
            }
            $this->log->debug($res::$livmax[$civ->getAge()]);
            if (($bool)&&($res::$livmax[$civ->getAge()]<=$this->data[$type]['liv'])) $bool=false;
            $disp[$res]=$bool;
        }
        
        return $disp;
    }
    /**
     * pr impiegati dalle ricerche
     * @return int
     */
    function busypr() {
    	global $research_array;
    	$pr=0;
    	foreach ($this->data as $key => $value) {
    		$pr+=$research_array[$key]::$cost[$value['liv']];
    	}
    	return $pr;
    }
}
