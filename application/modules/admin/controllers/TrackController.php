<?php

class Admin_TrackController extends Zend_Controller_Action
{

    /**
     * @var Model_track
     *
     *
     */
    public $track = null;

    public function init()
    {
        $this->track=new Model_track();
    }

    public function indexAction()
    {
    	$page=$this->getRequest()->getParam("page",1);
        $res=$this->track->getTrack();
        $track=Zend_Paginator::factory($res);
        $track->setCurrentPageNumber($page);
        $this->_log->debug($res);
        $this->view->track=$track;
    }

    public function seeAction()
    {
    	$id=$this->getRequest()->getParam("id");
        $this->view->track=$this->track->see($id);
        $this->_log->debug($this->view->track);
    }

    public function deleteAction()
    {
        $id=(int)$this->_getParam("id");
        $this->track->delete("`id`='$id'");
        $this->_db->delete('site_track_assoc_tag',"`id`='$id'");
        $this->_helper->redirector("index");
    }

}





