<?php
  require_once('db.php');  

  $sql = "SELECT unix_timestamp()";
  $res = mysqli_query($link, $sql);
  $row = mysqli_fetch_assoc($res);
  $time = $row['unix_timestamp()'];

  function newUserJSON($userName, $userID, $data=[]){
    $data['players'] = [];
    $data['players'][$userID] = [];
    $data['players'][$userID]['name'] = $userName;
    $data['players'][$userID]['time'] = $time;
    $data['collected'] = [];
    return json_encode($data);
  }
  function newUserJSON2($userName, $userID, $data=[]){
    $data->{'players'}->{$userID} = [];
    $data->{'players'}->{$userID}['name'] = $userName;
    $data->{'players'}->{$userID}['time'] = $time;
    $data->{'collected'} = [];
    return json_encode($data);
  }
?>
