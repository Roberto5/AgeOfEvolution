<?php

/**
 * NewZendModel
 * 
 * @author pagliaccio
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Model_mess extends Zend_Db_Table_Abstract {

	/**
	 * The default table name 
	 */
	protected $_name=MESS_TABLE;

    /**
     * controlla se ci sono messaggi e quanti.
     * @return int
     */
    static function ThereAreMess($cid=0,$uid=0) {
    	if (!$cid) $cid=Model_civilta::getInstance()->cid;
        if (!$uid) $uid=Zend_Auth::getInstance()->getIdentity()->user_id;
        return Zend_Db_Table::getDefaultAdapter()->fetchOne("SELECT count(*) FROM `s1_mess` WHERE `s1_mess`.`destinatario` = '$cid' AND `s1_mess`.`id` !=ALL (SELECT `id` FROM `s1_mess_read` WHERE `user`='$uid')");
    }
	
	static function send($dest_id, $mitt, $ogg, $mess) {
		Zend_Db_Table::getDefaultAdapter()->query("INSERT INTO `".MESS_TABLE."` (`id`, `oggetto`, `messaggio`, `ora`, `destinatario`, `mittente`) VALUES (NULL, '".$ogg."', '".$mess."', CURRENT_TIMESTAMP, '".$dest_id."', '".$mitt."')");
	}
	
	static function foglio($destinatario = "",$oggetto = "",$messaggio = "",$mittente = "",$write=false,$action='',$bottone='')
	{
		$t=Zend_Registry::get("translate");
		$base = new Zend_View_Helper_BaseUrl();
		$base=$base->getBaseUrl();
		$corpo_mess = '
		  <style type="text/css">
		  
		  #foglio{
			  background-image: url(\''.$base.'/common/images/foglio.png\'); 
			  height: 400px; 
			  width: 400px;
			  color: black;
		  }
		  #contenitore{
			  width: 310px;
			  color: black;
		  }
		  
		  input.testo{
			  background-color: transparent; 
			  border-color: black; 
			  width: 250px; 
			  border-width: 0px 0px 2px;
		  }
		  textarea{
			  background-color: transparent; 
			  height: 250px; 
			  width: 300px; 
			  border-width: 0px; 
			  margin-top: 10px; 
			  margin-left: 0px;
			  color: black;
		  }
		  .ui-autocomplete-loading { background: white url(\''.$base.'/common/images/loading.gif\') right center no-repeat; }
		  
		  </style>
		  <script type="text/javascript">
	$(function() {
		var cache = {},
			lastXhr;
		$( "#civ" ).autocomplete({
			minLength: 1,
			source: function( request, response ) {
				var term = request.term;
				if ( term in cache ) {
					response( cache[ term ] );
					return;
				}

				lastXhr = $.getJSON( path+"/"+module+"/message/autocomplete/", request, function( data, status, xhr ) {
					cache[ term ] = data;
					if ( xhr === lastXhr ) {
						response( data );
					}
				});
			}
		});
	});
	</script>
		  ';
		  if ($write) {
		  $corpo_mess .= '<div id="mess">';
		  }
		  $corpo_mess .= '<center><div id="foglio">
		  <div id="contenitore">
		  <br /><br />
		  <table';
		  if (!$write) $corpo_mess .= ' style="width: 300px;"';
		  $corpo_mess .= '>
		  <tr>
		  <td>a:</td><td style="text-align: right;">';
		  if ($write) {
			  $corpo_mess .= '<input id="civ" class="testo" name="destinatario" value="'.$destinatario.'" />';
		  }else{
			  $corpo_mess .= '<strong>'.$destinatario.'</strong>';
		  }
		  $corpo_mess .= '</td></tr>
		  <tr>
		  <td>da:</td><td style="text-align: right;"><strong>'.$mittente.'</strong></td>
		  </tr>
		  <tr>
		  <td>oggetto:</td><td style="text-align: right;">';
		  if ($write) {
			  $corpo_mess .= '<input class="testo" name="oggetto" id="ogg" value="'.$oggetto.'" />';
		  }else{
			  $corpo_mess .= '<strong>'.$oggetto.'</strong>';
		  }
		  $corpo_mess .= ' </td>
		  </tr>
		  </table>
		   <br />
		  testo:<br /><div style="text-align:left; height: 300px;overflow:auto;">';
		  if ($write) {
		  	  $corpo_mess .= '<textarea id="area" name="messaggio">'.$messaggio.'</textarea><br />';
		  }else{
			  $corpo_mess .= str_replace("\n", "<br/>", $messaggio);
		  }
		  $corpo_mess .= '</div><center>';
		  if ($write) {
		  	  $corpo_mess .= '<button onclick="ev.message.send(\''.$action.'\');$(\'#windows{wid}\').dialog(\'close\');">'.$t->_('invia').'</button></center>';
		  }elseif ($bottone) { $corpo_mess .= $bottone; }
		  $corpo_mess .= '</div></div></center>';
		  if ($write) $corpo_mess .= '</div>';
		  
		  return $corpo_mess;
	}
}
