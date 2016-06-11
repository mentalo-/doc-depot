<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0trict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<?php include 'header.php';	  ?>
    <head>
	 <?php
include 'calendrier.php';
include 'general.php';
include 'inc_style.php';	 
include 'include_mail.php';	 

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
	// ===============================================================================================
	// transforme la liste d'activité stockées en table en une liste affichable avec lien pour supprimer
	function mef_activites($act,$nom,$date)
		{
		$ret="";
		$d3=explode('#-#',$act);
		$i=0;
		while (isset($d3[$i]))
			{
			if ($d3[$i]!="")
				{
				$a=str_replace ('(A)','',$d3[$i]);
				$ret.=$a;
				$ret.="<a title=\"Suppresion $a\"  href=\"fissa.php?action=supp_activite&idx=$i&nom=$nom&date_jour=$date\"> <img src=\"images/croixrouge.png\"width=\"15\" height=\"15\"><a>";
				$ret.="; ";
				}
			
			$i++;
			}
		return $ret;
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
		
	function nbre_participants_activite( $act, $date )
		{
		global $bdd;
		
		$ret=0;
		
		$reponse = command("SELECT * FROM $bdd WHERE date='$date' and (activites like '%$act%') "); 
		while ($donnees = fetch_command($reponse) )
			$ret++;
		return($ret);
		}
	// ---------------------------------------------------------------------------------------	
	// charge à chaque fois les données du jours 
	// et définit la liste des noms à exclure des propositions
	// $imax est la valeur max chargée
	function charge_date($date_jour)
		{
		global $exclus, $imax, $nom_charge, $pres_repas, $nb_usager,$commentaire, $synth,$bdd,$select_activites,$activites, $qte;

		for($i=0;$i<$nb_usager;$i++) 
			{
			$nom_charge[$i]="";
			$pres_repas[$i]="";
			$commentaire[$i]="";
			$activites[$i]="";
			$qte[$i]=0;
			}
		$i=0; 
		$select_activites="";
		
		$d=mise_en_forme_date_aaaammjj( $date_jour);
		$reponse = command("SELECT DISTINCT * FROM $bdd WHERE date='$d' and pres_repas!='Suivi' and pres_repas!='reponse' and pres_repas!='partenaire' and pres_repas!='Age'  and pres_repas!='Telephone' and pres_repas!='Mail' order by nom ASC "); 
		while (($donnees = fetch_command($reponse) ) && ($i<10000))
			if ($donnees["nom"]!="Synth")
				{
				$nom_charge[$i]=$donnees["nom"];
				if (strpos($nom_charge[$i],"(A)")>0)
					$select_activites.="<OPTION  VALUE=\"".$nom_charge[$i]."\"> ".$nom_charge[$i]." </OPTION>";
				$pres_repas[$i]=$donnees["pres_repas"];
				$commentaire[$i]=$donnees["commentaire"];
				$activites[$i]=$donnees["activites"];
				$qte[$i]=$donnees["qte"];
				$i++; 		
				}
			else
				$synth=$donnees["commentaire"];
		$imax=$i;
		
		$exclus="";
		for ($i=0; $i<$imax; $i++)
			$exclus.="'".addslashes2($nom_charge[$i])."',";
		if ($exclus<>"")
			$exclus=  " and nom NOT IN (".$exclus." '' ) " ;
		}
	
	//===========================================================================
	// charge tous les noms possible différents pour les proposer dasn les SELECT
	function charge_nom()
		{
		global $jmax, $liste_nom, $bdd;
		
		for($i=0;$i<10000;$i++) 
			$liste_nom[$i]="";
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

	
	function nouveau( $date_jour, $nom, $pres2,$com ,$memo)
		{
		global $bdd,$pres;
		
		$nom=str_replace("\"","",$nom);
		$nom_slash= addslashes2($nom);	
		$pres2= addslashes2($pres2);	

		if (
			( ($nom!="")  && ($nom!=" (F)")  &&($nom!=" (M)")  &&($nom!=" (B)")  &&($nom!=" (S)")  )// le nom ne doit pas être vide
			&& 
			(!is_numeric($nom)))
			{
			$d=mise_en_forme_date_aaaammjj( $date_jour);
			
			$r1 = command("SELECT DISTINCT * FROM $bdd WHERE date='0000-00-00' and nom='$nom_slash'  ");
			$r2=nbre_enreg($r1); 
			if ($r2==0)
				{
				$user= $_SESSION['user'];
				$modif=time();
				$cmd = "INSERT INTO `$bdd`  VALUES ( '$nom', '', '','','$user','$modif','','')";
				$reponse = command($cmd);
				}
				
			$r1 = command("SELECT  * FROM $bdd WHERE date='$d' and nom='$nom_slash' and pres_repas<>'Suivi' and pres_repas<>'pda' and pres_repas<>'reponse' and pres_repas<>'partenaire' ");
			$r=nbre_enreg($r1); 
			$com= addslashes2($com);
			$nom= addslashes2($nom);
			$memo= addslashes2($memo);
			$user= $_SESSION['user'];
			$modif=time();
			if ($r==0)
				{
				if ( $pres2!="Erreur saisie")
					{
					if  (strstr($nom,"(A)") && ($pres2=="Visite")) 
						$pres2="Activité";
					
					if ( ((strstr($nom,"(B)")) || (strstr($nom,"(S)")) ) && ($pres2=="Visite") )		
						$pres2="Acteur Social";

					$cmd = "INSERT INTO `$bdd`  VALUES ( '$nom', '$d', '$pres2','$com','$user','$modif','','1')";
					$reponse = command($cmd);
					}
				}
			else
				{
				if (strpos($pres2,"(A)")===false)
					{
					$cmd = "UPDATE $bdd set commentaire='$memo' , user='$user' , modif='$modif' where nom='$nom' and date='0000-00-00' and pres_repas<>'pda' and pres_repas<>'Mail'  and pres_repas<>'Telephone'  and pres_repas<>'Age' ";
					$reponse = command($cmd);					

					if ( $pres2!="Erreur saisie")
						{
						if (strstr($nom,"(M)"))
							command("UPDATE $bdd SET commentaire='$com', qte='$pres2' , user='$user' , modif='$modif'  WHERE nom='$nom_slash' AND date='$d' and pres_repas!='reponse' and pres_repas!='partenaire' and pres_repas!='Suivi'  " );
						else
							command("UPDATE $bdd SET commentaire='$com', pres_repas='$pres2' , user='$user' , modif='$modif'  WHERE nom='$nom_slash' AND date='$d' and pres_repas!='reponse' and pres_repas!='partenaire' and pres_repas!='Suivi'  " );
						}
					else
						{
						if (strpos($nom,"(A)")!==false)
							{
							$reponse = command("select* from $bdd  WHERE activites like '%$nom%' AND date='$d'") ;
							if ($donnees = fetch_command($reponse))
								erreur ("Suppression impossible car il existe au moins une personne affectée à l'activité");
							 else								
								$reponse = command("DELETE FROM $bdd  WHERE nom='$nom' AND date='$d' and pres_repas<>'Suivi' and pres_repas<>'pda' and pres_repas<>'reponse' and pres_repas<>'partenaire' ") ;
							}
						else
							$reponse = command("DELETE FROM $bdd  WHERE nom='$nom' AND date='$d' and pres_repas<>'Suivi' and pres_repas<>'pda' and pres_repas<>'reponse' and pres_repas<>'partenaire' ") ;

						}	
					}
				else
					{
					// ajout activité
					$reponse = command("SELECT * FROM $bdd WHERE date='$d' and nom='$nom_slash' ");
					$donnees = fetch_command($reponse);
					$activites=$donnees["activites"];
					$pres_initial=$donnees["pres_repas"]; // on réinitialise correctement la présence
					
					if (strpos($activites, $pres2)===false)
						{
						$activites.="#-#".$pres2;
						command("UPDATE $bdd SET activites='$activites' WHERE nom='$nom_slash' AND date='$d' and pres_repas<>'Suivi' and pres_repas!='reponse' and pres_repas!='partenaire' ");
						}
					$pres=$pres_initial;
					}
				}
			}
		}

	

	function liste_presence( $val_init , $nom ="", $color ="")
		{
		global $acteur,$select_activites;
		
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
		
		// liste des activités
		if ( (!strstr($nom,"(A)") )	&& (!strstr($nom,"(A)")) && (!strstr($nom,"(B)") ) )	
			echo $select_activites;

		echo "</SELECT>";
		}
		
	function liste_type($val_init ="" )
		{
		echo "<SELECT name=\"type\"  >";
		affiche_un_choix($val_init,"Bénéficiaire","Homme");
		affiche_un_choix($val_init,"Bénéficiaire femme","Femme");
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

	// proposition sur score
	function proposition_sur_score($profil, $titre="", $fin_cadre="")
		{
		global  $exclus, $date_jour, $bdd;

		$i=0;
		$nu=0;
		$reponse = command("SELECT nom,qte FROM $bdd where date='0000-00-00' and pres_repas='' and qte<>'0' and qte<>'?' and nom not like '%(A)%' $exclus group by nom order by qte DESC  "); 
		while ($donnees = fetch_command($reponse) ) 
			{
			if ($i>80) 
				break;
			$tab_nom[$i]=$donnees["nom"];
						
			$i++;
			}
		if ($i==0)
			return;
			
		asort($tab_nom);
		echo "<tr> <td width=\"1000\">";

		if ($profil!="")
			echo "$titre: ";
		else
			echo "<hr>$titre: ";

		foreach ($tab_nom as $n)
			{
			$n_court=stripcslashes($n);
			if ( ( ($profil!="") && strstr($n_court,'(F)'))  ||  ( ($profil=="") && !strstr($n_court,'(F)')) )
				{
				$memo=retourne_memo($n);
				$n_court=str_replace("(F)","",$n_court);
				echo "<a href=\"fissa.php?action=nouveau&date_jour=$date_jour&nom=$n&memo=&presence=Visite&commentaire=\">$n_court</a> $memo; " ;
				}
				
			}

		
		}		
		
		
	function proposition($profil, $titre="", $fin_cadre="")
		{
		global  $exclus, $date_jour, $bdd, $acteur;

		$date_jour2=  mise_en_forme_date_aaaammjj($date_jour);
		$nu=0;
		if ($profil=="")
			$l= date('Y-m-d',  mktime(0,0,0 , date("m")-1, date("d"), date ("Y")));
		else
			$l= date('Y-m-d',  mktime(0,0,0 , date("m")-4, date("d"), date ("Y")));

		$reponse = command("SELECT *  FROM $bdd where date>'$l' and nom<>'Synth' and nom<>'Mail' $exclus group by nom order by nom  "); 
		$n=nbre_enreg($reponse);	
		
		$min=0;	
		if ( $n<20 )
			{
			if ($profil=="")
				$l= date('Y-m-d',  mktime(0,0,0 , date("m")-2, date("d"), date ("Y")));
			else
				$l= date('Y-m-d',  mktime(0,0,0 , date("m")-8, date("d"), date ("Y")));
			$min=0;
			}

		if ( $n>40 )
			{
			if ($profil=="")
				$l= date('Y-m-d',  mktime(0,0,0 , date("m"), date("d")-15, date ("Y")));
			else
				$l= date('Y-m-d',  mktime(0,0,0 , date("m")-2, date("d"), date ("Y")));
			$min=2; 
			 }	
			
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
							if ($donnees["TOTAL"]>$min )
								echo "<a href=\"fissa.php?action=nouveau&date_jour=$date_jour&nom=$n&memo=&presence=Visite&commentaire=\">$n_court</a>; " ;
							}
					
					}
				}
		if ( ($nu!=0) && ($fin_cadre!="") )
				echo "</td>";				
		}


	
	function rapport_mail2($date, $envoi_mail)
		{
		global $nb_usager,$nom_charge,$commentaire,$activites,$imax, $pres_repas, $bdd, $libelle,$format_date,$qte ;

		$date_a_afficher=$date;
		
		$date_gb=mise_en_forme_date_aaaammjj( $date);
		$reponse = command("SELECT * FROM $bdd WHERE date='$date_gb' and nom='Mail' "); 
		if (fetch_command($reponse) )  // il existe déjà un mail
			$mail_deja_envoyé=true;
		else
			$mail_deja_envoyé=false;
			
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
			if (!$mail_deja_envoyé )  // il existe déjà un mail
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
		
		$r1 = command("SELECT DISTINCT * FROM $bdd WHERE date='$date' and nom <>'Synth' and ( pres_repas='Visite+Repas' or pres_repas='Visite' or pres_repas='Refusé' ) ");
		$r=nbre_enreg($r1); 
		
		$r1 = command("SELECT DISTINCT * FROM $bdd WHERE date='$date' and nom <>'Synth' and pres_repas='Visite+Repas' ");
		$r3=nbre_enreg($r1); 
		
		$synth ="<b>$libelle</b> : aujourd'hui, $date_a_afficher, $r personnes accueillies, dont $r3 repas.";		
		
		if ( ( $envoi_mail) && (!$mail_deja_envoyé) )
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
					$valeur=str_replace("(A)","",$nom_charge[$i]);
					$nb=nbre_participants_activite($nom_charge[$i],$date);
					$txt= $txt . " $valeur ($nb pers), ";
					}		

		$flag=false;
		for($i=0;$i<$imax;$i++)
			if ($nom_charge[$i]!="")   
				if (strpos($nom_charge[$i],"(M)"))
					{
					if (!$flag)
						{
						$txt= $txt ."<BR><BR><b> Matériel </b>  : ";
						$flag=true;
						}
					$valeur=str_replace("(M)","",$nom_charge[$i]);
					$nb=$qte[$i];
					$txt= $txt . " $valeur ($nb), ";
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
			if (( ($nom_charge[$i]!="") &&  ($commentaire[$i]!="") )|| ( strpos($nom_charge[$i],'(A)')>0))
				{
				if (!$flag)
					{
					$txt = $txt. "<BR><BR><b>Détails</b> :  ";		
					$flag=true;
					}
				$valeur=$nom_charge[$i];
				$valeur2=mef_texte_a_afficher( stripcslashes($commentaire[$i]));
				if (strpos($nom_charge[$i],'(A)')>0)
					{
					$liste=liste_participants_activite($nom_charge[$i] ,$date);
					if ($liste!="")
						$txt .= "<BR>- $valeur : $valeur2 ($liste) ";
					}
				else
					$txt .= "<BR>- $valeur : $valeur2 ; ";
				}

		$txt = $txt. "<hr><center><a href=\"https://doc-depot.com\">
								<img src=\"http://doc-depot.com/images/logo.png\" width=\"75\" height=\"50\" ></a>\"".traduire('La Consigne Numérique Solidaire').'"';		

		$txt = $txt. "<a href=\"https://doc-depot.com\"><img src=\"http://doc-depot.com/images/fissa.jpg\" width=\"75\" height=\"50\" ></a>".traduire("Suivi Simplifié d'Activité");						
		$txt = $txt." - ". traduire("Services fournis par ")."<a href=\"https://adileos.doc-depot.com\"><img src=\"http://doc-depot.com/images/adileos.jpg\" width=\"150\" height=\"25\" ></a>";						
		
		
		if ( ( $envoi_mail)  && (!$mail_deja_envoyé) ) 
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

		echo "<hr>";
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
			if ( ($_SESSION['droit']=='R') ||($_SESSION['droit']=='S') )
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
	
	
	function retourne_memo($nom)
		{
		global $bdd,$format_date ;
		
		$c="";
		$reponse = command("SELECT * FROM $bdd WHERE nom='$nom' and commentaire<>'' and date='0000-00-00' and pres_repas<>'pda' and pres_repas<>'Age' and pres_repas<>'Mail' and pres_repas<>'Téléphone' and pres_repas<>'nationalie' and pres_repas<>'PE'  "); 
		if ($donnees = fetch_command($reponse) )
				$c=" ==> ". mef_texte_a_afficher( stripcslashes($donnees["commentaire"]) );
		return($c);
		}	
		
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
				echo "<a href=\"suivi.php?nom=$n\" > <b>$n</b> </a> : $c";
				echo "<a title=\"Suppression mémo\"  href=\"fissa.php?action=supp_memo&nom=$n\"> <img src=\"images/croixrouge.png\"width=\"15\" height=\"15\"><a>; ";
				
				$i++; 		
				}
		if ($i!=0)
			echo "<p>";
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
			
	function affiche_rdv($date_aff)
		{
		global $organisme, $bdd;
		$i=0;
		$reponse = command("SELECT *, DD_rdv.idx as idx_msg FROM r_user,DD_rdv WHERE r_user.organisme='$organisme' and r_user.idx=DD_rdv.auteur order by DD_rdv.date"); 
		while ($donnees = fetch_command($reponse) ) 
				{

				$date=$donnees["date"];	
				$user=$donnees["user"];	
				$d3= explode(" ",$date);
				$date=mef_date_fr($d3[0]);
			    if ($date==$date_aff) 
					{
					$heure=$d3[1];
					if (!is_numeric($user))
						{
						$avant=$donnees["avant"];	
						$idx=$donnees["idx_msg"];
						$ligne=stripcslashes($donnees["ligne"]);
						$auteur=libelle_user($donnees["auteur"]);
						if ($i++==0)
							echo "<tr><td><a href=\"rdv.php\"> <img src=\"images/reveil.png\" width=\"35\" height=\"35\"></a></td>";
						echo "<td> $heure : <a href=\"rdv.php?nom=$user\" >$user</a> </td>";
						}
					}
				}
		if ($i!=0)
			echo "<br>";
		}
	
	function ajouter_beneficiaire()
		{
		global $jmax,$liste_nom,$date_jour;
		
		echo "<table id=\"dujour\"  border=\"2\" >";
		echo "<form method=\"GET\" action=\"fissa.php#dujour\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
		echo "<input type=\"hidden\" name=\"femme\" value=\"\"> " ;
		echo "<input type=\"hidden\" name=\"memo\" value=\"\"> " ;	
		echo "<input type=\"hidden\" name=\"commentaire\" value=\"\"> " ;
		echo "<input type=\"hidden\" name=\"presence\" value=\"Visite\"> " ;	
		echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
		echo "<tr> <td bgcolor=\"#d4ffaa\"> ";
		echo "<SELECT name=nom onChange=\"this.form.submit();\">";
		echo "<OPTION  VALUE=\"\">  </OPTION>";
		for ($j=0;$j<$jmax;$j++)
			{
			$sel=$liste_nom[$j];
			if (  (strpos($sel,"(A)")===FALSE) && (strpos($sel,"(B)")===FALSE) && (strpos($sel,"(S)")===FALSE))
				echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
			}
		echo "</SELECT>";
		echo "</td> <td bgcolor=\"#d4ffaa\"> "; 
		echo "<input type=\"submit\" value=\"Ajouter\" >  ";
		echo "</td>";
		echo " </form> ";	
		}
		
		
	function liste_ajout_par_type( $presence,  $type)
		{
		global $date_jour,$liste_nom, $jmax;
		
		$select="";
		for ($j=0;$j<$jmax;$j++)
			{
			$sel=$liste_nom[$j];
			if  ( ( strstr($sel,"$type")) || (  ($type=="(B)") && ( strstr($sel,"(S)"))) )
				$select.= "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
			}		
		
		if ($select!="")
			{
			echo "<table id=\"dujour\"  border=\"2\" >";
			// =====================================================================loc AJOUTER 
			echo "<form method=\"GET\" action=\"fissa.php#dujour\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
			echo "<input type=\"hidden\" name=\"memo\" value=\"\"> " ;	
			echo "<input type=\"hidden\" name=\"commentaire\" value=\"\"> " ;
			echo "<input type=\"hidden\" name=\"presence\" value=\"$presence\"> " ;	
			echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
			echo "<tr> <td bgcolor=\"#d4ffaa\"> ";
			echo "<SELECT name=nom onChange=\"this.form.submit();\">";
			echo "<OPTION  VALUE=\"\">  </OPTION>";

			echo "$select </SELECT>";
			echo "</td> <td bgcolor=\"#d4ffaa\"> "; 
			echo "<input type=\"submit\" value=\"Ajouter\" >  ";
			echo "</td>";
			echo " </form> ";	
			return(true);
			}
			
		return(false);
		}
		
		
	// -====================================================================== Saisie
	function creation_par_type($type, $presence , $libelle)
		{
		global $date_jour;
		
		if  ($_SESSION['droit']=='R') 
			{
			echo "<td bgcolor=\"#d4ffaa\"></td> <td bgcolor=\"#d4ffaa\"><form method=\"GET\" action=\"fissa.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
			echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
			echo "<input type=\"hidden\" name=\"memo\" value=\"\"> " ;	
			echo "<input type=\"text\" name=\"nom\" size=\"30\" value=\"\">";	
			echo "<input type=\"hidden\" name=\"commentaire\" value=\"\"> " ;	
			if ($type=="Bénévole")
				{
				echo "<SELECT name=type >";
				affiche_un_choix($val_init,"Salarié");
				affiche_un_choix($val_init,"Bénévole");
				echo "</SELECT>";
				}
			else
				echo "<input type=\"hidden\" name=\"presence\" value=\"$presence\"> " ;
			echo "<input type=\"hidden\" name=\"type\" value=\"$type\"> " ;
			echo "<input type=\"submit\" value=\"Créer $libelle\" >  ";
			echo "</td></form> ";	
			}
		else
			echo "<td bgcolor=\"#d4ffaa\"></td> <td bgcolor=\"#d4ffaa\"></td>";
		
		}

		
	function liste_du_jour( $libelle, $l2, $type)
		 {
		global $bdd, $date_jour,$nom_charge, $pres_repas, $imax,$activites, $qte,$commentaire;
		
		echo "<tr> <td bgcolor=\"#3f7f00\"><font color=\"white\"> $libelle </td> <td bgcolor=\"#3f7f00\"> <font color=\"white\"> $l2 </td>";
		echo "<td bgcolor=\"#3f7f00\"> <font color=\"white\">Memo </td>";
		if ($type=="")
			echo "<td bgcolor=\"#3f7f00\"> <font color=\"white\"> Activités</font></td>";		

		echo "<td bgcolor=\"#3f7f00\"> <font color=\"white\"> Commentaire du jour</font></td>";		
		$ncolor=0;
		for($i=0;$i<$imax;$i++)
			if ( 
				( ($type<>"") && (( strstr($nom_charge[$i],"$type")) || (  ($type=="(B)") && ( strstr($nom_charge[$i],"(S)"))) ) )
				||
				( ($type=="") && ( (!strstr($nom_charge[$i],"(M)")) && (!strstr($nom_charge[$i],"(A)")) && (!strstr($nom_charge[$i],"(B)")) && (!strstr($nom_charge[$i],"(S)"))) )
				)
				{
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
				echo "<tr id=\"E$i\" > <td bgcolor=\"$color\"> ";
				echo "<form method=\"GET\" action=\"fissa.php#E$i\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
				echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
				$nom1=$nom_charge[$i];
				echo "<input type=\"hidden\" name=\"nom\" size=\"20\" value=\"$nom1\">";
				echo "<a href=\"suivi.php?nom=$nom1\"> <b>$nom1</b> </a></td>";
				
				switch ($type)
					{
					case "(M)" :
						$valeur=$qte[$i];
						echo "</td> <td bgcolor=\"$color\"> ";
						echo "<SELECT name=\"presence\"  onChange=\"this.form.submit();\">";
						for ($ix=-15; $ix<16;$ix++)
							if ($ix==0)
								{
								affiche_un_choix($valeur,"Pour info");
								affiche_un_choix($valeur,"Erreur saisie");	
								}
						else
							affiche_un_choix($valeur,"$ix");

						echo "</SELECT >";					
						break;
						
					default;
						$valeur=$pres_repas[$i];
						if ($nom1!= "Mail")
							liste_presence($valeur, $nom_charge[$i], $color);
						else
							echo "</td> <input type=\"hidden\" name=\"presence\" value=\"$valeur\"> <td bgcolor=\"$color\">";
						break;
					}


				echo "</td> <td bgcolor=\"$color\">";
				$nom_slash1= addslashes2($nom1);	
				$reponse = command("SELECT * FROM $bdd where date='0000-00-00' and pres_repas='' and nom='$nom_slash1' "); 
				if ($donnees = fetch_command($reponse) )
					$n=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));	
				else
				  $n="";
				
				if ($nom1!= "Mail")
					echo "<TEXTAREA rows=\"1\" cols=\"20\" name=\"memo\" onChange=\"this.form.submit();\">$n</TEXTAREA>";
				
				if ($type=="")
					{
					$valeur=mef_activites( $activites[$i],$nom_charge[$i],$date_jour); // T355
					echo "</td> <td bgcolor=\"$color\">$valeur </td>";		
					}
				
				echo "</td> <td bgcolor=\"$color\"> ";
				$valeur=mef_texte_a_afficher( stripcslashes($commentaire[$i]));
				echo "<TEXTAREA rows=\"1\" cols=\"50\" name=\"commentaire\" onChange=\"this.form.submit();\" >$valeur</TEXTAREA>";
				echo " </form> ";
				}
	 
			 }

			 $format_date = "d/m/Y";
	$user_lang='fr';

	$nom_charge=array();
	$liste_nom=array();
	$pres_repas=array();
	$commentaire=array();
	$nb_usager=200;
	
	// ConnexiondD
include "connex_inc.php";

$action=variable_s("action");	
require_once 'cx.php';

include 'suivi_liste.php';	
 
$reponse = command("SELECT * FROM fct_fissa WHERE support='$bdd' "); 
if ((!($donnees = fetch_command($reponse))) || (!$_SESSION['pass']) )
	{
	echo "<a href=\"https://doc-depot.com\">retour sur page d'accueil doc-depot.com</a>";
	}
else
	if (($_SESSION['droit']=="s") || ($_SESSION['droit']=="p") )
	{
	echo "Compte Inactif: merci de contacter votre responsable pour réactiver votre compte";
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

		$_SESSION['bene']="";
				
		$memo=variable_s("memo");

		$pda=variable_s("pda");
		$nom=variable_s("nom");
		$com=variable_s("com");
		$nouveau=variable_s("nouveau");
		$presence=variable_s("presence");
		$type= variable_s("type");	
		$quantite= variable_s("qte");	

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
				if ($type!="")
					{
					$nom=str_replace ('(A)','',$nom);
					$nom=str_replace ('(M)','',$nom);
					$nom=str_replace ('(S)','',$nom);
					$nom=str_replace ('(B)','',$nom);
					}

				if ($type=="Bénéficiaire femme")
					$nom .= " (F)";
				if ($type=="Bénévole")
					$nom .= " (B)";
				if ($type=="Salarié") // T362
					$nom .= " (S)";
				if ($type=="Activité")
					$nom .= " (A)";
				if ($type=="Matériel")
					$nom .= " (M)";		
				$com1=variable_s("commentaire");
				nouveau($date_jour,$nom, $presence,$com1,$memo);
				}
			
			if ($action=="nouveau2")
				{
				$nom=str_replace ('(A)','',$nom);
				$nom=str_replace ('(M)','',$nom);
				$nom=str_replace ('(S)','',$nom);
				$nom=str_replace ('(B)','',$nom);

				if ($type=="Bénéficiaire femme")
					$nom .= " (F)";
				nouveau2($date_jour,$nom, variable_s("age"),variable_s("nationalite"));
				}

			if ($action=="chgt_nom")
				chgt_nom($nom,$nouveau);

			if ($action=="rapport")
				{
				charge_date($date_jour);
				rapport_mail2($date_jour, false);
				}
				
			if ( ($action=="redacteur") && ( ($_SESSION['droit']=='R') ||($_SESSION['droit']=='S') ) )
				{
				redacteur($date_jour);
				}

			if ($action=="supp_memo")
				{
				$nom=variable_s("nom");
				$user= $_SESSION['user'];
				$modif=time();
				$nom_slash= addslashes2($nom);	
				command("UPDATE $bdd set commentaire='', user='$user' , modif='$modif' where nom='$nom_slash' and date='0000-00-00' and pres_repas<>'Age' and pres_repas<>'Téléphone' and pres_repas<>'pda' ") ;
				}
				
			if ($action=="supp_activite")
				{
				$nom=variable_s("nom");
				$nom_slash= addslashes2($nom);
				$date= mise_en_forme_date_aaaammjj(variable_s("date_jour")); // T355
				$idx=variable_s("idx");

				$reponse = command("SELECT * FROM $bdd WHERE date='$date' and nom='$nom_slash' ");
				$donnees = fetch_command($reponse);
				
				$act="";
				$d3=explode('#-#',$donnees["activites"]);
				$i=0;
				while (isset($d3[$i]))
					{
					if (($d3[$i]!="") && ($idx!=$i))
						$act.=$d3[$i].'#-#';
					$i++;
					}
		
				$user= $_SESSION['user'];
				$modif=time();
				command("UPDATE $bdd set  activites='$act' ,user='$user' , modif='$modif' where nom='$nom_slash' and date='$date' ") ;
				}
				
			if ($action=="mail")
				{
				charge_date($date_jour);
				rapport_mail2($date_jour, true);
				}	

			if (($action=="modif_liste_mail") && ( ($_SESSION['droit']=='R') ||($_SESSION['droit']=='S') ))
				{
				modif_liste_mail();
				}

			if ( ( ($action=="mail_detail") ||($action=="mail_synth") ) && ( ($_SESSION['droit']=='R') ||($_SESSION['droit']=='S') ))
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
				if ( ($_SESSION['droit']=='R') || ($_SESSION['droit']=='S') )
					{
					echo "<ul ><li><a href=\"fissa.php?action=redacteur&date_jour=$date_jour\"  target=_blank >Visualisation des rédacteurs</a>";
					if ($_SESSION['droit']=='R') 
						echo "<li><a href=\"export_fissa.php\" target=_blank>Export des données</a>";
					echo "</ul> ";

					}
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
				echo "<td><a href=\"suivi.php\"><img src=\"images/suivi.jpg\" width=\"70\" height=\"50\"><a></td>";						
				if (($_SESSION['droit']!="P") )
					{
					echo "<td><a href=\"rdv.php\"> <img src=\"images/rdv.jpg\" width=\"70\" height=\"50\"><a></td>";
					}
				echo "<td><a title=\"Alerte Grand Froid/Forte Pluie\" href=\"alerte.php\"><img src=\"images/logo-alerte.jpg\" width=\"70\" height=\"50\"></a> ";
				if ($logo!="")
					echo "<td> <a href=\"fissa.php\"> <img src=\"images/$logo\" width=\"200\" height=\"100\"  > </a> </td>";

				echo "</center></td>";					
				echo "</table> ";
				
				// =====================================================================loc Liste présents
				echo "<TABLE><TR> <td></td><td > <div class=\"CSS_titre\"  >";
				echo "<table border=\"0\" >";
				
				// =====================================================================loc AJOUTER
				// Echo "Visistes : ". presents(date('Ymd'));
				echo "<tr> <td width=\"1000\">";
				//ajouter_beneficiaire();
				echo "<table id=\"dujour\"  border=\"2\" >";
				// =====================================================================loc NOUVEAU
				$age=variable_s("age");
				echo "<tr><td bgcolor=\"#d4ffaa\"><table><tr><td><form method=\"GET\" action=\"fissa.php\">Prénom / Nom : ";
				echo "<input type=\"hidden\" name=\"action\" value=\"nouveau2\"> " ;
				echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
				if ($action=="nouveau2")
					echo "<input type=\"text\" name=\"nom\" size=\"30\" value=\"$nom\"></td>";	
				else
					echo "<input type=\"text\" name=\"nom\" size=\"30\" value=\"\"></td>";	
				
				echo " </td> <td> Date naissance : <input type=\"text\" name=\"age\" size=\"12\" value=\"$age\"> </td> <td> Origine : ";	
				select_pays( "", variable_s("nationalite") );
				echo "</td> <td> ";
				liste_type();
				echo "</td><td></table></td><td bgcolor=\"#d4ffaa\"><input type=\"submit\" value=\"Nouveau\" >  ";
				echo "</form></td> ";
				
				
				echo "</table></td>";
				
				// =====================================================================loc PROPOSITIONS 
				proposition_sur_score("(F)","Femme");
				proposition_sur_score("","Homme");
				proposition("(S)",$acteur);	
				proposition("(B)","Bénévoles");				
				proposition("(A)","Activités");					
				proposition("(M)","Matériel");					
				echo "  </table> <P> ";
				fin_cadre();


				// =====================================================================loc Rdv
				affiche_rdv($date_jour);
		
				// =====================================================================loc MEMO
				if ($date_jour==date('d/m/Y'))
					affiche_memo();
					
				// =====================================================================loc BENEFICIAIRES
				ajouter_beneficiaire()	;
				echo "<td bgcolor=\"#d4ffaa\"></td> ";
				echo "<td bgcolor=\"#d4ffaa\"></td> <td bgcolor=\"#d4ffaa\"></td> ";	
				liste_du_jour( "Prénom / Nom ","Evénement","");

				// =====================================================================loc SALARIE et BENEVOLES
				echo "</table> ";
				echo "<p> ";

				if (liste_ajout_par_type( "Visite", "(B)"))
					{
					creation_par_type( "Bénévole",  "Atelier",  "nouvel Acteur Social");
					liste_du_jour( "Acteur Social","Evénement","(B)");	
					echo "</table><p> ";
					}
				else
				if ($_SESSION['droit']=="R")					
					{
					echo "<table id=\"dujour\"  border=\"2\" >";
					creation_par_type( "Bénévole",  "Atelier",  "nouvel Acteur Social");					
					echo "</table><p> ";
					}
				// =====================================================================loc ACTIVITES
				if (liste_ajout_par_type( "Visite", "(A)"))
					{
					creation_par_type( "Activité",  "Atelier",  "nouvelle Activité");
					liste_du_jour( "Activité","Evénement","(A)");
					echo "</table><p> ";
					}
				else
				if ($_SESSION['droit']=="R")
					{
					echo "<table id=\"dujour\"  border=\"2\" >";
					creation_par_type( "Activité",  "Atelier",  "nouvelle Activité");					
					echo "</table><p> ";

					}
				// =====================================================================loc MATERIEL
				if (liste_ajout_par_type( "Matériel", "(M)"))
					{
					creation_par_type( "Matériel",  "Matériel",  "nouveau Matériel");
					liste_du_jour( "Matériel","Quantité","(M)");
					echo "</table><p> ";
					}
				else
					if ($_SESSION['droit']=="R")
						{	
						echo "<table id=\"dujour\"  border=\"2\" >";
						creation_par_type( "Matériel",  "Matériel",  "nouveau Matériel");
						echo "</table><p> ";
						}				
				
				
				// =====================================================================locYNTHESE
				
				echo "<font size=\"2\">Rappel CNIL: \"les informations personnelles enregistrées doivent être «adéquates, pertinentes et non excessives au regard des finalités pour lesquelles elles sont collectées (article 6-3°)\"</font>";
				echo "<TABLE><TR> <td></td><td > <div class=\"CSS_titre\"  >";
				echo "<table id=\"synthese\"  border=\"0\" >";
				echo "<tr> <td> <a href=\"suivi.php?nom=Synth\" > Synthèse de la journée </a>";
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

				if ( ($_SESSION['droit']=='R') ||($_SESSION['droit']=='S') )
					{
					// =====================================================================loc CHANGEMENT NOM
					echo "<P> <table border=\"0\" ><tr> <td>";
					echo "<form method=\"GET\" action=\"suivi.php\">";
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
					}
				

				break;
		}
	}

	
	pied_de_page();
		?>
	
    </body>
</html>
