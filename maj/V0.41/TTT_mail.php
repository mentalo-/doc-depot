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
	
	// supprime les rdv envoy� ayant plus d'un mois d'ancienet�
	function purge_rdv()
		{
		echo "<br>Purge Rdv";
		$reponse =command("","select * from  DD_rdv where etat='Envoy�' ");		
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
			// envoi toutes les heures d'un  mail vers la gatewaysms avec envoi de mail sur elle m�me qui va g�n�rer un mmail en retour
			if (date('Y-m-d-h',  time()) != date('Y-m-d-h',  $ancien_ttt ))
				{
				envoi_SMS( parametre('DD_numero_tel_sms') , "Supervision gatewaysms");
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
				
				// test de la date 
				if ( $time_corrige > $date  )
					{
					$ligne=stripcslashes($donnees["ligne"]);
					$user_idx=$donnees["user"];
					$r1 =command("","SELECT * from  r_user WHERE idx='$user_idx' ");
					if ($d1 = mysql_fetch_array($r1) ) 
						{
						$telephone_user=$d1["telephone"];
						$date=$donnees["date"];

						envoi_SMS( $telephone_user  , $ligne );
						ajout_log( $user_idx,"RDV - Envoi SMS au $telephone_user : '$ligne' ");

						command("","UPDATE DD_rdv set etat='Envoy�' where user='$user_idx' and date='$date' ");
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
			// attention cette action doit �tre en dernier sinon mise � jour z_ttt ne fonctionne pas !!!
			decremente_echec_cx ($delta/15);
			if ($delta> 30 )
				ajout_log_tech( "Traitement mail hors delais : $delta sec ");
			}

	
	// Affichage des principaux indicaturs
	titre_kpi();
	
	$date=date('Y-m-d',  mktime(0,0,0 , date("m"), date("d")-1, date ("Y")));
	kpi("$date");
	
	$date=date('Y-m-d',  mktime(0,0,0 , date("m"), date("d")-7, date ("Y")));
	kpi("$date");
	

	echo "</body>";

	
	?> 