<?php
	require("db.php");
	$key=mysqli_real_escape_string($link,$_POST['k']);
	$email=mysqli_real_escape_string($link,$_POST['email']);
	$sql="SELECT * FROM codegolfUsers WHERE email=\"$email\" AND emailKey=\"$key\"";
	$res=mysqli_query($link, $sql);
	if(mysqli_num_rows($res)){
		$row=mysqli_fetch_assoc($res);
		if(!$row['emailVerified']){
			$sql="UPDATE codegolfUsers SET emailVerified=1 WHERE email=\"$email\" AND emailKey=\"$key\"";
			mysqli_query($link, $sql);
			$sql="SELECT id FROM codegolfUsers WHERE email=\"$email\" AND emailKey=\"$key\"";
			$res=mysqli_query($link, $sql);
			$row=mysqli_fetch_assoc($res);
			copy("avatars/default.jpg","avatars/".$row['id'].'.jpg');
			echo 1;
		}
	}
?>