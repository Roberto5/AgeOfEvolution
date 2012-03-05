<?php
class S1_MarketController extends Zend_Controller_Action
{
    private $token = null;
    /**
     * @var Zend_Db_Adapter_Abstract
     *
     */
    private $db = null;
    /**
     * @var Model_civilta
     *
     */
    private $civ = null;
    public function init ()
    {
        $this->token = token_ctrl($this->getRequest()->getParams());
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $this->civ = Model_civilta::getInstance();
        $this->t = Zend_Registry::get("translate");
    }
    /*public function indexAction()
    {
    
    }*/
    public function sendAction ()
    {
        if ($this->token['tokenM']) {
            $this->civ->aggResource();
            $form = new Form_Marketsend();
            if ($form->isValid($_POST)) {
                $data = $form->getValues();
                $resource = array($data['res1'], $data['res2'], $data['res3']);
                $idv = $data['village'];
                $n = 1;
                $time = 0;
                $now = $this->civ->getCurrentVillage();
                //tempo di attraversata
                $village = $this->civ->village_list[$idv];
                $dist = getDistance($village, 
                array('x' => $this->civ->village->data[$now]['x'], 
                'y' => $this->civ->village->data[$now]['y']));
                $time = getTime($dist, mercants::$speed[$this->civ->getAge()]);
                //mercanti presenti
                $disp = $this->civ->village->building[$now]->getLiv(
                $this->civ->village->building[$now]->getBildForType(MARKET)) -
                 $this->civ->getMercantBusy();
                if ($disp > 0) {
                    //controllo capacità mercanti
                    $cap = $disp *
                     mercants::$capacity[$this->civ->getAge()];
                    $tot = $resource[0] + $resource[1] + $resource[2];
                    if ($tot != 0) {
                        if ($cap >= $tot) {
                            $num = $tot /
                             mercants::$capacity[$this->civ->getAge()];
                            $n = intval($num);
                            if ($n < $num)
                                $n ++;
                            $this->civ->sendMercants($idv, 
                            $this->civ->getCurrentVillage(), $resource, $n, 
                            $time);
                            $this->civ->aggResource($resource);
                            $this->_helper->redirector("show", "building", null, 
                            array('t' => MARKET));
                        } else
                            $this->view->error = $this->t->_(
                            'mercanti insufficenti');
                    } else
                        $this->view->error = $this->t->_('inserire le risorse');
                } else
                    $this->view->error = $this->t->_('mercanti insufficenti');
            } else
                $this->view->error = $form->getMessages();
        } else
            $this->_helper->redirector("show", "building", null, 
            array('t' => MARKET));
    }
    public function sellAction ()
    {
        if ($this->token['tokenM']) {
            //init variabili
            $this->civ->aggResource();
            $form = new Form_Marketsell();
            if ($form->isValid($_POST)) {
                $data = $form->getValues();
                $type = $data['type'];
                $res = $data['res'];
                $rap = $data['rap'];
                $now = $this->civ->getCurrentVillage();
                //mercanti presenti
                $disp = $this->civ->village->building[$now]->getLiv(
                $this->civ->village->building[$now]->getBildForType(MARKET)) -
                 $this->civ->getMercantBusy();
                if ($disp > 0) {
                    //controllo capacità
                    $cap = $disp *
                     mercants::$capacity[$this->civ->getAge()];
                    if ($cap >= $res) {
                        $num = $res / mercants::$capacity[$this->civ->getAge()];
                        $n = intval($num);
                        if ($n < $num)
                            $n ++;
                             //tempo di attraversata
                        $dist = getDistance(
                        array('x' => 0, 'y' => 0), 
                        array('x' => $this->civ->village->data[$now]['x'], 
                        'y' => $this->civ->village->data[$now]['y']));
                        $time = getTime($dist, 
                        mercants::$speed[$this->civ->getAge()]);
                        $resource = array(0, 0, 0);
                        $resource[$type] = $res;
                        $p = Zend_Registry::get("param");
                        $id_market = $p->get("id_market", 1);
                        $this->civ->sendMercants($id_market, $now, $resource, 
                        $n, $time, $rap);
                        $this->civ->aggResource($resource);
                        $this->_helper->redirector("show", "building", null, 
                        array('t' => MARKET));
                    } else
                        $this->view->error = $this->t->_(
                        "capacità insufficente");
                } else
                    $this->view->error = $this->t->_("mercanti insufficenti");
            } else
                $this->view->error = $form->getMessages();
        } else
            $this->_helper->redirector("show", "building", null, 
            array('t' => MARKET));
    }
    public function buyAction ()
    {
        if ($this->token['tokenM']) {
            $id = $this->getRequest()->getParam("id");
            if (is_numeric($id)) {
                //cotrollo esistenza offerta
                $offer = $this->db->fetchRow(
                "SELECT * FROM `" . OFFER_TABLE . "` WHERE `id`='" . $id . "'");
                if ($offer) {
                    $now = $this->civ->getCurrentVillage();
                    //controllo moneta disponibile
                    $tot = $offer['resource'] *
                     $offer['rapport'];
                    if ($this->civ->village->data[$now]['resource_1'] >= $tot) {
                        $dist = getDistance(array('x' => 0, 'y' => 0), 
                        array('x' => $this->civ->village->data[$now]['x'], 
                        'y' => $this->civ->village->data[$now]['y']));
                        $time = getTime($dist, 
                        mercants::$speed[$this->civ->getAge()]);
                        $resource = array(0, 0, 0);
                        $resource[$offer['type']] = $offer['resource'];
                        $resource2 = array($tot, 0, 0);
                        $p = Zend_Registry::get("param");
                        $id_market = $p->get("id_market", 1);
                        $this->civ->sendMercants($now, $id_market, $resource, 0, 
                        $time);
                        $this->civ->sendMercants($offer['vid'], $id_market, 
                        array(intval($offer['resource'] * $offer['rapport'])), 0, 
                        0);
                        $this->civ->aggResource($resource2);
                        $this->db->delete(OFFER_TABLE, "`id`='" . $id . "'");
                        $this->_helper->redirector("show", "building", null, 
                        array('t' => MARKET));
                    } else
                        $this->view->error = $this->t->_("risorse insufficenti");
                } else
                    $this->view->error = $this->t->_("offerta non disponibile");
            } else
                $this->_helper->redirector("show", "building", null, 
                array('t' => MARKET));
        } else
            $this->_helper->redirector("show", "building", null, 
            array('t' => MARKET));
    }
    public function blackAction ()
    {
        // action body
    }
    public function timeAction ()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        $now = $this->civ->getCurrentVillage();
        $vid = intval($this->getRequest()->getParam("vid"));
        $dist = getDistance($this->civ->village_list[$now], 
        $this->civ->village_list[$vid]);
        $time = getTime($dist, mercants::$speed[$this->civ->getAge()]);
        $reply = array(
        'update' => array('ids' => array('time' => timeStampToString($time))));
        $this->view->reply = $reply;
    }
}











