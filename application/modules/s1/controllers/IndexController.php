<?php
/**
 * IndexController
 * 
 * @author
 * @version 
 */
require_once 'Zend/Controller/Action.php';
class S1_IndexController extends Zend_Controller_Action
{
    /**
     * modello civiltà
     * @var Model_civilta
     */
    private $civ;
    /**
     * 
     * @var Zend_Db_Adapter_Abstract
     */
    private $db;
    public $now;
    function init ()
    {
        
        if (Zend_Registry::isRegistered("civ"))
            $civ = Zend_Registry::get("civ");
        else {
            $this->_log->log("civ non trovato", Zend_Log::CRIT);
            $e = new Zend_Exception();
            exit();
        }
        $this->civ = $civ;
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $this->t = Zend_Registry::get("translate");
        $this->now = $this->civ->getCurrentVillage();
    }
    public function indexAction ()
    {
    	$vid=$this->_getParam("vid",false);
    	$size=array(array('x'=>24,'y'=>18,'dim'=>50,'n'=>0),array('x'=>48,'y'=>36,'dim'=>25,'n'=>1),array('x'=>80,'y'=>60,'dim'=>15,'n'=>2));
    	$zoom=$size[$this->_getParam("zoom",0)];//@TODO selettore zoom
    	$this->view->zoom=$zoom;
        if ($this->civ->status) {
            $this->view->hasCivilty = $this->civ->status;
            $this->view->civ = $this->civ;
            $map=new Model_map();
    		if ($vid) {
    			$coord=Model_map::getInstance()->getCoordFromId($vid);
    			$this->view->x=$coord['x'];
    			$this->view->y=$coord['y'];
    			$this->view->vid=true;
    		}
    		else {
    			$now = Model_map::getInstance()->getCoordFromId($this->civ->getCurrentVillage());
    			$this->view->x=$now['x'];
    			$this->view->y=$now['y'];
    		}
    		//$this->view->village=$map->getVillageArray($this->view->x,$this->view->y,intval($zoom['x']/2),intval($zoom['y']/2),$zoom['x']);
    		//$this->view->table= $map->getMapTable($zoom['dim'],$zoom['y'],$zoom['x']);
        } else {
            $this->view->hasCivilty = false;
            $form = new Form_Regciv();
            $this->view->form = $form;
        }
    }
    public function villageAction ()
    {
        $this->_helper->layout()->x = 1000;
        $this->_helper->layout()->y = 740;
        $this->view->own = true;
        $this->view->civ = $this->civ;
        $this->view->disp = $this->civ->village->building[$this->now]->getDispBuilding($this->civ->getAge());
    }
    public function searchcivAction ()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        $name = addslashes($_POST['name']);
        $page = $_POST['page'];
        $start = (int) $_POST['start'];
        $row = $this->db->fetchAll(
        "SELECT * FROM `" . CIV_TABLE . "` WHERE `civ_name`LIKE '" . $name .
         "' AND `civ_id`!='0'");
        $paginator = Zend_Paginator::factory($row);
        //$paginator->setItemCountPerPage(1);
        $this->view->func = "ev.SearchCiv";
        $paginator->setCurrentPageNumber($page);
        $this->view->paginator = $paginator;
    }
    public function subscriveAction ()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        $id = (int) $_POST['id'];
        $civ = Zend_Registry::get("civ");
        $auth = Zend_Auth::getInstance();
        $r = $civ->subscrive($id, $auth->getIdentity()->user_id, false);
        $mess = $r ? "iscrizione inviata con successo, attendi l'attivazione da parte della civiltà" : "iscrizione non riuscita";
        $reply = array('data' => false, 'html' => false, 'javascript' => false, 
        'update' => array('ids' => array('mess' => $mess)));
        echo json_encode($reply);
    }
    public function createcivAction ()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        $req = $this->getRequest()->getParams();
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        $name = $req['name'];
        $agg = $req['agg'];
        $alnum = new Zend_Validate_Alnum();
        if (($alnum->isValid($name)) && ($alnum->isValid($agg))) {
            $sector = (int) $_POST['sector'];
            $x[0] = 0;
            $x[1] = 2;
            $x[2] = 1;
            $x[3] = 1;
            $x[4] = 2;
            $y[0] = 0;
            $y[1] = 2;
            $y[2] = 2;
            $y[3] = 1;
            $y[4] = 1;
            $n = $this->db->fetchOne(
            "SELECT count(*) 
        	FROM `" . CIV_TABLE . "` 
        	WHERE `civ_name`='" . $name . "'");
            if ($n) {
                $risposta['bool'] = false;
                $risposta['mess'] = $name . " " .
                 $this->t->_("esiste gi&aacute;!");
            } else {
                Model_civilta::register(
                array('civ_name' => $name, 'civ_adjective' => $agg));
                $id = $this->db->fetchOne(
                "SELECT `civ_id` 
            	FROM `" . CIV_TABLE . "` 
            	WHERE `civ_name`='" . $name . "'");
                $auth = Zend_Auth::getInstance();
                $param = new Model_params();
                Zend_Registry::set("param", $param);
                Model_civilta::subscrive($id, $auth->getIdentity()->user_id);
                $coord = Model_civilta::randomcoord($x[$sector], $y[$sector]);
                Model_civilta::addVillage($coord['x'], $coord['y'], $id, 1);
                $risposta = $this->t->_(
                "civilt&aacute; iscritta con successo! ricaricare la pagina");
            }
        } else {
            $risposta = $name . " " . $agg . " " . $this->t->_(
            "il nome e l'aggettivo devono contenere solo lettere o numeri");
        }
        echo json_encode(
        array(
        'html' => array('title' => $this->t->_('Attenzione'), 'text' => $risposta), 
        'data' => false, 'update' => false, 'javascript' => false));
    }
    public function delqueueAction ()
    {
        $token = token_ctrl($this->_getAllParams());
        if ($token['tokenB']) {
            global $building_array;
            $id = (int) $_POST['id'];
            $this->_log->debug(
            "`id`='$id' AND `type`IN('" . DESTROY_EVENT . "','" . BILD_EVENT .
             "')");
            $this->civ->ev->delete(
            "`id`='$id' AND `type`IN('" . DESTROY_EVENT . "','" . BILD_EVENT .
             "')");
            $now = $this->civ->getCurrentVillage();
            $where = array("`type`='1'", 
            "`params`LIKE'%\"village_id\";i:" . $now . "%'");
            $queue = $this->civ->ev->getEvent($where);
            $where = array("`type`='" . DESTROY_EVENT . "'", 
            "`params`LIKE'%\"village_id\";i:" . $now . "%'");
            $destroy = $this->civ->ev->getEvent($where);
            $this->civ->refresh->addIds("queue", 
            $this->view->template()
                ->queue($queue));
            $this->civ->refresh->addIds("destroy", 
            $this->view->template()
                ->queue($destroy));
        }
        $this->civ->refresh->addToken("tokenB", token_set("tokenB"));
    }
    /**
     * questa action serve solo per aggiornare la pagina via ajax.
     * non fa niente.
     */
    public function refreshAction ()
    {
    }
    public function sortAction ()
    {
        $user = Zend_Registry::get("user");
        // ricevo l'array tramite post
        $list = $_POST['list'];
        if (is_array($list)) { //se è un array
            $user->option->set("order", 4); //imposto l'ordine dei villaggi in modalità personalizzata
            foreach ($list as $key => $value) { //scorro l'array con foreach
                $value = substr($value, 1); //$value="v1234" quindi tolgo la v per poter cercare l'id
                //modifico il db inserendo nella tabella del villaggio la posizione nel vettore ricevuto
                $this->_db->update(MAP_TABLE, 
                array('order_n' => $key), "`id`='$value'");
            }
        }
    }
}
