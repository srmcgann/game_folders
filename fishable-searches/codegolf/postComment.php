<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	require("functions.php");
	$userID=$_COOKIE['id'];
	$pass=mysqli_real_escape_string($link, $_COOKIE['session']);
	$sql="SELECT * FROM codegolfUsers WHERE id=$userID AND pass=\"$pass\"";
	$res=mysqli_query($link, $sql);
	if(mysqli_num_rows($res)){
		$comment=mysqli_real_escape_string($link,$_POST['comment']);
		$comment=str_replace(":)","😊",$comment);
		$comment=str_replace(":D","😃",$comment);
		$appletID=mysqli_real_escape_string($link,$_POST['id']);
		$date=date("Y-m-d H:i:s",strtotime("now"));
		$sql="INSERT INTO codegolfComments (appletID, userID, comment, date) VALUES($appletID, $userID, \"$comment\", \"$date\")";
		mysqli_query($link, $sql);
	}else{
		echo "fail";
	}
?>