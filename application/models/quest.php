<?php
/**
 * quest
 * 
 * @author pagliaccio
 * @version 
 */
require_once 'Zend/Db/Table/Abstract.php';
class Model_quest extends Zend_Db_Table_Abstract
{
    /**
     * The default table name 
     */
    protected $_name = QUEST_TABLE;
    static $maxquest = array(7, 1, 0, 0, 0, 0);
    var $parser;
    var $n = 0;
    var $age = 0;
    var $save_quest = false;
    var $save_quest2 = false;
    var $CONDICTION = array();
    var $MASTER = null;
    var $TITLE = null;
    var $TEXT = null;
    var $BACK = null;
    var $REWARD = null;
    var $tag;
    var $c = 0;
    var $chache = false;
    var $read = true;
    var $bool = false;
    var $completed = false;
    /**
     * @var Model_civilta
     */
    var $civ;
    /**
     * @var Zend_Log
     */
    var $log;
    var $state;
    /**
     * effettua costruisce le quest
     */
    function __construct ($civ=null)
    {
    	if (!$civ) $civ=Model_civilta::getInstance();
        $this->log = Zend_Registry::get("log");
        $this->civ = $civ;
        if ($civ) {
            $this->t = Zend_Registry::get("translate");
            $this->age = $civ->getAge();
            $this->n = $civ->data['quest'];
            $this->MASTER = $this->civ->data['master'];
            $this->parser = xml_parser_create();
            $quest = $this->getDefaultAdapter()->fetchRow(
            "SELECT * FROM `" . QUEST_TABLE . "` WHERE `age`='" . $this->age .
             "' AND `n`='" . $this->n . "'");
            if ($quest) {
                $this->chache = true;
                $this->MASTER = $quest['master'];
                $this->TITLE = $quest['title'];
                $this->TEXT = $quest['text'];
                $this->BACK = $quest['back'];
                $this->CONDICTION = unserialize($quest['condiction']);
                $this->REWARD = unserialize($quest['reward']);
                $this->read = ($civ->data['read_quest'] ? true : false);
                $this->state = $civ->data['state'] ? true : false;
            }
            xml_set_object($this->parser, $this);
            xml_set_element_handler($this->parser, "tag_open", "tag_close");
            xml_set_character_data_handler($this->parser, "cdata");
        }
    }
    /**
     * effettua il parsing delle quest
     * @param Object $data
     */
    function parse ()
    {
        $data = file_get_contents(APPLICATION_PATH . "/quest/quest.xml");
        xml_parse($this->parser, $data);
    }
    /**
     *
     * @param Resource $parser
     * @param String $tag
     * @param mixed $attributes
     */
    function tag_open ($parser, $tag, $attributes)
    {
        $this->tag = $tag;
        if (($attributes['ID'] == $this->age) && ($tag == "AGE"))
            $this->save_quest2 = true;
        if (($attributes['N'] == $this->n) && ($tag == "QUEST") &&
         ($attributes['MASTER'] == $this->MASTER))
            $this->save_quest = true;
        elseif ($this->save_quest && $this->save_quest2) {
            if ($tag == "CONDICTION")
                $this->CONDICTION = $attributes;
            if ($tag == "REWARD")
                $this->REWARD = $attributes;
        }
    }
    /**
     *
     * @param Resource $parser
     * @param mixed $cdata
     */
    function cdata ($parser, $cdata)
    {
        $tag = $this->tag;
        if ($this->save_quest && $this->save_quest2) {
            if ($this->tag == "CONDICTION") {
                if ($this->CONDICTION['PARAM'] == null)
                    $this->CONDICTION['PARAM'] = $cdata;
            } elseif ($this->tag == "REWARD") {
                if ($this->REWARD['data'] == null)
                    $this->REWARD['data'] = $cdata;
            } elseif (($this->tag != "AGE") && ($this->tag != "QUEST") &&
             ($tag != "ROOT") && ($tag != "REWARD")) {
                if ($this->$tag == null) {
                    $this->$tag = $cdata;
                }
            }
        }
    }
    /**
     *
     * @param Resourse $parser
     * @param String $tag
     */
    function tag_close ($parser, $tag)
    {
        if ($tag == "QUEST")
            $this->save_quest = false;
        if ($tag == "AGE")
            $this->save_quest2 = false;
    }
    /**
     * visualizza le quest e il questmaster
     */
    function ShowQuest ($load = false, $n = false, $state = false)
    {
    	include_once APPLICATION_PATH.'/views/helpers/Image.php';
    	$img=new Zend_View_Helper_image();
        if (! $this->civ)
            return false;
        if ($this->n > Model_quest::$maxquest[$this->age]) {
            $this->TITLE = "Le quest sono finite...";
            $this->TEXT = "...per ora...";
            $quest = array('title' => $this->TITLE, 'text' => $this->TEXT, 
            'mod' => true, 'close' => 'ev.quest.read');
            $data=array('n'=>$this->civ->data['quest'],'state'=>$this->civ->data['state']);
        } else {
            if (! $this->chache) {
                $this->parse();
                $this->TEXT = str_replace("\n", "", $this->TEXT);
                $this->BACK = str_replace("\n", "", $this->BACK);
                $this->TEXT = str_replace("\r", "", $this->TEXT);
                $this->BACK = str_replace("\r", "", $this->BACK);
                $this->getDefaultAdapter()->query(
                "INSERT INTO `" . QUEST_TABLE . "` SET `age`='" . $this->age . "' ,
                `n`='" . $this->n . "' , `title`='" .
                 ($this->TITLE) . "' , `text`='" .
                 ($this->TEXT) . "' ,
                `master`='" . $this->MASTER . "' , `back`='" .
                 ($this->BACK) . "' ,
                `condiction`='" . serialize($this->CONDICTION) .
                 "' , `reward`='" . serialize($this->REWARD) . "'");
            }
            $data = true;
            $this->TEXT =$img->parse($this->TEXT);
            $this->BACK = $img->parse($this->BACK);
            //$this->TEXT=str_replace('"', '\\"', $this->TEXT);
            //$this->BACK=str_replace('"', '\\"', $this->BACK);
            $this->TITLE = ($this->TITLE);
            $ctrl=$this->control();
            /**
             * descrizione ingranaggio
             * la ruota ha n denti grandi che sono il numero di quest
             * tra una quest e l'altra c'e un dente intermedio che è definito 
             * dallo state=1
             */
            if ($ctrl && ($this->read) && ($this->state == 0)) {
                //obbiettivo completato, ha letto la quest e lo stato è a zero
                // l'ingranaggio fa un passo avanti in step state=1 read=0 
                $this->getreward();
                $quest = array('title' => $this->t->_('Obbiettivo Completato'), 
                'text' => $this->BACK, 'mod' => true, 'close' => 'ev.quest.read');
                $this->getDefaultAdapter()->query(
                "UPDATE `" . CIV_TABLE .
                 "` SET `read_quest`='0' , `state`='1' where `civ_id`='" .
                 $this->civ->cid . "'");
                $this->state = 1;
                $this->civ->data['read_quest'] = false;
                $this->read = false;
                $data=array('n'=>$n,'state'=>1);
            }
            elseif (!$load && ($n == $this->n) && ($state == $this->state)) {
                $quest = false;
                $data = false;
            } else {
                $quest = array('title' => $this->TITLE, 'text' => $this->TEXT, 
                'mod' => true, 'close' => 'ev.quest.read');
                $data=array('n'=>$this->civ->data['quest'],'state'=>$this->civ->data['state']);
            }
        }
        //@todo implementare update
        return array('html' => $quest, 'update' => false, 
        'data' => $data, 'javascript' => false);
    }
    /**
     * controlla se i requisiti sono stati completati
     * return bool
     */
    function control ()
    {
        global $Building_Array;
        $b = $this->CONDICTION['VALUE'];
        if (in_array($this->CONDICTION['PARAM'], $Building_Array)) {
            for ($i = 0; ($Building_Array[$i]) &&
             ($Building_Array[$i] != $this->CONDICTION['PARAM']); $i ++);
            $pos = $this->civ->village->building[$this->civ->getCurrentVillage()]->getBildForType(
            $i + 1);
            $a = ($pos < 0 ? false : $this->civ->village->building[$this->civ->getCurrentVillage()]->getLiv(
            $pos));
        } else {
            switch ($this->CONDICTION['PARAM']) {
                case "village":
                    $a = $this->civ->village->data[$this->civ->getCurrentVillage()]['name'];
                    break;
                case "prod":
                    if (substr($this->CONDICTION['TYPE'], 0, 1) == "L") {
                        $pos = $this->civ->village->building[$this->civ->getCurrentVillage()]->getBildForType(
                        PROD1);
                        $a = ($pos < 0 ? 0 : $this->civ->building[$this->civ->getCurrentVillage()]->getLiv(
                        $pos));
                        $pos = $this->civ->village->building[$this->civ->getCurrentVillage()]->getBildForType(
                        PROD2);
                        $t = ($pos < 0 ? 0 : $this->civ->building[$this->civ->getCurrentVillage()]->getLiv(
                        $pos));
                        if ($a < $t)
                            $a = $t;
                        $pos = $this->civ->village->building[$this->civ->getCurrentVillage()]->getBildForType(
                        PROD3);
                        $t = ($pos < 0 ? 0 : $this->civ->village->building[$this->civ->getCurrentVillage()]->getLiv(
                        $pos));
                        if ($a < $t)
                            $a = $t;
                    } else {
                        $pos = $this->civ->village->building[$this->civ->getCurrentVillage()]->getBildForType(
                        PROD1);
                        $a = ($pos < 0 ? 0 : $this->civ->village->building[$this->civ->getCurrentVillage()]->getLiv(
                        $pos));
                        $pos = $this->civ->village->building[$this->civ->getCurrentVillage()]->getBildForType(
                        PROD2);
                        $t = ($pos < 0 ? 0 : $this->civ->village->building[$this->civ->getCurrentVillage()]->getLiv(
                        $pos));
                        if ($a > $t)
                            $a = $t;
                        $pos = $this->civ->village->building[$this->civ->getCurrentVillage()]->getBildForType(
                        PROD3);
                        $t = ($pos < 0 ? 0 : $this->civ->village->building[$this->civ->getCurrentVillage()]->getLiv(
                        $pos));
                        if ($a > $t)
                            $a = $t;
                    }
                    break;
                case "storage":
                    if (substr($this->CONDICTION['TYPE'], 0, 1) == "L") {
                        $pos = $this->civ->village->building[$this->civ->getCurrentVillage()]->getBildForType(
                        STORAGE1);
                        $a = ($pos < 0 ? 0 : $this->civ->village->building[$this->civ->getCurrentVillage()]->getLiv(
                        $pos));
                        $pos = $this->civ->village->building[$this->civ->getCurrentVillage()]->getBildForType(
                        STORAGE2);
                        $t = ($pos < 0 ? 0 : $this->civ->village->building[$this->civ->getCurrentVillage()]->getLiv(
                        $pos));
                        if ($a < $t)
                            $a = $t;
                    } else {
                        $pos = $this->civ->village->building[$this->civ->getCurrentVillage()]->getBildForType(
                        STORAGE1);
                        $a = ($pos < 0 ? 0 : $this->civ->village->building[$this->civ->getCurrentVillage()]->getLiv(
                        $pos));
                        $pos = $this->civ->village->building[$this->civ->getCurrentVillage()]->getBildForType(
                        STORAGE2);
                        $t = ($pos < 0 ? 0 : $this->civ->village->building[$this->civ->getCurrentVillage()]->getLiv(
                        $pos));
                        if ($a > $t)
                            $a = $t;
                    }
                    break;
                case "attack":
                    $a = $this->civ->option->get("attack", false);
                    break;
                case "fromLastEv":
                    $a = mktime() - $this->civ->option->lastEv;
                    break;
                case "haveGod":
                    if ($this->civ->option->get("have_god") == "1") {
                        $a = true;
                    } else
                        $a = false;
                    break;
                default:
                    $this->log->log(
                    "errore condizione parametro sconosciuta!" . print_r(
                    $this->CONDICTION,true), Zend_Log::WARN);
                    $a = false;
                    $b = false;
            }
        }
        switch ($this->CONDICTION['TYPE']) {
            case "NE":
                return ($a != $b);
                break;
            case "EX":
                return $a;
                break;
            case "GE":
                return ($a >= $b);
                break;
            case "LE":
                return ($a <= $b);
                break;
            case "G":
                return ($a > $b);
                break;
            case "L":
                return ($a < $b);
                break;
            case "EQ":
                return ($a == $b);
                break;
            default:
                $this->log->log(
                "errore condizione sconosciuta!" . print_r($this->CONDICTION,true), 
                Zend_Log::WARN);
                return false;
                break;
        }
    }
    /**
     * applica la riconpensa
     */
    function getreward ()
    {
        switch ($this->REWARD['TYPE']) {
            case "RES":
                $this->civ->aggResource(explode(",", $this->REWARD['data']), 
                true);
                break;
            case "TR":
                $tr = explode(",", $this->REWARD['data']);
                $this->getDefaultAdapter()->query(
                "INSERT INTO `" . TROOPERS . "` SET `trooper_id`='" . $tr[0] . "' 
                , `numbers`='" . $tr[1] . "' , `civ_id`='" . $this->civ->cid . "' 
                , `village_now`='" . $this->civ->getCurrentVillage() . "' 
                , `village_prev`='" . $this->civ->getCurrentVillage() . "'");
                break;
            case "EV":
                $this->getDefaultAdapter()->query(
                "UPDATE `" . CIV_TABLE . "` SET `ev_ready`='1' WHERE `civ_id`='" .
                 $this->civ->cid . "'");
                break;
            default:
                $this->log->log(
                "errore ricompensa sconosciuta! \n" . print_r($this->REWARD,true), 
                Zend_Log::WARN);
        }
    }
}
/* $dom = new DOMDocument;
  $dom->Load('../xml/quest.xml');
  if ($dom->validate()) {
  echo "<h1>This document is valid! </h1>";
  }
  else echo "<h1>no!</h1>";
  $q = new Quest(1, 1);
  $q->parse();
  print_r($q);
  $print = htmlentities($data);
  $print = str_replace("\n", "<br>", $print);
  echo "<p>" . $print . "</p>"; */
?>