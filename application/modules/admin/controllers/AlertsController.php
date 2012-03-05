<?php

class Admin_AlertsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->alerts=$this->_db->fetchAll("SELECT * FROM `".ALERTS_TABLE."` ORDER BY `aid`");
        $ua=array();
        $na=array();
        if ($this->view->alerts)
        foreach ($this->view->alerts as $key=>$value) {
        	$user=$this->_db->fetchCol("SELECT `username` FROM `".ALERTS_READ."`,`".USERS_TABLE."` WHERE `".ALERTS_READ."`.`id`='".$value['aid']."' AND `".USERS_TABLE."`.`ID`=`user`");
    		$n=count($user);
    		
    		if ($n>0) $user=implode(",",$user);
    		$this->_log->debug($n);
    		$ua[$key]=$user;
    		$na[$key]=(int)$n;
        }
    	$this->view->user=$ua;
    	$this->view->n=$na;
    }

    public function addalertAction()
    {
        $text = $_POST['text'];
        $title=htmlentities($_POST['title'],ENT_QUOTES);
        $this->_db->query("INSERT INTO `" . ALERTS_TABLE . "` SET `text`='$text',`title`='$title'");
        $this->_helper->layout()->data=true;
    }

    public function modalertAction()
    {
        $id = (int) $_POST['id'];
        $text = $_POST['text'];
        $title=htmlentities($_POST['title'],ENT_QUOTES);
        $this->_db->query("UPDATE `" . ALERTS_TABLE . "` SET `text`='$text',`title`='$title' WHERE `aid`='$id'");
        $this->_db->query("DELETE FROM `" . ALERTS_READ . "` WHERE `id`='$id'");
    }

    public function delalertAction()
    {
        $id = (int) $_POST['id'];
        $this->_db->query("DELETE FROM `" . ALERTS_TABLE . "` WHERE `aid`='$id'");
        $this->_db->query("DELETE FROM `" . ALERTS_READ . "` WHERE `id`='$id'");
    }


}







