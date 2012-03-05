<?php

class Form_Marketsell extends Zend_Form
{

    public function init()
    {
    	$civ=Model_civilta::getInstance();
    	$now=$civ->getCurrentVillage();
    	$reso=array(0,$civ->village->data[$now]['resource_2'],$civ->village->data[$now]['resource_3']);
        $res=$this->createElement("text", "res");
        $rap=$this->createElement("text", "rap");
        $type=new Zend_Form_Element_Select("type");
        $type->addMultiOptions(array('1'=>'1','2'=>'2'));
        $res->setRequired(true)->addValidator("digits")->addValidator("LessThan",null,array('max'=>$reso[$_POST['type']]));
        $res->getValidator("LessThan")->setMessage("risorse insufficenti");
        $rap->setRequired(true)->addValidator("float",null,array('locale' => 'en'))
        	->addValidator("LessThan",null,array('max'=>'2.001'))
        	->addValidator("GreaterThan",null,array('min'=>'0.499'));
        $rap->getValidator("LessThan")->setMessage("il rapporto non deve superare il 2");
        $rap->getValidator("GreaterThan")->setMessage("il rapporto non deve essere minore di 0.5");
        $this->addElements(array($res,$rap,$type));
    }


}

