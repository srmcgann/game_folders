<?php
  require("functions.php");
  $userID=$_COOKIE['id'];
  $pass=mysqli_real_escape_string($link, $_COOKIE['session']);
  $sql="SELECT * FROM codegolfUsers WHERE id=$userID AND pass=\"$pass\"";
  $res=mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    ?>
      <div id="loggedInRightMenu">
        <div class="dropdown">
          <span onclick="location.href='<?php echo $baseURL.'/'.$name?>'" class="userMenu"><?php echo $name?></span>
          <img src='<?php echo $avatar?>' class="menuAvatar">
          <div class="dropdown-content">
            <p onclick="Preferences()" class="dropdownItem">Preferences</p>
            <p onclick="ChangePassword()" class="dropdownItem">Change Password</p>
            <p onclick="LogOut()" class="dropdownItem">Log Out</p>
          </div>
        </div>
      </div>
      <div id="header"></div>
    <?php
  }
?>
