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
        $this->user=Model_user::getInstance();
        $this->view->option=array(
        		//'prova'=>array(
        		//		'name'=>'[PROVA]',
        		//		'value'=>'prova',
        		//		'label'=>''//if null display value
        		//		'type'=>'Input'// default or Select or Textarea
        		//		'option'=>array())
        );
    }

    public function indexAction()
    {
    	$this->view->key=array('NICK'=>$this->user->data['username'],'EMAIL'=>$this->user->data['email']);
    }
    public function editAction()
    {
    	$this->_helper->layout->setLayout("ajax");
    	$this->_helper->viewRenderer->setNoRender(true);
    	$bool=false;$mess='""';
    	$form=new Form_Register();
    	if ($e=$form->getElement($_POST['key'])) {
    		if ($e->isValid($_POST['value'])) {
    			$bool=true;
    			$this->user->updateU(array($_POST['key']=>$_POST['value']));
    		}
    		else $mess=$this->_t->_('DATA_ERROR');
    	}
    	else {
    		$mess=$this->_t->_('DATA_ERROR');
    	}
    	$this->_helper->layout->data=$bool;
    	if (!$bool) {
    		$this->_helper->layout->content=$mess;
    	}
    }
    
    public function ctrlAction()
    {
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	header("Content-type: application/json");
    	$username=$_POST['username'];
    	$email=$_POST['email'];
    	$password=$_POST['password'];
    	$db=new Zend_Validate_Db_NoRecordExists(array('table'=>USERS_TABLE,'field'=>'username'));
    	$bool=true;
    	if ($username) {
    		$alnum= new Zend_Validate_Alnum();
    		$db->setField('username');
    		$bool= (($alnum->isValid($username)) && ($db->isValid($username)));
    	}
    	if ($email) {
    		$db->setField('email');
    		$vemail=new Zend_Validate_EmailAddress();
    		$bool= (($db->isValid($email)) && ($vemail->isValid($email)));
    	}
    	if ($password) {
    		$db->setField('email');
    		$alnum=new Zend_Validate_Alnum();
    		$bool= (($alnum->isValid($password)) && ($this->user->data['password']==sha1($password)));
    	}
    	echo json_encode($bool);
    }
    
    public function passwordAction()
    {
    	$this->_helper->layout->setLayout("ajax");
    	$this->_helper->viewRenderer->setNoRender(true);
    	$bool=false;$mess='';
    	$form=new Form_Register();
    	$e=$form->getElement('password');
    	if (sha1($_POST['password'])==$this->user->data['password']) {
    		if ($e->isValid($_POST['new'])&&($_POST['new']==$_POST['new2'])) {
    			$bool='true';
    			$this->user->updateU(array('password'=>$_POST['new']));
    		}
    		else $mess=$this->_t->_('DATA_ERROR');
    	}
    	else $mess=$this->_t->_('PASS_ERR');
    	$this->_helper->layout->data=$bool;
    	if (!$bool) {
    		$this->_helper->layout->content=$mess;
    	}
    }
    
    public function deleteAction()
    {
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	$bool=false;$mess='';
    	if (sha1($_POST['password'])==$this->user->data['password']) {
    		$id=$this->user->data['id'];
    		$this->user->delete("`id`='$id'");
    		$auth = Zend_Auth::getInstance();
    		if ($auth->hasIdentity())
    			$auth->clearIdentity();
    		$bool=true;
    	}
    	else $mess=$this->_t->_('PASS_ERR');
    	$this->_helper->layout->data=$bool;
    	if (!$bool) {
    		$this->_helper->layout->content=$mess;
    	}
    }
}

