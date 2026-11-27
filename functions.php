<?php
	include_once __DIR__ . '/config.php';
	date_default_timezone_set('Europe/Berlin');

	$composer_autoload = __DIR__ . '/vendor/autoload.php';

	if (file_exists($composer_autoload))
	{
		require_once $composer_autoload;
	}
	else
	{
		require_once __DIR__ . '/phpmailer/PHPMailerAutoload.php';
		if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer') && class_exists('PHPMailer'))
		{
			class_alias('PHPMailer', 'PHPMailer\\PHPMailer\\PHPMailer');
		}
	}

	use PHPMailer\PHPMailer\PHPMailer;
	
	function sqlconnect()
	{
		include __DIR__ . '/sqlconn.php';
		$dbconn = mysqli_connect ($sqlhost, $sqluser, $sqlpass);
		if (!$dbconn)die('keine Verbindung zur Datenbank möglich. ' . mysqli_connect_error(). mysqli_error($dbconn));
		$db_selected = mysqli_select_db($dbconn, $dbname);
		if (!$db_selected) die ('Kann Datenbank nicht benutzen : ' . mysqli_error($dbconn));

		$timezone_offset = (new DateTime('now', new DateTimeZone('Europe/Berlin')))->format('P');
		mysqli_query($dbconn, "SET time_zone='".$timezone_offset."'");
		return $dbconn;
	}
	
	function showsetting($dbconn, $key)
	{
		$value = "";
		$result = mysqli_query ($dbconn,"select set_value from set_setting where set_key='".$key."'");
		while ($row = mysqli_fetch_array($result)) $value =  $row['set_value'];
		return $value;
	}
	
	function showtirsetting($dbconn, $index, $col)
	{
		$value = "";
		$result = mysqli_query ($dbconn,"select ".$col." from tir_tipprunde where tir_index='".$index."'");
		while ($row = mysqli_fetch_array($result)) $value =  $row[$col];
		return $value;
	}
	
	
	function showusrsetting($dbconn, $index, $col)
	{
		$value = "";
		$result = mysqli_query ($dbconn,"select ".$col." from usr_user where usr_index='".$index."'");
		while ($row = mysqli_fetch_array($result)) $value =  $row[$col];
		return $value;
	}

	function getYoutubeVideoId($url)
	{
		$url_parts = parse_url(html_entity_decode($url, ENT_QUOTES, 'UTF-8'));
		if ($url_parts === false || !isset($url_parts['host'])) return "";

		$host = strtolower($url_parts['host']);
		if (strpos($host, 'www.') === 0) $host = substr($host, 4);

		$video_id = "";
		if ($host === 'youtu.be')
		{
			$video_id = trim(isset($url_parts['path']) ? $url_parts['path'] : "", '/');
		}
		elseif ($host === 'youtube.com' || substr($host, -12) === '.youtube.com')
		{
			$path = isset($url_parts['path']) ? $url_parts['path'] : "";
			if ($path === '/watch')
			{
				$query = array();
				parse_str(isset($url_parts['query']) ? $url_parts['query'] : "", $query);
				$video_id = isset($query['v']) ? $query['v'] : "";
			}
			elseif (preg_match('~^/(?:embed|shorts)/([A-Za-z0-9_-]{11})~', $path, $matches))
			{
				$video_id = $matches[1];
			}
		}

		return preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id) ? $video_id : "";
	}

	function getYoutubeEmbedHtml($video_id)
	{
		$video_id = htmlspecialchars($video_id, ENT_QUOTES, 'UTF-8');
		return '<span class="youtube-embed"><iframe width="560" height="315" src="https://www.youtube.com/embed/'.$video_id.'" title="YouTube video player" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></span>';
	}

	function renderCommentText($text)
	{
		$text = preg_replace_callback(
			'~<a\b[^>]*href=(["\'])(https?://(?:www\.)?(?:youtube\.com|youtu\.be)/[^"\']+)\1[^>]*>.*?</a>~is',
			function ($matches)
			{
				$video_id = getYoutubeVideoId($matches[2]);
				return $video_id !== "" ? getYoutubeEmbedHtml($video_id) : $matches[0];
			},
			$text
		);

		return preg_replace_callback(
			'~(?<!["\'=])(https?://(?:www\.)?(?:youtube\.com/(?:watch\?[^\s<]+|shorts/[A-Za-z0-9_-]{11}(?:\?[^\s<]*)?)|youtu\.be/[A-Za-z0-9_-]{11}(?:\?[^\s<]*)?))~i',
			function ($matches)
			{
				$video_id = getYoutubeVideoId($matches[1]);
				return $video_id !== "" ? getYoutubeEmbedHtml($video_id) : $matches[0];
			},
			$text
		);
	}

	function getOpenAiFootballTip($prompt, $api_key)
	{
		if ($api_key === "" || !function_exists('curl_init')) return null;

		$curl = curl_init('https://api.openai.com/v1/responses');
		curl_setopt_array($curl, array(
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTPHEADER => array(
				'Authorization: Bearer '.$api_key,
				'Content-Type: application/json'
			),
			CURLOPT_POSTFIELDS => json_encode(array(
				'model' => 'gpt-5.4-nano',
				'input' => $prompt,
				'reasoning' => array('effort' => 'none'),
				'max_output_tokens' => 30
			))
		));

		$response = curl_exec($curl);
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if ($response === false || $http_status < 200 || $http_status >= 300) return null;

		$response_data = json_decode($response, true);
		if (!isset($response_data['output']) || !is_array($response_data['output'])) return null;

		$response_text = "";
		foreach ($response_data['output'] as $output)
		{
			if (!isset($output['content']) || !is_array($output['content'])) continue;
			foreach ($output['content'] as $content)
			{
				if (isset($content['type']) && $content['type'] === 'output_text' && isset($content['text']))
				{
					$response_text .= $content['text'];
				}
			}
		}

		if (!preg_match('/\b(\d{1,2})\s*[-:]\s*(\d{1,2})\b/', $response_text, $matches)) return null;
		return array((int)$matches[1], (int)$matches[2]);
	}
	
	function userexists($dbconn, $username, $email)
	{
		$usr_index = 0;
		if (strlen($username) > 0)
		{
			$result = mysqli_query ($dbconn,"select usr_index from usr_user where usr_name='".$username."'");
			while ($row = mysqli_fetch_array($result)) $usr_index =  $row['usr_index'];
		}
		if (($usr_index == 0) && (strlen($email) > 0))
		{
			$result = mysqli_query ($dbconn,"select usr_index from usr_user where usr_email='".$email."'");
			while ($row = mysqli_fetch_array($result)) $usr_index =  $row['usr_index'];
		}
		return $usr_index;
	}
	
 	function mysqli_num_rows_c($result)
	{
		$counter = 0;
		if (mysqli_num_rows($result) !== false) $counter = mysqli_num_rows($result);
		return $counter;
	}
	
	function konform($s)
	{
		$new_s = trim($s);
		$new_s = str_replace('"', '', $new_s);
		$new_s = str_replace('\"', '', $new_s);
		$new_s = str_replace('\'', ' ', $new_s);
		$new_s = str_replace('&nbsp;', ' ', $new_s);
		$new_s = str_replace('<p>', ' ', $new_s);
		$new_s = str_replace('</p>', ' ', $new_s);
	//	$new_s = str_replace('<', '', $new_s);
	//	$new_s = str_replace('>', '', $new_s);
		$new_s = trim($new_s);

		return $new_s;
	}
	
	function issel($v, $z)
	{
		$s = "";
		if ($v == $z) $s = 'selected="selected"';
		return $s;	
	}
	
	function ischk($v, $z)
	{
		$s = "";
		if ($v == $z) $s = ' checked ';
		return $s;	
	}
		
	function sendmail($usr_index, $dbconn, $type, $msg, $tir_index = 0)
	{
		//include_once('phpmailer/class.phpmailer.php');
		//include_once('phpmailer/PHPMailerAutoload.php');
		
		$usr_email = "";
		$usr_link = "";
		$result = mysqli_query ($dbconn, "select usr_email, usr_hash from usr_user where usr_index=".$usr_index);
		while ($row = mysqli_fetch_array($result))
		{
			$usr_email = $row['usr_email'];
			$usr_link = env('APP_URL', 'http://localhost:8000/')."aktivieren.php?hash=".$row['usr_hash'];
		}	
		
		$mail = new PHPMailer(); 
		date_default_timezone_set('Etc/UTC');
		$mail->isSMTP();
		//$mail->SMTPDebug = 2;
		//$mail->Debugoutput = 'html';
		$mail->Host = env('SMTP_HOST', '');
		$mail->Port = intval(env('SMTP_PORT', '587'));
		$mail->SMTPSecure = 'tls';
		$mail->SMTPAuth = true;
		$mail->IsHTML(true);
		$mail->Username = env('SMTP_USER', '');
		$mail->Password = env('SMTP_PASSWORD', '');
		$mail->SetFrom(env('SMTP_FROM', 'noreply@example.com'), env('SMTP_FROM', 'noreply@example.com'));
		$mail->addReplyTo(env('SMTP_FROM', 'noreply@example.com'), env('SMTP_FROM', 'noreply@example.com'));

		
		if ($type == 0) //Aktivierung
		{
			$mail->addAddress($usr_email, '');
			$mail->Subject = 'Aktivierung deines Benutzerkontos auf tipppfehler.de';
			$mail->Body = 'Guten Tag.<br><br>Vielen Dank f&uuml;r deine Registriereung.			
			<br><br> Bevor du dich einloggen kannst, aktiviere bitte dein Benutzerkonto indem du auf nachfolgenden Link klickst:<br><br><a href="'.$usr_link.'">'.$usr_link.'</a><br><br>
			&Uuml;berweise bitte ausserdem den Einsatz von '.showtirsetting($dbconn, $tir_index, 'tir_einsatz').' Euro '.showtirsetting($dbconn, $tir_index, 'tir_bezahlung').'.';
		}
		elseif ($type == 1) //passwort vergessen
		{
			$mail->addAddress($usr_email, '');
			$mail->Subject = 'Neues Passwort tipppfehler.de';
			$mail->Body = $msg;
		}
		elseif ($type == 2) //Fehlende Tipps
		{
			$mail->addAddress($usr_email, '');
			$mail->Subject = 'Dir fehlen Tipps !!! tipppfehler.de';
			$mail->Body = $msg;
		}
		elseif ($type == 3) //Fehlende Bezahlung
		{
			$mail->addAddress($usr_email, '');
			$mail->Subject = 'tippeinsatz noch nicht bezahlt';
			$mail->Body = $msg;
			
		}
		if ($mail->send()) 
			return true;
		else
			return false;
	}
	
	function isEndrunde($dbconn)
	{
		$er = false;
		$result = mysqli_query ($dbconn, "SELECT mat_starttime, NOW() as now FROM mat_match where mat_grp_index<=".(showsetting($dbconn, 'anzahl_gruppen'))." order by mat_starttime desc LIMIT 1");
		while ($row = mysqli_fetch_array($result))
		{
			if ($row['mat_starttime'] < $row['now']) $er = true;
		}
		return 	$er;
	}
	
	function getWeekDay($weekday)
	{
		switch($weekday)
		{
			case 0: return 'Mo'; break;
			case 1: return 'Di'; break;
			case 2: return 'Mi'; break;
			case 3: return 'Do'; break;
			case 4: return 'Fr'; break;
			case 5: return 'Sa'; break;
			case 6: return 'So'; break;
			default : return '-';
		}
	}
	function getlivecolor($status)
	{
		$color='';
		if ($status == 1) $color=' red';
		return $color;
	}
	function checkifsaveok($dbconn, $mat_index)
	{
		$result=mysqli_query($dbconn, "SELECT (mat_status = 0 AND mat_starttime > NOW()) AS can_save FROM mat_match WHERE mat_index=".(int)$mat_index);
		$row=mysqli_fetch_array($result);
		return ($row && (int)$row['can_save'] === 1);
	}
	
	function tendenz_richtig($r1, $r2, $t1, $t2)
	{
		$result=0;
		if ((!is_null($t1)) && (!is_null($t2)))
		{
			if (($r1 <> $t1) || ($r2 <>$t2))
			{
				if((($r1<$r2) && ($t1<$t2)) || (($r1==$r2) && ($t1==$t2)) || (($r1>$r2) && ($t1>$t2)))
				{
					$result=1;
				}
			}
		}
		return $result;
	}
	
	function ergebnis_richtig($r1, $r2, $t1, $t2)
	{
		$result=0;
		if ((!is_null($t1)) && (!is_null($t2)))
		{
			if (($r1 == $t1) && ($r2 == $t2))
				$result=1;
		}		
		return $result;
	}
		
	function sortArrayByFields($arr, $fields)
	{
		if (!is_array($arr) || sizeof($arr) === 0) {
			return array();
		}

		$sortFields = array();
		$args       = array();

		foreach ($arr as $key => $row) 
		{
			foreach ($fields as $field => $order) 
			{
				$sortFields[$field][$key] = isset($row[$field]) ? $row[$field] : null;
			}
		}

		foreach ($fields as $field => $order) 
		{
			if (!isset($sortFields[$field])) {
				$sortFields[$field] = array();
			}
			$args[] = $sortFields[$field];

			if (is_array($order)) 
			{
				foreach ($order as $pt) 
				{
					$args[] = $pt;
				}
			} 
			else 
			{
				$args[] = $order;
			}
		}
		$args[] = &$arr;

		if (sizeof($args) <= 1) {
			return $arr;
		}

		call_user_func_array('array_multisort', $args);

		return $arr;
	}
	
	function tabchanged($last_poi, $last_erg, $last_ten, $last_gti, $last_sti, $last_aus, $last_kit, $poi, $erg, $ten, $gti, $sti, $aus, $kit)
	{	
		if ($last_poi != $poi) return true;
		if ($last_erg != $erg) return true;
		if ($last_ten != $ten) return true;
		if ($last_gti != $gti) return true;
		if ($last_sti != $sti) return true;
		if ($last_aus != $aus) return true;
		if ($last_aus != $aus) return true;
		if ($last_kit != $kit) return true;
		return false;
	}	

	function bewerte_match($dbconn, $mat_index)
	{	
		//lösche alte tipbewertungen
		mysqli_query($dbconn, 'UPDATE tip_tipp SET tip_points_aus=0, tip_points_erg=0, tip_points_ten=0, tip_evaluated=0 WHERE tip_mat_index="'.$mat_index.'"');
		//lösche alte gti
		mysqli_query($dbconn, 'UPDATE gti_grouptipp INNER JOIN mat_match ON gti_grp_index=mat_grp_index AND (gti_tea_index=mat_tea_index_home OR gti_tea_index=mat_tea_index_away) SET gti_points=0, gti_evaluated=0 WHERE mat_index="'.(int)$mat_index.'"');
		$grp_index = 0; 
		$grp_vorrunde = 0;
		$result=mysqli_query($dbconn, "select grp_vorrunde, grp_index from grp_group inner join mat_match on mat_grp_index=grp_index where mat_index=".$mat_index);
		$row = mysqli_fetch_array($result);
		if (!$row) return;
		$grp_vorrunde = $row['grp_vorrunde'];
		$grp_index = $row['grp_index'];
		
		//bewerte tipps
		$result=mysqli_query($dbconn, "select mat_result_home, mat_result_away, mat_status from mat_match where mat_index=".$mat_index);
		while ($row = mysqli_fetch_array($result))
		{
			if ($row['mat_status'] > 0)
			{
				$result_t=mysqli_query($dbconn, "select tip_index, tip_result_home, tip_result_away, mat_status from tip_tipp inner join mat_match on mat_index=tip_mat_index where mat_index=".$mat_index);
				while ($row_t = mysqli_fetch_array($result_t))
				{
					$eval=$row['mat_status'];
					
					if (($row_t['tip_result_home'] == $row['mat_result_home']) && ($row_t['tip_result_away'] == $row['mat_result_away']))
					{
						if ($grp_vorrunde == 1)
							mysqli_query($dbconn, "update tip_tipp set tip_points_erg=".showsetting($dbconn, 'punkte_richtiges_erg').", tip_evaluated=".$eval." where tip_index=".$row_t['tip_index']);
						if ($grp_vorrunde == 0)
							mysqli_query($dbconn, "update tip_tipp set tip_points_erg=".showsetting($dbconn, 'punkte_richtiges_erg_er').", tip_evaluated=".$eval." where tip_index=".$row_t['tip_index']);
					}
					elseif (tendenzpasst($row['mat_result_home'], $row['mat_result_away'], $row_t['tip_result_home'], $row_t['tip_result_away']))
					{
						mysqli_query($dbconn, "update tip_tipp set tip_points_ten=".showsetting($dbconn, 'punkte_richtige_ten').", tip_evaluated=".$eval." where tip_index=".$row_t['tip_index']);
					}
					else
					{
						mysqli_query($dbconn, "update tip_tipp set tip_evaluated=".$eval." where tip_index=".$row_t['tip_index']);
					}
				}
				
				//bewerte aussenseitertipp
				$result=mysqli_query($dbconn, "select tir_index from tir_tipprunde");
				while ($row = mysqli_fetch_array($result))
				{
					$count_usr = 0;
					$count_hit = 0;
					$usr = array();
					$result_t=mysqli_query($dbconn, "select tip_index, tip_points_erg, tip_usr_index, tip_points_ten 
					from tip_tipp 
					inner join usr_user on usr_index=tip_usr_index 
					INNER JOIN urm_usrtirmapping on urm_usr_index=usr_index 
					inner join mat_match on mat_index=tip_mat_index
					where mat_index=".$mat_index." and urm_tir_index=".$row['tir_index']);
					while ($row_t = mysqli_fetch_array($result_t))
					{
						$count_usr ++;
						if (($row_t['tip_points_erg']>0) || ($row_t['tip_points_ten']>0))
						{
							$count_hit++;
							$usr[] = $row_t['tip_index'];
						}
					}
					if ($count_usr < 10) $count_usr = 10;
					if (($count_hit * 100 / $count_usr) <= 10)
					{
						//Aussenseitertipp !
						
						foreach ($usr as $ui)
						{
							$punkte_richtiger_aus = showsetting($dbconn, 'punkte_richtiger_aus');
							mysqli_query($dbconn, "update tip_tipp set tip_points_aus=".$punkte_richtiger_aus." where tip_index=".$ui);
							
						}
						
					}
					
				}
					
			}
			else
			{
				$result_t=mysqli_query($dbconn, "select tip_index, tip_result_home, tip_result_away, mat_status from tip_tipp inner join mat_match on mat_index=tip_mat_index where mat_index=".$mat_index);
				while ($row_t = mysqli_fetch_array($result_t))
				{
					mysqli_query($dbconn, "update tip_tipp set tip_points_erg=0, tip_points_ten=0, tip_points_aus=0, tip_evaluated=0 where tip_index=".$row_t['tip_index']);
				}
			}
		}
		
		//bewerte gruppentipp
		if ($grp_vorrunde == 0)
		{				
			$result_m=mysqli_query($dbconn, "select mat_tea_index_home, mat_tea_index_away from mat_match where mat_index=".$mat_index);
			while ($row_m = mysqli_fetch_array($result_m))
			{
				$result_t=mysqli_query($dbconn, "select gti_index from gti_grouptipp where gti_grp_index=".$grp_index." and gti_tea_index=".$row_m['mat_tea_index_home']);
				while ($row_t = mysqli_fetch_array($result_t))
				{
					mysqli_query($dbconn, "update gti_grouptipp set gti_points=".showsetting($dbconn, 'punkte_richtiger_gti').", gti_evaluated=1 where gti_index=".$row_t['gti_index']);
				}
				$result_t=mysqli_query($dbconn, "select gti_index from gti_grouptipp where gti_grp_index=".$grp_index." and gti_tea_index=".$row_m['mat_tea_index_away']);
				while ($row_t = mysqli_fetch_array($result_t))
				{
					mysqli_query($dbconn, "update gti_grouptipp set gti_points=".showsetting($dbconn, 'punkte_richtiger_gti').", gti_evaluated=1 where gti_index=".$row_t['gti_index']);
				}
			}
		}
		
		
	}
	
	function tendenzpasst($r1, $r2, $t1, $t2)
	{
		$result=false;
		if (($r1 <> $t1) || ($r2 <>$t2))
		{
			if((($r1<$r2) && ($t1<$t2)) || (($r1==$r2) && ($t1==$t2)) || (($r1>$r2) && ($t1>$t2)))
			{
				$result=true;
			}
		}
		return $result;
	}
	
	function getmatchids($dbconn, $groups)
	{
		$filter = "";
		foreach ($groups as $grp_index) 
		{
			if (strlen ($filter) > 0) $filter = $filter.",";
			$filter = $filter.$grp_index;
		}
		$result = mysqli_query ($dbconn, "select grp_index, grp_data_index from grp_group where grp_index in (".$filter.")");
		while ($row = mysqli_fetch_array($result))
		{
			$data_from_api = file_get_contents ("https://livescore-api.com/api-client/fixtures/matches.json?&key=".showsetting($dbconn, 'api_key')."&secret=".showsetting($dbconn, 'api_secret')."&competition_id=362&lang=de&page=2");

			//$data_from_api = file_get_contents ("https://livescore-api.com/api-client/fixtures/matches.json?key=".showsetting($dbconn, 'api_key')."&secret=".showsetting($dbconn, 'api_secret')."&league=".$row['grp_data_index']);
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
								$mat_data_index=$value2["id"];
								$tea_data_index_home=$value2["home_id"];
								$tea_data_index_away=$value2["away_id"];
								
								//suche pendant
								$result_mat = mysqli_query ($dbconn, "select mat_index, th.tea_data_index as tea_data_index_home , ta.tea_data_index as tea_data_index_away from mat_match 
																		inner join tea_team th ON th.tea_index = mat_tea_index_home
																		inner join tea_team ta ON ta.tea_index = mat_tea_index_away
																		where mat_grp_index=".$row['grp_index']." 
																			and th.tea_data_index = ".$tea_data_index_home." 
																			and ta.tea_data_index = ".$tea_data_index_away);						
																			
								while ($row_mat = mysqli_fetch_array($result_mat))
								{
									 mysqli_query ($dbconn, "update mat_match set mat_data_index=".$mat_data_index." where mat_index=".$row_mat['mat_index']);
								}
							}
						}
					}
				}
			}	
		}
	}
	
	function generiere_autotipps($dbconn, $mat_index)
	{
		$result = mysqli_query ($dbconn, "select distinct usr_index, usr_auto from usr_user 
											left join tip_tipp on tip_usr_index=usr_index and tip_mat_index=".$mat_index."
											where tip_index is null and usr_auto>0");
		while ($row = mysqli_fetch_array($result))
		{
			$home_mw = rand(0, 3);
			$away_mw = rand(0, 3);
		
			$home_su = 0;
			$away_su = 0;
			$result_mat = mysqli_query ($dbconn, "select sum(tip_result_home) as home_su, sum(tip_result_away) as away_su from tip_tipp where tip_mat_index=".$mat_index);
			while ($row_mat = mysqli_fetch_array($result_mat))
			{
				$home_su = $row_mat["home_su"];
				$away_su = $row_mat["away_su"];
			}
			
			$count_t = 0;
			$result_tip = mysqli_query ($dbconn, "select count(tip_index) as count_t from tip_tipp where tip_mat_index=".$mat_index);
			while ($row_tip = mysqli_fetch_array($result_tip))
			{
				$count_t = $row_tip["count_t"];
			}
			
			if ($count_t > 0) 
			{
				$home_mw = round($home_su / $count_t);
				$away_mw = round($away_su / $count_t);
				if ($row["usr_auto"] == 1)
				{
					$vz = rand(0, 1); 
					if ($vz == 0) $home_mw = $home_mw + rand(0, 2);
					else $home_mw = $home_mw - rand(0, 2);
					
					$vz = rand(0, 1); 
					if ($vz == 0) $away_mw = $away_mw + rand(0, 2);
					else $away_mw = $away_mw - rand(0, 2);
					
					if ($home_mw<0) $home_mw = 0;
					if ($away_mw<0) $away_mw = 0;
					
				}
			}
			$already = 0;
			$result2 = mysqli_query ($dbconn, "select tip_index from tip_tipp where tip_usr_index=".$row["usr_index"]." and tip_mat_index=".$mat_index);
			while ($row2 = mysqli_fetch_array($result2)) $already = $row2["tip_index"];


			if ($already == 0)
			{
				if ($row["usr_auto"] == 2)
				{
					$teamname_home = "";
					$result_tn = mysqli_query ($dbconn, "select tea_name from tea_team inner join mat_match on tea_index=mat_tea_index_home where mat_index=".$mat_index);
					while ($row_tn = mysqli_fetch_array($result_tn)) $teamname_home = $row_tn["tea_name"];
					$teamname_away = "";
					$result_tn = mysqli_query ($dbconn, "select tea_name from tea_team inner join mat_match on tea_index=mat_tea_index_away where mat_index=".$mat_index);
					while ($row_tn = mysqli_fetch_array($result_tn)) $teamname_away = $row_tn["tea_name"];
					
					$promt = "Heute spielt ".$teamname_home." gegen ".$teamname_away." beim Fussball. 
					Was ist dein Tipp wie das Speil ausgeht ? Mich interessiert hierbei nur das Ergebnis nach 90 Minuten (also nach der regulären Spielzeit). Kein Elfmeterschiessen und keine Verlängerung.
					Bitte berücksichtige Stärke der Teams usw. und natürlich auch die Form im bisherigen Tunierverlauf.
					Da ich das Ergebnis automatisch weiter verarbeite, antworte bitte nur mit dem Ergebnis nach 90 Minuten in der Form ToreTeam1-ToreTeam2. Also z.B. 1-2";
				
					//$openai_api_key = getenv('OPENAI_API_KEY');
					$openai_api_key = env('OPENAI_API_KEY', '');
					if ($openai_api_key === false || $openai_api_key === "")
					{
						$openai_api_key = showsetting($dbconn, 'openai_api_key');
					}

					$ki_tipp = getOpenAiFootballTip($promt, $openai_api_key);
					if ($ki_tipp !== null)
					{
						$home_mw = $ki_tipp[0];
						$away_mw = $ki_tipp[1];
					}
					else
					{
						$home_mw = 5*$home_mw ;
						$away_mw = 5*$away_mw;
					}

				}
				mysqli_query($dbconn, "insert into tip_tipp
													(tip_mat_index,tip_usr_index,tip_result_home,tip_result_away, tip_ki)
												values
													(".$mat_index.", ".$row["usr_index"].", ".$home_mw.", ".$away_mw.", 1)");
				
			}
		}
	}
	
	function generiere_autogti($dbconn)
	{
		$result_usr = mysqli_query ($dbconn, "select usr_index, usr_auto from usr_user");
		while ($row_usr = mysqli_fetch_array($result_usr))
		{
			$result_grp = mysqli_query ($dbconn, "select grp_index, grp_anzahlteams from grp_group where grp_vorrunde=0 and grp_anzahlteams>0");
			while ($row_grp = mysqli_fetch_array($result_grp))
			{
				$tipps = array();
				$count = 0;
				$result_gti = mysqli_query ($dbconn, "select gti_tea_index from gti_grouptipp where gti_grp_index=".$row_grp["grp_index"]." and gti_usr_index=".$row_usr["usr_index"]);
				while ($row_gti = mysqli_fetch_array($result_gti))
				{
					$count ++;
					$tipps [] = $row_gti["gti_tea_index"];
				}
				
				
				
				while ($count < $row_grp["grp_anzahlteams"])
				{
					
					echo $row_usr["usr_index"]."<br>";
					$tt = 0; 
					$found = false;

					$maxteaid = 1;
					$result_mid = mysqli_query($dbconn, "select max(tea_index) as tea_max from tea_team");
					while ($row_mid = mysqli_fetch_array($result_mid)) $maxteaid = $row_mid["tea_max"];

					while (!$found)
					{
						
						$tt = rand(1, $maxteaid);
						if (!in_array($tt, $tipps))$found = true;
						
					}
					$tipps [] = $tt; 
					$count ++; 
					mysqli_query($dbconn, "insert into gti_grouptipp (gti_usr_index, gti_grp_index, gti_tea_index) values (".$row_usr["usr_index"].", ".$row_grp["grp_index"].", ".$tt.")");
				}
			}
			$sti_index = 0;
			$sti_meister = 0;
			$result_sti = mysqli_query ($dbconn, "select sti_index, sti_meister from sti_spezialtipp where sti_usr_index=".$row_usr["usr_index"]);
			while ($row_sti = mysqli_fetch_array($result_sti))     
			{
				$sti_index = $row_sti["sti_index"];
				$sti_meister = $row_sti["sti_meister"];
			}
			if (($sti_index==0) || ($sti_meister==0))
			{
					mysqli_query($dbconn, "insert into sti_spezialtipp 
													(sti_usr_index, sti_meister, sti_koenig)
												values
													(".$row_usr["usr_index"].", ".rand(1, 24).", '')");
			}
		}
	}
	
	function getmatidfromdataid($dbconn, $dataid)
	{
		$result = mysqli_query ($dbconn, "select mat_index from mat_match where mat_data_index=".$dataid);
		while ($row = mysqli_fetch_array($result))
			return $row['mat_index'];
	}
	
	
	function uploadfile($datei, $usr_index)
	{
		$dateiname = "";
		if (strlen($datei['name']) > 0)
		{			
			$verzeichnis = "uplds";
			$dateiname = $usr_index."_".date('Y-m-d-H-i-s').".".pathinfo($datei['name'], PATHINFO_EXTENSION);

			if (!isset($datei['error']) || $datei['error'] !== UPLOAD_ERR_OK)
			{
				$uploadError = isset($datei['error']) ? $datei['error'] : -1;
				$_SESSION['upload_error'] = 'Upload fehlgeschlagen (PHP-Fehlercode '.$uploadError.').';
				error_log("tipppfehler uploadfile: PHP upload error code=".$uploadError." user=".$usr_index);
				return "";
			}

			if (!is_uploaded_file($datei['tmp_name']))
			{
				$_SESSION['upload_error'] = 'Upload fehlgeschlagen: temporaere Datei wurde nicht erkannt.';
				error_log("tipppfehler uploadfile: tmp file is not uploaded file, user=".$usr_index);
				return "";
			}

			$bildInfo = @getimagesize($datei['tmp_name']);
			if ($bildInfo === false)
			{
				$_SESSION['upload_error'] = 'Upload fehlgeschlagen: Datei ist kein gueltiges Bild.';
				error_log("tipppfehler uploadfile: getimagesize failed, user=".$usr_index.", file=".$datei['name']);
				return "";
			}

			$allowedTypes = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
			if (!in_array($bildInfo[2], $allowedTypes, true))
			{
				$_SESSION['upload_error'] = 'Upload fehlgeschlagen: erlaubt sind nur JPG, PNG oder GIF.';
				error_log("tipppfehler uploadfile: unsupported image type=".$bildInfo[2].", user=".$usr_index.", file=".$datei['name']);
				return "";
			}

			if (!is_dir($verzeichnis) || !is_writable($verzeichnis))
			{
				$_SESSION['upload_error'] = 'Upload fehlgeschlagen: Upload-Ordner nicht beschreibbar.';
				error_log("tipppfehler uploadfile: upload directory not writable, dir=".$verzeichnis.", user=".$usr_index);
				return "";
			}

			if (!move_uploaded_file($datei['tmp_name'], $verzeichnis."/".$dateiname))
			{
				$_SESSION['upload_error'] = 'Upload fehlgeschlagen: Datei konnte nicht gespeichert werden.';
				error_log("tipppfehler uploadfile: move_uploaded_file failed, user=".$usr_index.", target=".$verzeichnis."/".$dateiname);
				return "";
			}

			chmod($verzeichnis."/".$dateiname,0666);

			$gdReady = function_exists('imagecreatefromjpeg') && function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled');
			if ($gdReady)
			{
				$resizeOk = smart_resize_image($verzeichnis."/".$dateiname, null, 400,600,true);
				if ($resizeOk === false)
				{
					$_SESSION['upload_error'] = 'Upload gespeichert, aber Bildverarbeitung fehlgeschlagen.';
					error_log("tipppfehler uploadfile: smart_resize_image failed, user=".$usr_index.", file=".$dateiname);
				}
			}
			else
			{
				$_SESSION['upload_error'] = 'Upload erfolgreich, aber GD ist auf dem Server nicht aktiv (kein Resize).';
				error_log("tipppfehler uploadfile: GD extension/functions missing, user=".$usr_index.", file=".$dateiname);
			}
		}	
		return $dateiname;
	}
	
	function smart_resize_image($file,
                              $string             = null,
                              $width              = 0, 
                              $height             = 0, 
                              $proportional       = false, 
                              $output             = 'file', 
                              $delete_original    = true, 
                              $use_linux_commands = false,
                              $quality            = 100,
                              $grayscale          = false
  		 ) {
      
    if ( $height <= 0 && $width <= 0 ) return false;
    if ( $file === null && $string === null ) return false;
    # Setting defaults and meta
    $info                         = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
    $image                        = '';
    $final_width                  = 0;
    $final_height                 = 0;
    list($width_old, $height_old) = $info;
	$cropHeight = $cropWidth = 0;
    # Calculating proportionality
    if ($proportional) {
      if      ($width  == 0)  $factor = $height/$height_old;
      elseif  ($height == 0)  $factor = $width/$width_old;
      else                    $factor = min( $width / $width_old, $height / $height_old );
      $final_width  = round( $width_old * $factor );
      $final_height = round( $height_old * $factor );
    }
    else {
      $final_width = ( $width <= 0 ) ? $width_old : $width;
      $final_height = ( $height <= 0 ) ? $height_old : $height;
	  $widthX = $width_old / $width;
	  $heightX = $height_old / $height;
	  
	  $x = min($widthX, $heightX);
	  $cropWidth = ($width_old - $width * $x) / 2;
	  $cropHeight = ($height_old - $height * $x) / 2;
    }
    # Loading image to memory according to type
    switch ( $info[2] ) {
      case IMAGETYPE_JPEG:  $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);  break;
      case IMAGETYPE_GIF:   $file !== null ? $image = imagecreatefromgif($file)  : $image = imagecreatefromstring($string);  break;
      case IMAGETYPE_PNG:   $file !== null ? $image = imagecreatefrompng($file)  : $image = imagecreatefromstring($string);  break;
      default: return false;
    }
    
    # Making the image grayscale, if needed
    if ($grayscale) {
      imagefilter($image, IMG_FILTER_GRAYSCALE);
    }    
    
    # This is the resizing/resampling/transparency-preserving magic
    $image_resized = imagecreatetruecolor( $final_width, $final_height );
    if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
      $transparency = imagecolortransparent($image);
      $palletsize = imagecolorstotal($image);
      if ($transparency >= 0 && $transparency < $palletsize) {
        $transparent_color  = imagecolorsforindex($image, $transparency);
        $transparency       = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
        imagefill($image_resized, 0, 0, $transparency);
        imagecolortransparent($image_resized, $transparency);
      }
      elseif ($info[2] == IMAGETYPE_PNG) {
        imagealphablending($image_resized, false);
        $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
        imagefill($image_resized, 0, 0, $color);
        imagesavealpha($image_resized, true);
      }
    }
    imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);
	
	
    # Taking care of original, if needed
    if ( $delete_original ) {
      if ( $use_linux_commands ) exec('rm '.$file);
      else @unlink($file);
    }
    # Preparing a method of providing result
    switch ( strtolower($output) ) {
      case 'browser':
        $mime = image_type_to_mime_type($info[2]);
        header("Content-type: $mime");
        $output = NULL;
      break;
      case 'file':
        $output = $file;
      break;
      case 'return':
        return $image_resized;
      break;
      default:
      break;
    }
    
    # Writing image according to type to the output destination and image quality
    switch ( $info[2] ) {
      case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
      case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
      case IMAGETYPE_PNG:
        $quality = 9 - (int)((0.9*$quality)/10.0);
        imagepng($image_resized, $output, $quality);
        break;
      default: return false;
    }
    return true;
  }
  
  function showkommentare($dbconn, $all, $tir_index, $m_id)
  {
	  echo '
			<b>
			<a onclick="toggleCompost()" href="#">Neuen Kommentar posten</a></b><br>
			<form action="member_post.php?m_id=5" method="post" enctype="multipart/form-data">
			<div id="compost" name="compost" style="display:none;">
				<textarea name="editor1" id="editor1"></textarea><script>CKEDITOR.replace( "editor1");</script><br>
				Bild hochladen: <input type="file" name="comimg" accept=".jpg, .jpeg, .gif, .png, .bmp"><br><br>
				<input type="submit" class="small" value="senden">
				<input type="hidden" name="newm" value="'.$m_id.'">
				</form>
			</div><br>';
			
			$filter = "";
			if ($all == 1) $filter = "LIMIT 20";
			$result=mysqli_query($dbconn, "SELECT com_text, DATE_FORMAT(com_timestamp, '%d.%m %H:%i' ) AS com_timestamp, com_color, com_media, usr_name, usr_vornachname from com_comment inner join usr_user on com_usr_index=usr_index INNER JOIN urm_usrtirmapping on urm_usr_index=usr_index where urm_tir_index=".$tir_index." order by com_index desc ".$filter);
			while($row=mysqli_fetch_array($result))
			{
				echo '<div class="comdiv bgc'.$row["com_color"].'">
					<b>'.$row["usr_name"].' ('.$row["usr_vornachname"].'), '.$row["com_timestamp"].':</b><br>';
				if (strlen($row["com_media"])>0) echo '<img src="images/uplds/'.$row["com_media"].'"><br>';
				echo renderCommentText($row["com_text"]).'</div>';
			}
			echo '
			<a href="member.php?m_id=5&all=1">Lade alle Kommentare</a>';
  }

	function addvz ($num)
	{
		$g = $num."";
		if ($num>=0)$g = "+".$num."";
		return $g;
	}
	
	function gettable($dbconn, $tir_index, $mat_index, $sieger=array(), $cmp=false)
	{
		$sieger_string ="";
		$secontablefilter ="";
		foreach ($sieger as $s) 
		{
			if (strlen($sieger_string) >0) $sieger_string=$sieger_string.",";
			$sieger_string = $sieger_string.$s;	
		}
		if (strlen($sieger_string) > 0) 
		{
			$sieger_string = " AND usr_index NOT IN (".$sieger_string.")";
			$secontablefilter =" AND mat_grp_index IN (SELECT grp_index FROM grp_group WHERE grp_vorrunde=0)";
		}
		 
		$tabelle = array();
		$mat_grp_index = 0;
		$mat_starttime = null;
		$bewerte_auch_endrunde = true;
		$filter = "where mat_index=".$mat_index; 
		if ($mat_index == 0) 	
			$filter = "where mat_status>0 order by mat_starttime asc LIMIT 1";
		if ($cmp)
			$filter = "where mat_index<>".$mat_index." and mat_status>0 order by mat_starttime desc LIMIT 1";
		
		$result = mysqli_query ($dbconn, "SELECT mat_index, mat_grp_index, mat_starttime FROM mat_match ".$filter);
		while ($row = mysqli_fetch_array($result))
		{ 
			$mat_index = $row['mat_index'];
			$mat_grp_index = $row['mat_grp_index'];
			$mat_starttime = $row['mat_starttime'];
			//$bewerte_auch_endrunde = ($row['mat_grp_index'] > 8 ? true : false);
		}
		$match_progress_filter = "mat_index<=".$mat_index;
		if (!is_null($mat_starttime))
			$match_progress_filter = "(mat_starttime<'".$mat_starttime."' OR (mat_starttime='".$mat_starttime."' AND mat_index<=".$mat_index."))";
		
		//erstelle spalten Array
		$eintrag = array ("usr", "poi", "ten", "erg", "gti", "sti", "aus");
		$result_usr=mysqli_query($dbconn, "select usr_index from usr_user INNER JOIN urm_usrtirmapping on urm_usr_index=usr_index where urm_tir_index=".$tir_index .$sieger_string);
		while ($row_usr	= mysqli_fetch_array($result_usr))
		{ 
			$usr_index = $row_usr["usr_index"]+0;
			$poi = 0;
			$ten = 0;
			$erg = 0;
			$gti = 0;
			$sti = 0;
			$aus = 0;
			$kit = 0;
			
			$result_tip=mysqli_query($dbconn, "select tip_points_erg, tip_points_ten, tip_points_aus, tip_ki, mat_grp_index from tip_tipp inner join mat_match on mat_index=tip_mat_index where ".$match_progress_filter." and tip_usr_index=".$usr_index.$secontablefilter);
			while ($row_tip	= mysqli_fetch_array($result_tip))
			{ 
				$poi = $poi + $row_tip["tip_points_erg"] + $row_tip["tip_points_ten"] + $row_tip["tip_points_aus"];
				if ($row_tip["tip_points_erg"] > 0)
					$erg ++;
				if ($row_tip["tip_points_ten"] > 0)
					$ten ++;
				if ($row_tip["tip_points_aus"] > 0)
					$aus ++;
				if ($row_tip["tip_ki"] > 0)
					$kit ++;
			}
			if (($bewerte_auch_endrunde) && (strlen($sieger_string)==0))
			{
				
				$result_gti=mysqli_query($dbconn, "select gti_points, gti_grp_index, gti_tea_index from gti_grouptipp where gti_usr_index=".$usr_index);
				while ($row_gti	= mysqli_fetch_array($result_gti))
				{ 
					if ($row_gti["gti_points"] > 0)
					{
						$result = mysqli_query ($dbconn, "SELECT mat_starttime FROM mat_match where mat_grp_index=".$row_gti["gti_grp_index"]." and (mat_tea_index_home=".$row_gti["gti_tea_index"]." OR mat_tea_index_away=".$row_gti["gti_tea_index"].")");
						while ($row = mysqli_fetch_array($result))
						{
							//if ($row['mat_starttime'] < $mat_starttime)
							//{
								$poi = $poi + $row_gti["gti_points"];
								$gti ++;
							//}
						}
					}
				}
				
				$result_sti=mysqli_query($dbconn, "select sti_points from sti_spezialtipp where sti_usr_index=".$usr_index);
				while ($row_sti	= mysqli_fetch_array($result_sti))
				{ 
					if ($row_sti["sti_points"] == (showsetting($dbconn, "punkte_richtiger_sti")+0))
					{
						$sti ++;
					
						$poi = $poi + (showsetting($dbconn, "punkte_richtiger_sti")+0);
					}
					/*if ($row_sti["sti_points"] > (showsetting($dbconn, "punkte_richtiger_sti")+0))
					{	$sti = $sti + 2;
					
						$poi = $poi + 2 * (showsetting($dbconn, "punkte_richtiger_sti")+0);
					}*/
				}
				
			}
			
			$eintrag = 	[
							"usr" => $usr_index,
							"poi" => $poi,
							"ten" => $ten,
							"erg" => $erg,
							"gti" => $gti,
							"sti" => $sti,
							"aus" => $aus,
							"kit" => $kit,
							"pla" => 0,
							"for" => 0
						];
						
						$tabelle[] = $eintrag;
			
		}
		
		$platz_print = 0;
		$platz_count = 0;
		$last_poi = -1;
		$last_erg = -1;
		$last_ten = -1;
		$last_gti = -1;
		$last_sti = -1;
		$last_aus = -1;
		$last_kit = -1;
		//weise positionen zu
		$tabelle = sortArrayByFields($tabelle,array('poi' => SORT_DESC,'ten' => SORT_DESC,'erg' => SORT_DESC,'gti' => SORT_DESC,'sti' => SORT_DESC,'aus' => SORT_DESC,'kit' => SORT_ASC));
		for($i=0; $i<sizeof($tabelle); $i++)
		{
			$usr = $tabelle[$i]["usr"];
			$poi = $tabelle[$i]["poi"];
			$erg = $tabelle[$i]["erg"];
			$ten = $tabelle[$i]["ten"];
			$gti = $tabelle[$i]["gti"];
			$sti = $tabelle[$i]["sti"];
			$aus = $tabelle[$i]["aus"];
			$kit = $tabelle[$i]["kit"];
			$platz_count ++;
			if (tabchanged($last_poi, $last_erg, $last_ten, $last_gti, $last_sti, $last_aus, $last_kit, $poi, $erg, $ten, $gti, $sti, $aus, $kit)) 
			{
				$platz_print = $platz_count;
				$last_poi = $poi;
				$last_erg = $erg;
				$last_ten = $ten;
				$last_gti = $gti;
				$last_sti = $sti;
				$last_aus = $aus;
				$last_kit = $kit;
			}
			
			$tabelle[$i]["pla"] = $platz_print;
		}
		
		if (!$cmp)
		{
			if ($mat_index==1)
			{
				for($i=0; $i<sizeof($tabelle); $i++)
				{
					$tabelle[$i]["for"] = "daumen_quer.gif";
				}
			}
			else
			{
				$tabelle_cmp = gettable($dbconn, $tir_index, $mat_index, $sieger, true);
				foreach ($tabelle_cmp as $eintrag_cmp)
				{
					$platz_vorher = $eintrag_cmp["pla"];
					for($i=0; $i<sizeof($tabelle); $i++)
					{
						if ($tabelle[$i]["usr"] == $eintrag_cmp["usr"])
						{
							//echo $tabelle[$i]["usr"]."_".$eintrag_cmp["pla"]."_".$tabelle[$i]["pla"]."<br>";
							if ($tabelle[$i]["pla"] == $eintrag_cmp["pla"])
								$tabelle[$i]["for"] = "daumen_quer.gif";
							elseif($tabelle[$i]["pla"] < $eintrag_cmp["pla"])
								$tabelle[$i]["for"] = "daumen_hoch.gif";
							elseif($tabelle[$i]["pla"] > $eintrag_cmp["pla"])
								$tabelle[$i]["for"] = "daumen_runter.gif"; 
						}
					}
				}
			}
		}
		
		return $tabelle;
		
	}
	
?>
