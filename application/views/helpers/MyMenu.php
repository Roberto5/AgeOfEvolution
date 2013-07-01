<?php
/**
 *
 * @author pagliaccio
 * @version 
 */
require_once 'Zend/View/Interface.php';
/**
 * MyMenu helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_MyMenu extends Zend_View_Helper_Abstract
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * 
     * @var Array
     */
    public $page=array();
    /**
     * 
     * @var Zend_Acl
     */
    private $acl;
    /**
     * @param Array $config
     * @return Zend_View_Helper_MyMenu 
     */
    public function myMenu($config=false)
    {
    	if (is_array($config)) {
    		if ($config['page']) {
    			$this->page=$config['page'];
    		}
    		if ($config['acl']) {
    			$this->acl=$config['acl'];
    		}
    		else {
    			$this->acl=Zend_Registry::get("acl");
    		}
    		if (!$config['page'] && !$config['acl']) {
    			$this->page=$config;
    		}
    	}
    	else $this->acl=Zend_Registry::get("acl");
    	return $this;
    }
    /**
     * 
     * @param string|array $label
     * @param string $module
     * @param string $controller
     * @param string $action
     * @param int $order
     * @param string $resource
     * @param string $privilege
     * @return Zend_View_Helper_MyMenu
     */
    public function add($label,$module=NULL,$controller=NULL,$action=NULL,$order=NULL,$resource=NULL,$privilege=NULL,$icon=null,$iconSize=null,$text=null) {
    	if (is_array($label)) $this->page[]=$label;
    	else {
    		$nav=array('label'=>$label
				,'order'=>$order
				,'resource'=>$resource
				,'privilege'=>$privilege
    			,'icon'=>$icon
    			,'iconSize'=>$iconSize
    			,'text'=>$text
    			,'attribs'=>array('rel'=>'link-'.str_replace(array('[',']'), "", $label))
			);
    		if (!$controller && !$action) {
    			if (strstr($module, 'javascript')) {
    				$nav['uri']='#'.$label;
    				$nav['attribs']['onclick']=$module;
    			}
    			else $nav['uri']=$module;
    		}
    		else {
    			$nav['module']=$module;
    			$nav['controller']=$controller;
    			$nav['action']=$action;
    		}
    		$this->page[]=$nav;
    	}
    		
    	return $this;
    }
    /**
     * 
     * @return string
     */
    public function render() {    	
    	try
    	{
    		$menu=new Zend_Navigation($this->page);
    		$this->view->navigation($menu)
    			->setAcl($this->acl)
    			->setRole(Model_Role::getRole());
    			//->menu()->setMaxDepth(0);
    	}
    	catch (Exception $e)
    	{
    		print_r($e);
    		trigger_error($e->getMessage());
    	}
    	
    	return $this->view->navigation()->render();
    }
    /**
     * @return string
     */
    public function __toString() {
    	try
    	{
    		$str = $this->render();
    	}
    	catch (Exception $e)
    	{
    		trigger_error($e->getMessage());
    	}
    	return $str;
    }
    /**
     * return the number of pages
     * @return int
     */
    public function getPageCount() {
    	return count($this->page);
    }
    /**
     * Sets the view field 
     * @param $view Zend_View_Interface
     */
    public function setView (Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}
