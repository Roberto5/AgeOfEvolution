<?php

class S1_EvController extends Zend_Controller_Action {

	/**
	 *
	 * Enter description here ...
	 * @var Model_civilta
	 */
	private $civ;

	public function init() {
		$this->civ=Model_civilta::getInstance();
	}

	public function indexAction() {
		$session=Zend_Auth::getInstance()->getStorage();
		//
		if (($_POST['tokenEv']!=0) && ($_POST['tokenEv'] == $session->get(
				"tokenEv",0))) {
			
			if (isset($_POST['no'])) {
				$this->_helper->redirector("index", "index", "s1");
			}
			else {
				if ($this->civ->evolution())
					$this->view->display="do";
				else $this->view->display="err";
			}
		
		}
		else {
			$auth=sha1(auth());
			$session->set("tokenEv", $auth);
			$this->view->token=$auth;
		}
	
	}

}

