<?php
/**
 * civilta
 *
 * @author pagliaccio
 * @version
 */
require_once 'Zend/Db/Table/Abstract.php';
class Model_civilta extends Zend_Db_Table_Abstract
{
	protected $_primary = 'civ_id';
	/**
	 *
	 * @var Model_refresh
	 */
	public $refresh;
	/**
	 * utente in attesa o sharer o proprietario
	 * @var int
	 */
	public $status = 0;
	public $displaytroop;
	/**
	 * mappa
	 * @var Model_map
	 */
	public $map = null;
	public $data = null;
	public $cid = null;
	public $resource = null;
	private $currentVillage = null;
	/**
	 * villaggio
	 * @var Model_village
	 */
	public $village = null;
	public $village_list = null;
	public $queue = null;
	private static $ages = array('Preistorica', 'Antica', 'Medioevale',
			'Industriale', 'Moderna', 'Futura');
	static $nameResource = array(array('pietra', 'cibo', 'legno'),
			array('sale', 'bronzo', 'legno'), array('oro', 'ferro', 'pietra'),
			array('dollari', 'carbone', 'cemento'),
			array('crediti', 'petrolio', 'acciaio'), array('nanomacchine', '(2)', '(3)'));
	/**
	 * truppe
	 * @var Model_troopers
	 */
	public $troopers = null;
	public $poptroop = 0;
	public $outAttack = null;
	public $inAttack = null;
	public $outReinf = null;
	public $inReinf = null;
	public $training = null;
	public $sharer = null;
	/**
	 * opzioni civiltà
	 * @var Model_option
	 */
	public $option = null;
	public $coord = array();
	/**
	 * gestore eventi
	 * @var Model_event
	 */
	public $ev;
	/**
	 *
	 * Enter description here ...
	 * @var Zend_Db_Table
	 */
	private $db;
	/**
	 * traduttore
	 * @var Zend_Translate
	 */
	private $t;
	/**
	 *
	 * @var Zend_Log
	 */
	private $log;
	public $dispTroops = array();
	/**
	 *
	 * @var Model_research
	 */
	public $research;
	public $pr = 0;
	public $bpr = 0;
	/**
	 *
	 * @var Zend_Db_Table_Rowset_Abstract
	 */
	public $destroy;
	/**
	 *
	 * @var Model_quest
	 */
	public $quest;
	/**
	 * cerca nel DB l'id della civiltà passato come parametro, e definisce i parametri della classe.
	 * @param int $id civ_id
	 * @param Model_option $user_opt opzioni user
	 * @param Model_event $event
	 * @param Zend_Db_Table $db
	 */
	function __construct ($cid, $user_opt, $event, $db = false)
	{
		$this->_name=SERVER.'_civ';
		parent::__construct();
		$row = $this->fetchRow("`civ_id`='" . $cid['civ_id'] ."'");
		if ($row) {
			$this->ev = $event;
			$this->t = Zend_Registry::get("translate");
			$this->log = Zend_Registry::get("log");
			/*if ($db) $this->setDefaultAdapter($db);
			 else $this->setDefaultAdapter(Zend_Db_Table::getDefaultAdapter());
			*/
			$this->map = new Model_map();

			$this->status = (int) $cid['status'];
			$this->uid = (int) $cid['user_id'];
			if ($this->status)
				$this->cid = (int) $row['civ_id'];
			if ($this->status > 1) {
				$this->cid = (int) $row['civ_id'];
				$this->data = $row;
				$this->option = new Model_option($this->cid, "civilta");
				// villaggio corrente
				if (is_numeric($_GET['vid'])) {
					$cid['current_village'] = $_GET['vid'];
					$this->changeCurrentVillage($_GET['vid']);
				}
				//lista villaggi
				// ordine di visualizzazione
				$order = $user_opt->get("order", "1");
				switch ($order) {
					case 2:
						$col = "name";
						break;
					case 5:
						$col = "busy_pop";
						break;
					case 4:
					case 3:
					case 1:
					default:
						$col = "id";
						break;
				}
				$orderT = ($user_opt->get("orderT") ? "DESC" : "ASC");
				$this->village = new Model_village($this->cid, "$col $orderT");
				$this->village_list = $this->village->getList();
				// controllo se il current_village è nella lista
				$bool = false;
				$bool=array_key_exists($cid['current_village'], $this->village_list);
				if ($bool)
					$this->currentVillage = (int) $cid['current_village'];
				else { //  se non c'e imposto il current_village con il primo pianeta della lista
					$array = array_values($this->village_list);
					$this->currentVillage = (int) $array[0]['id'];
					$this->changeCurrentVillage($this->currentVillage);
				}
				$this->village->setCurrentVillage($this->currentVillage);
				if ($user_opt->get("coord")) {
					$this->coord = array('x' => $user_opt->get("coord_x"),
							'y' => $user_opt->get("coord_y"));
				} else {
					$this->coord = Model_map::getInstance()->getCoordFromId(intval($this->currentVillage));
				}
				if ($order == 3) {
					usort($this->village_list, array($this, 'compare'));
					$temp = $this->village_list;
					$this->village_list = null;
					foreach ($temp as $value) {
						$this->village_list[$value['id']] = $value;
					}
				}
				if ($order == 4) {
					usort($this->village_list, array($this, 'costomCompare'));
					$temp = $this->village_list;
					$this->village_list = null;
					foreach ($temp as $value) {
						$this->village_list[$value['id']] = $value;
					}
				}
				$id = $this->currentVillage;
				//code edifici
				$where = array("`type`='1'",
						"`params`LIKE'%\"village_id\";i:" . $id . "%'");
				$this->queue = $this->ev->getEvent($where);
				$where = array("`type`='" . DESTROY_EVENT . "'",
						"`params`LIKE'%\"village_id\";i:" . $id . "%'");
				$this->destroy = $this->ev->getEvent($where);
				//gestione truppe
				$this->troopers = new Model_troopers(
						$this->currentVillage, $this->cid);
				$this->displaytroop = $this->troopers->other_troopers;
				foreach ($this->troopers->troopers_now as $value) {
					$bool = true;
					for ($i = 0; $this->displaytroop[$i] && $bool; $i ++) {
						if ($this->displaytroop[$i]['trooper_id'] ==
								$value['trooper_id']) {
							$this->displaytroop[$i]['numbers'] += $value['numbers'];
							$bool = false;
						}
					}
					if ($bool)
						$this->displaytroop[] = $value;
				}
				//movimenti truppa
				$movements = $this->ev->getEvent(
						"
						`type`='" . MOVEMENTS_EVENT . "'
						AND (`params`LIKE'%\"village_A\";i:" . $this->currentVillage .
						"%' OR `params`LIKE'%\"village_B\";i:" . $this->currentVillage .
						"%') ");
				for ($i = 0; $i < $movements->count(); $i ++) {
					$param = unserialize($movements[$i]['params']);
					switch ($param['type']) {
						case ATTACK:
						case RAID:
							if ($param['village_A'] == $this->currentVillage)
								$this->outAttack[] = $movements[$i];
							else
								$this->inAttack[] = $movements[$i];
							break;
						case RETURN_T:
						case REINFORCEMENT:
							if ($param['village_A'] == $this->currentVillage) {
								if ($param['civ_id'] == $this->cid)
									$this->outReinf[] = $movements[$i];
							} else
								$this->inReinf[] = $movements[$i];
							break;
						default:
					}
				}
				$this->training = $this->getDefaultAdapter()->fetchAll(
						"SELECT * FROM `" . EVENTS_TABLE . "` WHERE `type`='" .
						TRAINING_EVENT . "' AND `params`LIKE'%\"village_id\";i:" .
						$this->currentVillage . "%' ORDER BY `time`");
				$this->sharer = $this->getDefaultAdapter()->fetchAll(
						"SELECT `username`,`status`,`user_id`, IF( EXISTS ( SELECT `last_activity` FROM `" .
						SESSIONS_TABLE . "` WHERE `" . SESSIONS_TABLE . "`.`user_id` = `" .
						USERS_TABLE . "`.`ID` ) >0, (SELECT `last_activity` FROM `" .
						SESSIONS_TABLE . "` WHERE `" . SESSIONS_TABLE . "`.`user_id` = `" .
						USERS_TABLE . "`.`ID` ORDER BY `last_activity` DESC LIMIT 1), '0' ) AS `last_activity`
						FROM `" . RELATION_USER_CIV_TABLE . "` , `" . CIV_TABLE . "` , `" .
						USERS_TABLE . "`
						WHERE `" . CIV_TABLE . "`.`civ_id` = '" . $this->cid . "'
						AND `" . RELATION_USER_CIV_TABLE . "`.`civ_id`=`" . CIV_TABLE . "`.`civ_id`
						AND `" . RELATION_USER_CIV_TABLE . "`.`user_id`=`" . USERS_TABLE . "`.`ID`");
				//ricerca
				foreach ($this->village_list as $key => $value) {
					$pos = $this->village->building[$key]->getBildForType(RESEARCH);
					if ($pos > 0) {
						$liv = $this->village->building[$key]->getLiv($pos);
						$this->pr += research::$pr[$liv];
					}
				}
				$this->research = new Model_research($this->cid);
				$this->bpr = $this->research->busypr();
				//truppe addestrabili
				global $Troops_Array;
				$disp = array();
				for ($i = 0; $Troops_Array[$i]; $i ++) {
					$bool = true;
					if ($Troops_Array[$i]::$age != $this->getAge())
						$bool = false;
					//altri controlli sulle truppe disponibili
					if ($Troops_Array[$i]::$condiction) {
						$res = $Troops_Array[$i]::$condiction['research'];
						$build = $Troops_Array[$i]::$condiction['build'];
						if ($res) {
							foreach ($res as $value) {
								if ($this->research->data[$value['type']]['liv'] <
										$value['liv'])
									$bool = false;
							}
						}
						if ($build) {
							foreach ($build as $value) {
								$pos = $this->village->building[$this->currentVillage]->getBildForType(
										$value['type']);
								if ($pos < 0)
									$bool = false;
								else {
									$liv = $this->village->building[$this->currentVillage]->getLiv(
											$pos);
									if ($liv < $value['liv'])
										$bool = false;
								}
							}
						}
					}
					if ($bool)
						$disp[] = $i;
				}
				$this->dispTroops = $disp;
			}
			$this->refresh = Model_refresh::getInstance();
			$this->quest=new Model_quest($this);
			if ($this->quest->n <= Model_quest::$maxquest[$this->quest->age]) $ctrl=$this->quest->control(); else $ctrl=false;
			if (!$this->quest->read||$ctrl) {
				$this->refresh->addjs("ev.quest.showquest();");
			}
			// oggetto refresh

			if ($_POST['ajax']) {
				$this->refresh();
			}
		}
	}
	function refresh ($option = array('order'=>false,'queue'=>false))
	{
		//aggiorno la coda costruzioni
		if ($this->cid) {
			require_once APPLICATION_PATH . '/views/helpers/Template.php';
			$tmp = new Zend_View_Helper_template($this);
			if ($option['queue']) {//ricalcolo coda
				$where = array("`type`='1'",
						"`params`LIKE'%\"village_id\";i:" . $this->currentVillage . "%'");
				$this->queue = $this->ev->getEvent($where);
				$where = array("`type`='" . DESTROY_EVENT . "'",
						"`params`LIKE'%\"village_id\";i:" . $this->currentVillage . "%'");
				$this->destroy = $this->ev->getEvent($where);
			}
			if ($option['event']) {// ricalcolo eventi
				$movements = $this->ev->getEvent(
						"
						`type`='" . MOVEMENTS_EVENT . "'
						AND (`params`LIKE'%\"village_A\";i:" . $this->currentVillage .
						"%' OR `params`LIKE'%\"village_B\";i:" . $this->currentVillage .
						"%') ");
				$this->outAttack=array();
				$this->inAttack=array();
				$this->outReinf=array();
				$this->inReinf=array();
				for ($i = 0; $i < $movements->count(); $i ++) {
					$param = unserialize($movements[$i]['params']);
					switch ($param['type']) {
						case ATTACK:
						case RAID:
							if ($param['village_A'] == $this->currentVillage)
								$this->outAttack[] = $movements[$i];
							else
								$this->inAttack[] = $movements[$i];
							break;
						case RETURN_T:
						case REINFORCEMENT:
							if ($param['village_A'] == $this->currentVillage) {
								if ($param['civ_id'] == $this->cid)
									$this->outReinf[] = $movements[$i];
							} else
								$this->inReinf[] = $movements[$i];
							break;
						default:
					}
				}
			}
			$this->refresh->addIds('queue'.$this->currentVillage,
					$tmp->queue($this->queue, $option['order']));
			$this->refresh->addIds('destroy'.$this->currentVillage,
					$tmp->queue($this->destroy, $option['order'],true));
			//aggiorno la barra risorse
			$this->refresh->addIds('resbar'.$this->currentVillage, $tmp->resourceBar());
			$this->refresh->addDispB(
					$this->village->building[$this->currentVillage]->getDispBuilding(
							$this->getAge()));
			//aggiorno le truppe presenti
			$this->refresh->displaytroops = $this->displaytroop;
			//aggiorno gli edifici
			global $Building_Array;
			foreach ($this->village->building[$this->currentVillage]->data as $key => $value) {
				$title = Model_building::$name[$this->getAge()][$value['type']] .
				' liv ' . $value['liv'];
				$this->refresh->addBuilding($key, $Building_Array[$value['type'] - 1],
						$title);
			}
			//aggiorno i movimenti truppa
			$move = array();
			$item = array($this->inAttack, $this->outAttack,
					$this->getMercantsTravel(), $this->inReinf, $this->outReinf,
					$this->troopers->my_troopers);
			for ($i = 0; $i < 6; $i ++) {
				$n = count($item[$i]);
				if ($i == 2) {
					$in = $item[$i]['in'];
					$out = $item[$i]['out'];
					$n1 = count($in);
					$n2 = count($out);
					$n = $n1 + $n2;
				}
				$info = "";
				if ($n && ($i < 5) && ($i != 2)) {
					foreach ($item[$i] as $value) {
						$param = unserialize($value['params']);
						$time = $value['time'] - mktime();
						$info .= '<div> ' . $this->t->_('da') . ' ' .
								$tmp->village($param['village_A'],null,false) . ' ' .
								$this->t->_('a') . ' ' . $tmp->village(
										$param['village_B'],null,false) . ' <span class="countDown">' . $time .
										'</span></div>';
					}
				} elseif (($i == 2) && ($n)) {
					foreach ($in as $value) {
						$param = unserialize($value['params']);
						$time = $value['time'] - mktime();
						$info .= '<div> ' . $this->t->_('in ritorno da') . ' ' .
								$tmp->village($param['destinatario'],null,false) . ' ' .
								$this->t->_('a') . ' ' . $tmp->village(
										$param['mittente'],null,false) . ' <span class="countDown">' . $time .
										'</span></div>';
					}
					foreach ($out as $value) {
						$param = unserialize($value['params']);
						$time = $value['time'] - mktime();
						$info .= '<div> ' . $this->t->_('da') . ' ' .
								$tmp->village($param['mittente'],null,false) . ' ' .
								$this->t->_('a') . ' ' . $tmp->village(
										$param['destinatario'],null,false) . ' <span class="countDown">' . $time .
										'</span></div>';
					}
				} elseif ($n) {
					$troop =is_array( $item[$i]) ? $item[$i] : $item[$i]->toArray();
					$j = 0;
					$v = $troop[0]['village_now'];
					$name = $troop[0]['name'];
					do {
						$info .= '<div>' . $this->t->_('rinforzi a ') . ' ' .
								$tmp->village($v, $name,false) . '</div>';
						for ($temp = array(); ($troop[$j]) &&
						($v == $troop[$j]['village_now']); $j ++)
							$temp[$troop[$j]['trooper_id']] = $troop[$j];
						$v = $troop[$j]['village_now'];
						$name = $troop[$j]['name'];
					} while ($troop[$j]);
				}
				if ($n)
					$move[$i+1]=array('n'=>$n,'content'=>$info);
			}
			$this->refresh->addEvent($move);
			//aggiorno l'icona report
			$n=Model_report::ThereAreReport($this->cid);
			$this->refresh->addAttr("link-Report",array('title'=>'Report'.($n?" ($n)":""),'src'=>'/common/images/report'.($n?'0':'1').'.gif'));
			$nmess=Model_mess::ThereAreMess($this->cid);
			$mess=$this->t->_("Messaggi").($nmess ? " [$nmess]" : "");
			$this->refresh->addAttr("link-Message",array('title'=>$mess,'src'=>'/common/images/mess_read'.($nmess?"1":"0").'.gif'));
			if (is_numeric($_GET['vid'])) {
				$focus=Model_map::getInstance()->getCoordFromId($this->currentVillage);
				$focus['id']=$this->currentVillage;
				$this->refresh->setFocus($focus);
			}
		}
	}
	/**
	 * @param Array $vect , indice per il campo e valore come valore
	 * @return bool
	 * registra un utente, ritorna true se la registrazione &egrave; andata bene.
	 */
	static function register ($vect)
	{
		if ($vect) {
			$db = Zend_Db_Table::getDefaultAdapter();
			$db->insert(CIV_TABLE, $vect);
			return true;
		} else {
			return false;
		}
	}
	/**
	 * @param Array $vect, indice per il campo e valore come valore
	 * @param int $id user id
	 * @return bool
	 * modifica i valori della civiltà;
	 */
	function updateCiv ($vect, $cid = false)
	{
		if ($vect) {
			$this->update($vect,
					"`civ_id`='" . ($cid ? $cid : $this->cid) . "'");
			return true;
		} else
			return false;
	}
	/**
	 * @param int $age (parte da 1)
	 * @return String
	 * ritorna il nome di un'età dall'eta corrente
	 */
	static function getCivAge ($age = 0)
	{
		$ages = Model_civilta::$ages;
		return $ages[($age ? ($age - 1) : Model_civilta::getInstance()->data['civ_age'])];
	}
	/**
	 * aggiunge il legame user civiltà
	 * @param int $civ
	 * @param int $id
	 * @param bool $owner
	 * @return bool
	 */
	static function subscrive ($civ, $id, $owner = true)
	{
		if ($owner)
			$status = 3;
		else
			$status = 1;
		// @aggiungere limiti iscrizioni
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->query(
				"INSERT INTO " . RELATION_USER_CIV_TABLE . "
				SET `user_id`='" . $id . "' ,
				`server`='" . SERVER . "' ,
				`civ_id`='" . $civ . "' ,
				`status`='" . $status . "'");
		return true;
	}
	/**
	 * restituisce le risorse
	 * @return Array
	 */
	function getResource ()
	{
		$this->aggResource();
		return $this->village->getResource();
	}
	/**
	 * aggiorna le risorse in caso di transazioni di risorse
	 * @param Array $trans
	 * @param bool $add
	 */
	function aggResource ($trans = array(0, 0, 0, 0), $add = false)
	{
		global $Building_Array;
		global $Troops_Array;
		//$this->aggProd();
		if ($add) {
			for ($i = 0; $i < 4; $i ++) {
				$trans[$i] = - $trans[$i];
			}
		}
		//popolazione in truppe
		$troopers_now = $this->troopers->troopers_now;
		$my_troopers = $this->troopers->my_troopers;
		//$this->log->log($troopers_now,Zend_Log::DEBUG);
		//$this->log->log($my_troopers,Zend_Log::DEBUG);
		$this->poptroop = 0;
		if ($troopers_now)
			foreach ($troopers_now as $key => $value) {
			$cost = $Troops_Array[$key]::$cost[3];
			$this->poptroop += $value['numbers'] * $cost;
		}
		if ($my_troopers)
			foreach ($my_troopers as $key => $value) {
			$cost = $Troops_Array[$key]::$cost[3];
			$this->poptroop += $value[$i]['numbers'] * $cost;
		}
		//pop nelle strutture in coda
		$queue = $this->getQueue();
		$popc = 0;
		for ($i = 0; $i < count($queue); $i ++) {
			$param = unserialize($queue[$i]['params']);
			$popc += $this->village->building[$this->currentVillage]->getLiv(
					$param['pos']) + 1;
		}
		$this->popc = $popc;
		//$this->log->log("popc $popc",Zend_Log::DEBUG);
		// popolazione nelle strutture
		$maxP = $this->village->building[$this->currentVillage]->getCapTot(
				HOUSE);
		$popT = $this->poptroop;
		$busypop = $this->village->data[$this->currentVillage]['busy_pop'];
		//$this->log->log("bsypop $busypop",Zend_Log::DEBUG);
		$negativ = $popT + $busypop - $maxP;
		if ($negativ < 0)
			$negativ = 0;
		for ($i = 0; $this->troopers->other_troopers[$i]; $i ++) {
			$negativ += $this->troopers->other_troopers[$i]['numbers'] *
			$Troops_Array[$this->troopers->other_troopers[$i]['trooper_id']]::$cost[3];
		}
		//$this->log->log("popt $popT",Zend_Log::DEBUG);
		$this->village->negativ = $negativ;
		$now = mktime();
		$agg = $this->village->data[$this->currentVillage]['agg'];
		// ore di differenza dall'ultimo aggiornamento
		$dif = ($now - $agg) / 3600;
		//aggiornamento risorse
		$this->village->data[$this->currentVillage]['resource_1'] += $dif *
		($this->village->data[$this->currentVillage]['production_1'] - $negativ);
		$this->village->data[$this->currentVillage]['resource_2'] += $dif *
		$this->village->data[$this->currentVillage]['production_2'];
		$this->village->data[$this->currentVillage]['resource_3'] += $dif *
		$this->village->data[$this->currentVillage]['production_3'];
		// controllo saturamento magazzino
		$sto1 = $this->village->building[$this->currentVillage]->getCapTot();
		$sto2 = $this->village->building[$this->currentVillage]->getCapTot(
				STORAGE2);
		if ($this->village->data[$this->currentVillage]['resource_1'] > $sto1)
			$this->village->data[$this->currentVillage]['resource_1'] = $sto1;
		if ($this->village->data[$this->currentVillage]['resource_2'] > $sto2)
			$this->village->data[$this->currentVillage]['resource_2'] = $sto2;
		if ($this->village->data[$this->currentVillage]['resource_3'] > $sto2)
			$this->village->data[$this->currentVillage]['resource_3'] = $sto2;
		//applicazione transazioni
		$this->village->data[$this->currentVillage]['resource_1'] -= $trans[0];
		$this->village->data[$this->currentVillage]['resource_2'] -= $trans[1];
		$this->village->data[$this->currentVillage]['resource_3'] -= $trans[2];
		$this->village->data[$this->currentVillage]['pop'] -= $trans[3];
		$deficit = ($this->village->data[$this->currentVillage]['production_1'] -
				$negativ);
		//applicazione bonus difesa
		$master_class = 'master' . $this->data['master'];
		$now = $this->currentVillage;
		$zone = ($this->village_list[$now]['zone'] == $this->data['master'] ? 1 : 0);
		$bonus = 100 + $master_class::$troops_bonus[$zone][2];
		if (($this->village->data[$this->currentVillage]['resource_1'] <= 0) &&
				($deficit < 0)) {
			// consumo_truppe : deficit = 100 : x  x=deficit*100/consumotruppe
			$defbonus = 100 + intval(
					$deficit * 100 / $this->poptroop);
			if ($defbonus < 1)
				$defbonus = 1;
			$bonus *= $defbonus / 100;
		}
		$this->village->data[$this->currentVillage]['defence'] = intval($bonus);
		// aggiornamento popolazione. @ todo controllare
		if ($this->village->data[$this->currentVillage]['resource_1'] < 0)
			$this->village->data[$this->currentVillage]['resource_1'] = 0;
		$dif = (mktime() - $this->village->data[$this->currentVillage]['aggPop']) /
		3600;
		if ($dif > 1) { // se da +di un ora che non aggiorno
			$pop = $this->village->data[$this->currentVillage]['pop'];
			if (($pop + $popT) <= $maxP) {
				$d = (int) $dif; // ore intere
				$nat = 1 / ($this->getAge() + 1); //@todo modificare a seconda della ricerca
				for ($i = 1; ($i <= $d) && ($pop < $maxP); $i ++) { //aggiorno ora per ora
					$pr = $pop * $nat / 2;
					$pop = $pr + $pop;
				}
				$this->village->data[$this->currentVillage]['aggPop'] += $d *
				3600; //aggiorno il contatore
				if ($pop > ($maxP - $popT))
					$pop = ($maxP - $popT);
			} else { //senza alloggi le persone muoiono
				$d = (int) $dif; // ore intere
				for ($i = 1; ($i <= $d) && ($pop > $busypop) &&
				(($pop + $popT) > $maxP); $i ++) { //aggiorno ora per ora
					$pop --;
				}
				$this->village->data[$this->currentVillage]['aggPop'] += $d *
				3600;
			}
			$this->village->data[$this->currentVillage]['pop'] = $pop;
		}
		$this->village->data[$this->currentVillage]['agg'] = mktime();
		$this->village->write();
	}
	/**
	 * villaggio selezionato
	 * @return int
	 */
	function getCurrentVillage ()
	{
		return $this->currentVillage;
	}
	/**
	 *
	 * @param int $set
	 */
	function setCurrentVillage ($set)
	{
		$this->currentVillage = $set;
	}
	/**
	 * ritorna l'età della civiltà
	 * @return int
	 */
	function getAge ()
	{
		return $this->data['civ_age'];
	}
	/**
	 * @todo da rifare
	 * @param $id
	 * aggiorna la produzione.
	 */
	static function aggProd ($id)
	{
		$bool = false;
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$res = $db->fetchRow(
				"SELECT * FROM `" . SERVER . "_map` WHERE `id`='$id'");
		$civ = $db->fetchRow(
				"SELECT * FROM `" . SERVER . "_civ` WHERE `civ_id`='" . $res['civ_id'] .
				"'");
		$build = $db->fetchAll(
				"SELECT * FROM `" . SERVER . "_building`
				WHERE `village_id`='" . $id . "' AND `type`IN('" . PROD1 . "','" . PROD2 .
				"','" . PROD3 . "') ORDER BY `pos`");
		$prod = array(0, 0, 0, 0);
		foreach ($build as $key => $value) {
			switch ($value['type']) {
				case PROD1:
					$prod[1] += prod1::$prod[$value['liv']];
					break;
				case PROD2:
					$prod[2] += prod2::$prod[$value['liv']];
					break;
				case PROD3:
					$prod[3] += prod3::$prod[$value['liv']];
					break;
			}
		}
		$master_class = 'master' . $civ['master'];
		//Zend_Registry::get("log")->log($build, Zend_Log::DEBUG);
		for ($i = 1; $i <= 3; $i ++) {
			//Zend_Registry::get("log")->log(" prod $i ".$prod, Zend_Log::DEBUG);
			$correct_prod = intval(
					$prod[$i] + (($res['zone'] == $civ['master']) ? ($prod[$i] / 100 *
							$master_class::$prod_bonus[1][$i]) : ($prod[$i] / 100 *
									$master_class::$prod_bonus[0][$i])));
			$correct_prod = intval(
					$correct_prod * $res['prod' . $i . '_bonus'] / 100);
			//Zend_Registry::get("log")->log("corret prod $correct_prod", Zend_Log::DEBUG);
			if ($res['production_' . $i] != $correct_prod) {
				$res['production_' . $i] = $correct_prod;
				$bool = true;
			}
		}
		if ($bool)
			$db->update(SERVER.'_map',
					array('production_1' => $res['production_1'],
							'production_2' => $res['production_2'],
							'production_3' => $res['production_3']), "`id`='" . $id . "'");
	}
	/**
	 * crea un villaggio alle coordinate date
	 *@todo rifare
	 * @param int $x or $vid if $y=id
	 * @param int|String $y
	 * @param int $cap
	 * @param int $civ_id
	 * @param int $type
	 * @param String $name
	 */
	static function addVillage ($x, $y, $civ_id, $cap = 0, $type = 0, $name = 'NEWVIL')
	{
		
		if ((is_string($y))&&($y=="id")) $vid=$x;
		else $vid=Model_map::getInstance()->getIdFromCoord($x, $y);
		$area=json_decode(file_get_contents(MAP_FILE),true);
		//.layers[0].data
		$log=Zend_Registry::get('log');
		$log->debug($area['layers'][0]['data'][$vid],'area');
		$bonus=Model_map::getInstance()->calcbonus($area['layers'][0]['data'][$vid]);
		self::$_defaultDb->insert(SERVER.'_map', array(
			'id'=>$vid
			,'civ_id'=>$civ_id
			,'name'=>$name
			,'capital'=>$cap
			,'type'=>$type
			,'pop'=>20
			,'busy_pop'=>0
			,'resource_1'=>START_RES
			,'resource_2'=>START_RES
			,'resource_3'=>START_RES
			,'production_1'=>prod1::$prod[0]
			,'production_2'=>prod2::$prod[0]
			,'production_3'=>prod3::$prod[0]
			,'agg'=>mktime()
			,'aggPop'=>mktime()
			,'prod1_bonus'=>$bonus[0]
			,'prod2_bonus'=>$bonus[1]
			,'prod3_bonus'=>$bonus[2]
		));
		self::$_defaultDb->query("INSERT INTO `" . SERVER . "_building` (`village_id`,`type`,`liv`,`pos`,`pop`)
				value ('" . $vid . "','1','0','0','1'), 
				('" . $vid . "','4','0','1','0'),
				('" . $vid . "','5','0','2','0'),
				('" . $vid . "','6','0','3','0')");//main liv 1 
	}
	/**
	 * crea coordinate casuali polari che poi saranno convertite in  cartesiane
	 * come raggio viene preso il raggio massimo del settore +o- $intervallo
	 * $alfa è random
	 * $sectorx 0 settore casuale, 1 negativo 2 positivo
	 * $sectory 0 settore casuale, 1 negativo 2 positivo
	 * @param int $intervallo
	 * @param int $sectorx
	 * @param int $sectorY
	 * @author Pagliaccio
	 * @return array
	 * @buglow possibile errore dopo 100 tentativi di trovare coordinate libere.
	 */
	static function randomcoord ($sectorx = 0, $sectory = 0, $intervallo = 10)
	{
		// discriminazione settore
		//discriminazione x
		if ($sectorx)
			$tipex = $sectorx - 1;
		else
			$tipex = rand(1, 2) - 1;
		//discriminazione y
		if ($sectory)
			$tipey = $sectory - 1;
		else
			$tipey = rand(1, 2) - 1;
		// massimo e minimo di alfa
		$max[0][0] = 270; //--
		$max[0][1] = 180; //-+
		$max[1][0] = 360; //+-
		$max[1][1] = 90; //++
		$min[0][0] = 180; //--
		$min[0][1] = 90; //-+
		$min[1][0] = 270; //+-
		$min[1][1] = 0; //++
		//inizializzazione
		$fine = true;
		$c = 0;
		$db = Zend_Db_Table::getDefaultAdapter();
		//richiamo le variabili del server
		$minrad = Zend_Registry::get("param")->get("minrad", 1);
		$dif = mktime() - Zend_Registry::get("param")->get("time", mktime());
		$dif = (int) $dif / 86400;
		$maxrad = $minrad + $intervallo + $dif;
		do { // inizio 1° ciclo per generare le variabili
			$quadmin = $minrad * $minrad;
			$quadmax = $maxrad * $maxrad;
			// numero di tentativi per trovare delle coordinate valide
			// oltre si aumenta il raggio
			$quad = ($quadmax - $quadmin) / 10;
			// controllo di fare almeno 1 tentativo ;)
			if ($quad < 1)
				$quad = 1;
			for ($count = Zend_Registry::get("param")->get("count", 0); ($fine) &&
			($count < $quad); $count ++) {
				//genero le coordinate
				$alfa = rand($min[$tipex][$tipey],
						$max[$tipex][$tipey]);
				$rad = rand($minrad, $maxrad);
				//controllo raggio sia almeno 1
				if ($rad < 1)
					$rad = 1;
				//trasformazione in cartesiane
				$x = $rad * cos(deg2rad($alfa));
				$y = $rad * sin(deg2rad($alfa));
				//rendo intere le coordinate
				$xint = (int) $x;
				$yint = (int) $y;
				if ($tipex == 1) { //se x è positivo arrotondo ad un numero + grande se x non è intero
					if ($xint < $x)
						$x = (int) $x + 1;
				} else { // idem
					if ($xint > $x)
						$x = (int) $x - 1;
				}
				if ($tipey == 1) {
					if ($yint < $y)
						$y = (int) $y + 1;
				} else {
					if ($yint > $y)
						$y = (int) $y - 1;
				} //rendo intere le variabili x e y per sicurezza
				$x = (int) $x;
				$y = (int) $y;
				if (($x > MAX_X/2) || ($x < - MAX_X/2) || ($y > MAX_Y/2) || ($y <
						- MAX_Y/2)) {
					$minrad = 0;
					$maxrad = $intervallo;
					Zend_Registry::get("param")->set("minrad", $minrad);
					Zend_Registry::get("param")->set("time", mktime());
				} elseif (! $db->fetchOne(
						"SELECT `type` FROM `" . SERVER . "_map` WHERE `id`='" . Model_map::getInstance()->getIdFromCoord($x, $y) ."'"))
						$fine = false;
			}
			if ($count >= $quad)
				Zend_Registry::get("param")->set("count", 0);
			else
				Zend_Registry::get("param")->set("count", $count);
			$maxrad += $intervallo;
			$minrad += $intervallo;
			if ($fine) {
				Zend_Registry::get("param")->set("minrad", $minrad);
				Zend_Registry::get("param")->set("time", mktime());
			}
			$c ++;
		} while (($fine) && ($c < 1000));
		if ($fine) {
			echo "errore!!!";
			exit();
		}
		return array('x' => $x, 'y' => $y);
	}
	/**
	 * @param int $id id villaggio
	 */
	function changeCurrentVillage ($id)
	{
		$server = Zend_Registry::get("server");
		$user=Model_user::getInstance();
		Zend_Db_Table::getDefaultAdapter()->query(
				"UPDATE `" . RELATION_USER_CIV_TABLE . "` SET `current_village`='" . $id .
				"' WHERE `user_id`='" . $user->data['ID'] .
				"' AND `SERVER`='" . $server . "'");
	}
	/**
	 * ritorna un array ordinato con le informazioni delle code di costruzioni.
	 * @param int $id
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	function getQueue ()
	{
		return $this->queue;
	}
	/**
	 * aggiunge al DB un invio di risorse. dest e mitt sono id villaggi,
	 * $res array risorse, n numero di mercanti occupati, time secondi necessari
	 * @param int $dest
	 * @param int $mitt
	 * @param Array $res
	 * @param int $n
	 * @param int $time
	 */
	function sendMercants ($dest, $mitt, $res, $n, $time, $rap = 1)
	{
		$this->getDefaultAdapter()->query(
				"INSERT INTO `" . EVENTS_TABLE . "` SET `type`='" . MARKET_EVENT .
				"' , `time`='" . ($time + mktime()) . "' , `params`='" . serialize(
						array('mittente' => intval($mitt), 'destinatario' => intval($dest),
								'res' => $res, 'n' => $n, 'time' => $time, 'rap' => $rap)) . "'");
	}
	/**
	 * ritorna un array con tutti gli invii di mercanti in arrivo e in uscita dal villaggio
	 * @return Array
	 */
	function getMercantsTravel ()
	{
		$row = null; // "mittente";i:1207;s:12:"destinatario";i:1;
		$row['out'] = $this->getDefaultAdapter()->fetchAll(
				"SELECT * FROM `" . EVENTS_TABLE .
				"` WHERE (`params`LIKE '%\"mittente\";i:" . $this->currentVillage .
				"%' OR `params`LIKE'%\"destinatario\";i:" . $this->currentVillage .
				"%') AND `type`='" . MARKET_EVENT . "'");
		$row['in'] = $this->getDefaultAdapter()->fetchAll(
				"SELECT * FROM `" . EVENTS_TABLE .
				"` WHERE `params`LIKE '%\"mittente\";i:" . $this->currentVillage .
				"%' AND `type`='" . RETURN_MERCANT_EVENT . "'");
		return $row;
	}
	/**
	 * da il nome del villaggio
	 * @param int $id
	 * @return String
	 */
	static function getVillageName (int $id)
	{
		$city=Model_map::getInstance()->city[$id];
		if ($city)
			return $city['name'];
		else
			return $this->t->_("TYLE_EMPTY");
	}
	/**
	 * mercanti occupati
	 * @return int
	 */
	function getMercantBusy ()
	{
		$col = $this->getDefaultAdapter()->fetchCol(
				"SELECT `params` FROM `" . EVENTS_TABLE . "` WHERE (`type`='" .
				MARKET_EVENT . "' OR `type`='" . RETURN_MERCANT_EVENT .
				"' ) AND `params`LIKE'%\"mittente\";i:" . $this->currentVillage . "%'");
		for ($i = 0, $n = 0; $col[$i]; $i ++) {
			$num = unserialize($col[$i]);
			$n += $num['n'];
		}
		return $n;
	}
	/**
	 * ritorna il nome delle risorse
	 * @param int $res
	 * @param int $age
	 * @return String
	 */
	function getNameResource ($res, $age)
	{
		return Model_civilta::$nameResource[$age][$res];
	}
	/**
	 * aggiorna le risorse nel villaggio
	 * @param int $id
	 * @return array risorse
	 */
	static function aggResourceById ($id)
	{
		global $Troops_Array;
		$res = Zend_Db_Table::getDefaultAdapter()->fetchRow(
				"SELECT * FROM `" . SERVER . "_map` WHERE `id`='" . $id . "'");
		$now = mktime();
		$agg = $res['agg'];
		// ore di differenza dall'ultimo aggiornamento
		$dif = ($now - $agg) / 3600;
		//aggiornamento risorse
		$res['resource_1'] += $dif * $res['production_1'];
		$res['resource_2'] += $dif * $res['production_2'];
		$res['resource_3'] += $dif * $res['production_3'];
		// controllo saturamento magazzino
		$sto1 = Model_civilta::getStorageById($id);
		$sto2 = Model_civilta::getStorageById($id, STORAGE2);
		if ($res['resource_1'] > $sto1)
			$res['resource_1'] = $sto1;
		if ($res['resource_2'] > $sto2)
			$res['resource_2'] = $sto2;
		if ($res['resource_3'] > $sto2)
			$res['resource_3'] = $sto2;
		//popolazione in truppe
		$troopers_now = Zend_Db_Table::getDefaultAdapter()->fetchAll(
				"SELECT * FROM `" . TROOPERS . "`
				WHERE `civ_id`='" . $res['civ_id'] . "'
				AND `village_now`='" . $id . "'
				AND `village_prev`='" . $id . "'
				ORDER BY `trooper_id`
				");
		$my_troopers = Zend_Db_Table::getDefaultAdapter()->fetchAll(
				"SELECT `" . TROOPERS . "`.*,
				`" . SERVER . "_map`.`name`
				FROM `" . TROOPERS . "`,`" . SERVER . "_map`
				WHERE `village_prev`='" . $id . "'
				AND `village_now`!='" . $id . "'
				AND `village_now`=`" . SERVER . "_map`.`id`
				ORDER BY `village_now`,`trooper_id`");
		for ($i = 0; ($troopers_now[$i]) || ($my_troopers[$i]); $i ++) {
			if ($troopers_now[$i]) {
				$key = $troopers_now[$i]['trooper_id'];
				$cost = $Troops_Array[$key]::$cost[3];
				$poptroop += $troopers_now[$i]['numbers'] * $cost;
			}
			if ($my_troopers[$i]) {
				$cost = $Troops_Array[$my_troopers[$i]['trooper_id']]::$cost[3];
				$poptroop += $my_troopers[$i]['numbers'] * $cost;
			}
		}
		//pop nelle strutture in coda
		$queue = Zend_Db_Table::getDefaultAdapter()->fetchAll(
				"SELECT * FROM `" . EVENTS_TABLE .
				"` WHERE `type`='1' AND `params`LIKE'%\"village_id\";i:" . $id .
				"%' ORDER BY `time`,`id` ASC");
		$popc = 0;
		for ($i = 0; $queue[$i]; $i ++) {
			$param = unserialize($queue[$i]['params']);
			$liv = Zend_Db_Table::getDefaultAdapter()->fetchOne(
					"SELECT `liv` FROM `" . SERVER . "_building` WHERE `pos`='" .
					$param['pos'] . "' AND `village_id`='" . $param['village_id'] . "'");
			$popc += $liv + 1;
		}
		// popolazione nelle strutture
		$build = Zend_Db_Table::getDefaultAdapter()->fetchAll(
				"SELECT * FROM `" . SERVER . "_building` WHERE `village_id`='" .
				$param['village_id'] . "'");
		$maxP = Model_civilta::getStorageById($id, HOUSE);
		$popT = $poptroop;
		$busypop = $res['busy_pop'];
		$negativ = $popT + $busypop - $maxP;
		//controllo morte truppe
		$prod = ($res['production_1'] - $negativ);
		$other_troopers = Zend_Db_Table::getDefaultAdapter()->fetchAll(
				"SELECT `" . TROOPERS . "`.*,
				`" . SERVER . "_map`.`name`
				FROM `" . TROOPERS . "`,`" . SERVER . "_map`
				WHERE `village_prev`!='" . $id . "'
				AND `village_now`='" . $id . "'
				AND `village_prev`=`" . SERVER . "_map`.`id`
				ORDER BY `village_prev`,`trooper_id`");
		while (($res['resource_1'] < 0) && ($prod < 0)) {
			if ($other_troopers) { //se ci sono rinforzi iniziamo con loro
				$prod = $this->killtroop($other_troopers, $prod,
						$res['resource_1']);
				$res['resource_1'] = $prod['res'];
				$prod = $prod['prod'];
			} elseif ($troopers_now) {
				$prod = $this->killtroop($troopers_now, $prod,
						$res['resource_1']);
				$res['resource_1'] = $prod['res'];
				$prod = $prod['prod'];
			} else {
				$prod = $this->killtroop($my_troopers, $prod,
						$res['resource_1']);
				$res['resource_1'] = $prod['res'];
				$prod = $prod['prod'];
			}
		}
		if ($res['resource_1'] < 0)
			$res['resource_1'] = 0;
		Zend_Db_Table::getDefaultAdapter()->query(
				"UPDATE `" . SERVER . "_map`  SET `resource_1`='" . $res['resource_1'] .
				"' , `resource_2`='" . $res['resource_2'] . "' , `resource_3`='" .
				$res['resource_3'] . "' , `agg`='" . mktime() . "' WHERE `id`='" . $id .
				"'");
		return array($res['resource_1'], $res['resource_2'], $res['resource_3']);
	}
	/**
	 * ritorna il valore dei magazzini
	 * @param int $id
	 * @param int $storagetype
	 * @return int
	 */
	static function getStorageById ($id, $storagetype = STORAGE1)
	{
		global $Building_Array;
		$build = Zend_Db_Table::getDefaultAdapter()->fetchAll(
				"SELECT * FROM `" . SERVER . "_building` WHERE `village_id`='" . $id .
				"' AND `type`='" . $storagetype . "'");
		$storage = 0;
		for ($i = 0; $build[$i]; $i ++) {
			$storage += $Building_Array[$storagetype - 1]::$capacity[$build[$i]['liv']];
		}
		if ($storage == 0) {
			if ($storagetype == STORAGE1) {
				$storage = storage1::$capacity[0];
			} elseif ($storagetype == STORAGE2) {
				$storage = storage2::$capacity[0];
			} else {
				$storage = house::$capacity[0];
			}
		}
		return $storage;
	}
	/**
	 * evolve la civiltà
	 * @return bool
	 */
	function evolution ()
	{
		if ($this->data['ev_ready']) {
			$age = $this->getAge() + 1;
			$this->getDefaultAdapter()->update(CIV_TABLE,
					array('ev_ready' => '0', 'civ_age' => $age, 'quest' => '1'),
					"`civ_id`='" . $this->cid . "'");
			$this->option->set("change_god", '1');
			$this->option->set("lastEv", mktime());
			$this->option->del("have_god");
			return true;
		} else
			return false;
	}
	/**
	 * compara le due coordinate
	 * @param Array $a
	 * @param Array $b
	 * @return int
	 */
	function compare ($a, $b)
	{
		$ax = $a['x'] - $this->coord['x'];
		$ay = $a['y'] - $this->coord['y'];
		$da = (int) sqrt($ax * $ax + $ay * $ay);
		$bx = $b['x'] - $this->coord['x'];
		$by = $b['y'] - $this->coord['y'];
		$db = (int) sqrt($bx * $bx + $by * $by);
		return $da - $db;
	}
	/**
	 * compara l'ordine presonalizzato
	 * @param Array $a
	 * @param Array $b
	 * @return int
	 */
	function costomCompare ($a, $b)
	{
		return $a['order_n'] - $b['order_n'];
	}
	/**
	 * ritorna l'istanza della civiltà
	 * @return Model_civilta
	 */
	static function getInstance ()
	{
		if (Zend_Registry::isRegistered("civ"))
			return Zend_Registry::get("civ");
		else
			return false;
	}
}
