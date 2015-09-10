<?php
class AjaxController extends Zend_Controller_Action {

	/**
	 * validatore alnum
	 * @var Zend_Validate_Alnum
	 */
	private $alnum;

	/**
	 * DB adapter
	 * @var Zend_Db_Adapter_Pdo_Mssql
	 */
	private $db;

	/**
	 * traduttore
	 * @var Zend_Translate
	 */
	private $t;

	/**
	 * email validator
	 * @var Zend_Validate_EmailAddress
	 */
	private $email;

	public function init() {
		$this->alnum=new Zend_Validate_Alnum();
		$this->db=Zend_Db_Table::getDefaultAdapter();
		$this->t=Zend_Registry::get("translate");
		$this->view->text="";
		$layout=Zend_Layout::getMvcInstance();
		$layout->disableLayout();
		$this->email=new Zend_Validate_EmailAddress();
	}

	public function indexAction() {
		$this->view->text=$this->t->_("chiamata ajax non supportata");
	}

	public function regAction() {
		
		$cerca=addslashes($_POST['cerca']);
		$bool=false;
		$valore=addslashes($_POST['valore']);
		$validator=($cerca == "user_mail" ? $this->email : $this->alnum);
		if ($validator->isValid($valore)&&in_array($cerca, array('user_mail','username'))) {
			$num=$this->db->fetchOne(
					"SELECT count(*) 
            FROM `" . USERS_TABLE . "` 
            WHERE `" . $cerca . "`='" . $valore . "'");
			if ($num) {
				$bool=true;
			}
		}
		else
			$bool=true;
		$this->view->text=json_encode($bool);
	}

	public function sendmailAction() {
		global $messRegisterMail;
		$mail=$_POST['mail'];
		$id=$_POST['id'];
		$r=$this->db->fetchRow(
				"SELECT * FROM `" . USERS_TABLE . "` WHERE `ID`='" . $id . "'");
		if ($this->email->isValid($mail)) {
			$messRegisterMail=str_replace("{user}", $r['username'], 
					$messRegisterMail);
			$messRegisterMail=str_replace("{code}", $r['user_code'], 
					$messRegisterMail);
			
			$this->db->update(USERS_TABLE, 
					array('user_mail',$mail,"`ID`='" . $id . "'"));
			$email=new Zend_Mail();
			$email->setFrom(WEBMAIL, SITO)
				->setSubject($this->t->_("Conferma E-mail di registrazione"))
				->addTo($mail)
				->setBodyHtml($messRegisterMail)
				->setBodyText($messRegisterMail)
				->send();
			$bool=true;
		}
		else
			$bool=false;
		$this->view->text.=json_encode($bool);
	}

	

	

	
	
	
	
	
}

