<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0trict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

    <head>
	 <?php
include 'general.php';
	

		echo "<title> Export Bénéficiaires </title>";
		echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
		echo "</head><body>";
	
	
	function mise_en_forme_date_aaaammjj( $date_jour)
		{
		$d3= explode("/",$date_jour);  
		if (isset($d3[2]))
			$a=$d3[2];
		else
			$a=date("Y");
		if ($a<100) $a+=2000;
		$m=$d3[1];
		$j=$d3[0];	
		if (($j<1) || ($j>31) || ($m<1) || ($m>12) )
			return("");
		
		return( "$a-$m-$j" );
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
