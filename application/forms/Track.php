<?php

class Form_Track extends Zend_Form
{

    public function init()
    {
    	$db=Zend_Db_Table::getDefaultAdapter();
    	$temp=$db->fetchCol("SELECT `id` FROM `site_track_cat`");
    	$category=array();
    	foreach ($temp as $value) {
    		$category[$value]=$value;
    	}
        $type=new Zend_Form_Element_Select("type");
        $type->addMultiOptions(array('bug'=>'bug','idea'=>'idea','nolike'=>'nolike'));
        $type->setRequired(true);
        $tag=new Zend_Form_Element_Text("tag");
        $tag->addValidator(new Zend_Validate_Regex("/[\w,]{0,30}/"));
        $tag->getValidator('Regex')->setMessages(array('regexNotMatch'=>'tag errati'));
        $cat=new Zend_Form_Element_Select("category");
        $cat->setRequired(true)->addMultiOptions($category);
        $des=new Zend_Form_Element_Textarea("description");
        $html=new Zend_Filter_HtmlEntities();
        $des->setRequired(true)->addFilter("HtmlEntities",array('quotestyle'
=> ENT_QUOTES));
		$link=new Zend_Form_Element_Text("screen");
		$link->addValidator(new Zend_Validate_Regex("/(http:\/\/)*(www\.)*(\w*\.)*\w+\.[\w]{2,4}+(\/.*)*/"));
		$link->getValidator("Regex")->setMessages(array('regexNotMatch'=>'link errato'));
		$this->addElements(array($type,$tag,$cat,$des,$link));
    }


}

