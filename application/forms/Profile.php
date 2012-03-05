<?php

class Form_Profile extends Zend_Form
{
    public function init()
    {
    	$id=Zend_Auth::getInstance()->getIdentity()->user_id;
    	$this->setDefaultTranslator(Zend_Registry::get("translate"));
    	$this->setMethod("post");
        $pass=$this->createElement("password", "password");
        $pass->setLabel("Vecchia password");
        $pass->setRequired(true)
        	->addFilter("StringTrim")
        	->addValidator("alnum")
        	->addValidator("Db_RecordExists", null, 
        array('table' => USERS_TABLE, 'field' => 'user_pass','exclude'=>"`ID`='".$id."'"));
        $newpass=$this->createElement("password", "newpass");
        $newpass->setLabel("nuova password");
        $newpass->setAttrib("class", "password");
        $newpass->setRequired(false)
            ->addFilter('StringTrim')
            ->addValidator("alnum")
            ->addValidator("StringLength", null, array('min' => 8));
        $newpass2=$this->createElement("password", "newpass2");
        $newpass2->addFilter('StringTrim')->addValidator(new Zend_Validate_Identical($_POST['newpass']));
        $newpass2->setLabel("ripeti");
        $newpass2->setAttrib("class", "password");
        $this->addElements(array($pass,$newpass,$newpass2));
    }
}

