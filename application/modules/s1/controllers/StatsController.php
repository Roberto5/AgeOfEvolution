<?php

class S1_StatsController extends Zend_Controller_Action
{
	private $update=array();
    public function init()
    {
        Zend_Layout::getMvcInstance()->setLayout('ajax');
        //$this->update['server']=SERVER;
        /*@todo ottimizzazione evitare il processamento di civ*/
    }

    public function indexAction()
    {
    	$this->view->data=array();
    	$this->view->data['offline']=Zend_Registry::get("param")->get('offline');
    	$this->view->data['server']=SERVER;
    	$map=Model_map::getInstance();
    	
        $this->update['button'.SERVER.' .N_village']=count($map->fetchAll()->toArray());
    	$this->update['button'.SERVER.' .N_civ']=$this->_db->fetchOne("SELECT count(*) FROM `".CIV_TABLE."` WHERE `civ_id`!='0'");
    	Model_refresh::getInstance()->addIds($this->update);
    }
}

