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
    	echo '<td style="border:2px solid black;"><a href="./screen/'.$file.'" class="tumb"><img src="./screen/'.$file.'" alt="'.$file.'" width="100" heigth="100" /></a></td>';
    $i++;
}
echo '</tr></table>';
?>
<script type="text/javascript">

$(function (){
	$("a.tumb").lightBox({fixedNavigation:true});
});

</script>