<?php
/**
 * report
 * 
 * @author pagliaccio
 * @version 
 */
require_once 'Zend/Db/Table/Abstract.php';
class Model_report extends Zend_Db_Table_Abstract
{
    /**
     * The default table name 
     */
    protected $_name = REPORT_TABLE;
    /**
     * ritorna il numero di report
     * @return int
     */
    static function ThereAreReport ($cid=0)
    {
    	if (!$cid) $cid=Model_civilta::getInstance()->cid;
        
        $user = Zend_Auth::getInstance()->getIdentity();
        $query = "SELECT count(*) as `tot` FROM `" . REPORT_TABLE . "` 
WHERE `civ`='" . $cid . "' AND IF( EXISTS ( SELECT `id` FROM `" .
         REPORT_READ_TABLE . "` WHERE `" . REPORT_READ_TABLE . "`.`id` = `" .
         REPORT_TABLE . "`.`id` AND `" . REPORT_READ_TABLE . "`.`user` = '" .
         $user->user_id . "' ) >0, '1', '0' ) = '0' ";
        $n = Zend_Db_Table::getDefaultAdapter()->fetchOne($query);
        if ($n > 0)
            return $n;
        else
            return false;
    }
    /**
     * invia un report
     * @param int $civ
     * @param array $data
     * @param int $time
     */
    static function sendReport ($civ, $data, $time = 0)
    {
    	if ($civ) {
        if (! $time)
            $time = mktime();
        	Zend_Db_Table::getDefaultAdapter()->query(
        "INSERT INTO `" . REPORT_TABLE . "` SET `civ`='" . $civ . "' , `data`='" .
         serialize($data) . "' , `time`='" . $time . "'");
        	return self::getDefaultAdapter()->lastInsertId();
    	}
        else return false;
    }
    static function addtoreport ($id, $info)
    {
        $row = self::getDefaultAdapter()->fetchRow(
        "SELECT * FROM `" . REPORT_TABLE . "` WHERE `id`='$id'");
        $data = unserialize($row['data']);
        $data['infround'][] = $info;
        $data['supatk'] = $info['supa'];
        $data['supdef'] = $info['supd'];
        $data['round'] ++;
        $row['data'] = serialize($data);
        self::getDefaultAdapter()->update(REPORT_TABLE, $row, "`id`='$id'");
    }
    static function getReport ($cid)
    {
        $user = Zend_Auth::getInstance()->getIdentity();
        $civ = Model_civilta::getInstance();
        $query = "SELECT `" . REPORT_TABLE . "`.`id`, `" . REPORT_TABLE . "`. * , civ_name, IF(
			EXISTS (
				SELECT `id`
				FROM `" . REPORT_READ_TABLE . "`
				WHERE `" . REPORT_READ_TABLE . "`.`id` = `" . REPORT_TABLE .
         "`.`id` AND `" . REPORT_READ_TABLE . "`.`user` = '" . $user->user_id . "'
			) >0, '1', '0' ) AS `read`
			FROM `" . REPORT_TABLE . "` , `" . CIV_TABLE . "`
			WHERE `" . REPORT_TABLE . "`.`civ` = '" . $civ->cid . "'
			AND `civ` = `civ_id`
			ORDER BY `" . REPORT_TABLE . "`.`id` DESC";
        $table = Zend_Db_Table::getDefaultAdapter()->fetchAssoc($query);
        if ($table) {
            foreach ($table as $key => $value)
                $table[$key] = Model_report::assemblereport($value);
        }
        return $table;
    }
    static function getcontent ($id)
    {
        $user = Zend_Auth::getInstance()->getIdentity();
        $civ = Model_civilta::getInstance();
        $user = Zend_Auth::getInstance()->getIdentity();
        $query = "SELECT `" . REPORT_TABLE .
         "`. * , `civ_name`, IF( EXISTS ( SELECT `id` FROM `" . REPORT_READ_TABLE .
         "`  WHERE `" . REPORT_READ_TABLE . "`.`id` = `" . REPORT_TABLE .
         "`.`id` AND `" . REPORT_READ_TABLE . "`.`user` = '" . $user->ID .
         "') >0, '1', '0' ) AS `read` FROM `" . REPORT_TABLE . "` , `" .
         CIV_TABLE . "` , `" . USERS_TABLE . "` WHERE `" . REPORT_TABLE .
         "`.`civ` = '" . $civ->cid . "' AND `" . REPORT_TABLE . "`.`id` = '" .
         $id . "' LIMIT 1";
        $row = Zend_Db_Table::getDefaultAdapter()->fetchRow($query);
        if ($row['read'] == 0)
            Zend_Db_Table::getDefaultAdapter()->query(
            "INSERT IGNORE INTO `" . REPORT_READ_TABLE .
             "` (`id`, `user`) VALUES ('" . $id . "', '" . $user->user_id . "');");
        $row = Model_report::assemblereport($row);
        return $row;
    }
    /**
     * cancella uno o più report
     * @param int | Array $id
     */
    static function deleteReport ($id)
    {
        if (gettype($id) == "array") {
            $cond = "WHERE `id` IN ('" . implode("','", $id) . "')";
            Zend_Db_Table::getDefaultAdapter()->query(
            "DELETE FROM `" . REPORT_TABLE . "` " . $cond);
            Zend_Db_Table::getDefaultAdapter()->query(
            "DELETE FROM `" . REPORT_READ_TABLE . "` " . $cond);
        } else {
            Zend_Db_Table::getDefaultAdapter()->query(
            "DELETE FROM `" . REPORT_TABLE . "` WHERE `id`='" . $id . "'");
            Zend_Db_Table::getDefaultAdapter()->query(
            "DELETE FROM `" . REPORT_READ_TABLE . "` WHERE `id`='" . $id . "'");
        }
    }
    static function assemblereport ($row)
    {
        require_once APPLICATION_PATH . '/views/helpers/template.php';
        require_once APPLICATION_PATH . '/views/helpers/image.php';
        $t = Zend_Registry::get("translate");
        $tmp = new Zend_View_Helper_template();
        $img = new Zend_View_Helper_image();
        $civ = Model_civilta::getInstance();
        $data = unserialize($row['data']);
        //Zend_Registry::get("log")->log($data, Zend_Log::DEBUG);
        if ($data['error'])
            $row['testo'] = $data['error'];
        else {
            switch ($data['type']) {
                case MARKET_REPORT:
                    $what = " " . $t->_('rifornisce') . " ";
                    $row['testo'] = $t->_('sono state scaricate:') . '<br/>';
                    for ($i = 0; $i < 3; $i ++) {
                        $row['testo'] .= ' ' . $data['res'][$i] . ' ' .
                         $img->resource($i, $civ->getAge());
                    }
                    break;
                case ATTACK_REPORT:
                    $what = " " . $t->_('attacca') . " ";
                    $info = array();
                    $def = "";
                    $showdef = false;
                    if (($data['totsupa'] < 1) && ($data['round'] <= 5)) {
                        $info[] = $t->_(
                        "Abbiamo perso ogni contatto con l'esercito.");
                        if ($civ->cid == $data['def']['civ']) {
                            $def = $tmp->report($data['def'], $data['supdef'], 
                            $data['round'] + 1, $t->_('Difensore'));
                            $showdef = true;
                        }
                    } else {
                        $def = $tmp->report($data['def'], $data['supdef'], 
                        $data['round'] + 1, $t->_('Difensore'));
                        $showdef = true;
                    }
                    $bottino = $t->_("risorse rubate");
                    if ($data['res']) {
                        for ($i = 0; $i < 3; $i ++) {
                            $bottino .= " " . $data['res'][$i] . ' ' .
                             $img->resource($i, $data['atk']['age']) . " ";
                        }
                    }
                    $info[] = $bottino;
                    $round = '';
                    if ($data['infround']) {
                        $rep = array(
                        'title' => $row['oggetto'] = $tmp->village(
                        $data['village_A'], null, false) . $what .
                         $tmp->village($data['village_B'], null, false), 
                        'text' => '');
                        $atk = $data['atk'];
                        $defe = $data['def'];
                        foreach ($data['infround'] as $r => $inf) {
                            $atk['troops'] = $inf['atk'];
                            $defe['troops'] = $inf['def'];
                            $rep['text'] .= $tmp->report($atk, $inf['supa'], 
                            $r + 1, $t->_('Attaccante'));
                            if ($showdef)
                                $rep['text'] .= $tmp->report($defe, 
                                $inf['supd'], $r + 1, $t->_('Difensore'));
                        }
                        $round .= '<script type="text/javascript">var rep=' .
                         json_encode($rep) .
                         ';</script><a href="#" onclick="ev.windows({x:700,y:700},\'center\',rep)">' .
                         $t->_('guarda il report intero') . '</a>';
                    }
                    $row['testo'] = $round .
                     $tmp->report($data['atk'], $data['supatk'], 
                    $data['round'] + 1, $t->_('Attaccante'), $info) . $def;
                    break;
                case REINF_REPORT:
                    $what = " " . $t->_('rinforza') . " ";
                    $title = $t->_('trupppe di') . " " .
                     $tmp->village($data['village_A']);
                    $row['testo'] = $tmp->tabletroops($data['troops'], $title, 
                    $t->_('le truppe si sono schierate con successo'));
                    break;
                case RAID_REPORT:
                    $what = " " . $t->_('raida') . " ";
                    $info = array();
                    $def = "";
                    if (($data['totsupa'] < 1) && ($data['round'] <= 5)) {
                        $info[] = $t->_(
                        "Abbiamo perso ogni contatto con l'esercito.");
                        if ($civ->cid == $data['def']['civ']) {
                            $def = $tmp->report($data['def'], $data['supdef'], 
                            $data['round'] + 1, $t->_('Difensore'));
                            $showdef = true;
                        }
                    } else {
                        $def = $tmp->report($data['def'], $data['supdef'], 
                        $data['round'] + 1, $t->_('Difensore'));
                        $showdef = true;
                    }
                    $bottino = $t->_("risorse rubate");
                    for ($i = 0; $i < 3; $i ++) {
                        $bottino .= " " . $data['res'][$i] . ' ' .
                         $img->resource($i, $data['atk']['age']) . " ";
                    }
                    $info[] = $bottino;
                    $round = '';
                    if ($data['infround']) {
                        $rep = array(
                        'title' => $row['oggetto'] = $tmp->village(
                        $data['village_A'], null, false) . $what .
                         $tmp->village($data['village_B'], null, false), 
                        'text' => '');
                        $atk = $data['atk'];
                        $defe = $data['def'];
                        foreach ($data['infround'] as $r => $inf) {
                            $atk['troops'] = $inf['atk'];
                            $defe['troops'] = $inf['def'];
                            $rep['text'] .= $tmp->report($atk, $inf['supa'], 
                            $r + 1, $t->_('Attaccante'));
                            if ($showdef)
                                $rep['text'] .= $tmp->report($defe, 
                                $inf['supd'], $r + 1, $t->_('Difensore'));
                        }
                        $round .= '<script type="text/javascript">var rep=' .
                         json_encode($rep) .
                         ';</script><a href="#" onclick="ev.windows({x:700,y:700},\'center\',rep)">' .
                         $t->_('guarda il report intero') . '</a>';
                    }
                    $row['testo'] = $round .
                     $tmp->report($data['atk'], $data['supatk'], 
                    $data['round'] + 1, $t->_('Attaccante'), $info) . $def;
                    break;
                case REINF_LOST:
                    $def = array('troops' => $data['troop'], 
                    'village_id' => $data['village_B'], 
                    'village_name' => $data['village_name'], 
                    'user' => $civ->data['civ_name'], 'civ' => $civ->cid);
                    $what = " " . $t->_('rinforzi attaccati a') . " ";
                    $row['testo'] = $tmp->report($data['atk'], $data['supatk'], 
                    $data['round'], $t->_('Attaccante'), $info) . $tmp->report(
                    $def, $data['sup'], 0, $t->_('Supporter'));
                    break;
                case COLONY_REPORT:
                    $what = " " . $t->_('colonizza') . " ";
                    if ($data['succes']) {
                        $row['testo'] = $t->_('I nostri') . ' ' . $data['num'] .
                         ' ' . $t->_('coloni hanno colonizzato') . ' ' .
                         $tmp->village($data['village_B']);
                    } else {
                        $row['testo'] = $t->_('I nostri') . ' ' . $data['num'] .
                         ' ' . $t->_('coloni non sono riusciti a colonizzare') .
                         ' ' . $tmp->village($data['village_B']);
                    }
                    break;
                default:
                    $what = " ??? ";
                    $row['testo'] = "????";
                    break;
            }
        }
        $row['oggetto'] = $tmp->village($data['village_A'], null, false) . $what .
         $tmp->village($data['village_B'], null, false);
         if ($data['sim']) {
         	$row['oggetto']='('.$t->_('SIMULAZIONE').')'.$row['oggetto'];
         	$row['testo']='<h1>'.$t->_('SIMULAZIONE').'</h1>'.$row['testo'];
         }
        return $row;
    }
}
?>