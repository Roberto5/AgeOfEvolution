<?php
class S1_ReportController extends Zend_Controller_Action
{
    /**
     * @var Model_civilta
     *
     *
     */
    private $civ = null;
    public function init ()
    {
        $this->civ = Model_civilta::getInstance();
        $this->user = Zend_Auth::getInstance()->getIdentity();
    }
    public function indexAction ()
    {
        $page = $this->getRequest()->getParam("page", 1);
        $this->view->refresh=$this->getRequest()->getParam("ref",false);
        $result = Model_report::getReport($this->civ->cid);
        $paginator = Zend_Paginator::factory($result);
        //$paginator->setItemCountPerPage($gap);
        $paginator->setCurrentPageNumber($page);
        $this->view->row = $paginator;
    }
    public function seeAction ()
    {
        $id = $this->getRequest()->getParam("id", 0);
        $this->view->row = Model_report::getcontent($id);
        Zend_Registry::get("log")->log($this->view->row, Zend_Log::DEBUG);
    }
    public function deleteAction ()
    {
        $ids = $_POST['ids'];
        $id = $this->getRequest()->getParam("id", false);
        if ($id)
            Model_report::deleteReport($id);
        else
            Model_report::deleteReport($ids);
        if (!$_POST['ajax']) $this->_helper->redirector("index");
    }
    public function readAction ()
    {
    	$ids = array();
        if ($this->getRequest()->getParam("all", false)) {
            $cid = Model_civilta::getInstance()->cid;
            $uid = $this->user->user_id;
            $temp = $this->_db->fetchCol(
            "SELECT `id` FROM `" . REPORT_TABLE ."`
        		WHERE `civ`='$cid' AND `id` <> ALL (SELECT `id` FROM `" .
             REPORT_READ_TABLE . "` WHERE `user`='$uid')");
        	foreach ($temp as $value) {
        		$ids[]="('" . intval($value) . "','" . $this->user->user_id .
                     "')";
        	}
        } else {
        	$temp=$_POST['ids'];
        	foreach ($temp as $value) {
        		$ids[]="('" . intval($value) . "','" . $this->user->user_id .
                     "')";
        	}
        }
        if (count($ids) > 0) {
            $query = "INSERT IGNORE INTO `" . REPORT_READ_TABLE .
             "` (`id`, `user`) VALUES " . implode(",", $ids);
            try {
                $this->_db->query($query);
            } catch (Zend_Db_Exception $e) {
                $this->_log->warn($e);
            }
        }
        if (!$_POST['ajax']) $this->_helper->redirector("index");
    }
}







