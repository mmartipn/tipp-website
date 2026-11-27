<?php
	session_start ();
	include 'functions.php';
	$dbconn = sqlconnect();
	
	$m_id = 0; 	if (isset($_REQUEST["m_id"])) $m_id = $_REQUEST["m_id"];
	$action = 0; 	if (isset($_REQUEST["action"])) $action = $_REQUEST["action"]; //0=verwalten, 1=new, 2=del
	$usr_index = 0;
	
	if (!isset ($_SESSION["user_id_tipp"])) 
	{
		header("Location: index.php");
	}
	else
	{
		$usr_index = $_SESSION["user_id_tipp"];
		if ($usr_index != 1) header("Location: index.php");
	}
	
?>
<html>
	<head>
		<title>Admin</title>
		<link rel="stylesheet" href="assets/css/admin.css">
	</head>
	<body>
		<?php
		echo '<table class="tablemenu"><tr>
				<td class="tdmenu"><b><i>TIPPRUNDEN</i></b><br><a href="admin.php?m_id=1&action=0" class="menu">verwalten</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?m_id=1&action=1" class="menu">neu</a>'; echo '</td>
				<td class="tdmenu"><b><i>USER</i></b><br><a href="admin.php?m_id=2&action=0" class="menu">verwalten</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?m_id=2&action=1" class="menu">neu</a>'; echo '</td>
				<td class="tdmenu"><b><i>TUM</i></b><br><a href="admin.php?m_id=11&action=0" class="menu">verwalten</a></td>
				<td class="tdmenu"><b><i>SPIELE</i></b><br><a href="admin.php?m_id=3&action=0" class="menu">verwalten</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?m_id=3&action=1" class="menu">neu</a>'; echo '</td>
				<td class="tdmenu"><b><i>KOMMENTARE</i></b><br><a href="admin.php?m_id=4&action=0" class="menu">verwalten</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?m_id=4&action=1" class="menu">neu</a>'; echo '</td>
				 <td class="tdmenu"><b><i>BONUSPUNKTE</i></b><br><a href="admin.php?m_id=5&action=0" class="menu">verwalten</a></td>
				 <td class="tdmenu"><b><i>EINSTELLUNGEN</i></b><br><a href="admin.php?m_id=6&action=0" class="menu">verwalten</a></td>
				 <td class="tdmenu"><b><i>DATA-ID</i></b><br><a href="admin.php?m_id=7&action=0" class="menu">grp&tea</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?m_id=7&action=1" class="menu">import mat</a></td>
				 <td class="tdmenu"><b><i>MYSQL</i></b><br><a href="admin.php?m_id=8&action=0" class="menu">verwalten</a></td>
				 <td class="tdmenu"><b><i>Fehlende</i></b><br><a href="admin.php?m_id=9&action=0" class="menu">GRP</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?m_id=9&action=4" class="menu">MAT</a></td>
				 <td class="tdmenu"><b><i>Sonstiges</i></b><br><a href="admin.php?m_id=10&action=0" class="menu">test</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php" class="menu">zumTipp</a></td>

				</table><br><br>';
		
		if ($m_id == 1) // Tipprunden
		{
			if ($action == 0) //bearbeiten
			{
				$result=mysqli_query($dbconn, "SELECT tir_index, tir_name, tir_einsatz, tir_bezahlung, tir_usr_index, tir_winnercount, tir_scndtbl, tir_scndtbl_winnercount, tir_astrtipp, tir_gewinntext FROM tir_tipprunde");
				while($row=mysqli_fetch_array($result))
				{
					echo '
					<form action="admin_save.php?m_id='.$m_id.'&action='.$action.'" method="post">
						<input type="hidden" name="tir_index" value="'.$row['tir_index'].'"><br>
						Organisator: <select name="tir_usr_index">';
						$result_u=mysqli_query($dbconn, "SELECT usr_index, usr_name, usr_vornachname FROM usr_user");
						while($row_u=mysqli_fetch_array($result_u)) echo '<option value="'.$row_u['usr_index'].'" '.issel($row['tir_usr_index'], $row_u['usr_index']).'>'.$row_u['usr_name'].' ('.$row_u['usr_vornachname'].')</option>';
						echo '</select><br>
						Name der Tipprunde: <input type="text" name="tir_name" value="'.$row['tir_name'].'"><br>
						Einsatz: <input type="text" name="tir_einsatz" value="'.$row['tir_einsatz'].'"><br>
						Bezahlung: <input type="text" name="tir_bezahlung" value="'.$row['tir_bezahlung'].'"><br>
						Anzahl Gewinner: <input type="text" name="tir_winnercount" value="'.$row['tir_winnercount'].'"><br>
						LoserTabelle: <input type="text" name="tir_scndtbl" value="'.$row['tir_scndtbl'].'"><br>
						LoserTabelle Anzahl Gewinner: <input type="text" name="tir_scndtbl_winnercount" value="'.$row['tir_scndtbl_winnercount'].'"><br>
						GewinnerText: <input type="text" name="tir_gewinntext" value="'.$row['tir_gewinntext'].'"><br>
						L&ouml;schen: <input type="checkbox" name="chkdel" value="1"><br>
						<input type="submit" value="speichern">
					</form>';
				}
			}
			elseif ($action == 1) //neu
			{
				echo '
					<form action="admin_save.php?m_id='.$m_id.'&action='.$action.'" method="post"><br>
					<br>
					Name der Tipprunde: <input type="text" name="tir_name" value=""><br>
					admin index: <input type="text" name="tir_usr_index" value=""><br>
					Einsatz: <input type="text" name="tir_einsatz" value=""><br>
					Bezahlung: <input type="text" name="tir_bezahlung" value=""><br>
					Anzahl Gewinner: <input type="text" name="tir_winnercount" value=""><br>
					LoserTabelle: <input type="text" name="tir_scndtbl" value=""><br>
					LoserTabelle Anzahl Gewinner: <input type="text" name="tir_scndtbl_winnercount" value=""><br>
					GewinnerText: <input type="text" name="tir_gewinntext" value=""><br>
						<input type="submit" value="speichern">
				</form>';
			}
		}
		elseif ($m_id == 2) // User
		{
			if ($action == 0) //bearbeiten
			{
				echo '<table><tr><td>Benutzername</td><td>VorNachname</td><td>Email</td><td>Bezahlt</td><td>Neues Passwort</td><td>default Tipprunde</td><td>Gesperrt</td><td>Fehlg Logins</td><td>L&ouml;schen</td><td>speichern</td><td>nochmal</td></tr>';
						
				$result=mysqli_query($dbconn, "SELECT usr_index, usr_name, usr_vornachname, usr_email, usr_password, usr_hash, usr_locked, usr_loginfail, usr_tir_index__default, usr_bezahlt FROM usr_user order by usr_index asc");
				while($row=mysqli_fetch_array($result))
				{
					
					echo '
					<tr>
					<form action="admin_save.php?m_id='.$m_id.'&action='.$action.'" method="post">
						<input type="hidden" name="usr_index" value="'.$row['usr_index'].'">
						<td>'.$row['usr_index'].'<input type="text" name="usr_name" value="'.$row['usr_name'].'"></td>
						<td><input type="text" name="usr_vornachname" value="'.$row['usr_vornachname'].'"></td>
						<td><input type="text" name="usr_email" value="'.$row['usr_email'].'"></td>
						<td><input type="checkbox" value="1" name="usr_bezahlt" '.ischk($row['usr_bezahlt'], 1).'></td>
						<td><input type="text" name="usr_password" value=""></td>
						<td><select name="usr_tir_index__default">';
						$result_t=mysqli_query($dbconn, "SELECT tir_index, tir_name FROM tir_tipprunde order by tir_index");
						while($row_t=mysqli_fetch_array($result_t)) echo '<option value="'.$row_t['tir_index'].'" '.issel($row['usr_tir_index__default'], $row_t['tir_index']).'>'.$row_t['tir_name'].'</option>';
						echo '</select></td>
						<td><input type="text" name="usr_locked" value="'.$row['usr_locked'].'"></td>
						<td><input type="text" name="usr_loginfail" value="'.$row['usr_loginfail'].'"></td>
						<td><input type="checkbox" name="chkdel" value="1"></td>
						<td><input type="submit" value="speichern"></td>
						<td><a href="admin_save.php?m_id='.$m_id.'&action=2&usr='.$row['usr_index'].'">Email nochmal</a></td>
					</form>
					</tr>';
				}
				
				echo '</table>';
			}
			elseif ($action == 1) //neu
			{
				echo '
				<form action="admin_save.php?m_id='.$m_id.'&action='.$action.'" method="post">
					Tipprunde: <select name="usr_tir_index__default">';
						$result_t=mysqli_query($dbconn, "SELECT tir_index, tir_name FROM tir_tipprunde");
						while($row_t=mysqli_fetch_array($result_t)) echo '<option value="'.$row_t['tir_index'].'">'.$row_t['tir_name'].'</option>';
						echo '</select><br>
						BN Name: <input type="text" name="usr_name" value=""><br>
						VorNachname: <input type="text" name="usr_vornachname" value=""><br>
						Email: <input type="text" name="usr_email" value=""><br>
						Neues Passwort: <input type="text" name="usr_password" value=""><br>
						<input type="submit" value="speichern">
				</form>';
			}			
		}
		elseif ($m_id == 11) // TUM
		{
			if ($action == 0) //bearbeiten
			{
				echo '<table border=1><tr><td>Index</td><td>Benutzername</td><td>VorNachname</td><td>Email</td><td>Mapping</td><td>speichern</td></tr>';
						
				$result=mysqli_query($dbconn, "SELECT usr_index, usr_name, usr_vornachname, usr_email, usr_password, usr_hash, usr_locked, usr_loginfail, usr_tir_index__default, usr_bezahlt FROM usr_user order by usr_index asc");
				while($row=mysqli_fetch_array($result))
				{
					echo '
					<tr>
					<form action="admin_save.php?m_id='.$m_id.'&action='.$action.'" method="post">
						<input type="hidden" name="usr_index" value="'.$row['usr_index'].'">
						<td>'.$row['usr_index'].'</td>
						<td>'.$row['usr_name'].'</td>
						<td>'.$row['usr_vornachname'].'</td>
						<td>'.$row['usr_email'].'</td>
						<td>';
						$result_t=mysqli_query($dbconn, "SELECT tir_index, tir_name, urm_index FROM tir_tipprunde left join urm_usrtirmapping on urm_tir_index=tir_index and urm_usr_index=".$row['usr_index']." order by tir_index");
						while($row_t=mysqli_fetch_array($result_t)) 
						{
							$c = ''; if (!is_null($row_t['urm_index'])) $c = 'checked';
							echo '<input type="checkbox" name="chkurm_'.$row_t['tir_index'].'_'.$row['usr_index'].'" value="1" '.$c.'>'.$row_t['tir_name'].'<br>';
						}
						echo '
						</td>
						<td><input type="submit" value="speichern"></td>
					</form>
					</tr>';
				}
				
				echo '</table>';
			}			
		}
		elseif ($m_id == 3) // Spiele
		{
			if ($action == 0) //bearbeiten
			{
				echo '
				<table>
					<tr>
						<td>Gruppe</td>
						<td>Startzeit</td>
						<td>Team 1</td>
						<td>Team 2</td>
						<td>Erg 1</td>
						<td>Erg 2</td>
						<td>ErgText</td>
						<td>Status</td>
						<td>DataID</td>
						<td>L&ouml;schen</td>
						<td>speichern</td>
					</tr>
					';
				$orderby = "mat_order, mat_index";
				$result=mysqli_query($dbconn, "SELECT 
				mat_index, mat_grp_index, mat_tea_index_home, mat_tea_index_away, mat_result_home, mat_result_away, mat_result_string, mat_starttime, mat_status, mat_data_index, date_format( mat_starttime, '%Y%m%d%H:%i' ) AS mat_order,
				ta.tea_name AS tea_name_home,ta.tea_short AS tea_short_home, tb.tea_name AS tea_name_away, tb.tea_short AS tea_short_away, grp_short 
				FROM mat_match 
				INNER JOIN tea_team ta ON ta.tea_index = mat_tea_index_home 
				INNER JOIN tea_team tb ON tb.tea_index = mat_tea_index_away 
				INNER JOIN grp_group on grp_index=mat_grp_index 
				ORDER BY mat_order asc, mat_index");
				while($row=mysqli_fetch_array($result))
				{
					echo '<tr><form action="admin_save.php?m_id='.$m_id.'&action='.$action.'" method="post">
					<input type="hidden" name="mat_index" value="'.$row['mat_index'].'">
					<td><select name="mat_grp_index">';
					$result_g=mysqli_query($dbconn, "SELECT grp_index, grp_short FROM grp_group");
					while($row_g=mysqli_fetch_array($result_g)) echo '<option value="'.$row_g['grp_index'].'" '.issel($row_g['grp_index'], $row['mat_grp_index']).'>'.$row_g['grp_short'].'</option>';
					echo '</select></td>
					<td><input type="text" name="mat_starttime" value="'.$row['mat_starttime'].'"></td>
					<td><select name="mat_tea_index_home">';
					$result_g=mysqli_query($dbconn, "SELECT tea_index, tea_name FROM tea_team order by tea_name asc");
					while($row_g=mysqli_fetch_array($result_g)) echo '<option value="'.$row_g['tea_index'].'" '.issel($row_g['tea_index'], $row['mat_tea_index_home']).'>'.$row_g['tea_name'].'</option>';
					echo '</select></td>
					<td><select name="mat_tea_index_away">';
					$result_g=mysqli_query($dbconn, "SELECT tea_index, tea_name FROM tea_team order by tea_name asc");
					while($row_g=mysqli_fetch_array($result_g)) echo '<option value="'.$row_g['tea_index'].'" '.issel($row_g['tea_index'], $row['mat_tea_index_away']).'>'.$row_g['tea_name'].'</option>';
					echo '</select></td>
					<td><input type="text" name="mat_result_home" value="'.$row['mat_result_home'].'"></td>
					<td><input type="text" name="mat_result_away" value="'.$row['mat_result_away'].'"></td>
					<td><input type="text" name="mat_result_string" value="'.$row['mat_result_string'].'"></td>
					<td><select name="mat_status"><option value="0" '.issel(0, $row['mat_status']).'>noch nicht angefangen</option><option value="1" '.issel(1, $row['mat_status']).'>live</option><option value="2" '.issel(2, $row['mat_status']).'>beendet</option></select></td>
					<td><input type="text" name="mat_data_index" value="'.$row['mat_data_index'].'"></td>
					<td><input type="checkbox" name="chkdel" value="1"></td>
					<td><input type="submit" value="speichern"></td>
					</form>
					</tr>';
				}
				echo '
				</table>';
			}
			elseif ($action == 1) //neu
			{
				echo 'Format Startzeit: 2018-06-01 14:00:00<br>
				<table>
					<tr>
						<td>Gruppe</td>
						<td>Startzeit</td>
						<td>Team 1</td>
						<td>Team 2</td>
						<td>Erg 1</td>
						<td>Erg 2</td>
						<td>ErgText</td>
						<td>Status</td>
						<td>DataID</td>
						<td>speichern</td>
					</tr>
					<tr>
						<form action="admin_save.php?m_id='.$m_id.'&action='.$action.'" method="post">
						<td><select name="mat_grp_index">';
						$result_g=mysqli_query($dbconn, "SELECT grp_index, grp_short FROM grp_group");
						while($row_g=mysqli_fetch_array($result_g)) echo '<option value="'.$row_g['grp_index'].'">'.$row_g['grp_short'].'</option>';
						echo '</select></td>
						<td><input type="text" name="mat_starttime" value=""></td>
						<td><select name="mat_tea_index_home">';
						$result_g=mysqli_query($dbconn, "SELECT tea_index, tea_name FROM tea_team order by tea_name asc");
						while($row_g=mysqli_fetch_array($result_g)) echo '<option value="'.$row_g['tea_index'].'">'.$row_g['tea_name'].'</option>';
						echo '</select></td>
						<td><select name="mat_tea_index_away">';
						$result_g=mysqli_query($dbconn, "SELECT tea_index, tea_name FROM tea_team order by tea_name asc");
						while($row_g=mysqli_fetch_array($result_g)) echo '<option value="'.$row_g['tea_index'].'">'.$row_g['tea_name'].'</option>';
						echo '</select></td>
						<td><input type="text" name="mat_result_home" value=""></td>
						<td><input type="text" name="mat_result_away" value=""></td>
						<td><input type="text" name="mat_result_string" value=""></td>
						<td><select name="mat_status"><option value="0">noch nicht angefangen</option><option value="1">live</option><option value="2">beendet</option></select></td>
						<td><input type="text" name="mat_data_index" value=""></td>
						<td><input type="submit" value="speichern"></td>
						</form>
					</tr>
				</table>';
			}			
		}
		elseif ($m_id == 4) // KOMMENTARE
		{
			if ($action == 0) //bearbeiten
			{
				$tir_name = "";
				$result=mysqli_query($dbconn, "SELECT com_index, com_usr_index, com_timestamp, com_text, com_color, usr_name FROM com_comment inner join usr_user on usr_index=com_usr_index order by com_index desc");
				while($row=mysqli_fetch_array($result))
				{
					if ($tir_name != $row['tir_name'])
					{
						echo '<br><hr><br><h1>'.$row['tir_name'].'</h1><br>';
						$tir_name = $row['tir_name'];
					}
					echo '
					<form action="admin_save.php?m_id='.$m_id.'&action='.$action.'" method="post">
						<input type="hidden" name="com_index" value="'.$row['com_index'].'">
						'.$row['usr_name'].' '.$row['com_timestamp'].'<br>
						<textarea col=80 rows=5 name="com_text">'.$row['com_text'].'</textarea>
						L&ouml;schen: <input type="checkbox" name="chkdel" value="1"><br>
						<input type="submit" value="speichern"><br>
						---------------------
						<br>
					</form>';
				}
			}
			elseif ($action == 1) //neu
			{
				echo '
				<form action="admin_save.php?m_id='.$m_id.'&action='.$action.'" method="post">
				<textarea name="editor1" id="editor1"></textarea><br>
				Bild hochladen: <input type="file" name="comimg" accept=".jpg, .jpeg, .gif, .png, .bmp"><br><br>
				<input type="submit" class="small" value="senden">
				</form>';
			}			
		}
		elseif ($m_id == 5) // STI
		{
			if ($action == 0) //bearbeiten
			{
				echo'
					<form action="admin_save.php?m_id='.$m_id.'&action='.$action.'" method="post">';
				$result = mysqli_query ($dbconn, "select sti_index, sti_koenig from sti_spezialtipp");
				while ($row = mysqli_fetch_array($result))
				{
					echo $row['sti_index'].", ".$row['sti_koenig']."<br>";
				}
				
				echo '<hr><br>
				Meister: <select name="sti_meister">';
				$result_g=mysqli_query($dbconn, "SELECT tea_index, tea_name FROM tea_team");
				while($row_g=mysqli_fetch_array($result_g)) echo '<option value="'.$row_g['tea_index'].'">'.$row_g['tea_name'].'</option>';
				echo '</select><br>
				Torschützenkönig: <input type="text" name="sti_koenig" value="">
				<br><br><input type="submit" value="speichern"></form><br><br>';
			}
		}
		elseif ($m_id == 6) // SETTINGS
		{
			if ($action == 0) //bearbeiten
			{
				echo'
					<form action="admin_save.php?m_id='.$m_id.'&action='.$action.'" method="post">
					<table>
					<tr><td class="borderonly b">Referenz</td><td class="borderonly b">Text</td></tr>';
				$result = mysqli_query ($dbconn, "select set_index, set_key, set_value from set_setting");
				while ($row = mysqli_fetch_array($result))
				{	
					$value = stripslashes($row['set_value']);	
					echo '<tr>
							<td class="borderonly">'.$row['set_key'].'</td>
							<td class="borderonly"><textarea name="set_value_'.$row['set_index'].'" rows=10 cols=80>'.$row['set_value'].'</textarea></td>							
						  </tr>';
				}
				echo '</table><br><input type="submit" value="speichern"></form><br><br>';
			}
		}
		elseif ($m_id == 7) // DATAID
		{
			if ($action == 0) //grp,tea
			{
				echo 'GRUPPEN<br><table>';
				$result = mysqli_query ($dbconn, "select grp_index, grp_data_index, grp_name from grp_group");
				while ($row = mysqli_fetch_array($result))
				{
					echo '<tr>
							<form action="admin_save.php?m_id='.$m_id.'&action='.$action.'" method="post">
								<input type="hidden" name="grp_index" value="'.$row['grp_index'].'">
								<td class="">'.$row['grp_name'].'</td>
								<td class=""><input type="text" name="grp_data_index" value="'.$row['grp_data_index'].'"></td>
								<td class=""><input type="submit" value="speichern"></td>
							</form>	
						</tr>';
				}
				echo '<br><hr><br>TEAMS<br><table>';
				$result = mysqli_query ($dbconn, "select tea_index, tea_data_index, tea_name from tea_team");
				while ($row = mysqli_fetch_array($result))
				{
					echo '<tr>
							<form action="admin_save.php?m_id='.$m_id.'&action='.$action.'" method="post">
								<input type="hidden" name="tea_index" value="'.$row['tea_index'].'">
								<td class="">'.$row['tea_name'].'</td>
								<td class=""><input type="text" name="tea_data_index" value="'.$row['tea_data_index'].'"></td>
								<td class=""><input type="submit" value="speichern"></td>
							</form>	
						</tr>';
				}
			}
			if ($action == 1) //getmat
			{
				echo '<form action="admin_save.php?m_id='.$m_id.'&action='.$action.'" method="post">
						<select name="grp_index">
							<option value="100">Vorrunde</option>';
				$result = mysqli_query ($dbconn, "select grp_index, grp_name from grp_group where grp_vorrunde=0");
				while ($row = mysqli_fetch_array($result))	
				echo '<option value="'.$row['grp_index'].'">'.$row['grp_name'].'</option>';
				
				echo '<select><br><input type="submit" value="speichern"></form>';
			}
		}		
		elseif ($m_id == 8) // MYSQL
		{
			if ($action == 0) 
			{
				echo '<form action="admin.php?m_id='.$m_id.'&action=1" method="post">
						<textarea name="sql" cols=120 rows=30></textarea><br>
						<input type="submit" value="speichern">
					  </form>';
			}
			
			if ($action == 1) 
			{
				$result = null;
				$post = str_replace('\'','"', $_POST['sql']);
				$result = mysqli_query($dbconn, $post);
					
				if ($result === true)
				{
					echo "erfolgreich ausgefuerht";
				}
				elseif($result === false)
				{
					echo "FEHLER";
				}
				else
				{
					//echo var_dump($result);
					while ($row = mysqli_fetch_array($result))
					{
						for ($i=0; $i<mysqli_num_fields($result); $i++)
						{
							echo $row[$i]."&nbsp;&nbsp;&nbsp;";
						}
						echo "<br>";
					}
				}
			}
		}	
		elseif ($m_id == 9) // Fehlende
		{
			if ($action == 0) 
			{
				
				$send = 0; if (isset($_REQUEST['s'])) $send = $_REQUEST['s'] ;
				
				$result = mysqli_query ($dbconn, "select usr_index, usr_vornachname,usr_email from usr_user");
				while ($row = mysqli_fetch_array($result))
				{
					$sf = 32;
					$af = 16;
					$vf = 8;
					$hf = 4;
					$f = 2;
					$wm = 1;
					$tk = 0;
					$result_gti = mysqli_query ($dbconn, "select gti_index, gti_grp_index from gti_grouptipp where gti_usr_index=".$row['usr_index']);
					while ($row_gti = mysqli_fetch_array($result_gti))
					{
						if ($row_gti["gti_grp_index"] == 13 ) //SF
							$sf --;
						if ($row_gti["gti_grp_index"] == 14 ) //AF
							$af --;
						elseif ($row_gti["gti_grp_index"] == 15 ) //vF
							$vf --;
						elseif ($row_gti["gti_grp_index"] == 16 ) //hF
							$hf --;
						elseif ($row_gti["gti_grp_index"] == 18 ) //F
							$f --;
					}		
					$result_sti = mysqli_query ($dbconn, "select sti_meister, sti_koenig  from sti_spezialtipp where sti_usr_index=".$row['usr_index']);
					while ($row_sti = mysqli_fetch_array($result_sti))
					{
						if (($row_sti['sti_meister'] > 0))
							$wm --;
					}
					
					$fehlt = "";
					if ($sf > 0) $fehlt = $fehlt.$sf." Sechzehntelfinalisten <br> ";
					if ($af > 0) $fehlt = $fehlt.$af." Achtelfinalisten <br> ";
					if ($vf > 0) $fehlt = $fehlt.$vf." Viertelfinalisten <br>";
					if ($hf > 0) $fehlt = $fehlt.$hf." Halbfinalisten <br> ";
					if ($f > 0) $fehlt = $fehlt.$f." Finalisten <br> ";
					if ($wm > 0) $fehlt = $fehlt.$wm." Weltmeister <br> ";
					if ($tk > 0) $fehlt = $fehlt.$tk." Torschuetzenkoenig <br> ";
					
					if (strlen($fehlt) > 0)		
					{
						$fehlt = "Es fehlen folgende Tippps: <br> ".$fehlt;
						echo '<form action="admin.php?m_id='.$m_id.'&action=1" method="post">'.$row['usr_vornachname'].' ('.$fehlt.')
								<input type="hidden" value="'.$fehlt.'" name="fehlt">
								<input type="hidden" value="'.$row['usr_index'].'" name="usr_index">
								<input type="submit" value="Email schreiben">
							</form>	
								<br>';
								
								if ($send == 1)
								{
									$msg = "Du hast noch nicht alle Gruppentipps abgegeben ! <br> ";
									$msg = $msg."(Also wer ins Achtelfinale kommt, Wer ins Viertelfinale kommt usw.) <br><br> ";
									$msg = $msg."Diese Tipps sind nur bis zum Anpfiff des erstens Spiels aenderbar (das ist heute abend) !!! <br><br> ";
									$msg = $msg.$fehlt;
									sendmail($row['usr_index'], $dbconn, 2, $msg);
								}
					}
					else
					{ echo ' '.$row['usr_vornachname'].'<br>';}
				}
				echo "<br><hr><br><a href='admin.php?m_id=".$m_id."&action=2'>generiere automatische tipps</a>";
				echo "<br><hr><br><a href='admin.php?m_id=".$m_id."&action=3'>sende bezahlungserinnerung</a>";
			}
			elseif ($action == 1) 
			{
				$usr_index = $_POST['usr_index'];
				if ($usr_index > 0)
				{
					$msg = "Du hast noch nicht alle Gruppentipps abgegeben ! <br> ";
					$msg = $msg."(Also wer ins Achtelfinale kommt, Wer ins Viertelfinale kommt usw.) <br> ";
					$msg = $msg."Diese Tipps sind nur bis zum Anpfiff des erstens Spiels aenderbar!!! (das ist heute abend)<br> <br> ";
					$msg = $msg.$_POST['fehlt'];
					//echo $msg;
					sendmail($usr_index, $dbconn, 2, $msg);
				}
			}
			elseif ($action == 2) 
			{
				generiere_autogti($dbconn);
				echo "autotipps wurden generiert. bitte neu laden";
			}
			elseif ($action == 3) 
			{
				$result = mysqli_query ($dbconn, "select usr_index from usr_user where usr_bezahlt=0");
				while ($row = mysqli_fetch_array($result))
				{
					$msg = 'Hi.<br><br>Du hast den Tippeinsatz noch nicht bezahlt.<br><br>
Bitte per paypal an:<br> '.env('PAYMENT_PAYPAL', 'paypal@example.com').'<br><br>
Oder per Überweisung auf:<br>IBAN '.env('PAYMENT_IBAN', 'DE00 0000 0000 0000 0000 00').'<br>BIC '.env('PAYMENT_BIC', 'XXXXXXXXXXX').' <br><br>
WICHTIG: Das hier ist eine automatische Email!<br>Falls du bereits gezahlt hast hab ich es vergessen einzutragen - sag mir bitte nochmal Bescheid.<br>
Falls wir bereits ausgemacht haben dass du es mir in Bar gibst weil du so neumoderem Zeug wie Paypal oder Überweisung nicht vertraust o.ä. - dann einfach ignorieren.<br><br>
Gruß';
					sendmail($row["usr_index"], $dbconn, 3, $msg);
				
				}	
			}
			elseif ($action == 4)
			{
				echo "Fehlenden Tipp für ein Match nachtragen<br>
					<table border=1>
						<form action='admin_save.php?m_id=9&action=4' method='post'>
						<tr>
							<td>Nutzer</td>
							<td><select name='usr_index'>";
							$result=mysqli_query($dbconn,"select usr_index, usr_name, usr_email, usr_vornachname from usr_user order by usr_vornachname asc");
							while ($row = mysqli_fetch_array($result)) echo "<option value='".$row["usr_index"]."'>".$row["usr_vornachname"]." | ".$row["usr_name"]." | ".$row["usr_email"]."</option>";
							echo "</select></td>
						</tr>
						<tr>
							<td>Match</td>
							<td><select name='mat_index'>";
							$result=mysqli_query($dbconn,"SELECT mat_index, mat_starttime, mat_tea_index_home, mat_tea_index_away, t1.tea_name as tea_name_h, t2.tea_name as tea_name_a FROM mat_match inner join tea_team t1 on t1.tea_index=mat_tea_index_home inner join tea_team t2 on t2.tea_index=mat_tea_index_away ORDER By mat_index asc");
							while ($row = mysqli_fetch_array($result)) echo "<option value='".$row["mat_index"]."'>".$row["mat_starttime"]." | ".$row["tea_name_h"]." | ".$row["tea_name_a"]."</option>";
							echo "</select></td>
						</tr>
						<tr>
							<td>Tore Heim</td>
							<td><input type='text' name='tore_h' value='0'></td>
						</tr>
						<tr>
							<td>Tore Auswärts</td>
							<td><input type='text' name='tore_a' value='0'></td>
						</tr>
						<tr>
							<td>Bewertet</td>
							<td><select name='bewertet'><option value='0'>Nein</option><option value='2'>Ja</option></select></td>
						</tr>
						<tr>
							<td>Punkte T-E-A</td>
							<td><input type='text' name='points' value='0-0-0'></td>
						</tr>
						<tr>
							<td colspan=2><input type='submit' value='speichern'></td>
						</tr>
						</form>
					</table>";
			}
		}
		elseif ($m_id == 10) // Test
		{
		}
		?>
	</body>
</html>