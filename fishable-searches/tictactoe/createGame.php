<?php
  function alphaToDec($val){
    $pow=0;
    $res=0;
    while($val!=""){
      $cur=$val[strlen($val)-1];
      $val=substr($val,0,strlen($val)-1);
      $mul=ord($cur)<58?$cur:ord($cur)-(ord($cur)>96?87:29);
      $res+=$mul*pow(62,$pow);
      $pow++;
    }
    return $res;
  }

  function decToAlpha($val){
    $alphabet="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $ret="";
    while($val){
      $r=floor($val/62);
      $frac=$val/62-$r;
      $ind=(int)round($frac*62);
      $ret=$alphabet[$ind].$ret;
      $val=$r;
    }
    return $ret==""?"0":$ret;
  }
  
  require_once('db.php');
  require_once('functions.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});

  // ->maintenance
  // purge any player records that have not been updated in over 10 minutes
  for($i=2; $i--;){
    $table = '';
    switch($i){
      case 0: $table = 'tictactoeSessions'; break;
      //case 1: $table = 'tictactoeGames'; break;
    }
    if($table){
      $sql = "DELETE FROM $table WHERE TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP, date)) >= 600;";
      $res = mysqli_query($link, $sql);
    }
  }

  // purge any game records older than 1 day
  for($i=2; $i--;){
    $table = '';
    switch($i){
      //case 0: $table = 'tictactoeSessions'; break;
      case 1: $table = 'tictactoeGames'; break;
    }
    if($table){
      $sql = "DELETE FROM $table WHERE TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP, date)) >= 86400;";
      $res = mysqli_query($link, $sql);
    }
  }
  // /maintenance

  $success = false;
  
  $ct = 0;
  do{
    $gidx = 1e9 + (rand()%1e8);
    $sql = "SELECT id FROM tictactoeGames WHERE id = $gidx";
    $res = mysqli_query($link, $sql);
    $good = mysqli_num_rows($res) == 0;
  }while(!$good && $ct<1e3);
  
  if($ct<1e3){
    $sanitizedName = mysqli_real_escape_string($link, $userName);
    $sql = "INSERT INTO tictactoeSessions (name, data, gameID) VALUES(\"$sanitizedName\", \"[]\", $gidx)";
    mysqli_query($link, $sql);
    $userID = mysqli_insert_id($link);
    $data = mysqli_real_escape_string($link, newUserJSON($userName, $userID));
    $sql = "INSERT INTO tictactoeGames (data, id) VALUES(\"$data\", $gidx)";
    mysqli_query($link, $sql);
    $success = true;
    $slug = decToAlpha($gidx);
    $msg = "created game for: $userName, with slug: $slug (id=$gidx)";
    echo json_encode([$success, $slug, $msg, $userID, $sql]);
  }else{
    echo json_encode([$success, 'fail', $sql]);
    die();
  }
?>