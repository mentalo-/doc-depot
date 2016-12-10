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

session_start(); 

	echo "<head>";
	echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"15\">";
	echo "</head><body>";
	include "connex_inc.php";
	include 'general.php';
 	require_once 'include_crypt.php';
	include 'include_charge_image.php';	
	include 'audit_cnil.php';	

	// doublon avec INDEX.HTML
	
	function recept_mail($id,$date)
		{
		global $user_idx;
		
		$reponse = command("UPDATE `r_user` SET  recept_mail='$date' where idx='$id'  ");
		ajout_log( $id, traduire("Autorisation reception par mail"), 	 $user_idx );
		}
		
	include 'include_mail.php';
	include 'exploit.php';
		
		
	function purge_bdd_log()
		{
		commentaire_html("purge_log");

		echo "<br>Purge Log";
		$ilyaunmois =date('Y-m-d H\hi.00',  mktime(0,0,0 , date("m")-1, date("d"), date ("Y")));		
		$ilyaunesemaine =date('Y-m-d H\hi.00',  mktime(0,0,0 , date("m"), date("d")-7, date ("Y")));		
		command("delete from z_log_t  where ligne like '%Traitement mail hors delais%' and date<'$ilyaunmois' ");
		command("delete from z_log_t  where ligne like '%Reception supervision gatewaysms%' and date<'$ilyaunmois' ");
		command("delete from z_log_t  where ligne like '%Envoi SMS au ".parametre("DD_numero_tel_sms")."%' and date<'$ilyaunmois' ");
		command("delete from z_log_t  where ligne like '%Nb TTT pendant alarme%' and date<'$ilyaunmois' ");
		command("delete from z_log_t  where ligne like '%but Scoring%' and date<'$ilyaunmois' ");
		command("delete from z_log_t  where ligne like '%fin Scoring%' and date<'$ilyaunmois' ");
		command("delete from z_log_t  where ligne like '%fin Scoring%' and date<'$ilyaunmois' ");
		command("delete from z_log_t  where ligne like 'purge Log%' and date<'$ilyaunesemaine' ");
		command("delete from z_log_t  where ligne like 'purge Alerte %' and date<'$ilyaunesemaine' ");
		command("delete from z_log_t  where ligne like 'purge Log%' and date<'$ilyaunesemaine' ");
		command("delete from z_log_t  where ligne like 'purge BdD FISSA%' and date<'$ilyaunesemaine' ");
		command("delete from z_log_t  where ligne like 'Controle de signature%' and date<'$ilyaunesemaine' ");
		command("delete from z_log_t  where ligne like 'Backup tables%' and date<'$ilyaunesemaine' ");
		command("delete from z_log_t  where ligne like 'Traitement Purges%' and date<'$ilyaunesemaine' ");
		command("delete from z_log_t  where ligne like 'Rejet connexion%' and date<'$ilyaunesemaine' ");
		command("optimize table z_log_t");
		
		$hier =date('Y-m-d H\hi.00',  mktime(0,0,0 , date("m"), date("d")-1, date ("Y")));		
		command("delete from r_pages_users_debug  where  tps_exec<'$hier' ");
		
		}
		
	// supprime les rdv envoyé ayant plus d'un mois d'ancieneté
	function purge_rdv()
		{
		commentaire_html("purge_rdv");

		echo "<br>Purge Rdv";
		$ilya3j =date('Y-m-d H\hi',  mktime(0,0,0 , date("m"), date("d")-3, date ("Y")));		
		command("delete from DD_rdv where  date<'$ilya3j' ");
		}
	
	// supprime les commentaire de plus de 2 ans 
	function purge_bdd_fissa()
		{	
		commentaire_html("purge_dbb_fissa");
		$avant= date('Y-m-d',  mktime( 0,0, 0 , date("m"), date("d"), date ("Y")-3 ));
		echo "<br>Purge BDD FISSA";
		$reponse =command("select * from fct_fissa");		
		while ($donnees = fetch_command($reponse) ) 
				{
				$support =$donnees["support"];		
				$crit=" ( not (nom like '%(A)%')) and (nom<>'Synth') and (nom<>'Mail')   ";
				$r1 =command("update $support set commentaire='' where  $crit and date<'$avant' and date>'2000-01-01' and pres_repas<>'__upload' ");		
				}
		}
		
	// supprime les message de plus de 7j
	function purge_bdd_alerte()
		{	
		commentaire_html("purge_dbb_alerte");
		$avant= date('Y-m-d',  mktime( 0,0, 0 , date("m"), date("d")-7, date ("Y") ));
		echo "<br>Purge BDD Alerte";
		$reponse =command("delete from cc_alerte where tel='' and creation<'$avant' ");		
		}	
		
	// supprime tous les fichiers temporaires de plus de 2 minutes
	function purge_fichiers_temporaires($dir, $file ='*.*' )
		{
		commentaire_html("purge_fichiers_temporaires");
		
		// suppression des fichiers temporaire
		$l= date('Y-m-d H:i',  mktime(date ("H"),date ("i")-2, date ("s") , date("m"), date("d"), date ("Y") ));
		foreach(glob($dir.$file) as $v)
			{
			if (date ("Y-m-d H:i:s", filemtime($v))<$l)
				{
				echo "<p>Purge $v";
				unlink($v);
				}
			}
		}

		

	// supprime tous les fichiers temporaires de plus de 2 minutes
	function purge_dossiers( )
		{
		commentaire_html("purge_dossiers");
		
		// suppression des fichiers temporaire
		$l= date('Y-m-d H:i',  mktime(0,0, date ("s") , date("m"), date("d")-parametre("DD_duree_vie_dossier"), date ("Y") ));
		foreach(glob('dossiers/*.*') as $v)
			{
			if (date ("Y-m-d H:i:s", filemtime($v))<$l)
				{
				echo "<p>Purge dossier $v";
				unlink($v);
				}
			}
		}		
		
	// tous les jours à 23h on réinitialise les score pour chaque visiteur de Fissa de façon à pouvoir être calculé dasn le nuit par tranche
	function init_score_fissa()
		{
		echo "<BR> Init_score_fissa : ";
		ajout_log_tech( "Debut Scoring" );
		ecrit_parametre("TECH_Fissa_scoring", date('Y-m-d H:i', time() ) ) ;
		ecrit_parametre("TECH_Fissa_scoring_nb",  0 ) ;
		ecrit_parametre("TECH_Fissa_scoring_fin", "") ;			

		$reponse =command("select * from fct_fissa  ");
		while ($donnees = mysql_fetch_array($reponse) )
			{
			$support=$donnees["support"];
			echo "$support; ";
			command("UPDATE $support set qte='?' where date='0000-00-00' and pres_repas='' and nom<>'Synth'  and nom<>'Mail' ");
			}
		}			

	
	
	function plus1( $bdd, $nom, $fenetre )
		{
		global $score  ;
		
		$filtre = " and ( ( pres_repas='Visite') or  ( pres_repas='Visite+Repas') ) ";
		$t=  time() - $fenetre* 86400;
		$l= date('Y-m-d', $t);
		echo " $l ";

		$nbj=  (time() - $t) /86400; 
		$reponse = command("SELECT count(*) as TOTAL FROM $bdd where date>'$l' and nom='$nom' $filtre   "); 
		$donnees = fetch_command($reponse);
		$nb=$donnees["TOTAL"];				
		if ($nb>0)
			{
			$score+=$nb/$nbj;			
			return (true);
			}
		return (false);
		}	
		
	function calcule_score()
		{
		global $time_ttt, $score;
		
		$nb_calcul=0;
		echo "<BR> Calcule_score : ";
		$reponse =command("select * from fct_fissa  ");
		while ($donnees = mysql_fetch_array($reponse) )
			{
			$bdd=$donnees["support"];
			
			$r1 = command("SELECT * FROM $bdd where date='0000-00-00'  and qte='?' "); 
			while ($d1 = fetch_command($r1) ) 
				{
				$nb_calcul++;
				$score=0;
				$nom=addslashes2($d1["nom"]);
				echo "<br> $bdd -> $nom : ";
				if ( plus1( $bdd, $nom, 180 ))				
					if ( plus1( $bdd, $nom, 61 ))				
						if ( plus1( $bdd, $nom, 30 ))
							if ( plus1( $bdd, $nom, 15 ))
								 plus1( $bdd, $nom, 7);
				
				// si memo on le valorise dans le scoring 
				$reponse = command("SELECT * FROM $bdd WHERE nom='$nom' and commentaire<>'' and date='0000-00-00' and pres_repas<>'pda' and pres_repas<>'Age' and pres_repas<>'Mail' and pres_repas<>'Téléphone' and pres_repas<>'nationalie' and pres_repas<>'PE'  "); 
				if ($donnees = fetch_command($reponse) )
					$score++;
		
				command("UPDATE $bdd SET qte='$score' where nom='$nom' and date='0000-00-00' ");
				echo " ==> ". sprintf("%2.2f",  $score*10);
				if  ( (time() -$time_ttt) >5)
					break;
				}
			}
		if ($nb_calcul==0)
			{
			if (parametre("TECH_Fissa_scoring_fin","")=="")
				{
				ecrit_parametre("TECH_Fissa_scoring_fin", date('Y-m-d H:i', time() ) ) ;	
				ajout_log_tech( "Fin Scoring (#".parametre("TECH_Fissa_scoring_nb").")" );
				}				
			}
		else
			ecrit_parametre("TECH_Fissa_scoring_nb", parametre("TECH_Fissa_scoring_nb")+$nb_calcul ) ;
		}
	
	function alerte_sms_domiciliation( $aff_log)
				{
				$nbsms=0;
				echo "<BR> Alerte SMS Domiciliation : ";
				if ($aff_log)
					ajout_log_tech( "Debut Alerte SMS Domiciliation" );
					
				$reponse =command("select * from fct_fissa  ");
				while ($donnees = mysql_fetch_array($reponse) )
					{
					$support=$donnees["support"];
					$idx_organisme=$donnees["organisme"];
					echo "<hr>  $support:  ";
					
					if (parametre("Formation_num_structure","")!=$idx_organisme) // on ne traite pas l'organisme de formation
						{
						// on défini les dates où on recherche du courier en attente
						$date_debut_gb = date('Y-m-d',  mktime(0 ,0, 0  , date("m"), date("d")-10, date ("Y")) );
						$date_fin_gb = date('Y-m-d',  mktime(0 ,0, 0  , date("m"), date("d")-17, date ("Y")) );
						
						$r2 = command("SELECT * FROM $support WHERE date<'$date_debut_gb' and date>='$date_fin_gb' and pres_repas='Arrivée courrier' "); 
						while ($d2 = fetch_command($r2))
							{
							$nom=$d2["nom"]; 
							echo "$nom, ";
							
							// on vérifie qu'il n'y en a pas antérieurement (déjà traités normalement)
							$r3 = command("SELECT * FROM $support WHERE nom='$nom' and date<'$date_fin_gb' and pres_repas='Arrivée courrier' ","x"); 
							if (!($d3 = fetch_command($r3)))
								{
								// on recherche le numéro de téléphone
								$r3 = command("SELECT * FROM $support WHERE nom='$nom' and pres_repas='Téléphone' "); 
								if ($d3 = fetch_command($r3))
									{
									$telephone=$d3["commentaire"];
									if (VerifierTelephone($telephone) )
										{ 	// s'il exite et est valable on va cherher à récupérer le nom de l'organisme ainsi que son adresse
										$r3 = command("SELECT * FROM r_organisme WHERE idx='$idx_organisme'"); 
										if ($d3 = fetch_command($r3))
											{
											$organisme=$d3["organisme"];
											$adresse=$d3["adresse"];
											$ligne="Vous avez du courrier en attente au service de domiciliation de $organisme ($adresse)";
											//envoi_SMS_operateur( $telephone, $ligne );
											envoi_SMS( $telephone, $ligne );
											$nbsms++;
											}	else echo " organisme $idx_organisme n'existe pas ";
										}	else echo " Numero de téléphone '$telephone' n'est pas valide ou existant ";	
									}	else echo " Numero de téléphone de '$nom' n'est pas existant ";	
								} else echo " Existe déjaà du courrier non pris la semaine antérieure";	
							}
						}
					}
				if ($aff_log)
					ajout_log_tech( "Fin Alerte SMS Domiciliation($nbsms SMS envoyés)" );
				}
				

		
		
// Génére une chine aléatoire pour le message de supervision
function random_chaine($car) 
	{
	$string = "";
	$chaine = "abcdefghijklmnpqrstuvwxy";
	srand((double)microtime()*1000000);
	for($i=0; $i<$car; $i++) 
		{
		$string .= $chaine[rand()%strlen($chaine)];
		}
	return $string;
	}	
		
	$time_ttt= time();
	$heure=date('H',  time());
	ajout_log_jour(" ==================================================================================================== TTT");
	
	commentaire_html("Affiche Alarme");
	affiche_alarme();
	

	$ancien_ttt=parametre("TECH_date_dernier_ttt");
	$delta= (time()-$ancien_ttt);
	Echo "<p>Dernier traitement TTT :".date('Y-m-d H\hi.s',$ancien_ttt)." Il y a ". $delta . "sec<p>";	

	if ($time_ttt-$ancien_ttt>14) 
		{
		ecrit_parametre("TECH_date_dernier_ttt","$time_ttt") ;
		
		if ( ($time_ttt-parametre("TECH_date_mesure_echec_sur_periode")) > parametre("DD_periode_mesure_nbre_echec_max") )  
			{
			ecrit_parametre("TECH_date_mesure_echec_sur_periode", $time_ttt);
			ecrit_parametre("TECH_nbre_echec_sur_periode", "0");			
			}
		
		if (date('Y-m-d-h',  time()) != date('Y-m-d-h',  $ancien_ttt ))
			if ($heure==0)
				{
				ajout_log_tech( "Traitement Purges");
				purge_rdv();
				purge_log();
				purge_dde_acces(); 					
				purge_backup_tables();
				
				supp_fichier('tmp/av-hier.txt');
				rename('tmp/hier.txt','tmp/av-hier.txt');
				
				supp_fichier('tmp/hier.txt');
				rename('tmp/log.txt','tmp/hier.txt');

				ajout_log_tech( "Mails envoyés:".parametre("TECH_nb_mail_envoyes")." / ".parametre("DD_nbre_mail_jour_max"));
				ajout_log_tech( "SMS envoyés:".parametre("TECH_nb_sms_envoyes"));
				ajout_log_tech( "SMS OVH envoyés:".parametre("TECH_nb_sms_envoyes_operateur"));
				ecrit_parametre("TECH_nb_mail_envoyes",0) ;
				ecrit_parametre("TECH_nb_sms_envoyes",0) ;				
				ecrit_parametre("TECH_nb_sms_envoyes_operateur",0) ;
				}

		if (parametre("TECH_Fissa_scoring_fin","")=="")
			calcule_score();
	
		// traitement le 1 jour du mois à 6 heures
		if (date('d-h',  time())==parametre("DD_jour_heure_audit_cnil","01-06") )
				{
				$idx_organisme_formation = parametre("Formation_num_structure");
				$reponse =command("select * from fct_fissa  where organisme <>$idx_organisme_formation ");
				while ($donnees = mysql_fetch_array($reponse) )
					{
					$periode=date('Y-m',  time());
					$support=$donnees["support"];
					$r1 =command("select * from cc_audit_cnil where support='$support' and periode='$periode' ");
					if (!($d1 = mysql_fetch_array($r1) ) )
						{
						audit_cnil($periode, $support);
						break;
						}
					}
				}
	
		if (date('Y-m-d-h',  time()) != date('Y-m-d-h',  $ancien_ttt ))
			{
			if ($heure==23)
				init_score_fissa();
				
			if ($heure==1)
				{
				ajout_log_tech( "Backup tables");
				backup_tables();
				
				purge_dossiers( );
				}			
			
			if ($heure==2)
				{
				ajout_log_tech( "Controle de signature");
				ctrl_signature();
				audit_doc_internes();
				}			

			if ($heure==3)
				{
				ajout_log_tech( "purge BdD FISSA");
				purge_bdd_fissa();
				}
			if ($heure==4)
				{
				ajout_log_tech( "purge Alerte");
				purge_bdd_alerte();
				}		
				
			if ($heure==5)
				{
				ajout_log_tech( "purge Log");
				purge_bdd_log();
				}
				
			if ($heure==7)
				{
				if (parametre("TECH_Fissa_scoring_fin","")=="")
					{
					ajout_log_tech( "Fin Scoring non terminé à 7h !!" ,"P0" );
					envoi_sms( parametre('DD_tel_alarme1') , parametre('TECH_identite_environnement')." : Fin Scoring non terminé à 7h !!");
					}
				}
				
			// =========================================================================== traitement alerte SMS sur courrier en attente de domiciliation
			// sauf organisme de formation
			if (($heure==10) && (date("N")=="1") ) // le lundi matin à 10h
				alerte_sms_domiciliation(true);				
				
			// ----------------------------------------------------------------------- Envoi message de synthèses FISSA		
			if ($heure==19)
			    if ( parametre("Tech_date_envoi_synthses")!=date('Y-m-d',  time()) )
					{
					exploit_envoi_mail_synthese();
					ecrit_parametre("Tech_date_envoi_synthses", date('Y-m-d',  time()));
					}				
				
			}	
			
		// =========================================================================== traitement alerte SMS sur courrier en attente de domiciliation
			// sauf organisme de formation
			if 	($_SERVER['REMOTE_ADDR']=="127.0.0.1")
				alerte_sms_domiciliation(false);			
			
		commentaire_html( "Purge temporaire");
		purge_fichiers_temporaires("dir_zip/");		
		purge_fichiers_temporaires("tmp/","*.pdf");		
		purge_fichiers_temporaires("upload_tmp/");		
		
		$td_envoi=parametre('TECH_dernier_envoi_supervision');

			// envoi aleatoir  d'un  mail vers la gatewaysms avec envoi de mail sur elle même qui va générer un mmail en retour
			if ( // il ne faut pas que l'on soit déjà en traitement d'une supervision 
				($td_envoi=='') 
				&& // il faut à minima une heure entre chaque sms
 				( (time()-parametre('TECH_dernier_envoi_supervision_effectif') ) > 3600 ) 
				&& 
				( // frequence plus élevée en journée
				( ( rand(0,300)==1 ) && ($heure>6) && ($heure<20))
				||
				( rand(0,1300)==1 )
				||
				( parametre("TECH_alarme_supervision_sms")!="" )  // si alarme 
				)
				)
				{
				ecrit_parametre('TECH_msg_supervision_gatewaysms', random_chaine(6).' '.random_chaine(3).' '.random_chaine(6).'.');
				envoi_SMS( parametre('DD_numero_tel_sms') ,parametre('TECH_msg_supervision_gatewaysms').". ".date('H\hi',time()));
				ecrit_parametre('TECH_dernier_envoi_supervision', time() );
				ecrit_parametre('TECH_dernier_envoi_supervision_effectif',  time()  );
				}
			
			$td_envoi=parametre('TECH_dernier_envoi_supervision');
			if ($td_envoi!='') // s'il y a un marquage de l'heure d'envoi
				if ((time()-$td_envoi ) >10*60 ) // s'il y a eu plus de 10 minutes depuis l'envoi
					{
					if (parametre("TECH_alarme_supervision_sms") =="")
						{
						envoi_mail(parametre('DD_mail_gestinonnaire'),"Début alarme délais supervision gateway sms","");
						ajout_log_tech( "Dépassement délais supervision gateway SMS ","P0");
						ecrit_parametre("TECH_alarme_supervision_sms",time()) ;
						
						$nbmess=nb_message_file_envoi_sms (); // on récupére le nombre de message dasn la file SMS
						if (($nbmess=="") || ($nbmess==0) ) // Si file d'envoi est vide alors envoi SMS aux exploitants
							envoi_sms( parametre('DD_tel_alarme1') , parametre('TECH_identite_environnement')." : Alarme supervision SMS");

						}
					ecrit_parametre('TECH_dernier_envoi_supervision', '' );
					}
					
					
					
					
		// ----------------------------------------------------------------------- traitement de JOUR			
		if (($heure>4) && ($heure<21))
			{
	
			// ----------------------------------------------------------------------- traitement des RDV				
			Echo "<p>TTT rdv ";
			
			$reponse =command("select * from  DD_rdv where (etat='A envoyer' and avant<>'Aucun') ");		
			while ($donnees = fetch_command($reponse) ) 
				{
				$idx_rdv=$donnees["idx"];	
				$date=$donnees["date"];	
				$avant=$donnees["avant"];	
				$auteur=$donnees["auteur"];	
				$fuseau=$donnees["fuseau"];	
				
				// on recrée le timestamp du rdv 
				$annee=substr ($date,0,4);
				$mois=substr ($date,5,2);
				$jour=substr ($date,8,2);
				$heure=substr ($date,11,2);
				$minute=substr ($date,14,2);

				
				$decalage_horaire = 0;
				// cas de la Réunion, on traite le décalage horaire
				if ($fuseau!="")
					{
					if ($fuseau=="RE")
						$dateTimeZoneDOMTOM = new DateTimeZone("Indian/Reunion");
					if ($fuseau=="MQ")
						$dateTimeZoneDOMTOM = new DateTimeZone("America/Martinique");						
					if (($fuseau=="GP")  || ($fuseau=="GF"))
						$dateTimeZoneDOMTOM = new DateTimeZone("America/Guadeloupe");
						
					$dateTimeZoneFR = new DateTimeZone("Europe/Paris");
					$dateTimeDOMTOM = new DateTime("now", $dateTimeZoneDOMTOM);
					$timeOffset = $dateTimeZoneFR->getOffset($dateTimeDOMTOM);
					$decalage_horaire =$timeOffset/3600;
					}				
				
				switch ($avant )
					{
					case "1H": $heure = $heure-1-$decalage_horaire ;
								if ($heure<8) // si envoi avant 8h alors on envoi la veille
									{
									$jour = $jour-1;
									$heure = 18-$decalage_horaire;
									}
								if ($heure>20) // si envoi après 20h alors on reporsitionne 
									$heure = 19;
								break;
								
					case "La veille": 
								$heure = 18-$decalage_horaire;
								$minute	= rand(0,59);
								$jour = $jour-1;
								break;			
					}
				
				// si la notification doit être envoyé un dimanche alors on l'anticipe à la veille
				if ( date("N",mktime(12 ,0, 0  , $mois, $jour, $annee))==7) 
					$jour = $jour-1;
				
				$declenchement = mktime($heure ,$minute, 0  , $mois, $jour, $annee);
				
				echo "<br> $date ($decalage_horaire) --> ".date( "Y-m-d H\hi.s",$declenchement) ;

				// si heure actuelle est superieure à l'heure de déclenchement
				if ( $declenchement < time() )
					{
					$ligne=stripcslashes($donnees["ligne"]);
					$user_idx=$donnees["user"];
					$date=$donnees["date"];					
				
					$telephone_user="";
					if (!is_numeric($user_idx)) // le nom est en mode texte ==> origine FISSA
						{
						$auteur=$donnees["auteur"]; // on récupére l'auteur 
						$r1 =command("SELECT * from  r_user WHERE idx='$auteur' "); 
						if ($d1 = fetch_command($r1) ) 
							{
							$organisme=$d1["organisme"]; // on deduit de l'auteur l'organisme d'appartenance
							if ($organisme!=parametre("Formation_num_structure"))
								{
								$r1 =command("SELECT * from  fct_fissa WHERE organisme='$organisme' ");
								if ($d1 = fetch_command($r1) ) 
									{
									$support=$d1["support"]; // on déduit le support de l'organisme
									$r1 =command("SELECT * from  $support WHERE nom='$user_idx' and pres_repas='Téléphone'");
									if ($d1 = fetch_command($r1) )
										$telephone_user=$d1["commentaire"];	
									}
								}
							}
						}
					else // l'index est numrique ==> origine Doc-depot 
						{
						$r1 =command("SELECT * from  r_user WHERE idx='$user_idx' ");
						if ($d1 = fetch_command($r1) )
							if ($d1["organisme"] !=  parametre("Formation_num_structure") )
								$telephone_user=$d1["telephone"];
						}	
						
					if ( VerifierPortable($telephone_user) 	) 	// vérification au dernier momentdu le format du n° de téléphone avant envoi
						{
						envoi_SMS_operateur( $telephone_user  , $ligne );
						ajout_log( $user_idx,"RDV - Envoi SMS au $telephone_user : '$ligne' ",$auteur);
						command("UPDATE DD_rdv set etat='Envoyé' where user='$user_idx' and date='$date' ");
						}
					else
						command("UPDATE DD_rdv set etat='' where user='$user_idx' and date='$date' ");

					}
				}
			echo "<p>";
			}
		
		// -------------------------------------------------------------------traitement des mails et tempo de connexion
			if ($_SERVER['REMOTE_ADDR']!="127.0.0.1")	
				TTT_mail(true);
			else
				echo "TTT";

			$delta=($time_ttt-$ancien_ttt);
			// attention cette action doit être en dernier sinon mise à jour z_ttt ne fonctionne pas !!!
			decremente_echec_cx ($delta/15);
			
			ecrit_parametre("TECH_al_nb_TTT",parametre("TECH_al_nb_TTT")+1)  ;
			$tempo_mesure=5*(4*15);
			if ( (time()-parametre("TECH_al_tempo_TTT",time()))>$tempo_mesure) 
				{
				$nb_ttt=parametre("TECH_al_nb_TTT");
				if (parametre("TECH_alarme_delais_TTT")=="")
					// si pas d'alarme
					{
					if ($nb_ttt<($tempo_mesure/15/4))
						{
						ajout_log_tech( "Traitement mail hors delais ","P0");
						if (parametre("TECH_mail_sur_alarme_delais_TTT")!="")
							envoi_mail(parametre('DD_mail_gestinonnaire'),"Début alarme Traitement mail hors delais ","");
						ecrit_parametre("TECH_alarme_delais_TTT",time()) ;			
						}						
					}
				else
					{
					//ajout_log_tech( "Nb TTT pendant alarme : $nb_ttt");
					if ($nb_ttt>($tempo_mesure/15*2/3))
						{
						ajout_log_tech( "Fin alarme Traitement mail hors delais","P0");
						if (parametre("TECH_mail_sur_alarme_delais_TTT")!="")
							envoi_mail(parametre('DD_mail_gestinonnaire'),"Fin alarme Traitement mail hors delais ","");;
						ecrit_parametre("TECH_alarme_delais_TTT",'') ;
						}						
					}
				
				// on remet le compteur à zéro
				ecrit_parametre("TECH_al_nb_TTT",0) ;
				ecrit_parametre("TECH_al_tempo_TTT",time());
				}

		
		ajout_log_jour(" ==================================================================================================== TTT_Alerte");
		require_once "alerte_ttt.php";
		}		
		
	// memorise dans la table des paramétres avec le préfice MONITOR_ les appels fait avec la variable ddr
	// cela permet de vérifier si un superviseur ne fonctionne plus depuis longtemps
	echo "<hr>DDR";
	if (isset ($_GET["ddr"]))
		{  
		$ddr = $_GET["ddr"];
		ecrit_parametre("MONITOR_$ddr",time());
		}
	
	echo "</body>";

	
	?> 