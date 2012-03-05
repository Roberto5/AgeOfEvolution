<?php
/**
 * map
 * 
 * @author pagliaccio
 * @version 
 */
require_once 'Zend/Db/Table/Abstract.php';
class Model_map extends Zend_Db_Table_Abstract
{
    /**
     * The default table name 
     */
    protected $_name = 'map';
    private $t;
    /**
     * 
     * @var Model_civilta
     */
    private $civ;
    private $log;
    function __construct ()
    {
        $this->t = Zend_Registry::get("translate");
        $this->log = Zend_Registry::get("log");
        $this->civ=Model_civilta::getInstance();
    }
    /**
     * restituisce una matrice con le caratteristiche del villaggio
     * @param int $centX
     * @param int $centY
     * @param int $rx
     * @param int $ry
     * @return Array 
     */
    function getVillageArray ($centX = 0, $centY = 0, $rx = 3, $ry = 3,$cache=0)
    {
        $rows = $this->getDefaultAdapter()->fetchAll(
        "SELECT `" . MAP_TABLE . "`.`id`,`" . MAP_TABLE . "`.`civ_id`,`" . MAP_TABLE . "`.`name`,
        	`" . MAP_TABLE . "`.`capital`,`" . MAP_TABLE . "`.`type`,`" . MAP_TABLE . "`.`busy_pop`,
        	`" . MAP_TABLE . "`.`x`,`" . MAP_TABLE . "`.`y`,`" . MAP_TABLE . "`.`zone`,
        	`" . MAP_TABLE . "`.`prod1_bonus`,`" . MAP_TABLE . "`.`prod2_bonus`,`" . MAP_TABLE . "`.`prod3_bonus`, 
        	`" . CIV_TABLE ."`.`civ_name`, `" . CIV_TABLE . "`.`civ_age`,`" . CIV_TABLE . "`.`civ_ally`,
        		(SELECT `name` 
        			FROM `" . ALLY_TABLE . "` 
        			WHERE `" .
         ALLY_TABLE . "`.`id` =`" . CIV_TABLE . "`.`civ_ally` 
        		) AS `ally` 
        		FROM `" .
         MAP_TABLE . "`,`" . CIV_TABLE . "` WHERE `x` >= '" . ($centX - $rx-$cache) .
         "' AND `x` <= '" . ($centX + $rx+$cache) . "' AND `y` >= '" . ($centY - $ry-$cache) .
         "' AND `y` <= '" . ($centY + $ry+$cache) . "' AND `" . MAP_TABLE .
         "`.`civ_id`=`" . CIV_TABLE . "`.`civ_id`");
        for ($i = 0; $rows[$i]; $i ++) {
        	if ((abs($rows[$i]["x"]-$centX)<=$rx)&&((abs($rows[$i]["y"]-$centY)<=$ry)))
            	$map['focus'][$rows[$i]["x"]][$rows[$i]["y"]] = $rows[$i];
            else $map['cache'][$rows[$i]["x"]][$rows[$i]["y"]] = $rows[$i];
        }
        return $map;
    }
    /**
     * genera una tabella html del villaggi
     * @param array $villaggi (matrice x y)
     * @return string
     */
    function getMapTable ($villaggi, $dim, $h = 18, $w = 24)
    {
        $base = Zend_Controller_Front::getInstance()->getBaseUrl();
        $base.='/'.Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
        $table = '<map name="map">';
        $mx=min(array_keys($villaggi));
        $my=max(array_keys($villaggi[$mx]));
        
        for($y = $my,$j=1; $y>($my-$h); $y--,$j++) {
            
            for ($x =$mx,$i=1;$x<($w+$mx); $x++,$i++) {
            	$own=$villaggi[$x][$y]['civ_id']==$this->civ->cid ? '1' : '0';
                $table.='<area shape="rect" coords="'.(($i-1)*$dim).','.(($j-1)*$dim).','.(($i-1)*$dim+$dim).','.(($j-1)*$dim+$dim).'" class="map_village zoom-'.$dim.'" id="map_village_' . $i .
                 '_'.$j.'" onmouseover="ev.map.details($(this).attr(\'alt\'));" onmouseout="ev.map.hide_map_details();" onclick="if (!ev.drag) ev.map.get_village_info($(this).attr(\'alt\')); else ev.drag=false;" alt="'.$x.'|'.$y.'" />';
            }
            //$table .= "</div>";
        }
        $table.='</map>';
        return $table;
    }
    /**
     * ritorna l'id del dio che influenza la zona
     * @param int coordinata x
     * @param int coordinata y
     * @return int
     */
    static function getZone ($x, $y)
    {
        if (($x > 400) || ($x < - 400) || ($y > 400) || ($y < - 400))
            $zone = 5;
        else
            $zone = Zend_Db_Table::getDefaultAdapter()->fetchOne(
            "SELECT `zone` FROM `" . MAP_TABLE . "` WHERE `x`='" . $x .
             "' AND `y`='" . $y . "'");
        return ($zone ? $zone : "0");
    }
    /**
     * genera una macchia, disegnando angolo per angolo un raggio che aumenta o 
     * diminuisce in maniera casuale
     * @param unknown_type $zone
     * @param unknown_type $rad
     * @param unknown_type $cx
     * @param unknown_type $cy
     */
    static function generateZone ($zone, $rad, $cx, $cy)
    {
        //$co = array();
        //controllo macchia dentro la mappa
        if ((abs($cx) > (MAX_X - $rad))) {
            $cx = (MAX_X - $rad) * ($cx / abs($cx));
        }
        if ((abs($cy) > (MAX_Y - $rad))) {
            $cy = (MAX_Y - $rad) * ($cy / abs($cy));
        }
        $gap = 1; //massima differenza di raggio da un grado all'altro
        $radt = rand($rad / 2, $rad); //genero il raggio ad angolo 0
        $rad0 = $radt;
        for ($a = 0; $a <= 360; $a ++) { // ciglo angolo
            //differenza tra l'angolo a zero gradi e quello precedente (diviso il gap)
            $dif = intval(($rad0 - $radt) / $gap);
            if ($a < (360 - abs($dif))) {
                // se la differenza tra l'angolo a 0° e quello precedente
                // tolta all'angolo giro è maggiore dell'angolo attuale
                // possiamo aumentare o diminuire il raggio in maniera casuale
                if (rand(0, 1) &&
                 ($radt < $rad + $gap) || ($radt < ($rad / 2) + $gap))
                    // aumento il raggio se viene sorteggiato e se nn sforo il raggio massimo
                    // oppure se il raggio è minore del raggio minimo
                    $radt += rand(0, 
                    $gap);
                else
                    $radt -= rand(0, $gap);
            } else {
                //altrimenti dobbiamo ritornare all raggio 0°
                if ($dif < 0)
                    $radt --;
                elseif ($dif > 0)
                    $radt ++;
            }
            // disegnamo il raggio
            for ($r = 0; $r < $radt; $r ++) {
                $x = round(cos(deg2rad($a)) * $r) + $cx;
                $y = round(sin(deg2rad($a)) * $r) + $cy;
                //Zend_Db_Table::getDefaultAdapter()->delete("temp",array("x='$x'","y='$y'"));
                //Zend_Db_Table::getDefaultAdapter()->insert("temp", array('x'=>$x,'y'=>$y,'zone'=>$zone));
                $co[$x][$y] = $zone;
            }
        }
        return $co;
    }
}
