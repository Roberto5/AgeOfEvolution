<?php

class Plugin_Logweb extends Zend_Log_Writer_Abstract {
	private $layout;
	function __construct() {
		//$layout=new Zend_View_Helper_Layout();
		$this->layout=Zend_Layout::getMvcInstance()->getView();
		$this->layout->logger=array();
	}
	function write($event) {
		$this->_write($event);
	}
	protected function _write($event) {
		$event['message']=json_encode($event['message']);
		$this->layout->logger[]=$event;
	}
	static function factory($config) {
		$web=new Plugin_Logweb();
		return $web;
	}
	/*private function format($data,$label='') {
		
		//if ($label) $label="Value of '$label' is : ";
		if (is_string($data)) {
			return " String(".strlen($data).") '".htmlentities($data)."'";
		}
		elseif (is_array($data)) {
			$text=' <summary><b>Array</b><details>';
			foreach ($data as $key => $value) {
				$text.="<div>[$key]=>".$this->format($value)."</div>";
			}
			$text.='</details></summary> ';
			return $text;
		}
		elseif (is_numeric($data)) {
			return " number '$data' ";
		}
		elseif (is_bool($data)) {
			return " boolean '".($data ? 'true':'false')."' ";
		}
		elseif (is_object($data)) {
			$text=' <summary><b>Object '.get_class($data).'</b><details>';
			$text.='<pre>'.print_r($data,true).'</pre>';
			$text.='</details></summary> ';
			
			return $text;
		}
		elseif (is_null($data)) {
			return " NULL ";
		}
		else {
			return ' new type '.print_r($data,true);
		}
	}*/
}

?>