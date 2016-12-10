<?php
///////////////////////////////////////////////////////////////////////
//   This file is part of doc-depot.
//
//   doc-depot is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
//
//   doc-depot is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
//
//   You should have received a copy of the GNU General Public License along with doc-depot.  If not, see <http://www.gnu.org/licenses/>.
///////////////////////////////////////////////////////////////////////

	// ========================================================== BDD ==========================================
	function command($ligne, $flag="")
		{
		global $action;
			
		if ( ($flag!="") && ( poste_local() ))
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