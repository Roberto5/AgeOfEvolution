<?php
if (is_file('../application/configs/application.ini')) {
	echo json_encode("error, config file existing");
}
else {
	$require=array('write'=>false,'override'=>false);
	$require['write']=is_writeable('../');
	$require['override']=!is_null(getenv('OVERRIDE'));
	ob_start();
	phpinfo();
	$phpinfo=ob_get_clean();
	$require['rewrite']=strstr($phpinfo,"mod_rewrite")? true : false;
	$require['mysql']=strstr($phpinfo,"mysql")? true : false;
	echo json_encode($require);
}

?>