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


		
	function  kpi_sur_periode($jd, $jf, $choix )
			{
			global 	$bdd, $crit_bene,  $crit_activite ,  $suivi;
			
			$reponse = command("select * from $bdd where date>='$jd' and date<='$jf' and  ( $crit_bene or (pres_repas='$choix') ) order by date ");
			$suivi=0;
			while ($donnees = fetch_command($reponse) )
				{
				switch ($donnees["pres_repas"])
					{
					case "$choix": $suivi++; break;
					default : break;
					}
				}
		   switch ($choix)
				{
				case "__upload" : $choix="Dépôt doc interne"; break;
				case "releve" : $choix="Relevé courrier"; break;
				}
					
			echo "<tr> <td> $choix </td>";	
			echo "<td ALIGN=\"RIGHT\" width=\"20\"> $suivi </td>";
			}



	function 	kpi_detail_sur_periode($jd, $jf, $choix )
			{
			global  $jd,$jf,	$bdd, $crit_bene,  $crit_activite ;

			echo "<table> <tr> <td bgcolor=\"#3f7f00\"  ><font color=\"white\"> ";
			switch ($choix)
				 {
				 case "Suivi" : echo "Motif";	break;
				 case "reponse" : echo "Réponse";	break;

				 default: echo "$choix";	break;
				 }			
			echo "</td><td bgcolor=\"#3f7f00\" ><font color=\"white\">  Nbre </font></td>";	
			echo "<td bgcolor=\"#3f7f00\" ALIGN=\"RIGHT\" ><font color=\"white\"> % </font></td>";	
			
			$reponse = command("select distinct * from $bdd where date>='$jd' and date<='$jf' and pres_repas='$choix' and activites<>'' and not $crit_activite ");
			$total=nbre_enreg ($reponse);
			
			$ncolor=0;
			$n=0;
			$nt=0;
			$reponse = command("select distinct * from $bdd where date>='$jd' and date<='$jf' and pres_repas='$choix' and activites<>'' and not $crit_activite group by activites ");
			while ($donnees = fetch_command($reponse) )	
				{
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
				$nom=$donnees["activites"];		
				echo "<tr><td ALIGN=\"RIGHT\" bgcolor=\"$color\" > $nom</td>";		

				$r1 = command("select distinct * from $bdd where date>='$jd' and date<='$jf' and pres_repas='$choix' and not $crit_activite and activites='$nom' ");
					
				$nb=nbre_enreg ($r1);
				$n+=$nb;
				echo "<td ALIGN=\"RIGHT\" bgcolor=\"$color\" > $nb</td>";
				echo "<td ALIGN=\"RIGHT\" bgcolor=\"$color\"> &nbsp;&nbsp;".sprintf("%2.01f",$nb/$total*100)."% </td>";
				$nt+=$nb/$total*100;
				}
				
			echo "<tr><td ALIGN=\"RIGHT\" > Total</td>";		
			$n=$nb;
			echo "<td ALIGN=\"RIGHT\" > $n</td>";
			echo "<td ALIGN=\"RIGHT\" > $nt%</td>";
			echo "</table><hr>";
			
			}				
	
	$detail = true;	
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
				echo "<td> <a href=\"\"> <img src=\"images/suivi.jpg\" width=\"140\" height=\"100\"  >  <a> </td>   ";
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
				//if (!$detail) 
				//	echo "<input type=\"submit\" value=\"Historique (par mois)\" >  ";
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
			
			echo "<hr><table> <tr> <td bgcolor=\"#3f7f00\"><font color=\"white\"> Action </td><td bgcolor=\"#3f7f00\"><font color=\"white\">  Nbre </font></td>";	
			kpi_sur_periode($jd, $jf ,"Suivi");
			kpi_sur_periode($jd, $jf ,"__upload");	

			//kpi_sur_periode($jd, $jf ,"Arrivée courrier");
			kpi_sur_periode($jd, $jf ,"releve");

			echo "</table><hr>";	
								

			kpi_detail_sur_periode($jd, $jf, "Suivi" ); // demande
			kpi_detail_sur_periode($jd, $jf, "reponse" ); // reponse
			kpi_detail_sur_periode($jd, $jf, "Partenaire" ); // orientation
			
			

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

		fermeture_bdd ();
		}
?>