<?php
class Model_refresh {
	private $ids=array();
	private $master;
	private $disable=array();
	private $attr=array();
	private $token=array();
	private $dispB=array();
	public $displaytroops=array();
	public $building=array();
	public $event=array();
	private $javascript=array();
	private $focus;
	private $item=array('ids','master','disable','attr','token','dispB','building','event','focus');
	static protected $instance=null;
	function __construct($config=null) {
		if (is_array($config)) {
			foreach ($config as $key => $value) {
				$this->$key=$value;
			}
		}
		self::$instance=$this;
	}
	/**
	 * 
	 * @return Model_refresh
	 */
	static function getInstance() {
		if (self::$instance === null) {
            self::$instance = new Model_refresh();
        }
      
        return self::$instance; 
	}
	
	function setFocus($vid) {
		$this->focus=$vid;
		self::$instance=$this;
	}
	
	public function addjs($js) {
		$this->javascript[]=$js;
		self::$instance=$this;
	}
	public function getjs(){
		return implode("",	$this->javascript);
	}
	/**
	 * ritorna l'oggetto update
	 * @return array false se l'oggetto è vuoto
	 */
	public function getdata() {
		$reply=array();
		foreach ($this->item as $key) {
			$value=$this->$key;
			if ($value) $reply[$key]=$value;
		}
		if (count($reply)==0) $reply=false;
		return $reply;
	}
	/**
	 * 
	 * @param array|int $type [type]=>array('n'=>int,'content'=>String)
	 * @param int $n
	 * @param String $content
	 */
	public function addEvent($type,$n=0,$content=null) {
		if (is_array($type)) {
			$this->event=$type;
		}
		else {
			$this->event[$type]=array('n'=>$n,'content'=>$content);
		}
		self::$instance=$this;
	}
	/**
	 * aggiunge gli id
	 * @param string|array $key
	 * @param mixed $value
	 */
	function addIds($key,$value=null) {
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->ids[$k]=$v;
			}
		}
		else $this->ids[$key]=$value;
		self::$instance=$this;
	}
	/**
	 * aggiunge token
	 * @param string $key
	 * @param mixed $value
	 */
	function addToken($key,$value) {
		$this->token[$key]=$value;
		self::$instance=$this;
	}
	
	function setMaster($master) {
		$this->master=$master;
		self::$instance=$this;
	}
	/**
	 * aggiorna gli attributi
	 * @param String|array $id
	 * @param array $attr 'key'=>'value'
	 */
	function addAttr($id,$attr=null) {
		if (is_array($id)) {
			$this->attr=$id;
		}
		else $this->attr[$id]=$attr;
		self::$instance=$this;
	}
	
	function addDisable($key,$value) {
		$this->disable[$key]=$value;
		self::$instance=$this;
	}
	/**
	 * 
	 * @param array $disp type=>bool or int key
	 * @param bool $bool
	 */
	function addDispB($disp,$bool=true) {
		if (is_array($disp)) 
		$this->dispB=$disp;
		else $this->dispB[$disp]=$bool;
		self::$instance=$this;
	}
	/**
	 * inserisce i dati degli edifici
	 * @param int|array $pos
	 * @param String $type
	 * @param String $title
	 */
	function addBuilding($pos,$type="",$title="") {
		if (is_array($pos)) {
			foreach ($pos as $key => $value) {
				$this->building[$key]=$value;
			}
		}
		else $this->building[$pos]=array('type'=>$type,'title'=>$title);
		self::$instance=$this;
	}
}
?>