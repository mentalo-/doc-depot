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
		affiche_un_choix($act,"Association");
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
		affiche_un_choix($act,"O.F.I.I");
		
		affiche_un_choix($act,"Préfecture");
		affiche_un_choix($act,"Police/Gendarmerie");
		affiche_un_choix($act,"Pole Emploi");		
		
		affiche_un_choix($act,"RATP");		
		affiche_un_choix($act,"Restos du coeur");
		
		affiche_un_choix($act,"SAMU Social");		
		affiche_un_choix($act,"Secours Populaire");
		affiche_un_choix($act,"Secours catholique");
		affiche_un_choix($act,"SIAO");		
		affiche_un_choix($act,"S.P.I.P");		
		
		affiche_un_choix($act,"Tuteur");
		
		affiche_un_choix($act,"Unité mobile");
		affiche_un_choix($act,"URSSAF");		
		echo "</SELECT></td>";		
		echo "</form> ";
		}


	
$liste_pays = array(
		
'0' => 'Inconnu',
'1' => 'Afghanistan',
'2' => 'Afrique Centrale',
'3' => 'Afrique du sud',
'4' => 'Albanie',
'5' => 'Algérie',
'6' => 'Allemagne',
// '' => 'Andorre',
'7' => 'Angola',
//'' => 'Anguilla',
'8' => 'Arabie Saoudite',
'9' => 'Argentine',
'10' => 'Arménie',
'11' => 'Australie',
'12' => 'Autriche',
'13' => 'Autre Asie',
'14' => 'Autre Amérique',
'15' => 'Autre Afrique',
'16' => 'Autre DOM-TOM',
'17' => 'Autre Europe',
'18' => 'Autre Océanie',

'19' => 'Azerbaidjan',

//'' => 'Bahamas',
'20' => 'Bangladesh',
//'' => 'Barbade',
'21' => 'Bahrein',
'22' => 'Belgique',
//'' => 'Belize',
'23' => 'Benin',
//'' => 'Bermudes',
'24' => 'Biélorussie',
'25' => 'Bolivie',
'26' => 'Botswana',
'27' => 'Bhoutan',
'28' => 'Boznie Herzegovine',
'29' => 'Brésil',
'30' => 'Brunei',
'31' => 'Bulgarie',
'32' => 'Burkina Faso',
'33' => 'Burundi',

//'' => 'Caiman',
'34' => 'Cambodge',
'35' => 'Cameroun',
'36' => 'Canada',
'37' => 'Canaries',
'38' => 'Cap Vert',
'39' => 'Chili',
'40' => 'Chine',
'41' => 'Chypre',
'42' => 'Colombie',
'43' => 'Comores',
'44' => 'Congo',
'45' => 'Congo RD',
//'' => 'Cook',
'46' => 'Corée du Nord',
'47' => 'Corée du Sud',
'48' => 'Costa Rica',
'49' => 'Côte d Ivoire',
'50' => 'Croatie',
'51' => 'Cuba',

'52' => 'Danemark',
'53' => 'Djibouti',
'54' => 'Dominique',

'55' => 'Egypte',
'56' => 'Emirats Arabes Unis',
'57' => 'Equateur',
'58' => 'Erythree',
'59' => 'Espagne',
'60' => 'Estonie',
'61' => 'Etats Unis',
'62' => 'Ethiopie',
'63' => 'Erythrée',

//'' => 'Falkland',
//'' => 'Feroe',
//'' => 'Fidji',
'64' => 'Finlande',
'65' => 'France',

'66' => 'Gabon',
'67' => 'Gambie',
'68' => 'Géorgie',
'69' => 'Ghana',
'70' => 'Gibraltar',
'71' => 'Gréce',
//'' => 'Grenade',
'72' => 'Groenland',
//'' => 'Guadeloupe',
//'' => 'Guam',
'73' => 'Guatemala',
//'' => 'Guernesey',
'74' => 'Guinee',
'75' => 'Guinée Bissau',
'76' => 'Guinée équatoriale',
'77' => 'Guyana',
'78' => 'Guyane Française ',

'79' => 'Haïti',
'80' => 'Hawaii',
'81' => 'Honduras',
'82' => 'Hong Kong',
'83' => 'Hongrie',

'84' => 'Inde',
'85' => 'Indonésie',
'86' => 'Iran',
'87' => 'Iraq',
'88' => 'Irlande',
'89' => 'Islande',
'90' => 'Israël',
'91' => 'Italie',

'92' => 'Jamaique',
//'' => 'Jan Mayen',
'93' => 'Japon',
//'' => 'Jersey',
'94' => 'Jordanie',

'95' => 'Kazakhstan',
'96' => 'Kenya',
'97' => 'Kirghizstan',
//'' => 'Kiribati',
'98' => 'Kosovo ',
'99' => 'Koweit',

'100' => 'Laos',
'101' => 'Lesotho',
'102' => 'Lettonie',
'103' => 'Liban',
'104' => 'Liberia',
//'' => 'Liechtenstein',
'105' => 'Lituanie',
//'' => 'Luxembourg',
'106' => 'Lybie',

//'' => 'Macao',
'107' => 'Macédoine',
'108' => 'Madagascar',
//'' => 'Madère',
'109' => 'Malaisie',
'110' => 'Malawi',
//'' => 'Maldives',
'111' => 'Mali',
//'' => 'Malte',
//'' => 'Man',
//'' => 'Mariannes du Nord', 
'112' => 'Maroc',
//'' => 'Marshall',
'113' => 'Martinique',
'114' => 'Maurice',
'115' => 'Mauritanie',
'116' => 'Mayotte',
'117' => 'Mexique',
//'' => 'Micronesie',
//'' => 'Midway',
'118' => 'Moldavie',
//'' => 'Monaco',
'119' => 'Mongolie',
//'' => 'Montserrat',
'120' => 'Mozambique',

'121' => 'Namibie',
//'' => 'Nauru',
'122' => 'Népal',
'123' => 'Nicaragua',
'124' => 'Niger',
'125' => 'Nigéria',
//'' => 'Niue',
//'' => 'Norfolk',
'126' => 'Norvège',
'127' => 'Nouvelle Calédonie',
'128' => 'Nouvelle Zelande',

'129' => 'Oman',
'130' => 'Ouganda',
'131' => 'Ouzbekistan',

'132' => 'Pakistan',
//'' => 'Palau',
'133' => 'Palestine',
'134' => 'Panama',
'135' => 'Papouasie_Nouvelle_Guinee',
'136' => 'Paraguay',
'137' => 'Pays Bas',
'138' => 'Pérou',
'139' => 'Philippines',
'140' => 'Pologne',
'141' => 'Polynésie',
'142' => 'Porto Rico',
'143' => 'Portugal',

'144' => 'Qatar',

'145' => 'Republique Dominicaine',
'146' => 'Republique Tcheque',
//'' => 'Reunion',
'147' => 'Roumanie',
'148' => 'Royaume-Uni',
'149' => 'Russie',
'150' => 'Rwanda',

'151' => 'Sahara Occidental', 
//'' => 'Sainte_Lucie',
//'' => 'Saint_Marin',
//'' => 'Salomon',
'152' => 'Salvador',
//'' => 'Samoa_Occidentales',
//'' => 'Samoa_Americaine',
'153' => 'Samoa',
//'' => 'Sao_Tome_et_Principe',
'154' => 'Sénégal',
'155' => 'Serbie',

//'' => 'Seychelles',
'156' => 'Sierra Léone', 
'157' => 'Singapour',
'158' => 'Slovaquie',
'159' => 'Slovénie',
'160' => 'Somalie',
'161' => 'Soudan',
'162' => 'Sri Lanka',
'163' => 'Suède',
'164' => 'Suisse',
'165' => 'Surinam',
'166' => 'Swaziland',
'167' => 'Syrie',

'168' => 'Tadjikistan',
'169' => 'Taiwan',
//'' => 'Tonga',
'170' => 'Tanzanie',
'171' => 'Tchad',
'172' => 'Tchéquie',
'173' => 'Tchétchénie',
'174' => 'Thailande',
'175' => 'Tibet',
//'' => 'Timor_Oriental',
'176' => 'Togo',
//'' => 'Trinite_et_Tobago',
//'' => 'Tristan da cunha',
'177' => 'Tunisie',
'178' => 'Turkmenistan',
'179' => 'Turquie',

'180' => 'Ukraine',
'181' => 'Uruguay',

//'' => 'Vanuatu',
//'' => 'Vatican',
'182' => 'Venezuela',
//'' => 'Vierges_Americaines',
//'' => 'Vierges_Britanniques',

'183' => 'Vietnam',

//'' => 'Wake',
//'' => 'Wallis et Futuma',

'184' => 'Yemen',
'185' => 'Yougoslavie',

'186' => 'Zambie',
'187' => 'Zimbabwe'	
				);
				
				
	function select_pays($auto="", $nat="")
		{		
		global $liste_pays;
		
		if ($auto!="")
			echo "<SELECT name=\"nationalite\"  onChange=\"this.form.submit();\">";	
		else
			echo "<SELECT name=\"nationalite\">";	
		if ($nat=="") $nat= "Inconnu";
		for ($i=0; isset ($liste_pays[$i]); $i++)
			affiche_un_choix($nat,$liste_pays[$i]);

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
