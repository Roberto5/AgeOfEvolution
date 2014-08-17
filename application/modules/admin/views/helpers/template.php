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
class Zend_View_Helper_template
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    function __construct ()
    {
        $this->view = new Zend_View();
    }
    /**
     * 
     */
    public function alerts ($text, $link = null, $type = 1, $sec = 5)
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
    public function resourceBar ()
    {
        $t = Zend_Registry::get('translate');
        $civ = Model_civilta::getInstance();
        $now = $civ->getCurrentVillage();
        $r = '<center><h2>' . ($civ->getAge() < 3 ? $t->_('Villaggio') : $t->_(
        'citt&aacute;')) .
         ' <span id="nameVillage" ondblclick="ev.changeNameVillage(' . $now .
         ')">' . $civ->village->data[$now]['name'] . '</span></h2>';
        $res = $civ->getResource();
        $r .= '<div>' . $t->_('risorse') . ' : &nbsp &nbsp ';
        for ($i = 0; $i < 3; $i ++) {
            $age = $civ->getAge();
            $n = $i;
            $prod = "production_" . ($i + 1);
            $r .= ' <span class="resource" title="' . ($i == 0 ? $civ->village->data[$now][$prod] -
             $civ->village->negativ : $civ->village->data[$now][$prod]) . '">
            ' .
             $res[$i] . '/' . $civ->village->building[$now]->getCapTot(
            ($i == 0 ? STORAGE1 : STORAGE2)) . '
            </span> ';
            $r .= $this->view->resource($n, $age);
            $r .= ' &nbsp &nbsp ';
        }
        $pop = (int) $civ->village->data[$now]['pop'] + $civ->poptroop;
        $busy = (int) $civ->village->data[$now]['busy_pop'] + $civ->poptroop +
         $civ->popc;
        $maxP = $civ->village->building[$now]->getCapTot(HOUSE);
        $r .= $busy . '<img src="' . $this->baseUrl .
         '/common/images/popbusy.gif" alt="[' . $t->_("lavoratori") .
         ']" title="' . $t->_("lavoratori") . '" width="16" height="16"/>/';
        $n = 3;
        $r .= ($maxP < $pop ? '<blink><span style="color:red;">' : '') . $pop .
         ($maxP < $pop ? '</span></blink>' : '');
        $r .= $this->view->resource($n, $age);
        $r .= ' ' . $maxP . '<img src="' . $this->baseUrl .
         '/common/images/home.gif" alt="[' . $t->_('case') . ']" title="' .
         $t->_('case') . '" width="16" height="16"/></div></center>';
        return $r;
    }
    public function troopcontainer ($id, $title, $troops, $hidetroops = false, 
    $sim = false)
    {
        global $NameTroops, $Troops_Array;
        $r = '<div style="text-align:center;">
        <div id="' .
         $id . '" class="t ui-widget-content ui-state-default">
            <h4 class="ui-widget-header"> ' . $title . '</h4>
        </div>';
        if (! $hidetroops) {
            $r .= '<ul id="troops" class="troops ui-helper-reset ui-helper-clearfix">';
            for ($i = 0; $i < TOT_TYPE_TROOPS; $i ++) {
                if ($troops[$i] > 0)
                    $isset = true;
                else
                    $isset = false;
                if ($isset) {
                    $r .= '<li class="ui-widget-content ui-corner-tr">
            <h5 class="ui-widget-header">' .
                     $NameTroops[$i] . '</h5>
            ' . $this->view->troop($i) . '
            <input type="hidden" name="vel_troop' . $i .
                     '" value="' . $Troops_Array[$i]::$speed . '" />
            <input type="hidden" class="id_troop" value="' . $i . '" />
            <input type="hidden" name="tot' .
                     $i . '" value="' . $troops[$i] .
                     '" />
            <input type="text" style="display:none;" class="input_troops" name="t' .
                     $i . '" value="0" />
        </li>';
                }
            }
            if ($sim) {
                for ($i = 0; $i < TOT_TYPE_TROOPS; $i ++) {
                    if ($troops[$i] > 0)
                        $isset = true;
                    else
                        $isset = false;
                    if ($isset) {
                        $r .= '<li class="ui-widget-content ui-corner-tr" style="display:none;">
            <h5 class="ui-widget-header">' .
                         $NameTroops[$i] . '</h5>
            ' . $this->view->troop($i) . '
            <input type="hidden" name="vel_troop' . $i .
                         '" value="' . $Troops_Array[$i]::$speed . '" />
            <input type="hidden" class="id_troop" value="' . $i . '" />
            <input type="hidden" name="tot' .
                         $i . '" value="' . $troops[$i] .
                         '" />
            <input type="text" style="display:none;" class="input_troops" name="t' .
                         $i . '" value="0" />
        </li>';
                    }
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
            $table .= '<td>' . $this->view->troop($key) . '</td>';
            $truppe .= '<td>' . ($show ? $value['numbers'] : '?') . '</td>';
            $i ++;
        }
        $r .= $table . '<tr>' . $truppe . '</tr>
            <tr><td colspan="11">' . $info . '</td></tr></table>';
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
