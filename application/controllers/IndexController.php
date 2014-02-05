<?php

class IndexController extends Zend_Controller_Action {

	public function init() {
		/* Initialize action controller here */
	}

	public function indexAction() {
		//recuperiamo l'istanza di Zend_Auth
		$auth=Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$this->view->identity=$auth->getIdentity();
			/**
			 * user
			 * @var Model_user
			 */
			$user=Model_user::getInstance();
			$this->view->list=$user->getServerSubscrive();
			
		}
		else
			$this->view->identity=false;
	}
}

