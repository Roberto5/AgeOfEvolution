<?php

class Admin_IndexController extends Zend_Controller_Action
{
	/**
	 * 
	 * @var Model_params
	 */
	private $param;
	/**
	 * 
	 * @var Zend_Db_Adapter_Abstract
	 */
	private $db;
    public function init()
    {
        /* Initialize action controller here */
    	$this->log=Zend_Registry::get("log");
    	if (Zend_Registry::isRegistered("param"))
    	$this->param=Zend_Registry::get("param");
    	$this->db=Zend_Db_Table::getDefaultAdapter();
    }

    public function indexAction()
    {
        $this->view->sel=!$_COOKIE['server'];
        if ($_COOKIE['server']) {
        	$this->view->params=$this->param->getall();
        }
    }
    public function editAction() {
    	$this->_helper->layout->setLayout('ajax');
    	$this->_helper->viewRenderer->setNoRender(true);
    	$this->param->set($_POST['name'], htmlentities($_POST['value']));
    }

    public function selAction()
    {
		global $server;
		if (in_array($_POST['server'], $server))
		setcookie("server",$_POST['server'],mktime()+3600,"/");
		$this->_helper->redirector("index");
    }

    public function loaderAction()
    {
        //$val=$this->param->get("work");
        $val=$this->db->fetchCol("SELECT `value` FROM `".PARAMS_TABLE."` WHERE `name`IN ('work','comment')");
        //256:val=100:x x=val*100/256
        $this->view->value=intval($val[1]);
        $this->view->comment=$val[0];
        $this->view->flag=$_POST['flag'];
        if ($_POST['flag']) Zend_Layout::getMvcInstance()->disableLayout();
    }


}



