<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0trict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

    <head>
	 <?php
include 'calendrier.php';
include 'general.php';
include 'inc_style.php';	 
	
		if (isset ($_GET["action"])) $action=$_GET["action"]; else  	$action="";	
		
		if (isset ($_GET["nom"]))
			{
			$nom = $_GET["nom"];
			echo "<title> $nom </title>";
			}
	     else
			echo "<title> Suivi </title>";
		
		if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > TIME_OUT)) 
			$_SESSION['pass']=false;
		$_SESSION['LAST_ACTIVITY'] = time();
		
		$refr=TIME_OUT+10;

		echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"$refr\">";		
		echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
		echo "</head><body>";
		
	function liste_type($val_init ="" )
		{
		echo "<SELECT name=\"type\"  onChange=\"this.form.submit();\">";
		affiche_un_choix($val_init,"B�n�ficiaire");
		affiche_un_choix($val_init,"B�n�ficiaire femme");
		echo "</SELECT>";
		}


	
	function nouveau( $nom )
		{
		global $bdd;
		
		$nom=str_replace("\"","",$nom);
		$nom_slash= addslashes2($nom);	

		if (($nom!="") && (!is_numeric($nom)))
			{
			$r1 = command("SELECT DISTINCT count(*) FROM $bdd WHERE date='0000-00-00' and nom='$nom_slash' ");
			$r2=nbre_enreg($r1); 
			if ($r2[0]==0)
				{
				$user= $_SESSION['user_idx'];
				$modif=time();
				$cmd = "INSERT INTO `$bdd`  VALUES ( '$nom', '', '','','$user','$modif','')";
				$reponse = command($cmd);
				}
			}
		}

	

		
	function mise_en_forme_date_aaaammjj( $date_jour)
		{
		$d3= explode("/",$date_jour);  
		if (isset($d3[2]))
			$a=$d3[2];
		else
			$a=date("Y");
		if ($a<100) $a+=2000;
		$m=$d3[1];
		$j=$d3[0];	
		if (($j<1) || ($j>31) || ($m<1) || ($m>12) )
			return("");
		
		return( "$a-$m-$j" );
		}
		
	function histo($nom,$detail)
		{
		global $bdd;

		$nom_slash= addslashes2($nom);	

		echo "<table>";		
		$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and commentaire<>'' and date='0000-00-00' and pres_repas<>'pda' and pres_repas<>'Age' and pres_repas<>'Mail' and pres_repas<>'T�l�phone' and pres_repas<>'nationalie' and pres_repas<>'PE' order by nom DESC "); 
		if ($donnees = fetch_command($reponse) ) 
			{
			$c=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]) );
			echo "<tr><td ></td> <td ><b>Memo FISSA : </b></td><td ></td><td >$c</td> ";
			}
		
		$i=0; 
		if ($detail=="")
			$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date>'2000-00-00' and pres_repas<>'pda' and pres_repas<>'reponse' and pres_repas<>'partenaire' order by date DESC "); 
		else
			$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date>'2000-00-00' and pres_repas='Suivi' order by date DESC "); 

		$ncolor=0;
		while (($donnees = fetch_command($reponse) ) && ($i<10000))
				{
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 	

				$c=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));
				$date_jour=$donnees["date"];
				$d3= explode("-",$date_jour);  
				$a=$d3[0];
				$m=$d3[1];
				$j=$d3[2];	
				$d ="$j/$m/$a";
				$act=$donnees["activites"];
				$act=str_replace('#-#','; ',$act);
				$p=$donnees["pres_repas"];
				$c=nl2br ($c);
				
				if ($p=="Suivi")  
					{
					$user="";
					if ($donnees["user"]!="")
						$user=libelle_user($donnees["user"]);
						
					$r1 = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date='$date_jour' and pres_repas='reponse' "); 
					if ($d1 = fetch_command($r1) ) 
						$rep="R�ponse :  <b>".$d1["activites"]."</b>";
					else
						$rep="";

					$r1 = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date='$date_jour' and pres_repas='partenaire' "); 
					if ($d1 = fetch_command($r1) ) 
						{
						$act=$d1["activites"];
						if($act!="---")
							$partenaire=" (Partenaire:  <b>".$d1["activites"]."</b>)";
						else
							$partenaire="";
						}
					else
						$partenaire="";
						
					echo "<tr><td bgcolor=\"$color\"><b><a href=\"suivi.php?action=accompagnement&nom=$nom&date_jour=$d\" > $d </a> </td><td bgcolor=\"$color\">$p </td><td bgcolor=\"$color\"> <b>$act</b> </td><td bgcolor=\"$color\">$rep $partenaire <br>$c <td bgcolor=\"$color\">$user </b>"; 

					}
				else
					echo "<tr><td bgcolor=\"$color\"> $d </td><td bgcolor=\"$color\"></td><td bgcolor=\"$color\">$p </td><td bgcolor=\"$color\"> $c </td> ";
				
				// ++++ si activit� ajouter liste des participants
				if (strpos($nom_slash,"(A)")>0)
					echo "<td bgcolor=\"$color\">". liste_participants_activite($nom ,$date_jour)  ."</td> ";

				$i++; 		
				}
		echo "</table>";
		}
	
	function liste_participants_activite( $act, $date )
		{
		global $bdd;

		$ret="";
		
		$reponse = command("SELECT * FROM $bdd WHERE date='$date' and (activites like '%$act%') group by nom ASC "); 
		while ($donnees = fetch_command($reponse) )
			$ret.=stripcslashes($donnees["nom"]).", ";
	
		return($ret);
		}
		
	function affiche_rdv($nom)
		{
		global $organisme, $bdd;
		
		if  ((!is_numeric($nom)) && (strpos($nom,"(A)")===false)) 
			{
			echo "<a href=\"rdv.php?nom=$nom\" > <img src=\"images/reveil.png\" width=\"35\" height=\"35\">Rendez-vous de $nom : </a>";
			$i=0;
			$reponse = command("SELECT * FROM r_user,DD_rdv WHERE r_user.organisme='$organisme' and DD_rdv.user='$nom' GROUP BY DD_rdv.date  ORDER BY DD_rdv.date DESC Limit 10"); 
			while ($donnees = fetch_command($reponse) ) 
					{
					if ($i++==0)
						echo "<table>";
					$i++;
					$date=$donnees["date"];	
					$user=$donnees["user"];	
					$d3= explode(" ",$date);
					$date=mef_date_fr($d3[0]);
					$heure=$d3[1];
					$ligne=stripcslashes($donnees["ligne"]);
					$auteur=libelle_user($donnees["auteur"]);
					echo "<tr> <td> $date </td> <td> � $heure </td><td> $ligne </td> <td> $auteur </td> ";
					}
			if ($i==0)
				echo "Aucun rendez-vous enregistr�. <hr> ";
			else
				echo "</table><hr>";
			}
		}
		
	function chgt_nom(  $nom, $nouveau)
		{
		global $bdd, $organisme;
		
		if (($nom!="") && ($nouveau!="") && ($nom!="Synth")&& ($nom!="Mail"))
			{
			if ((strpos( $nom ,'(A)')>0) && (strpos( $nouveau ,'(A)')===false))
				 $nouveau.=" (A)";
			if ((strpos( $nom ,'(B)')>0) && (strpos( $nouveau ,'(B)')===false))
				 $nouveau.=" (B)";	
			if ((strpos( $nom ,'(S)')>0) && (strpos( $nouveau ,'(S)')===false))
				 $nouveau.=" (S)";				 
			$nouveau= addslashes2($nouveau);
			$user= $_SESSION['user_idx'];
			$modif=time();
			$nom_slash= addslashes2($nom);	
			$reponse = command("UPDATE $bdd SET nom='$nouveau' , user='$user', modif='$modif' WHERE nom='$nom_slash' ") ;

			// mise � jour sp�cifique pour les activit�s qui 
			$reponse = command("SELECT * FROM $bdd WHERE  (activites like '%$nom%') "); 
			while ($donnees = fetch_command($reponse) )
					{
					$nom_user=stripcslashes($donnees["nom"]);
					$date=$donnees["date"];
					$act=$donnees["activites"];
					$act = str_replace ($nom, $nouveau, $act);
					command("UPDATE $bdd SET activites='$act'  WHERE date='$date' and nom='$nom_user' ") ;
					}

			// Mise � jour du nom dan sla table des rendez vous
			$reponse = command("SELECT *, DD_rdv.idx as idx_msg FROM r_user,DD_rdv WHERE r_user.organisme='$organisme' and DD_rdv.user='$nom' GROUP BY idx_msg "); 
			while ($donnees = fetch_command($reponse) ) 
					{
					$idx_msg=$donnees["idx_msg"];
					command("UPDATE DD_rdv SET nom='$nouveau'  WHERE idx='$idx_msg'  ","x") ;
					}

			}
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

		$reponse = command("SELECT * FROM $bdd WHERE  date>'2000-00-00' and pres_repas='Suivi'  group by activites "); 		
		while ($donnees = fetch_command($reponse) ) 
			{
			$a= $donnees["activites"];
			if ($a!="")
				affiche_un_choix($act,$a);
			else
				affiche_un_choix($act,"---");			
			}
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

		echo " <td> - R�ponse : </td>";
		echo "<form method=\"GET\" action=\"suivi.php\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"reponse\"> " ;
		echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
		echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";

		echo "<td><SELECT name=\"reponse\"  onChange=\"this.form.submit();\">";		

		affiche_un_choix($act,"---");
		affiche_un_choix($act,"Accompagnement/bilan");
		affiche_un_choix($act,"Accompagnement administratif");
		affiche_un_choix($act,"Attestation pr�sence");
		affiche_un_choix($act,"Attestation situation");		
		affiche_un_choix($act,"Aide financi�re");	
		affiche_un_choix($act,"Appel telephone");
		affiche_un_choix($act,"Autre");
		
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
				
		affiche_un_choix($act,"CCAS");	
		affiche_un_choix($act,"Centre de soin");
		affiche_un_choix($act,"CPAM");
		affiche_un_choix($act,"CMU");		
		affiche_un_choix($act,"Croix-Rouge");		
		
		affiche_un_choix($act,"DALO");		
		
		affiche_un_choix($act,"EDF/GDF/autres");		
		
		affiche_un_choix($act,"H�bergement d'urgence");		
		affiche_un_choix($act,"Hopital");	
		affiche_un_choix($act,"HLM");	
		
		affiche_un_choix($act,"Impots");
		
		affiche_un_choix($act,"Justice");

		affiche_un_choix($act,"La Poste");
		
		affiche_un_choix($act,"Mairie");
		affiche_un_choix($act,"MDPH");
		affiche_un_choix($act,"M�decin");

		affiche_un_choix($act,"Ordre de Malte");
		
		affiche_un_choix($act,"Pr�fecture");
		affiche_un_choix($act,"Police/Gendarmerie");
		affiche_un_choix($act,"Pole Emploi");		
		
		affiche_un_choix($act,"RATP");		
		affiche_un_choix($act,"Restos du coeur");
		
		affiche_un_choix($act,"SAMU Social");		
		affiche_un_choix($act,"Secours Populaire");
		affiche_un_choix($act,"Secours catholique");
		affiche_un_choix($act,"SIAO");		
		
		affiche_un_choix($act,"Tuteur");
		
		affiche_un_choix($act,"Unit� mobile");
		affiche_un_choix($act,"URSSAF");		
		echo "</SELECT></td>";		
		echo "</form> ";
		}


	function choix_pays($nom, $nat)
		{		
		echo "<td> <b> Pays d'origine </b> : </td><td>";
		echo "<form method=\"GET\" action=\"suivi.php\" >";
		echo "<input type=\"hidden\" name=\"action\" value=\"nationalite\"> " ;
		echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
		echo "<SELECT name=\"nationalite\"  onChange=\"this.form.submit();\">";	

if ($nat=="") $nat= "Inconnu";
affiche_un_choix($nat,"Inconnu");
affiche_un_choix($nat,"Afghanistan");
affiche_un_choix($nat,"Afrique_Centrale");
affiche_un_choix($nat,"Afrique_du_sud");
affiche_un_choix($nat,"Albanie");
affiche_un_choix($nat,"Algerie");
affiche_un_choix($nat,"Allemagne");
// affiche_un_choix($nat,"Andorre");
affiche_un_choix($nat,"Angola");
//affiche_un_choix($nat,"Anguilla");
affiche_un_choix($nat,"Arabie_Saoudite");
affiche_un_choix($nat,"Argentine");
affiche_un_choix($nat,"Armenie");
affiche_un_choix($nat,"Australie");
affiche_un_choix($nat,"Autriche");
affiche_un_choix($nat,"Autre Asie");
affiche_un_choix($nat,"Autre Amerique");
affiche_un_choix($nat,"Autre Afrique");
affiche_un_choix($nat,"Autre DOM-TOM");
affiche_un_choix($nat,"Autre Europe");
affiche_un_choix($nat,"Autre Oc�anie");

affiche_un_choix($nat,"Azerbaidjan");

//affiche_un_choix($nat,"Bahamas");
affiche_un_choix($nat,"Bangladesh");
//affiche_un_choix($nat,"Barbade");
affiche_un_choix($nat,"Bahrein");
affiche_un_choix($nat,"Belgique");
//affiche_un_choix($nat,"Belize");
affiche_un_choix($nat,"Benin");
//affiche_un_choix($nat,"Bermudes");
affiche_un_choix($nat,"Bielorussie");
affiche_un_choix($nat,"Bolivie");
affiche_un_choix($nat,"Botswana");
affiche_un_choix($nat,"Bhoutan");
affiche_un_choix($nat,"Boznie_Herzegovine");
affiche_un_choix($nat,"Bresil");
affiche_un_choix($nat,"Brunei");
affiche_un_choix($nat,"Bulgarie");
affiche_un_choix($nat,"Burkina_Faso");
affiche_un_choix($nat,"Burundi");

//affiche_un_choix($nat,"Caiman");
affiche_un_choix($nat,"Cambodge");
affiche_un_choix($nat,"Cameroun");
affiche_un_choix($nat,"Canada");
affiche_un_choix($nat,"Canaries");
affiche_un_choix($nat,"Cap_vert");
affiche_un_choix($nat,"Chili");
affiche_un_choix($nat,"Chine");
affiche_un_choix($nat,"Chypre");
affiche_un_choix($nat,"Colombie");
affiche_un_choix($nat,"Comores");
affiche_un_choix($nat,"Congo");
affiche_un_choix($nat,"Congo_democratique");
//affiche_un_choix($nat,"Cook");
affiche_un_choix($nat,"Coree_du_Nord");
affiche_un_choix($nat,"Coree_du_Sud");
affiche_un_choix($nat,"Costa_Rica");
affiche_un_choix($nat,"Cote_d_Ivoire");
affiche_un_choix($nat,"Croatie");
affiche_un_choix($nat,"Cuba");

affiche_un_choix($nat,"Danemark");
affiche_un_choix($nat,"Djibouti");
affiche_un_choix($nat,"Dominique");

affiche_un_choix($nat,"Egypte");
affiche_un_choix($nat,"Emirats_Arabes_Unis");
affiche_un_choix($nat,"Equateur");
affiche_un_choix($nat,"Erythree");
affiche_un_choix($nat,"Espagne");
affiche_un_choix($nat,"Estonie");
affiche_un_choix($nat,"Etats_Unis");
affiche_un_choix($nat,"Ethiopie");

//affiche_un_choix($nat,"Falkland");
//affiche_un_choix($nat,"Feroe");
//affiche_un_choix($nat,"Fidji");
affiche_un_choix($nat,"Finlande");
affiche_un_choix($nat,"France");

affiche_un_choix($nat,"Gabon");
affiche_un_choix($nat,"Gambie");
affiche_un_choix($nat,"Georgie");
affiche_un_choix($nat,"Ghana");
affiche_un_choix($nat,"Gibraltar");
affiche_un_choix($nat,"Grece");
//affiche_un_choix($nat,"Grenade");
affiche_un_choix($nat,"Groenland");
//affiche_un_choix($nat,"Guadeloupe");
//affiche_un_choix($nat,"Guam");
affiche_un_choix($nat,"Guatemala");
//affiche_un_choix($nat,"Guernesey");
affiche_un_choix($nat,"Guinee");
affiche_un_choix($nat,"Guinee_Bissau");
affiche_un_choix($nat,"Guinee equatoriale");
affiche_un_choix($nat,"Guyana");
affiche_un_choix($nat,"Guyane_Francaise ");

affiche_un_choix($nat,"Haiti");
affiche_un_choix($nat,"Hawaii");;
affiche_un_choix($nat,"Honduras");
affiche_un_choix($nat,"Hong_Kong");
affiche_un_choix($nat,"Hongrie");

affiche_un_choix($nat,"Inde");
affiche_un_choix($nat,"Indonesie");
affiche_un_choix($nat,"Iran");
affiche_un_choix($nat,"Iraq");
affiche_un_choix($nat,"Irlande");
affiche_un_choix($nat,"Islande");
affiche_un_choix($nat,"Israel");
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
affiche_un_choix($nat,"Macedoine");
affiche_un_choix($nat,"Madagascar");
//affiche_un_choix($nat,"Mad�re");
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
affiche_un_choix($nat,"Nepal");
affiche_un_choix($nat,"Nicaragua");
affiche_un_choix($nat,"Niger");
affiche_un_choix($nat,"Nigeria");
//affiche_un_choix($nat,"Niue");
//affiche_un_choix($nat,"Norfolk");
affiche_un_choix($nat,"Norvege");
affiche_un_choix($nat,"Nouvelle_Caledonie");
affiche_un_choix($nat,"Nouvelle_Zelande");

affiche_un_choix($nat,"Oman");
affiche_un_choix($nat,"Ouganda");
affiche_un_choix($nat,"Ouzbekistan");

affiche_un_choix($nat,"Pakistan");
//affiche_un_choix($nat,"Palau");
affiche_un_choix($nat,"Palestine");
affiche_un_choix($nat,"Panama");
affiche_un_choix($nat,"Papouasie_Nouvelle_Guinee");
affiche_un_choix($nat,"Paraguay");
affiche_un_choix($nat,"Pays_Bas");
affiche_un_choix($nat,"Perou");
affiche_un_choix($nat,"Philippines");
affiche_un_choix($nat,"Pologne");
affiche_un_choix($nat,"Polynesie");
affiche_un_choix($nat,"Porto_Rico");
affiche_un_choix($nat,"Portugal");

affiche_un_choix($nat,"Qatar");

affiche_un_choix($nat,"Republique_Dominicaine");
affiche_un_choix($nat,"Republique_Tcheque");
//affiche_un_choix($nat,"Reunion");
affiche_un_choix($nat,"Roumanie");
affiche_un_choix($nat,"Royaume_Uni");
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
affiche_un_choix($nat,"Senegal");
//affiche_un_choix($nat,"Seychelles");
affiche_un_choix($nat,"Sierra Leone"); 
affiche_un_choix($nat,"Singapour");
affiche_un_choix($nat,"Slovaquie");
affiche_un_choix($nat,"Slovenie");
affiche_un_choix($nat,"Somalie");
affiche_un_choix($nat,"Soudan");
affiche_un_choix($nat,"Sri_Lanka");
affiche_un_choix($nat,"Suede");
affiche_un_choix($nat,"Suisse");
affiche_un_choix($nat,"Surinam");
affiche_un_choix($nat,"Swaziland");
affiche_un_choix($nat,"Syrie");

affiche_un_choix($nat,"Tadjikistan");
affiche_un_choix($nat,"Taiwan");
//affiche_un_choix($nat,"Tonga");
affiche_un_choix($nat,"Tanzanie");
affiche_un_choix($nat,"Tchad");
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

		echo "</SELECT></td>";		
		echo "</form> ";
		}



	
	function saisie ($titre,$libelle,$nom,$nom_slash)
		{
		global $bdd;
		
		$val=""; 
		$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='$libelle' "); 
		if ($donnees = fetch_command($reponse))
			$val=$donnees["commentaire"];
		else 
		$val="";

		echo "<td> <b> $titre </b> : </td><td>";
		echo "<form method=\"GET\" action=\"suivi.php\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"$libelle\"> " ;
		echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
		echo "<input type=\"text\" name=\"telephone\"  value=\"$val\"  onChange=\"this.form.submit();\">";
		echo "</form> </td>";
		}
	// -====================================================================== Saisie


	$format_date = "d/m/Y";
	$user_lang='fr';

	// ConnexiondD
	include "connex_inc.php";
	
	$action=variable_s("action");	
	require_once 'cx.php';
	
	$reponse = command("SELECT * FROM fct_fissa WHERE support='$bdd' "); 
	if ((!($donnees = fetch_command($reponse))) || (!$_SESSION['pass']) )
		{
		echo "<a href=\"https://doc-depot.com\">retour sur page d'accueil doc-depot.com</a>";
		}
	else
		{
		
		$organisme =$donnees["organisme"];
		
		$beneficiaire=$donnees["beneficiaire"];
		if ($beneficiaire=="") $beneficiaire="B�n�ficiaires";
			
		$acteur=$donnees["acteur"];
		if ($acteur=="") $acteur="Accueillants";
		
		$libelle=$donnees["libelle"];
		$logo=$_SESSION['logo'];	

		$memo=variable_s("memo");
		$action=variable_s("action");
		if ($action=="")
			$action="suivi";
		$pda=variable_s("pda");
		$com=variable_s("com");
		$act=variable_s("activites");
		$nom=variable_s("nom");
	
		if ($action=="chgt_nom")
			{
			$nouveau=variable_s("nouveau");
			chgt_nom($nom,$nouveau);
			$nom=$nouveau;
			$action="suivi";
			}
		if ($action=="nouveau")
			{
			$type=variable_s("type");
			if ($type=="B�n�ficiaire femme")
				$nom .= " (F)";
			nouveau($nom);
			}	
			
		if (!isset ($_GET["date_jour"]))
			$date_jour=date('d/m/Y');
		else 
			{
			$date_jour=variable_s("date_jour");
			if ( mise_en_forme_date_aaaammjj($date_jour)=="")
				{
				erreur("Format de date incorrect");
				$action="";
				$date_jour=date('d/m/Y');
				}
			}

		// =====================================================================loc IMAGE
		echo "<table border=\"0\" >";	
		echo "<tr> <td> <a href=\"suivi.php\"> <img src=\"images/suivi.jpg\" width=\"140\" height=\"100\"  ></a></td> ";		

		// =====================================================================loc Histo
		echo "<td>Suivi de <br>";
		echo "<form method=\"GET\" action=\"suivi.php\" >";
		echo "<input type=\"hidden\" name=\"action\" value=\"suivi\"> " ;
		echo "<SELECT name=nom onChange=\"this.form.submit();\">";
		echo "<OPTION  VALUE=\"\">  </OPTION>";
		$reponse = command("SELECT * FROM $bdd WHERE nom<>'Mail' group by nom "); 			
		while ($donnees = fetch_command($reponse) ) 
			{
			$sel=$donnees["nom"];				
			if ($sel==$nom)
				echo "<OPTION  VALUE=\"$sel\" selected > $sel </OPTION>";
			else
				echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
			}
		echo "</SELECT></form> </td>";
		// =====================================================================loc RAPPORT
		echo "<td width=\"150\"><center>";
		echo "<ul id=\"menu-bar\">";
		echo "<li><a href=\"index.php?action=dx\">Deconnexion</a>";
		echo "</ul> ";
		
		echo "</td>";
		echo "<td><a href=\"index.php\"><img src=\"images/logo.png\" width=\"70\" height=\"50\"><a></td>";			
		echo "<td><a href=\"fissa.php\"><img src=\"images/fissa.jpg\" width=\"70\" height=\"50\"><a></td>";			
		echo "<td><a href=\"rdv.php\"><img src=\"images/rdv.jpg\" width=\"70\" height=\"50\"><a></td>";			
		echo "<td><a title=\"Alerte Grand Froid/Forte Pluie\" href=\"alerte.php\"><img src=\"images/logo-alerte.jpg\" width=\"70\" height=\"50\"></a> ";
		if ($logo!="")
			echo "<td> <a href=\"fissa.php\"> <img src=\"images/$logo\" width=\"200\" height=\"100\"  > </a> </td>";
		echo "</center></td>";	
		echo "</table><hr> ";				
			
		ajout_log_jour("----------------------------------------------------------------------------------- [ SUIVI = $action ] $date_jour ");
		$date_jour_gb=mise_en_forme_date_aaaammjj( $date_jour);		
		
		$nom_slash= addslashes2($nom);	
		if ($nom!="")
			{
			if ($action=="activites")
				{
				$activites=variable_s("activites");
				$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour_gb' and nom='$nom_slash' and pres_repas='Suivi' "); 
				$user= $_SESSION['user_idx'];
				$modif=time();
				if ($donnees = fetch_command($reponse))
					$reponse = command("UPDATE $bdd set activites='$activites' , modif='$modif' where date='$date_jour_gb' and nom='$nom_slash' and pres_repas='Suivi' ");
				else
					$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash',  '$date_jour_gb', 'Suivi','','$user','$modif','$activites')");					
				$action="suivi";
				}	
				
			if (($action=="reponse")|| ($action=="partenaire") )
				{

				$activites=variable_s($action);
				$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour_gb' and nom='$nom_slash' and pres_repas='$action' "); 
				$user= $_SESSION['user_idx'];
				$modif=time();
				if ($donnees = fetch_command($reponse))
					$reponse = command("UPDATE $bdd set activites='$activites' , modif='$modif' where date='$date_jour_gb' and nom='$nom_slash' and pres_repas='$action' ");
				else
					$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '$date_jour_gb', '$action','','$user','$modif','$activites')");					
				$action="suivi";
				}			
				
				if ($action=="PE")
						{
						$PE=variable_s("PE");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='PE' "); 
						$user= $_SESSION['user_idx'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$PE' , modif='$modif' where nom='$nom_slash' and pres_repas='PE' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'PE','$PE','$user','$modif','')");					
						$action="suivi";
						}			
						
				if ($action=="adresse")
						{
						$adresse=variable_s("adresse");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='adresse' "); 
						$user= $_SESSION['user_idx'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$adresse' , modif='$modif' where nom='$nom_slash' and pres_repas='adresse' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'adresse','$adresse','$user','$modif','')");					
						$action="suivi";
						}
				if ($action=="nationalite")
						{
						$nationalite=variable_s("nationalite");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='nationalite' "); 
						$user= $_SESSION['user_idx'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$nationalite' , modif='$modif' where nom='$nom_slash' and pres_repas='nationalite' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'nationalite','$nationalite','$user','$modif','')");					
						$action="suivi";
						}				
				if ($action=="telephone")
						{
						$telephone=variable_s("telephone");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='T�l�phone' "); 
						$user= $_SESSION['user_idx'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$telephone' , modif='$modif' where nom='$nom_slash' and pres_repas='T�l�phone' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'T�l�phone','$telephone','$user','$modif','')");					
						$action="suivi";
						}	
		
				if ($action=="mail")
						{
						$mail=variable_s("mail");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='mail' "); 
						$user= $_SESSION['user_idx'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$mail' , modif='$modif' where nom='$nom_slash' and pres_repas='Mail' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'Mail','$mail','$user','$modif','')");					
						$action="suivi";
						}
					
					echo "<table><tr> <td >";
					echo "<ul id=\"menu-bar\">";					
					echo "<li><a href=\"suivi.php?action=suivi&nom=$nom\" > <b> $nom </b> </a></li>";
					echo "</ul> </td>";
					echo "</table> ";					
					
					/*
					echo "<form method=\"GET\" action=\"suivi.php\">";
					echo "<input type=\"hidden\" name=\"action\" value=\"$action\"> " ;
					echo "<input type=\"hidden\" name=\"nom\" value=\"$nom\"> " ;
					echo "<input type=\"text\" name=\"date_jour\" size=\"10\" value=\"$date_jour\" class=\"calendrier\"></td>";
					echo "<td>"; 
					echo "<input type=\"submit\" value=\"Selectionner\" > </td> </form>"; 
					*/
			if ($action=="age")
						{
						$age=variable_s("age");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Age' "); 
						$user= $_SESSION['user_idx'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$age' , modif='$modif' where nom='$nom_slash' and pres_repas='Age' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'Age','$age','$user','$modif','')");					
						$action="suivi";
						}	
						
			if ($action=="echeance")
						{
						$echeance=variable_s("echeance");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='pda' "); 
						$user= $_SESSION['user_idx'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set activites='$echeance' , modif='$modif' , user='$user' where nom='$nom_slash' and pres_repas='pda' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '', 'pda','$echeance','$user','$modif','')");					
						$action="suivi";
						}	
					
					if ($com=="") 
						{
						$date_jour_gb=mise_en_forme_date_aaaammjj( $date_jour);
						$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour_gb' and nom='$nom_slash' and pres_repas='Suivi' "); 
						if ($donnees = fetch_command($reponse))
							$com=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));
						else
							$com="";
						}
					else
						{
						$date_jour_gb=mise_en_forme_date_aaaammjj( $date_jour);
						$com=addslashes2($com);
						$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour_gb' and nom='$nom_slash' and pres_repas='Suivi' "); 
						
						$user= $_SESSION['user_idx'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$com' , user='$user' , modif='$modif' where nom='$nom_slash' and date='$date_jour_gb' and pres_repas='Suivi' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '$date_jour_gb', 'Suivi','$com','$user','$modif','$act')");					
						//$commentaire=$com;
						}
					
					$derniere_maj_pda="";
					
					if ($pda=="")
						{
						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='pda' "); 
						if ($donnees = fetch_command($reponse))
							{
							$pda=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));
							if ($donnees["user"]!="")
								$lib_user=libelle_user($donnees["user"]);
							else
								$lib_user="";
							$modif="";
							if ($donnees["modif"]!="")
								$modif=date ("d/m/Y H:i",$donnees["modif"]);
							if ($lib_user!="")
								$derniere_maj_pda=" (Derni�re modification le $modif par $lib_user).";
							}
						else
							$pda="";
						}
					else
						{
						$pda=addslashes2($pda);
						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='pda' "); 
						
						$user= $_SESSION['user_idx'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$pda' , user='$user' , modif='$modif' where nom='$nom_slash' and pres_repas='pda' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '', 'pda','$pda','$user','$modif','')");					
						$pda=stripcslashes($pda);
						}					
					

					if ($nom!="Synth")
						{
						if (!(strpos($nom,"(A)")>0))
							{
							echo "<TABLE><TR><td > <div class=\"CSS_titre\"  >";
							
							
							echo "<table border=\"0\" > <TR> <td> ";	

							$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Age' "); 
							if ($donnees = fetch_command($reponse))
								$age=$donnees["commentaire"];
							else 
								$age="";
							echo "<b> Date naissance </b> : </td>";
							echo " <td><TABLE><TR> <td><form method=\"GET\" action=\"suivi.php\">";
							echo "<input type=\"hidden\" name=\"action\" value=\"age\"> " ;
							echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
							echo "<input type=\"text\" name=\"age\" size=\"10\"  value=\"$age\"  onChange=\"this.form.submit();\">";
							echo "</form> </td> <td>jj/mm/aaaa </td></table> </td>";	
							
							$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='nationalite' "); 
							if ($donnees = fetch_command($reponse))
								$nat=$donnees["commentaire"];
							else 
								$nat="";
							choix_pays($nom, $nat);
								
							$tel="";
							$age="";
							$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='T�l�phone' "); 
							if ($donnees = fetch_command($reponse))
								$tel=$donnees["commentaire"];
							else 
								$tel="";

							echo "<tr> <td> <b> Portable </b> : </td><td>";
							echo "<form method=\"GET\" action=\"suivi.php\">";
							echo "<input type=\"hidden\" name=\"action\" value=\"telephone\"> " ;
							echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
							echo "<input type=\"text\" name=\"telephone\"  value=\"$tel\"  onChange=\"this.form.submit();\">";
							echo "</form> </td>";
							

							$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Mail' "); 
							if ($donnees = fetch_command($reponse))
								$mail=$donnees["commentaire"];
							else
								$mail="";
							echo "<td> <b> Mail </b> : </td><td>";
							echo "<form method=\"GET\" action=\"suivi.php\">";
							echo "<input type=\"hidden\" name=\"action\" value=\"mail\"> " ;
							echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
							echo "<input type=\"text\" name=\"mail\" size=\"40\"  value=\"$mail\"  onChange=\"this.form.submit();\">";
							echo "</form></td> ";
							
							
							echo "</td></table>";
							
							echo "<table>";
							$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='adresse' "); 
							if ($donnees = fetch_command($reponse))
								$adresse=$donnees["commentaire"];
							else 
								$adresse="";
							echo "<tr><td> <b> Adresse </b> : </td><td>";
							echo "<form method=\"GET\" action=\"suivi.php\">";
							echo "<input type=\"hidden\" name=\"action\" value=\"adresse\"> " ;
							echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
							echo "<input type=\"text\" name=\"adresse\" size=\"80\"  value=\"$adresse\"  onChange=\"this.form.submit();\">";
							echo "</form> </td>";														
							
							$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='PE' "); 
							if ($donnees = fetch_command($reponse))
								$PE=$donnees["commentaire"];
							else 
								$PE="";
							echo "<tr><td> <b> Commentaire </b> : </td><td>";
							echo "<form method=\"GET\" action=\"suivi.php\">";
							echo "<input type=\"hidden\" name=\"action\" value=\"PE\"> " ;
							echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
							echo "<input type=\"text\" name=\"PE\" size=\"80\"  value=\"$PE\"  onChange=\"this.form.submit();\">";
							echo "</form> </td>";
							

							echo "</table>";
							}
							
							fin_cadre();

							
						// ============================================================================== SUIVI
						echo "<table><tr> <td >";
						echo "<ul id=\"menu-bar\">";					
						echo "<li><a href=\"suivi.php?action=suivi&nom=$nom\" >Suivi du <b> $date_jour  </b> </a></li>";
						echo "</ul> </td>";
						echo "</table> ";	
						
						
						echo "<TABLE><TR><td > <div class=\"CSS_titre\"  >";

						echo " <table border=\"0\" >";
						echo "<tr> <td> <table border=\"0\" ><tr> <td> <b> Suivi </b> : </td>";
						echo "<form method=\"GET\" action=\"suivi.php\">";
						
						$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour_gb' and  nom='$nom_slash' and pres_repas='Suivi' "); 
						if ($donnees = fetch_command($reponse))
							$act=$donnees["activites"];
						else 
							$act="";
						choix_action_suivi($act);
						choix_reponse_suivi($act);
						choix_partenaire_suivi($act);

						echo "</table></td>";
						echo "<tr> <td><form method=\"GET\" action=\"suivi.php\">";
						echo "<input type=\"hidden\" name=\"action\" value=\"suivi\"> " ;
						echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
						echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
						echo "<TEXTAREA rows=\"4\" cols=\"110\" name=\"com\" onChange=\"this.form.submit();\">$com</TEXTAREA>";
						echo "</td> ";
						echo "</form> ";
						fin_cadre();	
						
						// ============================================================================== Plan d'action
						
						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='pda' "); 
						if ($donnees = fetch_command($reponse))
							$echeance=$donnees["activites"];
						else
							$echeance="";
						echo "<tr> <td> ";
						echo "<table> <tr><td> <b> Plan d'action en cours </b> </td> <td> - Ech�ance  : </td><td>";
						echo "<form method=\"GET\" action=\"suivi.php\">";
						echo "<input type=\"hidden\" name=\"action\" value=\"echeance\"> " ;
						echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
						echo "<input type=\"text\" name=\"echeance\" size=\"10\"  value=\"$echeance\"  onChange=\"this.form.submit();\"  >";
						echo "</form></td>  <td>  $derniere_maj_pda </td> ";
						
						echo "</table></td> ";
						
						echo "<tr> <td><form method=\"GET\" action=\"suivi.php\">";
						echo "<input type=\"hidden\" name=\"action\" value=\"pda\"> " ;
						echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
						echo "<TEXTAREA rows=\"4\" cols=\"110\" name=\"pda\" onChange=\"this.form.submit();\">$pda</TEXTAREA>";
						echo "</td> ";
						echo "</form> ";
						
						echo "</table>  ";
						
						echo "</td>";
						echo "</table>  ";
						

					if  (($action=="suivi") || ($action=="pda"))
						affiche_rdv($nom);		
						
						if ($action!="accompagnement")
								echo "<a href=\"suivi.php?action=accompagnement&nom=$nom&date_jour=$date_jour\" > ( N'afficher que l'accompagnement )</a>";
							else
								echo "<a href=\"suivi.php?action=suivi&nom=$nom&date_jour=$date_jour\" > ( Afficher tout l'historique )</a>";
						}						


						
					if  (($action=="suivi") || ($action=="pda"))
						histo($nom,"");
					else
						histo($nom,"accompagnement");

			}
		else 
			{
			$i=0;
			// liste des plans d'action ouvert
			$reponse = command("SELECT * FROM $bdd WHERE pres_repas='pda' and (activites<>''  and !(activites like 'termin�'))  order by activites "); 
			while ($donnees = fetch_command($reponse) ) 
				{
				if ($i++==0)
					echo "<P> <table border=\"0\" ><tr> <td>Qui </td><td>Ech�ance </td><td>Plan d'action </td>";
				
				$nom=$donnees["nom"];
				$echeance=$donnees["activites"];
				$pda=$donnees["commentaire"];
				echo "<tr> <td><a href=\"suivi.php?action=suivi&nom=$nom\" >$nom </td><td>$echeance </td><td>$pda</td>";
				}
				
			if ($i!=0)
				echo "</table><p><br><p><br>";
			else
				echo "<p><br><p><br><p><br><p><br><p><br><p><br><p><br><p><br>";
		
			// =====================================================================loc NOUVEAU
			echo "<table><tr><td bgcolor=\"#d4ffaa\">Cr�ation nouveau :</td><td bgcolor=\"#d4ffaa\"><form method=\"GET\" action=\"suivi.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
			echo "<input type=\"text\" name=\"nom\" size=\"30\" value=\"\">";	
			liste_type();
			echo "<input type=\"submit\" value=\"Cr�er Nouveau\" >  ";
			echo "</td></form> ";	
			echo " </table>";
				
			if ( ($_SESSION['droit']=='R') ||($_SESSION['droit']=='S') )
				{
				// =====================================================================loc CHANGEMENT NOM
				echo "<P> <table border=\"0\" ><tr> <td>Modification d'un nom :</td><td>";
				echo "<form method=\"GET\" action=\"suivi.php\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"chgt_nom\"> " ;
				echo "<SELECT name=nom>";
				echo "<OPTION  VALUE=\"\">  </OPTION>";
				$reponse = command("SELECT * FROM $bdd WHERE nom<>'Mail' and nom<>'Synth'  group by nom "); 			
				while ($donnees = fetch_command($reponse) ) 
					{
					$sel=$donnees["nom"];	
					echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
					}
				echo "</SELECT></td>";
				echo "</td> <td> � transformer en </td> <td>";
				echo "<input type=\"text\" name=\"nouveau\" size=\"20\" value=\"\">";	
				echo "<input type=\"submit\" value=\"Mise � jour du nom\" >  ";
				echo " </form> ";
				echo "</table>  ";
				}
				
			}
		}

	
	pied_de_page();
		?>
	
    </body>
</html>
