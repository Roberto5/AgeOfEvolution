<?php
class S1_SimulatorController extends Zend_Controller_Action
{
    private $log;
    public function init ()
    {
        $this->log = Zend_Registry::get("log");
        $t=Zend_Registry::get("translate");
        $this->view->headTitle($t->_('simulatore'));
    }
    public function indexAction ()
    {
        $age = Model_civilta::getInstance()->getAge();
        $cid = Model_civilta::getInstance()->cid;
        $type = intval($_POST['type']);
        $round = intval($_POST['round']);
        $bonusfa = (int) $_POST['bonusfa'];
        $bonushpa = (int) $_POST['bonushpa'];
        $bonusfd = (int) $_POST['bonusfd'];
        $bonushpd = (int) $_POST['bonushpd'];
        if ($this->getRequest()->isPost()) {
            $def = array();
            $atk = array();
            $this->log->debug($_POST);
            foreach ($_POST as $key => $value) {
                if (preg_match("#^def#", $key))
                    $def[substr($key, 3)] = $value;
                if (preg_match("#^atk#", $key))
                    $atk[substr($key, 3)] = $value;
            }
            $this->view->def = $def;
            $this->view->atk = $atk;
            $this->log->debug($def);
            $this->log->debug($atk);
            if ($_POST['sim']) {
            	$atk_array=array('troops' => $atk, 'age' => $age, 
                'bonusf' => $bonusfa, 'bonusd' => $bonushpa,'civ'=>$cid);
            	$def_array=array('troops' => $def, 'age' => $age, 
                'civ' => $cid, 'bonusf' => $bonusfd, 'bonusd' => $bonushpd);
            	$i=0;
            	$sim = new Model_battle();
            	$time=time();
            	do {
                /*array("attacker" => $atk, "defender" => $def, 
                "type" => $p['type'], "raid_round" => $p['round'], 
                'round_now' => $p['round_now'], 'rid' => $p['rid'], 
                'time' => $this->time,'stats'=>$p['stats'])*/
                $res=$sim->start(
                array(
                'attacker' => $atk_array, 
                'defender' => $def_array, 
                'type' => $type, 'raid_round' => $round, 
                'round_now' => $i, 'rid' => $res['rid'], 
                'time' => $time,'stats'=>$res['stats']), true, false);
                $atk_array['troops']=$res['attacker'];
                $def_array['troops']=$res['defender'];
                $i++;
            	}
            	while (!$res['finish']&&($i<100));
            }
        }
    }
}

