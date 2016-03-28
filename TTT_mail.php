  <?php  
// traduire() : Ok (inutile)

	echo "<head>";
	echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"15\">";
	echo "</head><body>";
	include "connex_inc.php";
	include 'general.php';
 	include 'include_crypt.php';
	include 'include_charge_image.php';	
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
		command("delete from z_log_t  where ligne like '%Traitement mail hors delais%' and date<'$ilyaunmois' ");
		command("delete from z_log_t  where ligne like '%Reception supervision gatewaysms%' and date<'$ilyaunmois' ");
		command("delete from z_log_t  where ligne like '%Envoi SMS au ".parametre("DD_numero_tel_sms")."%' and date<'$ilyaunmois' ");
		}
		
	// supprime les rdv envoyé ayant plus d'un mois d'ancieneté
	function purge_rdv()
		{
		commentaire_html("purge_rdv");

		echo "<br>Purge Rdv";
		$ilyaunmois =date('Y-m-d H\hi',  mktime(0,0,0 , date("m")-1, date("d"), date ("Y")));		
		command("delete from DD_rdv where etat='Envoyé' and date<'$ilyaunmois' ");
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
				$r1 =command("update $support set commentaire='' where  $crit and date<'$avant' and date>'2000-01-01' ");		
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
	
	// tous les jours à 23h on réinitialise les score pour chaque visiteur de Fissa de façon à pouvoir être calculé dasn le nuit par tranche
	function init_score_fissa()
		{
		echo "<BR> Init_score_fissa : ";
		ajout_log_tech( "Debut Scoring:".date('Y-m-d H:i', time() ) );
		ecrit_parametre("TECH_Fissa_scoring", date('Y-m-d H:i', time() ) ) ;
		ecrit_parametre("TECH_Fissa_scoring_nb",  0 ) ;
		ecrit_parametre("TECH_Fissa_scoring_fin", "") ;			

		$reponse =command("select * from fct_fissa  ");
		while ($donnees = mysql_fetch_array($reponse) )
			{
			$support=$donnees["support"];
			echo "$support; ";
			command("UPDATE $support set qte='?' where date='0000-00-00' and pres_repas='' ");
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
				if ( plus1( $bdd, $nom, 61 ))				
					if ( plus1( $bdd, $nom, 30 ))
						if ( plus1( $bdd, $nom, 15 ))
							 plus1( $bdd, $nom, 7);
					
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
				ajout_log_tech( "Fin Scoring:".date('Y-m-d H:i', time() ). " (#".parametre("TECH_Fissa_scoring_nb").")" );
				}				
			}
		else
			ecrit_parametre("TECH_Fissa_scoring_nb", parametre("TECH_Fissa_scoring_nb")+$nb_calcul ) ;
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
				ecrit_parametre("TECH_nb_mail_envoyes",0) ;
				ecrit_parametre("TECH_nb_sms_envoyes",0) ;
				}

		if (parametre("TECH_Fissa_scoring_fin","")=="")
			calcule_score();
			
		if (date('Y-m-d-h',  time()) != date('Y-m-d-h',  $ancien_ttt ))
			if ($heure==23)
				init_score_fissa();
				
		if (date('Y-m-d-h',  time()) != date('Y-m-d-h',  $ancien_ttt ))
			if ($heure==1)
				{
				ajout_log_tech( "Backup tables");
				backup_tables();
				}			
			
		if (date('Y-m-d-h',  time()) != date('Y-m-d-h',  $ancien_ttt ))
			if ($heure==2)
				{
				ajout_log_tech( "Controle de signature");
				ctrl_signature();
				}			

		if (date('Y-m-d-h',  time()) != date('Y-m-d-h',  $ancien_ttt ))
			if ($heure==3)
				{
				ajout_log_tech( "purge BdD FISSA");
				purge_bdd_fissa();
				}
		if (date('Y-m-d-h',  time()) != date('Y-m-d-h',  $ancien_ttt ))
			if ($heure==4)
				{
				ajout_log_tech( "purge Alerte");
				purge_bdd_alerte();
				}		
				
		if (date('Y-m-d-h',  time()) != date('Y-m-d-h',  $ancien_ttt ))
			if ($heure==5)
				{
				ajout_log_tech( "purge Log");
				purge_bdd_log();
				}
				
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
		
				
		if (($heure>6) && ($heure<21))
			{		
			if (date('Y-m-d-h',  time()) != date('Y-m-d-h',  $ancien_ttt ))
				if ($heure==19)
				    if ( parametre("Tech_date_envoi_synthses")!=date('Y-m-d',  time()) )
						{
						exploit_envoi_mail_synthese();
						ecrit_parametre("Tech_date_envoi_synthses", date('Y-m-d',  time()));
						}
					
		// ----------------------------------------------------------------------- traitement des RDV				
			Echo "<p>TTT rdv ";
			
			$reponse =command("select * from  DD_rdv where (etat='A envoyer' and avant<>'Aucun') ");		
			while ($donnees = fetch_command($reponse) ) 
				{
				$idx_rdv=$donnees["idx"];	
				$date=$donnees["date"];	
				$avant=$donnees["avant"];	
				$auteur=$donnees["auteur"];	
				
				// calcul de l'heure d'envoi
				switch ($avant )
					{
					case "1H": $time_corrige = date('Y-m-d H\hi',  mktime(date("H")+1 ,date("i"), 0  , date("m"), date("d"), date ("Y")) );
								break;
					case "4H": $time_corrige = date('Y-m-d H\hi',  mktime(date("H")+4 ,date("i"), 0  , date("m"), date("d"), date ("Y")) );
								break;
					case "La veille": 
								if ( ($heure>18) and rand(0,60)==1)
									$time_corrige = date('Y-m-d H\hi',  mktime(23 ,59, 0  , date("m"), date("d")+1, date ("Y")) );
								else
									$time_corrige=$date;
								break;
					case "24H": $time_corrige = date('Y-m-d H\hi',  mktime(date("H") ,date("i"), 0  , date("m"), date("d")+1, date ("Y")) );
								break;
					case "15min": $time_corrige = date('Y-m-d H\hi',  mktime(date("H") ,date("i")+15, 0  , date("m"), date("d"), date ("Y")) );
								break;
					default : $time_corrige=$date; break;
					}
				
				echo "<br> $time_corrige : $date";
				
				// test de l'heure d'envoi
				if ( $time_corrige > $date  )
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
					else // l'index est numrique ==> origine Doc-depot 
						{
						$r1 =command("SELECT * from  r_user WHERE idx='$user_idx' ");
						if ($d1 = fetch_command($r1) ) 
							$telephone_user=$d1["telephone"];
						}	
						
					if ($telephone_user!="") // si on a trouvé un numéro de téléphone 
						{
						if ( VerifierPortable($telephone_user) 	) 	// vérification au dernier momentdu le format du n° de téléphone avant envoi
							{
							envoi_SMS( $telephone_user  , $ligne );
							ajout_log( $user_idx,"RDV - Envoi SMS au $telephone_user : '$ligne' ",$auteur);
							}
						command("UPDATE DD_rdv set etat='Envoyé' where user='$user_idx' and date='$date' ");
						}
					else
						{
						// echo "User inconnu $user_idx ";
						command("UPDATE DD_rdv set etat='' where user='$user_idx' and date='$date' ");
						}
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
			if ($delta> 30 )
				{
				if (parametre("TECH_alarme_delais_TTT")=="")
					{
					ajout_log_tech( "Traitement mail hors delais : $delta sec ");
					if ($delta> 250 )
						{
						ajout_log_tech( "Début alarme Traitement mail hors delais","P0");
						envoi_mail(parametre('DD_mail_gestinonnaire'),"Début alarme Traitement mail hors delais ","");
						ecrit_parametre("TECH_alarme_delais_TTT",time()) ;
						}
					}
				}
			else
			if ($delta< 20 )
				{
				if (parametre("TECH_alarme_delais_TTT")!="")	
					{
					ajout_log_tech( "Fin alarme Traitement mail hors delais","P0");
					envoi_mail(parametre('DD_mail_gestinonnaire'),"Fin alarme Traitement mail hors delais ","");
					}
				ecrit_parametre("TECH_alarme_delais_TTT",'') ;
				}

		ajout_log_jour(" ==================================================================================================== TTT_Alerte");
		require_once "alerte_ttt.php";
		}		
		
	// memorise dans la table des paramétres avec le préfice MONITOR_ les appels fait avec la variable ddr
	// cela permet de vérifier si un superviseur ne fonctionne plus depuis longtemps
	echo "<br>-";
	if (isset ($_GET["ddr"]))
		{  
		$ddr = $_GET["ddr"];
		ecrit_parametre("MONITOR_$ddr",time());
		}
	
	
	echo "</body>";

	
	?> 