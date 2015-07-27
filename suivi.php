<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0trict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

    <head>
	 <?php
include 'calendrier.php';
include 'general.php';
include 'inc_style.php';	 
	
		if (isset ($_GET["action"])) $action=$_GET["action"]; else  	$action="";	
		
		if (isset ($_GET["nom"]))
			{
			$nom = $_GET["nom"];
			echo "<title> $nom </title>";
			}
	     else
			echo "<title> Suivi </title>";
		
		if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > TIME_OUT)) 
			$_SESSION['pass']=false;
		$_SESSION['LAST_ACTIVITY'] = time();
		
		$refr=TIME_OUT+10;

		echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"$refr\">";		
		echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
		echo "</head><body>";
		
	function liste_type($val_init ="" )
		{
		echo "<SELECT name=\"type\"  onChange=\"this.form.submit();\">";
		affiche_un_choix($val_init,"Bénéficiaire");
		affiche_un_choix($val_init,"Bénéficiaire femme");
		echo "</SELECT>";
		}


	
	function nouveau( $nom )
		{
		global $bdd;
		
		$nom=str_replace("\"","",$nom);
		$nom_slash= addslashes2($nom);	

		if (($nom!="") && (!is_numeric($nom)))
			{
			$r1 = command("SELECT DISTINCT count(*) FROM $bdd WHERE date='0000-00-00' and nom='$nom_slash' ");
			$r2=nbre_enreg($r1); 
			if ($r2[0]==0)
				{
				$user= $_SESSION['user_idx'];
				$modif=time();
				$cmd = "INSERT INTO `$bdd`  VALUES ( '$nom', '', '','','$user','$modif','')";
				$reponse = command($cmd);
				}
			}
		}

	

		
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
		
	function histo($nom,$detail)
		{
		global $bdd;

		$nom_slash= addslashes2($nom);	

		$i=0; 
		if ($detail=="")
			$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date<>'0000-00-00' and pres_repas<>'pda' order by date DESC "); 
		else
			$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date<>'0000-00-00' and pres_repas='Suivi' order by date DESC "); 

		$ncolor=0;
		echo "<table>";
		while (($donnees = fetch_command($reponse) ) && ($i<10000))
				{
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 	

				$c=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));
				$date_jour=$donnees["date"];
				$d3= explode("-",$date_jour);  
				$a=$d3[0];
				$m=$d3[1];
				$j=$d3[2];	
				$d ="$j/$m/$a";
				$p=$donnees["pres_repas"];
				$c=nl2br ($c);
				if ($p=="Suivi") 
					{
					$user="";
					if ($donnees["user"]!="")
						$user=libelle_user($donnees["user"]);
					echo "<tr><td bgcolor=\"$color\"><b>$d  </td><td bgcolor=\"$color\">$p </td><td bgcolor=\"$color\">$c </b><td bgcolor=\"$color\">$user </b>"; 

					}
				else
					echo "<tr><td bgcolor=\"$color\">$d </td><td bgcolor=\"$color\">$p </td><td bgcolor=\"$color\"> $c </td> ";
				
				// ++++ si activité ajouter liste des participants
				if (strpos($nom_slash,"(A)")>0)
					echo "<td bgcolor=\"$color\">". liste_participants_activite($nom ,$date_jour)  ."</td> ";

				$i++; 		
				}
		echo "</table>";
		}
	
	function liste_participants_activite( $act, $date )
		{
		global $bdd;

		$ret="";
		
		$reponse = command("SELECT * FROM $bdd WHERE date='$date' and (activites like '%$act%') group by nom ASC "); 
		while ($donnees = fetch_command($reponse) )
			$ret.=stripcslashes($donnees["nom"]).", ";
	
		return($ret);
		}
		
	function affiche_rdv($nom)
		{
		global $organisme, $bdd;
		
		if  ((!is_numeric($nom)) && (strpos($nom,"(A)")===false)) 
			{
			echo "<a href=\"rdv.php?nom=$nom\" > <img src=\"images/reveil.png\" width=\"35\" height=\"35\">Rendez-vous de $nom : </a>";
			$i=0;
			$reponse = command("SELECT * FROM r_user,DD_rdv WHERE r_user.organisme='$organisme' and DD_rdv.user='$nom' GROUP BY DD_rdv.date  ORDER BY DD_rdv.date DESC Limit 10"); 
			while ($donnees = fetch_command($reponse) ) 
					{
					if ($i++==0)
						echo "<table>";
					$i++;
					$date=$donnees["date"];	
					$user=$donnees["user"];	
					$d3= explode(" ",$date);
					$date=mef_date_fr($d3[0]);
					$heure=$d3[1];
					$ligne=stripcslashes($donnees["ligne"]);
					$auteur=libelle_user($donnees["auteur"]);
					echo "<tr> <td> $date </td> <td> à $heure </td><td> $ligne </td> <td> $auteur </td> ";
					}
			if ($i==0)
				echo "Aucun rendez-vous enregistré. <hr> ";
			else
				echo "</table><hr>";
			}
		}
		
	function chgt_nom(  $nom, $nouveau)
		{
		global $bdd, $organisme;
		
		if (($nom!="") && ($nouveau!="") && ($nom!="Synth")&& ($nom!="Mail"))
			{
			if ((strpos( $nom ,'(A)')>0) && (strpos( $nouveau ,'(A)')===false))
				 $nouveau.=" (A)";
			if ((strpos( $nom ,'(B)')>0) && (strpos( $nouveau ,'(B)')===false))
				 $nouveau.=" (B)";	
			if ((strpos( $nom ,'(S)')>0) && (strpos( $nouveau ,'(S)')===false))
				 $nouveau.=" (S)";				 
			$nouveau= addslashes2($nouveau);
			$user= $_SESSION['user_idx'];
			$modif=time();
			$nom_slash= addslashes2($nom);	
			$reponse = command("UPDATE $bdd SET nom='$nouveau' , user='$user', modif='$modif' WHERE nom='$nom_slash' ") ;

			// mise à jour spécifique pour les activités qui 
			$reponse = command("SELECT * FROM $bdd WHERE  (activites like '%$nom%') "); 
			while ($donnees = fetch_command($reponse) )
					{
					$nom_user=stripcslashes($donnees["nom"]);
					$date=$donnees["date"];
					$act=$donnees["activites"];
					$act = str_replace ($nom, $nouveau, $act);
					command("UPDATE $bdd SET activites='$act'  WHERE date='$date' and nom='$nom_user' ") ;
					}

			// Mise à jour du nom dan sla table des rendez vous
			$reponse = command("SELECT *, DD_rdv.idx as idx_msg FROM r_user,DD_rdv WHERE r_user.organisme='$organisme' and DD_rdv.user='$nom' GROUP BY idx_msg "); 
			while ($donnees = fetch_command($reponse) ) 
					{
					$idx_msg=$donnees["idx_msg"];
					command("UPDATE DD_rdv SET nom='$nouveau'  WHERE idx='$idx_msg'  ","x") ;
					}

			}
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

		$memo=variable_s("memo");
		$action=variable_s("action");
		if ($action=="")
			$action="suivi";
		$pda=variable_s("pda");
		$com=variable_s("com");
		$nom=variable_s("nom");
	
		if ($action=="chgt_nom")
			{
			$nouveau=variable_s("nouveau");
			chgt_nom($nom,$nouveau);
			$nom=$nouveau;
			$action="suivi";
			}
		if ($action=="nouveau")
			{
			$type=variable_s("type");
			if ($type=="Bénéficiaire femme")
				$nom .= " (F)";
			nouveau($nom);
			}	
			
		if (!isset ($_GET["date_jour"]))
			$date_jour=date('d/m/Y');
		else 
			{
			$date_jour=variable_s("date_jour");
			if ( mise_en_forme_date_aaaammjj($date_jour)=="")
				{
				erreur("Format de date incorrect");
				$action="";
				$date_jour=date('d/m/Y');
				}
			}

		// =====================================================================loc IMAGE
		echo "<table border=\"0\" >";	
		echo "<tr> <td> <a href=\"suivi.php\"> <img src=\"images/suivi.jpg\" width=\"140\" height=\"100\"  ></a></td> ";		

		// =====================================================================loc Histo
		echo "<td>Suivi de <br>";
		echo "<form method=\"GET\" action=\"suivi.php\" >";
		echo "<input type=\"hidden\" name=\"action\" value=\"suivi\"> " ;
		echo "<SELECT name=nom onChange=\"this.form.submit();\">";
		echo "<OPTION  VALUE=\"\">  </OPTION>";
		$reponse = command("SELECT * FROM $bdd WHERE nom<>'Mail' group by nom "); 			
		while ($donnees = fetch_command($reponse) ) 
			{
			$sel=$donnees["nom"];				
			if ($sel==$nom)
				echo "<OPTION  VALUE=\"$sel\" selected > $sel </OPTION>";
			else
				echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
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
		echo "<td><a href=\"rdv.php\"><img src=\"images/rdv.jpg\" width=\"70\" height=\"50\"><a></td>";			
		echo "<td><a title=\"Alerte Grand Froid/Forte Pluie\" href=\"alerte.php\"><img src=\"images/logo-alerte.jpg\" width=\"70\" height=\"50\"></a> ";
		if ($logo!="")
			echo "<td> <a href=\"fissa.php\"> <img src=\"images/$logo\" width=\"200\" height=\"100\"  > </a> </td>";
		echo "</center></td>";	
		echo "</table><hr> ";				
			
		ajout_log_jour("----------------------------------------------------------------------------------- [ SUIVI = $action ] $date_jour ");
		
		$nom_slash= addslashes2($nom);	
		if ($nom!="")
			{
			if ($action=="telephone")
						{
						$telephone=variable_s("telephone");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Téléphone' "); 
						$user= $_SESSION['user_idx'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$telephone' , modif='$modif' where nom='$nom_slash' and pres_repas='Téléphone' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '', 'Téléphone','$telephone','$user','$modif','')");					
						$action="suivi";
						}	
		
				if ($action=="mail")
						{
						$mail=variable_s("mail");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='mail' "); 
						$user= $_SESSION['user_idx'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$mail' , modif='$modif' where nom='$nom_slash' and pres_repas='Mail' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '', 'Mail','$mail','$user','$modif','')");					
						$action="suivi";
						}
					
					echo "<table><tr> <td >";
					echo "<ul id=\"menu-bar\">";					
					echo "<li><a href=\"\" >Suivi de <b> $nom </b> </a></li>";
					echo "</ul> </td><td>  - $date_jour";
					/*
					echo "<form method=\"GET\" action=\"suivi.php\">";
					echo "<input type=\"hidden\" name=\"action\" value=\"$action\"> " ;
					echo "<input type=\"hidden\" name=\"nom\" value=\"$nom\"> " ;
					echo "<input type=\"text\" name=\"date_jour\" size=\"10\" value=\"$date_jour\" class=\"calendrier\"></td>";
					echo "<td>"; 
					echo "<input type=\"submit\" value=\"Selectionner\" > </td> </form>"; 
					*/
			if ($action=="age")
						{
						$age=variable_s("age");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Age' "); 
						$user= $_SESSION['user_idx'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$age' , modif='$modif' where nom='$nom_slash' and pres_repas='Age' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '', 'Age','$age','$user','$modif','')");					
						$action="suivi";
						}	
						
					echo "</table> ";	
						
					if ($com=="") 
						{
						$date_jour_gb=mise_en_forme_date_aaaammjj( $date_jour);
						$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour_gb' and nom='$nom_slash' and pres_repas='Suivi' "); 
						if ($donnees = fetch_command($reponse))
							$com=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));
						else
							$com="";
						}
					else
						{
						$date_jour_gb=mise_en_forme_date_aaaammjj( $date_jour);
						$com=addslashes2($com);
						$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour_gb' and nom='$nom_slash' and pres_repas='Suivi' "); 
						
						$user= $_SESSION['user_idx'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$com' , user='$user' , modif='$modif' where nom='$nom_slash' and date='$date_jour_gb' and pres_repas='Suivi' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '$date_jour_gb', 'Suivi','$com','$user','$modif','')");					
						//$commentaire=$com;
						}
					
					$derniere_maj_pda="";
					
					if ($pda=="")
						{
						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='pda' "); 
						if ($donnees = fetch_command($reponse))
							{
							$pda=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));
							if ($donnees["user"]!="")
								$lib_user=libelle_user($donnees["user"]);
							else
								$lib_user="";
							$modif="";
							if ($donnees["modif"]!="")
								$modif=date ("d/m/Y H:i",$donnees["modif"]);
							if ($lib_user!="")
								$derniere_maj_pda=" (Derniére modification le $modif par $lib_user).";
							}
						else
							$pda="";
						}
					else
						{
						$pda=addslashes2($pda);
						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='pda' "); 
						
						$user= $_SESSION['user_idx'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$pda' , user='$user' , modif='$modif' where nom='$nom_slash' and pres_repas='pda' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '', 'pda','$pda','$user','$modif','')");					
						$pda=stripcslashes($pda);
						}					
					

					if ($nom!="Synth")
						{
						if (!(strpos($nom,"(A)")>0))
							{
							echo "<TABLE><TR> <td></td><td > <div class=\"CSS_titre\"  >";
							
							$tel="";
							$age="";
							$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Téléphone' "); 
							if ($donnees = fetch_command($reponse))
								$tel=$donnees["commentaire"];
							else 
								$tel="";
							echo "<table border=\"0\" >";
							echo "<tr> <td> <b> Portable </b> : </td><td>";
							echo "<form method=\"GET\" action=\"suivi.php\">";
							echo "<input type=\"hidden\" name=\"action\" value=\"telephone\"> " ;
							echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
							echo "<input type=\"text\" name=\"telephone\"  value=\"$tel\">";
							echo "</form> ";
							$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Age' "); 
							if ($donnees = fetch_command($reponse))
								$age=$donnees["commentaire"];
							else 
								$age="";
							echo "<td> <b> Age </b> : </td><td>";
							echo "<form method=\"GET\" action=\"suivi.php\">";
							echo "<input type=\"hidden\" name=\"action\" value=\"age\"> " ;
							echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
							echo "<input type=\"text\" name=\"age\" size=\"2\"  value=\"$age\">";
							echo "</form> ";
							
							$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Mail' "); 
							if ($donnees = fetch_command($reponse))
								$mail=$donnees["commentaire"];
							else
								$mail="";
							echo "<td> <b> Mail </b> : </td><td>";
							echo "<form method=\"GET\" action=\"suivi.php\">";
							echo "<input type=\"hidden\" name=\"action\" value=\"mail\"> " ;
							echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
							echo "<input type=\"text\" name=\"mail\" size=\"40\"  value=\"$mail\">";
							echo "</form> ";
							
							echo "</td></table>";
							}
						echo "<table border=\"0\" >";
						echo "<tr> <td> <b> Suivi </b> : ";
						echo "<form method=\"GET\" action=\"suivi.php\">";
						echo "<input type=\"hidden\" name=\"action\" value=\"suivi\"> " ;
						echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
						echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
						echo "<TEXTAREA rows=\"4\" cols=\"110\" name=\"com\" onChange=\"this.form.submit();\">$com</TEXTAREA>";
						echo "</td> ";
						echo "</form> ";
						echo "<tr> <td> <b> Plan d'action </b> :  $derniere_maj_pda ";
						echo "<form method=\"GET\" action=\"suivi.php\">";
						echo "<input type=\"hidden\" name=\"action\" value=\"pda\"> " ;
						echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
						echo "<TEXTAREA rows=\"4\" cols=\"110\" name=\"pda\" onChange=\"this.form.submit();\">$pda</TEXTAREA>";
						echo "</td> ";
						echo "</form> ";
						echo "</table>  ";
						fin_cadre();

					if  (($action=="suivi") || ($action=="pda"))
						affiche_rdv($nom);		
						
						if ($action!="accompagnement")
								echo "<a href=\"suivi.php?action=accompagnement&nom=$nom&date_jour=$date_jour\" > ( N'afficher que l'accompagnement )</a>";
							else
								echo "<a href=\"suivi.php?action=suivi&nom=$nom&date_jour=$date_jour\" > ( Afficher tout l'historique )</a>";
						}						


						
					if  (($action=="suivi") || ($action=="pda"))
						histo($nom,"");
					else
						histo($nom,"accompagnement");

			}
		else 
			{
			echo "<p><br><p><br><p><br><p><br><p><br><p><br><p><br><p><br><p><br><p><br>";
		
			// =====================================================================loc NOUVEAU
			echo "<table><tr><td bgcolor=\"#d4ffaa\">Création nouveau :</td><td bgcolor=\"#d4ffaa\"><form method=\"GET\" action=\"suivi.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
			echo "<input type=\"text\" name=\"nom\" size=\"30\" value=\"\">";	
			liste_type();
			echo "<input type=\"submit\" value=\"Créer Nouveau\" >  ";
			echo "</td></form> ";	
			echo " </table>";
				
			if ( ($_SESSION['droit']=='R') ||($_SESSION['droit']=='S') )
				{
				// =====================================================================loc CHANGEMENT NOM
				echo "<P> <table border=\"0\" ><tr> <td>Modification d'un nom :</td><td>";
				echo "<form method=\"GET\" action=\"suivi.php\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"chgt_nom\"> " ;
				echo "<SELECT name=nom>";
				echo "<OPTION  VALUE=\"\">  </OPTION>";
				$reponse = command("SELECT * FROM $bdd WHERE nom<>'Mail' group by nom "); 			
				while ($donnees = fetch_command($reponse) ) 
					{
					$sel=$donnees["nom"];	
					echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
					}
				echo "</SELECT></td>";
				echo "</td> <td> à transformer en </td> <td>";
				echo "<input type=\"text\" name=\"nouveau\" size=\"20\" value=\"\">";	
				echo "<input type=\"submit\" value=\"Mise à jour du nom\" >  ";
				echo " </form> ";
				echo "</table>  ";
				}
				
			}
		}

	
	pied_de_page();
		?>
	
    </body>
</html>
