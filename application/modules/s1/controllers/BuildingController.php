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
	//private $token;
	public function init ()
	{
		$this->civ = Model_civilta::getInstance();
		$this->now = $this->civ->getCurrentVillage();
		$this->req = $this->getRequest();
		$this->pos = intval($this->req->getParam("pos", 0));
		if (is_numeric($this->req->getParam('t')))
		$this->pos = $this->civ->village->building[$this->now]->getBildForType(
		$this->req->getParam('t'));
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
		$age = $this->civ->getAge();
		//$token = token_set("tokenB");
		$age = $this->civ->getAge();
		$display = '';
		if (isset($this->civ->village->building[$this->now]->data[$this->pos])) {
			//visualizza edificio
			$this->p = $this->civ->village->building[$this->now]->getproperty(
			$this->pos, $this->civ->getAge());
			$type = $this->civ->village->building[$this->now]->data[$this->pos]['type'];
			$liv = $this->civ->village->building[$this->now]->data[$this->pos]['liv'];
			$name = Model_building::$name[$this->civ->getAge()][$this->civ->village->building[$this->now]->data[$this->pos]['type']] .
             " " . $this->t->_("livello") . " " . $liv;
			$button = $this->t->_("aumenta al livello") . " " . ($liv + 1);
			$a = "u";
			$display .= '<h2>' .
			Model_building::$name[$this->civ->getAge()][$type] . ' liv ' . $liv .
             '</h2>';
			$display .= $Building_Array[$type - 1]::$Description[$this->civ->getAge()] .
             '<br/>';
			switch ($type) {
				case MARKET:
					$display .= '
    <script type="text/javascript">
	$(function() {
		$( "#tabs" ).tabs({
			ajaxOptions: {
				error: function( xhr, status, index, anchor ) {
					$( anchor.hash ).html(
						"load error" );
				}
			}
		});
	});
	</script>
	

<div class="ui-tabs ui-widget ui-widget-content ui-corner-all" id="tabs">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li><a href="' .
					$this->_helper->url("market", null, null,
					array('section' => '1')) . '">' . $this->t->_('invia') . '</a></li>
		<li><a href="' .
					$this->_helper->url("market", null, null,
					array('section' => '2')) . '">' . $this->t->_('compra') . '</a></li>
		<li><a href="' .
					$this->_helper->url("market", null, null,
					array('section' => '3')) . '">' . $this->t->_('vendi') . '</a></li>
		<li><a href="' .
					$this->_helper->url("market", null, null,
					array('section' => '4')) . '">' . $this->t->_('mercato nero') . '</a></li>
	</ul>
	
</div>';
					break;
				case BARRACK:
					$display .= '
    <script type="text/javascript">
	$(function() {
		$( "#tabs" ).tabs({
			ajaxOptions: {
				error: function( xhr, status, index, anchor ) {
					$( anchor.hash ).html(
						"load error" );
				}
			}
		});
	});
	</script>
	

<div class="ui-tabs ui-widget ui-widget-content ui-corner-all" id="tabs">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li><a href="' .
					$this->_helper->url("barrak", null, null,
					array('section' => '1')) . '">' . $this->t->_('addestra') . '</a></li>
		<li><a href="' .
					$this->_helper->url("barrak", null, null,
					array('section' => '2')) . '">' . $this->t->_('promuovi') . '</a></li>
		<li><a href="' .
					$this->_helper->url("barrak", null, null,
					array('section' => '3')) . '">' . $this->t->_('congeda') . '</a></li>
	</ul>
	
</div>';
					break;
				case COMMAND:
					$n = count($this->civ->village_list);
					$img = new Zend_View_Helper_image();
					$train = $this->civ->training;
					global $NameTroops, $Troops_Array;
					$display .= '<script type="text/javascript">resetinit();</script>
	<table class="troopers" style="margin-left: 10px;">
	<thead><td></td>
	<td>' . $this->t->_("numero") . '</td>
	<td>' . $this->t->_("durata") . '</td>
	<td>' . $this->t->_("finito") . '</td></thead>';
					for ($i = 0; $train[$i]; $i ++) {
						$params = unserialize($train[$i]['params']);
						$dif = $train[$i]['time'] - mktime();
						if ($dif <= 0)
						$dif = "00:00:0?";
						if ($params['trooper_id'] == 4)
						$display .= '<tr><td>' .
						$img->image()->Troop($params['trooper_id']) . '</td>
		<td>' . $params['num'] . '</td>
		<td><span class="countDown">' . $dif . '</span> </td>
			<td>' . date("H:i:s", $train[$i]['time']) . '</td></tr>';
					}
					$display .= "</table><br/>";
					//$tokenc = token_set("tokenCo");
					$display .= $Building_Array[$type - 1]::$content . ' <h3>' .
					$this->t->_('colonizzazioni') . ' ' . $n . '/' . $liv . '</h3>
                	<form action="' . $this->_helper->url("traincolony", 
                    "trainer", null/*, array('tokenCo' => $tokenc)*/) .
                     '" method="post"><div>' . $img->resource(3, 0);
					for ($i = 0; $i < 3; $i ++)
					$display .= ' ' . colony::$cost[$i] . ' ' .
					$img->resource($i, $age);
					$display .= ' <input class="number" name="num" size="4"/></div><input type="submit" value="' .
					$this->t->_('Assumi') . '" /></form>';
					break;
				case RESEARCH:
					//$tokenRe = token_set("tokenRe");
					$pr = $this->civ->pr;
					$bpr = $this->civ->bpr;
					$disp = $this->civ->research->dispRes();
					global $research_array;
					$display .= $this->t->_('Punti ricerca disponibili') . ' :' .
					($pr - $bpr) .
					//'<script type="text/javascript">ev.token.tokenRe=\'' .
					//$tokenRe . '\';</script>'.
                	'<table class="table-research">
                	<thead>
                		<tr>
                			<th>' . $this->t->_('Nome') . '</th>
                			<th>' .
					$this->t->_('Descrizione') . '</th>
                			<th>' . $this->t->_('Livello') . '</th>
                			<th>' . $this->t->_('Costo') . '</th>
                			<th></th>
                		</tr>
                	</thead>';
					foreach ($research_array as $ty => $res) {
						$require = $res::$require;
						$req = "";
						if ($require) {
							$req = '<div>' . $this->t->_('Richiede') . ':<ul>';
							foreach ($require['research'] as $value) {
								$req .= '<li>' .
								$research_array[$value['type']]::$name . ' liv ' .
								$value['liv'] . '</li>';
							}
							foreach ($require['build'] as $value) {
								$req .= '<li>' .
								Model_building::$name[$this->civ][$value['type']] .
                                 ' liv ' . $value['liv'] . '</li';
							}
						}
						//$require=print_r($require,true);
						$livr = (int) $this->civ->research->data[$ty]['liv'];
						$label = $livr > 0 ? $this->t->_('Aumenta') : $this->t->_(
                        'Ricerca');
						if ($res::$livmax[$this->civ->getAge()] <=
						$this->civ->research->data[$ty]['liv'])
						$label = $this->t->_('livello massimo');
						if ($disp[$res])
						$attr = 'onclick="ev.research(' . $ty . ');"';
						else
						$attr = 'disabled="disabled"';
						$display .= '
                		<tr>
                			<td>' . $res::$name . '</td>
                			<td>' . $res::$description . $req . '</td>
                			<td id="liv' . $ty . '">' .
						$livr . '</td>
                			<td>' . $res::$cost[$livr] . '</td>
                			<td><button id="button' . $ty .
                         '" ' . $attr . '>' . $label . '</button></td>
                		</tr>';
					}
					$display .= '</table>';
					break;
				default:
					$display .= $Building_Array[$type - 1]::getContent($liv);
					break;
			}
			$display .= '<div>' . $this->t->_("Costo") . ' : ';
			for ($i = 0; $i < 4; $i ++) {
				$var = "resource_" . ($i + 1);
				$color = "";
				if ($i < 3) {
					if ($this->p['cost'][$i] >
					$this->civ->village->data[$this->now][$var])
					$color = "color: red;";
				}
				$display .= ' <span style="' . $color . '">' .
				$this->p['cost'][$i] . '</span> ' .
				$this->view->image()->resource($i, $age) . ' &nbsp &nbsp &nbsp ';
			}
			$time = $this->p['time'];
			$can = $this->civ->village->building[$this->now]->canBuild(
			$this->p['cost'], $this->civ->getResource(), $this->p['liv'],
			$this->pos, 0,$age,$this->p['maxliv']);
			$display .= ' ' . timeStampToString($time) . '</div>';
			if (! $can['bool'])
			$display .= '<div class="gray">' . $can['mess'] . '</div>';
			else {
				$display .= '<div><a href="#" onclick="ev.request(module + \'/building/upgrade/pos/'.$this->pos.'\', \'post\', {
				ajax : \'true\'
			});$(this).parent().parent().dialog(\'close\');">' .
				$this->t->_($button) . '</a></div>';
			}
		} else { // costruzione edificio
			$name = $this->t->_("costruisci edificio");
			$button = $this->t->_("costruisci");
			$a = "b";
			$BuildingDisp = $this->civ->village->building[$this->now]->getDispBuilding(
			$this->civ->getAge());
			foreach ($BuildingDisp as $key => $value) {
				if ($value) {
					$name2 = Model_building::$name[$this->civ->getAge()][$key];
					$display .= '<h3>' . $this->t->_("Costruisci") . ' ' . $name2 .
                     '</h3>';
					$t = $key - 1;
					$this->p = $this->civ->village->building[$this->now]->getproperty(
					$this->pos, $this->civ->getAge(), $t);
					$display .= $Building_Array[$t]::$Description[$this->civ->getAge()] .
                     '<br/>';
					$display .= '<div>' . $this->t->_("Costo") .
                     ' : &nbsp &nbsp ';
					for ($j = 0; $j < 4; $j ++) {
						$var = "resource_" . ($j + 1);
						$color = "";
						if ($j < 3) {
							if ($this->p['cost'][$i] >
							$this->civ->village->data[$this->now][$var])
							$color = "color: red;";
						}
						$display .= ' <span style="' . $color . '">' .
						$this->p['cost'][$j] . '</span> ' .
						$this->view->image()->resource($j, $age) .
                         ' &nbsp &nbsp ';
					}
					$time = $this->p['time'];
					$display .= ' ' . timeStampToString($time) . '</div>';
					$can = $this->civ->village->building[$this->now]->canBuild(
					$this->p['cost'], $this->civ->getResource(), 0, $this->pos,$key,$age);
					if (! $can['bool'])
					$display .= '<div class="gray">' . $can['mess'] .
                         '</div>';
					else {
						$display .= '<div><a href="#" onclick="ev.request(module + \'/building/build/pos/'.$this->pos.'/type/'.$key.'\', \'post\', {
				ajax : \'true\'
			},function(){ev.refresh();});$(this).parent().parent().dialog(\'close\');">' . $this->t->_($button) . '</a></div>';
					}
				}
			}
		}
		$this->view->display = $display;
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
		$this->civ->getResource(), 0, $this->pos,$type,$this->civ->getAge());
		if (($can['bool']) /*&& ($this->token['tokenB'])*/) {
			$error=FALSE;
			try {
				Zend_Db_Table::getDefaultAdapter()->query(
            "INSERT INTO `" . SERVER . "_building` SET `village_id`='" .
				$this->now . "' , `type`='" . $type . "' , `liv`='0' , `pos`='" .
				$this->pos . "'");
			}
			catch (Zend_Db_Exception $e) {
				$this->view->error="[CANTBUILD]";
				$error=true;
			}
			$cost=$Building_Array[$type - 1]::$cost[0];
			$cost[3]=0;
			$this->civ->aggResource($cost);
			if (!$error) {
				$this->civ->village->building[$this->now]->data[$this->pos]=array('liv'=>0,'type'=>$type);
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
	public function upgradeAction ()
	{
		$this->p = $this->civ->village->building[$this->now]->getproperty(
		$this->pos, $this->civ->getAge());
		$liv = $this->civ->village->building[$this->now]->getLiv($this->pos);
		$can = $this->civ->village->building[$this->now]->canBuild(
		$this->p['cost'], $this->civ->getResource(), $liv, $this->pos,0,$this->civ->getAge());
		$this->view->layout()->x = 300;
		$this->view->layout()->y = 200;
		if (($can['bool']) /*&& ($this->token['tokenB'])*/) {
			if (isset($this->civ->village->building[$this->now]->data[$this->pos])) {
				$type = $this->civ->village->building[$this->now]->data[$this->pos]['type'];
				$this->p['cost'][3]=0;
				$this->civ->aggResource($this->p['cost']);
				$this->civ->village->building[$this->now]->addQueue(
				$this->p['time'], $type, $this->pos,
				$this->civ->getCurrentVillage());
				$queue = $this->civ->getQueue()->toArray();
				$param = serialize(array('pos' => $this->pos, 'village_id' => $this->now));
				$queue[] = array('params' => $param,'time' => (time() + $this->p['time']));
				/*require_once APPLICATION_PATH .
				 '/views/helpers/template.php';
				 $tmp = new Zend_View_Helper_template();
				 $this->civ->refresh->addIds('queue', $tmp->queue($queue, true));
				 $this->civ->refresh->addIds('resbar', $tmp->resourceBar());*/
				$this->civ->queue=$queue;
				$this->civ->refresh(array('order'=>true));
			}
		} else {
			if (! $can['bool']) $this->view->error = $can['mess'];
		}
		//$this->civ->refresh->addToken('tokenB', token_set("tokenB"));
		if (! $_POST['ajax']) $this->_helper->redirector("index", "index",$this->req->getModuleName());
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
		$liv = $this->civ->village->building[$this->now]->getLiv($pos);
		$disp = $liv - $this->civ->getMercantBusy();
		$this->view->disp = $disp;
		$this->view->liv = $liv;
		//$this->view->token = token_set("tokenM");
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
}
