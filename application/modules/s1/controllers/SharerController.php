<?php
class S1_SharerController extends Zend_Controller_Action
{
    /**
     * @var Model_civilta
     *
     *
     *
     */
    private $civ = null;
    /**
     * 
     * @var Zend_Db_Adapter_Abstract
     */
    private $db;
    public function init ()
    {
        $this->civ = Model_civilta::getInstance();
        $this->t = Zend_Registry::get("translate");
        $this->db = Zend_Db_Table::getDefaultAdapter();
    }
    public function indexAction ()
    {
        $this->view->token = sha1(auth());
        Zend_Auth::getInstance()->getStorage()->set("tokenSh", 
        $this->view->token);
        $this->view->sharer = $this->civ->sharer;
        $option = null;
        if ($this->civ->status == 3) {
            $option = array(
            array('name' => 'read_mess', 'abbr' => 'R.M.', 
            'title' => $this->t->_('leggere i messaggi')), 
            array('name' => 'write_mess', 'abbr' => 'W.M', 
            'title' => $this->t->_('scrivere i messaggi')), 
            array('name' => 'del_mess', 'abbr' => 'D.M', 
            'title' => $this->t->_('cancellare i messaggi')), 
            array('name' => 'read_report', 'abbr' => 'R.R', 
            'title' => $this->t->_('leggere i report')), 
            array('name' => 'del_report', 'abbr' => 'D.R', 
            'title' => $this->t->_('cancellare i report')), 
            array('name' => 'send_troops', 'abbr' => 'S.T', 
            'title' => $this->t->_('inviare truppe')), 
            array('name' => 'send_attack', 'abbr' => 'S.A', 
            'title' => $this->t->_('inviare attacchi')), 
            array('name' => 'market', 'abbr' => 'S.M', 
            'title' => $this->t->_('inviare mercanti')), 
            array('name' => 'build', 'abbr' => 'B', 
            'title' => $this->t->_('costruire')), 
            array('name' => 'option', 'abbr' => 'O', 
            'title' => $this->t->_('gestire gli sharer')));
        }
        //@todo estarre le opzioni dalle acl (implementare prima le acl per gli sharer)
        $this->view->option = $option;
    }
    public function permAction ()
    {
        // @todo implementare acl sharer
    	$res = token_ctrl($this->getRequest()->getParams());
        if ($res['and']) {
        	
        }
        $this->_helper->redirector("index");
    }
    public function deleteAction ()
    {
        $res = token_ctrl($this->getRequest()->getParams());
        if ($res['and']) {
            $uid = (int) $this->getRequest()->getParam("uid");
            $this->db->query(
            "DELETE FROM `" . RELATION_USER_CIV_TABLE . "` WHERE `user_id`='" .
             $uid . "' AND `server`='" . SERVER . "'");
        }
        $this->_helper->redirector("index");
    }
    public function addAction ()
    {
    	$uid = (int) $this->getRequest()->getParam("uid");
    	$res = token_ctrl($this->getRequest()->getParams());
        if ($res['and']) {
        $this->db->query("UPDATE `" . RELATION_USER_CIV_TABLE . "` SET `status`='2' WHERE `user_id`='" . $uid . "' AND `server`='" . SERVER . "'");
        }
        $this->_helper->redirector("index");
    }
}







