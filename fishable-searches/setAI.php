<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

  //$tgt = "whitehot.000webhostapp.com";
  //$newval = "orbs2.000webhostapp.com";
  echo "processing...";

  require('db.php');
  //$sql = 'USE `id21265367_videodemos`';
  //mysqli_query($link, $sql);
  $sql = 'SHOW TABLES;';
  $res = mysqli_query($link, $sql);
  echo "number of tables: " . mysqli_num_rows($res) . "<br>";
  for($i = 0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    echo json_encode($row) . "<br>";
    $tableName = $row['Tables_in_id21284549_videodemos2'];
    $proceed = false;
    $sql = "SHOW COLUMNS FROM $tableName";
    $res2 = mysqli_query($link, $sql);
    for($j = 0; $j < mysqli_num_rows($res2); ++$j){
      $row2 = mysqli_fetch_assoc($res2);
      if($row2["Field"] == 'id') $proceed = true;
    }
    if($proceed){
      $sql = "ALTER TABLE `$tableName` MODIFY `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT, ADD INDEX (id);";
      mysqli_query($link, $sql);
      echo "setting field `id` to be auto-incremental @ $tableName<br>";
      echo $sql."<br><br>";
    }
  }
  echo "\n\n\ndone\n";
?>
