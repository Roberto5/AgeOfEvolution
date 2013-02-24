<?php
/**
 * battle
 * 
 * @author pagliaccio
 * @version 
 */
class Model_battle
{
    /**
     * 
     * Enter description here ...
     * @var Zend_Db_Adapter_Abstract
     */
    private $db;
    private $log;
    public $atk;
    public $def;
    public $round = array();
    public $rid;
    public $continue = 0;
    public $sim = false;
    public $time = 0;
    public $stats = array();
    function __construct ()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $this->t = Zend_Registry::get("translate");
        $this->log = Zend_Registry::get("log");
    }
    /**
     * inizializza la battaglia
     * @param array $array
     * @param boolean $stamp
     * @param boolean $report
     */
    function start ($array, $sim = false)
    {
        $this->log->debug($array);
        $this->atk = $array['attacker'];
        $this->def = $array['defender'];
        $this->time = $array['time'];
        $this->rid = $array['rid'];
        $this->continue = $array['round_now'];
        $this->stats = $array['stats'];
        $this->sim = $sim;
        if (abs($this->atk['age'] - $this->def['age']) <= 1) {
            // Battaglia possibile
            $superstiti = $this->dobattle(
            array('type' => $array['type'], 'round' => $array['raid_round']));
        } else {
            // Battaglia impossibile
            $data = array();
            switch ($this->type) {
                case RAID:
                    $data['type'] = RAID_REPORT;
                    break;
                case ATTACK:
                    $data['type'] = ATTACK_REPORT;
                    break;
            }
            $data['village_A'] = $this->atk['village_id'];
            $data['village_B'] = $this->def['village_id'];
            $data['atk'] = $this->atk;
            $data['def'] = $this->def;
            $data['sim'] = $this->sim;
            $data['error'] = '<b>' .
             $this->t->_(
            'La differenza d&apos;et&agrave; &egrave; troppo alta, l&apos;esercito attaccante si ritira.') .
             '</b>';
            Model_report::sendReport($this->atk['civ'], $data, $this->time);
            Model_report::sendReport($this->def['civ'], $data, $this->time);
            $superstiti = array("attacker" => $this->atk['troops'], 
            "defender" => $this->def['troops'], 'finish' => true);
        }
        return $superstiti;
    }
    /**
     * avvia la battaglia
     * @param Array $atk
     * @param Array $def
     * @param Array $array
     * @param Boolean $stamp
     * @param Boolean $report
     */
    function dobattle ($array)
    {
        $this->type = $array['type'];
        if (count($this->def['troops']) < 1) {
            // Vittoria a tavolino
            $this->finishbattle($this->atk['troops'], array(), 0, 
            'Vittoria a tavolino');
            $superstiti = array("attacker" => $this->atk['troops'], 
            "defender" => array(), 'finish' => true);
        } else {
            switch ($array['type']) {
                case '1': // Normale
                    $superstiti = $this->normalatk(false);
                    break;
                case '2': // Raid
                    $superstiti = $this->normalatk(
                    $array['round']);
                    break;
            }
        }
        return $superstiti;
    }
    /**
     * attacco normale
     * @param Array $atk
     * @param Array $def
     * @param int $maxround
     * @param boolean $stamp
     * @param boolean $report
     */
    function normalatk ($maxround = false)
    {
        global $Troops_Array;
        $maxgit = 0;
        $tota = 0;
        $totb = 0;
        foreach ($this->atk['troops'] as $key => $value) {
            if ($this->stats) {
                $hpa = $this->stats['atk']['hpa'];
                $da1 = $this->stats['atk']['da1'];
                $da2 = $this->stats['atk']['da1'];
            } else {
                $hpa[$key] = $value * $Troops_Array[$key]::$hp;
                $da1[$key] = $Troops_Array[$key]::$def1;
                $da2[$key] = $Troops_Array[$key]::$def2;
            }
            $fatk[$key] = $value * $Troops_Array[$key]::$atk;
            $gita[$key] = $Troops_Array[$key]::$git;
            //bonus hp
            $bonushpa[$key] = 0;
            //bonus atk
            $bonusatka[$key] = 0;
            // abilità truppa
            $ability = $Troops_Array[$key]::specialEffect(
            array('type' => 'atk', 'num' => $value, 'obj' => $this->atk));
            $this->log->debug($ability);
            $bonushpa[$key] += $ability['hp'];
            $bonusatka[$key] += $ability['atk'];
            // bonus hp zona di influenza
            $bonushpa[$key] += $this->atk['bonusf'];
            $bonusatka[$key] += $this->atk['bonusd'];
            //applico il bonus
            $hpa[$key] += intval($hpa[$key] * $bonushpa[$key] / 100);
            $fatk[$key] += intval($fatk[$key] * $bonusatka[$key] / 100);
            $tota += $value;
            if ($value > 0)
                $supatk[$key] = $value;
            if ($maxgit < $gita[$key])
                $maxgit = $gita[$key];
        }
        foreach ($this->def['troops'] as $key => $value) {
            if ($this->stats) {
                $hpd = $this->stats['def']['hpd'];
                $dd1 = $this->stats['def']['dd1'];
                $dd2 = $this->stats['def']['dd1'];
            } else {
                $hpd[$key] = $value * $Troops_Array[$key]::$hp;
                $dd1[$key] = $Troops_Array[$key]::$def1;
                $dd2[$key] = $Troops_Array[$key]::$def2;
            }
            $fdef[$key] = $value * $Troops_Array[$key]::$atk;
            $gitd[$key] = $Troops_Array[$key]::$git;
            $totb += $value;
            //bonus hp
            $bonushpd[$key] = 0;
            //bonus atk
            $bonusatkd[$key] = 0;
            // abilità truppa
            $ability = $Troops_Array[$key]::specialEffect(
            array('type' => 'def', 'num' => $value, 'obj' => $this->def));
            $bonushpd[$key] += $ability['hp'];
            $bonusatkd[$key] += $ability['atk'];
            // bonus hp zona di influenza
            $bonushpd[$key] += $this->def['bonusf'];
            $bonusatkd[$key] += $this->def['bonusd'];
            //applico il bonus
            $hpd[$key] += intval($hpd[$key] * $bonushpd[$key] / 100);
            $fdef[$key] += intval($fdef[$key] * $bonusatkd[$key] / 100);
            if ($value > 0)
                $supdef[$key] = $value;
            if ($maxgit < $gitd[$key])
                $maxgit = $gitd[$key];
        }
        $bool = true;
        $mg = $maxgit;
        //for($r=0;($bool) &&(!$maxround||($r < $maxround));$r++) { //round
        //start mod
        $this->log->debug("continue " . $this->continue);
        $this->log->debug("maxround " . $maxround);
        $r = intval($this->continue);
        if (! $maxround || ($r < $maxround)) {
            $maxgit -= $r;
            if ($maxgit < 0)
                $maxgit = 0;
                 // end mod
            //attacco "spara"
            $attack = 0;
            $na = 0;
            $this->round[$r]['atk'] = $supatk;
            foreach ($supatk as $key => $value) {
                if ($value) {
                    $na ++;
                    if ($gita[$key] >= $maxgit) {
                        $attack += $fatk[$key];
                    }
                }
            }
            //difesa "spara"
            $defence = 0;
            $nd = 0;
            $this->round[$r]['def'] = $supdef;
            foreach ($supdef as $key => $value) {
                if ($value) {
                    $nd ++;
                    if ($gitd[$key] >= $maxgit) {
                        $defence += $fdef[$key];
                    }
                }
            }
            if ($maxgit > 0)
                $maxgit --;
            $mg --;
            //**************attacco subisce****************
            $totanew = 0;
            foreach ($supatk as $key => $value) {
                $medforza = intval($value / $tota * $defence);
                //applico valore scudo
                $da = intval(
                ($mg + 1) > 0 ? $da1[$key] -- : $da2[$key] --);
                if ($da1[$key] < 0)
                    $da1[$key] = 0;
                if ($da2[$key] < 0)
                    $da2[$key] = 0;
                if ($da < 0)
                    $da = 0;
                $medforza -= $da * $totb;
                if ($medforza < 0)
                    $medforza = 0; //*/
                $this->log->debug('medforza ' . $medforza);
                // end mod scudo
                $hpa[$key] -= $medforza;
                //controllo se la truppa muore
                if ($hpa[$key] > 0) { // se viva aggiorno il numero di truppe e l'attacco
                    $hpt = $Troops_Array[$key]::$hp *
                     $bonushpa[$key] / 100 + $Troops_Array[$key]::$hp;
                    $sup = $hpa[$key] / ($hpt);
                    //arrotondo in eccesso
                    if ($sup > intval($sup))
                        $sup = intval($sup) + 1;
                    $supatk[$key] = $sup;
                    $fatk[$key] = $sup * $Troops_Array[$key]::$atk;
                } else { // se è morta o in overdead azzero il numero di truppe
                    $hpa[$key] = 0;
                    $supatk[$key] = 0;
                    $fatk[$key] = 0;
                }
                $totanew += $supatk[$key];
            }
            $tota = $totanew;
            //**************difesa subisce****************
            //media attacco per tipo di truppe 
            $totbnew = 0;
            foreach ($supdef as $key => $value) {
                //tolgo al tipo di truppa il valore medio del'attacco
                $medforza = intval($value / $totb * $attack);
                //applico valore scudo
                $dd = intval(
                ($mg + 1) > 0 ? $dd1[$key] -- : $dd2[$key] --);
                if ($dd1[$key] < 0)
                    $dd1[$key] = 0;
                if ($dd2[$key] < 0)
                    $dd2[$key] = 0;
                if ($dd < 0)
                    $dd = 0;
                $medforza -= $dd * $tota;
                if ($medforza < 0)
                    $medforza = 0; //*/
                $this->log->debug('medforza ' . $medforza);
                // end mod scudo
                $hpd[$key] -= $medforza;
                //controllo se la truppa muore
                if ($hpd[$key] > 0) { // se viva aggiorno il numero di truppe e l'attacco
                    $hpt = $Troops_Array[$key]::$hp *
                     $bonushpd[$key] / 100 + $Troops_Array[$key]::$hp;
                    $sup = $hpd[$key] / ($hpt);
                    //arrotondo in eccesso
                    if ($sup > intval($sup))
                        $sup = intval($sup) + 1;
                    $supdef[$key] = $sup;
                    $fdef[$key] = $sup * $Troops_Array[$key]::$atk;
                } else { // se è morta o in overdead azzero il numero di truppe
                    $hpd[$key] = 0;
                    $supdef[$key] = 0;
                }
                $totbnew += $supdef[$key];
            }
            $totb = $totbnew;
            $boola = false;
            foreach ($supdef as $key => $value) {
                if ($value > 0)
                    $boola = true;
                if ($boola)
                    break;
            }
            $boold = false;
            foreach ($supatk as $key => $value) {
                if ($value > 0)
                    $boold = true;
                if ($boold)
                    break;
            }
            $bool = (($boola) && ($boold));
            $this->round[$r]['supa'] = $supatk;
            $this->round[$r]['supd'] = $supdef;
        }
        if (($maxround) && (($r + 1) >= $maxround))
            $bool = false;
        $rid = $this->finishbattle($supatk, $supdef, $r, '', $this->continue);
        //dd1[k] dd2[k] hpd[k] hpa[k]
        $stats = array(
        'atk' => array('hpa' => $hpa, 'da1' => $da1, 'da2' => $da2), 
        'def' => array('hpd' => $hpd, 'dd1' => $dd1, 'dd2' => $dd2));
        return array("attacker" => $supatk, "defender" => $supdef, 
        'finish' => ! $bool, 'rid' => $rid, 'round' => $r, 'stats' => $stats);
    }
    /**
     * genera report o lo stampa
     * @param Array $atk
     * @param Array $supatk
     * @param Array $def
     * @param Array $supdef
     * @param int $round
     * @param String $info
     * @param boolean $stamp
     * @param boolean $report
     */
    function finishbattle ($supatk, $supdef, $round = 0, $info = '', $add = false)
    {
        global $NameTroops, $Troops_Array;
        include_once APPLICATION_PATH . '/views/helpers/image.php';
        $img = new Zend_View_Helper_image();
        $b = new Zend_View_Helper_BaseUrl();
        $img->baseUrl = $b->baseUrl();
        $resource = array(0, 0, 0);
        $tota = 0;
        foreach ($supatk as $value)
            $tota += $value;
        if (! $this->sim) {
            $res = Model_civilta::aggResourceById($this->def['village_id']);
            $capacity = 0;
            foreach ($supatk as $key => $value) {
                $capacity += $Troops_Array[$key]::$capacity * $value;
            }
            $tot = $res[0] + $res[1] + $res[2] + 1;
            if ($capacity > $tot) {
                $resource = array(intval($res[0]), intval($res[1]), 
                intval($res[2]));
            } else {
                $r1 = $res[0] / $tot;
                $r2 = $res[1] / $tot;
                $r3 = $res[2] / $tot;
                $resource = array(intval($r1 * $capacity), 
                intval($r2 * $capacity), intval($r3 * $capacity));
            }
        }
        $data = array();
        switch ($this->type) {
            case RAID:
                $data['type'] = RAID_REPORT;
                break;
            case ATTACK:
                $data['type'] = ATTACK_REPORT;
                break;
        }
        $data['village_A'] = $this->atk['village_id'];
        $data['village_B'] = $this->def['village_id'];
        $data['res'] = $resource;
        $data['atk'] = $this->atk;
        $data['def'] = $this->def;
        $data['supatk'] = $supatk;
        $data['supdef'] = $supdef;
        $data['round'] = $round;
        $data['totsupa'] = $tota;
        $data['infround'] = $this->round;
        $data['sim'] = $this->sim;
        if ($add) {
            Model_report::addtoreport($this->rid, $this->round[$round]);
            $rid = $this->rid;
        } else
            $rid = Model_report::sendReport($this->atk['civ'], $data, 
            $this->time);
        if ($this->atk['civ'] != $this->def['civ'])
            Model_report::sendReport($this->def['civ'], $data, $this->time);
        return $rid;
    }
}
