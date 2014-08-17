<?php
class image
{
	/**
     * scrive l'immagine della truppa
     * @param int $id
     * @param String $path
     * @return String
     */
    static function getImageTroopers($id,$path) {
        global $NameTroops;
        $str = '<img src="' . $path . '/common/images/troops/t' . $id . '.gif" alt="[' . $NameTroops[$id] . ']" title="' . $NameTroops[$id] . '"  width="16" height="16" />';
        return $str;
    }
/**
     * crea il codice html per l'immagine di una risorsa
     * @param int $nResource da 0 a 3
     * @param int $age da 0 a 5
     * @param String $path
     * @return String
     */
    static function getImageResource($nResource, $age,$path) {
    	$t=Zend_Registry::get("translate");
        $str = '<img src="' . $path;
        if ($nResource < 3) {
            $str.=$fw->civ->getCivAge($age + 1) . '/' . $nResource . '.gif';
            $name = $fw->civ->nameResource[$age][$nResource];
        }
        else {
            $str.='pop.gif';
            $name = "popolazione";
        }
        $str.='" alt="[' . $t->_($name) . ']" title="' . $t->_($name) . '" width="16" height="16" />';
        return $str;
    }
    
}
?>