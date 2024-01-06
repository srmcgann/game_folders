<?php

  error_reporting(E_ERROR | E_PARSE);
  $port     = '3306';
  $db       = $_GET['db'];
  $db_pass  = $_GET['pass'];
  $db_user  = $_GET['user'];
  $db_host  = $_GET['server'];

  $link     = mysqli_connect($db_host,$db_user,$db_pass,$db,$port);

  $sql = 'SELECT * FROM orbsMirrors';
  $res = mysqli_query($link, $sql);
  echo json_encode([!!mysqli_num_rows($res)]);
?>