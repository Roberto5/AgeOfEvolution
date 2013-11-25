<?php
// Start info
define("START_RES","700");
define("START_POP","0");
define("MAX_BOTTINO","1000");
define("SERVER", "s1");
// tabelle db
define("CIV_TABLE", SERVER.'_civ');
define("ALLYPERM_TABLE","s1_ally_permissions");
define("EVENTS_TABLE","s1_events");
define("MESS_TABLE","s1_mess");
define("MESS_READ_TABLE","s1_mess_read");
define("PARAMS_TABLE", "s1_params");
define("OFFER_TABLE","s1_offer");
define("REPORT_TABLE","s1_report");
define("REPORT_READ_TABLE","s1_report_read");
define("TROOPERS","s1_troopers");
define("QUEST_TABLE", "s1_quest");
define("OPTION_CIV_TABLE","s1_option");
define("RESEARCH_TABLE","s1_research");



//map propriety
define("MAP_FILE",realpath(APPLICATION_PATH.'/../common/images/map/s1.json'));
define("WATER",57);
define("MAX_X", "101");
define("MAX_Y","101");


?>