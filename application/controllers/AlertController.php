<?php

class AlertController extends Zend_Controller_Action
{
	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	var $db;
    public function init()
    {
        $this->db=Zend_Db_Table::getDefaultAdapter();
    }

    public function indexAction()
    {
    	
    }
	public function readAction()
	{
		Zend_Layout::getMvcInstance()->disableLayout();
		$id = (int) $_POST['id'];
		$uid=Zend_Auth::getInstance()->getIdentity()->user_id;
        $this->db->query("INSERT INTO `" . ALERTS_READ . "` SET `user`='" . $uid . "' , `id`='" . $id . "'");
	}

}

