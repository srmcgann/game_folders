<?php
  require("db.php");

  function drawNewAppletButton(){
    ?>
      <div id="landingDivContainer">
            <div id="landingDiv">
                    <div class="newAppletButton" id="newAppletButton">
                      <img src="" class="NewAppletButtonGraphic">
                      Create New Demo 
                    </div>
                  </div>
    </div>
                <script>
                  document.getElementsByClassName('NewAppletButtonGraphic')[0].src = 'plus.png'
                </script>
    <?php
  }
  
  function drawLoggedInMenu($name,$id,$email){
    $avatar="avatars/$id.jpg?".rand();
    ?>
                <div id="config-container">
                  <div id="preferencesScreen">
                          <div class="inputDiv">
                                  <table class="inputTable" style="width:82%">
                                          <tr><td></td><td><div id="emailAvailability"></div></td></tr>
                                          <tr>
                                                  <td class="inputLabel">Email</td>
                                                  <td class="inputCell"><input  onkeyup="validatePrefEmail('<?php echo $email?>')" onpaste="setTimeout(validatePrefEmail('<?php echo $email?>'),0)" type="email" id="email" value='<?php echo $email?>'></td>
                                          </tr>
                                  </table>
                                  <hr>
                                  <div id="image-cropper">
                                          <div class="cropit-preview" style="margin-left: auto; margin-right: auto;margin-top: 25px; margin-bottom: 15px;"></div>
                                          <input type="range" class="cropit-image-zoom-input" />
                                          <input type="file" class="cropit-image-input" />
                                  </div>
                                  <script>
                                          $('#image-cropper').cropit({
                                                  maxZoom:2,
                                                  imageState: {src: '<?php echo $avatar?>'}
                                          });
                                          document.querySelectorAll('.cropit-preview-image-container')[0].style.borderRadius = '50%'
                                  </script>
                                  <hr>
                                  <table class="submitTable">
                                          <tr>
                                                  <td><button onclick="SavePreferences()">Save</button></td>
                                                  <td><button onclick="CancelPreferences()">Cancel</button></td>
                                          </tr>
                                  </table>
                          </div>
                  </div>

                  <div id="changePasswordScreen">
                          <div class="inputDiv">
                                  <table class="inputTable">
                                          <tr><td></td><td><div id="passwordConsistency"></div></td></tr>
                                          <tr>
                                                  <td class="inputLabel">New Password</td>
                                                  <td class="inputCell"><input onkeyup="validatePasswords()" onpaste="setTimeout(validatePasswords,0)" type="password" id="password"></td>
                                          </tr>
                                          <tr>
                                                  <td class="inputLabel">Confirm Password</td>
                                                  <td class="inputCell"><input onkeyup="validatePasswords()" onpaste="setTimeout(validatePasswords,0)" type="password" id="confirmpassword"></td>
                                          </tr>
                                  </table>
                                  <table class="submitTable">
                                          <tr>
                                                  <td><button onclick="SubmitNewPassword()">Save</button></td>
                                                  <td><button onclick="CancelNewPassword()">Close</button></td>
                                          </tr>
                                  </table>
                          </div>
                  </div>
                </div>
                <script>bindLoggedInEnterKey()</script>
                <div id="loggedInRightMenu">
                  <div class="dropdown">
                    <img src='<?php echo $avatar?>' class="menuAvatar"><br>
                    <span onclick="location.href='<?php echo $baseURL.'/?params=/'.$name?>'" class="userMenu"><?php echo $name?></span>
                    <div class="dropdown-content">
                      <p onclick="Preferences()" class="dropdownItem">Preferences</p>
                      <p onclick="ChangePassword()" class="dropdownItem">Change Password</p>
                      <p onclick="LogOut()" class="dropdownItem">Log Out</p>
                    </div>
                  </div>
                </div>
                <script>
                  oldParent = document.getElementById('config-container');
                  while (oldParent.childNodes.length > 0) {
                      document.body.appendChild(oldParent.childNodes[0]);
                  }
                  oldParent = document.getElementById('landingDivContainer');
                  while (oldParent.childNodes.length > 0) {
                      document.body.appendChild(oldParent.childNodes[0]);
                  }
                </script>
    <?php
  }

  
  function drawConfirmScreen($k,$email){
    require("logout.php");
    ?>
                <div id="emailConfContainer">
    <div id="emailConfirmScreen">
      <div class="inputDiv">
        <script>
                                  confirmEmail("<?php echo $k?>","<?php echo $email?>")
                                </script>
        <div id="confirmResult"></div>
      </div>
    </div>
                </div>
    <script>
                  $("#emailConfirmScreen").show()
                  oldParent = document.getElementById('emailConfContainer');
                  while (oldParent.childNodes.length > 0) {
                      document.body.appendChild(oldParent.childNodes[0]);
                  }
                </script>
    <?php
  }
    

  function drawLoggedOutMenu(){

    ?>
                <div id="config-container">
                  <div id="loginScreen">
                          <div class="inputDiv">
                                  <table class="inputTable">
                                          <tr><td></td><td><div id="loginResult">Login Failed!</div></td></tr>
                                          <tr>
                                                  <td class="inputLabel">User Name or Email</td>
                                                  <td class="inputCell"><input type="text" id="loginusername"></td>
                                          </tr>
                                          <tr>
                                                  <td class="inputLabel">Password</td>
                                                  <td class="inputCell"><input type="password" id="loginpassword"></td>
                                          </tr>
                                  </table>
                                  <table class="submitTable">
                                          <tr>
                                                  <td><button onclick="SubmitLogin()">Login</button></td>
                                                  <td><button onclick="CancelLogin()">Cancel</button></td>
                                          </tr>
                                  </table>
                          </div>
                  </div>
                  <div id="registerScreen">
                          <div class="inputDiv">
                                  <table class="inputTable">
                                          <tr><td></td><td><div id="usernameAvailability"></div></td></tr>
                                          <tr>
                                                  <td class="inputLabel">User Name</td>
                                                  <td class="inputCell"><input onkeyup="validateUsername()" onpaste="setTimeout(validateUsername,0)" type="text" id="username" maxlength="20"></td>
                                          </tr>
                                          <tr><td></td><td><div id="emailAvailability"></div></td></tr>
                                          <tr>
                                                  <td class="inputLabel">Email</td>
                                                  <td class="inputCell"><input  onkeyup="validateEmail()" onpaste="setTimeout(validateEmail,0)" type="email" id="email"></td>
                                          </tr>
                                          <tr><td></td><td><div id="passwordConsistency"></div></td></tr>
                                          <tr>
                                                  <td class="inputLabel">Password</td>
                                                  <td class="inputCell"><input onkeyup="validatePasswords()" onpaste="setTimeout(validatePasswords,0)" type="password" id="password"></td>
                                          </tr>
                                          <tr>
                                                  <td class="inputLabel">Confirm Password</td>
                                                  <td class="inputCell"><input onkeyup="validatePasswords()" onpaste="setTimeout(validatePasswords,0)" type="password" id="confirmpassword"></td>
                                          </tr>
                                  </table>
                                  <table class="submitTable">
                                          <tr>
                                                  <td><button onclick="SubmitRegistration()">Submit</button></td>
                                                  <td><button onclick="CancelRegistration()">Cancel</button></td>
                                          </tr>
                                  </table>
                          </div>
                  </div>
                </div>
    <script>bindEnterKey()</script>
                <script>
                  oldParent = document.getElementById('config-container');
                  while (oldParent.childNodes.length > 0) {
                      document.body.appendChild(oldParent.childNodes[0]);
                  }
                  oldParent = document.getElementById('landingDivContainer');
                  while (oldParent.childNodes.length > 0) {
                      document.body.appendChild(oldParent.childNodes[0]);
                  }
                </script>
    <?php
    if(isset($_GET['login']) && $_GET['login']){
      ?>
      <script>Login()</script>
      <?php
    }
  }

  
  function drawEmailVerification($email){
    ?>
                <div id="emailver-container">
      <div id="emailVerificationScreen">
      <div class="inputDiv">
        <center>
          You must click the link that was sent to<br><span class="highlighted"><?php echo $email?></span><br><br>
          Check your spam/junk folder if you cannot find it.<br><br>
          <table class="submitTable">
            <tr>
              <td class="inputLabel"><button onclick="resendVerificationEmail()">Resend Email</button></td>
              <td class="inputCell"><button onclick="LogOut();location.reload()">Close</button></td>
            </tr>
          </table>
          <div id="emailVerificationSendStatus"></div>
        </center>
      </div>
    </div>
                </div>
    <script>
                  $("#emailVerificationScreen").show();
                  oldParent = document.getElementById('emailver-container');
                  while (oldParent.childNodes.length > 0) {
                      document.body.appendChild(oldParent.childNodes[0]);
                  }
                </script>
    <?php
  }
  
  function drawNewPasswordRequiredScreen(){
    global $baseDomain;
    ?>
    <div id="forcePasswordScreen">
      <div class="inputDiv" style="text-align:justify;padding-left:20px;padding-right:20px;">
      <?php echo $baseDomain?> has recently implemented a stronger login system. As a result you must re-enter your password to continue using this site. Please enter and confirm your password. If you wish to change your password, you may do so now.<br><br>Questions can be emailed to the admin @ <br><a href="mailto:s.r.mcgann@hotmail.com">s.r.mcgann@hotmail.com</a><br><br><br>
        <table class="inputTable">
          <tr><td></td><td><div id="forcePasswordConsistency"></div></td></tr>
          <tr>
            <td class="inputLabel">New Password</td>
            <td class="inputCell"><input onkeyup="forceValidatePasswords()" onpaste="setTimeout(forceValidatePasswords,0)" type="password" id="newPassword"></td>
          </tr>
          <tr>
            <td class="inputLabel">Confirm Password</td>
            <td class="inputCell"><input onkeyup="forceValidatePasswords()" onpaste="setTimeout(forceValidatePasswords,0)" type="password" id="newConfirmpassword"></td>
          </tr>
        </table>
        <table class="submitTable">
          <tr>
            <td><button onclick="SubmitForceNewPassword()">Save</button></td>
          </tr>
        </table>
      </div>
    </div>
    <script>
      $("#forcePasswordScreen").show();
      setTimeout(function(){
        $("#newPassword").focus()
        document.getElementById('newPassword').onkeypress = function(e){
          if (!e) e = window.event;
          var keyCode = e.keyCode || e.which;
          if (keyCode == '13'){
            SubmitForceNewPassword();
          }
        }
        document.getElementById('newConfirmpassword').onkeypress = function(e){
          if (!e) e = window.event;
          var keyCode = e.keyCode || e.which;
          if (keyCode == '13'){
            SubmitForceNewPassword();
          }
        }
      },0);
    </script>
    <?php
  }
  
  function drawMenu(){
    global $link, $baseDomain;
    ?>
    <div class="navMenu"></div>
    <?php
    if(isset($_GET['k']) && $_GET['k'] !== '' && isset($_GET['email'])){
      $k=str_replace("'","",str_replace(";","",str_replace('"','',str_replace("<","",str_replace("%22","",$_GET['k'])))));
      $email=str_replace("'","",str_replace(";","",str_replace('"','',str_replace("<","",str_replace("%22","",$_GET['email'])))));
      drawConfirmScreen($k,$email);
      drawLoggedOutMenu();
    }else{
      if(isset($_COOKIE['id']) && $_COOKIE['id']){
        $id=$_COOKIE['id'];
        $pass=$_COOKIE['session'];
        $sql="SELECT * FROM codegolfUsers WHERE id=$id AND pass = \"$pass\"";
        $res=mysqli_query($link, $sql);
        if(mysqli_num_rows($res)){
          $row=mysqli_fetch_assoc($res);
          $email=$row['email'];
          if($row['emailVerified']){
            $name=$row['name'];
            drawLoggedInMenu($name,$id,$email);
            $date=date("Y-m-d H:i:s",strtotime("now"));
            $IP=$_SERVER['REMOTE_ADDR'];
            $sql="UPDATE codegolfUsers SET lastseen = \"$date\", IP=\"$IP\" WHERE id=$id";
            mysqli_query($link, $sql);
            //for new hashing transition
            $newHash=$row['newHash'];
            if($newHash=="") drawNewPasswordRequiredScreen();
            //
          }else{
            drawEmailVerification($email);
          }
        }else{
          require("logout.php");
          drawLoggedOutMenu();
        }
      }else{
        drawLoggedOutMenu();
      }
    }
    drawNavMenu();
    drawNewAppletButton();
  }
  
  function drawNavMenu(){
    $params = explode('/',  $_SERVER['REQUEST_URI']);
    if(sizeof($params)>3){
      for($i=3;$i--;)array_shift($params);
    }
    $user=$params[0];
    $filter=$params[1]?$params[1]:"all";
    $valid=0;
    if($user=="140" || $user=="512" || $user=="1024"){
      $filter=$user;
      $user="";
      $valid=1;
    }elseif($user=='a'){
      require("db.php");
      $id=mysqli_real_escape_string($link,$filter);
      $sql="SELECT userID FROM applets WHERE id=$id";
      $res=mysqli_query($link, $sql);
      $row=mysqli_fetch_assoc($res);
      $userID=$row['userID'];
      $sql="SELECT name FROM codegolfUsers WHERE id=$userID";
      $res=mysqli_query($link, $sql);
      $row=mysqli_fetch_assoc($res);
      $user=$row['name'];
      $filter="";
      $valid=1;
    }
    $user=str_replace("'","",str_replace(";","",str_replace('"','',str_replace("<","",str_replace("%22","",$user)))));
    $filter=str_replace("'","",str_replace(";","",str_replace('"','',str_replace("<","",str_replace("%22","",$filter)))));
    if(!$valid){
      $user='';
      $filter='';
    }
    ?>
    <script>
      <?php

      if($user){
        ?>
        $(".navMenu").html("( <?php echo "$user"?> ) ");
        $(".navMenu").append('<a href="/codegolf/?params=/<?php echo $user?>" class="navMenuButton<?php echo ($filter=="all"?"Selected":"")?>">all</a>');
        $(".navMenu").append('<a href="/codegolf/?params=/<?php echo $user?>/140" class="navMenuButton<?php echo ($filter=="140"?"Selected":"")?>">140b</a>');
        $(".navMenu").append('<a href="/codegolf/?params=/<?php echo $user?>/512" class="navMenuButton<?php echo ($filter=="512"?"Selected":"")?>">512b</a>');
        $(".navMenu").append('<a href="/codegolf/?params=/<?php echo $user?>/1024" class="navMenuButton<?php echo ($filter=="1024"?"Selected":"")?>">1024b</a>');
        <?php
      }else{
        ?>
        $(".navMenu").append('<a href="/codegolf/" class="navMenuButton<?php echo ($filter=="all"?"Selected":"")?>">all</a>');
        $(".navMenu").append('<a href="/codegolf/?params=/140" class="navMenuButton<?php echo ($filter=="140"?"Selected":"")?>">140b</a>');
        $(".navMenu").append('<a href="/codegolf/?params=/512" class="navMenuButton<?php echo ($filter=="512"?"Selected":"")?>">512b</a>');
        $(".navMenu").append('<a href="/codegolf/?params=/1024" class="navMenuButton<?php echo ($filter=="1024"?"Selected":"")?>">1024b</a>');
        <?php
      }
      ?>
    </script>
    <?php
  }
  
  function sendVerificationEmail($name,$email,$key){
    global $baseURL, $baseDomain;
    $to = '"'.$name.'" <'.$email.'>';
    $subject = "Welcome to test.cantelope.org!";
    $txt = "
    Thank you for signing up on $baseDomain!\r\n
    Click the link below to confirm your email address.\r\n\r\n
    $baseURL/?k=$key&email=$email\r\n\r\n
    Thank you for joining $baseDomain!\r\n\r\n
    P.S. If you did not register, just igonore this email.
    ";
    $headers = "From: no-reply@$baseDomain" . "\r\n";

    mail($to,$subject,$txt,$headers);
  }
  
  function ipToDec($ip){
    $parts=explode(".",$ip);
    return $parts[0]*pow(2,24)+$parts[1]*pow(2,16)+$parts[2]*pow(2,8)+$parts[3];
  }
  
  function syncUserRating($userID){
    $sql="SELECT * FROM votes where userID=$userID";
    $res=mysqli_query($link, $sql);
    $rating=0;
    for($i=0;$i<mysqli_num_rows($res);++$i){
      $row=mysqli_fetch_assoc($res);
      $rating+=$row['vote']-1;
    }
    $rating/=mysqli_num_rows($res);
    $rating*=20;
    $sql="UPDATE codegolfUsers SET rating = \"$rating\" WHERE id=$userID";
    $res=mysqli_query($link, $sql);
  }

  function drawcodegolfComments($id){
    global $link;
    $sql="SELECT * FROM codegolfComments WHERE appletID=$id ORDER BY date ASC";
    $res=mysqli_query($link, $sql);
    ?>
    <div class="commentsDiv">
      <div id="commentsDivInner<?php echo $id?>" class="commentsDivInner">
      <?php
      if(mysqli_num_rows($res)){
        for($i=0;$i<mysqli_num_rows($res);++$i){
          $row=mysqli_fetch_assoc($res);
          $userID=$row['userID'];
          $comment=$row['comment'];
          $sql="SELECT name FROM codegolfUsers WHERE id=$userID";
          $res2=mysqli_query($link, $sql);
          $row=mysqli_fetch_assoc($res2);
          $name=$row["name"];
          ?>
          <a class="commentUserName" href="/codegolf/?params=/<?php echo $name?>"><?php echo $name?></a>:<span> <?php echo htmlspecialchars($comment, ENT_QUOTES, 'UTF-8')?></span><br>
          <?php
        }
      }else{
        ?>
        <span>No comments...</span><br>
        <?php
      }
      ?>
      </div>
      <script>
        $("#commentsDivInner<?php echo $id?>").linkify();
      </script>
      <?php
      if(isset($_COOKIE['id'])){
        $userID=$_COOKIE['id'];
        $sql="SELECT * FROM codegolfUsers WHERE id=$userID";
        $res=mysqli_query($link, $sql);
        $row=mysqli_fetch_assoc($res);
        $userName=$row['name'];
        ?>
        <div style="display:flex">
          <input type="text" id="commentInput<?php echo $id?>" style="flex-grow:100;margin:10px;text-align:left;padding-left:5px;" />
          <button style="width:250px;float:right;overflow:hidden;font-size:.9em;margin:10px;" onclick="postComment(<?php echo $id?>,'<?php echo $userName?>')">Post Comment</button>
        </div>
        <div class="clear"></div>       
        <script>
          document.getElementById('commentInput<?php echo $id?>').onkeypress = function(e){
            if (!e) e = window.event;
            var keyCode = e.keyCode || e.which;
            if (keyCode == '13'){
              postComment(<?php echo $id?>,'<?php echo $userName?>');
            }
          }
        </script>
        <?php
      }
      ?>
    </div>
    <?php
  }

  function drawRateWidget($id,$userID){
    ?>
    <div class='assetChoice'>
      <div id="<?php echo $id?>" class="rate_widget<?php echo $id?>">
        <div class="cloud_1 ratings_clouds clouds<?php echo $id?>"></div>
        <div class="cloud_2 ratings_clouds clouds<?php echo $id?>"></div>
        <div class="cloud_3 ratings_clouds clouds<?php echo $id?>"></div>
        <div class="cloud_4 ratings_clouds clouds<?php echo $id?>"></div>
        <div class="cloud_5 ratings_clouds clouds<?php echo $id?>"></div>
        <div class="cloud_6 ratings_clouds clouds<?php echo $id?>"></div>
      </div>
    </div>
    <script>
      $('.clouds<?php echo $id?>').hover(
        function() {
          $(this).prevAll().andSelf().addClass('ratings_over');
          $(this).nextAll().removeClass('ratings_vote'); 
        },
        function() {
          $(this).prevAll().andSelf().removeClass('ratings_over');
          set_votes<?php echo $id?>($(this).parent(),$(this).attr('id'),0);
        }
      );
      
      function set_votes<?php echo $id?>(widget,id,updateUR) {
        var votes = $(widget).data('fsr').number_votes;
        var exact = $(widget).data('fsr').dec_avg;
        var user_vote = $(widget).data('fsr').user_vote;
        var userRating = $(widget).data('fsr').userRating;
        $(widget).find('.cloud_' + user_vote).prevAll().andSelf().addClass('ratings_vote');
        $(widget).find('.cloud_' + user_vote).nextAll().removeClass('ratings_vote'); 
        $('#popCell'+id).html("Pop. "+exact+'% &nbsp;'+votes+" vote"+(votes==1?"":"s"));
        if(updateUR)$('.userRating<?php echo $userID?>').html("User<br>Rating<br>"+userRating+"%");
      }
      
      $('.clouds<?php echo $id?>').bind('click', function() {
        var cloud = this;
        var widget = $(this).parent();
         
        var clicked_data = {
          clicked_on : $(cloud).attr('class'),
          id : widget.attr('id')
        };
        $.post(
          'ratings.php',
          clicked_data,
          function(INFO) {
            widget.data( 'fsr', INFO );
            set_votes<?php echo $id?>(widget,widget.attr('id'),1);
            $('.clouds<?php echo $id?>').prevAll().andSelf().removeClass('ratings_over');
          },
          'json'
        ); 
      });
      $('.rate_widget<?php echo $id?>').each(function(i) {
        var widget = this;
        var out_data = {
          id : $(widget).attr('id'),
          fetch: 1
        };
        $.post(
          'ratings.php',
          out_data,
          function(INFO) {
            $(widget).data( 'fsr', INFO );
            set_votes<?php echo $id?>(widget,$(widget).attr('id'),1);
          },
          'json'
        );
      });
    </script>
    <?php
  }
  
  function drawApplet($row,$drawLegend=0){
    global $link,$appletURL,$baseURL;
    $id=$row['id'];
    $userID=$row['userID'];
    $avatar="./avatars/$userID.jpg?".rand();
    $webgl=$row['webgl'];
    $code=$row['code'];
    $votes=$row['votes'];
    $rating=$row['rating'];
    $date=$row['date'];
    $bytes=$row['bytes'];
    $formerUserID=$row['formerUserID'];
    $formerAppletID=$row['formerAppletID'];
    $sql="SELECT * FROM codegolfUsers WHERE id=$userID";
    $res=mysqli_query($link, $sql);
    $row=mysqli_fetch_assoc($res);
    $name=$row['name'];
    $dateCreated=date('m / d / Y',strtotime($row['dateCreated']));
    $lastSeen=date('m / d / Y',strtotime($row['lastSeen']));
    $rating=$row['rating'];
    if(isset($_COOKIE['id'])){
      $sql="SELECT * FROM codegolfUsers WHERE id=".$_COOKIE['id'];
      $res=mysqli_query($link, $sql);
      $row=mysqli_fetch_assoc($res);
      $admin=$row['admin'];
    }else{
      $admin=0;
    }
    ?>
    <div class="appletDiv" id="appletDiv<?php echo $id?>">
      <iframe src="<?php echo $appletURL?>/?applet=<?php echo $id?>" sandbox="allow-same-origin allow-scripts" class="appletIframe" id="iframe<?php echo $id?>"></iframe>
      <script>
        var h=$(".appletIframe").width()/1.8;
        $(".appletIframe").css("height",h+"px");
        var h=Math.min($(".appletDiv").width()/38,24);
        $(".appletName").css("font-size",h/1.25+"px");
        var h=Math.min($(".appletDiv").width()/10.5,80);
        $(".appletAvatar").css("width",h+"px");
        var h=Math.min($(".appletDiv").width()/45,20);
        $(".userInfoTable").css("font-size",h+"px");
        if(!mobile){
          var h=$(".appletIframe").height();
          $(".code-input").css("height",h+"px");
          $(".appletCode").css("height",h/1.85+"px");
          $(".appletCode").css("max-height",h/1.85+"px");
        }
        $("#iframe<?php echo $id?>").load(function(){
          var i=$("#iframe<?php echo $id?>")[0].contentWindow;
          document.querySelector("#iframe<?php echo $id?>").loaded=1;
                                        var len=editor<?php echo $id?>.getValue().length;
                                        var text=editor<?php echo $id?>.getValue();
                                        if(len<1025)var max=1024;
                                        if(len<513)var max=512;
                                        if(len<141)var max=140;
                                        $("#count<?php echo $id?>").html("}//<span style='color:"+(len>1024?"red":"#888")+"'>"+len+"</span>/"+max<?php echo $formerUserID?"+' ('+((len-$bytes)>=0?'+':'')+(len-$bytes)+'b)'":""?>);
                                        var webgl=$("#webglCheckbox<?php echo $id?>").is(":checked");
                                        document.querySelector("#iframe<?php echo $id?>").contentWindow.postMessage("start:"+webgl+":"+text, "<?php echo $appletURL?>");
          startStopApplets();
        });
        
        $('#toggle_fullscreen<?php echo $id?>').on('click', function(){
          i=$("#iframe<?php echo $id?>")[0];
          if (i.requestFullscreen) {
            i.requestFullscreen();
          } else if (i.webkitRequestFullscreen) {
            i.webkitRequestFullscreen();
          } else if (i.mozRequestFullScreen) {
            i.mozRequestFullScreen();
          } else if (i.msRequestFullscreen) {
            i.msRequestFullscreen();
          }
          i=$(iframe<?php echo $id?>)[0];
          if (i.requestFullscreen) {
            i.requestFullscreen();
          } else if (i.webkitRequestFullscreen) {
            i.webkitRequestFullscreen();
          } else if (i.mozRequestFullScreen) {
            i.mozRequestFullScreen();
          } else if (i.msRequestFullscreen) {
            i.msRequestFullscreen();
          }
        });
      </script>
      <div class="code-input"><div class="function-wrap" style="float:left;margin-bottom:5px;">function u(t){</div><input id="webglCheckbox<?php echo $id?>" <?php if($webgl)echo "checked"?> style="margin-left:5%;float:left;" type="checkbox"></input><span style="float:left;margin-left:5px;font-size:.7em;" id="webglCheckboxLabel<?php echo $id?>">webgl</span>
        <a href="javascript:deleteApplet(<?php echo $id?>)" id="deleteButton<?php echo $id?>" class="deleteButton">Delete Applet</a>
        <textarea class="appletCode" id="textArea<?php echo $id?>" autocorrect="off" autocapitalize="off" spellcheck="false" oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px';"><?php echo $code?></textarea><div class="function-wrap" style="margin-top:5px;" id="count<?php echo $id?>"></div>
        <?php
        if(isset($_COOKIE['id']) && isset($_COOKIE['session'])){
          ?>
          <button id="postRemix<?php echo $id?>" onclick="saveApplet(<?php echo $id?>,<?php echo $userID?>,<?php echo $id?>)" class="postButton">Post Remix</button>
          <?php
        }else{
          ?>
          <span id="postRemix<?php echo $id?>" style="display:none;font-size:.8em;margin-left:20px;color:red;background:#222a;">Please login or register to post your work.</span>
          <?php
        }
        ?>
        <script>
          var editor<?php echo $id?> = CodeMirror.fromTextArea($("#textArea<?php echo $id?>")[0],{theme:"blackboard",lineWrapping:true});
          $('#webglCheckbox<?php echo $id?>').change(function() {
            var webgl=this.checked;
            var text=editor<?php echo $id?>.getValue();
            document.querySelector("#iframe<?php echo $id?>").contentWindow.postMessage("start:"+webgl+":"+text, "<?php echo $appletURL?>");
            $('#webglCheckboxLabel<?php echo $id?>').val(this.checked);
          });
          if(!($(window).width()<$(window).height())){
            var h=$(".appletIframe").height();
            $(".CodeMirror").height((h-65)+"px");
            $(".CodeMirror").css("margin-top","-25px");
          }else{
            var h=$(".appletIframe").height();
            $(".CodeMirror").height("145px");
            $(".CodeMirror").css("margin-top","-25px");           
          }
        </script>
      </div>
      <div class="clear"></div>
      <?php
      if($formerUserID){
        $sql="SELECT * FROM codegolfUsers WHERE id=$formerUserID";
        $res=mysqli_query($link, $sql);
        $row=mysqli_fetch_assoc($res);
        $formerName=$row['name'];
        $sql="SELECT bytes FROM applets WHERE id=$formerAppletID";
        $res=mysqli_query($link, $sql);
        $row=mysqli_fetch_assoc($res);
        $byteDiff=$bytes-$row['bytes'];
        ?>
        <div class="creditDiv">
          Remix of <a href="/codegolf/?params=/a/<?php echo $formerAppletID?>">Applet #<?php echo $formerAppletID?></a> by <a href="/codegolf/?params=/<?php echo $formerName?>"><?php echo $formerName?></a> (<?php echo ($byteDiff>=0?"+":"").$byteDiff?>b)
        </div>
        <?php
      }
      ?>
      <div class="toolbar">
        <?php drawRateWidget($id,$userID)?>
        <div class="toolbarText">
          <span id="popCell<?php echo $id?>">Pop<?php echo $rating?>%</span>
          <span><a href="javascript:;" id="toggle_fullscreen<?php echo $id?>">Fullscreen</a></span>
          <!--<span><a href="javascript:toggleShareBox(<?php echo $id?>)">Share</a></span>-->
          <span><a href="<?php echo $baseURL?>/?params=/a/<?php echo $id?>" target="_blank">Share</a></span>
          <br><input id="shareBox<?php echo $id?>" value="<?php echo $baseURL.'/?params=/a/'.$id?>" class="shareBox"></input>
        </div>
      </div>
      <table class="userInfoTable">
        <tr>
          <td style="border:0; width: 275px"><a href="/codegolf/?params=/<?php echo $name?>"><img src="<?php echo $avatar?>" class="appletAvatar" />
          <br><span class="appletName"><?php echo $name?></span></a></td>
          <td><span class="userRating<?php echo $userID?>">User<br>Rating<br><?php echo $rating?>%</span></td>
          <td>Member Since<br><?php echo $dateCreated?></td>
          <td style="border-right:0;">Last Seen<br><?php echo $lastSeen?></td>
        </tr>
      </table>
      <?php drawcodegolfComments($id)?>
    </div>
    <?php
    if($drawLegend){
      ?>
      <div class="appletLegend">
        <code class="legendText">u(t) is called 60 times per second.
          t: Elapsed time in seconds.
          S: Shorthand for Math.sin.
          C: Shorthand for Math.cos.
          T: Shorthand for Math.tan.
          c: A 1920x1080 canvas.
          x: A context for that canvas.
          There are 3 applet categories based on number of characters: 140, 512, or 1024. The category is determined automatically.
        </code>
      </div>
      <?php
    }
    ?>      
    <script>
      <?php
      if(isset($_COOKIE['id']) && $_COOKIE['id']==$userID || $admin){
        ?>
        $("#deleteButton<?php echo $id?>").show();
        <?php
      }
      ?>

      var oldcode=editor<?php echo $id?>.getValue();
      editor<?php echo $id?>.on("keyup",function(){
        if(editor<?php echo $id?>.getValue()!=oldcode){
          $("#postRemix<?php echo $id?>").show();
          oldcode=editor<?php echo $id?>.getValue();
          var len=editor<?php echo $id?>.getValue().length;
          var text=editor<?php echo $id?>.getValue();
          if(len<1025)var max=1024;
          if(len<513)var max=512;
          if(len<141)var max=140;
          $("#count<?php echo $id?>").html("}//<span style='color:"+(len>1024?"red":"#888")+"'>"+len+"</span>/"+max<?php echo $formerUserID?"+' ('+((len-$bytes)>=0?'+':'')+(len-$bytes)+'b)'":""?>);
          var webgl=$("#webglCheckbox<?php echo $id?>").is(":checked");
          document.querySelector("#iframe<?php echo $id?>").contentWindow.postMessage("start:"+webgl+":"+text, "<?php echo $appletURL?>");
        }
      });
      var len=editor<?php echo $id?>.getValue().length;
      if(len<1025)var max=1024;
      if(len<513)var max=512;
      if(len<141)var max=140;
      $("#count<?php echo $id?>").html("}//<span style='color:"+(len>1024?"red":"#888")+"'>"+len+"</span>/"+max);
    </script>
    <?php 
  }
?>

