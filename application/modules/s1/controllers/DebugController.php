<?php

class S1_DebugController extends Zend_Controller_Action {

	/**
	 * 
	 * Enter description here ...
	 * @var Zend_Db_Adapter_Abstract
	 */
	private $db;
	private $log;
	/**
	 * 
	 * @var Model_civilta
	 */
	private $civ;
	public function init() {
		$module=Zend_Controller_Front::getInstance()->getRequest()
			->getModuleName();
		$complete=new Form_Complete();
		$complete->setAction($this->_helper->url("complete"));
		$this->view->complete=$complete;
		$rid=new Form_CompleteEv();
		$rid->setAction($this->_helper->url('rid'));
		$this->view->rid=$rid;
		$addvillage=new Form_AddVillage();
		$addvillage->setAction($this->_helper->url('addVil'));
		$this->view->addvillage=$addvillage;
		$this->db=Zend_Db_Table::getDefaultAdapter();
		$this->view->gentroops=new Form_gentroop();
		$this->view->gentroops->setAction($this->_helper->url('gentroop'));
		$this->log=Zend_Registry::get("log");
		$this->_helper->layout()->x=300;
		$this->_helper->layout()->y=150;
		$this->view->headTitle('Debug tool');
		$this->view->civ=Model_civilta::getInstance();
	}
	public function indexAction() {

	}
	public function aggprodAction() {
		$vid=Model_civilta::getInstance()->getCurrentVillage();
		Model_civilta::aggProd($vid);
		$this->view->text="done";
		$this->view->display=true;
		$this->log->notice("produzione aggiornata in $vid");
		echo $this->view->render("debug/index.phtml");
		Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
	}
	public function addpopAction() {
		$vid=Model_civilta::getInstance()->getCurrentVillage();
		$this->db->query("UPDATE `".SERVER."_map` SET `pop`=`pop`+'100' WHERE `id`='$vid'");
		$this->log->notice("aggiunti 100 abitanti in $vid");
		$this->view->text="done";
		$this->view->display=true;
		echo $this->view->render("debug/index.phtml");
		Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
	}
	public function ridAction() {
		if ($this->view->rid->isValid($_POST)) {
			$data=$this->view->rid->getValues();
			$rid=(100-$data['rid'])/100;
			$now=time();
			Zend_Db_Table::getDefaultAdapter()->query(
					"UPDATE `s1_events` 
			  	SET `time`='$now'+(`time`-'$now')*'$rid'");
			$this->view->text="done";
		}
		else {
			$this->view->error=true;
			$this->view->text="form non valido";
			$this->view->rid->populate($_POST);
		}
		$this->view->display=true;
		$this->log->notice("tempi ridotti del $rid%");
		echo $this->view->render("debug/index.phtml");
		Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
	}
	public function completeAction() {
		$form=new Form_Complete();
		
		if ($form->isValid($_POST)) {
			$data=$form->getValues();
			$param="";
			if ($data['vid'] == "this") {
				$civ=Model_civilta::getInstance();
				$id=$civ->getCurrentVillage();
				$param="AND (`params`LIKE'%\"village_id\";i:" . $id . "%'
					OR `params`LIKE'%\"village_A\";i:" . $id . "%'
					OR `params`LIKE'%\"village_B\";i:" . $id . "%'
					OR `params`LIKE'%\"destinatario\";i:" . $id . "%'
					OR `params`LIKE'%\"mittente\";i:" . $id . "%'
					)";
			}
			Zend_Db_Table::getDefaultAdapter()->query(
					"UPDATE `s1_events` 
			  	SET `time`='".time()."' 
			  	WHERE `type`='" . $data['ev'] . "' " . ($param ? $param : ""));
			$this->view->text="done";
		}
		else {
			$this->view->error=true;
			$this->view->text="form non valido";
			$this->view->complete->populate($_POST);
		}
		$this->view->display=true;
		$this->log->notice("evento ".$data['ev']." completato in $id");
		sleep(2);
		echo $this->view->render("debug/index.phtml");
		Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
	}

	public function fillAction() {
		$civ=Model_civilta::getInstance();
		$id=$civ->getCurrentVillage();
		$sto1=$civ->village->building[$id]->getCapTot();
		$sto2=$civ->village->building[$id]->getCapTot(STORAGE2);
		$civ->aggResource(array($sto1,$sto2,$sto2),true);
		$this->view->text="done";
		$this->view->display=true;
		$this->log->notice("magazzini riempiti in $id");
		echo $this->view->render("debug/index.phtml");
		Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
	}

	public function all20Action() {
		$civ=Model_civilta::getInstance();
		$now=$civ->getCurrentVillage();
		Zend_Db_Table::getDefaultAdapter()->query(
				"UPDATE `" . BUILDING_TABLE . "` SET `liv`='20' WHERE `village_id`='" . $now . "'");
		Zend_Db_Table::getDefaultAdapter()->query(
				"UPDATE `" . SERVER . "_map` SET `pop`=`pop`+'2000' WHERE `id`='" . $now . "'");
		$this->view->text="done";
		$this->view->display=true;
		$this->log->notice("villaggio $now portato a liv 20");
		echo $this->view->render("debug/index.phtml");
		Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
	}

	public function resetvilAction() {
		$vid=Model_civilta::getInstance()->getCurrentVillage();
		$this->db->query(
				"DELETE FROM `s1_building` WHERE `village_id`='" . $vid . "'");
		$this->db->query(
				"INSERT INTO `" . BUILDING_TABLE . "` (`village_id`,`type`,`liv`,`pos`) value ('" . $vid . "','1','0','0')");
		$this->db->query(
				"INSERT INTO `" . BUILDING_TABLE . "` (`village_id`,`type`,`liv`,`pos`) value ('" . $vid . "','4','0','1')");
		$this->db->query(
				"INSERT INTO `" . BUILDING_TABLE . "` (`village_id`,`type`,`liv`,`pos`) value ('" . $vid . "','5','0','2')");
		$this->db->query(
				"INSERT INTO `" . BUILDING_TABLE . "` (`village_id`,`type`,`liv`,`pos`) value ('" . $vid . "','6','0','3')");
		$this->db->update(SERVER.'_map', array('busy_pop'=>'0','pop'=>'100','name'=>'Nuovo Villaggio'),"`id`='$vid'");
		$this->view->text="done";
		$this->view->display=true;
		$this->log->notice("villaggio resettato in $vid");
		echo $this->view->render("debug/index.phtml");
		Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
	}

	public function randomvilAction() {
		$civ=Model_civilta::getInstance();
		$s=array(array('x'=>0,'y'=>0),array('x'=>2,'y'=>2),array('x'=>1,'y'=>1),array('x'=>2,'y'=>1),array('x'=>1,'y'=>2));
		$x=$s[$this->getRequest()
			->getParam("sector", 0)]['x'];
		$y=$s[$this->getRequest()
			->getParam("sector", 0)]['y'];
		$coord=$civ->randomcoord();
		Model_civilta::addVillage($coord['x'], $coord['y'],$civ->cid);
		
		$this->view->text="done";
		$this->view->display=true;
		$this->log->notice("villaggio creato in ".print_r($coord,true)." per ".$civ->cid);
		echo $this->view->render("debug/index.phtml");
		Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
	}

	public function addvilAction() {
		$form=new Form_AddVillage();
		$civ=Model_civilta::getInstance();
		if ($form->isValid($_POST)) {
			$data=$form->getValues();
			Model_civilta::addVillage($data['x'], $data['y'], $civ->cid,0 , 0, 
					$data['name']);
			$this->view->text="done";
		}
		else {
			$this->view->addvillage->populate($_POST);
			$this->view->error=true;
			$this->view->text=$form->populate($_POST);
			
		}
		$this->view->display=true;
		$this->log->notice("villaggio creato in ".$data['x']."/".$data['y']." per ".$civ->cid);
		echo $this->view->render("debug/index.phtml");
		Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
	}

	public function deletelogAction() {
		chdir(APPLICATION_PATH."/log");
		$projectsListIgnore=array('.','..');
		$handle=opendir(".");
		$d=0;$n=0;
		while (($file=readdir($handle))!=false) {
			preg_match("/log(\d+).txt/", $file,$mat);
			$t=mktime(0,0,0,substr($mat[1], 2,2),substr($mat[1], 0,2),substr($mat[1], 4,4));
			
			$time=time()-$t;
			if (!is_dir($file) && !in_array($file, $projectsListIgnore)&&($time>604800)) {
				if ($t>$d) $d=$t;
				$n++;
				unlink($file);
			}
		}
		closedir($handle);
		
		$this->view->text="done";
		$this->view->display=true;
		$this->log->notice("$n log cancellati fino al ".date("d/m/Y",$d));
		echo $this->view->render("debug/index.phtml");
		Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
	}
	public function gentroopAction() {
		$form=new Form_gentroop();
		if ($form->isValid($_POST)) {
			$num=$form->getValue("number");
			$id=$form->getValue("type");
			$civ=Model_civilta::getInstance();
			$cid=$civ->cid;
			$vid=$civ->getCurrentVillage();
			$this->view->text="done";
			$this->db->query("INSERT INTO `".TROOPERS."` (`trooper_id`,`civ_id`,`numbers`,`village_now`,`village_prev`) VALUES ('$id','$cid','$num','$vid','$vid')");
		}
		else {
			$this->view->error=true;
			$this->view->text=$form->populate($_POST);
		}
		$this->view->display=true;
		echo $this->view->render("debug/index.phtml");
		Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
	}
}

