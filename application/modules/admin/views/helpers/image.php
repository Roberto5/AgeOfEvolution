<?php
/**
 *
 * @author pagliaccio
 * @version 
 */
require_once 'Zend/View/Interface.php';
/**
 * image helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_image
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    public $baseUrl;
    
    function __construct() {
    	$baseurl = new Zend_View_Helper_BaseUrl();
        $this->baseUrl = $baseurl->baseUrl();
    }
    /**
     * 
     */
    public function image ($src, $alt, $title = "", $width = 16, $heigth = 16)
    {
        return '<img src="' . $src . '" alt="[' . $alt . ']" title="' . $title .
         '" width="' . $width . '" height="' . $heigth . '" />';
    }
    /**
     * restituisce l'html dell'imagine di una risorsa
     * @param unknown_type $n
     * @param unknown_type $age
     */
    public function resource ($n, $age)
    {
        $t = Zend_Registry::get("translate");
        if ($n < 3) {
            $url = Model_civilta::getCivAge($age + 1) . "/" . $n . ".gif";
            $nameR = Model_civilta::$nameResource;
            $title = $alt = $nameR[$age][$n];
        } else {
            $url = "pop.gif";
            $title = $alt = $t->_("popolazione");
        }
       
        $src = $this->baseUrl . "/common/images/" . $url;
        $width = "16";
        $heigth = "16";
        return $this->image($src, $alt, $title, $width, $heigth);
    }
    public function troop ($id)
    {
        global $NameTroops;
        
        $title = $alt = $NameTroops[$id];
        $alt='['.$alt.']';
        $src = $this->baseUrl . "/common/images/troops/t" . $id .
         ".gif";
        return $this->image($src, $alt,$title);
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
