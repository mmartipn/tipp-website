<?php
	$dbconn = mysqli_connect ("localhost", "IhrName", "IhrPasswort");
	$db_selected = mysqli_select_db($dbconn, "tipp");
		
	function getData($dbconn)
	{	
		//gibt es daten im log die nicht uebertragen wurden?
		$result=mysqli_query($dbconn, "SELECT tim_index, tim_info, tim_error FROM tim_timestamp WHERE tim_error=1");
		while($row=mysqli_fetch_array($result))
		{
			$data_from_api = file_get_contents ("https://example.com/cronrasp.php?q_a=".$row['tim_info']);
			if ($data_from_api == "2")
			{
				//hat geklappt
				mysqli_query ($dbconn, "DELETE FROM INTO tim_timestamp WHERE tim_index=".$row['tim_index']);
			}
		}	
	
		//gibt es mind 1 spiel das angefangen hat dessen status nicht 2 ist ?
		$live = false;
		$db_mat_index = array ();
		$db_mat_data_index = array ();
		$db_tea_data_home_index = array ();
		$db_tea_data_away_index = array ();
		$db_tea_data_home_name = array ();
		$db_tea_data_away_name = array ();
		$result=mysqli_query($dbconn, "SELECT mat_index, mat_grp_index, mat_data_index, ta.tea_data_index AS tea_data_index_home, ta.tea_name AS tea_data_name_home, tb.tea_data_index AS tea_data_index_away, tb.tea_name AS tea_data_name_away  from mat_match inner join grp_group on mat_grp_index=grp_index INNER JOIN tea_team ta ON ta.tea_index = mat_tea_index_home INNER JOIN tea_team tb ON tb.tea_index = mat_tea_index_away where mat_status<2 and NOW()>= mat_starttime");
		while($row=mysqli_fetch_array($result))
		{
			$live = true;
			$db_mat_index[] = $row ['mat_index'];
			$db_mat_data_index[] = $row ['mat_data_index'];
			$db_tea_data_home_index[] = $row ['tea_data_index_home'];
			$db_tea_data_away_index[] = $row ['tea_data_index_away'];
			$db_tea_data_home_name[] = $row ['tea_data_name_home'];
			$db_tea_data_away_name[] = $row ['tea_data_name_away'];
		}
		if ($live)
		{
			//zuletzt daten geholt vor mehr als 1 Minute ?
		//	$result = mysqli_query ($dbconn, "SELECT * FROM tim_timestamp WHERE tim_date >= DATE_SUB(NOW(),INTERVAL 1 MINUTE)");
		//	if (mysqli_num_rows($result) == 0)
		//	{
				$data_from_api = file_get_contents ("https://livescore-api.com/api-client/scores/live.json?key=IHR_API_KEY&secret=IHR_API_SECRET&competition_id=362");
				foreach(json_decode($data_from_api ,true) as $cc => $value) 
				{
					if (strtoupper($cc) == "DATA")
					{
						foreach($value as $cure => $value1) 
						{
							if (strtoupper($cure) == "MATCH")
							{
								foreach($value1 as $cure1 => $value2) 
								{	
									$error = 0;
									$live_status = $value2["status"];
									$live_mat_data_index=$value2["id"];
									$live_grp_data_index=$value2["league_id"];
									$live_tea_data_index_home=$value2["home_id"];
									$live_tea_data_index_away=$value2["away_id"];
									$live_tea_data_name_home=$value2["home_name"];
									$live_tea_data_name_away=$value2["away_name"];
									
									$position = -1;
									
									for ($i=0; $i<sizeof($db_mat_data_index); $i++)
									{
										if ($db_mat_data_index[$i] == $live_mat_data_index)
										{
											$position = $i;
										}
									}
									if ($position == -1)
									{
										for ($i=0; $i<sizeof($db_tea_data_home_index); $i++)
										{
											if ($db_tea_data_home_index[$i] == $live_tea_data_index_home)
											{
												if ($db_tea_data_away_index[$i] == $live_tea_data_index_away)
													$position = $i;
												else
												{ 
													//log away index missing
													$error = 1;
												}
											}
											else 
											{
												if ($db_tea_data_away_index[$i] == $live_tea_data_index_away)
												{ 
													//log home index missing
													$error = 2;
												}
											}
										}
									}
									if ($position == -1)
									{
										for ($i=0; $i<sizeof($db_tea_data_home_name); $i++)
										{
											if ($db_tea_data_home_name[$i] == $live_tea_data_name_home)
											{
												if ($db_tea_data_away_name[$i] == $live_tea_data_name_away)
													$position = $i;
											}
										}
									}
									
									if ($position > -1)
									{
										$result = "";
										$result_home = 0;
										$result_away = 0;
										$resultstring = "";
										$mat_status = 1;
										if (strlen($value2["et_score"]) > 0)
										{
											if (strtoupper($live_status) == "FINISHED")
											{
												$result = $value2["ft_score"];
												$mat_status = 2;
											}
											else
											{
												$resultstring = str_replace(" ","", ($value2["score"]."nV"));
												$result = $value2["ft_score"];
											}
										}
										else
										{
											if (strtoupper($live_status) == "FINISHED")
											{
												$result = $value2["ft_score"];
												$mat_status = 2;
											}
											else
											{
												$result = $value2["score"];	
											}
										}
										$res = explode("-", $result);
										if (sizeof($res) == 2)
										{
											$result_home = trim($res[0]);
											$result_away = trim($res[1]);
										}
										//Post to Server
										$query = "update mat_match set mat_data_index='".$live_mat_data_index."', mat_result_home=".$result_home.", mat_result_away=".$result_away.", mat_status=".$mat_status.", mat_result_string='".$resultstring."' where mat_index=".$db_mat_index[$position]; 
										$q_a = array();
										$q_a [] = strval($live_mat_data_index); 
										$q_a [] = strval($result_home); 
										$q_a [] = strval($result_away); 
										$q_a [] = strval($mat_status); 
										$q_a [] = strval($resultstring);
										$q_a [] = strval($db_mat_index[$position]);
										
										$data_from_server = file_get_contents ("https://example.com/t/cronrasp.php?q_a=".serialize($q_a));
										//mysqli_query ($dbconn, "update mat_match set mat_data_index='".$live_mat_data_index."', mat_result_home=".$result_home.", mat_result_away=".$result_away.", mat_status=".$mat_status.", mat_result_string='".$resultstring."' where mat_index=".$db_mat_index[$position]);
										//bewerte_match($dbconn, $db_mat_index[$position]);
										if ($data_from_server == "2")
										{
											//hat geklappt
											mysqli_query ($dbconn, "INSERT INTO tim_timestamp (tim_info, tim_error) VALUES('OK', 0)");
											mysqli_query ($dbconn, "update mat_match set mat_data_index='".$live_mat_data_index."', mat_result_home=".$result_home.", mat_result_away=".$result_away.", mat_status=".$mat_status.", mat_result_string='".$resultstring."' where mat_index=".$db_mat_index[$position]);
										
										}
										else
										{
											//error
											mysqli_query ($dbconn, "INSERT INTO tim_timestamp (tim_info, tim_error) VALUES('".serialize($q_a)."', 1)");
											mysqli_query ($dbconn, "update mat_match set mat_data_index='".$live_mat_data_index."', mat_result_home=".$result_home.", mat_result_away=".$result_away.", mat_status=".$mat_status.", mat_result_string='".$resultstring."' where mat_index=".$db_mat_index[$position]);
										
										}
									}
									else
									{
										$e_msg = "both team ids not found";
										if ($error == 1) $e_msg = "home team id not found";
										if ($error == 2) $e_msg = "away team id not found";
										mysqli_query ($dbconn, "INSERT INTO tim_timestamp (tim_info, tim_error) VALUES('".$e_msg."', 2)");
									}
								}
							}
						}
					}
				}
				//mysqli_query ($dbconn, "INSERT INTO tim_timestamp (tim_info, tim_error) VALUES('')");
		//	}
		//	if (mysqli_num_rows($result) == 0)
		}
		
		$result = mysqli_query ($dbconn, "SELECT * FROM tim_timestamp WHERE tim_date >= DATE_SUB(NOW(),INTERVAL 15 MINUTE)");
		if (mysqli_num_rows($result) == 0)
		{
			mysqli_query ($dbconn, "INSERT INTO tim_timestamp (tim_info, tim_error) VALUES('no live match atm', 3)");
		}
	}
	
	getData($dbconn);
	//mysqli_query ($dbconn, "INSERT INTO tim_timestamp (tim_info, tim_error) VALUES('check', 2)");
	//$data_from_api = file_get_contents ("https://example.com/t/cronrasp_t.php?a=1");
	//echo "Antwort=".$data_from_api;
	
?>