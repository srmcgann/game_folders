<?php

  function pisearch($srch){
    $parts = [
      'https://archive.org/download/pi_dec_1t/pi_dec_1t_01.zip/pi_dec_1t_01.txt',
      'https://archive.org/download/pi_dec_1t/pi_dec_1t_02.zip/pi_dec_1t_02.txt',
      'https://archive.org/download/pi_dec_1t/pi_dec_1t_03.zip/pi_dec_1t_03.txt',
      'https://archive.org/download/pi_dec_1t/pi_dec_1t_04.zip/pi_dec_1t_04.txt',
      'https://archive.org/download/pi_dec_1t/pi_dec_1t_05.zip/pi_dec_1t_05.txt',
      'https://archive.org/download/pi_dec_1t/pi_dec_1t_06.zip/pi_dec_1t_06.txt',
      'https://archive.org/download/pi_dec_1t/pi_dec_1t_07.zip/pi_dec_1t_07.txt',
      'https://archive.org/download/pi_dec_1t/pi_dec_1t_08.zip/pi_dec_1t_08.txt',
      'https://archive.org/download/pi_dec_1t/pi_dec_1t_09.zip/pi_dec_1t_09.txt',
      'https://archive.org/download/pi_dec_1t/pi_dec_1t_10.zip/pi_dec_1t_10.txt',
    ];
    
    $ct = 0;
    
    forEach($parts as $url){
      $ct++;
      $src = fopen(str_replace(" ","%20", $url), 'r');
      echo "SCANNING 10BILLION ×($ct) DIGITS...<br>";
      flush();
      ob_flush();

      stream_set_blocking($src, true);
      $rct=$pct=$opct=0;
      do{
        $data = fread($src, 1024*64);
        if($data){
          $rct_ = $rct;
          $rct+=strlen($data);
          $opct = $pct;
          $pct = floor($rct/1e11*10000)/10000;
          if($opct != $pct) {
            echo "...[10b × $ct] $pct%<br>";
            flush();
            ob_flush();
          }
          if(strpos($data, $srch) !== false){
            $found = true;
            $ct_= $ct-1;
            $rct_ += strpos($data, $srch)-1;
            echo "<br><br><br>VALUE LOCATED.<br><br> $srch -> section 10b × $ct_ + (position): $rct_<br><br><br>";
            die();
          }
          //echo $data;
          flush();
          ob_flush();
        }
      }while($data);
    }
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
?>
<!DOCTYPE html>
<html>
  <head>
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
      body,html{
        background: #000;
        margin: 0;
        height: 100vh;
        overflow: auto;
        color: #0fc;
        font-size: 20px
        text-align: center;
        font-family: Courier Prime;
      }
      #res{
        position: absolute;
        min-width: 600px;
        background: #40f2;
        border-radius: 10px;
        padding: 25px;
        padding-top: 5px;
        padding-bottom: 5px;
        left: 50%;
        top: 45vh;
        transform: translate(-50%, -50%);
      }
    </style>
  </head>
  <body>
    <div id="res">
      <?php
        if(isset($_GET['alphatodec']) && strlen($_GET['alphatodec'])){
          echo  "<br><br>" . 'alphaToDec: ' . $_GET['alphatodec'] . ' &rarr; ' . alphaToDec($_GET['alphatodec']) . "<br><br>";
        }
        if(isset($_GET['dectoalpha']) && strlen($_GET['dectoalpha'])){
          echo  "<br><br>" . 'decToAlpha: ' . $_GET['dectoalpha'] . ' &rarr; ' . decToAlpha($_GET['dectoalpha']) . "<br><br>";
        }
        if(isset($_GET['pisearch']) && strlen($_GET['pisearch'])){
          echo  "<br><br>" . 'pi search, pos: ' . $_GET['pisearch'] . ' &rarr; ' . pisearch($_GET['pisearch']) . "<br><br>";
        }
      ?>
    </div>
  </body>
</html>
