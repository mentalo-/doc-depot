<?php
///////////////////////////////////////////////////////////////////////
//   This file is part of doc-depot.
//
//   doc-depot is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
//
//   doc-depot is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
//
//   You should have received a copy of the GNU General Public License along with doc-depot.  If not, see <http://www.gnu.org/licenses/>.
///////////////////////////////////////////////////////////////////////

 session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<?php include 'header.php';	  

	echo "<head>";
	echo "</head><body>";
	include "connex_inc.php";
	include 'general.php';
	include 'exploit.php';
	
	function aff_log_tech($prio)
		{
		$d="";
		echo "<div class=\"CSSTableGenerator\" ><table><tr><td > Date  </td><td> Prio </td><td> Evénement </td><td> Ip </td>";
		$reponse =command("select * from  z_log_t where prio='$prio' order by date desc limit 0,19");		
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
		}
		
	$time_ttt= time();
	$heure=date('H',  time());
	ajout_log_jour(" ==================================================================================================== X");
	
	affiche_alarme();

	// vérification que la météo est accessible 
	$url = "http://your-meteo.fr/prog/recup_data.php?id=29591";
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	$result = curl_exec($curl);
	$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	if ($statusCode!=200)
		echo "Météo $url :<FONT color=\"#ff0000\" > KO ($statusCode) </font><hr>";

	$reponse = command("SELECT * FROM DD_param WHERE nom like 'MONITOR_%' ");
	echo "<table><tr>";
	while ($donnees = fetch_command($reponse) ) 
		{
		$nom=$donnees["nom"];
		$h_old= $donnees["valeur"];
		if ($h_old!="")
			{
			$h=date("d/m/Y H:i",$h_old);
			echo "<tr><td>$nom :</td><td> $h </td>";
			$delta = (time()-$h_old);
			$c="";
			if ($delta<30) 
				$c =" bgcolor=\"lightgreen\"  ";
			if ($delta>6*60) 
				$c =" bgcolor=\"orange\"  ";
			if ($delta>10*60) 
				$c =" bgcolor=\"red\"  ";
			echo "<td $c> ==> $delta sec</td>";	
			if ($delta>2000000) 	
				ecrit_parametre($nom,"");
			}
		}
	echo "</table>";
	
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


	aff_log_tech("P0");
	aff_log_tech("P2");
	aff_log_tech("P1");

	echo "</body>";
	
	?>