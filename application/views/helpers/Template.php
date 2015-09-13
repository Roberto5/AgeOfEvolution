<?php
/**
 *
 * @author pagliaccio
 * @version 
 */
require_once 'Zend/View/Interface.php';
/**
 * alerts helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_template extends Zend_View_Helper_Abstract
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    public $baseUrl;
    public $villagename = array();
    public $civname = array('0' => '-');
    public $allyname = array('0' => '-');
    public $ids = 0;
    /**
     * 
     * @var Model_civilta
     */
    private $civ;
    function __construct ($civ=false)
    {
        $this->view = new Zend_View();
        $config=Zend_Registry::get("config");
        
        if ($config->local) $this->baseUrl =$config->path;
        else {
        	$baseurl = new Zend_View_Helper_BaseUrl();
        	$this->baseUrl = $baseurl->baseUrl();
        }
        if ($civ) $this->civ=$civ; else $this->civ=Model_civilta::getInstance();
    }
    /**
     * 
     * @return Zend_View_Helper_template
     */
    public function template ($template=null,$option=array())
    {
		if (! $this->baseUrl) {
            $baseurl = new Zend_View_Helper_BaseUrl();
            $this->baseUrl = $baseurl->baseUrl();
        }
        if (!$this->civ) $this->civ=Model_civilta::getInstance();
    	if ($template) {
    		return $this->$template($option);
    	}
        else {
        	
        	return $this;
        }
    }
    /**
     * avvisi
     * @param String $text
     * @param String $link
     * @param int $type 0-1
     * @param int $sec
     * @return String
     */
    public function Alerts ($text, $link = null, $type = 1, $sec = 0)
    {
        $html = "";
        $t = Zend_Registry::get('translate');
        $sec = (int) $sec;
        switch ($type) {
            default:
            case 1:
                $class = array('ui-state-highlight', 'ui-icon-info');
                break;
            case 2:
                $class = array('ui-state-error', 'ui-icon-alert');
                break;
        }
        $html .= '<div style="padding: 0pt 0.7em;" class="' . $class[0] .
         ' ui-corner-all"> 
				<p>
 				<span style="float: left; clear:both; margin-right: 0.3em;" class="ui-icon ' .
         $class[1] . '"></span>
 				<p>' . $text . " ";
        if ($link)
            $html .= $t->_('clicca') . ' <a href="' . $link . '">' . $t->_(
            'qui') . '</a> ' . $t->_('per continuare');
        $html .= '</p></p></div>';
        if ($sec)
            $html .= '<script type="text/javascript">
        <!--
        var ms=' . $sec . '000;
        if(ms<1000){ location.href = \'' . $link .
             '\';}else{window.setTimeout("location.href = \'' . $link . '\';", ms );}
        //-->
        </script>';
        return $html;
    }
    /**
     * barra delle risorse
     * @return String
     */
    public function resourceBar ()
    {
        $t = Zend_Registry::get('translate');
        $now = $this->civ->getCurrentVillage();
        $r = '<center><h2>' . ($this->civ->getAge() < 3 ? $t->_('Villaggio') : $t->_(
        'citt&aacute;')) .
         ' <span id="nameVillage'.$now.'" ondblclick="ev.village.changeName(' . $now .
         ')">' . $this->civ->village->data[$now]['name'] . '</span></h2>';
        $res = $this->civ->getResource();
        $r .= '<div>' . $t->_('risorse') . ' : &nbsp &nbsp ';
        for ($i = 0; $i < 3; $i ++) {
            $age = $this->civ->getAge();
            $n = $i;
            $prod = "production_" . ($i + 1);
            $r .= ' <span class="resource" title="' . ($i == 0 ? $this->civ->village->data[$now][$prod] -
             $this->civ->village->negativ : $this->civ->village->data[$now][$prod]) . '">
            ' . $res[$i] . '/' . $this->civ->village->building[$now]->getCapTot(
            ($i == 0 ? STORAGE1 : STORAGE2)) . '
            </span> ';
            $r .= $this->view->image()->resource($n, $age);
            $r .= ' &nbsp &nbsp ';
        }
        $pop = (int) $this->civ->village->data[$now]['pop'] + $this->civ->poptroop;
        $busy = (int) $this->civ->village->busy[$now] + $this->civ->poptroop +
         $this->civ->popc;
        //Zend_Registry::get('log')->debug($this->civ->village->busy[$now],'data array');
        $maxP = $this->civ->village->building[$now]->getCapTot(HOUSE);
        
        $r .= $busy . $this->view->image()->resource(3, $age).'/';
        $r .= ($maxP < $pop ? '<blink><span style="color:red;">' : '') . $pop .
         ($maxP < $pop ? '</span></blink>' : '');
        $r .= $this->view->image()->resource(4, $age);
        $r .= ' ' . $maxP . '<img src="' . $this->baseUrl .
         '/common/images/home.gif" alt="[' . $t->_('case') . ']" title="' .
         $t->_('case') . '" width="16" height="16"/></div></center>';
        return $r;
    }
    /**
     * contenitore truppe
     * @param String $id
     * @param String $title
     * @param array $troops
     * @param bool $hidetroops
     * @param bool $sim
     */
    public function troopcontainer ($id, $title, $troops, $hidetroops = false, 
    $sim = false, $loaded = array())
    {
        global $NameTroops, $Troops_Array;
        $r = '<div style="text-align:center;">
        <div id="' . $id . '" class="t ui-widget-content ui-state-default">
            <h4 class="ui-widget-header"> ' . $title .
         '</h4>';
        if (is_array($loaded)) {
            $r .= '<ul class="troops ui-helper-reset">';
            foreach ($loaded as $key => $value) {
                $r .= '<li class="ui-widget-content ui-corner-tr ui-draggable" style="position: relative;">
        		<h5 class="ui-widget-header">' . $NameTroops[$key] . '(' . $value . ')</h5>
            ' .
                 $this->view->image()->troop($key) . '
            <input type="hidden" name="vel_troop' . $key . '" value="' .
                 $Troops_Array[$key]::$speed . '" />
            <input type="hidden" class="id_troop" value="' . $key . '" />
            <input type="hidden" name="tot' . $key . '" value="' .
                 $troops[$key] . '" />';
                if (! $sim)
                    $r .= '<input type="text" style="display:none;" class="input_troops" name="t' .
                     $key . '" value="' . $value . '" />';
                else
                    $r .= '<input type="text" style="display:none;" class="input_troops" name="' .
                     $id . $key . '" value="' . $value . '" />';
                $r .= '</li>';
            }
            $r .= '</ul>';
        }
        $r .= '</div>';
        if (! $hidetroops) {
            $r .= '<ul id="troops" class="troops ui-helper-reset ui-helper-clearfix">';
            for ($i = 0; $i < TOT_TYPE_TROOPS; $i ++) {
                if ($troops[$i] > 0)
                    $isset = true;
                else
                    $isset = false;
                if ($isset) {
                    $r .= '<li class="ui-widget-content ui-corner-tr">
            <h5 class="ui-widget-header">' . $NameTroops[$i] . '</h5>
            ' .
                     $this->view->image()->troop($i) . '
            <input type="hidden" name="vel_troop' . $i . '" value="' .
                     $Troops_Array[$i]::$speed . '" />
            <input type="hidden" class="id_troop" value="' . $i . '" />
            <input type="hidden" name="tot' . $i . '" value="' . $troops[$i] .
                     '" />';
                    if ($sim) {
                        //$r.= '<input type="text" style="display:none;" class="input_troops" name="atk' .$i . '" value="0" /><input type="text" style="display:none;" class="input_troops" name="def' .$i . '" value="0" />';
                    } else {
                        $r .= '<input type="text" style="display:none;" class="input_troops" name="t' .
                         $i . '" value="0" />';
                    }
                    echo '</li>';
                }
            }
            $r .= '</ul>';
        }
        $r .= '</div>';
        return $r;
    }
    /**
     * stampa la tabella per visualizzare le truppe
     * @param array $troops array[$id_truppe]=numero_truppe
     * @param String $title
     * @param String $info
     * @param bool $show
     * @return String
     */
    public function tabletroops ($troop, $title, $info = '', $show = true)
    {
        $r = '<table class="troopers"><thead>
        <tr>
        <th colspan="11">' . $title . '</th></tr>
	</thead><tr>';
        $truppe = "";
        $table = "";
        $i = 0;
        foreach ($troop as $key => $value) {
            if ($i > 10) {
                $table .= "</tr>";
                $table .= '<tr>' . $truppe . '</tr><tr>';
                $truppe = '';
                $i = 0;
            }
            $table .= '<td>' . $this->view->image()->troop($key) . '</td>';
            if (is_numeric($value))
                $num = $value;
            else
                $num = $value['numbers'];
            $truppe .= '<td>' . ($show ? $num : '?') . '</td>';
            $i ++;
        }
        $r .= $table . '<tr>' . $truppe . '</tr>
            <tr><td colspan="11">' . $info .
         '</td></tr></table>';
        return $r;
    }
    /**
     * link di un villaggio
     * @param int $id
     * @param bool $link default:true inserisce il nome del villaggio dentro un link
     * @return String
     */
    public function village ($id, $name = "", $link = true)
    {
        if ($name)
            $this->villagename[$id] = $name;
        else {
            if ($this->villagename[$id])
                $name = $this->villagename[$id];
            else {
                $name = Model_map::getInstance()->city[$id];
                $this->villagename[$id] = $name;
            }
        }
        if ($link) {
        	$c=Model_map::getInstance()->getCoordFromId($id);
            //$module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
            $r = '<a class="village" href="#" onclick="ev.map.centre=['.$c['x'].','.$c['y'].'];ev.map.shift();ev.map.get_village_info('.$c['x'].','.$c['y'].',true);ev.map.focus={x:'.$c['x'].',y:'.$c['y'].'};">' . $name . '</a>';
        } else
            $r = $name;
        return $r;
    }
    /**
     * link di una civiltà
     * @param int $id
     * @param String $name
     * @param bool $link
     */
    public function civ ($id, $name = "", $link = true)
    {
        if ($name)
            $this->civname[$id] = $name;
        else {
            if ($this->civname[$id])
                $name = $this->civname[$id];
            else {
                $name = Zend_Db_Table::getDefaultAdapter()->fetchOne(
                "SELECT `civ_name` FROM `" . CIV_TABLE . "` WHERE `civ_id`='$id'");
                $this->civname[$id] = $name;
            }
        }
        if ($link) {
            $module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
            $r = '<a class="civ" href="#civ'.$id.'
             " onclick="ev.request(\''.$module.'/profile/index/cid/'.$id.'\',\'post\',{ajax:1});">' . $name . '</a>';
        } else
            $r = $name;
        return $r;
    }
    /**
     * genera il nome di una alleanza
     * @param int $id
     * @param String $name
     * @param bool $link
     * @todo aggiungere il link al profilo ally
     */
    public function ally ($id, $name = "", $link = true)
    {
        if ($name)
            $this->allyname[$id] = $name;
        else {
            if ($this->allyname[$id])
                $name = $this->allyname[$id];
            else {
                $name = Zend_Db_Table::getDefaultAdapter()->fetchOne(
                "SELECT `civ_name` FROM `" . CIV_TABLE . "` WHERE `civ_id`='$id'");
                $this->allyname[$id] = $name;
            }
        }
        if ($link)
            $r = '<a class="ally" href="#ally' . $id . '">' . $name . '</a>';
        else
            $r = $name;
        return $r;
    }
    /**
     * genera il link ad un user
     * @param int $id
     * @param String $name
     * @param bool $link
     */
    public function user($id,$name="",$link=true) {
    	if ($name)
            $this->username[$id] = $name;
        else {
            if ($this->username[$id])
                $name = $this->username[$id];
            else {
                $name = Zend_Db_Table::getDefaultAdapter()->fetchOne(
                "SELECT `username` FROM `" . USERS_TABLE . "` WHERE `ID`='$id'");
                $this->username[$id] = $name;
            }
        }
        if ($link)
            $r = '<a class="user" href="#user' . $id . '" onclick="ev.request(module+\'/profile/index/uid/'.$id.'\',\'post\',{ajax:1});">' . $name . '</a>';
        else
            $r = $name;
        return $r;
    }
    /**
     * tabella report
     * @param array $troop
     */
    public function report ($troop, $sup, $round, $label, $info = array())
    {
        $testo = '<table class="report">
			<thead>
				<tr>
					<th colspan="500">' . $label . ' ' .
         $this->civ($troop['civ'], $troop['user']) . ' da ' .
         $this->village($troop['village_id'], $troop['village_name']) . ' </th>
				</tr>
			</thead>
			<tbody>
				<tr class="troops"><td>&nbsp;</td>';
        foreach ($troop['troops'] as $key => $value) {
            $testo .= '<td>' . $this->view->image()->troop($key) . '</td>';
        }
        $testo .= '</tr>
				<tr>
					<td><b>Truppe</b></td>';
        foreach ($troop['troops'] as $key => $value) {
            $testo .= '<td>' . $value . '</td>';
        }
        $testo .= '</tr>
				<tr class="died">
					<td><b>Perdite</b></td>';
        foreach ($troop['troops'] as $key => $value) {
            $testo .= '<td>' . ($sup[$key] - $value) . '</td>';
        }
        $testo .= '</tr>
				<tr class="superstiti">
					<td>Superstiti </td>';
        foreach ($troop['troops'] as $key => $value) {
            $testo .= '<td>' . $sup[$key] . '</td>';
        }
        $testo .= '
				</tr>' . (($round != 0) ? '
				<tr>
					<td>Round impiegati</td>
					<td colspan="500">' . $round . '</td>
				</tr>' : '');
        if ($info) {
            foreach ($info as $value) {
                $testo .= '
				<tr>
					<td>Informazioni</td><td colspan="500">' . $value . '</td>
				</tr>';
            }
        }
        $testo .= '
			</tbody>
		</table><br><br>';
        return $testo;
        
    }
    /**
     * visualizza uno spoiler
     * @param String $content
     * @param String $hide
     * @param String $show
     * @return string 
     */
    function spoiler ($content, $status = false, $hide = "hide", $show = "show")
    {
        $this->ids ++;
        $html = '<a href="javascript:;" onclick="if ($(\'#spoiler' . $this->ids .
         '\').css(\'display\')==\'block\') $(\'#spoiler' . $this->ids .
         '\').css(\'display\',\'none\'); else $(\'#spoiler' . $this->ids .
         '\').css(\'display\',\'block\');">' . ($status ? $hide : $show) . '</a>
            <div id="spoiler' . $this->ids . '" ' .
         (! $status ? 'style="display:none;"' : "") . ' >';
        $html .= $content;
        $html .= '</div>';
        return $html;
    }
    /**
     * 
     * genera una coda
     * @param Array $queue
     * @param bool $order sort aray before display
     * @param bool $destroy
     */
    function queue ($queue=array(),$order=false,$destroy=false)
    {
    	
    	$t=Zend_Registry::get("translate");
    	$now=$this->civ->getCurrentVillage();
    	$r='';
    	if (!is_array($queue)&&($queue)) $queue->toArray();
    	if ($order) @usort($queue, compareQueue);
        foreach ($queue as $value) {
            $count = $value['time'] - mktime();
            /**
             * IMPORTANTE! la prossima istruzione serve per evitare di bloccare
             * il browser in un continuo ricaricamento se l'event procesor non
             * ha processato l'evento.
             */
            if ($count < 0)
                $count = "00:00:0?";
                 //**************************************
            $param = unserialize($value['params']);
            $r.= '<div> <a href="#'.$t->_('DELETE').'">'.$this->view->image('/common/images/del.gif',$t->_('DELETE'),null,16,16,array('onclick'=>'ev.build.deleteQueue('.$value['id'].');')).' '.
             Model_building::$name[$this->civ->getAge()][$param['type']] .' <span class="countDown">' . $count . '</span> ' . $t->_(
            "finito il ") . date("H:i:s d/m/Y", $value['time']) . "</a></div>";
        }
        return $r;
    }
    /**
     * Sets the view field 
     * @param $view Zend_View_Interface
     */
    public function setView (Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}
