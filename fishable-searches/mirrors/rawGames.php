<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  require('db.php');

  $gamesList = [];
  $sql = "SELECT * FROM arenaGames";
  $res = mysqli_query($link, $sql);
  for($i=0; $i<mysqli_num_rows($res);++$i){
    $gamesList[] = mysqli_fetch_assoc($res);
  }

  $sql = "SELECT unix_timestamp()";
  $res = mysqli_query($link, $sql);
  $row = mysqli_fetch_assoc($res);
  $time = intval($row['unix_timestamp()']);

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
  
  function status($row){
    $port_     = '3306';
    $db_       = $row['cred'];
    $db_user_  = $row['user'];
    $db_host_  = $row['server'];
    $db_pass_  = $row['pass'];
    $link_     = mysqli_connect($db_host_,$db_user_,$db_pass_,$db_,$port_);
    $sql_      = "SELECT * FROM orbsMirrors";
    $res_      = mysqli_query($link_, $sql_);
    $ret       = !!$res_;
    mysqli_close($link_);
    return $ret;
  }

  $sql = "SELECT * FROM orbsMirrors";
  $res = mysqli_query($link, $sql);
  
  if(mysqli_num_rows($res)){
    $liveCount = 0;
    $servers = [];
    $states = [];
    for($i = 0; $i < mysqli_num_rows($res); ++$i){
      $row = mysqli_fetch_assoc($res);
      if(!!intval($row['active'])){
        $servers[] = $row;
        $serverState = status($row);
        if($serverState) $liveCount++;
        $states[] = $serverState;
      }
    }
    
    if($liveCount){
      
      $liveGames = [];
      for($idx = 0; $idx < sizeof($servers); ++$idx){
        if($states[$idx]){
          $server        = $servers[$idx];
          $game_db_user  = $server['user'];
          $game_db_pass  = $server['pass'];
          $game_db_host  = $server['server'];
          $game_db       = $server['cred'];
          $game_port     = '3306';
          $game_link     = mysqli_connect($game_db_host,$game_db_user,$game_db_pass,$game_db,$game_port);
          forEach($gamesList as $game){
            $gameDB = $game['gameDB'];
            $sql = "SELECT * FROM " . $gameDB;
            $res = mysqli_query($game_link, $sql);
            $slug = '';
            if(mysqli_num_rows($res)){
              $row = mysqli_fetch_assoc($res);
              $lastUpdate = 0;
              $numberPlayers = 0;
              $gameFull = false;
              $tData = '';
              $gameMaster = '';
          
              $icon = $game['linkThumb'];
              $data = $row;
              if($data['data']){
                $gameID = $data['id'];
                $slug = decToAlpha($gameID);
                $sessionsDB = $game['sessionsDB'];
                $sql = "SELECT name, id FROM $sessionsDB WHERE gameID = $gameID";
                $res2 = mysqli_query($game_link, $sql);
                if(mysqli_num_rows($res2)){
                  $row2 = mysqli_fetch_assoc($res2);
                  $gameMaster = $row2['name'];
                  $gmid = $row2['id'];
                }else{
                  $gameMaster = '[absent]';
                }
                $tData = json_decode($data['data']);
                $players = $tData->{'players'};
                $numberPlayers = 0;
                $lastUpdate = 0;
                forEach($players as $player){
                  forEach($player as $key2=>$val2){
                    if($key2 == 'time'){
                      $numberPlayers++;
                      $lu = intval($val2);
                      if($lu > $lastUpdate) $lastUpdate = $lu;
                    }
                  }
                }
              }
              if($numberPlayers >= $game['maxPlayers']) $gameFull = true;
              $gameDir = $game['gameDir'];
              $gameLink = $server['actualURL'] . "$gameDir/g/?g=$slug&gmid=$gmid";
                  
                // old tetris
                /*$icon = '1XXD2f.png';
                  $data = $row;
                  $gameID = $row['id'];
                  $slug = decToAlpha($gameID);
                  if($data['gamedataA']){
                    $numberPlayers++;
                    $tDataA = json_decode($data['gamedataA']);
                    $gameMaster = $tDataA->{'playerName'};
                    $lu = $tDataA->{'lastUpdate'};
                    if($lu > $lastUpdate) $lastUpdate = $lu;
                  }
                  if($data['gamedataB']){
                    $numberPlayers++;
                    $tDataB = json_decode($data['gamedataB']);
                    $lu = $tDataB->{'lastUpdate'};
                    if($lu > $lastUpdate) $lastUpdate = $lu;
                  }
                  if($data['gamedataC']){
                    $numberPlayers++;
                    $tDataC = json_decode($data['gamedataC']);
                    $lu = $tDataC->{'lastUpdate'};
                    if($lu > $lastUpdate) $lastUpdate = $lu;
                  }
                  if($data['gamedataD']){
                    $numberPlayers++;
                    $tDataD = json_decode($data['gamedataD']);
                    $lu = $tDataD->{'lastUpdate'};
                    if($lu > $lastUpdate) $lastUpdate = $lu;
                  }
                  if($numberPlayers >= 4) $gameFull = true;
                  $gameLink = $server['actualURL'] . "trektris?i=/game/$slug/";*/

              $diff = $time - $lastUpdate;
              if($diff>60){
                $sql = "DELETE FROM $gameDB WHERE id = $gameID";
                mysqli_query($game_link, $sql);
              }else{
                if($numberPlayers && $lastUpdate){
                  $liveGames[] = [
                    'icon'          => $icon,
                    'game'          => $game['name'],
                    'OP'            => $gameMaster,
                    'users'         => $numberPlayers,
                    //'lastUpdate'    => $lastUpdate,
                    //'time'          => $time,
                    'diff'          => $diff,
                    //'gameID'        => $gameID,
                    //'server'        => $server['topURL'],
                    'gameFull'      => $gameFull,
                    'gameLink'      => $gameLink,
                    //'slug'          => $slug,
                    //'sizeof($servers)' => sizeof($servers),
                    //'idx'           => $idx
                  ];
                }
              }
            }
          }
          mysqli_close($game_link);
        }
      }
    }
    forEach($servers as &$server){
      unset($server['pass']);
    }
    echo json_encode(["liveGames"=>$liveGames, "servers"=>$servers]);
  }else{
    echo '[false]';
  }
?>