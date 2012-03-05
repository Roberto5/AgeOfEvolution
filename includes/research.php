<?php 
abstract class researchs {
	static $livmax=array(1,1,1,1,1,1);
	static $cost=array();
	/**
	 * 
	 * @var array (
	 * [research]->array([0]->array([liv],[type])),
	 * [build]->array([0]->array([liv],[type])))
	 */
	static $require=array();
	static $age=0;
	static $description="";
	static $name;
}
class Rarco extends researchs {
	static $cost=array(40);
	static $description="questa ricerca ti permetter&agrave; di addestrare gli arceri.";
	static $age=1;
	static $name="Arco corto";
}

?>