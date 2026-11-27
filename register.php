<?php
	session_start ();
	include 'functions.php';
	$dbconn = sqlconnect();
	
	$tipprunde = konform($_POST["tipprunde"]);
	$username = trim(strtolower(konform($_POST["name"])));
	$vornachname = konform($_POST["vornachname"]);
	$email = trim(strtolower(konform($_POST["email"])));
	$password1 = konform($_POST["passwort"]);
	$password2 = konform($_POST["passwort2"]);
	
	if ((strlen($username) == 0) || (strlen($email) == 0) || (strlen($password1) == 0))
	{		
		$_SESSION["registerfail"] = 1;
		header ("Location: index.php?m_id=2");
	}
	else // Feld leer
	{
		// gibt es den user schon ?
		$usr_index = userexists($dbconn, $username, $email);
		if ($usr_index <> 0)
		{
			$_SESSION["registerfail"] = 2;
			$_SESSION["registerfail_username"] = $username;
			$_SESSION["registerfail_username"] = $username;
			$_SESSION["registerfail_email"] = $email;
			$_SESSION["registerfail_vornachname"] = $vornachname;
			$_SESSION["registerfail_tipprunde"] = $tipprunde;
			header ("Location: index.php?m_id=2");
		}
		else  
		{
			$_SESSION["registerfail"] = 3;
			$hash = md5($username.$email.$password1); 
			mysqli_query ($dbconn, "insert into usr_user (usr_name, usr_vornachname, usr_email, usr_password, usr_hash, usr_locked, usr_loginfail, usr_tir_index__default, usr_bezahlt) values('".$username."', '".$vornachname."','".$email."', '".md5($password1)."', '".$hash."', 2, 0, ".$tipprunde.", 0)");
			$newuid = mysqli_insert_id($dbconn);
			mysqli_query ($dbconn, "insert into urm_usrtirmapping (urm_usr_index, urm_tir_index) values(".$newuid.", ".$tipprunde.")");

			sendmail($newuid, $dbconn, 0, "", $tipprunde);
			header ("Location: index.php?m_id=2");
		}
	}
	
?>
