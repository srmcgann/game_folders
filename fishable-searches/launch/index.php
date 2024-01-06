<?php
  require('db.php');
  
  function status($row){
    $port_     = '3306';
    $db_       = $row['cred'];
    $db_user_  = $row['user'];
    $db_host_  = $row['server'];
    $db_pass_  = $row['pass'];
    $link_     = mysqli_connect($db_host_,$db_user_,$db_pass_,$db_,$port_);
    $sql_      = 'SELECT * FROM orbsMirrors';
    $res_      = mysqli_query($link_, $sql_);
    mysqli_close($link_);
    return !!mysqli_num_rows($res_);
  }
  
  $sql = "SELECT * FROM orbsMirrors";
  $res = mysqli_query($link, $sql);
  
  if(mysqli_num_rows($res)){
    $servers = [];
    for($i = 0; $i < mysqli_num_rows($res); ++$i){
      $row = mysqli_fetch_assoc($res);
      if($row['active'] && status($row)) $servers[] = $row;
    }
  }else{
    echo '[false]';
  }

  $pathname = explode('?',$_SERVER['REQUEST_URI']);
  if(sizeof($pathname)>1){
    $gamesel = explode('&', explode('gamesel=', $pathname[1])[1])[0];
    //echo $gamesel . "<br>";
    switch($gamesel){
      case 'tictactoe':
        $serverList = $servers;
        $tgturl = $serverList[rand()%sizeof($serverList)]['actualURL'].$gamesel;
      break;
      case 'tetris':
        $serverList = $servers;
        $tgturl = $serverList[rand()%sizeof($serverList)]['actualURL'].$gamesel;
      break;
      case 'orbs':
        $serverList = $servers;
        $tgturl = $serverList[rand()%sizeof($serverList)]['actualURL'].$gamesel;
      break;
      case 'sidetoside':
        $serverList = $servers;
        $tgturl = $serverList[rand()%sizeof($serverList)]['actualURL'].$gamesel;
      break;
      case 'puyopuyo':
        $serverList = $servers;
        $tgturl = $serverList[rand()%sizeof($serverList)]['actualURL'].$gamesel;
      break;
      case 'battleracer':
        $serverList = $servers;
        $tgturl = $serverList[rand()%sizeof($serverList)]['actualURL'].$gamesel;
      break;
    }
    echo "<meta http-equiv=\"refresh\" content=\"0,$tgturl\">";
    //echo json_encode($servers);
 }else{
    echo '[false]';
  }
?>