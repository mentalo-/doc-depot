<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<?php include 'header.php';	  ?>

    <head>
	
	     <title>Statistiques </title>
		
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
    
		</head>
		<body>
<?php
include 'calendrier.php';
include 'general.php';
include 'suivi_liste.php';

		

	function date_fr($d)
		{
		$d3= explode("-",$d);  
		$a=$d3[0];
		$m=$d3[1];
		$j=$d3[2];
		return("$j/$m/$a");
		}
		
	function aff($v, $color="" )
		{
		global $detail,$color;
		
		if ($detail)
			if ($v==0)
				echo "<td width=\"20\" ALIGN=\"RIGHT\" bgcolor=\"$color\"> - </td>";
			else
				if (round($v )==$v)
					echo "<td width=\"20\" ALIGN=\"RIGHT\" bgcolor=\"$color\"> ".$v."</td>";
				else
					echo "<td width=\"20\" ALIGN=\"RIGHT\" bgcolor=\"$color\"> ".sprintf("%2.1f",$v)."</td>";
		return($v);
		}

		function  kpi_sur_periode($jd, $jf )
			{
			global 	$bdd, $crit_bene,  $crit_activite , $visite, $femme, $repas, $refus, $suivi, $ouvert;
			
			$reponse = command("select * from $bdd where date>='$jd' and date<='$jf' and  ( $crit_bene or (pres_repas='Suivi') ) order by date ");
			$visite=0;
			$femme=0;
			$repas=0;
			$refus=0;
			$suivi=0;
			while ($donnees = fetch_command($reponse) )
				{
				switch ($donnees["pres_repas"])
					{
					case "Visite+Repas": $repas++; 
					case "Visite": $visite++; break;
					case "Refusé": $refus++; break;
					case "Suivi": $suivi++; break;
					default : break;
					}
				if (strpos ($donnees["nom"] , "(F)" )!=0)
					$femme++;
				}
		
			$reponse = command("select distinct * from $bdd where date>='$jd' and date<='$jf' and pres_repas!='Suivi' and not $crit_activite group by date ");
			$ouvert=nbre_enreg ($reponse);
			}

	$format_date = "d/m/Y";		
	$aujourdhui=date($format_date,  mktime(0,0,0 , date("m"), date("d"), date ("Y")));		
		
	if ( !isset($_SESSION['pass']) ||($_SESSION['pass']==false) )
		// si pas de valeur pass en session on affiche le formulaire...
		Echo "Merci de vous reconnecter";
	else
		{
		$bdd=$_SESSION['support'];
		
			// ConnexiondD		
		include "connex_inc.php";	

		$token=variable("token");	
		if ($token!="")	
			$action=verifi_token($token,variable("action"));
		else
			$action=variable("action");		

		$detail=isset ($_POST["detail"]);
		
		if (!isset ($_POST["date_jour"]))
			$date_jour=date($format_date,  mktime(0,0,0 , date("m")-1, date("d"), date ("Y")));
		else 
			$date_jour=$_POST["date_jour"];

		if (!isset ($_POST["date_fin"]))
			$date_fin=date($format_date,  mktime(0,0,0 , date("m"), date("d"), date ("Y")));
		else 
			$date_fin=$_POST["date_fin"];		

				// ===================================================================== Bloc DATE
				echo "<table border=\"0\" >";
				echo "<td> <a href=\"\"> <img src=\"images/fissa.jpg\" width=\"140\" height=\"100\"  >  <a> </td>   ";
				echo "<td> Debut:</td> <td> ";
				formulaire("date") ;	
				echo "<input type=\"text\" name=\"date_jour\" size=\"10\" value=\"$date_jour\" class=\"calendrier\">";
				echo "</td> <td> ";
				
				echo "</td> <td> Fin: </td> <td>";			
				echo "<input type=\"text\" name=\"date_fin\" size=\"10\" value=\"$date_fin\" >";
				echo "</td> <td> ";
				echo "<input type=\"submit\" value=\"Valider les dates\" >  ";
				echo " </form> ";
				
				// ===================================================================== Bloc DATE
				echo "</td> <td> ";
				formulaire("date") ;	
				echo param("date_jour","$date_jour"); 
				echo param("date_fin","$date_fin"); 
				if (!$detail) 
					echo param("detail","") ;	
				echo "</td> <td> ";
				if (!$detail) 
					echo "<input type=\"submit\" value=\"Historique (par mois)\" >  ";
				echo " </form> ";		
				echo "</td> <td> <a href=\"javascript:window.close();\">Fermer la fenêtre</a></td> "; 
				echo " </form> </table>";	
		
			$crit_presence=" (pres_repas='présence') and ( not (nom like '%(B)%')) and ( not (nom like '%(S)%')) and ( not (nom like '%(A)%')) and ( not (nom like '%(M)%')) and (nom<>'Synth') and (nom<>'Mail')  ";
			$crit_bene="  ( not (nom like '%(B)%')) and ( not (nom like '%(S)%')) and ( not (nom like '%(A)%')) and ( not (nom like '%(M)%')) and (nom<>'Synth') and (nom<>'Mail') and (pres_repas<>'presence')  and (pres_repas<>'__upload') and (nom<>'Mail') and (pres_repas<>'Pour info') and (pres_repas<>'Suivi') and (pres_repas<>'reponse')and (pres_repas<>'partenaire')  ";
			$crit_AS=" (nom like '%(B)%' or nom like '%(S)%' )";
			$crit_activite="( nom like '%(A)%')";
			$crit_materiel="( nom like '%(M)%')";
			$crit_mail="(nom='Mail')";
			
			$jd=mise_en_forme_date_aaaammjj( $date_jour);
			$jf=mise_en_forme_date_aaaammjj( $date_fin);	
			
		if (!$detail)
			{
			echo "<table border=\"0\" >";	
	
			kpi_sur_periode($jd, $jf );

			echo "<tr> <td><hr></td><td> <hr> </td><td> <hr> </td>";	
			echo "<tr> <td> Nbre jours Ouverts </td>";	
			echo "<td ALIGN=\"RIGHT\" width=\"20\"> $ouvert</td>";
			
			echo "<tr> <td> Nbre Visites </td>";			
			echo "<td ALIGN=\"RIGHT\" width=\"20\"> $visite</td>";
			
			echo "<tr> <td> - dont femmes </td>";			
			echo "<td ALIGN=\"RIGHT\" width=\"20\"> $femme</td>";
			if ($femme!=0)
				echo "<td ALIGN=\"RIGHT\" > ".sprintf("%2.1f",$femme/$visite*100)."% </td>";			

			echo "<tr> <td> Visite /jour </td>";			
			if ($ouvert!=0)
				$ratio = sprintf("%2.1f",$visite/ $ouvert);
			else
				$ratio="-";
			echo "<td ALIGN=\"RIGHT\" width=\"20\">".$ratio."</td>";
			
			echo "<tr> <td><hr></td><td> <hr> </td><td> <hr> </td>";
			
			echo "<tr> <td> Nbre repas </td>";			
			echo "<td ALIGN=\"RIGHT\" width=\"20\"> $repas</td>";
			if ( ($visite!=0) &&  ($repas!=0) )
				echo "<td ALIGN=\"RIGHT\" > ".sprintf("%2.1f",$repas/$visite*100)."% </td>";

			echo "<tr> <td> Refusé </td>";			

			echo "<td ALIGN=\"RIGHT\" width=\"20\"> $refus</td>";
			if ( ($visite!=0) && ($refus!=0) )
				echo "<td ALIGN=\"RIGHT\" > ".sprintf("%2.1f",$refus/$visite*100)."% </td>";
			
	
			// ============================================================================ACTIVITES 
			
			$req_sql_activite="SELECT *,count(*) as TOTAL FROM $bdd where date<='$jf' and date>='$jd' and $crit_activite and pres_repas<>'Pour info' group by nom order by TOTAL DESC";
			
			$r1 = command($req_sql_activite); 
			$num=nbre_enreg ($r1);
				
			if ($num!=0)
				{
				echo "<tr> <td><hr></td><td> <hr> </td><td> <hr> </td><td> <hr> </td><td> <hr> </td>";	
				echo "<tr> <td> Nbres d'activités : </td><td> $num </td>";
				echo "<tr> <td> Activité </td><td> Nbre ateliers </td><td> Nbre Bénéficiaires </td><td> Frequentation moyenne </td>";	
				$ncolor=0;
				$num=0;
				$r1 = command($req_sql_activite); 
				while ($d1 = fetch_command($r1) )	
					{
					$num++;
					$nom=$d1["nom"];
					if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 

					echo "<tr> <td bgcolor=\"$color\"> $num - <a href=\"suivi.php?action=suivi&nom=$nom&date_jour=$aujourdhui\" target=_blank> <b>$nom  </b></td>";
					
					$req_sql_activite="SELECT *,count(*) as TOTAL  FROM $bdd where nom='$nom' and date<='$jf' and date>='$jd' and $crit_activite and pres_repas<>'Pour info' group by nom order by TOTAL DESC";
					$r2 = command($req_sql_activite); 
					$d2 = fetch_command($r2) ; 
					$nb = $d2["TOTAL"];
					echo "<td width=\"20\" ALIGN=\"RIGHT\" bgcolor=\"$color\"> $nb </td>";
					
					if ($nb!=0)
						{
						$req_sql_activite="SELECT *,count(*) as TOTAL  FROM $bdd where date<='$jf' and date>='$jd' and ( activites like '%".addslashes($nom)."%' ) and ( nom not like '%(B)%' ) and ( nom not like '%(S)%' )";
						$r2 = command($req_sql_activite); 
						$d2 = fetch_command($r2) ;	
						$freq = $d2["TOTAL"];
						echo "<td width=\"20\" ALIGN=\"RIGHT\" bgcolor=\"$color\"> $freq </td>";
						echo "<td width=\"20\" ALIGN=\"RIGHT\" bgcolor=\"$color\" > ".sprintf("%2.1f",$freq/$nb)."</td>";
						
						}
					else
						echo "<td width=\"20\" bgcolor=\"$color\"> </td><td width=\"20\" bgcolor=\"$color\"> </td>";
					}
				echo "<tr> <td><hr></td><td> <hr> </td><td> <hr> </td><td> <hr> </td><td> <hr> </td>";	
				}
				

	
		
			// ============================================================================ Matériel 
			
			$req_sql_materiel="SELECT *,count(*) as TOTAL FROM $bdd where date<='$jf' and date>='$jd' and $crit_materiel and pres_repas<>'Pour info' group by nom order by TOTAL DESC";
			
			$r1 = command($req_sql_materiel); 
			$num=nbre_enreg ($r1);
				
			if ($num!=0)
				{

				echo "<tr> <td> Matériel </td><td> Variation </td>";	
				$ncolor=0;
				$num=0;
				$r1 = command($req_sql_materiel); 
				while ($d1 = fetch_command($r1) )	
					{
					$num++;
					$nom=$d1["nom"];
					if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 

					echo "<tr> <td bgcolor=\"$color\"> $num - <a href=\"suivi.php?action=suivi&nom=$nom&date_jour=$aujourdhui\" target=_blank> <b>$nom  </b></td>";
					
					$req_sql_materiel="SELECT *  FROM $bdd where nom='$nom' and date<='$jf' and date>='$jd' and pres_repas<>'Pour info' group by nom ";
					$r2 = command($req_sql_materiel); 
					$delta=0;
					while ($d2 = fetch_command($r2))
						$delta+= $d2["qte"];
						
					echo "<td width=\"20\" ALIGN=\"RIGHT\" bgcolor=\"$color\"> $delta </td>";
					}
			echo "<tr> <td><hr></td><td> <hr> </td><td> <hr> </td>";
				}


	
		
			// ============================================================================ Présence   (activites>='$an_debut-01-01' or qte<='$an_fin-12-31')
			
			$req_sql_presence="SELECT *,count(*) as TOTAL FROM $bdd where (activites>='$jd' or qte<='$jf') and $crit_presence  group by nom order by TOTAL DESC";
			
			$r1 = command($req_sql_presence); 
			$num=nbre_enreg ($r1);
				
			if ($num!=0)
				{

				echo "<tr> <td> Hébergement   </td><td> Nuitées </td>";	
				$ncolor=0;
				$num=0;

				$r1 = command($req_sql_presence); 
				while ($d1 = fetch_command($r1) )	
					{
					$num++;
					$nom=$d1["nom"];
					if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 

					echo "<tr> <td bgcolor=\"$color\"> $num - <a href=\"suivi.php?action=suivi&nom=$nom&date_jour=$aujourdhui\" target=_blank> <b>$nom  </b></td>";
					
					$req_sql_presence2="SELECT *  FROM $bdd where nom='$nom' and  activites<='$jf' and qte>='$jd' and $crit_presence ";
					$r2 = command($req_sql_presence2); 
					$delta=0;
					while ($d2 = fetch_command($r2))
						{
						$date_deb=$d2["activites"];
						$date_fin=$d2["qte"];
						if ($date_deb <$jd) $date_deb =$jd;								
						if ($date_fin >$jf) $date_fin =$jf;
						$date_deb= str_replace("-","/",$date_deb);						
						$date_fin= str_replace("-","/",$date_fin);

						$delta+= (strtotime($date_fin) - strtotime($date_deb))/3600/24;
						}
						
					echo "<td width=\"20\" ALIGN=\"RIGHT\" bgcolor=\"$color\"> $delta </td>";
					}
				echo "<tr> <td><hr></td><td> <hr> </td><td> <hr> </td>";
				}
								

	// -------------------------------------------------------------	Usagers

			$req_sql="SELECT *,count(*) as TOTAL FROM $bdd where date<='$jf' and date>='$jd'  and $crit_bene  group by nom order by TOTAL DESC";
			$ncolor=0;
			$liste_nom="";
			$num=0;
			$numf=0;
			$r1 = command($req_sql); 
			while ($d1 = fetch_command($r1) )	
				{
				$nom=$d1["nom"];
				$liste_nom.=" nom='".addslashes($nom)."' or ";
				if (strpos ($nom , "(F)" )!=0)
					$numf++;
				$num++;
				}
			$num_usager=$num;
			
			echo "<tr> <td> Nbres d'usagers : </td><td ALIGN=\"RIGHT\"> $num_usager </td>";
			echo "<tr> <td> dont femmes</td><td ALIGN=\"RIGHT\"> $numf </td><td ALIGN=\"RIGHT\" > ".sprintf("%2.1f",$numf/$num_usager*100)."% </td>";
			$num=0;
			$pourcentage_cumule=0;
			$r1 = command($req_sql); 
			while ($d1 = fetch_command($r1) )	
				{
				$num++;

				$nom=$d1["nom"];
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 

				echo "<tr> <td bgcolor=\"$color\"> $num - <a href=\"suivi.php?action=suivi&nom=$nom&date_jour=$aujourdhui\" target=_blank> <b>$nom</b> </a></td>";
				$nb = $d1["TOTAL"];
				$pourcentage_cumule+=$nb/$visite*100;
				echo "<td width=\"20\"  ALIGN=\"RIGHT\"  bgcolor=\"$color\"> $nb </td>";
				}
					
	// -------------------------------------------------------------	Salariés et Bénévoles
			
			$req_sql_AS="SELECT *,count(*) as TOTAL FROM $bdd where date<='$jf' and date>='$jd' and $crit_AS and pres_repas<>'Pour info' group by nom order by TOTAL DESC";
			$num = nbre_enreg (command($req_sql_AS)); 
			if ($num!=0)
				{
				echo "<tr> <td><hr></td><td> <hr> </td>";
				echo "<tr> <td> Nbres d'accueillants : </td><td> $num </td>";
				$ncolor=0;
				$num=0;
				$r1 = command($req_sql_AS); 
				while ($d1 = fetch_command($r1) )	
					{
					$num++;
					$tot = $d1["TOTAL"];
					$nom=$d1["nom"];
					if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
					echo "<tr> <td bgcolor=\"$color\"> $num - <a href=\"suivi.php?action=suivi&nom=$nom&date_jour=$aujourdhui\" target=_blank> <b>$nom  </b></td>";
					$nb = $d1["TOTAL"];
					echo "<td width=\"20\"  ALIGN=\"RIGHT\" bgcolor=\"$color\"> $nb </td>";
					}
				echo "<tr> <td> <hr> </td><td> <hr> </td>";	
				}

			echo " </table>";
		
	// ----------------------------------------------------- Nouveaux
			echo "<hr><p>";
			echo "<table border=\"0\" >";				
			echo "<tr> <td bgcolor=\"#3f7f00\"><font color=\"white\">Nouveaux : </td></table>";
			
			$nb_nouveaux=0;
			$r1 = command($req_sql); 
			while ($d1 = fetch_command($r1) )	
				{
				$nom=$d1["nom"];

				$nb = $d1["TOTAL"];
				$reponse = command("select distinct * from $bdd where date<'$jd' and date>'2000-01-01'  and nom='".addslashes($nom)."'  group by nom");
				if (!fetch_command($reponse) )
					{
					echo "<a href=\"suivi.php?action=suivi&nom=$nom&date_jour=$aujourdhui\" target=_blank> $nom </a>($nb), ";
					$nb_nouveaux++;
					}
				}
			echo "<p>Total : $nb_nouveaux nouveaux";

	// ----------------------------------------------------- Retour
			echo "<hr><p>";
			echo "<table border=\"0\" >";				
			echo "<tr> <td bgcolor=\"#3f7f00\"><font color=\"white\">Retours (pas de visites depuis 6 mois) : </td></table>";
			
			$d3= explode("-",$jd);  
			$a=$d3[0];
			$m=$d3[1];
			$j=$d3[2];
			$horizon=date('Y-m-d',  mktime(0,0,0 , $m-6, $j, $a ));
			$nb_nouveaux=0;
			$r1 = command($req_sql); 
			while ($d1 = fetch_command($r1) )	
				{
				$nom=$d1["nom"];
				$reponse = command("select distinct * from $bdd where date<'$jd' and date>'$horizon'  and nom='".addslashes($nom)."' ");
				if (!fetch_command($reponse) )
					{
					$reponse = command("select distinct * from $bdd where date<='$horizon' and date>'$2000-01-01'  and nom='".addslashes($nom)."' ");
					if (fetch_command($reponse) )					
						{
						echo "<a href=\"suivi.php?action=suivi&nom=$nom&date_jour=$aujourdhui\" target=_blank> $nom </a>, ";
						$nb_nouveaux++;
						}
					}
				}
			echo "<p>Total : $nb_nouveaux retours";
	
// ----------------------------------------------------- pays
			echo "<hr><p> ";
			
			echo "<table border=\"0\" >";
			$ncolor=0;
			$pourcentage_cumule=0;
			$cumul=0;
			echo "<tr> <td bgcolor=\"#3f7f00\"><font color=\"white\"> Pays d'origine </td><td bgcolor=\"#3f7f00\"> <font color=\"white\"> Nombre </td><td bgcolor=\"#3f7f00\"> <font color=\"white\"> % </td>";
			$r1 = command("select * ,count(*) as TOTAL from $bdd where ( $liste_nom nom='' ) and date='1111-11-11' and pres_repas='nationalite' group by commentaire order by TOTAL DESC");
			while ($d1 = fetch_command($r1) )	
				{
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
				$pays=$d1["commentaire"];
				$tot = $d1["TOTAL"];
				$pourcentage_cumule+=$tot/$num_usager*100;
				$cumul+=$tot;
				echo "<tr> <td bgcolor=\"$color\"> $pays </td><td bgcolor=\"$color\"> $tot</td><td bgcolor=\"$color\"> ".sprintf("%2.1f",$tot/$num_usager*100)."% </td>";
			
				}
			$reste=$num_usager-$cumul;
			echo "<tr> <td bgcolor=\"$color\"> Inconnu </td><td bgcolor=\"$color\" > $reste </td><td bgcolor=\"$color\">".sprintf("%2.1f",100-$pourcentage_cumule)."% </td>";

			echo " </table>";
// ----------------------------------------------------- Année de naissance 
			echo "<hr><p>";
			
			$année_courante=date( "Y");
			$ncolor=0;
			$pourcentage_cumule=0;
			$cumul=0;
			for ($an=1900; $an<$année_courante; $an++)
				$nb_age[$an]=0;
				
			echo "<table border=\"0\" >";				
			echo "<tr> <td bgcolor=\"#3f7f00\"><font color=\"white\">Année de naissance </td><td bgcolor=\"#3f7f00\"><font color=\"white\">  Age </td><td bgcolor=\"#3f7f00\"> <font color=\"white\"> Nombre </td><td bgcolor=\"#3f7f00\"> <font color=\"white\"> % </td>";
			$r1 = command("select *  from $bdd where ( $liste_nom nom='' ) and date='1111-11-11' and pres_repas='Age' group by nom ");
			while ($d1 = fetch_command($r1) )	
				{
				$date=$d1["commentaire"];
				$d3= explode("/",$date);  
				if (isset($d3[2]) )
					$an=$d3[2];	
				else 
					$an=$date;
					
				$nb_age[$an]++;
				}
				
			for ($an=1900; $an<$année_courante; $an++)
				if ($nb_age[$an]!=0)
					{
					if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
					$pourcentage_cumule+=$nb_age[$an]/$num_usager*100;
					$cumul+=$nb_age[$an];

					echo "<tr> <td bgcolor=\"$color\"> $an </td><td bgcolor=\"$color\"> ~".($année_courante-$an)."</td><td ALIGN=\"RIGHT\" bgcolor=\"$color\"> ".$nb_age[$an]."</td><td ALIGN=\"RIGHT\" bgcolor=\"$color\"> ".sprintf("%2.1f",$nb_age[$an]/$num_usager*100)."% </td>";
					}
			if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
			$reste= $num_usager-$cumul;
			echo "<tr> <td bgcolor=\"$color\"> Inconnu </td><td bgcolor=\"$color\"> </td><td ALIGN=\"RIGHT\" bgcolor=\"$color\"> $reste </td><td ALIGN=\"RIGHT\" bgcolor=\"$color\"> ".sprintf("%2.1f",100-$pourcentage_cumule)."% </td>";
			echo " </table>";			
			
			echo "<p><a href=\"javascript:window.close();\">Fermer la fenêtre</a>"; 	
			}
		else
		// ================================================================================== HISTORIQUE
			{
			$deb=-1;
			$i=0;
			
			$d3= explode("-",$jd);  
			$an_debut=$d3[0];
			$m_debut=$d3[1];
			
			$d3= explode("-",$jf);  
			$an_fin=$d3[0];
			$m_fin=$d3[1];
			
			for ($an=$an_debut; $an<=$an_fin; $an++)
				for ($mois=1; $mois<=12; $mois++)
					{
					if ($an==$an_debut) 
						$mois=max($mois,$m_debut);
						
					$periode[$i]=sprintf("%02d/%02d",$mois,$an);

					$jd="$an-$mois-01";
					$jf="$an-$mois-31";
					kpi_sur_periode($jd, $jf );

					$tab_ouvert[$i]=$ouvert;
					if (($deb==-1) && ($ouvert>0))
						$deb=$i;
						
					$tab_visite[$i]=$visite;
					$tab_femme[$i]=$femme;
					$tab_repas[$i]=$repas;
					$tab_refus[$i]=$refus;
					$tab_suivi[$i]=$suivi;	
					$i++;
			
					if ($an==$an_fin) 
						if ($mois==$m_fin) 
							break;
							
					if (date("m/Y")==$periode[$i-1])  break;
					}			
			$imax=$i;
			
			echo "<table border=\"0\" >";	
			$ncolor=0;
			
			echo "<tr> <td bgcolor=\"#3f7f00\">  </td>";				
			for ($i=$deb; $i<$imax; $i++)
					echo "<td bgcolor=\"#3f7f00\"> <font color=\"white\">".$periode[$i]."</td>";	
			echo "<td bgcolor=\"#3f7f00\"> <font color=\"white\"> Total </td>";	
			
			if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
			echo "<tr> <td bgcolor=\"$color\"> Nbre jours Ouverts </td>";				
			for ($i=$deb; $i<$imax; $i++)
					aff($tab_ouvert[$i]);	
			for ($tot=0 , $i=$deb; $i<$imax; $i++) 
				$tot+=$tab_ouvert[$i];				
			aff($tot);						
			$memo_ouvert=$tot;
			
			if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 					
			echo "<tr> <td bgcolor=\"$color\"> Nbre visites </td>";				
			for ($i=$deb; $i<$imax; $i++)
					aff($tab_visite[$i]);
			for ($tot=0 , $i=$deb; $i<$imax; $i++) 
				$tot+=$tab_visite[$i];				
			aff($tot);						
			$memo_visite=$tot;
			
			if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
			echo "<tr> <td bgcolor=\"$color\"> - dont femme </td>";				
			for ($i=$deb; $i<$imax; $i++)
					aff ($tab_femme[$i]);	
			for ($tot=0 , $i=$deb; $i<$imax; $i++) 
				$tot+=$tab_femme[$i];				
			aff($tot);	
			
			if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
			echo "<tr> <td bgcolor=\"$color\"> Visites/jour </td>";				
			for ($i=$deb; $i<$imax; $i++)
				if ($tab_ouvert[$i]==0)
					echo "<td bgcolor=\"$color\"> - </td>";				
				else
					aff ($tab_visite[$i]/ $tab_ouvert[$i]);								
			aff ($memo_visite/ $memo_ouvert);	
	
			if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
			echo "<tr> <td bgcolor=\"$color\"> Repas </td>";				
			for ($i=$deb; $i<$imax; $i++)
					aff($tab_repas[$i]);	
			for ($tot=0 , $i=$deb; $i<$imax; $i++) 
				$tot+=$tab_repas[$i];				
			aff($tot);	
			
			if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
			echo "<tr> <td bgcolor=\"$color\"> Refus </td>";				
			for ($i=$deb; $i<$imax; $i++)
					aff($tab_refus[$i]);	
			for ($tot=0 , $i=$deb; $i<$imax; $i++) 
				$tot+=$tab_refus[$i];				
			aff($tot);	
			
			if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
			echo "<tr> <td bgcolor=\"$color\"> Suivi et accompagnement </td>";				
			for ($i=$deb; $i<$imax; $i++)
					aff ($tab_suivi[$i]);						
			for ($tot=0 , $i=$deb; $i<$imax; $i++) 
				$tot+=$tab_suivi[$i];				
			aff($tot);	
			
			
			
				// ===================================================== Hébergement  activites==j_fin' ,  qte=j_debut
			
			echo "<tr> <td bgcolor=\"#3f7f00\">  </td>";				
			for ($i=$deb; $i<$imax; $i++)
				echo "<td bgcolor=\"#3f7f00\"> <font color=\"white\">".$periode[$i]."</td>";	
			echo "<td bgcolor=\"#3f7f00\"> <font color=\"white\"> Total </td>";				

			if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
			echo "<tr> <td bgcolor=\"$color\">  <b> Hébergement (nuitées) </b></td>";
			for ($i=$deb; $i<=$imax; $i++)
				echo "<td bgcolor=\"$color\"> </td>";					
				
			$req_sql_presence="SELECT *,count(*) as TOTAL FROM $bdd where (activites>='$an_debut-01-01' or qte<='$an_fin-12-31') and $crit_presence and pres_repas<>'Pour info' group by nom order by TOTAL DESC";
			$r1 = command($req_sql_presence); 
			while ($d1 = fetch_command($r1) )	
				{
				$nom=$d1["nom"];
				
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
				echo "<tr> <td bgcolor=\"$color\"> <a href=\"suivi.php?action=suivi&nom=$nom&date_jour=$aujourdhui\" target=_blank> <b>$nom  </b></td>";
	
				for ($i=$deb; $i<$imax; $i++)
					$tab_delta[$i]=0;						
				$i=0;
				for ($an=$an_debut; $an<=$an_fin; $an++)
					for ($mois=1; $mois<=12; $mois++)
						{
						if ($an==$an_debut) 
							$mois=max($mois,$m_debut);
							
						if (($i>=$deb) && ($i<$imax) )
							{
							$jd=sprintf("%04d-%02d-01",$an,$mois) ;
							$jf=sprintf("%04d-%02d-31",$an,$mois);
							
							$req_sql_presence2="SELECT *  FROM $bdd where nom='$nom' and  activites<='$jf' and qte>='$jd' and $crit_presence ";
							$r2 = command($req_sql_presence2); 
							$delta=0;
							while ($d2 = fetch_command($r2))
								{
								$date_deb=$d2["activites"];
								$date_fin=$d2["qte"];
								
								if ($date_deb <$jd) $date_deb =$jd;								
								if ($date_fin >$jf) $date_fin =$jf;
								
								$date_deb= str_replace("-","/",$date_deb);						
								$date_fin= str_replace("-","/",$date_fin);
								
								$delta+= (strtotime($date_fin) - strtotime($date_deb))/3600/24;
								$tab_delta[$i] =$delta;
								}
							}
						$i++;
						}
						
				for ($i=$deb; $i<$imax; $i++)
					aff($tab_delta[$i]);		
				for ($tot=0 , $i=$deb; $i<$imax; $i++) 
					$tot+=$tab_delta[$i];				
				aff($tot);	
				}
		
			
			
			
			// ===================================================== Activités
			
			$req_sql_activite="SELECT *,count(*) as TOTAL FROM $bdd where date>='$an_debut-01-01' and date<='$an_fin-12-31' and $crit_activite and pres_repas<>'Pour info' group by nom order by TOTAL DESC";
			$r1 = command($req_sql_activite); 
			while ($d1 = fetch_command($r1) )	
				{
				$nom=$d1["nom"];

				echo "<tr> <td bgcolor=\"#3f7f00\">  </td>";				
				for ($i=$deb; $i<$imax; $i++)
						echo "<td bgcolor=\"#3f7f00\"> <font color=\"white\">".$periode[$i]."</td>";	
				echo "<td bgcolor=\"#3f7f00\"> <font color=\"white\"> Total </td>";	
					
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
				echo "<tr> <td bgcolor=\"$color\"> <a href=\"suivi.php?action=suivi&nom=$nom&date_jour=$aujourdhui\" target=_blank> <b>$nom  </b></td>";
				for ($i=$deb; $i<=$imax; $i++)
					echo "<td bgcolor=\"$color\"> </td>";		
					
				$i=0;
				for ($an=$an_debut; $an<=$an_fin; $an++)
					for ($mois=1; $mois<=12; $mois++)
						{
						if ($an==$an_debut) 
							$mois=max($mois,$m_debut);
							
						if (($i>=$deb) && ($i<$imax) )
							{
							$jd="$an-$mois-01";
							$jf="$an-$mois-31";
							
							$req_sql_activite="SELECT * FROM $bdd where date<='$jf' and date>='$jd' and ( activites like '%".addslashes($nom)."%' ) and ( nom not like '%(B)%' ) and ( nom not like '%(S)%' )";
							$freq[$i] = nbre_enreg ( command($req_sql_activite) ); 

							$req_sql_activite="SELECT *,count(*) as TOTAL  FROM $bdd where nom='$nom' and date<='$jf' and date>='$jd' and $crit_activite and pres_repas<>'Pour info' group by nom order by TOTAL DESC";
							$r2 = command($req_sql_activite); 
							
							/*
							$d2 = fetch_command($r2) ;
							$nb[$i] = $d2["TOTAL"];			
							*/
							$nb[$i] = 0;		
							// variante en tenant compte de qte 
							while ($d2 = fetch_command($r2))
								{
								$q=$d2["qte"];
								if ($q=="") $q=1;
								$nb[$i]+=$q;
								}
							
							}
						$i++;
						}
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
				echo "<tr> <td bgcolor=\"$color\"> Nbre d'ateliers </td>";
				for ($i=$deb; $i<$imax; $i++)
					aff($nb[$i]);		
				for ($tot=0 , $i=$deb; $i<$imax; $i++) 
					$tot+=$nb[$i];				
				aff($tot);	
				$memo_nb=$tot;
				
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
				echo "<tr> <td bgcolor=\"$color\"> Participants </td>";
				for ($i=$deb; $i<$imax; $i++)
					aff($freq[$i]);			
				for ($tot=0 , $i=$deb; $i<$imax; $i++) 
					$tot+=$freq[$i];				
				aff($tot);
				$memo_freq=$tot;
				
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
				echo "<tr> <td bgcolor=\"$color\"> Participants/atelier</td>";
				for ($i=$deb; $i<$imax; $i++)
					if ($nb[$i]==0)
						echo "<td ALIGN=\"RIGHT\" bgcolor=\"$color\"> - </td>";				
					else
						aff($freq[$i]/$nb[$i]);	
				if ($memo_nb>0)
					aff($memo_freq/$memo_nb);
				else
					aff(0);

					
				}
	

			// ===================================================== Matériel
			
			$req_sql_activite="SELECT *,count(*) as TOTAL FROM $bdd where date>='$an_debut-01-01' and date<='$an_fin-12-31' and $crit_materiel and pres_repas<>'Pour info' group by nom order by TOTAL DESC";
			$r1 = command($req_sql_activite); 
			while ($d1 = fetch_command($r1) )	
				{
				$nom=$d1["nom"];

				echo "<tr> <td bgcolor=\"#3f7f00\">  </td>";				
				for ($i=$deb; $i<$imax; $i++)
						echo "<td bgcolor=\"#3f7f00\"> <font color=\"white\">".$periode[$i]."</td>";	
				echo "<td bgcolor=\"#3f7f00\"> <font color=\"white\"> Total </td>";	
					
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
				echo "<tr> <td bgcolor=\"$color\"> <a href=\"suivi.php?action=suivi&nom=$nom&date_jour=$aujourdhui\" target=_blank> <b>$nom  </b></td>";
				for ($i=$deb; $i<=$imax; $i++)
					echo "<td bgcolor=\"$color\"> </td>";		
				for ($i=$deb; $i<$imax; $i++)
					$tab_delta[$i]=0;	
					
				$i=0;
				for ($an=$an_debut; $an<=$an_fin; $an++)
					for ($mois=1; $mois<=12; $mois++)
						{
						if ($an==$an_debut) 
							$mois=max($mois,$m_debut);
							
						if (($i>=$deb) && ($i<$imax) )
							{
							$jd="$an-$mois-01";
							$jf="$an-$mois-31";
							
							$req_sql_materiel="SELECT * FROM $bdd where nom='$nom' and date<='$jf' and date>='$jd' ";
							$r2 = command($req_sql_materiel); 
							$delta=0;
							while ($d2 = fetch_command($r2))
								$delta+=$d2["qte"];			
							$tab_delta[$i] =$delta;
							}
						$i++;
						}
						
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
				echo "<tr> <td bgcolor=\"$color\"> Variation </td>";
				for ($i=$deb; $i<$imax; $i++)
					aff($tab_delta[$i]);		
				for ($tot=0 , $i=$deb; $i<$imax; $i++) 
					$tot+=$tab_delta[$i];				
				aff($tot);	
				}

	
			}

		fermeture_bdd ();
		}
?>