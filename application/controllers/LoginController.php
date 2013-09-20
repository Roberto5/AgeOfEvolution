<?php
class LoginController extends Zend_Controller_Action
{
	public function init () {
	}
	public function indexAction ()
	{
		$form = new Form_LoginForm();
		$form->setAction($this->view->url(array('controller' => 'login',
				'action' => 'index')));
		$this->view->type = 0;
		$this->view->form = $form;
		if ($this->getRequest()->isPost()) {
			//Se il form è valido, lo processiamo
			$this->_helper->layout->setLayout("ajax");
			$this->view->layout()->x=300;
			$this->view->layout()->y='auto';
			$this->view->layout()->button="Ok";
			//$this->_helper->viewRenderer->setNoRender(true);
			if ($form->isValid($_POST)) {
				//recuperiamo i dati così .....
				$user = $this->getRequest()->getParam('username');
				$password = $this->getRequest()->getParam('password');
				$auth = Zend_Auth::getInstance();
				$adapter = new Zend_Auth_Adapter_DbTable(
						Zend_Db_Table::getDefaultAdapter());
				$adapter->setTableName("site_users")
				->setIdentityColumn('username')
				->setCredentialColumn('password')
				->setCredentialTreatment('sha1(?)');
				$adapter->setIdentity($user);
				$adapter->setCredential($password);
				$result = $adapter->authenticate();
				if ($result->isValid()) {
					$user = $adapter->getResultRowObject(array('ID',
							'username'));
					$auth->getStorage()->write(
							$user);
					//$this->_helper->layout->data->user=$adapter->getResultRowObject();
					//Zend_Layout::getMvcInstance()->getLayout();
					$this->view->layout()->data = 1;
					$this->view->mess = "[SUCCESS]";
				} else {
					$this->view->layout()->data = 2;
					switch ($result->getCode()) {
						case Zend_Auth_Result::FAILURE:
							$this->view->mess = "[FAILURE]";
							break;
						case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
							$this->view->mess = "[PASS_ERR]";
							break;
						case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
							$this->view->mess = "[USER_NOT_FOUND]";
							break;
						case Zend_Auth_Result::FAILURE_UNCATEGORIZED:
							$this->view->mess = $result->getMessages();
							break;
					}
				}
			}
			else {
				$this->view->layout()->data= 0;
				$this->view->mess = "[ERR_FORM]";
			}
		}
	}
	function logoutAction ()
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
			$auth->clearIdentity();
		$this->_helper->layout->setLayout('ajax');
	}
	public function recoverAction() {
		$conf=Zend_Registry::get('config');
		$code=$this->_getParam('code');
		if ($this->getRequest()->isPost()) {
			$v=new Zend_Validate();
			$v->addValidator(new Zend_Validate_EmailAddress());
			$v->addValidator(new Zend_Validate_Db_RecordExists(array('table'=>USERS_TABLE,'field'=>'email')));
			$this->view->type=2;
			if ($v->isValid($_POST['email'])) {
				$user=new Model_User(array('email'=>$_POST['email']));
				$code=$this->genrandpass();
				$user->updateU(array('code'=>sha1($code)));
				$this->view->type=1;
				if (!$conf->local) {
					include_once APPLICATION_PATH.'/language/email.php';
					$locale=$this->_t->getLocale();
					$sender = new Zend_Mail();
					$sender->addTo($_POST['email'])
					->setFrom($conf->webmail, $conf->site)
					->setBodyHtml(
							str_replace('{link}', $conf->url.$this->view->baseUrl('login/recover/code/'.$code),
									$message[$locale]['rec']['html']))
									->setBodyText(
											str_replace('{link}', $conf->url.$this->view->baseUrl('login/recover/code/'.$code),
													$message[$locale]['rec']['text']))
													->setSubject($message[$locale]['rec']['obj'])
													->send();
					$this->view->text=$this->_t->_('CTRL_MAIL');

				}
				else $this->view->text=str_replace('{link}', $conf->url.$this->view->baseUrl('login/recover/code/'.$code),
						$message[$locale]['rec']['html']);
			}
			else {
				$this->view->text=$v->getMessages();

			}
		}
		elseif ($code) {
			$code=sha1($code);
			$this->view->type=1;
			$user=new Model_User(array('code'=>$code));
			if ($user->data && ($user->data['code_time']+86400)<time()) {
				$pass=$this->genrandpass();
				$user->updateU(array('code'=>'','password'=>sha1($pass)));
				if (!$conf->local) {
					include_once APPLICATION_PATH.'/language/email.php';
					$locale=$this->_t->getLocale();
					$sender = new Zend_Mail();
					$sender->addTo($user->data['email'])
					->setFrom($conf->webmail,$conf->site)
					->setBodyHtml(
							str_replace(array('{pass}','{user}'), array($pass,$user->data['username']),
									$message[$locale]['pass']['html']))
									->setBodyText(
											str_replace(array('{pass}','{user}'), array($pass,$user->data['username']),$message[$locale]['pass']['text']))
											->setSubject($message[$locale]['pass']['obj'])
											->send();
					$this->view->text=$this->_t->_('CTRL_MAIL');
				}
				else $this->view->text=str_replace(array('{pass}','{user}'), array($pass,$user->data['username']),
						$message[$locale]['pass']['html']);
			}
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

