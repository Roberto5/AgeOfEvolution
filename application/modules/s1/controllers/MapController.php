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
        //$this->civ=Model_civilta::getInstance();
        //$this->db=Zend_Db_Table::getDefaultAdapter();
        //$this->t=Zend_Registry::get("translate");
    }

    public function indexAction()
    {
    	//view do only encode json of this->map
    	$this->_helper->layout->disableLayout();
    	$map=Model_map::getInstance();
    	$this->view->map=$map->getVillageArray();
    	//echo '<pre>'.print_r($map).'</pre>';
    }
}

