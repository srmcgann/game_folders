<?php

  //ini_set('display_errors', 0);
  //ini_set('display_startup_errors', 0);
  error_reporting(0);
  //error_reporting(E_ERROR | E_PARSE);
  $db_pass  = 'passwordface';
  $port     = '3306';
  $db_host  = 'localhost';

  $db = $_GET['db'];
  $db_user = $_GET['user'];

  $link     = mysqli_connect($db_host,$db_user,$db_pass,$db,$port);

  $sql = 'SELECT * FROM tictactoeSessions';
  $res = mysqli_query($link, $sql);
  echo json_encode([!!mysqli_num_rows($res)]);
?>