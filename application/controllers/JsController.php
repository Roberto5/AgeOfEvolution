<?php
class JsController extends Zend_Controller_Action
{
	public function init () {
	}
	public function indexAction ()
	{
		$this->_helper->layout->disableLayout();
		
		$conf = Zend_Registry::get("config");
		// set to a reaonable interval, say 3600 (1 hr)
		$this->view->expire = $conf->js->expire ? $conf->js->expire : 86400;
		$file=$conf->js->file->toArray();		
		$text="";
		$mtime=0;
		$path=APPLICATION_PATH.'/../common/js/';
		foreach ($file as $value) {
			if (preg_match("/\*(.+)\*/", $value)) {
				$text.="\n/************$value************/\n";
			}
			elseif (file_exists($path.$value)) {
				$text.=file_get_contents($path.$value);
				$stat=stat($path.$value);
				if ($mtime<$stat['mtime']) $mtime=$stat['mtime'];
			}
		
		}
		$this->mtime=$mtime;
		$this->view->text=$text;
	}	
}

