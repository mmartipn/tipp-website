<?php
	session_start ();

	include("sqlconn.php");
	$dbconn = mysqli_connect ($sqlhost, $sqluser, $sqlpass);
	$db_selected = mysqli_select_db($dbconn, $dbname);
	
	$usr_index = 0;
	$pwd = md5(trim($_POST["pwd1"]));
	if (strlen($_POST["hash"])>0)
	{
		$result = mysqli_query ($dbconn, "SELECT usr_index FROM usr_user WHERE usr_hash='".$_POST["hash"]."'");
		if (mysqli_num_rows ($result) > 0)
		{
			while ($row=mysqli_fetch_array($result)) 
				mysqli_query ($dbconn, 'update usr_user set usr_loginfail=0, usr_locked=0, usr_password="'.$pwd.'" where usr_index='.$row["usr_index"]); 
			$_SESSION["loginfail"] = 4;
			header ("Location: index.php");	
		}
		else
		{
			$_SESSION["loginfail"] = 5;
			header ("Location: index.php");	
		}
	}
?>