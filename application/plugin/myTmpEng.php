<?php
require_once ('Zend/Controller/Plugin/Abstract.php');
class plugin_myTmpEng extends Zend_Controller_Plugin_Abstract
{
	/**
	 * 
	 * @var Zend_Controller_Action_Helper_ViewRenderer
	 */
	private $viewrenderer;
	/**
	 * 
	 * @param Zend_Controller_Action_Helper_ViewRenderer $viewrender
	 */
    function __construct ($viewrender)
    {
    	$this->viewrenderer=$viewrender;
    }
   
	public function preDispatch($request) {
		$this->viewrenderer->view->key=array();
	}
	public function postDispatch($request) {
		Zend_View_Filter_Tmpeng::addkey($this->viewrenderer->view->key);
	}
}
?>