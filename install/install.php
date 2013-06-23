<?php
try {
	set_include_path(implode(PATH_SEPARATOR, array(
			realpath(dirname(__FILE__).'/../library/'),
			get_include_path(),
	)));
	include_once 'Zend/Db.php';
	include_once 'library/Zend/Validate.php';
	foreach ($_POST as $key=>$value) {
		$_POST[$key]=htmlentities($value);
	}
	$email = new Zend_Validate_EmailAddress();
	$alnum=new Zend_Validate_Alnum();
	if (!$email->isValid($_POST['adminemail']) 
			|| !$alnum->isValid($_POST['adminpass']) 
			|| !$alnum->isValid($_POST['admin']) 
			|| $_POST['adminpass']!=$_POST['pass2']) {
		echo json_encode(array('bool'=>false,'text'=>'form error'));
		exit(1);
	}
	function import($file,Zend_Db_Adapter_Abstract $adapter) {
		$all_lines = file($file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
		$query="";
		foreach($all_lines as $line) {
			if(substr($line, 0, 2) == "--") {
				if ($query) $adapter->query($query);
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
	$text=file_get_contents("template.ini");
	foreach ($_POST as $key => $value) {
		$text=str_replace('{'.$key.'}' ,$value, $text);
	}
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
	));
	echo json_encode(array('bool'=>true,'text'=>$text));
	$fp=fopen('../application/configs/application.ini', 'w');
	fclose($fp);
	$bool=file_put_contents('../application/configs/application.ini', $text);
}
catch (Exception $e) {
	echo json_encode(array('bool'=>false,'text'=>$e->getMessage().' query exec'.$query));
	exit(1);
}