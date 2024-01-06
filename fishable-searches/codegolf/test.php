<?php
  $params = explode('/',  $_SERVER['REQUEST_URI']);
  if(sizeof($params)>3){
    for($i=3;$i--;)array_shift($params);
  }
  echo json_encode($params);
?>