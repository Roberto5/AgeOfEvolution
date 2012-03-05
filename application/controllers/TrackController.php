<?php
/**
 * trackController
 * 
 * @author
 * @version 
 */
require_once 'Zend/Controller/Action.php';
class TrackController extends Zend_Controller_Action
{
    /**
     * 
     * @var Zend_Db_Adapter_Abstract
     */
    private $db;
    private $log;
    public function init ()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
        Zend_Layout::getMvcInstance()->disableLayout();
        $this->log = Zend_Registry::get("log");
    }
    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {
        $tag = $this->db->fetchAssoc("SELECT `name`,`id` FROM `site_track_tag`");
        $form = new Form_Track();
        if ($form->isValid($_POST)) {
            $data = $form->getValues();
            $tag2 = explode(",", $data['tag']);
            $tid = array();
            $newt = array();
            if (! $tag)
                $newt = $tag2;
            else {
                for ($i = 0; $i < count($tag2); $i ++) {
                    if (array_key_exists($tag2[$i], $tag))
                        $tid[] = $tag[$tag2[$i]]['id'];
                    else
                        $newt[] = $tag2[$i];
                }
            }
            $newid = array();
            for ($i = 0; $i < count($newt); $i ++) {
                $this->db->insert("site_track_tag", array('name' => $newt[$i]));
                $newid[] = $this->db->lastInsertId();
            }
            $this->log->debug($tid);
            $this->log->debug($newid);
            $tid = array_merge($tid, $newid);
            $this->log->debug($tid);
            $data = array_diff($data, array('tag' => $data['tag']));
            $ref=$_SERVER['HTTP_REFERER'];
            $ua=$_SERVER['HTTP_USER_AGENT'];
            $ip=$_SERVER['REMOTE_ADDR'];
            
            $data['description']='<div style="border: 1px solid black">referer:'.$ref.'</br>
            user agent:'.$ua.'</br>ip:'.$ip.'</div>'.$data['description'];
            $data['uid']=Zend_Auth::getInstance()->getIdentity()->user_id;
            $this->db->insert("site_track", $data);
            $t = $this->db->lastInsertId();
            for ($i = 0; $i < count($tid); $i ++) {
                $this->db->insert("site_track_assoc_tag", 
                array('tid' => $tid[$i], 'id' => $t));
            }
            $this->view->mess = array();
        } else
            $this->view->mess = $form->getMessages();
    }
    public function autoAction ()
    {
        $name = addslashes($_GET['term']);
        $tag = $this->db->fetchCol(
        "SELECT `name` FROM `site_track_tag` WHERE `name`LIKE '$name%'");
        echo json_encode($tag);
        Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
    }
}
