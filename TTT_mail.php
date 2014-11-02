  <?php  

	echo "<head>";
	echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"15\">";
	echo "</head><body>";
	include "connex_inc.php";
	include 'general.php';
 	include 'include_crypt.php';
	include 'include_charge_image.php';	
	include 'include_mail.php';
	include 'exploit.php';
	
	// supprime les rdv envoyé ayant plus d'un mois d'ancieneté
	function purge_rdv()
		{
		commentaire_html("purge_rdv");

		echo "<br>Purge Rdv";
		$reponse =command("","select * from  DD_rdv where etat='Envoyé' ");		
		while ($donnees = mysql_fetch_array($reponse) ) 
				{
				$date =$donnees["date"];	
				$ilyaunmois =date('Y-m-d H\hi',  mktime(0,0,0 , date("m")-1, date("d"), date ("Y")));
				// test de la date 
				if ( $ilyaunmois > $date  )
					{
					$user =$donnees["user"];	
					command("","delete from DD_rdv where user='$user' and date='$date' ");
					}
				}
		}
	
	
	function purge_fichiers_temporaires($dir)
		{
		commentaire_html("purge_fichiers_temporaires");
		
		// suppression des fichiers temporaire
		$l= date('Y-m-d H:i',  mktime(date ("H"),date ("i")-2, date ("s") , date("m"), date("d"), date ("Y") ));
		foreach(glob($dir.'*.*') as $v)
			{
			if (date ("Y-m-d H:i:s", filemtime($v))<$l)
				{
				echo "<p>Purge $v";
				unlink($v);
				}
			}
		}
	
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
		
		if (date('Y-m-d-h',  time()) != date('Y-m-d-h',  $ancien_ttt ))
			if ($heure==0)
				{
				ajout_log_tech( "Traitement Purges");
				purge_rdv();
				purge_log();
				purge_dde_acces(); 					
				purge_backup_tables();

				supp_fichier('tmp/hier.txt');
				copy('tmp/log.txt','tmp/hier.txt');
				supp_fichier('tmp/log.txt');

				ecrit_parametre("TECH_nb_mail_envoyes",0) ;
				ecrit_parametre("TECH_nb_sms_envoyes",0) ;
				}
			
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

		commentaire_html( "Purge temporaire");
		purge_fichiers_temporaires("dir_zip/");		
		purge_fichiers_temporaires("tmp/");		
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
						envoi_mail(parametre('DD_mail_gestinonnaire'),"Début alarme délais supervision gateway sms ","");
						ajout_log_tech( "Dépassement délais supervision gateway SMS ","P0");
						ecrit_parametre("TECH_alarme_supervision_sms",time()) ;
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
			
			$reponse =command("","select * from  DD_rdv where etat='A envoyer' ");		
			while ($donnees = mysql_fetch_array($reponse) ) 
				{
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
					case "La veille": $time_corrige = date('Y-m-d H\hi',  mktime(20 ,rand(0,59), 0  , date("m"), date("d"), date ("Y")) );
								break;
					case "24H": $time_corrige = date('Y-m-d H\hi',  mktime(date("H") ,date("i"), 0  , date("m"), date("d")+1, date ("Y")) );
								break;
					case "15min": $time_corrige = date('Y-m-d H\hi',  mktime(date("H") ,date("i")+15, 0  , date("m"), date("d"), date ("Y")) );
								break;
					}
				
				// test de l'heure d'envoi
				if ( $time_corrige > $date  )
					{
					$ligne=stripcslashes($donnees["ligne"]);
					$user_idx=$donnees["user"];
					$r1 =command("","SELECT * from  r_user WHERE idx='$user_idx' ");
					if ($d1 = mysql_fetch_array($r1) ) 
						{
						$telephone_user=$d1["telephone"];
						
						$date=$donnees["date"];
						if ( VerifierPortable($telephone_user) 	) 	// vérification au dernier momentdu le format du n° de téléphone avant envoi
								{
								envoi_SMS( $telephone_user  , $ligne );
								ajout_log( $user_idx,"RDV - Envoi SMS au $telephone_user : '$ligne' ",$auteur);
								}

						command("","UPDATE DD_rdv set etat='Envoyé' where user='$user_idx' and date='$date' ");
						}
					else
						echo "User inconnu $user_idx ";
					}
				}
			echo "<p>";
			}
		
		// -------------------------------------------------------------------traitement des mails et tempo de connexion


		if ($time_ttt-$ancien_ttt>14) 
			{
			ecrit_parametre("TECH_date_dernier_ttt","$time_ttt") ;
		
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
				{
				if (parametre("TECH_alarme_delais_TTT")!="")	
					{
					ajout_log_tech( "Fin alarme Traitement mail hors delais","P0");
					envoi_mail(parametre('DD_mail_gestinonnaire'),"Fin alarme Traitement mail hors delais ","");
					}
				ecrit_parametre("TECH_alarme_delais_TTT",'') ;
				}
			
			}

	commentaire_html("TTT: Affiche Indicateurs");

	// Affichage des principaux indicaturs
	titre_kpi();
	
	$date=date('Y-m-d',  mktime(0,0,0 , date("m"), date("d")-1, date ("Y")));
	kpi("$date");
	
	$date=date('Y-m-d',  mktime(0,0,0 , date("m"), date("d")-7, date ("Y")));
	kpi("$date");
	
	echo "<p> Nbre de mails envoyés : ". parametre("TECH_nb_mail_envoyes");
	echo "<p> Nbre de SMS envoyés : ". parametre("TECH_nb_sms_envoyes");
	

	echo "</body>";

	
	?> 