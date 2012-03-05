<?php

class Form_Mess extends Zend_Form
{
    public function init()
    {
    	/*$this->setDecorators(array(
            'FormElements',
            array('Description', array('tag' => 'div')),
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));
        
        $eldecorator=array(
    'ViewHelper',
    'Errors',
    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
    array('label', array('tag' => 'td')),
    array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
);
		$button=array(
    'ViewHelper',
    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
    array(array('label' => 'HtmlTag'), array('tag' => 'td', 'placement' => 'prepend')),
    array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
);*/
    	$dest=$this->createElement("text", "destinatario");
        $dest->setLabel("a:");
        $this->setDefaultTranslator(Zend_Registry::get("translate"));
        $dest->setRequired(true);
        $dest->addFilter("StringTrim");
        $dest->addValidator("alnum");
        $dest->addValidator("Db_RecordExists", null, 
        array('table' => CIV_TABLE, 'field' => 'civ_name'));
        $attribs=array(
    		'size'=>'20',
    		'maxlength'=>'30'
    	);
    	$dest->setAttribs($attribs);
    	$dest->getValidator("alnum")->setMessage(
        'Il nome della civiltà deve contenere solo lettere e numeri');
    	$dest->getValidator("Db_RecordExists")->setMessage("nome civiltà inesistente");
    	//$dest->setDecorators($eldecorator);
    	$ogg=$this->createElement("text", "oggetto");
    	$ogg->setRequired(false);
        $ogg->addFilter("StringTrim")->addFilter("HtmlEntities")
        	->addValidator("StringLength",false,array('max' => 40));
        	//$ogg->setDecorators($eldecorator);
        $attribs=array(
    		'size'=>'20',
    		'maxlength'=>'40'
    	);
    	
    	$ogg->setAttribs($attribs);
    	$ogg->setLabel("Oggetto:");
    	
    	$ogg->getValidator("StringLength")->setMessage("l'oggetto deve avere una lunghezza minore di 30 caratteri");
    	
    	$text=$this->createElement("textarea", "messaggio");
    	$text->setRequired(true)->addFilter("HtmlEntities",array('quotestyle'
=> ENT_QUOTES));
    	//$text->setDecorators($eldecorator);
    	$submit=$this->createElement("submit", "submit");
    	$submit->setLabel("invia");
    	//$submit->setDecorators($button);
    	$this->addElements(array($dest,$ogg,$text,$submit));
    }


}

