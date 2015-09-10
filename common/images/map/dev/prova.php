<html>
  <head>
    <title></title>
    <meta content="">
    <style></style>
  </head>
  <body><?php
  
  
  $x=5;$y=5;
  
for ($j=0;$j<$y;$j++) {
  echo "<div>";
  for ($i=0;$i<5;$i++) {
    echo '<img src="'.$_GET['src'].'" />';
  }
  echo "</div>";
}
  ?>
  
  
  
  </body>
</html>