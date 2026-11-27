<?php
	session_start ();
	include 'functions.php';
	$dbconn = sqlconnect();
	
	$m_id = 1; 	if (isset($_REQUEST["m_id"])) $m_id = $_REQUEST["m_id"];
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
	


		if ($m_id == 1) // Tipprunden
		{
			if ($action == 0) //bearbeiten
			{
				$chkdel = 0; if (isset($_POST["chkdel"])) $chkdel = $_POST["chkdel"];
				$tir_index = 0; if (isset($_POST["tir_index"])) $tir_index = $_POST["tir_index"];
				if ($chkdel > 0)
				{
					mysqli_query($dbconn, 'DELETE FROM tir_tipprunde WHERE tir_index="'.$tir_index.'"'); 
				}
				else
				{	
					if ($tir_index > 0)
					{
						mysqli_query($dbconn, 'UPDATE tir_tipprunde 
											   SET
												tir_name="'.$_POST['tir_name'].'",
												tir_einsatz="'.$_POST['tir_einsatz'].'",
												tir_bezahlung="'.$_POST['tir_bezahlung'].'",
												tir_usr_index="'.$_POST['tir_usr_index'].'",
												tir_winnercount="'.$_POST['tir_winnercount'].'",
												tir_scndtbl="'.$_POST['tir_scndtbl'].'",
												tir_scndtbl_winnercount="'.$_POST['tir_scndtbl_winnercount'].'",
												tir_gewinntext="'.$_POST['tir_gewinntext'].'"
											   WHERE
												tir_index="'.$tir_index.'"');
					}
				}	
			}
			elseif ($action == 1) //neu
			{
				
				mysqli_query($dbconn, 'INSERT INTO tir_tipprunde 
												(tir_name, tir_einsatz, tir_bezahlung, tir_usr_index, tir_winnercount, tir_scndtbl, tir_scndtbl_winnercount, tir_astrtipp, tir_gewinntext)
											   VALUES
											    ("'.$_POST['tir_name'].'", "'.$_POST['tir_einsatz'].'", "'.$_POST['tir_bezahlung'].'","'.$_POST['tir_usr_index'].'",'.$_POST['tir_winnercount'].','.$_POST['tir_scndtbl'].','.$_POST['tir_scndtbl_winnercount'].', 0, "'.$_POST['tir_gewinntext'].'")');
				$tir = mysqli_insert_id($dbconn);
				
			}
		}
		elseif ($m_id == 2) // User
		{
			if ($action == 0) //bearbeiten
			{
				$chkdel = 0; if (isset($_POST["chkdel"])) $chkdel = $_POST["chkdel"];
				$usr_index = 0; if (isset($_POST["usr_index"])) $usr_index = $_POST["usr_index"];
				if ($chkdel > 0)
				{
					mysqli_query($dbconn, 'DELETE FROM com_comment WHERE com_usr_index="'.$usr_index.'"'); 
					mysqli_query($dbconn, 'DELETE FROM tip_tipp WHERE tip_usr_index="'.$usr_index.'"'); 
					mysqli_query($dbconn, 'DELETE FROM gti_grouptipp WHERE gti_usr_index="'.$usr_index.'"'); 
					mysqli_query($dbconn, 'DELETE FROM sti_spezialtipp WHERE sti_usr_index="'.$usr_index.'"'); 
					mysqli_query($dbconn, 'DELETE FROM usr_user WHERE usr_index="'.$usr_index.'"'); 
				}
				else
				{	
					if ($usr_index > 0)
					{
						$usr_bezahlt = 0; if (isset($_POST["usr_bezahlt"])) $usr_bezahlt = $_POST["usr_bezahlt"];
						mysqli_query($dbconn, 'UPDATE usr_user 
											   SET
												usr_name="'.$_POST['usr_name'].'",
												usr_vornachname="'.$_POST['usr_vornachname'].'",
												usr_email="'.$_POST['usr_email'].'",
												usr_locked="'.$_POST['usr_locked'].'",
												usr_loginfail="'.$_POST['usr_loginfail'].'",
												usr_tir_index__default="'.$_POST['usr_tir_index__default'].'",
												usr_bezahlt = '.$usr_bezahlt.'
											   WHERE
												usr_index="'.$usr_index.'"');
												
						if (strlen(trim($_POST['usr_password'])) > 0)	
							mysqli_query($dbconn, 'UPDATE usr_user SET usr_password="'.md5($_POST['usr_password']).'" WHERE usr_index="'.$usr_index.'"');							
					}
				}	
			}
			elseif ($action == 1) //neu
			{
				$hash = md5($_POST['usr_name'].$_POST['usr_email'].$_POST['usr_password']);
				mysqli_query($dbconn, 'INSERT INTO usr_user 
											(usr_name, usr_vornachname, usr_email, usr_password, usr_hash, usr_locked, usr_loginfail, usr_tir_index__default, usr_bezahlt)
										   VALUES
											("'.$_POST['usr_name'].'", "'.$_POST['usr_vornachname'].'", "'.$_POST['usr_email'].'", "'.md5($_POST['usr_password']).'", "'.$hash.'",0,0, '.$_POST['usr_tir_index__default'].', 0)');
			}	
			elseif ($action == 2) //email nochmal
			{
				$usr_index = 0; if (isset($_REQUEST["usr"])) $usr_index = $_REQUEST["usr"];	
				if ($usr_index > 0)
				{
					sendmail($usr_index, $dbconn, 0, "");
					$action=0;
				}
			}
		}
		elseif ($m_id == 11) // TUM
		{
			if ($action == 0) //bearbeiten
			{
				$result_t=mysqli_query($dbconn, "SELECT tir_index, tir_name, urm_index FROM tir_tipprunde left join urm_usrtirmapping on urm_tir_index=tir_index and urm_usr_index=".$_POST['usr_index']." order by tir_index");
				while($row_t=mysqli_fetch_array($result_t)) 
				{
					//war er checked ?
					$c = 0;  if (isset($_POST['chkurm_'.$row_t['tir_index'].'_'.$_POST['usr_index']])) $c = $_POST['chkurm_'.$row_t['tir_index'].'_'.$_POST['usr_index']];
					if ($c == 1)
					{
						$result_t2=mysqli_query($dbconn, "SELECT urm_index from urm_usrtirmapping where urm_tir_index=".$row_t['tir_index']." and urm_usr_index=".$_POST['usr_index']);
						if (mysqli_num_rows($result_t2) == 0)
						
						mysqli_query($dbconn, "INSERT INTO urm_usrtirmapping(urm_usr_index, urm_tir_index) VALUES (".$_POST['usr_index'].",".$row_t['tir_index'].")");
					}
					else
					{
						
						$result_t2=mysqli_query($dbconn, "SELECT urm_index from urm_usrtirmapping where urm_tir_index=".$row_t['tir_index']." and urm_usr_index=".$_POST['usr_index']);
						if (mysqli_num_rows($result_t2) > 0)	
						mysqli_query($dbconn, "DELETE FROM urm_usrtirmapping WHERE urm_usr_index = ".$_POST['usr_index']." and urm_tir_index=".$row_t['tir_index']);
					}
				}	
			}
		}
		elseif ($m_id == 3) // Spiele
		{
			if ($action == 0) //bearbeiten
			{
				$chkdel = 0; if (isset($_POST["chkdel"])) $chkdel = $_POST["chkdel"];
				$mat_index = 0; if (isset($_POST["mat_index"])) $mat_index = $_POST["mat_index"];
				if ($chkdel > 0)
				{
					mysqli_query($dbconn, 'UPDATE mat_match set mat_status=0 where mat_index='.$mat_index); 
					bewerte_match($dbconn, $mat_index);
					
					$result_m=mysqli_query($dbconn, "select grp_vorrunde, mat_grp_index, mat_tea_index_home, mat_tea_index_away from mat_match inner join grp_group on grp_index=mat_grp_index where mat_index=".$mat_index);
					while ($row_m = mysqli_fetch_array($result_m))
					{
						if ($row_m['grp_vorrunde'] == 0)
						{
							$result_t=mysqli_query($dbconn, "select gti_index from gti_grouptipp where gti_grp_index=".$row_m['mat_grp_index']." and gti_tea_index=".$row_m['mat_tea_index_home']);
							while ($row_t = mysqli_fetch_array($result_t))
							{
								mysqli_query($dbconn, "update gti_grouptipp set gti_points=0 where gti_index=".$row_t['gti_index']);
							}
							$result_t=mysqli_query($dbconn, "select gti_index from gti_grouptipp where gti_grp_index=".$row_m['mat_grp_index']." and gti_tea_index=".$row_m['mat_tea_index_away']);
							while ($row_t = mysqli_fetch_array($result_t))
							{
								mysqli_query($dbconn, "update gti_grouptipp set gti_points=0 where gti_index=".$row_t['gti_index']);
							}
						}
					}
					mysqli_query($dbconn, 'DELETE FROM mat_match WHERE mat_index="'.$mat_index.'"'); 
					
				}
				else
				{
					mysqli_query($dbconn, 'UPDATE mat_match 
											   SET
												mat_grp_index="'.$_POST['mat_grp_index'].'",
												mat_starttime="'.$_POST['mat_starttime'].'",
												mat_tea_index_home="'.$_POST['mat_tea_index_home'].'",
												mat_tea_index_away="'.$_POST['mat_tea_index_away'].'",
												mat_result_home="'.$_POST['mat_result_home'].'",
												mat_result_away="'.$_POST['mat_result_away'].'",
												mat_result_string="'.$_POST['mat_result_string'].'",
												mat_status="'.$_POST['mat_status'].'",
												mat_data_index="'.$_POST['mat_data_index'].'"
											   WHERE
												mat_index="'.$mat_index.'"');
												
					
					generiere_autotipps($dbconn, $mat_index);							
					
					bewerte_match($dbconn, $mat_index);							
				}
			}
			elseif ($action == 1) //neu
			{
				$mat_grp_index = (int)$_POST['mat_grp_index'];
				$mat_starttime = mysqli_real_escape_string($dbconn, $_POST['mat_starttime']);
				$mat_tea_index_home = (int)$_POST['mat_tea_index_home'];
				$mat_tea_index_away = (int)$_POST['mat_tea_index_away'];
				$mat_result_home = ($_POST['mat_result_home'] === '') ? 0 : (int)$_POST['mat_result_home'];
				$mat_result_away = ($_POST['mat_result_away'] === '') ? 0 : (int)$_POST['mat_result_away'];
				$mat_result_string = mysqli_real_escape_string($dbconn, $_POST['mat_result_string']);
				$mat_status = (int)$_POST['mat_status'];
				$mat_data_index = ($_POST['mat_data_index'] === '') ? 0 : (int)$_POST['mat_data_index'];

				$inserted = mysqli_query($dbconn, 'INSERT INTO mat_match
											(mat_grp_index, mat_starttime,mat_tea_index_home,mat_tea_index_away,mat_result_home,mat_result_away,mat_result_string,mat_status,mat_data_index)
										VALUES
											("'.$mat_grp_index.'", "'.$mat_starttime.'", "'.$mat_tea_index_home.'", "'.$mat_tea_index_away.'",
											"'.$mat_result_home.'", "'.$mat_result_away.'", "'.$mat_result_string.'", "'.$mat_status.'", "'.$mat_data_index.'")');
				if ($inserted) bewerte_match($dbconn, mysqli_insert_id($dbconn));

				//lege auch auf api server an
			/*	$dbconn2 = mysqli_connect ("192.0.2.1", "IhrName", "IhrPasswort");
				if (mysqli_select_db($dbconn2, "tipp"))
				{
					mysqli_query($dbconn2, 'INSERT INTO mat_match
											(mat_grp_index, mat_starttime,mat_tea_index_home,mat_tea_index_away,mat_result_home,mat_result_away,mat_result_string,mat_status,mat_data_index)
										VALUES
											("'.$_POST['mat_grp_index'].'", "'.$_POST['mat_starttime'].'", "'.$_POST['mat_tea_index_home'].'", "'.$_POST['mat_tea_index_away'].'",
											"'.$_POST['mat_result_home'].'", "'.$_POST['mat_result_away'].'", "'.$_POST['mat_result_string'].'", "'.$_POST['mat_status'].'", "'.$_POST['mat_data_index'].'")');
				}*/


			}			
		}
		elseif ($m_id == 4) // KOMMENTARE
		{
			if ($action == 0) //bearbeiten
			{
				$chkdel = 0; if (isset($_POST["chkdel"])) $chkdel = $_POST["chkdel"];
				$com_index = 0; if (isset($_POST["com_index"])) $com_index = $_POST["com_index"];
				if ($chkdel > 0)
				{
					mysqli_query($dbconn, 'DELETE FROM com_comment WHERE com_index="'.$com_index.'"'); 
				}
				else
				{	
					if ($com_index > 0)
					{
						mysqli_query($dbconn, 'UPDATE com_comment 
											   SET
												com_text="'.$_POST['com_text'].'"
											   WHERE
												com_index="'.$com_index.'"');
																			
					}
				}	
			}
			elseif ($action == 1) //neu
			{
				$kommentar = konform($_POST['editor1']);
				$bild = "";
				if ( isset( $_FILES['comimg'] ) && !empty($_FILES['comimg']['name'] ) )
				{	
					$bild = uploadfile($_FILES['comimg'], $usr_index);
				}
				$color = rand(0,9);
				mysqli_query($dbconn, "insert into com_comment (com_usr_index, com_text, com_color, com_media) VALUES (".$usr_index.", '".$kommentar."', ".$color.", '".$bild."')");
				
			}
		}
		elseif ($m_id == 5) // STI
		{
			if ($action == 0) //bearbeiten
			{
				//lösche alte tipps
				mysqli_query($dbconn, 'UPDATE sti_spezialtipp set sti_evaluated=0, sti_points=0'); 
				
				//update schreibweise könig
			/*	$result = mysqli_query ($dbconn, "select sti_index, sti_koenig from sti_spezialtipp");
				while ($row = mysqli_fetch_array($result))
				{
					mysqli_query ($dbconn, 'update sti_spezialtipp set sti_koenig ="'.$_POST['sti_koenig_'.$row['sti_index']].'" where sti_index = "'.$row['sti_index'].'"');
				}*/
				
				mysqli_query ($dbconn, 'update sti_spezialtipp set sti_points=5 where sti_koenig like "%'.$_POST['sti_koenig'].'%"');
				
				//bewerte tipp
				$result = mysqli_query ($dbconn, "select sti_index, sti_meister, sti_koenig, sti_points from sti_spezialtipp");
				while ($row = mysqli_fetch_array($result))
				{
					$points = $row['sti_points'];
					//if ($_POST['sti_koenig_'.$row['sti_index']] == $row['sti_koenig']) $points = $points + 5;
					if ($_POST['sti_meister'] == $row['sti_meister']) $points = $points + 5;
					mysqli_query ($dbconn, 'update sti_spezialtipp set sti_points ='.$points.', sti_evaluated=1 where sti_index = "'.$row['sti_index'].'"');
					echo  'update sti_spezialtipp set sti_points ='.$points.', sti_evaluated=1 where sti_index = "'.$row['sti_index'].'"';
				}	
			}
		}
		elseif ($m_id == 6) // SETTINGS
		{
			if ($action == 0) //bearbeiten
			{
				$result = mysqli_query ($dbconn, "select set_index, set_key, set_value from set_setting");
				while ($row = mysqli_fetch_array($result))
				{
					mysqli_query ($dbconn, 'update set_setting set set_value ="'.$_POST['set_value_'.$row['set_index']].'" where set_index = "'.$row['set_index'].'"');
				}
			}	
		}
		elseif ($m_id == 7) // DATAID
		{
			if ($action == 0) //grp, tea manuell
			{
				$grp_index = 0; if (isset($_POST["grp_index"])) $grp_index = $_POST["grp_index"];
				$tea_index = 0; if (isset($_POST["tea_index"])) $tea_index = $_POST["tea_index"];
				
				if ($grp_index > 0) mysqli_query ($dbconn, 'update grp_group set grp_data_index ="'.$_POST['grp_data_index'].'" where grp_index = "'.$grp_index.'"');
				if ($tea_index > 0) mysqli_query ($dbconn, 'update tea_team  set tea_data_index ="'.$_POST['tea_data_index'].'" where tea_index = "'.$tea_index.'"');
			}	
			if ($action == 1) //mat import
			{
				$grp_index = 0; if (isset($_POST["grp_index"])) $grp_index = $_POST["grp_index"];
				$arg = array();
				if ($grp_index = 100)
				{
					$result = mysqli_query ($dbconn, "select grp_index from grp_group where grp_vorrunde=1");
					while ($row = mysqli_fetch_array($result)) $arg[] = $row['grp_index'];
				}
				else
					$arg[] = $grp_index;
				
				getmatchids($dbconn, $arg);
			}
			
		}
		elseif ($m_id == 9) // MYSQL
		{
			if ($action == 4)
			{
				$usr_index = $_POST['usr_index'];
				$mat_index = $_POST['mat_index'];
				$tore_h = $_POST['tore_h'];
				$tore_a = $_POST['tore_a'];
				$bewertet = $_POST['bewertet'];
				$points = trim($_POST['points']);
				
				$points_e = explode('-',$points)[1];
				$points_t = explode('-',$points)[0];
				$points_a = explode('-',$points)[2];
				
				$result = mysqli_query ($dbconn, "select tip_index from tip_tipp where tip_usr_index=".$usr_index." and tip_mat_index=".$mat_index);
				while ($row = mysqli_fetch_array($result))  mysqli_query ($dbconn, "DELETE FROM tip_tipp where tip_index=".$row['tip_index']);
				
				mysqli_query ($dbconn, "INSERT INTO tip_tipp (tip_mat_index, tip_usr_index, tip_result_home, tip_result_away, tip_points_erg, tip_points_ten, tip_points_aus, tip_evaluated)
				VALUES (".$mat_index.", ".$usr_index.", ".$tore_h.", ".$tore_a.", ".$points_t.", ".$points_e.", ".$points_a.", ".$bewertet.")");
						
			}
		}
		elseif ($m_id == 10)
		{
				$tip_index = 0; if (isset($_REQUEST["tip_id"])) $tip_index = $_REQUEST["tip_id"];
				if ($tip_index > 0)
				{
					 mysqli_query ($dbconn, "delete from tip_tipp where tip_index=".$tip_index);
				}
		}
	
	header ("Location: admin.php?m_id=".$m_id."&action=".$action);
	//echo "<br><a href='admin.php?m_id=".$m_id."'>weiter</a><br>";
?>
