<?php

class S1_ProfileController extends Zend_Controller_Action {

	/**
	 * @var Zend_Db_Adapter_Abstract
	 * 
	 */
	private $db=null;

	public $module='';

	public function init() {
		$this->db=Zend_Db_Table::getDefaultAdapter();
		$this->module=$this->getRequest()
			->getModuleName();
		$this->log=Zend_Registry::get("log");
	}

	public function indexAction() {
		$uid=$this->getRequest()
			->getParam("uid", 0);
		$cid=$this->getRequest()
			->getParam("cid", 0);
		//profilo user principale
		if (!$uid && !$cid) {
			$this->view->user=Zend_Registry::get("user");
			$this->view->civ=Zend_Registry::get("civ");
			$this->view->type=1;
		}
		elseif ($uid) { //profilo user generico
			$this->view->user=$this->db->fetchRow(
					"SELECT * FROM `" . USERS_TABLE . "` WHERE `ID`='$uid'");
			$this->view->type=2;
		}
		elseif ($cid) { //profilo civ generico
			$this->view->civ=$this->db->fetchRow(
					"SELECT * FROM `" . CIV_TABLE . "` WHERE `civ_id`='" . $cid . "'");
			$this->view->type=3;
			$sharer=$this->db->fetchAssoc(
					"SELECT `ID`,`username` FROM `" . USERS_TABLE . "`,`" . RELATION_USER_CIV_TABLE . "` WHERE `civ_id`='$cid' AND `user_id`=`ID`");
			
			$this->view->sharer=$sharer;
			//@todo ottimizzare
			$this->view->village_list=$this->db->fetchAssoc(
					"SELECT * FROM `" . SERVER. "_vilage` WHERE `civ_id`='$cid'");
		}
	}
	public function changenamevillageAction() {
		$filter=array('id'=>'Digits',
			'name'=>'HtmlEntities');
		$input=new Zend_Filter_Input($filter,null,$_POST);
		$input->setDefaultEscapeFilter(new Zend_Filter_Callback("addslashes"));
		$vid=$input->getEscaped('id');
		$name=$input->getEscaped('name');
		$this->db->query("UPDATE `".SERVER."_map` SET `name`='$name' WHERE `id`='$vid'");
		$civ=Model_civilta::getInstance();
		$civ->refresh->addIds('vid'.$vid, $name);
		$update=array('ids'=>array('vid'.$vid=>$name));
		$this->_helper->layout()->data=true;
		$civ->village->data[$vid]['name']=$name;
		$civ->refresh();
	}
	public function editAction() {
		$id=Zend_Auth::getInstance()->getIdentity()->user_id;
		$cid=Model_civilta::getInstance()->cid;
		$bool=token_ctrl($this->getRequest()->getParams());
		$this->view->token=token_set("tokenP");
		if ($this->getRequest()->isPost()&&$bool['tokenP']) {
			$this->db->query("UPDATE `".USERS_TABLE."` SET `des_user`='".htmlentities($_POST['des_user'],ENT_QUOTES)."' WHERE `ID`='$id'");
			$this->db->query("UPDATE `".CIV_TABLE."` SET `des_civ`='".htmlentities($_POST['des_civ'],ENT_QUOTES)."' WHERE `civ_id`='$cid'");
			$this->view->mod=true;
		}
		else {
			$this->view->des_user=$this->db->fetchOne("SELECT `des_user` FROM `".USERS_TABLE."` WHERE `ID`='$id'");
			$this->view->des_civ=$this->db->fetchOne("SELECT `des_civ` FROM `".CIV_TABLE."` WHERE `civ_id`='$cid'");
		}
	}

}



