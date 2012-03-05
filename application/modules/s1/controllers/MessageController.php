<?php
class S1_MessageController extends Zend_Controller_Action
{
    public function init ()
    {
        $t = Zend_Registry::get("translate");
        $this->log = Zend_Registry::get("log");
        $module = $this->getRequest()->getModuleName();
        $write = $t->_("scrivi");
        $outbox = $t->_("inviati");
        $this->user = Zend_Auth::getInstance()->getIdentity();
        $this->view->layout()->nav = array(
        $write => array('url' => $module . '/message/write'/*, 'img' => '/common/images/home.png'*/), 
        $outbox => array('url' => $module . '/message/outbox'/*,'img'=>'/common/images/forum.ico'*/));
    }
    public function indexAction ()
    {
        //@todo numero di elementi x pagina settabile via pannello di controllo
        $this->view->refresh=$this->getRequest()->getParam('ref',false);
        $gap = 1;
        $user = Zend_Auth::getInstance()->getIdentity();
        $civ = Model_civilta::getInstance();
        $page = $this->getRequest()->getParam("page", 0);
        $query = "SELECT `" . MESS_TABLE . "`. * , username, civ_name, 
                                IF(EXISTS (
                                	SELECT `id` 
                                	FROM `" .
         MESS_READ_TABLE .
         "` 
                                	WHERE `" .
         MESS_READ_TABLE . "`.`id` = `" . MESS_TABLE .
         "`.`id` 
                                	AND `" .
         MESS_READ_TABLE . "`.`user` = '" . $user->user_id .
         "') >0, '1', '0' 
                        		) AS `read`
                        		FROM `" .
         MESS_TABLE . "` , `s1_civ` , `" . USERS_TABLE .
         "` 
                        		WHERE `" .
         MESS_TABLE . "`.`destinatario` = '" . $civ->cid .
         "' 
                        		AND `" .
         MESS_TABLE . "`.`mittente` = `" . USERS_TABLE . "`.`ID` 
                        		AND `destinatario` = `civ_id` 
                        		ORDER BY `" .
         MESS_TABLE . "`.`ora` DESC";
        $result = Zend_Db_Table::getDefaultAdapter()->fetchAssoc($query);
        $paginator = Zend_Paginator::factory($result);
        //$paginator->setItemCountPerPage($gap);
        $paginator->setCurrentPageNumber($page);
        $this->view->mess = $paginator;
    }
    public function outboxAction ()
    {
        $gap = 1;
        $this->view->refresh=$this->getRequest()->getParam('ref',false);
        $civ = Model_civilta::getInstance();
        $page = $this->getRequest()->getParam("page", 0);
        $query = "SELECT `" . MESS_TABLE . "`. * , `civ_name` AS `dest_civ`, 
                            	IF( EXISTS ( 
                            		SELECT `id` 
                            		FROM `" . MESS_READ_TABLE .
         "` 
                            		WHERE `" .
         MESS_READ_TABLE . "`.`id` = `" . MESS_TABLE . "`.`id` ) >0, '1', '0' 
                            	) AS `read`, 
                            	(
                            		SELECT `username` 
                            		FROM `" . USERS_TABLE .
         "` 
                            		WHERE `" .
         USERS_TABLE . "`.`ID` = `" . MESS_TABLE .
         "`.`mittente`
                            	) AS `user_mitt` 
                            	FROM `" .
         MESS_TABLE . "` , `" . CIV_TABLE . "` , `" . USERS_TABLE .
         "` 
                            	WHERE ( 
                            		SELECT `civ_id` 
                            		FROM `" .
         RELATION_USER_CIV_TABLE . "` , `" . USERS_TABLE .
         "` 
                            		WHERE `" .
         RELATION_USER_CIV_TABLE . "`.`user_id` = `" . MESS_TABLE .
         "`.`mittente` 
                            		AND `" .
         RELATION_USER_CIV_TABLE . "`.`server` = '" . SERVER . "' 
                            		LIMIT 1 ) = '" . $civ->cid .
         "' 
                            	AND `" .
         MESS_TABLE . "`.`destinatario` = `" . CIV_TABLE .
         "`.`civ_id` 
                            	AND `" .
         MESS_TABLE . "`.`mittente`=`" . USERS_TABLE . "`.`ID`
                            	ORDER BY `" .
         MESS_TABLE . "`.`ora` DESC";
        $result = Zend_Db_Table::getDefaultAdapter()->fetchAssoc($query);
        $paginator = Zend_Paginator::factory($result);
        //$paginator->setItemCountPerPage($gap);
        $paginator->setCurrentPageNumber($page);
        $this->view->mess = $paginator;
    }
    public function writeAction ()
    {
        $t = Zend_Registry::get("translate");
        $log = Zend_Registry::get("log");
        $this->view->write = true;
        $module = $this->getRequest()->getModuleName();
        $this->view->action =  $module .
         "/message/write/send/true";
        $form = new Form_Mess();
        $form->setAction($this->view->action);
        $this->view->form = $form;
        if ($this->getRequest()->getParam("send")) {
            if ($form->isValid($_POST)) {
                $data = $form->getValues();
                $dest = Zend_Db_Table::getDefaultAdapter()->fetchOne(
                "SELECT `civ_id` FROM `" . CIV_TABLE . "` WHERE `civ_name`='" .
                 $data['destinatario'] . "'");
                Model_mess::send($dest, 
                Zend_Auth::getInstance()->getIdentity()->user_id, 
                $data['oggetto'], $data['messaggio']);
                $this->view->mess = "messaggio inviato con successo";
                if (!$_POST['ajax']) $this->_helper->redirector("index");
            } else {
                $this->view->data = $form->getValues();
                $mes = $form->getMessages();
                $mess = "";
                foreach ($mes as $v) {
                    foreach ($v as $value) {
                        $mess .= $value . "<br/>";
                    }
                }
                $this->view->mess = $mess;
            }
        }
        $mid = $this->getRequest()->getParam("reply");
        if ($mid) {
            $data = Zend_Db_Table::getDefaultAdapter()->fetchRow(
            "SELECT `" . MESS_TABLE .
             "`.*,`civ_name`,`username` 
                			FROM `" .
             MESS_TABLE . "`,`" . CIV_TABLE . "`,`" . RELATION_USER_CIV_TABLE .
             "`,`" . USERS_TABLE . "` 
                			WHERE `" . MESS_TABLE . "`.`id`='$mid' 
                			AND `" .
             RELATION_USER_CIV_TABLE .
             "`.`user_id`=`mittente` 
                			AND `" .
             RELATION_USER_CIV_TABLE . "`.`civ_id`=`" . CIV_TABLE .
             "`.`civ_id`
                			AND `" .
             RELATION_USER_CIV_TABLE . "`.`user_id`=`" . USERS_TABLE . "`.`ID`");
            $data['destinatario'] = $data['civ_name'];
            $data['messaggio'] = "\n\n\n" . $t->_("Scritto da") . " " .
             $data['username'] . ":\n______________\n" . $data['messaggio'];
            $data['mittente'] = Zend_Auth::getInstance()->getIdentity()->username;
            $numero = (int) substr($data['oggetto'], 3);
            $data['oggetto'] = "RE" . (substr($data['oggetto'], 0, 2) == "RE" ? (substr(
            $data['oggetto'], 0, 3) == "RE:" ? "^2:" . substr($data['oggetto'], 
            3) : "^" . ($numero + 1) . ":" .
             str_replace("RE^$numero:", "", $data['oggetto'])) : ":" .
             $data['oggetto']);
            $this->view->data = $data;
        }
    }
    public function seeAction ()
    {
        $id = (int) $this->getRequest()->getParam("message_id", 0);
        $query = "SELECT `" . MESS_TABLE .
         "`.*,`civ_name`,`username`,
                 IF( EXISTS ( SELECT `id` FROM `" .
         MESS_READ_TABLE . "` WHERE `" . MESS_READ_TABLE . "`.`id` = `" .
         MESS_TABLE .
         "`.`id` ) >0, '1', '0' ) AS `read` 
                FROM `" .
         MESS_TABLE . "`,`" . CIV_TABLE . "`,`" . USERS_TABLE . "` 
                WHERE `" . MESS_TABLE .
         "`.`id`='$id' 
                AND `" .
         MESS_TABLE . "`.`destinatario` = `" . CIV_TABLE .
         "`.`civ_id` 
                AND `" .
         USERS_TABLE . "`.`ID`=`" . MESS_TABLE . "`.`mittente`";
        $this->view->data = Zend_Db_Table::getDefaultAdapter()->fetchRow($query);
        $user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
        $t = Zend_Registry::get("translate");
        if ($this->view->data['mittente'] == $user_id)
            $button = '<button onclick="ev.message.send(\'' . $this->getRequest()->getModuleName() .
             '/message/write/reply/' . $id . '\');$(\'#windows{wid}\').dialog(\'close\');" >' . $t->_('Inoltra') .
             '</button>';
        else
            $button = '<button onclick="ev.message.send(\'' .
             $this->getRequest()->getModuleName() .
             '/message/write/reply/' . $id . '\');$(\'#windows{wid}\').dialog(\'close\');" >' . $t->_('Rispondi') .
             '</button>';
        $this->view->b = $button;
        if ($this->view->data['read'] == 0)
            Zend_Db_Table::getDefaultAdapter()->query(
            "INSERT INTO `" . MESS_READ_TABLE . "` (`id`, `user`) 
                		VALUES ('" . $id . "', '" .
             $user_id . "');");
    }
    public function deleteAction ()
    {
        $ids = $_POST['ids'];
        $n = count($ids);
        if ($n == 1) {
            Zend_Db_Table::getDefaultAdapter()->query(
            "DELETE FROM `" . MESS_TABLE . "` WHERE `id`='" . $ids[0] . "'");
            Zend_Db_Table::getDefaultAdapter()->query(
            "DELETE FROM `" . MESS_READ_TABLE . "` WHERE `id`='" . $ids[0] . "'");
        } elseif ($n > 1) {
            $cond = "WHERE `id` IN ('" . implode("','", $ids) . "')";
            Zend_Db_Table::getDefaultAdapter()->query(
            "DELETE FROM `" . MESS_TABLE . "` " . $cond);
            Zend_Db_Table::getDefaultAdapter()->query(
            "DELETE FROM `" . MESS_READ_TABLE . "` " . $cond);
        }
        if (!$_POST['ajax']) $this->_helper->redirector("index");
    }
    public function readAction ()
    {
    	$ids = array();
        if ($this->getRequest()->getParam("all", false)) {
        	$cid=Model_civilta::getInstance()->cid;
        	$uid=$this->user->user_id;
        	$temp=$this->_db->fetchCol("SELECT `id` FROM `".MESS_TABLE."`
        		WHERE `destinatario`='$cid' AND `id` <> ALL (SELECT `id` FROM `".MESS_READ_TABLE."` WHERE `user`='$uid')");
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
            $query = "INSERT IGNORE INTO `" . MESS_READ_TABLE .
             "` (`id`, `user`) VALUES " . implode(",", $ids);
            try {
                $this->_db->query($query);
            } catch (Zend_Db_Exception $e) {
                $this->_log->warn($e);
            }
        }
        if (!$_POST['ajax']) $this->_helper->redirector("index");
    }
    public function autocompleteAction ()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        $term = addslashes($_GET['term']);
        $vector = Zend_Db_Table::getDefaultAdapter()->fetchCol(
        "SELECT `civ_name` FROM `" . CIV_TABLE . "` WHERE `civ_name`LIKE'$term%'");
        echo json_encode($vector);
        Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
    }
}







