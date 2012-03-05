<?php
abstract class structure
{
    static $capacity = array();
    static $cost = array();
    static $require = array();
    static $multiple_at_level20 = false;
    static $multiple = false;
    static $maxliv = Array('0' => 20, '1' => 20, '2' => 20, '3' => 20, 
    '4' => 20, '5' => 20);
    static $content = "";
    static $Description = Array();
    static $param = Array();
    static $maxPop = Array('0' => 1, '1' => 1, '2' => 1, '3' => 1, '4' => 1, 
    '5' => 1);
    static $prod = array();
    static $rid = array();
}

class main extends structure
{

    public static $rid = array(
        6000,
        12000,
        24000,
        43200,
        70848,
        107122,
        150999,
        200478,
        253032,
        306096,
        357450,
        405426,
        448958,
        487523,
        521025,
        549668,
        573842,
        594032,
        610752,
        624504,
        635754
        );

    public static $cost = array(
        array(
            180,
            180,
            240,
            0
            ),
        array(
            396,
            360,
            480,
            1
            ),
        array(
            864,
            720,
            960,
            2
            ),
        array(
            1684,
            1296,
            1728,
            3
            ),
        array(
            2975,
            2125,
            2833,
            4
            ),
        array(
            4820,
            3213,
            4284,
            5
            ),
        array(
            7247,
            4529,
            6039,
            6
            ),
        array(
            10224,
            6014,
            8019,
            7
            ),
        array(
            13663,
            7590,
            10121,
            8
            ),
        array(
            17447,
            9182,
            12243,
            9
            ),
        array(
            21447,
            10723,
            14298,
            10
            ),
        array(
            25541,
            12162,
            16217,
            11
            ),
        array(
            29631,
            13468,
            17958,
            12
            ),
        array(
            33639,
            14625,
            19500,
            13
            ),
        array(
            37513,
            15630,
            20841,
            14
            ),
        array(
            41225,
            16490,
            21986,
            15
            ),
        array(
            44759,
            17215,
            22953,
            16
            ),
        array(
            48116,
            17820,
            23761,
            17
            ),
        array(
            51303,
            18322,
            24430,
            18
            ),
        array(
            54331,
            18735,
            24980,
            19
            ),
        array(
            57217,
            19072,
            25430,
            20
            )
        );

    public static $maxliv = array(
        20,
        20,
        20,
        20,
        20,
        20
        );

    public static $content = 'content';

    public static $Description = array('il focolare aumenta la velocit&agrave; di costruzione delle strutture. <blockquote>davanti il focolare si riunisce tutta la trib&ugrave; per decidere il da-farsi</blockquote>');

    public static $param = array('rid');

    public static $maxPop = array(
        1,
        10,
        20,
        40,
        80,
        160
        );

    public static $multiple_at_level20 = false;

    public function getContent($liv)
    {
        $text=main::$content;
           foreach (main::$param as $k => $v){
             $r=main::$$v;
             $text= str_replace("{".$k."}",$r[$liv],$text);
             $text= str_replace("[".$k."]",$r[$liv+1],$text);}
             return $text;
    }


}

class barrack extends structure
{

    public static $rid = array(
        0,
        50,
        65,
        80,
        95,
        109,
        122,
        133,
        143,
        151,
        158,
        164,
        169,
        173,
        176,
        178,
        180,
        181,
        182,
        183,
        183
        );

    public static $cost = array(
        array(
            0,
            0,
            0,
            0
            ),
        array(
            100,
            150,
            250,
            1
            ),
        array(
            260,
            390,
            650,
            2
            ),
        array(
            480,
            720,
            1200,
            3
            ),
        array(
            760,
            1140,
            1900,
            4
            ),
        array(
            1090,
            1635,
            2725,
            5
            ),
        array(
            1464,
            2196,
            3660,
            6
            ),
        array(
            1862,
            2793,
            4655,
            7
            ),
        array(
            2288,
            3432,
            5720,
            8
            ),
        array(
            2718,
            4077,
            6795,
            9
            ),
        array(
            3160,
            4740,
            7900,
            10
            ),
        array(
            3608,
            5412,
            9020,
            11
            ),
        array(
            4056,
            6084,
            10140,
            12
            ),
        array(
            4498,
            6747,
            11245,
            13
            ),
        array(
            4928,
            7392,
            12320,
            14
            ),
        array(
            5340,
            8010,
            13350,
            15
            ),
        array(
            5760,
            8640,
            14400,
            16
            ),
        array(
            6154,
            9231,
            15385,
            17
            ),
        array(
            6552,
            9828,
            16380,
            18
            ),
        array(
            6954,
            10431,
            17385,
            19
            ),
        array(
            7320,
            10980,
            18300,
            20
            )
        );

    public static $require = array(
        array(
            'type' => 1,
            'liv' => 5
            ),
        array(
            'type' => 5,
            'liv' => 3
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

    public function getContent($liv)
    {
        $text=barrack::$content;
           foreach (barrack::$param as $k => $v){
             $r=barrack::$$v;
             $text= str_replace("{".$k."}",$r[$liv],$text);
             $text= str_replace("[".$k."]",$r[$liv+1],$text);}
             return $text;
    }


}

class storage1 extends structure
{

    public static $capacity = array(
        1000,
        1500,
        2500,
        3900,
        5800,
        8200,
        11100,
        14500,
        18300,
        22400,
        26600,
        30900,
        35100,
        39200,
        43100,
        46700,
        50000,
        53000,
        55700,
        58100,
        60200
        );

    public static $cost = array(
        array(
            50,
            0,
            50,
            0
            ),
        array(
            75,
            0,
            75,
            1
            ),
        array(
            125,
            0,
            125,
            2
            ),
        array(
            195,
            0,
            195,
            3
            ),
        array(
            290,
            0,
            290,
            4
            ),
        array(
            410,
            0,
            410,
            5
            ),
        array(
            555,
            0,
            555,
            6
            ),
        array(
            725,
            0,
            725,
            7
            ),
        array(
            915,
            0,
            915,
            8
            ),
        array(
            1120,
            0,
            1120,
            9
            ),
        array(
            1330,
            0,
            1330,
            10
            ),
        array(
            1545,
            0,
            1545,
            11
            ),
        array(
            1755,
            0,
            1755,
            12
            ),
        array(
            1960,
            0,
            1960,
            13
            ),
        array(
            2155,
            0,
            2155,
            14
            ),
        array(
            2335,
            0,
            2335,
            15
            ),
        array(
            2500,
            0,
            2500,
            16
            ),
        array(
            2650,
            0,
            2650,
            17
            ),
        array(
            2785,
            0,
            2785,
            18
            ),
        array(
            2905,
            0,
            2905,
            19
            ),
        array(
            3010,
            0,
            3010,
            20
            )
        );

    public static $multiple_at_level20 = true;

    public static $Description = array('la capanna dello sciamano aumenta la capacit&agrave; di immagazzinamento della pietra. <blockquote>La capanna dello sciamano custodisce le pietre preziose per la civilt&agrave; preistorica.</blockquote>');

    public static $param = array('capacity');

    public static $content = 'capacit&agrave;: {0} <br/>al prossimo livello: [0]<br/>';

    public function getContent($liv)
    {
        $text=storage1::$content;
           foreach (storage1::$param as $k => $v){
             $r=storage1::$$v;
             $text= str_replace("{".$k."}",$r[$liv],$text);
             $text= str_replace("[".$k."]",$r[$liv+1],$text);}
             return $text;
    }


}

class storage2 extends structure
{

    public static $capacity = array(
        1000,
        1500,
        2500,
        3900,
        5800,
        8200,
        11100,
        14500,
        18300,
        22400,
        26600,
        30900,
        35100,
        39200,
        43100,
        46700,
        50000,
        53000,
        55700,
        58100,
        60200
        );

    public static $cost = array(
        array(
            0,
            0,
            100,
            0
            ),
        array(
            0,
            0,
            150,
            1
            ),
        array(
            0,
            0,
            250,
            2
            ),
        array(
            0,
            0,
            390,
            3
            ),
        array(
            0,
            0,
            580,
            4
            ),
        array(
            0,
            0,
            820,
            5
            ),
        array(
            0,
            0,
            1110,
            6
            ),
        array(
            0,
            0,
            1450,
            7
            ),
        array(
            0,
            0,
            1830,
            8
            ),
        array(
            0,
            0,
            2240,
            9
            ),
        array(
            0,
            0,
            2660,
            10
            ),
        array(
            0,
            0,
            3090,
            11
            ),
        array(
            0,
            0,
            3510,
            12
            ),
        array(
            0,
            0,
            3920,
            13
            ),
        array(
            0,
            0,
            4310,
            14
            ),
        array(
            0,
            0,
            4670,
            15
            ),
        array(
            0,
            0,
            5000,
            16
            ),
        array(
            0,
            0,
            5300,
            17
            ),
        array(
            0,
            0,
            5570,
            18
            ),
        array(
            0,
            0,
            5810,
            19
            ),
        array(
            0,
            0,
            6020,
            20
            )
        );

    public static $multiple_at_level20 = true;

    public static $Description = array('la dispensa aumenta la capacit&agrave; di immagazzinamento del legno e del cibo. <blockquote>nella dispensa viene conservato il cibo, e la legna raccolta durante il giorno</blockquote>');

    public static $param = array('capacity');

    public static $content = 'capacit&agrave;: {0} <br/>al prossimo livello: [0]<br/>';

    public function getContent($liv)
    {
        $text=storage2::$content;
           foreach (storage2::$param as $k => $v){
             $r=storage2::$$v;
             $text= str_replace("{".$k."}",$r[$liv],$text);
             $text= str_replace("[".$k."]",$r[$liv+1],$text);}
             return $text;
    }


}

class prod1 extends structure
{

    public static $prod = array(
        10,
        12,
        16,
        20,
        25,
        32,
        40,
        50,
        64,
        80,
        100,
        120,
        160,
        200,
        250,
        320,
        400,
        500,
        640,
        800,
        1000
        );

    public static $cost = array(
        array(
            40,
            80,
            80,
            0
            ),
        array(
            48,
            96,
            96,
            1
            ),
        array(
            64,
            128,
            128,
            2
            ),
        array(
            80,
            160,
            160,
            3
            ),
        array(
            100,
            200,
            200,
            4
            ),
        array(
            128,
            256,
            256,
            5
            ),
        array(
            160,
            320,
            320,
            6
            ),
        array(
            200,
            400,
            400,
            7
            ),
        array(
            256,
            512,
            512,
            8
            ),
        array(
            320,
            640,
            640,
            9
            ),
        array(
            400,
            800,
            800,
            10
            ),
        array(
            480,
            960,
            960,
            11
            ),
        array(
            640,
            1280,
            1280,
            12
            ),
        array(
            800,
            1600,
            1600,
            13
            ),
        array(
            1000,
            2000,
            2000,
            14
            ),
        array(
            1280,
            2560,
            2560,
            15
            ),
        array(
            1600,
            3200,
            3200,
            16
            ),
        array(
            2000,
            4000,
            4000,
            17
            ),
        array(
            2560,
            5120,
            5120,
            18
            ),
        array(
            3200,
            6400,
            6400,
            19
            ),
        array(
            4000,
            8000,
            8000,
            20
            )
        );

    public static $multiple = true;

    public static $Description = array('la cava di pietra produce pietra <blockquote>nella cava si trovano le pietre preziose per la trib&ugrave;, pietre ornamentali, oppure pietre da taglio</blockquote>');

    public static $param = array('prod');

    public static $maxPop = array(
        1,
        10,
        20,
        40,
        80,
        160
        );

    public static $content = 'produzione {0} <br/>al prossimo livello: [0]<br/>';

    public function getContent($liv)
    {
        $text=prod1::$content;
           foreach (prod1::$param as $k => $v){
             $r=prod1::$$v;
             $text= str_replace("{".$k."}",$r[$liv],$text);
             $text= str_replace("[".$k."]",$r[$liv+1],$text);}
             return $text;
    }


}

class prod2 extends structure
{

    public static $prod = array(
        10,
        12,
        16,
        20,
        25,
        32,
        40,
        50,
        64,
        80,
        100,
        120,
        160,
        200,
        250,
        320,
        400,
        500,
        640,
        800,
        1000
        );

    public static $cost = array(
        array(
            80,
            40,
            80,
            0
            ),
        array(
            96,
            48,
            96,
            1
            ),
        array(
            128,
            64,
            128,
            2
            ),
        array(
            160,
            80,
            160,
            3
            ),
        array(
            200,
            100,
            200,
            4
            ),
        array(
            256,
            128,
            256,
            5
            ),
        array(
            320,
            160,
            320,
            6
            ),
        array(
            400,
            200,
            400,
            7
            ),
        array(
            512,
            256,
            512,
            8
            ),
        array(
            640,
            320,
            640,
            9
            ),
        array(
            800,
            400,
            800,
            10
            ),
        array(
            960,
            480,
            960,
            11
            ),
        array(
            1280,
            640,
            1280,
            12
            ),
        array(
            1600,
            800,
            1600,
            13
            ),
        array(
            2000,
            1000,
            2000,
            14
            ),
        array(
            2560,
            1280,
            2560,
            15
            ),
        array(
            3200,
            1600,
            3200,
            16
            ),
        array(
            4000,
            2000,
            4000,
            17
            ),
        array(
            5120,
            2560,
            5120,
            18
            ),
        array(
            6400,
            3200,
            6400,
            19
            ),
        array(
            8000,
            4000,
            8000,
            20
            )
        );

    public static $multiple = true;

    public static $Description = array('l\'allevamento produce cibo <blockquote>l\'allevamento e la caccia sono i lavori essenziali per la sopravvivenza della trib&ugrave;.</blockquote>');

    public static $param = array('prod');

    public static $maxPop = array(
        1,
        10,
        20,
        40,
        80,
        160
        );

    public static $content = 'produzione {0} <br/>al prossimo livello: [0]<br/>';

    public function getContent($liv)
    {
        $text=prod2::$content;
           foreach (prod2::$param as $k => $v){
             $r=prod2::$$v;
             $text= str_replace("{".$k."}",$r[$liv],$text);
             $text= str_replace("[".$k."]",$r[$liv+1],$text);}
             return $text;
    }


}

class prod3 extends structure
{

    public static $prod = array(
        10,
        12,
        16,
        20,
        25,
        32,
        40,
        50,
        64,
        80,
        100,
        120,
        160,
        200,
        250,
        320,
        400,
        500,
        640,
        800,
        1000
        );

    public static $cost = array(
        array(
            80,
            80,
            40,
            0
            ),
        array(
            96,
            96,
            48,
            1
            ),
        array(
            128,
            128,
            64,
            2
            ),
        array(
            160,
            160,
            80,
            3
            ),
        array(
            200,
            200,
            100,
            4
            ),
        array(
            256,
            256,
            128,
            5
            ),
        array(
            320,
            320,
            160,
            6
            ),
        array(
            400,
            400,
            200,
            7
            ),
        array(
            512,
            512,
            256,
            8
            ),
        array(
            640,
            640,
            320,
            9
            ),
        array(
            800,
            800,
            400,
            10
            ),
        array(
            960,
            960,
            480,
            11
            ),
        array(
            1280,
            1280,
            640,
            12
            ),
        array(
            1600,
            1600,
            800,
            13
            ),
        array(
            2000,
            2000,
            1000,
            14
            ),
        array(
            2560,
            2560,
            1280,
            15
            ),
        array(
            3200,
            3200,
            1600,
            16
            ),
        array(
            4000,
            4000,
            2000,
            17
            ),
        array(
            5120,
            5120,
            2560,
            18
            ),
        array(
            6400,
            6400,
            3200,
            19
            ),
        array(
            8000,
            8000,
            4000,
            20
            )
        );

    public static $multiple = true;

    public static $Description = array('la segheria produce legno <blockquote>la segheria porta legno alla trib&ugrave; per la costruzione delle strutture</blockquote>');

    public static $param = array('prod');

    public static $maxPop = array(
        1,
        10,
        20,
        40,
        80,
        160
        );

    public static $content = 'produzione {0} <br/>al prossimo livello: [0]<br/>';

    public function getContent($liv)
    {
        $text=prod3::$content;
           foreach (prod3::$param as $k => $v){
             $r=prod3::$$v;
             $text= str_replace("{".$k."}",$r[$liv],$text);
             $text= str_replace("[".$k."]",$r[$liv+1],$text);}
             return $text;
    }


}

class house extends structure
{

    public static $capacity = array(
        100,
        150,
        250,
        400,
        600,
        850,
        1150,
        1500,
        1900,
        2350,
        2850,
        3400,
        4000,
        4650,
        5350,
        6100,
        6900,
        7750,
        8650,
        9600,
        10600
        );

    public static $cost = array(
        array(
            0,
            0,
            100,
            0
            ),
        array(
            0,
            0,
            150,
            1
            ),
        array(
            0,
            0,
            250,
            2
            ),
        array(
            0,
            0,
            400,
            3
            ),
        array(
            0,
            0,
            600,
            4
            ),
        array(
            0,
            0,
            850,
            5
            ),
        array(
            0,
            0,
            1150,
            6
            ),
        array(
            0,
            0,
            1500,
            7
            ),
        array(
            0,
            0,
            1900,
            8
            ),
        array(
            0,
            0,
            2350,
            9
            ),
        array(
            0,
            0,
            2850,
            10
            ),
        array(
            0,
            0,
            3400,
            11
            ),
        array(
            0,
            0,
            4000,
            12
            ),
        array(
            0,
            0,
            4650,
            13
            ),
        array(
            0,
            0,
            5350,
            14
            ),
        array(
            0,
            0,
            6100,
            15
            ),
        array(
            0,
            0,
            6900,
            16
            ),
        array(
            0,
            0,
            7750,
            17
            ),
        array(
            0,
            0,
            8650,
            18
            ),
        array(
            0,
            0,
            9600,
            19
            ),
        array(
            0,
            0,
            10600,
            20
            )
        );

    public static $Description = array('le palafitte aumentano la capacit&agrave; di immagazzinamento della popolazione<br/> <blockquote>quando le caverne diventarono strette per contenere tutta la trib&ugrave;, si decise di costruire case che fossero al sicuro dai predatori, cosi costruirono le loro case sull\'acqua, le palafitte.</blockquote>');

    public static $param = array('capacity');

    public static $content = 'alloggi disponibili {0}<br/>al prossimo livello: [0]<br/>';

    public static $maxliv = array(
        6,
        20,
        20,
        20,
        20,
        20
        );

    public function getContent($liv)
    {
        $text=house::$content;
           foreach (house::$param as $k => $v){
             $r=house::$$v;
             $text= str_replace("{".$k."}",$r[$liv],$text);
             $text= str_replace("[".$k."]",$r[$liv+1],$text);}
             return $text;
    }


}

class market extends structure
{

    public static $cost = array(
        array(
            300,
            0,
            700,
            0
            ),
        array(
            660,
            0,
            1540,
            1
            ),
        array(
            1080,
            0,
            2520,
            2
            ),
        array(
            1560,
            0,
            3640,
            3
            ),
        array(
            2100,
            0,
            4900,
            4
            ),
        array(
            2700,
            0,
            6300,
            5
            ),
        array(
            3360,
            0,
            7840,
            6
            ),
        array(
            4080,
            0,
            9520,
            7
            ),
        array(
            4860,
            0,
            11340,
            8
            ),
        array(
            5700,
            0,
            13300,
            9
            ),
        array(
            6600,
            0,
            15400,
            10
            ),
        array(
            7560,
            0,
            17640,
            11
            ),
        array(
            8580,
            0,
            20020,
            12
            ),
        array(
            9660,
            0,
            22540,
            13
            ),
        array(
            10800,
            0,
            25200,
            14
            ),
        array(
            12000,
            0,
            28000,
            15
            ),
        array(
            13260,
            0,
            30940,
            16
            ),
        array(
            14580,
            0,
            34020,
            17
            ),
        array(
            15960,
            0,
            37240,
            18
            ),
        array(
            17400,
            0,
            40600,
            19
            ),
        array(
            18900,
            0,
            44100,
            20
            )
        );

    public static $require = array(
        array(
            'type' => 2,
            'liv' => 1
            ),
        array(
            'type' => 3,
            'liv' => 1
            ),
        'age' => '1'
        );

    public static $Description = array('tenda del mercante permette di scambiare risorse con i tuoi villaggi e con il mercato');

    public static $multiple_at_level20 = true;

    public static $maxPop = array(
        2,
        10,
        20,
        30,
        40,
        50
        );

    public function getContent($liv)
    {
        $text=market::$content;
           foreach (market::$param as $k => $v){
             $r=market::$$v;
             $text= str_replace("{".$k."}",$r[$liv],$text);
             $text= str_replace("[".$k."]",$r[$liv+1],$text);}
             return $text;
    }


}

class command extends structure
{

    public static $cost = array(
        array(
            3000,
            3000,
            3000
            ),
        array(
            6000,
            6000,
            6000,
            1
            ),
        array(
            12000,
            12000,
            12000,
            2
            ),
        array(
            18000,
            18000,
            18000,
            3
            ),
        array(
            24000,
            24000,
            24000,
            4
            ),
        array(
            30000,
            30000,
            30000,
            5
            ),
        array(
            36000,
            36000,
            36000,
            6
            ),
        array(
            42000,
            42000,
            42000,
            7
            ),
        array(
            48000,
            48000,
            48000,
            8
            ),
        array(
            54000,
            54000,
            54000,
            9
            ),
        array(
            60000,
            60000,
            60000,
            10
            ),
        array(
            66000,
            66000,
            66000,
            11
            ),
        array(
            72000,
            72000,
            72000,
            12
            ),
        array(
            78000,
            78000,
            78000,
            13
            ),
        array(
            84000,
            84000,
            84000,
            14
            ),
        array(
            90000,
            90000,
            90000,
            15
            ),
        array(
            96000,
            96000,
            96000,
            16
            ),
        array(
            102000,
            102000,
            102000,
            17
            ),
        array(
            108000,
            108000,
            108000,
            18
            ),
        array(
            114000,
            114000,
            114000,
            19
            ),
        array(
            120000,
            120000,
            120000,
            20
            )
        );

    public static $require = array(
        array(
            'type' => '1',
            'liv' => 10
            ),
        'age' => '1'
        );

    public static $Description = array(1 => 'il senato &egrave; il cuore del comando della tu&aacute; civilt&aacute;, da qui potrai colonizzare o conquistare altri villaggi. La costruzione rende capitale il villaggio in cui risiede.');

    public function getContent($liv)
    {
        $text=command::$content;
           foreach (command::$param as $k => $v){
             $r=command::$$v;
             $text= str_replace("{".$k."}",$r[$liv],$text);
             $text= str_replace("[".$k."]",$r[$liv+1],$text);}
             return $text;
    }


}

class research extends structure
{

    public static $pr = array(
        0,
        50,
        65,
        80,
        95,
        109,
        122,
        133,
        143,
        151,
        158,
        164,
        169,
        173,
        176,
        178,
        180,
        181,
        182,
        183,
        183
        );

    public static $param = array('pr');

    public static $content = 'punti ricerca disponibili: {0}<br/>al prossimo livello: [0]<br/>';

    public static $cost = array(
        array(
            0,
            0,
            0,
            0
            ),
        array(
            350,
            0,
            150,
            1
            ),
        array(
            910,
            0,
            390,
            2
            ),
        array(
            1680,
            0,
            720,
            3
            ),
        array(
            2660,
            0,
            1140,
            4
            ),
        array(
            3815,
            0,
            1635,
            5
            ),
        array(
            5124,
            0,
            2196,
            6
            ),
        array(
            6517,
            0,
            2793,
            7
            ),
        array(
            8008,
            0,
            3432,
            8
            ),
        array(
            9513,
            0,
            4077,
            9
            ),
        array(
            11060,
            0,
            4740,
            10
            ),
        array(
            12628,
            0,
            5412,
            11
            ),
        array(
            14196,
            0,
            6084,
            12
            ),
        array(
            15743,
            0,
            6747,
            13
            ),
        array(
            17248,
            0,
            7392,
            14
            ),
        array(
            18690,
            0,
            8010,
            15
            ),
        array(
            20160,
            0,
            8640,
            16
            ),
        array(
            21539,
            0,
            9231,
            17
            ),
        array(
            22932,
            0,
            9828,
            18
            ),
        array(
            24339,
            0,
            10431,
            19
            ),
        array(
            25620,
            0,
            10980,
            20
            )
        );

    public static $Description = array(1 => 'Nel tempio puoi ricercare le tecnologie.<br/> <blockquote>Grazie alle pregiere dei sacerdoti, gli dei decisero di donare agli umani un po del loro sapere.</blockquote>');

    public static $require = array(
        array(
            'type' => '1',
            'liv' => 5
            ),
        'age' => 1
        );

    public static $maxPop = array(
        1,
        10,
        15,
        20,
        30,
        40
        );

    public static $maxliv = array(
        0,
        10,
        15,
        20,
        20,
        20
        );

    public function getContent($liv)
    {
        $text=research::$content;
           foreach (research::$param as $k => $v){
             $r=research::$$v;
             $text= str_replace("{".$k."}",$r[$liv],$text);
             $text= str_replace("[".$k."]",$r[$liv+1],$text);}
             return $text;
    }


}
?>