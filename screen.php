<?php
$list = array();
$dp = opendir("./screen");
$i=0;
echo '<table ><tr>';
while ($file = readdir($dp)) {
	if ($i>5) {
		$i=0;
		echo '</tr><tr>';
	}
    if (preg_match("/.+(\.png|\.jpg|\.gif)/", $file))
    	echo '<td style="border:2px solid black;"><a href="./screen/'.$file.'" rel="lightbox[screen]"><img src="./screen/'.$file.'" alt="'.$file.'" width="100" heigth="100" /></a></td>';
    $i++;
}
echo '</tr></table>';
?>