<?php
class JsController extends Zend_Controller_Action
{
	public function init () {
	}
	public function indexAction ()
	{
		$this->_helper->layout->disableLayout();
		//$this->_helper->viewRenderer->setNoRender(true);
		/*if(!empty($_SERVER["HTTP_ACCEPT_ENCODING"]) && strpos("gzip",$_SERVER["HTTP_ACCEPT_ENCODING"]) === NULL){
		}else{ob_start("ob_gzhandler");
		}*/
		
		header('Content-Type: text/javascript; charset: UTF-8');
		header('Cache-Control: must-revalidate');
		
		$expire_offset = 1814400; // set to a reaonable interval, say 3600 (1 hr)
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expire_offset) . ' GMT');
		
		
		$file=array("*framework*",
				"jquery.js"
				,"jquery-ui.js"
				,"jquery.validate.min.js"
				,"jquery.contextmenu.js"
				,"jquery.tools.min.js"
				,"jquery.cookie.js"
				,"lightbox.js"
				,"jquery.li-scroller.1.0.js"
				,"jquery.edit.js"
				,'*main script*'
				,'evolution.js'
				,"function.js"
				,"reg.js"
				,"time.js"
				,"profile.js");
		
		$text="";
		$mtime=0;
		foreach ($file as $value) {
			if (preg_match("/\*(.+)\*/", $value)) {
				$text.="\n/************$value************/\n";
			}
			else {
				$text.=file_get_contents(APPLICATION_PATH.'/../common/js/'.$value);
				$stat=stat(APPLICATION_PATH.'/../common/js/'.$value);
				if ($mtime<$stat['mtime']) $mtime=$stat['mtime'];
			}
		
		}
		include_once 'includes/jsmin.php';
		$js=new jsmin($file,APPLICATION_PATH.'/../common/js/');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
		$this->view->text=$text;
	}	
}

