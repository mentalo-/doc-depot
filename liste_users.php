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
  		require_once  'general.php';
		require_once "connex_inc.php"; 
		require_once 'exploit.php';	
		
		function liste_user($libelle, $droit)
			{
			$formation =  parametre("Formation_num_structure"); 
			ECHO "<hr><strong>$libelle : </strong>";
			$reponse =command("select * from  r_user where droit='$droit' and mail<>'' and organisme<>'$formation' and not (mail like '%@fixeo.com%')");
			while ($donnees = fetch_command($reponse) )
				{
				$mail=$donnees["mail"];	
				if (VerifierAdresseMail($mail) )
					echo $mail."; ";
				}		
		 
		}
			ECHO "<center>Liste Utilisateurs</center>";
		
liste_user("Responsables","R");

liste_user("Acteur Social","S");

liste_user("Accueil","P");

liste_user("Mandataire","M");
?>