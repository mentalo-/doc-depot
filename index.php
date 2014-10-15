<?php 

// ------------------------------------------------------
// DOC-DEPOT : COPYRIGTH ADILEOS - Décembre 2013/Octobre 2014

session_start(); 

error_reporting(E_ALL | E_STRICT);

include 'general.php';

	// session de 5 ou 30 minutes selon profil 
	if 	(!isset($_SESSION['droit']) || ($_SESSION['droit']=="S")|| ($_SESSION['droit']=="A")) 
		$to=TIME_OUT;	
	else
		$to=TIME_OUT_BENE;

	if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $to )) 
		{
		// last request was more than 30 minutes ago
		session_unset();     // unset $_SESSION variable for the run-time 
		session_destroy();   // destroy session data in storage
		}	
	$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp


if ( isset($_SESSION['pass']) && ($_SESSION['pass']==true) )
	switch (variable_s('action'))
		{
		case "exporter":	
			include "connex_inc.php";
			include 'include_crypt.php';
			
			$user_idx=$_SESSION['user_idx'];
			$reponse = command( "","SELECT * from  r_user WHERE idx='$user_idx'"); 
			$donnees = mysql_fetch_array($reponse);
			$id=$donnees["id"];
			if (encrypt(variable("pw"))!=$donnees["pw"]) 
				{
				erreur ("Mot de passe Incorrect. ");
				break; 
				}
			
			$id=rand(1000000,999999999999);
			$zip = new ZipArchive(); 
			$j=1;
			if ($zip->open("dir_zip/$id.zip", ZipArchive::CREATE) === true)
				{
				$reponse =command("","select * from r_attachement where  ref='A-$user_idx' or ref='P-$user_idx' ");
				while (($donnees = mysql_fetch_array($reponse) ) && ($j<100))
					{
					$f=$donnees["num"];
					 if(!$zip->addFile('upload/'.$f, $f))
						{
						  echo 'Impossible d&#039;ajouter &quot;'.$f.'&quot;.<br/>';
						}
					$j++; 	
					}
				
				// Ajout d’un fichier avec Notes et SMS
				$zip->addFile('SMS-et-notes.htm');
				$txt="<table>";
				// Ajout direct.
				$reponse =command("","select * from  r_sms where (idx='$user_idx') order by date desc");		
				while ($donnees = mysql_fetch_array($reponse) ) 
					{
					$date=$donnees["date"];	
					$ligne=stripcslashes($donnees["ligne"]);
					$txt.= "<tr> <td>$date </td><td> $ligne </td>";
					}
				$txt.= "</table>";
				$zip->addFromString('SMS-et-notes.htm',$txt );

				
				// Ajout d’un fichier avec Notes et SMS
				$zip->addFile('historique.htm');
				$txt="<table><tr><td> Date:   </td><td> Evénement:</td><td> Acteur:</td>";

				$reponse =command("","select * from  log where (user='$user_idx' ) or (acteur='$user_idx' or acteur='$id') order by date DESC ");		
				while ($donnees = mysql_fetch_array($reponse) ) 
					{
					$date=$donnees["date"];	
					$ligne=stripcslashes($donnees["ligne"]);
					$acteur=$donnees["acteur"];
					$ip=$donnees["ip"];
					if (is_numeric($donnees["user"]))
						$user=libelle_user($donnees["user"]);

					if (($acteur!="") && (is_numeric($acteur) ) )
						$acteur=libelle_user($acteur);
					$txt.= "<tr><td title=\"$ip\">  $date  </td><td> $ligne </td><td> $acteur </td>";
					}
	
				$txt.= "</table>";
				$zip->addFromString('historique.htm',$txt );				
				
				$zip->close();			
				
				ajout_log($user_idx, "Génération d'un fichier de sauvegarde (fonction export)",$user_idx);
				//header('Content-Transfer-Encoding: binary'); //Transfert en binaire (fichier).
				//header('Content-Disposition: attachment; filename="Archive.zip"'); //Nom du fichier.
				//header('Content-Length: '.filesize("dir_zip/$user_idx.zip")+3); 
				//readfile("dir_zip/$user_idx.zip");
				
				header("Location: dir_zip/$id.zip");			

				ajout_log($_SESSION['user'], "Export des fichiers et données du compte",$_SESSION['user']);

				exit();
				}
			break;
			
		case "visu_fichier":
			// Connexion BdD
			include "connex_inc.php";

			$id=rand(1000000,999999999999);
			$fichier=variable_s('num');
			if (variable_s('code')!="")
				{
				if ( est_image($fichier))
					copy("upload_prot/$fichier.pdf","upload_tmp/$id.pdf");
				else
					copy("upload_prot/$fichier","upload_tmp/$id.pdf");
				}
			else
				copy("upload/$fichier","upload_tmp/$id.pdf");
			 
			header("Location: upload_tmp/$id.pdf");			
			$bene= $_SESSION['bene'];
			if ($bene=="") 
				$bene=$_SESSION['user'];
			
			$fichier = substr($fichier,strpos($fichier,".")+1 );

			ajout_log($bene, "Acces au fichier $fichier en lecture",$_SESSION['user']);
			pied_de_page();
			break;

		case "visu_fichier_tmp":
			// Connexion BdD
			include "connex_inc.php";
			include "include_charge_image.php";

			$id=rand(1000000,999999999999);
			$fichier=variable_s('num');
			$code_lecture=variable_s('code');
			if ( est_image($fichier))
				pdfEncrypt ("upload_pdf/$fichier.pdf", $code_lecture, "upload_tmp/$id.pdf",'P');
			else
				pdfEncrypt ("upload/$fichier", $code_lecture , "upload_tmp/$id.pdf",'P');


			header("Location: upload_tmp/$id.pdf");			
			$bene= $_SESSION['bene'];
			if ($bene=="") 
				$bene=$_SESSION['user'];
			
			ajout_log($bene, "Acces au fichier $fichier en lecture",$_SESSION['user']);
			pied_de_page();
			break;

		case "visu_doc":
			// Connexion BdD
			include "connex_inc.php";

			$id=rand(1000,9999);
			$fichier=variable_s('num');
			copy("upload/$fichier","upload_tmp/$id.$fichier");

			header("Location: upload_tmp/$id.$fichier");			
			$bene= $_SESSION['bene'];
			if ($bene=="") 
				$bene=$_SESSION['user'];
			
			ajout_log($bene, "Acces au fichier $fichier en lecture",$_SESSION['user']);
			pied_de_page();
			break;
			
		case "visu_image_mini":

			// Définit le contenu de l'en-tête - dans ce cas, image/jpeg
			header('Content-Type: image/jpeg');
			$im = imagecreatefromjpeg( "upload_mini/".variable_s("nom"));
			imagejpeg($im);

			// Libération de la mémoire
			imagedestroy($im);
			exit();
			break;
	
		case "visu_image":
			// Définit le contenu de l'en-tête - dans ce cas, image/jpeg
			header('Content-Type: image/jpeg');

			// Création d'une image vide et ajout d'un texte
			$im = imagecreatefromjpeg("upload/".variable_s("nom") );
			imagejpeg($im);

			// Libération de la mémoire
			imagedestroy($im);
			exit();
			break;

		case "visu_pdf":			
			header('Content-type: application/pdf');
			header("Content-Disposition: inline; filename=\"".variable_s("nom")."\"");
			readfile("upload_prot/".variable("nom"));
			break;


		default : break;
		}

?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<script language="JavaScript">
function afficheNouveauType(){
    if (document.getElementById('organisme').value=="")
        document.getElementById('ReversBE').style.visibility= "visible";
    else
         document.getElementById('ReversBE').style.visibility="hidden";
}
</script>	


	<link href="css/dropzone.css" type="text/css" rel="stylesheet" />
	<script src="dropzone.min.js" > </script>
	<script>
	Dropzone.options.myAwesomeDropzone = {
	maxFilesize: TAILLE_FICHIER_dropzone, // MB
	maxFiles: 20, 

	  accept: function(file, done) 
		{
        var re = /(?:\.([^.]+))?$/;
        var ext = re.exec(file.name)[1];
        ext = ext.toUpperCase();
        if ( ext == "JPEG" ||  ext == "PDF" ||  ext == "JPG" ||  ext == "PNG" ||  ext == "JPE"||  ext == "DOC"||  ext == "DOCX"||  ext == "VCF") 
            {
            done();
                }
                else { done("Format de fichier non accepté."); }
           }
		};
		
function unmask(truefalse) {
    for (var f in new Array('pwd', 'pwd2')) {
        oldElem = document.getElementById(f);
        elem = document.createElement('input');
        elem.setAttribute('type', (truefalse == true ? 'text' : 'password'));
        elem.setAttribute('value', document.getElementById(f).value);
        elem.id = f;
        oldElem.parentNode.replaceChild(elem, oldElem);
        };
    }
}


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
	//	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	//	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date dans le passé
			
include 'inc_style.php';
include 'include_charge_image.php';
include 'exploit.php';	
include 'include_mail.php';

    echo "<head>";
	echo "<link rel=\"icon\" type=\"image/png\" href=\"images/identification.png\" />";
	echo "<title>Doc-Depot.com </title>";
    echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
	echo "</head><body>";

if (isset($_SESSION['DD_time_out']))
	echo $_SESSION['DD_time_out'];		
// --------------------------------------------------------------------
	function enreg_bug($titre,$descript,$type,$impact,$qui)
		{
		$version= parametre("DD_version_portail") ;
		
		$date_jour=date('Y-m-d');	
		$idx=inc_index("bug");
		$reponse = command("","INSERT INTO `z_bug` VALUES ( '$idx', '$titre', '$descript', '$type', '???','$qui','new','$date_jour','$impact','$version','','') ");
		$message = "Titre : $titre";
		$message .= "<p>Description : $descript";
		$message .= "<p>Type : $type";
		$message .= "<p>Impact : $impact";
		$message .= "<p>Auteur : $qui";
		envoi_mail(parametre('DD_mail_fonctionnel'),"Bug $idx ",$message, true);
		if ($type=="Bug") // les bugs sont aussi remontés à l'exploitant
			envoi_mail(parametre('DD_mail_gestinonnaire'),"Bug $idx ",$message, true);
		ajout_log( "", "Création Bug $idx :$titre /  $type / $impact / $qui", "");
		return ($idx);
		}

		
	function affiche_liste_bug( $cmd)
		{
			echo "</table><div class=\"CSSTableGenerator\" > ";
		
			echo "<table><tr><td > n°  </td><td > Création  </td><td> Titre</td><td> Type</td><td> Etat</td><td> Impact</td><td> Description</td><td> Origine</td><td> Priorité</td><td> Version</td><td> Commentaire</td><td> fonction</td>";
			$reponse =command("",$cmd);		
			while ($donnees = mysql_fetch_array($reponse) ) 
				{
				$idx1=$donnees["idx"];	
				$titre=stripcslashes($donnees["titre"]);
				$descript=stripcslashes($donnees["descript"]);
				$type=$donnees["type"];
				$impact=$donnees["impact"];
				$testeur=stripcslashes($donnees["testeur"]);
				$etat=$donnees["etat"];
				$date=$donnees["date"];
				$domaine=$donnees["domaine"];
				$version=$donnees["version"];
				$commentaire=stripcslashes($donnees["commentaire"]);
				$fonction=stripcslashes($donnees["fonction"]);
				echo "<tr><td><a href=\"index.php?action=modif_bug&idx=".encrypt($idx1)."\">  $idx1 <a></td><td> $date </td><td> $titre </td><td> $type </td><td> $etat </td><td> $impact </td><td> $descript </td><td> $testeur </td><td> $domaine </td><td> $version </td><td> $commentaire </td><td> $fonction </td>";
				}
			echo "</table></div>";
		}
		
	function liste_bug()
			{
			$filtre1=variable("filtre");
			
			if ($filtre1!="")
				$_SESSION["filtre"]=$filtre1;
			else
				if (isset($_SESSION["filtre"]))
					$filtre1=$_SESSION["filtre"];
			
			formulaire ("");
			echo "<table><tr><td><a href=\"index.php?action=bug\"> + Nouveau ticket </a> - </td><td > Filtre : <input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
			echo "</form><td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td>";
			lien_c ("images/croixrouge.png", "supp_filtre","" , "Supprimer");
			echo "</table> ";

			if ($filtre1!="")
				$filtre=" where (etat REGEXP '$filtre1' or date REGEXP '$filtre1'or idx REGEXP '$filtre1' or titre REGEXP '$filtre1' or type REGEXP '$filtre1' or impact REGEXP '$filtre1' or descript REGEXP '$filtre1'or domaine REGEXP '$filtre1' or version REGEXP '$filtre1' or commentaire REGEXP '$filtre1' or fonction REGEXP '$filtre1') and ";
			else
				$filtre=" where ";

			affiche_liste_bug("select * from  z_bug $filtre (etat<>'En production' and etat<>'Abandonné') order by domaine desc");
			echo"<p>";
			affiche_liste_bug("select * from  z_bug $filtre (etat='En production' ) order by version desc");
			echo"<p>";
			affiche_liste_bug("select * from  z_bug $filtre (etat='Abandonné') order by domaine desc");
		
			pied_de_page("x");
			}

		
		function modif_champ_bug($idx, $champ, $valeur)
			{
			$reponse =command("","update z_bug SET $champ = '$valeur' where idx='$idx' ");

			}

	function saisie_champ_bug($idx, $champ, $valeur, $size="")
		{
		$valeur= stripcslashes ($valeur);
		if ($size!="")
			$size = " size=\"$size\" ";
		return ("<form method=\"post\" > <input  type=\"hidden\" name=\"action\" value=\"modif_champ_bug\"/> ".param("idx","$idx").param("champ","$champ")."<input type=\"text\" name=\"valeur\" value=\"$valeur\" $size onChange=\"this.form.submit();\" >  </form> ");
		}

		
	function saisie_champ_bug_area($idx, $champ, $valeur, $size="")
		{
		$valeur= stripcslashes ($valeur);
		return ("<form method=\"post\" ><input  type=\"hidden\" name=\"action\" value=\"modif_champ_bug\"/>".param("idx","$idx").param("champ","$champ")."<TEXTAREA rows=\"5\" cols=\"$size\" name=\"valeur\"  onChange=\"this.form.submit();\" >$valeur</TEXTAREA></form> ");
		}
		
	function liste_type_bug( $idx, $champ, $val_init)
		{
		echo "<form method=\"post\" > <input  type=\"hidden\" name=\"action\" value=\"modif_champ_bug\"/> ".param("idx","$idx").param("champ","$champ");
		echo "<SELECT name=\"valeur\" onChange=\"this.form.submit();\"  >";
		affiche_un_choix($val_init,"Bugs");
		affiche_un_choix($val_init,"Fonctionnel");
		affiche_un_choix($val_init,"Technique");
		affiche_un_choix($val_init,"Sécurité");
		echo "</SELECT></form>";
		}

	function liste_impact_bug( $idx, $champ, $val_init)
		{
		echo "<form method=\"post\" > <input  type=\"hidden\" name=\"action\" value=\"modif_champ_bug\"/> ".param("idx","$idx").param("champ","$champ");
		echo "<SELECT name=\"valeur\" onChange=\"this.form.submit();\"  >";
		affiche_un_choix($val_init,"Très simple");
		affiche_un_choix($val_init,"Simple");
		affiche_un_choix($val_init,"Normal");
		affiche_un_choix($val_init,"Complexe");
		affiche_un_choix($val_init,"Très complexe");
		affiche_un_choix($val_init,"???");

		echo "</SELECT></form>";
		}		
		
	function liste_etat_bug( $idx, $champ, $val_init)
		{
		echo "<form method=\"post\" > <input  type=\"hidden\" name=\"action\" value=\"modif_champ_bug\"/> ".param("idx","$idx").param("champ","$champ");
		echo "<SELECT name=\"valeur\" onChange=\"this.form.submit();\"  >";
		affiche_un_choix($val_init,"New");
		affiche_un_choix($val_init,"Ouvert");
		affiche_un_choix($val_init,"En correction");
		affiche_un_choix($val_init,"A tester");
		affiche_un_choix($val_init,"OK pour MEP");
		affiche_un_choix($val_init,"En production");
		affiche_un_choix($val_init,"Abandonné");
		
		echo "</SELECT></form>";
		}	

	function liste_prioite_bug( $idx, $champ, $val_init)
		{
		echo "<form method=\"post\" > <input  type=\"hidden\" name=\"action\" value=\"modif_champ_bug\"/> ".param("idx","$idx").param("champ","$champ");
		echo "<SELECT name=\"valeur\" onChange=\"this.form.submit();\"  >";
		affiche_un_choix($val_init,"Urgent");
		affiche_un_choix($val_init,"Prioritaire +");
		affiche_un_choix($val_init,"Prioritaire");
		affiche_un_choix($val_init,"Normal +");
		affiche_un_choix($val_init,"Normal");
		affiche_un_choix($val_init,"Nice to have");
		affiche_un_choix($val_init,"Faible");
		echo "</SELECT></form>";
		}		
		
	function modif_bug($idx)
		{
		echo "</table> ";
		echo "<table border=\"2\">";
		$reponse =command("","select * from  z_bug where idx='$idx' ");		
		if ($donnees = mysql_fetch_array($reponse) )
			{
			echo "<tr><td> Numéro </td><td>".$donnees["idx"]."</td>";
			echo "<tr><td> Création</td><td>". $donnees["date"]."</td>";
			// champ modifiables
			echo "<tr><td> Titre</td><td>". saisie_champ_bug($idx,"titre",$donnees["titre"],100)."</td>";
			echo "<tr><td> Description</td><td>". saisie_champ_bug_area($idx,"descript",$donnees["descript"],100)."</td>";
			echo "<tr><td> Type</td><td>";  liste_type_bug($idx,"type",$donnees["type"]);  echo "</td>";
			echo "<tr><td> Impact</td><td>";  liste_impact_bug( $idx,"impact",$donnees["impact"]); echo"</td>";
			echo "<tr><td> Origine</td><td>".  saisie_champ_bug($idx,"testeur",$donnees["testeur"])."</td>";
			echo "<tr><td> Etat</td><td>";  liste_etat_bug($idx,"etat",$donnees["etat"]); echo "</td>";
			echo "<tr><td> Priorité</td><td>"; liste_prioite_bug($idx,"domaine",$donnees["domaine"]); echo "</td>";
			echo "<tr><td> Version</td><td>". saisie_champ_bug($idx,"version",$donnees["version"]) ."</td>";
			echo "<tr><td> Commentaire</td><td>".  saisie_champ_bug_area($idx,"commentaire",$donnees["commentaire"],100)."</td>";
			echo "<tr><td> Fonction</td><td>".  saisie_champ_bug($idx,"fonction",$donnees["fonction"])."</td>";
			}
		else
			erreur("Anomalie inconnue");
		echo "</table>";
		pied_de_page("x");
		}
		
	function enreg_contact($qui, $coordo, $descript)
		{
		// temporaire
		$message = "Auteur : $qui";	
		$message .= "<p>Coordonnées : $coordo";
		$message .= "<p>Description : $descript";
		envoi_mail(parametre('DD_mail_fonctionnel'),"Demande 'Nous contacter' ",$message, true);
		ajout_log( "", "Contact : $qui / $coordo ", "");
		
		}
// ---------------------------------------------------------------------------------------

function maj_mdp_fichier($idx, $pw )
	{
	$j=0;
	
	$reponse =command("","select * from r_attachement where ref='A-$idx'  ");
	while (($donnees = mysql_fetch_array($reponse) ) && ($j<100))
			{
			$num=$donnees["num"];		
			if (est_image($num))
				{
				supp_fichier("upload_prot/$num.pdf");
				pdfEncrypt("upload_pdf/$num.pdf", $pw , "upload_prot/$num.pdf","P" );
				maj_signature("hash_prot", "upload_prot/$num.pdf", $num);
				}	
			else
				if (!est_doc($num))
					{
					supp_fichier("upload_prot/$num");
					pdfEncrypt("upload_pdf/$num", $pw , "upload_prot/$num","P" );
					maj_signature("hash_prot", "upload_prot/".$num, $num);

					}	
			// les doc ne sont pas cryptées
			$j++; 	
			}
	}
	
	// =========================================================== procedures générales
	function  addslashes2($memo)
		{
		 return(addslashes($memo));
		}
	
	function affiche_un_choix($val_init, $val, $libelle="")
		{
		if ($libelle=="")
			$libelle=$val;
		if (( $val_init!=$val) || ($val_init=="") || ($val==""))
				echo "<OPTION  VALUE=\"$val\"> $libelle </OPTION>";
			else
				echo "<OPTION  VALUE=\"$val\" selected> $libelle </OPTION>";
		}

	function affiche_un_choix_2($val_init, $val, $val_aff)
		{
		if (( $val_init!=$val) || ($val_init=="") || ($val==""))
				echo "<OPTION  VALUE=\"$val\"> $val_aff </OPTION>";
			else
				echo "<OPTION  VALUE=\"$val\" selected> $val_aff </OPTION>";
		}
		
	function liste_type( $val_init , $mode="")
		{
		echo "<SELECT name=\"type\" $mode >";
		affiche_un_choix($val_init,"CNI");
		affiche_un_choix($val_init,"Passeport");
		affiche_un_choix($val_init,"Carte de séjour");
		affiche_un_choix($val_init,"Permis conduire");
		affiche_un_choix($val_init,"Pass Navigo");
		affiche_un_choix($val_init,"CAF");
		affiche_un_choix($val_init,"RIB");
		affiche_un_choix($val_init,"Pole Emploi");
		affiche_un_choix($val_init,"Mail");
		affiche_un_choix($val_init,"Autres");
		echo "</SELECT>";
		}
		
	function liste_type_user( $val_init , $mode="")
		{
		echo "<td><SELECT name=\"droit\" $mode >";
		affiche_un_choix($val_init,"R","Responsable");
		affiche_un_choix($val_init,"E","Exploitant");
		affiche_un_choix($val_init,"F","Fonctionnel");
		affiche_un_choix($val_init,"T","Formateur");
		echo "</SELECT></td>";
		}

	function liste_organisme( $val_init, $action=0 )
		{
		if ($action==0)
			echo "<td> <SELECT name=\"organisme\" id=\"organisme\" onChange=\"javascript:afficheNouveauType();\" >";
		else
			echo "<td> <SELECT name=\"organisme\" id=\"organisme\" onChange=\"this.form.submit();\"  >";
		
		affiche_un_choix($val_init,"");
		$reponse =command("","select * from  r_organisme  ");
		while ($donnees = mysql_fetch_array($reponse) ) 
			{
			$organisme=stripcslashes($donnees["organisme"]);
			$idx=$donnees["idx"];
			affiche_un_choix_2($val_init,$idx,$organisme);			
			}
		echo "</SELECT></td>";
		}


	function liste_AS( $organisme )
		{
		$reponse =command("","select * from  r_user where organisme=\"$organisme\" and droit='S'  ");

		echo "<td> <SELECT name=\"nom\"   >";
		affiche_un_choix("","Tous");
		while ($donnees = mysql_fetch_array($reponse) ) 
			{
			$nom=$donnees["nom"];
			$prenom=$donnees["prenom"];
			$idx=$donnees["idx"];
			affiche_un_choix_2("",$idx,"- $nom $prenom");			
			}
	
	
		// recherche des responsables d'organisme
		$reponse =command("","select * from  r_lien where organisme=\"$organisme\"  ");
		while ($donnees = mysql_fetch_array($reponse) ) 
			{
			$idx=$donnees["user"];
			$nom_prenom =  libelle_user($idx);
			// on vérifie aussi qu'il est déjà le référent d'au moins une personne
			$r1 =command("","select * from  r_referent  where nom=\"$idx\"  ");
			if (mysql_fetch_array($r1) ) 			
				affiche_un_choix_2("",$idx,"- $nom_prenom");
			}
		echo "</SELECT></td>";
		}

	function traite_upload($idx, $pw, $user)
		{
		global $id,$action;
		
		$nom1 =  $_FILES["nom"]['tmp_name'];
		$n =  $_FILES["nom"]['name'];

		if (isset ($_POST["ref"])) $ref=$_POST["ref"];else	$ref="";
		if (isset ($_POST["type"])) $type=$_POST["type"];else	$type="";				
		if (isset ($_POST["ident"])) $ident=$_POST["ident"];else	$ident="";		

		charge_image("0",$nom1,$n,$pw,$ref, $ident , $type, $idx, $user);
		$action=variable("retour");
		}

	// $num est le nom du fichier
	// $flag_acces est le code d'accès ( a minima pour répondre aux demandes d'accès des AS)
	function visu_doc($num, $flag_acces, $ordre=1, $sans_lien="")
		{
		global $user_droit,$user_idx,$doc_autorise, $action, $user_lecture;
		
		echo "</form>"; 
		$ordre += ($ordre-1)/4;
		$reponse =command("","select * from r_attachement where num='$num' ");
		if ($donnees = mysql_fetch_array($reponse) ) 
			{
			$ref=$donnees["ref"];
			$date=$donnees["date"];
			$num=$donnees["num"];	
			$l_num=strstr($num,".");
			$type=$donnees["status"];			
			$ident=stripcslashes($donnees["ident"]);	
			$type_org=$type;
			
			if ($ordre%2)
				$c="#d4ffaa"; 
			else
				$c="";
			echo "<td width=\"25%\" align=\"center\"  bgcolor=\"$c\" class=\"bordure_arrondi\">";

			if (($user_droit!="") && (stristr($doc_autorise, ";$type_org;") !== FALSE) )
				{
				echo "<img src=\"images/restreint.png\"  title=\"Document à accès restreint\" width=\"100\"  >";
				echo " <br> Accès restreint <br> <p> $type ";
				return;
				}

			if (est_image($num)) 
				{
				if ((substr($ref,0,1)=="A") &&( $user_lecture!=""))
					{
					if ( $action=="ajout_admin")
						$type="";
					else
						$type="<br>$type";
					
					if (($doc_autorise=="") || (stristr($doc_autorise, ";$type_org;") === FALSE) )
						{
						if ($flag_acces=="") 
							lien("index.php?action=visu_image_mini&nom=-$num", "visu_fichier", param ("num","$num.pdf"), "Document protégé", "","B",$sans_lien);
						else
							lien("index.php?action=visu_image_mini&nom=-$num", "visu_fichier", param ("num","$num").param ("code","$flag_acces"), "Document protégé", "","B",$sans_lien);
						echo " $type <br> $ident  ";
						}
					else
						{
						lien("index.php?action=visu_image_mini&nom=-$num", "visu_image", param ("nom","$num"), "", "","B",$sans_lien);
						echo " $type <br> $ident $num ";
						}
					}
				else
					lien("index.php?action=visu_image_mini&nom=$num", "visu_image", param ("nom","$num"), "", "","B",$sans_lien);
				}
			else
				if (extension_fichier($num)=="pdf")
					{
					if (!file_exists("upload_mini/$num.jpg"))
						{
						$icone_a_afficher="images/fichier.jpg";	
						$icone_a_afficher_cadenas="images/-fichier.jpg";	
						$l_num="<br>$l_num";
						}
					else
						{
						$icone_a_afficher="index.php?action=visu_image_mini&nom=$num.jpg";
						$icone_a_afficher_cadenas="index.php?action=visu_image_mini&nom=-$num.jpg";
						$l_num="";
						}
							
					if ((substr($ref,0,1)=="A") &&( $user_lecture!=""))
						{
						if ( ($user_droit=="") && ($action=="ajout_admin"))
							$type="";
						else
							$type="<br>$type";
							
						if (($doc_autorise=="") ||(stristr($doc_autorise, ";$type_org;") === FALSE) )
							{
							if ($flag_acces=="") 
								lien("$icone_a_afficher", "visu_fichier", param ("num","$num"), "", "","B",$sans_lien);
							else 
								lien("$icone_a_afficher_cadenas", "visu_fichier", param ("num","$num").param ("code","$flag_acces"), "", "","B",$sans_lien);
							echo " $type <br> $ident $l_num ";}

						else
							{
							lien("$icone_a_afficher", "visu_fichier", param ("num","$num"), "", "","B",$sans_lien);
							echo " $type <br> $ident $l_num ";
							}						
						}
					else
						{
						lien("$icone_a_afficher", "visu_fichier", param ("num","$num"), "", "","B",$sans_lien);
						echo " $date : $l_num <br>$type <br> $ident ";
						}				
					}
				else
					{
					lien("images/fichier.png", "visu_doc", param ("num","$num"), "", "","B",$sans_lien);
					echo " $date : $l_num <br>$type <br> $ident";
					}	

		
			if ((($action=="ajout_admin") &&  (substr($ref,0,1)=="A")) ) 
					{
					formulaire ("modif_type_doc");
					echo "<input type=\"hidden\" name=\"num\" value=\"$num\"> " ;
					liste_type( $type_org , " onChange=\"this.form.submit();\"" );
					echo "</form>";
					}
			}
		
		}

		
	function dossier( $ref )
		{
		global $action,$user_droit,$user_idx,$id;
		
		$j=1;
		formulaire ("creer_dossier");

		echo "Ajouter au dossier vos coordonnées ? <input type=\"checkbox\" name=\"add\" >adresse ,<input type=\"checkbox\" name=\"tel\" > téléphone , <input type=\"checkbox\" name=\"mail\" >mail";
		echo "<p>Sélectionnez les fichiers à prendre en compte :<table>";
		$reponse =command("","select * from r_attachement where ref='$ref' order by date DESC ");
		while (($donnees = mysql_fetch_array($reponse) ) && ($j<100))
			{
			$num=$donnees["num"];
			if ((($j-1) % 4)==0)
				echo "<tr>";			
			if (!est_doc($num))
				{
				echo "<td width=\"10\"><input type=\"checkbox\" name=\"d$j\" > </td> ";
				visu_doc($num,0,$j,"X");
				}
			else
				echo "<td width=\"10\"> </td> <td> Non utilisable dans un dossier ";
			echo "</td>";
				$j++; 	

			}

		echo "</table>	";	
		echo "<hr>Commentaire à introduire en 1ere page du dossier : <TEXTAREA rows=\"5\" cols=\"50\" name=\"comment\" ></TEXTAREA>";

		echo "<p><input type=\"submit\" id=\"creer_dossier\" value=\"Constituer le dossier\" ></form>  <HR> ";
		pied_de_page("x");
		}		
	
	function creer_dossier( $ref )
		{
		global $code_lecture, $user_prenom,$user_nom, $id;
		global $user_anniv,	$user_telephone, $user_mail,$user_adresse,$user_organisme;
		
		include 'PDFMerger.php';

		$pdf = new PDFMerger;
		
		supp_fichier("tmp/garde_$ref.pdf");
		$pdf1=new FPDF("P",'mm','A4');
						
		$pdf1->Open();

		// champs facultatifs
		$pdf1->SetAuthor('Doc-Depot.com');
		$pdf1->SetCreator('Doc-Depot.com & Fpdf');
		$pdf1->SetTitle('$ref');
		$pdf1->SetSubject('Dossier');

		$pdf1->SetMargins(10,10);
		$pdf1->AddPage();
		$pdf1->Image("images/logo.png",50,20,50,50,'PNG');
		$pdf1->SetFont('Arial','B',12);
		$date_jour=date('Y-m-d');
		$comment=stripcslashes(variable("comment"));

		$pdf1->ln(70);		
		$pdf1->write(20,"Dossier de $user_prenom $user_nom généré le $date_jour  ");
		$pdf1->ln(12);
		
		if (variable("add")=="on")
			{
			$pdf1->write(20,"Adresse: $user_adresse ");
			$pdf1->ln(12);
			}
			
		if ( (variable("tel")=="on") && ($user_telephone!=""))
			{
			$pdf1->write(20,"Téléphone :$user_telephone ");
			$pdf1->ln(12);
			}

		if ( (variable("mail")=="on") && ($user_mail!=""))
				{
			$pdf1->write(20,"Mail: $user_mail ");
			$pdf1->ln(12);
			}
		$pdf1->ln(12);
		$pdf1->write(12,"Commentaire : $comment");
		
	
		$pdf1->Output("tmp/garde_$ref.pdf");
		
		$pdf->addPDF("tmp/garde_$ref.pdf", 'all');
		$j=1;
		echo "<p> Génération du dossier ";
		$reponse =command("","select * from r_attachement where ref='$ref' order by date DESC ");
		while (($donnees = mysql_fetch_array($reponse) ) && ($j<100))
			{
			$num=$donnees["num"];
			$val=variable("d$j");

			if ($val=="on")
				{
				if (extension_fichier($num)=="pdf")
					$pdf->addPDF("upload_pdf/$num", 'all');
				else
					$pdf->addPDF("upload_pdf/$num.pdf", 'all');
				}
			$j++; 	
			}

		supp_fichier("tmp/_$ref.pdf");
		$pdf->merge('file', "tmp/_$ref.pdf");
		supp_fichier("tmp/$ref.pdf");
		pdfEncrypt ("tmp/_$ref.pdf", decrypt( $code_lecture ), "tmp/$ref.pdf",'P');
	
		echo " Ok. <p>Cliquez  <a href=\"tmp/$ref.pdf\"target=_blank > ici <img src=\"images/-fichier.jpg\"  title=\"Document protégé\" width=\"100\"  > </a> pour ouvrir le fichier avec code de lecture (recommandé) ";
		echo " ou cliquez <a href=\"tmp/_$ref.pdf\"target=_blank > ici <img src=\"images/-fichier.jpg\"  title=\"Document NON protégé\" width=\"40\" height=\"40\" ></a> pour accèder au fichier SANS code de lecture (non- recommandé). ";

		echo " <p><BR><p> Attention le fichier ne sera plus accessible dès que vous aurez quitté cette page: ";
		echo " <BR> - Soit vous consultez maintenant le document (cliquez sur le lien et saisissez le code de lecture ";
		echo " <BR> - Soit vous imprimez maintenant le document (ouvrir le fichier en cliquant sur le lien, saisissez le code de lecture et faire 'Imprimer') ; ";
		echo " <BR> - Soit vous enregistrer maintenant le fichier sur votre disque (clic droit sur le lien et faire 'enregistrer la cible du lien sous' ) ";

		echo " <p><BR><p> Pour lire le fichier, il est nécessaire d'utiliser des logiciels tels que <a href=\"http://get.adobe.com/fr/reader/\">Acrobat Reader </a>, <a href=\"http://www.foxitsoftware.com/Secure_PDF_Reader/\">Foxit Reader </a>, etc. ";

		ajout_log( $id, "Génération dossier : $comment", $id );

		pied_de_page("x");
		}
		
	function bouton_upload($ref,$idx)
		{
		global $action,$user_droit,$user_lecture,$user_idx,$id;
			
		$flag_acces="";
		$date_jour=date('Y-m-d');
		$nb_doc=0;
		
		$reponse =command("","select * from r_attachement where ref='$ref'  ");
		while (($donnees = mysql_fetch_array($reponse) ) && ($nb_doc<100))
			$nb_doc++; 	
			
		$j=1;

		echo "<table>";
		echo "<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"upload\"> " ;
		echo "<input type=\"hidden\" name=\"retour\" value=\"$action\"> " ;
		echo "<tr> ";
		if (substr($ref,0,1)=="A")
			{
			$reponse =command("","select * from  r_user where idx='$idx' ");
			$donnees = mysql_fetch_array($reponse) ;
			$recept_mail=$donnees["recept_mail"];	

			$reponse =command("","select * from r_dde_acces where type='A' and user='$idx' and date_dde='$date_jour'");
			if ($donnees = mysql_fetch_array($reponse) )
				{
				$code=$donnees["code"];	
				if ($code=="")
					$code="????";
				}
			else 
				$code="";
				
			echo "<td> <img src=\"images/papier.png\" width=\"35\" height=\"35\" > </td>";
			
			echo "<td><ul id=\"menu-bar\">";
			echo "<li><a href=\"index.php?action=ajout_admin\"  > + Papier Administratif  </a>";
			$_SESSION['user_idx']=$idx;
			
			echo "<ul >";
		
			if ($recept_mail<$date_jour)
				echo "<li><a id=\"depot\" href=\"index.php?action=recept_mail\">Autoriser dépot par Mail (Aujourd'hui)</a></li>";
			
			if ($user_droit=="")
				echo "<li><a href=\"index.php?action=visu_lecture\">Code de lecture </a></li>";

			if ($user_droit=="") 
				echo "<li><a href=\"index.php?action=dossier\"> Constituer un dossier </a></li>";

			
			if ($user_droit!="")
				{
				if ($code=="")
					echo "<li><a href=\"index.php?action=dde_acces\">Demander code d'accès</a></li>";
				else
					if ($code=="????")
						echo "<li> - En attente d'autorisation d'accès par responsable</li>";
					else
						{
						echo "</td>";
						echo "<td> Accès Autorisé par ".libelle_user($donnees["autorise"])."</td>";
						$flag_acces=$code;
						}
				}
			else
				$flag_acces=$user_lecture;
			echo "</ul></ul></td>";
			}
		else
			{
			echo "<td> <img src=\"images/photo.png\" width=\"35\" height=\"35\" > </td>";
			echo "<td><ul id=\"menu-bar\">";
			if ($user_droit=="")
				echo "<li><a href=\"index.php?action=ajout_photo\" > + Espace Perso </a></li>";
			else
				echo "<li><a href=\"index.php?action=ajout_photo\" > + Justificatifs </a></li>";

			echo "</ul></td>";
			}
		
		if (substr($ref,0,1)=="A")
			if ($recept_mail>=$date_jour)
				{
				echo "<td> - Réception de document par mail autorisé pour la journée. ";
				lien ("images/croixrouge.png", "supp_recept_mail", param("idx","$idx" ),"Annuler l'autorisation." );
				echo "</td>";
				}
		if ((($action=="ajout_admin") &&  (substr($ref,0,1)=="A"))|| ( ($action=="ajout_photo")&&  (substr($ref,0,1)=="P")) ) 
			{
			if ( $nb_doc<MAX_FICHIER)
				{
				if (substr($ref,0,1)=="A")
					echo "<td> <a href=\"index.php?action=draganddrop\"> ";
				else
					echo "<td> <a href=\"index.php?action=draganddrop_p\"> ";

				echo "Dépot par Glisser/Déposer (Drag&drop) <img src=\"images/fichier.png\" width=\"35\" height=\"35\" ><img src=\"images/fleche.png\" width=\"35\" height=\"35\" ><img src=\"images/dossier.png\" width=\"35\" height=\"35\" ></a></td>";
				
				echo "</table> <table><tr><td></td><td>Dépot de fichier unique </td>";
				if ($action=="ajout_admin")
					{
					echo "<td> ";
					liste_type("");
					echo "</td>";
					}
				if ($_SERVER['REMOTE_ADDR']=="127.0.0.1")	
					{
			
					if (substr($ref,0,1)=="A")
							echo "<td> Référence : <input type=\"text\" name=\"ident\" size=\"15\"  value=\"\"></td>  " ;
					echo "<td><input type=\"file\" size=\"50\"  name=\"nom\" >";
					echo "<input type=\"hidden\" name=\"ref\" value=\"$ref\"> " ;
					echo "<input type=\"hidden\" name=\"idx\" value=\"$idx\"> </td> " ;
					echo "<td><input type=\"submit\" id=\"upload\" value=\"Charger\" >  ";
					echo " </form> </td>";
					}
			
				}
			else
				echo "<td>Nombre maximum de fichiers atteind.</td>";
		
			}
		echo " </table>  <table>";
		$reponse =command("","select * from r_attachement where ref='$ref' order by date DESC ");
		while (($donnees = mysql_fetch_array($reponse) ) && ($j<100))
			{
			if ((($j-1) % 4)==0)
				echo "<tr>";
			$num=$donnees["num"];	
			$ref=$donnees["ref"];				
			visu_doc($num,$flag_acces,$j);
			
			echo " <table> <tr>";

			if (($user_droit=="") || (($user_droit=="S") && ( ($donnees["deposeur"]==$user_idx) || ($donnees["deposeur"]=='') ) ) )
				{
				if ((($action=="ajout_admin") &&  (substr($ref,0,1)=="A"))|| ( ($action=="ajout_photo")&&  (substr($ref,0,1)=="P")) ) 
					lien ("images/croixrouge.png", "supp_upload_a_confirmer", param("num","$num" ). param("retour","$action" ),"Supprimer" );
				}
			echo " . ";
			if ($user_droit=="")
				if ((($action=="ajout_admin") &&  (substr($ref,0,1)=="A"))|| ( ($action=="ajout_photo")&&  (substr($ref,0,1)=="P")) ) 
					lien ("images/switch.png", "switch", param("num","$num" ). param("retour","$action" ),"Changer d'espace" );
			echo " . ";
			
			if ((($action=="ajout_admin") &&  (substr($ref,0,1)=="A"))|| ( ($action=="ajout_photo")&&  (substr($ref,0,1)=="P")) ) 
				lien ("images/illicite.png", "illicite", param("num","$num" ). param("retour","$action" ),"Signaler comme illicte" );
			echo " </table> ";

			echo "</td>";
			$j++; 	
			}

		echo "</table>";
		if ($action!="ajout_admin")	
			echo "<HR>";	
		}
		
	function supp_attachement ($num)
		{
		supp_fichier("upload/$num");
		supp_fichier("upload/org.$num");
		supp_fichier("upload_chi/$num.chi");
		supp_fichier("upload_mini/$num");
		supp_fichier("upload_mini/-$num");
		supp_fichier("upload_pdf/$num.pdf");
		supp_fichier("upload_pdf/$num");
		supp_fichier("upload_prot/$num.pdf");
		supp_fichier("upload_prot/$num");
		command("","delete from r_attachement where num='$num' ");
		}
		
	function supp_tous_fichiers($idx)
		{
		$reponse =command("","select * from  r_attachement where ref='A-$idx' or ref='P-$idx'");		
		while ( $donnees = mysql_fetch_array($reponse) ) 
			supp_attachement ($donnes["num"]);
		}
		
	function modif_type_doc($num, $type)
		{
		global $action;
		
		$reponse =command("","update r_attachement SET status = '$type' where num='$num' ");
		$action = "ajout_admin";
		}
	
	FUNCTION nouveau_referent($user,$organisme,$nom,$prenom,$tel,$mail,$adresse)
		{
		global $action,$user_idx;
		
		$action="ajout_referent";
		if ( 
			(($organisme=="") and ($nom!="") and ( ($prenom!="") || ($tel.$adresse!="")  )  )  
			|| (($organisme!="") and ($nom!="")) 
			) 
			{
			$reponse = command("","select * FROM `r_referent`  where user='$user' and organisme='$organisme' and nom='$nom' and prenom='$prenom' and tel='$tel' and mail='$mail' and adresse='$adresse' ");
			if ($donnees = mysql_fetch_array($reponse))
				{
				erreur("Ce référent de confiance existe déjà.");
				}
			else
				{
				$nb=0;
				$reponse = command("","select * FROM `r_referent`  where user='$user'  ");
				while ($donnees = mysql_fetch_array($reponse)) $nb++;
				
				if ($nb>9) 
					erreur("Nombre maximum (10) de référents atteint.");
				else
					{
					$idx=inc_index("referent");
					$reponse = command("","INSERT INTO `r_referent`  VALUES ( '$idx', '$user', '$organisme', '$nom','$prenom', '$tel','$mail','$adresse')");

					if (is_numeric($nom))
						$nom=libelle_user($nom);

					$organisme = libelle_organisme($organisme);	
					
					ajout_log( $user_idx, "Ajout Référent  $organisme / $nom" );
					}
				}
			}
		}
		
	FUNCTION supp_referent($idx)
		{
		global $user_idx;
		
		$reponse = command("","select * FROM `r_referent`  where idx='$idx' ");
		$donnees = mysql_fetch_array($reponse)	;	
		$idx_ref= $donnees["nom"];
		
		if (is_numeric($idx_ref))
			$lib=libelle_user($idx_ref);
		else
			if ($idx_ref!="Tous")
				$lib =$idx_ref." ".$donnees["prenom"];
			else
				$lib = libelle_organisme($donnees["organisme"]);
		
		$cmd = "DELETE FROM `r_referent`  where idx='$idx' ";
		$reponse = command( "",$cmd);
		ajout_log( $user_idx, "Suppression Référent de confiance $lib " );
		}		

	function visu_referent($idx, $user="", $masque="")
		{
		$reponse =command("","select * from  r_referent where idx='$idx' ");
		$donnees = mysql_fetch_array($reponse);

		$organisme=$donnees["organisme"];

		if ($organisme=="")
			{
			$nom=$donnees["nom"];
			$prenom=$donnees["prenom"];
			$tel=$donnees["tel"];	
			$mail=$donnees["mail"];	
			$adresse=stripcslashes($donnees["adresse"]);	
			echo "<tr><td> $organisme </td><td> $nom   </td><td> $prenom   </td><td> $tel </td><td> $mail</td><td> $adresse</td>";
			}
		else
			{
			$idx=$donnees["nom"];
			if ($idx!="Tous")			
				visu_referent_user($idx, $user, $masque);
			else
				{
				$organisme=libelle_organisme($organisme);
				echo "<tr><td> $organisme </td><td> $idx   </td><td> </td><td> </td><td></td><td> </td>";
				}
			}
		}

	function visu_referent_user($idx, $user="", $masque="")
		{
		$r1 =command("","select * from  r_user where idx='$idx' ");
		$d1 = mysql_fetch_array($r1);
		$nom=$d1["nom"];
		$idx2=$d1["idx"];
		$prenom=$d1["prenom"];
		$tel=$d1["telephone"];	
		$mail=$d1["mail"];	
		$droit=$d1["droit"];
		$organisme=libelle_organisme($d1["organisme"]);
		$adresse=adresse_organisme($d1["organisme"]);

		if (($user!="") && ($droit!="s") && ($masque==""))
			{
			$voir =	"<form method=\"POST\" action=\"index.php\" >";
			$voir .="<input type=\"image\" width=\"45\" height=\"45\" src=\"images/contact.png\" title=\"Demander\">";
			$voir .="<input type=\"hidden\" name=\"action\" value=\"recup_mdp\">";
			$voir .="<input type=\"hidden\" name=\"user\" value=\"$user\">";
			$voir .="<input type=\"hidden\" name=\"as\" value=\"$idx2\">";
			$voir .="</form>";
			$mail =$voir; 
			}
		if ($masque!="") 
			$mail="";
		if ($droit=="s")
			echo "<tr><td> $organisme </td><td> <img src=\"images/inactif.png\" title=\"Inactif\" width=\"15\" height=\"15\"> $nom   </td><td> $prenom   </td><td> $tel </td><td> $mail</td><td> $adresse</td>";
		else
			echo "<tr><td> $organisme </td><td> $nom   </td><td> $prenom   </td><td> $tel </td><td> $mail</td><td> $adresse</td>";
		}

	function titre_referent($organisme, $mode="")
		{
		echo "<div class=\"CSSTableGenerator\" ><table> ";
		if ($organisme=="")
			{
			if ($mode=="")
				echo "<tr><td> Structure Sociale </td><td> Nom: </td><td> Prénom: </td><td> Telephone: </td><td> Mail: </td><td> Adresse: </td>" ;
			else
				echo "<tr><td> Structure Sociale </td><td> Nom: </td><td> Prénom: </td><td> Telephone: </td><td>  </td><td> Adresse: </td>" ;
			
			}
		else
			echo "<tr><td> Structure Sociale </td><td> Acteur Social</td>" ;
		}
		
	function bouton_referent($idx)
		{
		global $action,$num_lien;

		echo "<table><tr><td> <img src=\"images/referent.png\" width=\"35\" height=\"35\" > </td><td>  <ul id=\"menu-bar\">";
		if ($action!="detail_user")
			echo "<li><a href=\"index.php?action=ajout_referent\"  > + Référents de confiance </a></li>";
		else
			echo "<li><a > + Référents de confiance </a></li>";
		
		echo "</ul></td></table>";
		
		$organisme=variable("organisme");
		if ($action!="modif_domicile") // cas particulier 
			titre_referent($organisme);
		else
			titre_referent("");
		
		if ($action=="ajout_referent")
			{
			formulaire ("nouveau_referent");
			echo "<tr>";
			liste_organisme("$organisme",1);
			if ($organisme=="")
				{
				echo "<td> <input type=\"texte\" name=\"nom\"   size=\"20\" value=\"\"> </td>";
				echo "<td> <input type=\"texte\" name=\"prenom\"   size=\"20\" value=\"\"> </td>";
				echo "<td> <input type=\"texte\" name=\"tel\"   size=\"10\" value=\"\"> </td>" ;
				echo "<td> <input type=\"texte\" name=\"mail\"   size=\"25\" value=\"\"> </td>" ;
				echo "<td> <input type=\"texte\" name=\"adresse\" size=\"40\"  value=\"\"> </td> " ;
				}
			else
				{
				liste_AS("$organisme");
				echo "<td>  <input type=\"hidden\" name=\"prenom\"    value=\"\"> </td>" ;
				echo "<td>  <input type=\"hidden\" name=\"tel\"   value=\"\"> </td>" ;
				echo "<td> <input type=\"hidden\" name=\"mail\"    value=\"\"> </td>" ;
				echo "<td> <input type=\"hidden\" name=\"adresse\"   value=\"\"> </td> " ;
				}
			echo "<input type=\"hidden\" name=\"user\"  value=\"$idx\"> " ;
			echo "<td><input type=\"submit\" id=\"nouveau_referent\" value=\"Ajouter\" ></form> </td> ";
			}
		
		$nb_rc=0;
		$reponse =command("","select * from  r_referent where user='$idx' ");
		while ($donnees = mysql_fetch_array($reponse) ) 
			{
			$organisme=stripcslashes($donnees["organisme"]);
			if ($organisme!="")
				$nb_rc++;
			$idx=$donnees["idx"];
			visu_referent($idx);
			if ($action=="ajout_referent")
				lien_c ("images/croixrouge.png", "supp_referent_a_confirmer", param("idx","$idx" ), "Supprimer" );
			}
		echo "</table></div>";
		if ($nb_rc==0)
			echo "<p>Vous n'avez pas désigné de référent de confiance appartenant à une structure sociale, nous vous recommandons vivement d'en désigner un, voire plusieurs, pour vous permettre de récupérer votre mot de passe en cas de perte de ce dernier.";
		}
		
	FUNCTION nouveau_user($id,$pw,$droit,$mail,$organisme,$nom,$prenom,$anniv,$telephone,$nationalite,$ville_nat,$adresse,$recept_mail ,$prenom_p,$prenom_m,$code_lecture,$nss)
		{
		global $action,$user_idx,$user_prenom,$user_nom;
		
		$action="ajout_user";
		$date_jour=date('Y-m-d');
		$idx="";
		$reponse = command("","select * from r_user where nom='$nom' and prenom='$prenom' and anniv='$anniv'and ville_nat='$ville_nat'");
		if (!mysql_fetch_array($reponse) )
			{
			if ($code_lecture=="")
				$code_lecture=$pw;		

			$anniv=mef_date($anniv);
			if ($anniv=="")
				echo "Format de date incorrect (doit être jj/mm/aaaa)";
			else
				{
				$nom=mef_nom($nom);
				$prenom=mef_prenom($prenom);
				$prenom_p=mef_prenom($prenom_p);
				$prenom_m=mef_prenom($prenom_m);
				$organisme=mef_prenom($organisme);
				$nationalite=mef_prenom($nationalite);
				$ville_nat=mef_prenom($ville_nat);
				$adresse=mef_prenom($adresse);
				
				if ( ($nom=="") ||($prenom=="") ||($prenom_p=="") ||($prenom_m=="") ||($nationalite=="") ||($ville_nat=="")  )
					erreur( "Attention tous les champs ne sont pas renseignés") ;
				else
					{
					$pw=encrypt($pw);
					$code_lecture=encrypt($code_lecture);					

					if ($droit!="")
						{
						if (($mail!="") || ( VerifierAdresseMail($mail)) )
							{
							if ( ($organisme!="") || ($droit=="R") || ($droit=="E") || ($droit=="F"))
								{
								$idx=inc_index("user");
								
								$plus="";
								if ($telephone[0]=='+')
									$plus='+';
								$telephone = $plus.preg_replace('`[^0-9]`', '', $telephone);
								
								command("","INSERT INTO `r_user`  VALUES (  '$idx', '$id', '$pw','$droit','$mail','$organisme','$nom','$prenom','$anniv','$telephone','$nationalite','$ville_nat','$adresse','$recept_mail' ,'$prenom_p','$prenom_m','$date_jour','$code_lecture','','' ,'' )");
								ajout_log( $idx, "Création utilisateur  $idx / $droit / $nom/ $prenom",	 $user_idx );
									
								$body= "Bonjour, $prenom $nom ";
								$body.= "<p> $user_prenom $user_nom vous a créé un compte sur 'Doc-depot.com': <p>Pour accepter et finaliser la création de votre compte sur 'Doc-depot.com, cliquez sur ce <a id=\"lien\" href=\"".serveur."index.php?action=finaliser_user&idx=".addslashes(encrypt($idx))."\">lien</a> et compléter les informations manquantes.";
								$body .= "<p> <hr> <center> Copyright ADILEOS 2014 </center>";
								// Envoyer mail pour demander saisie pseudo et PW
								envoi_mail($mail,"Finaliser la création de votre compte",$body);
									
								envoi_mail(parametre('DD_mail_gestinonnaire'),"Création du compte $prenom $nom par $user_prenom $user_nom ","",true);
								}
							else
								erreur ("La structure sociale doit être renseignée.");
							}
						else
							erreur ("Format de mail incorrect (ou absent) $mail.");
						}	
					else
						{
						$idx=inc_index("user");
						$reponse = command("","select * from r_user where id='$id' ");
						if ( (!mysql_fetch_array($reponse) ) || ($id!="jm") || ($id!="jean-michel.cot")|| ($id!="jm.cot") || ($id!="contact")|| ($id!="fixeo"))
							{
							command("","INSERT INTO `r_user`  VALUES (  '$idx', '$id', '$pw','$droit','$mail','$organisme','$nom','$prenom','$anniv','$telephone','$nationalite','$ville_nat','$adresse','$recept_mail' ,'$prenom_p','$prenom_m','$date_jour','$code_lecture','$nss','','')");
							ajout_log( $idx, "Création compte Bénéficiaire  $idx / $nom/ $prenom", $user_idx );
							}
						else
							{
							$idx="";
							erreur ("Identifiant déjà existant");
							}
						}
					}
				}
			}
			else
				erreur ("Utilisateur déja existant.");	
		return($idx);
		}

	FUNCTION mail_user($idx)
		{
		$reponse = command("","select * from r_user where idx='$idx' ");
		$d1 = mysql_fetch_array($reponse);
		return ($d1["mail"]);
		}
	FUNCTION telephone_user($idx)
		{
		$reponse = command("","select * from r_user where idx='$idx' ");
		$d1 = mysql_fetch_array($reponse);
		return ($d1["telephone"]);
		}
		
	FUNCTION maj_user($idx,$id,$pw,$mail,$nom,$prenom,$telephone)
		{
		global $action;
		
		$reponse = command("","select * from r_user where id='$id' ");
		if (!mysql_fetch_array($reponse) )
			{
			if (strlen($id)>7 )
				{
				if (strlen($pw)>7 )
					{
					if ($pw!=$id ) // testpassword($mdp)
						{
						if ( testpassword($pw)>65 ) 
							{
							// si changement d'"id" vérifier qu'il n'existe pas déja
							$pw=encrypt($pw);
							$reponse = command("","UPDATE `r_user` SET id='$id', pw='$pw', nom='$nom', prenom='$prenom',mail='$mail', telephone='$telephone'  where idx='$idx'  ");
							ajout_log( $id, "Finlisation compte $id / $nom / $prenom" );
							$_SESSION['pass']=true;	 
							$_SESSION['user']=$idx;				
							unset($_SESSION['profil']);				
							echo "Compte créé avec succès";
							return(TRUE);
							}
						else 
							erreur("Le mot de passe n'est pas assez complexe (utiliser des Majuscules, Chiffres, caractéres spéciaux)");
						}
					else 
						erreur("Le mot de passe doit être différent de l'identifiant.");
					}
				else 
					erreur("Le mot de passe est trop court (au moins 8 caractères).");
				}
			else 
				erreur("L'identifiant trop court (au moins 8 caractères).");
			}
		else
			erreur ("Identifiant déjà existant");	
		return (false);
		}
			
	FUNCTION modif_tel($idx,$telephone, $mail)
		{
		$plus="";
		if ($telephone[0]=='+')
			$plus='+';
		$telephone = $plus.preg_replace('`[^0-9]`', '', $telephone);
		if ($mail!="")
			if (!VerifierAdresseMail($mail))
				{
				erreur ("Format de mail incorrect");
				return(false);
				}
		if ($telephone!="")
			if (!VerifierTelephone($telephone))
				{
				erreur ("Format de téléphone incorrect");
				return(false);
				}
		$reponse = command("","UPDATE `r_user` SET mail='$mail', telephone='$telephone'  where idx='$idx'  ");
		ajout_log( $idx, "Modification tel : $telephone /mail :$mail" );
		return(true);
		}

	FUNCTION modification_user($idx,$nom, $prenom , $telephone, $mail, $droit)
		{
		global $user_id;
		
		$reponse = command("","UPDATE `r_user` SET mail='$mail', telephone='$telephone', nom='$nom', prenom='$prenom', droit='$droit'  where idx='$idx'  ");
		ajout_log( $idx, "Modification nom/prenom/tel/mail par $user_id" );
		}		
		
	FUNCTION modif_domicile($idx,$organisme, $adresse)
		{
		if ($organisme!="")
			{
			$r1 =command("","select * from  r_organisme where idx='$organisme' ");
			$d1 = mysql_fetch_array($r1);
			$adresse=$d1["adresse"];
			}
		$reponse = command("","UPDATE `r_user` SET organisme='$organisme', adresse='$adresse'  where idx='$idx'  ");
		ajout_log( $idx, "Modification Domiciliation" );
		}
		
	function recept_mail($id,$date)
		{
		global $user_idx;
		
		$reponse = command("","UPDATE `r_user` SET  recept_mail='$date' where idx='$id'  ");
		ajout_log( $id, "Autorisation reception par mail", 	 $user_idx );
		}

	function supp_recept_mail($id)
		{
		global $user_idx;
		
		$reponse = command("","UPDATE `r_user` SET  recept_mail='' where idx='$id'  ");
		ajout_log( $id, "Annulation autorisation reception par mail", 	 $user_idx );
		}
		
	function maj_droit($id,$droit)
		{
		global $user_idx;
		
		$reponse = command("","UPDATE `r_user` SET  droit='$droit' where idx='$id'  ");
		ajout_log( $id, "Mise à jour droit ==> $droit",	$user_idx );
		}
		
	FUNCTION supp_user($idx)
		{
		global $user_idx;

		$reponse =command("","select * from  r_user where idx='$idx'  ");		
		if ($donnees = mysql_fetch_array($reponse))
			{
			$nom=$donnees["nom"];
			$prenom=$donnees["prenom"];	
			$reponse =command("", "DELETE FROM `r_referent` where user='$idx' ");
			$reponse =command("","DELETE FROM `r_lien`  where user='$idx' ");
			$reponse =command("","DELETE FROM `r_user`  where idx='$idx' ");
			ajout_log( $idx, "Suppression compte $nom $prenom ($idx)" ,  $user_idx);
			}
		}		
		
	
		
	function titre_user($droit)
		{
		echo "<tr><td> Nom: </td><td> Prenom:</td><td> Telephone: </td><td> Mail: </td>";
		if ($droit=="A")			
			echo "<td> Structure Sociale </td><td> Droit </td>";
		else
			if (($droit!="R")&&($droit!="F")&&($droit!="E"))
				echo "<td> Anniv: </td><td> Nationalité:  </td><td> Ville natale: </td><td> Adresse </td><td> Structure sociale </td>";
			else
				echo "<td> Structure sociale </td>";
	
		}
		
	function visu_user($idx,$droit)
		{
		global $user_droit;
		
		$reponse =command("","select * from  r_user where idx='$idx'  ");		
				
		if ($donnees = mysql_fetch_array($reponse) ) 
			{
			//$pw=$donnees["pw"];				
			$organisme=stripcslashes($donnees["organisme"]);	

			$r1 =command("","select * from  r_organisme where idx='$organisme' ");
			$d1 = mysql_fetch_array($r1);
			$organisme=stripcslashes($d1["organisme"]);
			
			$mail=$donnees["mail"];	
			$nom=$donnees["nom"];
			$prenom=$donnees["prenom"];				
			$telephone=$donnees["telephone"];	
			$anniv=$donnees["anniv"];	
			$nationalite=$donnees["nationalite"];
			$ville_nat=$donnees["ville_nat"];				
			$id=$donnees["id"];				
			
			$adresse=stripcslashes($donnees["adresse"]);	
			if ( ($user_droit=="A") || ($user_droit=="R"))
				{
				// !!!
				if ($donnees["droit"]=="S")
					$nom=  "<img src=\"images/actif.png\"width=\"20\" height=\"20\"> $nom ";
				if ($donnees["droit"]=="s")
					$nom=  "<img src=\"images/inactif.png\"width=\"20\" height=\"20\"> $nom  ";
					
				if ($id=="???")
					$nom= "$nom (compte non finalisé: <a  id=\"lien\"  href=\"index.php?action=renvoyer_mail&idx=".encrypt($idx)."\"> renvoyer mail</a>)";
				else
					$nom= "$nom (Identifiant='$id')";
				}
			echo "<tr><td> $nom   </td><td> $prenom </td><td> $telephone</td><td> $mail</td>";
			if ($droit=="A")			
				{ 
				echo "<td>".organisme_d_un_responsable($idx)."</td>"; 
				echo "<td>".$donnees["droit"]."</td>"; 
				}
			else
				if ( ($droit!="R") && ($droit!="E") && ($droit!="F") )
					echo "<td> $anniv </td><td> $nationalite   </td><td> $ville_nat </td><td> $adresse </td><td> $organisme </td><td> ".$donnees["droit"]." </td>";
				else
					echo "<td> $organisme </td>";
		
			}
		}
		
	function bouton_user($droit, $organisme, $filtre1="")
		{
		global $action, $user_idx;
		
		if ($filtre1!="")
			$filtre="and (nom REGEXP '$filtre1' or prenom REGEXP '$filtre1' or telephone REGEXP '$filtre1' or mail REGEXP '$filtre1' or anniv REGEXP '$filtre1') ";
		else
			$filtre="";
		echo "<table><tr><td width> <ul id=\"menu-bar\">";
		if ($droit=="A")
			echo "<li><a href=\"index.php?action=ajout_user\"  > + Responsable  </a></li>";
		else
			if ($droit=="R")
				echo "<li><a href=\"index.php?action=ajout_user\"  > + Acteur Social  </a></li>";
			else
				if ($droit=="S")
					{
					echo "<li><a href=\"index.php?action=ajout_user\"  > + Bénéficiaires   </a></li>";
					}
		echo "</ul></td>";
		
		echo "</td><td>";
		formulaire ("");
		echo "<input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
		echo "</form><td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td>";
		if ($filtre1!="")
			lien_c ("images/croixrouge.png", "","" , "Supprimer");

		echo "</td></table>";
				
		echo "<div class=\"CSSTableGenerator\"><table> ";
		
		titre_user($droit);

		if ($action=="ajout_user")
			{
			formulaire ("nouveau_user");
			echo "<tr>";
			echo "<input type=\"hidden\" name=\"id\"   value=\"???\"> </td>";
			echo "<input type=\"hidden\" name=\"pw\"   value=\"123456\">" ;
			echo "<td> <input type=\"texte\" name=\"nom\"   size=\"20\" value=\"\"> </td>" ;
			echo "<td> <input type=\"texte\" name=\"prenom\"   size=\"15\" value=\"\"> </td>" ;
			echo "<td> <input type=\"texte\" name=\"telephone\"   size=\"10\" value=\"\"> </td>" ;
			echo "<td>  <input type=\"texte\" name=\"mail\"   size=\"20\" value=\"\"> </td>" ;
						
			if ($droit!="S")
				{
				echo "<input type=\"hidden\" name=\"anniv\"  value=\"01/01/2000\"> " ;
				echo "<input type=\"hidden\" name=\"nationalite\"  value=\"xxx\"> " ;
				echo "<input type=\"hidden\" name=\"ville_nat\"  value=\"xxx\"> " ;
				echo "<input type=\"hidden\" name=\"adresse\"  value=\"xxx\"> " ;
				}
			else
				{
				echo "<td> <input type=\"texte\" name=\"anniv\"   size=\"10\" value=\"\"> </td>" ;
				echo "<td> <input type=\"texte\" name=\"nationalite\"   size=\"15\" value=\"\"> </td>" ;
				echo "<td>  <input type=\"texte\" name=\"ville_nat\"   size=\"20\" value=\"\"> </td>" ;
				echo "<td> <input type=\"texte\" name=\"adresse\"   size=\"20\" value=\"\"> </td>" ;
				}
			if ($droit=="R")
				liste_organisme_du_responsable ($user_idx);
			else
				if ($droit!="A")
					liste_organisme("");
				else
					echo "<td><input type=\"hidden\" name=\"organisme\"  value=\"\"> </td> ";

			if ($droit=="A")
				//echo "<input type=\"hidden\" name=\"droit\"  value=\"R\"> " ;
				liste_type_user("R");
			else
				if ($droit=="R")
					echo "<input type=\"hidden\" name=\"droit\"  value=\"S\"> " ;
				else
					echo "<input type=\"hidden\" name=\"droit\"  value=\"\"> " ;
			echo "<td><input type=\"submit\" id=\"nouveau_user\" value=\"Ajouter\" > </td> ";				
			echo "<td><input type=\"hidden\" name=\"prenom_p\"  value=\"\"> </td> ";
			echo "<input type=\"hidden\" name=\"prenom_p\"  value=\"???\"> " ;
			echo "<input type=\"hidden\" name=\"prenom_m\"  value=\"???\"> " ;
			echo "<input type=\"hidden\" name=\"code_lecture\"  value=\"\"> " ;
			echo "<input type=\"hidden\" name=\"recept_mail\"  value=\"\"></form> " ;
			}
	
		if ($droit=="R")			
			$reponse =command("","SELECT * FROM `r_lien`, `r_user` WHERE r_user.droit='S' and r_user.organisme=r_lien.organisme and r_lien.user='$user_idx' $filtre  ");
		else
			if ($droit=="A")			
				$reponse =command("","select * from  r_user where droit='R' or droit='E' or droit='F' $filtre  ");
			else
				$reponse =command("","select * from  r_user where droit='' $filtre ");		
				
		while ($donnees = mysql_fetch_array($reponse) ) 
			{
			$idx=$donnees["idx"];
			
			visu_user($idx,$droit);

			if ($action=="ajout_user")
				{
				lien_c("images/croixrouge.png", "supp_user_a_confirmer", param("idx","$idx" ), "Supprimer" );
				lien_c ("images/modifier.jpg", "modifier_user", param("idx","$idx" ), "Modifier" );
				if ($donnees["droit"]=="S")
					lien_c("images/inactif.png", "user_inactif", param("idx","$idx" ), "Rendre Inatcif" );
				if ($donnees["droit"]=="s")
					lien_c("images/actif.png", "user_actif", param("idx","$idx" ), "Rende actif" );
				}
			}
		echo "</table></div>";
		}
			
	function modif_user($idx)
		{
		$reponse =command("","select * from  r_user where idx='$idx'  ");				
		if ($donnees = mysql_fetch_array($reponse) ) 
			{
			//$pw=$donnees["pw"];				
			$organisme=stripcslashes($donnees["organisme"]);	

			$r1 =command("","select * from  r_organisme where idx='$organisme' ");
			$d1 = mysql_fetch_array($r1);
			$organisme=stripcslashes($d1["organisme"]);
			
			$droit=$donnees["droit"];	
			$mail=$donnees["mail"];	
			$nom=$donnees["nom"];
			$prenom=$donnees["prenom"];				
			$telephone=$donnees["telephone"];	
			$adresse=stripcslashes($donnees["adresse"]);	
					
			echo "<hr></table> Modification <p><div class=\"CSSTableGenerator\"><table> ";
			titre_user("R");
			formulaire ("modif_user");
			echo "<tr>";
			echo "<input type=\"hidden\" name=\"idx\"   value=\"$idx\"> </td>";
			echo "<td> <input type=\"texte\" name=\"nom\"   size=\"20\" value=\"$nom\"> </td>" ;
			echo "<td> <input type=\"texte\" name=\"prenom\"   size=\"15\" value=\"$prenom\"> </td>" ;
			echo "<td> <input type=\"texte\" name=\"telephone\"   size=\"12\" value=\"$telephone\"> </td>" ;
			echo "<td>  <input type=\"texte\" name=\"mail\"   size=\"35\" value=\"$mail\"> </td>" ;
			liste_type_user($droit);
			echo "<td><input type=\"submit\"  id=\"modif_user\" value=\"Modifier\" > </td> ";

		echo "</form></table></div>";
		pied_de_page();
		}
	}
	
	function ajout_beneficiaire($idx,$organisme)
		{
		global $action,$user_droit,$user_organisme;
		
		echo "<table> ";

		echo "<tr><td width> <ul id=\"menu-bar\">";
		echo "<li><a href=\"index.php?action=ajout_beneficiaire\"  > + Ajout </a></li>";
		
		echo "</ul></td>";
		echo "</table>";
		
		echo "<center><TABLE><TR> <td  width=\"700\">Important: Tous les  champs sont obligatoires et prennez soin de bien les orthographier et vérifier chaque champ car il n'est plus possible de les modifier. ";
		echo "Les réponses à ces questions vous seront demandées pour récupérer le mot de passe de votre compte, si vous l'avez perdu.<p></td> ";
		
		debut_cadre("700");
		echo "<table>";
		formulaire ("nouveau_user");
		echo "<tr> <td>Identifiant:   </td><td>  <input type=\"texte\" name=\"id\"   size=\"20\" value=\"".variable("id")."\"> </td>";
		echo "<td> Au moins 8 caractères </td>" ;
		echo "<tr> <td> Mot de passe 1ere connexion :</td><td> 123456 </td>" ;
		echo "<input type=\"hidden\" name=\"pw\"  value=\"123456\"> " ;
		echo "<input type=\"hidden\" name=\"droit\"  value=\"\"> " ;
		echo "<tr><td> Nom:   </td> <td><input type=\"texte\" name=\"nom\"   size=\"20\" value=\"".variable("nom")."\"> </td>" ;
		echo "<tr><td> Prenom:</td> <td><input type=\"texte\" name=\"prenom\"   size=\"20\" value=\"".variable("prenom")."\"> </td>" ;
		echo "<tr> <td> Date de naissance: </td><td><input type=\"texte\" name=\"anniv\"   size=\"10\" value=\"".variable("anniv")."\"> </td><td> jj/mm/aaaa</td>" ;
		echo "<tr><td> Ville natale: </td><td>  <input type=\"texte\" name=\"ville_nat\"   size=\"20\" value=\"".variable("ville_nat")."\"> </td>" ;
		echo "<tr> <td> Nationalité:  </td><td><input type=\"texte\" name=\"nationalite\"   size=\"20\" value=\"".variable("nationalite")."\"> </td>" ;
		echo "<input type=\"hidden\" name=\"recept_mail\"  value=\"\"> " ;
		echo "<input type=\"hidden\" name=\"telephone\"  value=\"\"> " ;
		echo "<input type=\"hidden\" name=\"mail\"  value=\"\"> " ;
		echo "<input type=\"hidden\" name=\"organisme\"  value=\"\"> " ;
		echo "<input type=\"hidden\" name=\"adresse\"  value=\"\"> " ;
		echo "<tr><td> Prenom du Pére:</td> <td><input type=\"texte\" name=\"prenom_p\"   size=\"20\" value=\"".variable("prenom_p")."\"> </td>" ;
		echo "<tr><td> Prenom de la Mére:</td> <td><input type=\"texte\" name=\"prenom_m\"   size=\"20\" value=\"".variable("prenom_m")."\"> </td>" ;

		echo "<input type=\"hidden\" name=\"code_lecture\" value=\"\"> " ;
		echo "<input type=\"hidden\" name=\"recept_mail\"  value=\"\"> " ;
		$_SESSION['img_number'] = ""; 
		echo "<input type=\"hidden\" name=\"num\" value=\"\"> " ;
		echo "<tr><td> </td><td><input type=\"submit\"  id=\"nouveau_user\"  value=\"Valider création\" > </td> ";
		echo "</table> En validant cette création, vous confirmez avoir pris connaissance des <a href=\"conditions.html\">conditions d'utilisations </a>. <p>";
		fin_cadre();
		pied_de_page("x");
		}
	
	function verif_existe_user()
		{
		echo "<center><p><br>Saisissez les informations ci-dessous pour vérifier si la personne a déjà un compte.<p> ";
		debut_cadre("500");
		echo "<br><table>";
		formulaire ("verif_user");
		echo "<tr><td> Nom:   </td> <td><input type=\"texte\" name=\"nom\"   size=\"20\" value=\"".variable("nom")."\"> </td>" ;
		echo "<tr><td> Prenom:</td> <td><input type=\"texte\" name=\"prenom\"   size=\"20\" value=\"".variable("prenom")."\"> </td>" ;
		echo "<tr> <td> Date de naissance: </td><td><input type=\"texte\" name=\"anniv\"   size=\"10\" value=\"".variable("anniv")."\"> </td><td> jj/mm/aaaa</td>" ;
		echo "<tr><td> </td><td><input type=\"submit\"  id=\"verif_user\"  value=\"Vérifier\" > </td> ";
		echo "</table><p>";
		fin_cadre();
		pied_de_page("x");
		}
		
	function affiche_sms($filtre1)
		{
		global $user_idx, $action;
		$j=0;
		
		signet("affiche_sms");
		
		if ($filtre1!="")
			$filtre="and (date REGEXP '$filtre1' or ligne REGEXP '$filtre1') ";
		else
			$filtre="";
	
		$num_seq = variable("num_seq");	
		if (($action=="note_sms") and ($num_seq!=""))
			$reponse =command("","DELETE FROM `r_sms`  where idx='$user_idx' and num_seq='$num_seq' ");
		echo "<table> <tr><td> <img src=\"images/sms.png\" width=\"35\" height=\"35\" ></td> ";
		echo "<td> <ul id=\"menu-bar\">";
		echo "<li> <a href=\"index.php?action=note_sms\"  >+ Notes et SMS </a> </li></ul></td>";
		echo "<td> </form>";
		formulaire ("");
		echo "<input type=\"text\" name=\"filtre\" size=\"0\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
		echo "</td><td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td>";
		echo "</form>";
		
		if ($filtre1!="")
			lien_c ("images/croixrouge.png", "", "" , "Supprimer filtre");
		echo "</table> ";
		echo "<div class=\"CSSTableGenerator\" > ";
		echo "<table><tr><td width=\"15%\"> Date   </td><td> texte</td>";		
		if ($action=="note_sms")
			{
			formulaire ("ajout_note");
			echo "<tr><td> </td>";
			echo "<td> <input type=\"texte\" name=\"note\"   size=\"100\" value=\"\"> </td>";
			echo "<input type=\"hidden\" name=\"user\"  value=\"$user_idx\"> " ;
			echo "<td><input type=\"submit\" id=\"ajout_note\" value=\"Ajouter\" ></form> </td> ";
			}
		$reponse =command("","select * from  r_sms where (idx='$user_idx' $filtre ) order by date desc");		
		while ($donnees = mysql_fetch_array($reponse) ) 
			{
			$num_seq=$donnees["num_seq"];	
			$date=$donnees["date"];	
			$d3= explode(" ",$date);
			$date=mef_date_fr($d3[0])." ".$d3[1];
			$ligne=stripcslashes($donnees["ligne"]);
			echo "<tr><td>  $date   </a></td><td> $ligne </td>";
			if ($action=="note_sms")
				lien_c ("images/croixrouge.png", "note_sms", param("num_seq","$num_seq" ) , "Supprimer");
			}
		echo "</table></div>";
		}
	
	function affiche_beneficiaire($libelle,$ligne_cmd,$filtre1="",$filtre2="")
		{
		echo "<table><tr><td> <ul id=\"menu-bar\">";
		echo "<li> <a href=\"index.php?action=ajout_beneficiaire\"  >+ $libelle </a> ";
		echo "<ul> <a href=\"index.php?action=verif_existe_user\"  > Vérifier si bénéficiaire existe déjà</a> </li> </ul>";

		echo "</td><td>";
		formulaire ("");		
		echo "<input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
		echo "</form><td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td>";
		if ($filtre1!="")
			lien_c ("images/croixrouge.png", "", "" , "Supprimer filtre");
		echo "</table><div class=\"CSSTableGenerator\" ><table> ";
		echo "<tr><td>   </td><td> Nom:   </td><td> Prenom:</td><td>  Telephone: </td><td> Mail: </td><td> Anniv: </td><td> Nationalité:  </td><td> Ville natale: </td><td> Adresse </td>";
		
		$reponse =command("",$ligne_cmd);		
		while ($donnees = mysql_fetch_array($reponse) ) 
			{
			if (isset($donnees["user"]))
				$nom1=$donnees["user"];
				else
				$nom1=$donnees["idx"];

			$r1 =command("","select * from  r_user where idx='$nom1' $filtre2 ");
			if ($d1 = mysql_fetch_array($r1))
				{
				
				$mail=$d1["mail"];	
				$nom=$d1["nom"];
				$prenom=$d1["prenom"];				
				$telephone=$d1["telephone"];	
				$anniv=$d1["anniv"];	
				$nationalite=$d1["nationalite"];
				$ville_nat=$d1["ville_nat"];				
				$adresse=stripcslashes($d1["adresse"]);	
				echo "<tr>";
				lien_c ("images/voir.png", "detail_user", param("user","$nom1" ), "Voir le détail" );
				echo "<td>$nom   </a></td><td> $prenom </td><td> $telephone</td><td> $mail</td>";
				echo "<td> $anniv </td><td> $nationalite   </td><td> $ville_nat </td><td> $adresse </td>";
				}
			}
		echo "</table></div>";
		
		}
		
	function bouton_beneficiaire($nom,$organisme,$filtre="")
		{
		global $user_idx;
		
		if ($filtre!="")
			$filtre2="and (nom REGEXP '$filtre' or prenom REGEXP '$filtre' or telephone REGEXP '$filtre' or mail REGEXP '$filtre' or anniv REGEXP '$filtre' or adresse REGEXP '$filtre'or nationalite REGEXP '$filtre' ) ";
		else
			$filtre2="";
		
		$libelle_organisme= libelle_organisme($organisme);
		echo "<table><tr>";
		affiche_beneficiaire("Je suis le référent de ","select * from  r_referent where (nom='$user_idx' or nom='Tous' ) and organisme='$organisme' ", $filtre, $filtre2);		
		}
		
	FUNCTION nouveau_organisme($organisme,$tel,$mail,$adresse,$sigle,$doc)
		{
		global $action,$user_idx;
		
		$action="ajout_organisme";
		
		$r1 =command("","select * from  r_organisme where organisme='$organisme' ");
		if (!($d1 = mysql_fetch_array($r1)))
			{
			$idx=inc_index("organisme");
			if ($doc=="")
				$doc = ";Tous;";
			$cmd = "INSERT INTO `r_organisme`  VALUES ( '$idx','$organisme', '$tel','$mail','$adresse','$sigle','','$doc','1')";
			$reponse = command( "",$cmd);
			ajout_log( $user_idx, "Création organisme ($idx) : $organisme / $mail / $tel / $adresse / $sigle" );
			}
		else
			echo "Structure sociale déjà existante!";
		}
	
	FUNCTION modif_organisme($id,$telephone, $mail, $adresse, $sigle,$doc)
		{
		global $user_idx;
		
		$l=libelle_organisme($id);
		$reponse = command("","UPDATE `r_organisme` SET mail='$mail', tel='$telephone' , adresse='$adresse' , sigle='$sigle', doc_autorise='$doc'  where idx='$id'  ");
		ajout_log( $user_idx, "Modification organisme $l : $mail / $telephone / $adresse / $sigle/ $doc" );
		}	
		
	FUNCTION supp_organisme($idx)
		{
		global $user_idx;
		
		$l=libelle_organisme($idx);
		
		$cmd = "DELETE FROM `r_organisme`  where idx='$idx' ";
		$reponse = command( "",$cmd);
		ajout_log( $user_idx, "Suppresion organisme $l " );
		}		
		
	function libelle_organisme($organisme	)
		{
		$r1 =command("","select * from  r_organisme where idx='$organisme' ");
		$d1 = mysql_fetch_array($r1);
		return(stripcslashes($d1["organisme"]));
		}
	
	function doc_autorise($organisme	)
		{
		$r1 =command("","select * from  r_organisme where idx='$organisme' ");
		$d1 = mysql_fetch_array($r1);
		return($d1["doc_autorise"]);
		}
	
	function adresse_organisme($organisme	)
		{
		$r1 =command("","select * from  r_organisme where idx='$organisme' ");
		$d1 = mysql_fetch_array($r1);
		return(stripcslashes($d1["adresse"]));
		}
		
	function responsables_organisme($organisme)
		{
		$ligne="";
		$r1 =command("","select * from r_lien where organisme='$organisme' ");
		while ($d1 = mysql_fetch_array($r1) ) 
			{
			$ligne= $ligne . libelle_user($d1["user"])."; ";
			}
		return($ligne);
		}		
	
	function titre_organisme()
		{
		global $user_droit;

		echo "<div class=\"CSSTableGenerator\" ><table><tr><td> Structure sociale: </td><td> Sigle: </td><td> Adresse: </td><td> Telephone: </td><td> Mail: </td>" ;
		if ($user_droit=="A")
			echo "<td> Doc restreints: </td><td> Responsables </td>";
		}
		
	function bouton_organisme()
		{
		global $action, $user_droit;

		$filtre1=variable("filtre");

		echo "<table><tr><td width> <ul id=\"menu-bar\">";
		echo "<li><a href=\"index.php?action=ajout_organisme\"  > + Structure sociale </a></li>";
		echo "</ul></td>";
		formulaire ("");
		echo " <td> <input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
		echo "</form> </td> <td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td> ";
		if ($filtre1!="")
			lien_c ("images/croixrouge.png", "","" , "Supprimer");
		
		if ($action=="ajout_organisme") 
			echo "<td> Ne créez une nouvelle structure qu'après avoir vérifié qu'elle n'est pas déjà enregistrée.</td>";
		echo "</table>";

			{
			titre_organisme();
			if ($action=="ajout_organisme") 
				{
				formulaire ("nouveau_organisme");
				echo "<tr><td> <input type=\"texte\" name=\"organisme\"  size=\"30\" value=\"\"> " ;
				echo "<td>  <input type=\"texte\" name=\"sigle\"   size=\"15\" value=\"\"> </td>";
				echo "<td>  <input type=\"texte\" name=\"adresse\"   size=\"60\" value=\"\"> </td>";
				echo "<td>  <input type=\"texte\" name=\"tel\"   size=\"10\" value=\"\"> </td>" ;
				echo "<td> <input type=\"texte\" name=\"mail\"   size=\"40\" value=\"\"> </td>" ;
				if ($user_droit=="A")
					echo "<td> <input type=\"texte\" name=\"doc\"   size=\"10\" value=\"\"> </td>" ;
				else
					echo "<input type=\"hidden\" name=\"doc\"  value=\"\"> " ;
				echo "<td><input type=\"submit\"   id=\"nouveau_organisme\"  value=\"Valider\" > </form></td> ";
				}

			if ($filtre1=="")
				$reponse =command("","select * from  r_organisme order by organisme asc");
			else
				$reponse =command("","select * from  r_organisme where (adresse REGEXP '$filtre1' or organisme REGEXP '$filtre1' or sigle REGEXP '$filtre1' or mail REGEXP '$filtre1' or tel REGEXP '$filtre1') order by organisme asc");			

			while ($donnees = mysql_fetch_array($reponse) ) 
				{
				$idx=$donnees["idx"];	
				$adresse=stripcslashes($donnees["adresse"]);
				$organisme=stripcslashes($donnees["organisme"]);		
				if ($user_droit=="A")
					$organisme="<a href=\"index.php?action=membres_organisme&organisme=".encrypt($idx)."\"> $organisme </a>";
				$tel=$donnees["tel"];	
				$mail=$donnees["mail"];	
				
				$sigle=stripcslashes($donnees["sigle"]);	
				$doc_autorise=$donnees["doc_autorise"];	
				echo "<tr><td> $organisme </td><td> $sigle </td><td> $adresse   </td><td> $tel </td><td> $mail</td>";
				if ($user_droit=="A") 
					echo "<td> $doc_autorise</td><td>".responsables_organisme($idx)."</td>";
				if (($action=="ajout_organisme") && ( $user_droit=="A")) 
					lien_c ("images/croixrouge.png", "supp_organisme_a_comfirmer", param("idx","$idx" ), "Supprimer" );
				}
			echo "</table></div><HR>";				
			}

		}

	function titre_affectation()
		{
		global $user_droit;

		echo "<div class=\"CSSTableGenerator\" ><table><tr><td> Structure sociale: </td><td> Responsable: </td>" ;
		}

	function liste_responsables(  )
		{
		echo "<td> <SELECT name=\"responsable\" id=\"responsable\"  >";
		
		affiche_un_choix("","");
		$reponse =command("","select * from  r_user where droit='R' ");
		while ($donnees = mysql_fetch_array($reponse) ) 
			{
			$idx=$donnees["idx"];
			$nom= $donnees["nom"]." ".$donnees["prenom"];
			affiche_un_choix_2("",$idx,$nom);			
			
			}
		echo "</SELECT></td>";
		}

	function liste_organisme_du_responsable( $responsable )
		{
		echo "<td> <SELECT name=\"organisme\" id=\"organisme\"  >";
		
		$reponse =command("","select * from r_lien where user='$responsable'  ");
		while ($donnees = mysql_fetch_array($reponse) ) 
			{
			$idx = $donnees["organisme"];
			affiche_un_choix_2("",$idx,libelle_organisme($idx));			
			}
		echo "</SELECT></td>";
		}
		
	function organisme_d_un_responsable($responsable)
		{
		$ligne="";
		$r1 =command("","select * from r_lien where user='$responsable' ");
		while ($d1 = mysql_fetch_array($r1) ) 
			{
			$ligne= $ligne . libelle_organisme($d1["organisme"])."; ";
			}
		return($ligne);
		}	
		
	FUNCTION supp_affectation($organisme,$responsable)
		{
		global $action,$user_idx;
		
		$reponse =command("","DELETE FROM `r_lien`  where organisme='$organisme' and user='$responsable' ");
		ajout_log( $user_idx, "Suppresion affectation $organisme  <-> $responsable " );
		}	
		
	FUNCTION nouvelle_affectation ($organisme,$responsable)
		{
		global $action,$user_idx;
		
		$date_jour=date('Y-m-d');
		$r1 =command("","select * from  r_lien where organisme='$organisme' and user='$responsable' ");
		if (!($d1 = mysql_fetch_array($r1)))
			{
			$cmd = "INSERT INTO `r_lien`  VALUES ('$date_jour','$organisme', '$responsable')";
			$reponse = command( "",$cmd);
			ajout_log( $user_idx, "Affectation : ".libelle_organisme($organisme)."($organisme)  <-> ".libelle_user($responsable)." ($responsable)" );
			}
		else
			echo "Affectation existante!";
		}		
		
	function bouton_affectation()
		{
		global $action, $user_droit;

		echo "<table><tr><td width> <ul id=\"menu-bar\">";
		echo "<li><a href=\"index.php?action=ajout_affectation\"  > + Affectation </a></li>";
		echo "</ul></td></table>";

		titre_affectation();
		if ($action=="ajout_affectation") 
				{
				formulaire ("nouvelle_affectation");
				echo "<tr> " ;
				liste_organisme( "" );
				liste_responsables( );
				echo "<td><input type=\"submit\"   id=\"nouvelle_affectation\"  value=\"Valider\" > </form></td> ";
				}

		$reponse =command("","select * from  r_lien order by organisme asc");

		while ($donnees = mysql_fetch_array($reponse) ) 
				{
				$user=$donnees["user"];
				$orga=$donnees["organisme"];
				$responsable=libelle_user($user);
				$organisme=libelle_organisme($orga);		
				echo "<tr><td> $organisme </td><td> $responsable</td>";
				if ($action=="ajout_affectation")  
					lien_c ("images/croixrouge.png", "supp_affectation", param("organisme","$orga" ).param("user","$user" ), "Supprimer" );
				}
			echo "</table></div><HR>";				
		}

function dde_acces($idx,$user, $type='A', $duree=0)
	{

	$date_jour=date('Y-m-d',  mktime(0,0,0 , date("m"), date("d")+$duree, date ("Y")));
		
	$reponse =command("","select * from r_dde_acces where user='$user' and type='$type' and ddeur='$idx' and date_dde>'$date_jour'  ");
	if (! ($donnees = mysql_fetch_array($reponse) )  )
		{
		$reponse =command("","INSERT INTO `r_dde_acces`  VALUES ('$idx' , '', '$date_jour', '$user', '', '', '$type' ) ");
		if ($type=="A")
			ajout_log( $idx, "Demande d'accès au compte par ".libelle_user($user) );
		else
			ajout_log( $idx, "Demande de recupération MdP ".libelle_user($user) );
		}
	}

function autorise_acces($ddeur,$bene,$autorise)
	{
	$date_jour=date('Y-m-d');
	
	$str1 = "abcdefghijklmnopqrstuvwxyz0123456789"; 
	$str="";
	for($i=0;$i<6;$i++)
		{ 
		$pos = rand(0,35); 
		$str .= $str1{$pos}; 
		} 
	$code=$str;
	
	$reponse =command("","UPDATE r_dde_acces set code='$code' , date_auto='$date_jour', autorise='$autorise' where user='$bene' and ddeur='$ddeur' and date_dde>='$date_jour' ");
	ajout_log( $bene, "Autorisation d'accès au compte par ".libelle_user($autorise)." à ".libelle_user($ddeur), $autorise );
	ajout_log( $ddeur, "Autorisation d'accès au compte par ".libelle_user($autorise)." à ".libelle_user($ddeur), $autorise );
	
	}	



	function supp_recup_mdp($as,$bene)
		{
		$date_jour=date('Y-m-d');

		$reponse =command("","UPDATE r_dde_acces set code='' , date_auto='' where user='$bene' and ddeur='$as' and type=''  ");
		ajout_log( $bene, "Fin d'autorisation de recupération par $as" );
		}

	Function traite_demande_acces($organisme,$user_idx)
		{
		$date_jour=date('Y-m-d');
		$j=0;
		$reponse =command("","select * from r_user where droit='S' and organisme='$organisme' ");
		while ($donnees = mysql_fetch_array($reponse) ) 
			{
			$idx2=$donnees["idx"];
			$r1 =command("","select * from r_dde_acces where type='A' and ddeur=$idx2 and date_dde>='$date_jour' ");
			while ($d1 = mysql_fetch_array($r1) ) 
				{
				if ($j++==0)
					{
					echo "<table><tr><td> <ul id=\"menu-bar\">";
					echo "<li> <a href=\"index.php?\"  > Demande d'accès à traiter </a> </li>";
					echo "</ul></td></table> ";
					echo "<div class=\"CSSTableGenerator\" ><table> ";
					echo "<tr><td> Date demande  </td><td> Demandeur   </td><td> Autorisateur </td><td> Bénéficiaire </td><td>  Date Autorisation </td><td>  Code  </td><td>   </td>";
					}
				$date_dde=$d1["date_dde"];
				$qui=$d1["user"];
				$code=$d1["code"];
				$date_auto=$d1["date_auto"];
				$autorise=$d1["autorise"];
				
				echo "<tr><td> $date_dde </td><td> ".libelle_user($idx2)."</td><td>".libelle_user($autorise)." </td><td>".libelle_user($qui)." </td><td> $date_auto </td><td> $code </td>";
				echo "<td><form method=\"POST\" action=\"index.php\">  ";
				echo "<input type=\"hidden\" name=\"ddeur\" value=\"$idx2\"> " ;
				echo "<input type=\"hidden\" name=\"bene\" value=\"$qui\"> " ;
				echo "<input type=\"hidden\" name=\"autorise\" value=\"$user_idx\"> " ;
				if ($code=="")
					echo "<input type=\"hidden\" name=\"action\" value=\"autorise_acces\"><input type=\"submit\" value=\"Autoriser\"/> " ;
				else
					echo "<input type=\"hidden\" name=\"action\" value=\"supp_acces\"> <input type=\"submit\" value=\"Supprimer accès\"/>" ;
				echo "</form>  </td>";	
				}
			if ($j!=0)
				echo "</div></table> ";
			}
		}
	
	// affiche l'historique dès que la personnes est concernée ou acteur
	// on ne travaille qu'avec l'index et plus l'id 
	function histo_beneficiaire($user_idx, $id)
		{
		global $user_droit;
		echo "<hr><img src=\"images/histo.png\" width=\"25\" height=\"25\" >  Historique du compte : ";	

		$j=0;
		$reponse =command("","select * from  log where (user='$user_idx' ) or (acteur='$user_idx' ) order by date DESC ");		
		while ($donnees = mysql_fetch_array($reponse) ) 
			{
			if ($j++==0)
				{
				echo "<div class=\"CSSTableGenerator\" ><table> ";
				echo "<tr><td> Date:   </td><td> Evénement:</td><td> Acteur:</td>";
				if ($user_droit!="")
					echo "<td> Bénéficiaire:</td>";
				}
			$date=$donnees["date"];	
			
			$d3= explode(" ",$date);
			$date=mef_date_fr($d3[0])." ".$d3[1];
			
			$ligne=stripcslashes($donnees["ligne"]);
			$acteur=$donnees["acteur"];
			$ip=$donnees["ip"];
			if (is_numeric($donnees["user"]))
				$user=libelle_user($donnees["user"]);

			if (($acteur!="") && (is_numeric($acteur) ) )
				$acteur=libelle_user($acteur);
			echo "<tr><td title=\"$ip\">  $date  </td><td> $ligne </td><td> $acteur </td>";
			if ($user_droit!="")
				{
				if ($user!=$acteur)
					echo "<td> $user</td>";	
				else
					echo "<td> </td>";	
				}
			}
		if ($j!=0)
			echo "</table></div>";	  
		pied_de_page("x");
	  }

function visitor_country()	
	{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];
    $result  = "Unknown";
    if(filter_var($client, FILTER_VALIDATE_IP))
        $ip = $client;
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
        $ip = $forward;
    else

        $ip = $remote;

    $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));

    if($ip_data && $ip_data->geoplugin_countryName != null)
        $result = $ip_data->geoplugin_countryName;
 
    return $result;
}

function pied_de_page($r="")
	{
	if ($r!="")
		echo "<center><p><br><a id=\"accueil\" href=\"index.php\">Retour à la page d'accueil.</a>"; 

	echo "<br><br>";
	echo "<hr><center> <a id=\"lien_conditions\" href=\"conditions.html\">Conditions d'utilisation</a>";
	echo "- <a id=\"lien_contact\" href=\"index.php?action=contact\">Nous contacter</a>";
	echo "- Copyright <a href=\"http://adileos.doc-depot.com\">ADILEOS 2014</a> ";
	$version= parametre("DD_version_portail") ;
	if ($_SERVER['REMOTE_ADDR']=="127.0.0.1")
		echo "- <a href=\"version.htm\"target=_blank > $version </a>";	
	else
		echo "- $version ";	

	echo "- <a href=\"index.php?action=bug\">Signaler un bug ou demander une évolution.</a> <p> ";
	mysql_close( );	
	exit();
	}
 
function testpassword($mdp)	{ // $mdp le mot de passe passé en paramètre
 
$point = 0;
$point_min =0;
$point_maj =0;
$point_caracteres =0;
$point_chiffre =0;
// On récupère la longueur du mot de passe	
$longueur = strlen($mdp);
 
// On fait une boucle pour lire chaque lettre
for($i = 0; $i < $longueur; $i++) 	{
 
	// On sélectionne une à une chaque lettre
	// $i étant à 0 lors du premier passage de la boucle
	$lettre = $mdp[$i];
 
	if ($lettre>='a' && $lettre<='z'){
		// On ajoute 1 point pour une minuscule
		$point = $point + 1;
 
		// On rajoute le bonus pour une minuscule
	$point_min = 1;
	}
	else if ($lettre>='A' && $lettre <='Z'){
		// On ajoute 2 points pour une majuscule
		$point = $point + 2;
 
		// On rajoute le bonus pour une majuscule
		$point_maj = 2;
	}
	else if ($lettre>='0' && $lettre<='9'){
		// On ajoute 3 points pour un chiffre
		$point = $point + 3;
 
		// On rajoute le bonus pour un chiffre
		$point_chiffre = 3;
	}
	else {
		// On ajoute 5 points pour un caractère autre
		$point = $point + 5;
 
		// On rajoute le bonus pour un caractère autre
		$point_caracteres = 5;
	}
}
 
// Calcul du coefficient points/longueur
$etape1 = $point / $longueur;
 
// Calcul du coefficient de la diversité des types de caractères...
$etape2 = $point_min + $point_maj + $point_chiffre + $point_caracteres;
 
// Multiplication du coefficient de diversité avec celui de la longueur
$resultat = $etape1 * $etape2;
 
// Multiplication du résultat par la longueur de la chaîne
$final = $resultat * $longueur;
  
return $final;
 
}

function affiche_titre_user($user)
	{
	$reponse =command("","select * from  r_user where idx='$user' ");		
	$donnees = mysql_fetch_array($reponse) ;

	$nom=$donnees["nom"];
	$prenom=$donnees["prenom"];				
	$anniv=$donnees["anniv"];	
	$tel=$donnees["telephone"];				
	$mail=$donnees["mail"];	
			
	$organisme=$donnees["organisme"];
	$organisme=libelle_organisme($organisme);
	$adresse=stripcslashes($donnees["adresse"]);
		
	echo "<table><tr><td> <ul id=\"menu-bar\">";
	echo "<li> <a   > $nom-$prenom ( $anniv ) </a></li>";
	echo "</ul></td><td> - Domiciliation: $organisme / $adresse / $tel / $mail </td></table>";
	}

function affiche_membre($idx)
	{
	$reponse =command("","select * from r_user where idx='$idx' ");		
	$donnees = mysql_fetch_array($reponse) ;

	$droit=$donnees["droit"];	
	$nom=$donnees["nom"];	
	$prenom=$donnees["prenom"];
	$tel=$donnees["telephone"];
	$mail=$donnees["mail"];
	$id=$donnees["id"];
	$idx=$donnees["idx"];
	echo "<tr><td>  $droit </td><td>  $id  </td><td> $nom </td><td> $prenom </td><td> $tel </td><td> $mail </td>";
	$r1 =command("","select * from r_attachement where ref='P-$idx' ");		
	while ($d1 = mysql_fetch_array($r1) ) 
		{
		$num=$d1["num"];
		visu_doc($num,0);
		}
	}

	function liste_avant( $val_init , $mode="")
		{
		echo "<td><SELECT name=\"avant\" $mode >";
		affiche_un_choix($val_init,"15min");
		affiche_un_choix($val_init,"1H");
		affiche_un_choix($val_init,"4H");
		affiche_un_choix($val_init,"La veille", "La veille soir");
		affiche_un_choix($val_init,"24H");
		echo "</SELECT></td>";
		}	
	// ------------------------------------------------------------------------- Rendez-vous --------------------------------
	function titre_rdv($user_telephone)
		{
		echo "<div class=\"CSSTableGenerator\" > ";
		echo "<table><tr><td width=\"15%\"> Date - Heure </td><td> Message envoyé par SMS au $user_telephone </td><td> Alerte </td><td> Etat </td>";		
		}
		
	function rdv($U_idx)
		{
		global  $action,$user_idx;
		include 'calendrier.php';
		$j=0;
		
		$user_telephone=telephone_user($U_idx);
		signet("affiche_rdv");
		$idx = variable("idx");
		if (($action=="rdv") and ($idx!="") )
			{
			$reponse =command("","select * FROM `DD_rdv` where idx='$idx' ");
			if ($donnees = mysql_fetch_array($reponse))
				{
				$date=$donnees["date"];	
				$ligne=$donnees["ligne"];	
				$reponse =command("","DELETE FROM `DD_rdv` where idx='$idx' ");
				ajout_log( $idx, "Suppression RDV le $date : '$ligne' ", $user_idx );		
				}
				
			}

		echo "<table> <tr>";
		echo "<td><img src=\"images/reveil.png\" width=\"35\" height=\"35\" > <td> ";		
		echo "<td> <ul id=\"menu-bar\">";
		if ( !VerifierPortable($user_telephone) )
			{
					echo "<li> <a href=\"index.php\"  >+ Rendez-vous </a> </li></ul></td>";

					if ($user_idx==$U_idx)
						echo "<td> - Pour accèder à cette fonction il faut disposer d'un n° de téléphone portable </td> ";
					else
						echo "<td> - Fonction non accessible car le bénéficiaire ne dispose pas d'un n° de téléphone portable </td> ";
				}
			else
				echo "<li> <a href=\"index.php?action=rdv\"  >+ Rendez-vous </a> </li></ul></td>";

		echo "<td> </table> ";
			
		if ( ($action=="rdv") && (VerifierPortable($user_telephone) ) )
			{
			titre_rdv($user_telephone);
			$j++;
			formulaire ("ajout_rdv");
			echo "<tr>";
			echo "<td> <input type=\"texte\" name=\"date\"  class=\"calendrier\" size=\"10\" value=\"\">  ";
			echo " <input type=\"texte\" name=\"heure\"   size=\"5\" value=\"\"> </td>";
			echo "<td> <input type=\"texte\" name=\"ligne\"   size=\"100\" value=\"\"> </td>";
			liste_avant( "1H" );
			echo "<input type=\"hidden\" name=\"user\"  value=\"$U_idx\"> " ;
			echo "<td><input type=\"submit\" id=\"ajout_rdv\" value=\"Ajouter\" ></form> </td> ";
			}
		if ($user_idx==$U_idx)
			$reponse =command("","select * from  DD_rdv where user='$U_idx'  order by date desc");		
		else
			$reponse =command("","select * from  DD_rdv where user='$U_idx' and auteur='$user_idx'  order by date desc");	
			
		while ($donnees = mysql_fetch_array($reponse) ) 
			{
			if ($j==0)
				titre_rdv($user_telephone);
			$date=$donnees["date"];	
			$d3= explode(" ",$date);
			$date=mef_date_fr($d3[0])." ".$d3[1];
			$avant=$donnees["avant"];	
			$etat=$donnees["etat"];	
			$idx=$donnees["idx"];	
			$ligne=stripcslashes($donnees["ligne"]);
			echo "<tr><td>  $date </td><td> $ligne </td><td> $avant </td><td> $etat </td>";
			if ( ($action=="rdv") && ($etat=="A envoyer")) 
				lien_c ("images/croixrouge.png", "rdv", param("idx","$idx" ) , "Supprimer");
			$j++;
			}
		if ($j!=0)
			echo "</table></div>";
		else
			echo "<hr>";


		}

	// liste des  autorisées
	function verif_action_autorise($action)
		{
		
		switch ($action)
				{
				case "":
				case "recup_mdp":
				case "enreg_bug":
				case "enreg_contact":
				case "bug":
				case "contact":
				case "maj_user":
				case "reinit_mdp":
				case "changer_mdp":
				case "finaliser_user":
				case "finaliser_user2":
				case "traite_dde_identifiant":
				case "dde_identifiant":
				case "valider_dde_mdp_avec_code":
				case "dde_code_par_sms":
				case "dde_code_par_mail":
				case "dde_mdp_avec_code":
				case "dde_mdp_avec_code2":
				case "envoi_mdp":
				case "envoi_mdp2":
				case "dde_mdp":
				case "dde_mdp2":
				case "detail_user":
				case "recept_mail":
				case "supp_recept_mail":
				case "user_inactif":
				case "user_actif":
				case "phpinfo":
				case "archivage_php":
				case "dde_chgt_cle":
				case "chgt_cle":
				case "supp_upload":
				case "autorise_recup_mdp":
				case "supp_recup_mdp":
				case "autorise_acces":
				case "supp_acces":
				case "supp_compte":
				case "modif_type_doc":
				case "modif_domicile":
				case "modif_organisme":
				case "nouveau_organisme":
				case "supp_organisme":
				case "supp_affectation":
				case "nouvelle_affectation":
				case "nouveau_referent":
				case "supp_referent":
				case "nouveau_user":
				case "modif_user":
				case "supp_user":
				case "modif_profil":
				case "modif_mdp":
				case "init_selenium":
				case "modif_tel":
				case "supp_upload_a_confirmer":
				case "ajout_admin":
				case "draganddrop":
				case "rdv":
				case "ajout_rdv":
				case "en_trop":
				case "integrite":
				case "authenticite":
				case "liste_compte":
				case "afflog":
				case "afflog_t":
				case "histo": 
				case "membres_organisme":
				case "renvoyer_mail":
				case "visu_lecture":
				case "liste_bug":
				case "visualisation_lecture":
				case "modification_lecture":
				case "exporter":
				case "exporter_a_confirmer":
				case "supp_compte_a_confirmer":
				case "supp_referent_a_confirmer":
				case "supp_organisme_a_comfirmer":
				case "supp_user_a_confirmer":
				case "draganddrop":
				case "draganddrop_p":
				case "modif_champ_bug":
				case "modif_bug":
				case "param_sys":
				case "modif_valeur_param":
				case "illicite":
				case "switch":
				case "creer_dossier":
				case "dossier":
				case "ajout_affectation":
				case "note_sms":
				case "ajout_note":
				case "ajout_photo":
				case "ajout_organisme";
				case "ajout_referent":
				case "ajout_user":
				case "visu_fichier":
				case "visu_fichier_tmp":
				case "visu_doc":
				case "visu_pdf":
				case "visu_image_mini":
				case "ajout_beneficiaire":
				case "verif_existe_user":
				case "verif_user":
				case "dx":
				case "upload":
				case "modifier_user":
				case "supp_filtre":
				case "alerte_admin":
				case "raz_mdp":
				case "raz_mdp1":
				case "init_formation": 
				case "raz_mdp_formation":
				
					ajout_log_jour("----------------------------------------------------------------------------------- [ Action= $action ] ");
					return($action);
				break;
				
				default : 
					erreur ("Action '$action' inconnue");
					if (isset($_SESSION['user']))
						$nom=libelle_user($_SESSION['user']);
					else 
						$nom="user non connecté (".$_SERVER["REMOTE_ADDR"].")";
					ajout_log_tech ( "Action '$action' inconnue par $nom ", "P1" );
					return ("");
				}
		}
		
		// -====================================================================== DEBUT de PAGE ===============================
// Connexion BdD

require_once "connex_inc.php";
require_once 'include_crypt.php';

	// on teste le pays d'origine
	$pays= visitor_country(); 
	if (($pays!="France") && ($pays!="Unknown"))  // on n'autorise que la france 
		{
		aff_logo("x");
		echo "<p>Erreur :pays d'origine n'est pas la France";
		pied_de_page(); 
		}
		
	/*	
	if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)!="fr") 
		{ 
		aff_logo("x");
		echo "<p>Erreur : Langue navigateur n'est pas le français";
		pied_de_page(); 
		}
	*/
	
	// ------------------------------------------------------------------------------ traitement des actions sans mot de passe
	$action=variable("action");	

	// on vérifie que l'action demandée (champ en clair) fait bien partie de la liste officielle ==> évite le piratage
	$action = verif_action_autorise($action);	
	
	if ($action!="")
		{
		if ($action=="recup_mdp")
			{
			aff_logo();
			$idx1=variable("as");
			dde_acces(variable("user"),$idx1,"",5);
			echo "<p>Demande enregistrée.";
			echo "<p><br><p> Rappel: vous avez 5 jours pour la contacter en personne. Elle s'assurera de votre identité. ";
			
			titre_referent("");
			$reponse =command("","select * from  r_user where idx='$idx1' ");
			$d1 = mysql_fetch_array($reponse);
			$nom=$d1["nom"];
			$idx2=$d1["idx"];
			$prenom=$d1["prenom"];
			$tel=$d1["telephone"];	
			$organisme=libelle_organisme($d1["organisme"]);
			$adresse=adresse_organisme($d1["organisme"]);
			echo "<tr><td> $organisme </td><td> $nom   </td><td> $prenom   </td><td> $tel </td><td> </td><td> $adresse</td>";
			echo "</table></div>";			
			pied_de_page("x");
			}
			
		if ($action=="enreg_bug")
			{
			aff_logo();
			$ip= $_SERVER["REMOTE_ADDR"];
			tempo_cx ($ip);
			$n=enreg_bug(variable("titre"),variable("descript"),variable("type"),variable("impact"),variable("qui"));
			msg_ok( "Bug enregistré ($n).");
			pied_de_page("x");
			}	
			
		if ($action=="alerte_admin")
			{
			aff_logo();
			echo "<p><br>";
			$motif = variable_get("motif");
			ajout_log_tech( "Signalement : $motif" , "P1");
			msg_ok( "Votre signalmement a été tansmis à l'administrateur du site.");
			echo "<p><br>";

			pied_de_page("x");
			}	
		if ($action=="enreg_contact")
			{
			aff_logo();
			$ip= $_SERVER["REMOTE_ADDR"];
			tempo_cx ($ip);
			enreg_contact(variable("qui"),variable("coordonnees"),variable("descript"));
			msg_ok("Demande enregistrée.");
			pied_de_page("x");
			}		

		if ($action=="bug")
			{
			$date_jour=date('Y-m-d');
			aff_logo();
			$ip= $_SERVER["REMOTE_ADDR"];
			ajout_echec_cx ($ip);
			
			debut_cadre("700");
			echo "<p><br>Signaler un bug ou faire une demande d'évolution : ";
			formulaire ("enreg_bug");
			echo "<TABLE><TR><td> Rédacteur</td> ";
			if (isset($_SESSION['user']))
				$nom=libelle_user($_SESSION['user']);
			else 
				$nom="";
			echo "<td> <input type=\"text\" name=\"qui\" size=\"50\" value=\"$nom\"/></td>";
			echo "<TR> <td>Type </td>";
			echo "<td><SELECT name=\"type\"  >";
			affiche_un_choix($val_init,"Bugs","Bug");
			affiche_un_choix($val_init,"Fonctionnel","Evolution");
			echo "</SELECT></td>";
			echo "<TR> <td>Importance</td>";
			echo "<td><SELECT name=\"impact\"  >";
			$val_init="Normal";
			affiche_un_choix($val_init,"Urgent","Bloquant");
			affiche_un_choix($val_init,"Prioritaire","Fort");
			affiche_un_choix($val_init,"Normal");
			affiche_un_choix($val_init,"Faible");
			echo "</SELECT></td>";
			echo "<TR> <td>Titre </td><td> <input type=\"text\" name=\"titre\" size=\"70\" value=\"\"/></td>";
			echo "<TR> <td>Description </td><td><TEXTAREA rows=\"5\" cols=\"50\" name=\"descript\" ></TEXTAREA></td>";
			echo "<TR> <td> </td> <td><input type=\"submit\"  id=\"enreg_bug\" value=\"Enregistrer\"/><p></td>";
			echo "</form> </table> ";
			fin_cadre();
			pied_de_page("x");
			}	

		if ($action=="contact")
			{
			$date_jour=date('Y-m-d');
			aff_logo();
			$ip= $_SERVER["REMOTE_ADDR"];
			ajout_echec_cx ($ip);
			debut_cadre("700");
			echo "<p><br> Pour faire une demande auprès du support de Doc-depot.com, merci de remplir le formulaire suivant : ";
			formulaire ("enreg_contact");
			echo "<TABLE><TR><td> Vos nom et prénom : </td> ";
			if (isset($_SESSION['user']))
				$nom=libelle_user($_SESSION['user']);
			else 
				$nom="";
			echo "<td> <input type=\"text\" name=\"qui\" size=\"60\" value=\"$nom\"/></td>";
			echo "<TR> <td>Vos coordonnées:</td><td> <input type=\"text\" name=\"coordonnees\" size=\"60\" value=\"\"/></td>";
			echo "<TR> <td>Description de <br>votre demande :</td><td><TEXTAREA rows=\"5\" cols=\"60\" name=\"descript\" ></TEXTAREA></td>";
			echo "<TR> <td> </td> <td><input type=\"submit\"  id=\"enreg_contact\" value=\"Enregistrer\"/><br></td>";
			echo "</form> </table>  <p> Nous vous contacterons dans les meilleurs délais. <br><br>";
			fin_cadre();
			pied_de_page("x");
			}				
	
		if ($action=="maj_user")
			{
			if (!maj_user(variable("idx"),variable("id"),variable("pw"),variable("mail"),variable("nom"),variable("prenom"),variable("telephone")))
				$action="finaliser_user2";
			else
				$action="";
			}
			
		if ($action=="reinit_mdp")
			{
			$date_jour=date('Y-m-d');

			$code=variable_get("code");
			$reponse =command("","select * from r_dde_acces where code='$code' and type='' and  date_dde='$date_jour'  ");
			if ( ($donnees = mysql_fetch_array($reponse) )  )
				{
				aff_logo();
				$user=$donnees["user"];
				
				$r1 =command("","select * from r_user where  idx='$user'  ");
				$d1 = mysql_fetch_array($r1) ;
					
				$identifiant=$d1["id"];
				$pw=decrypt($d1["pw"]);
				
				echo "<p>Réinitialisation de votre mot de passe";
				debut_cadre("700");
				echo "<br>Rappel format mot de passe : Au minimum 8 caractères, au moins une majuscule, <br>une minuscule et un chiffre (caractères spéciaux possibles)<p>";
				formulaire ("changer_mdp");
				echo "<table><TR><td> Identifiant: </td> ";
				echo "<td> $identifiant <input type=\"hidden\" name=\"idx\" value=\"$user\"/></td>";
				echo "<input type=\"hidden\" name=\"ancien\" value=\"$pw\"/></td>";
				echo "<TR> <td>Nouveau Mot de passe </td><td><input class=\"center\" type=\"password\" id=\"pwd\" name=\"n1\" value=\"\"/></td>";

				echo "<TR> <td>Confirmation</td><td><input class=\"center\" type=\"password\" name=\"n2\" id=\"pwd1\" value=\"\"/></td>";
				echo "<td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> Voir saisie<td>";
				echo "<TR>  <td></td><td><input type=\"submit\"  id=\"changer_mdp\"  value=\"Modifier\"/></td>";
				echo "</form></table><p>  ";
				fin_cadre();
				pied_de_page("x");
				}
			else
				erreur("Lien invalide.");
			}	

		if ($action=="changer_mdp") 
			{
			echo "<center>";					
			$action="modif_mdp";
			$ok=FALSE;
			$idx=variable('idx');
			$reponse =command("","SELECT * from  r_user WHERE idx='$idx'"); 
			if ($donnees = mysql_fetch_array($reponse))
				{
				$id=$donnees["id"];	
				$mdp_ancien=$donnees["pw"];
				if ($mdp_ancien==encrypt(variable('ancien') ) )
					{
					$mdp=variable('n1');
					if ($mdp==variable('n2') )
						{
						if (strlen($mdp)>7 )
							{
							if ($id!=$mdp )
								{
								if ( testpassword($mdp)>70 ) 
									{
									if ( compte_non_protege($id) )
										{
										$code_lecture=$donnees["lecture"];
										
										if  ($code_lecture=="$mdp_ancien") 
											command( "","UPDATE r_user set lecture='".encrypt($mdp)."' where id='$id'");

										$mdp=encrypt($mdp);
										aff_logo();
										command( "","UPDATE r_user set pw='$mdp' where id='$id'");
										echo "<p><br><p>Modification du mot de passe réalisée.";
										$ok=TRUE;
										ajout_log( $id, 'Changement de Mot de passe' );
										$action="";
										}
									else 
										erreur (" Le mot de passe de ce compte n'est pas modifiable");
									}
								else 
									erreur (" Le mot de passe n'est pas assez complexe (utiliser des Majuscules, Chiffres, caractéres spéciaux)");
								}
							else 
								erreur ( "Le mot de passe doit être différent de l'identifiant.");
							}
						else 
							erreur ("Le mot de passe est trop court.");
						}
					else 
						erreur ("Les 2 mots de passe ne sont pas identiques.");
					}
				else 
					{
					erreur("Mot de passe incorrect.");
					ajout_log( $id, 'Changement de Mot de passe: ancien MdP incorrect' );
					}
				}
			else 
				erreur("Identifiant incorrect. xxx");
				
			if (isset ($id) && (variable('ancien')=="123456") && ($ok==FALSE))
				$identifiant=$id;
			else
				if ($ok)
					pied_de_page("x");
				else
					$action="modif_mdp";
			}	

			
		if  ( ($action=="finaliser_user") || ($action=="finaliser_user2")) 
			{
			if  ($action=="finaliser_user")
				$idx=variable_get("idx");
			else
				$idx=variable("idx");			
			$reponse = command( "","SELECT * from  r_user WHERE idx='$idx'"); 
			if ($donnees = mysql_fetch_array($reponse))
				{
				$user_nom=$donnees["nom"];
				$user_prenom=$donnees["prenom"];
				$user_telephone=$donnees["telephone"];
				$user_mail=$donnees["mail"];
				$id=$donnees["id"];
				if ($id=="???")
					{
					aff_logo();
					debut_cadre("700");
					echo "<p><br><p>Pour finaliser la création de votre compte, merci de compléter les informations ci-dessous :";
					formulaire ("maj_user");
					echo "<TABLE><TR> <td></td>";
					echo "<tr><td> Identifiant </td><td><input type=\"texte\" name=\"id\"   value=\"\"> </td>";
					echo " <td>Au minimum 8 caractères </td>";
					echo "<tr><td> Mot de passe </td><td><input type=\"password\" id=\"pwd\" name=\"pw\"   value=\"\"> </td> " ;
					echo " <td>Au minimum 8 caractères, au moins <br>une majuscule, une minuscule et un chiffre <br>(caractères spéciaux possibles) </td>";
					echo "<tr> <td></td><td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> Voir saisie<td>";
					echo "<input type=\"hidden\" name=\"idx\"  value=\"$idx\"> " ;
					echo "<tr><td> Nom </td><td> <input type=\"texte\" name=\"nom\"   size=\"20\" value=\"$user_nom\"> </td>" ;
					echo "<tr><td> Prénom </td><td> <input type=\"texte\" name=\"prenom\"   size=\"20\" value=\"$user_prenom\"> </td>" ;
					echo "<tr><td> Téléphone </td><td><input type=\"texte\" name=\"telephone\"   size=\"15\" value=\"$user_telephone\"> </td>" ;
					echo "<tr><td> Mail </td><td> <input type=\"texte\" name=\"mail\"   size=\"30\" value=\"$user_mail\"> </td>" ;
					echo "<td><input type=\"submit\" id=\"maj_user\" value=\"Valider\" > </td> ";
					echo "</form> </table>";
					fin_cadre();
					pied_de_page("x");
					}
				else
					erreur("Compte déjà initialisé.");
				}
			else
				erreur("Action interdite.");
			}		

		if ($action=="traite_dde_identifiant")
			{
			$nom=mef_nom(variable("nom")); 
			$prenom=mef_prenom(variable("prenom")); 
			$nationalite=mef_prenom(variable("nationalite")); 
			$ville_natale=mef_prenom(variable("ville_natale")); 
			$anniv=mef_date(variable("anniv")); 
			$prenom_p=mef_prenom(variable("prenom_p")); 
			$prenom_m=mef_prenom(variable("prenom_m")); 

			$reponse = command( "","SELECT * from  r_user WHERE droit='' and nom='$nom' and prenom='$prenom' and prenom_p='$prenom_p' and prenom_m='$prenom_m' and anniv='$anniv'and ville_nat='$ville_natale'"); 
			if ($donnees = mysql_fetch_array($reponse))
				{
				aff_logo();
				$id=$donnees["id"];
				echo "<br><br><br>Rappel : votre identifiant est : '<strong>$id<strong>'";
				$action="envoi_mdp2";
				}
			else
				{
				erreur ("Les informations communiquées ne permettent pas de retrouver votre identifiant.<p>");
				$action="dde_identifiant";
				}
			}			

		if ($action=="dde_identifiant")
			{
			aff_logo();
			echo "<p>Pour récupérer votre identifiant: <p>";
			echo "<p>Si vous êtes un acteur social, merci de contacter votre responsable. </a><p>";
			echo "Si vous êtes un responsable , merci de contacter l'administrateur de ce site (voir lien \"Nous contacter\" ci-dessous)<p>";
			debut_cadre();
			echo "<br>Si vous êtes un bénéficiaire, merci de compléter ce formulaire : <p>";
			echo "<TABLE><TR>";
			formulaire ("traite_dde_identifiant");
			echo "<tr><td> Nom</td><td><input ctype=\"text\" size=\"20\" name=\"nom\" value=\"".variable("nom")."\"/></td>";
			echo "<tr><td> Prénom  </td><td><input  type=\"text\" size=\"20\" name=\"prenom\" value=\"".variable("prenom")."\"/></td>";
			echo "<tr><td> Date de naissance   </td><td><input  type=\"text\" size=\"20\" name=\"anniv\" value=\"".variable("anniv")."\"/></td>";
			echo "<tr><td> Ville natale   </td><td><input  type=\"text\" size=\"20\" name=\"ville_natale\" value=\"".variable("ville_natale")."\"/></td>";
			echo "<tr><td> Prénom de votre mére </td><td><input  type=\"text\" size=\"20\" name=\"prenom_m\" value=\"".variable("prenom_m")."\"/></td>";
			echo "<tr><td> Prénom de votre pére </td><td><input  type=\"text\" size=\"20\" name=\"prenom_p\" value=\"".variable("prenom_p")."\"/></td>";
			echo "<tr> <td></td><td><input type=\"submit\"  id=\"traite_dde_identifiant\"  value=\"Valider la demande\"><p></td>";
			echo "</form> </table>";
			fin_cadre();
			pied_de_page("x");
			}	
			
		if ($action=="valider_dde_mdp_avec_code")
			{
			$nom=mef_nom(variable("nom")); 
			$prenom=mef_prenom(variable("prenom")); 
			$nationalite=mef_prenom(variable("nationalite")); 
			$ville_natale=mef_prenom(variable("ville_natale")); 
			$anniv=mef_date(variable("anniv")); 
			$prenom_p=mef_prenom(variable("prenom_p")); 
			$prenom_m=mef_prenom(variable("prenom_m")); 
			$code=variable("code"); 
			echo $nom.$prenom.$prenom_p.$prenom_m.$anniv.$ville_natale;
			$reponse = command ("","SELECT * from  r_user WHERE droit='' and nom='$nom' and prenom='$prenom' and prenom_p='$prenom_p' and prenom_m='$prenom_m' and anniv='$anniv'and ville_nat='$ville_natale'"); 
			if ($donnees = mysql_fetch_array($reponse))
				{
				aff_logo();
				$idx=$donnees["idx"];
				$id=$donnees["id"];
				$mdp_ancien=$donnees["pw"];
				$mdp=variable('pwd');
						if (strlen($mdp)>7 )
							{
							if ($id!=$mdp )
								{
								if ( testpassword($mdp)>70 ) 
									{
									if ( compte_non_protege($id) )
										{
										$code_lecture=$donnees["lecture"];
										
										if  ($code_lecture=="$mdp_ancien") 
											command ("","UPDATE r_user set lecture='".encrypt($mdp)."' where id='$id'");

										$mdp=encrypt($mdp);
										command ("","UPDATE r_user set pw='$mdp' where id='$id'");
										echo "<p><br><p>Modification du mot de passe réalisée.<p><br><p>";
										$ok=TRUE;
										ajout_log( $idx, "Réinitialisation Mot de passe avec code de déverrouillage $code" );
										$action="";
										}
									else 
										erreur (" Le mot de passe de ce compte n'est pas modifiable");

									}
								else 
									erreur (" Le mot de passe n'est pas assez complexe (utiliser des Majuscules, Chiffres, caractéres spéciaux)");
								}
							else 
								erreur ( "Le mot de passe doit être différent de l'identifiant.");
							}
						else 
							erreur ("Le mot de passe est trop court.");
				pied_de_page("x");
				}
			else
				{
				erreur ("Les informations communiquées ne permettent pas de traiter votre demande.");
				// Ajout log à faire
				$action="dde_mdp_avec_code";
				}
			}		

		
		if ($action=="dde_code_par_sms")
			{
			aff_logo();
			$code=rand(1000000,999999999);
			$date_jour=date('Y-m-d');
			$telephone=variable_get("telephone");
			$reponse =command("","SELECT * from  r_user WHERE telephone='$telephone' "); 
			if (($donnees = mysql_fetch_array($reponse)) && (strlen($telephone)==10 ))
				{
				$idx=$donnees["idx"];

				$reponse =command("","SELECT * from  r_dde_acces WHERE user='$idx'  and date_dde='$date_jour' "); 
				if ($donnees = mysql_fetch_array($reponse))
					{
					echo "<br><p><strong>Un SMS ou mail a déjà été envoyé aujourd'hui. </strong> <p><br>";
					}
				else
					{
					$reponse =command("","INSERT INTO `r_dde_acces`  VALUES ('$idx' , '$code', '$date_jour', '$idx', '$idx', '', '' ) ");
					envoi_SMS($telephone , "Code de déverrouillage : $code ");
					echo "<br><p><strong>Vous receverez dans quelques minutes un SMS avec le code de déverrouillage.</strong> ";
					}
				$action="dde_mdp_avec_code2";
				}
			else
				{
				erreur("Numero inconnu");
				pied_de_page("x");
				}
			}
			
		if ($action=="dde_code_par_mail")
			{
			aff_logo();
			$code=rand(1000000,999999999);
			$date_jour=date('Y-m-d');
			$mail=variable_get("mail");
			$reponse =command("","SELECT * from  r_user WHERE mail='$mail' "); 
			if (($donnees = mysql_fetch_array($reponse)) && (VerifierAdresseMail($mail) ) )
				{
				$idx=$donnees["idx"];

				$reponse =command("","SELECT * from  r_dde_acces WHERE user='$idx'  and date_dde='$date_jour' "); 
				if ($donnees = mysql_fetch_array($reponse))
					{
					echo "<br><p><strong>Un SMS ou mail a déjà été envoyé aujourd'hui. </strong> <p><br>";
					}
				else
					{
					$reponse =command("","INSERT INTO `r_dde_acces`  VALUES ('$idx' , '$code', '$date_jour', '$idx', '$idx', '', '' ) ");
					
					envoi_mail($mail,"Code de déverrouillage","Suite à votre demande votre code de déverrouillage est '$code' ");
					echo "<br><p><strong>Vous receverez dans quelques minutes un mail avec le code de déverrouillage.</strong> <p><br>";
					}
				$action="dde_mdp_avec_code2";
				}
			else
				{
				erreur("Numero inconnu");
				pied_de_page("x");
				}
			}			
			
		if ( ($action=="dde_mdp_avec_code") || ($action=="dde_mdp_avec_code2"))
			{
			if ($action=="dde_mdp_avec_code")
				aff_logo();
			
			debut_cadre("700");
			echo "<p><br><p>Pour récupérer votre mot de passe, merci de compléter ce formulaire : <p>";
			echo "<TABLE><TR> ";
			formulaire ("valider_dde_mdp_avec_code");
			echo "<tr><td> Nom</td><td><input ctype=\"text\" size=\"20\" name=\"nom\" value=\"".variable("nom")."\"/></td>";
			echo "<tr><td> Prénom  </td><td><input  type=\"text\" size=\"20\" name=\"prenom\" value=\"".variable("prenom")."\"/></td>";
			echo "<tr><td> Date de naissance   </td><td><input  type=\"text\" size=\"20\" name=\"anniv\" value=\"".variable("anniv")."\"/></td>";
			echo "<tr><td> Ville natale   </td><td><input  type=\"text\" size=\"20\" name=\"ville_natale\" value=\"".variable("ville_natale")."\"/></td>";
			echo "<tr><td> Prénom de votre mére </td><td><input  type=\"text\" size=\"20\" name=\"prenom_m\" value=\"".variable("prenom_m")."\"/></td>";
			echo "<tr><td> Prénom de votre pére </td><td><input  type=\"text\" size=\"20\" name=\"prenom_p\" value=\"".variable("prenom_p")."\"/></td>";
			echo "<tr><td> Code déverrouillage </td><td><input  type=\"password\" id=\"pwd1\" size=\"10\" name=\"code\" value=\"".variable("code")."\"/></td>";
			echo "<td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> Voir saisies<td>";
			echo "<tr><td> Nouveau mot de passe </td><td><input  type=\"password\" id=\"pwd\" size=\"12\" name=\"pwd\" value=\"".variable("pwd")."\"/></td>";
			echo "<td>Au minimum 8 caractères, au moins <br>une majuscule,une minuscule et un chiffre <br>(caractères spéciaux possibles) </td>";
			echo "<tr> <td></td><td><input type=\"submit\"  id=\"valider_dde_mdp_avec_code\"  value=\"Valider la demande\"><p></td>";
			echo "</form> </table>";
			fin_cadre();
			pied_de_page("x");
			}	


		if (($action=="envoi_mdp") || ($action=="envoi_mdp2") )
			{
			if ($action=="envoi_mdp") // pour envoi_mp2 la variable $id est déja positionné par demande identifiant
				{
				$id=variable('id');
				aff_logo();
				}
			echo "<center>";	
			$reponse = command( "","SELECT * from  r_user WHERE (id='$id' or mail='$id' or telephone='$id')"); 
			if ($donnees = mysql_fetch_array($reponse))
				{
				if (!mysql_fetch_array($reponse)) // vérifiction qu'il est unique
					{
					$mail=$donnees["mail"];
					$telephone=$donnees["telephone"];
					$droit=$donnees["droit"];
					$idx1=$donnees["idx"];
					$id1=$donnees["id"];
					$date_jour=date('Y-m-d');				
					
					if ( ( (strlen(strstr($telephone,"06"))==10) || (strlen(strstr($telephone,"07"))==10) || (VerifierAdresseMail($mail) ) ) && ($droit=="") )
						{
						echo "Pour recevoir votre code de déverrouillage : ";
						echo "<p> <table border=\"2\" ><tr> <td> <center>";
						echo "<img src=\"images/sms.png\" width=\"35\" height=\"35\" >Soit directement en cliquant sur le(s) choix ci-dessous : ";

						// si téléphone portable valide alors on propose de l'encoyer par SMS 
						if ( (strlen(strstr($telephone,"06"))==10) || (strlen(strstr($telephone,"07"))==10) )
							{
							$tel_tronque= $telephone;
							$tel_tronque[3]='.';
							$tel_tronque[4]='.';
							$tel_tronque[5]='.';
							echo "<p><a  id=\"sms\" href=\"index.php?action=dde_code_par_sms&telephone=".encrypt($telephone)."\"> par SMS au <strong>$tel_tronque</strong></a>";
							}
							
						if (VerifierAdresseMail($mail) )
							{
							$mail_tronque= $mail;
							for ($i=5; $i<strlen($mail)-10; $i++)
								$mail_tronque[$i]='.';
							echo "<p><a  id=\"mail\" href=\"index.php?action=dde_code_par_mail&mail=".encrypt($mail)."\"> Par mail à l'adresse <strong>'$mail_tronque' </strong></a>";
							}
						echo "</td></table>" ;
						echo "<p> Soit en contactant un référent de confiance.";
						}

					
					if (( ($id1==$id) || ($mail==$id)|| ($telephone==$id)) && ($droit!="")) // cas des AS et responsables
						{
						$id=$donnees["id"];
						$idx=$donnees["idx"];
						$code=rand(1000000,999999999);
			
						$reponse =command("","select * from r_dde_acces where user='$idx1' and ddeur='$idx1' and type='' and  date_dde='$date_jour'  ");
						if (! ($donnees = mysql_fetch_array($reponse) )  )
							{
							if (compte_non_protege($id))
								{
								$reponse =command("","INSERT INTO `r_dde_acces`  VALUES ('$idx' , '$code', '$date_jour', '$idx', '', '', '' ) ");
								$synth ="Pour réinitialiser votre mot de passe, cliquez <a  id=\"lien\"  href=\"".serveur."index.php?action=reinit_mdp&code=".encrypt($code)."\"> ici</a>' .";
								$synth .="<p><br> Si vous n'êtes pas à l'origine de cette demande, cliquez <a  id=\"alerte\"  href=\"".serveur."index.php?action=alerte_admin&motif=".encrypt("reinit_mdp avec $code")."\"> ici</a>' .";
								$dest = "$mail";

								echo "<p><br><p>Un mail contenant un lien, valable uniquement aujourd'hui,<p> permettant de réinitialiser votre mot de passe a été envoyé à $mail. ";
								envoi_mail( $dest , "Information pour $id", "$synth" );		
								ajout_log( $id, "Lien pour reinitialisation mail envoyé à l'adresse $mail" );
								}
							else
								erreur("Désolé compte protégé");
							}
						else
							echo "<p><br><p><br><p>Vous avez demandé la réinitialisation de votre mot de passe mais un mail vous a déjà été envoyé aujourd'hui. <p><br><p><br>"; 
						}
					else
						{
						$reponse =command("","select * from r_dde_acces where user='$idx1' and user<>autorise and type='' and  date_dde>='$date_jour'  ");
						if (! ($donnees = mysql_fetch_array($reponse) )  )
							{
							echo "<p><br>Cliquez sur <img src=\"images/contact.png\" width=\"25\" height=\"25\" > correspondant à la personne que vous allez contacter pour vous aider à récupérer votre mot de passe. ";

							echo "Après avoir cliqué sur le lien, vous aurez 5 jours pour la contacter en personne. Elle s'assurera de votre identité. ";
							echo "Elle vous communiquera alors le code de déverouillage (mais elle ne connaitra pas votre mot de passe). ";
							titre_referent("","x");
							$reponse =command("","select * from  r_referent where user='$idx1' ");
							while ($donnees = mysql_fetch_array($reponse) ) 
								{
								$organisme=stripcslashes($donnees["organisme"]);
								$idx=$donnees["idx"];
								$nom=$donnees["nom"];
								if ($nom!="Tous")
									{
									if ($organisme!="")
										visu_referent($idx,$idx1);
									}
								else
									{
									$r1 =command("","select * from  r_user where organisme='$organisme' and droit='S' ");
									while ($d1 = mysql_fetch_array($r1) ) 
										{
										$idx=$d1["idx"];
										$nom=$donnees["nom"];
										visu_referent_user($idx,$idx1);
										}								
									
									}
								}
							echo "</table></div>";					
							}
						else // cas où il y  déjà une demande en cours
							{
							echo "<p><br>Il y a dejà une demande en cours auprès de ";
							$ddeur=$donnees["ddeur"];
							titre_referent("","x");
							$reponse =command("","select * from  r_referent where user='$idx1' and nom='$ddeur' ");
							while ($donnees = mysql_fetch_array($reponse) ) 
								{
								$organisme=stripcslashes($donnees["organisme"]);
								$idx=$donnees["idx"];
								if ($organisme!="")
									visu_referent($idx,$idx1,"x");
								}
							echo "</table></div>";						
							echo "<p><br><p> Contacter cette personne, elle s'assurera de votre identité. ";
							echo "<br> Puis, elle vous communiquera votre code de déverouillage (mais elle ne connaitra pas votre mot de passe). ";
							echo "<p> Si vous n'arrivez pas à la joindre et que vous voulez contacter un autre référent de confiance,";
							echo "<br>attendez que le delais initial soit écoulé pour faie une nouvelle demande. ";
							echo "<br><p><br><p><a href=\"index.php?action=dde_mdp_avec_code\"> <img src=\"images/code.png\" width=\"35\" height=\"35\" >Si vous avez déjà reccueilli le code de déverouillage, cliquez ici</a><p><br><p><br></center>";
						
							}
						}
					pied_de_page("x");
					}
				else
					{
					erreur( "Plusieurs comptes correspondent à cette référence"); 
					$action="dde_mdp2";
					}
				}
			else
				{
				erreur( "Identifiant ou mail inconnu"); 
				$action="dde_mdp2";
				}			
			}	
		}

		
		if (($action=="dde_mdp") || ($action=="dde_mdp2") )
			{
			if ($action=="dde_mdp")
				aff_logo();
			$id=variable('id');
			debut_cadre();
			echo "<p><br><TABLE>";
			formulaire ("envoi_mdp");
			echo "<TR> <td><center>Si vous avez oublié votre mot de passe, saisissez votre identifiant,<p> votre adresse mail ou numéro de téléphone.</td>";
			echo "<TR> <td><center><input class=\"center\" type=\"text\" size=\"30\" name=\"id\" value=\"$id\"/>";
			echo " <input type=\"submit\"  id=\"envoi_mdp\"  value=\"Valider\"><p></td>";
			echo "</form> </table>";
			fin_cadre();
			echo "<br><p><a href=\"index.php?action=dde_identifiant\" > <img src=\"images/identifiant.png\" width=\"35\" height=\"35\" > Si vous avez oublié votre identifiant, cliquez ici. </a>";
			echo "<br><p><br><p><a href=\"index.php?action=dde_mdp_avec_code\"> <img src=\"images/code.png\" width=\"35\" height=\"35\" >Si vous avez déjà reccueilli le code de déverrouillage, cliquez ici</a><p><br><p><br></center>";
			pied_de_page("x");
			}	


	if ($action=="dx") 
		{
		if ( (isset ($_SESSION['pass'])) && ($_SESSION['pass']==TRUE)) 
				ajout_log( $_SESSION['user_idx'], 'Deconnexion' );
		$_SESSION['pass']=false;// et hop le mot de passe... poubelle !
		unset($_SESSION['profil']);	
		echo "<div id=\"msg_dx\">Vous êtes déconnecté!</div><br>";
		}

// ------------------------------------------------------------------------------ FIN des actions sans identification (pas de mot de passe)		

// ---------------------------------------on récupére les information de la personne connectée
if (isset($_POST['pass']))
	{
	$id=$_POST['id'];
	$reponse = command( "","SELECT * from  r_user WHERE id='$id' "); 
	$donnees = mysql_fetch_array($reponse);
	$mot_de_passe=$donnees["pw"];	
	$id=$donnees["id"];
	$date_log=date('Y-m-d');	
	$heure_jour=date("H\hi.s");	
	$_SESSION['bene']="";
	// verifion si la variable = mot de passe...
		if ( ( 
			(encrypt($_POST['pass'])==$mot_de_passe) // on vérifie le mot de passe 
			||
			// cas particulier en mode poste de développement on vérifie aussu un mot de passe en clair 
			(($_POST['pass']==$mot_de_passe) && ($_SERVER['REMOTE_ADDR']=="127.0.0.1")	 )
			) && ( !strstr($donnees["droit"] ,"-" ) ) ) // ceux qui sont désactivé ne peuvent pas accéder
			{
			supp_echec_cx ($_POST['id']);
			$ip= $_SERVER["REMOTE_ADDR"];
			supp_echec_cx ($ip);
			$_SESSION['pass']=true;	 
			$idx=$donnees["idx"];
			$_SESSION['user']=$idx;	 
			ajout_log( $idx, 'Connexion' );
			if (decrypt($mot_de_passe)=="123456") 
				{
				$action="modif_mdp";
				$identifiant=$id;
				}
				
			$ancien_droit="";
			if (isset($_SESSION['droit']))
				$ancien_droit=$_SESSION['droit'];
				
			$_SESSION['user_idx']=$donnees["idx"];
			$_SESSION['droit']= $donnees["droit"];
			$_SESSION['profil']= $donnees["droit"];
			
			if (($donnees["droit"]=="A") && ($ancien_droit!="A"))
				envoi_mail( parametre('DD_mail_gestinonnaire') , "Connexion administrateur", "IP : $ip" );		
			
			// supprime les demandes de recupération de mot de passe encore actif 
			$reponse =command("","UPDATE r_dde_acces set type='-' where user='$idx' and type='' and date_dde>='$date_log' ");
			$label = libelle_user($idx);
			$last_cx = "";
			$reponse =command("","select * from  log where ( user='$idx' or user='$id'or user='$label'  ) and  (ligne regexp 'Connexion') and  (not (ligne regexp 'Deconnexion')) and ( not (ligne regexp 'Echec Connexion'))  order by date DESC ");		
			if ($donnees = mysql_fetch_array($reponse))// c'est la connexion actuelle
				if ($donnees = mysql_fetch_array($reponse) )// c'est la connexion précédente
					{
					$last_cx=$donnees["date"];
					if ($last_cx!="")
						{
						maj_last_cx($idx);
						$ligne_last_cx = "Dernière connexion :<br> $last_cx. ";
						
						$reponse =command("","select * from  log where ( user='$idx' or user='$id'  or user='$label' ) and  (ligne regexp 'Echec Connexion') order by date DESC ");		
						$donnees = mysql_fetch_array($reponse); 
						$last_echec_cx=$donnees["date"];	
						if ($last_echec_cx>$last_cx)
							echo "Depuis votre derniére connexion, il y a eu tentative de connexion à votre compte, merci de consulter votre <a href=\"index.php?action=histo\"  >historique</a> ";
						}
					}
			ctrl_signature_user( $idx );
			
			// verification que les dates CG n'ont pas changé depuis la derniere connexion
			// si c'est le cas on en infome l'utilisateur
			$date_cg=parametre("DD_date_cg");			
			if ($last_cx<$date_cg)
				echo "Les conditions générales de 'doc-depot.com' ont changé, merci d'en prendre connaissance en cliquant <a href=\"conditions.html\"  >ici</a> ";

			}
		else
			{	
			$id=$_POST['id'];

			tempo_cx ($id);
			$ip= $_SERVER["REMOTE_ADDR"];
			tempo_cx ($ip);	
			erreur("Mot de passe incorrect !!"). 
			$_SESSION['pass']=false;
			ajout_log( $id," Echec Connexion  $id / $mot_de_passe /".$_POST['pass']);
			ajout_echec_cx ($_POST['id']);
			ajout_echec_cx ($ip);
			}
	}

	// ------------------------------------ on collecte les infos utiles du user connceté
	if (isset($_SESSION['user']))
		{
		$idx=$_SESSION['user'];
		$reponse = command( "","SELECT * from  r_user WHERE idx='$idx'"); 
		$donnees = mysql_fetch_array($reponse);
		$user_idx=$donnees["idx"];
		$_SESSION['acteur']=$user_idx; // utilisé par le upload en mode drag and drop 
		$id=$donnees["id"];
		$pw=$donnees["pw"];
		$user_nom=$donnees["nom"];
		$user_prenom=$donnees["prenom"];
		$user_droit=$donnees["droit"];
		$user_anniv=$donnees["anniv"];
		$user_telephone=$donnees["telephone"];
		$user_mail=$donnees["mail"];
		$user_lecture=$donnees["lecture"];
		$user_nationalite=$donnees["nationalite"];
		$user_ville_nat=$donnees["ville_nat"];
		$user_adresse=stripcslashes($donnees["adresse"]);
		$user_organisme=stripcslashes($donnees["organisme"]);
		if ($user_droit=="S")
			$doc_autorise=doc_autorise($user_organisme);
		else
			$doc_autorise="";
		}

	if ( !isset($_SESSION['pass']) ||($_SESSION['pass']==false) || !(isset($_SESSION['user'])) || ($_SESSION['user']=="") )
		// si pas de valeur pass en session on affiche le formulaire...
		{
		aff_logo("x");
		debut_cadre();
		echo "<br><TABLE><TR> <td> <img src=\"images/identification.png\"  width=\"50\" height=\"50\"  > </td> <td>";
		echo "<TABLE><TR> <td><form class=\"center\"  method=\"post\"> Identifiant : </td><td><input required type=\"text\" name=\"id\" value=\"\"/></td>";
		echo "<TR> <td>Mot de passe: </td><td><input required  id=\"pwd2\"  type=\"password\" name=\"pass\"  autocomplete=\"off\" value=\"\"/>";
		echo "<td><input type=\"checkbox\" onchange=\"document.getElementById('pwd2').type = this.checked ? 'text' : 'password'\"> Voir saisie<td>";
		echo "<input  type=\"hidden\" name=\"action\" value=\"\"/></td>";
		echo "<TR> <td></td><td><input type=\"submit\" value=\"Se connecter\"/><p></td>";
		echo "</form> </table> </table> ";
		fin_cadre();
		echo "<br><p><br><a href=\"index.php?action=dde_mdp\" > <img src=\"images/oubli.png\" width=\"35\" height=\"35\" > Si vous avez oublié votre mot de passe, cliquez ici. </a><p><p>";
		echo "<p><br><p><p><br></center></div>";
		pied_de_page();
		} 
	
	$user=variable("user"); 
	if ($user=="")
		if (isset ($_SESSION['user']))	$user=$_SESSION['user']; else $user="";
	$memo=variable("memo");	
	$filtre=variable("filtre");	
	
	if (!isset ($_GET["date_jour"]))
		$date_jour=date('Y-m-d');
	else 
		$date_jour=variable("date_jour");


	if ($action=="detail_user")
		$_SESSION['bene']=variable("user");
	if ($action=="")
		$_SESSION['bene']=$user_idx;
	if (($user_droit=="R") || ($user_droit=="A")) 
		$_SESSION['bene']="";
	
	
	if ($action=="recept_mail")
		recept_mail($_SESSION['user_idx'],$date_jour);	
	
	if ($action=="supp_recept_mail")
		supp_recept_mail($_SESSION['user_idx']);
		
	if (($action=="user_inactif") && ($user_droit=="R"))
		maj_droit(variable("idx"),"s");
			
	if ( ($action=="user_actif") && ($user_droit=="R"))
		maj_droit(variable("idx"),"S");
			
	if (($action=="dde_chgt_cle") && ($user_droit=="A"))
		dde_chgt_cle();
		
	if (($action=="chgt_cle") && ($user_droit=="A"))
		chgt_cle();
		
	if ($action=="supp_upload")
		{
		$num=variable("num");
	
		$reponse =command("","select * from r_attachement where  num='$num' ");
		if ($donnees = mysql_fetch_array($reponse) )
				{
				$type=$donnees["type"];		
				
				supp_attachement ($num);
				$num = substr($num,strpos($num,".")+1 );
				if ($type=="A")
					ajout_log( $user_idx, "Suppression du fichier '$num' (Espace partagé)" );	
				else
					ajout_log( $user_idx, "Suppression du fichier '$num' (espace personnel)" );	
				$action=variable("retour");
				}
		}
			
	if ($action=="autorise_recup_mdp")
		{
		autorise_acces(variable("autorise"),variable("bene"),"");
		$action="detail_user";
		$user=variable("bene");
		}
		
	if ($action=="supp_recup_mdp")
		{
		supp_recup_mdp(variable("autorise"),variable("bene"));	
		$action="detail_user";
		$user=variable("bene");	
		}		
		
	if ($action=="autorise_acces")
		autorise_acces(variable("ddeur"), variable("bene"),variable("autorise"));
		
	if ($action=="supp_acces")
		supp_acces(variable("ddeur"),variable("bene"),variable("autorise"));

	if (($action=="supp_compte")  && ($user_droit==""))
		{
		if (encrypt(variable("pw"))==$donnees["pw"]) 
			{
			if ( compte_non_protege($id) )
				{
				aff_logo();
				maj_droit("$user_idx","$user_droit-");
				supp_tous_fichiers($user_idx);			
				command("","delete from r_sms where idx='$user_idx' ");
				$_SESSION['pass']=false;	
				echo "<hr><p><br><p> Suppression de compte réalisée!";
				pied_de_page("x");
				}
			else
				erreur ("Suppression impossible car compte protégé. ");
			}
		else
			{
			erreur ("Code Incorrect ");
			$action="supp_compte_a_confirmer";
			}
		}
		
	if ($action=="modif_type_doc")	
		modif_type_doc(variable("num"), variable("type"));


	if ($action=="modif_domicile")
		modif_domicile(variable("idx"), variable("organisme"),variable("adresse"));

	if ($action=="modif_organisme")
		modif_organisme(variable("id"),variable("telephone"),variable("mail"),variable("adresse"),variable("sigle"),variable("doc"));

	if ($action=="nouveau_organisme") 
		nouveau_organisme(variable("organisme"), variable("tel"),variable("mail"),variable("adresse"),variable("sigle"),variable("doc"));

	if ($action=="supp_organisme")
		supp_organisme(variable("idx"));
		
	if ($action=="supp_affectation")
		supp_affectation(variable("organisme"),variable("user"));

	if ($action=="nouvelle_affectation")
		nouvelle_affectation(variable("organisme"), variable("responsable"));

	if (($action=="nouveau_referent") && ($user_droit==""))
		{
		nouveau_referent(variable("user"),variable("organisme"), variable("nom"), variable("prenom"),variable("tel"),variable("mail"),variable("adresse"));
		$action="ajout_referent";
		}

	if ( ($action=="supp_referent") && ($user_droit==""))
		supp_referent(variable("idx"));

	if (($action=="nouveau_user") && ($user_droit!=""))
		{
		$idx1= nouveau_user(variable("id"),variable("pw"),variable("droit"),variable("mail"),variable("organisme"),variable("nom"),variable("prenom"),mef_date(variable("anniv")),variable("telephone"),variable("nationalite"),variable("ville_nat"),variable("adresse"),variable("recept_mail"),variable("prenom_p"),variable("prenom_m"),variable("code_lecture"),variable("nss"));
		if ( ($idx1!="") && (variable("droit")=="") )
			// par défaut on impose le créateur comme référent de  confiance
			nouveau_referent($idx1 ,$user_organisme, $user_idx, "", "","","");
		$action="";
		}

	if (($action=="modif_user") && (($user_droit=="R") || ($user_droit=="A") ) )
		{
		modification_user(variable("idx"),variable("nom"),variable("prenom"),variable("telephone"),variable("mail"),variable("droit"));
		$action="";
		}		
		
	if ( ($action=="supp_user") &&(  ($user_droit=="A") || ($user_droit=="R") ) )
		supp_user(variable("idx"));
		
	if (($action=="modif_profil") && ( ($user_droit=="R") || ($user_droit=="A") ) )
		{
		$_SESSION['profil']=variable("profil");
		$action="";
		}

	if ($action=="modif_mdp")
		{
		aff_logo();
		
		debut_cadre("500");
		echo "<p><br>Modification de votre mot de passe";
		echo "<p>Au minimum 8 caractères, au moins une majuscule, une minuscule et un chiffre (caractères spéciaux possibles)";
		
		echo "<TABLE><TR><td>";
		formulaire ("changer_mdp");
		if (variable('ancien')=="123456")
			{
			echo "Identifiant: </td><td><input class=\"center\" type=\"text\" name=\"id\" value=\"\"/>	";
			echo "<input  type=\"hidden\" name=\"ancien\" value=\"123456\"/></td>";
			}
		else
			{
			echo " <input  type=\"hidden\" name=\"idx\" value=\"$idx\"/>";
			echo "<TR> <td>Ancien: </td><td><input class=\"center\" id=\"pwd1\" type=\"password\" name=\"ancien\" value=\"\"/></td>";
			}
		echo "<TR> <td>Nouveau</td><td><input class=\"center\" id=\"pwd2\" type=\"password\" name=\"n1\" value=\"\"/></td>";
		echo "<td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' ; ; document.getElementById('pwd2').type = this.checked ? 'text' : 'password'\"> Voir saisies<td>";

		echo "<TR> <td>Confirmation</td><td><input class=\"center\" id=\"pwd\" type=\"password\" name=\"n2\" value=\"\"/></td>";
		echo "<TR>  <td></td><td><input type=\"submit\"  id=\"changer_mdp\"  value=\"Modifier\"/><br><p></td>";
		echo "</form> </table> ";
		fin_cadre();
		pied_de_page("x");
		}	
				
	if(($action=="init_selenium") && ($user_droit=="A"))
		{
		echo "Init Selenium:";
		
		command("","delete from r_organisme where organisme REGEXP 'SELENIUM' ");
		
		$reponse =command("","select * from  r_user where nom REGEXP 'SELENIUM' or id REGEXP 'SELENIUM'  ");		
		while ($donnees = mysql_fetch_array($reponse) ) 
			{	
			$idx= $donnees["idx"];
			command("","delete from r_lien where user='$idx' ");
			command("","delete from r_dde_acces where user='$idx' or  ddeur='$idx' ");
			command("","delete from r_referent where user='$idx' or  idx='$idx' ");
			command("","delete from r_sms where idx='$idx' ");
			command("","delete from log where user='$idx' or  acteur='$idx'  ");
			supp_tous_fichiers($idx);
			command("","delete from r_user where idx='$idx'  ");
			command("","trunc table log ");
			command("","trunc table z_log_t  ");
			
			echo "<br>User $idx  Ok.";
			}
		echo "<p>ok.";
		exit();
		}
	
	// pour l'adminitrateur  on fait une vérification de la mise à jour des BdD
	if(($user_droit=="A"))
		include ("maj_bdd.php");

		// ===================================================================== Bloc IMAGE
		$reponse = command( "","SELECT * from  r_user WHERE idx='$idx'"); 
		$donnees = mysql_fetch_array($reponse);
		$user_idx=$donnees["idx"];
		$id=$donnees["id"];
		$user_nom=$donnees["nom"];
		$user_prenom=$donnees["prenom"];
		$user_droit_org=$donnees["droit"];
		
		if (!isset($_SESSION['profil']))
			$user_droit=$donnees["droit"];
		else
			$user_droit=$_SESSION['profil'];
		$user_anniv=$donnees["anniv"];
		$user_telephone=$donnees["telephone"];
		$user_mail=$donnees["mail"];
		$user_nationalite=$donnees["nationalite"];
		$user_ville_nat=$donnees["ville_nat"];
		$user_adresse=stripcslashes($donnees["adresse"]);
		$user_organisme=stripcslashes($donnees["organisme"]);	
		$code_lecture=$donnees["lecture"];	
		$user_lecture=$donnees["lecture"];
		
		supp_fichier("tmp/A-$user_idx.pdf");	
		supp_fichier("tmp/_A-$user_idx.pdf");	
	
		echo "<table border=\"0\" >";	
		echo "<tr> <td> ";		
		echo "<a href=\"index.php\" > ";
		echo "<img id=\"logo\" src=\"images/logo.png\" width=\"140\" height=\"100\" ></a> </td> ";	
		if ($_SERVER['REMOTE_ADDR']=="127.0.0.1")
			echo "<td> <table><tr> <td align=\"center\" > <table><tr><td align=\"center\" bgcolor=\"lightgreen\" ><b>$user_nom - $user_prenom </b></td>";
		else
			echo "<td> <table><tr><td align=\"center\"> <table><tr><td align=\"center\" ><b>$user_nom - $user_prenom </b></td>";
			
		if (($user_droit_org=="R") || ($user_droit_org=="A") )
			{
			echo "<td>Profil :</td><td>";
			if (!isset($_SESSION['profil']))
				liste_profil($user_droit_org,$user_droit_org);
			else
				liste_profil($user_droit_org,$_SESSION['profil']);
			echo "</td>";

			}
		echo "<td>";

		if ($user_droit=="s")
			echo "- ( <img src=\"images/inactif.png\"width=\"20\" height=\"20\"> Compte inactif ) ";
		if($user_droit=="")
			echo "- ( $user_anniv ) ";

		echo "</td></table></td>";
		
		echo "<tr><td><hr>";

		if ($action=="upload")
			traite_upload($user_idx, $code_lecture, variable ("idx") );
			
		if ($action=="modif_tel")
			{
			if (modif_tel(variable("idx"), variable("telephone"),variable("telephone2")) )
				{
				$user_telephone=variable("telephone");
				$user_mail=variable("telephone2");	
				}
			}
		
		echo "</td><tr> ";

		if (($action=="") || ($action=="modif_tel")|| ($action=="modif_domicile"))
			{
			formulaire ("modif_tel");
			echo "<input type=\"hidden\" name=\"idx\" value=\"$idx\"> " ;
			if ($user_droit=="")
				{
				echo "<td> <img src=\"images/telephone.png\" width=\"25\" height=\"25\" > Tel :<input type=\"texte\" name=\"telephone\"   size=\"15\" value=\"$user_telephone\" onChange=\"this.form.submit();\"> " ;
				echo " - <img src=\"images/mail.png\" width=\"25\" height=\"25\" > Mail : <input type=\"texte\" name=\"telephone2\"   size=\"30\" value=\"$user_mail\" onChange=\"this.form.submit();\"> " ;
				}
			else
				{
				echo "<td> <img src=\"images/telephone.png\" width=\"25\" height=\"25\" > Tel pro:<input type=\"texte\" name=\"telephone\"   size=\"15\" value=\"$user_telephone\" onChange=\"this.form.submit();\"> " ;
				echo " - <img src=\"images/mail.png\" width=\"25\" height=\"25\" > Mail pro. : <input type=\"texte\" name=\"telephone2\"   size=\"30\" value=\"$user_mail\" onChange=\"this.form.submit();\"> " ;
				}		
			echo "</form></table> </td>";
			}
		else
			{
			if ($user_droit=="")
				{
				echo "<td> <img src=\"images/telephone.png\" width=\"25\" height=\"25\" > Tel : $user_telephone " ;
				echo " - <img src=\"images/mail.png\" width=\"25\" height=\"25\" > Mail : $user_mail" ;
				}
			else
				{
				echo "<td> <img src=\"images/telephone.png\" width=\"25\" height=\"25\" > Tel pro: $user_telephone " ;
				echo " - <img src=\"images/mail.png\" width=\"25\" height=\"25\" > Mail pro. : $user_mail" ;
				}		
			echo "</table> </td>";
			}
		
		echo "<td> <ul id=\"menu-bar\">";
		echo "<li><a href=\"index.php?action=dx\"  > Deconnexion</a>";
		echo "<ul >";
		echo "<li><a href=\"index.php?action=modif_mdp\"  > Modification mot de passe</a></li>";
		echo "<li><a href=\"index.php?action=histo\"  > Historique </a></li>";
		if ($user_droit=="")
			{
			echo "<li><a href=\"index.php?action=supp_compte_a_confirmer\"  > Suppression compte </a></li>";
			echo "<li><a href=\"index.php?action=exporter_a_confirmer\"> Tout exporter </a></li>";
			}		

		if  ($user_droit=="A") 
			{
			echo "<li><a href=\"index.php?action=dde_chgt_cle\"> Changement clé (en dev) </a></li>";
			}
			
		echo "</ul></li>";
		if (isset($ligne_last_cx)) 
			if ($ligne_last_cx!="")
				echo "<br>$ligne_last_cx";
	
		echo "</td><td> </td>";
		if ($user_droit=="")
			echo "<td><ul id=\"menu-bar\"> <li><a href=\"aide_b.html\" target=_blank > Aide</a>";
		if ($user_droit=="S")
			echo "<td><ul id=\"menu-bar\"> <li><a href=\"aide_as.html\" target=_blank > Aide</a>";
		if ($user_droit=="R")
			echo "<td><ul id=\"menu-bar\"> <li><a href=\"aide_r.html\" target=_blank > Aide</a>";		
//		echo "<ul >";
//		echo "<li><a href=\"index.php?action=faq\"  > Questions fréquentes </a></li></ul>";
		echo "</td>";

		
		if ($user_droit!="")
			{
			$r1 =command("","select * from  r_organisme where idx='$user_organisme' ");
			$d1 = mysql_fetch_array($r1);
			$logo=$d1["logo"];
			if ($logo!="")
				echo "<td> <img src=\"images/$logo\" width=\"200\" height=\"100\"  > </td>";
			}
	
		echo "</table>";

		

		
		if ($user_droit=="")	 
			// on n'affiche au bénéficiaire sa domiciliation que sur l'écran d'accueil 
			if (($action=="") || ($action=="modif_tel")|| ($action=="modif_domicile"))
				{
				echo "<table><tr><td> <img src=\"images/maison.png\" width=\"25\" height=\"25\" >  Domiciliation postale : </td>";
				formulaire ("modif_domicile");
				liste_organisme($user_organisme,"1");
				if($user_droit=="")
					echo "<tr><td> <img src=\"images/enveloppe.png\" width=\"25\" height=\"25\" >  Adresse postale:</td><td> <input type=\"texte\" name=\"adresse\" id=\"ReversBE\" onfocus=\"javascript:if(this.value=='ReversBE')this.value='';\"   size=\"80\" value=\"$user_adresse\" onChange=\"this.form.submit();\"> " ;
				else
					echo "<input type=\"hidden\" name=\"adresse\"  value=\"\" > " ;
				echo "<input type=\"hidden\" name=\"idx\" value=\"$idx\"> " ;
				echo "</form> </table>";
				}	
		if (($user_droit=="E") || ($user_droit=="A") || ($user_droit=="F") || ($user_droit=="T"))
			affiche_alarme();
		
		echo "<hr>";

	if (($action=="phpinfo") && ( ($user_droit=="A") || ($user_droit=="E")) )
		{
		phpinfo();
		pied_de_page();
		}
		
	if (($action=="archivage_php") &&  ($user_droit=="E")) 
		{
		archivage_php();
		echo "<p> Sauvegarde Tables ";
		backup_tables(false);
		pied_de_page();
		}		
		
		if ($action=="supp_upload_a_confirmer")
			{
			echo "<hr><p>Attention, vous avez demandé la suppression de <table><tr>";
			$num=variable('num');
			visu_doc($num,"");
			echo "<tr><td> Confirmez-vous la suppression ? : </td>";
			lien_c ("images/oui.png","supp_upload",param("num",$num). param("retour",variable("retour")));
			echo "</table></div><p><p><p>";
			pied_de_page("x");
			}	
			

		
		// !!!!!!!!!!!!! ZONE COMPLEXE  !!!!!!!!!!!
		if (($_SESSION['bene']!="") && ($action!="") && ($action!="dde_acces") && ($user_droit=="S"))
			{
			if (($action!="ajout_admin") &&  ($action!="draganddrop") &&  ($action!="rdv") &&  ($action!="ajout_rdv"))
				$action="detail_user";
			$user=$_SESSION['bene'];
			}
		else
			$_SESSION['bene']="";
		// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

	if ($user_droit=="E") 
			{
			echo "<table><tr><td width=\"400\">";
			echo " <ul id=\"menu-bar\">";
			echo "<li><a href=\"index.php\"> Journaux </a><ul>";
			echo "<li><a href=\"index.php?action=afflog_t\"> Log technique </a></li>";
			echo "<li><a href=\"index.php?action=afflog\"> Log Fonctionnel</a></li>";		
			echo "<li><a href=\"tmp/log.txt\"> Aujourd'hui</a></li>";		
			echo "<li><a href=\"tmp/hier.txt\"> Hier</a></li>";		
			echo "</li></ul>";			
			echo "<li><a href=\"index.php?action=liste_compte\"> Liste User</a></li>";
			echo "</li><li> <a href=\"index.php\">CTRL</a><ul>";			

			echo "<li><a href=\"index.php?action=en_trop\"> Fichiers en trop </a></li>";
			echo "<li><a href=\"index.php?action=authenticite \"> Aunthenticité </a></li>";
			echo "<li><a href=\"index.php?action=integrite \"> Intégrité BdD </a></li>";
			echo "</ul><li> <a href=\"index.php?action=param_sys\">Paramètrage</a><ul>";			

			echo "<li><a href=\"index.php?action=phpinfo\"> Phpinfo </a></li>";
			echo "<li><a href=\"index.php?action=archivage_php\"> Archivage Php+Sql </a></li>";
			echo "</ul></ul >";
			echo "<td></table>";

			}		

		if ($user_droit=="T") 
			{
			echo "<table><tr><td width=\"400\">";
			echo " <ul id=\"menu-bar\">";
			echo "<li><a href=\"index.php?action=init_formation\"> Init comptes </a></li>";			
			echo "<li><a href=\"index.php?action=raz_mdp_formation\"> Init Mots de passe</a></li>";
			echo "</ul>";
			echo "<td></table>";
			}		

			
	if (($action=="supp_filtre") && ($user_droit=="F"))
		{
		$_SESSION["filtre"]="";
		$action="";
		}		
		
	if (($action=="") && ($user_droit=="E"))
		{
		indicateurs();
		pied_de_page("x");
		}		
		
	if (($action=="en_trop") && ($user_droit=="E"))
		{
		fichiers_en_trop();
		pied_de_page("x");
		}		

	if (($action=="integrite") &&($user_droit=="E"))
		{
		verif_integrite_bdd();
		pied_de_page("x");
		}	
		
	if (($action=="authenticite") &&  ($user_droit=="E"))
		{
		echo "Controle Aunthenticité des fichiers";
		ctrl_signature(true);
		pied_de_page("x");
		}	

		
	if (($action=="raz_mdp1") &&  ($user_droit=="E"))
		{
		$idx=variable('idx');
		$reponse =command("","SELECT * from  r_user WHERE idx='$idx'"); 
		if ($donnees = mysql_fetch_array($reponse))
			{
			$id=$donnees["id"];	
			debut_cadre();
			echo "<p><br>Réinitialisation de mot de passe <p> Compte : $id <br><p>";
			echo "<TABLE><TR>";
			formulaire ("raz_mdp");
			echo "<TR> <td>Nouveau mot de passe </td><td><input  id=\"pwd\" type=\"password\" name=\"mdp\" value=\"\"/></td>";
			echo "<td><input type=\"image\" src=\"images/oui.png\" width=\"20\" height=\"20\"  >";
			echo "<tr><td></td><td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> Voir saisie<td>";
			echo "<input type=\"hidden\" name=\"idx\" value=\"$idx\"> " ;
			echo "</form> </table> ";
			fin_cadre();
			pied_de_page("x");
			}
		}				
	
	// permet la reinitialisation d'un mot de passe paticilier d'un compte 
	if (($action=="raz_mdp") &&  ($user_droit=="E"))
		{
		$idx=variable('idx');
		$reponse =command("","SELECT * from  r_user WHERE idx='$idx'"); 
		if ($donnees = mysql_fetch_array($reponse))
			{
			$id=$donnees["id"];	
			$mdp=variable('mdp');
			echo "Réinitilaition du mot de passe de '$id' faite (Mdp = '$mdp')";
			ajout_log_tech("Réinitilaition du mot de passe de '$id' faite par exploitant. (Mdp = '$mdp')");
			$mdp=encrypt($mdp);
			$reponse =command("","UPDATE r_user set pw='$mdp' where idx='$idx'");
			$action="liste_compte";
			}
		}				
	
	if ( ($action=="liste_compte") && ($user_droit=="E"))
			{
			echo "</table><div class=\"CSSTableGenerator\" > ";
			echo "<table><tr><td > n°  </td><td> Creation</td><td> Nom</td><td> Prénom</td><td> Mail</td><td> Tel</td><td> Droit</td>";
			$reponse =command("","select * from  r_user order by idx desc");		
			while ($donnees = mysql_fetch_array($reponse) ) 
				{
				$idx1=$donnees["idx"];	
				$creation=$donnees["creation"];
				$nom=$donnees["nom"];
				$prenom=$donnees["prenom"];
				$mail=$donnees["mail"];
				$tel=$donnees["telephone"];
				$droit=$donnees["droit"];
				echo "<tr><td>  $idx1   </a></td><td> $creation </td><td> $nom </td><td> $prenom </td><td> $mail </td><td> $tel </td><td> $droit </td>";

				lien_c ("images/illicite.png", "raz_mdp1", param("idx","$idx1" ) , "Raz MdP");
				}
			echo "</table></div>";
			pied_de_page("x");
			}	

			
		if (($action=="afflog") &&  ($user_droit=="E"))
			{
			echo "</table> ";
			$filtre1=variable("filtre");
			formulaire ("afflog");
			echo "<table><tr> <td>Filtre : <input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
			echo "</form> </td></table> ";
			
			echo "<div class=\"CSSTableGenerator\" ><table><tr><td > Date </td><td> IP</td><td> Action </td><td> Compte </td><td> Acteur </td>";
			if ($filtre1=="")
				$reponse =command("","select * from  log  order by date desc limit 0,1000");		
			else
				$reponse =command("","select * from  log where (date REGEXP '$filtre1' or ligne REGEXP '$filtre1' or user REGEXP '$filtre1' or acteur REGEXP '$filtre1' or ip REGEXP '$filtre1') order by date desc");		
			while ($donnees = mysql_fetch_array($reponse) ) 
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
			echo "</table></div>";
			pied_de_page("x");
			}
			
		if (($action=="afflog_t") && ($user_droit=="E"))
			{
			echo "</table> ";
			$filtre1=variable("filtre");
			formulaire ("afflog_t");
			echo "<table><tr> <td>Filtre : <input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
			echo "</form> </td></table> ";
			
			echo "<div class=\"CSSTableGenerator\" ><table><tr><td > Date  </td><td> Prio </td><td> Evénement </td><td> Ip </td>";
			if ($filtre1=="")
				$reponse =command("","select * from  z_log_t  order by date desc limit 0,1000");		
			else
				$reponse =command("","select * from  z_log_t where (date REGEXP '$filtre1' or ligne REGEXP '$filtre1' or ip REGEXP '$filtre1'or prio REGEXP '$filtre1') order by date desc");		
			while ($donnees = mysql_fetch_array($reponse) ) 
				{
				$date=$donnees["date"];	
				$ligne=$donnees["ligne"];
				$ip=$donnees["ip"];
				$prio=$donnees["prio"];
				echo "<tr><td>  $date   </a></td><td> $prio </td><td> $ligne </td><td> $ip </td>";
				}
			echo "</table></div>";
			pied_de_page("x");
			}
			
		
	if ($action=="histo")
		histo_beneficiaire($user_idx, $id);
		

	if ( ($action=="membres_organisme") && ( ($user_droit=="A") || ($user_droit=="S") ) )
			{
			$organisme=variable_get("organisme");
			echo libelle_organisme ($organisme); 
			echo "<div class=\"CSSTableGenerator\" ><table><tr><td > Droit  </td><td > Identifiant  </td><td> Nom </td><td> Prenom </td><td> telephone </td><td> Mail </td>";
			$reponse =command("","select * from r_lien where organisme='$organisme' ");		
			while ($donnees = mysql_fetch_array($reponse) ) 
				{
				$idx=$donnees["user"];	
				affiche_membre($idx);
				}			
				
			$reponse =command("","select * from r_user where (organisme='$organisme' and (droit='S' or droit='s') )");		
			while ($donnees = mysql_fetch_array($reponse) ) 
				{
				$idx=$donnees["idx"];	
				affiche_membre($idx);
				}
			echo "</table></div>";
			pied_de_page("x");
			}	
			
		if (($action=="renvoyer_mail") &&( ($user_droit=="R") || ($user_droit=="A")))
			{
			$idx=variable_get('idx');
			$mail=mail_user($idx);
			$body="Création de compte sur 'Doc-depot.com': <p>Pour accepter et finaliser la création de votre compte sur 'Doc-depot.com', merci de cliquer sur ce <a   id=\"lien\"  href=\"".serveur."index.php?action=finaliser_user&idx=".addslashes(encrypt($idx))."\">lien</a> et compléter les informations manquantes.";
			$body .= "<p> Message de la part de $user_prenom $user_nom";
			$body .= "<p> <hr> <center> Copyright ADILEOS 2014 </center>";			// Envoyer mail pour demander saisie pseudo et PW
			envoi_mail($mail,"Création compte",$body);
			ajout_log( $idx, "Renvoi mail de finalisation de compte : $mail", $user_idx );
			}

		if (($action=="modification_lecture") && ($user_droit==""))
			{
			$action="visualisation_lecture2";
			$ok=FALSE;
			$reponse = command( "","SELECT * from  r_user WHERE id='$id'"); 
			if ($donnees = mysql_fetch_array($reponse))
				{
				$ancienne_lecture=$donnees['lecture'];
				$mdp=variable('n1');
				if ($mdp==variable('n2') )
					{
					if (strlen($mdp)==0)
						{
						command( "","UPDATE r_user set lecture='$mdp' where id='$id'");
						echo "Modification effectuée: votre code de lecture est désactivé.";
						$ok=TRUE;
						ajout_log( $id, 'Effacement code lecture' );
						$action="";
						$code_lecture=$mdp;	
						$user_lecture=$mdp;
						}
					else
						if (strlen($mdp)>7 ) 
							{
							if ( testpassword($mdp)>50 ) 
								{
								$pw=$mdp; // passage du parametre en global 
								maj_mdp_fichier($user_idx, $mdp );
								$mdp=encrypt($mdp);
								command( "","UPDATE r_user set lecture='$mdp' where id='$id'");
								msg_ok( "Modification code lecture réalisée.");
								$ok=TRUE;
								ajout_log( $id, 'Changement code lecture' );
								$code_lecture=$mdp;	
								$user_lecture=$mdp;
								$action="";
								}
							else 
								erreur( "Le mot de passe n'est pas assez complexe (utiliser des Majuscules, Chiffres, caractéres spéciaux)");
							}
						else 
							erreur("Le mot de passe est trop court.");
					}
				else 
					erreur("Les 2 mots de passe ne sont pas identiques.");
				}
			else 
				erreur("Identifiant Incorrect.");
			}
				
		if ((($action=="visualisation_lecture") || ($action=="visualisation_lecture2")) && ($user_droit==""))
			{
			$reponse = command( "","SELECT * from  r_user WHERE id='$id' "); 
			$donnees = mysql_fetch_array($reponse);
			$mot_de_passe=$donnees["pw"];	
			if ( ($action=="visualisation_lecture2")
				||
				( ( (variable('pw')==$mot_de_passe) || (encrypt(variable('pw'))==$mot_de_passe)) && ( !strstr($donnees["droit"] ,"-" ) ) ) 
				)
				{
				echo "<hr>";
				debut_cadre();
				if ($code_lecture!="")
					{
					$cl=decrypt("$code_lecture");
					echo "<p><br><p>Votre code de lecture est : '".$cl."' <p>";
					}
				else
					echo "<p><br><p>Votre code de lecture n'est pas défini. <p><br><p>";
				
				echo "<hr><p>Modification de votre code de lecture des fichiers";
				echo "<TABLE><TR>";
				formulaire ("modification_lecture");
				echo "<TR> <td>Nouveau code de lecture </td><td><input  id=\"pwd\" type=\"password\" name=\"n1\" value=\"\"/></td>";
				echo "<TR> <td>Confirmation</td><td><input  type=\"password\" id=\"pwd1\" name=\"n2\" value=\"\"/></td>";
				echo "<td><input type=\"image\" src=\"images/oui.png\" width=\"20\" height=\"20\"  >";
				echo "<tr><td></td><td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> Voir saisie<td>";
				echo "</form> </table> ";
				fin_cadre();
				pied_de_page("x");
				}
			else
				{
				erreur ("Mot de passe incorrect");
				$action="visu_lecture";
				}

			}	
		if (($action=="visu_lecture") && ($user_droit==""))
			{
			echo "<hr><p><center>";
			debut_cadre("700");
			echo "<br><p>Vous souhaitez visualiser ou modifier votre code secret permettant la lecture des documents <table><tr>";
			formulaire ("visualisation_lecture");
			echo "<td> Votre mot de passe :</td><td> <input type=\"password\" id=\"pwd\" name=\"pw\"  value=\"\">  </td>";
			echo "<td><input type=\"image\" src=\"images/oui.png\" width=\"20\" height=\"20\"  >";
			echo "<tr><td></td><td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' \"> Voir saisie<p><td>";
			echo "</form> </table></div><p>";
			fin_cadre();
			pied_de_page("x");
			}	


			
		// si on arrive ici avec action=exporter c'est que l'export a echoué 
		if (( ($action=="exporter_a_confirmer") || ($action=="exporter")) && ($user_droit==""))
			{
			echo "<hr><center><p>";
			debut_cadre("700");
			echo "<p><br>Pour confirmer la génération d'un export, il vous faut saisir à nouveau votre mot de passe <p>";
			formulaire ("exporter");
			echo "<table><tr><td>Mot de passe : <input type=\"password\" name=\"pw\" id=\"pwd\" value=\"\">  </td>";
			echo "<td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> Voir saisie<td>";

			echo "<tr><td> Confirmez-vous la génération de l'export : </td>";
			echo "<td><br><input type=\"image\" src=\"images/oui.png\" width=\"20\" height=\"20\" title=\"Confirmer la génération de l'export\" >";
			echo "</form> </td>";
			echo "</table></div><p><p><p>";
			fin_cadre();
			echo "<p><center>Merci de noter que le fichier généré ne sera pas protégé par mot de passe. <p>";
			pied_de_page("x");
			}			
			
		if (($action=="supp_compte_a_confirmer") && ($user_droit==""))
			{
			echo "<hr><p><br><p><center>Attention, vous avez demandé la suppression de  votre compte.";
			echo "<p>Il est vivement recommandé de faire une sauvegarde de tous vos documents, photos et notes/sms en cliquant sur <a href=\"index.php?action=exporter_a_confirmer\">ce lien </a>";
			echo "puisqu'en supprimant le compte l'ensemble des informations et contenu sera détruit et ";
			echo "que cette action est irreversible.";
			debut_cadre("700");
			echo "<p><br>Si vous voulez confimer la suppression du compte, il faut saisir à nouveau votre mot de passe ";
			formulaire ("supp_compte");
			echo "<table><tr><td></td><td>Mot de passe : <input type=\"password\" name=\"pw\" id=\"pwd\"  value=\"\">  </td>";
			echo "<td> <input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> Voir saisie<td>";

			echo "<tr><td></td><td> Confirmez-vous la suppression : </td>";
			echo "<td><br><input type=\"image\" src=\"images/oui.png\" width=\"20\" height=\"20\" title=\"Confirmer la suppression\" >";
			echo "</form> </td>";
			echo "</table><p><p><p>";
			fin_cadre();
			pied_de_page("x");
			}

	
		if (($action=="supp_referent_a_confirmer") && ($user_droit==""))
			{
			echo "<hr><p>Attention, vous avez demandé la suppression du référent de confiance suivant :<p>";
			$idx=variable('idx');
			titre_referent("");
			visu_referent($idx);
			echo "<td> Confirmez-vous la suppression :";
			lien ("images/oui.png","supp_referent",param("idx",$idx) );
			echo " </td></table></div><p><p><p>";
			pied_de_page("x");
			}

		if (($action=="supp_organisme_a_comfirmer") && ($user_droit=="A"))
			{
			echo "<hr><p>Attention, vous avez demandé la suppression de la structure sociale suivante :<p>";
			$idx=variable('idx');
			titre_organisme();
			$reponse =command("","select * from  r_organisme where idx='$idx' ");
			$donnees = mysql_fetch_array($reponse) ;
			$adresse=stripcslashes($donnees["adresse"]);
			$organisme=stripcslashes($donnees["organisme"]);				
			$tel=$donnees["tel"];	
			$sigle=stripcslashes($donnees["sigle"]);	
			$mail=$donnees["mail"];	
			$doc_autorise=$donnees["doc_autorise"];	
			$idx=$donnees["idx"];	
			echo "<tr><td> $organisme </td><td> $sigle   </td><td> $adresse   </td><td> $tel </td><td> $mail</td><td> $doc_autorise</td>";
			echo "<td> Confirmez-vous la suppression : </td>";
			lien_c ("images/oui.png","supp_organisme",param("idx",$idx),"Confirmer la suppression" );
			echo "</table></div><p><p><p>";
			pied_de_page("x");
			}		
			
		if ( ($action=="supp_user_a_confirmer") &&(  ($user_droit=="A") || ($user_droit=="R") ) )
			{
			echo "<hr><br>Attention, vous avez demandé la suppression du responsable ou de l'acteur social suivant :<p> ";
			echo "<div class=\"CSSTableGenerator\"><table>";
			$idx=variable('idx');
	
			titre_user($user_droit);
			visu_user($idx,$user_droit);
			echo "<td> Confirmez-vous la suppression : ";
			lien ("images/oui.png","supp_user",param("idx",$idx) ,"Confirmer la suppression");
			echo "</td></table></div><p><p><p>";
			pied_de_page("x");
			}

		if ( (($action=="draganddrop") ||  ($action=="draganddrop_p")  )  &&(  ($user_droit=="") || ($user_droit=="S") ) )
			{
			$user = $_SESSION['user_idx'];

			echo "<hr>";
			affiche_titre_user($user);
			echo "Déposer ci-dessous les fichiers à charger : <p> ";

			if  ($action=="draganddrop") 
				echo "<form action=\"upload_dd.php\" class=\"dropzone\" id=\"my-awesome-dropzone\" >";
			else
				echo "<form action=\"upload_dd_p.php\" class=\"dropzone\" id=\"my-awesome-dropzone\" >";

			echo "</form>";
			echo "<p><br><center><a href=\"index.php\" > Cliquez ici quand vous avez terminé.</a><br></center>";
			pied_de_page("");
			}			
			
		if ( ($action=="modifier_user") && (($user_droit=="R") || ($user_droit=="A") ) )
			modif_user	(variable("idx"));
				
			
		if ( (($user_droit=="R") || ($user_droit=="S") ) && (($action=="") ||($action=="modif_organisme")  ) )
			{
			$r1 =command("","select * from  r_organisme where idx='$user_organisme' ");
			$d1 = mysql_fetch_array($r1);
			$orga=stripcslashes($d1["organisme"]);
			$adresse=stripcslashes($d1["adresse"]);
			$mail=$d1["mail"];
			$telephone=$d1["tel"];
			$id_org=$d1["idx"];
			if ($user_droit=="R") 
				{
				$reponse =command("","select * from r_lien where user='$user_idx'  ");
				echo "<table>";
				while ($donnees = mysql_fetch_array($reponse) ) 
					{
					$id_org = $donnees["organisme"];
					$r1 =command("","select * from r_organisme where idx='$id_org'  ");
					$d1 = mysql_fetch_array($r1);
					$orga=stripcslashes($d1["organisme"]);
					$adresse = $d1["adresse"];
					$telephone = $d1["tel"];
					$mail = $d1["mail"];
					formulaire ("modif_organisme");
					echo "<tr><td> $orga  : </td>";
					echo "<td> Adresse: <input type=\"texte\" name=\"adresse\" onChange=\"this.form.submit();\" size=\"60\" value=\"$adresse\"> " ;
					echo "<td> - Téléphone: <input type=\"texte\" name=\"telephone\" onChange=\"this.form.submit();\"  size=\"15\" value=\"$telephone\"> " ;
					echo "<td> - Mail: <input type=\"texte\" name=\"mail\" onChange=\"this.form.submit();\"  size=\"25\" value=\"$mail\"> " ;
					echo "<input type=\"hidden\" name=\"id\" value=\"$id_org\"> " ;
					echo "</form> ";	
					}		
				echo "</table>";
				}
			else
				if ($action=="")
					echo "<tr><td>  <img src=\"images/organisme.png\" width=\"25\" height=\"25\" ></td><td> Structure sociale: $orga </td><td> / $adresse </td><td> / $telephone </td><td> / $mail </td><td> (Resp.:".responsables_organisme($user_organisme).")</td>";
			
			/* ----------------------------------FISSA
			$jfissa=0;
			$reponse = mysql_query("SELECT * from  fissa WHERE organisme='$user_organisme'"); 
			while($donnees = mysql_fetch_array($reponse))
				{
				if ($jfissa++==0)
					echo "<td>--> </td><td>";
				$l=$donnees["libelle"];
				$b=$donnees["support"];
				echo "<a href=\"fissa.php?support=$b&logo=$logo\"> $l </a>";
				}
			if ($jfissa!=0)
				echo "</td>";
			-----------------------------------*/ 
			}
	

	if (($action=="modif_champ_bug") && ($user_droit=="F") )
		{
		modif_champ_bug(variable('idx'),variable('champ'),variable('valeur'));	
		modif_bug(variable('idx'));	
		}
			
	if (($action=="") && ($user_droit=="F") )
		liste_bug();

	if (($action=="modif_bug") && ($user_droit=="F") )
		modif_bug(variable_get('idx'));	

	if  (($action=="param_sys") && ($user_droit=="E") )
		param_sys();
		
	if ( ($action=="modif_valeur_param") && ($user_droit=="E") )
		{
		modif_valeur_param(variable('nom'),variable('valeur'));
		param_sys();
		}
	
	if ( ($action=="") && ($user_droit=="T") )
		echo "Mot de passe par défaut : '".parametre("Formation_mdp")."'<br>";

	
	// intialisation des comptes de formation
	if ( ($action=="init_formation") && ($user_droit=="T") )
		{	
		$i=0;
		echo "Initialisation comptes de formation : ";
		$mdp=encrypt (parametre("Formation_mdp", "Form_1234")) ;
		
		// Purge des toutes les tables 

		$reponse = command("","Select * from r_user where (id REGEXP 'FORM_R') or (id REGEXP 'FORM_B') or (id REGEXP 'FORM_A')");
		while ($donnees = mysql_fetch_array($reponse) )
			{
			$id=$donnees["id"];
			echo "<br>- $id";
			$idx=$donnees["idx"];
			command("","UPDATE r_user set pw='$mdp', lecture='$mdp', mail='$id@fixeo.com', telephone='0651256164' where idx='$idx' ");
			command("","delete from r_sms where idx='$idx' ");
			command("","delete from DD_rdv where user='$idx' or auteur='$idx' ");
			command("","delete from r_dde_acces where user='$idx' or ddeur='$idx' or autorise='$idx' ");
			command("","delete from log where user='$idx' or acteur='$idx'  ");
			command("","delete from r_referent where user='$idx'  or organisme='$idx'  ");
			$i++;
			
			if ($donnees["droit"]=="")
				{
				ajoute_note($idx,"Numéro d''envoi de SMS pour Doc-depot 06.98.47.43.12 ");
				$idx_rdv=inc_index("rdv");
				$date=date('Y-m-d');
				command("","INSERT INTO DD_rdv VALUES ('$idx_rdv', '$idx','$idx','$date 18H00', 'Penser à supprimer les documents inutiles', '15min', 'A envoyer' ) ");
				}
			}
		
		echo "<p>Chaque bénéficiaire de la formation a pour référent tous les acteurs socicaux de la formation";
		// initialisation pour chaque bénéficiaire que de tous les Acteur sociaux
		$reponse = command("","Select * from r_user where (id REGEXP 'FORM_A')");
		while ($donnees = mysql_fetch_array($reponse) )
			{
			$idx=$donnees["idx"];
			// recherche des benéficiaire à ratacher
			$r1 = command("","Select * from r_user where (id REGEXP 'FORM_B')");
			while ($d1 = mysql_fetch_array($r1) )
				{
				$idx1=$d1["idx"];
				$i=inc_index("referent");					
				$ns= parametre("Formation_num_structure");
				command("","INSERT INTO `r_referent`  VALUES ( '$i', '$idx1', '$ns', '$idx','', '','','')");
				}
			}	
		
		echo "<p>Un seul document par espace et utilisateur de la formation";
		// on ne garde qu'un document par compte 
		$reponse = command("","Select * from r_user where (id REGEXP 'FORM_B') or (id REGEXP 'FORM_A')");
		while ($donnees = mysql_fetch_array($reponse) )
			{
			$i=0;
			// espace perso
			$r1 =command("","select * from r_attachement where ref='P-$idx' ");		
			while ($d1 = mysql_fetch_array($r1) ) 
				{
				if ($i++ !=0)
					supp_attachement ($d1["num"]);
				}	

			$i=0;				
			// espace partagé
			$r1 =command("","select * from r_attachement where ref='A-$idx' ");		
			while ($d1 = mysql_fetch_array($r1) ) 
				{
				if ($i++ !=0)
					supp_attachement ($d1["num"]);
				}
			}
	
		msg_ok ("<br>Mot de passe par défaut : '".parametre("Formation_mdp")."'<br>");
		}					


		if ( ($action=="raz_mdp_formation") && ($user_droit=="T") )
		{	
		$mdp=encrypt (parametre("Formation_mdp", "Form_1234")) ;
		$reponse = command("","UPDATE r_user set pw='$mdp' where (id REGEXP 'FORM_R') or (id REGEXP 'FORM_B') or (id REGEXP 'FORM_A')");
		msg_ok("Mot de passe des comptes de formation initialisé ($mdp)");
		}	

		
	// -------------------------------------------------------------------------------------------------------
	// au dela les fonctions ne sont pas accesssibles pour les profils E et F
	if ( ($user_droit=="E") || ($user_droit=="F")|| ($user_droit=="T"))
		pied_de_page("x");

		
			
	if ( ($user_droit=="S") && ($action=="verif_user") )
			{
			echo "<table><tr><td width> <ul id=\"menu-bar\">";
			echo "<li><a href=\"index.php?action=verif_user\"  > Vérification existence </a></li>";
			echo "</ul></td></table>";
			
			echo "<p><center><table><tr>";
			$nom=variable('nom');
			$prenom=variable('prenom');
			$anniv=variable('anniv');
			if (($nom!="") && ($prenom!="") && ($anniv!=""))
				{
				$reponse = command("","select * from r_user where nom='$nom' and prenom='$prenom' and anniv='$anniv' and droit='' ");
				if ($donnees = mysql_fetch_array($reponse) )
					echo"<td ALIGN=\"CENTER\" BGCOLOR=\"lightgreen\" ><br>Il existe un compte avec ces informations. Cliquez <a href=\"index.php?action=dde_identifiant\">ici</a> pour récupérer son compte<p>";
				else
					echo "<td ALIGN=\"CENTER\" BGCOLOR=\"yellow\" ><br>Il n'existe pas de compte avec ces informations. Cliquez <a href=\"index.php?action=ajout_beneficiaire\">ici</a> pour cérer un compte<p>";
				}
			else
				if (($nom!="") || ($prenom!="") || ($anniv!=""))
					echo "<td ALIGN=\"CENTER\"  ><br>Merci de compléter tous les champs.<p>";
			echo "</td></table></center>";
			verif_existe_user();

			}
			
	if ( ($user_droit=="S") && ($action=="verif_existe_user") )
		{
			echo "<table><tr><td width> <ul id=\"menu-bar\">";
			echo "<li><a href=\"index.php?action=verif_user\"  > Vérification existence </a></li>";
			echo "</ul></td></table>";
			verif_existe_user();
			}
		
	// signalement d'un contenu illicite
	if ($action=="illicite")
			{
			$num=variable('num');
			$num = substr($num,strpos($num,".")+1 );
			ajout_log( $idx, "Signalement document illicite : $num", $user_idx );
			ajout_log_tech( "Signalement par $user_idx  document illicite : $num" ,"P1");
			msg_ok("<Strong>Signalement transmis à l'administrateur. </strong>");
			$action=variable('retour');
			}	
			
	// permet de basculer un document d'un espace à un autre
	if ( ($action=="switch")&& ($user_droit=="") )
			{
			$num=variable('num');
			$reponse =command("","select * from r_attachement where  num='$num' ");
			if ($donnees = mysql_fetch_array($reponse) )
				{
				$ref=$donnees["ref"];
				if ($ref[0]=='A') $ref[0]='P'; else $ref[0]='A';
				$type=$ref[0];
				$reponse =command("","update r_attachement SET ref='$ref' where num='$num' ");
				$reponse =command("","update r_attachement SET type='$type' where num='$num' ");
				$num = substr($num,strpos($num,".")+1 );
				ajout_log( $idx, "Basculement d'espace de $num", $user_idx );				
				}
			$action=variable('retour');
			}			
			
	if (($action=="dossier") && ($user_droit=="") )
			dossier("A-$user_idx");
			
	if ( ($action=="creer_dossier") && ($user_droit=="") )
		creer_dossier("A-$user_idx");	

	
	if ( ( ($user_droit=="S") ) && ($action=="detail_user"))
			{
			affiche_titre_user($user);
			// Vérification si demande de mot de passe

			$date_jour=date('Y-m-d');
			$j=0;
			$r1 =command("","select * from r_dde_acces where type='' and user=$user and ddeur=$user_idx and date_dde>='$date_jour' ");
			If ($d1 = mysql_fetch_array($r1) ) 
				{
				$date_dde=$d1["date_dde"];
				$qui=$d1["user"];
				$code=$d1["code"];
				$date_auto=$d1["date_auto"];
				$autorise=$d1["autorise"];
				
				echo "</table><table><tr> <td>Attention: Demande de recupération de mot de passe ($date_dde) </td>";
				echo "<form method=\"POST\" action=\"index.php\">  ";
				echo "<td><input type=\"hidden\" name=\"bene\" value=\"$qui\"> " ;
				echo "<input type=\"hidden\" name=\"autorise\" value=\"$user_idx\"> " ;
				if ($code=="")
					echo "<input type=\"hidden\" name=\"action\" value=\"autorise_recup_mdp\"><input type=\"submit\"  id=\"autorise_recup_mdp\"  value=\"Autoriser après controle d'identité\"/> " ;
				else
					echo "<td> Code à communiquer <u>après vérification identité</u>: '<strong>$code</strong>' </td><td> (Valable jusqu'au $date_auto) </td><td>  <input type=\"hidden\" name=\"action\" value=\"supp_recup_mdp\"> <input type=\"submit\"  id=\"supp_recup_mdp\" value=\"Supprimer accès\"/>" ;
				echo "</form>  </td>";	
				}
			}

		if(($user_droit!="") &&  ( (strtoupper($user_droit)!="S") ||  ($user_droit=="R"))  )
			if (($action!="ajout_affectation") && ($action!="ajout_admin") && ($action!="detail_user")&& ($action!="ajout_photo")&& ($action!="ajout_referent")&& ($action!="ajout_organisme") )
				bouton_user($user_droit, $user_organisme,$filtre);	

		if (($user_droit=="") && ($filtre==""))
			if (($action!="note_sms") &&($action!="ajout_note")&& ($action!="rdv")&& ($action!="ajout_rdv")&& ($action!="ajout_note") && ($action!="ajout_admin") && ($action!="ajout_photo")&& ($action!="ajout_organisme") && ($action!="ajout_user") )
				bouton_referent($user_idx);
			
		if (( ($user_droit=="S") ) && ($action=="dde_acces"))
			{
			dde_acces($_SESSION['user_idx'],$user_idx);
			$user=$_SESSION['user_idx'];
			$action="detail_user";
			}
		
		if (( (($user_droit=="S") && ($action!="rdv") )||  ($user_droit=="R")) && ($action=="detail_user"))
			if (isset($_GET["user"]))
				bouton_referent(variable("user"));
			else
				bouton_referent($user);
				
			
		if( ($user_droit!="") && ($user_droit!="F") && ($user_droit!="E") )
			{
			if ($action=="ajout_beneficiaire")
				ajout_beneficiaire($user_idx,$user_organisme);
			else			
				if (( (strtoupper($user_droit)=="S") ) && ($action!="detail_user")&& ($action!="rdv")&& ($action!="ajout_rdv"))
					if (($action!="ajout_admin") && ($action!="ajout_photo")&& ($action!="ajout_organisme") && ($action!="ajout_user") )
						bouton_beneficiaire($user_idx,$user_organisme,$filtre);
			}
		// ---------------------------------------------------------------- Bloc RDV --------------------------
		if  ( ($action=="ajout_rdv") && ( ($user_droit=="S") || ($user_droit=="") ) )
			{
			$ligne=variable ('ligne');
			$date=mef_date_BdD(variable ('date'));
			$heure=mef_heure_BdD(variable ('heure')	);
			
			
			$avant=variable ('avant');
			$user1=variable ('user');
			if ( ($ligne!="") && ($date!="") && ($heure!=""))
				{
				$date_jour=date('Y-m-d');
				$idx=inc_index("rdv");
				if ($date_jour<=$date)
					{
					command("","INSERT INTO DD_rdv VALUES ('$idx', '$user1','$user_idx','$date $heure', '$ligne', '$avant', 'A envoyer' ) ");
					ajout_log( $idx, "Ajout RDV le $date $heure : $ligne ", $user1 );				
					}
				else
					erreur("La date doit être dans le futur");
				}
			else
				erreur("Il manque des informations pour enregistrer le rendez vous.");
			$action="rdv";
			}
			
		if ($user_droit=="") 
			if (($action!="note_sms") &&($action!="ajout_note") &&($action!="ajout_photo")&& ($action!="ajout_referent") && ($action!="ajout_admin") && ($action!="ajout_user") )
				rdv($user_idx);
				
		if ( (($user_droit=="S") ) && (($action=="detail_user") || ($action=="rdv")  ) )
			{
			$user = $_SESSION['bene'];
			if ($action=="rdv")
				affiche_titre_user($user);
			rdv($user);	
			}
		//----------------------------------------------------------------------------------------------------------------

		
		if ( (($user_droit=="S") ) && (($action=="detail_user") || ($action=="ajout_admin")  ) )
			{
			$user = $_SESSION['bene'];
			if($action=="ajout_admin")
				affiche_titre_user($user);
			bouton_upload("A-$user",$user);	
			}

		if (($user_droit=="R") && ($action!="ajout_user"))
			traite_demande_acces($user_organisme,$user_idx);
		
		if (($user_droit=="") && ($filtre==""))
			if (($action!="note_sms") &&($action!="ajout_note")&& ($action!="rdv") &&($action!="ajout_photo")&& ($action!="ajout_referent") && ($action!="ajout_organisme") && ($action!="ajout_user") )
				{
				signet("ajout_admin");
				bouton_upload("A-$user_idx",$user_idx);	
				}
				
		if ( ($user_droit=="") && ($action==""))
			echo "<div class=\"CSS_perso\"  ><br><center> Les documents et informations dans cette zone ne sont jamais visibles des référents de confiance. </center> ";
			
		if (($user_droit!="A") && ($filtre==""))

			if (($action!="note_sms") &&($action!="ajout_note")&&($action!="rdv") && ($action!="ajout_admin") && ($action!="ajout_referent") && ($action!="ajout_organisme")  && ($action!="ajout_user"))
				{
				signet("ajout_photo");
				if (($user_droit=="") || ( ( ($user_droit=="S") ) && ($action!="detail_user") ))
					bouton_upload("P-$user_idx",$user_idx);	
				}
		//----------------------------------------------------------------------------- Bloc SMS et NOTES --------------
		if (($action=="ajout_note") && ($user_droit=="") )
			{
			ajoute_note( $user_idx, variable ('note'));
			$action="note_sms";
			}
			
		if ($user_droit=="")
			if ( ($action!="ajout_admin")&& ($action!="rdv") &&($action!="ajout_photo") && ($action!="ajout_referent") && ($action!="ajout_organisme")  && ($action!="ajout_user"))
				affiche_sms($filtre);
		//----------------------------------------------------------------------------------------------------------------

		if (($user_droit=="") && ($action==""))
			echo "<p>.</div >";
		
		if ($action!="ajout_user") 
			if ($user_droit=="A")
				bouton_affectation();	
							
		if ($user_droit!="")	
			if (($action!="ajout_affectation") &&($action!="ajout_admin") && ($action!="rdv") && ($action!="ajout_photo")&& ($action!="ajout_referent") && ($action!="ajout_user") )
				if ( (( ($user_droit=="S") ) && ($action!="detail_user") ) || ($user_droit=="A"))
					bouton_organisme();	
				
		pied_de_page("x");
	
		?>
	
    </body>
</html>

