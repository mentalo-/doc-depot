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

		$ancien_ttt=parametre("TECH_date_dernier_ttt");
		$delta= (time()-$ancien_ttt);
		Echo "Dernier traitment z_TTT :".date('Y-m-d H\hi.s',$ancien_ttt)." Il y a ". $delta . "sec<p>";	
		
		if (date('Y-m-d',  time()) != date('Y-m-d',  $ancien_ttt ))
			{
			ajout_log_tech( "Traitement Changement jour");
			purge_rdv();
			purge_log();
			purge_dde_acces(); 					
			backup_tables();
			purge_backup_tables();
			ctrl_signature();
			}
			
		Echo "<p>Purge temporaire";
		purge_fichiers_temporaires("dir_zip/");		
		purge_fichiers_temporaires("tmp/");		
		purge_fichiers_temporaires("upload_tmp/");		
		
		$heure=date('H',  time());
		if (($heure>6) && ($heure<20))
			{
			// envoi toutes les heures d'un  mail vers la gatewaysms avec envoi de mail sur elle même qui va générer un mmail en retour
			if (date('Y-m-d-h',  time()) != date('Y-m-d-h',  $ancien_ttt ))
				{
				ecrit_parametre('TECH_msg_supervision_gatewaysms', random_chaine(6).' '.random_chaine(3).' '.random_chaine(6).'.');
			
				envoi_SMS( parametre('DD_numero_tel_sms') ,parametre('TECH_msg_supervision_gatewaysms').". ".date('Y-m-d H\hi.s',time()));
				ecrit_parametre('TECH_dernier_envoi_supervision', time() );
				}
			
			$td_envoi=parametre('TECH_dernier_envoi_supervision');
			if ($td_envoi!='') // s'il y a un marquage de l'heure d'envoi
				if ((time()-$td_envoi ) >10*60 ) // s'il y a eu plus de 10 minutes depuis l'envoi
					{
					envoi_mail(parametre('DD_mail_gestinonnaire'),"Dépassement délais supervision gateway sms ","");
					ajout_log_tech( "Dépassement délais supervision gateway SMS ");

					ecrit_parametre('TECH_dernier_envoi_supervision', '' );
					}
		
			if (date('Y-m-d-h',  time()) != date('Y-m-d-h',  $ancien_ttt ))
				if ($heure==19)
					exploit_envoi_mail_synthese();
					
		// ----------------------------------------------------------------------- traitement des RDV				
			Echo "<p>TTT rdv ";
			
			$reponse =command("","select * from  DD_rdv where etat='A envoyer' ");		
			while ($donnees = mysql_fetch_array($reponse) ) 
				{
				$date=$donnees["date"];	
				$avant=$donnees["avant"];	
				
				// calcul de l'heure d'envoi
				switch ($avant )
					{
					case "1H": $time_corrige = date('Y-m-d H\hi',  mktime(date("H")+1 ,date("i"), 0  , date("m"), date("d"), date ("Y")) );
								break;
					case "4H": $time_corrige = date('Y-m-d H\hi',  mktime(date("H")+4 ,date("i"), 0  , date("m"), date("d"), date ("Y")) );
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
								ajout_log( $user_idx,"RDV - Envoi SMS au $telephone_user : '$ligne' ");
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
						ajout_log_tech( "Début alarme Traitement mail hors delais");
						envoi_mail(parametre('DD_mail_gestinonnaire'),"Début alarme Traitement mail hors delais ","");
						ecrit_parametre("TECH_alarme_delais_TTT",time()) ;
						}
					}
				}
			else
				{
				if (parametre("TECH_alarme_delais_TTT")!="")				
					ajout_log_tech( "Fin alarme Traitement mail hors delais");
				ecrit_parametre("TECH_alarme_delais_TTT",'') ;
				}
			
			}

	
	// Affichage des principaux indicaturs
	titre_kpi();
	
	$date=date('Y-m-d',  mktime(0,0,0 , date("m"), date("d")-1, date ("Y")));
	kpi("$date");
	
	$date=date('Y-m-d',  mktime(0,0,0 , date("m"), date("d")-7, date ("Y")));
	kpi("$date");
	

	echo "</body>";

	
	?> 