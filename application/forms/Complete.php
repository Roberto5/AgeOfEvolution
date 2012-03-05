<?php

class Form_Complete extends Zend_Form
{
    public function init()
    {
    	$decoratorsForm=array(
            'FormElements',
            array('Description', array('tag' => 'span')),
            array('HtmlTag', array('tag' => 'div')),
            'Form'
        );
        $decoratorsEl=array(
        'ViewHelper',
        'Errors',
        array('Description', array('tag' => 'span', 'class' => 'quiet')),
        array(array('data' => 'HtmlTag'),
        array('tag' => 'span', 'class' => 'element')),
        array('Label', array('tag' => 'span')),
        array(array('row' => 'HtmlTag'), array('tag' => 'span'))
    );
    	$this->setName("complete")->setMethod("post");
    	$this->setDefaultTranslator(Zend_Registry::get("translate"));
    	//$this->setDecorators($decoratorsForm);
    	
        $event=new Zend_Form_Element_Select("ev");
        global $event_array;
        for ($i = 0; $i < TOT_EVENT;$i++)
        	$event->addMultiOption($i+1,$event_array[$i]);
        //$event->setDecorators($decoratorsEl);
        
        $event->setRequired(true)
        	->addFilter("StringTrim")
        	->addFilter("int")
        	->addValidator("digits")
        	->addValidator("LessThan", null, array('max' => MAX_EV));
        $this->addElement($event);
        
        $village=new Zend_Form_Element_Radio("vid");
        $village->setRequired(true);
        $village->addMultiOption("this","questo villaggio");
        $village->addMultiOption("all","tutti");
        $village->setSeparator("")->setValue("this");
        //$village->setDecorators($decoratorsEl);
        $this->addElement($village);
        
        $this->addElement("submit","completa");
        $this->getElement("completa");
        /* ->setDecorators(array(
        'ViewHelper',
        'Errors',
        array('Description', array('tag' => 'span', 'class' => 'quiet')),
        array(array('data' => 'HtmlTag'),
        array('tag' => 'span', 'class' => 'element')),
        array(array('row' => 'HtmlTag'), array('tag' => 'span'))
    )) 
    
    <form action="debug.php?action=event" method="post">
    <select name="ev">
<?php 
 {
    echo '<option value="'.($i+1).'">'..'</option>';
}
?>
    </select>
    <input type="submit" /><form>*/
    }


}

