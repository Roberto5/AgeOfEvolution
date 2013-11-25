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
    private $t;
    /**
     * 
     * @var Model_civilta
     */
    //private $civ;
    private $log;
    private $map;
    function __construct ()
    {
    	$this->_name=SERVER.'_map';
    	
        $this->t = Zend_Registry::get("translate");
        $this->log = Zend_Registry::get("log");
        //$this->civ=Model_civilta::getInstance();
        //parent::__construct();
        $this->map=json_decode(file_get_contents(MAP_FILE));
    }
    /**
     * understandig id sistem
     * es map 5x5
     * y/x 0  1  2  3  4
     * 0   00 01 02 03 04
     * 1   05 06 07 08 09
     * 2   10 11 12 13 14
     * 3   15 16 17 18 19
     * 4   20 21 22 23 24
     * id=5*y+x
     * 
     * id=17
     * x=17%5=2
     * y=intval(17/5)=3
     * @param int $id
     * @return array
     */
    function getCoordFromId(int $id) {
    	$x=$id%MAX_X;
    	$y=intval($id/MAX_X);
    	return array('x'=>$x,'y'=>$y);
    }
    /**
     * 
     * @param int $x
     * @param int $y
     * @return int
     */
    function getIdFromCoord(int $x,int $y) {
    	$id=MAX_X*$y+$x;
    	return $id;
    }
    /**
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
         "`.`civ_id`=`" . CIV_TABLE . "`.`civ_id`"
     * @return Array 
     */
    function getVillageArray ()
    {
        return $this->fetchAll();;
    }
    /**
     * genera una tabella html del villaggi
     * @param $dim
     * @param $h
     * @param $w
     * @return string
     */
    function getMapTable ($dim, $h = 18, $w = 24)
    {
        //$base = Zend_Controller_Front::getInstance()->getBaseUrl();
        //$base.='/'.Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
        
        $gap=0;$n=0;
        for($j=1;$j<=$h;$j++) {
            for ($i=1;$i<=$w;$i++) {
            	$table.='<div style="position:relative;left:'.(($i-1)*$dim).'px;top:'.($gap+($j-1)*$dim).'px;" 
            				class="map_village zoom-'.$dim.'" id="map_village_' . $i .
                 '_'.$j.'" onmouseover="ev.map.details($(this).data(\'coords\'),'.$n++.');" 
            				onmouseout="ev.map.hide_map_details();" 
            				onclick="if (!ev.drag) ev.map.get_village_info($(this).data(\'coords\')); else ev.drag=false;" alt="'.$x.'|'.$y.'" ><div><!--vilage--><div><!--flag--></div></div></div>';
            	$gap-=50;
            }
        }
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
    	
        if (($x > MAX_X) || ($x < - MAX_X) || ($y > MAX_Y) || ($y < - MAX_Y))
            $zone = WATER;
        else
            $zone=$this->map->layers[0]->data[$this->getIdFromCoord($x, $y)];
        return ($zone ? $zone : "0");
    }
    /**
     * genera una macchia, disegnando angolo per angolo un raggio che aumenta o 
     * diminuisce in maniera casuale
     * @param unknown_type $zone
     * @param unknown_type $rad
     * @param unknown_type $cx
     * @param unknown_type $cy
     *
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
    }//*/
}
