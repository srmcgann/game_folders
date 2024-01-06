<?php
	require("db.php");
	require("functions.php");
	if(isset($_COOKIE["id"]) && isset($_COOKIE['session'])){
		$id=$_COOKIE["id"];
		$pass=$_COOKIE["session"];
		$email=mysqli_real_escape_string($link,$_POST['email']);
		$avatar=mysqli_real_escape_string($link,$_POST['avatar']);
		$imgData = str_replace(' ','+',$avatar);
		$imgData =  substr($imgData,strpos($imgData,",")+1);
		$imgData = base64_decode($imgData);
		$sql="SELECT id FROM codegolfUsers WHERE id = $id AND pass = \"$pass\"";
		$res=mysqli_query($link, $sql);
		if(mysqli_num_rows($res)){
			$h=fopen("avatars/$id.jpg","w");
			fwrite($h,$imgData);
			fclose($h);
      //$h=fopen("../public/avatars/$id.jpg","w");
      //fwrite($h,$imgData);
      //fclose($h);
			if($email){
				$sql="UPDATE codegolfUsers SET email=\"$email\" WHERE id=$id";
				mysqli_query($link, $sql);
			}
			//mysqli_query($link, $sql);
			echo 1;
		}
	}
?>