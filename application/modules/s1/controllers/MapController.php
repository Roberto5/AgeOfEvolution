<?php

class S1_MapController extends Zend_Controller_Action
{
	/**
	 * 
	 * Enter description here ...
	 * @var Model_civilta
	 */
	var $civ;
	/**
	 * 
	 * Enter description here ...
	 * @var Zend_Db_Adapter_Abstract
	 */
	var $db;
	var $t;
    public function init()
    {
        $this->civ=Model_civilta::getInstance();
        $this->db=Zend_Db_Table::getDefaultAdapter();
        $this->t=Zend_Registry::get("translate");
    }

    public function indexAction()
    {
    }
	public function getvettorvillageAction() {
		$bool=true;
		$minx=$_POST['x'][0];
		$maxx=$_POST['x'][1];
		$miny=$_POST['y'][0];
		$maxy=$_POST['y'][1];
        $extra=false;
        if ((abs($maxx)>MAX_X)||(abs($minx)>MAX_X)||(abs($maxy)>MAX_Y)||(abs($miny)>MAX_Y))
        	$extra=true;
        $villages=$this->db->fetchAll("SELECT `" . MAP_TABLE. "`.*, 
        	`".CIV_TABLE."`.`civ_name`, `".CIV_TABLE."`.`civ_age`,
        	(SELECT `name` 
        		FROM `" . ALLY_TABLE . "` 
        		WHERE `" . ALLY_TABLE . "`.`id` =`" . CIV_TABLE . "`.`civ_ally` 
        	) AS `ally` 
        	FROM `" . MAP_TABLE. "`,`".CIV_TABLE."` 
        	WHERE `" . MAP_TABLE. "`.`civ_id`=`".CIV_TABLE."`.`civ_id` AND (`x`<='$maxx' AND `x`>='$minx' AND `y`<='$maxy' AND `y`>='$miny')");
       	
        //$villages=array_merge($villages,$extra);
        $this->_helper->layout()->data=array('village'=>$villages);
	}

}

