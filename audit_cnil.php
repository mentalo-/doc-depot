  <?php  

	include 'audit_cnil_mots.php';

	function homogene ( $commentaire)
		{
	//	$commentaire = strtr($commentaire, 'Êáàâäãåçéèêëíìîïñóòôöõúùûüýÿ-_()<>;.,"?/!’«”“‘', 'Eaaaaaaceeeeiiiinooooouuuuyy                  ');
		$commentaire = strtr($commentaire, "'", " ");
		$commentaire = str_replace ("&apos"," ", $commentaire);
		$commentaire = str_replace ("   "," ", $commentaire);
		$commentaire = str_replace ("  "," ", $commentaire);
		$commentaire = str_replace ("\n"," ", $commentaire);
		$commentaire = str_replace ("\r"," ", $commentaire);
		return($commentaire);
		}
		
	function dd_strstr($commentaire,$recherche)
		 {
		 global $nb_tst;
		 
		$nb_tst++;
		return(stristr($commentaire,$recherche));
		 }
	

	function test1( $m)
		{
		global $autorises, $commentaire;

		if 	(dd_strstr($commentaire,$m) )
			$commentaire=str_ireplace ($m, "<B><span style=\"background-color:#ffff66;\" >&nbsp;$m&nbsp;</span></b>", $commentaire);
		}
		
	function test( $m)
		{
		global $autorises, $commentaire;
					
		if 	(dd_strstr($commentaire," ".$m))
			{
			test1(" ".$m."e ");
			test1(" ".$m."es ");
			test1(" ".$m."s ");
			test1(" ".$m."r ");
			test1(" $m ");		
			}
			
		}
	
	function test_approx( $org, $approx,$m)
		{
		if 	(dd_strstr($m,$org))
			test(str_replace ($org,$approx, $m) );	
		}
		
		
	function audit_cnil($periode, $support, $envoi_mail=true )
		{
		global $commentaire,$mots,$nb_tst;
		
		$nb_tst=0;
		
		for ($i=0; isset($mots[$i]); $i++)
				$mots[$i] = trim( strtr ($mots[$i], 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ-()<>;.', 'aaaaaaceeeeiiiinooooouuuuyy       ') ) ;	
				
		echo "<p><hr>Audit CNIL sur $support ($i mots à tester) <br>";

		if ($_SERVER['REMOTE_ADDR']=="127.0.0.1")
			$debut = mktime(0,0,0 , date("m")-10, 1, date ("Y"));
		else
			$debut = mktime(0,0,0 , date("m")-1, 1, date ("Y"));
		$fin = mktime(0,0,0 , date("m"), 1, date ("Y"));
		$r1 = command("SELECT *  FROM $support where commentaire<>'' and pres_repas<>'nationalite' and modif>'$debut' and modif<'$fin' "); 
		
		$ncolor=0;			
		$nb=0;
		$cumul="";
			
		while ($d1 = mysql_fetch_array($r1) )
			{
			$commentaire = $d1["commentaire"];
			$org=$commentaire ;		
			$commentaire = " ".homogene ( $commentaire)." ";
			$original=$commentaire;
				
			for ($i=0; isset($mots[$i]); $i++)
				{
				$m=$mots[$i];
				
				test( $m);
				
				if (strlen($m)>6)
					{
					test_approx("eux","euse", $m);	
					test_approx("eau","eaux", $m);		
					test_approx("eur","euse", $m);
					test_approx("iste","isme", $m);			
					test_approx("xuel","xuelle", $m);
					test_approx("gie","gue", $m);
					test_approx("if","ive", $m);	
					
						// test ortographe approximatif
					test_approx("ph","f", $m) ;	
					test_approx("tt","t", $m);
					test_approx("ll","l", $m);
					test_approx("rr","r", $m);
					test_approx("mm","m", $m);
					test_approx("pp","p", $m);
					test_approx("th","t", $m);					
					test_approx("y","i", $m);			
					test_approx("o","0", $m);			
					test_approx("au","o", $m);
					test_approx("mp","np", $m);			
					test_approx("mb","nb", $m);			
					test_approx("en","an", $m);			
					test_approx("an","en", $m);		
					}
				test_approx(" ","", $m);	
				}
					
			if ($original!=$commentaire)
				{
				$user=$d1["user"];
				$jour=$d1["date"];
				$beneficiaire=$d1["nom"];
				$modif=$d1["modif"];
				$pres_repas=$d1["pres_repas"];
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 	
				$r2 =command("select * from  r_user where idx='$user'  ");		
				if ($d2 = fetch_command($r2) ) 
					$nom=stripcslashes($d2["nom"])." ".stripcslashes($d2["prenom"]);	
				else
					$nom="???";
				
				switch ($pres_repas)
					{
					case "pda": $pres_repas="Plan d'action"; break;
					case "Synth": $beneficiaire="Synthèse journée";  $pres_repas="FISSA"; break;
					case "Pour info": 
					case "Visite+Repas": 
					case "Visite": $pres_repas="FISSA "; break;
					}	
				if ($jour=="0000-00-00")
					$jour="";						
				$date=date('Y-m-d H:i',  $modif );	
				
				$cumul.="<tr> <td bgcolor=\"$color\">  $nom </td> ";
				$cumul.="<td bgcolor=\"$color\">  $date </td> ";
				$cumul.="<td bgcolor=\"$color\"> ./!\.  </td> ";
				$cumul.="<td bgcolor=\"$color\">  $beneficiaire </td> ";	
				$cumul.= "<td bgcolor=\"$color\">  $jour </td> ";	
				$cumul.= "<td bgcolor=\"$color\">  $pres_repas </td> ";	
				$cumul.= "<td bgcolor=\"$color\">  $org </td> ";
				$cumul.= "<td bgcolor=\"$color\">  $commentaire </td> ";
				$nb++;
				}
			}
				
			if ($nb!=0)
				{
				$entete=  "<p>Bonjour,<p>Veuillez trouver ci-dessous les saisies du mois dernier faites par les acteurs sociaux de votre structure et qui nécessitent votre regard. ";
				$entete.=  " Certains mots ou expressions utilisés sont susceptibles de ne pas respecter les directives de la CNIL.";
				$entete.=  "<p>Ce système de détection, en phase expérimentale, peut ne pas avoir détecté certains mots ou expressions inappropriés, n'hésitez pas à nous faire des retours via le lien <a id=\"lien\"  href=\"https://www.doc-depot.com/index.php?action=contact\">contact de FISSA</a><p>";				
				$entete.=  "<p>Rappel CNIL: les informations personnelles enregistrées doivent être «adéquates, pertinentes et non excessives au regard des finalités pour lesquelles elles sont collectées (article 6-3°).";
				$entete.=  "<br>En principe, les données sensibles (information concernant l’origine raciale ou ethnique, les opinions politiques, philosophiques ou religieuses, l’appartenance syndicale, la santé ou la vie sexuelle) ne peuvent être recueillies et exploitées qu’avec le consentement explicite des personnes. ";
				
				
				$entete.=  "<table border=\"2\"><tr>";
				$color="#3f7f00";
				$entete.=  "<td bgcolor=\"$color\"> <font color=\"white\"> Dernier rédacteur de la saisie </td> <td bgcolor=\"$color\"> <font color=\"white\"> Date de la saisie </td> <td bgcolor=\"$color\">   </td> ";
				$entete.=  "<td bgcolor=\"$color\">  <font color=\"white\">Bénéficiaire </td> <td bgcolor=\"$color\"> <font color=\"white\"> Jour </td>";
				$entete.=  "<td bgcolor=\"$color\">  </td> <td bgcolor=\"$color\"><font color=\"white\">  Commentaire  saisie</td> ";						
				$entete.=  "<td bgcolor=\"$color\"><font color=\"white\">  En souligné jaune, partie nécessitant votre attention </td> ";						
				$resultat=addslashes2("$entete $cumul </table>"); 
				command("INSERT INTO `cc_audit_cnil`  VALUES ( '$periode','$support', '$resultat' )" );		
				// il faut envoyer le mail
				
				$mail_struct="";
				$dest="";				
				$reponse = command("SELECT * FROM fct_fissa WHERE support='$support' "); 
				if ($donnees = fetch_command($reponse) )
					{
					$idx=$donnees["organisme"];
					$libelle=$donnees["libelle"];			
					
					$reponse = command("SELECT * FROM r_organisme WHERE idx='$idx' "); 
					if ($donnees = fetch_command($reponse) )
						$mail_struct=$donnees["mail"];
											
					$reponse = command("SELECT * FROM r_user WHERE droit='R' and organisme='$idx' "); 
					while ($donnees = fetch_command($reponse) ) 
						$dest.=$donnees["mail"].",";				
					}
				if ($envoi_mail)
					{
					if ($dest !="")
						{
						if  (mail2	( $dest , "FISSA et Suivi : Analyse textes saisies ($periode)", "$entete $cumul </table>" , $libelle, $mail_struct  )) 
							 ajout_log_tech( "Envoi audit CNIL de  $support sur $periode à $dest ");
						 else 
							echo "Echec";
						}
					}
					else
					 echo "$dest : $entete $cumul </table> nbtest= $nb_tst";
					
				}
			else
				command("INSERT INTO `cc_audit_cnil`  VALUES ( '$periode','$support', '')" );

	
		}


	?>