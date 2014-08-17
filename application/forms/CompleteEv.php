<?php

class Form_CompleteEv extends Zend_Form
{

    public function init()
    {
    	$this->setName("rid")->setMethod("post");
    	$this->setDefaultTranslator(Zend_Registry::get("translate"));
        $rid=$this->createElement("text","rid",array('value'=>'50','size'=>'2','maxlength'=>'3'));
        $this->setDecorators(array(
            'FormElements',
            array('Description', array('tag' => 'span')),
            array('HtmlTag', array('tag' => 'div')),
            'Form'
        ));
        $rid->setLabel("riduci i tempi del...");
        $rid->setDecorators(array(
        'ViewHelper',
        'Errors',
        array('Description', array('tag' => 'span', 'class' => 'quiet')),
        array(array('data' => 'HtmlTag'),
        array('tag' => 'span', 'class' => 'element')),
        array('Label', array('tag' => 'span')),
        array(array('row' => 'HtmlTag'), array('tag' => 'span'))
    ));
    
        $rid->setRequired(true)
        	->addFilter("StringTrim")
        	->addFilter("int")
        	->addValidator("digits")
        	->addValidator("LessThan", null, array('max' => 101));
        $this->addElement($rid);
        $this->addElement("submit","riduci");
        $this->getElement("riduci")->setDecorators(array(
        'ViewHelper',
        'Errors',
        array('Description', array('tag' => 'span', 'class' => 'quiet')),
        array(array('data' => 'HtmlTag'),
        array('tag' => 'span', 'class' => 'element')),
        array(array('row' => 'HtmlTag'), array('tag' => 'span'))
    ));
        /*<form action="debug.php"><?php echo $t->_("riduci i tempi del...") ; ?>
    <input type="hidden" name="action" value="rid"/>
    <input name="rid" value="50" size="2" maxlength="3"/> 
    <input type="submit"/>%<br/><br/>
    </form>*/
    }


}

