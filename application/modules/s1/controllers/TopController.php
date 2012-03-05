<?php

class S1_TopController extends Zend_Controller_Action
{
	/**
	 * 
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $db;
    public function init()
    {
        $this->db=Zend_Db_Table::getDefaultAdapter();
    }

    public function indexAction()
    {
    	$page=$this->getRequest()->getParam("page",1);
    	$this->view->ref=$this->getRequest()->getParam("ref",false);
        $result=$this->db->fetchAssoc("SELECT *,(SELECT COUNT(*) FROM `".MAP_TABLE."` WHERE `".MAP_TABLE."`.`civ_id`=`".CIV_TABLE."`.`civ_id`) as `villages` FROM `".CIV_TABLE."` WHERE `civ_id`!='0' ORDER BY `civ_pop` DESC");
        $top=Zend_Paginator::factory($result);
        $top->setCurrentPageNumber($page);
        //$top->setItemCountPerPage(1);
        $this->view->top=$top;
    }


}

