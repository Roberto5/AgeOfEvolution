<?php

class Form_Regciv extends Zend_Form
{

    public function init()
    {
    	$this->setName("civ")->setMethod("post");
    	$this->setAttrib("name", "civ");
    	$this->setDefaultTranslator(Zend_Registry::get("translate"));
    	$name=$this->createElement("text", "name");
    	$name->setLabel("Nome");
    	$name->addFilter("StringTrim")->setRequired(true)->addValidator("alnum");
    	$name->addValidator("StringLength",false,array('max' => 30, 'min' => 4)) 
    	->addValidator("Db_NoRecordExists", null, 
        array('table' => CIV_TABLE, 'field' => 'civ_name'));
        $attribs=array(
    		'size'=>'20',
    		'maxlength'=>'30'
    	);
    	$name->setAttribs($attribs);
    	$name->getValidator("alnum")->setMessage(
        'Il nome utente deve contenere solo lettere e numeri');
    	$name->getValidator("StringLength")->setMessage("il nome utente deve avere una lunghezza tra i 4 ai 30 caratteri");
        $name->getValidator("Db_NoRecordExists")->setMessage("nome utente in uso");
        $this->addElement($name);
        
        $agg=$this->createElement("text", "agg");
        $agg->setLabel("Aggettivo");
        $agg->addFilter("StringTrim")->setRequired(true)->addValidator("alnum");
        $agg->addValidator("StringLength",false,array('max' => 30));
        $agg->setAttribs($attribs);
        
        $this->addElement($agg);
        
        $sector=new Zend_Form_Element_Radio("sector");//$this->createElement("radio", "sector");
        
        $sector->setMultiOptions(array('1'=>'nord/est (+/+)',
        '2'=>'nord/ovest (-/+)',
        '3'=>'sud/ovest (-/-)',
        '4'=>'sud/est (+/-)')
        );
        
        $this->addElement($sector);
        
        $button=$this->createElement("button", "submit");
        $button->setAttrib("onclick","ev.createciv()");
        $button->setLabel("crea");
        
        $this->addElement($button);
        /*echo '<script type="text/javascript" src="scripts/civ.js"></script>
        <h2>crea una nuova civilt&agrave;</h2><span id="load"></span>
        <p id="mess"><form name="civ"><table>
        <tr>
        <td>Nome:</td>
        <td><input id="name" size="20" maxlength="30" /></td>
        </tr>
        <tr>
        <td>Aggettivo</td>
        <td><input id="agg" size="20" maxlength="30" /></td>
        </tr>
	<tr>
	<td colspan="2"><input type="radio" name="sector" value="0" checked="true" /> settore casuale</td>
	</tr>
	<tr>
	<td><input type="radio" name="sector" value="1" /> nord/est (+/+)</td>
	<td><input type="radio" name="sector" value="2" /> nord/ovest (-/+)</td>
	</tr><tr>
	<td><input type="radio" name="sector" value="3" /> sud/ovest (-/-)</td>
	<td><input type="radio" name="sector" value="4" /> sud/est (+/-)</td>
	</tr>
        <tr>
	<td colspan="2"><input type="button" value="crea" onclick="createciv()" /></td>
	</tr>
        </table>
        </form></p>';*/
    }


}

