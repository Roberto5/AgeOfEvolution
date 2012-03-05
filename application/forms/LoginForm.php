<?php
class Form_LoginForm extends Zend_Form {
	public function init() {
         
		// (1) Impostiamo gli attributi "action", "method" e "id" del form
        $this->setMethod('post')->setAttrib('id', 'loginForm');
 		$t=Zend_Registry::get("translate");
        $this->setDefaultTranslator($t);
                /************************ Textbox nome utente *************************/
                // (2) creiamo un elemento di tipo "text".....
        $user = $this->createElement('text', 'username');
 
                //...gli associamo una etichetta
        $user->setLabel('Nome utente:');
                 
                // (3) impostiamo i filtri e i validatori per questo elemento HTML
        $user->setRequired(TRUE)->addFilter('StringTrim')->addValidator('alnum');
                 
                //Impostiamo un messaggio di errore personalizzato
        $user->getValidator('alnum')->setMessage('Il nome utente deve contenere solo lettere e numeri');
 
        $user->setAttrib('size', 10);
                 
                //Aggiungiamo l'oggetto appena creato nel nostro form
        $this->addElement($user);
 
                /************************ Textbox password *************************/
 
        $password = $this->createElement('password', 'password');
        $password->setLabel('Password:');
        $password->setRequired(TRUE)->addValidator('stringLength', true, array(8))
        ->addValidator("alnum")
        ->addFilter('StringTrim');
        $password->setAttrib('size', 10);
        $password->getValidator('stringLength')->setMessage('La password deve contenere 8 caratteri');
        
        $this->addElement($password);
                 
                //Aggiungiamo al form il pulsante di invio
        $this->addElement('submit', 'submit', array('label' => 'Entra'));
 
	}
}

?>