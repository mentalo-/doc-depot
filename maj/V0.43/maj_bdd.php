  <?php  
	{
		$version=parametre("DD_version_bdd");
			
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



				
		 }
?>