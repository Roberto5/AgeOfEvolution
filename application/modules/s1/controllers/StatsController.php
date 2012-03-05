<?php

class S1_StatsController extends Zend_Controller_Action
{

    public function init()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        $this->view->data=array();
        $this->view->data['server']=SERVER;
    }

    public function indexAction()
    {
    	$this->view->data['offline']=Zend_Registry::get("param")->get('offline');
        $this->view->data['N_village']=$this->_db->fetchOne("SELECT count(*) FROM `".MAP_TABLE."` WHERE `type`!='0'");
    	$this->view->data['N_civ']=$this->_db->fetchOne("SELECT count(*) FROM `".CIV_TABLE."` WHERE `civ_id`!='0'");
    	
    }
}

