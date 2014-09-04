<?php
abstract class structure
{
    static $capacity = array();
    static $cost = array();
    static $require = array();
    static $multiple = false;
    static $content = "";
    static $Description = Array();
    static $param = Array();
    static $minPop= Array('0' => 1, '1' => 1, '2' => 1, '3' => 1, '4' => 1, 
    '5' => 1);
    static $maxPop = Array('0' => 1, '1' => 1, '2' => 1, '3' => 1, '4' => 1, 
    '5' => 1);
    static $prod = array();
    static $rid = array();
    static $slotForPop=array(1,1,1,1,1,1);
}

class main extends structure
{

    public static $rid =6000;

    public static $cost = array(
            180,
            180,
            240,
            0
            );
    public static $content = 'content';

    public static $Description = array('il focolare aumenta la velocit&agrave; di costruzione delle strutture. <blockquote>davanti il focolare si riunisce tutta la trib&ugrave; per decidere il da-farsi</blockquote>');

    public static $param = array('rid');

    public static $maxPop = array(
        5,
        10,
        20,
        40,
        80,
        160
        );


    public function getContent($param)
    {
        $text=main::$content;
           foreach (main::$param as $k => $v){
             $r=main::$$v;
             $text= str_replace("{".$k."}",$r[$param],$text);
             $text= str_replace("[".$k."]",$r[$param+1],$text);}
             return $text;
    }


}

class barrack extends structure
{
    public static $cost = array(
            100,
            150,
            250,
            1
            );

    public static $require = array(
        array(
            'type' => 1,
            ),
        array(
            'type' => 5,
            ),
        'age' => '1'
        );

    public static $Description = array(1 => 'addestra truppe');

    public static $param = array('rid');

    public static $maxPop = array(
        2,
        10,
        20,
        30,
        40,
        50
        );
	public static $slotForPop=array(5,10,20,30,40,50);
    public function getContent($param)
    {
        $text=barrack::$content;
           foreach (barrack::$param as $k => $v){
             $r=barrack::$$v;
             $text= str_replace("{".$k."}",$r[$param],$text);
             $text= str_replace("[".$k."]",$r[$param+1],$text);}
             return $text;
    }


}

class storage1 extends structure
{

    public static $capacity = 10000;

    public static $cost = array(
            75,
            0,
            75,
            1
            );
    public static $Description = array('la capanna dello sciamano aumenta la capacit&agrave; di immagazzinamento della pietra. <blockquote>La capanna dello sciamano custodisce le pietre preziose per la civilt&agrave; preistorica.</blockquote>');

    public static $param = array('capacity');

    public static $content = 'capacit&agrave;: {0} <br/>al prossimo : [0]<br/>';
	public static $multiple = true;
    public function getContent($param)
    {
        $text=storage1::$content;
           foreach (storage1::$param as $k => $v){
             $r=storage1::$$v;
             $text= str_replace("{".$k."}",$r[$param],$text);
             $text= str_replace("[".$k."]",$r[$param+1],$text);}
             return $text;
    }


}

class storage2 extends structure
{

    public static $capacity = 10000;

    public static $cost = array(
            0,
            0,
            150,
            1
            );

    public static $Description = array('la dispensa aumenta la capacit&agrave; di immagazzinamento del legno e del cibo. <blockquote>nella dispensa viene conservato il cibo, e la legna raccolta durante il giorno</blockquote>');

    public static $param = array('capacity');

    public static $content = 'capacit&agrave;: {0} <br/>al prossimo : [0]<br/>';
	public static $multiple = true;
    public function getContent($param)
    {
        $text=storage2::$content;
           foreach (storage2::$param as $k => $v){
             $r=storage2::$$v;
             $text= str_replace("{".$k."}",$r[$param],$text);
             $text= str_replace("[".$k."]",$r[$param+1],$text);}
             return $text;
    }


}

class prod1 extends structure
{

    public static $prod = 10;

    public static $cost = array(
            40,
            80,
            80,
            0
            );

    public static $multiple = true;

    public static $Description = array('la cava di pietra produce pietra <blockquote>nella cava si trovano le pietre preziose per la trib&ugrave;, pietre ornamentali, oppure pietre da taglio</blockquote>');

    public static $param = array('prod');

    public static $maxPop = array(
        3,
        10,
        20,
        40,
        80,
        160
        );

    public static $content = 'produzione {0} <br/>al prossimo : [0]<br/>';

    public function getContent($param)
    {
        $text=prod1::$content;
           foreach (prod1::$param as $k => $v){
             $r=prod1::$$v;
             $text= str_replace("{".$k."}",$r[$param],$text);
             $text= str_replace("[".$k."]",$r[$param+1],$text);}
             return $text;
    }


}

class prod2 extends structure
{

    public static $prod = 10;

    public static $cost = array(
            80,
            40,
            80,
            0
            );

    public static $multiple = true;

    public static $Description = array('l\'allevamento produce cibo <blockquote>l\'allevamento e la caccia sono i lavori essenziali per la sopravvivenza della trib&ugrave;.</blockquote>');

    public static $param = array('prod');

    public static $maxPop = array(
        3,
        10,
        20,
        40,
        80,
        160
        );

    public static $content = 'produzione {0} <br/>al prossimo : [0]<br/>';

    public function getContent($param)
    {
        $text=prod2::$content;
           foreach (prod2::$param as $k => $v){
             $r=prod2::$$v;
             $text= str_replace("{".$k."}",$r[$param],$text);
             $text= str_replace("[".$k."]",$r[$param+1],$text);}
             return $text;
    }


}

class prod3 extends structure
{

    public static $prod = 10;

    public static $cost = array(
            80,
            80,
            40,
            0
            );

    public static $multiple = true;

    public static $Description = array('la segheria produce legno <blockquote>la segheria porta legno alla trib&ugrave; per la costruzione delle strutture</blockquote>');

    public static $param = array('prod');

    public static $maxPop = array(
        3,
        10,
        20,
        40,
        80,
        160
        );

    public static $content = 'produzione {0} <br/>al prossimo : [0]<br/>';

    public function getContent($param)
    {
        $text=prod3::$content;
           foreach (prod3::$param as $k => $v){
             $r=prod3::$$v;
             $text= str_replace("{".$k."}",$r[$param],$text);
             $text= str_replace("[".$k."]",$r[$param+1],$text);}
             return $text;
    }


}

class house extends structure
{

    public static $capacity = 200;

    public static $cost = array(
            0,
            0,
            100,
            0
            );

    public static $Description = array('le palafitte aumentano la capacit&agrave; di immagazzinamento della popolazione<br/> <blockquote>quando le caverne diventarono strette per contenere tutta la trib&ugrave;, si decise di costruire case che fossero al sicuro dai predatori, cosi costruirono le loro case sull\'acqua, le palafitte.</blockquote>');

    public static $param = array('capacity');

    public static $content = 'alloggi disponibili {0}<br/>al prossimo : [0]<br/>';


    public function getContent($param)
    {
        $text=house::$content;
           foreach (house::$param as $k => $v){
             $r=house::$$v;
             $text= str_replace("{".$k."}",$r[$param],$text);
             $text= str_replace("[".$k."]",$r[$param+1],$text);}
             return $text;
    }


}

class market extends structure
{

    public static $cost = array(
            300,
            0,
            700,
            0
            );

    public static $require = array(
        array(
            'type' => 2,
            ),
        array(
            'type' => 3,
            ),
        'age' => '1'
        );

    public static $Description = array('tenda del mercante permette di scambiare risorse con i tuoi villaggi e con il mercato');


   /* public static $maxPop = array(
        2,
        10,
        20,
        30,
        40,
        50
        );*/

    public function getContent($param)
    {
        $text=market::$content;
           foreach (market::$param as $k => $v){
             $r=market::$$v;
             $text= str_replace("{".$k."}",$r[$param],$text);
             $text= str_replace("[".$k."]",$r[$param+1],$text);}
             return $text;
    }


}

class command extends structure
{

    public static $cost = array(
            3000,
            3000,
            3000
            );

    public static $require = array(
        array(
            'type' => '1'
            ),
        'age' => '1'
        );

    public static $Description = array(1 => 'il senato &egrave; il cuore del comando della tu&aacute; civilt&aacute;, da qui potrai colonizzare o conquistare altri villaggi. La costruzione rende capitale il villaggio in cui risiede.');

    public function getContent($param)
    {
        $text=command::$content;
           foreach (command::$param as $k => $v){
             $r=command::$$v;
             $text= str_replace("{".$k."}",$r[$param],$text);
             $text= str_replace("[".$k."]",$r[$param+1],$text);}
             return $text;
    }


}

class research extends structure
{

    public static $pr = 100;

    public static $param = array('pr');

    public static $content = 'punti ricerca disponibili: {0}<br/>al prossimo : [0]<br/>';

    public static $cost = array(
            350,
            0,
            150,
            1
            );

    public static $Description = array(1 => 'Nel tempio puoi ricercare le tecnologie.<br/> <blockquote>Grazie alle pregiere dei sacerdoti, gli dei decisero di donare agli umani un po del loro sapere.</blockquote>');

    public static $require = array(
        array(
            'type' => '1'
            ),
        'age' => 1
        );

    public static $maxPop = array(
        5,
        10,
        15,
        20,
        30,
        40
        );

    public function getContent($param)
    {
        $text=research::$content;
           foreach (research::$param as $k => $v){
             $r=research::$$v;
             $text= str_replace("{".$k."}",$r[$param],$text);
             $text= str_replace("[".$k."]",$r[$param+1],$text);}
             return $text;
    }


}
?>