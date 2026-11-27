<?php
	// Datenbank-Zugangsdaten kommen aus .env (siehe .env.example / config.php)
	include_once __DIR__ . '/config.php';
	$sqlhost = env('DB_HOST', 'localhost');
	$sqluser = env('DB_USER', 'root');
	$sqlpass = env('DB_PASSWORD', '');
	$dbname  = env('DB_NAME', 'tippspiel_demo');
?>
