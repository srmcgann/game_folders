<?php 
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $comment = mysqli_real_escape_string($link, $data->{'comment'});
  $postID = mysqli_real_escape_string($link, $data->{'postID'});
  $sql = 'SELECT * FROM users WHERE name LIKE "'.$userName.'" AND passhash = "'.$passhash.'";';
  $res = mysqli_query($link, $sql);
  $success = false;
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    if($row['enabled']){
      $sql1=$sql = 'INSERT INTO gamesComments (text, postID, userID) VALUES("'.$comment.'", '.$postID.', '.$row['id'].')';
      mysqli_query($link, $sql);
      $success = true;
    }
  }

  if($success){
    $insertID = mysqli_insert_id($link);
    $sql = 'SELECT date FROM gamesComments WHERE id = ' . $insertID;
    $res = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($res);
    echo json_encode([$success, $insertID, $row['date'], $sql1]);
  } else {
    echo json_encode([$success]);
  }
?>
