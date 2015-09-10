<?php
/**
 * classe astratta delle truppe
 *
 * @author pagliaccio
 */
abstract class troops {
    /**
     * struttura
     * ['build']
     *      [1]
     *          ['type']
     * ['research']
     *      [1]
     *          ['type']
     *          ['liv']
     * @var array $condiction
     */
    public static $age=0;
    public static $atk = 0;
    public static $hp = 1;
    public static $def1 = 0;
    public static $def2 = 0;
    public static $git = 0;
    public static $speed = 0;
    public static $cost = array(0,0,0,0);// truppe edifici monete pop
    public static $capacity = 0;
    public static $description = "";
    public static $condiction=array();
    public static $type=1;
    public static abstract function specialEffect($param);
}
class mercants {

    static $speed = array(15, 20, 25, 30, 50);
    static $capacity = array(1000, 1500, 3000, 6000, 9000, 15000);

}

class colony extends troops {
	static $speed=5;
	static $capacity = 15;
	static $cost=array(50,0,0,1);
	static $description="I coloni sono assunti tra la gente comune, pionieri in cerca di nuove terre in cui abitare.";
	public static function specialEffect($param) {
		return array();
	}
}

/* PREISTORIA */

class clava extends troops {

    static $age = 0;
    static $atk = 5;
    static $hp = 20;
    static $def1 = 0;
    static $def2 = 0;
    static $git = 0;
    static $speed = 2;
    static $cost = array(0, 27, 0, 1);
    static $time = 3700;
    static $capacity = 10;
    static $description = "i guerrieri con la clava sono truppe primitive, attaccano con ossa o legni i propri nemici.";
    public static function specialEffect($param=0) {
        return array();
    }
}

class fionda extends troops {

    static $age = 0;
    static $atk = 3;
    static $hp = 10;
    static $def1 = 0;
    static $def2 = 0;
    static $git = 1;
    static $speed = 3;
    static $cost = array(7, 10, 0, 1);
    static $time = 2700;
    static $capacity = 10;
    static $description = "i guerrieri con la fionda sono truppe primitive, attaccano lanciando sassi sui nemici con rudimentali fionde o con le mani.";
    public static function specialEffect($param=0) {
        return array();
    }
}

// ANTICA

class falange extends troops {
    public static $age=1;
    public static $atk = 10;
    public static $hp = 50;
    public static $def1 = 5;
    public static $def2 = 5;
    public static $git = 0;
    public static $speed = 5;
    public static $cost = array(0,86,0,1);
    public static $capacity = 10;
    public static $description = "le falangi sono truppe organizzate per la difesa, si ammassano con i loro scudi per creare un fronte saldo e impenetrabile. riceve il 10% di bonus hp ogni 100 unità fino a un massimo di 100% in difesa.";
    public static $condiction=array();
    /**
     * calcola il bonus difesa
     * @param int $param numero delle truppe
     */
    public static function specialEffect($param=0) {
    	$bonus=0;
    	if ($param['type']=='def') 
        	for($i=100;($i<=1000)&&($i<=$param['num']);$i+=100,$bonus+=10);
        return array('hp'=>$bonus);
    }
}

class arco extends troops {
	
    public static $age=1;
    public static $atk = 8;
    public static $hp = 35;
    public static $def1 = 1;
    public static $def2 = 1;
    public static $git = 2;
    public static $speed = 8;
    public static $cost = array(0,50,25,1);
    public static $time = 0;
    public static $capacity = 20;
    public static $description = "gli arcieri sono truppe ottime truppe di supporto, colpiscono a distanza indebolendo l'avanzata del nemico";
    /**
     * struttura
     * ['build']
     *      [1]
     *          ['type']
     * ['research']
     *      [1]
     *          ['type']
     *          ['liv']
     * @var array $condiction
     */
    public static $condiction=array('research'=>array(array('type'=>RES_ARCO,'liv'=>1)),'build'=>array(array('type'=>BARRACK)));
    public static function specialEffect($param) {
    	return array();
    }
}
?>
