<?php
class Form_Register extends Zend_Form
{
    public function init ()
    {
    	/**
    	 * traduttore
    	 * @var Zend_Translate
    	 */
        $t = Zend_Registry::get("translate");
        $this->setAction('./reg/')
            ->setMethod("post")
            ->setAttrib("id", "registration_form");
        $this->setDefaultTranslator($t);
        // username
        $user = $this->createElement("text", "username");
        $user->setLabel($t->_("Username"));
        $user->setRequired(true)
            ->addFilter('StringTrim')
            ->addValidator("alnum")
            ->addValidator("StringLength", null, array('max' => 30, 'min' => 4))
            ->addValidator("Db_NoRecordExists", null, 
        array('table' => USERS_TABLE, 'field' => 'username'));
        
        $attribs = array('size' => '16'
        		,'maxlength' => '30'
        		,'required'=>'required'
        );
        $user->setAttribs($attribs);
        $user->getValidator("alnum")->setMessage(
        'Il nome utente deve contenere solo lettere e numeri');
        $user->getValidator("StringLength")->setMessage("il nome utente deve avere una lunghezza tra i 4 ai 30 caratteri");
        $user->getValidator("Db_NoRecordExists", null, 
        array('table' => USERS_TABLE, 'field' => 'username'))->setMessage("nome utente in uso");
        $this->addElement($user);
        //password
        $pass = $this->createElement("password", "password");
        $pass->setLabel($t->_("Password"));
        $pass->setRequired(true)
            ->addFilter('StringTrim')
            ->addValidator("alnum")
            ->addValidator("StringLength", null, array('min' => 8));
        $pass->getValidator("alnum")->setMessage(
        'La password deve contenere solo lettere e numeri');
        $pass->getValidator("StringLength")->setMessage("la password deve essere di almeno 8 caratteri");
        $attribs = array('size' => '16','class'=>'password','required'=>'required');
        $pass->setAttribs($attribs);
        $this->addElement($pass);
        //conferma
        $pass2 = $this->createElement("password", "password2");
        $pass2->setLabel("Conferma password");
        $pass2->setRequired(true)
            ->addFilter('StringTrim')
            ->addValidator(new Zend_Validate_Identical($_POST['password']));
        $attribs = array('size' => '16','class'=>'password','required'=>'required');
        $pass2->setAttribs($attribs);
        $this->addElement($pass2);
        //email
        $mail = $this->createElement("email", "email");
        $mail->setLabel($t->_("Email"));
        $mail->setRequired(true)
            ->addFilter("StringTrim")
            ->addValidator("EmailAddress")
            ->addValidator("Db_NoRecordExists", null, 
        array('table' => USERS_TABLE, 'field' => 'email'));
        $mail->getValidator("EmailAddress")->setMessage("Email non valida");
        $mail->getValidator("Db_NoRecordExists")->setMessage("Email in uso");
        $attribs = array('size' => '16','required'=>'required');
        $mail->setAttribs($attribs);
        $this->addElement($mail);
        $conf=Zend_Registry::get("config");
        if ((!$conf->local)||($_GET['test'])) {
        	$this->addElement('captcha', 'captcha', 
        array('label' => 'controllo anti-bot', 'required' => true, 
        'captcha' => array(
        'pubkey' => '6LeZUsISAAAAABXfDdeAHWcm3VuyRno0V4h3cGDr', 
        'privkey' => '6LeZUsISAAAAAOZITe6cRWgSXKG6X1GfM-1t8_Xo', 
        'captcha' => 'reCaptcha')));
        }
        
        //submit
        $this->addElement('submit', 'submit', 
        	array('label' => $t->_('Registra')
        	)
        );
    }
}

