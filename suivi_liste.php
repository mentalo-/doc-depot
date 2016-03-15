<?php
	
	function nouveau2( $date_jour, $nom, $age, $nationalite)
		{
		global $bdd;

		$nom=str_replace("\"","",$nom);
		$nom_slash= addslashes2($nom);	
		$user= $_SESSION['user'];
		$modif=time();
		if ($nom!="") // le nom ne doit pas être vide
			{
			$d=mise_en_forme_date_aaaammjj( $date_jour);
			$r1 = command("SELECT DISTINCT count(*) FROM $bdd WHERE nom='$nom_slash'  ");
			$r2=nbre_enreg($r1); 
			if ($r2[0]==0) // il ne doit pas déjà exister 
					{
					$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '', '','','$user','$modif','','')");
					$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '$d', 'Visite','','$user','$modif','','1')");
					if ($age!="")
						$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'Age','$age','$user','$modif','','')");
					if ($nationalite!="Inconnu")
						$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'nationalite','$nationalite','$user','$modif','','')");					
					}
				else
					erreur("Ce nom existe déjà");
			}
		else
			erreur("Le champ nom ne doit pas être vide");
		}
		
	function choix_action_suivi($act)
		{
		global $bdd,$nom,$date_jour;
		echo " <td>  Motif  : </td>";		
		echo "<form method=\"GET\" action=\"suivi.php\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"activites\"> " ;
		echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
		echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
		echo "<td><SELECT name=\"activites\"  onChange=\"this.form.submit();\">";		
		affiche_un_choix($act,"---");		
		
		affiche_un_choix($act,"Accompagnement/bilan");			
		
		affiche_un_choix($act,"Administratif");
		affiche_un_choix($act,"Administratif : Passeport ");
		affiche_un_choix($act,"Administratif : CI  ");
		affiche_un_choix($act,"Administratif : Etat civil  ");
		affiche_un_choix($act,"Administratif : Acte de naissance  ");
		affiche_un_choix($act,"Administratif : Régularisation  ");
		affiche_un_choix($act,"Administratif : Demande d’asile ");
		affiche_un_choix($act,"Administratif : Impôts ");
		affiche_un_choix($act,"Administratif : Transports ");
		affiche_un_choix($act,"Administratif : Amendes  ");
		affiche_un_choix($act,"Administratif : Justice ");

		affiche_un_choix($act,"Alimentaire");		
		
		affiche_un_choix($act,"Domiciliation");
		affiche_un_choix($act,"Domiciliation : Instruction dossier");
		affiche_un_choix($act,"Domiciliation : Orientation");
		affiche_un_choix($act,"Domiciliation : Recours");
		
		affiche_un_choix($act,"Emploi et insertion professionnelle");
		affiche_un_choix($act,"Emploi et insertion professionnelle : CV");
		affiche_un_choix($act,"Emploi et insertion professionnelle : lettre de motivation");
		affiche_un_choix($act,"Emploi et insertion professionnelle : pôle emploi");

		affiche_un_choix($act,"Finance");			

		affiche_un_choix($act,"Juridique");			
		
		affiche_un_choix($act,"Logement et hébergement");
		affiche_un_choix($act,"Logement et hébergement : HLM");
		affiche_un_choix($act,"Logement et hébergement : SIAO");
		affiche_un_choix($act,"Logement et hébergement : DALO");
		affiche_un_choix($act,"Logement et hébergement : DAHO ");

		affiche_un_choix($act,"Matériel");			
		
		affiche_un_choix($act,"Prestations sociales");
		affiche_un_choix($act,"Prestations sociales : CAF");
		affiche_un_choix($act,"Prestations sociales : RSA");
		affiche_un_choix($act,"Prestations sociales : MDPH");
		
		affiche_un_choix($act,"Santé");
		affiche_un_choix($act,"Santé : AME");
		affiche_un_choix($act,"Santé : PUMA");
		affiche_un_choix($act,"Santé : PASS");
		affiche_un_choix($act,"Santé : réseau de santé");
		affiche_un_choix($act,"Santé : pharmacie");
		

		


//		affiche_un_choix($act,"Hygiène et soins personnels");

		/*
		affiche_un_choix($act,"Absences");			
		affiche_un_choix($act,"Accompagnement/bilan");			
		affiche_un_choix($act,"Administratif");			
		affiche_un_choix($act,"Alimentaire");			
		affiche_un_choix($act,"Attestation");			
		affiche_un_choix($act,"Autres");			
		affiche_un_choix($act,"Domiciliation");			
		affiche_un_choix($act,"Emploi");			
		affiche_un_choix($act,"Finance");			
		affiche_un_choix($act,"Formation");			
		affiche_un_choix($act,"Hébergement");			
		affiche_un_choix($act,"Immigration");			
		affiche_un_choix($act,"Juridique");			
		affiche_un_choix($act,"Matériel");			
		affiche_un_choix($act,"Médical");			
		affiche_un_choix($act,"Prise RV");	
		*/
		
		echo "</SELECT></td>";		
		echo "</form> ";
		}	
		
	function choix_reponse_suivi()
		{
		global $bdd,$nom,$nom_slash,$date_jour,$date_jour_gb;

		$reponse = command("SELECT * FROM $bdd WHERE  date='$date_jour_gb' and pres_repas='Reponse' and nom='$nom_slash' "); 	
		if ($donnees = fetch_command($reponse))
		$act=$donnees["activites"];
			else 
		$act="---";

		echo " <td> - Réponse : </td>";
		echo "<form method=\"GET\" action=\"suivi.php\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"reponse\"> " ;
		echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
		echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";

		echo "<td><SELECT name=\"reponse\"  onChange=\"this.form.submit();\">";		

		affiche_un_choix($act,"---");

		

		
		affiche_un_choix($act,"Accompagnement/bilan");
		affiche_un_choix($act,"Accompagnement administratif");
		affiche_un_choix($act,"Attestation présence");
		affiche_un_choix($act,"Attestation situation");		
		affiche_un_choix($act,"Aide financière");	
		affiche_un_choix($act,"Appel telephone");
		affiche_un_choix($act,"Autres");
		
		affiche_un_choix($act,"CV/lettre motivation");		
		affiche_un_choix($act,"Courrier/mail");		
		
		affiche_un_choix($act,"Domiciliation");
		
		affiche_un_choix($act,"Entretien");
		affiche_un_choix($act,"Impression");
		
		affiche_un_choix($act,"Instruction dossier");		
		affiche_un_choix($act,"Note sociale");		
		
		affiche_un_choix($act,"Support administratif");
		affiche_un_choix($act,"Orientation");

		affiche_un_choix($act,"Partenariat");
		affiche_un_choix($act,"Prise de RV");
		affiche_un_choix($act,"Prise de contact");
		affiche_un_choix($act,"Suivi demande en cours");
		
		echo "</SELECT></td>";		
		echo "</form> ";
		}
	
	function choix_partenaire_suivi()
		{
		global $bdd,$nom,$nom_slash,$date_jour,$date_jour_gb;

		$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour_gb' and pres_repas='partenaire' and nom='$nom_slash' "); 	
		if ($donnees = fetch_command($reponse))
		$act=$donnees["activites"];
			else 
		$act="---";

		echo " <td> - Partenaire : </td>";
		echo "<form method=\"GET\" action=\"suivi.php\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"partenaire\"> " ;
		echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
		echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";

		echo "<td><SELECT name=\"partenaire\"  onChange=\"this.form.submit();\">";		

		affiche_un_choix($act,"---");
		
		affiche_un_choix($act,"115");
		
		affiche_un_choix($act,"Ambassade");
		affiche_un_choix($act,"Agence Interim");
		affiche_un_choix($act,"Accueil de jour");
		affiche_un_choix($act,"AME");
		affiche_un_choix($act,"ASTI");		

		affiche_un_choix($act,"Banque");
				
		affiche_un_choix($act,"CAF");	
		affiche_un_choix($act,"CCAS");	
		affiche_un_choix($act,"Centre de soin");
		affiche_un_choix($act,"CPAM");
		affiche_un_choix($act,"CMU");		
		affiche_un_choix($act,"Croix-Rouge");		
		
		affiche_un_choix($act,"DALO");		
		
		affiche_un_choix($act,"EDF/GDF/autres");		
		
		affiche_un_choix($act,"Hébergement d'urgence");		
		affiche_un_choix($act,"Hopital");	
		affiche_un_choix($act,"HLM");	
		
		affiche_un_choix($act,"Impots");
		
		affiche_un_choix($act,"Justice");

		affiche_un_choix($act,"La Poste");
		
		affiche_un_choix($act,"Mairie");
		affiche_un_choix($act,"MDPH");
		affiche_un_choix($act,"Médecin");

		affiche_un_choix($act,"Ordre de Malte");
		
		affiche_un_choix($act,"Préfecture");
		affiche_un_choix($act,"Police/Gendarmerie");
		affiche_un_choix($act,"Pole Emploi");		
		
		affiche_un_choix($act,"RATP");		
		affiche_un_choix($act,"Restos du coeur");
		
		affiche_un_choix($act,"SAMU Social");		
		affiche_un_choix($act,"Secours Populaire");
		affiche_un_choix($act,"Secours catholique");
		affiche_un_choix($act,"SIAO");		
		
		affiche_un_choix($act,"Tuteur");
		
		affiche_un_choix($act,"Unité mobile");
		affiche_un_choix($act,"URSSAF");		
		echo "</SELECT></td>";		
		echo "</form> ";
		}


		
	function select_pays($auto="", $nat="")
		{		
		if ($auto!="")
			echo "<SELECT name=\"nationalite\"  onChange=\"this.form.submit();\">";	
		else
			echo "<SELECT name=\"nationalite\">";	

if ($nat=="") $nat= "Inconnu";
affiche_un_choix($nat,"Inconnu");
affiche_un_choix($nat,"Afghanistan");
affiche_un_choix($nat,"Afrique Centrale");
affiche_un_choix($nat,"Afrique du sud");
affiche_un_choix($nat,"Albanie");
affiche_un_choix($nat,"Algérie");
affiche_un_choix($nat,"Allemagne");
// affiche_un_choix($nat,"Andorre");
affiche_un_choix($nat,"Angola");
//affiche_un_choix($nat,"Anguilla");
affiche_un_choix($nat,"Arabie Saoudite");
affiche_un_choix($nat,"Argentine");
affiche_un_choix($nat,"Arménie");
affiche_un_choix($nat,"Australie");
affiche_un_choix($nat,"Autriche");
affiche_un_choix($nat,"Autre Asie");
affiche_un_choix($nat,"Autre Amérique");
affiche_un_choix($nat,"Autre Afrique");
affiche_un_choix($nat,"Autre DOM-TOM");
affiche_un_choix($nat,"Autre Europe");
affiche_un_choix($nat,"Autre Océanie");

affiche_un_choix($nat,"Azerbaidjan");

//affiche_un_choix($nat,"Bahamas");
affiche_un_choix($nat,"Bangladesh");
//affiche_un_choix($nat,"Barbade");
affiche_un_choix($nat,"Bahrein");
affiche_un_choix($nat,"Belgique");
//affiche_un_choix($nat,"Belize");
affiche_un_choix($nat,"Benin");
//affiche_un_choix($nat,"Bermudes");
affiche_un_choix($nat,"Biélorussie");
affiche_un_choix($nat,"Bolivie");
affiche_un_choix($nat,"Botswana");
affiche_un_choix($nat,"Bhoutan");
affiche_un_choix($nat,"Boznie Herzegovine");
affiche_un_choix($nat,"Brésil");
affiche_un_choix($nat,"Brunei");
affiche_un_choix($nat,"Bulgarie");
affiche_un_choix($nat,"Burkina Faso");
affiche_un_choix($nat,"Burundi");

//affiche_un_choix($nat,"Caiman");
affiche_un_choix($nat,"Cambodge");
affiche_un_choix($nat,"Cameroun");
affiche_un_choix($nat,"Canada");
affiche_un_choix($nat,"Canaries");
affiche_un_choix($nat,"Cap Vert");
affiche_un_choix($nat,"Chili");
affiche_un_choix($nat,"Chine");
affiche_un_choix($nat,"Chypre");
affiche_un_choix($nat,"Colombie");
affiche_un_choix($nat,"Comores");
affiche_un_choix($nat,"Congo");
affiche_un_choix($nat,"Congo RD");
//affiche_un_choix($nat,"Cook");
affiche_un_choix($nat,"Corée du Nord");
affiche_un_choix($nat,"Corée du Sud");
affiche_un_choix($nat,"Costa Rica");
affiche_un_choix($nat,"Côte d'Ivoire");
affiche_un_choix($nat,"Croatie");
affiche_un_choix($nat,"Cuba");

affiche_un_choix($nat,"Danemark");
affiche_un_choix($nat,"Djibouti");
affiche_un_choix($nat,"Dominique");

affiche_un_choix($nat,"Egypte");
affiche_un_choix($nat,"Emirats Arabes Unis");
affiche_un_choix($nat,"Equateur");
affiche_un_choix($nat,"Erythree");
affiche_un_choix($nat,"Espagne");
affiche_un_choix($nat,"Estonie");
affiche_un_choix($nat,"Etats Unis");
affiche_un_choix($nat,"Ethiopie");
affiche_un_choix($nat,"Erythrée");

//affiche_un_choix($nat,"Falkland");
//affiche_un_choix($nat,"Feroe");
//affiche_un_choix($nat,"Fidji");
affiche_un_choix($nat,"Finlande");
affiche_un_choix($nat,"France");

affiche_un_choix($nat,"Gabon");
affiche_un_choix($nat,"Gambie");
affiche_un_choix($nat,"Géorgie");
affiche_un_choix($nat,"Ghana");
affiche_un_choix($nat,"Gibraltar");
affiche_un_choix($nat,"Gréce");
//affiche_un_choix($nat,"Grenade");
affiche_un_choix($nat,"Groenland");
//affiche_un_choix($nat,"Guadeloupe");
//affiche_un_choix($nat,"Guam");
affiche_un_choix($nat,"Guatemala");
//affiche_un_choix($nat,"Guernesey");
affiche_un_choix($nat,"Guinee");
affiche_un_choix($nat,"Guinée Bissau");
affiche_un_choix($nat,"Guinée équatoriale");
affiche_un_choix($nat,"Guyana");
affiche_un_choix($nat,"Guyane Française ");

affiche_un_choix($nat,"Haïti");
affiche_un_choix($nat,"Hawaii");;
affiche_un_choix($nat,"Honduras");
affiche_un_choix($nat,"Hong Kong");
affiche_un_choix($nat,"Hongrie");

affiche_un_choix($nat,"Inde");
affiche_un_choix($nat,"Indonésie");
affiche_un_choix($nat,"Iran");
affiche_un_choix($nat,"Iraq");
affiche_un_choix($nat,"Irlande");
affiche_un_choix($nat,"Islande");
affiche_un_choix($nat,"Israël");
affiche_un_choix($nat,"Italie");

affiche_un_choix($nat,"Jamaique");
//affiche_un_choix($nat,"Jan Mayen");
affiche_un_choix($nat,"Japon");
//affiche_un_choix($nat,"Jersey");
affiche_un_choix($nat,"Jordanie");

affiche_un_choix($nat,"Kazakhstan");
affiche_un_choix($nat,"Kenya");
affiche_un_choix($nat,"Kirghizstan");
//affiche_un_choix($nat,"Kiribati");
affiche_un_choix($nat,"Kosovo ");
affiche_un_choix($nat,"Koweit");

affiche_un_choix($nat,"Laos");
affiche_un_choix($nat,"Lesotho");
affiche_un_choix($nat,"Lettonie");
affiche_un_choix($nat,"Liban");
affiche_un_choix($nat,"Liberia");
//affiche_un_choix($nat,"Liechtenstein");
affiche_un_choix($nat,"Lituanie");
//affiche_un_choix($nat,"Luxembourg");
affiche_un_choix($nat,"Lybie");

//affiche_un_choix($nat,"Macao");
affiche_un_choix($nat,"Macédoine");
affiche_un_choix($nat,"Madagascar");
//affiche_un_choix($nat,"Madère");
affiche_un_choix($nat,"Malaisie");
affiche_un_choix($nat,"Malawi");
//affiche_un_choix($nat,"Maldives");
affiche_un_choix($nat,"Mali");
//affiche_un_choix($nat,"Malte");
//affiche_un_choix($nat,"Man");
//affiche_un_choix($nat,"Mariannes du Nord"); 
affiche_un_choix($nat,"Maroc");
//affiche_un_choix($nat,"Marshall");
affiche_un_choix($nat,"Martinique");
affiche_un_choix($nat,"Maurice");
affiche_un_choix($nat,"Mauritanie");
affiche_un_choix($nat,"Mayotte");
affiche_un_choix($nat,"Mexique");
//affiche_un_choix($nat,"Micronesie");
//affiche_un_choix($nat,"Midway");
affiche_un_choix($nat,"Moldavie");
//affiche_un_choix($nat,"Monaco");
affiche_un_choix($nat,"Mongolie");
//affiche_un_choix($nat,"Montserrat");
affiche_un_choix($nat,"Mozambique");

affiche_un_choix($nat,"Namibie");
//affiche_un_choix($nat,"Nauru");
affiche_un_choix($nat,"Népal");
affiche_un_choix($nat,"Nicaragua");
affiche_un_choix($nat,"Niger");
affiche_un_choix($nat,"Nigéria");
//affiche_un_choix($nat,"Niue");
//affiche_un_choix($nat,"Norfolk");
affiche_un_choix($nat,"Norvège");
affiche_un_choix($nat,"Nouvelle Calédonie");
affiche_un_choix($nat,"Nouvelle Zelande");

affiche_un_choix($nat,"Oman");
affiche_un_choix($nat,"Ouganda");
affiche_un_choix($nat,"Ouzbekistan");

affiche_un_choix($nat,"Pakistan");
//affiche_un_choix($nat,"Palau");
affiche_un_choix($nat,"Palestine");
affiche_un_choix($nat,"Panama");
affiche_un_choix($nat,"Papouasie_Nouvelle_Guinee");
affiche_un_choix($nat,"Paraguay");
affiche_un_choix($nat,"Pays Bas");
affiche_un_choix($nat,"Pérou");
affiche_un_choix($nat,"Philippines");
affiche_un_choix($nat,"Pologne");
affiche_un_choix($nat,"Polynésie");
affiche_un_choix($nat,"Porto Rico");
affiche_un_choix($nat,"Portugal");

affiche_un_choix($nat,"Qatar");

affiche_un_choix($nat,"Republique Dominicaine");
affiche_un_choix($nat,"Republique Tcheque");
//affiche_un_choix($nat,"Reunion");
affiche_un_choix($nat,"Roumanie");
affiche_un_choix($nat,"Royaume-Uni");
affiche_un_choix($nat,"Russie");
affiche_un_choix($nat,"Rwanda");

affiche_un_choix($nat,"Sahara Occidental"); 
//affiche_un_choix($nat,"Sainte_Lucie");
//affiche_un_choix($nat,"Saint_Marin");
//affiche_un_choix($nat,"Salomon");
affiche_un_choix($nat,"Salvador");
//affiche_un_choix($nat,"Samoa_Occidentales");
//affiche_un_choix($nat,"Samoa_Americaine");
affiche_un_choix($nat,"Samoa");
//affiche_un_choix($nat,"Sao_Tome_et_Principe");
affiche_un_choix($nat,"Sénégal");
affiche_un_choix($nat,"Serbie");

//affiche_un_choix($nat,"Seychelles");
affiche_un_choix($nat,"Sierra Léone"); 
affiche_un_choix($nat,"Singapour");
affiche_un_choix($nat,"Slovaquie");
affiche_un_choix($nat,"Slovénie");
affiche_un_choix($nat,"Somalie");
affiche_un_choix($nat,"Soudan");
affiche_un_choix($nat,"Sri Lanka");
affiche_un_choix($nat,"Suède");
affiche_un_choix($nat,"Suisse");
affiche_un_choix($nat,"Surinam");
affiche_un_choix($nat,"Swaziland");
affiche_un_choix($nat,"Syrie");

affiche_un_choix($nat,"Tadjikistan");
affiche_un_choix($nat,"Taiwan");
//affiche_un_choix($nat,"Tonga");
affiche_un_choix($nat,"Tanzanie");
affiche_un_choix($nat,"Tchad");
affiche_un_choix($nat,"Tchéquie");
affiche_un_choix($nat,"Tchétchénie");
affiche_un_choix($nat,"Thailande");
affiche_un_choix($nat,"Tibet");
//affiche_un_choix($nat,"Timor_Oriental");
affiche_un_choix($nat,"Togo");
//affiche_un_choix($nat,"Trinite_et_Tobago");
//affiche_un_choix($nat,"Tristan da cunha");
affiche_un_choix($nat,"Tunisie");
affiche_un_choix($nat,"Turkmenistan");
affiche_un_choix($nat,"Turquie");

affiche_un_choix($nat,"Ukraine");
affiche_un_choix($nat,"Uruguay");

//affiche_un_choix($nat,"Vanuatu");
//affiche_un_choix($nat,"Vatican");
affiche_un_choix($nat,"Venezuela");
//affiche_un_choix($nat,"Vierges_Americaines");
//affiche_un_choix($nat,"Vierges_Britanniques");

affiche_un_choix($nat,"Vietnam");

//affiche_un_choix($nat,"Wake");
//affiche_un_choix($nat,"Wallis et Futuma");

affiche_un_choix($nat,"Yemen");
affiche_un_choix($nat,"Yougoslavie");

affiche_un_choix($nat,"Zambie");
affiche_un_choix($nat,"Zimbabwe");

		echo "</SELECT>";		
		}
		
	function choix_pays($nom, $nat)
		{		
		echo "<td> <b> Pays d'origine </b> : </td><td>";
		echo "<form method=\"GET\" action=\"suivi.php\" >";
		echo "<input type=\"hidden\" name=\"action\" value=\"nationalite\"> " ;
		echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
		select_pays("auto_submit",$nat);

		echo "</td>";		
		echo "</form> ";
		}

		?>
