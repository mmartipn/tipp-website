<?php
	include 'functions.php';
	$dbconn = sqlconnect();
	
//get matches
echo "<table border=1>
<tr>
<td>MatID</td>
<td>date</td>
<td>Home</td>
<td>HomeID</td>
<td>Away</td>
<td>AwayID</td>
</tr>
";
$data_from_api = file_get_contents ("https://livescore-api.com/api-client/fixtures/matches.json?&key=".showsetting($dbconn, 'api_key')."&secret=".showsetting($dbconn, 'api_secret')."&competition_id=362&lang=de&page=1");
foreach(json_decode($data_from_api ,true) as $cc => $value) 
{
	if (strtoupper($cc) == "DATA")
	{
		foreach($value as $cure => $value1) 
		{
			if (strtoupper($cure) == "FIXTURES")
			{
				foreach($value1 as $cure1 => $value2) 
				{	
				
					$hid = 0;
					$result = mysqli_query ($dbconn, "select tea_index from tea_team where tea_data_index=".$value2["home_id"]);
					while ($row = mysqli_fetch_array($result)) $hid = $row["tea_index"];
					$aid = 0;
					$result = mysqli_query ($dbconn, "select tea_index from tea_team where tea_data_index=".$value2["away_id"]);
					while ($row = mysqli_fetch_array($result)) $aid = $row["tea_index"];
						
					$found = false;
					$result = mysqli_query ($dbconn, "select mat_index from mat_match where mat_tea_index_home=".$hid." and mat_tea_index_away=".$aid);
					while ($row = mysqli_fetch_array($result)) 
					{
						 mysqli_query ($dbconn, "UPDATE mat_match set mat_data_index=".$value2["id"]." where mat_index=".$row["mat_index"]);
						 $found = true;
					}
					
					if (!$found)
					{
					echo '<table border=1>
					<tr>
					<td>'.$value2["id"].'</td>
					<td>'.$value2["date"].' '.$value2["time"].'</td>
					<td>'.$value2["home_name"].'</td>
					<td>'.$value2["home_id"].'</td>
					<td>'.$value2["away_name"].'</td>
					<td>'.$value2["away_id"].'</td>
					</tr>';	
					}
						
				}
			}
		}
	}
}

echo"</table>";

?>