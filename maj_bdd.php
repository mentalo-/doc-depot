  <?php 
  
  		require_once  'general.php';
		require_once "connex_inc.php"; 
		require_once 'exploit.php';		
	
		function maj_version( $version )
			{
			ajout_log_tech( "Montée de version BDD : $version" , "P0");

			echo "<p>MAJ -> $version";
			ecrit_parametre("DD_version_bdd", $version);
			ecrit_parametre("DD_version_portail", $version);
			return ($version);
			}
			
		$version=parametre("DD_version_bdd");
		echo "<p> Version actuelle -> $version <br>";
		
		if ($version<"V0.32")
				{
				if (!file_exists ("backup_chi"))
					mkdir("backup_chi");
				backup_tables();
				command("1","CREATE TABLE IF NOT EXISTS `z_log_t` ( `date` text, `ligne` text, `ip` text NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
				command("1","CREATE TABLE IF NOT EXISTS `r_lien` ( `date` text, `organisme` text, `user` text NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
				command("1","ALTER TABLE r_attachement ADD hash TEXT not null");
				command("1","ALTER TABLE r_attachement ADD hash_chi TEXT not null");
				command("1","ALTER TABLE r_attachement ADD hash_pdf TEXT not null");
				command("1","ALTER TABLE r_attachement ADD hash_prot TEXT not null");
				command("1","ALTER TABLE r_attachement ADD hash_mini TEXT not null");
				command("1","ALTER TABLE r_user change nss last_cx TEXT not null");
				command("1","ALTER TABLE r_user ADD last_hash_ctrl TEXT not null");
				command("1","ALTER TABLE z_version ADD date_cg TEXT not null");
				command("1","UPDATE z_version set bdd='V0.32' ");	
				command("1","UPDATE z_version set date_cg='2014-03-01' ");	
				ctrl_signature()				;
				$version="V0.32";
				}
				
		if ($version<"V0.35")
				{
				command("1","ALTER TABLE r_sequence ADD rdv TEXT not null");
				command("1","ALTER TABLE r_sequence ADD bug TEXT not null");

				command("1","CREATE TABLE IF NOT EXISTS `DD_rdv` ( `idx` text,`user` text, `auteur` text, `date` text,  `ligne` text , `avant` text, `etat` text) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

				command("1","CREATE TABLE IF NOT EXISTS `DD_param` ( `nom` text, `valeur` text) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

				command("1","INSERT INTO DD_param VALUES ('DD_taille_SQL_Mo', '200' ) ");
				command("1","INSERT INTO DD_param VALUES ('DD_taille_Espace_Go', '100' ) ");

				command("1","INSERT INTO DD_param VALUES ('DD_numero_tel_sms', '+33698474312' ) ");

				command("1","INSERT INTO DD_param VALUES ('MAX_FICHIER', '60' ) ");
				command("1","INSERT INTO DD_param VALUES ('TIME_OUT', '1800' ) ");
				command("1","INSERT INTO DD_param VALUES ('TIME_OUT_BENE', '300' ) ");
				command("1","INSERT INTO DD_param VALUES ('TAILLE_FICHIER', '8000000' ) ");
				command("1","INSERT INTO DD_param VALUES ('TAILLE_FICHIER_dropzone', '8' ) ");

				command("1","INSERT INTO DD_param VALUES ('DD_mail_gestinonnaire', 'jm@fixeo.com' ) ");
				command("1","INSERT INTO DD_param VALUES ('DD_mail_pour_gateway_sms', 'gatewaysms@doc-depot.com' ) ");

				command("1","ALTER TABLE z_bug ADD date TEXT not null");
				command("1","ALTER TABLE z_bug ADD domaine TEXT not null");
				command("1","ALTER TABLE z_bug ADD version TEXT not null");				
				command("1","ALTER TABLE z_bug ADD commentaire TEXT not null");				
				command("1","ALTER TABLE z_bug ADD fonction	TEXT not null");

				command("1","ALTER TABLE r_attachement ADD deposeur TEXT not null");

				command("1","UPDATE z_version set bdd='V0.35' ");	
				command("1","UPDATE z_version set portail='V0.35' ");	
				$version="V0.35";
				}
				
				
		if ($version<"V0.36")
				{
				$version="V0.36";
				command("1","ALTER TABLE `cx` ADD  INDEX ( `id` (32)) ;");
				command("1","ALTER TABLE `dd_rdv` ADD UNIQUE INDEX ( `idx` (10) ) ;");			
				command("1","ALTER TABLE `r_sequence` ADD  INDEX ( `user` ) ;");	
				command("1","ALTER TABLE `z_version` ADD  INDEX ( `bdd` (10)) ;");		
	
				command("1","ALTER TABLE `dd_param` ADD  UNIQUE INDEX ( `nom` (32) ) ;");				
				command("1","ALTER TABLE `log` ADD  INDEX ( `date` (32)) ;");
				command("1","ALTER TABLE `r_attachement` ADD  INDEX ( `ref` (10) ) ;");				
				command("1","ALTER TABLE `r_dde_acces` ADD  INDEX ( `user` (10)) ;");		
				command("1","ALTER TABLE `r_lien` ADD  INDEX ( `user` (10)) ;");	
				command("1","ALTER TABLE `r_organisme` ADD UNIQUE INDEX ( `idx` ) ;");
				command("1","ALTER TABLE `r_referent` ADD UNIQUE INDEX ( `idx` ) ;");	
				command("1","ALTER TABLE `r_sms` ADD  INDEX ( `idx` ) ;");	
				command("1","ALTER TABLE `r_user` ADD UNIQUE INDEX ( `idx` ) ;");					
				command("1","ALTER TABLE `z_bug` ADD UNIQUE INDEX ( `idx` (10) ) ;");					
				command("1","ALTER TABLE `z_log_t` ADD  INDEX ( `date` (32)) ;");
				command("1","ALTER TABLE `z_ttt` ADD  INDEX ( `date` (32)) ;");

				ecrit_parametre("DD_version_bdd", "V0.36");
				ecrit_parametre("DD_version_portail", "V0.36");
				ecrit_parametre("DD_date_cg", "2014-03-01");

				$reponse =command("","select * from  z_TTT ");
				$donnees = mysql_fetch_array($reponse) ;
				$ancien_ttt=$donnees["date"];
				ecrit_parametre("TECH_date_dernier_ttt", "$ancien_ttt");

				$reponse =command("","select * from  r_sequence ");
				$donnees = mysql_fetch_array($reponse) ;

				ecrit_parametre("TECH_sequence_upload", $donnees["upload"]);	
				ecrit_parametre("TECH_sequence_user", $donnees["user"]);	
				ecrit_parametre("TECH_sequence_referent", $donnees["referent"]);	
				ecrit_parametre("TECH_sequence_organisme", $donnees["organisme"]);	
				ecrit_parametre("TECH_sequence_numero", $donnees["numero"]);	
				ecrit_parametre("TECH_sequence_rdv", $donnees["rdv"]);	
				ecrit_parametre("TECH_sequence_bug", $donnees["bug"]);	


				ecrit_parametre('DEF_SERVEUR_MAIL_TTT',"{ssl0.ovh.net:993/imap/ssl}INBOX");
				ecrit_parametre('DEF_ADRESSE_MAIL_TTT',"fixeo@doc-depot.com");
				ecrit_parametre('DEF_PW_MAIL_TTT',"55364963");
				
				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);			
				}
				
		if ($version<"V0.37")
				{
				$version="V0.37";
				
				command("1","ALTER TABLE r_attachement ADD sens VARCHAR(1) DEFAULT 'P' ");
				command("1","ALTER TABLE r_sms ADD num_seq TEXT not null");
				ecrit_parametre('TECH_sequence_notes',"1");
				ecrit_parametre('DD_mail_fonctionnel',"assistance@doc-depot.com");

				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);
				}
				
		if ($version<"V0.38")
				{
				$version="V0.38";
				
				command("1","ALTER TABLE r_attachement ADD idx TEXT not null ");
				command("1","ALTER TABLE r_attachement ADD user TEXT not null ");
				command("1","ALTER TABLE r_attachement ADD type TEXT not null ");
				command("1","ALTER TABLE r_user ADD tel_valide BOOLEAN not null");
				command("1","ALTER TABLE r_organisme ADD convention BOOLEAN not null");
				
				$reponse =command("1","select * from r_attachement  ");
				while($donnees = mysql_fetch_array($reponse) )
					{
					$ref=$donnees["ref"];
					$num=$donnees["num"];
					
					if ($ref[0]=='A') $type='A'; else $type='P';
					$u = substr($ref,strpos($ref,"-")+1 );
					$tabfile = explode('.',$num);
					$idx = $tabfile[0];
					
					command("1","update r_attachement SET idx='$idx', user='$u', type='$type' where num='$num' ");
					}

				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);
				}
				
		if ($version<"V0.39")
				{
				$version="V0.39";
				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);
				}			
				
		if ($version<"V0.40")
				{
				$version="V0.40";
				ecrit_parametre('TECH_msg_supervision_gatewaysms',"Supervision gatewaysms");
				ecrit_parametre('TECH_dernier_envoi_supervision',"");

				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);
				}	

		if ($version<"V0.41")
				{
				$version="V0.41";
				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);
				}					
				
		if ($version<"V0.42")
				{
				$version="V0.42";
				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);
				}	
		if ($version<"V0.43")
				{
				$version="V0.43";
				ecrit_parametre("TECH_alarme_delais_TTT",'') ;
				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);
				}	

		if ($version<"V0.44")
				{
				$version="V0.44";
				ecrit_parametre("TECH_nb_mail_envoyes",0) ;
				ecrit_parametre("TECH_nb_sms_envoyes",0) ;
				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);
				}	

		if ($version<"V0.45")
				{
				$version="V0.45";

				command("1","ALTER TABLE z_log_t ADD prio TEXT not null ");
				
				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);
				}	

		if ($version<"V0.46")
				{
				$version="V0.46";

				ecrit_parametre("Formation_num_structure",19) ;
				ecrit_parametre("TECH_alarme_supervision_sms","") ;
				
				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);
				}	

		if ($version<"V0.47")
				{
				$version="V0.47";
				// backup_tables(); // A utiliser si chnagement de structure ou de contenu de la base

				ecrit_parametre("DD_tel_alarme1", "+33625841153");
				ecrit_parametre("DD_tel_alarme2", "");
				ecrit_parametre('DD_numero_tel_sms_E',"+33651256164");
				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);
				}
				
		if ($version<"V0.48")
				{
				$version="V0.48";
				
				// backup_tables(); // A utiliser si chnagement de structure ou de contenu de la base
				ecrit_parametre('FORM_msg_rdv','Penser à supprimer les documents inutiles');
				ecrit_parametre('FORM_tel_rdv','0651256164');
				ecrit_parametre("Tech_date_envoi_synthses", "");
				
				
				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);
				}	

		if ($version<"V0.49")
				{
				$version="V0.49";

				backup_tables(false); // A utiliser si chnagement de structure ou de contenu de la base

				command("1","ALTER TABLE r_user ADD cg_valide TEXT not null ");
				command("1","ALTER TABLE r_user ADD type_user TEXT not null ");
				command("1","ALTER TABLE r_organisme ADD offre TEXT not null ");
				ecrit_parametre("DD_nbre_mail_jour_max", 500);
				ecrit_parametre("DD_nom_environnement", "");
				command("1","CREATE TABLE IF NOT EXISTS `z_traduire` ( `idx` text,`fr` text, `gb` text, `es` text, `it` text, `de` text, `ru` text) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
				ecrit_parametre("TECH_sequence_traduire", '500');	
				ecrit_parametre("TECH_identite_environnement", 'DOC-DEPOT');	
				
				// -------------------------------------------
				echo "<p>MAJ -> $version";
				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);
				}
				
		if ($version<"V1.01")
				{
				$version="V1.01";

				// -------------------------------------------
				echo "<p>MAJ -> $version";
				ecrit_parametre("DD_version_bdd", $version);
				ecrit_parametre("DD_version_portail", $version);
				}

		// 	----------------------------------------------------------------------------------------------
		$nelle_version="V1.02";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version
				command("ALTER TABLE z_traduire ADD original TEXT not null ","1");
				command("ALTER TABLE z_traduire ADD commentaire TEXT not null ","1");
				command("UPDATE `z_traduire` SET `original`=`fr` WHERE 1","1");
				
				command("ALTER TABLE r_user ADD langue TEXT not null ","1");
				command("UPDATE `r_user` SET `langue`=`fr` WHERE `langue`=``","1");

				command("CREATE TABLE IF NOT EXISTS `fct_fissa` ( `organisme` text NOT NULL, `support` text NOT NULL,  `libelle` text NOT NULL,  `acteur` text NOT NULL,  `beneficiaire` text NOT NULL,  `mails_rapports` text NOT NULL,  `mails_rapport_detaille` text NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1; ","1");

				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}
		// 	----------------------------------------------------------------------------------------------
		$nelle_version="V1.03";
		if ($version<=$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version
				ecrit_parametre("DD_nbre_echec_max_par_periode", "10");
				ecrit_parametre("DD_periode_mesure_nbre_echec_max", "300");
				ecrit_parametre("TECH_nbre_echec_sur_periode", "0");
				ecrit_parametre("TECH_date_mesure_echec_sur_periode", time());

				ecrit_parametre("DD_seuil_tempo_cx_max", "116");
				
				ecrit_parametre("DD_alerte_extension_fichier", ";php;php3;php4;js;ls;cgi;pl;phtml;exe;com;dll;asp;aspx;htaccess;sh;");

				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}		// 	----------------------------------------------------------------------------------------------

				$nelle_version="V1.04";
		if ($version<=$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version

				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}
				
		$nelle_version="V1.05";
		if ($version<=$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version
				ecrit_parametre("TECH_sequence_offre", '1');	
				ecrit_parametre("TECH_sequence_creneau", '1');	
				ecrit_parametre("TECH_sequence_usager", '1');	
				
				ecrit_parametre("nb_echec_alarme_acces_bal",'10');
				
				command("CREATE TABLE IF NOT EXISTS `fct_fissa` (
						  `organisme` text NOT NULL,
						  `support` text NOT NULL,
						  `libelle` text NOT NULL,
						  `acteur` text NOT NULL,
						  `beneficiaire` text NOT NULL,
						  `mails_rapports` text NOT NULL,
						  `mails_rapport_detaille` text NOT NULL
						) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}
				
				
				
		$nelle_version="V1.06";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version
			
				command("CREATE TABLE IF NOT EXISTS `fct_fissa` (
						  `organisme` text NOT NULL,
						  `support` text NOT NULL,
						  `libelle` text NOT NULL,
						  `acteur` text NOT NULL,
						  `beneficiaire` text NOT NULL,
						  `mails_rapports` text NOT NULL,
						  `mails_rapport_detaille` text NOT NULL
						) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}
				
				
		$nelle_version="V1.07";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version
			

				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}
				
		$nelle_version="V1.08";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version
			

				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}		
		
		$nelle_version="V1.09";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version
				command("DROP TABLE `cc_alerte` ");
				command("CREATE TABLE IF NOT EXISTS `cc_alerte` (
						  `creation` text NOT NULL,
						  `tel` text NOT NULL,
						  `dept` text NOT NULL,
						  `sueil` text NOT NULL,
						  `dernier_ttt` text NOT NULL,
						  `dernier_envoi` text NOT NULL,
						  `debut_alerte` text NOT NULL,
						  `stop` text NOT NULL,
						  `ip` text NOT NULL,
						  `modif` text NOT NULL
						) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
						
				ecrit_parametre("DD_alerte_dept",'1');

				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}
				
		$nelle_version="V1.10";
		if ($version<$nelle_version)
				{
				backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version
						
				command("ALTER TABLE effectif ADD user TEXT not null ","1");
				command("ALTER TABLE ZZ_SEC_CATH ADD user TEXT not null ","1");
				command("ALTER TABLE ZZ_assol ADD user TEXT not null ","1");

				command("ALTER TABLE effectif ADD modif TEXT not null ","1");
				command("ALTER TABLE ZZ_SEC_CATH ADD modif TEXT not null ","1");
				command("ALTER TABLE ZZ_assol ADD modif TEXT not null ","1");
				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}
				
		$nelle_version="V1.11";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version

				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}
				
		$nelle_version="V1.12";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version

				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}
				
		$nelle_version="V1.13";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version
				command("ALTER TABLE effectif ADD activites TEXT not null ","1");
				command("ALTER TABLE ZZ_SEC_CATH ADD activites TEXT not null ","1");
				command("ALTER TABLE ZZ_assol ADD activites TEXT not null ","1");
				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}		
		$nelle_version="V1.14";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version

				
				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}		
				
		$nelle_version="V1.15";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version
				ecrit_parametre("DD_msg_1ere_page",'');

				
				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}		
				
		$nelle_version="V1.16";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version

				
				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}				
		$nelle_version="V1.17";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version

				
				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}

		$nelle_version="V1.18";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version

				
				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}				
				
		$nelle_version="V1.19";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version

				
				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}						
		$nelle_version="V1.20";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version

				
				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}				
		
		$nelle_version="V1.21";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version

				
				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}			
				
		$nelle_version="V1.22";
		if ($version<$nelle_version)
				{
				//backup_tables(false);  // A utiliser si changement de structure ou de contenu de la base
				
				// ------------------------------------------- Bloc Spécifique à la montée de version

				
				// ------------------------------------------- Fin bloc spécifique

				// ------------------------------------------- Bloc générique
				$version=maj_version($nelle_version);
				}				
				
?>