<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0trict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

    <head>
	 <?php
include 'calendrier.php';
include 'general.php';
include 'inc_style.php';	 

	if (!$_SESSION['pass'])
		echo "<meta http-equiv=\"refresh\" content=\"0; url=index.php\" />";
	
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
		$_SESSION['LAST_ACTIVITY'] = time();
		
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
			$exclus.="'".addslashes2($nom_charge[$i])."',";
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
				$liste_nom[$j]=mef_texte_a_afficher( stripcslashes($donnees["nom"]) );
				$j++; 		
				}
		$jmax=$j;
		}	
	
	// =========================================================== procedures générales

	
	function nouveau( $date_jour, $nom, $pres,$com ,$memo)
		{
		global $bdd;
		$nom=str_replace("\"","",$nom);
		$nom_slash= addslashes2($nom);	
		
		if ($nom!="")
			{
			$d=mise_en_forme_date_aaaammjj( $date_jour);
			
			$r1 = command("SELECT DISTINCT count(*) FROM $bdd WHERE date='0000-00-00' and nom='$nom_slash' ");
			$r2=nbre_enreg($r1); 
			if ($r2[0]==0)
				{
				$user= $_SESSION['user_idx'];
				$modif=time();
				$cmd = "INSERT INTO `$bdd`  VALUES ( '$nom', '', '','','$user','$modif')";
				$reponse = command($cmd);
				}
				
			$r1 = command("SELECT  count(*) FROM $bdd WHERE date='$d' and nom='$nom_slash' ");
			$r2=nbre_enreg($r1); 
			$r=$r2[0];	
			$com= addslashes2($com);
			$nom= addslashes2($nom);
			$memo= addslashes2($memo);
			$user= $_SESSION['user_idx'];
			$modif=time();
			if ($r==0)
				{
				if ( $pres!="Erreur saisie")
					{
					if  (strstr($nom,"(A)") && ($pres=="Visite")) 
						$pres="Activité";
					
					if ( ((strstr($nom,"(B)")) || (strstr($nom,"(S)")) ) && ($pres=="Visite") )		
						$pres="Acteur Social";

					$cmd = "INSERT INTO `$bdd`  VALUES ( '$nom', '$d', '$pres','$com','$user','$modif')";
					$reponse = command($cmd);
					}
				}
			else
				{
				$cmd = "UPDATE $bdd set commentaire='$memo' , user='$user' , modif='$modif' where nom='$nom' and date='0000-00-00' ";
				$reponse = command($cmd);					

				if ( $pres!="Erreur saisie")
					$cmd="UPDATE $bdd SET commentaire='$com', pres_repas='$pres' , user='$user' , modif='$modif'  WHERE nom='$nom_slash' AND date='$d'" ;
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
			$user= $_SESSION['user_idx'];
			$modif=time();
			$nom_slash= addslashes2($nom);	
			$cmd="UPDATE $bdd SET nom='$nouveau' , user='$user', modif='$modif' WHERE nom='$nom_slash' " ;
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
		$crit_bene="  ( not (nom like '%(B)%')) and ( not (nom like '%(S)%')) and ( not (nom like '%(A)%')) and (nom<>'Synth') and (nom<>'Mail') and (pres_repas<>'Pour info')  ";

		$reponse = command("SELECT * FROM $bdd WHERE date='$date' and $crit_bene group by nom ASC"); 
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
		$crit_AS=" (nom like '%(B)%' or nom like '%(S)%' )";
		$reponse = command("SELECT * FROM $bdd WHERE date='$date' and $crit_AS group by nom ASC");  
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
								echo "<tr> <td width=\"1000\"><hr> $titre: ";
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

	function mail2($dest, $titre, $contenu, $libelle, $mail_struct )
		{
		
		// Entete
		$headers  = "MIME-Version: 1.0 \n"; 
		$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
        $headers .='Content-Transfer-Encoding: 8bit'."\n";			

		$headers  .= "From: FISSA $libelle <$mail_struct>" . "\r\n"; 
		
		// mise en forme HTML
		$contenu = "<html><body>$contenu</body></html>";	
		$contenu = '=?iso-8859-1?B?'.base64_encode($contenu).'?=';
		
		mail ( $dest , $titre, $contenu,$headers );
		return(true);
		}
	
	
	function rapport_mail2($date, $envoi_mail)
		{
		global $nb_usager,$nom_charge,$commentaire,$imax, $pres_repas, $bdd, $libelle,$format_date ;

		$date_a_afficher=$date;
		
		$reponse = command("SELECT * FROM fct_fissa WHERE support='$bdd' "); 
		if ($donnees = fetch_command($reponse) )
			{
			$idx=$donnees["organisme"];
			$dest=$donnees["mails_rapport_detaille"].",";
			$dest_synthese=$donnees["mails_rapports"].",";
			
			$reponse = command("SELECT * FROM r_organisme WHERE idx='$idx' "); 
			if ($donnees = fetch_command($reponse) )
				{
				$mail_struct=$donnees["mail"];
				$dest_synthese.=$mail_struct.",";
				}
				
			$reponse = command("SELECT * FROM r_user WHERE droit='R' and organisme='$idx' "); 
			while ($donnees = fetch_command($reponse) ) 
				$dest.=$donnees["mail"].",";				
			}

		if (!$envoi_mail)
			{
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
			}
		else
			Echo "Envoi mails de la journée du $date";
			
		echo "<a href=\"javascript:window.close();\">Fermer la fenêtre</a>"; 
		$i=0; 
			
		$date=mise_en_forme_date_aaaammjj( $date);		
		
		$r1 = command("SELECT DISTINCT count(*) FROM $bdd WHERE date='$date' and nom <>'Synth' and ( pres_repas='Visite+Repas' or pres_repas='Visite' or pres_repas='Refusé' ) ");
		$r2=nbre_enreg($r1); 
		$r=$r2[0];
		
		$r1 = command("SELECT DISTINCT count(*) FROM $bdd WHERE date='$date' and nom <>'Synth' and pres_repas='Visite+Repas' ");
		$r2=nbre_enreg($r1); 
		$r3=$r2[0];
		
		$synth ="<b>$libelle</b> : aujourd'hui, $date_a_afficher, $r personnes accueillies, dont $r3 repas.";		
		
		if ( $envoi_mail)
			{
			Echo "<BR><BR>- Envoi Synthèse $bdd à '$dest_synthese' ";
			
			if  (mail2 ( $dest_synthese , "FISSA : Synthèse d'activité $libelle du $date_a_afficher ", "$synth",  $libelle, $mail_struct   )) 
				 {
				 echo "OK"; 
				 ajout_log_tech( "FISSA : Envoi Synthèse $bdd du $date à $dest_synthese ");
				 }
			else echo "Echec";
			}

		$txt= $synth. "<BR><BR><b>Présents</b> : ";	
		$txt= $txt . presents($date);
		
		$acc=accueillants($date);
		if ($acc!="")
			{
			$txt= $txt ."<BR><BR><b>Accueillants</b> :" ;
			$txt= $txt . $acc ;
			}
	
		$flag=false;
		for($i=0;$i<$imax;$i++)
			if ($nom_charge[$i]!="")   
				if (strpos($nom_charge[$i],"(A)"))
					{
					if (!$flag)
						{
						$txt= $txt ."<BR><BR><b> Activités</b>  : ";
						$flag=true;
						}
					$valeur=$nom_charge[$i];
					$txt= $txt . " $valeur, ";
					}			
		$i=0; 
		$reponse = command("SELECT * FROM $bdd WHERE date='$date' and nom='Synth' "); 
		while (($donnees = fetch_command($reponse) ) && ($i<10000))
				{
				$c=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));
				$txt=$txt ."<BR><BR><b>Synthèse</b> :  $c";
				$i++; 		
				}
				
		$flag=false;		
		for($i=0;$i<$imax;$i++)
			if (($nom_charge[$i]!="") &&  ($commentaire[$i]!=""))
				{
				if (!$flag)
					{
					$txt = $txt. "<BR><BR><b>Détails</b> :  ";		
					$flag=true;
					}
				$valeur=$nom_charge[$i];
				$txt = $txt. "<BR>- $valeur :  ";
				$valeur=mef_texte_a_afficher( stripcslashes($commentaire[$i]));
				$txt = $txt. " $valeur ; ";
				}

		$txt = $txt. "<center><a href=\"https://doc-depot.com\">
								<img src=\"http://doc-depot.com/images/logo.png\" width=\"75\" height=\"50\" ></a>\"".traduire('La Consigne Numérique Solidaire').'"';		

		$txt = $txt. "<a href=\"https://doc-depot.com\"><img src=\"http://doc-depot.com/images/fissa.jpg\" width=\"75\" height=\"50\" ></a>".traduire("Suivi Simplifé d'Activité");						
		$txt = $txt." - ". traduire("Services fournis par ")."<a href=\"https://adileos.doc-depot.com\"><img src=\"http://doc-depot.com/images/adileos.jpg\" width=\"150\" height=\"25\" ></a>";						
		
		
		if ( $envoi_mail)
			{
			Echo "<BR><BR>- Envoi rapport detaillé à $dest : ";
			if  (mail2 ( $dest , "FISSA : Rapport d'activité $libelle du $date", "$txt", $libelle, $mail_struct  )) 
				 {
				 echo "OK"; 
				 ajout_log_tech( "FISSA : Envoi rapport detaillé $bdd du $date à $dest ");
				 }
			 else echo "Echec";
			
			$date_jour=date($format_date );
			nouveau($date_a_afficher,"Mail", "Mail","Envoyé le $date_a_afficher","");
			}
		else
			{
			echo "<hr>";

			}
		echo $txt;
		if ( !$envoi_mail)		
			{
			echo "<hr>";
			echo "Liste de diffusion (en plus des responsables)";
			$reponse = command("SELECT * FROM fct_fissa WHERE support='$bdd' "); 
			if ($donnees = fetch_command($reponse) )
				{
				$dest2=$donnees["mails_rapport_detaille"];
				$dest=$donnees["mails_rapports"];
				}
			echo "<div class=\"CSSTableGenerator\" ><table><tr><td > ".traduire('Rapport')."  </td><td> ".traduire('Destinataires')." </td>";
			echo "<tr><td > ".traduire('Synthèse')."  </td><td>$dest</td>";
			echo "<tr><td > ".traduire('Détaillé')."  </td><td>$dest2</td>";
			echo "</table></div>";
			echo "<a href=\"fissa.php?action=modif_liste_mail\">".traduire("Modifier les listes")."</a>";						
			}
		
		}
		
		
	function modification_liste_mail()
		 {
		 global $action,$bdd;

		 $liste_finale="";
		 $liste= str_replace(";",",",variable("liste_mail"));
		 $liste= str_replace(" ","",$liste);
		 $liste= str_replace('\\\\r',",",$liste);
		 $liste= str_replace('\\\\n',"",$liste);
		 $mail = explode(",",$liste); 
		 $i=0;
		 while ((isset ($mail[$i])) && ($i<20) ) 
			{
			$m=$mail[$i];
			if ($m!="")
				{
				if ( !VerifierAdresseMail($m) )
					erreur ("$m : adresse incorrecte; ");
				else
					$liste_finale.="$m,";
				}
			$i++;
			}
			
		if ($action=="mail_detail") 
			command("UPDATE fct_fissa set mails_rapport_detaille='$liste_finale' where support='$bdd' ") ;
		else
			command("UPDATE fct_fissa set mails_rapports='$liste_finale' where support='$bdd' ") ;

		 }
		 
		 
	function modif_liste_mail()
			{
			global $bdd;
			
			echo "Liste de diffusion (utilisez ',' ou ';' comme séparateur entre chaque adresse mail; 20 destinataires max par liste)";
			$reponse = command("SELECT * FROM fct_fissa WHERE support='$bdd' "); 
			if ($donnees = fetch_command($reponse) )
				{
				$dest=$donnees["mails_rapport_detaille"];
				$dest_synthese=$donnees["mails_rapports"];
				}
			echo "<div class=\"CSSTableGenerator\" ><table><tr><td > ".traduire('Rapport')."  </td><td> ".traduire('Destinataires')." </td>";
			echo "<tr><td > ".traduire('Synthèse')."  </td> ";
			echo "<td><form method=\"POST\" action=\"fissa.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"mail_synth\"> " ;
			echo "<TEXTAREA rows=\"4\" cols=\"110\" name=\"liste_mail\" onChange=\"this.form.submit();\">$dest_synthese</TEXTAREA>";
			echo "</td> </form> ";
			
			echo "<tr><td > ".traduire('Détaillé')."  </td>";
			echo "<td><form method=\"POST\" action=\"fissa.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"mail_detail\"> " ;
			echo "<TEXTAREA rows=\"4\" cols=\"110\" name=\"liste_mail\" onChange=\"this.form.submit();\">$dest</TEXTAREA>";
			echo "</td> </form> ";
			echo "</table></div>";
			echo "<p><a href=\"javascript:window.close();\">Fermer la fenêtre</a>"; 

			}
			
	function redacteur($date)
		{
		global $bdd;

		$ncolor=0;
		Echo '<h2>'.traduire("Rédacteurs et dates des informations concernant la journée du"). " $date</h2><p>";
		echo "<a href=\"javascript:window.close();\">Fermer la fenêtre</a>"; 
		echo "<table border=\"1\"><tr> <td bgcolor=\"#3f7f00\"><font color=\"white\"> Nom </td> <td bgcolor=\"#3f7f00\"> </td> <td bgcolor=\"#3f7f00\"> <font color=\"white\">Commentaire</td> <td bgcolor=\"#3f7f00\"><font color=\"white\">Rédacteur </td> <td bgcolor=\"#3f7f00\"><font color=\"white\">Date Heure</td></font>";
		
		$d=mise_en_forme_date_aaaammjj( $date);
		$reponse = command("SELECT DISTINCT * FROM $bdd WHERE date='$d' or (date='0000-00-00' and commentaire<>'') "); 
		while ($donnees = fetch_command($reponse) ) 
			{
			if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
			$nom_charge=stripcslashes($donnees["nom"]);
			$pres_repas=$donnees["pres_repas"];
			$commentaire=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));
			$user="";
			if ($donnees["user"]!="")
				$user=libelle_user($donnees["user"]);
			$modif="";
			if ($donnees["modif"]!="")
				$modif=date ("d/m/Y H:i",$donnees["modif"]);
			echo "<tr> <td bgcolor=\"$color\"> $nom_charge</td> <td bgcolor=\"$color\">$pres_repas</td> <td bgcolor=\"$color\">$commentaire</td> <td bgcolor=\"$color\">$user</td> <td bgcolor=\"$color\">$modif</td>";
			}
		echo "</table>";
		echo "<a href=\"javascript:window.close();\">Fermer la fenêtre</a>"; 
		}
		
	function affiche_memo()
		{
		global $bdd,$format_date ;
		
		$date_jour=date($format_date );
		$i=0; 
		$reponse = command("SELECT * FROM $bdd WHERE commentaire<>'' and date='0000-00-00' and pres_repas<>'pda' order by nom DESC "); 
		while (($donnees = fetch_command($reponse) ) && ($i<10000))
				{
				if ($i==0)
					echo "<b>Memo: </b> ";
				$c=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]) );
				$n=$donnees["nom"];
				echo "<BR><a href=\"fissa.php?action=suivi&nom=$n&date_jour=$date_jour\" target=_blank> <b>$n</b> </a> : $c";
				echo "<a title=\"Suppresion mémo\"  href=\"fissa.php?action=supp_memo&nom=$n\"> <img src=\"images/croixrouge.png\"width=\"15\" height=\"15\"><a>";
				
				$i++; 		
				}
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
					echo "<tr><td bgcolor=\"$color\"><b>$d  </td><td bgcolor=\"$color\">$p </td><td bgcolor=\"$color\">$c </b>"; 
				else
					echo "<tr><td bgcolor=\"$color\">$d </td><td bgcolor=\"$color\">$p </td><td bgcolor=\"$color\"> $c </td> ";
				$i++; 		
				}
		echo "</table>";
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
	if ((!($donnees = fetch_command($reponse))) || (!$_SESSION['pass']) )
		{
		echo "<a href=\"https://doc-depot.com\">retour sur page d'accueildoc-depot.com</a>";
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
				rapport_mail2($date_jour, false);
				}
				
			if ($action=="redacteur")
				{
				redacteur($date_jour);
				}

			if ($action=="supp_memo")
				{
				$nom=variable_s("nom");
				$user= $_SESSION['user_idx'];
				$modif=time();
				$nom_slash= addslashes2($nom);	
				command("UPDATE $bdd set commentaire='', user='$user' , modif='$modif' where nom='$nom_slash' and date='0000-00-00' ") ;
				}
				
			if ($action=="mail")
				{
				charge_date($date_jour);
				rapport_mail2($date_jour, true);
				}	

			if ($action=="modif_liste_mail")
				{
				modif_liste_mail();
				}

			if ( ($action=="mail_detail") ||($action=="mail_synth") )
				{
				modification_liste_mail();
				modif_liste_mail();
				}	


		ajout_log_jour("----------------------------------------------------------------------------------- [ FISSA = $action ] $date_jour ");
		
		charge_nom();	
		switch ($action)
			{
			case "rapport":
			case "mail":
			case "redacteur":
			case "modif_liste_mail":
			case "mail_detail":
			case "mail_synth":
						break;
						
			case "suivi":
			case "accompagnement":
			case "pda":
			
				// =====================================================================locelection
				
				$nom_slash= addslashes2($nom);	
				
				if ($nom!="")
					{
					echo "<table><tr> <td width=\"60%\">";
					echo "<ul id=\"menu-bar\">";					
					if ($nom!="Synth")
						{
						echo "<li><a href=\"\" >Suivi et Accompagnement de <b> $nom </b> </a></li>";
						echo "</ul> </td><td>";
						echo "<form method=\"GET\" action=\"fissa.php\">";
						echo "<input type=\"hidden\" name=\"action\" value=\"$action\"> " ;
						echo "<input type=\"hidden\" name=\"nom\" value=\"$nom\"> " ;
						echo "<input type=\"text\" name=\"date_jour\" size=\"10\" value=\"$date_jour\" class=\"calendrier\"></td>";
						echo "<td>"; 
						echo "<input type=\"submit\" value=\"Selectionner\" > </td> </form>";
						}
					else
						{
						echo "<li><a href=\"\" >Historique des synthèses </b> </a></li>";
						echo "</ul> </td>";
						}
					echo "<td> - <a href=\"javascript:window.close();\">Fermer la fenêtre</a></td>"; 
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
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '$date_jour_gb', 'Suivi','$com','$user','$modif')");					
						//$commentaire=$com;
						}

					if ($pda=="")
						{
						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='pda' "); 
						if ($donnees = fetch_command($reponse))
							$pda=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));
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
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '', 'pda','$pda','$user','$modif')");					
						$pda=stripcslashes($pda);
						}					
					
					if ($nom!="Synth")
						{
						echo "<TABLE><TR> <td></td><td > <div class=\"CSS_titre\"  >";

						echo "<table border=\"0\" >";
						echo "<tr> <td> Suivi : ";
						echo "<form method=\"GET\" action=\"fissa.php\">";
						echo "<input type=\"hidden\" name=\"action\" value=\"suivi\"> " ;
						echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
						echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
						echo "<TEXTAREA rows=\"4\" cols=\"110\" name=\"com\" onChange=\"this.form.submit();\">$com</TEXTAREA>";
						echo "</td> ";
						echo "</form> ";
						echo "<tr> <td> Plan d'action :  ";
						echo "<form method=\"GET\" action=\"fissa.php\">";
						echo "<input type=\"hidden\" name=\"action\" value=\"pda\"> " ;
						echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
						echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
						echo "<TEXTAREA rows=\"4\" cols=\"110\" name=\"pda\" onChange=\"this.form.submit();\">$pda</TEXTAREA>";
						echo "</td> ";
						echo "</form> ";
						echo "</table>  ";
						fin_cadre();

						if ($action!="accompagnement")
								echo "<a href=\"fissa.php?action=accompagnement&nom=$nom&date_jour=$date_jour\" > ( N'afficher que l'accompagnement )</a>";
							else
								echo "<a href=\"fissa.php?action=suivi&nom=$nom&date_jour=$date_jour\" > ( Afficher tout l'historique )</a>";
						}						

					if  (($action=="suivi") || ($action=="pda"))
						histo($nom,"");
					else
						histo($nom,"accompagnement");
					break;
					}
				
		default:
				// =====================================================================loc IMAGE
				$d3= explode("/",$date_jour);  
				$a=$d3[2];
				$m=$d3[1];
				$j=$d3[0];	
				echo "<table border=\"0\" >";	
				echo "<tr> <td> <a href=\"fissa.php\"> <img src=\"images/fissa.jpg\" width=\"140\" height=\"100\"  ></a></td> ";		
				charge_date($date_jour);
				$i=0;

				// =====================================================================loc RAPPORT
				echo "<td width=\"450\"><center>";

				echo "<ul id=\"menu-bar\">";
				echo "<li><a href=\"fissa.php?date_jour=$date_jour&action=rapport\" target=_blank>Rapport du $date_jour </a></li>";
				echo "<li><a href=\"stat.php\" target=_blank>Statistiques</a>";
				echo "<li><a href=\"index.php?action=dx\">Deconnexion</a>";
				echo "</ul> ";
				
				echo " <table> <tr> <td><b>$libelle</b> : </td>";

				echo "<td> ";
			
				$veille=date($format_date,  mktime(0,0,0 , $m, $j-1, $a));
				echo "<td><a href=\"fissa.php?action=date&date_jour=$veille\"> <img src=\"images/gauche.png\" width=\"20\" height=\"20\"> </a> </td> <td> ";
				echo "<form method=\"GET\" action=\"fissa.php\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"date\"> " ;	
				echo "<input type=\"text\" name=\"date_jour\" size=\"10\" value=\"$date_jour\" class=\"calendrier\" >";
				$aujourdhui=date('d/m/Y');
				if ($date_jour!=$aujourdhui)
					echo "<br><a href=\"fissa.php?action=date&date_jour=$aujourdhui\">Aujourd'jui</a>";
				$jsuivant=date($format_date,  mktime(0,0,0 , $m, $j+1, $a));
				echo "</td> <td> <a href=\"fissa.php?action=date&date_jour=$jsuivant\"> <img src=\"images/droite.png\"  width=\"20\" height=\"20\">  </a> </td> <td> ";
				echo "<input type=\"submit\" value=\"Valider\" >  ";
				echo " </td></form> ";	
				echo "<br>";
			
				echo "</td></table>";

				echo "</td>";
				echo "<td><a href=\"index.php\"> <img src=\"images/logo.png\" width=\"70\" height=\"50\"><a></td>";			
				if ($logo!="")
					echo "<td> <a href=\"fissa.php\"> <img src=\"images/$logo\" width=\"200\" height=\"100\"  > </a> </td>";
				
				echo "</table> ";
				
				// =====================================================================loc Liste présents
				echo "<TABLE><TR> <td></td><td > <div class=\"CSS_titre\"  >";
				echo "<table border=\"0\" >";
				
				// =====================================================================loc AJOUTER

				proposition("","Ajout rapide");
				proposition("(S)",$acteur);	
				proposition("(B)","Bénévoles");				
				proposition("(A)","Activités");					
				echo "  </table> <P> ";
				fin_cadre();

				if ($date_jour==date('d/m/Y'))
					affiche_memo();
					
				echo "<table id=\"dujour\"  border=\"2\" >";
				
				// =====================================================================loc AJOUTER
				echo "<form method=\"GET\" action=\"fissa.php#dujour\">";
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
						echo "<tr id=\"E$i\" > <td bgcolor=\"$color\"> ";
						echo "<form method=\"GET\" action=\"fissa.php#E$i\">";
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
						$nom_slash1= addslashes2($nom1);	

						$reponse = command("SELECT * FROM $bdd where date='0000-00-00' and nom='$nom_slash1' "); 
						if ($donnees = fetch_command($reponse) )
							$n=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));	
						else
						  $n="";
						  
						echo "<TEXTAREA rows=\"1\" cols=\"20\" name=\"memo\" onChange=\"this.form.submit();\">$n</TEXTAREA>";
						echo "</td> <td bgcolor=\"$color\"> ";

						$valeur=mef_texte_a_afficher( stripcslashes($commentaire[$i]));
						echo "<TEXTAREA rows=\"1\" cols=\"60\" name=\"commentaire\" onChange=\"this.form.submit();\" >$valeur</TEXTAREA>";
						echo " </form> ";
						echo "</td>";

						}
				echo "</table> ";
				// =====================================================================locYNTHESE
				
				echo "<font size=\"2\">Rappel CNIL: \"les informations personnelles enregistrées doivent être «adéquates, pertinentes et non excessives au regard des finalités pour lesquelles elles sont collectées (article 6-3°)\"</font>";
				echo "<TABLE><TR> <td></td><td > <div class=\"CSS_titre\"  >";
				echo "<table id=\"synthese\"  border=\"0\" >";
				echo "<tr> <td> <a href=\"fissa.php?action=suivi&nom=Synth\" target=_blank> Synthèse de la journée </a>";
				echo "<form method=\"GET\" action=\"fissa.php#synthese\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
				echo "<input type=\"hidden\" name=\"nom\"  value=\"Synth\">";
				echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
				echo "<input type=\"hidden\" name=\"femme\" value=\"\"> " ;
				echo "<input type=\"hidden\" name=\"presence\"  value=\"Synth\">";
				if ($synth=="") 
					$synth="Faits marquants:\n ";
				else 
					$synth=mef_texte_a_afficher(  stripcslashes($synth) );
				echo "<TEXTAREA rows=\"6\" cols=\"120\" name=\"commentaire\" onChange=\"this.form.submit();\" >$synth</TEXTAREA>";
				echo "</td> ";
				echo "</form> ";
				echo "</table>  ";
				fin_cadre();

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
				echo "</td> <td> à transformer en </td> <td>";
				echo "<input type=\"text\" name=\"nouveau\" size=\"20\" value=\"\">";	
				echo "<input type=\"submit\" value=\"MaJ nom\" >  ";
				echo " </form> ";
				echo "</table>  ";
				
					// =====================================================================loc Histo
				echo "<P> <table border=\"0\" ><tr> <td>Consulter l'historique de </td><td>";
				echo "<form method=\"GET\" action=\"fissa.php\" target=_blank>";
				echo "<input type=\"hidden\" name=\"action\" value=\"suivi\"> " ;
				echo "<SELECT name=nom onChange=\"this.form.submit();\">";
				echo "<OPTION  VALUE=\"\">  </OPTION>";
				for ($j=0;$j<$jmax;$j++)
					{
					$sel=$liste_nom[$j];
					if ($sel!= "Mail") 
						echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
					}
				echo "</SELECT>";
				echo "</form>  </td>";
				echo "</table>  ";
				
				echo "<br><a href=\"fissa.php?action=redacteur&date_jour=$date_jour\"  target=_blank >Visualisation des rédacteurs</a>";
				
				break;
		}
	}
	
	pied_de_page();
		?>
	
    </body>
</html>
