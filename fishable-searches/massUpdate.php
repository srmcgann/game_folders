<?php
  error_reporting(E_ALL);
  $db_user  = 'id21552617_user';
  $db_pass  = 'passwordface';
  $db_host  = 'localhost';
  $db       = "id21552617_orbs2";
  $port     = '3306';
  $link     = mysqli_connect($db_host,$db_user,$db_pass,$db,$port);
  
  $sql = "SELECT * FROM orbsMirrors";
  $res = mysqli_query($link, $sql);
  for($i=0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    $db = $row['cred'];
    $user = $row['user'];
    $pass = 'passwordface';
    $port = 3306;
    $host = 'localhost';
    $link2 = mysqli_connect($host, $user, $pass, $db, $port);
    $sql = "UPDATE orbsMirrors SET active = 0 WHERE id = 7";
    mysqli_query($link2, $sql);
    mysqli_close($link2);
    echo 'setting id:' . $row['id'] . '<br>';
  }
  echo "<br><br>done."
?>
