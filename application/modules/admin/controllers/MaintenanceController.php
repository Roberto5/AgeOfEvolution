<?php
class Admin_MaintenanceController extends Zend_Controller_Action
{
    /**
     * @var Model_params
     *
     */
    private $param = null;
    public function init ()
    {
        if (Zend_Registry::isRegistered("param"))
            $this->param = Zend_Registry::get("param");
        $this->_helper->layout()->nav = array(
        'log' => array('url' => "admin/maintenance/log"));
    }
    public function indexAction ()
    {
        $mode = $this->_request->getParam("mode");
        if ($mode == 'on') {
            $mode = $this->_request->getParam("section", 1);
            $this->param->set("offline", $mode);
        } elseif ($mode == 'off') {
            $this->param->set("offline", 0);
        }
        $this->view->on = $this->param->get("offline");
        $this->view->last=$this->param->get("epla");
    }
    public function restartepAction ()
    {
        $this->param->set("epon", 0);
        sleep(2);
        $this->_log->emerg("restart event processor!");
        $client = new Zend_Http_Client();
        $uri = Zend_Uri_Http::factory(
        "http://ageofevolution.altervista.org/" . $_COOKIE['server'] . "/processing?ep=1");
        $client->setUri($uri);
        try {
            $content = $client->request()->getBody();
            $this->getResource("log")->debug($content);
        } catch (Exception $e) {
            $this->_log->debug($e);
        }
        $this->_helper->redirector("index");
    }
    public function logAction ()
    {
    	$list=array();
        $dp = opendir(APPLICATION_PATH . "/log");
        while ($file = readdir($dp)) {
        	if (($file != ".") && ($file != "..")&& !is_dir($file))
        		$list[] =substr($file, 3,-4) ;
        }
        $this->_log->debug($list);
        if (is_array($list)) sort($list);
        $this->view->list=$list;
        $req = $this->getRequest();
        $date = $req->getParam("date", date("Ymd"));
        $page = $req->getParam("page", 1);
        $priority = $req->getParam("priority", - 1);
        $data = file_get_contents(APPLICATION_PATH . "/log/log$date.txt");
        $obj = simplexml_load_string('<root>' . $data . '</root>');
        $this->_log->debug($obj);
        $log = array();
        $pr = array();
        $prn = array();
        foreach ($obj->logEntry as $value) {
            if (! in_array($value->priority, $pr)) {
                $pr[] = (int) $value->priority;
                $prn[] = $value->priorityName;
            }
            if (($value->priority == $priority) || ($priority < 0))
                $log[] = array('timestamp' => $value->timestamp, 
                'message' => $value->message, 'priority' => $value->priority, 
                'priorityName' => $value->priorityName);
        }
        $paginator = Zend_Paginator::factory($log);
        $paginator->setCurrentPageNumber($page);
        $this->view->log = $paginator;
        $this->view->date=$date;
        $this->view->priority = array('n' => $pr, 'name' => $prn);
    }
}



