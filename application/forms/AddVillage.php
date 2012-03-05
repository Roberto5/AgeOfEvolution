<?php

class Form_AddVillage extends Zend_Form
{

    public function init()
    {
    	$this->setName("addvillage");
    	
    	$x=$this->createElement("text", "x")
    		->addFilter("StringTrim")
        	->addFilter("int")
        	->addValidator("digits")
        	->addValidator("LessThan", null, array('max' => MAX_X))
        	->setLabel("x")
        	->setAttribs(array('size'=>'2','maxlength'=>'4'));
    	
    	$y=$this->createElement("text", "y")
    		->addFilter("StringTrim")
        	->addFilter("int")
        	->addValidator("digits")
        	->addValidator("LessThan", null, array('max' => MAX_Y))
        	->setLabel("y")
        	->setAttribs(array('size'=>'2','maxlength'=>'4'));
    	
    	$name=$this->createElement("text", "name")
    		->addFilter("StringTrim")
        	->addValidator("alnum",null,array('allowWhiteSpace'=>true))
        	->addValidator("StringLength", null, array('max' => 30))
        	->setLabel("name")->setValue('Nuovo Villaggio')
        	->setAttribs(array('size'=>'20','maxlength'=>'30'));
    	
        $this->addElements(array($x,$y,$name));
    	$this->addElement("submit","crea");
        /* <form action="debug.php"><?php echo $t->_("crea villaggio alle coordinate ") ; ?>
    <input type="hidden" name="action" value="vil"/>
    <input name="x" size="3" maxlength="3" />
    <input name="y" size="3" maxlength="3" />
    <?php echo $t->_(' nome '); ?><input name="name" value="nuovo villaggio" />
    <input type="submit"/><br/><br/>
</form> */
    }


}

