<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0trict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

    <head>
	 <?php
include 'general.php';

		echo "<title> Export </title>";
				echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
		echo "</head><body>";
		

	function affiche_memo()
		{
		global $bdd,$format_date ;
		
		$date_jour=date($format_date );
		$i=0; 
		$reponse = command("SELECT * FROM $bdd WHERE commentaire<>'' and date='0000-00-00' and pres_repas<>'pda' and pres_repas<>'Age' and pres_repas<>'Mail' and pres_repas<>'Téléphone' and pres_repas<>'nationalie' and pres_repas<>'PE' order by nom DESC "); 
		while (($donnees = fetch_command($reponse) ) && ($i<10000))
				{
				if ($i==0)
					echo "<b>Memo: </b> ";
				$c=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]) );
				$n=$donnees["nom"];
				echo "<BR> <b>$n</b>: $c";
				$i++; 		
				}
		}
		
	

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
	if (($_SESSION['droit']=="s") || ($_SESSION['droit']=="p") )
	{
	echo "Compte Inctif: merci de contacter votre responsable pour réactiver votre compte";
	}
	else
		{
		
		echo "Pour sauvegarder ces données, dans le menu de votre navigateur, faites 'Fichier', puis 'Enreistrer sous ', et choisissez un nom et un dossier de stockage.<hr>";

		$organisme =$donnees["organisme"];
		
		$memo=variable_s("memo");
		
		affiche_memo();

		$ncolor=0;
		$reponse = command("SELECT DISTINCT * FROM $bdd WHERE date>'2010-01-01' and pres_repas!='Suivi' and pres_repas!='reponse' and pres_repas!='partenaire' and pres_repas!='Age'  and pres_repas!='Telephone' order by date  desc, nom asc "); 
		echo "<table>";
		$color="#d4ffaa" ; 
		echo "<tr><td bgcolor=\"$color\"><b>Qui  </td><td bgcolor=\"$color\">Date </td><td bgcolor=\"$color\"> <b>Action</b> </td><td bgcolor=\"$color\">Description</td><td bgcolor=\"$color\">Activités/Participants</td><td bgcolor=\"$color\">Auteur saisie</td><td bgcolor=\"$color\">Date saisie</td>"; 
		

		while (($donnees = fetch_command($reponse) ) )
				{
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 	
				$date_jour=$donnees["date"];
				$nom=$donnees["nom"];
				$pres_repas=$donnees["pres_repas"];
				$commentaire=$donnees["commentaire"];
				$activites=$donnees["activites"];
				$user=$donnees["user"];
				$modif=$donnees["modif"];
					
				if ($donnees["user"]!="")
					$user=libelle_user($donnees["user"]);
				$c=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));
				$d3= explode("-",$date_jour);  
				$a=$d3[0];
				$m=$d3[1];
				$j=$d3[2];	
				$d ="$j/$m/$a";
				$p=$donnees["pres_repas"];
				$c=nl2br ($c);
				$nom_slash= addslashes2($nom);		
				echo "<tr><td bgcolor=\"$color\">$nom_slash  </td><td bgcolor=\"$color\">$d  </td><td bgcolor=\"$color\">$p </td><td bgcolor=\"$color\">$c </b></td>"; 
				

				// ++++ si activité ajouter liste des participants
				if (strpos($nom_slash,"(A)")>0)
					echo "<td bgcolor=\"$color\">". liste_participants_activite($nom_slash ,$date_jour)  ."</td> ";
				else
						
					{
					$activites = str_replace("#-#",",",$activites);
					echo "<td bgcolor=\"$color\">$activites</td> ";
					}
				if ($modif!="")
					$modif=date ("d/m/Y H:i",$modif);			
				echo "<td bgcolor=\"$color\">$user </td><td bgcolor=\"$color\">$modif </td>"; 

				}
		echo "</table>";

		}
		?>
	
    </body>
</html>
