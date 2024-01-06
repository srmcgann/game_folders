<?php
	require("db.php");
	require("functions.php");
	if(isset($_COOKIE['id']) && isset($_COOKIE['session'])){
		$id=$_COOKIE['id'];
		$pass=$_COOKIE['session'];
		$sql="SELECT * FROM codegolfUsers WHERE id=$id AND pass=\"$pass\"";
		$res=mysqli_query($link, $sql);
		if(mysqli_num_rows($res)){
			$row=mysqli_fetch_assoc($res);
			$name=$row['name'];
			$email=$row['email'];
			$key=$row['emailKey'];
			sendVerificationEmail($name,$email,$key);
			echo 1;
		}
	}
?>