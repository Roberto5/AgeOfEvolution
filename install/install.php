<?php
$text=file_get_contents("template.ini");
foreach ($_POST as $key => $value) {
	$text=str_replace('{'.$key.'}' ,$value, $text);
}
echo json_encode(array('bool'=>true,'text'=>$text));