<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<?php include 'header.php';	  ?>

    <head>
	
	     <title>Planning hébergement</title>
		
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
    
		</head>
		<body>
<?php
include 'calendrier.php';
include 'general.php';
include 'suivi_liste.php';

		
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
		
	function aff($v, $color2="" )
		{
		global $color;
		

		if ($v>1)
			echo "<td width=\"20\" ALIGN=\"RIGHT\" bgcolor=\"red\"> ".$v."</td>";		
		else
			if ($v==0)
					{	
					if($color2=="") $color2=$color;
					echo "<td width=\"20\" ALIGN=\"RIGHT\" bgcolor=\"$color2\">  </td>";
					}
				else
					if ($color2=="")
						echo "<td width=\"20\" ALIGN=\"RIGHT\" bgcolor=\"silver\"> ".$v."</td>";
					else
						echo "<td width=\"20\" ALIGN=\"RIGHT\" bgcolor=\"$color2\"> ".$v."</td>";

					
		return($v);
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

		$detail=isset ($_GET["detail"]);
		
		if (!isset ($_GET["date_jour"]))
			$date_jour=date($format_date,  mktime(0,0,0 , date("m"), date("d")-2, date ("Y")));
		else 
			$date_jour=$_GET["date_jour"];

		if (!isset ($_GET["date_fin"]))
			$date_fin=date($format_date,  mktime(0,0,0 , date("m"), date("d")+20, date ("Y")));
		else 
			$date_fin=$_GET["date_fin"];		

				// ===================================================================== Bloc DATE
				echo "<table border=\"0\" >";
				echo "<td> <a href=\"\"> <img src=\"images/suivi.jpg\" width=\"140\" height=\"100\"  >  <a> </td>   ";
				echo "<td> Debut:</td> <td> ";
				echo "<form method=\"GET\" action=\"hebergement.php\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"date\"> " ;	
				echo "<input type=\"text\" name=\"date_jour\" size=\"10\" value=\"$date_jour\" class=\"calendrier\">";
				echo "</td> <td> ";
				
				echo "</td> <td> Fin: </td> <td>";			
				echo "<input type=\"text\" name=\"date_fin\" size=\"10\" value=\"$date_fin\" class=\"calendrier\">";
				echo "</td> <td> ";
				echo "<input type=\"submit\" value=\"Valider les dates\" >  ";
				echo " </form> ";
				
				// ===================================================================== Bloc DATE
				echo "</td> <td> ";
				echo "<form method=\"GET\" action=\"hebergement.php\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"date\"> " ;	
				echo "<input type=\"hidden\" name=\"date_jour\" value=\"$date_jour\">";
				echo "<input type=\"hidden\" name=\"date_fin\" value=\"$date_fin\">";
				echo "</td> <td> ";
				echo " </form> ";		
				echo "</td> <td> <a href=\"javascript:window.close();\">Fermer la fenêtre</a></td> "; 
				echo " </form> </table>";	
		
			$crit_presence=" (pres_repas='présence') and ( not (nom like '%(B)%')) and ( not (nom like '%(S)%')) and ( not (nom like '%(A)%')) and (nom<>'Synth') and (nom<>'Mail')    ";

			
			$jd=mise_en_forme_date_aaaammjj( $date_jour);
			$jf=mise_en_forme_date_aaaammjj( $date_fin);	
		
			$date_deb= str_replace("-","/",$jd);						
			$date_fin= str_replace("-","/",$jf);
								
			$imax = (strtotime($date_fin) - strtotime($date_deb))/3600/24;			

		// ================================================================================== HISTORIQUE
		if (($imax<1) || ($imax>366))
			{
			erreur ("Dates incorrectes ou trop éloignées (période max d'un an)");
			exit();
			}
			echo "<table border=\"0\" >";	
			$ncolor=0;
			
			$color="#3f7f00";
			echo "<tr> <td bgcolor=\"$color\">  </td><td bgcolor=\"$color\"><font color=\"white\"> Commentaire </td>";
			$jj=-1;
			for ($i=0; $i<$imax; $i++)
				{
				if (date("Y-m-d", strtotime($date_deb)+$i*3600*24  )==date("Y-m-d"))
					$jj=$i;
				echo "<td bgcolor=\"$color\"> <font color=\"white\">".date("d/m", strtotime($date_deb)+$i*3600*24  )." </td>";		
				$periode[$i]=date("Y-m-d", strtotime($date_deb)+$i*3600*24  );
				}
			
			for ($i=0; $i<$imax; $i++)
				$tab_tot[$i]=0;				
			$req_sql_presence="SELECT *,count(*) as TOTAL FROM $bdd where activites<'$jf' and qte>'$jd' and $crit_presence  group by nom order by TOTAL DESC";
			$r1 = command($req_sql_presence); 
			while ($d1 = fetch_command($r1) )	
				{
				$nom=$d1["nom"];
				
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 
				echo "<tr> <td bgcolor=\"$color\"> <a href=\"suivi.php?action=suivi&nom=$nom\" target=_blank> <b>$nom  </b></td>";
	
				for ($i=0; $i<$imax; $i++)
					$tab_delta[$i]=0;
				$comment="";
				$req_sql_presence2="SELECT *  FROM $bdd where nom='$nom' and  activites<'$jf' and qte>'$jd' and $crit_presence ";
				$r2 = command($req_sql_presence2); 
				while ($d2 = fetch_command($r2))
					{
					$date_deb=$d2["activites"];
					$date_fin=$d2["qte"];
					
					if ($comment=="")
						$comment.=$d2["commentaire"];
					else
						$comment.="<br>".$d2["commentaire"];
						
					if ($date_deb <$jd) $date_deb =$jd;								
					if ($date_fin >$jf) $date_fin =$jf;
				
					$date_deb= str_replace("-","/",$date_deb);						
					$date_fin= str_replace("-","/",$date_fin);
							
					$delta= (strtotime($date_fin) - strtotime($date_deb))/3600/24;
					$i= (strtotime($date_deb) - strtotime(str_replace("-","/",$jd)))/3600/24;
					for ($j=0; $j<$delta; $j++) 
						{
						$tab_delta[$i+$j]++;	
						$tab_tot[$i+$j]++;	
						}
					}

				echo "<td bgcolor=\"$color\">$comment</td>";
						
				for ($i=0; $i<$imax; $i++)
					if ($i==$jj)
						aff($tab_delta[$i],"yellow");	
					else
						aff($tab_delta[$i]);	
				
				}
			$color="#3f7f00"; 
			echo "<tr> <td bgcolor=\"$color\"><font color=\"white\"> Total  </td><td bgcolor=\"$color\">   </td>";
			for ($i=0; $i<$imax; $i++)
				echo "<td width=\"20\" ALIGN=\"RIGHT\" bgcolor=\"$color\"> <font color=\"white\">  ".$tab_tot[$i]."</td>";

		fermeture_bdd ();
		}
?>