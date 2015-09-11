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
	//private $map;
	//public $city;
	static private $instance;
	function __construct ()
	{
		$this->_name=SERVER.'_data_map';
		$this->_primary='id';
		$this->t = Zend_Registry::get("translate");
		$this->log = Zend_Registry::get("log");
		parent::__construct();
		$this->getDefaultAdapter()->setFetchMode(Zend_Db::FETCH_ASSOC);
		self::$instance=$this;
	}
	/**
	 * @return Model_map
	 */
	static function getInstance() {
		if (self::$instance) return self::$instance;
		else return new Model_map();
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
	  
	 * @param int $id
	 * @return array
	 */
	function getCoordFromId($id) {
		$id=intval($id);
		$x=$id%MAX_X-intval(MAX_X/2);
		$y=intval(MAX_Y/2)-intval($id/MAX_X);
		return array('x'=>$x,'y'=>$y);
	}
	/**
	 *
	 * @param int $x
	 * @param int $y
	 * @return int
	 */
	function getIdFromCoord($x,$y) {
		$id=MAX_X*(intval(MAX_Y/2)-$y)+($x+intval(MAX_X/2));
		return $id;
	}
	/**

	* @return Array
	*/
	function getVillageArray()
	{
		if (!isset($this->city)) {
			$this->city=array();
			$rows=$this->fetchAll()->toArray();
			foreach ($rows as $value) {
				$this->city[$value['id']]=$value;
			}
		}
		return $this->city;
	}
	static function getzone($area) {
		switch ($area) {
			case 1:
			case 3:
			case 13:
			case 15:
			case 2:
			case 7:
			case 9:
			case 14:
			case 19:
			case 20:
			case 25:
			case 26:
			case 8:$zone=1;
			break;
			case 4:
			case 6:
			case 16:
			case 18:
			break;
			case 5:
			case 10:
			case 12:
			case 17:
			break;
			case 22:
			case 23:
			case 28:
			case 29:
			case 11:$zone=2;
			break;
			case 31:
			case 33:
			case 43:
			case 45:
			case 32:
			case 37:
			case 39:
			case 44:
			case 49:
			case 50:
			case 55:
			case 56:
			case 38:$zone=3;
			break;
			case 34:
			case 36:
			case 46:
			case 48:
			case 35:
			case 42:
			case 40:
			case 47:
			case 52:
			case 53:
			case 58:
			case 59:
			case 41:$zone=4;
			break;
			/*case :
			case :
			case :
			case :$bonus=$this->area();
			break;*/
			default:
				$zone=0;
				break;
		}
		return $zone;
	}
	/**
	 * area 0 100 100 100 max 300
	 * area 1 150 150 150 max 450 (+-50%)
	 * area 2 125 200 125 max 450
	 * area 3 125 125 200 max 450
	 * area 4 200 125 125 max 450
	 *
	 * zona 1-3-13-15 area 1 al 25%
	 * zona 2-7-9-14 area 1 al 50%
	 * zona 19-20-25-26 area 1 al 75%
	 * zona 8 area 1 al 100%
	 * zona 4-6-16-18 area 2 al 25%
	 * zona 5-10-12-17 area 2 al 50%
	 * zona 22-23-28-29 area 2 al 75%
	 * zona 11 area 2 al 100%
	 * zona 31-33-43-45 area 3 al 25%
	 * zona 32-37-39-44 area 3 al 50%
	 * zona 49-50-55-56 area 3 al 75%
	 * zona 38 area 3 al 100%
	 * zona 34-36-46-48 area 4 al 25%
	 * zona 35-40-42-47 area 4 al 50%
	 * zona 52-53-58-59 area 4 al 75%
	 * zona 41 area 4 al 100%
	 * zona 21 area 0
	 * 57
	 * 24-27-30-51-54-60
	 */
	function calcbonus($area) {
		switch ($area) {
			case 1:
			case 3:
			case 13:
			case 15:$bonus=$this->area(1,25);
			break;
			case 2:
			case 7:
			case 9:
			case 14:$bonus=$this->area(1,50);
			break;
			case 19:
			case 20:
			case 25:
			case 26:$bonus=$this->area(1,75);
			break;
			case 8:$bonus=$this->area(1,100);
			break;
			case 4:
			case 6:
			case 16:
			case 18:$bonus=$this->area(2,25);
			break;
			case 5:
			case 10:
			case 12:
			case 17:$bonus=$this->area(2,50);
			break;
			case 22:
			case 23:
			case 28:
			case 29:$bonus=$this->area(2,75);
			break;
			case 11:$bonus=$this->area(2,100);
			break;
			case 31:
			case 33:
			case 43:
			case 45:$bonus=$this->area(3,25);
			break;
			case 32:
			case 37:
			case 39:
			case 44:$bonus=$this->area(3,50);
			break;
			case 49:
			case 50:
			case 55:
			case 56:$bonus=$this->area(3,75);
			break;
			case 38:$bonus=$this->area(3,100);
			break;
			case 34:
			case 36:
			case 46:
			case 48:$bonus=$this->area(4,25);
			break;
			case 35:
			case 42:
			case 40:
			case 47:$bonus=$this->area(4,50);
			break;
			case 52:
			case 53:
			case 58:
			case 59:$bonus=$this->area(4,75);
			break;
			case 41:$bonus=$this->area(4,100);
			break;
			/*case :
			case :
			case :
			case :$bonus=$this->area();
			break;*/
			default:
				$bonus=array('100','100','100');
				break;
		}
		return $bonus;
	}
	function area($t,$v) {
		switch ($t) {
			case 2:
			$n=1;
			break;
			case 3:
				$n=2;
				break;
			case 4:
				$n=0;
			break;
		}
		$this->log->debug($t,'t');
		$this->log->debug($n,'n');
		for ($i = 0; $i < 3; $i++) {
			if ($t==1) $bonus[i] = rand(100, 100+$v);
			elseif ($i==$n) $bonus[$i]=rand(150, 150+$v);
			else $bonus[$i]=rand(75, 125);
		}
		return $bonus;
	}
}
