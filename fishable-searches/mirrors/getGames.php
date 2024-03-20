<?php
  require('db.php');
  $gamesList = [];
  $sql = "SELECT * FROM arenaGames";
  $res = mysqli_query($link, $sql);
  for($i=0; $i<mysqli_num_rows($res);++$i){
    $gamesList[] = mysqli_fetch_assoc($res);
  }
  echo json_encode($gamesList);
?>