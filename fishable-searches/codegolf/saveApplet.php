<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);	require("db.php");
	$userID=$_COOKIE['id'];
	$pass=mysqli_real_escape_string($link,$_COOKIE['session']);
	$sql="SELECT * FROM codegolfUsers WHERE id=$userID AND pass=\"$pass\"";
	$res=mysqli_query($link, $sql);
	if(mysqli_num_rows($res)){
		$row=mysqli_fetch_assoc($res);
		$name=$row['name'];
		$code=str_replace("\r\n","\n",$_POST['code']);
		$bytes=mb_strlen($code);
		if($bytes>1024){
			echo "fail";
		}else{
			$webgl=mysqli_real_escape_string($link,$_POST['webgl']);
			$code=mysqli_real_escape_string($link,$code);
			$formerUserID=mysqli_real_escape_string($link,$_POST['formerUserID']);
			$formerAppletID=mysqli_real_escape_string($link,$_POST['formerAppletID']);
			$date=date("Y-m-d H:i:s",strtotime("now"));
			$sql="INSERT INTO applets (userID,code,rating,votes,date,formerUserID,formerAppletID,bytes,webgl) VALUES($userID,\"$code\",0,0,\"$date\",$formerUserID,$formerAppletID,$bytes,$webgl)";
			mysqli_query($link, $sql);


                        $id=$link->insert_id;
	                require("functions.php");
			$vote=6;
	                $IP=ipToDec($_SERVER['REMOTE_ADDR']);

        	        $sql="SELECT userID FROM applets where id=$id";
                	$res=mysqli_query($link, $sql);
	                $row=mysqli_fetch_assoc($res);
	                $userID=$row['userID'];

        	        $sql="SELECT * FROM votes WHERE IP=$IP AND appletID=$id";
	                $res=mysqli_query($link, $sql);
        	        if(mysqli_num_rows($res)){
                	        $sql="UPDATE votes SET vote=$vote WHERE IP=$IP AND appletID=$id";
                        	mysqli_query($link, $sql);
	                }else{
        	                $sql="INSERT INTO votes (IP,appletID,vote,userID) VALUES($IP,$id,$vote,$userID)";
	                        mysqli_query($link, $sql);
	                }

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
	                $res=mysqli_query($link, $sql);

        	        $sql="SELECT vote FROM votes WHERE appletID=$id";
	                $res=mysqli_query($link, $sql);
	                $votes=mysqli_num_rows($res);
	                $total=0;
	                for($i=0;$i<$votes;++$i){
	                        $row=mysqli_fetch_assoc($res);
	                        $total+=($row['vote']-1);
	                }
	                $rating=$total/$votes*20;
	                $sql="UPDATE applets SET rating=$rating, votes=$votes WHERE id=$id";
	                mysqli_query($link, $sql);
			echo $name;

                        // generate thumbnail for social media
                        //shell_exec('node thumb.js ' . $id);
		}

	}else{
		echo "fail";
	}
?>