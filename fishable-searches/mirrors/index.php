<?php
  require('db.php');

  $sql = "SELECT unix_timestamp()";
  $res = mysqli_query($link, $sql);
  $row = mysqli_fetch_assoc($res);
  $time = $row['unix_timestamp()'];

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
      
      $gameTables = [
        'tic tac toe'  => 'tictactoeGames',
        'tetris'       => 'tetrisGames',
        'orbs'         => 'platformGames',
        'side to side' => 'sideToSideGames',
        'puyopuyo'     => 'puyopuyoGames',
        'battleracer'     => 'battleracerGames',
      ];
      
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
          forEach($gameTables as $key=>$val){
            $sql = "SELECT * FROM " . $val;
            $res = mysqli_query($game_link, $sql);
            $slug = '';
            if(mysqli_num_rows($res)){
              $row = mysqli_fetch_assoc($res);
              $lastUpdate = 0;
              $numberPlayers = 0;
              $gameFull = false;
              $tData = '';
              $gameMaster = '';
              switch($key){
                case 'tic tac toe':
                  $icon = 'tictactoe.png';
                  $data = $row;
                  if($data['data']){
                    $gameID = $data['id'];
                    $slug = decToAlpha($gameID);
                    $sql = "SELECT name, id FROM tictactoeSessions WHERE gameID = $gameID";
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
                          $lu = $val2;
                          if($lu > $lastUpdate) $lastUpdate = $lu;
                        }
                      }
                    }
                  }
                  if($numberPlayers >= 2) $gameFull = true;
                  $gameLink = $server['actualURL'] . "tictactoe/g/?g=$slug&gmid=$gmid";
                break;
                case 'tetris':
                  $icon = '1XXD2f.png';
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
                  $gameLink = $server['actualURL'] . "tetris?i=/game/$slug/";
                break;
                case 'orbs':
                  $icon = 'burst.png';
                  $data = $row;
                  if($data['data']){
                    $gameID = $data['id'];
                    $slug = decToAlpha($gameID);
                    $sql = "SELECT name, id FROM platformSessions WHERE gameID = $gameID";
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
                          $lu = $val2;
                          if($lu > $lastUpdate) $lastUpdate = $lu;
                        }
                      }
                    }
                  }
                  if($numberPlayers >= 4) $gameFull = true;
                  $gameLink = $server['actualURL'] . "orbs/%CE%94/?g=$slug&gmid=$gmid";
                break;
                case 'side to side':
                  $icon = 'sideToSideThumb.png';
                  $data = $row;
                  if($data['data']){
                    $gameID = $data['id'];
                    $slug = decToAlpha($gameID);
                    $sql = "SELECT name, id FROM sideToSideSessions WHERE gameID = $gameID";
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
                          $lu = $val2;
                          if($lu > $lastUpdate) $lastUpdate = $lu;
                        }
                      }
                    }
                  }
                  if($numberPlayers >= 2) $gameFull = true;
                  $gameLink = $server['actualURL'] . "sidetoside/g/?g=$slug&gmid=$gmid";
                break;
                case 'puyopuyo':
                  $icon = 'puyopuyoThumb.png';
                  $data = $row;
                  if($data['data']){
                    $gameID = $data['id'];
                    $slug = decToAlpha($gameID);
                    $sql = "SELECT name, id FROM puyopuyoSessions WHERE gameID = $gameID";
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
                          $lu = $val2;
                          if($lu > $lastUpdate) $lastUpdate = $lu;
                        }
                      }
                    }
                  }
                  if($numberPlayers >= 2) $gameFull = true;
                  $gameLink = $server['actualURL'] . "puyopuyo/g/?g=$slug&gmid=$gmid";
                break;
                case 'battleracer':
                  $icon = 'battleracerThumb.png';
                  $data = $row;
                  if($data['data']){
                    $gameID = $data['id'];
                    $slug = decToAlpha($gameID);
                    $sql = "SELECT name, id FROM battleracerSessions WHERE gameID = $gameID";
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
                          $lu = $val2;
                          if($lu > $lastUpdate) $lastUpdate = $lu;
                        }
                      }
                    }
                  }
                  if($numberPlayers >= 2) $gameFull = true;
                  $gameLink = $server['actualURL'] . "battleracer/g/?g=$slug&gmid=$gmid";
                break;
              }
              $diff = $time - $lastUpdate;
              if($diff>60){
                $sql = "DELETE FROM $val WHERE id = $gameID";
                mysqli_query($game_link, $sql);
              }else{
                if($numberPlayers && $lastUpdate){
                  $liveGames[] = [
                    'icon'          => $icon,
                    'game'          => $key,
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
    
    $servers = json_encode($servers);
  }else{
    echo '[false]';
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>ARENA mirrors [updated: 11/27/23]</title>
    <style>
      /* latin-ext */
      @font-face {
        font-family: 'Courier Prime';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url(https://fonts.gstatic.com/s/courierprime/v9/u-450q2lgwslOqpF_6gQ8kELaw9pWt_-.woff2) format('woff2');
        unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
      }
      /* latin */
      @font-face {
        font-family: 'Courier Prime';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url(https://fonts.gstatic.com/s/courierprime/v9/u-450q2lgwslOqpF_6gQ8kELawFpWg.woff2) format('woff2');
        unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
      }
      body, html{
        margin: 0;
        background: #000;
        color: #fff;
        font-family: Courier Prime;
        overflow-x: hidden;
        min-height: 100vh;
      }
      #title {
        padding: 20px;
        background: linear-gradient(90deg,#600, #000, #000);
        color: #fff;
        font-size: 3em;
        position: fixed;
        display: block;
        width: 100%;
        max-height: 46px;
        text-shadow: 3px 3px 3px #000;
        z-index: 1000;
      }
      .link{
        padding: 1px;
        border: none;
        display: inline-block;
        min-width: 300px;
        font-size: 1.25em;
        background: #40f8;
        color: #fff;
        text-decoration: none;
        margin: 20px;
        margin-bottom: 0;
        border: 10px solid #4f82;
        border-radius: 20px;
      }
      .gameLink{
        padding: 1px;
        border: none;
        display: inline-block;
        width: 300px;
        font-size: 1.25em;
        color: #fff;
        text-align: left;
        text-decoration: none;
        margin: 20px;
        margin-bottom: 0;
        border: 10px solid #48f2;
        border-radius: 20px;
      }
      .gameLinkItem{
        margin: 0px;
        display: block;
        color: #fff;
        display: inline-block;
      }
      #mirrorList{
        text-align: center;
        background: #111;
        position: absolute;
        right: 0;
        width: 50%;
        margin-top: 86px;
        min-height: calc(100vh - 86px);
      }
      #gamesList{
        text-align: center;
        background: linear-gradient(90deg, #400,#000);
        position: absolute;
        left: 0;
        width: 50%;
        margin-top: 86px;
        min-height: calc(100vh - 86px);
      }
      .caption{
        color: #888;
        font-size: .6em;
        margin-left: 20px;
        float: left;
      }
      .msgText{
        display: block;
        text-align: center;
        padding-top: 25px;
        font-size: 2em;
      }
      .statusDiv{
        background-size: 25px 25px;
        background-position: 5px center;
        background-repeat: no-repeat;
        display: inline-block;
        width: 260px;
        height: 30px;
        margin-bottom: 10px;
        padding-top: 9px;
      }
      .statusText{
        margin-left: 25px;
      }
      .gameLinkTitle{
        font-size: 2em;
      }
      .gameLinkIcon{
        width: 100px;
        height: 100px;
        float: right;
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        margin-top: 10px;
      }
    </style>
  </head>
  <body>
    <div id="title">ARENA GAMES</div>
    <div id="gamesList">
      <div class="msgText">
        LIVE GAMES
      </div>
    </div>
    <div id="mirrorList">
      <div class="msgText">
        SERVER STATUS
      </div>
    </div>
    <script>
      alphaToDec = val => {
        let pow=0
        let res=0
        let cur, mul
        while(val!=''){
          cur=val[val.length-1]
          val=val.substring(0,val.length-1)
          mul=cur.charCodeAt(0)<58?cur:cur.charCodeAt(0)-(cur.charCodeAt(0)>96?87:29)
          res+=mul*(62**pow)
          pow++
        }
        return res
      }
      decToAlpha = n => {
        let alphabet='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
        let ret='', r
        while(n){
          ret = alphabet[Math.round((n/62-(r=n/62|0))*62)|0] + ret
          n=r
        }
        return ret == '' ? '0' : ret
      }
      
      liveGames = JSON.parse('<?=json_encode($liveGames)?>')
      
      liveGames.sort((a,b)=>a.diff - b.diff)
      
      br = () => document.createElement('br')

      mirrorList = document.querySelector('#mirrorList')
      gamesList = document.querySelector('#gamesList')
      
      if(liveGames.length){
        liveGames.map((liveGame, idx) => {
          let container = document.createElement('div')
          container.className = 'gameLink'
          Object.entries(liveGame).forEach(([key, value]) => {
            if(key != 'diff'){
              if(key == 'gameFull'){
                container.style.background = value ? '#602' : '#062'
                if(!value){
                  container.style.cursor = 'pointer'
                  container.title = 'JOIN GAME!'
                  container.onclick = () => {
                  }
                }
              }
              let el = document.createElement('div')
              if(key == 'icon'){
                el.className = 'gameLinkIcon'
                el.style.backgroundImage = `url(${value})`
                container.appendChild(el)
              }else{
                if(key == 'game'){
                  el.className = 'gameLinkItem gameLinkTitle'
                  el.innerHTML = `${value}`
                }else{
                  if(key!='gameFull' || (key=='gameFull' && value)){
                    el.className = 'gameLinkItem'
                    if(key == 'gameLink'){
                      el.innerHTML = `${key} : <a target="_blank" href="${value}">link</a>`
                    }else{
                      el.innerHTML = `${key} : ${value}`
                    }
                  }
                }
                container.appendChild(el)
                if(key!='gameFull') container.appendChild(br())
              }
            }
          })
          gamesList.appendChild(container)
          setTimeout(() => {
            if(mirrorList.clientHeight < gamesList.clientHeight){
              mirrorList.style.height = gamesList.clientHeight + 'px'
            }
          },0)
        })
      }else{
        gamesList.innerHTML = `
          <div class="msgText">
            LIVE GAMES<hr><br><br>
            NO GAMES FOUND!<br><br><br>
            maybe you should<br>
            create one!<br><br>
            click<br>
            "<b>ARENA</b>"<br>
            on any of the games<br>
            shown in the practice area
          </div>`
      }
      
      links = JSON.parse('<?=$servers?>')
      completed = Array(links.length).fill(false)
      els = Array(links.length).fill(v=>{return {el: '', status: false}})
      function genStatus (v, i) {
        fetch(`status.php?url=${v.topURL}&server=${v.server}&pass=${v.pass}&db=${v.cred}&user=${v.user}`).then(res=>res.text()).then(data=>{
          data = data ? JSON.parse(data) : [false]
          let el = document.createElement('a')
          el.style.pointerEvents = 'none'
          // clicking disabled   ^
          el.className = 'link'
          el.target = '_blank'
          topURL = v.topURL.split('://')[1]
          actualURL = v.actualURL.split('://')[1]
          el.innerHTML = `mirror ${i+1}<br><span class="caption">[${topURL}]</span><br><span class="caption">[${actualURL}]</span>`
          el.href = v.topURL
          els[i] = [el, data[0]]
          completed[i] = true
          if(completed.filter(v=>v).length == completed.length){
            els.sort((a,b)=>b[1]-a[1])
            els.map(el=>{
              let statusEl = document.createElement('div')
              statusEl.className = 'statusDiv'
              statusEl.style.backgroundImage = el[1] ? 'url(check.png)' : 'url(x.png)'
              statusEl.style.backgroundColor = el[1] ? '#084' : '#400'
              statusEl.style.color = el[1] ? '#0f8' : '#f00'
              statusEl.innerHTML = '<span class="statusText">' + (el[1] ? 'connected / online' : 'ruh roh / problems') + '</span>'
              br = document.createElement('br')
              el[0].appendChild(br)
              el[0].appendChild(statusEl)
              br = document.createElement('br')
              mirrorList.appendChild(el[0])
              mirrorList.appendChild(br)
              setTimeout(() => {
                if(gamesList.clientHeight < mirrorList.clientHeight){
                  gamesList.style.height = mirrorList.clientHeight + 'px'
                }
              },0)
            })
          }
        })
      }
      links.map((v, i) => {
        genStatus(v,i)
      })
      if(1)setTimeout(()=>{
        location.reload()
      }, 10000)
      
    </script>
  </body>
</html>