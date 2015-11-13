<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0trict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

    <head>
	 <?php
include 'calendrier.php';
include 'general.php';
include 'inc_style.php';	 

		echo "<title> Rendez-vous </title>";
		
		if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > TIME_OUT)) 
			$_SESSION['pass']=false;
		$_SESSION['LAST_ACTIVITY'] = time();
		
		$refr=TIME_OUT+10;

		echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"$refr\">";		
		echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
		echo "</head><body>";
		
	
	 function ajout_rdv()
			{
			global $user_organisme;
			
			$user_idx=$_SESSION['user'];
			$ligne=variable ('ligne');
			$date=mef_date_BdD(variable ('date'));
			$heure=mef_heure_BdD(variable ('heure')	);
						
			$avant=variable ('avant');
			$user1=variable ('user');
			
			$ligne .= "; De ".libelle_user($user_idx)." (".libelle_organisme($user_organisme).")";;
			if ( ($ligne!="") && ($date!="") && ($heure!=""))
				{
				$date_jour=date('Y-m-d');
				$idx=inc_index("rdv");
				if ($date_jour<=$date)
					{
					command("INSERT INTO DD_rdv VALUES ('$idx', '$user1','$user_idx','$date $heure', '$ligne', '$avant', 'A envoyer' ) ");
					}
				else
					erreur(traduire("La date doit être dans le futur"));
				}
			else
				erreur(traduire("Il manque des informations pour enregistrer le rendez-vous."));
			}
			
	 function supp_rdv($idx )
		{
		$reponse =command("select * FROM `DD_rdv` where idx='$idx' ");
		if ($donnees = fetch_command($reponse))
			{
			$date=$donnees["date"];	
			$ligne=$donnees["ligne"];	
			$reponse =command("DELETE FROM `DD_rdv` where idx='$idx' ");
			}
		}	
		
	function titre_rdv()
		{
		echo "<div class=\"CSSTableGenerator\" > ";
		echo "<table><tr><td > ".traduire('Date')." </td><td > ".traduire('Heure')." </td><td> ".traduire('Destinataire')." </td><td> ".traduire('Message envoyé par SMS')."  </td><td> ".traduire('Préavis')." </td><td> ".traduire('Auteur')." </td>";		
		}
			
			
	function affiche_rdv($nom)
		{
		global $organisme, $bdd;
		
		$format_date = "d/m/Y";	
		$aujourdhui=date($format_date,  mktime(0,0,0 , date("m"), date("d"), date ("Y")));
		echo "<p>";
		
		$filtre1=variable("filtre");
		formulaire ("");
		echo "<table><tr><td><b> Rendez-vous déjà enregistrés</td><td> - Filtre :</a></td>";
		echo " <td> <input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
		echo "</form> </td> <td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td> ";
		if ($filtre1!="")
			{
			echo "<td><form method=\"POST\" action=\"rdv.php\"  >";
			echo "<input type=\"image\" src=\"images/croixrouge.png\" width=\"20\" height=\"20\" title=\"".traduire("Supprimer filtre")."\" >";
			echo "<input type=\"hidden\" name=\"action\" value=\"supp_filtre\">";
			echo  "</form></td>";
			}
		echo "</table>";


		if ($nom=="")
			{
			if ($filtre1=="")
				$reponse = command("SELECT *, DD_rdv.idx as idx_msg FROM r_user,DD_rdv WHERE r_user.organisme='$organisme' and r_user.idx=DD_rdv.auteur order by DD_rdv.date asc  "); 
			else
				$reponse = command("SELECT *, DD_rdv.idx as idx_msg FROM r_user,DD_rdv WHERE r_user.organisme='$organisme' and r_user.idx=DD_rdv.auteur and ( DD_rdv.user  REGEXP '$filtre1' or ligne REGEXP '$filtre1' ) order by DD_rdv.date asc  "); 
			}
		else
			{
			if ($filtre1=="")
				$reponse = command("SELECT *, DD_rdv.idx as idx_msg FROM r_user,DD_rdv WHERE r_user.organisme='$organisme' and r_user.idx=DD_rdv.auteur and DD_rdv.user='$nom' order by DD_rdv.date asc  "); 
			else
				$reponse = command("SELECT *, DD_rdv.idx as idx_msg FROM r_user,DD_rdv WHERE r_user.organisme='$organisme' and r_user.idx=DD_rdv.auteur and DD_rdv='$nom' and ( nom  REGEXP '$filtre1' or ligne REGEXP '$filtre1' ) order by DD_rdv.date asc  "); 
			}		
		titre_rdv();
		echo "<td> ".traduire('Etat')." </td><td> </td>";		
		while ($donnees = fetch_command($reponse) ) 
				{
				$date=$donnees["date"];	
				$user=$donnees["user"];	
				$d3= explode(" ",$date);
				$date=mef_date_fr($d3[0]);
				$heure=$d3[1];

				if (!is_numeric($user))
					{
					$avant=$donnees["avant"];	
					$etat=$donnees["etat"];	
					if ($avant=="Aucun")
						$etat="";
					$idx=$donnees["idx_msg"];
					$ligne=stripcslashes($donnees["ligne"]);
					$auteur=libelle_user($donnees["auteur"]);
					
					// Ajouter ici filtrage sur autre champs
					if ( ($filtre1=="")
						||
						((stripos($user,$filtre1)!==false)  || (stripos($ligne,$filtre1)!==false)  || (stripos($date,$filtre1)!==false)  || (stripos($heure,$filtre1)!==false) || (stripos($auteur,$filtre1)!==false)  )
						)
						{
						$r1 = command("SELECT * FROM $bdd WHERE nom='$user' and pres_repas='Téléphone' "); 
						if (!($d1 = fetch_command($r1)))
							$avant="";
						else
							if ( !VerifierPortable($d1["commentaire"] )  )
							$avant="";

								
						echo "<tr><td> $date </td><td> $heure </td><td> <a href=\"suivi.php?nom=$user\" > $user</a></td><td> $ligne </td><td> $avant </td><td> $auteur </td>";
						if (  ($etat=="A envoyer") || ($avant=="Aucun")) 
							{
							echo "<td><form method=\"POST\" action=\"rdv.php\"  >";
							echo "<input type=\"image\" src=\"images/croixrouge.png\" width=\"20\" height=\"20\" title=\"".traduire("Supprimer")."\" >";
							echo "<input type=\"hidden\" name=\"action\" value=\"supp_rdv\">";
							echo  param("idx","$idx" )."</form></td>";
							if ($avant=="")
								echo "<td><input type=\"image\" src=\"images/illicite.png\" width=\"20\" height=\"20\" title=\"".traduire("Pas de portable enregistré")."\" ></td>";

							}
						else
						 echo "<td>(Déjà envoyé)</td>";
						}
					}
				}

		echo "</table></div>";
		}

		
	// -====================================================================== Saisie


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
		
		$organisme =$donnees["organisme"];
		
		$beneficiaire=$donnees["beneficiaire"];
		if ($beneficiaire=="") $beneficiaire="Bénéficiaires";
			
		$acteur=$donnees["acteur"];
		if ($acteur=="") $acteur="Accueillants";
		
		$libelle=$donnees["libelle"];
		$logo=$_SESSION['logo'];	

		$action=variable_s("action");
		$nom=variable_s("nom");

		if ($action=="ajout_rdv")
			ajout_rdv();

		if ($action=="supp_rdv")
			supp_rdv(variable("idx"));
					
		ajout_log_jour("----------------------------------------------------------------------------------- [ RDV = $action ] ");
		
		// =====================================================================loc IMAGE
		echo "<table border=\"0\" >";	
		echo "<tr> <td> <a href=\"rdv.php\"> <img src=\"images/rdv.jpg\" width=\"140\" height=\"100\"  ></a></td> ";		

		
		// =====================================================================loc Histo
		echo "<td>Rendez-vous de <br>";
		echo "<form method=\"GET\" action=\"rdv.php\" >";
		echo "<SELECT name=nom onChange=\"this.form.submit();\">";
		echo "<OPTION  VALUE=\"\">  </OPTION>";
		$reponse = command("SELECT * FROM $bdd WHERE nom<>'Mail' group by nom "); 			
		while ($donnees = fetch_command($reponse) ) 
			{
			$sel=$donnees["nom"];				
			if ( ($sel!= "Mail") && (strpos($sel,"(A)")===false) )
				{
				if ($sel==$nom)
					echo "<OPTION  VALUE=\"$sel\" selected> $sel </OPTION>";
				else
					echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
				}
			}
		echo "</SELECT></form> </td>";
		
		// =====================================================================loc RAPPORT
		echo "<td width=\"150\"><center>";
		echo "<ul id=\"menu-bar\">";
		echo "<li><a href=\"index.php?action=dx\">Deconnexion</a>";
		echo "</ul> ";
		
		echo "</td>";
		echo "<td><a href=\"index.php\"><img src=\"images/logo.png\" width=\"70\" height=\"50\"><a></td>";			
		echo "<td><a href=\"fissa.php\"><img src=\"images/fissa.jpg\" width=\"70\" height=\"50\"><a></td>";			
		echo "<td><a href=\"suivi.php\"><img src=\"images/suivi.jpg\" width=\"70\" height=\"50\"><a></td>";			
		echo "<td><a title=\"Alerte Grand Froid/Forte Pluie\" href=\"alerte.php\"><img src=\"images/logo-alerte.jpg\" width=\"70\" height=\"50\"></a> ";
		if ($logo!="")
			echo "<td> <a href=\"fissa.php\"> <img src=\"images/$logo\" width=\"200\" height=\"100\"  > </a> </td>";
		echo "</center></td>";	
		echo "</table> ";				
		echo"<hr>";

		
		if ($nom!="")
			{
			echo "<table><tr> <td >";
			echo "<ul id=\"menu-bar\">";					
			echo "<li><a href=\"\" >Rendez-vous de <b> $nom </b> </a></li>";
			echo "</ul> </td>";	
			echo " <td>Vers le suivi de <a href=\"suivi.php?nom=$nom\" > $nom </a> </td></table>";
			
			$reponse = command("SELECT * FROM $bdd WHERE nom='$nom' and pres_repas='Téléphone' "); 
			if ($donnees = fetch_command($reponse))
				$tel=$donnees["commentaire"];
			else 
				$tel="";				
			if (!VerifierPortable($tel)) 
				echo " <img src=\"images/illicite.png\" width=\"30\" height=\"30\"> Attention: numéro de portable de $nom non valide, si vous souhaitez qu'il recoive le SMS merci de renseigner un numéro valide <a href=\"suivi.php?nom=$nom\" > ici </a><p> ";
			}

		// ---------------------------------------------------------------- Ajout de RDV
		echo "<b>Nouveau Rendez-vous </b> ";
		titre_rdv();
		formulaire ("ajout_rdv");
		echo "<tr>";
		echo "<td> <input type=\"texte\" name=\"date\"  class=\"calendrier\" size=\"10\" value=\"\"> </td> ";
		echo " <td><input type=\"texte\" name=\"heure\"   size=\"5\" value=\"\"> </td>";
			
		echo "<td> <SELECT name=user>";
		echo "<OPTION  VALUE=\"\">  </OPTION>";
		$reponse = command("SELECT * FROM $bdd WHERE nom<>'Mail' and nom<>'Synth' group by nom "); 			
		while ($donnees = fetch_command($reponse) ) 
			{
			$sel=$donnees["nom"];				
			if ( (strpos($sel,"(A)")===false)  )
				{
				if ($sel==$nom)
					echo "<OPTION  VALUE=\"$sel\" selected> $sel </OPTION>";
				else
					echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
				
				}
			}
		echo "</SELECT></td>";
	
		echo "<td> <input type=\"texte\" name=\"ligne\"   size=\"100\" value=\"\"> </td>";
		liste_avant( "1H" );
		echo "<td><input type=\"submit\" id=\"ajout_rdv\" value=\"".traduire('Ajouter')."\" ></form> </td> ";
		echo "</table></div>";	
		
		affiche_rdv($nom);
		
		}

	pied_de_page();
		?>
	
    </body>
</html>
