<?php

class Admin_FaqController extends Zend_Controller_Action
{
	/**
     * @var Zend_Db_Adapter_Abstract
     *
    private $_db;// */
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->faq=$this->_db->fetchAll("SELECT * FROM `".FAQ_TABLE."`");
    }

    public function modquestAction()
    {
        $id = (int) $_POST['id'];
        $text = $_POST['reply'];
        $title=htmlentities($_POST['question'],ENT_QUOTES);
        $this->_db->query("UPDATE `" . FAQ_TABLE . "` SET `question`='$title',`reply`='$text' WHERE `id`='$id'");
    }

    public function addquestAction()
    {
        $text = $_POST['reply'];
        $title=htmlentities($_POST['question'],ENT_QUOTES);
        $this->_db->query("INSERT INTO `" . FAQ_TABLE . "` SET `question`='$title',`reply`='$text'");
        $this->_helper->layout()->data=true;
    }

    public function delquestAction()
    {
        $id = (int) $_POST['id'];
        $this->_db->query("DELETE FROM `" . FAQ_TABLE . "` WHERE `id`='$id'");
        
    }


}







