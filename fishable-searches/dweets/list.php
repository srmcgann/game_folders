<!DOCTYPE html>
<html>
  <head>
    <style>
      body, html{
        background: #000;
        text-align: center;
        color: #fff;
        font-family: courier;
        margin: 0;
        font-size: 24px;
      }
      .dweetLink{
        cursor: pointer;
        border-radius: 5px;
        min-width: 200px;
        background: #40f8;
        color: #0f8;
        font-family: courier;
        font-size: 16px;
        display: inline-block;
      }
      #pageTitle{
        color: #fff;
        font-size: 32px;
      }
      .main{
        width: 500px;
        margin-left: auto;
        margin-right: auto;
        position: relative;
        display: inline-block;
      }
    </style>
  </head>
  <body>
    <span id="pageTitle">dweets (some might not work!)</span><br><br>
    <div class="main">
      <?php
      require('db.php');
      $sql = "SELECT slug FROM dweet_links";
      $res = mysqli_query($link, $sql);
      for($i = 0; $i < mysqli_num_rows($res); ++$i){
        $row = mysqli_fetch_assoc($res);
        echo "<a class=\"dweetLink\" href=\"/dweets/{$row['slug']}\" target=\"_blank\">{$row['slug']}</a><br>";
      }
      ?>
    </div>
  </body>
</html>