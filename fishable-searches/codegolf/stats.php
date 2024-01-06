<?php
	require("db.php");
	
  $ret = '<div style="width: calc(100% - 15px); text-align: left; margin-left: 15px;">';

  $sql="SELECT id, userID, rating, votes, bytes FROM applets ORDER BY rating * votes DESC";
  $res=mysqli_query($link, $sql);
  $apps = [];
  if(mysqli_num_rows($res)){
    for($i = 0; $i < mysqli_num_rows($res); ++$i) {
      $row = mysqli_fetch_assoc($res);
      $sql = "SELECT name FROM codegolfUsers WHERE id = $row[userID]";
      $res2 = mysqli_query($link, $sql);
      $row2 = mysqli_fetch_assoc($res2);
      array_push($apps, [$row['id'], $row['bytes'], $row2['name'], round($row['rating']), $row['votes']]);
    }
    $ret .= "<span style=\"font-size:24px;\">Most Popular Demos</span><br>";
    $ret .= "<table style=\"width: calc(100% - 40px);margin-left: 20px;border-collapse:collapse;font0size:0.7em;margin-top:10px;\"><tr><th></th><th style=\"border:1px solid #8888\">pop</th><th style=\"border:1px solid #8888\">votes</th></tr>";
    $top = 0;
    foreach ($apps as $key => $val) {
        $top++;
        if($top < 7) $ret .= "<tr><td style=\"border:1px solid #8888\"><a href=\"/codegolf/?params=/a/$val[0]\">#$val[0]</a></td><td style=\"border:1px solid #8888\">$val[3]%</td><td style=\"border:1px solid #8888\">$val[4]</td></tr>";
    }
    $ret .= "</table>";
  }
  $ret .= "<br><br>";

  $sql="SELECT id, name, rating FROM codegolfUsers ORDER BY rating DESC";
  $res=mysqli_query($link, $sql);
  $codegolfUsers = [];
  if(mysqli_num_rows($res)){
    for($i = 0; $i < mysqli_num_rows($res); ++$i) {
      $row = mysqli_fetch_assoc($res);
      $sql = "SELECT COUNT(id) FROM votes WHERE userID = $row[id]";
      $res2 = mysqli_query($link, $sql);
      $row2 = mysqli_fetch_assoc($res2);
      $count = $row2['COUNT(id)'];
      $codegolfUsers[$row['name']] = (1 + $count / 8000)  * $row['rating'];
    }
    $ret .= "<span style=\"font-size:24px;\">Most Popular users</span><br>";
    arsort($codegolfUsers);
    $top = 0;
    foreach ($codegolfUsers as $key => $val) {
        $top++;
        if($top < 7) $ret .= "&nbsp;&nbsp;$top) <a href=\"/codegolf/?params=/$key\">$key</a><br>";
    }
  }
  $ret .= "<br><br>";

  $sql="SELECT userID FROM applets";
  $res=mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $codegolfUsers = [];
    for($i = 0; $i < mysqli_num_rows($res); ++$i) {
      $row = mysqli_fetch_assoc($res);
      if(isset($codegolfUsers['USER'.$row['userID']]) === false) $codegolfUsers['USER'.$row['userID']] = 0;
      $codegolfUsers['USER'.$row['userID']]++;
    }
    arsort($codegolfUsers);
    $top = 0;
    $ret .= "<span style=\"font-size:24px\">Most demos</span><br>";

    foreach($codegolfUsers as $key => $val) {
      if($top < 6) {
        $uid = substr($key, 4);
        $sql = "SELECT name FROM codegolfUsers WHERE id = $uid";
        $res2 = mysqli_query($link, $sql);
        $row2 = mysqli_fetch_assoc($res2);
        $ret .= "
          &nbsp;&nbsp;$val <a href=\"/codegolf/?params=/$row2[name]\">$row2[name]</a><br>
        ";
      }
      $top++;
    }
  }
  $ret .= '</div>';
  echo $ret;
?>