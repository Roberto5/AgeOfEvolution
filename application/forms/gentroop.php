<?php

class Form_gentroop extends Zend_Form
{

    public function init()
    {
    	$this->setName("gentroop");
    	
    	$number=$this->createElement("text", "number")
    		->addFilter("StringTrim")
        	->addFilter("int")
        	->addValidator("digits")
        	->setAttribs(array('size'=>'4','class'=>'number'));
    	
    	$type=new Zend_Form_Element_Select("type");
    	for ($i = 0; $i < TOT_TYPE_TROOPS;$i++)
    		$type->addMultiOption($i,$i);
    	
    		$type->setRequired(true)
        	->addFilter("StringTrim")
        	->addFilter("int")
        	->addValidator("digits")
        	->addValidator("LessThan", null, array('max' => TOT_TYPE_TROOPS));
        	
        $this->addElements(array($number,$type));
    	$this->addElement("submit","crea");
    }


}

