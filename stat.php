<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
    <head>
	
	     <title>Statistiques </title>
		
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
 <?php
include 'calendrier.php';
?> 	      
		</head>
		<body>
<?php

	function echelle_semaine($s_fin,$detail)
		{
		global $jour_d, $jour_f, $s_org;
		
		echo "<tr> <td> </td><td>  </td>";
		if ($detail)
			for ($k=$s_org;$k<$s_fin;$k++)
				echo "<td width=\"20\">S$k </td>";
		echo "<td > Total</td>";	
			
		echo "<tr> <td> </td><td>  </td>";
		for ($k=$s_org;$k<$s_fin;$k++)
			{
			$tmp=get_lundi_dimanche_from_week($k,"2013");
			$lundi=$tmp[0];
			$jour_d[$k]=$lundi;
			$lundi = date_fr2(substr($lundi,2));
			if ($detail) echo "<td width=\"20\">$lundi</td>";		
			}
		echo "<tr> <td> </td><td>  </td>";
		for ($k=$s_org;$k<$s_fin;$k++)
			{
			$tmp=get_lundi_dimanche_from_week($k,"2013");
			$dimanche=$tmp[1];
			$jour_f[$k]=$dimanche;
			if ($detail) echo "<td width=\"20\"> </td>";		
			}
		
		}
		
		
	function date_gb($d)
		{
		$a=substr($d,6,4);
		$m=substr($d,3,2);
		$j=substr($d,0,2);
		return("$a-$m-$j");
		}

	function date_fr2($d)
		{
		$a=substr($d,0,2);
		$m=substr($d,3,2);
		$j=substr($d,6,2);
		return("$j/$m/$a");
		}
		
	function date_fr($d)
		{
		$a=substr($d,0,4);
		$m=substr($d,5,2);
		$j=substr($d,8,2);
		return("$j/$m/$a");
		}
		
	function semaine($date)
		{
		$jour=date("z",$date);
		$num_sem=($jour/7)+1;
		$num=intval($num_sem);
		return $num;
		}
		
		
	function get_lundi_dimanche_from_week($week,$year)
		{
		if(strftime("%W",mktime(0,0,0,01,01,$year))==1)
		  $mon_mktime = mktime(0,0,0,01,(01+(($week-1)*7)),$year);
		else
		  $mon_mktime = mktime(0,0,0,01,(01+(($week)*7)),$year);
		 
		if(date("w",$mon_mktime)>1)
		  $decalage = ((date("w",$mon_mktime)-1)*60*60*24);
		 
		$lundi = $mon_mktime - $decalage;
			$dimanche = $lundi + (6*60*60*24);
		 
		return array(date("Y-m-d",$lundi),date("Y-m-d",$dimanche));
		}
 
	function echelle_jour($org,$nb_j)
		{
		for ($k=$org; $k<$nb_j; $k++)
			{
			$t=time()+$k*(24*60*60);
			$d=date( "d/m",$t);
			$w=date("w",$t);
			if (($w==0) || ($w==6))
				echo "<td BGCOLOR=\"silver\" width=\"20\"> $d </td>";
			else
				echo "<td width=\"20\">$d </td>";
			}
		}

	function couleur($j)
			{
			if ($j %2 == 0) return("BGCOLOR=\"#F2F2F2\" "); else  return("BGCOLOR=\"#FAFAFA\" ");
			}


			
	function aff ($v,$detail)
		{
		if ($detail)
			if ($v==0)
				echo "<td width=\"20\"> - </td>";
			else
				if (round($v )==$v)
					echo "<td width=\"20\">".$v."</td>";
				else
					echo "<td width=\"20\">".sprintf("%2.2f",$v)."</td>";
		return($v);
		}

include 'general.php';

	$bdd = variable_s("support");
	if ($bdd!="") 
		$_SESSION['support']=$bdd;
	else
		$bdd=$_SESSION['support'];
		
	// ConnexiondD		
include "connex_inc.php";		

if ( !isset($_SESSION['pass']) ||($_SESSION['pass']==false) )
	// si pas de valeur pass en session on affiche le formulaire...
	{
	exit;
	} // mot de pass invalide => STOP la page ne s'affiche pas ! 

	$detail=isset ($_GET["detail"]);
	
	if (!isset ($_GET["date_jour"]))
		$date_jour=date('Y-m-d',  mktime(0,0,0 , date("m")-1, date("d"), date ("Y")));
	else 
		$date_jour=$_GET["date_jour"];

	if (!isset ($_GET["date_fin"]))
		$date_fin=date('Y-m-d',  mktime(0,0,0 , date("m"), date("d"), date ("Y")));
	else 
		$date_fin=$_GET["date_fin"];		
		
		$a=substr($date_jour,0,4);
		$m=substr($date_jour,5,2);
		$j=substr($date_jour,8,2);
		$s_org= floor($a-2013 + ($m-1)*52/12 + $j/7);
		if ($a=="2014")
			$s_org+=52;
		if ($a=="2015")
			$s_org+=2*52;		
		if ($a=="2016")
			$s_org+=3*52;

	
			// ===================================================================== Bloc DATE
			echo "<table border=\"0\" >";
			echo "<td> <a href=\"\"> <img src=\"images/fissa.jpg\" width=\"200\" height=\"40\">  <a> </td>   ";
			echo "<td> Debut:</td> <td> ";
			echo "<form method=\"GET\" action=\"stat.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"date\"> " ;	
			echo "<input type=\"text\" name=\"date_jour\" size=\"10\" value=\"$date_jour\" class=\"calendrier\">";
			echo "</td> <td> ";
			
			echo "</td> <td> Fin: </td> <td>";			
			echo "<input type=\"text\" name=\"date_fin\" size=\"10\" value=\"$date_fin\" >";
			echo "</td> <td> ";
			echo "<input type=\"submit\" value=\"Valider les dates\" >  ";
			echo " </form> ";
			
			// ===================================================================== Bloc DATE
			echo "</td> <td> ";
			echo "<form method=\"GET\" action=\"stat.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"date\"> " ;	
			echo "<input type=\"hidden\" name=\"date_jour\" value=\"$date_jour\">";
			echo "<input type=\"hidden\" name=\"date_fin\" value=\"$date_fin\">";

			if (!$detail) 
				echo "<input type=\"hidden\" name=\"detail\" value=\"\"> " ;	
			echo "</td> <td> ";
			if (!$detail) 
				echo "<input type=\"submit\" value=\"Details par semaine\" >  ";
			else
				echo "<input type=\"submit\" value=\"Vue Synthètique\" >  ";
			echo " </form> ";		

			echo " </form> </table>";	
					echo "<a href=\"javascript:window.close();\">Fermer la fenêtre</a>"; 
					
					
		$crit_bene="  ( not (nom regexp '(B)')) and ( not (nom regexp '(S)')) and ( not (nom regexp '(A)')) and (nom<>'Synth') and (nom<>'Mail') and (pres_repas<>'Pour info')  ";
		$crit_AS=" (nom regexp '(B)' or nom regexp '(S)' ))";
		$crit_activite="( nom regexp '(A)')";
		$crit_mail="(nom='Mail')";
		
		echo "<table border=\"0\" >";

		$a=substr($date_fin,0,4);
		$m=substr($date_fin,5,2);
		$j=substr($date_fin,8,2);
		$s_fin= floor($a-2013 + ($m-1)*52/12 + $j/7);
		
		if ($a=="2014")
			$s_fin+=52;
		if ($a=="2015")
			$s_fin+=2*52;		
		if ($a=="2016")
			$s_fin+=3*52;			
		echelle_semaine($s_fin,$detail);
		
		for ($j=$s_org;$j<$s_fin;$j++)
			{
			$jd=$jour_d[$j];
			$jf=$jour_f[$j];
			$reponse = command("select * from effectif where date>='$jd' and date<='$jf' and  $crit_bene  order by date ");
			$visite[$j]=0;
			$femme[$j]=0;
			$repas[$j]=0;
			$refus[$j]=0;
			$suivi[$j]=0;
			$accompt[$j]=0;
			while ($donnees = fetch_command($reponse) )
				{
				$n=$donnees["date"];

					$n=$donnees["pres_repas"];
					switch ($n)
						{
						case "Visite+Repas": $repas[$j]++; 
						case "Visite": $visite[$j]++; break;
						case "Refusé": $refus[$j]++; break;
						case "Suivi": $suivi[$j]++; break;
						default : break;
						}
					$n=$donnees["nom"];
					if (strpos ($n , "(F)" )!=0)
						$femme[$j]++;
	
				}
			}

		for ($j=$s_org;$j<$s_fin;$j++)
			{
			$jd=$jour_d[$j];
			$jf=$jour_f[$j];
			$reponse = command("select distinct * from effectif where date>='$jd' and date<='$jf' and pres_repas!='Suivi' and not $crit_activite group by date ");
			$ouvert[$j]=0;
			
			while ($donnees = fetch_command($reponse) )
				$ouvert[$j]++;
			}
			
		echo "<tr> <td> Nbe jours Ouverts </td><td>  </td>";	
		$nb=0;		
		for ($j=$s_org;$j<$s_fin;$j++)
			$nb+=aff($ouvert[$j],$detail);
		echo "<td width=\"20\"> $nb</td>";
		$memo_ouvert=$nb;		
		
		echo "<tr> <td> Nbre Visites </td><td>  </td>";			
		$nb=0;		
		for ($j=$s_org;$j<$s_fin;$j++)
			$nb+=aff($visite[$j],$detail);
		echo "<td width=\"20\"> $nb</td>";
		$memo_visite=$nb;
		
		echo "<tr> <td>  </td><td> Nbre repas </td>";			
		$nb=0;		
		for ($j=$s_org;$j<$s_fin;$j++)
			$nb+=aff($repas[$j],$detail);
		echo "<td width=\"20\"> $nb</td>";
		
		echo "<tr> <td>  </td><td> Visites femme </td>";			
		$nb=0;		
		for ($j=$s_org;$j<$s_fin;$j++)
			$nb+=aff($femme[$j],$detail);
		echo "<td width=\"20\"> $nb</td>";

		echo "<tr> <td> Refusé </td><td> </td>";			
		$nb=0;		
		for ($j=$s_org;$j<$s_fin;$j++)
			$nb+=aff($refus[$j],$detail);	
		echo "<td width=\"20\"> $nb</td>";
		
		echo "<tr> <td> Suivi et accompagnement </td><td> </td>";			
		$nb=0;		
		for ($j=$s_org;$j<$s_fin;$j++)
			$nb+=aff($suivi[$j],$detail);	
		echo "<td width=\"20\"> $nb</td>";
		

		echo "<tr> <td> Visite /jour </td><td> </td>";			

		if ($detail)
			for ($j=$s_org;$j<$s_fin;$j++)
				if ($ouvert[$j]!=0)
						{
						$ratio = sprintf("%2.1f",$visite[$j]/ $ouvert[$j]);
						echo "<td width=\"20\">".$ratio."</td>";		
						}
					else
						aff(0,$detail);
		if ($memo_ouvert!=0)
			$ratio = sprintf("%2.1f",$memo_visite/ $memo_ouvert);
		else
			$ratio="-";
		echo "<td width=\"20\">".$ratio."</td>";
		
		
// ----------------------------------------------------- Nouveaux
		$req_sql="SELECT *,count(*) as TOTAL FROM effectif where date<='$date_fin' and date>='$date_jour'  and $crit_bene  group by nom order by TOTAL DESC";

		echo "<tr> <td> - - - - - - </td>";
		echo "<tr> <td>Nouveaux </td>";
		$r1 = command($req_sql); 
		while ($d1 = fetch_command($r1) )	
			{
			$nom=$d1["nom"];
			$reponse = command("select distinct * from effectif where date<'$date_jour' and date<>'0000-00-00'  and nom='$nom' group by nom");
			if (!fetch_command($reponse) )
				{
				echo "<tr> <td> + $nom </td>";
				}
			}
		echo "<tr> <td> - - - - - - </td>";

// -------------------------------------------------------------			
			
		$num=0;
		$numf=0;
		$r1 = command($req_sql); 
		while ($d1 = fetch_command($r1) )	
			{
			$nom=$d1["nom"];
			if (strpos ($nom , "(F)" )!=0)
				$numf++;
			$num++;
			}

		echo "<tr> <td> Nbres de personnes différentes: </td><td> $num </td>";
		echo "<tr> <td> dont femmes</td><td> $numf </td>";
		echo "<tr> <td> - - - - - - -</td><td> Par personne</td>";
		$num=0;
		$r1 = command($req_sql); 
		while ($d1 = fetch_command($r1) )	
			
			{
			$num++;
			$tot = $d1["TOTAL"];
			$nom=$d1["nom"];
		
			echo "<tr> <td> $num - <a href=\"fissa.php?action=suivi&nom=$nom&date_jour=$date_jour\" target=_blank> <b>$nom</b> </a></td><td> </td>";
			$nb=0;
			$nbr=0;
			for ($j=$s_org;$j<$s_fin;$j++)
				{
				$jd=$jour_d[$j];
				$jf=$jour_f[$j];
				$reponse = command("select distinct * from effectif where date>='$jd' and date<='$jf' and nom='$nom' and pres_repas<>'Suivi' and pres_repas<>'Pour info'  order by date ");
				$n=0;


				while ($donnees = fetch_command($reponse) )
					{
					$n++;
					if ($donnees["pres_repas"] =="Visite+Repas")
						$nbr++;
					}
				aff($n,$detail);
				$nb+=$n;
				}
			echo "<td width=\"20\"> $nb </td>";
			if ($nbr!=0)echo "<td width=\"20\">($nbr)</td>";
			}
			
		
		$req_sql="SELECT *,count(*) as TOTAL FROM effectif where date<='$date_fin' and date>='$date_jour' and (nom regexp '(B)' or nom regexp '(S)')  group by nom order by TOTAL DESC";
		$num=0;
		$r1 = command($req_sql); 
		while ($d1 = fetch_command($r1) )	
			{
			$num++;
			}

		echo "<tr> <td> Nbres de bénévoles différentes: </td><td> $num </td>";
		echo "<tr> <td> - - - - - - -</td><td> Par personne</td>";
		$num=0;
		$r1 = command($req_sql); 
		while ($d1 = fetch_command($r1) )	
			
			{
			$num++;
			$tot = $d1["TOTAL"];
			$nom=$d1["nom"];
			echo "<tr> <td> $num - $nom </td><td> </td>";
			$nb=0;
			for ($j=$s_org;$j<$s_fin;$j++)
				{
				$jd=$jour_d[$j];
				$jf=$jour_f[$j];
				$reponse = command("select distinct * from effectif where date>='$jd' and date<='$jf' and nom='$nom' and pres_repas<>'Suivi' and pres_repas<>'Pour info' and nom<>'Mail' and (nom regexp '(B)' or nom regexp '(S)') order by date ");
				$n=0;
				while ($donnees = fetch_command($reponse) )
					{
					$n++;
					}
				aff($n,$detail);
				$nb+=$n;
				}
			echo "<td width=\"20\"> $nb </td>";
			}
			
		echo " </table>";
		echo "<p><a href=\"javascript:window.close();\">Fermer la fenêtre</a>"; 	
	// ========================================== STAT MENSUELLE
	for ($j=1;$j<32; $j++)
		$dj[$j]=0;
		
		$r1 = command("SELECT * FROM effectif where date<='$date_fin' and date>='$date_jour' and nom<>'Synth' and pres_repas<>'Pour info' and pres_repas<>'Accueillant (B)' and pres_repas<>'Accueillant (S)' and nom<>'Mail' and pres_repas<>'pda'  "); 
		while ($d1 = fetch_command($r1) )	
			{
			
			$j = substr($d1["date"],8,2);
			if (substr($j,0,1)=="0")
				$j = substr($j,1,1);
			$dj[$j]++;
			}

	echo "<p><table>";
	echo "<tr> <td> Jour du mois </td>";	
		for ($j=1;$j<32; $j++)
			echo "<td> $j </td>";
	echo "<tr> <td> Fréquentation </td>";	
		for ($j=1;$j<32; $j++)
			echo "<td> $dj[$j]</td>";			
	echo " </table>";	

		// ========================================== STAT HEBDO
		$joursem = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
	for ($j=0;$j<10; $j++)
		$dj[$j]=0;
		
		$r1 = command("SELECT * FROM effectif where date<='$date_fin' and date>='$date_jour' and nom<>'Synth' and pres_repas<>'Pour info' and pres_repas<>'Accueillant (B)' and pres_repas<>'Accueillant (S)' and nom<>'Mail' and pres_repas<>'pda' "); 
		while ($d1 = fetch_command($r1) )	
			{
			

			// extraction des jour, mois, an de la date
			list($annee, $mois, $jour) = explode('-', $d1["date"]);
			// calcul du timestamp
			$timestamp = mktime (0, 0, 0, $mois, $jour, $annee);
		
			$dj[date("w",$timestamp)]++;
			}

			echo "<p><table>";
			echo "<tr> <td> Jour de la semaine </td>";	
				for ($j=0;$j<7; $j++)
					echo "<td>".$joursem[$j]  ."</td>";
			echo "<tr> <td> Fréquentation </td>";	
				for ($j=0;$j<7; $j++)
					echo "<td> $dj[$j]</td>";			
			echo " </table>";	
			
	
	fermeture_bdd ();

?>