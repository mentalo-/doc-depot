  <?php  

	echo "<head>";
	echo "</head><body>";
	include "connex_inc.php";
	include 'general.php';
	include 'exploit.php';
	
		
	$time_ttt= time();
	$heure=date('H',  time());
	ajout_log_jour(" ==================================================================================================== X");
	
	affiche_alarme();

	$ancien_ttt=parametre("TECH_date_dernier_ttt");
	$delta= (time()-$ancien_ttt);
	Echo "<p>Dernier traitement TTT : Il y a ". $delta . "sec<p>";	
	commentaire_html("X: Affiche Indicateurs");
	echo "<p> Nbre de mails envoyés : ". parametre("TECH_nb_mail_envoyes");
	echo "<p> Nbre de SMS envoyés : ". parametre("TECH_nb_sms_envoyes");
	echo "<p> Nbre de SMS envoyés OVH : ". parametre("TECH_nb_sms_envoyes_operateur");
	
	echo "<hr>";

	// Affichage des principaux indicaturs
	titre_kpi();
	
	$date=date('Y-m-d',  mktime(0,0,0 , date("m"), date("d")-1, date ("Y")));
	kpi("$date");
	
	$date=date('Y-m-d',  mktime(0,0,0 , date("m"), date("d")-7, date ("Y")));
	kpi("$date");
	
	echo "</table><hr>";

	$d="";
	echo "<div class=\"CSSTableGenerator\" ><table><tr><td > Date  </td><td> Prio </td><td> Evénement </td><td> Ip </td>";
	$reponse =command("select * from  z_log_t  order by date desc limit 0,19");		
	while ($donnees = fetch_command($reponse) ) 
			{
			$date=$donnees["date"];	
			$d3= explode(" ",$date);
			if(( $d!="") && ($d!=$d3[0]))
				echo "<tr><td> - - -  </td><td> - - </td><td> - - - - - -  </td><td> - - -  </td>";
			$d=$d3[0];
			$ligne=$donnees["ligne"];
			$ip=$donnees["ip"];
			$prio=$donnees["prio"];
			echo "<tr><td>  $date   </a></td><td> $prio </td><td> $ligne </td><td> $ip </td>";

			}
	echo "</table></div><hr>";

	
		$d="";
		echo "<div class=\"CSSTableGenerator\" ><table><tr><td > Date </td><td> IP</td><td> Action </td><td> Compte </td><td> Acteur </td>";
		$reponse =command("select * from  log  order by date desc limit 0,15");		
		while ($donnees = fetch_command($reponse) ) 
				{
				$date=$donnees["date"];	
				$d3= explode(" ",$date);
				if(( $d!="") && ($d!=$d3[0]))
				echo "<tr><td> - - - </td><td> - - </td><td> - - - - - - -  </td><td> - - - </td><td> - - - </td>";
				$d=$d3[0];
				$ligne=$donnees["ligne"];
				$user=$donnees["user"];
				if (is_numeric($donnees["user"]))
					$user=libelle_user($donnees["user"]);
				$acteur=$donnees["acteur"];
				if (is_numeric($donnees["acteur"]))
					$acteur=libelle_user($donnees["acteur"]);
				$ip=$donnees["ip"];

				echo "<tr><td>  $date   </a></td><td> $ip </td><td> $ligne </td><td> $user </td><td> $acteur </td>";

				}
		echo "</table></div><hr>";

	$reponse = command("SELECT * FROM DD_param WHERE nom like 'MONITOR_%' ");
	echo "<table><tr>";
	while ($donnees = fetch_command($reponse) ) 
		{
		$nom=$donnees["nom"];
		$h_old= $donnees["valeur"];
		$h=date("d/m/Y H:i",$h_old);
		echo "<tr><td>$nom :</td><td> $h </td>";
		$delta = (time()-$h_old);
		$c="";
		if ($delta<30) 
			$c =" gcolor=\"lightgreen\"  ";
		if ($delta>6*60) 
			$c =" gcolor=\"orange\"  ";
		if ($delta>10*60) 
			$c =" gcolor=\"red\"  ";
			
		echo "<td $c> ==> $delta sec</td>";	
		}
	echo "</table>";
	echo "</body>";
	
	?> 