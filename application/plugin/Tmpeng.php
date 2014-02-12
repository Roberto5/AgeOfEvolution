<?php
class Zend_View_Filter_Tmpeng //implements Zend_Filter_Interface
{
	static private $key=array();
	public function filter($buffer)
	{
		foreach (self::$key as $key => $value) {
			$buffer=str_replace($key, $value, $buffer);
		}
		$n=preg_match_all('/\[\w+?\]/',$buffer,$word);
		if ($n) $buffer=$this->translate($buffer, $word[0]);
		$img=new Zend_View_Helper_image();
		$buffer=$img->parse($buffer);
		return $buffer;
	}
	public function translate($buffer,$word) {
		/**
		 * 
		 * @var Zend_Translate
		 */
		$t=Zend_Registry::get('translate');
		foreach ($word as $value) {
			$w=str_replace(array('[',']'), "", $value);
			$trad=$t->_($w);
			if ($trad!=$w) {
				$buffer=str_replace($value, $trad, $buffer);
			}
		}
		return $buffer;
	}
	public static function addkey(Array $keys) {
		self::$key=array_merge(self::$key,$keys);
	}
}
?>