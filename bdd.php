<?php


	// ========================================================== BDD ==========================================
	function command($ligne, $flag="")
		{
		global $action;
			
		if ( ($flag!="") && ($_SERVER['REMOTE_ADDR']=="127.0.0.1"))
			echo "<p>$ligne ";
		
		if (isset ($_SESSION['chgt_user']) && 	($_SESSION['chgt_user']==true) && (strpos( strtolower($ligne),"select" )===false) )
			{
			return(false);
			}
		else
			{
			ajout_log_jour($ligne);
			$result= mysql_query($ligne);
			if (!$result) 
				{
				if (!isset($action))
					$action="";
				ajout_log_tech( "Requête '$ligne' invalide (Action='$action'): " . mysql_error(),"P0");
				}
			return( $result );	
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
		return(  mysql_num_rows($reponse) );
		}	

	function nbre_enreg_fetch ($reponse)
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