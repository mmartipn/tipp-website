<?php
	require_once __DIR__ . '/functions.php';

	header('Content-Type: text/plain; charset=utf-8');

	function cronjob_prepare_log($dbconn)
	{
		$cutoff = date('Y-m-d H:i:s', strtotime('-30 days'));
		$statement = mysqli_prepare($dbconn, "DELETE FROM crl_cronlog WHERE crl_date < ?");
		mysqli_stmt_bind_param($statement, 's', $cutoff);
		mysqli_stmt_execute($statement);
		mysqli_stmt_close($statement);
	}

	function cronjob_log($dbconn, $message, $level = 'INFO')
	{
		$level = strtoupper((string)$level);
		if (!in_array($level, array('INFO', 'WARNING', 'ERROR'), true)) $level = 'INFO';
		$message = (string)$message;
		$log_date = date('Y-m-d H:i:s');

		$statement = mysqli_prepare($dbconn, "INSERT INTO crl_cronlog (crl_date, crl_level, crl_message) VALUES (?, ?, ?)");
		mysqli_stmt_bind_param($statement, 'sss', $log_date, $level, $message);
		mysqli_stmt_execute($statement);
		mysqli_stmt_close($statement);

		if (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg')
		{
			echo '['.date('Y-m-d H:i:s').'] ['.$level.'] '.$message.PHP_EOL;
		}
	}

	function cronjob_normalize_name($name)
	{
		$name = html_entity_decode((string)$name, ENT_QUOTES, 'UTF-8');
		$name = preg_replace('/\s+/u', ' ', trim($name));
		return function_exists('mb_strtolower') ? mb_strtolower($name, 'UTF-8') : strtolower($name);
	}

	function cronjob_parse_score($score)
	{
		if (!preg_match('/^\s*(\d+)\s*-\s*(\d+)\s*$/', (string)$score, $matches)) return null;
		return array((int)$matches[1], (int)$matches[2]);
	}

	function cronjob_fetch_json($url)
	{
		if (function_exists('curl_init'))
		{
			$curl = curl_init($url);
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CONNECTTIMEOUT => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_FOLLOWLOCATION => true
			));
			$response = curl_exec($curl);
			$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$error = curl_error($curl);
			curl_close($curl);

			if ($response === false) throw new RuntimeException('API-Verbindung fehlgeschlagen: '.$error);
			if ($http_status < 200 || $http_status >= 300)
			{
				$error_data = json_decode($response, true);
				$error_message = '';
				if (is_array($error_data))
				{
					if (isset($error_data['error'])) $error_message = is_string($error_data['error']) ? $error_data['error'] : json_encode($error_data['error']);
					elseif (isset($error_data['message'])) $error_message = (string)$error_data['message'];
				}
				throw new RuntimeException('API antwortet mit HTTP '.$http_status.($error_message !== '' ? ': '.$error_message : ''));
			}
		}
		else
		{
			$context = stream_context_create(array('http' => array('timeout' => 30)));
			$response = @file_get_contents($url, false, $context);
			if ($response === false) throw new RuntimeException('API-Verbindung fehlgeschlagen');
		}

		$data = json_decode($response, true);
		if (!is_array($data)) throw new RuntimeException('API liefert kein gueltiges JSON');
		return $data;
	}

	function cronjob_find_match($api_match, $matches)
	{
		$api_match_id = isset($api_match['id']) ? (int)$api_match['id'] : 0;
		$api_home_id = isset($api_match['home_id']) ? (int)$api_match['home_id'] : 0;
		$api_away_id = isset($api_match['away_id']) ? (int)$api_match['away_id'] : 0;
		$api_home_name = cronjob_normalize_name(isset($api_match['home_name']) ? $api_match['home_name'] : '');
		$api_away_name = cronjob_normalize_name(isset($api_match['away_name']) ? $api_match['away_name'] : '');

		foreach ($matches as $match)
		{
			if ($api_match_id > 0 && (int)$match['mat_data_index'] === $api_match_id) return $match;
		}

		foreach ($matches as $match)
		{
			if ($api_home_id > 0 && $api_away_id > 0
				&& (int)$match['home_data_index'] === $api_home_id
				&& (int)$match['away_data_index'] === $api_away_id)
			{
				return $match;
			}
		}

		foreach ($matches as $match)
		{
			if ($api_home_name !== '' && $api_away_name !== ''
				&& cronjob_normalize_name($match['home_name']) === $api_home_name
				&& cronjob_normalize_name($match['away_name']) === $api_away_name)
			{
				return $match;
			}
		}

		return null;
	}

	if (PHP_SAPI === 'cli' && isset($argv) && in_array('--self-test', $argv, true))
	{
		$test_matches = array(array(
			'mat_index' => 1,
			'mat_data_index' => 123,
			'home_data_index' => 10,
			'away_data_index' => 20,
			'home_name' => 'Team Eins',
			'away_name' => 'Team Zwei'
		));
		$test_api_match = array(
			'id' => 123,
			'home_id' => 10,
			'away_id' => 20,
			'home_name' => 'Team Eins',
			'away_name' => 'Team Zwei'
		);
		$test_score = cronjob_parse_score(' 2 - 1 ');
		$test_match = cronjob_find_match($test_api_match, $test_matches);
		if ($test_score !== array(2, 1) || $test_match === null || (int)$test_match['mat_index'] !== 1)
		{
			exit("SELF_TEST_FAILED\n");
		}
		exit("SELF_TEST_OK\n");
	}

	$dbconn = sqlconnect();
	cronjob_prepare_log($dbconn);

	if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg')
	{
		$expected_token = showsetting($dbconn, 'cron_secret');
		$provided_token = isset($_GET['token']) ? (string)$_GET['token'] : '';
		if ($expected_token === '' || !hash_equals($expected_token, $provided_token))
		{
			http_response_code(403);
			exit("Zugriff verweigert\n");
		}
	}

	$lock_result = mysqli_query($dbconn, "SELECT GET_LOCK('tipppfehler_cronjob', 0) AS cron_lock");
	$lock_row = mysqli_fetch_assoc($lock_result);
	if (!$lock_row || (int)$lock_row['cron_lock'] !== 1)
	{
		cronjob_log($dbconn, 'Ein anderer Cronjob laeuft bereits.', 'WARNING');
		exit;
	}

	try
	{
		$matches = array();
		$result = mysqli_query($dbconn, "SELECT
				mat_index,
				mat_data_index,
				mat_result_home,
				mat_result_away,
				mat_result_string,
				mat_status,
				ta.tea_data_index AS home_data_index,
				ta.tea_name AS home_name,
				tb.tea_data_index AS away_data_index,
				tb.tea_name AS away_name
			FROM mat_match
			INNER JOIN tea_team ta ON ta.tea_index=mat_tea_index_home
			INNER JOIN tea_team tb ON tb.tea_index=mat_tea_index_away
			WHERE mat_status<2 AND NOW()>=mat_starttime");

		while ($row = mysqli_fetch_assoc($result)) $matches[] = $row;

		if (count($matches) === 0)
		{
			cronjob_log($dbconn, 'Kein laufendes Match gefunden.');
			exit;
		}

		$api_key = trim(showsetting($dbconn, 'api_key'));
		$api_secret = trim(showsetting($dbconn, 'api_secret'));
		if ($api_key === '' || $api_secret === '') throw new RuntimeException('API-Zugangsdaten fehlen in set_setting');

		$competition_id = showsetting($dbconn, 'api_competition_id');
		if ($competition_id === '') $competition_id = '362';

		$api_url = 'https://livescore-api.com/api-client/scores/live.json?'.http_build_query(array(
			'key' => $api_key,
			'secret' => $api_secret,
			'competition_id' => $competition_id
		));
		$api_data = cronjob_fetch_json($api_url);
		$api_matches = isset($api_data['data']['match']) && is_array($api_data['data']['match'])
			? $api_data['data']['match']
			: array();

		$updated = 0;
		$unchanged = 0;
		$unmatched = 0;

		$update = mysqli_prepare($dbconn, "UPDATE mat_match
			SET mat_data_index=?, mat_result_home=?, mat_result_away=?, mat_status=?, mat_result_string=?
			WHERE mat_index=?");

		foreach ($api_matches as $api_match)
		{
			if (!is_array($api_match)) continue;
			$match = cronjob_find_match($api_match, $matches);
			if ($match === null)
			{
				$unmatched++;
				continue;
			}

			$status_text = strtoupper(trim(isset($api_match['status']) ? $api_match['status'] : ''));
			$finished = in_array($status_text, array('FINISHED', 'FT', 'AET', 'AFTER PENALTIES'), true);
			$mat_status = $finished ? 2 : 1;
			$has_extra_time = trim(isset($api_match['et_score']) ? $api_match['et_score'] : '') !== '';
			$score_text = $finished ? (isset($api_match['ft_score']) ? $api_match['ft_score'] : '') : (isset($api_match['score']) ? $api_match['score'] : '');
			$result_string = '';

			if ($has_extra_time)
			{
				$score_text = isset($api_match['ft_score']) ? $api_match['ft_score'] : $score_text;
				$display_score = isset($api_match['score']) ? trim($api_match['score']) : '';
				if ($display_score !== '') $result_string = $display_score.' n.V.';
			}

			$score = cronjob_parse_score($score_text);
			if ($score === null)
			{
				cronjob_log($dbconn, 'Ungueltiger Spielstand fuer Match '.$match['mat_index'].': '.$score_text, 'WARNING');
				continue;
			}

			$api_match_id = isset($api_match['id']) ? (int)$api_match['id'] : 0;
			$changed = (int)$match['mat_data_index'] !== $api_match_id
				|| (int)$match['mat_result_home'] !== $score[0]
				|| (int)$match['mat_result_away'] !== $score[1]
				|| (int)$match['mat_status'] !== $mat_status
				|| (string)$match['mat_result_string'] !== $result_string;

			if (!$changed)
			{
				$unchanged++;
				continue;
			}

			$mat_index = (int)$match['mat_index'];
			$result_home = $score[0];
			$result_away = $score[1];
			mysqli_stmt_bind_param($update, 'iiiisi', $api_match_id, $result_home, $result_away, $mat_status, $result_string, $mat_index);
			mysqli_stmt_execute($update);

			generiere_autotipps($dbconn, $mat_index);
			bewerte_match($dbconn, $mat_index);
			$updated++;

			cronjob_log($dbconn, 'Match '.$mat_index.' aktualisiert: '.$score[0].'-'.$score[1].', Status '.$mat_status);
		}

		mysqli_stmt_close($update);
		cronjob_log($dbconn, 'Fertig. Aktualisiert: '.$updated.', unveraendert: '.$unchanged.', nicht zugeordnet: '.$unmatched.'.');
	}
	catch (Throwable $error)
	{
		http_response_code(500);
		cronjob_log($dbconn, $error->getMessage(), 'ERROR');
		exit(1);
	}
	finally
	{
		mysqli_query($dbconn, "SELECT RELEASE_LOCK('tipppfehler_cronjob')");
	}
