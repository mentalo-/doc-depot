<?php

$temp_3j= array();
$temp_3j_max= array();

$mode_test = ($_SERVER['REMOTE_ADDR']=="127.0.0.1") ;
 
	

  /*
	function charge_liste_ville_csv()
		{
		global $dept;
		
		$f= fopen("liste-villes.csv","r");
		$i=0;
		while (($ligne=fgets($f)) && ($i<100000))
			{
			$d3= explode("/",$ligne); 
			if (strlen($d3[2])==4) 
				$d3[2]="0".$d3[2];
			$d3[1] = str_replace ("-"," ",$d3[1]);
			
			for ($j=1;$j<96; $j++)
				if ($d3[0]==$dept[$j])
					echo "<br>$j - ". $d3[1];
			
			$i++;
			}
		echo "<br> $i villes";
		fclose($f);
		exit();
		}
*/		
		
 
	function calcul_moyenne($departement )
		{
		global $temp_3j,$temp_3j_max,$jour_3j,$pluie,$jour_pluie,$heure_pluie,$mode_test;
		
			$dept= array(
				'1' => '49', '2' => '793','3' => '1377', '4' => '1515',	'5' => '1757',
				'6' => '1945',	'7' => '2185',	'8' => '2423',	'9' => '2891',	'10' => '3445',
				'11' => '3576',	'12' => '4111',	'13' => '4330',	'14' => '4468',	'15' => '5072',
				'16' => '5309',	'17' => '5947',	'18' => '6157',	'19' => '6657',	'2A' => '6677',	'2B' => '6702',	'20' => '6702',
				'21' => '7221',	'22' => '7922',	'23' => '8129',	'24' => '8563',	'25' => '8843',
				'26' => '9697',	'27' => '9923',	'28' => '10429','29' => '10948','30' => '11186',
				'31' => '11856','32' => '11897','33' => '12363','34' => '12966','35' => '13349',
				'36' => '13511','37' => '13946','38' => '14125','39' => '14743','40' => '15192',
				'41' => '15345','42' => '15801','43' => '35213','44' => '16253','45' => '16599',
				'46' => '16747','47' => '17015','48' => '17407','49' => '17500','50' => '18313',
				'51' => '18840','52' => '19134','53' => '19643','54' => '20278','55' => '20385',
				'56' => '21122','57' => '21564','58' => '22021','59' => '22464','60' => '22816',
				'61' => '23414','62' => '23925','63' => '24855','64' => '25613','65' => '26141',
				'66' => '26304','67' => '26852','68' => '27005','69' => '27595','70' => '28088',
				'71' => '28366','72' => '28820','73' => '29073','74' => '29311','75' => '29591',
				'76' => '29914','77' => '30554','78' => '31031','79' => '31221','80' => '31385',
				'81' => '32130','82' => '32525','83' => '32720','84' => '32756','85' => '33059',
				'86' => '33342','87' => '33514','88' => '33786','89' => '34146','90' => '34582',
				'91' => '34733','92' => '34876','93' => '34893','94' => '34941','95' => '35015'		);		
				
		$ville= $dept[$departement];
		$url = "http://your-meteo.fr/prog/recup_data.php?id=$ville";
		echo "$url";
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		$result = curl_exec($curl);
		$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		if ($statusCode==200)
			{
			/*Initialisation de la ressource curl*/
			$curl = curl_init();
			/*On indique à curl quelle url on souhaite télécharger*/
			curl_setopt($curl, CURLOPT_URL, $url);
			/*On indique à curl de nous retourner le contenu de la requête plutôt que de l'afficher*/
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			/*On indique à curl de ne pas retourner les headers http de la réponse dans la chaine de retour*/
			curl_setopt($curl, CURLOPT_HEADER, false);
			/*On execute la requete*/
			$output = curl_exec($curl);
			/*On a une erreur alors on la lève*/
			
			$nb= substr_count($output, '<br>');
			$alerte="";
			
			$moyenne_min=0;
			$nb_moy_min=0;
			$moyenne_max=0;
			$nb_moy_max=0;
			$j=0;
			$j_max=0;
			$lignes= explode("<br>",$output);  // on recupére ligne par ligne
			
			$d3= explode("/",$lignes[0]);  // on recupére champ par champ
			
			if (!isset($d3[0][9])) // on vérifie que la ligne n'est pas vide
				{
				echo "-".$url;
				return(false);
				}
				
			if (!$mode_test)
				if ($d3[0][6]*10+$d3[0][7]!=date("d"))
					return(false); // si ce ne sont pas les données du jour on arrête (sauf en mode test)
				
			$p=0;
			$nbj=7;
			for ($n=0;$n<$nbj*8;$n++)
				{
				$d3= explode("/",$lignes[$n]); 
				$mois3=$d3[0][4]*10+$d3[0][5];
				$jour3=$d3[0][6]*10+$d3[0][7];
				
				$heure_pluie[$p]=$d3[0][8].$d3[0][9]."h";
				$jour_pluie[$p]=date("d/m", mktime(0 ,0, 0 , $mois3, $jour3, date ("Y")));

				if ($heure_pluie[$p]=="00h")
					{
					$heure_pluie[$p]="minuit";
					$jour_pluie[$p]=$jour_pluie[$p-1];
					}					
				$pluie[$p++]=$d3[4];

				if ($d3[4]!=0)
					echo "<br>".$jour_pluie[$p-1]." ".$heure_pluie[$p-1]. " . . . . . . . . . . . : ".$pluie[$p-1]. " mm "; 
				
				// mesure grand froid
				if ($d3[0][8]=='0')
					if ($d3[0][9]!='9')
						{
						$t=$d3[3];
						$v=$d3[13];
						$v=pow ($v,0.16);
						$t = 13.12 + 0.6215*$t - 11.37*$v + 0.3965*$t*$v; 
				
						$moyenne_min+=$t;
						$nb_moy_min++;
						}
					else
						{
						$jour_3j[$j] = date("d/m", mktime(0,0, 0 , $mois3, $jour3-1, date ("Y")));
					
						if ( ($nb_moy_min!=0) 
							&& (
							mktime(0,0, 0 ,$mois3 , $jour3, date ("Y"))>mktime(0,0, 0 , date("m"), date("d"), date ("Y"))) )
							{
							$temp_3j[$j++]= $moyenne_min/$nb_moy_min;
							echo sprintf("<br> %s : min %1.1f°c ",$jour_3j[$j-1],$temp_3j[$j-1]);
							}
						$moyenne_min=0;
						$nb_moy_min=0;
						}
						
				// Canicule
				if ($d3[0][8]=='1')
					{
					$t=$d3[3];
					$moyenne_max+=$t;
					$nb_moy_max++;
					}
				if ($d3[0][8]=='2')
					{
					$jour_3j[$j_max] = date("d/m", mktime(0,0, 0 , $mois3, $jour3, date ("Y")));
				
					if ( ($nb_moy_max!=0) && ( mktime(0,0, 0 ,$mois3 , $jour3, date ("Y"))>mktime(0,0, 0 , date("m"), date("d"), date ("Y"))) )
						{
						$temp_3j_max[$j_max++]= $moyenne_max/$nb_moy_max;
						echo sprintf("<br> %s : - - - - - - - Max %1.1f°C ", $jour_3j[$j_max-1] ,$temp_3j_max[$j_max-1]);
						}
					$moyenne_max=0;
					$nb_moy_max=0;
					}
				}
			curl_close($curl);
			return(true);
			}
		else
			echo "Erreur accès données ==>".$statusCode;
		return(false);
		}

		
	function msg_alerte_pluie($sueil)
		{	
		global $pluie,$jour_pluie,$heure_pluie,	$mode_test;
		
		echo "<p> msg pluie"; 
		
		$max_pluie=0;
		$msg="";
		$alarme=false;
		$jour_debut="";
		$nbj=2;
		if ($mode_test)
			$nbj=6;
		for ($n=0;$n<$nbj*8;$n++) // T396  on commence l'analyse à 9h du jour en cours
			if (isset($pluie[$n+2]))
				{
				$m2=0;
				if ($n>0)
				$m2=$pluie[$n]+$pluie[$n-1]+$pluie[$n+1];
				$m1=$pluie[$n]+$pluie[$n+1];
				$m=$pluie[$n];
				
				if($m1>$max_pluie)
					$max_pluie=$m1;
				if ( $alarme ) 
					{
					if ( ($m<$sueil/2) || ($m1<$sueil/2) )
						{
						if ($jour_pluie[$n]==$jour_debut)
								$msg.=" jusqu'à ".$heure_pluie[$n].", ";						
							else
								$msg.=" jusqu'au ".$jour_pluie[$n]." ".$heure_pluie[$n].", ";
						$alarme=false;
						}
					}
				else
					{
					if (($m>$sueil) || ( ($m1>$sueil) && ($pluie[$n]>$sueil/2) ) ||  ( ($m2>$sueil)  && ($pluie[$n]>$sueil/2) ) )
						{
//						if ($jour_pluie[$n]!=$jour_pluie[3]) // si  le jour de pluie n'est pasaujourd'hui
							{
							$msg.=" du ".$jour_pluie[$n]." ".$heure_pluie[$n];
							$jour_debut=$jour_pluie[$n];
							}
//						else
//							{
//							if ($heure_pluie[$n]<=date("h")."h")  //  on ne traite de debut avant l'heure courante
//								{
//								$msg.=" de ".$heure_pluie[$n];
//								$jour_debut=$jour_pluie[$n];
//								}	
//							}								
						$alarme=true;
						}
					}
				}
		
		if ($msg!="")
			{
			if ($alarme)
				$msg = " à partir ".$msg;
			if ($max_pluie>2*$sueil)
				$msg="Risque de fortes pluies $msg";
			else
				$msg="Risque de pluies $msg";
			
			}
		return $msg;
		}
		
		
	function msg_alerte_min($sueil)
		{
		global $temp_3j,$jour_3j;
		
		$msg="";
		for ($j=0;$j<3;$j++)
			{
			if ($temp_3j[$j]<$sueil)
				{
				if ($msg=="")
					$msg="Grand Froid pour la nuit ";
						else
					$msg.=" et ";
		
				$msg.=" du ".$jour_3j[$j]. " (".sprintf("%1.1f",$temp_3j[$j])."°C ressentis) ";
				}
			}
			
		if ($msg !="")
			$msg.= " Pensez à vous mettre à l'abri.";
		
		return ($msg);
		}

////////////////////////////////////////////////////////////////////////////////////////////
// Définition canicule 		
// http://quoi.info/actualite-societe/2012/08/16/quand-peut-on-parler-de-canicule-1148076/	
	function msg_alerte_max($sueil, $sueil_min)
		{
		global $temp_3j_max,$jour_3j,$temp_3j,$jour_3j;
		
		$msg="";
		for ($j=0;$j<2;$j++)
			{
			if ( ($temp_3j_max[$j]>$sueil) && ($temp_3j[$j]>$sueil_min))
				{
				if ($msg=="")
					$msg="Forte chaleur ";
						else
					$msg.=" et ";
		
				$msg.=" le ".$jour_3j[$j]. " (".sprintf("%2.0f",$temp_3j_max[$j])."°C) ";
				}
			}
			
		if ($msg !="")
			$msg.= ". Pensez à vous hydrater régulièrement.";
	
		return ($msg);
		}

		function reformule_msg($msg)
			{
			$libelle_jour= array( 1 => "Lundi" , 2  => "Mardi" ,3  => "Mercredi" , 4  => "Jeudi" , 5  => "Vendredi" , 6  => "Samedi" , 7  => "Dimanche");
			
			$apres_demain= date("d/m",mktime(0 ,0, 0 , date("m"), date("d")+2, date ("Y")));
			$demain= date("d/m",mktime(0 ,0, 0 , date("m"), date("d")+1, date ("Y")));
			$aujoudhui= date("d/m",mktime(0 ,0, 0 , date("m"), date("d"), date ("Y")));
			
			$js_aujourdhui=$libelle_jour[date("N",mktime(0 ,0, 0 , date("m"), date("d"), date ("Y")))];
			$js_demain=$libelle_jour[date("N",mktime(0 ,0, 0 , date("m"), date("d")+1, date ("Y")))];
			$js_apres_demain= $libelle_jour[date("N",mktime(0 ,0, 0 , date("m"), date("d")+2, date ("Y")))];
			
			$msg=str_replace ($apres_demain,"après-demain $js_apres_demain", $msg);
			$msg=str_replace ($demain,"demain $js_demain", $msg);
			$msg=str_replace ($aujoudhui,"aujourd'hui $js_aujourdhui", $msg);
		
			$msg=str_replace ("le demain","demain", $msg);
			$msg=str_replace ("le aujourd","aujourd", $msg);
			$msg=str_replace ("le après-","après-", $msg);
			
			
			// Spécifique pluie 			
			$msg=str_replace ("au demain","à demain", $msg);
			$msg=str_replace ("du demain","de demain", $msg);
			$msg=str_replace ("du après-","d'après-", $msg);
			$msg=str_replace ("du aujourd","d'aujourd", $msg);
			$msg=str_replace ("au aujourd","à aujourd", $msg);
			$msg=str_replace ("au minuit","à minuit", $msg);	
			
			$msg=str_replace ("12h", "midi", $msg);
			$msg=str_replace ("18h", "en fin d'après-midi", $msg);
			$msg=str_replace ("15h", "après-midi", $msg);
			$msg=str_replace ("21h", "soir", $msg);
			$msg=str_replace ("00h", "minuit", $msg);
			$msg=str_replace ("09H", "matin", $msg);
			$msg=str_replace ("03h", "dans la nuit", $msg);
			$msg=str_replace ("06h", "à l'aube", $msg);		
			
			$msg=str_replace ("jusqu'à à", "jusqu'à", $msg);		
			$msg=str_replace ("jusqu'à soir", "jusqu'au soir", $msg);		
			$msg=str_replace ("jusqu'à en", "jusqu'en", $msg);		
			$msg=str_replace ("jusqu'à après", "jusque l'après", $msg);		
			$msg=str_replace ("jusqu'à matin", "jusqu'au matin", $msg);		
			$msg=str_replace ("jusqu'à dans", "jusque dans", $msg);		
					
			return($msg);
			}
			
			
require_once "connex_inc.php";
require_once "general.php";
require_once "param.php";
require_once "include_mail.php";

	 if ($mode_test)
		Echo "mode test alerte";
		
	$ilya5minutes = mktime(date("H"),date("i")-5, 0 , date("m"), date("d"), date ("Y"));
	$hier = mktime(date("H")-12,date("i"), 0 , date("m"), date("d"), date ("Y"));
	$maintenant = mktime(date("H"),date("i"), 0 , date("m"), date("d"), date ("Y"));
	echo "<br>TTT alerte: ";	


	$reponse = command("SELECT * FROM cc_alerte WHERE dernier_envoi>'$ilya5minutes' ");
	if ( ($donnees = fetch_command($reponse)) && (!$mode_test) )
		Echo "<BR> Pas de traitment car envoi récent ";
	else
		{

		if ($mode_test)
			$reponse = command("SELECT * FROM cc_alerte WHERE tel<>''  ");	
		else
			{
			if ((date("H")<21) && (date("H")>8) )
				$reponse = command("SELECT * FROM cc_alerte WHERE ( dernier_ttt <='$hier' or dernier_envoi='' ) and tel<>'' limit 1 ");
			else
				$reponse = command("SELECT * FROM cc_alerte WHERE dernier_ttt='' and tel<>''  limit 1 ");
			}

		$auj=date("Y-m-d");
		while ($donnees = fetch_command($reponse) ) 
			{
			$telephone=$donnees["tel"];	
			$dept=$donnees["dept"];	
			echo "<p> $telephone ($dept) ==> ";

			$r1 = command("SELECT * FROM cc_alerte WHERE dept='$dept' and tel='' and creation='$auj' ");
			
			$result = false;
			if ( ($d1 = fetch_command($r1) ) && (!$mode_test) ) 
				{
				$msg=stripcslashes($d1["sueil"]);
				$result = true;
				}
			else
				{
				$result = calcul_moyenne($dept );
				if ($result)
					{
					$msg = msg_alerte_min(-5);
					
					if ($msg=="")
						$msg = msg_alerte_max(28, 8);
					// if ($mode_test)
						{
						if ($mode_test)
							$msg_pluie= msg_alerte_pluie(0.5);
						else
							$msg_pluie= msg_alerte_pluie(2);

						$msg=$msg_pluie.$msg;	
						}				
					$msg= reformule_msg($msg);
					$msg= filtre_xss($msg);
					command("INSERT INTO `cc_alerte`  VALUES ( '$auj', '', '$dept', '$msg','','','','','','')");
					}
				else
					$msg="Pb accès info météo";
				}
			echo "<br> --> ".stripcslashes ($msg)." (".strlen($msg).")";

			if ($result)
				{
				if ($msg !="") 
					{

					
					$msg=str_replace (" .", ".", $msg);
					command("UPDATE `cc_alerte` SET dernier_envoi='$maintenant'  where tel='$telephone'  ");
					if (!$mode_test)
						envoi_SMS($telephone,stripcslashes($msg));
					}
				command("UPDATE `cc_alerte` SET dernier_ttt='$maintenant'  where tel='$telephone'  ");
				}
			}
		}
			
?>

