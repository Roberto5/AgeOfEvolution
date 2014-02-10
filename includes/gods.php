<?php
class master1 // Generale
{
	static $prod_bonus = array('0' => array(0,5,5,5), '1' => array(0,25,25,25)); // array(null,%prod1,%prod2,%prod3)
	static $troops_bonus = array('0' => array(0,0,0), '1' => array(5,5,5)); // array(%atk,%hpatk,%hpdef)
}
class master2 // Offensiva
{
	static $prod_bonus = array('0' => array(0,0,5,0), '1' => array(0,0,50,0)); // array(null,%prod1,%prod2,%prod3)
	static $troops_bonus = array('0' => array(0,0,0), '1' => array(10,0,0)); // array(%atk,%hpatk,%hpdef)
}
class master3 // Difensiva
{
	static $prod_bonus = array('0' => array(0,0,0,5), '1' => array(0,0,0,50)); // array(null,%prod1,%prod2,%prod3)
	static $troops_bonus = array('0' => array(0,0,0), '1' => array(0,0,20)); // array(%atk,%hpatk,%hpdef)
}
class master4 // Economica
{
	static $prod_bonus = array('0' => array(0,5,0,0), '1' => array(0,50,0,0)); // array(null,%prod1,%prod2,%prod3)
	static $troops_bonus = array('0' => array(0,0,0), '1' => array(0,0,10)); // array(%atk,%hpatk,%hpdef)
}
?>