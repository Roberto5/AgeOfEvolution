<?php

class S1_ResearchController extends Zend_Controller_Action
{

    public function init()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
    }

    public function indexAction()
    {
    	$t=Zend_Registry::get("translate");
        global $research_array;
        $token=token_ctrl($_POST);
        if ($token['tokenRe']) {
        	$civ=Model_civilta::getInstance();
        	$r=(int)$_POST['type'];
        	$disp=$civ->research->dispRes();
        	$class=$research_array[$r];
        	if ($disp[$class]) {
        		$liv=0;
        		$liv=$civ->research->data[$r]['liv'];
        		if (($civ->pr-$civ->bpr)>=$class::$cost[$liv]) {
        			//aumento la ricerca
        			if ($civ->research->data[$r]) {
        				$civ->research->update(array('liv'=>$liv+1), "`civ_id`='".$civ->cid."' AND `rid`='$r'");
        			}
        			else {
        				$civ->research->insert(array('liv'=>1, "civ_id"=>$civ->cid,"rid"=>$r));
        			}
        			$liv++;
        			$civ->research->data[$r]['liv']=$liv;
        			$disp=$civ->research->dispRes();
        			foreach ($research_array as $key=>$value) {
        				$dis["button$key"]=$disp[$value];
        			}
        			$label=$t->_('Aumenta');
        			if ($research_array[$r]::$livmax[$civ->getAge()]<=$civ->research->data[$r]['liv']) $label=$t->_('livello massimo');
                        
        			$this->view->update=array('ids'=>array('liv'.$r=>$liv,"button$r"=>$label),'disable'=>$dis,'token'=>array('tokenRe'=>token_set("tokenRe")));
        		}
        		else $this->view->error=array('title'=>$t->_('Errore'),'text'=>$t->_('punti ricerca insufficenti'));
        	}
        	else $this->view->error=array('title'=>$t->_('Errore'),'text'=>$t->_('ricerca non disponibile'));
        }
    }


}

