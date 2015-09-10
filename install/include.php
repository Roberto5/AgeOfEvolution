<?php
try {
	set_include_path(implode(PATH_SEPARATOR, array(
    realpath('../library'),
    get_include_path(),
)));
	include_once '../library/Zend/Config.php';
include_once '../library/Zend/Config/Ini.php';

$conf=new Zend_Config_Ini('template.ini');
$file=array();
//print_r($conf->production);
foreach ($conf->production->evolution->js->file as $value) {
	$file[]=array('name'=>$value,'type'=>'js');
}
foreach ($conf->production->evolution->css->file as $value) {
	$file[]=array('name'=>$value,'type'=>'css');
}
echo json_encode($file);}
catch (Exception $e) {
	echo $e;
}
?>