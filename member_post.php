<?php
	session_start ();
	include 'functions.php';
	$dbconn = sqlconnect();
	
	$m_id = 0; 	if (isset($_REQUEST["m_id"])) $m_id = $_REQUEST["m_id"];
	
	$usr_index = 0;
	$tir_index = 0;
	
	$era = ""; if ((isEndrunde($dbconn)) && ($m_id==2)) $era = "#era"; 
	
	if (!isset ($_SESSION["user_id_tipp"])) 
	{
		header("Location: index.php");
	}
	else
	{
		$usr_index = $_SESSION["user_id_tipp"];
		$tir_index = $_SESSION["tir_id_tipp"];
	}
	
	if ($m_id == 2) // Mein Tipp
	{
		//speichere Spiele
		$result=mysqli_query($dbconn, "SELECT mat_index, mat_starttime FROM mat_match WHERE mat_status=0 AND mat_starttime>NOW()");
		while($row=mysqli_fetch_array($result))
		{
			$notipp = false;
			$tip_result_home = isset($_POST["tip_result_home".$row['mat_index']]) ? $_POST["tip_result_home".$row['mat_index']] : "";
			$tip_result_away = isset($_POST["tip_result_away".$row['mat_index']]) ? $_POST["tip_result_away".$row['mat_index']] : "";
			if ((strlen($tip_result_home) == 0) || !is_numeric($tip_result_home)) $notipp = true;
			if ((strlen($tip_result_away) == 0) || !is_numeric($tip_result_away)) $notipp = true;
						
			if (!$notipp)
			{
				$tip_index = 0;
				$result_tip=mysqli_query($dbconn, "SELECT tip_index FROM tip_tipp WHERE tip_mat_index=".$row['mat_index']." AND tip_usr_index=".$usr_index);
				while($row_tip=mysqli_fetch_array($result_tip)) $tip_index = $row_tip["tip_index"];
				
				if ($tip_index == 0) //neu
				{
					mysqli_query($dbconn, "insert into tip_tipp
												(tip_mat_index,tip_usr_index,tip_result_home,tip_result_away,tip_ki)
											values
												(".$row['mat_index'].", ".$usr_index.", ".$tip_result_home.", ".$tip_result_away.", 0)");
											
				}
				else
				{
					mysqli_query($dbconn, "update tip_tipp set
												tip_result_home = ".$tip_result_home.",
												tip_result_away = ".$tip_result_away.",
												tip_ki = 0
											where
												tip_index = ".$tip_index."");
				}
				
				
			}
		}
		
		//speichere finalisten
		if (checkifsaveok($dbconn, 1))
		{
			$result_grp = mysqli_query($dbconn, "SELECT grp_index, grp_anzahlteams FROM grp_group WHERE grp_vorrunde=0 and grp_anzahlteams>0 ORDER BY grp_index asc");
			while($row_grp = mysqli_fetch_array($result_grp))
			{	
				$gti_akt = array();
				$result_gti = mysqli_query($dbconn, "SELECT gti_tea_index FROM gti_grouptipp WHERE gti_usr_index=".$usr_index." AND gti_grp_index=".$row_grp["grp_index"]);
				while($row_gti = mysqli_fetch_array($result_gti)) $gti_akt [] = $row_gti["gti_tea_index"];
				
				$gti_neu = array();
				$result_tea = mysqli_query($dbconn, "SELECT tea_index FROM tea_team");
				while($row_tea = mysqli_fetch_array($result_tea)) 
				{
					if (isset($_POST['chk_'.$row_grp["grp_index"].'_'.$row_tea['tea_index']])) 
						$gti_neu [] = $row_tea["tea_index"];
				}

				$gti_del = array_diff( $gti_akt , $gti_neu );
				$gti_new = array_diff( $gti_neu , $gti_akt );
				
				foreach($gti_del as $del_id)
				{
					mysqli_query($dbconn, "delete from gti_grouptipp where gti_grp_index=".$row_grp["grp_index"]." and gti_usr_index=".$usr_index." and gti_tea_index=".$del_id);
					
				}
				foreach($gti_new as $new_id)
				{
					mysqli_query($dbconn, "insert into gti_grouptipp 
												(gti_usr_index, gti_grp_index, gti_tea_index)
											values
												(".$usr_index.", ".$row_grp["grp_index"].", ".$new_id.")");
												
				}
			}
			
			$sti_index = 0;
			$result=mysqli_query($dbconn, "SELECT sti_index FROM sti_spezialtipp WHERE sti_usr_index=".$usr_index);
			while($row=mysqli_fetch_array($result)) $sti_index = $row["sti_index"];
			
			if ($sti_index == 0)
			{
				mysqli_query($dbconn, "insert into sti_spezialtipp 
													(sti_usr_index, sti_meister, sti_koenig)
												values
													(".$usr_index.", ".$_POST['sti_meister'].", '')");
			}
			else
			{
				mysqli_query($dbconn, "update sti_spezialtipp set sti_meister=".$_POST['sti_meister'].", sti_koenig='' where sti_usr_index=".$usr_index);
			}
		}
		
	}							
	if ($m_id == 8) // Kommentare
	{
		$kommentar = konform($_POST['editor1']);
		$com_com_index = isset($_POST['com_com_index']) ? (int)$_POST['com_com_index'] : 0;
		$save_comment = true;
		
		if ($com_com_index > 0)
		{
			$parent_result = mysqli_query($dbconn, "SELECT com_index
				FROM com_comment
				INNER JOIN urm_usrtirmapping ON com_usr_index=urm_usr_index
				WHERE com_index=".$com_com_index."
				AND com_com_index=0
				AND urm_tir_index=".$tir_index."
				LIMIT 1");
			
			if (!$parent_result || mysqli_num_rows($parent_result) == 0)
			{
				$save_comment = false;
				$_SESSION['upload_error'] = 'Die Antwort konnte keinem gültigen Hauptkommentar zugeordnet werden.';
			}
		}
		
		/*$pos = stripos($kommentar, "youtube.com");
		if ($pos !== false)
		{
			$teil_1 = substr($kommentar, 0, $pos);
			$teil_2 = substr($kommentar, $pos);
			$startpos = strripos($teil_1, " "); if ($startpos === false) $startpos = 0;
			$endpos = stripos($teil_2, " "); if ($endpos === false) $endpos = strlen($teil_2);
			$endpos = $endpos + strlen($teil_1);
			
			$youtube = substr($kommentar, $startpos, $endpos - $startpos);
			$teil1 = substr($kommentar, 0, $startpos);
			$teil2 = substr($kommentar, $endpos);
			
			$youtube = str_ireplace ( "watch?v=" , "embed/" , $youtube );
			$youtube = '<iframe width="400" height="300" src="'.$youtube.'" frameborder="0" allow="encrypted-media" allowfullscreen></iframe>';
			
			$kommentar = $teil1." ".$youtube." ".$teil2;
		}*/
		
		
		if ($save_comment)
		{
			$bild = "";
			if ( isset( $_FILES['comimg'] ) && !empty($_FILES['comimg']['name'] ) )
			{	
				$bild = uploadfile($_FILES['comimg'], $usr_index);
			}
			$color = rand(0,9);
			mysqli_query($dbconn, "insert into com_comment (com_usr_index, com_text, com_color, com_media, com_com_index) VALUES (".$usr_index.", '".$kommentar."', ".$color.", '".$bild."', ".$com_com_index.")");
		}
		
		$m_id=$_POST['newm'];
	}
							
	if ($m_id == 9) // AutoTipp
	{
		if (isset($_REQUEST["pw"]))
		{
			if ($_POST['passwort'] == $_POST['passwort2'])
			{
				if (strlen($_POST['passwort'])>0)
				{
					mysqli_query($dbconn, 'UPDATE usr_user 
												   SET
													usr_password='.md5($_POST['passwort']).'
												   WHERE
													usr_index="'.$_POST['usr_index'].'"');
				}
			}
		}
		else
		{
			$auto = 0; if (isset($_POST["usr_auto"])) $auto = 1;
			mysqli_query($dbconn, 'UPDATE usr_user 
												   SET
													usr_auto='.$auto.'
												   WHERE
													usr_index="'.$_POST['usr_index'].'"');
		}
	}

	header ("Location: member.php?m_id=".$m_id.$era);
	
?>
