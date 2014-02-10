<?php
class S1_MovementsController extends Zend_Controller_Action
{
    public $t = null;
    public $module = null;
    /**
     * @var Model_civilta
     */
    public $civ = null;
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    public $db = null;
    public function init ()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $this->t = Zend_Registry::get("translate");
        $this->view->t = $this->t;
        $this->module = $this->getRequest()->getModuleName();
        $this->view->module = $this->module;
        $send = $this->t->_("invia truppe");
        $sim = $this->t->_("simulatore");
        $this->view->layout()->nav = array(
        $send => array('url' => $this->module . '/movements/send'), 
        $sim => array('url' => $this->module . '/simulator'));
        $this->civ = Model_civilta::getInstance();
        $this->view->age = $this->civ->getAge();
        $this->view->cid = $this->civ->cid;
        $this->log = Zend_Registry::get("log");
    }
    public function indexAction ()
    {
        $this->view->troupper_now = $this->civ->troopers->troopers_now;
        $this->view->my_troopers = $this->civ->troopers->my_troopers;
        $this->view->other_troopers = $this->civ->troopers->other_troopers;
        $this->view->inAttack = $this->civ->inAttack;
        $this->view->outAttack = $this->civ->outAttack;
        $this->view->inReinf = $this->civ->inReinf;
        $this->view->outReinf = $this->civ->outReinf;
        $this->view->civ_id = $this->civ->cid;
        //$this->log->log($this->civ->village->data, Zend_Log::DEBUG);
    }
    public function changehomeAction ()
    {
        error_reporting(E_ALL);
        // controllo villaggio di proprietà
        $vid = (int) $this->getRequest()->getParam("vid");
        $bool = false;
        foreach ($this->civ->village->data as $key => $value) {
            if ($key == $vid) {
                $bool = true;
                break;
            }
        }
        if ($bool) {
            $tn = $this->db->fetchCol(
            "SELECT `trooper_id` FROM `" . TROOPERS . "` WHERE `village_now`='" .
             $this->civ->getCurrentVillage() . "' AND `village_prev`='" .
             $this->civ->getCurrentVillage() . "'");
            $ts = $this->db->fetchAll(
            "SELECT `trooper_id`,`numbers` FROM `" . TROOPERS .
             "` WHERE `village_now`='" . $this->civ->getCurrentVillage() .
             "' AND `village_prev`='" . $vid . "'");
            $filter = array();
            for ($i = 0; $ts[$i]; $i ++) {
                if (in_array($ts[$i]['trooper_id'], $tn)) {
                    $filter[] = $ts[$i]['trooper_id'];
                    $this->db->query(
                    "UPDATE `" . TROOPERS . "` SET `numbers`=`numbers`+'" .
                     $ts[$i]['numbers'] . "' WHERE `village_now`='" .
                     $this->civ->getCurrentVillage() . "' AND `village_prev`='" .
                     $this->civ->getCurrentVillage() . "' AND `trooper_id`='" .
                     $ts[$i]['trooper_id'] . "'");
                    $this->db->query(
                    "DELETE FROM `" . TROOPERS . "` WHERE `village_now`='" .
                     $this->civ->getCurrentVillage() . "' AND `village_prev`='" .
                     $vid . "' AND `trooper_id`='" . $ts[$i]['trooper_id'] . "'");
                }
            }
            $this->db->query(
            "UPDATE `" . TROOPERS . "` SET `village_prev`='" .
             $this->civ->getCurrentVillage() . "' WHERE `village_now`='" .
             $this->civ->getCurrentVillage() . "' AND `village_prev`='" . $vid .
             "' " . ($filter ? "AND `trooper_id` NOT IN ('" . implode("','", 
            $filter) . "')" : ""));
        }
        if (!$_POST['ajax']) $this->_helper->redirector("index");
    }
    public function ritAction ()
    {
        global $Troops_Array;
        $vid = (int) $this->getRequest()->getParam("vid");
        $type = ($this->getRequest()->getParam("vid") == 1 ? 1 : 0);
        //param int:'type',int:'village_A',int:'village_b',array:'troopers',array:'resource'
        $params['type'] = RETURN_T;
        $params['civ_id'] = $this->civ->cid;
        if ($type == 1) {
            $tr = $this->civ->troopers->other_troopers;
            $index = "village_prev";
            $index2 = "village_now";
            $params['village_A'] = $this->civ->getCurrentVillage();
            $params['village_B'] = $vid;
        } else {
            $tr = $this->civ->troopers->my_troopers;
            $index = "village_now";
            $index2 = "village_prev";
            $params['village_B'] = $this->civ->getCurrentVillage();
            $params['village_A'] = $vid;
        }
        $params['troopers'] = array();
        $speed = 0;
        foreach ($tr as $key => $value) {
            if (! $speed)
                $speed = $Troops_Array[$key]::$speed;
            if ($value['village_now'] == $params['village_A']) {
                $params['troopers'][$key] = $value['numbers'];
                $coord2 = array('x' => $value['x'], 'y' => $value['y']);
                if ($speed > $Troops_Array[$key]::$speed)
                    $speed = $Troops_Array[$key]::$speed;
            }
        }
        $params['resource'] = array(0, 0, 0);
        $p = serialize($params);
        $dist = getDistance(
        array('x' => $this->civ->village->data[$this->civ->getCurrentVillage()]['x'], 
        'y' => $this->civ->village->data[$this->civ->getCurrentVillage()]['y']), $coord2);
        $time = getTime($dist, $speed);
        $this->db->query(
        "INSERT INTO `" . EVENTS_TABLE . "` SET `type`='" . MOVEMENTS_EVENT .
         "' , `params`='" . $p . "' , `time`='" . (mktime() + $time) . "'");
        $this->_log->startMovement("truppe ".print_r($params['troopers'],true).
                        " si ritirano dal ".$params['village_A']." verso ".$params['village_B'].
                        " arrivo stimato ".date('H:i:s d/m/Y',(mktime() + $time)));
        $this->db->query(
        "DELETE FROM `" . TROOPERS . "` WHERE `village_prev`='" .
         $params['village_B'] . "' AND `village_now`='" . $params['village_A'] .
         "'");
        $this->_log->startMovement("tabella truppe aggiornata");
        if (!$_POST['ajax']) $this->_helper->redirector("index");
    }
    public function ritpartAction ()
    {
        $vid = $this->getRequest()->getParam("vid");
        $type = ($this->getRequest()->getParam("type") == 1 ? 1 : 0);
        $token = sha1(auth());
        Zend_Auth::getInstance()->getStorage()->set("tokenRit", $token);
        if ($type == 1) {
            $troop = $this->civ->troopers->other_troopers;
            $index = "village_prev";
        } else {
            $troop = $this->civ->troopers->my_troopers;
            $index = "village_now";
        }
        $this->log->log($troop, Zend_Log::DEBUG);
        foreach ($troop as $key => $value) {
            if ($value[$index] == $vid)
                $troops[$value['trooper_id']] = $value['numbers'];
        }
        $this->view->troops = $troops;
        $this->view->token = $token;
        $this->view->urlaction = $this->_helper->url("doritpart", "movements", 
        $this->module, array('vid' => $vid, 'type' => $type));
    }
    public function doritpartAction ()
    {
        global $Troops_Array;
        $vid = (int) $this->getRequest()->getParam("vid");
        $type = ($this->getRequest()->getParam("type") == 1 ? 1 : 0);
        $params['type'] = RETURN_T;
        $params['civ_id'] = $this->civ->cid;
        if ($_GET['type'] == 1) {
            $tr = $this->civ->troopers->other_troopers;
            $index = "village_prev";
            $index2 = "village_now";
            $params['village_A'] = $this->civ->getCurrentVillage();
            $params['village_B'] = $vid;
        } else {
            $tr = $this->civ->troopers->my_troopers;
            $index = "village_now";
            $index2 = "village_prev";
            $params['village_B'] = $this->civ->getCurrentVillage();
            $params['village_A'] = $vid;
        }
        $params['troopers'] = null;
        $speed = 0;
        $log = Zend_Registry::get("log");
        $log->log("tr " . print_r($tr, true), Zend_Log::DEBUG);
        foreach ($tr as $key => $value) {
            if (! $speed)
                $speed = $Troops_Array[$key]::$speed;
            if ($value[$index] == $vid) {
                $num = intval($_POST['t' . $key]);
                if ($num) {
                    if ($value['numbers'] > $num) {
                        $params['troopers'][$key] = $num;
                        $this->db->query(
                        "UPDATE `" . TROOPERS .
                         "` SET `numbers`=`numbers`-'$num' WHERE `village_prev`='" .
                         $params['village_B'] . "' AND `village_now`='" .
                         $params['village_A'] . "' AND `trooper_id`='" . $key .
                         "'");
                    } else {
                        $params['troopers'][$key] = $value['numbers'];
                        $this->db->query(
                        "DELETE FROM `" . TROOPERS . "` WHERE `village_prev`='" .
                         $params['village_B'] . "' AND `village_now`='" .
                         $params['village_A'] . "' AND `trooper_id`='" . $key .
                         "'");
                    }
                    if ($speed > $Troops_Array[$key]::$speed)
                        $speed = $Troops_Array[$key]::$speed;
                }
                $coord2 = array('x' => $value['x'], 'y' => $value['y']);
            }
        }
        if ($params['troopers']) {
            $params['resource'] = array(0, 0, 0);
            $p = serialize($params);
            $dist = getDistance(
            array('x' => $this->civ->village->data[$this->civ->getCurrentVillage()]['x'], 
            'y' => $this->civ->village->data[$this->civ->getCurrentVillage()]['y']), $coord2);
            $time = getTime($dist, $speed);
            $this->db->query(
            "INSERT INTO `" . EVENTS_TABLE . "` SET `type`='" . MOVEMENTS_EVENT .
             "' , `params`='" . $p . "' , `time`='" . (mktime() + $time) . "'");
            $this->_log->startMovement("truppe ".print_r($params['troopers'],true).
                        " si ritirano dal ".$params['village_A']." verso ".$params['village_B'].
                        " arrivo stimato ".date('H:i:s d/m/Y',(mktime() + $time)));
        }
        if (!$_POST['ajax']) $this->_helper->redirector("index");
    }
    public function sendAction ()
    {
    	$map=Model_map::getInstance();
        $this->view->type = $this->getRequest()->getParam("type", "attack");
        $this->view->token = sha1(auth());
        Zend_Auth::getInstance()->getStorage()->set("tokenMov", 
        $this->view->token);
        $villages = null;
        $list = $this->civ->village_list;
        $coord['x'] = '0';
        $coord['y'] = '0';
        $tr = null;
        $id = (int) $this->getRequest()->getParam("vid", 0);
        if ($id > 0)
            $coord = $map->getCoordFromId($id);
        $this->log->log($this->civ->troopers->troopers_now, Zend_Log::DEBUG);
        if ($this->civ->troopers->troopers_now) {
            foreach ($this->civ->troopers->troopers_now as $key => $value) {
                $tr[$key] = $value['numbers'];
            }
        }
        $this->view->troops = $tr;
        $this->log->log($this->view->troops, Zend_Log::DEBUG);
        foreach ($list as $key => $value)
            if ($key != $this->civ->getCurrentVillage())
                $villages .= '<option value="' . $key . '" title="' . $value['x'] .
                 '|' . $value['y'] . '">' . $value['name'] . '</option>';
        $this->view->villages = $villages;
        $this->view->coord = $coord;
    }
    public function dosendAction ()
    {
        global $Troops_Array;
        $map=Model_map::getInstance();
        $user = Zend_Auth::getInstance()->getIdentity();
        if (($_POST['tokenMov'] == $user->tokenMov) && ($user->tokenMov)) {
            for ($i = 0; $i < TOT_TYPE_TROOPS; $i ++) {
                if (($_POST['t' . $i] > 0) && (is_numeric($_POST['t' . $i])))
                    $t[$i] = $_POST['t' . $i];
            }
            $coord = array('x' => $_POST['x'], 'y' => $_POST['y']);
            $idv = $map->getIdFromCoord($coord['x'],$coord['y']);
            // controllo id valido
            if ($idv) {
                //tempo di attraversata */
                $speed = 0;
                foreach ($t as $key => $value) {
                    if (($speed == 0) || ($speed > $Troops_Array[$key]::$speed))
                        $speed = $Troops_Array[$key]::$speed;
                }
                if ($speed != 0) {
                    $dist = getDistance($coord, 
                    array(
                    'x' => $this->civ->village->data[$this->civ->getCurrentVillage()]['x'], 
                    'y' => $this->civ->village->data[$this->civ->getCurrentVillage()]['y']));
                    $time = getTime($dist, $speed);
                    //truppe presenti
                    $troop = $this->civ->troopers->troopers_now;
                    $tr = null;
                    foreach ($troop as $key => $value) {
                        $tr[$key] = $value['numbers'];
                    }
                    $bool = true;
                    foreach ($t as $key => $value) {
                        if ($value > $tr[$key]) {
                            $bool = false;
                            break;
                        }
                    }
                    if ($bool) {
                        /**
                         * array troopers[$idtruppe]=numero_truppe array resource[3]
                         * Array $param int:'civ_id',int:'type',int:'village_A',int:'village_b',array:'troopers',array:'resource'
                         */
                        $params['civ_id'] = $this->civ->cid;
                        $params['type'] = (int) $_POST['type'];
                        $params['village_A'] = $this->civ->getCurrentVillage();
                        $params['village_B'] = (int) $idv;
                        $params['troopers'] = $t;
                        $params['round'] = $_POST['round'];
                        $params['time'] = mktime();
                        $params['coord'] = $coord;
                        $param = serialize($params);
                        $this->db->query(
                        "INSERT INTO `" . EVENTS_TABLE . "` SET `time`='" .
                         (mktime() + $time) . "' , `type`='" . MOVEMENTS_EVENT .
                         "' , `params`='" . $param . "'");
                        $this->_log->startMovement("truppe ".print_r($params['troopers'],true).
                        " inviate da ".$params['village_A']." verso ".$params['village_B'].
                        " tipo di movimento ".$params['type'].
                        " arrivo stimato ".date('H:i:s d/m/Y',(mktime() + $time)).
                        " round richiesti ".$params['round']);
                        foreach ($t as $key => $value) {
                            //aggiorno i dati delle truppe
                            $this->civ->troopers->troopers_now[$key]['numbers'] -= $value;
                            if ($value == $tr[$key])
                                $this->db->query(
                                "DELETE FROM `" . TROOPERS .
                                 "` WHERE `village_prev`='" .
                                 $this->civ->getCurrentVillage() .
                                 "' AND `village_now`='" .
                                 $this->civ->getCurrentVillage() .
                                 "' AND `civ_id`='" . $this->civ->cid .
                                 "' AND `trooper_id`='" . $key . "'");
                            else
                                $this->db->query(
                                "UPDATE `" . TROOPERS .
                                 "` SET `numbers`=`numbers`-'" . $value .
                                 "' WHERE `village_prev`='" .
                                 $this->civ->getCurrentVillage() .
                                 "' AND `village_now`='" .
                                 $this->civ->getCurrentVillage() .
                                 "' AND `civ_id`='" . $this->civ->cid .
                                 "' AND `trooper_id`='" . $key . "'");
                        }
                    }
                }
            }
        } else
            $this->_log->warn("errore token");
        if (! $_POST['ajax'])
            $this->_helper->redirector("index");
            else $this->civ->refresh(array('event'=>true));
    }
    function rAction ()
    {
        $id = (int) $this->getRequest()->getParam("id");
        $mov = $this->db->fetchRow(
        "SELECT * FROM `" . EVENTS_TABLE . "` WHERE `id`='" . $id .
         "' AND `type`='" . MOVEMENTS_EVENT . "'");
        $params = unserialize($mov['params']);
        $time = mktime() - $params['time'];
        if (($params['type'] != RETURN_T) && ($time < 120)) {
        	$this->_log->movement("truppe ".print_r($params['troopers'],true).
                        " si dirigevano da ".$params['village_A']." a ".$params['village_B'].
        	"in modalità ".$params['type'].
                        " arrivo stimato ".date('H:i:s d/m/Y',$mov['time'])." saranno ritirate");
            $a = $params['village_B'];
            $b = $params['village_A'];
            $params['village_A'] = $a;
            $params['village_B'] = $b;
            $params['type'] = RETURN_T;
            $p = serialize($params);
            $this->db->query(
            "INSERT INTO `" . EVENTS_TABLE . "` SET `type`='" . MOVEMENTS_EVENT .
             "' , `time`='" . ($time + mktime()) . "' , `params`='" . $p . "'");
            $this->db->query(
            "DELETE FROM `" . EVENTS_TABLE . "` WHERE `id`='" . $id . "'");
            $this->_log->movement("movimento ritirato, orario di ritorno ".
            date('H:i:s d/m/Y',($time + mktime()))." parametri ".print_r($params,true));
            
        }
        if (!$_POST['ajax']) $this->_helper->redirector("index");
    }
    /**
     * 
     */
    public function gettimeAction ()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        //compatibility layer
        $x = (int) $_POST['x'];
        $y = (int) $_POST['y'];
        $vid=is_numeric($_POST['vid']) ? $_POST['vid'] :Model_map::getInstance()->getIdFromCoord($x, $y);
        $village=Model_map::getInstance()->getVillageArray();
        $reply = array('data' => false, 'html' => false, 'javascript' => false, 
        'update' => array(
        'ids' => array('village_player' => $village[$vid]['civ_name'], 
        'village_name' => $village[$vid]['name'], 'village_ally' => $village[$vid]['ally'])));
        echo json_encode($reply);
        Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
    }
    public function colonyAction ()
    {
    	$map=Model_map::getInstance();
        Zend_Layout::getMvcInstance()->disableLayout();
        $id = (int) $this->getRequest()->getParam("id");
        $nump = (int) $_POST['num'];
        $n = count($this->civ->village_list);
        $vids = array_keys($this->civ->village_list);
        $liv = $this->db->fetchOne(
        "SELECT `liv` FROM `" . SERVER . "_building` WHERE `type`='" . COMMAND .
         "' AND `village_id`IN('" . implode("','", $vids) . "')");
        $now = $this->civ->getCurrentVillage();
        $num = $this->db->fetchOne(
        "SELECT `numbers` FROM `" . TROOPERS .
         "` WHERE `village_prev`='$now' AND `village_now`='$now' AND `trooper_id`='4'");
        $coord =$map->getCoordFromId($id);
        if ($nump >= 100) {
            if ($nump > $num)
                $nump = $num;
            $token = token_ctrl($this->getRequest()->getParams());
            if ($token[tokenCM]) {
                $dist = getDistance($this->civ->village->data[$now], $coord);
                $time = getTime($dist, colony::$speed) + mktime();
                $param = serialize(
                array('village_B' => $id, 'village_A' => $now, 'num' => $nump, 
                'cid' => $this->civ->cid));
                $this->db->update(TROOPERS, array('numbers' => ($num - $nump)), 
                "`village_prev`='$now' AND `village_now`='$now' AND `trooper_id`='4'");
                $this->db->insert(EVENTS_TABLE, 
                array('time' => $time, 'type' => COLONY_EVENT, 
                'params' => $param));
                $this->_log->startMovement("coloni inviati, parametri ".print_r(unserialize($param),true).
                        " arrivo stimato ".date('H:i:s d/m/Y',$time));
                $text = $nump . ' ' . $this->t->_('coloni inviati con successo');
            }
        } else {
            if ($liv <= $n)
                $text = $this->t->_(
                'Non puoi colonizzare altre citta se non aumenti il') . ' ' .
                 Model_building::$name[$this->civ->getAge()][COMMAND] . ' ';
            elseif (isset($map->city[$id])) {
                $text = $this->t->_('Esiste già un villaggio qui.');
            } else {
                $rand = rand(1, 1000);
                $token = token_set("tokenCM");
                $img = new Zend_View_Helper_image();
                $text = $img->troop(4) . '<span id="amount' . $rand .
                 '">100</span> <div id="colbar' . $rand .
                 '" style="width:300;"></div> ' .
                 $img->image("/common/images/ok.gif", "invia", "invia", 16, 16, 
                array(
                'onclick' => "val=$('#colbar" . $rand .
                 "').slider('value');ev.request(module+'/movements/colony/id/" .
                 $id . "/tokenCM/" . $token .
                 "','post',{num:val});$('#windows{wid}').dialog('close');")) .
                 '<script type="text/javascript">$("#colbar' . $rand . '").slider({
			range: "min",
			value: 100,
			min: 100,
			max: ' . $num . ',
			slide: function( event, ui ) {
				$( "#amount' . $rand . '" ).text(ui.value );
			}
		});
		</script>';
            }
        }
        $reply = array('data' => false, 
        'html' => array("title" => $this->t->_('invia coloni'), "text" => $text, 
        'x' => 400, 'y' => 200), 'javascript' => false, 'update' => false);
        echo json_encode($reply);
        Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
    }
}
?>



