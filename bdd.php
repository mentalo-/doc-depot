<?php


	// ========================================================== BDD ==========================================
	function command($ligne, $flag="")
		{
		if ( ($flag!="") && ($_SERVER['REMOTE_ADDR']=="127.0.0.1"))
			echo "<p>$ligne ";
		
		if (isset ($_SESSION['chgt_user']) && 	($_SESSION['chgt_user']==true) && (strpos( strtolower($ligne),"select" )===false) )
			{
			return(false);
			}
		else
			{
			ajout_log_jour($ligne);
			if (
				(!stristr($ligne,"z_traduire"))
				&&
				(!stristr($ligne," DD_param "))	
				&&
				( !isset($_SESSION['pass']) || ($_SESSION['pass']==false) || !(isset($_SESSION['user'])) || ($_SESSION['user']=="") )
					)
					{
					$date_log=date('Y-m-d');	
					$heure_jour=date("H\hi.s");	
				
					$f_log = fopen('tmp/sql.txt', 'a+');		
					fputs($f_log, $date_log." ".$heure_jour." : ".addslashes($ligne)."\r\n"); 
					fclose($f_log);					
					}
			return( mysql_query($ligne) );	
			}
		}
	

	function fetch_command ($reponse)
		{
		return(  mysql_fetch_array($reponse) );
		}

	function nbre_colonnes ($reponse)
		{
		return(  mysql_num_fields($reponse) );
		}
		
	function nbre_enreg ($reponse)
		{
		return(  mysql_fetch_row($reponse) );
		}	
	
	// nom utilisé
	function ouverture_bdd ()
		{
		global 	$ZZ_CLE;
		
		require_once "connex_inc.php";
		}

	function fermeture_bdd ()
		{
		return(  mysql_close( ) );
		}
?>