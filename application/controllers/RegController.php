<?php

class RegController extends Zend_Controller_Action
{
<<<<<<< HEAD
    /**
     * traduttore
     * @var Zend_Translate
     */
    private $t;
    public function init ()
    {
        $this->t = Zend_Registry::get("translate");
    }
    public function indexAction ()
    {
    	global $messRegisterMail;
        $form = new Application_Form_Register();
        $form->setAction($this->view->url(array('controller' => 'reg')));
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $this->view->send = true;
            if ($form->isValid($_POST)) {
                $this->view->type = 1;
                $this->view->text = $this->t->_("registrazione avvenuta con successo");
                $post = $form->getValues();
                $conf = Zend_Registry::get("config");
                $code = "";
                for ($i = 0; $i < 8; $i ++) {
                    $code .= (rand(0, 1) ? chr(rand(65, 122)) : rand(0, 9));
                }
                $code = sha1($code);
                $active=($conf->email->validation ? 0 : 1 );
                //ID 	username 	user_pass 	user_mail 	user_active 	user_code 	des_user
                $data = array('username' => $post['username'], 
                'password' => sha1($post['password']), 'email' => $post['email'], 
                'active' => $active, 'code' => $code);
                $user = new Model_user(1);
                $user->register($data);
                if ($conf->email->validation) {
                    $sender = new Zend_Mail();
                    $sender->addTo($post['email'])
                        ->setFrom(WEBMAIL, SITO)
                        ->setBodyHtml($messRegisterMail)
                        ->setBodyText($messRegisterMail)
                        ->setSubject($this->t->_("Conferma E-mail di registrazione"))
                        ->send();
                }
            } else {
                $this->view->type = 2;
                $this->view->form->populate($_POST);
            }
        }
    }
=======
>>>>>>> 67759326d774f3784664dea8eeece2e45eb40db1

	public function init()
	{
	}

	public function indexAction()
	{
		$form = new Form_Register();
		$form->setAction($this->view->url(array('controller' => 'reg')));
		$this->view->form = $form;
		if ($this->getRequest()->isPost()) {
			$this->view->send = true;
			if ($form->isValid($_POST)) {
				$this->view->type = 1;
				$this->view->text = $this->view->t->_("registrazione avvenuta con successo");
				$post = $form->getValues();
				$conf = Zend_Registry::get("config");
				$code=$this->genrandpass();
				$cryptcode = sha1($code);
				$active=($conf->email->validation ? 0 : 1 );
				$data = array('username' => $post['username'],
						'password' => sha1($post['password']), 'email' => $post['email'],
						'active' => $active, 'code' => $cryptcode);
				Model_User::register($data);
				if ($conf->email->validation) {
					include_once APPLICATION_PATH.'/language/email.php';
					$locale=$this->_t->getLocale();
					$sender = new Zend_Mail();
					$sender->addTo($post['email'])
					->setFrom($conf->webmail,$conf->site)
					->setBodyHtml(
							str_replace('{link}', $conf->url.$this->view->baseUrl('reg/active/code/'.$code),
									str_replace('{user}', $post['username'], $message[$locale]['html'])))
									->setBodyText(
											str_replace('{link}', $conf->url.$this->view->baseUrl('reg/active/code/'.$code),
													str_replace('{user}', $post['username'], $message[$locale]['text'])))
													->setSubject($message[$locale]['obj'])
													->send();
				}
			} else {
				$this->view->type = 2;
				$this->view->form->populate($_POST);
			}
		}
	}

	public function ctrlAction()
	{	
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		header("Content-type: application/json");
		$username=$_POST['username'];
		$email=$_POST['email'];
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
		echo json_encode($bool);
	}
	public function activeAction() {
		$code=$this->_getParam('code');
		$code=$code ? sha1($code):null;
		$user=new Model_User(array('code'=>$code));
		$this->_log->debug(print_r($user->data,true));
		if ($user->data && ($user->data['code_time']+86400)<time()) {
			$auth=Zend_Auth::getInstance();
			$data=new stdClass();
			$data->id=$user->data['id'];
			$data->username=$user->data['username'];
			$auth->getStorage()->write($data);
			$this->view->text=$this->_t->_("SUC_ACTIV");
			$user->updateU(array('active'=>1,'code'=>''));
		}
		else {
			$this->view->text=$this->_t->_("ERR_ACTIV");
			$this->view->type=2;
		}
	}
	public function resendAction() {
		$code=$this->_getParam('code');
		$user=Model_User::getInstance();
		include_once APPLICATION_PATH.'/language/email.php';
		$locale=$this->_t->getLocale();
		if ($user->data) {
			$this->view->email=$user->data['email'];
			if ($this->getRequest()->isPost()) {
				$valid=new Zend_Validate();
				$valid->addValidator(new Zend_Validate_EmailAddress())->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => USERS_TABLE, 'field' => 'email')));
				if ($valid->isValid($_POST['email']) || $_POST['email']==$user->data['email']) {
					$code=$this->genrandpass();
					$user->updateU(array('email'=>$_POST['email'],'code'=>sha1($code)));
					$conf = Zend_Registry::get("config");
					if ($conf->email->active) {
						$sender = new Zend_Mail();
						$sender->addTo($_POST['email'])
						->setFrom($conf->webmail,$conf->site)
						->setBodyHtml(
								str_replace('{link}', $conf->url.$this->view->baseUrl('reg/active/code/'.$code),
										str_replace('{user}', $user->data['username'], $message[$locale]['reg']['html'])))
										->setBodyText(
												str_replace('{link}', $conf->url.$this->view->baseUrl('reg/active/code/'.$code),
														str_replace('{user}', $user->data['username'], $message[$locale]['reg']['text'])))
														->setSubject($message[$locale]['reg']['obj'])
														->send();
					}else
						$this->view->text=str_replace('{link}', $conf->url.$this->view->baseUrl('reg/active/code/'.$code),
								str_replace('{user}', $user->data['username'], $message[$locale]['reg']['html']));
					$this->view->text=$this->_t->_("CTRL_MAIL");
					$this->view->success=true;
				}
				else {
					$this->view->error=true;
					$this->view->text=$valid->getMessages();
				}
			}
		}
		else {
			$this->view->error=true;
			$this->view->text=$this->_t->_('ERRORE');
		}
	}
	/**
	 * generate random string 8 char lenght
	 * @return string
	 */
	private function genrandpass() {
		$code = "";
		for ($i = 0; $i < 8; $i ++) {
			$code .= (rand(0, 1) ? chr(rand(65, 122)) : rand(0, 9));
		}
		return $code;
	}
}

