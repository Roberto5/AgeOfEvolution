<?php
class Admin_MkdataController extends Zend_Controller_Action
{
    private $log;
    public function init ()
    {
        $this->log = Zend_Registry::get("log");
    }
    public function indexAction ()
    {
        $file = new Zend_CodeGenerator_Php_File();
        $file->setFilename("data.php");
        //**************main************
        //rid
        $rid = array(6000, 12000);
        for ($i = 2, $j = 1; $i <= 20; $i ++, $j *= 0.8)
            $rid[$i] = intval($rid[$i - 1] + $rid[$i - 1] * $j);
             //cost
        $cost = array();
        for ($i = 0, $j = 1; $i <= 20; $i ++, $j += 0.1)
            $cost[$i] = array(intval($rid[$i] * $j * 3 / 100), 
            intval($rid[$i] * 3 / 100), intval($rid[$i] * 4 / 100), $i);
            //@todo modificare con popolazione
        $content = "fattore di riduzione: {0}<br/> [0]<br/>";
        $Description = Array(
        "il focolare aumenta la velocit&agrave; di costruzione delle strutture. <blockquote>davanti il focolare si riunisce tutta la trib&ugrave; per decidere il da-farsi</blockquote>");
        $param = Array("rid");
        $maxPop = Array(1, 10, 20, 40, 80, 160);
        $main = $this->getclass("main", 
        array('rid' => $rid, 'cost' => $cost, 
        'content' => content, 'Description' => $Description, 'param' => $param, 
        'maxPop' => $maxPop, 'multiple_at_level20' => false));
        //******************** barrack
        //rid
        $rid = array(0, 50);
        for ($i = 2, $j = 0.3; $i <= 20; $i ++, $j *= 0.8)
            $rid[$i] = intval($rid[$i - 1] + $rid[$i - 1] * $j);
        $cost = array();
        for ($i = 0; $i <= 20; $i ++)
            $cost[$i] = array(intval($rid[$i] * $i * 200 / 100), 
            intval($rid[$i] * $i * 300 / 100), intval($rid[$i] * $i * 500 / 100),$i
            );
        $require = Array('0' => Array('type' => 1), 
        '1' => Array('type' => 5), 'age' => '1');
        $Description = Array('1' => "addestra truppe");
        $maxPop = Array('0' => 2, '1' => 10, '2' => 20, '3' => 30, '4' => 40, 
        '5' => 50);
        $barack = $this->getclass("barrack", 
        array('rid' => $rid, 'cost' => $cost, 'require' => $require, 
        'Description' => $Description, 'param' => $param, 'maxPop' => $maxPop));
        //******************************************** storage1
        $capacity = array(1000, 1500);
        for ($i = 2, $j = 0.7; $i <= 20; $i ++, $j *= 0.85)
            $capacity[$i] = intval(
            ($capacity[$i - 1] + $capacity[$i - 1] * $j) / 100) * 100;
        $cost = array();
        for ($i = 0; $i <= 20; $i ++)
            $cost[$i] = array(intval($capacity[$i] * 5 / 100), 0, 
            intval($capacity[$i] * 5 / 100), $i);
        $Description = Array(
        '0' => "la capanna dello sciamano aumenta la capacit&agrave; di immagazzinamento della pietra. <blockquote>La capanna dello sciamano custodisce le pietre preziose per la civilt&agrave; preistorica.</blockquote>");
        $content = "capacit&agrave;: {0} <br/>al prossimo : [0]<br/>";
        $param = Array('0' => "capacity");
        $storage1 = $this->getclass("storage1", 
        array('capacity' => $capacity, 'cost' => $cost, 
        'multiple_at_level20' => true, 'Description' => $Description, 
        'param' => $param, 'content' => $content));
        //******************************************** storage2
        $cost = array();
        for ($i = 0; $i <= 20; $i ++)
            $cost[$i] = array(0, 0, intval($capacity[$i] / 10), $i);
        $Description = Array(
        '0' => "la dispensa aumenta la capacit&agrave; di immagazzinamento del legno e del cibo. <blockquote>nella dispensa viene conservato il cibo, e la legna raccolta durante il giorno</blockquote>");
        $param = Array('0' => "capacity");
        $storage2 = $this->getclass("storage2", 
        array('capacity' => $capacity, 'cost' => $cost, 
        'multiple_at_level20' => true, 'Description' => $Description, 
        'param' => $param, 'content' => $content));
        //***********************************************prod1
        $prod = Array('0' => 10, '1' => 12, '2' => 16, '3' => 20, 
        '4' => 25, '5' => 32, '6' => 40, '7' => 50, '8' => 64, '9' => 80, 
        '10' => 100, '11' => 120, '12' => 160, '13' => 200, '14' => 250, 
        '15' => 320, '16' => 400, '17' => 500, '18' => 640, '19' => 800, 
        '20' => 1000);
        $cost = array();
        for ($i = 0; $i <= 20; $i ++)
            $cost[$i] = array(intval($prod[$i] * 400 / 100), 
            intval($prod[$i] * 800 / 100), intval($prod[$i] * 800 / 100), $i);
        $content = "produzione {0} <br/>al prossimo : [0]<br/>";
        $Description = Array(
        '0' => "la cava di pietra produce pietra <blockquote>nella cava si trovano le pietre preziose per la trib&ugrave;, pietre ornamentali, oppure pietre da taglio</blockquote>");
        $param = Array('0' => "prod");
        $maxPop = Array('0' => 1, '1' => 10, '2' => 20, '3' => 40, '4' => 80, 
        '5' => 160);
        $prod1 = $this->getclass("prod1", 
        array('prod' => $prod, 'cost' => $cost, 'multiple' => true, 
        'Description' => $Description, 'param' => $param, 'maxPop' => $maxPop, 
        'content' => $content));
        //***********************************************prod2
        $cost = array();
        for ($i = 0; $i <= 20; $i ++)
            $cost[$i] = array(intval($prod[$i] * 800 / 100), 
            intval($prod[$i] * 400 / 100), intval($prod[$i] * 800 / 100), $i);
        $content = "produzione {0} <br/>al prossimo : [0]<br/>";
        $Description = Array(
        '0' => "l'allevamento produce cibo <blockquote>l'allevamento e la caccia sono i lavori essenziali per la sopravvivenza della trib&ugrave;.</blockquote>");
        $param = Array('0' => "prod");
        $maxPop = Array('0' => 1, '1' => 10, '2' => 20, '3' => 40, '4' => 80, 
        '5' => 160);
        $prod2 = $this->getclass("prod2", 
        array('prod' => $prod, 'cost' => $cost, 'multiple' => true, 
        'Description' => $Description, 'param' => $param, 'maxPop' => $maxPop, 
        'content' => $content));
        //***********************************************prod3
        $cost = array();
        for ($i = 0; $i <= 20; $i ++)
            $cost[$i] = array(intval($prod[$i] * 800 / 100), 
            intval($prod[$i] * 800 / 100), intval($prod[$i] * 400 / 100), $i);
        $content = "produzione {0} <br/>al prossimo : [0]<br/>";
        static $Description = Array(
        '0' => "la segheria produce legno <blockquote>la segheria porta legno alla trib&ugrave; per la costruzione delle strutture</blockquote>");
        $param = Array('0' => "prod");
        $maxPop = Array('0' => 1, '1' => 10, '2' => 20, '3' => 40, '4' => 80, 
        '5' => 160);
        $prod3 = $this->getclass("prod3", 
        array('prod' => $prod, 'cost' => $cost, 'multiple' => true, 
        'Description' => $Description, 'param' => $param, 'maxPop' => $maxPop, 
        'content' => $content));
        //*******************************************house
        $capacity = Array('0' => 100, '1' => 150, '2' => 250, 
        '3' => 400, '4' => 600, '5' => 850, '6' => 1150, '7' => 1500, 
        '8' => 1900, '9' => 2350, '10' => 2850, '11' => 3400, '12' => 4000, 
        '13' => 4650, '14' => 5350, '15' => 6100, '16' => 6900, '17' => 7750, 
        '18' => 8650, '19' => 9600, '20' => 10600);
        $cost = array();
        for ($i = 0; $i <= 20; $i ++)
            $cost[$i] = array(0, 0, intval($capacity[$i]), $i);
        $content = "alloggi disponibili {0}<br/>al prossimo : [0]<br/>";
        $Description = Array(
        '0' => "le palafitte aumentano la capacit&agrave; di immagazzinamento della popolazione<br/> <blockquote>quando le caverne diventarono strette per contenere tutta la trib&ugrave;, si decise di costruire case che fossero al sicuro dai predatori, cosi costruirono le loro case sull'acqua, le palafitte.</blockquote>");
        $param = Array('0' => "capacity");
        $house = $this->getclass("house", 
        array('capacity' => $capacity, 'cost' => $cost, 
        'Description' => $Description, 'param' => $param, 'content' => $content));
        //******************** market
        //rid
        $cost = array();
        for ($i = 0,$j=1; $i <= 20; $i ++,$j+=0.1)
            $cost[$i] = array(intval( ($i+1)*$j * 300 ), 0, intval( ($i+1)*$j * 700 ), 
            $i);
        $require = Array('0' => Array('type' => 2), 
    '1' => Array('type' => 3), 'age' => '1');
        $multiple_at_level20 = true;
        $Description = Array(
    '0' => "tenda del mercante permette di scambiare risorse con i tuoi villaggi e con il mercato");
        $maxPop = Array('0' => 2, '1' => 10, '2' => 20, '3' => 30, '4' => 40, 
        '5' => 50);
        $market = $this->getclass("market", 
        array('cost' => $cost, 'require' => $require, 
        'Description' => $Description, 'multiple_at_level20' => true, 'maxPop' => $maxPop));
        //*********************command
        $cost = array(array(3000,3000,3000));
        for ($i = 1; $i <= 20; $i ++)
            $cost[$i] = array(intval( 6000*$i ), intval(6000*$i), intval(6000*$i), 
            $i);
        $require = Array('0' => Array('type' => MAIN),'age' => '1');
        $Description = Array(
    '1' => "il senato &egrave; il cuore del comando della tu&aacute; civilt&aacute;, da qui potrai colonizzare o conquistare altri villaggi. La costruzione rende capitale il villaggio in cui risiede.");
        $command = $this->getclass("command", 
        array('cost' => $cost, 'require' => $require, 
        'Description' => $Description));
        //*******************************************research
        $pr = array(0, 50);
        for ($i = 2, $j = 0.3; $i <= 20; $i ++, $j *= 0.8)
            $pr[$i] = intval($pr[$i - 1] + $pr[$i - 1] * $j);
        $cost = array();
        for ($i = 0; $i <= 20; $i ++)
            $cost[$i] = array(intval($pr[$i] * $i * 700 / 100), 0, intval($pr[$i] * $i * 300 / 100), 
            $i);
        $content="punti ricerca disponibili: {0}<br/>al prossimo : [0]<br/>";
        $Description = Array(
        '1' => "Nel tempio puoi ricercare le tecnologie.<br/> <blockquote>Grazie alle pregiere dei sacerdoti, gli dei decisero di donare agli umani un po del loro sapere.</blockquote>");
        $require=array(array('type'=>MAIN),'age'=>1);
        $maxPop=array(1,10,15,20,30,40);
        $param=array('pr');
        $resarch = $this->getclass("research", 
        array('pr' => $pr,'param'=>$param,'content'=>$content, 'cost' => $cost, 
        'Description' => $Description, 'require' => $require, 'maxPop' => $maxPop));
        //*****************inserimento classi 
        $file->setClasses(
        array($main, $barack, $storage1, $storage2, $prod1,$prod2,$prod3,$house,$market,$command,$resarch));
        $source = $file->generate();
        //$this->log->debug($source);
        $source = htmlentities($source . '?>');
        $this->view->data = $source;
    }
    function getclass ($name, $param)
    {
        $method = new Zend_CodeGenerator_Php_Method();
        $method->setName("getContent");
        $method->setBody(
        '$text='.$name.'::$content;
   foreach ('.$name.'::$param as $k => $v){
     $r='.$name.'::$$v;
     $text= str_replace("{".$k."}",$r[$liv],$text);
     $text= str_replace("[".$k."]",$r[$liv+1],$text);}
     return $text;')->setParameter(
        array('name' => "liv"));
        $class = new Zend_CodeGenerator_Php_Class();
        $class->setExtendedClass("structure");
        $class->setName($name);
        $class->setMethod($method);
        foreach ($param as $key => $value) {
            $p = new Zend_CodeGenerator_Php_Property();
            $p->setName($key)
                ->setStatic(true)
                ->setDefaultValue($value);
            $class->setProperty($p);
        }
        return $class;
    }
}

