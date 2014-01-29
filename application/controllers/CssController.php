<?php

class CssController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		$this->_helper->layout->disableLayout();
		/*if (! empty($_SERVER["HTTP_ACCEPT_ENCODING"]) &&
				strpos("gzip", $_SERVER["HTTP_ACCEPT_ENCODING"]) === NULL) {
		} else {
			ob_start("ob_gzhandler");
		}*/
		
		$conf = Zend_Registry::get("config");
		// set to a reaonable interval, say 3600 (1 hr)
		$this->view->expire = $conf->css->expire ? $conf->css->expire : 86400;
		
		/*$css=array(
		 'jquery.contextmenu.css',
				'lightbox.css',
				//'jquery-ui.css',
				//'scroll.css',
				'style.css'
		);*/
		$css=$conf->css->file->toArray();
		if ($_GET['l'] == 'g') {
			$css[] = 'game.css';
			if ((is_numeric($_GET['s'])) && ($_GET['s'] > 0) && ($_GET['s'] < 7)) {
				$css[] = "style_" . $_GET['s'] . ".css";
				$css[] = "jquery-ui-" . $_GET['s'] . ".css";
			} else {
				$css[] = "style_1.css";
			}
		} else {
			$css[] = 'home.css';
			$css[] = "jquery-ui-home.css";
		}
		$key=array('NORMAL'=>'#e6e6e6',
				'HOVER'=>'#dadada',
				'ACTIVE'=>'#eee',
				'INPUT_TEXT'=>'#000',
				'INPUT_BG'=>'#fff',
				'BACKGROUND2'=>'#aaa',
				'BACKGROUND'=>'#eee',
				'COLOR'=>'#000',
				'BORDER'=>'#001',
				'PATH'=>$this->view->baseUrl()
		);
		$mtime = 0;
		chdir(APPLICATION_PATH.'/../common/css/');
		foreach ($css as $value) {
			$r = $this->dump_css_cache($value,$key);
			$display .= $r['text'];
			if ($mtime < $r['mtime']) $mtime = $r['mtime'];
		}
		$this->view->mtime=$mtime;
		$display.=$this->sprite("area",10,6,50,50);
		$this->view->output=$display;
	}
	function css_compress ($buffer)
	{
		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer); // remove comments
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  '), '',
				$buffer); // remove tabs, spaces, newlines, etc.
		$buffer = str_replace('{ ', '{', $buffer); // remove unnecessary spaces.
		$buffer = str_replace(' }', '}', $buffer);
		$buffer = str_replace('; ', ';', $buffer);
		$buffer = str_replace(', ', ',', $buffer);
		$buffer = str_replace(' {', '{', $buffer);
		$buffer = str_replace('} ', '}', $buffer);
		$buffer = str_replace(': ', ':', $buffer);
		$buffer = str_replace(' ,', ',', $buffer);
		$buffer = str_replace(' ;', ';', $buffer);
		return $buffer;
	}
	 
	function dump_css_cache ($filename,$key=array())
	{
		$cwd = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'cache'.DIRECTORY_SEPARATOR;
		$stat = stat($filename);
		$current_cache = $cwd . '.' . $filename . '.' . $stat['size'] . '-' .
				$stat['mtime'] . '.cache';
		// the cache exists - just dump it
		if (is_file($current_cache)) {
			$cache_contents = file_get_contents($current_cache);
		} else {
			// remove any old, lingering caches for this file
			$dead_files = glob($cwd . '.' . $filename . '.*.cache',
					GLOB_NOESCAPE);
			if ($dead_files)
				foreach ($dead_files as $dead_file)
				@unlink($dead_file);
			if (! function_exists('file_put_contents')) {
				function file_put_contents ($filename, $contents)
				{
					$handle = fopen($filename, 'w');
					fwrite($handle, $contents);
					fclose($handle);
				}
			}
			$cache_contents = $this->css_compress(file_get_contents($filename));
			@file_put_contents($current_cache, $cache_contents);
		}
		foreach ($key as $k => $v) {
			$cache_contents=str_replace($k, $v, $cache_contents);
		}
		return array('text' => $cache_contents, 'mtime' => $stat['mtime']);
	}
	function sprite($name,$rows,$cols,$tile_h,$tile_w) {
		$output="";
		for ($i = 0; $i < ($rows*$cols); $i++) {
			$x=($i%$cols)*$tile_w;
			$y=intval($i/$cols)*$tile_h;
			$output.=".$name-$i {background-position: ".-$x."px ".-$y."px;}";
		}
		return $output;
	}
}

