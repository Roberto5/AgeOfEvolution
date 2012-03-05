<?php

/**
 * genera una stringa alfanumerica casuale
 * @return string
 */
function auth() {
    $auth = null;
    $char = explode(" ", "qwertyuiopasdfghjklzxcvbnm1234567890", 1);
    for ($l = 0; $char[$l]; $l++)
        ;
    for ($i = 0; $i < 16; $i++)
        $auth .= $char[rand(0, $l)];
    return md5($auth);
}

/**
 * ritorna il timastam di linux da una stringa formato YYYY-mm-dd HH:ii:ss
 * @param String $data
 * @return int
 */
function getTimestamp($data) {
    // yyyy-mm-dd HH:ii:ss
    // 0123456789012345678
    $h = substr($data, 11, 2);
    $i = substr($data, 14, 2);
    $s = substr($data, 17, 2);
    $m = substr($data, 5, 2);
    $d = substr($data, 8, 2);
    $y = substr($data, 0, 4);
    //**************
    return mktime($h, $i, $s, $m, $d, $y);
}

/**
 * converte il timestamp in stringa h:mm:ss con h che può essere > 24
 * @param int $time
 * @return String
 */
function timeStampToString($time) {
    $time = intval($time);
    $h = "0";
    $min = "0";
    $sec = "0";
    if ($time < 60)
        $sec = $time;
    elseif ($time < 3600) {
        $min = intval($time / 60);
        $sec = $time - $min * 60;
    }
    if ($time > 3600) {
        $h = intval($time / 3600);
        $time2 = $time - $h * 3600;
        if ($time2 > 60) {
            $min = intval($time2 / 60);
            $sec = $time2 - $min * 60;
        }
        else
            $sec=$time2;
    }
    if ($h < 10)
        $h = "0" . $h;
    if ($min < 10)
        $min = "0" . $min;
    if ($sec < 10)
        $sec = "0" . $sec;
    return $h . ":" . $min . ":" . $sec;
}
/**
 * ritorna la distanza tra le coordinate
 * @param Array $coord1
 * @param Array $coord2
 */
function getDistance($coord1,$coord2) {
	$x=($coord1['x']-$coord2['x'])*($coord1['x']-$coord2['x']);
	$y=($coord1['y']-$coord2['y'])*($coord1['y']-$coord2['y']);
	return sqrt($x+$y);
}
/**
 * ritorna il tempo di attraversamento
 * @param int $distance
 * @param int | Array $speed
 */
function getTime($distance,$speed) {
	if (gettype($speed)=="array") {
		$min=0;
		foreach ($speed as $value) {
			if (($min==0)||($min>$value)) {
				$min=$value;
			}
		}
		if ($min==0) {
			
			$log=Zend_Registry::get("log");
			$log->log("velocità 0!!!!",Zend_Log::CRIT);
			return -1;
		}
		else $speed=$min;
	}
	return intval(($distance/$speed)*3600);
}
/**
 * controlla i token passati come array key=>value
 * @param array $token ['token'.$nome]=>value
 * @return array 
 * [$key]= bool risultato della comparazione di key
 * ['or']= or di tutte le comparazioni
 * ['and']= di tutte le comparazioni
 */
function token_ctrl($token) {
	$session=Zend_Auth::getInstance()->getStorage();
	$log=Zend_Registry::get("log");
	$reply=array('or'=>false,'and'=>true);
	foreach ($token as $key => $value) {
		if (preg_match("#^token#", $key)) {
			$t=$session->get($key);
			$reply[$key]=($value==$t);
			$reply['or']=$reply['or']||$reply[$key];
			$reply['and']=$reply['and']&&$reply[$key];
			$session->delete($key);
			if (!$reply[$key]) $log->log("errore $key, valore ricevuto $value, valore memorizzato $t",Zend_Log::WARN);
		}
	}
	return $reply;
}
/**
 * genera un token e lo memorizza nella sessione
 * @param String $name nome token
 * @return String token generato
 */
function token_set($name) {
	$session=Zend_Auth::getInstance()->getStorage();
	$token=auth();
	$session->set($name,$token);
	
	return $token;
}

/**
 * @param String $destinatario
 * @param String $oggetto
 * @param String $messaggio
 * @param String $mittente
 * @return String html
 * foglio in stile carta ingiallita per le lettere
 */
function foglio($destinatario = "", $oggetto = "", $messaggio = "", $mittente = "") {
    return '
<style type="text/css">

#foglio{
    background-image: url(\'common/images/foglio.jpg\'); 
    height: 500px; 
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
</style>

<form action="mail.php" method="post">
<center><div id="foglio">
<div id="contenitore">
<br /><br />
<table>
<tr>
<td>a:</td><td style="text-align: right;"><input class="testo" name="destinatario" value="' . $destinatario .
    '" /></td>
</tr>
<tr>
<td>da:</td><td style="text-align: right;"><input class="testo" name="mittente" value="' . $mittente .
    '" /></td>
</tr>
<tr>
<td>oggetto:</td><td style="text-align: right;"><input class="testo" name="oggetto" value="' . $oggetto .
    '" /> </td>
</tr>
</table>
 <br />
testo:<br />
<textarea id="area" name="messaggio">' . $messaggio . '</textarea><br />
<center>
<input type="submit" name="action" value="invia" /></center>
</div></div></center></form>';
}

function compareQueue($a,$b) {
	return $a['time']-$b['time'];
}
?>
