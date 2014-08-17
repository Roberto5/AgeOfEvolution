<?php

class Form_Regciv extends Zend_Form
{

    public function init()
    {
    	$this->setName("civ")->setMethod("post");
    	$this->setAttrib("name", "civ");
    	$this->setDefaultTranslator(Zend_Registry::get("translate"));
    	$name=$this->createElement("text", "name");
    	$name->setLabel("Nome");
    	$name->addFilter("StringTrim")->setRequired(true)->addValidator("alnum");
    	$name->addValidator("StringLength",false,array('max' => 30, 'min' => 4)) 
    	->addValidator("Db_NoRecordExists", null, 
        array('table' => CIV_TABLE, 'field' => 'civ_name'));
        $attribs=array(
    		'size'=>'20',
    		'maxlength'=>'30'
    	);
    	$name->setAttribs($attribs);
    	$name->getValidator("alnum")->setMessage(
        'Il nome utente deve contenere solo lettere e numeri');
    	$name->getValidator("StringLength")->setMessage("il nome utente deve avere una lunghezza tra i 4 ai 30 caratteri");
        $name->getValidator("Db_NoRecordExists")->setMessage("nome utente in uso");
        $this->addElement($name);
        
        $agg=$this->createElement("text", "agg");
        $agg->setLabel("Aggettivo");
        $agg->addFilter("StringTrim")->setRequired(true)->addValidator("alnum");
        $agg->addValidator("StringLength",false,array('max' => 30));
        $agg->setAttribs($attribs);
        
        $this->addElement($agg);
        
        $sector=new Zend_Form_Element_Radio("sector");//$this->createElement("radio", "sector");
        
        $sector->setMultiOptions(array('1'=>'[NORTH]/[EAST] (+/+)',
        '2'=>'[NORTH]/[WEST][ (-/+)',
        '3'=>'[SOUTH]/[WEST] (-/-)',
        '4'=>'[SOUTH]/[EAST] (+/-)',
        '5'=>'[RANDOM]',
        '6'=>'[CHOOSE]')
        );
        $sector->setValue(5);
        $this->addElement($sector);
        
        $x=$this->createElement('number', 'cx');
        $x->setLabel('x');
        $max=intval(MAX_X/2);
        $x->setAttribs(array('min'=>-$max,'max'=>$max));
        $x->addValidator('Between',null,array('min'=>-$max,'max'=>$max));
        $x->setValue(0);
        $y=$this->createElement('number', 'cy');
        $y->setLabel('y');
        $y->setValue(0);
        $max=intval(MAX_Y/2);
        $y->setAttribs(array('min'=>-$max,'max'=>$max));
        $y->addValidator('Between',null,array('min'=>-$max,'max'=>$max));
        $max=intval(MAX_Y/2);
        $this->addElements(array($x,$y));
        $button=$this->createElement("button", "submit");
        $button->setAttrib("onclick","ev.createciv();");
        $button->setLabel("crea");
        $view=$this->createElement('button', 'view');
        $view->setLabel('[VIEW] [MAP]');
        $view->setAttrib("onclick","$('#modalwindows').dialog('close');");
        $this->addElement($view);
        $this->addElement($button);
    }


}

