<?php

$temp_3j= array();
$temp_3j_max= array();

$mode_test = (($_SERVER['REMOTE_ADDR']=="127.0.0.1") || ( (isset($_SERVER['PHP_SELF'])) && ($_SERVER['PHP_SELF'] == '/alerte_ttt.php')) ) ;
 
	function charge_liste_ville_csv()
		{
		$f= fopen("liste-villes.csv","r");
		$i=0;
		while (($ligne=fgets($f)) && ($i<100000))
			{
			$d3= explode("/",$ligne); 
			if (strlen($d3[2])==4) 
				$d3[2]="0".$d3[2];
			$d3[1] = str_replace ("-"," ",$d3[1]);
			
			//if (strpos($d3[2],"000")==2)
			//	echo "<br>". $d3[1]. " - ". $d3[2]. " - ". $d3[3];
			
			$i++;
			}
		echo "<br> $i villes";
		fclose($f);
		exit();
		}
		
		
 
	function calcul_moyenne($departement )
		{
		global $temp_3j,$temp_3j_max,$jour_3j,$pluie,$jour_pluie,$mode_test;
		
		$dept= array(
				'01' => '3445',
				'13' => '4330',
				'14' => '4468',
				'17' => '5947',
				'31' => '11856',
				'33' => '12363',
				'35' => '13349',
				'37' => '13946',
				'38' => '14125',
				'59' => '22464',
				'51' => '18840',
				'67' => '26852',
				'69' => '27595',
				'75' => '29591',
				'78' => '31031',
				'91' => '34669',
				'92' => '34876',
				'93' => '34893',
				'94' => '34941',
				'95' => '35015'
				);	
			
		$ville= $dept[$departement];
		$url = "http://your-meteo.fr/prog/recup_data.php?id=$ville";

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
			$lignes= explode("<br>",$output); 
			
			$d3= explode("/",$lignes[0]); 
			
			if (!$mode_test)
				if ($d3[0][6]*10+$d3[0][7]!=date("d"))
					return(false); // si ce ne sont pas les données du jour on arrête
				
			$p=0;
			$nbj=7;
			for ($n=0;$n<$nbj*8;$n++)
				{
				$d3= explode("/",$lignes[$n]); 
				$jour_pluie[$p]=date("d/m", mktime(0 ,0, 0 , date("m"), $d3[0][6]*10+$d3[0][7], date ("Y")))." ".$d3[0][8].$d3[0][9]."h ";
				$pluie[$p++]=$d3[4];
				if ($d3[4]!=0)
					echo "<br>".$jour_pluie[$p-1]. " . . . . . . . . . . . : ".$pluie[$p-1]. " mm "; 
				
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
						$jour_3j[$j] = date("d/m", mktime(0,0, 0 , date("m"), $d3[0][6]*10+$d3[0][7]-1, date ("Y")));
					
//						if ( ($nb_moy_min!=0) && ($d3[0][6]*10+$d3[0][7]> date("d")) )
						if ( ($nb_moy_min!=0) 
							&& (
							mktime(0,0, 0 ,$d3[0][4]*10+$d3[0][5] , $d3[0][6]*10+$d3[0][7], date ("Y"))>mktime(0,0, 0 , date("m"), date("d"), date ("Y"))) )
							{
							$temp_3j[$j++]= $moyenne_min/$nb_moy_min;
							echo sprintf("<br> %s/%s : %1.1f°c ",$d3[0][6].$d3[0][7], $jour_3j[$j-1],$temp_3j[$j-1]);
							}
						$moyenne_min=0;
						$nb_moy_min=0;
						}
				if ($d3[0][8]=='1')
					{
					$t=$d3[3];
					$moyenne_max+=$t;
					$nb_moy_max++;
					}
				if ($d3[0][8]=='2')
					{
					$jour_3j[$j_max] = date("d/m", mktime(0,0, 0 , date("m"), $d3[0][6]*10+$d3[0][7], date ("Y")));
				
					if ( ($nb_moy_max!=0) && ( mktime(0,0, 0 ,$d3[0][4]*10+$d3[0][5] , $d3[0][6]*10+$d3[0][7], date ("Y"))>mktime(0,0, 0 , date("m"), date("d"), date ("Y"))) )
						{
						$temp_3j_max[$j_max++]= $moyenne_max/$nb_moy_max;
						echo sprintf("<br> %s/%s : %1.1f°C ",$d3[0][6].$d3[0][7], $jour_3j[$j_max-1] ,$temp_3j_max[$j_max-1]);
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
		global $pluie,$jour_pluie;
		
		$msg="";
		$alarme=false;
		$nbj=3;
		for ($n=0;$n<$nbj*8;$n++)
			if (isset($pluie[$n+2]))
				{
				$m=$pluie[$n]+$pluie[$n+1]+$pluie[$n+2];
			
				if ( $alarme ) 
					{
					if ($m<$sueil)
						{
						if ($msg=="")
							$msg.=" jusqu'au ".$jour_pluie[$n].", ";
						else
							$msg.=" au ".$jour_pluie[$n].", ";
						$alarme=false;
						}
					}
				else
					{
					if ($m>$sueil)
						{
						$msg.=" du ".$jour_pluie[$n];
				
						$alarme=true;
						}
					}
				}
		if ($msg!="")
			$msg="Risque de fortes pluies $msg";
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
					$msg="Bonjour, Alerte Grand Froid pour la nuit ";
						else
					$msg.=" et ";
		
				$msg.=" du ".$jour_3j[$j]. " (".sprintf("%1.1f",$temp_3j[$j])."°C ressentis) ";
				}
			}
			
		if ($msg !="")
			$msg.= " Pensez à vous mettre à l'abri.";
		
		return ($msg);
		}
		
	function msg_alerte_max($sueil)
		{
		global $temp_3j_max,$jour_3j;
		
		$msg="";
		for ($j=0;$j<3;$j++)
			{
			if ($temp_3j_max[$j]>$sueil)
				{
				if ($msg=="")
					$msg="Bonjour, Alerte Forte chaleur ";
						else
					$msg.=" et ";
		
				$msg.=" le ".$jour_3j[$j]. " (".sprintf("%2.1f",$temp_3j_max[$j])."°C) ";
				}
			}
			
		if ($msg !="")
			$msg.= " Pensez à vous mettre à l'abri et à vous hydrater réguliérement.";
	
		return ($msg);
		}
			
			
require_once "connex_inc.php";
require_once "general.php";
require_once "include_mail.php";


 if ($mode_test)
	Echo "mode test alerte";
	
	{
	$dept= parametre("DD_alerte_dept");
	
	if ($mode_test)
		$dept=92;
	
	echo '<hr> Dept: '.$dept."<p>";
	$hier = mktime(date("H"),date("i"), 0 , date("m"), date("d")-1, date ("Y"));
	$maintenant = mktime(date("H"),date("i"), 0 , date("m"), date("d"), date ("Y"));
	
	if ($mode_test)
		$reponse = command("SELECT * FROM cc_alerte WHERE dept='$dept' ");	
	else
		{
		if ((date("H")<20) && (date("H")>12) )
			$reponse = command("SELECT * FROM cc_alerte WHERE dept='$dept' and ( dernier_ttt<'$hier' or dernier_ttt='' ) limit 2");
		else
			$reponse = command("SELECT * FROM cc_alerte WHERE dept='$dept' and dernier_ttt='' limit 2");
		}

	$calcul=false;

	while ($donnees = fetch_command($reponse) ) 
		{
		if (!$calcul) 
			{
			$calcul=true;
			calcul_moyenne($dept );

			$msg = msg_alerte_min(-5);
			
			if ($msg=="")
				$msg = msg_alerte_max(22);
			
			$msg_pluie= msg_alerte_pluie(2);
			
			if ($mode_test)
				echo "<p> ==> $msg_pluie";
			}
			
		$telephone=$donnees["tel"];

		if (!$mode_test)
			if (($msg !="") || ($msg_pluie !="")  )
				{
				command("UPDATE `cc_alerte` SET dernier_envoi='$maintenant'  where tel='$telephone'  ");
				envoi_SMS($telephone,$msg.$msg_pluie);
				}
		command("UPDATE `cc_alerte` SET dernier_ttt='$maintenant'  where tel='$telephone'  ");
		}

	if ($dept==99)
		$dept=0;
		
	ecrit_parametre("DD_alerte_dept",$dept+1);	
	}		
?>

