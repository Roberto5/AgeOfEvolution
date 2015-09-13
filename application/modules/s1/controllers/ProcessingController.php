<?php
class S1_ProcessingController extends Zend_Controller_Action
{
    /**
     * 
     * @var Model_params
     */
    private $p;
    /**
     * 
     * @var int
     */
    private $now;
    /**
     * 
     * @var array
     */
    private $events = array();
    /**
     * 
     * @var bool
     */
    private $refresh = false;
    private $map;
    public function init ()
    {
        $this->p = Zend_Registry::get("param");
        $this->_db->getProfiler()->setEnabled(false);
        $this->map=Model_map::getInstance();
    }
    public function indexAction ()
    {
        ignore_user_abort(true);
        set_time_limit(0);
        try {
            if (! $this->p->get("epon", false)) {
                $this->p->set("epon", 1);
                while ($this->p->get("epon", false,true)) { //array('', ' asc'), MAX_EV)
                    $this->now = time();
                    $this->events = $this->_db->fetchAll(
                    "SELECT * FROM `" . EVENTS_TABLE . "` 
                	WHERE `time`<='" .
                     $this->now . "' ORDER BY `time`,`id` ASC");
                    if (count($this->events) > 0) {
                        $et = time();
                        $this->_log->info("processo eventi in corso...");
                        for ($i = 0; $this->events[$i]; $i ++) {
                            // aggiungere eventi
                            $bool = false;
                            $this->i = $i;
                            $this->time = $this->events[$i]['time'];
                            $this->id = $this->events[$i]['id'];
                            $this->_log->info(
                            "l'azione è avvenuta alle " .
                             date("H:i:s d/m/Y", $this->time));
                            switch ($this->events[$i]['type']) {
                                case BILD_EVENT:
                                    $bool = $this->bildEvent(
                                    $this->events[$i]['params']);
                                    break;
                                case MARKET_EVENT:
                                    $bool = $this->marketEvent(
                                    $this->events[$i]['params']);
                                    break;
                                case RETURN_MERCANT_EVENT:
                                    $bool = $this->returnEvent(
                                    $this->events[$i]['params']);
                                    break;
                                case MOVEMENTS_EVENT:
                                    $bool = $this->movementsEvent(
                                    $this->events[$i]['params'], 
                                    $this->events[$i]['time']);
                                    break;
                                case TRAINING_EVENT:
                                    $bool = $this->trainingEvent(
                                    $this->events[$i]['params']);
                                    break;
                                case COLONY_EVENT:
                                    $bool = $this->colonyEvent(
                                    $this->events[$i]['params']);
                                    break;
                                case DESTROY_EVENT:
                                    $bool = $this->destroyEvent(
                                    $this->events[$i]['params']);
                                    break;
                                default:
                                    $e = $this->events[$i]['type'];
                                    $this->_log->crit(
                                    "Evento '" . $e . "' sconosciuto!!");
                            }
                            if ($bool) {
                                $this->_db->delete(EVENTS_TABLE,
                                "`id`='" . $this->events[$i]['id'] . "'");
                            } else {
                                $this->_log->info(
                                "l'evento " . $this->events[$i]['id'] .
                                 " del tipo " . $this->events[$i]['type'] .
                                 " non è concluso");
                                $this->events[$i]['time'] ++;
                                $this->_db->update(EVENTS_TABLE,$this->events[$i], 
                                "`id`='" . $this->events[$i]['id'] . "'");
                                $this->refresh = true;
                            }
                        }
                        if ($this->refresh) {
                            break;
                        }
                    } else {
                    	$this->p->set("epla", time());
                    	sleep(1);
                    }
                        
                }
            }
        } catch (Exception $e) {
            $this->p->set("epon", 0);
            $this->_log->err($e->getMessage());
            $this->view->error=$e->getMessage().'at line '.$e->getLine();
        }
    }
    /*********** funzioni per la gestione di eventi qui************

     **
     * gestore delle costruzioni
     * @param Array $param 'village_id' 'pos'
     * @return bool
     */
    function bildEvent ($param)
    {
        global $Building_Array;
        $param = unserialize($param);
        //info civiltà
        $civ = $this->_db->fetchRow(
        "SELECT `civ_age`,`" . CIV_TABLE . "`.`civ_id` FROM `" . CIV_TABLE .
         "`,`" . SERVER . "_map` WHERE `" . SERVER . "_map`.`civ_id`=`" . CIV_TABLE .
         "`.`civ_id` AND `id`='" . $param['village_id'] . "'");
        $age = $civ['civ_age'];
        //info edifici
        $build = $this->_db->fetchAssoc(
        "SELECT `pos`,`village_id`,`type`,`pop`,`built` FROM `" . SERVER .
         "_building` WHERE `village_id`='" . $param['village_id'] . "'");
        $ctrl = $build;
        $build[$param['pos']]['built'] =true;
        //aggiornamento popolazione
        foreach ($build as $key => $value) {
            //pop min
            $minpop = $Building_Array[$value['type'] - 1]::$minPop[$age];
            //popo max 
            $maxpop = $Building_Array[$value['type'] - 1]::$maxPop[$age];
            if ($value['pop'] > $maxpop) {
                $build[$key]['pop'] = $maxpop;
            }
            // pop minima
            if ($value['pop'] < $minpop) {
                $build[$key]['pop'] = $minpop;
            }
        }
        $dif_pop = 0;
        foreach ($build as $key => $value) {
            if (($value['pop'] != $ctrl[$key]['pop']) ||
             ($value['built'] != $ctrl[$key]['built'])) {
                $this->_log->debug(
                "pop aggiornata: '" . $value['pop'] . "' pop precedente '" .
                 $ctrl[$key]['pop'] . "'\n
        		costruzione aggiornato '" . $value['built'] . "' costruzione prec '" .
                 $ctrl[$key]['built'] . "'");
                $this->_db->update(SERVER.'_building', $value, 
                "`village_id`='" . $param['village_id'] . "' AND `pos`='" .
                 $value['pos'] . "'");
                $dif_pop += $value['pop'] - $ctrl[$key]['pop'];
            }
        }
        Model_civilta::aggProd($param['village_id']);
        Model_civilta::aggResourceById($param['village_id']);
        // se costruisco il senato allora aggiorno la captale
        if (($build[$param['pos']]['type'] == COMMAND)&&($build[$param['pos']]['built'] )) {
            // cerco il senato e lo cancello
            $village = $this->_db->fetchCol(
            "SELECT `id` FROM `" . SERVER . "_map` WHERE `civ_id`='" .
             $civ['civ_id'] . "' AND `id`!='" . $param['village_id'] . "'");
            if ($village)
                $this->_db->delete(SERVER.'_building', 
                "`village_id`IN('" . implode("','", $village) . "') AND `type`='" .
                 COMMAND . "'");
            $this->_db->update(SERVER.'_map', array('capital' => 0), 
            "`civ_id`='" . $civ['civ_id'] . "'");
             $this->_log->build("capitale spostata in ". $param['village_id']);
            $this->_db->query("UPDATE `".SERVER."_map` SET `capital`='1' WHERE `id`='" . $param['village_id'] . "'");   
		}
        $this->_log->build(
        "costruzione in posizione " . $param['pos'] . " aumentata nel villaggio " .
         $param['village_id']);
        return true;
    }
    /**
     * arrivo dei mercanti a destinazione
     * @param Array $param 'res' 'destinatario' 'mittente 'time' 'n'
     * @return bool
     */
    function marketEvent ($param2)
    {
        $param = unserialize($param2);
        $p = new Model_params();
        $id_market = $p->get("id_market", 1);
        $mitt=$this->map->city[$param['mittente']];
        $dest = $this->map->city[$param['destinatario']]['civ_id'];
        if ($param['destinatario'] != $id_market)
            $this->_db->query(
            "UPDATE `" . SERVER . "_map` SET `resource_1`=`resource_1`+'" .
             $param['res'][0] . "' , `resource_2`=`resource_2`+'" .
             $param['res'][1] . "' , `resource_3`=`resource_3`+'" .
             $param['res'][2] . "' WHERE `id`='" . $param['destinatario'] . "'");
        else {
            $cid = $mitt['civ_id'];
            $this->_db->query(
            "INSERT INTO `" . OFFER_TABLE . "` SET `civ_id`='" . $cid .
             "', `vid`='" . $param['mittente'] . "' , `resource`='" .
             ($param['res'][1] != 0 ? $param['res'][1] : $param['res'][2]) .
             "' , `type`='" . ($param['res'][1] != 0 ? "1" : "2") .
             "' ,`rapport`='" . $param['rap'] . "'");
        }
        // genero report 
        $data = array();
        $data['type'] = MARKET_REPORT;
        $data['village_A'] = $param['mittente'];
        $data['village_B'] = $param['destinatario'];
        $data['res'] = $param['res'];
        if ($dest != "0") {
            Model_report::sendReport($dest, $data, $this->time);
        } elseif ($mitt != "0") {
            Model_report::sendReport($mitt, $data, $this->time);
        }
        //rispedisco indietro i mercanti*/
        if ($param['mittente'] != $id_market) {
            $data = array('params' => $param2, 
            'time' => ($this->time + $param['time']), 
            'type' => RETURN_MERCANT_EVENT);
            $this->_db->insert(EVENTS_TABLE,$data);
            $this->refresh = true;
        }
        $this->_log->market(
        "mercanti arrivati a " . $param['destinatario'] . " da " .
         $param['mittente'] . " parametri " . print_r($param, true).print_r(array($mitt,$dest),true));
        return true;
    }
    /**
     * ritorno mercanti non fa niente per ora.
     * @param array $param
     * @return bool
     */
    function returnEvent ($param)
    {
        $this->_log->market("mercanti ritornati parametri " . print_r($param, true));
        return true;
    }
    /**
     * evento arrivo truppe
     * array troopers[$idtruppe]=numero_truppe array resource[3]
     * @param Array $param int:'civ_id',int:'type',int:'village_A',int:'village_b',array:'troopers',array:'resource',int:round;
     */
    function movementsEvent ($param)
    {
        $p = unserialize($param);
        $finish = true;
        switch ($p['type']) {
            case RETURN_T: // ritorno truppe
                foreach ($p['troopers'] as $key => $value) {
                    // controllo se le truppe sono già presenti
                    // no: aggiungo la tabella
                    // si: uppo la tabella
                    if ($this->_db->fetchOne(
                    "SELECT count(*) FROM `" . TROOPERS .
                     "` WHERE `village_now`='" . $p['village_B'] .
                     "' AND `village_prev`='" . $p['village_B'] .
                     "' AND `civ_id`='" . $p['civ_id'] . "' AND `trooper_id`='" .
                     $key . "'") == 0)
                        $this->_db->query(
                        "INSERT INTO `" . TROOPERS . "` SET `village_now`='" .
                         $p['village_B'] . "' , `village_prev`='" .
                         $p['village_B'] . "' , `civ_id`='" . $p['civ_id'] .
                         "' , `trooper_id`='" . $key . "' , `numbers`='" . $value .
                         "'");
                    else
                        $this->_db->query(
                        "UPDATE `" . TROOPERS . "` SET `numbers`=`numbers`+'" .
                         $value . "' WHERE `village_now`='" . $p['village_B'] .
                         "' AND `village_prev`='" . $p['village_B'] .
                         "' AND `civ_id`='" . $p['civ_id'] .
                         "' AND `trooper_id`='" . $key . "'");
                }
                //aggiungo risorse al villaggio
                if ($p['resource']) {
                    $this->_db->query(
                    "UPDATE `" . SERVER . "_map` SET `resource_1`=`resource_1`+'" .
                     $p['resource'][0] . "' , `resource_2`=`resource_2`+'" .
                     $p['resource'][1] . "' , `resource_3`=`resource_3`+'" .
                     $p['resource'][2] . "' WHERE `id`='" . $p['village_B'] . "'");
                }
                //log
                $this->_log->movement(
                "le seguenti truppe '" . print_r($p['troopers'], true) .
                 "' della civiltà " . $p['civ_id'] . " sono tornate da " .
                 $p['village_A'] . " in " . $p['village_B']);
                break;
            case REINFORCEMENT:
                // scarico risorse
                $this->_db->update(SERVER.'_map', 
                array('resource_1' => $p['res'][0], 
                'resource_2' => $p['res'][1], 'resource_3' => $p['res'][2]), 
                "`id`='" . $p['village_B'] . "'");
                Model_civilta::aggResourceById($p['village_B']);
                foreach ($p['troopers'] as $key => $value) {
                    // controllo presenza truppe
                    //no:aggiungo riga alla tabella
                    //si:uppo la riga
                    if ($this->_db->fetchOne(
                    "SELECT count(*) FROM `" . TROOPERS .
                     "` WHERE `village_now`='" . $p['village_B'] .
                     "' AND `village_prev`='" . $p['village_A'] .
                     "' AND `civ_id`='" . $p['civ_id'] . "' AND `trooper_id`='" .
                     $key . "'") == 0)
                        $this->_db->query(
                        "INSERT INTO `" . TROOPERS . "` SET `village_now`='" .
                         $p['village_B'] . "' , `village_prev`='" .
                         $p['village_A'] . "' , `civ_id`='" . $p['civ_id'] .
                         "' , `trooper_id`='" . $key . "' , `numbers`='" . $value .
                         "'");
                    else
                        $this->_db->query(
                        "UPDATE `" . TROOPERS . "` SET `numbers`=`numbers`+'" .
                         $value . "' WHERE `village_now`='" . $p['village_B'] .
                         "' AND `village_prev`='" . $p['village_A'] .
                         "' AND `civ_id`='" . $p['civ_id'] .
                         "' AND `trooper_id`='" . $key . "'");
                }
                //report
                $data = array();
                $data['type'] = REINF_REPORT;
                $data['village_A'] = $p['village_A'];
                $data['village_B'] = $p['village_B'];
                $data['res'] = $p['res'];
                $data['troops'] = $p['troopers'];
                Model_report::sendReport($p['civ_id'], $data, $this->time);
                $dest =$map->city[$p['village_B'] ]['civ_id'];
                Model_report::sendReport($dest, $data, $this->time);
                //log
                $this->_log->movement(
                "le seguenti truppe '" . print_r($p['troopers'], true) .
                 "' della civiltà " . $p['civ_id'] . " dal villaggio " .
                 $p['village_A'] . " sono in rinforzo da " . $p['village_B']);
                break;
            case RAID:
            case ATTACK:
                $this->battle = new Model_battle();
                //aggiorno risorse nel villaggio attaccato
                $res = Model_civilta::aggResourceById(
                $p['village_B']);
                //informazioni attaccante
                $a=$this->map->city[$p['village_A']];
                //informazioni difensore
                $d=$this->map->city[$p['village_B']];
                $query = "SELECT sum(`numbers`) as `numbers` ,`trooper_id` FROM `s1_troopers` WHERE `village_now`='" .
                 $p['village_B'] . "' GROUP BY `trooper_id`";
                $dif = $this->_db->fetchAll($query);
                //formattazione truppe difensore
                $td = array();
                for ($i = 0; $dif[$i]; $i ++) {
                    $td[$dif[$i]['trooper_id']] = $dif[$i]['numbers'];
                }
                $master = 'master' . $a['master'];
                $bonusf = master1::$troops_bonus[($a['zone'] == $a['master'] ? 1 : 0)][0];
                $bonusd = master1::$troops_bonus[($a['zone'] == $a['master'] ? 1 : 0)][1];
                $atk = array('age' => $a['civ_age'], 
                'village_name' => $a['name'], 'village_id' => $p['village_A'], 
                'user' => $a['civ_name'], "civ" => $a['civ_id'], 
                "troops" => $p['troopers'], 'bonusd' => $bonusd, 
                'bonusf' => $bonusf);
                $master = 'master' . $d['master'];
                $bonusf = master1::$troops_bonus[($d['zone'] == $d['master'] ? 1 : 0)][0];
                $bonusd = $d['defence'] - 100;
                $def = array('age' => $d['civ_age'], 
                'village_name' => $d['name'], 'village_id' => $p['village_B'], 
                'user' => $d['civ_name'], "civ" => $d['civ_id'], "troops" => $td, 
                'x' => $d['x'], 'y' => $d['y'], 'bonusd' => $bonusd, 
                'bonusf' => $bonusf);
                //**************************************avvio battaglia*******************************************
                $sup = $this->battle->start(
                array("attacker" => $atk, "defender" => $def, 
                "type" => $p['type'], "raid_round" => $p['round'], 
                'round_now' => $p['round_now'], 'rid' => $p['rid'], 
                'time' => $this->time, 'stats' => $p['stats']));
                $this->_db->query(
                "INSERT IGNORE INTO `" . OPTION_CIV_TABLE . "` SET `civ_id`='" .
                 $atk['civ'] . "',`option`='attack',`value`='1'");
                global $Troops_Array;
                //danni difese
                if ($td != $sup['defender']) {
                    $query = "SELECT `numbers` ,`trooper_id`,`village_prev`,`civ_id` FROM `s1_troopers` WHERE `village_now`='" .
                     $p['village_B'] . "' ORDER BY `trooper_id`";
                    $dif = $this->_db->fetchAll($query);
                    $type = '0';
                    $n = 0;
                    $index = null;
                    $supporter = array();
                    for ($i = 0; $dif[$i]; $i ++) {
                        if ($dif[$i]['trooper_id'] == $dif[$i + 1]['trooper_id']) {
                            $n ++;
                            $index[] = $i;
                        } else {
                            $n ++;
                            $index[] = $i;
                            if ($n == 1) {
                                $this->_db->query(
                                "UPDATE `" . TROOPERS . "` SET `numbers`='" .
                                 $sup['defender'][$dif[$i]['trooper_id']] .
                                 "' WHERE `trooper_id`='" .
                                 $dif[$i]['trooper_id'] . "' AND `village_now`='" .
                                 $p['village_B'] . "'");
                                if ($dif[$i]['civ_id'] != $def['civ']) {
                                    $supporter[$dif[$i]['civ_id']]['troop'][$dif[$i]['trooper_id']] = $dif[$i]['numbers'];
                                    $supporter[$dif[$i]['civ_id']]['sup'][$dif[$i]['trooper_id']] = $sup['defender'][$dif[$i]['trooper_id']];
                                }
                            } else {
                                $id = $dif[$i]['trooper_id'];
                                $tot = $td[$id];
                                $perd = $tot -
                                 $sup['defender'][$dif[$i]['trooper_id']];
                                // tot:perd=num:x   x=num*perd/tot
                                for ($j = 0; $j <
                                 $n; $j ++) {
                                    $kp = $perd / $tot;
                                    $kpt = round(
                                    $kp * $dif[$index[$j]]['numbers']);
                                    $this->_db->query(
                                    "UPDATE `" . TROOPERS .
                                     "` SET `numbers`=`numbers`-'" . $kpt .
                                     "' WHERE `trooper_id`='" .
                                     $dif[$i]['trooper_id'] .
                                     "' AND `village_prev`='" .
                                     $dif[$index[$j]]['village_prev'] .
                                     "' AND `village_now`='" . $p['village_B'] .
                                     "'");
                                    if ($dif[$i]['civ_id'] != $def['civ']) {
                                        $supporter[$dif[$i]['civ_id']]['troop'][$dif[$i]['trooper_id']] = $dif[$index[$j]]['numbers'];
                                        $supporter[$dif[$i]['civ_id']]['sup'][$dif[$i]['trooper_id']] = $dif[$index[$j]]['numbers'] -
                                         $kpt;
                                    }
                                }
                            }
                            $n = 0;
                            $index = null;
                        }
                    }
                }
                if ($supporter) {
                    foreach ($supporter as $cid => $value) {
                        $data = array();
                        $data = $value;
                        $data['type'] = REINF_LOST;
                        $data['atk'] = $atk;
                        $data['supatk'] = $sup['attacker'];
                        $data['village_B'] = $p['village_B'];
                        $data['village_name'] = $d['name'];
                        Model_report::sendReport($cid, $data, $this->time);
                    }
                }
                //ritorno
                $speed = array();
                $capacity = 0;
                $tot = 0;
                foreach ($sup['attacker'] as $key => $value) {
                    $speed[] = $Troops_Array[$key]::$speed;
                    $capacity += $Troops_Array[$key]::$capacity * $value;
                    $tot += $value;
                }
                $finish = $sup['finish'];
                $this->_log->debug($finish);
                if (($tot > 0) && $sup['finish']) { // controllo qualcuno si è salvato
                    $dist = getDistance(
                    array('x' => $a['x'], 'y' => $a['y']), 
                    array('x' => $d['x'], 'y' => $d['y']));
                    $time = getTime($dist, min($speed)) + $this->time;
                    //saccheggio
                    $tot = $res[0] + $res[1] + $res[2];
                    if ($capacity > $tot) {
                        $par['resource'] = array(intval($res[0]), 
                        intval($res[1]), intval($res[2]));
                    } else {
                        $r1 = $res[0] / $tot;
                        $r2 = $res[1] / $tot;
                        $r3 = $res[2] / $tot;
                        $par['resource'] = array(intval($r1 * $capacity), 
                        intval($r2 * $capacity), intval($r3 * $capacity));
                    }
                    $this->_db->query(
                    "UPDATE `" . SERVER . "_map` SET `resource_1`=`resource_1`-'" .
                     $par['resource'][0] . "' , `resource_2`=`resource_2`-'" .
                     $par['resource'][1] . "' , `resource_3`=`resource_3`-'" .
                     $par['resource'][2] . "' WHERE `id`='" . $p['village_B'] .
                     "'");
                    $par['civ_id'] = (int) $a['civ_id'];
                    $par['type'] = RETURN_T;
                    $par['village_A'] = $p['village_B'];
                    $par['village_B'] = $p['village_A'];
                    $par['troopers'] = $sup['attacker'];
                    $params = serialize($par);
                    $this->_db->insert(EVENTS_TABLE,
                    array('type' => MOVEMENTS_EVENT, 'time' => $time, 
                    'params' => $params));
                    $this->refresh = true;
                } elseif (! $finish) {
                    $data = array();
                    $new = $p;
                    $new['round_now'] = $sup['round'] + 1;
                    $new['rid'] = $sup['rid'];
                    $new['troopers'] = $sup['attacker'];
                    $new['stats'] = $sup['stats'];
                    $data['time'] = $this->time + 1;
                    $data['type'] = MOVEMENTS_EVENT;
                    $new = serialize($new);
                    $data['params'] = $new;
                    $this->_log->debug($data);
                    $this->_log->debug($this->id);
                    $this->events[$this->i]['params'] = $new;
                     $this->_db->update(EVENTS_TABLE,$data, "`id`='".$this->id."'");
                }
                $this->_log->movement(
                "attacco in arrivo su " . $p['village_B'] . " da " .
                 $p['village_A'] . " della civiltà " . $p['civ_id'] . " con '" .
                 print_r($p['troopers'], true) . "' difensori: '" .
                 print_r($td, true) . "' superstiti attaccante '" .
                 print_r($sup['attacker'], true) . "' superstiti difensore '" .
                 print_r($sup['defender'], true) . "' risorse raziate '" .
                 print_r($par['resource'], true) . "'");
                break;
        }
        return $finish;
    }
    /**
     * evento addestramento
     * @param array $param int:'village_id',int:'num',int:'trooper_id',int:'civ_id'
     * @return bool
     */
    function trainingEvent ($param)
    {
        $p = unserialize($param);
        if ($this->_db->fetchOne(
        "SELECT count(*) FROM `" . TROOPERS . "` WHERE `village_now`='" .
         $p['village_id'] . "' AND `village_prev`='" . $p['village_id'] .
         "' AND `civ_id`='" . $p['civ_id'] . "' AND `trooper_id`='" .
         $p['trooper_id'] . "'") == 0)
            $this->_db->query(
            "INSERT INTO `" . TROOPERS . "` SET `village_now`='" .
             $p['village_id'] . "' , `village_prev`='" . $p['village_id'] .
             "' , `civ_id`='" . $p['civ_id'] . "' , `trooper_id`='" .
             $p['trooper_id'] . "' , `numbers`='" . $p['num'] . "'");
        else
            $this->_db->query(
            "UPDATE `" . TROOPERS . "` SET `numbers`=`numbers`+'" . $p['num'] .
             "' WHERE `village_now`='" . $p['village_id'] .
             "' AND `village_prev`='" . $p['village_id'] . "' AND `civ_id`='" .
             $p['civ_id'] . "' AND `trooper_id`='" . $p['trooper_id'] . "'");
        $this->_log->train("truppe addestrate. parametri " . print_r($param, true));
        return true;
    }
    function colonyEvent ($param)
    {
        $param = unserialize($param);
        //@todo ottimizzare
        $vids = $this->_db->fetchCol(
        "SELECT `id` FROM `" . SERVER . "_map` WHERE `civ_id`='" . $param['cid'] .
         "'");
        $n = count($vids);
        //@todo modificare con la ricerca
        $disp=1;
        $cords = array($this->map->getCoordFromId($param['village_B']),$this->map->getCoordFromId($param['village_A']));
        
        $this->_log->debug(array($cords, $disp, $n));
        if (($disp > $n) && (!isset($this->map->city[$param['village_B']]))) {
            Model_civilta::addVillage($cords[0]['x'], $cords[0]['y'], 
            $param['cid']);
            $this->_db->query(
            "UPDATE `" . SERVER . "_map` SET `pop`='" . $param[num] .
             "' WHERE `id`='" . $param['village_B'] . "'");
            $param['succes'] = true;
        } else {
            $param['succes'] = false;
            $p = $param;
            $p['type'] = RETURN_T;
            $p['troopers'] = array('4' => $param['num']);
            $p['village_A'] = $param['village_B'];
            $p['village_b'] = $param['village_A'];
            $k = array_keys($cords);
            $dist = getDistance($cords[$k[0]], $cords[$k[1]]);
            $time = getTime($dist, colony::$speed) + mktime();
            $data = array('type' => MOVEMENTS_EVENT, 'time' => $time, 
            'params' => serialize($p));
            $this->_db->insert(EVENTS_TABLE,$data);
            $this->refresh = true;
        }
        $param['type'] = COLONY_REPORT;
        Model_report::sendReport($param['cid'], $param, $this->time);
        return true;
    }
    /**
     * 
     * demolisce una struttura
     * @param array $param 'pos'=>int,'village_id'=>int,'civ_id`,'pop'
     */
    function destroyEvent ($param)
    {
        $param = unserialize($param);
        Model_civilta::aggResourceById($param['village_id']);
        Model_civilta::aggProd($param['village_id']);
        $this->_db->delete(SERVER.'_building', 
        "`pos`='" . $param['pos'] . "' AND `village_id`='" . $param['village_id'] .
         "'");
        $this->_log->build(
        "edificio in posizione " . $param['pos'] . " è stato demolito in " .
         $param['village_id']);
        return true;
    }
/**
 * @todo inserire altri gestori
 */

}

