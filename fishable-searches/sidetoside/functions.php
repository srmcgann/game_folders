<?php
  require_once('db.php');  


  function newUserJSON($userName, $userID, $data=[]){
    global $link;
    $sql = "SELECT unix_timestamp()";
    $res = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($res);
    $time = $row['unix_timestamp()'];
    
    $data['players'] = [];
    $data['players'][$userID] = [];
    $data['players'][$userID]['name'] = $userName;
    $data['players'][$userID]['time'] = $time;
    return json_encode($data);
  }
  function newUserJSON2($userName, $userID, $data=[]){
    global $link;
    $sql = "SELECT unix_timestamp()";
    $res = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($res);
    $time = $row['unix_timestamp()'];
    $data->{'players'}->{$userID} = [];
    $data->{'players'}->{$userID}['name'] = $userName;
    $data->{'players'}->{$userID}['time'] = $time;
    return json_encode($data);
  }
?>
