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
        $civ = Model_civilta::getInstance();
        $auth = Zend_Auth::getInstance();
        $r = $civ->subscrive($id, $auth->getIdentity()->user_id, false);
        $mess = $r ? "iscrizione inviata con successo, attendi l'attivazione da parte della civiltà" : "iscrizione non riuscita";
        $reply = array('data' => false, 'html' => false, 'javascript' => false, 
        'update' => array('ids' => array('mess' => $mess)));
        echo json_encode($reply);
    }
    public function unsubscriveAction() {
    	Zend_Layout::getMvcInstance()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id=Zend_Auth::getInstance()->getIdentity()->user_id;
        $this->_db->delete(RELATION_USER_CIV_TABLE,"`user_id`='$id'"); 
        echo json_encode(array('data'=>null,'html'=>null,'javascript'=>null,'update'=>null));       
    }
    public function createcivAction ()
    {
    	try {
    	$this->_helper->viewRenderer->setNoRender(true);
        Zend_Layout::getMvcInstance()->disableLayout();
        $req = $this->getRequest()->getParams();
        $form=new Form_Regciv();
        $error=false;
        if ($form->isValid($_POST)) {
        	$data=$form->getValues();
            $sector = $data['sector'];
            $name=$data['name'];
            $agg = $data['agg'];
            $x=(int)$data['cx'];
        	$y=(int)$data['cy'];
            $cx[5] = 0;
            $cx[1] = 2;
            $cx[2] = 1;
            $cx[3] = 1;
            $cx[4] = 2;
            $cy[5] = 0;
            $cy[1] = 2;
            $cy[2] = 2;
            $cy[3] = 1;
            $cy[4] = 1;
            Model_civilta::register(array('civ_name' => $name, 'civ_adjective' => $agg));
            $id = $this->db->fetchOne("SELECT `civ_id` FROM `" . CIV_TABLE . "` WHERE `civ_name`='" . $name . "'");
            $auth = Zend_Auth::getInstance();
            $param = new Model_params();
            Zend_Registry::set("param", $param);
            $bool=$this->db->fetchOne("SELECT `id` FROM `".SERVER."_map` WHERE `id`='".Model_map::getInstance()->getIdFromCoord($x, $y)."'");
            if ($bool && ($sector==6)) 
            	$sector=5;
            switch ($sector) {
            	case 6: 
            		break;
            	default:$coord = Model_civilta::randomcoord($cx[$sector], $cy[$sector]);
            		$x=$coord['x'];$y=$coord['y'];
            		break;
            };
            Model_civilta::addVillage($x, $y, $id, 1);
                $risposta = $this->_t->_('REG_SUCCESS');
            Model_civilta::subscrive($id, $auth->getIdentity()->user_id);
        } else {
        	$error=true;
        	$risposta = "";
        	foreach ($form->getMessages() as $key => $value) {
        		foreach ($value as $mess) {
        			$risposta.='<div>'.$key.':'.$mess.'</div>';
        		}
        	}
        }
        echo json_encode(
        array(
        'html' => array('title' => $this->_t->_('WARNING'), 'text' => $risposta,'x'=>200,'y'=>200,'error'=>$error,'button'=>true), 
        'data' => false, 'update' => false, 'javascript' => 'setTimeout(function() {location.reload()},3000)'));
    	}
    	catch (Exception $e) {
    		echo $e;
    	}
    }
    public function delqueueAction ()
    {
            global $building_array;
            $id = (int) $_POST['id'];
            $this->civ->ev->delete(
            "`id`='$id' AND `type`IN('" . DESTROY_EVENT . "','" . BILD_EVENT .
             "')");
            $new=array();
            foreach ($this->civ->queue as $value) {
            	if ($value['id']!=$id) $new[]=$value;
            }
            $this->civ->queue=$new;
            $new=array();
            foreach ($this->civ->destroy as $value) {
            	if ($value['id']!=$id) $new[]=$value;
            }
            $this->civ->destroy=$new;
            $this->civ->refresh();
            /*$now = $this->civ->getCurrentVillage();
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
                ->queue($destroy));*/
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
        $user = Model_user::getInstance();
        // ricevo l'array tramite post
        $list = $_POST['list'];
        if (is_array($list)) { //se è un array
            $user->option->set("order", 4); //imposto l'ordine dei villaggi in modalità personalizzata
            foreach ($list as $key => $value) { //scorro l'array con foreach
                $value = substr($value, 1); //$value="v1234" quindi tolgo la v per poter cercare l'id
                //modifico il db inserendo nella tabella del villaggio la posizione nel vettore ricevuto
                $this->_db->update(SERVER.'_map', 
                array('order_n' => $key), "`id`='$value'");
            }
        }
    }
}
