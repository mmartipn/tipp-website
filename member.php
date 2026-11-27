<?php
	session_start ();
	include 'functions.php';
	$dbconn = sqlconnect();

	if (!isset ($_SESSION["user_id_tipp"])) 
	{
		header("Location: index.php");
		exit;
	}
	
	$m_id = 5; 	if (isset($_REQUEST["m_id"])) $m_id = $_REQUEST["m_id"];
	$tir_index = 0; if (isset($_REQUEST["tir_index"])) $tir_index = (int)$_REQUEST["tir_index"];
	if (isset($_POST["tir_index_change"])) {
		$tir_index = (int)$_POST["tir_index_change"];
		$_SESSION["tir_id_tipp"] = $tir_index;
	}
	if (isset($_POST["tir_index_new"])) {
		if ($_POST["tir_index_new"] > 0)
		{
			mysqli_query($dbconn, "INSERT INTO urm_usrtirmapping (urm_usr_index, urm_tir_index) VALUES(".$_SESSION["user_id_tipp"].",".$_POST["tir_index_new"].")");
			mysqli_query($dbconn, "UPDATE usr_user set usr_tir_index__default =".$_POST["tir_index_new"]);	
			unset($_SESSION['user_id_tipp']); 
			header ("Location: index.php");
			exit;
		}
	}
	if ($m_id == 6)
	{
		unset($_SESSION['user_id_tipp']); 
		unset($_SESSION['tir_id_tipp']); 
		header ("Location: index.php");
		exit;
	}
	
	$usr_index = 0;
	$usr_name = "";
	$usr_vornachname = "";
	
	$usr_index = $_SESSION["user_id_tipp"];
	$usr_name = showusrsetting($dbconn, $usr_index, "usr_name");
	$usr_vornachname = showusrsetting($dbconn, $usr_index, "usr_vornachname");
	if ($tir_index == 0) $tir_index = $_SESSION["tir_id_tipp"];
	
	
	$era = ""; 
	$result=mysqli_query($dbconn, "SELECT mat_index FROM mat_match WHERE mat_status<2 LIMIT 1");
	while($row=mysqli_fetch_array($result)) 
	{
		if ($row["mat_index"]>3)		
			$era = "#anc".($row["mat_index"]-3);
	}

?>
<!DOCTYPE HTML>

<html>
	<head>
		<title>tipppfehler.de</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="assets/css/maincss2.css" />
		<script type="text/javascript" src="assets/js/ckeditor/ckeditor.js"></script>
			
	<link href="assets/css/jquery.simplyscroll.css"  rel="stylesheet">
	</head>
	<body class="page-member<?php if ($m_id == 5) echo ' page-member-home'; ?>">
		<div class="page-wrap">
			<div class="wrsec">
			<!-- Nav -->
				<section id="header">
						<div class="header-round-switch">
							<form action="member.php?m_id=<?php echo $m_id; ?>" method="post">
								<select name="tir_index_change" aria-label="Tipprunde wechseln" onchange="this.form.submit()">
								<?php
									$result=mysqli_query($dbconn, "SELECT urm_tir_index, tir_name FROM urm_usrtirmapping INNER JOIN tir_tipprunde on tir_index=urm_tir_index WHERE urm_usr_index=".$usr_index." and tir_active=1");
									while($row=mysqli_fetch_array($result)) {
										echo '<option value="'.$row["urm_tir_index"].'" '.issel($row["urm_tir_index"], $tir_index).'>'.$row["tir_name"].'</option>';
									}
								?>	
								</select>
							</form>
						</div>
						<div class="logo"><img src="assets/images/logo_white_small.png" alt="Logo"><a href="member.php">tipppfehler.de</a></div>
					<nav id="nav">
						<?php
							if ($tir_index > 0)
							{
								echo '<a href="member.php?m_id=5"'; if ($m_id == 5) echo ' class="menactive"'; echo '>Aktuelles</a>';
								echo '<a href="member.php?m_id=1"'; if ($m_id == 1) echo ' class="menactive"'; echo '>Tabelle</a>';
								echo '<a href="member.php?m_id=2'.$era.'"'; if ($m_id == 2) echo ' class="menactive"'; echo '>Mein Tipp</a>';
								echo '<a href="member.php?m_id=3"'; if ($m_id == 3) echo ' class="menactive"'; echo '>Spielplan</a>';
								echo '<a href="member.php?m_id=4"'; if ($m_id == 4) echo ' class="menactive"'; echo '>Regeln</a>';
								echo '<a href="member.php?m_id=8"'; if ($m_id == 8) echo ' class="menactive"'; echo '>Chat</a>';
							}
							echo '<a href="member.php?m_id=9"'; if ($m_id == 9) echo ' class="menactive"'; echo '>Einstellungen</a>';
							if ($usr_index == 1) {echo '<a href="admin.php">ADMIN</a>';}
							echo '<a href="member.php?m_id=6">Ausloggen</a>';
						?>	
					</nav>
					<a href="#navPanel" class="navPanelToggle"><span class="fa fa-bars"></span></a>
					
				</section>
			<!-- Main -->
				<section id="main">
					<!-- Section -->
							<?php
								if ($tir_index == 0)
								{
									//check if no tirs available
									$result=mysqli_query($dbconn, "SELECT urm_tir_index, tir_name FROM urm_usrtirmapping INNER JOIN tir_tipprunde on tir_index=urm_tir_index WHERE urm_usr_index=".$usr_index." and tir_active=1");
									if (mysqli_num_rows($result) > 0)
									{
										echo '
										<div class="box ">
											<div class="header-box">
												<h5>Fehler</h5>
											</div>
											<div class="detail-box">
												...das sollte eigentlich nicht passieren.<br>
												Bitte schreib mir: '.showsetting($dbconn, "email").'
											</div>
										</div>';	
									}
									else
									{
										echo '
										<div class="box ">
											<div class="header-box">
												<h5>Bitte Tipprunde w&auml;hlen</h5>
											</div>
											<div class="detail-box">
												Du hast im Moment keine aktive Tipprunde bei der du mitmachst !<br>
												Bitte wähle eine und klicke dann auf speichern:<br><br>
												<form action="member.php?m_id='.$m_id.'" method="post">
												<select name="tir_index_new" aria-label="Tipprunde beitreten" class="login-field">';
												$result=mysqli_query($dbconn, "SELECT tir_index, tir_name FROM tir_tipprunde WHERE tir_active=1");
												while($row=mysqli_fetch_array($result)) 
												{
													echo '<option value="'.$row["tir_index"].'">'.$row["tir_name"].'</option>';
												}
												echo '</select>
												<br>Hinweis: du wirst dann automatisch nochmal ausgeloggt!<br>	<br>
												<input type="submit" value="speichern">
												</form>
											</div>
										</div>';
									}
								}
								else
								{
									if ($m_id == 1) // Tabelle
									{
										$islive = 0;
										$result=mysqli_query($dbconn, "SELECT mat_index from mat_match where mat_status=1");
										if (mysqli_num_rows($result) > 0) $islive = 1;
										
										$mat_index = 0; if (isset($_POST["mat_index"]))$mat_index = $_POST["mat_index"];
										echo '<form class="table-match-filter" action="member.php?m_id=1" method="post">
												<label for="table-match-select">Stand nach Spiel:</label>
												<select id="table-match-select" name="mat_index" onchange="this.form.submit()">';
												$stamperl = null;
												$orderby = "mat_order desc, mat_index desc";
												//vorrunde vorbei
												if (isEndrunde($dbconn)) $orderby = "grp_vorrunde, ".$orderby;
												$result=mysqli_query($dbconn, "SELECT mat_index, WEEKDAY(mat_starttime) as mat_weekday, UNIX_TIMESTAMP(mat_starttime) as mat_abfragezeit, DATE_FORMAT(mat_starttime, '%d.%m %H:%i' ) AS mat_starttime,  date_format( mat_starttime, '%Y%m%d%H:%i:%s' ) AS mat_order, mat_status, ta.tea_name AS tea_name_home,ta.tea_short AS tea_short_home, tb.tea_name AS tea_name_away, tb.tea_short AS tea_short_away, mat_result_home, mat_result_away, mat_result_string, grp_short FROM mat_match INNER JOIN tea_team ta ON ta.tea_index = mat_tea_index_home INNER JOIN tea_team tb ON tb.tea_index = mat_tea_index_away INNER JOIN grp_group on grp_index=mat_grp_index where mat_status>0 ORDER BY ".$orderby);
												while($row=mysqli_fetch_array($result))
												{
													if ($mat_index == 0) $mat_index = $row['mat_index'];
													echo '<option value="'.$row['mat_index'].'" '.issel($row['mat_index'], $mat_index).'>'.getWeekDay($row["mat_weekday"]).' '.$row["mat_starttime"].' '.$row["tea_name_home"].' - '.$row["tea_name_away"].'</option>';
												}
												echo '</select>
												</form>';
										$showsecondtable = ((showtirsetting($dbconn, $tir_index, "tir_scndtbl") == 1) && isEndrunde($dbconn));
										echo '<section class="columns double1'.(!$showsecondtable ? ' single-table' : '').'">';
										
										$sieger = array();
										$meixiproblem = "";
										$gewinercount = 0;
										$gewinnermax = showtirsetting($dbconn, $tir_index, "tir_winnercount") + 0;
										echo '
										<div class="column1">
											<header>
											<h1>Tabelle</h1>
											</header>
											<p>
													<table>
													<thead>
														<th class="td_pla bld tb_notmobile">Pl</th>
														<th class="td_nam bld tb_notmobile">Benutzer</th>
														<th class="td_nam_s bld tb_notmobile">Nutzer</th>
														<th class="td_erg bld tb_notmobile">Erg</th>
														<th class="td_ten bld tb_notmobile">Ten</th>
														<th class="td_gti bld tb_notmobile">Gti</th>
														<th class="td_sti bld tb_notmobile">Sti</th>
														<th class="td_aus bld tb_notmobile">Aus</th>
														<th class="td_aus bld tb_notmobile">KI</th>
														<th class="td_all bld tb_notmobile">E/T/G/S/A/Ki</th>
														<th class="td_poi bld tb_notmobile">Ges</th>
														<th class="td_thu bld tb_notmobile">&nbsp;</th>
													</thead>
													';
													
											$borderprinted = 0;
											$platz_print = 0;
											$platz_count = 0;
											$last_poi = -1;
											$last_erg = -1;
											$last_ten = -1;
											$last_gti = -1;
											$last_sti = -1;
											$last_aus = -1;
											$last_kit = -1;
											$tabelle = gettable($dbconn, $tir_index, $mat_index);
											//echo $mat_index;
											foreach ($tabelle as $eintrag)
											{
												$usr = $eintrag["usr"];
												$poi = $eintrag["poi"];
												$erg = $eintrag["erg"];
												$ten = $eintrag["ten"];
												$gti = $eintrag["gti"];
												$sti = $eintrag["sti"];
												$kit = $eintrag["kit"];
												$aus = $eintrag["aus"];
												$pla = $eintrag["pla"];
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
												
												//speichere sieger für secondtable
												$gewinercount ++;
												
											//		echo "(".$gewinercount.",".$gewinnermax.")";
												if ($gewinercount <= $gewinnermax)
												{
													$sieger[] = $usr;
													if (strlen($meixiproblem)>0)$meixiproblem = $meixiproblem."#";
													$meixiproblem = $meixiproblem.$usr;
												}
												$border = "";
												if (($pla+0 > $gewinnermax) && ($borderprinted == 0))
												{
													$border = " bline";
													$borderprinted = 1;
												}
												
													echo '
													<tr> <td class="td_pla tb_notmobile'.getlivecolor($islive).$border.'">'.$pla.'</td>
													<td class="td_nam tb_notmobile'.getlivecolor($islive).$border.'"><a class="fuck'.trim(getlivecolor($islive)).'" href="member.php?m_id=2&usr_id='.$usr.'">'.showusrsetting($dbconn, $usr, "usr_name").' ('.showusrsetting($dbconn, $usr, "usr_vornachname").')</a></td>
													<td class="td_nam_s tb_notmobile'.getlivecolor($islive).$border.'"><a class="fuck'.trim(getlivecolor($islive)).'" href="member.php?m_id=2&usr_id='.$usr.'" title="'.showusrsetting($dbconn, $usr, "usr_vornachname").'">'.showusrsetting($dbconn, $usr, "usr_name").'</a></td>
													<td class="td_erg tb_notmobile'.getlivecolor($islive).$border.'">'.$erg.'</td>
													<td class="td_ten tb_notmobile'.getlivecolor($islive).$border.'">'.$ten.'</td>
													<td class="td_gti tb_notmobile'.getlivecolor($islive).$border.'">'.$gti.'</td>
													<td class="td_sti tb_notmobile'.getlivecolor($islive).$border.'">'.$sti.'</td>
													<td class="td_aus tb_notmobile'.getlivecolor($islive).$border.'">'.$aus.'</td>
													<td class="td_aus tb_notmobile'.getlivecolor($islive).$border.'">'.$kit.'</td>
													<td class="td_all tb_notmobile'.getlivecolor($islive).$border.'">'.$erg.'/'.$ten.'/'.$gti.'/'.$sti.'/'.$aus.'/'.$kit.'</td>
													<td class="td_poi tb_notmobile'.getlivecolor($islive).$border.'"><b>'.$poi.'</b></td>
													<td class="td_thu tb_notmobile'.getlivecolor($islive).$border.'"><img src="assets/images/'.$eintrag["for"].'"></td>
													</tr>';                                                      
											}
											
										echo'</table></div><p>&nbsp;</p>';
										$gewinnermax = showtirsetting($dbconn, $tir_index, "tir_scndtbl_winnercount");
										$borderprinted = 0;
										$platz_print = 0;
										$platz_count = 0;
										$last_poi = -1;
										$last_erg = -1;
										$last_ten = -1;
										$last_aus = -1;
										$last_kit = -1;
										
										if ($showsecondtable)
										{
											echo '
											<div class="column2">
											<header>
											<h1>LuckyLoserTabelle</h1>
											</header>
											<p>
												<table>
													<thead>
														<th class="td_pla bld tb_notmobile">Pl</th>
														<th class="td_nam bld tb_notmobile">Benutzer</th>
														<th class="td_nam_s bld tb_notmobile">Nutzer</th>
														<th class="td_erg bld tb_notmobile">Erg</th>
														<th class="td_ten bld tb_notmobile">Ten</th>
														<th class="td_aus bld tb_notmobile">Aus</th>
														<th class="td_aus bld tb_notmobile">KI</th>
														<th class="td_all bld tb_notmobile">E/T/A/Ki</th>
														<th class="td_poi bld tb_notmobile">Ges</th>
														<th class="td_thu bld tb_notmobile">&nbsp;</th>
													</thead>';
											
											if (sizeof($sieger) == 0)
											{
												$sieger = explode("#",$meixiproblem);
											}
											if (sizeof($sieger) == 0)
											{
												echo "meixiproblem !";
											}
											
											$tabelle = gettable($dbconn, $tir_index, $mat_index, $sieger);
											foreach ($tabelle as $eintrag)
											{
												$usr = $eintrag["usr"];
												$poi = $eintrag["poi"];
												$erg = $eintrag["erg"];
												$ten = $eintrag["ten"];
												$aus = $eintrag["aus"];
												$pla = $eintrag["pla"];
												$kit = $eintrag["kit"];
												$platz_count ++;
												if (tabchanged($last_poi, $last_erg, $last_ten, 0, 0, $last_aus, $last_kit, $poi, $erg, $ten, 0, 0, $aus, $kit)) 
												{
													$platz_print = $platz_count;
													$last_poi = $poi;
													$last_erg = $erg;
													$last_ten = $ten;
													$last_aus = $aus;
													$last_kit = $kit;
												}
												
												
												
												$border = "";
												if (($pla+0 > $gewinnermax) && ($borderprinted == 0))
												{
													$border = " bline";
													$borderprinted = 1;
												}

												echo '
													<tr> <td class="td_pla tb_notmobile'.getlivecolor($islive).$border.'">'.$pla.'</td>
													<td class="td_nam tb_notmobile'.getlivecolor($islive).$border.'"><a class="fuck'.trim(getlivecolor($islive)).'" href="member.php?m_id=2&usr_id='.$usr.'">'.showusrsetting($dbconn, $usr, "usr_name").' ('.showusrsetting($dbconn, $usr, "usr_vornachname").')</a></td>
													<td class="td_nam_s tb_notmobile'.getlivecolor($islive).$border.'"><a class="fuck'.trim(getlivecolor($islive)).'" href="member.php?m_id=2&usr_id='.$usr.'" title="'.showusrsetting($dbconn, $usr, "usr_vornachname").'">'.showusrsetting($dbconn, $usr, "usr_name").'</a></td>
													<td class="td_erg tb_notmobile'.getlivecolor($islive).$border.'">'.$erg.'</td>
													<td class="td_ten tb_notmobile'.getlivecolor($islive).$border.'">'.$ten.'</td>
													<td class="td_aus tb_notmobile'.getlivecolor($islive).$border.'">'.$aus.'</td>
													<td class="td_aus tb_notmobile'.getlivecolor($islive).$border.'">'.$kit.'</td>
													<td class="td_all tb_notmobile'.getlivecolor($islive).$border.'">'.$erg.'/'.$ten.'/'.$aus.'/'.$kit.'</td>
													<td class="td_poi tb_notmobile'.getlivecolor($islive).$border.'"><b>'.$poi.'</b></td>
													<td class="td_thu tb_notmobile'.getlivecolor($islive).$border.'"><img src="assets/images/'.$eintrag["for"].'"></td>
													</tr>';
											}
												
											echo'</table></div>';
										}
										
										echo '</section></div>';
									}
									elseif ($m_id == 2) // Mein Tipp
									{
										$usr_id = $usr_index; if (isset($_REQUEST['usr_id'])) $usr_id = $_REQUEST['usr_id'];
										$userlocked = ($usr_id != $usr_index);
										echo '
											<header>
												<h1 class="nom">';
												if ($userlocked)
												{
													$result=mysqli_query($dbconn, "select usr_name, usr_vornachname from usr_user where usr_index = '".$usr_id."'");
													while($row=mysqli_fetch_array($result))
														echo "Tipps von: ".$row['usr_name']." (".$row['usr_vornachname'].")";
												}
												else
												{
													echo "Mein Tipp";
												} echo'
												</h1>
												<br><form action="member.php?m_id=2" method="post">...oder Zeige Tipps von:
												<select class="user-tip-select" name="usr_id" onchange="this.form.submit()"><option value="'.$usr_index.'" '.issel($usr_id, $usr_index).'><b>Mein Tipp</b></option>';
												$result=mysqli_query($dbconn, "SELECT usr_index, usr_name, usr_vornachname FROM usr_user INNER JOIN urm_usrtirmapping on usr_index=urm_usr_index where usr_index<>".$usr_index." and urm_tir_index='".$tir_index."' order by usr_name");
												while($row=mysqli_fetch_array($result)) echo '<option value="'.$row["usr_index"].'" '.issel($usr_id, $row["usr_index"]).'>'.$row["usr_name"].' ('.$row["usr_vornachname"].')</option>';
												echo '</select></form>
											</header>
												<form class="fl" id="meintippForm" action="member_post.php?m_id=2" method="post">
											<p>
												<div class="member-match-list">
												
												<div class="mt mt_dat bld notmobile">Datum</div>
												<div class="mt mt_dat_s bld notmobile">Datum</div>
												<div class="mt mt_gru bld notmobile">Gr</div>
												<div class="mt mt_hom bld notmobile">Team1</div>
												<div class="mt mt_hom_s bld notmobile">Team1</div>
												<div class="mt mt_vsp bld notmobile">-</div>
												<div class="mt mt_awa bld notmobile">Team2</div>
												<div class="mt mt_awa_s bld notmobile">Team2</div>
												<div class="mt mt_tip bld notmobile">Tipp</div>
												<div class="mt mt_erg bld notmobile">Neu/Erg</div>
												<div class="mt mt_thu bld notmobile">&nbsp;</div>
												<div class="cb notmobile"></div>
												
										';
										
										$orderby = "mat_order, mat_index";
										//vorrunde vorbei
									//	if (isEndrunde($dbconn)) $orderby = "grp_vorrunde, ".$orderby;
										
										$endrunden_anchor_set=false;
										
										$result=mysqli_query($dbconn, "SELECT tip_index, tip_result_home, tip_result_away, tip_points_aus, tip_ki, mat_index, WEEKDAY(mat_starttime) as mat_weekday, UNIX_TIMESTAMP(mat_starttime) as mat_abfragezeit, DATE_FORMAT(mat_starttime, '%d.%m %H:%i' ) AS mat_starttime, DATE_FORMAT(mat_starttime, '%d.%m %Hh' ) AS mat_starttime_s,  date_format( mat_starttime, '%Y%m%d%H:%i' ) AS mat_order, mat_status, (mat_status = 0 AND mat_starttime > NOW()) AS can_tip, ta.tea_name AS tea_name_home,ta.tea_short AS tea_short_home, tb.tea_name AS tea_name_away, tb.tea_short AS tea_short_away, mat_result_home, mat_result_away, mat_result_string, mat_grp_index, grp_short FROM mat_match INNER JOIN tea_team ta ON ta.tea_index = mat_tea_index_home INNER JOIN tea_team tb ON tb.tea_index = mat_tea_index_away INNER JOIN grp_group on grp_index=mat_grp_index left join tip_tipp on tip_mat_index=mat_index and tip_usr_index = ".$usr_id." ORDER BY ".$orderby);
										while($row=mysqli_fetch_array($result))
										{
											//<div class="mt mt_hom'.getlivecolor($row['mat_status']).'"><a class="fuck" href="member.php?m_id=7&mat_index='.$row["mat_index"].'"><img src="assets/images/flags/'.$row["tea_short_home"].'.gif"> '.$row["tea_name_home"].'</a></div>
											$anchor = "";
											/*if (($row["mat_grp_index"]>=9) && (!$endrunden_anchor_set))
											{
												$endrunden_anchor_set=true;
												$anchor = ' id="era"';
											}
											*/
											$anchor = ' id="anc'.$row["mat_index"].'"';
											
											$status = 0;
											$tipplocked = ((int)$row['can_tip'] !== 1);
											echo '
											
												<div class="tr_nx'.getlivecolor($row['mat_status']).'">'.$row["mat_starttime_s"].', '.$row["grp_short"].'</div>
												<div class="mt mt_dat'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" >'.getWeekDay($row["mat_weekday"]).' '.$row["mat_starttime"].'</a></div>
												<div class="mt mt_dat_s'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" >'.$row["mat_starttime_s"].'</a></div>
												<div class="mt mt_gru'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" >'.$row["grp_short"].'</a></div>
												<div class="mt mt_hom'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" ><img src="assets/images/flags/'.$row["tea_short_home"].'.gif"> '.$row["tea_name_home"].'</a></div>
												<div class="mt mt_hom_s'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" ><img src="assets/images/flags/'.$row["tea_short_home"].'.gif"> '.strtoupper($row["tea_short_home"]).'</a></div>
												<div class="mt mt_vsp'.getlivecolor($row['mat_status']).'" '.$anchor.'><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" >-</a></div>
												<div class="mt mt_awa'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" ><img src="assets/images/flags/'.$row["tea_short_away"].'.gif"> '.$row["tea_name_away"].'</a></div>
												<div class="mt mt_awa_s'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" ><img src="assets/images/flags/'.$row["tea_short_away"].'.gif"> '.strtoupper($row["tea_short_away"]).'</a></div>';
												
												if(!$tipplocked && !$userlocked) //tip abgeben
												{
													/* echo '
														<input type="hidden" name="action" value="1">
														<input type="hidden" name="mat_index" value="'.$row["mat_index"].'">
														<input type="hidden" name="tip_index'.$row["mat_index"].'" value="'.(is_null($row["tip_index"])? 0 : $row["tip_index"]).'">
														<div class="mt mt_tip">
															<input type="text" name="tip_result_home'.$row["mat_index"].'" class="small" value="'.$row["tip_result_home"].'">-<input type="text" class="small" name="tip_result_away'.$row["mat_index"].'" size="2" value="'.$row["tip_result_away"].'"></div>
														<div class="mt mt_erg"><input type="submit" name="submit'.$row["mat_index"].'" class="small" value="speichern"></div>
														<div class="mt mt_thu"></div>';	*/
													$tip = $row["tip_result_home"].'-'.$row["tip_result_away"]; if (strlen($tip) == 1)$tip="";
													echo '
														<div class="mt mt_tip">'.$tip.'</div>
														<div class="mt mt_erg">
															<input type="text" name="tip_result_home'.$row["mat_index"].'" class="small" value="">-<input type="text" class="small" name="tip_result_away'.$row["mat_index"].'" size="20" value="">
														</div>
														<div class="mt mt_thu"></div>';											
														
												}
												elseif (!$tipplocked && $userlocked) //nichts sehen
												{
													echo '
														<div class="mt mt_tip"></div>
														<div class="mt mt_erg"></div>
														<div class="mt mt_thu"></div>'; 
												}
												else  //tip und daumen sehen
												{  										  
													$thumb='daumen_runter.gif';
													if (tendenz_richtig($row["mat_result_home"], $row["mat_result_away"], $row["tip_result_home"], $row["tip_result_away"]) == 1) $thumb='daumen_quer.gif';
													elseif (ergebnis_richtig($row["mat_result_home"], $row["mat_result_away"],  $row["tip_result_home"], $row["tip_result_away"]) == 1) $thumb='daumen_hoch.gif';
													if (isset($row["tip_index"]))
													{
														if ($row["tip_points_aus"] == 1)
																$thumb='shark.gif';
														if ($row["tip_ki"] == 1)
														{
															if ($thumb == 'daumen_runter.gif') $thumb='daumen_runter_a.gif';
															elseif ($thumb == 'daumen_quer.gif') $thumb='daumen_quer_a.gif';
															elseif ($thumb == 'daumen_hoch.gif')$thumb='daumen_hoch_a.gif';
															elseif ($thumb == 'shark.gif') $thumb='shark_a.gif';
														}
													}
													if($row['mat_status'] == 1) //spiel ist live
													{
														echo '
														<div class="mt mt_tip'.getlivecolor($row['mat_status']).'">'.$row["tip_result_home"].'-'.$row["tip_result_away"].'</div>
														<div class="mt mt_erg'.getlivecolor($row['mat_status']).'">'.$row["mat_result_home"].'-'.$row["mat_result_away"].'</div>
														<div class="mt mt_thu'.getlivecolor($row['mat_status']).'"><img src="assets/images/'.$thumb.'"></div>'; 
													}
													//else if($row['mat_status'] == 2) //spiel ist vorbei
													else
													{
														$resultsstring = $row["mat_result_home"].'-'.$row["mat_result_away"];
														if (strlen($row['mat_result_string'])>0) $resultsstring = $row['mat_result_string']. '('.$resultsstring.')';
														echo '<div class="mt mt_tip">'.$row["tip_result_home"].'-'.$row["tip_result_away"].'</div>';
														if ($row['mat_status'] == 2)
														echo '<div class="mt mt_erg">'.$resultsstring.'</div><div class="mt mt_thu"><img src="assets/images/'.$thumb.'"></div>';
														else
														echo '<div class="mt mt_erg"></div><div class="mt mt_thu"></div>';
													}
												}
												echo '<div class="cb"></div>
												
												<div id="alletipps'.$row["mat_index"].'" name="alletipps'.$row["mat_index"].'" style="display:none;">
												';
												if ($tipplocked)
												{
													$result_t=mysqli_query($dbconn, "SELECT tip_index, tip_result_home, tip_result_away, tip_points_erg, tip_points_ten, tip_points_aus, tip_ki, usr_name, usr_vornachname from tip_tipp inner join usr_user on usr_index=tip_usr_index INNER JOIN urm_usrtirmapping on usr_index=urm_usr_index where tip_mat_index=".$row['mat_index']." and urm_tir_index=".$tir_index." order by usr_name");
													while($row_t=mysqli_fetch_array($result_t))
													{
														$thumb='daumen_runter.gif';
														if ($row_t["tip_points_ten"]>1)  $thumb='daumen_quer.gif';
														elseif ($row_t["tip_points_erg"]>1)   $thumb='daumen_hoch.gif';
														if (isset($row_t["tip_index"]))
														{
															if ($row_t["tip_points_aus"] == 1)
															$thumb='shark.gif';
														if ($row_t["tip_ki"] == 1)
														{
															if ($thumb == 'daumen_runter.gif') $thumb='daumen_runter_a.gif';
															elseif ($thumb == 'daumen_quer.gif') $thumb='daumen_quer_a.gif';
															elseif ($thumb == 'daumen_hoch.gif')$thumb='daumen_hoch_a.gif';
															elseif ($thumb == 'shark.gif') $thumb='shark_a.gif';
														}
														}
														echo '
															<div class="mt_bn'.getlivecolor($row['mat_status']).'">'.$row_t["usr_name"].'</div>
															<div class="mt_bn2'.getlivecolor($row['mat_status']).'">'.$row_t["tip_result_home"].' - '.$row_t["tip_result_away"].'</div>
															<div class="mt_bn3'.getlivecolor($row['mat_status']).'"><img src="assets/images/'.$thumb.'"></div>
															<div class="cb"></div>';
													} 
												}
												else
												{
													echo "Tipps anderer Nutzer erst nach Anpfiff sichtbar"; 
												}
												echo'</div>
												';
										}
										echo'
										</div>
										<br><br>
										
										<div class="column">
											<header>
												<h1>FinalistenTipp</h1>
											</header>
											<section class="columns quater">';
											
											$fitipplocked = !checkifsaveok($dbconn, 1);
											
											$result_grp = mysqli_query($dbconn, "SELECT grp_index, grp_name, grp_short, grp_anzahlteams FROM grp_group WHERE grp_vorrunde=0 and grp_anzahlteams>0 ORDER BY grp_index asc");
											while($row_grp = mysqli_fetch_array($result_grp))
											{	

														//<form name="fiti_'.$row_grp["grp_index"].'"  id="fiti_'.$row_grp["grp_index"].'" action="member_post.php?m_id=2" method="post">										
												echo '
													<div class="column"><div id="gtidiv'.$row_grp["grp_index"].'" style="text-align:left;">';
														
															$result=mysqli_query($dbconn, "SELECT gti_index FROM gti_grouptipp where gti_usr_index=".$usr_id." and gti_grp_index=".$row_grp['grp_index']);
															$noch = $row_grp["grp_anzahlteams"] - mysqli_num_rows($result);
															
															echo '<p><b>'.$row_grp["grp_name"].'</b><br>W&auml;hle noch <b id="noch_'.$row_grp["grp_index"].'">'.$noch.'</b><br>';
															//if (!$fitipplocked && !$userlocked) echo '<input type="submit" class="small" value="'.$row_grp["grp_name"].' speichern"></p>';
															$result=mysqli_query($dbconn, "SELECT tea_index, tea_name, tea_short, gti_index, gti_points , grp_short FROM tea_team 
																							INNER JOIN grp_group on tea_grp_index=grp_index
																							LEFT JOIN gti_grouptipp on gti_tea_index=tea_index and gti_usr_index=".$usr_id." and gti_grp_index=".$row_grp['grp_index']."
																							ORDER BY grp_name asc, tea_index asc");
															while($row=mysqli_fetch_array($result))
															{
																$checked = ""; if (!is_null($row['gti_index']))$checked = " checked";
																
																$chkbx_name = 'chk_'.$row_grp["grp_index"].'_'.$row['tea_index'];
																if ((!$fitipplocked && !$userlocked)) // tip abgeben
																{
																	echo '<input type="checkbox" id="'.$chkbx_name.'" name="'.$chkbx_name.'" value="'.$row['tea_index'].'" '.$checked.' onclick="check('.$row_grp["grp_anzahlteams"].', '.$row_grp["grp_index"].', \''.$chkbx_name.'\')">
																	<label for="'.$chkbx_name.'">('.$row['grp_short'].') <img src="assets/images/flags/'.$row['tea_short'].'.gif"> '.$row['tea_name'].'</label>';
																}
																elseif (!$fitipplocked && $userlocked) //nichts sehen
																{
																	echo '('.$row['grp_short'].') <img src="assets/images/flags/'.$row['tea_short'].'.gif"> '.$row['tea_name'];
																}
																else // tip sehen
																{
																	if ($row["gti_points"]>0)
																	{	
																		echo '<img src="assets/images/daumen_hoch.gif">'.$row['tea_name'];
																	}
																	else
																		echo '<input type="checkbox" id="'.$chkbx_name.'" name="'.$chkbx_name.'" value="'.$row['tea_index'].'" '.$checked.' disabled><label for="'.$chkbx_name.'">('.$row['grp_short'].') <img src="assets/images/flags/'.$row['tea_short'].'.gif"> '.$row['tea_name'].'</label>';
																}	
																echo '<br>';
																
															}
															
												echo '</div></div>';
											}
											echo '</section><p></p>
												<header>
													<h1>'.showsetting($dbconn, "pokal").'Tipp</h1>
												</header>';	
											if ((!$fitipplocked && !$userlocked)) // tip abgeben
											{	
												//<form action="member_post.php?m_id=2" method="post">-->
												
												echo '<input type="hidden" name="action" value="3">
												<p>'.showsetting($dbconn, "pokal").' wird: <select class="user-tip-select" name="sti_meister"><option value="0">Bitte w&auml;hlen...</option>';
												
													$result=mysqli_query($dbconn, "SELECT tea_index, tea_name, tea_short, sti_index, sti_meister FROM tea_team 
																								LEFT JOIN sti_spezialtipp on sti_meister=tea_index and sti_usr_index=".$usr_id."
																								ORDER BY tea_index asc");
													while($row=mysqli_fetch_array($result))
														echo '<option value="'.$row["tea_index"].'" '.issel($row["tea_index"], $row["sti_meister"]).'>'.$row["tea_name"].'</option>';
													echo '</select><br>';
													
												//	$koenig = "";
												//	$result=mysqli_query($dbconn, "SELECT sti_index, sti_koenig FROM sti_spezialtipp where sti_usr_index=".$usr_id);
												//	while($row=mysqli_fetch_array($result)) $koenig = $row["sti_koenig"];
												//	echo 'Torsch&uuml;tzenk&ouml;nig wird: <input type="text" name="sti_koenig" value="'.$koenig.'"><br>';
													/*if ((!$fitipplocked && !$userlocked))
														echo '<br><input type="submit" class="small" value="'.showsetting($dbconn, "pokal").' & Torsch&uuml;tzenk&ouml;nig  speichern">';
													*/
													echo'
												</p>';
											}										
											elseif (!$fitipplocked && $userlocked) //nichts sehen
											{
												
											}
											else
											{
												//<form action="member_post.php?m_id=2" method="post">-->
												echo '
												<p>'.showsetting($dbconn, "pokal").' wird: ';
													$result=mysqli_query($dbconn, "SELECT tea_index, tea_name, tea_short, sti_index, sti_meister FROM tea_team 
																								INNER JOIN sti_spezialtipp on sti_meister=tea_index and sti_usr_index=".$usr_id);
													while($row=mysqli_fetch_array($result))
														echo $row["tea_name"];
													echo '<br>';
													
												//	$koenig = "";
												//	$result=mysqli_query($dbconn, "SELECT sti_index, sti_koenig FROM sti_spezialtipp where sti_usr_index=".$usr_id);
												//	while($row=mysqli_fetch_array($result)) $koenig = $row["sti_koenig"];
												//	echo 'Torsch&uuml;tzenk&ouml;nig wird: '.$koenig.'<br>';
													echo'
												</p>';
											}
											echo '
										</div>';
										
									}
									elseif ($m_id == 3) // Spielplan
									{
										echo '
											<header>
												<h1>Spielplan</h1>
											</header><br>
											';
										$result_grp=mysqli_query($dbconn, "SELECT grp_index, grp_name, grp_vorrunde from grp_group order by grp_index asc");
										while($row_grp=mysqli_fetch_array($result_grp))
										{
											echo '
											<section class="columns double">
												<div class="column">
													<b>'.$row_grp["grp_name"].'</b><br>
													<div class="mw6"><table>';				
													$result=mysqli_query($dbconn, "SELECT mat_index, WEEKDAY(mat_starttime) as mat_weekday, UNIX_TIMESTAMP(mat_starttime) as mat_abfragezeit, DATE_FORMAT(mat_starttime, '%d.%m %H:%i' ) AS mat_starttime,  date_format( mat_starttime, '%Y%m%d%H:%i' ) AS mat_order, mat_status, ta.tea_name AS tea_name_home,ta.tea_short AS tea_short_home, tb.tea_name AS tea_name_away, tb.tea_short AS tea_short_away, mat_result_home, mat_result_away, mat_result_string, grp_short FROM mat_match INNER JOIN tea_team ta ON ta.tea_index = mat_tea_index_home INNER JOIN tea_team tb ON tb.tea_index = mat_tea_index_away INNER JOIN grp_group on grp_index=mat_grp_index where mat_grp_index=".$row_grp['grp_index']." ORDER BY mat_order");
													while($row=mysqli_fetch_array($result))
													{
														$status = 0;
														$tipplocked = (date(time()) > date($row['mat_abfragezeit']));
														echo '
															<tr>
															<td class="td2_dat" '.getlivecolor($row['mat_status']).'>'.$row["mat_starttime"].'</td>
															<td class="td2_hom" '.getlivecolor($row['mat_status']).'><img src="assets/images/flags/'.$row["tea_short_home"].'.gif"> '.$row["tea_name_home"].'</td>
															<td class="td2_hom_s" '.getlivecolor($row['mat_status']).'><img src="assets/images/flags/'.$row["tea_short_home"].'.gif"> '.strtoupper($row["tea_short_home"]).'</td>
															<td class="td2_vsp" '.getlivecolor($row['mat_status']).'>-</td>
															<td class="td2_awa" '.getlivecolor($row['mat_status']).'><img src="assets/images/flags/'.$row["tea_short_away"].'.gif"> '.$row["tea_name_away"].'</td>
															<td class="td2_awa_s" '.getlivecolor($row['mat_status']).'><img src="assets/images/flags/'.$row["tea_short_away"].'.gif"> '.strtoupper($row["tea_short_away"]).'</td>';
															if(!$tipplocked ) //spiel ist nocht nicht
															{
																echo '<td class="td2_erg2">-</td>';	
															}
															else
															{
																$resultsstring = $row["mat_result_home"].'-'.$row["mat_result_away"];
																if (strlen($row['mat_result_string'])>0) $resultsstring = $row['mat_result_string']. ' ('.$resultsstring.')';
																
																echo '<td class="td2_erg2" '.getlivecolor($row['mat_status']).'>'.$resultsstring.'</td>'; 
															}
															echo '</tr>';
													}
													echo '
													</table></div>
												</div>
												<div class="column">
													<b>';
													if ($row_grp["grp_vorrunde"] == 1) echo $row_grp["grp_name"];
													echo '</b><br>
													<div class="mw"><table>';
										
												if ($row_grp["grp_vorrunde"] == 1)
												{
													$platz = 1;
													echo '
														<tr>
														<td class="gt_pla">Pl</td>
														<td class="gt_tea">Team</td>
														<td class="gt_spi">Sp</td>
														<td class="gt_dif">Dif</td>
														<td class="gt_tov">Ver</td>
														<td class="gt_pun">Pkt</td>
														</tr>';
														
													$grp_teams = array();	
													$result=mysqli_query($dbconn, "SELECT tea_index, tea_name, tea_short from tea_team where tea_grp_index=".$row_grp['grp_index']);
													while($row=mysqli_fetch_array($result))
													{
														$punkte = 0;
														$spiele = 0;
														$tore_g = 0;
														$tore_k = 0;
														$tore_d = 0;
														
														$result_mat=mysqli_query($dbconn, "SELECT mat_tea_index_away, mat_result_away, mat_result_home, mat_status from mat_match where mat_grp_index=".$row_grp['grp_index']." and (mat_tea_index_away=".$row['tea_index']." OR mat_tea_index_home=".$row['tea_index'].")");
														while($row_mat=mysqli_fetch_array($result_mat))
														{
															if ($row_mat['mat_status'] > 0)
															{
																$goals_self = $row_mat['mat_result_home'];
																$goals_othe = $row_mat['mat_result_away'];
																if ( $row_mat['mat_tea_index_away'] == $row['tea_index'] )
																{
																	$goals_self = $row_mat['mat_result_away'];
																	$goals_othe = $row_mat['mat_result_home'];
																}
																$tore_g = $tore_g + $goals_self;
																$tore_k = $tore_k + $goals_othe;
																$tore_d = $tore_d + ($goals_self - $goals_othe);
																if ($goals_self > $goals_othe)
																	$punkte = $punkte + 3;
																if ($goals_self == $goals_othe)
																	$punkte = $punkte + 1;
																$spiele ++;
															}
														}
														
														$grp_teams[] = [
																		"tea" => $row['tea_index'] + 0,
																		"poi" => $punkte + 0,
																		"tog" => $tore_g + 0,
																		"tok" => $tore_k + 0,
																		"tod" => $tore_d + 0,
																		"spi" => $spiele + 0
																	];
													}
													
													$grp_teams = sortArrayByFields($grp_teams,array('poi' => SORT_DESC,'tod' => SORT_DESC,'tog' => SORT_DESC,'tok' => SORT_ASC));
																										
													foreach($grp_teams as $team)	
													{
														
														$result_tea=mysqli_query($dbconn, "SELECT tea_index, tea_name, tea_short from tea_team where tea_index=".$team["tea"]);
														$row_tea=mysqli_fetch_array($result_tea);
														echo '
														<tr>
														<td class="gt_pla">'.$platz.'</td>
														<td class="gt_tea"><img src="assets/images/flags/'.$row_tea["tea_short"].'.gif"> '.$row_tea["tea_name"].'</td>
														<td class="gt_spi">'.$team["spi"].'</td>
														<td class="gt_dif">'.addvz($team["tod"]).'</td>
														<td class="gt_tov">'.$team["tog"].':'.$team["tok"].'</td>
														<td class="gt_pun">'.$team["poi"].'</td>
														</tr>';
														$platz ++;
													}	
												}
												echo '
													</table></div>
											</section>
											<br><hr><br>';
										}
										
									}
									elseif ($m_id == 4) // Regeln
									{
										$gewinntext = showtirsetting($dbconn, $tir_index, "tir_gewinntext");
										$filler = '<br><br><br><br><br><br><br>';
										echo '
											<header>
												<h1>Regeln</h1>
											</header>
										<p>
										<ul>
											<li>Der Einsatz betr&auml;gt '.showtirsetting($dbconn, $tir_index, "tir_einsatz").' Euro. Bezahlung '.showtirsetting($dbconn, $tir_index, "tir_bezahlung").'</li>
											<li>Jedes Spiel, vom Er&ouml;ffnungspiel bis zum Finale kann jederzeit, maximal aber nat&uuml;rlich bis zum Anpfiff des jeweiligen Spiels unter &quot;Meine Tipps&quot; getippt und beliebig oft ge&auml;ndert werden</li>
											<li>In der Vorrunde bekommt man f&uuml;r ein richtig getipptes Ergebnis '.showsetting($dbconn, "punkte_richtiges_erg").' Punkte, f&uuml;r die richtige Tendenz '.showsetting($dbconn, "punkte_richtige_ten").' Punkte. (Richtige Tendenz bedeutet falsches Ergebnis, aber zumindest war richtig wer gewonnen hat, bzw. ob Unentschieden gespielt wurde)</li>
											<li>F&uuml;r alle Finals (16/, 8/, 4/, Halbfinale, SuP3, Finale) bekommt man f&uuml;r ein richtig getipptes Ergebnis '.showsetting($dbconn, "punkte_richtiges_erg_er").' Punkte, f&uuml;r die Richtige Tendenz '.showsetting($dbconn, "punkte_richtige_ten").' Punkte. Als Tippergebniss ist hier immer der Spielausgang nach 90 Minuten zu tippen (also OHNE Verl&auml;ngerung oder Elmeterschiessen).</li>
											<li>Es gibt zudem den Außenseiterbonus: Tippt man als einer der wenigen eine Tendenz oder ein Ergebnis richtig, so bekommt man '.showsetting($dbconn, "punkte_richtiger_aus").' Punkt extra. "einer der wenigen" bedeutet: Maximal 10 % der Nutzer liegen richtig.</li>
											<li>Vor Beginn des ersten Spiels tippt man Ausserdem unabh&auml;ngig von den einzelnen Spieletipps alle Finalsten (..., Achtelfinale, Viertelfinale,...)
												<br>Dabei ist es egal, ob eine Mannschaft z.B. im Viertelfinale nicht getippt wird, im Halbfinale aber schon wieder usw... alle tipps sind hier frei w&auml;hlbar. <br>F&uuml;r jeden richtig getippen Finalisten bekommt man '.showsetting($dbconn, "punkte_richtiger_gti").' Punkte.
												<br>Diese Tipps tippt und &auml;ndert man NUR bis zum Anpfiff des Er&ouml;ffnungspiels, danach kann man sie NICHT mehr &auml;ndern
											</li>
											<li>Ebenfalls vor Beginn des ersten Spiels tippt man den Weltmeister. Dieser Tipp ist nat&uuml;rlich auch nur bis zum Anpfiff des Er&ouml;ffnungsspiels ver&auml;nderbar. Richtiger Tipp bedeutet am Ende '.showsetting($dbconn, "punkte_richtiger_sti").' Punkte<br></li>
											<li>Aus diesen Punkten ergibt sich eine Tabelle mit den Gesamtpunkten. Bei Punktegleichstand wird nach folgenden Kriterien platziert: Richtige Tendenzen->Richtige Ergebnisse->Richtige Grupeentipps->Richtige Siegertipps->Richtige Aussenseitertipps.</li> 
											<li>Neben der normalen Tabelle gibt es ausserdem noch die Lucky Loser Tabelle. Diese gilt ab dem Sechzehntelfinalefinale. Dort finden sich alle Spieler die aktuell in der "richtigen" Tabelle nicht in den Gewinnrängen stehen. Hier gelten nur die Spieletipps ab dem Sechzehntelfinale (keine Gruppen- oder Siegertipps).</li>
											'.(strlen($gewinntext)>0 ? '<li>Die Preisgeldliste:'.$gewinntext : $filler).'</li>
										</ul>
										</p>';
									}
									
									elseif ($m_id == 5) // Aktuelles
									{
										echo '
										<div class="box ">
										<div class="header-box">
											<h5>last & next 3</h5>
										</div>
										<div class="detail-box">
											<div class="member-match-list">
											<div class="mtnb mt_dat bld notmobile"></div>
											<div class="mtnb mt_dat_s bld notmobile"></div>
											<div class="mtnb mt_gru bld notmobile"></div>
											<div class="mtnb mt_hom bld notmobile"></div>
											<div class="mtnb mt_hom_s bld notmobile"></div>
											<div class="mtnb mt_vsp bld notmobile"></div>
											<div class="mtnb mt_awa bld notmobile"></div>
											<div class="mtnb mt_awa_s bld notmobile"></div>
											<div class="mtnb mt_tip bld notmobile">Tipp</div>
											<div class="mtnb mt_erg bld notmobile">Erg</div>
											<div class="mtnb mt_thu bld notmobile">&nbsp;</div>
											<div class="cb notmobile"></div>';

										$last_mat_id = 0;
										$result=mysqli_query($dbconn, "SELECT tip_index, tip_result_home, tip_result_away, tip_ki, tip_points_aus, mat_match.mat_index, WEEKDAY(mat_starttime) as mat_weekday, UNIX_TIMESTAMP(mat_starttime) as mat_abfragezeit, DATE_FORMAT(mat_starttime, '%d.%m %H:%i' ) AS mat_starttime, DATE_FORMAT(mat_starttime, '%d.%m %Hh' ) AS mat_starttime_s, date_format(mat_starttime, '%Y%m%d%H:%i') AS mat_order, mat_status, ta.tea_name AS tea_name_home, ta.tea_short AS tea_short_home, tb.tea_name AS tea_name_away, tb.tea_short AS tea_short_away, mat_result_home, mat_result_away, mat_result_string, grp_short FROM (SELECT mat_index FROM mat_match WHERE mat_status>0 ORDER BY mat_starttime DESC, mat_index DESC LIMIT 3) recent_matches INNER JOIN mat_match ON mat_match.mat_index=recent_matches.mat_index INNER JOIN tea_team ta ON ta.tea_index=mat_tea_index_home INNER JOIN tea_team tb ON tb.tea_index=mat_tea_index_away INNER JOIN grp_group ON grp_index=mat_grp_index LEFT JOIN tip_tipp ON tip_mat_index=mat_match.mat_index AND tip_usr_index=".$usr_index." ORDER BY mat_match.mat_starttime ASC, mat_match.mat_index ASC");
										while($row=mysqli_fetch_array($result))
										{ 	
											echo '
											<div class="mtnb mt_dat'.getlivecolor($row['mat_status']).'">'.getWeekDay($row["mat_weekday"]).' '.$row["mat_starttime"].'</div>
											<div class="mtnb mt_dat_s'.getlivecolor($row['mat_status']).'">'.$row["mat_starttime_s"].'</div>
											<div class="mtnb mt_gru'.getlivecolor($row['mat_status']).'">'.$row["grp_short"].'</div>
											<div class="mtnb mt_hom'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" ><img src="assets/images/flags/'.$row["tea_short_home"].'.gif"> '.$row["tea_name_home"].'</a></div>
											<div class="mtnb mt_hom_s'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" ><img src="assets/images/flags/'.$row["tea_short_home"].'.gif"> '.strtoupper($row["tea_short_home"]).'</a></div>
											<div class="mtnb mt_vsp'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" >-</a></div>
											<div class="mtnb mt_awa'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" ><img src="assets/images/flags/'.$row["tea_short_away"].'.gif"> '.$row["tea_name_away"].'</a></div>
											<div class="mtnb mt_awa_s'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" ><img src="assets/images/flags/'.$row["tea_short_away"].'.gif"> '.strtoupper($row["tea_short_away"]).'</a></div>';
											
											$thumb='daumen_runter.gif';
											if (tendenz_richtig($row["mat_result_home"], $row["mat_result_away"], $row["tip_result_home"], $row["tip_result_away"]) == 1) $thumb='daumen_quer.gif';
											elseif (ergebnis_richtig($row["mat_result_home"], $row["mat_result_away"],  $row["tip_result_home"], $row["tip_result_away"]) == 1) $thumb='daumen_hoch.gif';
											
											if (isset($row["tip_index"]))
											{
												if ($row["tip_points_aus"] == 1)
													$thumb='shark.gif';
												if ($row["tip_ki"] == 1)
												{
													if ($thumb == 'daumen_runter.gif') $thumb='daumen_runter_a.gif';
													elseif ($thumb == 'daumen_quer.gif') $thumb='daumen_quer_a.gif';
													elseif ($thumb == 'daumen_hoch.gif')$thumb='daumen_hoch_a.gif';
													elseif ($thumb == 'shark.gif') $thumb='shark_a.gif';
												}
											}
											
											echo '
													<div class="mtnb mt_tip'.getlivecolor($row['mat_status']).'">'.$row["tip_result_home"].'-'.$row["tip_result_away"].'</div>
													<div class="mtnb mt_erg'.getlivecolor($row['mat_status']).'">'.$row["mat_result_home"].'-'.$row["mat_result_away"].'</div>
													<div class="mtnb mt_thu'.getlivecolor($row['mat_status']).'"><img src="assets/images/'.$thumb.'"></div><div class="cb"></div>';
											
											$last_mat_id = $row["mat_index"];
										}
										if ($last_mat_id>0) $era = "#anc".($last_mat_id-1);

										$result=mysqli_query($dbconn, "SELECT tip_index, tip_result_home, tip_result_away, mat_index, WEEKDAY(mat_starttime) as mat_weekday, UNIX_TIMESTAMP(mat_starttime) as mat_abfragezeit, DATE_FORMAT(mat_starttime, '%d.%m %H:%i' ) AS mat_starttime, DATE_FORMAT(mat_starttime, '%d.%m %Hh' ) AS mat_starttime_s, date_format(mat_starttime, '%Y%m%d%H:%i') AS mat_order, mat_status, ta.tea_name AS tea_name_home, ta.tea_short AS tea_short_home, tb.tea_name AS tea_name_away, tb.tea_short AS tea_short_away, mat_result_home, mat_result_away, mat_result_string, grp_short FROM mat_match INNER JOIN tea_team ta ON ta.tea_index=mat_tea_index_home INNER JOIN tea_team tb ON tb.tea_index=mat_tea_index_away INNER JOIN grp_group ON grp_index=mat_grp_index LEFT JOIN tip_tipp ON tip_mat_index=mat_index AND tip_usr_index=".$usr_index." WHERE mat_status=0 AND mat_starttime>NOW() ORDER BY mat_match.mat_starttime ASC, mat_match.mat_index ASC LIMIT 3");
										while($row=mysqli_fetch_array($result))
										{ 
											echo '
												<div class="mtnb mt_dat'.getlivecolor($row['mat_status']).'">'.getWeekDay($row["mat_weekday"]).' '.$row["mat_starttime"].'</div>
												<div class="mtnb mt_dat_s'.getlivecolor($row['mat_status']).'">'.$row["mat_starttime_s"].'</div>
												<div class="mtnb mt_gru'.getlivecolor($row['mat_status']).'">'.$row["grp_short"].'</div>
												<div class="mtnb mt_hom'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" ><img src="assets/images/flags/'.$row["tea_short_home"].'.gif"> '.$row["tea_name_home"].'</a></div>
												<div class="mtnb mt_hom_s'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" ><img src="assets/images/flags/'.$row["tea_short_home"].'.gif"> '.strtoupper($row["tea_short_home"]).'</a></div>
												<div class="mtnb mt_vsp'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" >-</a></div>
												<div class="mtnb mt_awa'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" ><img src="assets/images/flags/'.$row["tea_short_away"].'.gif"> '.$row["tea_name_away"].'</a></div>
												<div class="mtnb mt_awa_s'.getlivecolor($row['mat_status']).'"><a class="fuck'.trim(getlivecolor($row['mat_status'])).'" onclick="togglealletipps('.$row["mat_index"].')" ><img src="assets/images/flags/'.$row["tea_short_away"].'.gif"> '.strtoupper($row["tea_short_away"]).'</a></div>';
												
												$tip = $row["tip_result_home"].'-'.$row["tip_result_away"]; if (strlen($tip) == 1)$tip='<a href="member.php?m_id=2">TIPPEN</a>';
												$current_result = "";
												if ((int)$row["mat_status"] === 1)
													$current_result = $row["mat_result_home"].'-'.$row["mat_result_away"];
												echo '
												<div class="mtnb mt_tip'.getlivecolor($row['mat_status']).'">'.$tip.'</div>
												<div class="mtnb mt_erg'.getlivecolor($row['mat_status']).'">'.$current_result.'</div>
												<div class="mtnb mt_thu'.getlivecolor($row['mat_status']).'"></div><div class="cb"></div>';
										
												$last_mat_id = $row["mat_index"];
										}
										
										if ($last_mat_id>0) $era = "#anc".($last_mat_id-4);
											
										echo '</div><br>
										<a href="member.php?m_id=2'.$era.'">gehe zu Meine Tipps</a>
										&nbsp;&nbsp;&nbsp;
										<a href="member.php?m_id=1'.$era.'">gehe zur Tabelle</a>
										</div></div>';


											
										
											echo '
											<div class="box ">
											<div class="header-box">
												<h5>Die letzten 3 Chat Eintr&auml;ge</h5>
											</div><div class="detail-box">';
										$filter = "LIMIT 3";

										$result=mysqli_query($dbconn, "SELECT com_index, com_text, DATE_FORMAT(com_timestamp, '%d.%m %H:%i' ) AS com_timestamp, com_color, com_media, usr_name, usr_vornachname from com_comment inner join usr_user on com_usr_index=usr_index INNER JOIN urm_usrtirmapping on usr_index=urm_usr_index where urm_tir_index=".$tir_index." AND com_com_index=0 order by com_index desc ".$filter);
										while($row=mysqli_fetch_array($result))
										{
											echo '<div class="comment-thread">
												<div class="comdiv">
													<b>'.$row["usr_name"].' ('.$row["usr_vornachname"].'), '.$row["com_timestamp"].':</b><br>';
											if (strlen($row["com_media"])>0) echo '<img src="uplds/'.$row["com_media"].'"><br>';
											echo renderCommentText($row["com_text"]).'</div>';
											
											$answer_result=mysqli_query($dbconn, "SELECT com_text, DATE_FORMAT(com_timestamp, '%d.%m %H:%i' ) AS com_timestamp, com_color, com_media, usr_name, usr_vornachname from com_comment inner join usr_user on com_usr_index=usr_index INNER JOIN urm_usrtirmapping on usr_index=urm_usr_index where urm_tir_index=".$tir_index." AND com_com_index=".(int)$row["com_index"]." order by com_index asc");
											while($answer=mysqli_fetch_array($answer_result))
											{
												echo '<div class="comdiv comdiv-answer">
													<b>'.$answer["usr_name"].' ('.$answer["usr_vornachname"].'), '.$answer["com_timestamp"].':</b><br>';
												if (strlen($answer["com_media"])>0) echo '<img src="uplds/'.$answer["com_media"].'"><br>';
												echo renderCommentText($answer["com_text"]).'</div>';
											}
											echo '</div>';
										}
										echo '<br>
										<a href="member.php?m_id=8">Zeige den gesamten Chat</a>
										</div></div>';
										
										//beginn tabelle
										$mat_index=0;
											$islive = 0;
										$result=mysqli_query($dbconn, "SELECT mat_index, mat_status from mat_match where mat_status>0 order by mat_starttime desc limit 1");
										while($row=mysqli_fetch_array($result)) 
										{
											$mat_index=$row["mat_index"];
											if ($row["mat_status"] == 1) $islive = 1;
										}
										echo '
											<div class="box ">
											<div class="header-box">
												<h5>Aktuelle Tabelle</h5>
											</div><div class="detail-box">';
										$showsecondtable = ((showtirsetting($dbconn, $tir_index, "tir_scndtbl") == 1) && isEndrunde($dbconn));
										echo '<section class="columns double1'.(!$showsecondtable ? ' single-table' : '').'">';
										
										$sieger = array();
										$gewinercount = 0;
										$gewinnermax = showtirsetting($dbconn, $tir_index, "tir_winnercount") + 0;
													
											$borderprinted = 0;
											$platz_print = 0;
											$platz_count = 0;
											$last_poi = -1;
											$last_erg = -1;
											$last_ten = -1;
											$last_gti = -1;
											$last_sti = -1;
											$last_aus = -1;
											$last_kit = -1;
											$tabelle = gettable($dbconn, $tir_index, $mat_index);
											$printtabelle = array();
											$usrrow = 0;
											foreach ($tabelle as $eintrag)
											{
												$usr = $eintrag["usr"];
												$poi = $eintrag["poi"];
												$erg = $eintrag["erg"];
												$ten = $eintrag["ten"];
												$gti = $eintrag["gti"];
												$sti = $eintrag["sti"];
												$aus = $eintrag["aus"];
												$kit = $eintrag["kit"];
												$pla = $eintrag["pla"];
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
												
												//speichere sieger für secondtable
												$gewinercount ++;
												
											//		echo "(".$gewinercount.",".$gewinnermax.")";
												if ($gewinercount <= $gewinnermax)
												{
													$sieger[] = $usr;
												}
												if ($usr_index == $usr) $usrrow = sizeof($printtabelle);
												
												$printtabelle[] = '
													<tr> <td class="td_pla tb_notmobile'.getlivecolor($islive).'">'.$pla.'</td>
													<td class="td_nam tb_notmobile'.getlivecolor($islive).'"><a class="fuck'.trim(getlivecolor($islive)).'" href="member.php?m_id=2&usr_id='.$usr.'">'.showusrsetting($dbconn, $usr, "usr_name").' ('.showusrsetting($dbconn, $usr, "usr_vornachname").')</a></td>
													<td class="td_nam_s tb_notmobile'.getlivecolor($islive).'"><a class="fuck'.trim(getlivecolor($islive)).'" href="member.php?m_id=2&usr_id='.$usr.'" title="'.showusrsetting($dbconn, $usr, "usr_vornachname").'">'.showusrsetting($dbconn, $usr, "usr_name").'</a></td>
													<td class="td_erg tb_notmobile'.getlivecolor($islive).'">'.$erg.'</td>
													<td class="td_ten tb_notmobile'.getlivecolor($islive).'">'.$ten.'</td>
													<td class="td_gti tb_notmobile'.getlivecolor($islive).'">'.$gti.'</td>
													<td class="td_sti tb_notmobile'.getlivecolor($islive).'">'.$sti.'</td>
													<td class="td_aus tb_notmobile'.getlivecolor($islive).'">'.$aus.'</td>
													<td class="td_all tb_notmobile'.getlivecolor($islive).'">'.$erg.'/'.$ten.'/'.$gti.'/'.$sti.'/'.$aus.'</td>
													<td class="td_poi tb_notmobile'.getlivecolor($islive).'"><b>'.$poi.'</b></td>
													</tr>'; 
													
											}
											if (sizeof($printtabelle)>2)
											{
												
										echo '
										<div class="column1">
											<p>
													<table>
													';
												
											if ($usrrow>1) echo '<tr><td class="td_pla"></td><td colspan="10">...</td></tr>';
											if ($usrrow==0) echo $printtabelle[0].$printtabelle[1].$printtabelle[2];
											elseif ($usrrow==(sizeof($printtabelle)-1)) echo $printtabelle[$usrrow-2].$printtabelle[$usrrow-1].$printtabelle[$usrrow];
											else echo $printtabelle[$usrrow-1].$printtabelle[$usrrow].$printtabelle[$usrrow+1];
											if ($usrrow<(sizeof($printtabelle)-2)) echo '<tr><td class="td_pla"></td><td  colspan="10">...</td></tr>';
											echo '</table></div><p>&nbsp;</p>';
											}
											
										$gewinnermax = showtirsetting($dbconn, $tir_index, "tir_scndtbl_winnercount");
										$borderprinted = 0;
										$platz_print = 0;
										$platz_count = 0;
										$last_poi = -1;
										$last_erg = -1;
										$last_ten = -1;
										$last_aus = -1;
																			
										if ($showsecondtable)
										{
												
											$printtabelle = array();
											$usrrow = 0;									
											$tabelle = gettable($dbconn, $tir_index, $mat_index, $sieger);
											foreach ($tabelle as $eintrag)
											{
												$usr = $eintrag["usr"];
												$poi = $eintrag["poi"];
												$erg = $eintrag["erg"];
												$ten = $eintrag["ten"];
												$aus = $eintrag["aus"];
												$pla = $eintrag["pla"];
												$kit = $eintrag["kit"];
												$platz_count ++;
												if (tabchanged($last_poi, $last_erg, $last_ten, 0, 0, $last_aus, $last_kit, $poi, $erg, $ten, 0, 0, $aus, $kit)) 
												{
													$platz_print = $platz_count;
													$last_poi = $poi;
													$last_erg = $erg;
													$last_ten = $ten;
													$last_aus = $aus;
													$last_kit = $kit;
												}

												if ($usr_index == $usr) $usrrow = sizeof($printtabelle);
												$printtabelle[] = '
													<tr> <td class="td_pla tb_notmobile'.getlivecolor($islive).'">'.$pla.'</td>
													<td class="td_nam tb_notmobile'.getlivecolor($islive).'"><a class="fuck'.trim(getlivecolor($islive)).'" href="member.php?m_id=2&usr_id='.$usr.'">'.showusrsetting($dbconn, $usr, "usr_name").' ('.showusrsetting($dbconn, $usr, "usr_vornachname").')</a></td>
													<td class="td_nam_s tb_notmobile'.getlivecolor($islive).'"><a class="fuck'.trim(getlivecolor($islive)).'" href="member.php?m_id=2&usr_id='.$usr.'" title="'.showusrsetting($dbconn, $usr, "usr_vornachname").'">'.showusrsetting($dbconn, $usr, "usr_name").'</a></td>
													<td class="td_erg tb_notmobile'.getlivecolor($islive).'">'.$erg.'</td>
													<td class="td_ten tb_notmobile'.getlivecolor($islive).'">'.$ten.'</td>
													<td class="td_aus tb_notmobile'.getlivecolor($islive).'">'.$aus.'</td>
													<td class="td_all tb_notmobile'.getlivecolor($islive).'">'.$erg.'/'.$ten.'/'.$aus.'</td>
													<td class="td_poi tb_notmobile'.getlivecolor($islive).'"><b>'.$poi.'</b></td>
													</tr>';
											}
											if (sizeof($printtabelle)>2)
											{
												
										echo '
										<div class="column2">
											<p>
													<table>
													';
												
											if ($usrrow>1) echo '<tr><td class="td_pla"></td><td colspan="10">...</td></tr>';
											if ($usrrow==0) echo $printtabelle[0].$printtabelle[1].$printtabelle[2];
											elseif ($usrrow==(sizeof($printtabelle)-1)) echo $printtabelle[$usrrow-2].$printtabelle[$usrrow-1].$printtabelle[$usrrow];
											else echo $printtabelle[$usrrow-1].$printtabelle[$usrrow].$printtabelle[$usrrow+1];
											if ($usrrow<(sizeof($printtabelle)-2)) echo '<tr><td class="td_pla"></td><td  colspan="10">...</td></tr>';
											echo '</table></div><p>&nbsp;</p>';
											}
										}
										echo '</section><br>
											<a href="member.php?m_id=1">gehe zur Tabelle</a>
											</div></div>';
										
										
										//end tabelle
										
									}
									elseif ($m_id == 7) // Spielanalyse
									{
										$mat_index=0; if (isset($_REQUEST['mat_index'])) $mat_index=$_REQUEST['mat_index'];
										echo '
											<header>
												<h1>Spielanalyse</h1>
											</header>
										<p>';
											echo '
											<section class="columns double">
												<div class="column">
													<div class="mw6">';				
													$result=mysqli_query($dbconn, "SELECT mat_index, WEEKDAY(mat_starttime) as mat_weekday, UNIX_TIMESTAMP(mat_starttime) as mat_abfragezeit, DATE_FORMAT(mat_starttime, '%d.%m %H:%i' ) AS mat_starttime,  date_format( mat_starttime, '%Y%m%d%H:%i' ) AS mat_order, mat_status, ta.tea_name AS tea_name_home,ta.tea_short AS tea_short_home, tb.tea_name AS tea_name_away, tb.tea_short AS tea_short_away, mat_result_home, mat_result_away, mat_result_string, grp_short FROM mat_match INNER JOIN tea_team ta ON ta.tea_index = mat_tea_index_home INNER JOIN tea_team tb ON tb.tea_index = mat_tea_index_away INNER JOIN grp_group on grp_index=mat_grp_index where mat_index=".$mat_index);
													while($row=mysqli_fetch_array($result))
													{
														$status = 0;
														$tipplocked = (date(time()) > date($row['mat_abfragezeit']));
														$resultsstring = $row["mat_result_home"].'-'.$row["mat_result_away"];
														if (strlen($row['mat_result_string'])>0) $resultsstring = $row['mat_result_string']. '('.$resultsstring.')';
														if ($tipplocked)
														{	
														echo '<table>
																<tr>
																	<td class="td2_dat" '.getlivecolor($row['mat_status']).'>'.$row["mat_starttime"].'</td>
																	<td class="td2_hom" '.getlivecolor($row['mat_status']).'><img src="assets/images/flags/'.$row["tea_short_home"].'.gif"> '.$row["tea_name_home"].'</td>
																	<td class="td2_hom_s" '.getlivecolor($row['mat_status']).'><img src="assets/images/flags/'.$row["tea_short_home"].'.gif"> '.strtoupper($row["tea_short_home"]).'</td>
																	<td class="td2_vsp" '.getlivecolor($row['mat_status']).'>-</td>
																	<td class="td2_awa" '.getlivecolor($row['mat_status']).'><img src="assets/images/flags/'.$row["tea_short_away"].'.gif"> '.$row["tea_name_away"].'</td>
																	<td class="td2_awa_s" '.getlivecolor($row['mat_status']).'><img src="assets/images/flags/'.$row["tea_short_away"].'.gif"> '.strtoupper($row["tea_short_away"]).'</td>
																	<td class="td2_erg" '.getlivecolor($row['mat_status']).'>'.$resultsstring.'</td> 
																</tr>
																</table><a href="member.php?m_id=2">zur&uuml;ck zu Mein Tipp</a><br><br>
															<table>
																<tr>
																	<td class="td2_homnr">Benutzer</td>
																	<td class="td2_erg">Tipp</td>
																	<td class="td2_erg">Ergebnis</td> 
																</tr>';
																
																$result_t=mysqli_query($dbconn, "SELECT tip_index,  tip_result_home, tip_result_away, tip_points_erg, tip_points_ten, tip_points_aus, tip_ki, usr_name, usr_vornachname from tip_tipp inner join usr_user on usr_index=tip_usr_index INNER JOIN urm_usrtirmapping on usr_index=urm_usr_index where tip_mat_index=".$row['mat_index']." and urm_tir_index = ".$tir_index);
																while($row_t=mysqli_fetch_array($result_t))
																{
																	$thumb='daumen_runter.gif';
																	if ($row_t["tip_points_ten"]>1)  $thumb='daumen_quer.gif';
																	elseif ($row_t["tip_points_erg"]>1)   $thumb='daumen_hoch.gif';
																	if (isset($row_t["tip_index"]))
																	{
																		if ($row_t["tip_points_aus"] == 1)
																			$thumb='shark.gif';
																		if ($row_t["tip_ki"] == 1)
																		{
																			if ($thumb == 'daumen_runter.gif') $thumb='daumen_runter_a.gif';
																			elseif ($thumb == 'daumen_quer.gif') $thumb='daumen_quer_a.gif';
																			elseif ($thumb == 'daumen_hoch.gif')$thumb='daumen_hoch_a.gif';
																			elseif ($thumb == 'shark.gif') $thumb='shark_a.gif';
																		}
													
																	}
																	echo '
																		<tr>
																		<td class="td2_homnr" '.getlivecolor($row['mat_status']).'>'.$row_t["usr_name"].' ('.$row_t["usr_vornachname"].')</td>
																		<td class="td2_erg" '.getlivecolor($row['mat_status']).'>'.$row_t["tip_result_home"].' - '.$row_t["tip_result_away"].'</td>
																		<td class="td2_erg" '.getlivecolor($row['mat_status']).'><img src="images/'.$thumb.'"></td> 
																		</tr>';
																} 
																echo '</table>';
														}	
														else
														{
															echo "Tippps anderer Nutzer erst nach Anpfiff sichtbar";
														}
													}
													echo '<br><br><a href="member.php?m_id=2">zur&uuml;ck zu Mein Tipp</a>
													</div>
												</div>
												</section>
											</p>';
									}
									
									elseif ($m_id == 8) // Chat
									{
										$all = 0; if (isset($_REQUEST['all'])) $all = $_REQUEST['all'];
										$upload_error = "";
										if (isset($_SESSION['upload_error']))
										{
											$upload_error = $_SESSION['upload_error'];
											unset($_SESSION['upload_error']);
										}
										
										echo '
												<div class="box ">
												<div class="detail-box">';
										if (strlen($upload_error) > 0)
										{
											echo '<div class="box"><div class="detail-box"><b>Fehler:</b> '.htmlspecialchars($upload_error, ENT_QUOTES, 'UTF-8').'</div></div>';
										}
										echo '
												<b>
													<br><a onclick="toggleCompost()" href="#" class="button" name="newComment" id="newComment">Neuen Eintrag verfassen</a></b><br>
													<form action="member_post.php?m_id='.$m_id.'" method="post" enctype="multipart/form-data">
													<div id="compost" name="compost" style="display:none;">
														<br><textarea name="editor1" id="editor1"></textarea><script>CKEDITOR.replace( "editor1");</script><br>
														Bild hochladen: <input type="file" name="comimg" accept=".jpg, .jpeg, .gif, .png"><br><br>
														<input type="submit" class="small" value="speichern">
														<a onclick="toggleCompost()" href="#" class="button small">abbrechen</a></b><br>
														<input type="hidden" name="newm" value="'.$m_id.'">
														<input type="hidden" name="com_com_index" value="0">
													</div>
													</form><br>
													
													<div id="answerOverlay" class="comment-answer-overlay" aria-hidden="true">
														<div class="comment-answer-dialog" role="dialog" aria-modal="true" aria-labelledby="answerDialogTitle">
															<button type="button" class="comment-answer-close" onclick="closeAnswer()" aria-label="Fenster schließen">&times;</button>
															<h3 id="answerDialogTitle">Antwort verfassen</h3>
															<form action="member_post.php?m_id='.$m_id.'" method="post" enctype="multipart/form-data">
																<textarea name="editor1" id="answerEditor"></textarea>
																<br>
																Bild hochladen: <input type="file" name="comimg" accept=".jpg, .jpeg, .gif, .png"><br><br>
																<input type="hidden" name="newm" value="'.$m_id.'">
																<input type="hidden" name="com_com_index" id="answerComIndex" value="0">
																<input type="submit" class="small" value="Antwort speichern">
																<button type="button" class="button small" onclick="closeAnswer()">abbrechen</button>
															</form>
														</div>
													</div>';
													
													$filter = "LIMIT 20";
													if ($all == 1) $filter = "";
													$result=mysqli_query($dbconn, "SELECT com_index, com_text, DATE_FORMAT(com_timestamp, '%d.%m %H:%i' ) AS com_timestamp, com_color, com_media, usr_name, usr_vornachname from com_comment inner join usr_user on com_usr_index=usr_index INNER JOIN urm_usrtirmapping on usr_index=urm_usr_index where urm_tir_index=".$tir_index." AND com_com_index=0 order by com_index desc ".$filter);
													while($row=mysqli_fetch_array($result))
													{
														//echo '<div class="comdiv bgc'.$row["com_color"].'">
														echo '<div class="comment-thread">
															<div class="comdiv">
															<b>'.$row["usr_name"].' ('.$row["usr_vornachname"].'), '.$row["com_timestamp"].':</b><br>';
														if (strlen($row["com_media"])>0) echo '<img src="uplds/'.$row["com_media"].'"><br>';
														echo renderCommentText($row["com_text"]).'<br>
														<a onclick="newanswer('.$row["com_index"].'); return false;" href="#" class="button small">Antwort verfassen</a><br>
														</div>';
														
														$answer_result=mysqli_query($dbconn, "SELECT com_text, DATE_FORMAT(com_timestamp, '%d.%m %H:%i' ) AS com_timestamp, com_color, com_media, usr_name, usr_vornachname from com_comment inner join usr_user on com_usr_index=usr_index INNER JOIN urm_usrtirmapping on usr_index=urm_usr_index where urm_tir_index=".$tir_index." AND com_com_index=".(int)$row["com_index"]." order by com_index asc");
														while($answer=mysqli_fetch_array($answer_result))
														{
															echo '<div class="comdiv comdiv-answer">
																<b>'.$answer["usr_name"].' ('.$answer["usr_vornachname"].'), '.$answer["com_timestamp"].':</b><br>';
															if (strlen($answer["com_media"])>0) echo '<img src="uplds/'.$answer["com_media"].'"><br>';
															echo renderCommentText($answer["com_text"]).'
															</div>';
														}
														echo '</div>';
													}
													echo '<a href="member.php?m_id='.$m_id.'&all=1">Lade den gesamten Chat</a>
													</div></div>';
									}
									
									elseif ($m_id == 9) // Einstellungen
									{
										echo '<div class="box ">
											<div class="header-box">
												<h5>Auto-Tipp</h5>
											</div>
											<div class="detail-box">
											<form action="member_post.php?m_id='.$m_id.'" method="post" enctype="multipart/form-data">';
												$auto = 0;
												$result=mysqli_query($dbconn, "SELECT usr_auto from usr_user where usr_index=".$usr_index);
												while($row=mysqli_fetch_array($result))$auto = $row["usr_auto"];
												echo '
												...d.h. wenn man mal vergisst zu tippen, tippt der Computer automatisch ein zuf&auml;lliges Ergebnis<br>
												<input type="checkbox" id="usr_auto" name="usr_auto" value="1" '.ischk($auto, 1).'><label for="usr_auto">AutoTipp aktivert</label><br>
												<input type="submit" class="small" value="speichern">
												<input type="hidden" name="usr_index" value="'.$usr_index.'">
												</form>
											</div>
											</div>';
											
										echo '<div class="box ">
											<div class="header-box">
												<h5>Neues Passwort setzen</h5>
											</div>
											<div class="detail-box">
											<form action="member_post.php?m_id='.$m_id.'&pw=1" method="post" enctype="multipart/form-data">
												<input name="passwort" type="password" placeholder="Neues Passwort">
												<input name="passwort2" type="password" placeholder="Neues Passwort (noch einmal)">
												<input type="submit" class="small" value="speichern">
												<input type="hidden" name="usr_index" value="'.$usr_index.'">
												</form>
											</div>
											</div>';
										echo '<div class="box ">
											<div class="header-box">
												<h5>Eigene Tipprunde erstellen ?</h5>
											</div>
											<div class="detail-box">
												...kein Problem, schreib mir einfach eine Email. 
											</div>
											</div>';

									}	
								}
							?>
							
				
				</section>
				
		<?php
			if (($m_id==2) && (!$userlocked))
			{
				echo '	<div class="mobile-tip-save"><input type="submit" value="ALLE TIPPS SPEICHERN"></div>
					<section id="footer">
						<center><input type="submit" value="ALLE TIPPS SPEICHERN" style="margin-top: 7px;"></center>
					</footer></form>';	 
			}
		?>
		
			</div>						
		</div>
		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.poptrox.min.js"></script>
			<script src="assets/js/jquery.scrolly.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>
			<script language="JavaScript">
			  function check(max, group, boxname)
			  {
				  
				var objekte_gewaehlt=0;   
				var formname = "fiti_"+group;
				//var x = document.forms[formname].elements;
				var x = document.getElementById("gtidiv"+group).querySelectorAll("input"); 
				var i;
				var txt = "";
				for (i = 0; i < x.length; i++) {
					if (x.item(i).type.toLowerCase() == "checkbox") 
					{	
						if (x.item(i).checked == true)
							objekte_gewaehlt ++;
					}
				}
				
				if (objekte_gewaehlt > max)
				{
					document.getElementById(boxname).checked = false;
					//document.forms[formname].elements[boxname].checked = false;
					objekte_gewaehlt --;
				}
				var boldname = "noch_"+group;
				var y = max - objekte_gewaehlt;
				document.getElementById(boldname).innerHTML = y;
				
				
				//var x = document.getElementById("gtidiv"+group).querySelectorAll("input"); 
					//alert (x.length);
				//'chk_'.$row_grp["grp_index"].'_'.$row['tea_index'];
				
			  }
			</script>
			<script language="JavaScript">
			function toggleCompost() {
				var x = document.getElementById("compost");
				var y = document.getElementById("newComment");
				if (x.style.display === "none") {
					x.style.display = "block";
					y.style.display = "none";
				} else {
					x.style.display = "none";
					y.style.display = "block";
				}
			}
			function newanswer(comIndex) {
				var overlay = document.getElementById("answerOverlay");
				var indexField = document.getElementById("answerComIndex");
				if (!overlay || !indexField) return;
				
				indexField.value = comIndex;
				overlay.classList.add("is-open");
				overlay.setAttribute("aria-hidden", "false");
				document.body.classList.add("comment-answer-open");
				
				if (!CKEDITOR.instances.answerEditor) {
					var answerEditor = CKEDITOR.replace("answerEditor");
					answerEditor.on("instanceReady", function() {
						answerEditor.focus();
					});
				} else {
					CKEDITOR.instances.answerEditor.focus();
				}
			}
			function closeAnswer() {
				var overlay = document.getElementById("answerOverlay");
				var indexField = document.getElementById("answerComIndex");
				if (!overlay) return;
				
				overlay.classList.remove("is-open");
				overlay.setAttribute("aria-hidden", "true");
				document.body.classList.remove("comment-answer-open");
				if (indexField) indexField.value = 0;
			}
			document.addEventListener("keydown", function(event) {
				if (event.key === "Escape") closeAnswer();
			});
			document.addEventListener("click", function(event) {
				var overlay = document.getElementById("answerOverlay");
				if (overlay && event.target === overlay) closeAnswer();
			});
			function togglealletipps(mat) {
				var formname = "alletipps"+mat;
				var x = document.getElementById(formname);
				if (x.style.display === "none") {
					x.style.display = "block";
				} else {
					x.style.display = "none";
				}
			}
			</script>
		<?php
			if (($m_id==2) && (!$userlocked))
			{
				echo '
			<script language="JavaScript">
			(function() {
				var tipForm = document.getElementById("meintippForm");
				if (!tipForm) return;

				var isSubmitting = false;

				function hasUnsavedTipInputs() {
					var fields = tipForm.querySelectorAll("input[name^=\'tip_result_home\'], input[name^=\'tip_result_away\']");
					for (var i = 0; i < fields.length; i++) {
						if ((fields[i].value || "").trim().length > 0) return true;
					}
					return false;
				}

				tipForm.addEventListener("submit", function() {
					isSubmitting = true;
				});

				document.addEventListener("click", function(e) {
					var link = e.target.closest("a[href]");
					if (!link) return;
					if (isSubmitting) return;
					var href = link.getAttribute("href");
					if (!href || href === "#" || href.indexOf("javascript:") === 0) return;
					if (!hasUnsavedTipInputs()) return;

					e.preventDefault();
					var saveNow = window.confirm("Vorsicht: Mindestens ein Tipp wurde noch nicht gespeichert.\\n\\nOK = Jetzt speichern\\nAbbrechen = Nicht speichern");
					if (saveNow) {
						isSubmitting = true;
						tipForm.submit();
					} else {
						window.location.href = href;
					}
				});

				window.addEventListener("beforeunload", function(e) {
					if (isSubmitting) return;
					if (!hasUnsavedTipInputs()) return;
					e.preventDefault();
					e.returnValue = "";
				});
			})();
			</script>';
			}
		?>
	<script type="text/javascript" src="assets/js/jquery.simplyscroll.js"></script>
<script type="text/javascript">
(function($) {
	$(function() { //on DOM ready
		$("#scroller").simplyScroll({
			customClass: 'simply-scroll',
			orientation: 'vertical',
            auto: true,
            manualMode: 'loop',
			frameRate: 20,
			speed: 1
		});
	});
})(jQuery);
</script>

	</body>
</html>
