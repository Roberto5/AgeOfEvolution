<?php
class S1_VillageController extends Zend_Controller_Action
{
    public function init ()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
        header("Content-Type: image/png");
        header('Cache-Control: must-revalidate');
		$expire_offset = 1814400; // set to a reaonable interval, say 3600 (1 hr)
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expire_offset) . ' GMT');
    }
    public function indexAction ()
    {}
    public function villageAction ()
    {
    	try {
        $type = $this->getRequest()->getParam('type',0);
        $age = $this->getRequest()->getParam('age',0);
        $zone = $this->getRequest()->getParam('zone',0);
        $capital = $this->getRequest()->getParam('capital');
        if ($type)
            $file = APPLICATION_PATH . '/../common/images/village/village-' .
             $age . '.gif';
        else
            $file = APPLICATION_PATH . '/../common/images/village/village0.gif';
        $file3 = APPLICATION_PATH . '/../common/images/map/village-' . $zone .
         '.jpg';
        $size3 = getimagesize($file3);
        $im4 = imagecreatefromjpeg($file3);
        $im = imagecreatetruecolor(300, 300);
        imagecopyresized($im, $im4, 0, 0, 0, 0, 300, 300, $size3[0], $size3[1]);
        $im3 = imagecreatefromgif($file);
        $size = getimagesize($file);
        imagecopy($im, $im3, 0, 0, 0, 0, $size[0], $size[1]);
        if ($capital == 1) {
            $file2 = APPLICATION_PATH . '/../common/images/village/corona.gif';
            $size2 = getimagesize($file2);
            $im2 = imagecreatefromgif($file2);
            imagecopy($im, $im2, $size[0] - $size2[0] - 10, 5, 0, 0, $size2[0], 
            $size2[1]);
        }
    	} catch (Exception $e) {
    		$this->_log->err($e);
    	}
        imagepng($im);
        imagedestroy($im);
        imagedestroy($im2);
    }
    /**
     * @todo rifare
     * 
     */
    public function mapAction ()
    {
        $zoom = $this->getRequest()->getParam('zoom', 0);
        $size=array(array('x'=>24,'y'=>18,'dim'=>50,'n'=>1),array('x'=>48,'y'=>36,'dim'=>25,'n'=>2),array('x'=>80,'y'=>60,'dim'=>15,'n'=>3));
    	$zo=$size[$zoom];
        $x = $this->getRequest()->getParam('x', 0);
        $y = $this->getRequest()->getParam('y', 0);
        $base=imagecreatetruecolor(3600,2700);
        $land[0] = imagecreatefromjpeg(APPLICATION_PATH . '/../common/images/map/village-0.jpg');
        $land[1] = imagecreatefromjpeg(APPLICATION_PATH . '/../common/images/map/village-1.jpg');
        $land[2] = imagecreatefromjpeg(APPLICATION_PATH . '/../common/images/map/village-2.jpg');
        $land[3] = imagecreatefromjpeg(APPLICATION_PATH . '/../common/images/map/village-3.jpg');
        $land[4] = imagecreatefromjpeg(APPLICATION_PATH . '/../common/images/map/village-4.jpg');
        //carico i dati dei villaggi
        $civ=Model_civilta::getInstance();
        $village=$civ->map->getVillageArray($x,$y,intval($zo['x']*3/2),intval($zo['y']*3/2));
    	$village=$village['focus'];
    	$mx=min(array_keys($village));
        $my=max(array_keys($village[$mx]));
        $dim=$zo['dim'];
        $h=$zo['y']*3;
        $w=$zo['x']*3;
        //@TODO in futuro si potrà "smussare i bordi"
        for($j=0,$y = $my;$j<=2700;$j+=$dim,$y--) {
        	for($i=0,$x =$mx;$i<=3600;$i+=$dim,$x++) {
        		if ($village[$x][$y]['type']) {
        			$vilimg=imagecreatefromjpeg(APPLICATION_PATH . '/../common/images/map/village0.jpg');
        			//controllo proprietà
        			$flag=false;
        			//flag proprietà propria
        			//print_r($civ->cid);
        			if ($village[$x][$y]['civ_id']==$civ->cid) {
        				$flag = APPLICATION_PATH . '/../common/images/map/own.png';
        			}
        			if ($flag) {//applico flag
                		$fl = imagecreatefrompng($flag);
                		$size2 = getimagesize($flag);
                		imagecopyresized($vilimg, $fl, 0, 34, 0, 0, 16, 16, $size2[0], 
                		$size2[1]);
            		}
            		imagecopyresized($base,$vilimg,$i,$j,0,0,$dim,$dim,50,50);
            		imagedestroy($vilimg);
        		}
        		else 
        			imagecopyresized($base,$land[$village[$x][$y]['zone']],$i,$j,0,0,$dim,$dim,200,200);
        	}
        }
        imagejpeg($base);
        imagedestroy($base);
        imagedestroy($vilimg);
        for ($i=0;$i<=4;$i++)
        	imagedestroy($land[$i]);
        
    }
}

