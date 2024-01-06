<?php
	require("db.php");
	$email=mysqli_real_escape_string($link,$_POST['email']);
	$sql="SELECT * FROM codegolfUsers WHERE email LIKE \"$email\"";
	$res=mysqli_query($link, $sql);
	if(!mysqli_num_rows($res))echo 1;
?>