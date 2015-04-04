<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0trict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">


    <head>
	 <?php
include 'calendrier.php';
include 'general.php';
include 'inc_style.php';	 

		if (isset ($_GET["action"])) $action=$_GET["action"]; else  	$action="";	
		if ((isset ($_GET["nom"])) && ($action=="suivi"))
			{
			$nom = $_GET["nom"];
			echo "<title> $nom </title>";
			}
	     else
			echo "<title> FISSA </title>";
		
		if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > TIME_OUT)) 
			$_SESSION['pass']=false;
		$refr=TIME_OUT+10;

		echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"$refr\">";		
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
// ---------------------------------------------------------------------------------------	
	function charge_date($date_jour)
		{
		global $exclus, $imax, $nom_charge, $pres_repas, $nb_usager,$commentaire, $synth,$bdd;

		for($i=0;$i<$nb_usager;$i++) 
			{
			$nom_charge[$i]="";
			$pres_repas[$i]="";
			$commentaire[$i]="";
			}
		$i=0; 
			
		$d=mise_en_forme_date_aaaammjj( $date_jour);
		$reponse = command("SELECT DISTINCT * FROM $bdd WHERE date='$d' and pres_repas!='Suivi' "); 
		while (($donnees = fetch_command($reponse) ) && ($i<10000))
			if ($donnees["nom"]!="Synth")
				{
				$nom_charge[$i]=$donnees["nom"];
				$pres_repas[$i]=$donnees["pres_repas"];
				$commentaire[$i]=$donnees["commentaire"];
				$i++; 		
				}
			else
				$synth=$donnees["commentaire"];
		$imax=$i;
		
		
		$exclus="";
		for ($i=0; $i<$imax; $i++)
			$exclus.="'".$nom_charge[$i]."',";
		$exclus=  " and nom NOT IN (".$exclus." '' ) " ;
		}
	
	function charge_nom()
		{
		global $jmax, $liste_nom, $bdd;
		
		for($i=0;$i<1000;$i++) $liste_nom[$i]="";
		$j=0; 		
		
		$reponse = command("select nom from $bdd group by nom ASC");
			
		while (($donnees = fetch_command($reponse) ) && ($j<10000))
			if ($donnees["nom"]!="Synth")
				{
				$liste_nom[$j]=stripcslashes($donnees["nom"]);
				$j++; 		
				}
		$jmax=$j;
		}	
	
	// =========================================================== procedures générales

	
	function nouveau( $date_jour, $nom, $pres,$com ,$memo)
		{
		global $bdd;
			
		if ($nom!="")
			{
			$d=mise_en_forme_date_aaaammjj( $date_jour);
			
			$r1 = command("SELECT DISTINCT count(*) FROM $bdd WHERE date='0000-00-00' and nom='$nom' ");
			$r2=nbre_enreg($r1); 
			if ($r2[0]==0)
				{
				$cmd = "INSERT INTO `$bdd`  VALUES ( '$nom', '', '','')";
				$reponse = command($cmd);
				}
				
			$r1 = command("SELECT  count(*) FROM $bdd WHERE date='$d' and nom='$nom' ");
			$r2=nbre_enreg($r1); 
			$r=$r2[0];	
			$com= addslashes2($com);
			$nom= addslashes2($nom);
			$memo= addslashes2($memo);
			if ($r==0)
				{
				if ( $pres!="Erreur saisie")
					{
					$cmd = "INSERT INTO `$bdd`  VALUES ( '$nom', '$d', '$pres','$com')";
					$reponse = command($cmd);
					}
				}
			else
				{
				$cmd = "UPDATE $bdd set commentaire='$memo' where nom='$nom' and date='0000-00-00' ";
				$reponse = command($cmd);				
				
				if ( $pres!="Erreur saisie")
					$cmd="UPDATE $bdd SET commentaire='$com', pres_repas='$pres'  WHERE nom='$nom' AND date='$d'" ;
				else
					$cmd="DELETE FROM $bdd  WHERE nom='$nom' AND date='$d'" ;
							
				$reponse = command($cmd);
				}
			}
		}

	function chgt_nom(  $nom, $nouveau)
		{
		global $bdd;
		
		if (($nom!="") && ($nom!="Synth")&& ($nom!="Mail"))
			{
			$nouveau= addslashes2($nouveau);
			$cmd="UPDATE $bdd SET nom='$nouveau' WHERE nom='$nom' " ;
			$reponse = command($cmd);
			}
		}	

	function liste_presence( $val_init , $nom ="", $color ="")
		{
		global $acteur;
		
		echo "</td> <td bgcolor=\"$color\"> ";
		echo "<SELECT name=\"presence\"  onChange=\"this.form.submit();\">";
		if ((!strstr($nom,"(A)")) && (!strstr($nom,"(B)")) && (!strstr($nom,"(S)")) )		
			{
			affiche_un_choix($val_init,"Visite");
			affiche_un_choix($val_init,"Visite+Repas");
			affiche_un_choix($val_init,"Refusé");
			}
		else
			{
			if ((!strstr($nom,"(B)")) && (!strstr($nom,"(S)")) )	
				affiche_un_choix($val_init,"Activité");	
			else
				affiche_un_choix($val_init,"$acteur");
			}
		affiche_un_choix($val_init,"Pour info");

		affiche_un_choix($val_init,"Erreur saisie");
		echo "</SELECT>";
		}
		
	function liste_type($val_init ="" )
		{
		echo "<SELECT name=\"type\"  onChange=\"this.form.submit();\">";
		affiche_un_choix($val_init,"Bénéficiaire");
		affiche_un_choix($val_init,"Bénéficiaire femme");
		affiche_un_choix($val_init,"Bénévole");
		affiche_un_choix($val_init,"Salarié");
		affiche_un_choix($val_init,"Activité");
		echo "</SELECT>";
		}

	
	function presents($date)
		{
		global $bdd;

		$t="";
		$i=0; 
		$reponse = command("SELECT * FROM $bdd WHERE date='$date' and nom <>'Synth' and ( pres_repas='Visite+Repas' or pres_repas='Visite' or pres_repas='Refusé' ) group by nom ASC"); 
		while (($donnees = fetch_command($reponse) ) && ($i<10000))
				{
				$nom=stripcslashes($donnees["nom"]);
				$t = $t."$nom; ";

				$i++; 		
				}
		return $t;
		}

	function accueillants($date)
		{
		global $bdd,$acteur;

		$t="";
		$i=0; 
		$reponse = command("SELECT * FROM $bdd WHERE date='$date' and nom <>'Synth' and (pres_repas='$acteur' ) group by nom ASC"); 
		while (($donnees = fetch_command($reponse) ) && ($i<10000))
				{
				$nom=stripcslashes($donnees["nom"]);
				$t = $t."$nom; ";
				$i++; 		
				}
		return $t;
		}
		
	function proposition($profil, $titre="", $fin_cadre="")
		{
		global  $exclus, $date_jour, $bdd, $acteur;


		$date_jour2=  mise_en_forme_date_aaaammjj($date_jour);
		$nu=0;
		if ($profil=="")
			$l= date('Y-m-d',  mktime(0,0,0 , date("m")-2, date("d"), date ("Y")));
		else
			$l= date('Y-m-d',  mktime(0,0,0 , date("m")-4, date("d"), date ("Y")));

		$reponse = command("SELECT *,count(*) as TOTAL FROM $bdd where date>'$l' and nom<>'Synth' and nom<>'Mail' $exclus group by nom order by nom  "); 
		while ($donnees = fetch_command($reponse) ) 
				{
				$n=$donnees["nom"];
					{
					if ($profil!="")
						{
						if (strpos($n,"$profil")!==FALSE)
							{
							if ( ($nu==0) && ($titre!="") )
								{
								echo "<tr> <td width=\"1000\"> $titre: ";
								$nu++;
								}
							$n_court=substr($n, 0, strlen($n)-3);
							if ( (strpos($n,"(B)")!==FALSE) ||(strpos($n,"(S)")!==FALSE) )
								echo "<a href=\"fissa.php?action=nouveau&date_jour=$date_jour&nom=$n&memo=&presence=Acteur+Social&commentaire=\">$n_court</a>; " ;
							if ( (strpos($n,"(A)")!==FALSE))
								echo "<a href=\"fissa.php?action=nouveau&date_jour=$date_jour&nom=$n&memo=&presence=Activité&commentaire=\">$n_court</a>; " ;

							}
						}
					else
						if ( (strstr($n,"(B)")===FALSE) &&(strstr($n,"(S)")===FALSE) && (strstr($n,"(A)")===FALSE))
							{
							if ( ($nu==0) && ($titre!="") )
								{
								echo "<tr> <td width=\"1000\"> $titre: ";							
								$nu++;
								}		
							$n_court=stripcslashes($n);
							echo "<a href=\"fissa.php?action=nouveau&date_jour=$date_jour&nom=$n&memo=&presence=Visite&commentaire=\">$n_court</a>; " ;
							}
					
					}
				}
		if ( ($nu!=0) && ($fin_cadre!="") )
				echo "</td>";				
		}

	function rapport($date)
		{
		global $nb_usager,$nom_charge,$commentaire,$imax, $pres_repas, $bdd, $acteur, $beneficiaire, $libelle;

		$date_a_afficher=$date;		
		$i=0; 
		$date_gb=mise_en_forme_date_aaaammjj( $date);
		$reponse = command("SELECT * FROM $bdd WHERE date='$date_gb' and nom='Mail' "); 
		if (!fetch_command($reponse) )  // il existe déjà un mail
			{
			echo "<form method=\"GET\" action=\"fissa.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"mail\"> " ;
			echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date\">";
			echo "<input type=\"submit\" value=\">>> Envoi Mail Synthèse et Rapport d'activité >>>\" ></form>";	
			}
		else
			echo "Mails déjà envoyés.";
			
		echo "<table border=\"0\" >";	
		echo "<tr> <td width=\"800\"> ";

		$r1 = command("SELECT DISTINCT pres_repas,count(*) FROM $bdd WHERE date='$date' and nom <>'Synth'  ");
		$r2=nbre_enreg($r1); 
		$r=$r2[0];

		$txt="FISSA : Rapport d'activité du $date concernant '$libelle' <br>";
		echo "<BR>$txt";

		$date=mise_en_forme_date_aaaammjj( $date);		
		
		$r1 = command("SELECT DISTINCT count(*) FROM $bdd WHERE date='$date' and nom <>'Synth' and ( pres_repas='Visite+Repas' or pres_repas='Visite' or pres_repas='Refusé' ) ");
		$r2=nbre_enreg($r1); 
		$r=$r2[0];
		
		$r1 = command("SELECT DISTINCT count(*) FROM $bdd WHERE date='$date' and nom <>'Synth' and pres_repas='Visite+Repas' ");
		$r2=nbre_enreg($r1); 
		$r3=$r2[0];
		
		echo "<BR>Aujourd'hui, $date_a_afficher, $r personnes accueillies, dont $r3 repas.<BR>";		
		
		echo "<BR><b>Synthèse : </b><BR>";
		$i=0; 
		$reponse = command("SELECT * FROM $bdd WHERE date='$date' and nom='Synth' "); 
		while (($donnees = fetch_command($reponse) ) && ($i<10000))
				{
				$c=$donnees["commentaire"];
				$c=nl2br (stripcslashes($c));
				echo "$c ";
				$i++; 		
				}
				
		echo "<BR><BR><b>$beneficiaire : </b>";	
		echo presents($date);
					
		echo "<BR><BR><b>$acteur : </b>";
		echo accueillants($date);
				
		echo "<BR><BR><b> Activités : </b> ";		
		for($i=0;$i<$imax;$i++)
			if (($nom_charge[$i]!="")  &&  ($commentaire[$i]!="")  )
				if (strpos($nom_charge[$i],"(A)"))
							{
							echo "<BR> ";
							$valeur=$nom_charge[$i];
							echo "- $valeur ";
							
							$valeur=$pres_repas[$i];
							echo "($valeur) : ";

							$valeur=stripcslashes($commentaire[$i]);
							echo " $valeur";
							}		
							
		echo "<BR><BR><b> Détails : </b> ";		
		for($i=0;$i<$imax;$i++)
			if (($nom_charge[$i]!="")  &&  ($commentaire[$i]!="")  )
				if (strpos($nom_charge[$i],"(B)")===FALSE)
					if (strpos($nom_charge[$i],"(S)")===FALSE)
						if (strpos($nom_charge[$i],"(A)")===FALSE)
							{
							echo "<BR> ";
							$valeur=$nom_charge[$i];
							echo "- $valeur ";
							
							$valeur=$pres_repas[$i];
							echo "($valeur) : ";

							$valeur=stripcslashes($commentaire[$i]);
							echo " $valeur";
							}
				
		echo "<BR><BR> </td></table>";

		}


	function mail2($dest, $titre, $contenu, $headers)
		{
		mail ( $dest , $titre, $contenu,$headers );
		return(true);
		}
	
	
	function envoi_mail($date)
		{
		global $nb_usager,$nom_charge,$commentaire,$imax, $pres_repas, $bdd, $libelle,$format_date ;

		$date_a_afficher=$date;
		
		$reponse = command("SELECT * FROM fct_fissa WHERE support='$bdd' "); 
		if ($donnees = fetch_command($reponse) )
			{
			$idx=$donnees["organisme"];
			$dest=$donnees["mails_rapports"];
			
			$reponse = command("SELECT * FROM r_organisme WHERE idx='$idx' "); 
			if ($donnees = fetch_command($reponse) )
				$mail_struct=$donnees["mail"];
						
			$reponse = command("SELECT * FROM r_user WHERE droit='R' and organisme='$idx' "); 
			while ($donnees = fetch_command($reponse) ) 
				$dest.=$donnees["mail"].",";				
			}

		$headers  = 'MIME-Version: 1.0\r\nContent-type: text/html; charset="iso-8859-1"'. "\r\n";
		$headers  .= "From: FISSA $libelle <$mail_struct>" . "\r\n"; 

		Echo "Envoi mails de la journée du $date";
		$i=0; 
			

		$date=mise_en_forme_date_aaaammjj( $date);		
		
		$r1 = command("SELECT DISTINCT count(*) FROM $bdd WHERE date='$date' and nom <>'Synth' and ( pres_repas='Visite+Repas' or pres_repas='Visite' or pres_repas='Refusé' ) ");
		$r2=nbre_enreg($r1); 
		$r=$r2[0];
		
		$r1 = command("SELECT DISTINCT count(*) FROM $bdd WHERE date='$date' and nom <>'Synth' and pres_repas='Visite+Repas' ");
		$r2=nbre_enreg($r1); 
		$r3=$r2[0];
		
		$synth ="$libelle : aujourd'hui, $date_a_afficher, $r personnes accueillies, dont $r3 repas.";
		
		Echo "<BR><BR>- Envoi Synthèse $bdd à $dest : '$synth' ";
		
		if  (mail2 ( $dest , "FISSA : Synthèse d'activité $libelle du $date_a_afficher ", "$synth", $headers   )) echo "OK"; else echo "Echec";

		$txt= $synth. "\n\nPrésents : ";	
		$txt= $txt . presents($date);
		
		$txt= $txt ."\n\nAccueillants :" ;
		$txt= $txt . accueillants($date);
		
		$i=0; 
		$txt=$txt ."\n\nSynthèse : ";
		$reponse = command("SELECT * FROM $bdd WHERE date='$date' and nom='Synth' "); 
		while (($donnees = fetch_command($reponse) ) && ($i<10000))
				{
				$c=stripcslashes($donnees["commentaire"]);
				$txt = $txt." $c" ;
				$i++; 		
				}
		
		$txt = $txt. "\n\n Détails :  ";		
		for($i=0;$i<$imax;$i++)
			if (($nom_charge[$i]!="") &&  ($commentaire[$i]!=""))
				if (!strstr($pres_repas[$i],"(B)"))
					if (!strstr($pres_repas[$i],"(S)"))
						if (!strstr($pres_repas[$i],"(A)"))
							{
							$valeur=$nom_charge[$i];
							$txt = $txt. "\n\n- $valeur :  ";
							$valeur=stripcslashes($commentaire[$i]);
							$txt = $txt. " $valeur ; ";
							}

		Echo "<BR><BR>- Envoi rapport detaillé à $dest : ";
		if  (mail2 ( $dest , "FISSA : Rapport d'activité $libelle du $date", "$txt", $headers   )) echo "OK"; else echo "Echec";
		
		$date_jour=date($format_date );
		nouveau($date_a_afficher,"Mail", "Mail","Envoyé le $date_a_afficher","");
		}
		
	function affiche_memo()
		{
		global $bdd,$format_date ;

		$date_jour=date($format_date );
		$i=0; 
		$reponse = command("SELECT * FROM $bdd WHERE commentaire<>'' and date='0000-00-00' order by nom DESC "); 
		while (($donnees = fetch_command($reponse) ) && ($i<10000))
				{
				if ($i==0)
					echo "<b>Memo: </b> ";
				$c=stripcslashes($donnees["commentaire"]);
				$n=$donnees["nom"];
				echo "<BR><a href=\"fissa.php?action=suivi&nom=$n&date_jour=$date_jour\" target=_blank> <b>$n</b> </a> : $c";
				echo "<a title=\"Suppresion mémo\"  href=\"fissa.php?action=supp_memo&nom=$n\"> <img src=\"images/croixrouge.png\"width=\"15\" height=\"15\"><a>";
				
				$i++; 		
				}
		}
		
	function histo($nom,$detail)
		{
		global $bdd;

		$i=0; 
		if ($detail=="")
			$reponse = command("SELECT * FROM $bdd WHERE nom='$nom' and date<>'0000-00-00' and pres_repas<>'pda' order by date DESC "); 
		else
			$reponse = command("SELECT * FROM $bdd WHERE nom='$nom' and date<>'0000-00-00' and pres_repas='Suivi' order by date DESC "); 
		
		while (($donnees = fetch_command($reponse) ) && ($i<10000))
				{
				$c=stripcslashes($donnees["commentaire"]);
				$d=$donnees["date"];
				$p=$donnees["pres_repas"];
				$c=nl2br ($c);
				if ($p=="Suivi") 
					echo "<BR><b>$d ($p) : $c </b>"; 
				else
					echo "<BR>$d ($p) : $c ";
				$i++; 		
				}
		
			}
			

	// -======================================================================aisie


	$format_date = "d/m/Y";
	$user_lang='fr';

	$nom_charge=array();
	$liste_nom=array();
	$pres_repas=array();
	$commentaire=array();
	$nb_usager=100;
	
	$bdd=$_SESSION['support'];
		
	// ConnexiondD
	include "connex_inc.php";
	
	$reponse = command("SELECT * FROM fct_fissa WHERE support='$bdd' "); 
	if (!($donnees = fetch_command($reponse))) 
		{
		erreur("Accès interdit.");
		}
	else
		{
		$beneficiaire=$donnees["beneficiaire"];
		if ($beneficiaire=="") $beneficiaire="Bénéficiaires";
			
		$acteur=$donnees["acteur"];
		if ($acteur=="") $acteur="Accueillants";
		
		$libelle=$donnees["libelle"];
		$logo=$_SESSION['logo'];	

		$memo=variable_s("memo");
		$action=variable_s("action");
		$pda=variable_s("pda");
		$nom=variable_s("nom");
		$com=variable_s("com");
		$nouveau=variable_s("nouveau");
		$presence=variable_s("presence");
		$type= variable_s("type");	

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
			
			if ($action=="nouveau")
				{
				if ($type=="Bénéficiaire femme")
					$nom .= " (F)";
				if ($type=="Bénévole")
					$nom .= " (B)";
				if ($type=="$acteur")
					$nom .= " (S)";
				if ($type=="Activité")
					$nom .= " (A)";		
				$com1=variable_s("commentaire");
				nouveau($date_jour,$nom, $presence,$com1,$memo);
				}

			if ($action=="chgt_nom")
				chgt_nom($nom,$nouveau);
				
			if ($action=="rapport")
				{
				charge_date($date_jour);
				rapport($date_jour);
				}

			if ($action=="supp_memo")
				{
				$nom=variable_s("nom");
				command("UPDATE $bdd set commentaire='' where nom='$nom' and date='0000-00-00' ") ;
				}
				
			if ($action=="mail")
				{
				charge_date($date_jour);
				envoi_mail($date_jour);
				}
		
		charge_nom();	
		switch ($action)
			{
					
			case "rapport":
			case "mail":
						echo "<p><a href=\"javascript:window.close();\">Fermer la fenêtre</a>"; 
						break;
						
			case "suivi":
			case "accompagnement":
			case "pda":
			
				// =====================================================================locelection
				
				
				if ($nom!="")
					{
					if ($nom!="Synth")
						{
						echo "<form method=\"GET\" action=\"fissa.php\">";
						echo "<input type=\"hidden\" name=\"action\" value=\"$action\"> " ;
						echo "<input type=\"hidden\" name=\"nom\" value=\"$nom\"> " ;
						echo "<tr> <td> ";
						echo "<input type=\"text\" name=\"date_jour\" size=\"10\" value=\"$date_jour\" class=\"calendrier\"></td>";
						echo "<td>"; 
						echo "<input type=\"submit\" value=\"Selectionner\" > </td> </form>  ";
						
						echo "<p>Suivi et Accompagnement de <b> $nom </b>";	

						if ($action!="accompagnement")
							echo "<a href=\"fissa.php?action=accompagnement&nom=$nom&date_jour=$date_jour\" > ( N'afficher que l'accompagnement )</a><p>";
						else
							echo "<a href=\"fissa.php?action=suivi&nom=$nom&date_jour=$date_jour\" > ( Afficher tout l'historique )</a><p>";
						
						}
					else
						echo "<p> Historique des faits marquants <p>";	
			
					if ($com=="")
						{
						$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour' and nom='$nom' and pres_repas='Suivi' "); 
						if ($donnees = fetch_command($reponse))
							$com=stripcslashes($donnees["commentaire"]);
						else
							$com="";
						}
					else
						{
						$com=addslashes2($com);
						$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour' and nom='$nom' and pres_repas='Suivi' "); 
						
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$com' where nom='$nom' and date='$date_jour' and pres_repas='Suivi' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom', '$date_jour', 'Suivi','$com','1')");					
						//$commentaire=$com;
						}

					if ($pda=="")
						{
						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom' and pres_repas='pda' "); 
						if ($donnees = fetch_command($reponse))
							$pda=stripcslashes($donnees["commentaire"]);
						else
							$pda="";
						}
					else
						{
						$pda=addslashes2($pda);
						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom' and pres_repas='pda' "); 
						
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$pda' where nom='$nom' and pres_repas='pda' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom', '$date_jour', 'pda','$pda','1')");					
						$pda=stripcslashes($pda);
						}					
					
					if ($nom!="Synth")
						{
						echo "<P> <table border=\"2\" >";
						echo "<tr> <td> ";
						echo "<form method=\"GET\" action=\"fissa.php\">";
						echo "<input type=\"hidden\" name=\"action\" value=\"suivi\"> " ;
						echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
						echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
						echo "<TEXTAREA rows=\"4\" cols=\"110\" name=\"com\">$com</TEXTAREA>";
						echo "</td> <td>";
						echo "<input type=\"submit\" value=\"MaJ Suivi\" >  ";
						echo "</form> ";
						echo "<tr> <td> ";
						echo "<form method=\"GET\" action=\"fissa.php\">";
						echo "<input type=\"hidden\" name=\"action\" value=\"pda\"> " ;
						echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
						echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
						echo "<TEXTAREA rows=\"4\" cols=\"110\" name=\"pda\">$pda</TEXTAREA>";
						echo "</td> <td>";
						echo "<input type=\"submit\" value=\"MaJ Plan d'action\" >  ";
						echo "</form> ";
						echo "</table>  ";
						}
					echo "<a href=\"javascript:window.close();\">Fermer la fenêtre</a>"; 
					if  (($action=="suivi") || ($action=="pda"))
						histo($nom,"");
					else
						histo($nom,"accompagnement");
					break;
					}
				
		default:
				// =====================================================================loc IMAGE
				echo "<table border=\"0\" >";	
				echo "<tr> <td> <a href=\"index.php\"> <img src=\"images/logo.png\" width=\"140\" height=\"100\"  > </a> </td> ";		

				charge_date($date_jour);

				echo "<td><table> <tr><td>  </td><td>  </td><td>  </td><td>  </td><td>  </td>";
				echo "<td><a href=\"fissa.php\"> <img src=\"images/fissa.jpg\" width=\"200\" height=\"40\"> $libelle <a> </td> <tr> <td> ";

				$i=0;
				// =====================================================================loc DATE
				echo "<td> ";
				$d3= explode("/",$date_jour);  
				$a=$d3[2];
				$m=$d3[1];
				$j=$d3[0];	
				
				$veille=date($format_date,  mktime(0,0,0 , $m, $j-1, $a));
				echo "<a href=\"fissa.php?action=date&date_jour=$veille\"> <img src=\"images/gauche.png\" width=\"20\" height=\"20\"> </a> </td> <td> ";
				echo "<form method=\"GET\" action=\"fissa.php\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"date\"> " ;	
				echo "<input type=\"text\" name=\"date_jour\" size=\"10\" value=\"$date_jour\" class=\"calendrier\" >";
				echo "<br>";
				$aujourdhui=date('d/m/Y');
				if ($date_jour!=$aujourdhui)
					echo " <a href=\"fissa.php?action=date&date_jour=$aujourdhui\"> Aujourd'jui</a>";
				
				echo "</td> <td> ";
				$jsuivant=date($format_date,  mktime(0,0,0 , $m, $j+1, $a));
				echo " <a href=\"fissa.php?action=date&date_jour=$jsuivant\"> <img src=\"images/droite.png\"  width=\"20\" height=\"20\">  </a> </td> <td> ";
				echo "<input type=\"submit\" value=\"Valider\" >  ";
				echo " </form> ";
				
				// =====================================================================loc RAPPORT
				echo "</td> <td width=\"450\"> ";
			
				echo "<ul id=\"menu-bar\">";
				echo "<li><a href=\"fissa.php?date_jour=$date_jour&action=rapport\" target=_blank>Rapport du $date_jour </a></li>";
				echo "<li><a href=\"stat.php\" target=_blank>Statistiques</a>";
				echo "<li><a href=\"index.php?action=dx\">Deconnexion</a>";
				echo "</ul> </td>";
				
				echo "</td> </table> ";
			
				if ($logo!="")
					echo "<td> <a href=\"fissa.php\"> <img src=\"images/$logo\" width=\"200\" height=\"100\"  > </a> </td>";
				
				echo "</table> <P> ";
				
				// =====================================================================loc Liste présents
				echo "<table border=\"2\" >";
				
				// =====================================================================loc AJOUTER

				proposition("","Ajout rapide");
				proposition("(S)",$acteur);	
				proposition("(B)","Bénévoles");				
				proposition("(A)","Activités");					
				echo "  </table> <P> ";
				
				if ($date_jour==date('d/m/Y'))
					affiche_memo();
					
				echo "<table border=\"2\" >";
				
				// =====================================================================loc AJOUTER
				echo "<form method=\"GET\" action=\"fissa.php\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
				echo "<input type=\"hidden\" name=\"femme\" value=\"\"> " ;
				echo "<input type=\"hidden\" name=\"memo\" value=\"\"> " ;	
				echo "<input type=\"hidden\" name=\"commentaire\" value=\"\"> " ;
				echo "<input type=\"hidden\" name=\"presence\" value=\"Visite\"> " ;	
				echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
				echo "<tr> <td bgcolor=\"#d4ffaa\"> ";
				echo "<SELECT name=nom>";
				echo "<OPTION  VALUE=\"\">  </OPTION>";
				for ($j=0;$j<$jmax;$j++)
					{
					$sel=$liste_nom[$j];
					if ($sel!= "Mail") 
						echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
					}
				echo "</SELECT>";
				echo "</td> <td bgcolor=\"#d4ffaa\">"; 
				echo "<input type=\"submit\" value=\"Ajouter\" >  ";
				echo "</td>";
	//			<td></td>";
				echo " </form> ";	
				// =====================================================================loc NOUVEAU
				echo "<td bgcolor=\"#d4ffaa\"></td> <td bgcolor=\"#d4ffaa\"><form method=\"GET\" action=\"fissa.php\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
				echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
				echo "<input type=\"hidden\" name=\"memo\" value=\"\"> " ;	
				echo "<input type=\"text\" name=\"nom\" size=\"30\" value=\"\">";	
				liste_type();
				echo "<input type=\"hidden\" name=\"commentaire\" value=\"\"> " ;	
				echo "<input type=\"hidden\" name=\"presence\" value=\"Visite\"> " ;
				echo "<input type=\"submit\" value=\"Créer Nouveau\" >  ";
				echo "</td></form> ";	

				echo "<tr> <td bgcolor=\"#3f7f00\"><font color=\"white\"> Prénom / Nom </td> <td bgcolor=\"#3f7f00\"> <font color=\"white\">Evénement </td>";
				echo "<td bgcolor=\"#3f7f00\"> <font color=\"white\">Memo </td><td bgcolor=\"#3f7f00\"> <font color=\"white\">Commentaire </font></td>";		
				$ncolor=0;
				for($i=0;$i<$imax;$i++)
					if ($nom_charge[$i]!="")
						{
						if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
						echo "<tr> <td bgcolor=\"$color\"> ";
						echo "<form method=\"GET\" action=\"fissa.php\">";
						echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
						echo "<input type=\"hidden\" name=\"femme\" value=\"\"> " ;
						echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
						$nom1=$nom_charge[$i];
					
						echo "<input type=\"hidden\" name=\"nom\" size=\"20\" value=\"$nom1\">";
						echo "<a href=\"fissa.php?action=suivi&nom=$nom1&date_jour=$date_jour\" target=_blank> <b>$nom1</b> </a></td>";
						$valeur=$pres_repas[$i];
						if (($nom1!= "Mail") && ($valeur!="Atelier") )
							liste_presence($valeur, $nom_charge[$i], $color);
						else
							echo "</td> <input type=\"hidden\" name=\"presence\" value=\"$valeur\"> <td bgcolor=\"$color\">";
						echo "</td> <td bgcolor=\"$color\">";
						$reponse = command("SELECT * FROM $bdd where date='0000-00-00' and nom='$nom1' "); 
						if ($donnees = fetch_command($reponse) )
							$n=stripcslashes($donnees["commentaire"]);	
						else
						  $n="";
						  
						echo "<TEXTAREA rows=\"1\" cols=\"20\" name=\"memo\" onChange=\"this.form.submit();\">$n</TEXTAREA>";
						echo "</td> <td bgcolor=\"$color\"> ";

						$valeur=stripcslashes($commentaire[$i]);
						echo "<TEXTAREA rows=\"1\" cols=\"60\" name=\"commentaire\"onChange=\"this.form.submit();\" >$valeur</TEXTAREA>";
						echo " </form> ";
						echo "</td>";

						}
				echo "</table> ";
				// =====================================================================locYNTHESE
				
				echo "<font size=\"2\">Rappel CNIL: \"les informations personnelles enregistrées doivent être «adéquates, pertinentes et non excessives au regard des finalités pour lesquelles elles sont collectées (article 6-3°)\"</font>";
				echo "<table border=\"2\" >";
				echo "<tr> <td> ";
				echo "<form method=\"GET\" action=\"fissa.php\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
				echo "<input type=\"hidden\" name=\"nom\"  value=\"Synth\">";
				echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
				echo "<input type=\"hidden\" name=\"femme\" value=\"\"> " ;
				echo "<input type=\"hidden\" name=\"presence\"  value=\"Synth\">";
				if ($synth=="") 
					$synth="Faits marquants:\n ";
				else 
					$synth=stripcslashes($synth);
				echo "<TEXTAREA rows=\"8\" cols=\"110\" name=\"commentaire\">$synth</TEXTAREA>";
				echo "</td> <td>";
				echo "<input type=\"submit\" value=\"Maj Synthése\" >";
				echo "<p><p><a href=\"fissa.php?action=suivi&nom=Synth\" target=_blank> Historique </a>";
				echo "</form> ";
				echo "</table>  ";


				// =====================================================================loc CHANGEMENT NOM
				echo "<P> <table border=\"0\" ><tr> <td>";
				echo "<form method=\"GET\" action=\"fissa.php\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"chgt_nom\"> " ;
				echo "<SELECT name=nom>";
				echo "<OPTION  VALUE=\"\">  </OPTION>";
				for ($j=0;$j<$jmax;$j++)
					{
					$sel=$liste_nom[$j];
					if ($sel!= "Mail") 
						echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
					}
				echo "</SELECT>";
				echo "</td> <td> à tranformer en </td> <td>";
				echo "<input type=\"text\" name=\"nouveau\" size=\"20\" value=\"\">";	
				echo "<input type=\"submit\" value=\"MaJ nom\" >  ";
				echo " </form> ";
				echo "</table>  ";
				
					// =====================================================================loc Histo
				echo "<P> <table border=\"0\" ><tr> <td>";
				echo "<form method=\"GET\" action=\"fissa.php\" target=_blank>";
				echo "<input type=\"hidden\" name=\"action\" value=\"suivi\"> " ;
				echo "<SELECT name=nom>";
				echo "<OPTION  VALUE=\"\">  </OPTION>";
				for ($j=0;$j<$jmax;$j++)
					{
					$sel=$liste_nom[$j];
					if ($sel!= "Mail") 
						echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
					}
				echo "</SELECT>";
				echo "<input type=\"submit\" value=\"Historique\" >  ";
				echo "</form> ";
				echo "</table>  ";
				break;
		}
	}
	
	pied_de_page();
		?>
	
    </body>
</html>
