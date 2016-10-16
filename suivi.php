<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0trict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<?php include 'header.php';	  ?>
    <head>
	

	<link href="css/dropzone.css" type="text/css" rel="stylesheet" />
	<script src="dropzone.min.js" > </script>
	<script>
	Dropzone.options.myAwesomeDropzone = {
	maxFilesize: TAILLE_FICHIER_dropzone, // MB
	maxFiles: 20, 
	
};


<script type="text/javascript">

window.onload = function() {
	for(var i = 0, l = document.getElementsByTagName('input').length; i < l; i++) {
		if(document.getElementsByTagName('input').item(i).type == 'password') {
			document.getElementsByTagName('input').item(i).setAttribute('autocomplete', 'off');
		};
	};
};

<script>
	</script>	
	
	 <?php

	 
include 'calendrier.php';
include 'general.php';
include 'inc_style.php';	 
	
//		if (isset ($_GET["action"])) $action=$_GET["action"]; else  	$action="";	// inutile
		
		if (isset ($_GET["nom"]))
			{
			$nom = $_GET["nom"];
			echo "<title> $nom </title>";
			}
	     else
			echo "<title> Suivi </title>";
		
		if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > TIME_OUT)) 
			$_SESSION['pass']=false;
		$_SESSION['LAST_ACTIVITY'] = time();
		
		$refr=TIME_OUT+10;

		echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"$refr\">";		
		echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
		echo "</head><body>";
		
	function liste_type($val_init ="" )
		{
		echo "<SELECT name=\"type\"  onChange=\"this.form.submit();\">";
		affiche_un_choix($val_init,"Bénéficiaire");
		affiche_un_choix($val_init,"Bénéficiaire femme");
		echo "</SELECT>";
		}


	
	function nouveau( $nom )
		{
		global $bdd;
		
		$nom=str_replace("\"","",$nom);
		$nom_slash= addslashes2($nom);	

		if (($nom!="") && ($nom!="Mail") && ($nom!="Synth") && (!is_numeric($nom)))
			{
			$r1 = command("SELECT DISTINCT * FROM $bdd WHERE date='0000-00-00' and nom='$nom_slash' ");
			$r2=nbre_enreg($r1); 
			if ($r2==0)
				{
				$user= $_SESSION['user'];
				$modif=time();
				$cmd = "INSERT INTO `$bdd`  VALUES ( '$nom', '', '','','$user','$modif','','')";
				$reponse = command($cmd);
				}
			}
		}

	
		
	function histo($nom,$detail)
		{
		global $bdd;

		$nom_slash= addslashes2($nom);	

		echo "<table>";		
		echo "<tr><td  bgcolor=\"#3f7f00\"> <font color=\"white\">Date </td> <td  bgcolor=\"#3f7f00\"></td><td  bgcolor=\"#3f7f00\"></td><td  bgcolor=\"#3f7f00\"></td> <td bgcolor=\"#3f7f00\" > <font color=\"white\">Auteur</td> ";

		/*
		$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and commentaire<>'' and date='0000-00-00' and pres_repas<>'pda' and pres_repas<>'Age' and pres_repas<>'Mail' and pres_repas<>'Téléphone' and pres_repas<>'nationalie' and pres_repas<>'PE' order by nom DESC "); 
		if ($donnees = fetch_command($reponse) ) 
			{
			$c=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]) );
			echo "<tr><td ></td> <td ><b>Memo FISSA : </b></td><td ></td><td >$c</td> ";
			}
		*/
		$i=0; 
//		if ( $_SESSION['droit']=="R")
//			$date_deb="0000-00-00";
//		else
			$date_deb="2000-00-00";
		if ($detail=="")
			$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date>'$date_deb' and pres_repas<>'pda' and pres_repas<>'reponse' and pres_repas<>'partenaire' order by date DESC "); 
		else
			if ($detail=="Visites")
				$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date>'$date_deb' and pres_repas<>'suivi' and pres_repas<>'pda' and pres_repas<>'reponse' and pres_repas<>'partenaire' order by date DESC "); 
			else
				$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date>'$date_deb' and pres_repas='Suivi' order by date DESC "); 

		$ncolor=0;
		while (($donnees = fetch_command($reponse) ) && ($i<10000))
				{
				if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 	

				$c=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));
				$c=str_replace("$bdd/","",$c);
				$date_jour=$donnees["date"];
				$d3= explode("-",$date_jour);  
				$a=$d3[0];
				$m=$d3[1];
				$j=$d3[2];	
				$d ="$j/$m/$a";
				$act=$donnees["activites"];
				$act=str_replace('#-#','; ',$act);
				$p=$donnees["pres_repas"];	
				$p=str_replace("__upload","Chargement",$p);
				$c=nl2br ($c);
				$user="";
				if ($donnees["user"]!="")
					$user=libelle_user($donnees["user"]);				
			
				if ($p=="Suivi")  
					{
					$r1 = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date='$date_jour' and pres_repas='reponse' "); 
					if ($d1 = fetch_command($r1) ) 
						$rep="Réponse :  <b>".$d1["activites"]."</b>";
					else
						$rep="";

					$r1 = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date='$date_jour' and pres_repas='partenaire' "); 
					if ($d1 = fetch_command($r1) ) 
						{
						$act=$d1["activites"];
						if($act!="---")
							$partenaire=" (Partenaire:  <b>".$d1["activites"]."</b>)";
						else
							$partenaire="";
						}
					else
						$partenaire="";
						
					echo "<tr><td bgcolor=\"$color\"><b><a href=\"suivi.php?".token_ref("accompagnement")."&nom=$nom&date_jour=$d\" > $d </a> </td><td bgcolor=\"$color\">$p </td><td bgcolor=\"$color\"> <b>$act</b> </td><td bgcolor=\"$color\">$rep $partenaire <br>$c </td><td bgcolor=\"$color\">$user "; 

					}
				else
					echo "<tr><td bgcolor=\"$color\"> $d </td><td bgcolor=\"$color\"></td><td bgcolor=\"$color\">$p </td><td bgcolor=\"$color\"> $c </td> <td bgcolor=\"$color\">$user </td>";
				
				// ++++ si activité ajouter liste des participants
				if (strpos($nom_slash,"(A)")>0)
					echo "<td bgcolor=\"$color\">". liste_participants_activite($nom ,$date_jour)  ."</td> ";

				$i++; 		
				}
		echo "</table>";
		}
	
		
	function affiche_rdv($nom)
		{
		global $organisme, $bdd;
		
		if  ((!is_numeric($nom)) && (strpos($nom,"(A)")===false)) 
			{
			echo "<a href=\"rdv.php?nom=$nom\" > <img src=\"images/reveil.png\" width=\"35\" height=\"35\">Rendez-vous de $nom : </a>";
			$i=0;
			$reponse = command("SELECT * FROM r_user,DD_rdv WHERE r_user.organisme='$organisme' and DD_rdv.user='$nom' GROUP BY DD_rdv.date  ORDER BY DD_rdv.date DESC Limit 7"); 
			while ($donnees = fetch_command($reponse) ) 
					{
					if ($i++==0)
						echo "<table>";
					$i++;
					$date=$donnees["date"];	
					$user=$donnees["user"];	
					$d3= explode(" ",$date);
					$date=mef_date_fr($d3[0]);
					$heure=$d3[1];
					$ligne=stripcslashes($donnees["ligne"]);
					$auteur=libelle_user($donnees["auteur"]);
					echo "<tr> <td> $date </td> <td> à $heure </td><td> $ligne </td> <td> $auteur </td> ";
					}
			if ($i==0)
				echo "Aucun rendez-vous enregistré. <hr> ";
			else
				echo "</table><hr>";
			}
		}
		
	function chgt_nom(  $nom, $nouveau)
		{
		global $bdd, $organisme;

		$nouveau=str_replace("(A)","",$nouveau);
		$nouveau=str_replace("(B)","",$nouveau);
		$nouveau=str_replace("(S)","",$nouveau);
		$nouveau=trim(str_replace("(M)","",$nouveau));		
		
		if (($nom!="") && ($nouveau!="") && ($nom!="Synth")&& ($nom!="Mail"))
			{
			if ((strpos( $nom ,'(A)')>0) && (strpos( $nouveau ,'(A)')===false))
				 $nouveau.=" (A)";
			if ((strpos( $nom ,'(B)')>0) && (strpos( $nouveau ,'(B)')===false))
				 $nouveau.=" (B)";	
			if ((strpos( $nom ,'(S)')>0) && (strpos( $nouveau ,'(S)')===false))
				 $nouveau.=" (S)";	
			if ((strpos( $nom ,'(M)')>0) && (strpos( $nouveau ,'(M)')===false))
				 $nouveau.=" (M)";	
		
			$nouveau= addslashes2($nouveau);
			$user= $_SESSION['user'];
			$modif=time();
			$nom_slash= addslashes2($nom);	
			$reponse = command("UPDATE $bdd SET nom='$nouveau' , user='$user', modif='$modif' WHERE nom='$nom_slash' ") ;

			// mise à jour spécifique pour les activités qui 
			$reponse = command("SELECT * FROM $bdd WHERE  (activites like '%$nom%') "); 
			while ($donnees = fetch_command($reponse) )
					{
					$nom_user=stripcslashes($donnees["nom"]);
					$date=$donnees["date"];
					$act=$donnees["activites"];
					$act = str_replace ($nom, $nouveau, $act);
					command("UPDATE $bdd SET activites='$act'  WHERE date='$date' and nom='$nom_user' ") ;
					}

			// Mise à jour du nom dan sla table des rendez vous
			$reponse = command("SELECT *, DD_rdv.idx as idx_msg FROM r_user,DD_rdv WHERE r_user.organisme='$organisme' and DD_rdv.user='$nom' GROUP BY idx_msg "); 
			while ($donnees = fetch_command($reponse) ) 
					{
					$idx_msg=$donnees["idx_msg"];
					command("UPDATE DD_rdv SET nom='$nouveau'  WHERE idx='$idx_msg'  ","x") ;
					}
			}
		return($nouveau);
		}
		
		
	include 'suivi_liste.php';	

	function proposition_suivi( $titre="")
		{
		global  $date_jour, $bdd, $acteur;

		$date_jour2=  mise_en_forme_date_aaaammjj($date_jour);
		$nu=0;
		$l= date('Y-m-d',  mktime(0,0,0 , date("m")-2, date("d"), date ("Y")));
		$l1= date('Y-m-d',  mktime(0,0,0 , date("m")-1, date("d"), date ("Y")));
		$reponse = command("SELECT * FROM $bdd where (date>'$l' and pres_repas='Suivi') or (pres_repas='presence' and qte>'$l1')  group by nom order by nom  "); 
		while ($donnees = fetch_command($reponse) ) 
			{
			$n=$donnees["nom"];
			if ($nu==0) 
				{
				echo "<TABLE><TR> <td></td><td > <div class=\"CSS_titre\"  >";
				echo "<table border=\"0\" >";
				echo "<tr> <td width=\"1000\"> $titre: ";							
				$nu++;
				}		
			$n_court=stripcslashes($n);
			echo "<a href=\"suivi.php?".token_ref("suivi")."&nom=$n\">$n_court</a>; " ;
			}

		if ( $nu!=0) 
			{
			echo "</td>";
			echo "  </table> <P> ";
			fin_cadre();				
			}				
		}
	
	function saisie ($titre,$libelle,$nom,$nom_slash)
		{
		global $bdd;
		
		$val=""; 
		$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='$libelle' "); 
		if ($donnees = fetch_command($reponse))
			$val=$donnees["commentaire"];
		else 
		$val="";

		echo "<td> <b> $titre </b> : </td><td>";
		formulaire("$libelle") ;
		echo param ("nom","$nom");
		echo "<input type=\"text\" name=\"telephone\"  value=\"$val\"  onChange=\"this.form.submit();\">";
		echo "</form> </td>";
		}
		
		
	// $num est le nom du fichier
	// $flag_acces est le code d'accès ( a minima pour répondre aux demandes d'accès des AS)
	function visu_doc_liste($num,$flag_acces="", $sans_lien="")
		{
		global $user_droit,$doc_autorise, $user_lecture;
		
		$taille="50";
		
		$reponse =command("select * from r_attachement where num='$num' ");
		if ($donnees = fetch_command($reponse) ) 
			{
			$ref=$donnees["ref"];
			$date=$donnees["date"];
			$num=$donnees["num"];	
			$l_num=strstr($num,".");
			$type=$donnees["status"];			
			$ident=stripcslashes($donnees["ident"]);	
			$type_org=$type;
			
			echo "<td width=\"25\" align=\"center\" >";
					
			if (est_image($num)) 
				{
				if ((substr($ref,0,1)=="A") &&( $user_lecture!=""))
					{
					if (($doc_autorise=="") || (stristr($doc_autorise, ";$type_org;") === FALSE) )
						{
						if ($flag_acces=="") 
							lien("visu.php?action=visu_image_mini&nom=$num", "visu_image", param ("nom","$num"), "", $taille,"B",$sans_lien, false);
						else
							lien("visu.php?action=visu_image_mini&nom=-$num", "visu_fichier", param ("num","$num").param ("code","$flag_acces"), traduire("Document protégé"), $taille,"B",$sans_lien, false);
						}
					else
						lien("visu.php?action=visu_image_mini&nom=-$num", "visu_image", param ("nom","$num"), "", $taille,"B",$sans_lien, false);
					}
				else
					lien("visu.php?action=visu_image_mini&nom=$num", "visu_image", param ("nom","$num"), "", $taille,"B",$sans_lien, false);

				}
			else
				if (extension_fichier($num)=="pdf")
					{
					if (!file_exists("upload_mini/$num.jpg"))
						{
						$icone_a_afficher="images/fichier.jpg";	
						$icone_a_afficher_cadenas="images/-fichier.jpg";	
						}
					else
						{
						$icone_a_afficher="visu.php?action=visu_image_mini&nom=$num.jpg";
						$icone_a_afficher_cadenas="visu.php?action=visu_image_mini&nom=-$num.jpg";
						}
							
					if ((substr($ref,0,1)=="A") &&( $user_lecture!=""))
						{
							
						if (($doc_autorise=="") ||(stristr($doc_autorise, ";$type_org;") === FALSE) )
							{
							if ($flag_acces=="") 
								lien("$icone_a_afficher", "visu_fichier", param ("num","$num"), "", $taille,"B",$sans_lien, false);
							else 
								lien("$icone_a_afficher_cadenas", "visu_fichier", param ("num","$num").param ("code","$flag_acces"), "", $taille,"B",$sans_lien, false);
							}
						else
							lien("$icone_a_afficher", "visu_fichier", param ("num","$num"), "", $taille,"B",$sans_lien, false);
				
						}
					else
						lien("$icone_a_afficher", "visu_fichier", param ("num","$num"), "", $taille,"B",$sans_lien, false);
			
					}
				else
					lien("images/fichier.png", "visu_doc", param ("num","$num"), "", $taille,"B",$sans_lien, false);
			}
		}

		
		function affiche_choix_sur_filtre($filtre, $limite)
			{
			global $bdd;
			
			echo "<table>";
			
			$champs=" ( pres_repas='Suivi' or pres_repas='pda' or pres_repas='Age'  or pres_repas='Mail' or pres_repas='Partenaire' or pres_repas='adresse' or pres_repas='PE' or pres_repas='nationalite'  and pres_repas='__upload' )"; 
			$filtre1="(date REGEXP '$filtre' or date REGEXP '$filtre' or nom REGEXP '$filtre' or commentaire REGEXP '$filtre' )";
			$nu=0;
			$reponse = command("SELECT * FROM $bdd WHERE $champs and $filtre1 group by nom order by modif DESC  "); 			
			while ($donnees = fetch_command($reponse) ) 
				{
			    $nom =$donnees["nom"];
			    $date =$donnees["date"];
			    $modif =$donnees["modif"];
				$n_court=stripcslashes($nom);
				if ($modif>10000)
					{
					if ($nu==0)
						echo "<tr><td bgcolor=\"#3f7f00\" > <font color=\"white\">Mise à jour </font></td><td bgcolor=\"#3f7f00\"> <font color=\"white\"> Bénéficiaire </font> </td>";
						
					if (($nu++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 	
					
					$date=date ("d/m/Y H:i",$modif);	
					echo "<tr> <td bgcolor=\"$color\"> $date </td>";
					echo "<td bgcolor=\"$color\"> <a href=\"suivi.php?".token_ref("suivi")."&nom=$nom\">$n_court</a> </td>";
					
					if ($nu==$limite) 
						{
						echo "<tr><td> - - - </td>";
						echo "<td> Liste limitée à $limite  réponses</td>";
						break;
						}
					}
				}
			if ($nu==0)
				echo "Aucune information ne contient cette information.";				
			echo "</table>";
			}
			
		function affiche_mes_actions($filtre,$limite)
			{
			global $bdd;
			
			echo "<table>";
			$nu=0;
			$user=$_SESSION['user'];
			$reponse = command("SELECT * FROM $bdd WHERE user='' group by nom order by modif DESC  "); 			
			while ($donnees = fetch_command($reponse) ) 
				{
			    $nom =$donnees["nom"];
			    $date =$donnees["date"];
			    $modif =$donnees["modif"];
				$n_court=stripcslashes($nom);
				if ($modif>10000)
					{
					if ($nu==0)
						echo "<tr><td bgcolor=\"#3f7f00\" > <font color=\"white\">Mise à jour </font></td><td bgcolor=\"#3f7f00\"> <font color=\"white\"> Bénéficiaire </font> </td>";
						
					if (($nu++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 	
					
					$date=date ("d/m/Y H:i",$modif);	
					echo "<tr> <td bgcolor=\"$color\"> $date </td>";
					echo "<td bgcolor=\"$color\"> <a href=\"suivi.php?".token_ref("suivi")."&nom=$nom\">$n_court</a> </td>";
					
					if ($nu==$limite) 
						{
						echo "<tr><td> - - - </td>";
						echo "<td> Liste limitée à $limite réponses</td>";
						break;
						}
					}
				}
			if ($nu==0)
				echo "Aucune information ne contient cette information.";				
			echo "</table>";
			}
	// -====================================================================== Saisie


	$format_date = "d/m/Y";
	$user_lang='fr';

	// ConnexiondD
	include "connex_inc.php";
	include "ctrl_pays.php";
		
	$token=variable("token");	
	if ($token!="")	
		$action=verifi_token($token,variable("action"));
	else
		$action=variable("action");		
	//$action=variable_s("action");	
	require_once 'cx.php';
	
	$reponse = command("SELECT * FROM fct_fissa WHERE support='$bdd' "); 
	if ((!($donnees = fetch_command($reponse))) || (!$_SESSION['pass']) )
		{
		echo "<a href=\"https://doc-depot.com\">retour sur page d'accueil doc-depot.com</a>";
		}
	else
		if (($_SESSION['droit']=="s") || ($_SESSION['droit']=="p") )
		{
		echo "Compte Inactif: merci de contacter votre responsable pour réactiver votre compte";
		}
		else
		{
		
		$organisme =$donnees["organisme"];
		
		$beneficiaire=$donnees["beneficiaire"];
		if ($beneficiaire=="") $beneficiaire="Bénéficiaires";
			
		$acteur=$donnees["acteur"];
		if ($acteur=="") $acteur="Accueillants";
		
		$libelle=$donnees["libelle"];
		$logo=$_SESSION['logo'];	

		$_SESSION['bene']="";
		
		$memo=variable_s("memo");

		if ($action=="")
			$action="suivi";
		$pda=variable_s("pda");
		$com=variable_s("com");
		$act=variable_s("activites");
		$nom=variable_s("nom");

		$filtre="";
		if ($action=="filtre_suivi")
			$filtre=variable_s("filtre");		
		$filtre_aff=$filtre;
		
		// cas particulier où c'est une date
		if ($filtre!="")
			{
			$d3= explode("/",$filtre);
			if (isset($d3[2]))
				{ 
				if ( (is_numeric($d3[2])) && (is_numeric($d3[1])) && (is_numeric($d3[0])) )
					$filtre=sprintf("%04d-%02d-%02d",$d3[2],$d3[1],$d3[0]);
				}
			else
				if (isset($d3[2]))
					{
					if ((is_numeric($d3[1])) && (is_numeric($d3[0])) )
						{
						if ($d3[1] >2000)
							$filtre=sprintf("%04d-%02d",$d3[1],$d3[0]);
						else
							$filtre=sprintf("%02d-%02d",$d3[1],$d3[0]);
							
						}
					}
			}

		if ($action=="chgt_nom")
			{
			$nouveau=variable_s("nouveau");
			$nom=chgt_nom($nom,$nouveau);
			$action="suivi";
			}


			$date_jour=variable_s("date_jour");		
			if ($date_jour=="")
				$date_jour=date('d/m/Y');
			else
				if ( mise_en_forme_date_aaaammjj($date_jour)=="")
					{
					erreur("Format de date incorrect");
					$action="";
					$date_jour=date('d/m/Y');
					}
			
		if ($action=="nouveau2")
			{
			$nom=str_replace ('(A)','',$nom);
			$nom=str_replace ('(M)','',$nom);
			$nom=str_replace ('(S)','',$nom);
			$nom=str_replace ('(B)','',$nom);
			if (variable_s("type")=="Bénéficiaire femme")
				$nom .= " (F)";
			nouveau2($date_jour,$nom, variable_s("age"),variable_s("nationalite"),false);
			}
		
		$nom_slash= addslashes2($nom);	
		
		// =====================================================================loc IMAGE
		echo "<table border=\"0\" >";	
		echo "<tr> <td> <a href=\"suivi.php\"> <img src=\"images/suivi.jpg\" width=\"140\" height=\"100\"  ></a></td> ";		

		// =====================================================================loc Histo
		echo "<td>Suivi de ";
		formulaire("suivi");
		echo "<SELECT name=nom onChange=\"this.form.submit();\">";
		echo "<OPTION  VALUE=\"\">  </OPTION>";
		if ($filtre=="")
				$reponse = command("SELECT * FROM $bdd WHERE nom<>'Mail' group by nom "); 			
			else
				$reponse = command("SELECT * FROM $bdd WHERE nom<>'Mail' and nom like '%$filtre%' group by nom order by modif desc"); 			
			
		while ($donnees = fetch_command($reponse) ) 
			{
			$sel=$donnees["nom"];				
			if ($sel==$nom)
				echo "<OPTION  VALUE=\"$sel\" selected > $sel </OPTION>";
			else
				echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
			}
		echo "</SELECT></form>";
		if ($nom!="")
			{
			$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and commentaire<>'' and date='0000-00-00' and pres_repas<>'pda' and pres_repas<>'Age' and pres_repas<>'Mail' and pres_repas<>'Téléphone' and pres_repas<>'nationalie' and pres_repas<>'PE' order by nom DESC "); 
			if ($donnees = fetch_command($reponse) ) 
				{
				$c=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]) );
				echo "<br>Memo FISSA : $c ";
				}
			}

			
			formulaire("filtre_suivi");
			echo "<hr><table><tr><td>Filtre </td><td><input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre_aff\" onChange=\"this.form.submit();\"> ";
			echo "</form><td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td>";
			if ($filtre!="")
				lien_c ("images/croixrouge.png", "supp_filtre_suivi","" , traduire("Supprimer"));

			echo "<td> <a href=\"suivi.php?".token_ref("mes_actions")."&filtre=$filtre\" > Mes derniéres activités </a></td>";				
			echo "</table>";
		echo "</td>";
		// =====================================================================loc RAPPORT
		echo "<td width=\"150\"><p><center>";
		
		echo "<ul id=\"menu-bar\">";
		echo "<li><a href=\"stat_suivi.php\" target=_blank>Statistiques</a>";
		echo "</li> </ul> ";		
		
		echo "<p><ul id=\"menu-bar\">";
		echo "<li><a href=\"index.php?".token_ref("dx")."\">Deconnexion</a>";
		if ($_SESSION['droit']=='R') 
			{
			echo "<ul ><li><a href=\"export_suivi.php\" target=_blank>Export des Suivis</a> ";
			echo "<li><a href=\"export_bene.php\" target=_blank>Export des Bénéficiaires</a></ul >";
			}
		echo "</li> </ul> ";


		
		$reponse = command("SELECT * FROM $bdd WHERE  pres_repas='présence' "); 
			if ($donnees = fetch_command($reponse))
				echo "<p><a href=\"hebergement.php\"  target=_blank > Planning Hébergement </a> ";
		echo "</td>";
		echo "<td><a href=\"index.php\"><img src=\"images/logo.png\" width=\"70\" height=\"50\"><a></td>";			
		echo "<td><a href=\"fissa.php\"><img src=\"images/fissa.jpg\" width=\"70\" height=\"50\"><a></td>";			
		echo "<td><a href=\"rdv.php\"><img src=\"images/rdv.jpg\" width=\"70\" height=\"50\"><a></td>";			
		//echo "<td><a title=\"Alerte Grand Froid/Forte Pluie\" href=\"alerte.php\"><img src=\"images/logo-alerte.jpg\" width=\"70\" height=\"50\"></a> ";
		if ($logo!="")
			echo "<td> <img src=\"images/$logo\" width=\"200\" height=\"100\"  >  </td>";
		echo "</center></td>";	
		echo "</table><hr> ";				
			
		ajout_log_jour("----------------------------------------------------------------------------------- [ SUIVI = $action ] $date_jour ");
		$date_jour_gb=mise_en_forme_date_aaaammjj( $date_jour);		
		

		if ($nom!="")
			{

			if ($action=="compte_dd")
				{
				$user= $_SESSION['user'];
				$modif=time();
				$compte_dd=variable_s("compte_dd");
				command("UPDATE $bdd set activites='$compte_dd' , modif='$modif', user='$user' where date='0000-00-00' and nom='$nom_slash' and pres_repas='' ");
				$action="suivi";
				}
							
			if ($action=="activites")
				{
				$activites=variable_s("activites");
				$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour_gb' and nom='$nom_slash' and pres_repas='Suivi' "); 
				$user= $_SESSION['user'];
				$modif=time();
				if ($donnees = fetch_command($reponse))
					$reponse = command("UPDATE $bdd set activites='$activites' , modif='$modif', user='$user' where date='$date_jour_gb' and nom='$nom_slash' and pres_repas='Suivi' ");
				else
					$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash',  '$date_jour_gb', 'Suivi','','$user','$modif','$activites','')");					
				$action="suivi";
				}	
				
			if (($action=="reponse")|| ($action=="partenaire") )
				{

				$activites=variable_s($action);
				$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour_gb' and nom='$nom_slash' and pres_repas='$action' "); 
				$user= $_SESSION['user'];
				$modif=time();
				if ($donnees = fetch_command($reponse))
					$reponse = command("UPDATE $bdd set activites='$activites' , modif='$modif' , user='$user' where date='$date_jour_gb' and nom='$nom_slash' and pres_repas='$action' ");
				else
					$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '$date_jour_gb', '$action','','$user','$modif','$activites','')");					
				$action="suivi";
				}			
				
			if ($action=="releve")
				{
				$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Arrivée courrier' "); 
				$user= $_SESSION['user'];
				$modif=time();
				$ic=0;
				while ($donnees = fetch_command($reponse))
					{
					$date=$donnees["date"];
					$qte=$donnees["qte"];
					$ic+=$qte;
					// Attention : cette commande génére un message d'erreur sur poste local
					$reponse = command("UPDATE $bdd set qte='0', pres_repas='Remise courrier', commentaire='$qte courrier(s) remis le $date_jour_gb', modif='$modif', user='$user' where date='$date' and nom='$nom_slash' and pres_repas='Arrivée courrier' ");
					}
					
				$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '$date_jour_gb', 'Relevé courrier',' $ic courrier(s) remis','$user','$modif','','1')");					
				$action="suivi";
				}
						
			if ($action=="Arrivée courrier")
				{
				$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour_gb' and nom='$nom_slash' and pres_repas='Arrivée courrier' "); 
				$user= $_SESSION['user'];
				$modif=time();
				if ($donnees = fetch_command($reponse))
					{
					$qte=$donnees["qte"];
					$qte++;
					$reponse = command("UPDATE $bdd set qte='$qte' , modif='$modif' , user='$user' where date='$date_jour_gb' and nom='$nom_slash' and pres_repas='Arrivée courrier' ");
					}
				else
					$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '$date_jour_gb', 'Arrivée courrier','','$user','$modif','','1')");					
				$action="suivi";
				}				
						
			if ($action=="présence")
				{
				$date_deb=mise_en_forme_date_aaaammjj( variable_s("date_deb"));
				$date_fin=mise_en_forme_date_aaaammjj( variable_s("date_fin"));
				$presence_comment=variable_s("commentaire");
				$idx=variable_s("idx");
				$user= $_SESSION['user'];
				$modif=time();
				if ($idx!="new" )
					{
					if ($date_deb==$date_fin)
						{
						erreur ("Suppression");
						$reponse = command("DELETE FROM `$bdd` where nom='$nom_slash' and pres_repas='présence' and activites='$idx' ");					
						}
					else
						$reponse = command("UPDATE $bdd set commentaire='$presence_comment' ,activites='$date_deb' ,qte='$date_fin' , modif='$modif' , user='$user' where nom='$nom_slash' and pres_repas='présence' and activites='$idx' ");
					}
				else
					{
					if ($idx=="" )
						erreur ("Il faut une date de début");
					else
						if ($date_deb>$date_fin)
							erreur ("Dates incohérentes");
						else	
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'présence','$presence_comment','$user','$modif','$date_deb','$date_fin')");					
						}
				$action="suivi";
				}

			if ($action=="alertecourrier")
				{
				$alerte=variable_s("alerte");
				$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='alertecourrier' "); 
				$user= $_SESSION['user'];
				$modif=time();
				if ($donnees = fetch_command($reponse))
					$reponse = command("UPDATE $bdd set commentaire='$alerte' , date='1111-11-11', modif='$modif' , user='$user' where nom='$nom_slash' and pres_repas='alertecourrier' ");
				else
					$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'alertecourrier','$alerte','$user','$modif','','')");					
				$action="suivi";
				}				
						
			if ($action=="PE")
				{
				$PE=variable_s("PE");
				$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='PE' "); 
				$user= $_SESSION['user'];
				$modif=time();
				if ($donnees = fetch_command($reponse))
					$reponse = command("UPDATE $bdd set commentaire='$PE' ,date='1111-11-11', modif='$modif' , user='$user' where nom='$nom_slash' and pres_repas='PE' ");
				else
					$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'PE','$PE','$user','$modif','','')");					
				$action="suivi";
				}			
						
				if ($action=="adresse")
						{
						$adresse=variable_s("adresse");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='adresse' "); 
						$user= $_SESSION['user'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$adresse' , date='1111-11-11', modif='$modif' , user='$user' where nom='$nom_slash' and pres_repas='adresse' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'adresse','$adresse','$user','$modif','','')");					
						$action="suivi";
						}
				if ($action=="nationalite")
						{
						$nationalite=variable_s("nationalite");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='nationalite' "); 
						$user= $_SESSION['user'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$nationalite' , date='1111-11-11', modif='$modif' , user='$user' where nom='$nom_slash' and pres_repas='nationalite' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'nationalite','$nationalite','$user','$modif','','')");					
						$action="suivi";
						}				
				if ($action=="telephone")
						{
						$telephone=variable_s("telephone");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Téléphone' "); 
						$user= $_SESSION['user'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$telephone' , date='1111-11-11', modif='$modif', user='$user' where nom='$nom_slash' and pres_repas='Téléphone' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'Téléphone','$telephone','$user','$modif','','')");					
						$action="suivi";
						}	 
		
				if ($action=="mail")
						{
						$mail=variable_s("mail");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='mail' "); 
						$user= $_SESSION['user'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$mail' , modif='$modif', date='1111-11-11', user='$user' where nom='$nom_slash' and pres_repas='Mail' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'Mail','$mail','$user','$modif','','')");					
						$action="suivi";
						}
					
					$premiere_visite="---";
					$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and ( pres_repas='Visite' or pres_repas='Visite+repas' ) order by date ASC "); 
					if ($donnees = fetch_command($reponse))
						$premiere_visite=mef_date_fr($donnees["date"]);
					
					
					echo "<table><tr> <td >";
					echo "<ul id=\"menu-bar\">";					
					echo "<li><a href=\"suivi.php?".token_ref("suivi")."&nom=$nom\" > <b> $nom </b> </a></li>";
					echo "</ul></td>";
					if (!(strstr($nom,"(M)")))
						echo "<td> - 1ere visite : $premiere_visite </td>";


					echo "</table> ";					
					

			if ($action=="age")
						{
						$age=variable_s("age");

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Age' "); 
						$user= $_SESSION['user'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$age' , modif='$modif' where nom='$nom_slash' and pres_repas='Age' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '1111-11-11', 'Age','$age','$user','$modif','','')");					
						$action="suivi";
						}	
						
			if ($action=="echeance")
						{
						$echeance=mef_date_BdD(variable_s("echeance"));

						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='pda' "); 
						$user= $_SESSION['user'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set activites='$echeance' , modif='$modif' , user='$user' where nom='$nom_slash' and pres_repas='pda' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '', 'pda','','$user','$modif','$echeance','')");					
						$action="suivi";
						}	
					
			if ($action=="suivi_maj")					
						{
						$date_jour_gb=mise_en_forme_date_aaaammjj( $date_jour);
						$com=addslashes2($com);
						$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour_gb' and nom='$nom_slash' and pres_repas='Suivi' "); 
						
						$user= $_SESSION['user'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$com' , user='$user' , modif='$modif' where nom='$nom_slash' and date='$date_jour_gb' and pres_repas='Suivi' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '$date_jour_gb', 'Suivi','$com','$user','$modif','$act','')");					
						$action="suivi";
						}					
					
				$date_jour_gb=mise_en_forme_date_aaaammjj( $date_jour);
				$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour_gb' and nom='$nom_slash' and pres_repas='Suivi' "); 
				if ($donnees = fetch_command($reponse))
					$com=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));
				else
					$com="";
		
					
					$derniere_maj_pda="";
					
					if ($action!="pda")
						{
						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='pda' "); 
						if ($donnees = fetch_command($reponse))
							{
							$pda=mef_texte_a_afficher( stripcslashes($donnees["commentaire"]));
							if (($donnees["user"]!="") && ($donnees["user"]!= $_SESSION['user']) )
								$lib_user=libelle_user($donnees["user"]);
							else
								$lib_user="";
							$modif="";
							if ($donnees["modif"]!="")
								$modif=date ("d/m/Y H:i",$donnees["modif"]);
							if ($lib_user!="")
								$derniere_maj_pda=" (Derniére modification le $modif par $lib_user).";
							}
						else
							$pda="";
						}
					else
						{
						$pda=addslashes2($pda);
						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='pda'"); 
						
						$user= $_SESSION['user'];
						$modif=time();
						if ($donnees = fetch_command($reponse))
							$reponse = command("UPDATE $bdd set commentaire='$pda' , user='$user' , modif='$modif' where nom='$nom_slash' and pres_repas='pda' ");
						else
							$reponse = command("INSERT INTO `$bdd`  VALUES ( '$nom_slash', '', 'pda','$pda','$user','$modif','','')");					
						$pda=stripcslashes($pda);
						}					
					

					if ($nom!="Synth")
						{

							echo "<TABLE><TR><td > <div class=\"CSS_titre\"  >";
							
							
							echo "<table border=\"0\" >";
							if (!(strstr($nom,"(M)")))
								{
								echo "<TR> <td> ";	
								if (!(strpos($nom,"(A)")>0))
									{
									$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Age' "); 
									if ($donnees = fetch_command($reponse))
										$age=$donnees["commentaire"];
									else 
										$age="";
									echo "<b> Date naissance </b> : </td>";
									echo " <td><TABLE><TR> <td>";
									formulaire("age");
									echo param ("nom","$nom");
									echo "<input type=\"text\" name=\"age\" size=\"10\"  value=\"$age\"  onChange=\"this.form.submit();\">";
									echo "</form> </td> <td>jj/mm/aaaa </td></table> </td>";	
									
									$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='nationalite' "); 
									if ($donnees = fetch_command($reponse))
										$nat=$donnees["commentaire"];
									else 
										$nat="";
									choix_pays($nom, $nat);
									}
									
								$tel="";
								$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Téléphone' "); 
								if ($donnees = fetch_command($reponse))
									$tel=$donnees["commentaire"];
								else 
									$tel="";

								echo "<tr> <td> <b> Portable </b> : ";
								if ( VerifierPortable($tel )  )
										echo "<a href=\"index.php?".token_ref("sms_test_ovh")."&tel=$tel\" > <img src=\"images/sms.png\" width=\"30\" height=\"30\" title=\"Envoyer SMS\"></a>";
								echo "</td><td>";
								formulaire("telephone");
								echo param ("nom","$nom");
								echo "<input type=\"text\" name=\"telephone\"  value=\"$tel\"  onChange=\"this.form.submit();\">";
								echo "</form> ";

								echo "</td>";
								

								$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Mail' "); 
								if ($donnees = fetch_command($reponse))
									$mail=$donnees["commentaire"];
								else
									$mail="";
								echo "<td> <b> Mail </b> : ";
								if ( VerifierAdresseMail($mail )  )
										echo "<a href=\"index.php?".token_ref("mail_test")."&mail=$mail\" > <img src=\"images/mail2.png\" width=\"30\" height=\"30\" title=\"Envoyer Mail\"></a>";
								echo "</td><td>";
								formulaire("mail");
								echo param ("nom","$nom");
								echo "<input type=\"text\" name=\"mail\" size=\"40\"  value=\"$mail\"  onChange=\"this.form.submit();\">";
								echo "</form></td> ";
								
								
								echo "</td></table>";
								
								echo "<table>";
								$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='adresse' "); 
								if ($donnees = fetch_command($reponse))
									$adresse=$donnees["commentaire"];
								else 
									$adresse="";
								echo "<tr><td> <b> Adresse </b> : </td><td>";
								formulaire("adresse");
								echo param ("nom","$nom");
								echo "<input type=\"text\" name=\"adresse\" size=\"80\"  value=\"$adresse\"  onChange=\"this.form.submit();\">";
								echo "</form> </td>";														
								}
							
							$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='PE' "); 
							if ($donnees = fetch_command($reponse))
								$PE=$donnees["commentaire"];
							else 
								$PE="";
							echo "<tr><td> <b> Commentaire </b> : </td><td>";
							formulaire("PE");
							echo param ("nom","$nom");
							echo "<input type=\"text\" name=\"PE\" size=\"80\"  value=\"$PE\"  onChange=\"this.form.submit();\">";
							echo "</form> </td>";
							echo "</table>";
							
							echo "</td>";
							
							
							
						// =========================================================  DOMICILIATION 
						if ( (!(strstr($nom,"(M)"))) && (!(strstr($nom,"(A)"))) &&	(!(strstr($nom,"(B)"))) && 	(!(strstr($nom,"(S)"))) &&  ($_SESSION['droit']!="P") ) 				
							{
							$date_deb="";
							$date_fin="";
							$presence_comment="";
							$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='Arrivée courrier' "); 

							echo "<td > <TABLE><TR><td > <div class=\"CSS_titre\"  >";
							
							echo "<table>";
							
							echo "<tr><td> </td><td><ul id=\"menu-bar\">";					
							echo "<li><a href=\"suivi.php?".token_ref("suivi")."&nom=$nom\"> Domiciliation </a> ";
							echo "<ul><li><a href=\"suivi.php?nom=$nom&".token_ref("releve")."\">Relevé du courrier</a>";

							$val_init=0;
							$r2 = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='alertecourrier' "); 
							if ($d2 = fetch_command($r2))
								$val_init=$d2["commentaire"];
							if ($val_init>0)
								echo "<li><a href=\"suivi.php?nom=$nom&".token_ref("alertecourrier")."&alerte=\">Désactiver alerte SMS </a>";
							else
								echo "<li><a href=\"suivi.php?nom=$nom&".token_ref("alertecourrier")."&alerte=2\">Activer alerte SMS </a>";
								
							echo "<li><a href=\"suivi.php?nom=$nom&".token_ref("Arrivée courrier")."\">Arrivée d'un courrier (+1)</a>";						

							echo "</ul>  </ul>  </td><tr> ";	
							$ic=0;
							while ($donnees = fetch_command($reponse))
								{
								if ($ic==0) 
									{
									echo "<td ><img src=\"images/lettres.png\" width=\"35\" height=\"35\"></td>";
									echo "<td > Courrier en attente : </td>";
									}

								$d3= explode("-",$donnees["date"]);
								$m=$d3[1];
								$j=$d3[2];
								$courrier=$donnees["qte"];
								echo "<td> $j/$m=$courrier </td>";
								$ic++;
								}						

							
							if ($ic==0) 
								echo "<td><img src=\"images/lettres_vide.png\" width=\"50\" height=\"40\" > </td><td > Pas de courrier en attente.</td>";		

							if ( VerifierPortable($tel )  )
								{
								echo "</table><p><table>";
								$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='alertecourrier' "); 
								if ($donnees = fetch_command($reponse))
									$val_init=$donnees["commentaire"];
								if ($val_init>0)
									echo "<tr><td> Alerte SMS active si courrier depuis 2 semaines </td> ";		
								}								
							echo "<p></table> ";		
							fin_cadre();							
							echo "</td></TABLE>";
							}						
					
					// ==================================================================== upload documents internes
					if ($action=="charge")					
						{
						$_SESSION['nom_suivi']=$nom;
						echo "<ul id=\"menu-bar\">";					
						echo "<li><a href=\"suivi.php?".token_ref("charge")."&nom=$nom\" >Chargement d'images </a></li>";
						echo "</ul><br>";	
						echo "<form action=\"upload_suivi.php\" method=\"POST\"  class=\"dropzone\" id=\"my-awesome-dropzone\" >";
						echo "</form>";	
						pied_de_page();
						}						
					// ==================================================================== rendre visible
					if ($action=="fichier_actif")					
						{
						$fichier=variable_s("f");
						$nom=variable_s("nom");
						$user= $_SESSION['user'];
						$modif=time();
						$reponse = command("UPDATE $bdd set qte='' , user='$user' , modif='$modif' where  nom='$nom' and pres_repas='__upload' and commentaire='$fichier' ");
						$action="gestion_doc";
						}		

				// ==================================================================== supprime 1 document interne
					if ($action=="fichier_inactif")					
						{
						$fichier=variable_s("f");
						$nom=variable_s("nom");
						$user= $_SESSION['user'];
						$modif=time();
						$reponse = command("UPDATE $bdd set qte='-1' , user='$user' , modif='$modif' where  nom='$nom' and pres_repas='__upload' and commentaire='$fichier' ");
						$action="gestion_doc";
						}		

						// ==================================================================== supprime 1 document interne
					if ($action=="supp_fichier")					
						{
						$fichier=variable_s("f");
						$nom=variable_s("nom");
						$user= $_SESSION['user'];
						$modif=time();
						$reponse = command("UPDATE $bdd set qte='-2' , user='$user' , modif='$modif' where  nom='$nom' and pres_repas='__upload' and commentaire='$fichier' ");
						// ATTENTION: faut il supprimer le fichier  .?????
						supp_fichier("suivi/$fichier");
						supp_fichier("suivi_mini/$fichier");
						supp_fichier("suivi_mini/$fichier.jpg");
						$action="gestion_doc";
						}	
					
					// ==================================================================== gére  documents internes
					if ($action=="gestion_doc")					
						{
						// ======================================================================== Visualisation des miniatures des documents internes
						echo "<table ><tr><td> <ul id=\"menu-bar\">";
						echo "<li><a href=\"suivi.php?".token_ref("gestion_doc")."&nom=$nom\" >Documents internes</a></li>";
						echo "</ul></td>";	
						echo "<td> <a href=\"suivi.php?".token_ref("charge")."&nom=$nom\" ><img src=\"images/ajouter.png\" width=\"30\" height=\"30\" > </a></td> ";
					
						$reponse = command("SELECT distinct* FROM $bdd WHERE nom='$nom_slash' and pres_repas='__upload' and qte<>'-2' "); 
						while ($donnees = fetch_command($reponse))
							{
							$fichier=$donnees["commentaire"];
							$date=$donnees["date"];
							$qte=$donnees["qte"];							
							echo "<tr><td>";
							if (extension_fichier($fichier)=="pdf")
								{
								if (file_exists("suivi_mini/$fichier.jpg"))
									{
									//echo "<a href=\"suivi/$fichier\"  target=_blank ><img src=\"suivi_mini/$fichier.jpg\"  width=\"100\" height=\"100\" ><a> ";
									lien("suivi_mini/$fichier.jpg", "visu_suivi", param ("fichier","$fichier"), "", "100","B","", true);
									}
								else
									{
									//echo "<a href=\"suivi/$fichier\"  target=_blank ><img src=\"images/fichier.jpg\"  width=\"100\" height=\"100\" ><a> ";
									lien("images/fichier.jpg", "visu_suivi", param ("fichier","$fichier"), "", "100","B","", true);
									}
								}
							else
								if (est_doc($fichier))
									{
									//echo "<a href=\"suivi/$fichier\"  target=_blank ><img src=\"images/fichier.png\" width=\"100\" height=\"100\" ><a>  ";	
									lien("images/fichier.png", "visu_suivi", param ("fichier","$fichier"), "", "100","B","", true);
									}
								else
									{
									// echo "<a href=\"suivi/$fichier\"  target=_blank ><img src=\"suivi_mini/$fichier\" width=\"100\" height=\"100\" ><a> xxx";
									lien("suivi_mini/$fichier", "visu_suivi", param ("fichier","$fichier"), "", "100","B","", true);
									}
							echo "</td>";
							
							if ($qte!="")
								echo "<td> Masqué: <a href=\"suivi.php?".token_ref("fichier_actif")."&nom=$nom&f=$fichier\" > Rendre visible le fichier<img src=\"images/oui.png\" title=\"Rendre visible le fichier\" width=\"30\" height=\"30\" > </a></td> ";
								else
								echo "<td> Visible  <a href=\"suivi.php?".token_ref("fichier_inactif")."&nom=$nom&f=$fichier\" >Masquer le fichier<img src=\"images/restreint.png\"  title=\"Masquer le fichier\" width=\"30\" height=\"30\" > </a></td> ";

								
							echo "<td> $date</td> ";

							if ($qte!="")
								echo "<td> <a href=\"suivi.php?".token_ref("supp_fichier")."&nom=$nom&f=$fichier\" >Supprimer le fichier <img src=\"images/croixrouge.png\" title=\"Supprimer le fichier\" width=\"30\" height=\"30\" > </a></td> ";
								
							}						
				
						echo "</table>";							

						pied_de_page();
						}	

							
					//+===================================================================Accès à Doc-depot
					if ( (!(strstr($nom,"(M)"))) && (!(strstr($nom,"(A)"))) &&	(!(strstr($nom,"(B)"))) && 	(!(strstr($nom,"(S)"))) &&  ($_SESSION['droit']!="P") ) 				
						{
						echo "<table ><tr>";
						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and date='0000-00-00' and pres_repas='' and activites<>''  "); 
						if ($donnees = fetch_command($reponse))
							{ // compte Doc-depot existe
							$idx=$donnees["activites"];
							
							//
							$_SESSION['user_idx']=$idx;
							
							lien_c ("images/logo.png", "detail_user", param("user","$idx" ), traduire("Accès à Doc-depot") , "50");
							
							$r1 =command("select * from  r_user where idx='$idx' ");
							$d1 = fetch_command($r1) ;
							$flag_acces=$d1["lecture"];
							
							$r1 =command("select * from r_attachement where ref='A-$idx' order by date DESC ");
							while ($d1 = fetch_command($r1) ) 
								{
								$num=$d1["num"];	
								visu_doc_liste($num,$flag_acces);
								}
				
							lien_c ("images/ajouter.png", "draganddrop", "" , traduire("Ajouter un doc") , "30");
							
							}
						else // compte Doc-depot n'existe pas ==> alors  propose création
							{
							echo "<td> <img src=\"images/logo.png\" width=\"60\" height=\"50\" > </td>";

							$nom2="";
							$d3= explode(" ",$nom);  
							$prenom=trim($d3[0]);
							if (isset($d3[1]))
								$nom2=$d3[1];
							if (isset($d3[2]))
								$nom2.=$d3[2];
							
							$nom2=trim(str_replace("(F)","",$nom2));
							echo "<TD>";						
							formulaire ("ajout_beneficiaire","index.php");
							echo param ("fissa","$nom");
							echo param ("nom","$nom2");
							echo param ("prenom","$prenom");
							echo param ("anniv","$age");
							echo param ("fissa_mail","$mail");
							echo param ("fissa_tel","$tel");
							echo param ("fissa_add","$adresse");							
							echo param ("nationalite","$nat");
							echo "<input type=\"submit\"  id=\"nouveau_user\"  value=\"Lui créer un compte Doc-depot\" > </form></td>  ";		
							}
							
						
						// ======================================================================== Visualisation des miniatures des documents internes
						echo "<td> <ul id=\"menu-bar\">";
						echo "<li><a href=\"suivi.php?".token_ref("gestion_doc")."&nom=$nom\" >Documents internes</a></li>";
						echo "</ul></td><td> - </td>";	
					
						$reponse = command("SELECT distinct* FROM $bdd WHERE nom='$nom_slash' and pres_repas='__upload'  and qte='' "); 
						while ($donnees = fetch_command($reponse))
							{
							$fichier=$donnees["commentaire"];
							$date=$donnees["date"];
							
							echo "<td>";
							if (extension_fichier($fichier)=="pdf")
								{
								if (file_exists("suivi_mini/$fichier.jpg"))
//									echo "<a href=\"suivi/$fichier\"  target=_blank ><img src=\"suivi_mini/$fichier.jpg\" title=\"$date\" width=\"50\" height=\"50\" ><a> ";
									lien("suivi_mini/$fichier.jpg", "visu_suivi", param ("fichier","$fichier"), "", "50","B","", true);
								else
//									echo "<a href=\"suivi/$fichier\"  target=_blank ><img src=\"images/fichier.jpg\" title=\"$date\" width=\"50\" height=\"50\" ><a> ";
									lien("images/fichier.jpg", "visu_suivi", param ("fichier","$fichier"), "", "50","B","", true);
								}
							else
								if (est_doc($fichier))
//									echo "<a href=\"suivi/$fichier\"  target=_blank ><img src=\"images/fichier.png\" title=\"$date\" width=\"50\" height=\"50\" ><a>  ";			
									lien("images/fichier.png", "visu_suivi", param ("fichier","$fichier"), "", "50","B","", true);
								else
//									echo "<a href=\"suivi/$fichier\"  target=_blank ><img src=\"suivi_mini/$fichier\" title=\"$date\" width=\"50\" height=\"50\" ><a> ";
									lien("suivi_mini/$fichier", "visu_suivi", param ("fichier","$fichier"), "", "50","B","", true);

							echo "</td>";
							}
						echo "<td> <a href=\"suivi.php?".token_ref("charge")."&nom=$nom\" ><img src=\"images/ajouter.png\" width=\"30\" height=\"30\" > </a></td> ";
						
				
						echo "</table>";							
						}

					
					if (($_SESSION['droit']!="P") )
						{		
						// ============================================================================== SUIVI
						echo "<table><tr> <td >";
						echo "<ul id=\"menu-bar\">";					
						echo "<li><a href=\"suivi.php?".token_ref("suivi")."&nom=$nom\" >Suivi du </a></li>";
						echo "</ul> </td>";
						echo "<td> : </td><td>";
						formulaire("suivi");
						echo param ("nom","$nom");
						echo "<input type=\"text\" name=\"date_jour\" size=\"10\"  value=\"$date_jour\"  onChange=\"this.form.submit();\" class=\"calendrier\" >";
						echo "<input type=\"submit\" value=\"Valider date\" > </form> 	</td>   ";					
						echo "</table> ";	
						
						echo "<TABLE><TR><td > <div class=\"CSS_titre\"  >";

						echo " <table border=\"0\" >";
						echo "<tr> <td> <table border=\"0\" >";
						if (!(strstr($nom,"(M)")))
							{						
							echo "<tr> <td> <b> Suivi </b> : </td>";
							if (!(strpos($nom,"(A)")>0))
								{
								// echo "<form method=\"POST\" action=\"suivi.php\">";
								
								$reponse = command("SELECT * FROM $bdd WHERE date='$date_jour_gb' and  nom='$nom_slash' and pres_repas='Suivi' "); 
								if ($donnees = fetch_command($reponse))
									$act=$donnees["activites"];
								else 
									$act="";
								choix_action_suivi($act);
								choix_reponse_suivi($act);
								choix_partenaire_suivi($act);
								}
							}

						echo "</table></td>";
						echo "<tr> <td>";
						formulaire("suivi_maj");
						echo param ("nom","$nom");
						echo param ("date_jour","$date_jour");
						echo "<TEXTAREA rows=\"4\" cols=\"110\" name=\"com\" onChange=\"this.form.submit();\">$com</TEXTAREA>";
						echo "<input type=\"submit\" value=\"Valider texte\" ></td>  ";
						echo "</form> ";
						fin_cadre();	
						
						// ============================================================================== Plan d'action
						
						$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='pda' "); 
						if ($donnees = fetch_command($reponse))
							$echeance=mef_date_fr($donnees["activites"]);
						else
							$echeance="";
						echo "<tr> <td> ";
						echo "<table> <tr><td> <b> Plan d'action en cours </b> </td> <td> - Echéance  : </td><td>";
						formulaire("echeance");
						echo param ("date_jour","$date_jour");
						echo param ("nom","$nom");
						echo "<input type=\"text\" name=\"echeance\" size=\"10\"  value=\"$echeance\"  onChange=\"this.form.submit();\" class=\"calendrier\" >";
						echo "<input type=\"submit\" value=\"Valider date\" > </form> 	</td>   ";
						
						echo "</table></td> ";
						
						echo "<tr> <td>";
						formulaire("pda");
						echo param ("date_jour","$date_jour");
						echo param ("nom","$nom");
						echo "<TEXTAREA rows=\"4\" cols=\"110\" name=\"pda\" onChange=\"this.form.submit();\">$pda</TEXTAREA>";
//						echo "<input type=\"image\" src=\"images/oui.png\" width=\"20\" height=\"20\" title=\"".traduire('Valider le texte')."\" ></td> </form> ";
						echo "<input type=\"submit\" value=\"Valider texte\" > </td> </form> ";
						echo "<tr><td>  $derniere_maj_pda </td> ";
						echo "</table>  ";
						
						echo "</td>";
						echo "</table>  ";
						
						// =========================================================  Présence 
						if ( (!(strstr($nom,"(M)"))) && (!(strstr($nom,"(A)"))) &&	(!(strstr($nom,"(B)"))) && 	(!(strstr($nom,"(S)"))) &&  ($_SESSION['droit']!="P") ) 				
							{
							$date_deb="";
							$date_fin="";
							$presence_comment="";
							$reponse = command("SELECT * FROM $bdd WHERE nom='$nom_slash' and pres_repas='présence' order by activites desc"); 
							echo "<TABLE>";
							echo "<TR><td > <div class=\"CSS_titre\"  >";
							echo "<table>";
							echo "<tr> <td >";
							echo "<ul id=\"menu-bar\">";					
							echo "<li><a href=\"suivi.php?".token_ref("suivi")."&nom=$nom\" >Hébergement </a></li>";
							echo "</ul> </td>";
							echo "<td> - </td><td> <a href=\"hebergement.php\"  target=_blank > Planning détaillé </a> </td>";
							echo "</table>  ";
	
							echo "<table>";
							$ip=0;
							while ($donnees = fetch_command($reponse))
								{
								$date_deb=$donnees["activites"];
								$date_fin=$donnees["qte"];
								$presence_comment=$donnees["commentaire"];
								$idx=$donnees["date"];
								if  ($ip==0) 
										{
										echo "<tr> <td > Présence </td>";
										echo "<td> du : </td><td>";
										formulaire("présence");
										echo param ("nom","$nom");
										echo param ("idx","new");
										echo "<input type=\"text\" name=\"date_deb\" size=\"10\"  value=\"\"  onChange=\"this.form.submit();\" class=\"calendrier\" >";
										echo "<td> au </td><td>";
										echo "<input type=\"text\" name=\"date_fin\" size=\"10\"  value=\"\"  onChange=\"this.form.submit();\" class=\"calendrier\" >";
										echo "<input type=\"submit\" value=\"Valider dates\" > </td>   ";		
										echo "<td> Commentaire </td><td><input type=\"text\" name=\"commentaire\" size=\"40\"  value=\"\"  onChange=\"this.form.submit();\"></form> 	</td>";						
										}							
								
								if ( 
									($date_fin=="") 
									|| 
									($date_fin>=date('Y-m-d')) 
									)
									{
									
									if ( ($ip==0) && ($date_fin<date('Y-m-d')) )
										{
										$date_deb="";
										$date_deb_org=$date_deb;
										$date_fin="";
										$presence_comment="";
										}
									else
										{
										$date_deb_org=$date_deb;
										$date_deb=mef_date_fr($date_deb);
										$date_fin=mef_date_fr($date_fin);
										}									
									echo "<tr> <td > Présence </td>";
									echo "<td> du : </td><td>";
									formulaire("présence");
									echo param ("nom","$nom");
									echo param ("idx","$date_deb_org");
									echo "<input type=\"text\" name=\"date_deb\" size=\"10\"  value=\"$date_deb\"  onChange=\"this.form.submit();\" class=\"calendrier\" >";
									echo "<td> au </td><td>";
									echo "<input type=\"text\" name=\"date_fin\" size=\"10\"  value=\"$date_fin\"  onChange=\"this.form.submit();\" class=\"calendrier\" >";
									echo "<input type=\"submit\" value=\"Modifier dates\" > </td>   ";		
									echo "<td> Commentaire </td><td><input type=\"text\" name=\"commentaire\" size=\"40\"  value=\"$presence_comment\"  onChange=\"this.form.submit();\"></form> 	</td>";						
									}
								else
									{
									$date_deb=mef_date_fr($date_deb);
									$date_fin=mef_date_fr($date_fin);								
									echo "<tr> <td > Présence </td>";
									echo "<td> du : </td><td> $date_deb <td> au </td><td> $date_fin </td><td></td><td>$presence_comment	</td>";						
									}							
								$ip++;								
								}

								
								if ($ip==0)
									{
									echo "<tr> <td > Présence </td>";
									echo "<td> du : </td><td>";
									formulaire("présence");
									echo param ("nom","$nom");
									echo param ("idx","new");
									echo "<input type=\"text\" name=\"date_deb\" size=\"10\"  value=\"$date_deb\"  onChange=\"this.form.submit();\" class=\"calendrier\" >";
									echo "<td> au </td><td>";
									echo "<input type=\"text\" name=\"date_fin\" size=\"10\"  value=\"$date_fin\"  onChange=\"this.form.submit();\" class=\"calendrier\" >";
									echo "<input type=\"submit\" value=\"Valider dates\" > </td>   ";		
									echo "<td> Commentaire <input type=\"text\" name=\"commentaire\" size=\"40\"  value=\"$presence_comment\"  onChange=\"this.form.submit();\"></form> 	</td>";						
									}
									
							echo "</table> ";	
							fin_cadre();	
							}

			
						if (!(strstr($nom,"(M)")))
							{
							if  (($action=="suivi") || ($action=="pda")) 
								affiche_rdv($nom);		
								
							if ($action=="accompagnement")
									echo "<a href=\"suivi.php?".token_ref("suivi")."&nom=$nom&date_jour=$date_jour\" > ( N'afficher que l'accompagnement )</a>";
								else
									echo "<a href=\"suivi.php?".token_ref("accompagnement")."&nom=$nom&date_jour=$date_jour\" > (Afficher aussi les visites)</a>";
							}
						}						
					}
			if (!(strstr($nom,"(M)")))
				{
				if (($_SESSION['droit']!="P") )
					{						
					if ($action=="accompagnement")
						histo($nom,"");
					else
						histo($nom,"accompagnement");
					}
				else
					histo($nom,"Visites");
				}
			}
		else 
			{

			
			
			if (($filtre!="") && ($action!="mes_actions"))	
				{
				affiche_choix_sur_filtre($filtre,30);
				}
			else
				{
				if ($action=="mes_actions")
					affiche_mes_actions($filtre,30);
				else
					{
						
					proposition_suivi ("Accès rapide");

					echo "<table id=\"dujour\"  border=\"2\" >";
					// =====================================================================loc NOUVEAU
					$age=variable_s("age");
					echo "<tr><td bgcolor=\"#d4ffaa\"><table><tr><td>Prénom / Nom : ";
					formulaire("nouveau2");
					echo param ("date_jour","$date_jour");
					echo "<input type=\"text\" name=\"nom\" size=\"30\" value=\"$nom\"></td>";	
					echo " </td> <td> Date naissance : <input type=\"text\" name=\"age\" size=\"12\" value=\"$age\"> </td> <td> Origine : ";	
					select_pays( "", variable_s("nationalite") );
					echo "</td> <td> ";
					liste_type();
					echo "</td><td></table></td><td bgcolor=\"#d4ffaa\"><input type=\"submit\" value=\"Nouveau\" >  ";
					echo "</form></td> ";
					echo "</table>";			
					$i=0;
					
					// liste des plans d'action ouvert
					$reponse = command("SELECT * FROM $bdd WHERE pres_repas='pda' and (activites<>''  and !(activites like 'terminé'))  order by activites "); 
					while ($donnees = fetch_command($reponse) ) 
						{
						if ($i++==0)
							echo "<P><div class=\"CSSTableGenerator\" > <table border=\"0\" ><tr> <td>Qui </td><td>Echéance </td><td>Plan d'action </td>";
						
						$nom=$donnees["nom"];
						$echeance=$donnees["activites"];
						$pda=$donnees["commentaire"];
						echo "<tr> <td><a href=\"suivi.php?".token_ref("suivi")."&nom=$nom\" >$nom </td>";
						$aujourdhui=date('Y-m-d');
						if ($echeance<$aujourdhui)
							echo "<td bgcolor=\"orange\" >";
						else
							echo "<td>";

						echo mef_date_fr($echeance)."</td><td>$pda</td>";
						}
						
					if ($i!=0)
						echo "</table></div><p><br><p><br>";
					else
						echo "<p><br><p><br><p><br><p><br><p><br><p><br><p><br><p><br>";
				

						
					if ( ($_SESSION['droit']=='R') ||($_SESSION['droit']=='S') )
						{
						// =====================================================================loc CHANGEMENT NOM
						echo "<P> <table border=\"0\" ><tr> <td>Modification d'un nom :</td><td>";
						formulaire("chgt_nom");
						echo "<SELECT name=nom>";
						echo "<OPTION  VALUE=\"\">  </OPTION>";
						$reponse = command("SELECT * FROM $bdd WHERE nom<>'Mail' and nom<>'Synth'  group by nom "); 			
						while ($donnees = fetch_command($reponse) ) 
							{
							$sel=$donnees["nom"];	
							echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
							}
						echo "</SELECT></td>";
						echo "</td> <td> à transformer en </td> <td>";
						echo "<input type=\"text\" name=\"nouveau\" size=\"20\" value=\"\">";	
						echo "<input type=\"submit\" value=\"Mise à jour du nom\" >  ";
						echo " </form> ";
						echo "</table>  ";
						}
					}
				}
				
			}
		}

	
	pied_de_page();
		?>
	
    </body>
</html>
