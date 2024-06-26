<?php 
  require('db.php');
  function alphaToDec($val){
    $pow=0;
    $res=0;
    while($val!=""){
      $cur=$val[strlen($val)-1];
      $val=substr($val,0,strlen($val)-1);
      $mul=ord($cur)<58?$cur:ord($cur)-(ord($cur)>96?87:29);
      $res+=intval($mul)*pow(62,$pow);
      $pow++;
    }
    return intval($res);
  }

  if(isset($_GET['i'])){
    $HTML=$CSS=$JS='';
    $demoslug = $_GET['i'];
    //$demoslug = explode('?i=',$_GET['i']);
    //$demoslug = $demoslug[sizeof($demoslug)-1];
    $demoid = alphaToDec($demoslug);
    $sql = 'SELECT * FROM items WHERE id = ' . $demoid;
    if($res = mysqli_query($link, $sql)){
      $row = mysqli_fetch_assoc($res);
      $userID = $row['userID'];
      $sql2 = 'SELECT * FROM users WHERE id = ' . $userID;
      $res2 = mysqli_query($link, $sql2);
      $row2 = mysqli_fetch_assoc($res2);
      if(!$row['private'] || ($res2 && ((isset($_COOKIE['token']) && $_COOKIE['token'] == $row2['passhash']) || (isset($_COOKIE['token']) && $row2['admin'])))){
        $HTML = $row['demoHTML'];
        $CSS = $row['demoCSS'];
        $JS = $row['demoJS'];
        $title = $row['title'];
        if($res2){
          $favicon = $row2['avatar'];
        } else {
          $favicon = 'https://jsbot.twilightparadox.com/1HVS37.png';
        }
      } else {
        echo '404';
        die();
      }
    } else {
      echo '404';
      die();
    }
  } else {
    echo '404';
    die();
  }

?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $title?></title>
    <link rel="icon" href="<?php echo $favicon?>" type="image/png">
    <style>
      <?php echo $CSS?>

      .homeLink{
        z-index: 10000;
        position: fixed;
        background: #2466;
        cursor: pointer;
        font-size: 12px;
        color: #fff8;
        top: 5px;
        right: 5px;
        border-radius: 3px;
        border: 1px solid #4688;
      }
      .homeLink:focus{
        outline: none;
      }
    </style>
  </head>
  <body>
    <?php echo $HTML?>
    <script>
      let sendData = {demoID: <?php echo $demoid?>}
      let url = '<?php echo $baseURL?>/incrementViews.php'
      fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(sendData),
      })
    </script>
    <script><?php echo $JS?></script>
  </body>
</html>
