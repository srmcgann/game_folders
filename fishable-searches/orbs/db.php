<?php 
  error_reporting(E_ERROR | E_PARSE);
  //$db_user  = 'id21269596_user';
  $db_user  = 'id21284549_user';
  //$db_user  = 'id21257390_user';
  //$db_user  = 'id21552617_user';
  //$db_user  = 'id21553412_user';
  //$db_user  = 'id21583283_user';
  $db_pass  = 'passwordface';
  $db_host  = 'localhost';
  //$db       = "id21269596_videodemos";
  $db       = "id21284549_videodemos2";
  //$db       = "id21257390_default";
  //$db       = "id21552617_orbs2";
  //$db       = "id21553412_orbs3";
  //$db       = "id21583283_orbs4";
  $port     = '3306';
  $link     = mysqli_connect($db_host,$db_user,$db_pass,$db,$port);
  $baseURL  = "https://fishable-searches.000webhostapp.com/delta";
?>
