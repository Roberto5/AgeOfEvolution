<?php 

error_reporting(E_ALL);


class jsmin {
	private $file;
	private $out;
	private $path;
	public function __construct($file,$path="") {
		if (is_array($file)) $this->file=$file;
		else $this->file[0]=$file;
		$this->path=$path;
	}
	private function comprime($buffer) {
		//remove comments \/\/.*$
		$buffer=preg_replace("(\/\/[^\n\r]*(\n?\r?))","",$buffer);
		$buffer=preg_replace("(\/\*[\s\S]*?\*\/)","",$buffer);
		//remove tabs
		//$buffer=preg_replace("(\t+)","",$buffer);
		$buffer=str_replace("\t", "", $buffer);
		//remove repeat space
		
		$buffer=preg_replace("([\n\r])"," ",$buffer);
		$buffer=preg_replace("((\s\s)+)","",$buffer);
		$buffer=preg_replace("(\s*([\(])\s*)","$1",$buffer);
		$buffer=preg_replace("(\s*([\)])\s*)","$1 ",$buffer);
		$buffer=preg_replace("(\s*([\.=:;+,\{\}\[\]])\s*)","$1",$buffer);
		
		return $buffer;
	}
	public function min()
	{
		$this->out="";
		foreach ($this->file as $value) {
			$buffer=file_get_contents($this->path.$value);
			$this->out.='\n'.$this->comprime($buffer);
		}
		return $this->out;
	}
}
/*$min=new jsmin($_GET['f']);
echo '<pre>'.htmlentities($min->min()).'</pre>';*/
?>