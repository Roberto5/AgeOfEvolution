<?php
/**
 * buildingController
 *
 * @author
 * @version
 */
require_once 'Zend/Controller/Action.php';
class S1_BuildingController extends Zend_Controller_Action
{
	/**
	 *
	 * @var Model_civilta
	 */
	private $civ;
	/**
	 *
	 * @var Zend_Controller_Request_Http
	 */
	private $req;
	private $now;
	private $pos;
	private $p;
	private $log;
	private $building;
	//private $token;
	public function init ()
	{
		$this->civ = Model_civilta::getInstance();
		$this->now = $this->civ->getCurrentVillage();
		$this->req = $this->getRequest();
		$this->pos = intval($this->req->getParam("pos", 0));
		if (is_numeric($this->req->getParam('t')))
		$this->pos = $this->civ->village->building[$this->now]->getBildForType($this->req->getParam('t'));
		$this->building=$this->civ->village->building[$this->now];
		$this->t = Zend_Registry::get("translate");
		$this->db = Zend_Db_Table::getDefaultAdapter();
		$this->log = Zend_Registry::get("log");
		//$this->token = token_ctrl($this->getRequest()->getParams());
	}
	/**
	 * The default action - show the home page
	 */
	public function indexAction ()
	{}
	public function showAction ()
	{
		global $Building_Array;
		$module = $this->getRequest()->getModuleName();
		$this->view->civ = $this->civ;
		$this->view->pos=$this->pos;
		$this->view->now=$this->now;
		$age = $this->civ->getAge();
		$this->view->building=$this->building;
		//$token = token_set("tokenB");
		$this->view->age=$age;
	}
	public function buildAction ()
	{
		global $Building_Array;
		$this->view->layout()->x = 300;
		$this->view->layout()->y = 200;
		$type = intval($this->req->getParam('type'));
		$p = $this->civ->village->building[$this->now]->getproperty($this->pos,
		$this->civ->getAge(), $type - 1);
		$can = $this->civ->village->building[$this->now]->canBuild($p['cost'],
		$this->civ->getResource(), $this->pos,$type,$this->civ->getAge());
		if (($can['bool']) /*&& ($this->token['tokenB'])*/) {
			$error=FALSE;
			try {
				Zend_Db_Table::getDefaultAdapter()->query(
            "INSERT INTO `" . SERVER . "_building` SET `village_id`='" .
				$this->now . "' , `type`='" . $type . "' , `pos`='" .
				$this->pos . "'");
			}
			catch (Zend_Db_Exception $e) {
				$this->view->error="[CANTBUILD]";
				$error=true;
			}
			$cost=$Building_Array[$type - 1]::$cost;
			$this->civ->aggResource($cost);
			if (!$error) {
				$this->civ->village->building[$this->now]->data[$this->pos]=array('type'=>$type);
				$this->civ->village->building[$this->now]->addQueue($p['time'],
				$type, $this->pos, $this->civ->getCurrentVillage());
				$queue = $this->civ->getQueue()->toArray();
				$param = serialize(array('pos' => $this->pos, 'type' => $type));
				$queue[] = array('params' => $param,
            'time' => (time() + $p['time']));
				/*require_once APPLICATION_PATH . '/views/helpers/template.php';
				 $tmp = new Zend_View_Helper_template();
				 $this->civ->refresh->addIds('queue', $tmp->queue($queue, true));
				 $this->civ->refresh->addIds('resbar', $tmp->resourceBar());*/
				$this->civ->queue=$queue;
				$this->civ->refresh(array('order'=>true));
			}
		} else {
			if (! $can['bool'])
			$this->view->error = $can['mess'];
		}
		//$this->civ->refresh->addToken('tokenB', token_set("tokenB"));
		if (! $_POST['ajax'])
		$this->_helper->redirector("index", "index",
		$this->req->getModuleName());
	}
	public function marketAction ()
	{
		Zend_Layout::getMvcInstance()->disableLayout();
		$villages = null;
		$list = $this->civ->village_list;
		$this->view->list = $list;
		$this->view->section = $this->getRequest()->getParam("section");
		foreach ($list as $key => $value)
		if ($key != $this->now)
		$villages .= '<option value="' . $key . '">' . $value['name'] .
                 '</option>';
		$this->view->villages = $villages;
		$this->view->age = $this->civ->getAge();
		$this->view->offer1 = $this->db->fetchAll(
        "SELECT *,`civ_name` FROM `" . OFFER_TABLE . "`,`" . CIV_TABLE .
         "` WHERE `type`='1' AND `" . CIV_TABLE . "`.`civ_id`=`" . OFFER_TABLE .
         "`.`civ_id` ORDER BY `rapport` LIMIT 10");
		$this->view->offer2 = $this->db->fetchAll(
        "SELECT *,`civ_name` FROM `" . OFFER_TABLE . "`,`" . CIV_TABLE .
         "` WHERE `type`='2' AND `" . CIV_TABLE . "`.`civ_id`=`" . OFFER_TABLE .
         "`.`civ_id` ORDER BY `rapport` LIMIT 10");
		$pos = $this->civ->village->building[$this->now]->getBildForType(MARKET);
		//@todo rifare con popolazione
		$mercants=1;
		$disp = $mercants - $this->civ->getMercantBusy();
		$this->view->disp = $disp;
		$this->view->mercants = $mercants;
		$this->view->travel = $this->civ->getMercantsTravel();
		$this->view->res = array($list[$this->now]['resource_1'],
		$list[$this->now]['resource_2'], $list[$this->now]['resource_3']);
		$this->view->name1 = $this->civ->getNameResource(1,
		$this->civ->getAge());
		$this->view->name2 = $this->civ->getNameResource(2,
		$this->civ->getAge());
	}
	public function barrakAction ()
	{
		Zend_Layout::getMvcInstance()->disableLayout();
		$this->view->disp = $this->civ->dispTroops;
		//$this->view->token = token_set("tokenT");
		$this->view->training = $this->civ->training;
		$this->view->section = $this->getRequest()->getParam("section", 1);
		$this->view->age = $this->civ->getAge();
		$this->view->popl = $this->civ->village->data[$this->now]['pop'] -
		$this->civ->village->data[$this->now]['busy_pop'];
		switch ($this->view->section) {
			default:
			case 1:
				$p = $this->civ->village->building[$this->now]->getproperty(
				$this->civ->village->building[$this->now]->getBildForType(
				BARRACK), $this->civ->getAge());
				$this->view->res = $this->civ->village->data[$this->now];
				$this->view->p = $p;
				break;
			case 2:
				$this->view->troop = $this->civ->troopers->troopers_now;
				$this->view->resource_1 = $this->civ->village->data[$this->now]['resource_1'];
				break;
			case 3:
				break;
		}
	}
	public function destroyAction ()
	{
		$pos = $this->getRequest()->getParam("pos");
		if (isset($this->civ->village->building[$this->now]->data[$this->pos])) {
			$pop = $this->civ->village->building[$this->now]->data[$pos]['pop'];
			$param = serialize(
			array('pos' => $this->pos, 'village_id' => $this->now,
            'type' => $this->civ->village->building[$this->now]->getType(
			$this->pos), 'civ_id' => $this->civ->cid, 'pop' => $pop));
			$id=$this->civ->ev->insert(
			array('type' => DESTROY_EVENT, 'time' => (time() + 1200),
            'params' => $param));
			
			$destroy = $this->civ->destroy->toArray();
			$destroy[] = array('id'=>$id,'params' => $param, 'type' => DESTROY_EVENT,
            'time' => (time() + 1200));
			/*require_once APPLICATION_PATH . '/views/helpers/template.php';
			 $tmp = new Zend_View_Helper_template();
			 $this->civ->refresh->addIds('destroy', $tmp->queue($destroy, true));*/
			$this->civ->destroy=$destroy;
			$this->civ->refresh(array('order'=>true));
		} else
		$this->view->error = $this->_t->_('nessun edificio da demolire!');
		//$this->civ->refresh->addToken('tokenB', token_set("tokenB"));
	}
	public function popAction()
	{
		global $Building_Array;
		$this->_helper->layout->setLayout("ajax");
		$this->_helper->viewRenderer->setNoRender(true);
		$pop=intval($_POST['pop']);
		$type = $this->civ->village->building[$this->now]->data[$this->pos]['type'];
		if (($pop<=$Building_Array[$type - 1]::$maxPop[$this->civ->getAge()])&&($pop>0)) {
			$this->building->data[$this->pos]['pop']=$pop;
			$this->log->debug("pop $pop query `pos`='".$this->pos."' and `village_id`='".$this->now."'","query");
			$this->building->update(array('pop'=>$pop), "`pos`='".$this->pos."' and `village_id`='".$this->now."'");
		}
	}
}
