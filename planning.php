<?php 

	$restriction_ad =  $_SESSION['ad'] || ( (($user_droit=="S") || ($user_droit=="R"))  )  ;
	
	if ( (($user_droit=="S") || ($user_droit=="R")) && ($action=="cc_usager") )
		{
		$action="cc_usagers";
		$_SESSION["type_usager"]="";
		}
	
	if ( (($user_droit=="S") || ($user_droit=="R")) && ($action=="cc_accueillant") )
		{
		$action="cc_usagers";
		$_SESSION["type_usager"]="B";
		}
		
	function libelle_usager( $idx)
		{
		$r1 =command("select * from  cc_user where idx='$idx'  ");
		$d1 = fetch_command($r1);
		return(stripcslashes($d1["nom"]." ".$d1["prenom"]));
		}			
	function libelle_activite( $idx)
		{
		$r1 =command("select * from  fct_calendrier where idx_activite='$idx'  ");
		$d1 = fetch_command($r1);
		return(stripcslashes($d1["libelle"]));
		}	
		
	if (($action=="supp_filtre_usager") )
		{
		$_SESSION["filtre"]="";
		$action="cc_usagers";
		}	
		
	function traite_heure_creneau ($heure)
		{
		$d3= explode("H",$heure);  
		$m3=0;
		
		if (isset($d3[1]))
			{
			$m= $d3[1];
			if ($m<15) $m3=0;
				else
					if ($m<30) $m3=1;
						else
							if ($m<45) $m3=2;
								else
								 $m3=3;
			}
		return($d3[0]*4+$m3);
		}
		
	function traite_creneau ($creneau, $etat)
		{
		global $occupation,$nb_occupation;
		
		$d3= explode("/",$creneau);  
		$d2=traite_heure_creneau($d3[0]);
		$f2=traite_heure_creneau($d3[1]);
		
		for ($m=$d2;$m<$f2;$m++)
			{
			if ($etat=="libre")
				$occupation[$m]=$etat;
			else
				{
				$occupation[$m]=" title=\"".libelle_usager($etat)."\"";		
				$nb_occupation[$m]++;
				}
			}
		}
		
	function traite_planning_jour ($creneau, $etat)
		{
		$d3= explode(";",$creneau);  
		
		for ($i=0;$i<10;$i++) 
			{
			if (isset($d3[$i]))
				traite_creneau ($d3[$i], $etat);
			else
				break;
			}
		return($creneau);			
		}
	
	function affiche_titre_heure($debut_min,$fin_max )
		{	
		echo "<table><tr><td></td><td></td><td></td>";
		for ($h=$debut_min; $h<$fin_max; $h++)
			echo "<td> </td><td> </td><td> </td><td> </td><td></td>";
		}
		
	function liste_usager_activite($activite)
		{
		global $user_organisme;
		
		echo "<td><SELECT name=\"usager\" >";

		$reponse =command("select * from  cc_user where organisme='$user_organisme' and type='' and etat=''  order by nom ");
		while ($donnees = fetch_command($reponse) ) 
			{
			$nom=stripcslashes($donnees["nom"]);
			$prenom=stripcslashes($donnees["prenom"]);
			$idx=$donnees["idx"];
			affiche_un_choix_2($val_init,$idx,"$nom $prenom");			
			}
		echo "</SELECT></td>";
		}

	function liste_accueillants($activite)
		{
		global $user_organisme;
		
		echo "<td><SELECT name=\"usager\" >";

		$reponse =command("select * from  cc_user where organisme='$user_organisme' and type<>'' and etat='' order by nom ");
		while ($donnees = fetch_command($reponse) ) 
			{
			$nom=stripcslashes($donnees["nom"]);
			$prenom=stripcslashes($donnees["prenom"]);
			$idx=$donnees["idx"];
			affiche_un_choix_2($val_init,$idx,"$nom $prenom");			
			}
		echo "</SELECT></td>";
		}
		
		
	function liste_quart_d_heure($val_init="")
		{
		echo "<td><SELECT name=\"duree\" >";
		affiche_un_choix($val_init,"15");
		affiche_un_choix($val_init,"30");
		affiche_un_choix($val_init,"45");
		affiche_un_choix($val_init,"1H");
		affiche_un_choix($val_init,"1H30");
		affiche_un_choix($val_init,"1H");

		echo "</SELECT></td>";
		}	
		

	function liste_etat_creneau($val_init="")
		{
		echo "<td><SELECT name=\"etat\" >";
		affiche_un_choix($val_init,"???");
		affiche_un_choix($val_init,"Validé");
		affiche_un_choix($val_init,"Refusé");
		echo "</SELECT></td>";
		}		
		
	function liste_d_heure($val_init="")
		{
		echo "<td><SELECT name=\"duree\" >";
		for ($h=0;$h<9;$h++)
			for ($m=0;$m<60;$m+=30)		
				affiche_un_choix($val_init,$h."H".$m);

		echo "</SELECT></td>";
		}		

	if ( ($restriction_ad) && ($action=="supp_creneau") )
		{
		$idx=variable("idx");	
		command("delete from cc_creneau where idx='$idx' ");
		if ($_SESSION['ad'])
			$action="cc_activite";
		else
			$action="cc_creneau";
		}	
	
	if ( (($user_droit=="S") || ($user_droit=="R")) && ($action=="usager_a_inactiver") )
		{
		$idx=variable("idx");	
		command("UPDATE cc_user set etat='Inactif' where idx='$idx'");
		$action="cc_activite";
		}	
	
	
	function affiche_journee_calendrier($idx_activite, $nb_max, $act, $i, $debut_min, $fin_max, $mode="")
		{
		global $jour_ouvert, $aff_date,$couleur_libre, $couleur_occupe,$couleur_plein, $occupation, $nb_occupation;
		 
		echo "<tr><td>";
		$j=date('w',$i);
		$ej=encrypt("$i");
		$ea=encrypt("$idx_activite");
		if ($aff_date)
			{
			echo libelle_jour (date('w',$i))."</td><td>". date('d',$i)."</td>";	
			$aff_date=FALSE;
			}
		else
			echo '</td><td> </td>';	
					
		if ( ($mode=="") && (!$_SESSION['ad']) )
			echo "<td ><a href=\"".$_SERVER['PHP_SELF'] ."?action=cc_ajout&jour=$ej&activite=$ea\"> + </a></td><td >|</td>";		
		else
			echo "<td ></td><td >|</td>";
			
		for ($m=0;$m<24*4;$m++)
			{
			$occupation[$m]="";	
			$nb_occupation[$m]=0;	
			}
						
		// on regarde les heures d'ouvertures
		$r1 =command("select * from  cc_creneau where activite='$idx_activite' and date='$i' and user='' ");
					
		$d3="0/0";
		while ($d1=fetch_command($r1) )
			$d3= traite_planning_jour ($d1['horaire'],"libre");

		if ($d3=="0/0")	
			if ($jour_ouvert[$act][$j]!="")
				$d3= traite_planning_jour ($jour_ouvert[$act][$j],"libre"); // standard

		$r1 =command("select * from  cc_creneau where activite='$idx_activite' and  date='$i' and user<>'' ");
		while($d2 = fetch_command($r1) )
			traite_planning_jour ($d2['horaire'], $d2['user']);
			
		for ($h=$debut_min; $h<$fin_max; $h++)
			{
			for ($m=0; $m<4; $m++)
				{
				if ($occupation[$h*4+$m]=="")
					echo "<td width=\"10\" BGCOLOR=\"lightgrey\" >  </td>";
				else
					{
					$ej=encrypt("$i");
					$ea=encrypt("$idx_activite");
						
					$eh=$h*4+$m;		
					$eh=encrypt("$eh");	
					
					if ($occupation[$h*4+$m]=="libre")
						echo "<td width=\"10\" BGCOLOR=\"".$couleur_libre[$act]."\" ><a href=\"".$_SERVER['PHP_SELF'] ."?action=cc_ajout&jour=$ej&activite=$ea&heure=$eh\" title=\"ajout\">+</a></td>";									
					else
						{
						if ( $nb_occupation[$h*4+$m]<$nb_max)
							echo "<td width=\"10\" ".$occupation[$h*4+$m]." BGCOLOR=\"".$couleur_occupe[$act]."\" ><a href=\"".$_SERVER['PHP_SELF'] ."?action=cc_ajout&jour=$ej&activite=$ea&heure=$eh\" title=\"ajout\">+</a></td>";
						else
							echo "<td width=\"10\" ".$occupation[$h*4+$m]." BGCOLOR=\"".$couleur_plein[$act]."\" > </td>";
						}
					}
				}
			echo "<td >|</td>";
			}	
		}
	

	if ( ($restriction_ad) && ($action=="ajout_creneau_usager") )
		{
		$idx_activite=variable("activite");	
		$jour=variable("jour");	
		$heure=variable("heure");	
		$duree=variable("duree");	
		$preavis=variable("preavis");	
		$usager=variable("usager");	
		$comment=variable("commentaire");	
		$idx_creneau=inc_index("creneau");
		$etat=variable("etat");
		$nbre=variable("nbre");
		
		$d3= explode("H",$heure);  
		$h=$d3[0];
		$m=$d3[1];		
		switch ($duree)
			{
			case "15": $m+=15; break;
			case "30": $m+=30; break;
			case "45": $m+=45; break;
			case "1H": $m+=60; break;	
			default :
				$d4= explode("H",$duree);  
				$h+=$d4[0];
				$m+=$d4[1];		
				break;
			}
		if ($m>59)
			{
			$h++;
			$m-=60;
			}
		$reponse =command("insert into cc_creneau VALUES ('$idx_creneau', '$idx_activite', '$jour', '$heure/$h"."H"."$m', '$usager', '', '$preavis', '$comment', '$etat', '$nbre' ) ");		
		$action="cc_ajout";
		}

	if ( (($user_droit=="S") || ($user_droit=="R")) && ($action=="nouveau_usager") )
		{
		$nom=variable("nom");	
		$prenom=variable("prenom");	
		if ( ($nom!="") && ($prenom!=""))
			{
			$mail=variable("mail");	
			$tel=variable("tel");	
			$adresse=variable("adresse");	
			$commentaire=variable("commentaire");	
			$type=variable("type");	
			$idx_usager=inc_index("usager");
			$reponse =command("insert into cc_user VALUES ('$idx_usager', '$user_organisme','$nom', '$prenom', '$tel', '$mail', '$adresse', '$commentaire' , '$type', '' ) ");		
			}
		else 
			erreur ("Pour créer un usager, il faut au moins indiquer un nom et un prénom");
		$action="cc_usagers";
		}	
		
	if ( ($restriction_ad) && ($action=="cc_precedent") )
		$_SESSION["mois"]-=1;
		
	if ( ($restriction_ad) && ($action=="cc_suivant") )
		$_SESSION["mois"]+=1;	
		
	if ( (($user_droit=="S") || ($user_droit=="R")) && ($action=="planning_select") )
		{
		$act=variable("act");
		if ($_SESSION["d$act"]=="checked")
			$_SESSION["d$act"]="";		
		else
			$_SESSION["d$act"]="checked";	
		}
		
	if ( ($action=="cc_activite")  ||($action=="cc_precedent") || ($action=="cc_suivant") || ($action=="planning_deselect")   || ($action=="planning_select")   )
		if ($restriction_ad)
		{
		echo "<table><tr>";

		if (($user_droit=="S") || ($user_droit=="R"))
			{
			echo "<td><ul id=\"menu-bar\"><li><a href=\"".$_SERVER['PHP_SELF'] ."?action=cc_usager\"  > ".traduire('Usagers')."</a>";
			echo "</ul ></td>";
			echo "<td><ul id=\"menu-bar\">";
			echo "<li><a href=\"".$_SERVER['PHP_SELF'] ."?action=cc_accueillant\"  > ".traduire('Accueillants')."</a>";		
			echo "</ul ></td>";
			echo "<td><ul id=\"menu-bar\">";
			echo "<li><a href=\"".$_SERVER['PHP_SELF'] ."?action=planning_ajout\"  > ".traduire('Calendrier')."</a>";
			}

		$eu = encrypt($_SESSION['user']);
		if  ( $_SESSION['ad']) 
			echo "<td><a href=\"".$_SERVER['PHP_SELF'] ."?action=cc_detail_usager&usager=$eu\">Voir mes prochains créneaux</a></td>";
			
		echo "<td> | </td>";
		if (isset($_SESSION["mois"]))
			$mois=$_SESSION["mois"];
		else
			{
			$_SESSION["mois"]=0;
			$mois=0;
			}
			
		$t0=mktime(0,0,0 ,date('m')+$mois, 1, date('Y'));
		$tfin=mktime(0,0,0 ,date('m')+$mois+1, 0, date('Y'));
		echo "<td> <a href=\"".$_SERVER['PHP_SELF'] ."?action=cc_precedent\"  ><img src=\"images/gauche.png\" width=\"20\" height=\"20\"></a> ".date('m-Y',$t0)." <a href=\"".$_SERVER['PHP_SELF'] ."?action=cc_suivant\"><img src=\"images/droite.png\" width=\"20\" height=\"20\"></a></td>";		
		echo "<td> | </td>";

		for ($j=0;$j<7;$j++)			
			$jo[$j]="";
		$act=0;
		$liste_activite="";
		$debut_min=24;
		$fin_max=0;		
		
		$debut=date('Y-m-d',$t0);
		$fin= date('Y-m-d',$tfin);
		
		switch( $user_droit) 
			{
			
			case 'R':
			case 'S':
				if ($_SESSION['ad'])	
					$reponse =command("select * from  fct_calendrier where organisme='$user_organisme' and ( debut<'$fin' or debut='') and ( fin>='$debut' or fin='') and type='A' and acces_direct<>'' ");
				else
					$reponse =command("select * from  fct_calendrier where organisme='$user_organisme' and ( debut<'$fin' or debut='') and ( fin>='$debut' or fin='') ","x");
				
				break;

			case '':
			case ' ':
				$reponse =command("select * from  fct_calendrier where organisme='$user_organisme' and ( debut<'$fin' or debut='') and ( fin>='$debut' or fin='') and acces_direct<>'' and type='' ");
				break;
				
			case 'B':
				$reponse =command("select * from  fct_calendrier where organisme='$user_organisme' and ( debut<'$fin' or debut='') and ( fin>='$debut' or fin='') and acces_direct<>'' and type='A' ");
				break;
			}

		while ($donnees = fetch_command($reponse) ) 
			{
			$d3= explode("/",$donnees['horaires']);
			$debut_min=min($debut_min,$d3[0]);
			$fin_max=max($fin_max,$d3[1]);

			for ($j=0;$j<7;$j++)
				{
				$jour_ouvert[$act][$j]=$donnees[$j];
				$jo[$j].=$donnees[$j];
				}
				
			$libelle[$act]=$donnees["libelle"];
			$type[$act]=$donnees["type"];
			$idx_activite[$act]=$donnees["idx_activite"];
			if($liste_activite=="")
				$liste_activite=$idx_activite[$act];
			else
				$liste_activite.=",".$idx_activite[$act]."";
				
			if (!isset($_SESSION["d$act"]) )	
				$_SESSION["d$act"]="checked";		

			echo "<td>";
			formulaire ("planning_select");
			echo "<input type=\"hidden\" name=\"act\" value=\"$act\">";
			echo "<input type=\"checkbox\" name=\"val\" ".$_SESSION["d$act"]." onChange=\"this.form.submit();\" > ".$libelle[$act]."</form></td> ";
			
			$couleur_libre[$act]=$donnees["couleur_libre"];
			if ($couleur_libre[$act]=="")
				$couleur_libre[$act]="green";
			$couleur_occupe[$act]=$donnees["couleur_occupe"];
			if ($couleur_occupe[$act]=="")
				$couleur_occupe[$act]="yellow";
			$couleur_plein[$act]=$donnees["couleur_plein"];
			if ($couleur_plein[$act]=="")
				$couleur_plein[$act]="red";

			$nb_max[$act]=$donnees["nbre"];
			
			$debut_act[$act]=$donnees["debut"];
			$fin_act[$act]=$donnees["fin"];
				
			if ($_SESSION["d$act"]=="checked")
				{
				echo "<td  BGCOLOR=\"".$couleur_libre[$act]."\">_</td>";
				echo "<td  BGCOLOR=\"".$couleur_occupe[$act]."\">_</td>";
				echo "<td  BGCOLOR=\"".$couleur_plein[$act]."\">_</td>";
				}
			echo "<td> | </td>";
			
			$act++;
			}
		$act_max=$act;	
		
		if ($liste_activite=="")
			{
			$debut_min=8;
			$fin_max=20;
			}

		affiche_titre_heure($debut_min,$fin_max );

		for ($i=$t0; $i<$tfin; $i+= 60*60*24)
			{
			$j=date('w',$i);
			$aff_date=true;
			$date_du_jour= date('Y-m-d',$i);
			
			if ($liste_activite!="")
				{
				$r0 = command("select * from  cc_creneau where activite IN ($liste_activite) and date='$i' order by activite");
				$d0 = fetch_command($r0) ;
				}
			else
				$d0 = false ;

			
			if ($j==1)
				{
				echo "<tr><td></td><td></td><td></td><td></td>";
				for ($h=$debut_min; $h<$fin_max; $h++)
					echo "<td>-</td><td>-</td><td>-</td><td>-</td><td>|</td>";
				}
				
			if (($jo[$j]!="") || ($d0))
				{
				for ($act=0; $act<$act_max;$act++)
					{
					if (($_SESSION["d$act"]=="checked") 
					&& ($jour_ouvert[$act][$j]!="") 
					&& ( ($debut_act[$act]<$date_du_jour) || ($debut_act[$act]=="") )
					&& ( ($fin_act[$act]>$date_du_jour) || ($fin_act[$act]=="") ) )
						{
						affiche_journee_calendrier($idx_activite[$act], $nb_max[$act], $act, $i,$debut_min,$fin_max);
						// détail par personne
						if (
							(isset ($_SESSION["detaille"]) && ($_SESSION["detaille"]=="") )
							||
							(( $restriction_ad ) && ($type[$act]!="") )
							)
							{
							$reponse =command("select * from  cc_creneau where activite='$idx_activite[$act]' and  date='$i' and user<>'' ");
							while($donnees = fetch_command($reponse) )
								{
								$idx=$donnees["idx"];
								$horaire=$donnees["horaire"];
								$eu = encrypt($donnees["user"]);
								if ( ( $_SESSION['ad']) && ($donnees["user"]!=$_SESSION['user'] ) )
									$usager= libelle_usager($donnees["user"]);
								else
									$usager= "<a href=\"".$_SERVER['PHP_SELF'] ."?action=cc_detail_usager&usager=$eu\">".libelle_usager($donnees["user"])."</a>";
								$preavis=$donnees["preavis"];
								$commentaire=$donnees["commentaire"];
								
								affiche_journee_creneau($idx, $usager,$debut_min,$fin_max,$horaire, $commentaire);
								}	
							}								
						}
					}
				}	
			}
		echo "</table>";
		echo "<hr>";
		pied_de_page("");
		}
					

	if ( (($user_droit=="S") || ($user_droit=="R")) && ($action=="cc_maj_activite") )
		{
		$idx_activite=variable_get("idx_activite");	
		
		// MAJ activité
		// - libellé
		// - horaires
		// - texte standard

		pied_de_page("x");
		}	
		
	if ( (($user_droit=="S") || ($user_droit=="R")) && ($action=="cc_maj_rdv") )
		{
		$idx_activite=variable_get("idx_activite");	
		
		// MAJ rdv
		// Changement texte et délai d'alerte
		// possibilité suppression
		// Ajout commentaire

		pied_de_page("x");
		}

	function 	affiche_journee_creneau($idx, $usager,$debut_min,$fin_max,$horaire, $commentaire, $color="grey")
		{
 		global $occupation,$nb_occupation;
		
		echo "<tr><td>$usager</td><td ></td>";
		if ( (!$_SESSION['ad']) || ($usager==$_SESSION['user']) )
			lien_c ("images/croixrouge.png", "supp_creneau", param("idx","$idx" ), traduire("Supprimer") );	
		else
				echo "<td ></td>";	
		echo "<td >|</td>";		
		for ($m=0;$m<24*4;$m++)
			{
			$occupation[$m]="";	
			$nb_occupation[$m]=0;	
			}
			
		traite_creneau ($horaire, "x");
			
		for ($h=$debut_min; $h<$fin_max; $h++)
			{
			for ($m=0; $m<4; $m++)
				{
				if ($occupation[$h*4+$m]=="")
					{
					if ($color!="grey")
						echo "<td width=\"10\" BGCOLOR=\"lightgrey\" > </td>";
					else
						echo "<td width=\"10\"  > </td>";
					
					}
				else
					echo "<td width=\"10\"  BGCOLOR=\"$color\" > </td>";
				}
			echo "<td >|</td>";
			}
		if ( (!$_SESSION['ad']) || ($usager==$_SESSION['user']) )
			lien_c ("images/croixrouge.png", "supp_creneau", param("idx","$idx" ), traduire("Supprimer") );	
		echo "<td >$commentaire</td>";

		}

	 function titre_tableau_usager()
		{
			echo "<p>";
			echo "<div class=\"CSSTableGenerator\" ><table> ";
			echo "<tr>	<td> ".traduire('Date')." </td>
								<td> ".traduire('Heure')." </td>
								<td> ".traduire('Usager')." </td>
								<td> ".traduire('Durée')." </td>
								<td> ".traduire('Préavis par SMS')." </td>
								<td> ".traduire('Etat')." </td>
								<td> ".traduire('Commentaire')." </td>
								<td> </td>
								";
		}
		
					
	if ( ($restriction_ad) &&  ($action=="cc_ajout")  )
		{
		$idx_activite=variable_get("activite");	
		$t0=variable_get("jour");	
		if (($idx_activite=="") && ($t0=="") )
			{
			$idx_activite=variable("activite");	
			$t0=variable("jour");	
			}
		$m=variable_get("heure");	

		$reponse =command("select * from  fct_calendrier where organisme='$user_organisme' and idx_activite='$idx_activite'" );
		if ($donnees = fetch_command($reponse) ) 
			{
			$duree_creneau=$donnees['duree_creneau'];
			$validation=$donnees['validation'];
			
			$d3= explode("/",$donnees['horaires']);
			$debut_min=$d3[0];
			$fin_max=$d3[1];
			$act=0;
			
			for ($j=0;$j<7;$j++)
				$jour_ouvert[$act][$j]=$donnees[$j];

			$libelle=$donnees["libelle"];
			echo "<p>$libelle ";
			$couleur_libre[$act]=$donnees["couleur_libre"];
			if ($couleur_libre[$act]=="")
				$couleur_libre[$act]="ligthgreen";
			$couleur_occupe[$act]=$donnees["couleur_occupe"];
			if ($couleur_occupe[$act]=="")
				$couleur_occupe[$act]="yellow";
			$couleur_plein[$act]=$donnees["couleur_plein"];
			if ($couleur_plein[$act]=="")
				$couleur_plein[$act]="red";
			$nb_max=$donnees["nbre"];
			$type=$donnees["type"];
			
		
			$aff_date=true;
			affiche_titre_heure($debut_min,$fin_max);
			affiche_journee_calendrier($idx_activite,$nb_max,0,$t0,$debut_min,$fin_max,"1");

			$date_du_jour= date('Y-m-d',$t0);
			
			echo "<tr><td></td><td></td><td></td><td></td>";
			for ($h=$debut_min; $h<$fin_max; $h++)
				echo "<td>-</td><td>-</td><td>-</td><td>-</td><td>|</td>";			
			
			if (($_SESSION['ad']) && ($type=="") )
				$reponse =command("select * from  cc_creneau where activite='$idx_activite' and  date='$t0' and user='".$_SESSION['user']."' ");
			else
				$reponse =command("select * from  cc_creneau where activite='$idx_activite' and  date='$t0' and user<>'' ");
			while($donnees = fetch_command($reponse) )
				{
				$idx=$donnees["idx"];
				$horaire=$donnees["horaire"];
				$eu = encrypt($donnees["user"]);
				if ( ( $_SESSION['ad']) && ($donnees["user"]!=$_SESSION['user'] ) )
					$usager= libelle_usager($donnees["user"]);
				else				
					$usager= "<a href=\"".$_SERVER['PHP_SELF'] ."?action=cc_detail_usager&usager=$eu\">".libelle_usager($donnees["user"])."</a>";
				$preavis=$donnees["preavis"];
				$commentaire=$donnees["commentaire"];
				affiche_journee_creneau($idx, $usager,$debut_min,$fin_max,$horaire, $commentaire, "lightgreen");
				}
			echo "</table> ";

			
			if ($m!="")
				{
				titre_tableau_usager();

				$heure=(int)($m/4);		
				$minute=($m-$heure*4)*15;		
				echo "<tr>	<td> ".libelle_jour (date('w',$t0))." ". date('d-m',$t0)."</td><td> ".$heure."H".$minute." </td>";
				formulaire("ajout_creneau_usager");
			
				echo "<input type=\"hidden\" name=\"activite\"  value=\"$idx_activite\"> " ;
				echo "<input type=\"hidden\" name=\"jour\"  value=\"$t0\"> " ;
				echo "<input type=\"hidden\" name=\"heure\"  value=\"".$heure."H".$minute."\"> " ;
				
				if ($_SESSION['ad'])
					{
					echo "<input type=\"hidden\" name=\"usager\"  value=\"".$_SESSION['user']."\"> " ;
					echo "<td>".libelle_usager($_SESSION['user'])."</td>";
					
					if ($type=="") // type usager
						{
						echo "<input type=\"hidden\" name=\"duree\"  value=\"$duree_creneau\"> " ;
						echo "<td>$duree_creneau</td>";
						liste_avant( "1H" , "", traduire("non") );
						}
					else
						{
						liste_d_heure($duree_creneau);
						liste_avant( traduire("non") , "", traduire("non") );
						}
					if ($validation=="oui")
						echo "<input type=\"hidden\" name=\"etat\"  value=\"???\"> " ;
					else
						echo "<input type=\"hidden\" name=\"etat\"  value=\"Validé\"> " ;
					
					echo "<td> </td>";
					}
				else
					{
					if ($type=="")
						{
						liste_usager_activite($idx_activite);
						liste_quart_d_heure();
						liste_avant( "1H" , "", traduire("non") );
						echo "<input type=\"hidden\" name=\"etat\"  value=\"Validé\"> " ;
						echo "<td> </td>";
						}
					else
						{
						liste_accueillants($idx_activite);
						liste_d_heure("3H0");
						liste_avant( traduire("non") , "", traduire("non") );
						liste_etat_creneau("Validé");
						}
					}
					
				echo "<td> <input type=\"texte\" name=\"commentaire\"   size=\"70\" value=\"\"> </td>";
				echo "<td><input type=\"submit\" id=\"ajout_creneau_usager\" value=\"".traduire('Ajouter')."\" ></form> </td> ";
				echo "</table></div>";
				}

			}
		pied_de_page("x");
		}


	if ( (($user_droit=="S") || ($user_droit=="R")) && ($action=="cc_jour") )
		{
		$idx_activite=variable_get("activite");	
		$t0=variable_get("jour");	

		echo "<table border=\"2\">";
		$reponse =command("select * from  cc_creneau where date='$t0' and activite='$idx_activite' ");		
		if ($donnees = fetch_command($reponse) )
			{
			$horaire=$donnees["horaire"];
			$commentaire=$donnees["commentaire"];
			}
		else
			echo traduire("Journée standard");
		
		echo "<tr><td> horaires </td><td>".$donnees["idx"]."</td>";
		echo "<tr><td> Commentaire</td><td>".  saisie_champ_bug_area($idx,"commentaire",$donnees["commentaire"],100)."</td>";			
		echo "</table>";
		pied_de_page("");
		}		

	function titre_usagers()
		{
		echo "<p>";
		echo "<div class=\"CSSTableGenerator\" ><table> ";
		echo "<tr>	<td> Nom </td>
					<td> Prenom </td>
					<td> Téléphone </td>
					<td> Mail </td>
					<td> Adresse </td>
					<td> Commentaire </td>
					";
		}

	function visu_usager($donnees)
			{
			$idx=$donnees["idx"];
			$eu=encrypt($idx);
			$nom=$donnees["nom"];
			$prenom=$donnees["prenom"];
			$mail=$donnees["mail"];
			$tel=$donnees["tel"];
			$etat=$donnees["etat"];
			$adresse=stripcslashes($donnees["adresse"]);
			$commentaire=stripcslashes($donnees["commentaire"]);
			echo "<tr>	<td> <a href=\"".$_SERVER['PHP_SELF'] ."?action=cc_detail_usager&usager=$eu\">$nom </a></td>
						<td> $prenom </td>
						<td> $tel </td>
						<td> $mail </td>
						<td> $adresse </td>
						<td> $commentaire </td>
						";

			lien_c ("images/modifier.jpg", "usager_a_modifier", param("idx","$idx" ), traduire("modifier") );
			if ($etat=="")
				lien_c ("images/inactif.png", "usager_a_inactiver", param("idx","$idx" ), traduire("Inactiver") );
			}
		
	if ( (($user_droit=="S") || ($user_droit=="R")) && ($action=="cc_usagers") )
		{

		echo "<table><tr><td> </td><td>  <ul id=\"menu-bar\">";
		echo "<li><a> + ".traduire('Usagers')." </a></li>";
		echo "</ul></td><td> - </td>";
		
		$filtre1=variable("filtre");
		if ($filtre1!="")
			$_SESSION["filtre"]=$filtre1;
			else
			if (isset($_SESSION["filtre"]))
				$filtre1=$_SESSION["filtre"];
		
		$type=$_SESSION["type_usager"];
		if ($type == "") 
			$filtre_type= " and type='' ";
		else
			$filtre_type= " and type!='' ";
		
		formulaire("cc_usagers");
		echo "<td > ".traduire('Filtre')." : <input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
		echo "</form><td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td>";
		lien_c ("images/croixrouge.png", "supp_filtre_usager","" , traduire("Supprimer"));

		if ($filtre1!="")
			$filtre=" and (nom REGEXP '$filtre1' or prenom REGEXP '$filtre1'or tel REGEXP '$filtre1' or mail REGEXP '$filtre1' or adresse REGEXP '$filtre1' or commentaire REGEXP '$filtre1' )  ";
		else
			$filtre="";
		
		titre_usagers();	
		formulaire ("nouveau_usager");
		echo "<tr>";
		echo "<td> <input type=\"texte\" name=\"nom\"   size=\"20\" value=\"\"> </td>";
		echo "<td> <input type=\"texte\" name=\"prenom\"   size=\"20\" value=\"\"> </td>";
		echo "<td> <input type=\"texte\" name=\"tel\"   size=\"10\" value=\"\"> </td>" ;
		echo "<td> <input type=\"texte\" name=\"mail\"   size=\"25\" value=\"\"> </td>" ;
		echo "<td> <input type=\"texte\" name=\"adresse\" size=\"40\"  value=\"\"> </td> " ;
		echo "<td> <input type=\"texte\" name=\"commentaire\" size=\"40\"  value=\"\"> </td> " ;
		echo "<input type=\"hidden\" name=\"type\"  value=\"$type\"> " ;
		echo "<input type=\"hidden\" name=\"organisme\"  value=\"\"> " ;
		echo "<td><input type=\"submit\" id=\"nouveau_usager\" value=\"".traduire('Ajouter')."\" ></form> </td> ";
				
		$reponse =command("select * from  cc_user where organisme='$user_organisme' $filtre  $filtre_type order by etat,nom");
		while ($donnees = fetch_command($reponse) ) 
			visu_usager($donnees);
		echo "</table></div>";
		pied_de_page("");
		}

		
		
			
	if ( (($user_droit=="S") || ($user_droit=="R")) && ($action=="modif_usager") )
		{
		$idx=variable("usager");
		$nom=variable("nom");
		$prenom=variable("prenom");
		$mail=variable("mail");
		$tel=variable("tel");
		$adresse=variable("adresse");
		$commentaire=variable("commentaire");
		if (($nom!="") && ($prenom!=""))
			command("UPDATE cc_user set nom='$nom', prenom='$prenom', mail='$mail',  adresse='$adresse', tel='$tel', commentaire='$commentaire' where idx='$idx'");
		$action="cc_detail_usager2";
		}
		
	if ( ($restriction_ad) && ( ($action=="cc_detail_usager") || ($action=="cc_detail_usager2"))  )
		{
		if ($action=="cc_detail_usager")
			$idx_usager=variable_get("usager");
		else
			$idx_usager=$idx;
	
		$reponse =command("select * from  cc_user where idx='$idx_usager' ");
		if ($donnees = fetch_command($reponse) ) 
			{
			$eu=encrypt($idx_usager);
			$nom=$donnees["nom"];
			$prenom=$donnees["prenom"];
			$mail=$donnees["mail"];
			$tel=$donnees["tel"];
			$adresse=stripcslashes($donnees["adresse"]);
			$commentaire=stripcslashes($donnees["commentaire"]);

			echo "<table><tr><td> </td><td>  <ul id=\"menu-bar\">";
			echo "<li><a> $nom - $prenom </a></li>";
			echo "</ul></td><td> - </td>";
			echo "</table>";	
	
			$date_du_jour=mktime(0,0,0 ,date('m'), date('d'), date('Y'));
			if (!$_SESSION['ad'])
				{
				titre_usagers();	
				formulaire ("modif_usager");
				echo "<tr>";
				echo "<td> <input type=\"texte\" name=\"nom\"   size=\"20\" value=\"$nom\"> </td>";
				echo "<td> <input type=\"texte\" name=\"prenom\"   size=\"20\" value=\"$prenom\"> </td>";
				echo "<td> <input type=\"texte\" name=\"tel\"   size=\"10\" value=\"$tel\"> </td>" ;
				echo "<td> <input type=\"texte\" name=\"mail\"   size=\"25\" value=\"$mail\"> </td>" ;
				echo "<td> <input type=\"texte\" name=\"adresse\" size=\"40\"  value=\"$adresse\"> </td> " ;
				echo "<td> <input type=\"texte\" name=\"commentaire\" size=\"40\"  value=\"$commentaire\"> </td> " ;
				echo "<input type=\"hidden\" name=\"usager\"  value=\"$idx_usager\"> " ;
				echo "<td><input type=\"submit\" id=\"modif_usager\" value=\"".traduire('Modifier')."\" ></form> </td> ";
				echo "</table>";	
				}
				
			echo "Prochains créneaux";
			$aujourdhui=mktime(0,0,0 ,date('m'), date('d'), date('Y'));
			
			echo "<p><div class=\"CSSTableGenerator\" ><table> ";
			echo "<tr><td> Date </td><td> Heure </td><td> Activité </td><td> Commentaire</td><td> Préavis</td><td> Etat </td>";
			
			$reponse =command("select * from  cc_creneau where user='$idx_usager' and date>='$aujourdhui' order by date desc");
			while ($donnees = fetch_command($reponse) )
				{
				$idx=$donnees["idx"];
				$horaire=$donnees["horaire"];
				$activite=libelle_activite($donnees["activite"]);
				$date=$donnees["date"];
				if ($date<$date_du_jour)
					$color="grey";
				else 
					$color="yellow";
				$date= date('d-m-Y',$date); 
				$preavis=$donnees["preavis"];
				$etat=$donnees["etat"];
				$commentaire=stripcslashes($donnees["commentaire"]);	
				if ($etat=="???")
					$etat="Non validé";
				echo "<tr><td> $date </td><td> $horaire </td><td> $activite </td><td> $commentaire</td><td> $preavis</td><td> $etat</td>";
				}
			echo "</table></div>";	
			}
		else
			erreur ("Usager inconnu");
		pied_de_page("");
		}


	function titre_plannings()
		{
		echo "<p>";
		echo "<div class=\"CSSTableGenerator\" ><table> ";
		echo "<tr>	<td> Libelle </td>
					<td> Type </td>	
					<td> Horaire min </td>
					<td> Horaire max </td>
					<td> Date début </td>
					<td> Date fin </td>
					<td> Durée créneau</td>
					<td> Nbre </td>
					<td> </td>";
		}

	function liste_type_creneau($val_init="")
		{
		echo "<td><SELECT name=\"type\" >";
		affiche_un_choix($val_init,"A","Usager");
		affiche_un_choix($val_init,"","Interne");
		echo "</SELECT></td>";
		}	
		
		
	if ( (($user_droit=="S") || ($user_droit=="R")) && ($action=="nouveau_planning") )
		{
		$libelle=variable("libelle");	
		$horaire_min=variable("horaire_min");	
		$horaire_max=variable("horaire_max");	
		$type=variable("type");	
		$date_debut=variable("date_debut");	
		$date_fin=variable("date_fin");	
		$duree=variable("duree");
		$nbre=variable("nbre");		
		if( ($libelle!="") && ($horaire_min!="") && ($horaire_max!="") && ($date_debut!="") )
			{
			$idx_calendrier=inc_index("calendrier");
			$reponse =command("insert into fct_calendrier VALUES ('$user_organisme','$idx_calendrier', '$libelle', '', '$horaire_min/$horaire_min','','','','','','','', '$duree', '','','', '$nbre', '$type', '$date_debut', '$date_fin' , '','','') ");		
			}
		else
			erreur (traduire("Merci de completer les champs Libellé, horaires et date de début a minima."));
		$action="planning_ajout2";
		}


		
	if ( (($user_droit=="S") || ($user_droit=="R")) && ( ($action=="planning_ajout")  ||($action=="planning_ajout2")  ) )
		{
		echo "<table><tr><td> </td><td>  <ul id=\"menu-bar\">";
		echo "<li><a> + ".traduire('Calendriers')." </a></li>";
		echo "</ul></td><td> - </td>";
		echo "</table>";
		
		if ($action=="planning_ajout")
			{
			$libelle="";	
			$horaire_min="8";	
			$horaire_max="20";	
			$type="";	
			$date_debut= date('d/m/Y',mktime(0,0,0 ,date('m'),date('d'), date('Y')) );	
			$date_fin="";	
			$duree="15";
			$nbre="1";	
			}
		
		titre_plannings();	
		formulaire ("nouveau_planning");
		echo "<tr>";
		echo "<td> <input type=\"texte\" name=\"libelle\"   size=\"30\" value=\"$libelle\"> </td>";
		liste_type_creneau($type);
		echo "<td> <input type=\"texte\" name=\"horaire_min\"   size=\"5\" value=\"$horaire_min\"> </td>";
		echo "<td> <input type=\"texte\" name=\"horaire_max\"   size=\"5\" value=\"$horaire_max\"> </td>";

		echo "<td> <input type=\"texte\" name=\"date_debut\" size=\"10\" class=\"calendrier\"  value=\"$date_debut\"> </td> " ;
		echo "<td> <input type=\"texte\" name=\"date_fin\" size=\"10\"  value=\"$date_fin\"> </td> " ;
		liste_quart_d_heure($duree);
		echo "<td> <input type=\"texte\" name=\"nbre\" size=\"2\"  value=\"$nbre\"> </td> " ;
		echo "<input type=\"hidden\" name=\"organisme\"  value=\"$user_organisme\"> " ;
		echo "<td><input type=\"submit\" id=\"nouveau_planning\" value=\"".traduire('Ajouter')."\" ></form> </td> ";
	
		$reponse =command("select * from  fct_calendrier where organisme='$user_organisme' order by fin desc");
		while ($donnees = fetch_command($reponse) )
			{
			$idx=$donnees["idx_activite"];
			$libelle=$donnees["libelle"];		
			$horaires=$donnees["horaires"];
			$d3= explode("/",$donnees['horaires']);
			$h_min=$d3[0];
			$h_max=$d3[1];
			
			$msg_std=$donnees["msg_std"];
			$type=$donnees["type"];
			if ($type=="")
				$type="Usager";
			else
				$type="Interne";
			$date_debut=$donnees["debut"];
			$date_fin=$donnees["fin"];
			$nbre=$donnees["nbre"];
			$duree_creneau=$donnees["duree_creneau"];
			echo "<tr>			<td> $libelle </td>
								<td> $type </td>
								<td> $h_min </td>
								<td> $h_max </td>
								<td> $date_debut </td>
								<td> $date_fin </td>
								<td> $duree_creneau</td>
								<td> $nbre </td>";
			lien_c ("images/modifier.jpg", "modifier_calendrier", param("idx","$idx" ), traduire("Modifier") );
			}
		echo "</table></div>";	
		
		pied_de_page("");
		}
		
	if ( (($user_droit=="S") || ($user_droit=="R")) && ($action=="modifier_calendrier")  )
		{
		echo "<table><tr><td> </td><td>  <ul id=\"menu-bar\">";
		echo "<li><a> + ".traduire('Calendriers')." </a></li>";
		echo "</ul></td><td> - </td>";
		echo "</table>";
		
		$idx=variable("idx");
		$reponse =command("select * from  fct_calendrier where idx_activite='$idx' ");
		if ($donnees = fetch_command($reponse) )
			{
			echo "<p><div class=\"CSSTableGenerator\" ><table><tr> <td></td><td></td>";
			formulaire ("modif_planning");
			echo "<tr>";
			$libelle=$donnees["libelle"];		
			$horaires=$donnees["horaires"];
			$d3= explode("/",$donnees['horaires']);
			$h_min=$d3[0];
			$h_max=$d3[1];
			$msg_std=$donnees["msg_std"];
			$type=$donnees["type"];
			$date_debut=$donnees["debut"];
			$date_fin=$donnees["fin"];
			$nbre=$donnees["nbre"];
			$duree_creneau=$donnees["duree_creneau"];
	
			echo "<tr><td> Libellé </td><td><input type=\"texte\" name=\"libelle\"   size=\"30\" value=\"$libelle\"> </td>";
			echo "<tr><td><td> Type </td>";
			liste_type_creneau($type);
			echo "<tr><td><td> Horaire min </td><td> <input type=\"texte\" name=\"horaire_min\"   size=\"5\" value=\"$h_min\"> </td>";
			echo "<tr><td><td>  Horaire max </td><td> <input type=\"texte\" name=\"horaire_max\"   size=\"5\" value=\"$h_max\"> </td>";
			echo "<tr><td><td> Date début </td><td> <input type=\"texte\" name=\"date_debut\" size=\"10\" class=\"calendrier\"  value=\"$date_debut\"> </td> " ;
			echo "<tr><td><td> Date fin </td><td> <input type=\"texte\" name=\"date_fin\" size=\"10\"  value=\"$date_fin\"> </td> " ;
			echo "<tr><td><td> Durée créneau </td>";
			liste_quart_d_heure($duree_creneau);
			echo "<tr><td><td> Nombre </td><td> <input type=\"texte\" name=\"nbre\" size=\"2\"  value=\"$nbre\"> </td> " ;
			echo "<tr><td><td> Horaires Lundi </td><td> <input type=\"texte\" name=\"0_min\"   size=\"5\" value=\"$h_min\"> </td>";
			
			echo "<input type=\"hidden\" name=\"organisme\"  value=\"$user_organisme\"> " ;
			echo "<tr><td><td>  </td><td><input type=\"submit\" id=\"modif_planning\" value=\"".traduire('Modifier')."\" ></form> </td> ";
			}
		echo "</table></div>";	
		
		pied_de_page("");
		}

		?>