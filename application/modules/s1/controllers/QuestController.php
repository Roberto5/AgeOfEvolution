<?php

class S1_QuestController extends Zend_Controller_Action
{
	/**
	 * 
	 * @var Model_civilta
	 */
	private $civ;
    public function init()
    {
        $this->civ=Model_civilta::getInstance();
        Zend_Layout::getMvcInstance()->disableLayout();
    }

    public function indexAction()
    {
    	
    	///
        $this->view->data=json_encode($this->civ->quest->ShowQuest(false,$_POST['n'],$_POST['state']));
    }
	public function readAction() {
		//meccanismo ad ingranaggio
		if ($this->civ->quest->state) {//se ho visualizzato la parte finale della quest allora l'ingranaggio
			// fa un passo avanti
			Zend_Db_Table::getDefaultAdapter()->query(
                    "UPDATE `" . CIV_TABLE .
                     "` SET `read_quest`='0',`quest`=`quest`+'1',`state`='0' WHERE `civ_id`='" . $this->civ->cid .
                     "'");
		}
		elseif (!$this->civ->quest->read)
        	Zend_Db_Table::getDefaultAdapter()->query(
                    "UPDATE `" . CIV_TABLE .
                     "` SET `read_quest`='1' WHERE `civ_id`='" . $this->civ->cid .
                     "'");    
	}
	public function changegodAction() {
		
		$master=(int)$_POST['master'];
		if (($master>0)&&($master<5)&&$this->civ->option->get("change_god")) {
			$this->civ->option->del("change_god");
			$this->civ->updateCiv(array('master'=>$master,'state'=>1));
			$this->civ->option->set("have_god", 1);
			$this->view->update=array('master'=>$master);
		}
	}
}

