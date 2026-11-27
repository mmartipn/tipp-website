<?php
	session_start ();
	include 'functions.php';
	$dbconn = sqlconnect();
	
	$t_id = 1;
	$m_id = 1; 	if (isset($_REQUEST["m_id"])) $m_id = $_REQUEST["m_id"];
	$pw_v = 0; 	if (isset($_REQUEST["pw_v"])) {$pw_v = $_REQUEST["pw_v"]; $m_id=3;}
	$loginfail = 0;	if (isset($_SESSION["loginfail"])) $loginfail = $_SESSION["loginfail"]; $_SESSION["loginfail"] = 0; unset($_SESSION["loginfail"]);
	$registerfail = 0;	if (isset($_SESSION["registerfail"])) $registerfail = $_SESSION["registerfail"]; $_SESSION["registerfail"] = 0; unset($_SESSION["registerfail"]);
	if (isset ($_SESSION["user_id_tipp"])) header("Location: member.php");
		
	$anmeldung = 0;
	$result = mysqli_query ($dbconn, "SELECT mat_starttime, NOW() as now FROM mat_match order by mat_starttime asc LIMIT 1");
	while ($row = mysqli_fetch_array($result))
	{
		if ($row['mat_starttime'] > $row['now']) $anmeldung = 1;
	}
	$username = "";
	$email = "";
	$tipprunde = 0;
	$vornachname = "";
	
	$message = "";
	if ($loginfail == 1)//vertippt
	{
		$message = "Benutzername und/oder Passwort falsch.<br>";
	}
	elseif ($loginfail == 2)//gesperrt
	{
		$message =  "Benutzername gesperrt.<br>";
	}
	elseif ($loginfail == 3)//nicht aktiviert
	{
		$message =  "Benutzername noch nicht aktiviert. Bitte den Link in der Best&auml;igungsmail klicken.<br>";
	}
	elseif ($loginfail == 4)//erfolgreich
	{
		$message =  "Passwort erfolgreich gesetzt. Bitte neu einloggen.<br>";
	}
	elseif ($loginfail == 5)//erfolgreich
	{
		$message =  "Passwort setzen nicht erfolgreich.<br>";
	}
	
?>
<!DOCTYPE HTML>

<html>
	<head>
		<title>tipppfehler.de</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="assets/css/maincss2.css" />
	</head>
	<body class="page-index">
		<div class="page-wrap">
			<div class="wrsec">
				<!-- Nav -->
					<section id="header">
						<div class="logo"><img src="assets/images/logo_white_small.png" alt="Logo">tipppfehler.de</div>
						<nav id="nav">
							<?php
								echo '<a href="index.php?m_id=1" '; if ($m_id == 1) echo 'class="loginactive"'; echo'>Login</a>';
								if ($anmeldung == 1) {echo '<a href="index.php?m_id=2" '; if ($m_id == 2) echo 'class="loginactive"'; echo'>Registrierung</a>';}
								echo '<a href="index.php?m_id=3" '; if ($m_id == 3) echo 'class="loginactive"'; echo'>Passwort vergessen</a>';
								echo '<a href="index.php?m_id=5" '; if ($m_id == 5) echo 'class="loginactive"'; echo'>Eigenes Tippspiel ?</a>';
								echo '<a href="index.php?m_id=4" '; if ($m_id == 4) echo 'class="loginactive"'; echo'>Impressum & Datenschutz</a>';
							?>	
						</nav>
						<a href="#navPanel" class="navPanelToggle"><span class="fa fa-bars"></span></a>
					</section>
			<!-- Main -->
					<section id="main">
						<?php
							if ($m_id == 1) // Login
							{
								echo '
									<div class="box ">
										<div class="header-box">
											<h5>Login</h5>
										</div>
										<div class="detail-box">
										
											<img src="assets/images/logo.png" width=160px>
											<form action="login.php" method="post">
												'.$message.'<br>
												<div class="form-row"><label for="login_name">Benutzername:</label><input id="login_name" name="name" type="text" class="login-field" placeholder="Benutzername"></div>
												<div class="form-row"><label for="login_passwort">Passwort:</label><input id="login_passwort" name="passwort" type="password" class="login-field" placeholder="Passwort"></div>
												<input value="Login" class="button" type="submit">
											</form>
										</div>
									</div>';
									
										if ($anmeldung == 1) 
										{
											echo '<div class="box ">
											<div class="detail-box">
												 Noch nicht registriert -> <a href="index.php?m_id=2">Hier</a><br>
												 Passwort vergessen -> <a href="index.php?m_id=3">Hier</a><br>
												 Eigenes Tippspiel erstellen -> <a href="index.php?m_id=5">Hier</a><br>
												 </div>
												</div>';
										}
							}
							elseif ($m_id == 2) // Registrierung
							{
									echo '<div class="box ">
										<div class="header-box">
											<h5>Registrierung</h5>
										</div>
										<div class="detail-box">';
										
								$show = true;
								if ($registerfail == 1)
								{
									echo "Du hast das Formular falsch ausg&uuml;llt.<br>";
								}	
								elseif ($registerfail == 2)
								{
									echo "Ein Nutzer mit diesem Benutzernamen oder dieser EmailAdresse existiert bereits. Bitte w&auml;hle eine(n) andere(n).<br>";
									$username = $_SESSION["registerfail_username"]; unset($_SESSION["registerfail_username"]);
									$email = $_SESSION["registerfail_email"]; unset($_SESSION["registerfail_email"]);
									$vornachname = $_SESSION["registerfail_vornachname"]; unset($_SESSION["registerfail_vornachname"]);
									$tipprunde = $_SESSION["registerfail_tipprunde"]; unset($_SESSION["registerfail_tipprunde"]);
								}		
								elseif ($registerfail == 3)
								{
									echo "Registrierung erfolgreich.<br> Du hast soeben eine Best&auml;igungemail bekommen. Bitte folge dem Link darin um dein Benutzerkonto zu aktivieren.<br>
										Solltest du keine Email erhalten, schreib bitte an: ".showsetting($dbconn, "email")."<br>";
									$show = false;
								}
								
								if ($show)
								{
									/*<select name="tipprunde">
												<option value="0" '.issel(0, $tipprunde).'>Bitte w&auml;hle eine Tipprunde</option>';
												$array = mysqli_query ($dbconn, "select tir_index, tir_name from tir_tipprunde order by tir_index asc");
												while ($row = mysqli_fetch_array($array))
													echo '<option value="'.$row['tir_index'].'" '.issel($row['tir_index'], $tipprunde).'>'.$row['tir_name'].'</option>';
												echo '</select>
												
												
											<input name="tipprunde" type="hidden" value="'.$t_id.'">*/
												
									echo '
									<form name="form_reg" id="form_reg" action="register.php" method="post" onsubmit="return fPruefen(2)">
										<p><br>'.$message.'
											<div class="form-row"><label for="reg_tipprunde">Tipprunde:</label><select id="reg_tipprunde" name="tipprunde"  class="login-field">
												<option value="0" '.issel(0, $tipprunde).'>Bitte w&auml;hle deine Tipprunde</option>';
												$array = mysqli_query ($dbconn, "select tir_index, tir_name from tir_tipprunde where tir_active=1 order by tir_index asc");
												while ($row = mysqli_fetch_array($array))
													echo '<option value="'.$row['tir_index'].'" '.issel($row['tir_index'], $tipprunde).'>'.$row['tir_name'].'</option>';
												echo '</select></div>
											<div class="form-row"><label for="reg_name">Benutzername:</label><input id="reg_name" name="name" type="text"  placeholder="Benutzername" value="'.$username.'" class="login-field"></div>
											<div class="form-row"><label for="reg_vornachname">Vor- und Nachname:</label><input id="reg_vornachname" name="vornachname" type="text"  placeholder="Vor- und Nachname" value="'.$vornachname.'" class="login-field"></div>
											<div class="form-row"><label for="reg_email">E-Mail:</label><input id="reg_email" name="email" type="text"  placeholder="Email" value="'.$email.'" class="login-field"></div>
											<div class="form-row"><label for="reg_passwort">Passwort:</label><input id="reg_passwort" name="passwort" type="password" placeholder="Passwort" class="login-field"></div>
											<div class="form-row"><label for="reg_passwort2">Passwort (wiederholen):</label><input id="reg_passwort2" name="passwort2" type="password" placeholder="Passwort (noch einmal)" class="login-field"></div>
										</p>
										<ul class="actions">
											<li><input value="Registrieren" class="button" type="submit"></li>
										</ul>
									</form>';
								}
								echo '</div></div>';
							} 
							elseif ($m_id == 3) //Passwort vergessen
							{
								echo '
									<div class="box ">
										<div class="header-box">
											<h5>Passwort vergessen</h5>
										</div>
										<div class="detail-box">';
												
								if ($pw_v == 1)
								{
									$hash = ""; if (isset($_REQUEST["hash"])) $hash = $_REQUEST["hash"];
									if (strlen($hash) == 0)
									{
										$username = $_POST["name"];
										$usr_index = userexists($dbconn, $username, $username);
										if ($usr_index > 0)
										{
											$hash = "";
											$array = mysqli_query ($dbconn, "select usr_name, usr_hash from usr_user where usr_index=".$usr_index);
											while ($row = mysqli_fetch_array($array))
											{
												$username = $row['usr_name'];
												$hash = $row['usr_hash'];
											}	
											$msg = "Dein Benutzername lautet: ".$username."\nUm ein neues Passwort zu setzen folge bitte diesem Link:\n ".env('APP_URL', 'http://localhost:8000/')."index.php?pw_v=1&hash=".$hash;
											
											sendmail($usr_index, $dbconn, 1, $msg);
											
											
											echo "Es wurde eine Email mit Informationen zum erstellen eines neuen Passworts an dich geschickt. Solltest du keine Email erhalten, schreibe bitte an: ".showsetting($dbconn, "email");
										}
										else
										{
											echo "Benutzername und Email sind nicht bekannt<br><a href='index.php?m_id=3'>Bitte erneut vesuchen</a>.";
										}
									}
									else
									{
										echo '<form name="form_pwd" id="form_pw" action="update_pwd.php" method="post" onsubmit="return fPruefen(1)">
														Bitte setze ein Neues Passwort:<br><br>
														<input name="hash" type="hidden" value="'.$hash.'" class="login-field" >
														<div class="form-row"><label for="pwd1">Neues Passwort:</label><input id="pwd1" name="pwd1" type="password" placeholder="neues Passwort" class="login-field"></div>
														<div class="form-row"><label for="pwd2">Neues Passwort (wiederholen):</label><input id="pwd2" name="pwd2" type="password" placeholder="neues Passwort (noch einmal)" class="login-field"></div>
														<input value="speichern" class="button" type="submit">
													</form>';
									}	
									
								}
								else
								{
									echo '
									<form action="index.php?m_id=3&pw_v=1" method="post">
										'.$message.'
											<div class="form-row"><label for="pwd_name">Benutzername oder E-Mail:</label><input id="pwd_name" name="name" type="text" placeholder="Benutzername oder Email" class="login-field"></div>
										<input value="zusenden" class="button" type="submit">
									</form>';
								}
								
								echo '
										</div>
									</div>';
							}
							elseif ($m_id == 4)
							{
								echo '
									<div class="box ">
										<div class="header-box">
											<h5>Impressum</h5>
										</div>
										<div class="detail-box">
											'.showsetting($dbconn, "impressum").'
										</div>
									</div>
									<div class="box ">
										<div class="header-box">
											<h5>Datenschutz</h5>
										</div>
										<div class="detail-box">
											'.showsetting($dbconn, "datenschutz").'
										</div>
									</div>';
							}							
							elseif ($m_id == 5) // Eigenes Tippspiel
							{
								echo '
									<div class="box ">
										<div class="header-box">
											<h5>Eigenes Tippspiel erstellen</h5>
										</div>
										<div class="detail-box">
											Du willst ein eigens Tippspiel erstellen ?<br>
											Das heisst eine Tipprunde wo nur du & die von dir eingeladenen Leute Teilnehmen ?<br><br>
											Kein Problem.<br>
											Du musst dich um nichts kümmern. Du schickst den Leuten einfach eine Einladung und fertig. 
											Alles andere passiert automatisch. <br><br>
											Schick einfach eine Email an '.showsetting($dbconn, "email").' mit folgenden Infomationen:
											<ul>
												<li>Name deiner Tipprunde ?</li>
												<li>Einsatz (auch 0 € möglich) ?</li>
												<li>Wieviele Gewinner soll es geben ?</li>
												<li>Soll es eine LuckyLoser Tabelle geben ?</li>
											</ul>	
											Alle diese Einstellungen können aber bis zum Tunierbgeinn jederzeit nochmal geändert werden !
										</div>
									</div>';
							}
						?>
					</section>	
			</div>
		</div>	
			<!-- Scripts -->
				<script src="assets/js/jquery.min.js"></script>
				<script src="assets/js/jquery.poptrox.min.js"></script>
				<script src="assets/js/jquery.scrolly.min.js"></script>
				<script src="assets/js/skel.min.js"></script>
				<script src="assets/js/util.js"></script>
				<script src="assets/js/main.js"></script>
				<script src="assets/js/formular_pruefen.js"></script>
				<script>
					function togglediv(index) {
						var a = "tdiv"+index;
						var x = document.getElementById(a);
						if (x.style.display === "none") {
							x.style.display = "block";
						} else {
							x.style.display = "none";
						}
					}
				</script>

	</body>
</html>
