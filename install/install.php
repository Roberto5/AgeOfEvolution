<?php
try {
	function compress_config($parent,$child=array()) {
		foreach ($parent as $key => $value) {
			if (isset($child[$key])) {
				if (is_array($value)&&is_array($child[$key])) $child[$key]=compress_config($value,$child[$key]);
				else {
					if ($value==$child[$key]) unset($child[$key]);
				}
			}
		}
		return $child;
	}
	set_include_path(implode(PATH_SEPARATOR, array(
	realpath(dirname(__FILE__).'/../library/'),
	get_include_path(),
	)));
	include_once 'Zend/Db.php';
	include_once 'Zend/Validate.php';
	include_once 'Zend/Validate/EmailAddress.php';
	include_once 'Zend/Validate/Alnum.php';
	include_once 'Zend/Config/Ini.php';
	include_once 'Zend/Config/Writer/Ini.php';
	foreach ($_POST as $key=>$value) {
		if (is_string($value))
		$_POST[$key]=htmlentities($value);
	}
	$email = new Zend_Validate_EmailAddress();
	$alnum=new Zend_Validate_Alnum();
	if ((!$email->isValid($_POST['adminemail'])
	|| !$alnum->isValid($_POST['adminpass'])
	|| !$alnum->isValid($_POST['admin'])
	|| $_POST['adminpass']!=$_POST['pass2'])&&$_POST['dbconfig']) {
		echo json_encode(array('bool'=>false,'text'=>'form error'));
		exit(1);
	}
	$debugQuery="";
	function import($file,Zend_Db_Adapter_Abstract $adapter) {
		$all_lines = file($file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
		$query="";
		foreach($all_lines as $line) {
			if(substr($line, 0, 2) == "--") {
				if ($query) $adapter->query($query);
				$debugQuery=$query;
				$query="";
				continue;
			}
			$query.=$line;
			if (substr($line, -1)==";") {
				$adapter->query($query);
				$query="";
			}
		}
	}
	if ($_POST['dbconfig']) {
		//write db
		$db=new Zend_Db();
		$adapter=$db->factory('pdo_mysql',array('username'=>$_POST['dbuser']
		,'host'=>$_POST['host']
		,'password'=>$_POST['dbpass']
		,'dbname'=>$_POST['dbname']
		));
		$adapter->beginTransaction();
		import("struttura.sql", $adapter);
		import("dati.sql", $adapter);
		$adapter->commit();
		$adapter->insert('site_users', array('username'=>$_POST['admin']
		,'password'=>sha1($_POST['adminpass'])
		,'email'=>$_POST['adminemail']
		,'active'=>1
		));
		$id=$adapter->lastInsertId('site_user');
		$adapter->insert('site_role', array('uid'=>$id
		,'role'=>'admin'
		));//*/
	}
	$conf=new Zend_Config_Ini('template.ini',null,true);
	$conf->development->resources->db->params->host=$_POST['host'];
	$conf->development->resources->db->params->username=$_POST['dbuser'];
	$conf->development->resources->db->params->password=$_POST['dbpass'];
	$conf->development->resources->db->params->dbname=$_POST['dbname'];
	$conf->development->evolution->local=$_POST['local'];
	$conf->development->evolution->debug=$_POST['debug'];
	$conf->development->evolution->email->validation=$_POST['email'];
	$conf->development->evolution->mobile=$_POST['mobile'];
	$conf->development->evolution->path=$_POST['path'];
	$conf->development->evolution->url=$_POST['url'];
	//print_r($_POST);
	$conf->production->evolution->js->file=$_POST['js'];
	$conf->production->evolution->css->file=$_POST['css'];
	$conf=$conf->toArray();
	//
	$conf['development : production']=compress_config($conf['production'],$conf['development']);
	unset($conf['development']);
	//print_r($conf);
	$writer=new Zend_Config_Writer_Ini(array('filename'=>'../application/configs/application.ini'));
	$writer->setConfig(new Zend_Config($conf));
	$text=$writer->render();
	//$text=file_get_contents('../application/configs/application.ini');
	$text=str_replace('"APPLICATION_PATH', 'APPLICATION_PATH "', $text);
	file_put_contents('../application/configs/application.ini', $text);
	chmod('../application/configs/application.ini', 'u+rw,g+rw');
	echo json_encode(array('bool'=>true,'text'=>$text));
}
catch (Exception $e) {

	echo json_encode(array('bool'=>false,'text'=>$e->getMessage().' query exec "'.$debugQuery.'" at line '.$e->getLine().'<pre>'.$e->getTraceAsString().'</pre>'));
	exit(1);
}