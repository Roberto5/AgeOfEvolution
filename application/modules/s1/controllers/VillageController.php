<?php
class S1_VillageController extends Zend_Controller_Action
{
	/**
	 * 
	 * Enter description here ...
	 * @var Model_civilta
	 */
	private $civ;
    public function init ()
    {
    	$this->civ=Model_civilta::getInstance();
    }
    public function indexAction ()
    {
    	$this->_helper->layout->disableLayout();
    	$this->view->age=$this->civ->getAge();
    }
   
}

