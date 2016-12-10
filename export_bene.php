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
	

		echo "<title> Export Bénéficiaires </title>";
		echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
		echo "</head><body>";
	

	$format_date = "d/m/Y";
	$user_lang='fr';

	// ConnexiondD
	include "connex_inc.php";
	
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
		echo "<tr><td bgcolor=\"$color\">Qui  </td><td bgcolor=\"$color\">Date naissance </td><td bgcolor=\"$color\"> Mail </td><td bgcolor=\"$color\">Téléphone</td><td bgcolor=\"$color\">Adresse</td><td bgcolor=\"$color\">Pays d'origine</td><td bgcolor=\"$color\">Commentaires</td><td bgcolor=\"$color\">Dernier Suivi</td><td bgcolor=\"$color\">Derniere visite</td>"; 
		
		$reponse = command("SELECT * FROM $bdd WHERE nom<>'Mail' and nom<>'Synth'  and !(nom like '%(B)%') and !(nom like '%(S)%') and !(nom like '%(A)%')  group by nom ASC"); 
		$ncolor=0;
		while ($donnees = fetch_command($reponse) ) 
				{
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 	
				$nom=$donnees["nom"];
				$nom_slash=addslashes2($donnees["nom"]);
					
				$date="";
				$tel="";
				$mail="";
				$adresse="";
				$pays="";
				$com="";
				$last="";
				$last_v="";
				
				$r1 = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date='1111-11-11' and   pres_repas='Age' "); 
				if ($d1 = fetch_command($r1) )
					$date=$d1["commentaire"];
				$r1 = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date='1111-11-11' and pres_repas='Mail' "); 
				if ($d1 = fetch_command($r1) )
					$mail=$d1["commentaire"];				
				$r1 = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date='1111-11-11' and pres_repas='Téléphone' "); 
				if ($d1 = fetch_command($r1) )
					$tel=$d1["commentaire"];				
				$r1 = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date='1111-11-11' and pres_repas='nationalite' "); 
				if ($d1 = fetch_command($r1) )
					$pays=$d1["commentaire"];				
				$r1 = command("SELECT * FROM $bdd WHERE nom='$nom_slash'  and date='1111-11-11' and pres_repas='adresse' "); 
				if ($d1 = fetch_command($r1) )
					$adresse=$d1["commentaire"];				
				$r1 = command("SELECT * FROM $bdd WHERE nom='$nom_slash'  and date='1111-11-11'  and pres_repas='PE' "); 
				if ($d1 = fetch_command($r1) )
					$com=$d1["commentaire"];
				$r1 = command("SELECT * FROM $bdd WHERE nom='$nom_slash'  and date>'2000-01-01' and pres_repas='Suivi' order by date desc"); 
				if ($d1 = fetch_command($r1) )
					$last=$d1["date"];					
				$r1 = command("SELECT * FROM $bdd WHERE nom='$nom_slash'  and date>'2000-01-01' and  pres_repas like 'Visite%'order by date desc"); 
				if ($d1 = fetch_command($r1) )
					$last_v=$d1["date"];			
					
				echo "<tr><td bgcolor=\"$color\"><b>$nom </td><td bgcolor=\"$color\">$date </td><td bgcolor=\"$color\">$mail </td><td bgcolor=\"$color\"> $tel </td><td bgcolor=\"$color\">$adresse</td><td bgcolor=\"$color\"> $pays </td><td bgcolor=\"$color\">$com </td><td bgcolor=\"$color\">$last </td><td bgcolor=\"$color\">$last_v </td>"; 
				}
		echo "</table>";		
		}

		?>
	
    </body>
</html>
