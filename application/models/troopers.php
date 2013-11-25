<?php

/**
 * troopers
 * 
 * @author pagliaccio
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Model_troopers extends Zend_Db_Table_Abstract {

	/**
	 * The default table name 
	 */
	protected $_name=TROOPERS;
	protected $_primary="trooper_id";
	public $my_troopers = null;
    public $other_troopers = null;
    public $troopers_now=null;

	function __construct($vid, $cid) {
		parent::__construct();
		$this->delete("`numbers`<='0'");
		$where="`civ_id`='" . $cid . "' AND `village_now`='" . $vid . "' AND 
		`village_prev`='" . $vid . "'";
		$temp=$this->fetchAll($where, 'trooper_id') or array();
		$this->troopers_now=array();
		for($i=0, $prev=null;$i<count($temp);$i++) {
			if ($prev['trooper_id'] == $temp[$i]['trooper_id']) {
				$this->troopers_now[$prev['trooper_id']]['numbers']+=$temp[$i]['numbers'];
				$where=array("`numbers`='" . $temp[$i]['numbers'] . "'",
						"`civ_id`='" . $cid . "' ",
						"`village_now`='" . $vid . "'",
						"`village_prev`='" . $vid . "'",
						" `trooper_id`='" . $temp[$i]['trooper_id'] . "'");
				$this->getDefaultAdapter()->query("DELETE FROM `".TROOPERS."` WHERE ".implode(" AND ", $where)." LIMIT 1");
				$data=array("numbers"=>
						$this->troopers_now[$prev['trooper_id']]['numbers']);
				$where=array("`civ_id`='" . $cid . "'",
						"`village_now`='" . $vid . "' ",
						" `village_prev`='" . $vid . "' ",
						" `trooper_id`='" . $temp[$i]['trooper_id'] . "'");
				$this->update($data, $where);
				$prev=$temp[$i];
			}
			else {
				$this->troopers_now[$temp[$i]['trooper_id']]=$temp[$i];
				$prev=$temp[$i];
			}
		}
		//truppe in altri villi
		$this->my_troopers=$this->getDefaultAdapter()
			->fetchAll(
				"SELECT `" . TROOPERS . "`.*,
			`" . SERVER . "_village`.`name`
			FROM `" . TROOPERS . "`,`" . SERVER . "_village` 
			WHERE `village_prev`='" . $vid . "' 
			AND `village_now`!='" . $vid . "' 
			AND `village_now`=`" . SERVER . "_village`.`id` 
			ORDER BY `village_now`,`trooper_id`") or array();
		
		//truppe straniere nel proprio villo
		$this->other_troopers=$this->getDefaultAdapter()
			->fetchAll(
				"SELECT `" . TROOPERS . "`.*,
            `" . SERVER . "_village`.`name`
             FROM `" . TROOPERS . "`,`" . SERVER . "_village` 
             WHERE `village_prev`!='" . $vid . "' 
             AND `village_now`='" . $vid . "' 
             AND `village_prev`=`" . SERVER . "_village`.`id` 
             ORDER BY `village_prev`,`trooper_id`") or array();
		
	}

}
