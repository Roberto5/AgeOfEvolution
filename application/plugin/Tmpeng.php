<?php
class Zend_View_Filter_Tmpeng //implements Zend_Filter_Interface
{
	static private $key=array();
	public function filter($buffer)
	{
		foreach (self::$key as $key => $value) {
			$buffer=str_replace($key, $value, $buffer);
		}
		//$log=Zend_Registry::get('log');
		$n=preg_match_all('/\[\w+?\]/',$buffer,$word);
		if ($n) $buffer=$this->translate($buffer, $word[0]);
		//	$log->debug($word,'word');
		//$log->debug($buffer,'buffer');
	//	file_put_contents('/var/www/preg2', print_r(self::$key,true).' 
	//			****  
	//			'.$buffer,FILE_APPEND);
		
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