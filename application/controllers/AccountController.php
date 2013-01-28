<?php

class AccountController extends Zend_Controller_Action
{
	/**
	 * @var Model_user
	 */
	var $user;
	var $log;
    public function init()
    {
        $this->user=Zend_Registry::get("user");
        $this->log=Zend_Registry::get("log");
    }

    public function indexAction()
    {
    	$bool=token_ctrl($this->getRequest()->getParams());
    	$_POST['password']=sha1($_POST['password']);
    	$this->view->token=auth();
    	Zend_Auth::getInstance()->getStorage()->set("tokenP",$this->view->token);
    	$t=Zend_Registry::get("translate");
    	$pass=new Form_Profile();
    	$this->view->pass=$pass;
    	$this->log->debug($_POST);
        if (($_POST['submit'])&&($bool['tokenP'])) {
			if ($_POST['newpass']) {
				
				if (!$pass->isValid($_POST)) {
					$this->view->pass->populate($_POST);
					//$this->log->debug("fallito");
				}
				else {
					$this->user->updateU(array('user_pass'=>sha1($pass->getValue('newpass'))));
					$session=Zend_Auth::getInstance()->getStorage();
					$uid=$this->user->ID;
					$session->destroyAll($uid);
					//$this->log->debug("successo nuova pass ".$pass->getValue('newpass'));
				}
			}
			/*
			 * @todo implementare cambio email
			 * if ($_POST['email']) {
				$val=new Zend_Validate_EmailAddress();
				if (!$val->isValid($_POST['email'])) {
					$bool=false;
				}
				else {
					$this->db->query("UPDATE `".USERS_TABLE."` SET `user_pass`='".$_POST['newpass']."' WHERE `ID`='$id'");
				}
			}*/
        	$this->log->debug($_POST['order']);
        	$this->log->debug($this->user->option->get("order", 1));
    		if ($_POST['order'] != $this->user->option->get("order", 1))
				$this->user->option->set("order", intval($_POST['order']));
    		if ($_POST['orderT'] != $this->user->option->get("orderT"))
				$this->user->option->set("orderT", intval($_POST['orderT']));
    		if ($_POST['coordB'] != $this->user->option->get("coord"))
				$this->user->option->set("coord", intval($_POST['coordB']));
    		if ($_POST['x'] != $this->user->option->get("coord_x"))
				$this->user->option->set("coord_x", intval($_POST['x']));
    		if ($_POST['y'] != $this->user->option->get("coord_y"))
				$this->user->option->set("coord_y", intval($_POST['y']));
    		if ($_POST['news'] != $this->user->option->get("news"))
				$this->user->option->set("news", intval($_POST['news']));
    //$this->alerts("profile.php", $t->_('opzioni modifiate'));
        }
        else {
        	$this->view->order = $this->user->option->get("order", "1");
        	$this->log->debug($this->view->order);
    		$this->view->orderT = $this->user->option->get("orderT");
    		$this->view->x=$this->user->option->get("coord_x");
    		$this->view->y=$this->user->option->get("coord_y");
    		$this->view->coordB=$this->user->option->get("coord");
    		$this->view->news=$this->user->option->get("news",1);
        }
    }
}

