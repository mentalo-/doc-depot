  <?php  
// traduire() : Ok (inutile)

	echo "<head>";
	echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"15\">";
	echo "</head><body>";
	include "connex_inc.php";
	include 'general.php';
 	include 'include_crypt.php';
	include 'include_charge_image.php';	
	include 'include_mail.php';
	include 'exploit.php';
	
	// supprime les rdv envoy� ayant plus d'un mois d'ancienet�
	function purge_rdv()
		{
		commentaire_html("purge_rdv");

		echo "<br>Purge Rdv";
		$reponse =command("select * from  DD_rdv where etat='Envoy�' ");		
		while ($donnees = fetch_command($reponse) ) 
				{
				$date =$donnees["date"];	
				$ilyaunmois =date('Y-m-d H\hi',  mktime(0,0,0 , date("m")-1, date("d"), date ("Y")));
				// test de la date 
				if ( $ilyaunmois > $date  )
					{
					$user =$donnees["user"];	
					command("delete from DD_rdv where user='$user' and date='$date' ");
					}
				}
		}
	
	
	function purge_bdd_fissa()
		{	
		commentaire_html("purge_dbb_fissa");
		$avant= date('Y-m-d',  mktime( 0,0, 0 , date("m"), date("d"), date ("Y")-2 ));
		echo "<br>Purge BDD FISSA";
		$reponse =command("select * from fct_fissa");		
		while ($donnees = fetch_command($reponse) ) 
				{
				$support =$donnees["support"];		
				$crit=" ( not (nom like '%(A)%')) and (nom<>'Synth') and (nom<>'Mail')   ";
				$r1 =command("update $support set commentaire='' where  $crit and date<'$avant' ");		
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

				supp_fichier('tmp/hier.txt');
				rename('tmp/log.txt','tmp/hier.txt');

				ajout_log_tech( "Mails envoy�s:".parametre("TECH_nb_mail_envoyes")." / ".parametre("DD_nbre_mail_jour_max"));
				ajout_log_tech( "SMS envoy�s:".parametre("TECH_nb_sms_envoyes"));
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

		if (date('Y-m-d-h',  time()) != date('Y-m-d-h',  $ancien_ttt ))
			if ($heure==3)
				{
				ajout_log_tech( "purge BdD FISSA");
				purge_bdd_fissa();
				}
				
		commentaire_html( "Purge temporaire");
		purge_fichiers_temporaires("dir_zip/");		
		purge_fichiers_temporaires("tmp/");		
		purge_fichiers_temporaires("upload_tmp/");		
		
		$td_envoi=parametre('TECH_dernier_envoi_supervision');

			// envoi aleatoir  d'un  mail vers la gatewaysms avec envoi de mail sur elle m�me qui va g�n�rer un mmail en retour
			if ( // il ne faut pas que l'on soit d�j� en traitement d'une supervision 
				($td_envoi=='') 
				&& // il faut � minima une heure entre chaque sms
 				( (time()-parametre('TECH_dernier_envoi_supervision_effectif') ) > 3600 ) 
				&& 
				( // frequence plus �lev�e en journ�e
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
						envoi_mail(parametre('DD_mail_gestinonnaire'),"D�but alarme d�lais supervision gateway sms","");
						ajout_log_tech( "D�passement d�lais supervision gateway SMS ","P0");
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
			
			$reponse =command("select * from  DD_rdv where etat='A envoyer' ");		
			while ($donnees = fetch_command($reponse) ) 
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
					$r1 =command("SELECT * from  r_user WHERE idx='$user_idx' ");
					if ($d1 = fetch_command($r1) ) 
						{
						$telephone_user=$d1["telephone"];
						
						$date=$donnees["date"];
						if ( VerifierPortable($telephone_user) 	) 	// v�rification au dernier momentdu le format du n� de t�l�phone avant envoi
								{
								envoi_SMS( $telephone_user  , $ligne );
								ajout_log( $user_idx,"RDV - Envoi SMS au $telephone_user : '$ligne' ",$auteur);
								}

						command("UPDATE DD_rdv set etat='Envoy�' where user='$user_idx' and date='$date' ");
						}
					else
						echo "User inconnu $user_idx ";
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
			// attention cette action doit �tre en dernier sinon mise � jour z_ttt ne fonctionne pas !!!
			decremente_echec_cx ($delta/15);
			if ($delta> 30 )
				{
				if (parametre("TECH_alarme_delais_TTT")=="")
					{
					ajout_log_tech( "Traitement mail hors delais : $delta sec ");
					if ($delta> 250 )
						{
						ajout_log_tech( "D�but alarme Traitement mail hors delais","P0");
						envoi_mail(parametre('DD_mail_gestinonnaire'),"D�but alarme Traitement mail hors delais ","");
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
		ajout_log_jour(" ==================================================================================================== TTT_Alerte");
		require_once "alerte_ttt.php";
		
	echo "<br>-";
	if (isset ($_GET["ddr"]))
		{  
		$ddr = $_GET["ddr"];
		ecrit_parametre("MONITOR_$ddr",time());
		}
	
	echo "</body>";

	
	?> 