<?php session_start ();
if (!isset ($_SESSION["user_id_tipp"]))
{
  header ("Location: formular.php");
}
?>