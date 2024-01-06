<?php
	require("db.php");
	$name=mysqli_real_escape_string($link,$_POST['name']);
	$sql="SELECT * FROM codegolfUsers WHERE name LIKE \"$name\"";
	$res=mysqli_query($link, $sql);
	if(!mysqli_num_rows($res))echo 1;
?>