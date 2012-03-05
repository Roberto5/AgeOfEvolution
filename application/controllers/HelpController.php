<?php

class HelpController extends Zend_Controller_Action
{
	/**
	 * @var Zend_Translate
	 */
	private $t;
    public function init()
    {
    	$this->t=Zend_Registry::get("translate");
    	/*$layout=Zend_Layout::getMvcInstance();
		$layout->disableLayout();*/
    }

    public function indexAction()
    {
    	$this->view->flag=$this->getRequest()->getParam("flag",false);
    	$this->view->age=$this->getRequest()->getParam("age",0);
    	$troop=$this->t->_("Truppe");
    	$build=$this->t->_("Edifici");
        $this->view->link=array($troop=>array('url'=>$this->_helper->url('troop')),
        	$build=>array('url'=>$this->_helper->url('building',null,null,array('age'=>$this->view->age))),
        	'F.a.q'=>array('url'=>$this->_helper->url('faq')),
        );
    }

    public function troopAction()
    {
    	Zend_Layout::getMvcInstance()->disableLayout();
        $this->view->id=$this->getRequest()->getParam("id",false);
    }
	public function faqAction() {
		Zend_Layout::getMvcInstance()->disableLayout();
		$this->view->faq=Zend_Db_Table::getDefaultAdapter()->fetchAll("SELECT * FROM `site_faq`");
	}
	public function buildingAction() {
		
		$this->view->id=$this->getRequest()->getParam("id",false);
		if (!$this->view->id) Zend_Layout::getMvcInstance()->disableLayout();
		$this->view->age=$this->getRequest()->getParam("age",0);
	}
}



