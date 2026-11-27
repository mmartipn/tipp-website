<?php
	include 'functions.php';
	$dbconn = sqlconnect();
	$resp = 1;	
	if (isset($_REQUEST["q_a"]))
	{
		$q_a = unserialize($_REQUEST["q_a"]);
		if (sizeof($q_a) == 6)
		{
			$live_mat_data_index = intval($q_a[0]); 
			$result_home = intval($q_a[1]); 
			$result_away = intval($q_a[2]); 
			$mat_status = intval($q_a[3]); 
			$resultstring = $q_a[4]; 
			$mat_index = intval($q_a[5]); 
		
			mysqli_query ($dbconn, "update mat_match set mat_data_index='".$live_mat_data_index."', mat_result_home=".$result_home.", mat_result_away=".$result_away.", mat_status=".$mat_status.", mat_result_string='".$resultstring."' where mat_index=".$mat_index);
			generiere_autotipps($dbconn, $mat_index);
			bewerte_match($dbconn, $mat_index);
			mysqli_query ($dbconn, "INSERT INTO tim_timestamp (tim_info) VALUES('3')");
			$resp = 2;	
		}	
	}
	echo $resp;
?>