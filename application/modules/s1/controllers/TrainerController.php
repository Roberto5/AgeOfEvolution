<?php
class S1_TrainerController extends Zend_Controller_Action
{
    private $log;
    private $civ;
    /**
     * 
     * @var Zend_Db_Adapter_Abstract
     */
    private $db;
    public function init ()
    {
        $this->log = Zend_Registry::get("log");
        $this->civ = Model_civilta::getInstance();
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $this->t = Zend_Registry::get("translate");
    }
    /**public function indexAction()
    {
        // action body
     */
    public function trainAction ()
    {
        global $Troops_Array;
        $now = $this->civ->getCurrentVillage();
        $token = token_ctrl($this->getRequest()->getParams());
        if ($token['tokenT']) {
            $totrain = array();
            foreach ($_POST as $key => $value) {
                if (preg_match("/t(?P<id>\d+)/", $key, $matches))
                    $totrain[$matches['id']] = (int) $value;
            }
            $this->log->debug($totrain);
            $p = $this->civ->village->building[$now]->getproperty(
            $this->civ->village->building[$now]->getBildForType(BARRACK), 
            $this->civ->getAge());
            foreach ($totrain as $key => $value) {
                if ($value > 0) {
                    $max = $p['liv'] * $p['maxpop'];
                    if ($value > $max)
                        $value = $max;
                    if (in_array($key, $this->civ->dispTroops)) {
                        $bool = true;
                        $cost = array();
                        $tot = 0;
                        for ($i = 0; ($i < 3) && $bool; $i ++) {
                            $tot += $Troops_Array[$key]::$cost[$i];
                            if (($Troops_Array[$key]::$cost[$i] * $value) > $this->civ->village->data[$now]['resource_' .
                             ($i + 1)])
                                $bool = false;
                            else
                                $cost[$i] = $Troops_Array[$key]::$cost[$i] *
                                 $value;
                        }
                        $freepop = $this->civ->village->data[$now]['pop'] - ($this->civ->poptroop +
                         $this->civ->popc +
                         $this->civ->village->data[$now]['busy_pop']);
                        if ((($Troops_Array[$key]::$cost[3] * $value) < $freepop) &&
                         $bool) {
                            $cost[3] = $Troops_Array[$key]::$cost[3] * $value;
                            $this->civ->aggResource($cost);
                            $ev = new Model_event(false);
                            $time = intval($tot / $p['rid'] * 3600);
                            $ev->addtrain($value, $key, $time);
                        }
                    }
                }
            }
        }
        $this->_helper->redirector("show", "building", null, 
        array('t' => BARRACK));
    }
    public function promoteAction ()
    {
        global $Troops_Array;
        $now = $this->civ->getCurrentVillage();
        $token = token_ctrl($this->getRequest()->getParams());
        if ($token['tokenT']) {
            $t = array();
            for ($i = 0; $i < TOT_TYPE_TROOPS; $i ++)
                if (intval($_POST['t' . $i]) > 0)
                    $t[$i] = (int) $_POST['t' . $i];
            foreach ($t as $key => $value) {
                // controllo truppa daconvertire è disponibile
                $type = (int) $_POST['type' . $key];
                if (in_array($type, $this->civ->dispTroops)) {
                    //calcolo costo unitario
                    $res1 = $Troops_Array[$key]::$cost;
                    $res2 = $Troops_Array[$type]::$cost;
                    $pop = $res2[3] - $res1[3];
                    $cost = 0;
                    for ($i = 0; $i < 4; $i ++) {
                        $cost += $res2[$i] - $res1[$i];
                    }
                    // controllo truppe massime
                    $max = $value;
                    $troop = $this->civ->troopers->troopers_now;
                    if ($troop[$key]) { // controllo se le truppe da convertire ci sono
                        $max = $troop[$key]['numbers'];
                        if ($value > $max)
                            $value = $max;
                        $cost *= $value;
                        //controllo costo
                        if (($cost <
                         $this->civ->village->data[$now]['resource_1']) && ($pop <= ($this->civ->village->data[$now]['pop'] -
                         $this->civ->village->data[$now]['busy_pop']))) {
                            if ($troop[$key]['numbers'] == $value) {
                                $this->db->query(
                                "UPDATE `" . TROOPERS . "` SET `trooper_id`='" .
                                 $type . "' WHERE `trooper_id`='" . $key .
                                 "' AND `civ_id`='" . $this->civ->cid .
                                 "' AND `village_now`='" .
                                 $this->civ->getCurrentVillage() .
                                 "' AND `village_prev`='" .
                                 $this->civ->getCurrentVillage() . "'");
                            } else {
                                $this->db->query(
                                "UPDATE `" . TROOPERS .
                                 "` SET `numbers`=`numbers`-'" . $value .
                                 "' WHERE `trooper_id`='" . $key .
                                 "' AND `civ_id`='" . $this->civ->cid .
                                 "' AND `village_now`='" .
                                 $this->civ->getCurrentVillage() .
                                 "' AND `village_prev`='" .
                                 $this->civ->getCurrentVillage() . "'");
                                $this->db->query(
                                "INSERT INTO `" . TROOPERS . "` SET `numbers`='" .
                                 $value . "' , `trooper_id`='" . $type .
                                 "' , `civ_id`='" . $this->civ->cid .
                                 "' , `village_now`='" .
                                 $this->civ->getCurrentVillage() .
                                 "' , `village_prev`='" .
                                 $this->civ->getCurrentVillage() . "'");
                            }
                            $this->civ->aggResource(array($cost, 0, 0, $pop));
                            $this->_helper->redirector("show", "building", null, 
                            array('t' => BARRACK));
                        } else
                            $this->view->error = $this->t->_(
                            "risorse insufficenti");
                    } else
                        $this->view->error = $this->t->_(
                        "non ci sono truppe da convertire");
                } else
                    $this->view->error = $this->t->_(
                    "devi ancora sblocare questa truppa!");
            }
        } else
            $this->_helper->redirector("show", "building", null, 
            array('t' => BARRACK));
    }
    //@implementare congedo truppe
    public function traincolonyAction ()
    {
        $now = $this->civ->getCurrentVillage();
        $token = token_ctrl($this->getRequest()->getParams());
        if ($token['tokenCo']) {
            $num = (int) $_POST['num'];
            if ($num > 0) {
                $bool = true;
                $cost = array();
                $tot = 0;
                for ($i = 0; ($i < 3) && $bool; $i ++) {
                    $tot += colony::$cost[$i];
                    if ((colony::$cost[$i] * $num) > $this->civ->village->data[$now]['resource_' .
                     ($i + 1)])
                        $bool = false;
                    else
                        $cost[$i] = colony::$cost[$i] * $num;
                }
                $freepop = $this->civ->village->data[$now]['pop'] - ($this->civ->poptroop +
                 $this->civ->popc + $this->civ->village->data[$now]['busy_pop']);
                if (((colony::$cost[3] * $num) < $freepop) &&
                 $bool) {
                    $cost[3] = colony::$cost[3] * $num;
                    $this->civ->aggResource($cost);
                    $ev = new Model_event(false);
                    $time = 60*$num;
                    $ev->addtrain($num, 4, $time);
                }
            }
        }
        $this->_helper->redirector("show", "building", null, 
        array('t' => COMMAND));
    }
}

