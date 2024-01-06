<?php
	require("functions.php");
	$userID=$_COOKIE['id'];
	$pass=$_COOKIE['session'];
	$sql="SELECT * FROM codegolfUsers WHERE id=$userID AND pass=\"$pass\"";
	$res=mysqli_query($link, $sql);
	if(mysqli_num_rows($res)){
		$row=mysqli_fetch_assoc($res);
		$admin=$row['admin'];
		$id=$_POST['id'];
		$sql="SELECT * FROM applets WHERE id=$id";
		$res=mysqli_query($link, $sql);
		$row=mysqli_fetch_assoc($res);
		if($userID==$row['userID'] || $admin){
			$sql="DELETE FROM applets WHERE id=$id";
			mysqli_query($link, $sql);
			$sql="DELETE FROM votes WHERE appletID=$id";
			mysqli_query($link, $sql);
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
			mysqli_query($link, $sql);
			$sql="UPDATE applets SET formerUserID=0, formerAppletID=0 WHERE formerAppletID=$id";
			mysqli_query($link, $sql);
		}else{
			echo "fail\n".$userID;
		}
	}else{
		echo "fail\n".$userID;
	}
?>