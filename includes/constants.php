<?php
// nomi tabelle
define("USERS_TABLE","site_users");
define("SESSIONS_TABLE","site_sessions");
define("RELATION_USER_CIV_TABLE","site_user_civ");
define("ALLY_TABLE","s1_ally");
define("CONFIG_TABLE","site_config");
define("OPTION_TABLE", "site_option");
define("ALERTS_TABLE","site_alerts");
define("ALERTS_READ","site_alerts_read");
define("ROLE_TABLE", "site_role");
define("FAQ_TABLE", "site_faq");

//definizioni tipi di edificio vedi wiki categorie
define("TOTBUILDING","19");
define("TOT_TYPE_BUILING","11");
define("MAIN","1");
define("STORAGE1","2"); // moneta di scambio
define("STORAGE2","3"); //altre risorse
define("PROD1","4"); //moneta
define("PROD2","5"); //truppe
define("PROD3","6"); //edifici
define("HOUSE","7"); //sistemi abitativi
define("MARKET","8");// mercato
define("BARRACK","9");//caserma
define("COMMAND","10");//caserma
define("RESEARCH","11");//ricerca
$Building_Array = array("main","storage1","storage2","prod1","prod2","prod3","house","market","barrack","command","research");
$Troops_Array = array("clava","fionda","falange","arco","colony");
$NameTroops=array("Clava","Fionda","Falange","Arciere","Colono");
define("TOT_TYPE_TROOPS","5");
// definizioni tipi di evento
define("TOT_EVENT", "7");
define("BILD_EVENT","1");
define("MARKET_EVENT","2");
define("RETURN_MERCANT_EVENT", "3");
define("MOVEMENTS_EVENT","4");
define("TRAINING_EVENT","5");
define("COLONY_EVENT","6");
define("DESTROY_EVENT", "7");
$event_array=array("bild","market","return mercant","movements","training","colony","destroy");
// tipi di attacco
define("ATTACK","1");
define("RAID","2");
define("REINFORCEMENT","3");
define("RETURN_T", "4");

//tipi report
define("MARKET_REPORT","1");
define("ATTACK_REPORT","2");
define("RAID_REPORT","3");
define("REINF_REPORT","4");
define("REINF_LOST", "5");
define("COLONY_REPORT", "6");

//ricerche
define("RES_ARCO", 1);
$research_array=array('1'=>'Rarco');

//altre costanti
define("VILLAGE_IMAGE_FILE", "common/images/villaggi.php");
define("MAX_EV","100");
define("MAP_ADD","3");


define("OWNER", 3);
define("SHARER", 2);
define("WAIT", 1);
define("REVISION", '1');

$server=array('s1');

define("ANONYMOUS",1);
define("MESS_FOR_PAGE",10);
define("SITO","Age Of Evolution");
define("BACK","javascript:history.back();");
define("URLSITO","http://www.ageofevolution.it");
define("WEBMAIL","admin@ageofevolution.it");


?>