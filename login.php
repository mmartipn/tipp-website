<?php
	session_start ();

	include("sqlconn.php");
	$dbconn = mysqli_connect ($sqlhost, $sqluser, $sqlpass);
	$db_selected = mysqli_select_db($dbconn, $dbname);
	
	$username = trim(strtolower($_REQUEST["name"]));
	$password = md5(trim($_REQUEST["passwort"]));
	
	
	$result = mysqli_query ($dbconn, "SELECT usr_index, usr_locked, usr_tir_index__default FROM usr_user WHERE (LOWER(usr_email)=LOWER('".$username."') or LOWER(usr_name)=LOWER('".$username."')) and usr_password = '".$password."'");
	if (mysqli_num_rows ($result) == 0) $result = mysqli_query ($dbconn, "SELECT usr_index, usr_locked FROM usr_user WHERE usr_name='".$username."' and usr_password = '".$password."'");
	
	if (mysqli_num_rows ($result) > 0)
	{
		$data = mysqli_fetch_array ($result);
		if ($data["usr_locked"] == 1)
		{
			$_SESSION["loginfail"] = 2;
			header ("Location: index.php#main");
		}
		elseif ($data["usr_locked"] == 2)
		{
			$_SESSION["loginfail"] = 3;
			header ("Location: index.php#main");
		}
		else
		{
			$_SESSION["user_id_tipp"] = $data["usr_index"];
			
			$result_tir = mysqli_query ($dbconn, "SELECT urm_tir_index from urm_usrtirmapping where urm_usr_index=".$data["usr_index"]." and urm_tir_index=".$data["usr_tir_index__default"]); 
			if (mysqli_num_rows ($result_tir) > 0)
				$_SESSION["tir_id_tipp"] = $data["usr_tir_index__default"];
			
			mysqli_query ($dbconn, 'update usr_user set usr_loginfail=0 where usr_index='.$data["usr_index"]); 
			
			$_SESSION["loginfail"] = 0;
			header ("Location: member.php");
		}
	}
	else
	{
		$result = mysqli_query ($dbconn, "SELECT usr_index, usr_loginfail FROM usr_user WHERE usr_name='".$username."'"); 
		if (mysqli_num_rows ($result) > 0)
		{
			
			$data = mysqli_fetch_array ($result);
			$lf = $data['usr_loginfail'];
			$lf ++;
			mysqli_query ($dbconn, 'update usr_user set usr_loginfail='.$lf.' where usr_index='.$data["usr_index"]); 
			if ($lf > 10)
			{
				mysqli_query ($dbconn, 'update usr_user set usr_locked=1 where usr_index='.$data["usr_index"]); 	
			}
		}
		
		$_SESSION["loginfail"] = 1;
		header ("Location: index.php#main");
	}
?>
