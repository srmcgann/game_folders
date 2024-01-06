<?php
  require_once('../db.php');
  $data = json_decode(file_get_contents('php://input'));
  $gameID = mysqli_real_escape_string($link, $data->{'gameID'});
  $userID = mysqli_real_escape_string($link, $data->{'userID'});
  //$collected = $data->{'collected'};
  $individualPlayerData = $data->{'individualPlayerData'};
  
  $igt = gettype($individualPlayerData);
  $has = false;
  switch($igt){
    case 'array':
      $has = array_key_exists('name', $individualPlayerData);
    break;
    case 'object':
      $has = property_exists($individualPlayerData, 'name');
    break;
  }
  
  $success = false;

  $sql = "SELECT unix_timestamp()";
  $res = mysqli_query($link, $sql);
  $row = mysqli_fetch_assoc($res);
  $time = $row['unix_timestamp()'];

  $sql = "SELECT * FROM battleracerGames WHERE id = $gameID";
  $res = mysqli_query($link, $sql);

  $data = '';
  $newData = '';
  $needsReg = false;
  if(mysqli_num_rows($res)){
    $success = true;
    $row = mysqli_fetch_assoc($res);
    $data = json_decode($row['data']);
    /*
    $colSize = sizeof($collected);
    $existingSize = sizeof($data->{'collected'});
    $maxSize = max($colSize, $existingSize);
    $newCol = [];
    for($i = 0; $i<$maxSize; $i++){
      if(
        ($i < $colSize && $collected[$i] === '1') ||
        ($i < $existingSize && $data->{'collected'} === '1')
      ){
        $newCol[]= '1';
      }else{
        $newCol[]= '0';
      }
    }
    $data->{'collected'} = $newCol;
    */
    forEach($data->{'players'} as $key=>$player){
      // player drops if unseen for 10 seconds
      if(isset($player->{'time'}) && ($time - $player->{'time'} > 10)){
        unset($data->{'players'}->{$key});
      }
    }
    if($userID){
      if($has){
        if($time - $individualPlayerData->{'time'} < 60){ // player may reconnect for up to a minute
          $individualPlayerData->{'time'} = $time;
          $data->{'players'}->{$userID} = (object)$individualPlayerData;
        }
      }
      if($userID && $data->{'players'}->{$userID}){
        if($has){
          forEach($individualPlayerData as $key=>$val){
            $data->{'players'}->{$userID}->{$key} = $val;
          }
        }
        $data->{'players'}->{$userID}->{'time'} = $time;
        $newData = mysqli_real_escape_string($link, json_encode($data));
        $sql = "UPDATE battleracerGames SET data = \"$newData\" WHERE id = $gameID";
        mysqli_query($link, $sql);
        $needsReg = false;
      }else{
        $e = 1;
        $needsReg = true;
      }
    }else{
      $e = 2;
      $needsReg = true;
    }
  }

  echo json_encode([$success, $data, $needsReg, $userID, $newData, $individualPlayerData, $has, $igt]);
?>