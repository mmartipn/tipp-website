<?php
	/*
	 * Zentrale Konfiguration.
	 * Liest Schluessel/Wert-Paare aus der Datei .env im Projektverzeichnis
	 * (siehe .env.example). Faellt auf Umgebungsvariablen und danach auf den
	 * angegebenen Standardwert zurueck. Es werden keine Zugangsdaten im Code
	 * hinterlegt. Weitere Einstellungen (Punkte, API-Keys) liegen in der
	 * Tabelle set_setting und werden ueber die Admin-Oberflaeche gepflegt.
	 */
	if (!function_exists('env'))
	{
		function env($key, $default = '')
		{
			static $vars = null;
			if ($vars === null)
			{
				$vars = array();
				$file = __DIR__ . '/.env';
				if (file_exists($file))
				{
					foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line)
					{
						$line = trim($line);
						if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
						list($k, $v) = explode('=', $line, 2);
						$vars[trim($k)] = trim($v, " \t\"'");
					}
				}
			}
			if (isset($vars[$key])) return $vars[$key];
			$e = getenv($key);
			if ($e !== false) return $e;
			return $default;
		}
	}
?>
