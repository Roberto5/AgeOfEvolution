<?php
class Admin_GenerateController extends Zend_Controller_Action
{
    /**
     * 
     * @var Zend_Db_Adapter_Abstract
     */
    private $db;
    /**
     * 
     * @var Model_params
     */
    private $param;
   
    public function init ()
    {
        $this->log = Zend_Registry::get("log");
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $this->param = Zend_Registry::get("param");
        $this->t = Zend_Registry::get("translate");
    }
    public function indexAction ()
    {
        //$val=$this->param->get("work");
        $val = $this->db->fetchCol(
        "SELECT `value` FROM `" . PARAMS_TABLE . "` WHERE `name`in('work','comment')");
        //256:val=100:x x=val*100/256
        $this->view->value = intval($val[1]);
        $this->view->comment = intval($val[0]);
        $this->view->flag = $_POST['flag'];
        if ($_POST['flag'])
            Zend_Layout::getMvcInstance()->disableLayout();
    }
    public function mapAction ()
    {
    	$this->db->setProfiler(false);
        Zend_Layout::getMvcInstance()->disableLayout();
        set_time_limit(0);
        //ignore_user_abort(true);
        $coord = null;
        $data = null;
        $this->param->set("work", 1);
        $w = 1; //indicatore di lavoro
        $w2 = 1;//altro indicatore
        $lf = 1;
        $q = 100; // larghezza settore 100x100 blocchi
        $tot = (MAX_X * 2 / $q) * (MAX_Y * 2 / $q) * 4 + 1;
        //$i=-MAX_X;$j = -MAX_Y;$k = 1;
        for ($i = - MAX_X; $i < MAX_X; $i += $q) {
            for ($j = - MAX_Y; $j < MAX_Y; $j += $q) {
            	$this->param->set("comment", "generate zone in sector $i/$j");
                for ($k = 1; $k <= 4; $k ++) {
                    //tot:w=100:x x= w*100/tot
                    $perc = intval($w++ * 100 / $tot);// calcolo percentuale
                    $this->param->set("work", $perc);
                    $rad = rand(10, 25);//raggio macchia
                    $cx = rand($i, $i + $q);//coordinate macchia
                    $cy = rand($j, $j + $q);
                    //$this->log->log("rad $rad cx $cx cy $cy",Zend_Log::DEBUG);
                    $c = Model_map::generateZone($k, $rad, 
                    $cx, $cy);
                    foreach ($c as $x => $value) {
                        foreach ($value as $y => $v) {
                            $coord[$x][$y]['zone'] = $v;
                            switch ($v) {
                                case 1:
                                    $coord[$x][$y]['bonus1'] = rand(100, 200);
                                    $max = 450 - $coord[$x][$y]['bonus1'] - 100;
                                    if ($max > 200)
                                        $max = 200;
                                    $min = 450 - $coord[$x][$y]['bonus1'] - 200;
                                    if ($min < 100)
                                        $min = 100;
                                    $coord[$x][$y]['bonus2'] = rand($min, $max);
                                    $coord[$x][$y]['bonus3'] = 450 -
                                     $coord[$x][$y]['bonus1'] -
                                     $coord[$x][$y]['bonus2'];
                                    break;
                                case 2:
                                    $coord[$x][$y]['bonus2'] = rand(200, 250);
                                    $max = 450 - $coord[$x][$y]['bonus2'] - 100;
                                    if ($max > 125)
                                        $max = 125;
                                    $min = 450 - $coord[$x][$y]['bonus2'] - 125;
                                    if ($min < 100)
                                        $min = 100;
                                    $coord[$x][$y]['bonus1'] = rand($min, $max); //100/125
                                    $coord[$x][$y]['bonus3'] = 450 -
                                     $coord[$x][$y]['bonus1'] -
                                     $coord[$x][$y]['bonus2'];
                                    break;
                                case 3:
                                    $coord[$x][$y]['bonus3'] = rand(200, 250);
                                    $max = 450 - $coord[$x][$y]['bonus3'] - 100;
                                    if ($max > 125)
                                        $max = 125;
                                    $min = 450 - $coord[$x][$y]['bonus3'] - 125;
                                    if ($min < 100)
                                        $min = 100;
                                    $coord[$x][$y]['bonus1'] = rand($min, $max); //100/125
                                    $coord[$x][$y]['bonus2'] = 450 -
                                     $coord[$x][$y]['bonus1'] -
                                     $coord[$x][$y]['bonus3'];
                                    break;
                                case 4:
                                    $coord[$x][$y]['bonus1'] = rand(200, 250);
                                    $max = 450 - $coord[$x][$y]['bonus1'] - 100;
                                    if ($max > 125)
                                        $max = 125;
                                    $min = 450 - $coord[$x][$y]['bonus1'] - 125;
                                    if ($min < 100)
                                        $min = 100;
                                    $coord[$x][$y]['bonus2'] = rand($min, $max); //100/125
                                    $coord[$x][$y]['bonus3'] = 450 -
                                     $coord[$x][$y]['bonus1'] -
                                     $coord[$x][$y]['bonus2'];
                                    break;
                            }
                            $w2 ++;
                        }
                    }
                }
            }
        }
        $this->param->set("comment", "delete table temp");
        //$tot=$w2;$w=1;
        $this->db->query("TRUNCATE TABLE `temp`");
        $data = null;
        $this->param->set("comment", "compiling query");
        
        /*foreach ($coord as $x => $value) {
            foreach ($value as $y => $v) {
                //$perc=intval($w*100/$tot);$w++;
                //$this->param->set("work", $perc);
                $data[] = "('$x','$y','" .
                 $v['zone'] . "','" . $v['bonus1'] . "','" . $v['bonus2'] . "','" .
                 $v['bonus3'] . "')";
            }
        }*/
        $this->param->set("comment", "send query");
        file_put_contents(APPLICATION_PATH."/../temp.txt",serialize($coord));
        //$this->db->query("INSERT INTO `temp`  (`x`,`y`,`zone`,`bonus1`,`bonus2`,`bonus3`) VALUES" . implode(",", $data));
        $this->param->set("comment", "success");
        $this->param->set("work", 100);
    }
    public function imageAction ()
    {
        $data = $this->getRequest()->getParams();
        /*$where = "";
        if (is_numeric($data['rad'])) {
            $where = "WHERE ABS(`x`+'" . intval($data['x']) . "')<'" .
             $data['rad'] . "' 
				AND ABS(`y`+'" .
             intval($data['y']) . "')<'" . $data['rad'] . "'";
        } elseif (($data['maxx']) || ($data['maxy']) || ($data['miny']) ||
         ($data['minx'])) {
            $data['maxx'] = $data['maxx'] ? intval($data['maxx']) : MAX_X;
            $data['maxy'] = $data['maxy'] ? intval($data['maxy']) : MAX_Y;
            $where = "WHERE `x`<'" . $data['maxx'] . "' 
				AND `x`>'" . intval($data['minx']) . "'
				AND `y`<'" . $data['maxy'] . "'
				AND `y`>'" . intval($data['miny']) . "'";
        }*/
        Zend_Layout::getMvcInstance()->disableLayout();
        //$c = $this->db->fetchAll("SELECT * FROM `temp` $where");
        $coord =array();$c=unserialize(file_get_contents(APPLICATION_PATH."/../temp.txt"));
        foreach ($c as $x=>$value) {
        	foreach ($value as $y => $v) {
        		$coord[$x][$y] = $v['zone'];
        	}
        }
        unset($c);
        $this->view->coord = $coord;
        unset($coord);
        $this->view->resize = $this->getRequest()->getParam("resize", false);
        $n = $this->param->get("nmap");
        /*$ctot=null;
		for ($i = 1; $i <= $n; $i++) {
			$c=unserialize($this->param->get("map$i"));
			foreach ($c as $key=>$value)
						$ctot[$key]=$value;
		}
		$this->view->coord=$ctot;
		$this->log->log($this->view->coord,Zend_Log::DEBUG);*/
    }
    public function macchiaAction ()
    {
        $rad = rand(10, 25);
        $cx = rand(0, 400);
        $cy = rand(0, 400);
        $c = Model_map::generateZone(1, $rad, $cx, $cy);
        $this->param->set("map1", serialize($c));
        $this->param->set("nmap", 1);
    }
    public function applyAction ()
    {
    	//$this->_log->addFilter(new Zend_Log_Filter_Suppress());
    	$this->db->setProfiler(false);
        set_time_limit(0);
        Zend_Layout::getMvcInstance()->disableLayout();
        /*$m = $this->db->fetchAll("SELECT * FROM `temp`");
        foreach ($m as $value) {
            $map[$value['x']][$value['y']] = $value;
        }*/
        $m=file_get_contents(APPLICATION_PATH."/../temp.txt");
        $map=unserialize($m);
        unset($m);
        $w = 1;
        $tot = (MAX_X * 2) * (MAX_Y * 2);
        $c = 0;
        $this->db->query("DELETE FROM `" . MAP_TABLE . "` WHERE 1");
        $this->param->set("work", 0);
        $prec=0;
        $data=array();
        for ($i = - MAX_X; $i <= MAX_X; $i ++) {
            for ($j = - MAX_Y; $j <= MAX_Y; $j ++) {
                if (! $map[$i][$j]['zone']) {
                    $map[$i][$j]['bonus1'] = rand(75, 125);
                    $max = 300 - $map[$i][$j]['bonus1'] - 75;
                    $min = 300 - $map[$i][$j]['bonus1'] - 125;
                    if ($max > 125)
                        $max = 125;
                    if ($min < 75)
                        $min = 75;
                    $map[$i][$j]['bonus2'] = rand($min, $max);
                    $map[$i][$j]['bonus3'] = 300 - $map[$i][$j]['bonus1'] -
                     $map[$i][$j]['bonus2'];
                }
                $perc = intval($w++ * 100 / $tot);
                if ($prec!=$perc) {$this->param->set("work", $perc);$prec=$perc;}
                
                $data[] = "('" . $this->t->_('Valle Inabitata') . "','" .
                 START_RES . "','" . START_RES . "','" . START_RES .
                 "','$i','$j','" . intval($map[$i][$j]['zone']) . "','" .
                 START_RES . "','" . START_RES . "','" . START_RES . "','" . intval($map[$i][$j]['bonus1']) . "','" . intval($map[$i][$j]['bonus2']) . "','" . intval($map[$i][$j]['bonus3']) . "')";
               $map[$i][$j]=null;
               
               if ($c > 1000) {
                    $c = 0;
                    $this->db->query(
                    "INSERT INTO `" . MAP_TABLE .
                     "` 
			(`name`,`resource_1`,`resource_2`,`resource_3`,`x`,`y`,`zone`,`production_1`,`production_2`,`production_3`,`prod1_bonus`,`prod2_bonus`,`prod3_bonus`) values" .
                     implode(",", $data));
                    $data=array();
                }
                $c ++;
            }
        }
        unset($map);
        if ($data)
            $this->db->query(
            "INSERT INTO `" . MAP_TABLE .
             "` 
			 (`name`,`resource_1`,`resource_2`,`resource_3`,`x`,`y`,`zone`,`production_1`,`production_2`,`production_3`,`prod1_bonus`,`prod2_bonus`,`prod3_bonus`) VALUES" .
             implode(",", $data));
        unset($data);
        $this->db->update(MAP_TABLE, array('name' => 'mercato', 'type' => 1), 
        "`x`='0' AND `y`='0'");
        $idm = $this->db->lastInsertId();
        $this->param->set("id_market", $idm);
        $this->param->set("work", 100);
    }
}

