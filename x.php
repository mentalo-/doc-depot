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
	
	echo "<hr>";

	// Affichage des principaux indicaturs
	titre_kpi();
	
	$date=date('Y-m-d',  mktime(0,0,0 , date("m"), date("d")-1, date ("Y")));
	kpi("$date");
	
	$date=date('Y-m-d',  mktime(0,0,0 , date("m"), date("d")-7, date ("Y")));
	kpi("$date");
	
	echo "</table><hr>";

		
	echo "<div class=\"CSSTableGenerator\" ><table><tr><td > Date  </td><td> Prio </td><td> Evénement </td><td> Ip </td>";
	$reponse =command("select * from  z_log_t  order by date desc limit 0,15");		
	while ($donnees = fetch_command($reponse) ) 
			{
			$date=$donnees["date"];	
			$ligne=$donnees["ligne"];
			$ip=$donnees["ip"];
			$prio=$donnees["prio"];
			echo "<tr><td>  $date   </a></td><td> $prio </td><td> $ligne </td><td> $ip </td>";
			}
	echo "</table></div><hr>";

	
		echo "<div class=\"CSSTableGenerator\" ><table><tr><td > Date </td><td> IP</td><td> Action </td><td> Compte </td><td> Acteur </td>";
		$reponse =command("select * from  log  order by date desc limit 0,10");		
		while ($donnees = fetch_command($reponse) ) 
				{
				$date=$donnees["date"];	
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
			

	
	
	echo "</body>";

	
	?> 