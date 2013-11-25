<?php

class Form_Marketsend extends Zend_Form
{

    public function init()
    {
    	$civ=Model_civilta::getInstance();
    	$now=$civ->getCurrentVillage();
    	$reso=array($civ->village->data[$now]['resource_1'],$civ->village->data[$now]['resource_2'],$civ->village->data[$now]['resource_3']);
    	Zend_Registry::get("log")->debug($reso);
    	for ($i = 0; $i < 3; $i++) {
    		$res=$this->createElement("text", "res".($i+1));
        	$res->addFilter("StringTrim")->addValidator("digits")
        		->addValidator("LessThan",null,array('max'=>intval($reso[$i])));
        	$res->getValidator("LessThan")->setMessage("risorse insufficenti");
        	$this->addElement($res);
    	}
        $village=new Zend_Form_Element_Select("village");
        $list=$civ->village_list;
        foreach ($list as $key => $value) 
            if ($key != $civ->getCurrentVillage())
                $options[$key]=$key;
        $village->addMultiOptions($options);
        $village->addValidator("Db_RecordExists",null,
        	array('table' => SERVER.'_village', 'field' => 'id','exclude'=>"`civ_id`='".$civ->cid."'"))->setRequired(true);
        $village->getValidator("Db_RecordExists")->setMessage("seleziona uno dei tuoi villaggi!");
        $this->addElement($village);
    }


}
?>
