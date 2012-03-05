<?php
class LoginController extends Zend_Controller_Action
{
    public function init () {}
    public function indexAction ()
    {
        $form = new Form_LoginForm();
        $form->setAction($this->view->url(array('controller' => 'login', 
    'action' => 'index')));
        $this->view->type = 0;
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            //Se il form è valido, lo processiamo   
            if ($form->isValid($_POST)) {
                //recuperiamo i dati così .....
                $user = $this->getRequest()->getParam(
                'username');
                $password = $this->getRequest()->getParam('password');
                $auth = Zend_Auth::getInstance();
                $adapter = new Zend_Auth_Adapter_DbTable(
                Zend_Db_Table::getDefaultAdapter());
                $adapter->setTableName("site_users")
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('user_pass')
                    ->setCredentialTreatment('md5(?)');
                $adapter->setIdentity($user);
                $adapter->setCredential($password);
                $result = $adapter->authenticate();
                if ($result->isValid()) {
                    $user = $adapter->getResultRowObject(array('ID', 
                    'username'));
                    $auth->getStorage()->write(
                    $user);
                    $this->view->type = 1;
                    $this->view->text = "login eseguito con successo";
                } else {
                	$this->view->type = 2;
                    switch ($result->getCode()) {
                        case Zend_Auth_Result::FAILURE:
                            $this->view->text = "login fallito";
                            break;
                        case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                            $this->view->text = "password errata";
                            break;
                        case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                            $this->view->text = "Username '" .
                             $result->getIdentity() . "' inesistente";
                            break;
                        case Zend_Auth_Result::FAILURE_UNCATEGORIZED:
                            $this->view->text = $result->getMessages();
                            break;
                    }
                }
            } 
            else 
                $this->view->form->populate($_POST);
        }
    }
    function logoutAction ()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity())
            $auth->clearIdentity();
    }
    function recoverAction()
    {
    	$uid=Zend_Auth::getInstance()->getIdentity()->user_id;
    	$dbval=new Zend_Validate_Db_RecordExists(array('table' => USERS_TABLE, 'field' => 'user_mail'));
    	$alnum=new Zend_Validate_Alnum();
    	$strval=new Zend_Validate_StringLength(array('min' => 8));
    	$code=Zend_Db_Table::getDefaultAdapter()->fetchOne("SELECT `user_code` FROM `".USERS_TABLE."` WHERE `ID`='$uid'");
    	if ($_POST['submit']) {
    		if ($dbval->isValid($_POST['email'])) {
    			$auth=auth();
    			Zend_Db_Table::getDefaultAdapter()->update(USERS_TABLE, array('user_code'=>$auth),array('ID'=>$uid));
    			$sender = new Zend_Mail();
                $sender->addTo($_POST['email'])
                	->setFrom(WEBMAIL, SITO)
                    ->setBodyHtml('per cambiare la password clicca su <a href="'.URLSITO.$this->_helper->url("recover","login","default",array('code'=>$auth)).'">questo link</a>')
                    ->setBodyText('per cambiare la password clicca su questo link ('.URLSITO.$this->_helper->url("recover","login","default",array('code'=>$auth)).')')
                    ->setSubject($this->t->_("Recupero password"))
                    ->send();
                $this->view->selector="send_mail";
    		}
    	}
    	elseif ($this->getRequest()->getParam("code")==$code) {
    		$this->view->selector="change_pass";
    		$this->view->action=$this->_helper->url("recover","login","default",array('changepass'=>'true','code'=>$code));
    	}
    	elseif ($this->getRequest()->getParam("changepass")&&($this->getRequest()->getParam("code")==$code)) {
    		if (($alnum->isValid($_POST['password']))&&($strval->isValid($_POST['password']))&&($_POST['password']==$_POST['password2'])) {
    			Zend_Db_Table::getDefaultAdapter()->update(USERS_TABLE, array('user_pass'=>$_POST['password'],'user_code'=>''),array('ID'=>$uid));
    			$this->view->selector="done";
    		}
    		else
    			$this->view->selector="errorpass";
    	}
    	else {
    		$this->view->selector="get_mail";
    	}
    }
}

