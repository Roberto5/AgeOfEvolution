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
class Zend_View_Helper_MyMenu
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * @param Array $nav
     * @return String 
     */
    public function myMenu($nav)
    {
    	$display='<ul>';
    	foreach ($nav as $key => $value) {
        	if ($value['js']) $display.= '<li><a href="#'.($value['id'] ? $value['id'] : $key).'" onclick="' . $value['url'] . '">';
            else $display.= '<li><a href="' . $this->view->baseUrl()."/".$value['url'] . '">';
            if ($value['img'])
                $display.= '<img id="link-'.($value['id'] ? $value['id'] : $key).'" src="' . $this->view->baseUrl() . $value['img'] . '" alt="' .
                 $key . '" title="' . $key . '"  width="16" height="16" />';
            else
                $display.= $key;
            $display.= '</a></li>';
        } 
        return $display.'</ul>';
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
