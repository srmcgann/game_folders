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
  
  require_once('../db.php');
  require_once('../functions.php');
  $data = json_decode(file_get_contents('php://input'));
  $gameID = mysqli_real_escape_string($link, $data->{'gameID'});
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $userID = mysqli_real_escape_string($link, $data->{'userID'});
  $gmid = mysqli_real_escape_string($link, $data->{'gmid'});

  $success = false;
  
  function process($checkExisting = false){
    global $link, $sql, $userName, $userID, $slug, $res;
    global $gameID, $success, $msg, $gmid, $gidx, $data;
    $sql = "SELECT data FROM platformGames WHERE id = $gidx";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $success = true;
      $row = mysqli_fetch_assoc($res);
      if($checkExisting){
        $data = json_decode($row['data']);
        //if(!isset($data->{'players'}->{$userID})) die();
        $msg = "re-joined game as: $userName, with slug: $slug (id=$gidx)";
      }else{
        $data = mysqli_real_escape_string($link, newUserJSON2($userName, $userID, json_decode($row['data'])));
        $sql = "UPDATE platformGames SET data = \"$data\" WHERE id = $gameID";
        mysqli_query($link, $sql);
        $msg = "joined game as: $userName, with slug: $slug (id=$gidx)";
      }
      $slug = decToAlpha($gidx);
      echo json_encode([$success, $slug, $msg, $gmid, $userID]);
    }else{
      echo json_encode([$success, 'fail', $sql]);
      die();
    }
  }
  
  if(isset($gameID) && $gameID){
    $gidx = $gameID;
    if($userID){
      $sql = "SELECT * FROM platformSession WHERE id = $userID";
      $res = mysqli_query($link, $sql);
      if(mysqli_num_rows($res)){
        process(true);
      }
    }else{
      $sanitizedName = mysqli_real_escape_string($link, $userName);
      $sql = "INSERT INTO platformSessions (name, data, gameID) VALUES(\"$sanitizedName\", \"[]\", $gidx)";
      mysqli_query($link, $sql);
      $userID = mysqli_insert_id($link);
      process(false);
    }
  }else{
    echo json_encode([$success, 'fail', $sql]);
    die();
  }
?>