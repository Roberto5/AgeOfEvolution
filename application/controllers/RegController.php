<?php
class RegController extends Zend_Controller_Action
{
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
                $code = md5($code);
                $active=($conf->email->validation ? 0 : 1 );
                //ID 	username 	user_pass 	user_mail 	user_active 	user_code 	des_user
                $data = array('username' => $post['username'], 
                'user_pass' => md5($post['password']), 'user_mail' => $post['email'], 
                'user_active' => $active, 'user_code' => $code);
                $user = new Model_user();
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
}

