<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
    <head>
	
	     <title>Statistiques </title>
		
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
    
		</head>
		<body>
<?php
include 'calendrier.php';
include 'general.php';

		
	function mise_en_forme_date_aaaammjj( $date_jour)
		{
		$d3= explode("/",$date_jour);  
		$a=$d3[2];
		$m=$d3[1];
		$j=$d3[0];
			
		return( "$a-$m-$j" );
		}

	function date_fr($d)
		{
		$d3= explode("-",$d);  
		$a=$d3[0];
		$m=$d3[1];
		$j=$d3[2];
		return("$j/$m/$a");
		}
		
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
			$lundi = date_fr(substr($lundi,2));
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

		
	function aff($v,$detail, $color="" )
		{
		if ($detail)
			if ($v==0)
				echo "<td width=\"20\"  $color> - </td>";
			else
				if (round($v )==$v)
					echo "<td width=\"20\"  $color>".$v."</td>";
				else
					echo "<td width=\"20\"  $color>".sprintf("%2.2f",$v)."</td>";
		return($v);
		}

	function num_semaine($date_jour)
		{
		$d3= explode("/",$date_jour);  
		$a=$d3[2];
		$m=$d3[1];
		$j=$d3[0];
		
		$s_org= floor($a-2013 + ($m-1)*52/12 + $j/7);
		$s_org+=($a-2013)*52;
		return ($s_org-1);
		}

		
		
		
	if ( !isset($_SESSION['pass']) ||($_SESSION['pass']==false) )
		// si pas de valeur pass en session on affiche le formulaire...
		Echo "Merci de vous reconnecter";
	else
		{
		$bdd=$_SESSION['support'];
		
			// ConnexiondD		
		include "connex_inc.php";	
		$format_date = "d/m/Y";
		
		$detail=isset ($_GET["detail"]);
		
		if (!isset ($_GET["date_jour"]))
			$date_jour=date($format_date,  mktime(0,0,0 , date("m")-1, date("d"), date ("Y")));
		else 
			$date_jour=$_GET["date_jour"];

		if (!isset ($_GET["date_fin"]))
			$date_fin=date($format_date,  mktime(0,0,0 , date("m"), date("d"), date ("Y")));
		else 
			$date_fin=$_GET["date_fin"];		

		$s_org= num_semaine($date_jour);

				// ===================================================================== Bloc DATE
				echo "<table border=\"0\" >";
				echo "<td> <a href=\"\"> <img src=\"images/fissa.jpg\" width=\"140\" height=\"100\"  >  <a> </td>   ";
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
				echo "</td> <td> <a href=\"javascript:window.close();\">Fermer la fenêtre</a></td> "; 
				echo " </form> </table>";	

						
						
			$crit_bene="  ( not (nom like '%(B)%')) and ( not (nom like '%(S)%')) and ( not (nom like '%(A)%')) and (nom<>'Synth') and (nom<>'Mail') and (pres_repas<>'Pour info')  ";
			$crit_AS=" (nom like '%(B)%' or nom like '%(S)%' )";
			$crit_activite="( nom like '%(A)%')";
			$crit_mail="(nom='Mail')";
			
			echo "<table border=\"0\" >";

			$s_fin=num_semaine($date_fin);
			echelle_semaine($s_fin,$detail);
			
			for ($j=$s_org;$j<$s_fin;$j++)
				{
				$jd=$jour_d[$j];
				$jf=$jour_f[$j];
				$reponse = command("select * from $bdd where date>='$jd' and date<='$jf' and  $crit_bene  order by date ");
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
				$reponse = command("select distinct * from $bdd where date>='$jd' and date<='$jf' and pres_repas!='Suivi' and not $crit_activite group by date ");
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
		
	
			$date_jour_fr=  $date_jour;
			$date_jour=mise_en_forme_date_aaaammjj( $date_jour);
			$date_fin=mise_en_forme_date_aaaammjj( $date_fin);	
			
			$req_sql_activite="SELECT *,count(*) as TOTAL FROM $bdd where date<='$date_fin' and date>='$date_jour' and $crit_activite and pres_repas<>'Pour info' group by nom order by TOTAL DESC";
			$num=0;
			$r1 = command($req_sql_activite); 
			while ($d1 = fetch_command($r1) )	
				{
				$num++;
				}
					
			echo "<tr> <td><hr></td><td> <hr> </td><td> <hr> </td>";	
			echo "<tr> <td> Nbres d'activités différentes: </td><td> $num </td>";
			echo "<table border=\"0\" >";
			$req_sql_activite="SELECT *,count(*) as TOTAL  FROM $bdd where date<='$date_fin' and date>='$date_jour' and $crit_activite and pres_repas<>'Pour info' group by nom order by TOTAL DESC";
			echo "<tr> <td> Activité </td><td> Nbre ateliers </td><td> Nbre Bénéficiaires </td><td> Frequentation moyenne </td>";	
			$ncolor=0;
			$num=0;
			$r1 = command($req_sql_activite); 
			while ($d1 = fetch_command($r1) )	
				{
				$num++;
				$nom=$d1["nom"];
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
				echo "<tr> <td bgcolor=\"$color\"> $num - <a href=\"fissa.php?action=suivi&nom=$nom&date_jour=$date_jour_fr\" target=_blank> <b>$nom  </b></td>";
				$nb = $d1["TOTAL"];
				echo "<td width=\"20\" bgcolor=\"$color\"> $nb </td>";
				
				if ($nb!=0)
					{
					$req_sql_activite="SELECT *,count(*) as TOTAL  FROM $bdd where date<='$date_fin' and date>='$date_jour' and ( activites like '%$nom%' ) and ( nom not like '%(B)%' ) and ( nom not like '%(S)%' )";
					$r2 = command($req_sql_activite); 
					while ($d2 = fetch_command($r2) )	
						{
						$freq = $d2["TOTAL"];
						echo "<td width=\"20\" bgcolor=\"$color\"> $freq </td>";
						echo "<td width=\"20\" bgcolor=\"$color\"> ".sprintf("%2.1f",$freq/$nb)." </td>";
						}
					}
				else
					echo "<td width=\"20\" bgcolor=\"$color\"> </td><td width=\"20\" bgcolor=\"$color\"> </td>";
						
				}
		if (!$detail)
			{
			echo "<tr> <td><hr></td><td> <hr> </td><td> <hr> </td>";

	// -------------------------------------------------------------			
			$req_sql="SELECT *,count(*) as TOTAL FROM $bdd where date<='$date_fin' and date>='$date_jour'  and $crit_bene  group by nom order by TOTAL DESC";
			$ncolor=0;

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

			echo "<tr> <td> Nbres d'usagers différents: </td><td> $num </td>";
			echo "<tr> <td> dont femmes</td><td> $numf </td>";
			$num=0;
			$r1 = command($req_sql); 
			while ($d1 = fetch_command($r1) )	
				{
				$num++;

				$nom=$d1["nom"];
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 

				echo "<tr> <td bgcolor=\"$color\"> $num - <a href=\"fissa.php?action=suivi&nom=$nom&date_jour=$date_jour_fr\" target=_blank> <b>$nom</b> </a></td><td bgcolor=\"$color\"> </td>";
				$nb = $d1["TOTAL"];
				echo "<td width=\"20\" bgcolor=\"$color\"> $nb </td>";
				// if ($nbr!=0) echo "<td width=\"20\" bgcolor=\"$color\">($nbr)</td>";
				}
				
					
			
			$req_sql_AS="SELECT *,count(*) as TOTAL FROM $bdd where date<='$date_fin' and date>='$date_jour' and $crit_AS and pres_repas<>'Pour info' group by nom order by TOTAL DESC";
			$num=0;
			$r1 = command($req_sql_AS); 
			while ($d1 = fetch_command($r1) )	
				{
				$num++;
				}
			echo "<tr> <td><hr></td><td> <hr> </td><td> <hr> </td>";
			echo "<tr> <td> Nbres d'accueillants différents: </td><td> $num </td>";
			$ncolor=0;
			$num=0;
			$r1 = command($req_sql_AS); 
			while ($d1 = fetch_command($r1) )	
				{
				$num++;
				$tot = $d1["TOTAL"];
				$nom=$d1["nom"];
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
				echo "<tr> <td bgcolor=\"$color\"> $num - <a href=\"fissa.php?action=suivi&nom=$nom&date_jour=$date_jour_fr\" target=_blank> <b>$nom  </b></td><td bgcolor=\"$color\"> </td>";
				$nb = $d1["TOTAL"];
				echo "<td width=\"20\" bgcolor=\"$color\"> $nb </td>";
				}

			echo "<tr> <td> <hr> </td><td> <hr> </td><td> <hr> </td>";	

			echo " </table>";
			
	// ----------------------------------------------------- Nouveaux
			echo "Nouveaux :";
			$nb_nouveaux=0;
			$r1 = command($req_sql); 
			while ($d1 = fetch_command($r1) )	
				{
				$nom=$d1["nom"];
				$nb = $d1["TOTAL"];
				$reponse = command("select distinct * from $bdd where date<'$date_jour' and date<>'0000-00-00'  and nom='".addslashes($nom)."'  group by nom");
				if (!fetch_command($reponse) )
					{
					echo "$nom ($nb), ";
					$nb_nouveaux++;
					}
				
				}
			echo "<p>Total : $nb_nouveaux nouveaux";
			echo "<p><a href=\"javascript:window.close();\">Fermer la fenêtre</a>"; 	
			}


			echo " </table>";
		fermeture_bdd ();
		}
?>