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
class Zend_View_Helper_image extends Zend_View_Helper_Abstract
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    public $baseUrl;
    public $images=array('Clava'=>'troops/t0.gif','Fionda'=>'troops/t1.gif','Falange'=>'troops/t2.gif',
    'pietra'=>'Preistorica/0.gif','legno'=>'Preistorica/2.gif','cibo'=>'Preistorica/1.gif',
    'Zeus'=>'master1_1.png','Ares'=>'master1_2.png','Atena'=>'master1_3.png','Demetra'=>'master1_4.png','city'=>'city.png');
    /**
     * restituisce il codice html dell'immagine se src e alt sono definiti, altrimenti l'oggetto helper
     * @param String $src url immagine
     * @param String $alt attributo alt
     * @param String $title attributo title
     * @param int $width larghezza
     * @param int $heigth altezza
     * @param array $attr attributi nome=>valore
     * @return Zend_View_Helper_image
     */
    public function image($src=false, $alt=false, $title = "", $width = 16, $heigth = 16,$attr=false)
    {
    	if (!$this->baseUrl) {
    		$config=Zend_Registry::get("config");
        	if ($config->local) $this->baseUrl =$config->path;
        	else {
        		$baseurl = new Zend_View_Helper_BaseUrl();
    			$this->baseUrl = $baseurl->baseUrl();
        	}
    	}
    	$a="";
    	if (is_array($attr)&&$attr) {
    		foreach ($attr as $key => $value) {
    			$a.=$key.'="'.$value.'" ';
    		}
    	}
    	if ($src&&$alt) 
      		return '<img src="'.$this->baseUrl . $src . '" alt="[' . $alt . ']" '.($title? 'title="' . $title .
         '"' : '').' width="' . $width . '" height="' . $heigth . '" '.$a.' />';
      	else return $this;
    }
    /**
     * restituisce l'html dell'imagine di una risorsa
     * @param int $n
     * @param int $age
     * @return String
     */
    public function resource($n, $age)
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
       
        $src =  "/common/images/" . $url;
        $width = "16";
        $heigth = "16";
        return $this->image($src, $alt, $title, $width, $heigth);
    }
    /**
     * 
     * immagine truppa
     * @param int $id
     */
    public function troop($id)
    {
        global $NameTroops;
        
        $title = $alt = $NameTroops[$id];
        $src = "/common/images/troops/t" . $id .
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
    /**
     * sostituisce dei tag [tag] con delle immagini
     * @param String $text testo da sostituire
     * @return String testo sostituito con il codice html 
     */
    public function parse($text) {
    	foreach ($this->images as $key => $value) {
    		$pattern="/\[($key)\|(\d+)\|(\d+)\]|\[($key)\]|\[($key)\|(\d+)\|(\d+)\|([\w_\(\)\.]+)\]/";
    		$bool=preg_match($pattern, $text,$mat);
    		if ($bool) {
    			$h=$mat[3]+$mat[7];if ($h<1) $h=" ";
    			$w=$mat[2]+$mat[6];if ($w<1) $w=" ";
    			$replace=$this->image("/common/images/$value","$key",$key,$w,$h,array('onclick'=>$mat[8]));
    			$text=str_replace($mat[0], $replace, $text);
    		}
    	}
    	return $text;
    }
}
