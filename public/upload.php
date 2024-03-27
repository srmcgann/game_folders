<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

  require_once('db.php');
  require_once('functions.php');

  $ct = 0;
  $links = [];
  $types = [];
  $sizes = [];
  $error = '';
  $success = false;
  $maxFileSize = 1e8/4;
  $uploadDir = 'uploads';
  if(sizeof($_FILES)){
    forEach($_FILES as $key => $val){
      $unlink = false;
      $tmp_name = $_FILES["uploads_$ct"]['tmp_name'];
      $slug = genSlug();
      move_uploaded_file($tmp_name, "$uploadDir/$slug");
      $type = mime_content_type("$uploadDir/$slug");
      $continue = false;
      $size = filesize("$uploadDir/$slug");
      if($size < $maxFileSize){
        switch($type){
          case 'audio/wav': $continue = true; $suffix = 'wav';  break;
          case 'audio/x-wav': $continue = true; $suffix = 'wav';  break;
          case 'audio/mp3': $continue = true; $suffix = 'mp3';  break;
          case 'audio/mpeg': $continue = true; $suffix = 'mp3';  break;

          case 'image/jpg': $continue = true; $suffix = 'jpg'; break;
          case 'image/jpeg': $continue = true; $suffix = 'jpeg';  break;
          case 'image/png': $continue = true; $suffix = 'png';  break;
          case 'image/gif': $continue = true; $suffix = 'gif';  break;
          case 'image/webp': $continue = true; $suffix = 'webp';  break;

          case 'video/webm': $continue = true; $suffix = 'webm';  break;
          case 'video/mkv': $continue = true; $suffix = 'mkv';  break;
          case 'video/mp4': $continue = true; $suffix = 'mp4';  break;
        }
        if($continue){
          if($type == 'video/mp4' && strpos($_FILES["uploads_$ct"]["name"], '.mp3') !== false){
            $type = 'audio/mp3';
            $suffix = 'mp3';
          }
          $hash = hash_file('md5', "$uploadDir/$slug");
          
          $sql = "SELECT * FROM imjurUploads WHERE hash = \"$hash\"";
          $res = mysqli_query($link, $sql);
          if(mysqli_num_rows($res)){
            $row = mysqli_fetch_assoc($res);
            $originalSlug = $row['slug'];
            $unlink = true;
          }else{
            $originalSlug = $slug;
          }
          
          $id = alphaToDec($slug);
          $original_name = basename($_FILES["uploads_$ct"]["name"]);
          $meta = mysqli_real_escape_string($link, json_encode([
            "file size" => $size,
            "sender IP" => $_SERVER['REMOTE_ADDR'],
            "original name" => $original_name,
          ]));
          $description = $_FILES["uploads_$ct"]["description"];
          $origin = "user file: $original_name";
          $userID = -1;
          
$sql = <<<SQL
INSERT INTO imjurUploads (id, 
                          slug,
                          originalSlug,
                          meta,
                          hash,
                          filetype,
                          origin,
                          userID,
                          upvotes,
                          downvotes,
                          views,
                          description
                          )VALUES(
                            $id,
                            "$slug",
                            "$originalSlug",
                            "$meta",
                            "$hash",
                            "$type",
                            "$origin",
                            $userID,
                            0,
                            0,
                            0,
                            "$description"
                          )
SQL;
          
          mysqli_query($link, $sql);
          $success = true;
          $links[] = "$uploadDir/$originalSlug.$suffix";
          $sizes[] = $size;
          $types[] = $type;
          if($unlink){
            unlink("$uploadDir/$slug");
          }else{
            rename("$uploadDir/$slug", "$uploadDir/$slug.$suffix");
          }
        }else{
          $error = "ERROR: one or more files had an unrecognized or unsupported file type";
          unlink("$uploadDir/$slug");
        }
      }else{
        $error = "ERROR: one or more files were too large. $maxFileSize max";
        unlink("$uploadDir/$slug");
      }
      $ct++;
    }else{
    }
  } else {
    $error = 'ERROR: no files were received';
  }
  
  echo json_encode([$success, $links, $sizes, $types, $ct, $error]);
?>