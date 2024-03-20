<?php
  require('db.php');
  $sql = "SELECT * FROM orbsMirrors";
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $servers = [];
    for($i = 0; $i < mysqli_num_rows($res); ++$i){
      $row = mysqli_fetch_assoc($res);
      $servers[] = $row;
    }
    echo json_encode($servers);
  }else{
    echo '[false]';
  }
?>