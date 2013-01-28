<?php
if(!empty($_SERVER["HTTP_ACCEPT_ENCODING"]) && strpos("gzip",$_SERVER["HTTP_ACCEPT_ENCODING"]) === NULL){}else{ob_start("ob_gzhandler");}

header('Content-Type: text/javascript; charset: UTF-8');
header('Cache-Control: must-revalidate');

$expire_offset = 1814400; // set to a reaonable interval, say 3600 (1 hr)
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expire_offset) . ' GMT');


$file=array('*main script*','evolution.js',"function.js","reg.js","time.js","*framework*","jquery.js","jquery-ui.js","jquery.contextmenu.js","jquery.tools.min.js","jquery.cookie.js","jquery.lightbox-0.5.min.js","jquery.li-scroller.1.0.js");

$text="";$mtime=0;
foreach ($file as $value) {
	if (preg_match("/\*(.+)\*/", $value)) {
		$text.="\n/************$value************/\n";
	}
	else {
		$text.=file_get_contents($value);
		$stat=stat($value);
	if ($mtime<$stat['mtime']) $mtime=$stat['mtime'];
	}
	
}
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
echo $text;

?>