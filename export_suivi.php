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

session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0trict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

    <head>
	 <?php
include 'general.php';
	

			echo "<title> Export Suivi </title>";
		
	
		echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
		echo "</head><body>";
		
	$format_date = "d/m/Y";
	$user_lang='fr';

	// ConnexiondD
	include "connex_inc.php";
	$action=variable_s("action");	
	
	require_once 'cx.php';
	
	$reponse = command("SELECT * FROM fct_fissa WHERE support='$bdd' "); 
	if ((!($donnees = fetch_command($reponse))) || (!$_SESSION['pass']) )
		{
		echo "<a href=\"https://doc-depot.com\">retour sur page d'accueil doc-depot.com</a>";
		}
	else
		{
		echo "Pour sauvegarder ces données, dans le menu de votre navigateur, faites 'Fichier', puis 'Enreistrer sous ', et choisissez un nom et un dossier de stockage.<hr>";

		echo "<table>";	
		$color="#d4ffaa" ; 
		echo "<tr><td bgcolor=\"$color\"><b>Qui  </td><td bgcolor=\"$color\">Date </td><td bgcolor=\"$color\"> </td><td bgcolor=\"$color\"> <b>Action</b> </td><td bgcolor=\"$color\">Réponse</td><td bgcolor=\"$color\">Partenaire</td><td bgcolor=\"$color\">Description</td><td bgcolor=\"$color\">Auteur saisie</td><td bgcolor=\"$color\">Date saisie</td>"; 
		
		$reponse = command("SELECT * FROM $bdd WHERE date>'2000-00-00' and pres_repas='Suivi' order by nom ASC , date DESC "); 
		$ncolor=0;
		while ($donnees = fetch_command($reponse) ) 
				{
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 	
				$nom_slash=$donnees["nom"];

				$c=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));
				$date_jour=$donnees["date"];
				$d3= explode("-",$date_jour);  
				$a=$d3[0];
				$m=$d3[1];
				$j=$d3[2];	
				$d ="$j/$m/$a";
				$p=$donnees["pres_repas"];
				$act=$donnees["activites"];
				$act=str_replace('#-#','; ',$act);

				$c=nl2br ($c);
				

					$user="";
					if ($donnees["user"]!="")
						$user=libelle_user($donnees["user"]);
						
					$r1 = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date='$date_jour' and pres_repas='reponse' "); 
					if ($d1 = fetch_command($r1) ) 
						$rep=$d1["activites"];
					else
						$rep="";

					$r1 = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date='$date_jour' and pres_repas='partenaire' "); 
					if ($d1 = fetch_command($r1) ) 
						{
						$act=$d1["activites"];
						if($act!="---")
							$partenaire=$d1["activites"];
						else
							$partenaire="";
						}
					else
						$partenaire="";
						
					echo "<tr><td bgcolor=\"$color\"><b>$nom_slash </td><td bgcolor=\"$color\">$d </td><td bgcolor=\"$color\">$p </td><td bgcolor=\"$color\"> <b>$act</b> </td><td bgcolor=\"$color\">$rep</td><td bgcolor=\"$color\"> $partenaire </td><td bgcolor=\"$color\">$c </td>"; 



				$modif=$donnees["modif"];

				if ($modif!="")
					$modif=date ("d/m/Y H:i",$modif);			
				echo "<td bgcolor=\"$color\">$user </td><td bgcolor=\"$color\">$modif </td>"; 
				}
		echo "</table>";		

		
		}

		?>
	
    </body>
</html>
