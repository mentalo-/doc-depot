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

// ------------------------------------------------------
// DOC-DEPOT : COPYRIGTH ADILEOS - D�cembre 2013/Avril 2015

session_start(); 

error_reporting(E_ALL | E_STRICT);
/*
// mi en commentaire car empeche les test seleni�um 

// { D�but - Premi�re partie
if(!empty($_POST) OR !empty($_FILES))
	{
    $_SESSION['sauvegarde'] = $_POST ;
    $_SESSION['sauvegardeFILES'] = $_FILES ;

    $fichierActuel = $_SERVER['PHP_SELF'] ;
    if(!empty($_SERVER['QUERY_STRING']))
       $fichierActuel .= '?' . $_SERVER['QUERY_STRING'] ;
    header('Location: ' . $fichierActuel);
    exit;
	}
// } Fin - Premi�re partie

// { D�but - Seconde partie
if(isset($_SESSION['sauvegarde']))
	{
    $_POST = $_SESSION['sauvegarde'] ;
    $_FILES = $_SESSION['sauvegardeFILES'] ;
    unset($_SESSION['sauvegarde'], $_SESSION['sauvegardeFILES']);
	}
// } Fin - Seconde partie
*/

include 'general.php';

	// session de 5 ou 30 minutes selon profil 
	if 	(!isset($_SESSION['droit']) || ($_SESSION['droit']=="S")|| ($_SESSION['droit']=="A")|| ($_SESSION['droit']=="R")) 
		$to=TIME_OUT;	
	else
		$to=TIME_OUT_BENE;  // tempo courte

	if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $to )) 
		$_SESSION['pass']=false;
		
	$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<?php include 'header.php';	  ?>

<script src="http://code.jquery.com/jquery-1.5.1.min.js"></script> 
		<script type="text/javascript">
			$(document).ready(function() { $('#msg_ok').delay(3000).fadeOut();});
		</script>
		
<script language="JavaScript">
function Expand(obj) {
 if (!obj) return;
 while(obj && !obj.className.match(/\bexpandbox\b/)) obj = obj.parentNode; //on remonte jusqu'au parent
 if (!obj) return; //on sort si on a part trouv� d'obj parent
 var aElt = obj.getElementsByTagName("*" ); //onrecupere tous les noeuds enfants
 for (var i=0; i<aElt.length; i++) { //on parcours
  if (aElt[i].className.match(/\bexpand\b/)) { //tous les noeuds avec une classe expand on les affichent ou on les cachent
   with(aElt[i].style) display = (display=="none" ) ? "" : "none";
  }
 }
}

function afficheNouveauType(){
    if (document.getElementById('organisme').value=="")
        document.getElementById('ReversBE').style.visibility= "visible";
    else
         document.getElementById('ReversBE').style.visibility="hidden";
}

function calculeLongueur(){
   var iLongueur, iLongueurRestante;
   iLongueur = document.getElementById('msg').value.length;
   if (iLongueur>220) {
      document.getElementById('msg').value = document.getElementById('msg').value.substring(0,220);
      iLongueurRestante = 0;
   }
   else {
      iLongueurRestante = 220 - iLongueur;
   }
   if (iLongueurRestante <= 1)
      document.getElementById('indic').innerHTML = iLongueurRestante + "&nbsp;caract&egrave;re&nbsp;disponible";
   else
      document.getElementById('indic').innerHTML = iLongueurRestante + "&nbsp;caract&egrave;res&nbsp;disponibles";
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
                else { done("Format de fichier non accept�."); };

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
    };
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
	//	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	//	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date dans le pass�
			
include 'inc_style.php';
include 'include_charge_image.php';
include 'exploit.php';	
include 'include_mail.php';
require_once "connex_inc.php";
require_once "suivi_liste.php";  // pour la liste des pays

    echo "<head>";
	echo "<link rel=\"icon\" type=\"image/png\" href=\"images/identification.png\" />";
	echo "<title>Doc-Depot.com </title>";
    echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
	if (isset($_SESSION['droit']))
		{
		if ($_SESSION['droit']=="")
			$refr=TIME_OUT_BENE+10;
		else
			$refr=TIME_OUT+10;

		echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"$refr\">";
		}
	echo "</head><body>";

// --------------------------------------------------------------------
	function enreg_bug($titre,$descript,$type,$impact,$qui)
		{
		$version= parametre("DD_version_portail") ;
		
		$date_jour=date('Y-m-d');	
		$idx=inc_index("bug");
		$reponse = command("INSERT INTO `z_bug` VALUES ( '$idx', '$titre', '$descript', '$type', '???','$qui','new','$date_jour','$impact','$version','','') ");
		$message = "Titre : $titre";
		$message .= "<p>Description : $descript";
		$message .= "<p>Type : $type";
		$message .= "<p>Impact : $impact";
		$message .= "<p>Auteur : $qui";
		envoi_mail(parametre('DD_mail_fonctionnel'),"Bug $idx ",$message, true);
		if ($type=="Bugs") // les bugs sont aussi remont�s � l'exploitant
			envoi_mail(parametre('DD_mail_gestinonnaire'),"Bug $idx ",$message, true);
		ajout_log( "", "Cr�ation Bug $idx :$titre /  $type / $impact / $qui", "");
		return ($idx);
		}

		
	function affiche_liste_bug( $cmd)
		{
			echo "</table><div class=\"CSSTableGenerator\" > ";
			echo "Urgent <1j; Prio + <1mois; Prio <3 mois; Normal+ <6mois; Normal <1 an; Nice to have > 1 an";
	
			echo "<table><tr>	<td> N�  </td>
								<td>".traduire('Cr�ation')."  </td>
								<td>".traduire('Titre')."</td>
								<td>".traduire('Type')." </td>
								<td>".traduire('Etat')." </td>
								<td>".traduire('Impact')." </td>
								<td>".traduire('Description')." </td>
								<td>".traduire('Origine')." </td>
								<td>".traduire('Priorit�')." </td>
								<td>".traduire('Version')." </td>
								<td>".traduire('Commentaire')." </td>
								<td>".traduire('fonction')." </td>";
			$reponse =command($cmd);		
			while ($donnees = fetch_command($reponse) ) 
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
				echo "<tr><td><a href=\"index.php?".token_ref("modif_bug")."&idx=".encrypt($idx1)."\">  $idx1 <a></td><td> $date </td><td> $titre </td><td> $type </td><td> $etat </td><td> $impact </td><td> $descript </td><td> $testeur </td><td> $domaine </td><td> $version </td><td> $commentaire </td><td> $fonction </td>";
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
			echo "<table><tr><td><a href=\"index.php?".token_ref("bug")."\"> + ".traduire('Nouveau ticket')." </a> - </td><td > ".traduire('Filtre')." : <input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
			echo "</form><td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td>";
			lien_c ("images/croixrouge.png", "supp_filtre","" , traduire("Supprimer"));
			echo "</table> ";

			if ($filtre1!="")
				$filtre=" where (etat REGEXP '$filtre1' or date REGEXP '$filtre1'or idx REGEXP '$filtre1' or titre REGEXP '$filtre1' or type REGEXP '$filtre1' or impact REGEXP '$filtre1' or descript REGEXP '$filtre1'or domaine REGEXP '$filtre1' or version REGEXP '$filtre1' or commentaire REGEXP '$filtre1' or fonction REGEXP '$filtre1') and ";
			else
				$filtre=" where ";

			affiche_liste_bug("select * from  z_bug $filtre (etat='New') order by domaine desc");
			echo"<p>";
			affiche_liste_bug("select * from  z_bug $filtre (etat='OK pour MEP') order by domaine desc");
			echo"<p>";
			affiche_liste_bug("select * from  z_bug $filtre (etat<>'En production' and etat<>'Abandonn�' and etat<>'OK pour MEP' and etat<>'New') order by domaine desc");
			echo"<p>";
			affiche_liste_bug("select * from  z_bug $filtre (etat='En production' ) order by version desc");
			echo"<p>";
			affiche_liste_bug("select * from  z_bug $filtre (etat='Abandonn�') order by domaine desc");
		
			pied_de_page("x");
			}

		
		function modif_champ_bug($idx, $champ, $valeur)
			{
			$reponse =command("update z_bug SET $champ = '$valeur' where idx='$idx' ");

			}

	function saisie_champ_bug($idx, $champ, $valeur, $size="")
		{
		$valeur= stripcslashes ($valeur);
		if ($size!="")
			$size = " size=\"$size\" ";
		return ("<form method=\"post\" >".token_return("modif_champ_bug").param("idx","$idx").param("champ","$champ")."<input type=\"text\" name=\"valeur\" value=\"$valeur\" $size onChange=\"this.form.submit();\" >  </form> ");
		}

		
	function saisie_champ_bug_area($idx, $champ, $valeur, $size="")
		{
		$valeur= stripcslashes ($valeur);
		return ("<form method=\"post\" >".token_return("modif_champ_bug").param("idx","$idx").param("champ","$champ")."<TEXTAREA rows=\"5\" cols=\"$size\" name=\"valeur\"  onChange=\"this.form.submit();\" >$valeur</TEXTAREA></form> ");
		}
		
	function liste_type_bug( $idx, $champ, $val_init)
		{
		echo "<form method=\"post\" >".token_return("modif_champ_bug").param("idx","$idx").param("champ","$champ");
		echo "<SELECT name=\"valeur\" onChange=\"this.form.submit();\"  >";
		affiche_un_choix($val_init,"Bugs");
		affiche_un_choix($val_init,"Fonctionnel");
		affiche_un_choix($val_init,"Technique");
		affiche_un_choix($val_init,"S�curit�");
		echo "</SELECT></form>";
		}	
		
	function liste_modules_bug( $idx, $champ, $val_init)
		{
		echo "<form method=\"post\" >".token_return("modif_champ_bug").param("idx","$idx").param("champ","$champ");
		echo "<SELECT name=\"valeur\" onChange=\"this.form.submit();\"  >";
		affiche_un_choix($val_init,"-");
		affiche_un_choix($val_init,"DOC-DEPOT");
		affiche_un_choix($val_init,"FISSA");
		affiche_un_choix($val_init,"SUIVI");
		affiche_un_choix($val_init,"RDV");
		affiche_un_choix($val_init,"ALERTE");
		affiche_un_choix($val_init,"PORTAIL");	
		affiche_un_choix($val_init,"CANICULE");
		affiche_un_choix($val_init,"Webmail");
		affiche_un_choix($val_init,"Planning");
		affiche_un_choix($val_init,"Conditions");
		echo "</SELECT></form>";
		}

	function liste_impact_bug( $idx, $champ, $val_init)
		{
		echo "<form method=\"post\" > ".token_return("modif_champ_bug").param("idx","$idx").param("champ","$champ");
		echo "<SELECT name=\"valeur\" onChange=\"this.form.submit();\"  >";
		affiche_un_choix($val_init,"Tr�s simple");
		affiche_un_choix($val_init,"Simple");
		affiche_un_choix($val_init,"Normal");
		affiche_un_choix($val_init,"Complexe");
		affiche_un_choix($val_init,"Tr�s complexe");
		affiche_un_choix($val_init,"???");

		echo "</SELECT></form>";
		}		
		
	function liste_etat_bug( $idx, $champ, $val_init)
		{
		echo "<form method=\"post\" >".token_return("modif_champ_bug").param("idx","$idx").param("champ","$champ");
		echo "<SELECT name=\"valeur\" onChange=\"this.form.submit();\"  >";
		affiche_un_choix($val_init,"New");
		affiche_un_choix($val_init,"Ouvert");
		affiche_un_choix($val_init,"En correction");
		affiche_un_choix($val_init,"A tester");
		affiche_un_choix($val_init,"OK pour MEP");
		affiche_un_choix($val_init,"En production");
		affiche_un_choix($val_init,"Abandonn�");
		
		echo "</SELECT></form>";
		}	

	function liste_prioite_bug( $idx, $champ, $val_init)
		{
		echo "<form method=\"post\" >".token_return("modif_champ_bug").param("idx","$idx").param("champ","$champ");
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
		$reponse =command("select * from  z_bug where idx='$idx' ");		
		if ($donnees = fetch_command($reponse) )
			{
			echo "<tr><td> Num�ro </td><td>".$donnees["idx"]."</td>";
			echo "<tr><td> Cr�ation</td><td>". $donnees["date"]."</td>";
			// champ modifiables
			echo "<tr><td> ".traduire('Titre')."</td><td>". saisie_champ_bug($idx,"titre",$donnees["titre"],100)."</td>";
			echo "<tr><td> ".traduire('Description')."</td><td>". saisie_champ_bug_area($idx,"descript",$donnees["descript"],100)."</td>";
			echo "<tr><td> ".traduire('Type')."</td><td>";  liste_type_bug($idx,"type",$donnees["type"]);  echo "</td>";
			echo "<tr><td> ".traduire('Effort')."</td><td>";  liste_impact_bug( $idx,"impact",$donnees["impact"]); echo"</td>";
			echo "<tr><td> ".traduire('Origine')."</td><td>".  saisie_champ_bug($idx,"testeur",$donnees["testeur"])."</td>";
			echo "<tr><td> ".traduire('Etat')."</td><td>";  liste_etat_bug($idx,"etat",$donnees["etat"]); echo "</td>";
			echo "<tr><td> ".traduire('Priorit�')."</td><td>"; liste_prioite_bug($idx,"domaine",$donnees["domaine"]); echo "</td>";
			echo "<tr><td> ".traduire('Version')."</td><td>". saisie_champ_bug($idx,"version",$donnees["version"]) ."</td>";
			echo "<tr><td> ".traduire('Commentaire')."</td><td>".  saisie_champ_bug_area($idx,"commentaire",$donnees["commentaire"],100)."</td>";
			echo "<tr><td> ".traduire('Fonction')."</td><td> "; liste_modules_bug($idx,"fonction",$donnees["fonction"]);  echo "</td>";
			}
		else
			erreur(traduire("Anomalie inconnue"));
		echo "</table>";
		pied_de_page("x");
		}
		

// ---------------------------------------------------------------------------------------

function maj_mdp_fichier($idx, $pw )
	{
	$j=0;
	
	$reponse =command("select * from r_attachement where ref='A-$idx'  ");
	while (($donnees = fetch_command($reponse) ) && ($j<100))
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
			// les doc ne sont pas crypt�es
			$j++; 	
			}
	}
	
	// =========================================================== procedures g�n�rales


	function affiche_un_choix_2($val_init, $val, $val_aff)
		{
		if (( $val_init!=$val) || ($val_init=="") || ($val==""))
				echo "<OPTION  VALUE=\"$val\"> $val_aff </OPTION>";
			else
				echo "<OPTION  VALUE=\"$val\" selected> $val_aff </OPTION>";
		}
		
	function liste_type( $val_init , $mode="")
		{
		/*
		Justificatif de domicile
		D�claration de grossesse �tablie par le m�decin
		Carte d'invalidit�
		R�c�piss� de votre demande, convocation ou rdv en pr�fecture
		Notification d�attribution de pensions
		Carte d'ancien combattant
		Attestation MDPH
		*/
		
		echo "<SELECT name=\"type\" $mode >";
		affiche_un_choix($val_init,"Justificatif d'identit�");
		// Justificatif d'identit�
		// Carte nationale d'identit�
		
		// Livret de famille
		// Acte de naissance
		
		affiche_un_choix($val_init,"Banque");
		//Relev�s bancaires			
		
		affiche_un_choix($val_init,"CAF");		
		
		affiche_un_choix($val_init,"Imp�ts");		
		//D�claration de revenus	
		// Dernier avis d�imposition		
		
		affiche_un_choix($val_init,"Mariage, PACS, Divorce");
		// Certificat de concubinage ou attestation d'enregistrement d'un pacs
		// Jugement de divorce
		
		affiche_un_choix($val_init,"Passeport");
		// Passeport
		
		affiche_un_choix($val_init,"Permis conduire");
		
		affiche_un_choix($val_init,"Pole Emploi");
		// Attestation P�le emploi / Avis de paiement des Assedic		

		affiche_un_choix($val_init,"Quittances et Factures");		
		//Quittance de loyer, gaz, �lectricit�, t�l�phone
			
		affiche_un_choix($val_init,"RIB");
		// RIB				
		
		affiche_un_choix($val_init,"Salaires");
		//Bulletin de salaire		
		
		affiche_un_choix($val_init,"Titre de s�jour");
		// Titre de s�jour
		
		affiche_un_choix($val_init,"Transport");
	
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
		affiche_un_choix($val_init,"t","Traducteur");
		echo "</SELECT></td>";
		}

	function liste_organisme( $val_init, $action=0 )
		{

		if ($action==0)
			echo "<td> <SELECT name=\"organisme\" id=\"organisme\" onChange=\"javascript:afficheNouveauType();\" >";
		else
			echo "<td> <SELECT name=\"organisme\" id=\"organisme\" onChange=\"this.form.submit();\"  >";
		
		affiche_un_choix($val_init,"");
		if ($_SESSION['droit']=='A')
			$reponse =command("select * from  r_organisme ");
		else
			$reponse =command("select * from  r_organisme where convention<>'2'");

		while ($donnees = fetch_command($reponse) ) 
			{
			$organisme=stripcslashes($donnees["organisme"]);
			$idx=$donnees["idx"];
			affiche_un_choix_2($val_init,$idx,$organisme);			
			}
		echo "</SELECT></td>";
		}


	function liste_AS( $organisme )
		{
		$reponse =command("select * from  r_user where organisme=\"$organisme\" and (droit='S' or droit='M' or droit='R')  ");

		echo "<td> <SELECT name=\"nom\"   >";
		affiche_un_choix("","Tous");
		while ($donnees = fetch_command($reponse) ) 
			{
			$nom=$donnees["nom"];
			$prenom=$donnees["prenom"];
			$idx=$donnees["idx"];
			affiche_un_choix_2("",$idx,"- $nom $prenom");			
			}
	
	
		// recherche des responsables d'organisme
		$reponse =command("select * from  r_lien where organisme=\"$organisme\"  ");
		while ($donnees = fetch_command($reponse) ) 
			{
			$idx=$donnees["user"];
			$nom_prenom =  libelle_user($idx);
			// on v�rifie aussi qu'il est d�j� le r�f�rent d'au moins une personne
			$r1 =command("select * from  r_referent  where nom=\"$idx\"  ");
			if (fetch_command($r1) ) 			
				affiche_un_choix_2("",$idx,"- $nom_prenom");
			}
		echo "</SELECT></td>";
		}

	function traite_upload($idx, $pw, $user)
		{
		global $id,$action ;
		
		$nom1 =  $_FILES['nom']['tmp_name'];
		$n =  $_FILES['nom']['name'];

		if (isset ($_POST["ref"])) $ref=$_POST["ref"];else	$ref="";
		if (isset ($_POST["type"])) $type=$_POST["type"];else	$type="";				
		if (isset ($_POST["ident"])) $ident=$_POST["ident"];else	$ident="";		

		charge_image("0",$nom1,$n,$pw,$ref, $ident , $type, $idx, $user);
		$action=variable("retour");
		}

		
		
	function visu_liste_miniature ($ref, $type_doc)
		{
	
		$reponse =command("select * from r_attachement where ref='$ref' and status='$type_doc' ");
		while ($donnees = fetch_command($reponse) ) 
			{
			$num=$donnees["num"];	
			if (est_image($num)) 
				{
//				if (($doc_autorise=="") || (stristr($doc_autorise, ";$type_org;") === FALSE) )
					$icone_a_afficher="visu.php?action=visu_image_mini&nom=$num";
				}
			else
				if (extension_fichier($num)=="pdf")
					{
					if (!file_exists("upload_mini/$num.jpg"))
						$icone_a_afficher="images/fichier.jpg";	
					else
						$icone_a_afficher="visu.php?action=visu_image_mini&nom=$num.jpg";
					}
				else
					$icone_a_afficher="images/fichier.png";


			echo "<img src=\"$icone_a_afficher\" height=\"55\" width=\"55\"  >";
			}
		
		}
		
		
	// $num est le nom du fichier
	// $flag_acces est le code d'acc�s ( a minima pour r�pondre aux demandes d'acc�s des AS)
	function visu_doc($num, $flag_acces, $ordre=1, $sans_lien="")
		{
		global $user_droit,$user_idx,$doc_autorise, $action, $user_lecture;
		
		echo "</form>"; 
		$ordre += ($ordre-1)/4;
		$reponse =command("select * from r_attachement where num='$num' ");
		if ($donnees = fetch_command($reponse) ) 
			{
			$ref=$donnees["ref"];
			$date=mef_date_fr($donnees["date"]);
			$deposeur=$donnees["deposeur"];
			if (is_numeric($deposeur))
				{
				$r1 =command("select * from  r_user where idx='$deposeur' ");
				$d1 = fetch_command($r1);
				if ( ($d1["droit"]=="S") || ($d1["droit"]=="s") || ($d1["droit"]=="R")|| ($d1["droit"]=="r")|| ($d1["droit"]=="M")|| ($d1["droit"]=="M") ) 
					{
					$deposeur=" ".traduire("par")." ".libelle_user($deposeur)." (".libelle_organisme_du_user($deposeur).")";
					}
				else
					$deposeur="";
				}
			$date.=$deposeur;
			$num=$donnees["num"];	
			$l_num=strstr($num,".");
			$type=$donnees["status"];			
			$ident=stripcslashes($donnees["ident"]);	
			$type_org=$type;
			
			if ($ordre%2)
				{
				if ( ( (!isset( $_SESSION['bene'])) || ($_SESSION['bene']=="")) && ($_SESSION['droit']!=""))
					$c="#d4ffaa"; 
				else
					$c="#CCDCDC"; 
				}
			else
				$c="";
			echo "<td width=\"25%\" align=\"center\"  bgcolor=\"$c\" class=\"bordure_arrondi\">";

			if (($user_droit!="") && (stristr($doc_autorise, ";$type_org;") !== FALSE) )
				{
				echo "<img src=\"images/restreint.png\"  title=\"".traduire('Document � acc�s restreint')."\" width=\"100\"  >";
				echo " <br> ".traduire('Acc�s restreint')." <br> <p> $type ";
				return;
				}
			
					
			if (est_image($num)) 
				{
				if ((substr($ref,0,1)=="A") &&( $user_lecture!=""))
					{
					if ( $action=="ajout_admin")
						$type="";
					else
						$type="$type";
					
					if (($doc_autorise=="") || (stristr($doc_autorise, ";$type_org;") === FALSE) )
						{
						if ($flag_acces=="") 
							lien("visu.php?action=visu_image_mini&nom=$num", "visu_image", param ("nom","$num"), traduire('D�pos� le')." $date", "","B",$sans_lien, true);
						else
							lien("visu.php?action=visu_image_mini&nom=-$num", "visu_fichier", param ("num","$num").param ("code","$flag_acces"), traduire("Document prot�g�")." D�pos� le $date", "","B",$sans_lien, true);
						echo " $type <br> $ident ";
						}
					else
						{
						lien("visu.php?action=visu_image_mini&nom=-$num", "visu_image", param ("nom","$num"), traduire('D�pos� le')." $date", "","B",$sans_lien, true);
						echo " $type <br> $ident  ";
						}
					}
				else
					{
					lien("visu.php?action=visu_image_mini&nom=$num", "visu_image", param ("nom","$num"), traduire('D�pos� le')." $date", "","B",$sans_lien, true);
					echo " $ident <br> ";
					}
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
						$icone_a_afficher="visu.php?action=visu_image_mini&nom=$num.jpg";
						$icone_a_afficher_cadenas="visu.php?action=visu_image_mini&nom=-$num.jpg";
						$l_num="";
						}
							
					if ((substr($ref,0,1)=="A") &&( $user_lecture!=""))
						{
						if ( ($user_droit=="") && ($action=="ajout_admin"))
							$type="";
						else
							$type="$type";
							
						if (($doc_autorise=="") ||(stristr($doc_autorise, ";$type_org;") === FALSE) )
							{
							if ($flag_acces=="") 
								lien("$icone_a_afficher", "visu_fichier", param ("num","$num"), "", "","B",$sans_lien, true);
							else 
								lien("$icone_a_afficher_cadenas", "visu_fichier", param ("num","$num").param ("code","$flag_acces"), traduire("Document prot�g�").traduire('D�pos� le')." $date","","B",$sans_lien, true);
							echo " $type <br> $ident  ";}

						else
							{
							lien("$icone_a_afficher", "visu_fichier", param ("num","$num"), traduire('D�pos� le')." $date", "","B",$sans_lien, true);
							echo " $type <br> $ident  ";
							}						
						}
					else
						{
						lien("$icone_a_afficher", "visu_fichier", param ("num","$num"), traduire('D�pos� le')." $date", "","B",$sans_lien, true);
						echo " $ident<br>  ";
						}				
					}
				else
					{
					if ($deposeur=="")
						lien("images/fichier.png", "visu_doc", param ("num","$num"), traduire('D�pos� le')." $date", "","B",$sans_lien, true);
					else
						lien("images/fichier_coche.png", "visu_doc", param ("num","$num"), traduire('D�pos� le')." $date", "","B",$sans_lien, true);
					if (substr($ref,0,1)=="A")
						echo "$type <br>";
					echo "$ident <br>";
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

		
		
	// ================================================================================= Module DOSSIERS
	
	function recup_dossier()
		{
		echo "<center><a href=\"index.php\"><img src=\"images/logo.png\" width=\"150\" height=\"100\" ></a>";
		echo "<h3>".traduire('La Consigne Num�rique Solidaire')."</h3><font size=\"5\">'' ".traduire("Mon essentiel � l'abri en toute confiance")." ''</font> <p>";
		debut_cadre("700");
		$ref=variable_get("ref");
		echo "<p><br>".traduire('R�cup�ration documents')." : ";
		
		$d3= explode("-",$ref);
		if (isset($d3[0]))
			{
			$user=$d3[0];
			$reponse =command("select * from  r_user where idx='$user' ");		
			$donnees = fetch_command($reponse) ;
			$nom=$donnees["nom"];
			$prenom=$donnees["prenom"];	
			echo "<strong>$nom - $prenom</strong>";
			}
		echo "<br>(".traduire('R�f�rence dossier')." = $ref )";
		if ($ref!="") 
			{
			if (file_exists("dossiers/$ref.pdf"))
				{
				echo "<p>".traduire("Cliquez sur l'image pour ouvrir le fichier au format PDF contenant le(s) documents transmis")." : ";	
				echo "<br><p>";				
				if (file_exists("dossiers/$ref.jpg"))
					lien("dossiers/$ref.jpg", "visu_dossier", param ("fichier","$ref"), "Dossier $ref", "100","B","", true);
				else
					lien("images/fichier.jpg", "visu_dossier", param ("fichier","$ref"), "Dossier $ref", "100","B","", true);
	
				echo "<p>".traduire('Enregistrer le fichier pour le traiter').".<br><p>";				
				}
			else
				{
				echo "<p><br>".traduire("D�sol� le dossier demand� n'est plus disponible").".<p>";		
				}		
			}
		else
			erreur ('Lien incorrect');
		fin_cadre();
		pied_de_page("x");
		}
		
	function envoyer_dossier( $ref)
		{
		global $user_idx , $idx, $user_nom ,$user_prenom,$user_organisme, $user_telephone, $user_mail;
		
		$src=variable("src");
		$dest=variable("dest");
		$comment=variable("comment");		
		if (VerifierAdresseMail($dest) )
			{
			$tab3= explode("-", $ref);
			$user=$tab3[1] ;			
			
			// 10 tentatives pour trouver 
			for ($i=0; $i<10;$i++)
				{
				$identifiant=$user."-".rand(100000,999999);
				if ( (!file_exists("dossiers/$identifiant.jpg")) && (!file_exists("dossiers/$identifiant.pdf")) ) 
					 break;
				}
				
			copy("tmp/_$ref.pdf", "dossiers/$identifiant.pdf");
			genere_miniature_pdf("dossiers/$identifiant.pdf" , "dossiers/$identifiant" );
			
//			exec ( "/usr/bin/convert -density 100 dossiers/$ref.pdf dossiers/$identifiant.jpg" ) ;  //on cr�e une miniature
//			if (!file_exists("dossiers/$identifiant.jpg"))
//				copy("images/fichier.jpg", "dossiers/$identifiant.jpg");
				
			$reponse =command("select * from  r_user where idx='$user' ");		
			$donnees = fetch_command($reponse) ;
			$user_nom_b=$donnees["nom"];
			$user_prenom_b=$donnees["prenom"];				
			$title =traduire("Envoi des documents de")." $user_nom_b $user_prenom_b";
			
			$lien = serveur."index.php?".token_ref("recup_dossier")."&ref=".addslashes(encrypt("$identifiant"));
			
			$body= "Bonjour, <p> Veuillez trouver ci-dessous le lien vers les documents de $user_nom_b $user_prenom_b"  ;
			$comment=html_entity_decode($comment);
			$comment=stripcslashes($comment);
			$comment=stripcslashes($comment);
			$comment=nl2br($comment);
			if ($comment!="")
				$body .= "<p> Commentaire de l'exp�diteur $user_nom $user_prenom : <table><tr><td><i>".$comment."</i></td> </table>";            
		
			$body .= "<p> Pour acc�der aux documents sur 'Doc-depot.com', merci de cliquer sur ce <a  id=\"lien\"  href=\"$lien\">lien. </a> "; 
			
			$date_limite=date("d/m/Y",  mktime(0,0,0 , date("m"), date("d")+parametre("DD_duree_vie_dossier"), date ("Y")));
			$body .= "<br>Remarque importante: les documents doivent �tre r�cup�r�s avant le $date_limite "; 	
			          
			$body .="<br><br>Si le lien ne fonctionne pas, recopiez dans votre navigateur internet cette adresse : <br><strong>$lien</strong>";
			$body .= "<p> Cordialement";		
			$body .= "<p> $user_nom $user_prenom ( $user_telephone / $user_mail ) <br>";		
			if ($user_organisme!="")
				{
				$body .= "<br>". libelle_organisme($user_organisme) ;		
				$body .= "<br>". adresse_organisme($user_organisme) ;		
				$body .= "<br>Tel:". telephone_organisme($user_organisme) ;		
				$body .= "<br>mail:". mail_organisme($user_organisme) ;		
				}

			$body .= "<p> <hr> <center> Copyright ADILEOS </center>";	
			envoi_mail($dest,$title ,$body);
			ajout_log( $idx, traduire("Envoi dossier")." '$identifiant' ".traduire("par mail de")." $user_nom $user_prenom  : $dest", $user_idx );	
			msg_ok( traduire("Envoi dossier")." '$identifiant' ".traduire("par mail")." : $dest" );	
			return(true);
			}
		else
			return(false);
		}
		
	function dossier_mail( $ref )
		{
		global $action,$user_droit,$user_idx,$id;

		$dest=variable("dest");
		$comment=variable("comment");
		echo traduire('Envoi du dossier par mail');
		echo "<p>".traduire('Le dossier g�n�r� qui sera envoy� via un lien :')."<a href=\"tmp/_$ref.pdf\"target=_blank > <img src=\"images/fichier.jpg\" width=\"40\" height=\"40\" ></a>";
		formulaire ("envoyer_dossier");
		echo" <table> <tr>";
		echo "<td>".traduire('Adresse mail du destinataire')." : </td>";
		echo "<input type=\"hidden\" name=\"src\" value=\"_$ref.pdf\"> " ;
		echo "<td><input type=\"texte\" name=\"dest\" size=\"60\" value=\"$dest\"></td> " ;
		echo "<tr><td>".traduire('Commentaire � introduire<br>dans le mail envoy�')." : </td><td><TEXTAREA rows=\"5\" cols=\"70\" name=\"comment\" >$comment</TEXTAREA></td>";
		echo "<tr><td></td><td><input type=\"submit\" id=\"envoi_dossier\" value=\"".traduire('Envoyer le dossier')."\" ></form> </td></table>  ";
		pied_de_page("x");
		}	
		
		
	function dossier( $ref )
		{
		global $action,$user_droit,$user_idx,$id;
		
		$j=1;
		formulaire ("creer_dossier");
		
		if ($user_droit!="")
			affiche_titre_user($_SESSION['user_idx']);
		echo traduire('Ajouter au dossier vos coordonn�es ?')." <input type=\"checkbox\" name=\"add\" > ".traduire('Adresse')." ,<input type=\"checkbox\" name=\"tel\" > ".traduire('T�l�phone')." , <input type=\"checkbox\" name=\"mail\" >".traduire('Mail').", <input type=\"checkbox\" name=\"anniv\" >".traduire('Date de naissance');
		echo "<p>".traduire('S�lectionnez les fichiers � prendre en compte')." :<table>";
		$reponse =command("select * from r_attachement where ref='$ref' order by date DESC ");
		while (($donnees = fetch_command($reponse) ) && ($j<100))
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
				echo "<td width=\"10\"> </td> <td>".traduire('Non utilisable dans un dossier');
			echo "</td>";
				$j++; 	

			}

		echo "</table><hr>	";	
		echo traduire('Commentaire � introduire en 1ere page du dossier')." : <TEXTAREA rows=\"5\" cols=\"50\" name=\"comment\" ></TEXTAREA>";

		echo "<p><input type=\"submit\" id=\"creer_dossier\" value=\"".traduire('Constituer le dossier')."\" ></form>  <HR> ";
		pied_de_page("x");
		}		
	
	function creer_dossier( $ref )
		{
		global $user_idx;
	
		include 'PDFMerger.php';

		$tab3= explode("-", $ref);
		$user=$tab3[1] ;
		$reponse =command("select * from  r_user where idx='$user' ");		
		$donnees = fetch_command($reponse) ;
		$user_nom=$donnees["nom"];
		$user_prenom=$donnees["prenom"];				
		$user_anniv=$donnees["anniv"];	
		$user_telephone=$donnees["telephone"];				
		$user_mail=$donnees["mail"];	
		$code_lecture=$donnees["lecture"];	
		$user_adresse=stripcslashes($donnees["adresse"]);
	
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
		$pdf1->Image("images/logo.png",150,10,50,40,'PNG');
		$pdf1->SetFont('Arial','B',14);
		$date_jour=date('Y-m-d');

		$pdf1->text(10,10,"Dossier de $user_prenom $user_nom ");
		$pdf1->SetFont('Arial','',12);		
		$pdf1->text(10,17,"g�n�r� le ". mef_date_fr($date_jour));
		
		if ( (variable("anniv")=="on") && ($user_anniv!=""))
			$pdf1->text(10,30,"Date naissance  : $user_anniv ");

		if ( (variable("tel")=="on") && ($user_telephone!=""))
			$pdf1->text(10,37,"T�l�phone : $user_telephone ");

		if ( (variable("mail")=="on") && ($user_mail!=""))
			$pdf1->text(10,44,"Mail : $user_mail ");
			
		if  ((variable("add")=="on") && ($user_adresse!="") )
			$pdf1->text(10,51,"Adresse : $user_adresse ");
			
		$comment=stripcslashes(html_entity_decode(variable("comment")));	
		if ($comment!="")
			$pdf1->text(10,58,"Commentaire : $comment");
		
		$nb_doc=0;
		for ($j=1;$j<100;$j++)
			if (variable("d$j")=="on") $nb_doc++;
			
		$offset=80;
		$taille=30;
		if ($nb_doc>7) 
			$taille=20;	
		if ($nb_doc>10) 
			$taille=15;
		$pdf1->text(80, $offset-5,"Liste des $nb_doc document(s)");
		$pdf1->line(10,$offset-2,190,$offset-2);

		$j=1;
		$reponse =command("select * from r_attachement where ref='$ref' order by date DESC ");
		while (($donnees = fetch_command($reponse) ) && ($j<100))
			{
			$num=$donnees["num"];
			$val=variable("d$j");
			$date= mef_date_fr($donnees["date"]);

			if ($val=="on")
				{
				if (est_doc($num))
					$pdf1->Image("images/fichier.png",10,$offset,$taille,$taille,'PNG');
				else
					if (extension_fichier($num)=="pdf")
						{
						if (file_exists("upload_mini/$num.jpg"))
							$pdf1->Image("upload_mini/$num.jpg",10,$offset,$taille,$taille,'JPG');
						else
							$pdf1->Image("images/fichier.jpg",10,$offset,$taille,$taille,'JPG');							
						}
				else
					$pdf1->Image("upload_mini/$num",10, $offset,$taille,$taille,'JPG');
				
				$deposeur=$donnees["deposeur"];
				if (is_numeric($deposeur))
					{
					$r1 =command("select * from  r_user where idx='$deposeur' ");
					$d1 = fetch_command($r1);
					if ( ($d1["droit"]=="S") || ($d1["droit"]=="s") || ($d1["droit"]=="R")|| ($d1["droit"]=="r") ) 
						$deposeur=" ".traduire("par")." ".libelle_user($deposeur)." (".libelle_organisme_du_user($deposeur).")";
					else
						$deposeur=traduire("par b�n�ficiaire");
					}	
					
				$pdf1->text(15+$taille, $offset+$taille-10,"D�pos� $deposeur le $date ");
				$pdf1->ln(10);
				
				$offset+=$taille;
				}
			$j++; 	
			}		

	
		$pdf1->Output("tmp/garde_$ref.pdf");
		
		$pdf->addPDF("tmp/garde_$ref.pdf", 'all');
		$j=1;
		echo "<p>".traduire('G�n�ration du dossier');
		$reponse =command("select * from r_attachement where ref='$ref' order by date DESC ");
		while (($donnees = fetch_command($reponse) ) && ($j<100))
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
		
		if ($code_lecture!="")
			pdfEncrypt ("tmp/_$ref.pdf", decrypt( $code_lecture ), "tmp/$ref.pdf",'P');
	
		echo " : ".traduire('Ok').". <p> ";
				
		if ($code_lecture!="")
			{
			echo traduire('Cliquez ')." <a href=\"tmp/$ref.pdf\"target=_blank > ".traduire('ici')." <img src=\"images/-fichier.jpg\"  title=\"".traduire('Document prot�g�')."\" width=\"100\"  > </a>".traduire("pour ouvrir le fichier avec code de lecture (recommand�) ");
			echo " ".traduire('ou cliquez ')."<a href=\"tmp/_$ref.pdf\"target=_blank > ".traduire('ici')." <img src=\"images/fichier.jpg\"  title=\"".traduire('Document NON prot�g�')."\" width=\"40\" height=\"40\" ></a>".traduire("pour acc�der au fichier SANS code de lecture (non- recommand�). ");
			}
		else
			echo " ".traduire('Cliquez ')."<a href=\"tmp/_$ref.pdf\"target=_blank > ".traduire('ici')." <img src=\"images/fichier.jpg\"  title=\"".traduire('Document NON prot�g�')."\" width=\"40\" height=\"40\" ></a>".traduire("pour acc�der au fichier (sans code de lecture). ");
		
		echo " <p><BR><p>".traduire("Attention, le fichier ne sera plus accessible d�s que vous aurez quitt� cette page: ");
		echo " <BR> - ".traduire("Soit vous consultez maintenant le document : cliquez sur le lien et saisissez le code de lecture;");
		echo " <BR> - ".traduire("Soit vous imprimez maintenant le document : ouvrir le fichier en cliquant sur le lien, saisissez le code de lecture et faire 'Imprimer';");
		echo " <BR> - ".traduire("Soit vous enregistrer maintenant le fichier sur votre disque : clic droit sur le lien et faire 'enregistrer la cible du lien sous';");
		echo " <BR> - ".traduire("Soit vous d�cidez d'envoyer un lien vers le dossier par mail vers le(s) destinataire(s) de votre choix en cliquant <a href=\"index.php?".token_ref("dossier_mail")."&dossier=_$ref.pdf\"  >ici</a>;");
		echo " <p><BR><p> ".traduire("Pour lire le fichier, il est n�cessaire d'utiliser des logiciels tels que ")."<a href=\"http://get.adobe.com/fr/reader/\">Acrobat Reader </a>, <a href=\"http://www.foxitsoftware.com/Secure_PDF_Reader/\">Foxit Reader </a>, etc. ";

		ajout_log( $user_idx, traduire("G�n�ration dossier:")." $comment", $user_idx );
		pied_de_page("x");
		}
		
	function bouton_upload($ref,$idx, $type_doc="")
		{
		global $action,$user_droit,$user_lecture,$user_idx,$id;
			
		$flag_acces="";
		$date_jour=date('Y-m-d');
		$nb_doc=0;
		
		$reponse =command("select * from r_attachement where ref='$ref'  ");
		while (($donnees = fetch_command($reponse) ) && ($nb_doc<100))
			$nb_doc++; 	
		$j=1;

		echo "<table><tr> ";
		//echo "<form method=\"POST\" action=\"index.php\" enctype=\"multipart/form-data\">";
		//echo "<input type=\"hidden\" name=\"action\" value=\"upload\"> " ;
		//token ("upload");
		//echo "<input type=\"hidden\" name=\"retour\" value=\"$action\"> " ;
		//echo "</form>";
		if (substr($ref,0,1)=="A")
			{
			$reponse =command("select * from  r_user where idx='$idx' ");
			$donnees = fetch_command($reponse) ;
			$recept_mail=$donnees["recept_mail"];	
			$flag_acces=$donnees["lecture"];
			$tel=$donnees["telephone"];
			$id=$donnees["id"];
			
			$reponse =command("select * from r_dde_acces where type='A' and user='$idx' and date_dde='$date_jour'");
			if ($donnees = fetch_command($reponse) )
				{
				$code=$donnees["code"];	
				if ($code=="")
					$code="????";
				}
			else 
				$code="";
				
			echo "<td> <img src=\"images/papier.png\" width=\"35\" height=\"35\" > </td>";
			
			echo "<td><ul id=\"menu-bar\">";
			echo "<li><a href=\"index.php?".token_ref("ajout_admin")."\"  > + ".traduire('Papier Administratif')."  </a>";
			$_SESSION['user_idx']=$idx;
			
			echo "<ul >";

			echo "<li> <a href=\"index.php?".token_ref("ajout_admin")."\"> ".traduire('G�rer les documents')." </a></li>";
			echo "<li> <a href=\"index.php?".token_ref("draganddrop")."\"> ".traduire('D�poser des documents')." </a></li>";			
			if ($recept_mail<$date_jour)
				echo "<li><a id=\"depot\" href=\"index.php?".token_ref("recept_mail")."\">".traduire('Autoriser d�pot par Mail (Aujourd\'hui)')."</a></li>";
			
			if ($user_droit=="")
				echo "<li><a href=\"index.php?".token_ref("visu_lecture")."\">".traduire('Code de lecture')." </a></li>";
		// dossier aussi pour AS
		//	if ($user_droit=="") 
				echo "<li><a href=\"index.php?".token_ref("dossier")."\"> ".traduire('Constituer un dossier')." </a></li>";
		
			if ($user_droit!="")
				{
				if ($code=="")
					echo "<li><a href=\"index.php?".token_ref("dde_acces")."\">".traduire('Demander code d\'acc�s')."</a></li>";
				else
					if ($code=="????")
						echo "<li> - ".traduire('En attente d\'autorisation d\'acc�s par responsable')."</li>";
					else
						{
						echo "</td>";
						echo "<td>".traduire('Acc�s autoris� par')." ".libelle_user($donnees["autorise"])."</td>";
//						$flag_acces=$code;
						$flag_acces=""; // contournement de T248 sur g�n�ration du fichier SANS code provisoire
						}
				}
			else
				$flag_acces=$user_lecture;
			echo "</ul></ul></td>";
			lien_c ("images/ajouter.png", "draganddrop", "" , traduire("Ajouter un doc") , "30");
			}
		else
			{
			echo "<td> <img src=\"images/photo.png\" width=\"35\" height=\"35\" > </td>";
			echo "<td><ul id=\"menu-bar\">";
			if ($user_droit=="")
				echo "<li><a href=\"index.php?".token_ref("ajout_photo")."\" > + ".traduire('Espace personnel')." </a>";
			else
				echo "<li><a href=\"index.php?".token_ref("ajout_photo")."\" > + ".traduire('Justificatifs')." </a>";
				
			echo "<ul>";
			echo "<li> <a href=\"index.php?".token_ref("ajout_photo")."\"> ".traduire('G�rer les documents')." </a></li>";
			echo "<li> <a href=\"index.php?".token_ref("draganddrop_p")."\"> ".traduire('D�poser des documents')." </a></li>";	
			
			echo "</ul></ul></td>";
			lien_c ("images/ajouter.png", "draganddrop_p", "" , traduire("Ajouter un doc") , "30");

			}
		
		if (substr($ref,0,1)=="A")
			{
			if ($recept_mail>=$date_jour)
				{
				$id=strtolower($id);
				echo "<td> - ".traduire("R�ception de document par mail autoris� pour la journ�e")." (<strong>'$id@fixeo.com</strong>' ";
				if (VerifierTelephone($tel))
					echo traduire("ou")." '<strong>$tel@fixeo.com</strong>'";
				echo ")";				
				lien ("images/croixrouge.png", "supp_recept_mail", param("idx","$idx" ),"Annuler l'autorisation." );
				}
			else
				{
				echo "<td> - ".traduire("D�pot permanent par r�f�rent de confiance ou b�n�ficiaire")." (<strong>'$id@fixeo.com</strong>' ";
				if (VerifierTelephone($tel))
					echo traduire("ou")." '<strong>$tel@fixeo.com</strong>'";
				echo ")";	
				}					
			echo "</td>";
			}
			
		if ((($action=="ajout_admin") &&  (substr($ref,0,1)=="A"))|| ( ($action=="ajout_photo")&&  (substr($ref,0,1)=="P")) ) 
			{
			if ( $nb_doc<MAX_FICHIER)
				{
				if (substr($ref,0,1)=="A")
					echo "<td> <a href=\"index.php?".token_ref("draganddrop")."\"> ";
				else
					echo "<td> <a href=\"index.php?".token_ref("draganddrop_p")."\"> ";

				echo traduire("D�pot par Glisser/D�poser (Drag&drop)")."<img src=\"images/fichier.png\" width=\"35\" height=\"35\" ><img src=\"images/fleche.png\" width=\"35\" height=\"35\" ><img src=\"images/dossier.png\" width=\"35\" height=\"35\" ></a></td>";
				
				if ($_SERVER['REMOTE_ADDR']=="127.0.0.1")	
					{				
					echo "</table> <table><tr><td><form method=\"POST\" action=\"index.php\" >".traduire('D�pot de fichier unique')." </td>";
					if ($action=="ajout_admin")
						{
						echo "<td> ";
						liste_type("");
						echo "</td>";
						}

					if (substr($ref,0,1)=="A")
							echo "<td> R�f�rence : <input type=\"text\" name=\"ident\" size=\"15\"  value=\"\"></td>  " ;
					echo "<td><input type=\"file\" size=\"50\"  name=\"nom\" />";
					echo "<input type=\"hidden\" name=\"ref\" value=\"$ref\"> " ;
					echo "<input type=\"hidden\" name=\"idx\" value=\"$idx\"> </td> " ;
					token ("upload");
					echo "<td><input type=\"submit\" id=\"upload\" value=\"".traduire('Charger')."\" >  ";
					echo " </form> </td>";
					}
				}
			else
				echo "<td>".traduire('Nombre maximum de fichiers atteind.')."</td>";
			}
		
		echo "</table> ";
		$type_doc_avant="???";
		if ($user_droit=="")
			{
			$reponse =command("select * from r_attachement where ref='$ref' order by date DESC ");
			echo "<table> ";
			}
		else
			$reponse =command("select * from r_attachement where ref='$ref' order by status ASC");
			
		$n=0;
		while (($donnees = fetch_command($reponse) ) && ($j<100))
			{
			$type_doc=$donnees["status"];
			if ( ($type_doc_avant!=$type_doc) && ($user_droit!="") )
					{
					
					echo "</table> </div>";
					
					echo "<div class=\"expandbox\">	<a href=\"#\" onclick=\"Expand(this); return false;\"> <table><tr><td width=\"100\">$type_doc</td><td>";
					
					visu_liste_miniature($ref,$type_doc);
					$j=1;
					echo " </td></table> </a><table style=\"display:none\" class=\"expand\">";
					
					}
					
			$type_doc_avant=$type_doc;		
			
			if ((($j-1) % 4)==0)
				echo "<tr>";
			$num=$donnees["num"];	
			$ref=$donnees["ref"];				
			visu_doc($num,$flag_acces,$j);
			
			echo " <table> <tr>";

			if (($user_droit=="") || (($user_droit=="S") && ( ($donnees["deposeur"]==$user_idx) || ($donnees["deposeur"]=='') ) ) )
				if ((($action=="ajout_admin") &&  (substr($ref,0,1)=="A"))|| ( ($action=="ajout_photo")&&  (substr($ref,0,1)=="P")) ) 
					{
					lien ("images/croixrouge.png", "supp_upload_a_confirmer", param("num","$num" ). param("retour","$action" ),"Supprimer" );
					echo " . ";	
					}

			if ($user_droit=="")
				if ((($action=="ajout_admin") &&  (substr($ref,0,1)=="A"))|| ( ($action=="ajout_photo")&&  (substr($ref,0,1)=="P")) ) 
					{
					lien ("images/switch.png", "switch", param("num","$num" ). param("retour","$action" ),"Changer d'espace" );
					echo " . ";
					}
			
			if ((($action=="ajout_admin") &&  (substr($ref,0,1)=="A"))|| ( ($action=="ajout_photo")&&  (substr($ref,0,1)=="P")) ) 
				lien ("images/illicite.png", "illicite", param("num","$num" ). param("retour","$action" ),"Signaler comme illicite" );
			echo " </table> ";

			echo "</td>";
			$j++; 	
			}

		echo "</div></table> ";
		
		if ($action!="ajout_admin")	
			echo "<HR>";	
		}
		
	function supp_attachement ($num)
		{
		if ( !isset ($_SESSION['chgt_user']) || ($_SESSION['chgt_user']==false)  )
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
			command("delete from r_attachement where num='$num' ");
			}
		}
		
	function supp_tous_fichiers($idx)
		{
		$reponse =command("select * from  r_attachement where ref='A-$idx' or ref='P-$idx'");		
		while ( $donnees = fetch_command($reponse) ) 
			supp_attachement ($donnes["num"]);
		}
		
	function modif_type_doc($num, $type)
		{
		global $action;
		
		$reponse =command("update r_attachement SET status = '$type' where num='$num' ");
		$action = "ajout_admin";
		}
		
		
	
	function liste_mail_administratifs($username, $password)
		{
		$domain = explode('@', $username); 
		switch ($domain[1]) 
			{
			case "gmail.com": $hostname = '{imap.gmail.com:993/imap/ssl}INBOX'; break;
			case "doc-depot.com": $hostname = '{ssl0.ovh.net:993/imap/ssl}INBOX'; break;
			case "orange.fr": 
			case "wanadoo.fr":$hostname = '{imap.orange.fr:993/imap/ssl}INBOX'; break; 
			case "free.fr":$hostname = '{imap.free.fr:143/imap}INBOX'; break;

			case "laposte.net":$hostname = '{imap.laposte.net:993}INBOX'; break;			
			
			case "hotmail.com":
			case "hotmail.fr":
			case "live.fr":$hostname = '{pop3.live.com:995/pop/ssl}INBOX'; break;			
			
			case "bbox.fr":$hostname = '{imap4.bbox.com:993/imap/ssl}INBOX'; break; 
			case "yahoo.fr":$hostname = '{imap.mail.yahoo.com:995/pop/ssl}INBOX'; break;
			case "neuf.fr":
			case "sfr.fr":$hostname = '{imap.sfr.com:143/imap}INBOX'; break;
			case "cegetel.fr":$hostname = '{imap.gmail.com:993/imap/ssl}INBOX'; break;
			case "aol.com":$hostname = '{imap.fr.aol.com:143/imap}INBOX'; break;

				
			default : return;
			}

		/* try to connect */
		$inbox = imap_open($hostname,$username,$password,OP_READONLY) or die('Cannot connect : ' . imap_last_error());

		if($inbox)
			{
			/* grab emails */
			//$emails = imap_search($inbox,'ALL');

			//$emails = imap_search ( $inbox, "UNSEEN" );
			$emails[] = imap_search ( $inbox, " UNSEEN FROM \"@raz.com\"  " );

			/* if emails are returned, cycle through each... */
			if($emails) 
				{

				/* begin output var */
				$output = '';
				
				/* put the newest emails on top */
				rsort($emails);
				
				$i=0;
				/* for every email... */
				foreach($emails as $email_number) 
					if (!is_numeric($email_number))
					break;
					else
					{
					/* get information specific to this email */
					$overview = imap_fetch_overview($inbox,$email_number,0);
					$header = imap_header($inbox, $email_number);
					
					if ( isset($header->subject) && isset($header->from) && isset($header->date) )
						{
						$from = $header->from;
						foreach ($from as $id => $object) {
							$fromaddress = strtolower($object->mailbox . "@" . $object->host);
							}
							
						if ($overview[0]->seen=="1")
							$gras="</b>";
						else
							$gras="<b>";

						
						/* output the email header information */
						$output.= "<tr><td>$gras".date ("d/m/Y H:i",strtotime($header->date));	
						$output.= "</td><td>$gras".$fromaddress."</td><td>$gras";	
						
						$elements = imap_mime_header_decode($header->subject);
						for ($j=0; $j<count($elements); $j++) {
							$output.= $elements[$j]->text;
							}
						
						/* output the email body */
				   //    $structure = imap_fetchstructure($inbox, $email_number);
				//		$message = imap_fetchbody($inbox,$email_number,0);
				//		$output.= '<div class="body">'.$message.'</div>';
						$i++;
						if ($i==10) break;
						}
					}
				
				echo "</center><p>".traduire("Exemples des mails concern�s").": <table><tr><td>Date</td><td>Exp�diteur</td><td>Sujet mail</td><tr>$output</td></table>";
				} 

			/* close the connection */
			imap_close($inbox);
			}
		else
			erreur ("Mot de passe incorrect");
		}
		
	function mail_valide_surv($user_mail)
		{
		$domain = explode('@', $user_mail); 
		if (isset($domain[1]))
			switch ($domain[1]) 
				{
				case "gmail.com":
				case "doc-depot.com":
				case "orange.fr":
				case "wanadoo.fr":
				case "hotmail.com":
				case "hotmail.fr":
				case "yahoo.fr":
				case "live.fr":
				case "sfr.fr":
				case "cegetel.fr":
				case "neuf.fr":
				case "free.fr":
				case "aol.com":
				case "laposte.net":
						return (true);
						break;
						
				default : return (false);
				}
			return (false);
		}

	function alerte_surv_mail()
		{
		global $user_idx;
		
		$pw=encrypt(variable("n1"));
		$pw2=encrypt(variable("n2"));
		$alerte=variable("alerte");
		if ($pw==$pw2)
			{
			$reponse = command("select * FROM r_surv_mail where idx_user='$user_idx' ");
			if ($donnees = fetch_command($reponse))
				command("UPDATE `r_surv_mail` set pw='$pw' , alerte='$alerte' where idx_user='$user_idx' ");
			else
				command("INSERT INTO `r_surv_mail`  VALUES ( '$user_idx', '$pw', '$alerte', '')");
			}
		else
			erreur ("Les 2 mots de passe ne sont pas identiques.");
		}
	
	function surv_mail()
		{
		global $action,$user_mail, $user_idx;

		echo "<table><tr><td > <img src=\"images/voir.png\" width=\"35\" height=\"35\" > </td><td >  <ul id=\"menu-bar\">";
		echo "<li><a href=\"index.php?".token_ref("surv_mail")."\"  > + ".traduire('Surveillance arriv�e mails administratifs')." </a></li>";
		echo "</ul></td></table>";

		echo traduire("En enregistrant votre mot de passe de votre messagerie mail, nous v�rifierons l'arriv�e de mails administratifs (CAF, pole_emploi, imp�ts, etc) et alerterons vous-m�me ou vos r�f�rents de confiance selon votre choix s'ils ne sont pas consult�s dans les 5 jours suivnats leur arriv�e). ");
		echo traduire("Remarque : vos r�f�rents de confiance ne peuvent r�pondre ou traiter ces mails.");
		echo "<p>";
		
		if (mail_valide_surv($user_mail))
			{
			$pw="";
			$alerte="";
			$reponse = command("select * FROM `r_surv_mail`  where idx_user='$user_idx' ");
			if ($donnees = fetch_command($reponse))
				{
				$pw=decrypt($donnees["pw"]);
				$alerte=$donnees["alerte"];
				}
			debut_cadre (500);
			formulaire ("alerte_surv_mail");
			echo "<table><tr>";
			echo "<tr> <td> ".traduire('Mail')." : </td> <td> $user_mail</td>";
			echo "<TR> <td>".traduire('Mot de passe').": </td><td><input class=\"center\" type=\"password\" id=\"pwd\" name=\"n1\" value=\"$pw\"/></td>";
			echo "<TR> <td>".traduire('Confirmation').": </td><td><input class=\"center\" type=\"password\" name=\"n2\" id=\"pwd1\" value=\"$pw\"/></td>";
			echo "<td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> Voir saisie<td>";

			$val_init=$alerte;
			echo "<tr> <td> ".traduire('Pr�venir')." </td> <td><SELECT name=\"alerte\" >";
			affiche_un_choix($val_init,"Aucun", traduire("Personne"));
			affiche_un_choix($val_init,"Moi", traduire("Uniquement moi"));
			affiche_un_choix($val_init,"RC", traduire("R�f�rents de confiance"));
			echo "</SELECT></td>";
			echo "<input type=\"hidden\" name=\"user\"  value=\"$user_idx\"> " ;
			echo "<tr> <td> </td> <td><input type=\"submit\" id=\"nouveau_referent\" value=\"".traduire('Modifier')."\" ></form> </td> ";
			echo "</table></div></center>";
			fin_cadre();
			liste_mail_administratifs($user_mail, $pw);
			}
		else
			{
			debut_cadre (500);
			echo "<p>";
			echo traduire("Cette fonctionnalit� n'est disponible que pour les adresses se terminant par @gmail.com, @doc-depot.com, @orange.fr, @wanadoo.fr, @hotmail.com, @hotmail.fr, @live.fr, @sfr.fr, @cegetel.fr, @neuf.fr, @yahoo.fr, @free.fr, @aol.com, @laposte.net");
			echo "<p>";
			fin_cadre();
			}
		
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
			$reponse = command("select * FROM `r_referent`  where user='$user' and organisme='$organisme' and nom='$nom' and prenom='$prenom' and tel='$tel' and mail='$mail' and adresse='$adresse' ");
			if ($donnees = fetch_command($reponse))
				{
				erreur(traduire("Ce r�f�rent de confiance existe d�j�."));
				}
			else
				{
				$nb=0;
				$reponse = command("select * FROM `r_referent`  where user='$user'  ");
				while ($donnees = fetch_command($reponse)) $nb++;
				
				if ($nb>9) 
					erreur(traduire("Nombre maximum (10) de r�f�rents atteint."));
				else
					{
					$idx=inc_index("referent");
					$reponse = command("INSERT INTO `r_referent`  VALUES ( '$idx', '$user', '$organisme', '$nom','$prenom', '$tel','$mail','$adresse')");
					if (is_numeric($nom))
						$nom=libelle_user($nom);

					$organisme = libelle_organisme($organisme);	
					
					ajout_log( $user_idx, traduire("Ajout R�f�rent")."  $organisme / $nom" );
					}
				}
			}
		}
		
	FUNCTION supp_referent($idx)
		{
		global $user_idx;
		
		$reponse = command("select * FROM `r_referent`  where idx='$idx' ");
		$donnees = fetch_command($reponse);	
		$idx_ref= $donnees["nom"];
		
		if (is_numeric($idx_ref))
			$lib=libelle_user($idx_ref);
		else
			if ($idx_ref!="Tous")
				$lib =$idx_ref." ".$donnees["prenom"];
			else
				$lib = libelle_organisme($donnees["organisme"]);
		
		$cmd = "DELETE FROM `r_referent`  where idx='$idx' ";
		$reponse = command($cmd);
		ajout_log( $user_idx, traduire("Suppression R�f�rent de confiance")." $lib " );
		}		

	function visu_referent($idx, $user="", $masque="")
		{
		$reponse =command("select * from  r_referent where idx='$idx' ");
		$donnees = fetch_command($reponse);

		$organisme=$donnees["organisme"];

		if ($organisme=="")
			{
			$nom=($donnees["nom"]);
			$prenom=($donnees["prenom"]);
			$tel=($donnees["tel"]);	
			$mail=($donnees["mail"]);	
			$adresse=(stripcslashes($donnees["adresse"]));	
			$mail= (formate_mail($mail));

			echo "<tr><td> $organisme </td><td> $nom   </td><td> $prenom   </td><td> $tel </td><td> $mail</td><td> $adresse</td>";
			}
		else
			{
			$idx=$donnees["nom"];
			if ($idx!="Tous")			
				visu_referent_user($idx, $user, $masque);
			else
				{
				$r1 =command("select * from  r_organisme where idx='$organisme' ");
				$d1 = fetch_command($r1);
				
				$tel=$d1["tel"];	
				$mail=formate_mail($d1["mail"]);	
				$adresse=stripcslashes($d1["adresse"]);	
				$organisme=libelle_organisme($organisme);
				echo "<tr><td> $organisme </td><td> </td><td> </td><td> $tel </td><td> $mail</td><td> $adresse</td>";
				}
			}
		}

	function visu_referent_user($idx, $user="", $masque="")
		{
		$r1 =command("select * from  r_user where idx='$idx' ");
		$d1 = fetch_command($r1);
		$nom=($d1["nom"]);
		$idx2=$d1["idx"];
		$prenom=($d1["prenom"]);
		$tel=($d1["telephone"]);	
		$mail=($d1["mail"]);	
		$droit=$d1["droit"];
		$organisme=libelle_organisme($d1["organisme"]);
		$adresse=adresse_organisme($d1["organisme"]);

		if (($user!="") && (($droit!="s") || ($droit!="m")) && ($masque==""))
			{
			$voir =	"<form method=\"POST\" action=\"index.php\" >";
			$voir .="<input type=\"image\" width=\"45\" height=\"45\" src=\"images/contact.png\" title=\"Demander\">";
			//$voir .="<input type=\"hidden\" name=\"action\" value=\"recup_mdp\">";
			$voir .= token_return("recup_mdp");
			$voir .="<input type=\"hidden\" name=\"user\" value=\"$user\">";
			$voir .="<input type=\"hidden\" name=\"as\" value=\"$idx2\">";
			$voir .="</form>";
			$mail =$voir; 
			}
		if ($masque!="") 
			$mail="";
			
		$mail= formate_mail($mail);
		if (($droit=="s") || ($droit=="p") || ($droit=="m")  )
			{
			if ($masque!="") 
				echo "<tr><td> $organisme </td><td> <img src=\"images/inactif.png\" title=\"Inactif\" width=\"15\" height=\"15\"> $nom   </td><td> $prenom   </td><td> $tel </td><td> $mail</td><td> $adresse</td>";
			}
		else
			echo "<tr><td> $organisme </td><td> $nom   </td><td> $prenom   </td><td> $tel </td><td> $mail</td><td> $adresse</td>";
		}

	function titre_referent($organisme, $mode="")
		{
		echo "<div class=\"CSSTableGeneratorB\" ><table> ";
		if ($organisme=="")
			{
				echo "<tr>	<td> ".traduire('Structure sociale')." </td>
							<td> ".traduire('Nom')." </td>
							<td> ".traduire('Pr�nom')." </td>
							<td> ".traduire('T�l�phone')." </td>";
			if ($mode=="")			
				echo "<td> ".traduire('Mail')." </td>";
			else
				echo "<td>  </td>";
			echo "<td> ".traduire('Adresse')." </td>" ;
			}
		else
			echo "<tr><td> ".traduire('Structure Sociale')." </td><td> ".traduire('Acteur Social')."</td>" ;
		}
		
	function bouton_referent($idx)
		{
		global $action,$num_lien;

		echo "<table><tr><td> <img src=\"images/referent.png\" width=\"35\" height=\"35\" > </td><td>  <ul id=\"menu-bar\">";
		if ($action!="detail_user")
			echo "<li><a href=\"index.php?".token_ref("ajout_referent")."\"  > + ".traduire('R�f�rents de confiance')." </a></li>";
		else
			echo "<li><a> + ".traduire('R�f�rents de confiance')." </a></li>";
		
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
			echo "<td><input type=\"submit\" id=\"nouveau_referent\" value=\"".traduire('Ajouter')."\" ></form> </td> ";
			}
		
		$nb_rc=0;
		$reponse =command("select * from  r_referent where user='$idx' order by organisme ");
		while ($donnees = fetch_command($reponse) ) 
			{
			$organisme=stripcslashes($donnees["organisme"]);
			if ($organisme!="")
				$nb_rc++;
			$idx=$donnees["idx"];
			visu_referent($idx);
			if ($action=="ajout_referent")
				lien_c ("images/croixrouge.png", "supp_referent_a_confirmer", param("idx","$idx" ), traduire("Supprimer") );
			}
		echo "</table></div>";
		if ($nb_rc==0)
			echo "<p>".traduire("Vous n'avez pas d�sign� de r�f�rent de confiance appartenant � une structure sociale, nous vous recommandons vivement d'en d�signer un, voire plusieurs, pour vous permettre de r�cup�rer votre mot de passe en cas de perte de ce dernier.");
		}
		
	FUNCTION nouveau_user($id,$pw,$droit,$mail,$organisme,$nom,$prenom,$anniv,$telephone,$nationalite,$ville_nat,$adresse,$recept_mail ,$prenom_p,$prenom_m,$code_lecture,$nss,$type_user)
		{
		global $action,$user_idx,$user_prenom,$user_nom,$bdd, $user_fuseau;
		
		$action="ajout_user";
		$date_jour=date('Y-m-d');
		$idx="";
		$reponse = command("select * from r_user where nom='$nom' and prenom='$prenom' and anniv='$anniv'and ville_nat='$ville_nat'");
		if (!fetch_command($reponse) )
			{
		// A la cr�ation du compte le code lecture n'est plus initialis� avec le mot de passe de connexion
		//	if ($code_lecture=="")
		//		$code_lecture=$pw;		

			$anniv=mef_date($anniv);
			if ($anniv=="")
				erreur( traduire("Format de date incorrect (doit �tre jj/mm/aaaa)"));
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
					erreur( traduire("Attention, tous les champs ne sont pas renseign�s")) ;
				else
					if ( ($id!="???") && (strlen($id)<8 ) )
						erreur(traduire("L'identifiant est trop court (au moins 8 caract�res)."));
					else
						{
						$reponse = command("select * from r_user where id='$id' ");
						if ( (!fetch_command($reponse) ) || ($id!="jm") || ($id!="jean-michel.cot")|| ($id!="jm.cot") || ($id!="contact")|| ($id!="fixeo"))
							{
							$pw=encrypt($pw);
							if ($code_lecture!="")
								$code_lecture=encrypt($code_lecture);					

							if ($droit!="")
								{
								$mail = trim($mail);
								if ( VerifierAdresseMail($mail))
									{
									if ( ($organisme!="") || ($droit=="R") || ($droit=="E") || ($droit=="F"))
										{
										$idx=inc_index("user");
										
										if ($telephone!="") // T353
											{
											$telephone = trim($telephone);

											$plus="";
											if ($telephone[0]=='+')
												$plus='+';
											$telephone = $plus.preg_replace('`[^0-9]`', '', $telephone);
											}
										
										command("INSERT INTO `r_user`  VALUES (  '$idx', '$id', '$pw','$droit','$mail','$organisme','$nom','$prenom','$anniv','$telephone','$nationalite','$ville_nat','$adresse','$recept_mail' ,'$prenom_p','$prenom_m','$date_jour','$code_lecture','','' ,'' ,'','$type_user','fr','$user_fuseau')");
										ajout_log( $idx, traduire("Cr�ation utilisateur")."  $idx / $droit / $nom/ $prenom",	 $user_idx );

										$body= traduire("Bonjour").", $prenom $nom ";
										$body.= "<p> $user_prenom $user_nom ".traduire("vous a cr�� un compte sur 'Doc-depot.com': ");
										$body.= "<p> ".traduire("Pour accepter et finaliser la cr�ation de votre compte sur 'Doc-depot.com', merci de cliquer sur ce")." <a id=\"lien\" href=\"".serveur."index.php?".token_ref("finaliser_user")."&idx=".addslashes(encrypt($idx))."\">".traduire('lien')."</a> ". traduire("et compl�ter les informations manquantes.");
										$body .="<br><br>".traduire("Si le lien ne fonctionne pas, recopiez dans votre navigateur Internet cette adresse : ")."<strong>".serveur."index.php?".token_ref("finaliser_user")."=&idx=".addslashes(encrypt($idx))."</strong>.";
										$body .= "<p> <hr> <center> Copyright ADILEOS </center>";
										// Envoyer mail pour demander saisie pseudo et PW
										envoi_mail($mail,traduire("Finaliser la cr�ation de votre compte"),$body);
											
										envoi_mail(parametre('DD_mail_gestinonnaire'),traduire("Cr�ation du compte")." $prenom $nom ".traduire('par')." $user_prenom $user_nom ","",true);
										}
									else
										erreur (traduire("La structure sociale doit �tre renseign�e."));
									}
								else
									erreur (traduire("Format de mail incorrect ou absent")." $mail.");
								}	
							else
								{ // cr�ation b�n�ficiaire 
								
								// si cr�ation depuis fissa
								$compte_fissa= variable("fissa");	
								if ($compte_fissa!="")
									{
									$mail= variable("fissa_mail");
									$telephone= variable("fissa_tel");
									$adresse= variable("fissa_add");
									}
								
								$idx=inc_index("user");
								command("INSERT INTO `r_user`  VALUES (  '$idx', '$id', '$pw','$droit','$mail','$organisme','$nom','$prenom','$anniv','$telephone','$nationalite','$ville_nat','$adresse','$recept_mail' ,'$prenom_p','$prenom_m','$date_jour','$code_lecture','$nss','','','','$type_user','fr','$user_fuseau')");
								ajout_log( $idx, traduire("Cr�ation compte B�n�ficiaire")."  $idx / $nom/ $prenom", $user_idx );

								if ($compte_fissa!="")
									{
									$user= $_SESSION['user'];
									$modif=time();
									command("UPDATE $bdd set activites='$idx' , modif='$modif', user='$user' where date='0000-00-00' and nom='$compte_fissa' and pres_repas='' ");
									}
								}

							}
						else
							erreur (traduire("Identifiant d�j� existant"));
						}
				}
			}
			else
				erreur (traduire("Utilisateur d�ja existant."));	
		return($idx);
		}

	FUNCTION mail_user($idx)
		{
		$reponse = command("select * from r_user where idx='$idx' ");
		$d1 = fetch_command($reponse);
		return ($d1["mail"]);
		}
	FUNCTION telephone_user($idx)
		{
		$reponse = command("select * from r_user where idx='$idx' ");
		$d1 = fetch_command($reponse);
		return ($d1["telephone"]);
		}
		
	FUNCTION maj_user($idx,$id,$pw,$nom,$prenom)
		{
		global $action;
		
		$reponse = command("select * from r_user where id='$id' ");
		if (!$donnees=fetch_command($reponse) )
			{
			if (strlen($id)>7 )
				{
				if (strlen($pw)>7 )
					{
					if ($pw!=$id ) // testpassword($mdp)
						{
						if ( testpassword($pw)>65 ) 
							{
							// si changement d'"id" v�rifier qu'il n'existe pas d�ja
							$_SESSION['droit']=$donnees['droit'];
							$pw=encrypt($pw);
							$reponse = command("UPDATE `r_user` SET id='$id', pw='$pw', nom='$nom', prenom='$prenom'  where idx='$idx'  ");
							ajout_log( $id, traduire("Finalisation compte")." $id / $nom / $prenom" );
							$_SESSION['pass']=true;	 
							$_SESSION['user']=$idx;		

							
							msg_ok(traduire("Compte cr�� avec succ�s"));
							return(TRUE);
							}
						else 
							erreur(traduire("Le mot de passe n'est pas assez complexe (utiliser des Majuscules, Chiffres, caract�res sp�ciaux)"));
						}
					else 
						erreur(traduire("Le mot de passe doit �tre diff�rent de l'identifiant."));
					}
				else 
					erreur(traduire("Le mot de passe est trop court (au moins 8 caract�res)."));
				}
			else 
				erreur(traduire("L'identifiant est trop court (au moins 8 caract�res)."));
			}
		else
			erreur (traduire("Identifiant d�j� existant"));	
		return (false);
		}
			
	FUNCTION modif_tel($idx,$telephone, $mail)
		{
		$mail=trim($mail);
		$telephone=trim($telephone);
		
		$plus="";
		if (isset ($telephone[0]) && ($telephone[0]=='+') )
			$plus='+';
		$telephone = $plus.preg_replace('`[^0-9]`', '', $telephone);
		if ($mail!="")
			if (!VerifierAdresseMail($mail))
				{
				erreur (traduire("Format de mail incorrect"));
				return(false);
				}
		if ($telephone!="")
			if (!VerifierTelephone($telephone))
				{
				erreur (traduire("Format de t�l�phone incorrect"));
				return(false);
				}
		$reponse = command("UPDATE `r_user` SET mail='$mail', telephone='$telephone'  where idx='$idx'  ");
		ajout_log( $idx, traduire("Modification tel")." : $telephone /mail :$mail" );
		return(true);
		}

	FUNCTION modification_user($idx,$nom, $prenom , $telephone, $mail, $droit, $organisme)
		{
		global $user_idx;
		
		$mail=trim($mail);
		$telephone=trim($telephone);
		$reponse = command("UPDATE `r_user` SET mail='$mail', telephone='$telephone', nom='$nom', prenom='$prenom', droit='$droit' , organisme='$organisme'  where idx='$idx'  ");
		ajout_log( $idx, traduire("Modification nom/prenom/tel/mail"), $user_idx );
		}		
		
	FUNCTION modification_langue($idx,$langue)
		{
		$reponse = command("UPDATE `r_user` SET langue='$langue' where idx='$idx'  ");
		ajout_log( $idx, traduire("Modification langue en ")." '$langue'" );
		}		
		
	FUNCTION modif_domicile($idx,$organisme, $adresse)
		{
		if ($organisme!="")
			{
			$r1 =command("select * from  r_organisme where idx='$organisme' ");
			$d1 = fetch_command($r1);
			$adresse=$d1["adresse"];
			}
		$adresse=stripcslashes($adresse);
		$reponse = command("UPDATE `r_user` SET organisme='$organisme', adresse='$adresse'  where idx='$idx'  ");
		ajout_log( $idx, traduire("Modification Domiciliation") );
		}
		
	function recept_mail($id,$date)
		{
		global $user_idx;
		
		$reponse = command("UPDATE `r_user` SET  recept_mail='$date' where idx='$id'  ");
		ajout_log( $id, traduire("Autorisation reception par mail"), 	 $user_idx );
		}

	function supp_recept_mail($id)
		{
		global $user_idx;
		
		$reponse = command("UPDATE `r_user` SET  recept_mail='' where idx='$id'  ");
		ajout_log( $id, traduire("Annulation autorisation reception par mail"), 	 $user_idx );
		}
		
	function maj_droit($id,$droit)
		{
		global $user_idx;
		
		$reponse = command("UPDATE `r_user` SET  droit='$droit' where idx='$id'  ");
		if (ctype_upper($droit))
			ajout_log( $id, traduire("Mise � jour droit")." ==> ".traduire("Actif"), $user_idx );
		else
			ajout_log( $id, traduire("Mise � jour droit")." ==> ".traduire("Inactif"),	$user_idx );

		}
		
	FUNCTION supp_user($idx)
		{
		global $user_idx;

		$reponse =command("select * from  r_user where idx='$idx'  ");		
		if ($donnees = fetch_command($reponse))
			{
			$nom=$donnees["nom"];
			$prenom=$donnees["prenom"];	
			$reponse =command( "DELETE FROM `r_referent` where user='$idx' ");
			$reponse =command("DELETE FROM `r_lien`  where user='$idx' ");
			$reponse =command("UPDATE `r_user` SET droit='-s' , pw='(supprim�)' , organisme=''  where idx='$idx' ");
			ajout_log( $idx, traduire("Suppression compte")." $nom $prenom ($idx)" ,  $user_idx);
			}
		}		
		
	
		
	function titre_user($droit)
		{
		echo "<tr><td> ".traduire('Nom')." </td><td> ".traduire('Pr�nom')."</td><td> ".traduire('T�l�phone')." </td><td> ".traduire('Mail')." </td>";
		if ($droit=="A")			
			echo "<td> ".traduire('Structure Sociale')." </td><td> ".traduire('Droit')." </td>";
		else
			if (($droit!="R")&&($droit!="F")&&($droit!="E"))
				echo "<td> ".traduire('Anniv')." </td><td> ".traduire('Nationalit�')."  </td><td> ".traduire('Ville natale')." </td><td> ".traduire('Adresse')." </td><td> ".traduire('Structure sociale')." </td>";
			else
				echo "<td> ".traduire('Structure sociale')." </td><td> ".traduire('Droit')." </td>";
	
		}
		
	function visu_user($idx,$droit)
		{
		global $user_droit;
		
		$reponse =command("select * from  r_user where idx='$idx'  ");		
				
		if ($donnees = fetch_command($reponse) ) 
			{
			//$pw=$donnees["pw"];				
			$organisme=stripcslashes($donnees["organisme"]);	

			$r1 =command("select * from  r_organisme where idx='$organisme' ");
			$d1 = fetch_command($r1);
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
			if ( ($user_droit=="M") || ($user_droit=="S") || ($user_droit=="s") || ($user_droit=="A") || ($user_droit=="R"))
				{
				// !!!
				if (($donnees["droit"]=="S") || ($donnees["droit"]=="P") || ($donnees["droit"]=="M") )
					$nom=  "<img src=\"images/actif.png\"width=\"20\" height=\"20\"> $nom ";
				if (($donnees["droit"]=="s") || ($donnees["droit"]=="p") || ($donnees["droit"]=="m"))
					$nom=  "<img src=\"images/inactif.png\"width=\"20\" height=\"20\"> $nom  ";
				}

			if ( ($user_droit=="A") || ($user_droit=="R"))
				{
				if ($id=="???")
					$nom= "$nom (compte non finalis�: <a  id=\"lien\"  href=\"index.php?".token_ref("renvoyer_mail")."&idx=".encrypt($idx)."\"> renvoyer mail</a>)";
				else
					$nom= "$nom (Identifiant='$id')";
				}
			$mail = formate_mail($mail);
			$telephone = formate_telephone($telephone);
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
					{
					echo "<td> $organisme </td>";
					if ( ($donnees["droit"]=="S") || ($donnees["droit"]=="s"))
						echo "<td> DD+FISSA </td>";	
					else
						if ( ($donnees["droit"]=="M") || ($donnees["droit"]=="m"))
							echo "<td> Mandataire </td>";
						else
							echo "<td> FISSA </td>";
					}
			}
		}

	function liste_droit_as( )
		{
		echo "<td><SELECT name=\"droit\"  >";
		affiche_un_choix($val_init,"S","DD+FISSA");
		affiche_un_choix($val_init,"P","FISSA");
		affiche_un_choix($val_init,"M","Mandataire");
		echo "</SELECT></td>";
		}	
		
	function bouton_user($droit, $organisme, $filtre1="")
		{
		global $action, $user_idx,$user_type_user, $user_organisme;
		
		if ($filtre1!="")
			$filtre="and (nom REGEXP '$filtre1' or prenom REGEXP '$filtre1' or telephone REGEXP '$filtre1' or mail REGEXP '$filtre1' or anniv REGEXP '$filtre1') ";
		else
			$filtre="";
		echo "<table><tr><td width> <ul id=\"menu-bar\">";
		if ($droit=="A")
			echo "<li><a href=\"index.php?".token_ref("ajout_user")."\"  > + ".traduire('Responsable')."  </a></li>";
		else
			if ($droit=="R")
				{
				echo "<li><a href=\"index.php?".token_ref("ajout_user")."\"  > + ".traduire('Acteur Social')."  </a>";
				echo "<ul><li><a href=\"index.php?".token_ref("justificatifs")."&organisme=".encrypt($user_organisme)."\"> Liste des justificatifs </a></li></ul>";
				}
			else
				if ($droit=="S") 
					echo "<li><a href=\"index.php?".token_ref("ajout_user")."\"  > + ".traduire('B�n�ficiaires')."   </a></li>";
				else
				if ($droit=="M") 
					echo "<li><a href=\"index.php\"  > + ".traduire('B�n�ficiaires')."   </a></li>";
					
		echo "</ul></td>";
		
		echo "</td><td>";
		formulaire ("");
		echo "<input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
		echo "</form><td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td>";
		if ($filtre1!="")
			lien_c ("images/croixrouge.png", "","" , traduire("Supprimer"));
		
	//	if ($droit=="R")
	//		echo "<td><a href=\"index.php?action=justificatifs&organisme=".encrypt($user_organisme)."\"> Liste des justificatifs </a></td>";	
			
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
			echo "<input type=\"hidden\" name=\"type_user\"  value=\"$user_type_user\"> " ;
					
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
				if ($droit=="A") // T350
					liste_organisme("");
				else
					echo "<td><input type=\"hidden\" name=\"organisme\"  value=\"\"> </td> ";

			if ($droit=="A") 
				//echo "<input type=\"hidden\" name=\"droit\"  value=\"R\"> " ;
				{
				liste_type_user("R");
				}
			else
				if ($droit=="R")
					{
					liste_droit_as();
					}
				else
					echo "<input type=\"hidden\" name=\"droit\"  value=\"\"> " ;
				
			echo "<td><input type=\"submit\" id=\"nouveau_user\" value=\"".traduire('Ajouter')."\" > </td> ";				
			echo "<td><input type=\"hidden\" name=\"prenom_p\"  value=\"\"> </td> ";
			echo "<input type=\"hidden\" name=\"prenom_p\"  value=\"???\"> " ;
			echo "<input type=\"hidden\" name=\"prenom_m\"  value=\"???\"> " ;
			echo "<input type=\"hidden\" name=\"code_lecture\"  value=\"\"> " ;
			echo "<input type=\"hidden\" name=\"recept_mail\"  value=\"\"></form> " ;
			}
	
		if ($droit=="R")			
			$reponse =command("SELECT * FROM `r_lien`, `r_user` WHERE (r_user.droit='S' or r_user.droit='P' or r_user.droit='M' ) and r_user.organisme=r_lien.organisme and r_lien.user='$user_idx' $filtre  ");
		else
			if ($droit=="A")			
				$reponse =command("select * from  r_user where droit='R' or droit='E' or droit='F' or droit='t' or droit='T' or droit='A' $filtre  "); // T352
			else
				$reponse =command("select * from  r_user where droit='' $filtre ");		
				
		while ($donnees = fetch_command($reponse) ) 
			{
			$idx=$donnees["idx"];
			
			visu_user($idx,$droit);

			if ($action=="ajout_user")
				{
				lien_c("images/croixrouge.png", "supp_user_a_confirmer", param("idx","$idx" ), traduire("Supprimer"));
				lien_c ("images/modifier.jpg", "modifier_user", param("idx","$idx" ), traduire("Modifier") );
				if ($donnees["droit"]=="S") 
					lien_c("images/inactif.png", "user_inactif", param("idx","$idx" ), traduire("Rendre Inactif") );
				if  ($donnees["droit"]=="P") 
					lien_c("images/inactif.png", "user_inactif_P", param("idx","$idx" ), traduire("Rendre Inactif") );				
				if  ($donnees["droit"]=="M") 
					lien_c("images/inactif.png", "user_inactif_M", param("idx","$idx" ), traduire("Rendre Inactif") );
				if ($donnees["droit"]=="s") 
					lien_c("images/actif.png", "user_actif", param("idx","$idx" ), traduire("Rendre actif") );
				if  ($donnees["droit"]=="p") 
					lien_c("images/actif.png", "user_actif_P", param("idx","$idx" ), traduire("Rendre actif") );
				if  ($donnees["droit"]=="m") 
					lien_c("images/actif.png", "user_actif_M", param("idx","$idx" ), traduire("Rendre actif") );
					}
			}
		echo "</table></div>";
		}
			
	function modif_user($idx)
		{
		global $user_idx, $user_droit;

		$reponse =command("select * from  r_user where idx='$idx'  ");				
		if ($donnees = fetch_command($reponse) ) 
			{
			//$pw=$donnees["pw"];				
			$organisme=stripcslashes($donnees["organisme"]);	

			$r1 =command("select * from  r_organisme where idx='$organisme' ");
			$d1 = fetch_command($r1);
			$organisme=stripcslashes($d1["organisme"]);
			
			$droit=$donnees["droit"];	
			$mail=$donnees["mail"];	
			$nom=$donnees["nom"];
			$prenom=$donnees["prenom"];				
			$telephone=$donnees["telephone"];	
			$adresse=stripcslashes($donnees["adresse"]);	
					
			echo "<hr></table> ".traduire('Modification')." <p><div class=\"CSSTableGenerator\"><table> ";
			titre_user("R");
			formulaire ("modif_user");
			echo "<tr>";
			echo "<input type=\"hidden\" name=\"idx\"   value=\"$idx\"> </td>";
			echo "<td> <input type=\"texte\" name=\"nom\"   size=\"20\" value=\"$nom\"> </td>" ;
			echo "<td> <input type=\"texte\" name=\"prenom\"   size=\"15\" value=\"$prenom\"> </td>" ;
			echo "<td> <input type=\"texte\" name=\"telephone\"   size=\"12\" value=\"$telephone\"> </td>" ;
			echo "<td>  <input type=\"texte\" name=\"mail\"   size=\"35\" value=\"$mail\"> </td>" ;
			if ($user_droit=='R')
				{
				echo "<input type=\"hidden\" name=\"droit\"  value=\"$droit\"> " ; // T328
				liste_organisme_du_responsable ($user_idx);
				}
			else
				liste_type_user($droit);
				
			echo "<td><input type=\"submit\"  id=\"modif_user\" value=\"".traduire('Modifier')."\" > </td> ";

		echo "</form></table></div>";
		pied_de_page();
		}
	}
	
	function ajout_beneficiaire($idx,$organisme)
		{
		global $action,$user_droit,$user_organisme,$user_type_user;
		
		echo "<table> ";

		echo "<tr><td width> <ul id=\"menu-bar\">";
		echo "<li><a href=\"index.php?".token_ref("ajout_beneficiaire")."\"  > + ".traduire('Ajout')." </a></li>";
		
		echo "</ul></td>";
		echo "</table>";
		
		echo "<center><TABLE><TR> <td  width=\"700\">";
		echo traduire("Important: Tous les  champs sont obligatoires et prenez soin de bien les orthographier et v�rifier chaque champ car il n'est plus possible de les modifier. ");
		echo traduire("Les r�ponses � ces questions vous seront demand�es pour r�cup�rer le mot de passe de votre compte, si vous l'avez perdu.")."<p></td> ";
		
		debut_cadre("700");
		echo "<table>";
		formulaire ("nouveau_user");
		echo "<tr> <td>".traduire('Identifiant').": </td><td>  <input type=\"texte\" name=\"id\"   size=\"20\" value=\"".variable("id")."\"> </td>";
		echo "<td> ".traduire('Au moins 8 caract�res')." </td>" ;
		echo "<tr> <td> ".traduire('Mot de passe 1ere connexion')." :</td><td> 123456 </td>" ;
		echo "<input type=\"hidden\" name=\"pw\"  value=\"123456\"> " ;
		echo "<input type=\"hidden\" name=\"droit\"  value=\"\"> " ;
		echo "<input type=\"hidden\" name=\"fissa\"   value=\"".variable("fissa")."\">" ;
		echo "<input type=\"hidden\" name=\"fissa_mail\"   value=\"".variable("fissa_mail")."\">" ;
		echo "<input type=\"hidden\" name=\"fissa_tel\"   value=\"".variable("fissa_tel")."\">" ;
		echo "<input type=\"hidden\" name=\"fissa_add\"   value=\"".variable("fissa_add")."\">" ;
		echo "<tr><td> ".traduire('Nom')." :  </td> <td><input type=\"texte\" name=\"nom\"   size=\"20\" value=\"".variable("nom")."\"> </td>" ;
		echo "<tr><td> ".traduire('Pr�nom').":</td> <td><input type=\"texte\" name=\"prenom\"   size=\"20\" value=\"".variable("prenom")."\"> </td>" ;
		echo "<tr><td> ".traduire('Date de naissance').": </td><td><input type=\"texte\" name=\"anniv\"   size=\"10\" value=\"".variable("anniv")."\"> </td><td>".traduire('jj/mm/aaaa')."</td>" ;
		echo "<tr><td> ".traduire('Ville natale').": </td><td>  <input type=\"texte\" name=\"ville_nat\"   size=\"20\" value=\"".variable("ville_nat")."\"> </td>" ;
		echo "<tr><td> ".traduire("Pays d'origine").": </td><td> ";
		select_pays("", variable("nationalite"));
		echo "</td>" ;
		echo "<input type=\"hidden\" name=\"recept_mail\"  value=\"\"> " ;
		echo "<input type=\"hidden\" name=\"telephone\"  value=\"\"> " ;
		echo "<input type=\"hidden\" name=\"mail\"  value=\"\"> " ;
		echo "<input type=\"hidden\" name=\"organisme\"  value=\"\"> " ;
		echo "<input type=\"hidden\" name=\"adresse\"  value=\"\"> " ;
		echo "<input type=\"hidden\" name=\"type_user\"  value=\"$user_type_user\"> " ;
		echo "<tr><td> ".traduire('Pr�nom du p�re')." (*):</td> <td><input type=\"texte\" name=\"prenom_p\"   size=\"20\" value=\"".variable("prenom_p")."\"> </td><td> (*) ".traduire('Indiquer AUCUN si non connu')."</td> " ;
		echo "<tr><td> ".traduire('Pr�nom de la m�re')." (*) :</td> <td><input type=\"texte\" name=\"prenom_m\"   size=\"20\" value=\"".variable("prenom_m")."\"> </td>" ;

		echo "<input type=\"hidden\" name=\"code_lecture\" value=\"\"> " ;
		echo "<input type=\"hidden\" name=\"recept_mail\"  value=\"\"> " ;
		$_SESSION['img_number'] = "";  // A quoi ca sert ?????
		echo "<input type=\"hidden\" name=\"num\" value=\"\"> " ;
		echo "<tr><td> </td><td><input type=\"submit\"  id=\"nouveau_user\"  value=\"".traduire('Valider cr�ation')."\" > </td> ";
		echo "</table> ".traduire('En validant cette cr�ation, vous confirmez avoir pris connaissance des')." <a href=\"conditions.html\">".traduire('Conditions d\'utilisation')." </a>. <p>";
		fin_cadre();
		pied_de_page("x");
		}
	
	function verif_existe_user()
		{
		echo "<center><p><br>".traduire('Saisissez les informations ci-dessous pour v�rifier si la personne a d�j� un compte.')."<p> ";
		debut_cadre("500");
		echo "<br><table>";
		formulaire ("verif_user");
		echo "<tr><td> ".traduire('Nom').":  </td> <td><input type=\"texte\" name=\"nom\"   size=\"20\" value=\"".variable("nom")."\"> </td>" ;
		echo "<tr><td> ".traduire('Pr�nom').":</td> <td><input type=\"texte\" name=\"prenom\"   size=\"20\" value=\"".variable("prenom")."\"> </td>" ;
		echo "<tr> <td> ".traduire('Date de naissance').": </td><td><input type=\"texte\" name=\"anniv\"   size=\"10\" value=\"".variable("anniv")."\"> </td><td> ".traduire('jj/mm/aaaa')."</td>" ;
		echo "<tr><td> </td><td><input type=\"submit\"  id=\"verif_user\"  value=\"".traduire('V�rifier')."\" > </td> ";
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
			$reponse =command("DELETE FROM `r_sms`  where idx='$user_idx' and num_seq='$num_seq' ");
		echo "<table> <tr><td> <img src=\"images/sms.png\" width=\"35\" height=\"35\" ></td> ";
		echo "<td> <ul id=\"menu-bar\">";
		echo "<li> <a href=\"index.php?".token_ref("note_sms")."\"  >+ ".traduire('Notes et SMS')." </a> </li></ul></td>";
		echo "<td> </form>";
		formulaire ("");
		echo "<input type=\"text\" name=\"filtre\" size=\"0\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
		echo "</td><td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td>";
		echo "</form>";
		
		if ($filtre1!="")
			lien_c ("images/croixrouge.png", "", "" , "Supprimer filtre");
		echo "</table> ";
		echo "<div class=\"CSSTableGeneratorB\" > ";
		echo "<table><tr><td width=\"15%\"> ".traduire('Date')."   </td><td> ".traduire('Texte')."</td>";		
		if ($action=="note_sms")
			{
			formulaire ("ajout_note");
			echo "<tr><td> </td>";
			echo "<td> <input type=\"texte\" name=\"note\"   size=\"100\" value=\"\"> </td>";
			echo "<input type=\"hidden\" name=\"user\"  value=\"$user_idx\"> " ;
			echo "<td><input type=\"submit\" id=\"ajout_note\" value=\"".traduire('Ajouter')."\" ></form> </td> ";
			}
		$reponse =command("select * from  r_sms where (idx='$user_idx' $filtre ) order by date desc");		
		while ($donnees = fetch_command($reponse) ) 
			{
			$num_seq=$donnees["num_seq"];	
			$date=$donnees["date"];	
			$d3= explode(" ",$date);
			$date=mef_date_fr($d3[0])." ".$d3[1];
			$ligne=stripcslashes($donnees["ligne"]);
			echo "<tr><td>  $date   </a></td><td> $ligne </td>";
			if ($action=="note_sms")
				lien_c ("images/croixrouge.png", "note_sms", param("num_seq","$num_seq" ) , traduire("Supprimer"));
			}
		echo "</table></div>";
		}

		
	function bouton_beneficiaire($nom,$organisme,$filtre1="")
		{
		global $user_idx, $user_droit;
		
		if ($filtre1!="")
			$filtre2="and (nom REGEXP '$filtre1' or prenom REGEXP '$filtre1' or telephone REGEXP '$filtre1' or mail REGEXP '$filtre1' or anniv REGEXP '$filtre1' or adresse REGEXP '$filtre1'or nationalite REGEXP '$filtre1' ) ";
		else
			$filtre2="";
		
		$libelle_organisme= libelle_organisme($organisme);
		echo "<table><tr>";
		$libelle= "Je suis le r�f�rent de ";
		
		echo "<table><tr><td><img src=\"images/bene.png\" width=\"35\" height=\"35\" ></td><td> <ul id=\"menu-bar\">";

		if (strtoupper($user_droit)!="M")	
			{
			echo "<li> <a href=\"index.php?".token_ref("ajout_beneficiaire")."\"  >+ ".traduire($libelle)." </a> ";
			echo "<ul> <a href=\"index.php?".token_ref("verif_existe_user")."\"  > ".traduire('V�rifier si b�n�ficiaire existe d�j�')."</a> </li> </ul>";
			}
		else
			echo "<li> <a href=\"index.php\"  >+ ".traduire($libelle)." </a>  </li> </ul>";

		echo "</td><td>";
		formulaire ("");		
		echo "<input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
		echo "</form><td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td>";
		if ($filtre1!="")
			lien_c ("images/croixrouge.png", "", "" , traduire("Supprimer filtre"));
		echo "</table><div class=\"CSSTableGenerator\" ><table> ";
		echo "<tr><td>   </td><td> ".traduire('Nom')."  </td><td> ".traduire('Pr�nom')." </td><td>  ".traduire('T�l�phone')." </td><td> ".traduire('Mail')." </td><td> ".traduire('Anniv')." </td><td> ".traduire("Pays d'origine")."  </td><td> ".traduire('Ville natale')." </td><td> ".traduire('Adresse')." </td>";
		
		affiche_beneficiaire(2,$organisme, $user_idx, $filtre2);		
		if (strtoupper($user_droit)!="M")		
			affiche_beneficiaire(1,$organisme, $user_idx, $filtre2);		
		echo "</table></div>";
		}		
		
		
	function affiche_beneficiaire($mode,$organisme,$user_idx,$filtre2="")
		{
	
		if ($mode==1) // on affiche  ceux qui sont li� � la structure
			$reponse =command("select * from  r_referent where nom='Tous'  and organisme='$organisme' ");		
		else //mode=2 ==> on affiche ceux qui sont RC personnellement 
			$reponse =command("select * from  r_referent where nom='$user_idx' and organisme='$organisme' ");		

		while ($donnees = fetch_command($reponse) ) 
			{
			if (isset($donnees["user"]))
				$nom1=$donnees["user"];
			else
				$nom1=$donnees["idx"];
		
			$tst_doublon=false;
			if ($mode==2)
				{
				$r1 =command("select * from  r_referent where nom='Tous' and organisme='$organisme' and user='$nom1' ");
				if ($d1 = fetch_command($r1))
					$tst_doublon=TRUE;
				}
				
			if (!$tst_doublon)
				{
				$r1 =command("select * from  r_user where idx='$nom1' $filtre2 ");
				if ($d1 = fetch_command($r1))
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
					$mail= formate_mail($mail);
					$telephone= formate_telephone($telephone);
					
					lien_c ("images/voir.png", "detail_user", param("user","$nom1" ), traduire("Voir le d�tail") );
					echo "<td>$nom   </a></td><td> $prenom </td><td> $telephone</td><td> $mail</td>";
					echo "<td> $anniv </td><td> $nationalite   </td><td> $ville_nat </td><td> $adresse </td>";
					}
				}
			}

		}
		

		
	FUNCTION nouveau_organisme($organisme,$tel,$mail,$adresse,$sigle,$doc,$fuseau)
		{
		global $action,$user_idx;
		
		$action="ajout_organisme";
		
		$r1 =command("select * from  r_organisme where organisme='$organisme' ");
		if (!($d1 = fetch_command($r1)))
			{
			$idx=inc_index("organisme");
			if ($doc=="")
				$doc = ";Tous;";
			$cmd = "INSERT INTO `r_organisme`  VALUES ( '$idx','$organisme', '$tel','$mail','$adresse','$sigle','','$doc','1','','$fuseau')";
			$reponse = command($cmd);
			ajout_log( $user_idx, traduire("Cr�ation structure")." ($idx) : $organisme / $mail / $tel / $adresse / $sigle" );
			}
		else
			erreur (traduire("Structure sociale d�j� existante!"));
		}
	
	FUNCTION modif_organisme($id,$telephone, $mail, $adresse, $sigle,$doc)
		{
		global $user_idx;
		
		$l=libelle_organisme($id);
		$reponse = command("UPDATE `r_organisme` SET mail='$mail', tel='$telephone' , adresse='$adresse' , sigle='$sigle', doc_autorise='$doc'  where idx='$id'  ");
		ajout_log( $user_idx, traduire("Modification structure")." $l : $mail / $telephone / $adresse / $sigle/ $doc" );
		}	
		
	FUNCTION supp_organisme($idx)
		{
		global $user_idx;
		
		$l=libelle_organisme($idx);
		
		$cmd = "DELETE FROM `r_organisme`  where idx='$idx' ";
		$reponse = command($cmd);
		ajout_log( $user_idx, traduire("Suppresion organisme")." $l " );
		}		

	
	function doc_autorise($organisme	)
		{
		$r1 =command("select * from  r_organisme where idx='$organisme' ");
		$d1 = fetch_command($r1);
		return($d1["doc_autorise"]);
		}
	
		
	function responsables_organisme($organisme)
		{
		$ligne="";
		$r1 =command("select * from r_lien where organisme='$organisme' ");
		while ($d1 = fetch_command($r1) ) 
			{
			$ligne= $ligne . libelle_user($d1["user"])."; ";
			}
		return($ligne);
		}		
	
	function titre_organisme()
		{
		global $user_droit;

		echo "<div class=\"CSSTableGenerator\" ><table><tr><td> ".traduire('Structure sociale')." </td><td> ".traduire('Sigle')." </td><td> ".traduire('Adresse')." </td><td> ".traduire('T�l�phone')." </td><td> ".traduire('Mail')." </td>" ;
		if ($user_droit=="A")
			echo "<td> ".traduire('Doc restreints')." </td><td> ".traduire('Responsables')." </td><td> ".traduire('Fuseau')." </td>";
		}
		
	function	liste_fuseau()
		{
		echo "<td><SELECT name=\"fuseau\" >";
		affiche_un_choix($val_init,"","FR");
		affiche_un_choix($val_init,"RE","RE");
		echo "</SELECT></td>";
		}
		
	function bouton_organisme()
		{
		global $action, $user_droit,$user_organisme;
	
		$filtre1=variable("filtre");

		echo "<table><tr><td><img src=\"images/organisme.png\" width=\"35\" height=\"35\" ></td><td width> <ul id=\"menu-bar\">";
		echo "<li><a href=\"index.php?".token_ref("ajout_organisme")."\"  > + ".traduire('Structure sociale')." </a></li>";
		echo "</ul></td>";
		formulaire ("");
		echo " <td> <input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
		echo "</form> </td> <td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td> ";
		if ($filtre1!="")
			lien_c ("images/croixrouge.png", "","" , traduire("Supprimer"));
		
		if ($action=="ajout_organisme") 
			echo "<td> ".traduire("Ne cr�ez une nouvelle structure qu'apr�s avoir v�rifi� qu'elle n'est pas d�j� enregistr�e.")."</td>";
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
					{
					echo "<td> <input type=\"texte\" name=\"doc\"   size=\"10\" value=\"\"> </td>" ;
					echo "<td> </td>" ;
					liste_fuseau();
					}
				else
					echo "<input type=\"hidden\" name=\"doc\"  value=\"\"> " ;
				echo "<td><input type=\"submit\"   id=\"nouveau_organisme\"  value=\"".traduire('Valider')."\" > </form></td> ";
				}

			if ($filtre1=="")
				$reponse =command("select * from  r_organisme  order by organisme asc");
			else
				$reponse =command("select * from  r_organisme where (adresse REGEXP '$filtre1' or organisme REGEXP '$filtre1' or sigle REGEXP '$filtre1' or mail REGEXP '$filtre1' or tel REGEXP '$filtre1') order by organisme asc");			

			while ($donnees = fetch_command($reponse) ) 
				{
				$idx=$donnees["idx"];	
				$adresse=stripcslashes($donnees["adresse"]);
				$organisme=stripcslashes($donnees["organisme"]);		
				if  (($user_droit=="A") || ( ($user_droit=="R ") && ($user_organisme==$idx)  ) )
					$organisme="<a href=\"index.php?".token_ref("membres_organisme")."&organisme=".encrypt($idx)."\"> $organisme </a>";

				$tel=$donnees["tel"];	
				$mail=$donnees["mail"];	
				$fuseau=$donnees["fuseau"];	
				
				$sigle=stripcslashes($donnees["sigle"]);	
				$doc_autorise=$donnees["doc_autorise"];	
				echo "<tr><td> $organisme </td><td> $sigle </td><td> $adresse   </td><td> $tel </td><td> $mail</td>";
				if ($user_droit=="A") 
					echo "<td> $doc_autorise</td><td>".responsables_organisme($idx)."</td><td> $fuseau</td>";
				if (($action=="ajout_organisme") && ( $user_droit=="A")) 
					lien_c ("images/croixrouge.png", "supp_organisme_a_comfirmer", param("idx","$idx" ), traduire("Supprimer") );
				}
			echo "</table></div><HR>";				
			}

		}

	function titre_affectation()
		{
		global $user_droit;

		echo "<div class=\"CSSTableGenerator\" ><table><tr><td> ".traduire('Structure sociale')." </td><td> ".traduire('Responsable')." </td>" ;
		}

	function liste_responsables(  )
		{
		echo "<td> <SELECT name=\"responsable\" id=\"responsable\"  >";
		
		affiche_un_choix("","");
		$reponse =command("select * from  r_user where droit='R' ");
		while ($donnees = fetch_command($reponse) ) 
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
		
		$reponse =command("select * from r_lien where user='$responsable'  ");
		while ($donnees = fetch_command($reponse) ) 
			{
			$idx = $donnees["organisme"];
			affiche_un_choix_2("",$idx,libelle_organisme($idx));			
			}
		echo "</SELECT></td>";
		}
		
	function organisme_d_un_responsable($responsable)
		{
		$ligne="";
		$r1 =command("select * from r_lien where user='$responsable' ");
		while ($d1 = fetch_command($r1) ) 
			{
			$ligne= $ligne . libelle_organisme($d1["organisme"])."; ";
			}
		return($ligne);
		}	
	

//================================================================================== affectation =====
	
	FUNCTION supp_affectation($organisme,$responsable)
		{
		global $action,$user_idx;
		
		$reponse =command("DELETE FROM `r_lien`  where organisme='$organisme' and user='$responsable' ");
		ajout_log( $user_idx, traduire("Suppresion affectation")." $organisme  <-> $responsable " );
		}	
		
	FUNCTION nouvelle_affectation ($organisme,$responsable)
		{
		global $action,$user_idx;
		
		$date_jour=date('Y-m-d');
		$r1 =command("select * from  r_lien where organisme='$organisme' and user='$responsable' ");
		if (!($d1 = fetch_command($r1)))
			{
			command("INSERT INTO `r_lien`  VALUES ('$date_jour','$organisme', '$responsable')");
			$reponse=command("select * from r_organisme where idx='$organisme' ");
			if ($donnees = fetch_command($reponse) ) 
				$fuseau=$donnees["fuseau"];
			else
				$fuseau="";
		
			command("UPDATE r_user SET organisme='$organisme', fuseau='$fuseau' where idx='$responsable' "); // T344
			
			ajout_log( $user_idx, traduire("Affectation")." : ".libelle_organisme($organisme)."($organisme)  <-> ".libelle_user($responsable)." ($responsable)" );
			}
		else
			erreur ( traduire("Affectation existante!"));
		}		
		
	function bouton_affectation()
		{
		global $action, $user_droit;

		echo "<table><tr><td width> <ul id=\"menu-bar\">";
		echo "<li><a href=\"index.php?".token_ref("ajout_affectation")."\"  > + ".traduire('Affectation')." </a></li>";
		echo "</ul></td></table>";

		titre_affectation();
		if ($action=="ajout_affectation") 
				{
				formulaire ("nouvelle_affectation");
				echo "<tr> " ;
				liste_organisme( "" );
				liste_responsables( );
				echo "<td><input type=\"submit\"   id=\"nouvelle_affectation\"  value=\"".traduire('Valider')."\" > </form></td> ";
				}

		$reponse =command("select * from  r_lien order by organisme asc");

		while ($donnees = fetch_command($reponse) ) 
				{
				$user=$donnees["user"];
				$orga=$donnees["organisme"];
				$responsable=libelle_user($user);
				$organisme=libelle_organisme($orga);		
				echo "<tr><td> $organisme </td><td> $responsable</td>";
				if ($action=="ajout_affectation")  
					lien_c ("images/croixrouge.png", "supp_affectation", param("organisme","$orga" ).param("user","$user" ), traduire("Supprimer") );
				}
			echo "</table></div><HR>";				
		}

function dde_acces($idx,$user, $type='A', $duree=0)
	{

	$date_jour=date('Y-m-d',  mktime(0,0,0 , date("m"), date("d")+$duree, date ("Y")));
		
	$reponse =command("select * from r_dde_acces where user='$user' and type='$type' and ddeur='$idx' and date_dde>'$date_jour'  ");
	if (! ($donnees = fetch_command($reponse) )  )
		{
		$reponse =command("INSERT INTO `r_dde_acces`  VALUES ('$idx' , '', '$date_jour', '$user', '', '', '$type' ) ");
		if ($type=="A")
			ajout_log( $idx, traduire("Demande d'acc�s au compte par")." ".libelle_user($user), $user );
		else
			ajout_log( $idx, traduire("Demande de recup�ration MdP")." ".libelle_user($user), $user );
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
	
	$reponse =command("UPDATE r_dde_acces set code='$code' , date_auto='$date_jour', autorise='$autorise' where user='$bene' and ddeur='$ddeur' and date_dde>='$date_jour' ");
	ajout_log( $ddeur, traduire("Autorisation d'acc�s au compte de")." ".libelle_user($bene)." ".traduire("par")." ".libelle_user($autorise)." ".traduire("�")." ".libelle_user($ddeur), $autorise );
	ajout_log( $bene, traduire("Autorisation d'acc�s au compte de")." ".libelle_user($bene)." ".traduire("par")." ".libelle_user($autorise)." ".traduire("�")." ".libelle_user($ddeur) );
	}	



	function supp_recup_mdp($as,$bene)
		{
		$date_jour=date('Y-m-d');

		$reponse =command("UPDATE r_dde_acces set code='' , date_auto='' where user='$bene' and ddeur='$as' and type=''  ");
		ajout_log( $bene, traduire("Fin d'autorisation de recup�ration par")." $as" );
		}

	Function traite_demande_acces($organisme,$user_idx)
		{
		$date_jour=date('Y-m-d');
		$j=0;
		$reponse =command("select * from r_user where droit='S' and organisme='$organisme' ");
		while ($donnees = fetch_command($reponse) ) 
			{
			$idx2=$donnees["idx"];
			$r1 =command("select * from r_dde_acces where type='A' and ddeur=$idx2 and date_dde>='$date_jour' ");
			while ($d1 = fetch_command($r1) ) 
				{
				if ($j++==0)
					{
					echo "<table><tr><td> <ul id=\"menu-bar\">";
					echo "<li> <a href=\"index.php?\"  > ".traduire('Demande d\'acc�s � traiter')." </a> </li>";
					echo "</ul></td></table> ";
					echo "<div class=\"CSSTableGenerator\" ><table> ";
					echo "<tr><td> ".traduire('Date demande')."  </td><td> ".traduire('Demandeur')."   </td><td> ".traduire('Autorisateur')." </td><td> ".traduire('B�n�ficiaire')." </td><td>  ".traduire('Date Autorisation')." </td><td>  ".traduire('Code')."  </td><td>   </td>";
					}
				$date_dde=$d1["date_dde"];
				$qui=$d1["user"];
//				$code=$d1["code"];
				$code="";
				$date_auto=$d1["date_auto"];
				$autorise=$d1["autorise"];
				
				echo "<tr><td> $date_dde </td><td> ".libelle_user($idx2)."</td><td>".libelle_user($autorise)." </td><td>".libelle_user($qui)." </td><td> $date_auto </td><td> $code </td>";
				echo "<td><form method=\"POST\" action=\"index.php\">  ";
				echo "<input type=\"hidden\" name=\"ddeur\" value=\"$idx2\"> " ;
				echo "<input type=\"hidden\" name=\"bene\" value=\"$qui\"> " ;
				echo "<input type=\"hidden\" name=\"autorise\" value=\"$user_idx\"> " ;
				if ($code=="")
					echo token_return("autorise_acces")."<input type=\"submit\" value=\"".traduire('Autoriser')."\"/> " ;
				else
					echo token_return("supp_acces")."<input type=\"submit\" value=\"".traduire('Supprimer acc�s')."\"/>" ;
				echo "</form>  </td>";	
				}
			if ($j!=0)
				echo "</div></table> ";
			}
		}
	
	// affiche l'historique d�s que la personnes est concern�e ou acteur
	// on ne travaille qu'avec l'index et plus l'id 
	function histo_beneficiaire($user_idx, $id)
		{
		global $user_droit,$filtre;
		
		echo "<hr><table><tr><td><img src=\"images/histo.png\" width=\"25\" height=\"25\" >  ".traduire('Historique')." : </td><td>";	
		
		formulaire("filtre_histo");
		echo "</td><td><input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre\" onChange=\"this.form.submit();\"> ";
		echo "</form><td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td>";
		if ($filtre!="")
			lien_c ("images/croixrouge.png", "supp_filtre_histo","" , traduire("Supprimer"));
		echo "</table>";
		
		$j=0;
		$reponse =command("select * from  log where (user='$user_idx' ) or (acteur='$user_idx' ) order by date DESC ");		

		echo "<div class=\"CSSTableGenerator\" ><table> ";
		echo "<tr><td> ".traduire('Date')." </td><td> ".traduire('Ev�nement')."</td><td> ".traduire('Acteur')."</td>";
		if ($user_droit!="")
			echo "<td> ".traduire('B�n�ficiaire')."</td>";

		while ($donnees = fetch_command($reponse) ) 
			{
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
			
			if (($filtre=="") || ( stripos($date, $filtre)!==false) || ( stripos($ligne, $filtre)!==false) || ( stripos($acteur, $filtre)!==false)|| ( stripos($user, $filtre)!==false))
				{
				echo "<tr><td title=\"$ip\">  $date  </td><td> $ligne </td><td> $acteur </td>";
				if ($user_droit!="")
					{
					if ($user!=$acteur)
						echo "<td> $user</td>";	
					else
						echo "<td> </td>";	
					}
				}
			}
		echo "</table></div>";	  
		pied_de_page("x");
	  }

 
function testpassword($mdp)	{ // $mdp le mot de passe pass� en param�tre
 
$point = 0;
$point_min =0;
$point_maj =0;
$point_caracteres =0;
$point_chiffre =0;
// On r�cup�re la longueur du mot de passe	
$longueur = strlen($mdp);
 
// On fait une boucle pour lire chaque lettre
for($i = 0; $i < $longueur; $i++) 	{
 
	// On s�lectionne une � une chaque lettre
	// $i �tant � 0 lors du premier passage de la boucle
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
		// On ajoute 5 points pour un caract�re autre
		$point = $point + 5;
 
		// On rajoute le bonus pour un caract�re autre
		$point_caracteres = 5;
	}
}
 
// Calcul du coefficient points/longueur
$etape1 = $point / $longueur;
 
// Calcul du coefficient de la diversit� des types de caract�res...
$etape2 = $point_min + $point_maj + $point_chiffre + $point_caracteres;
 
// Multiplication du coefficient de diversit� avec celui de la longueur
$resultat = $etape1 * $etape2;
 
// Multiplication du r�sultat par la longueur de la cha�ne
$final = $resultat * $longueur;
  
return $final;
 
}

function affiche_titre_user($user)
	{
	$reponse =command("select * from  r_user where idx='$user' ");		
	$donnees = fetch_command($reponse) ;

	$nom=$donnees["nom"];
	$prenom=$donnees["prenom"];				
	$anniv=$donnees["anniv"];	
	if ($anniv!="")
		$anniv="($anniv)";
	$tel=$donnees["telephone"];				
	$mail=$donnees["mail"];	
			
	$organisme=$donnees["organisme"];
	$organisme=libelle_organisme($organisme);
	$adresse=stripcslashes($donnees["adresse"]);
	
	$user=encrypt($user);

	if ($_SESSION['bene']!="")
		{
		echo "<table><tr><td> <ul id=\"menu-bar\">";
		echo "<li> <a href=\"index.php?".token_ref("detail_user2")."&user=$user\" > $nom $prenom $anniv </a></li>";
		echo "</ul></td><td> - ".traduire('Domiciliation').": $organisme / $adresse / $tel / $mail </td></table>";
		}
	else
		{
		echo "<table><tr><td> <ul id=\"menu-bar\">";
		echo "<li> <a href=\"index.php\">Justificatifs $nom $prenom </a></li>";
		echo "</ul></td></table>";
		}	
	}

function affiche_membre($idx, $opt_aff="")
	{
	$reponse =command("select * from r_user where idx='$idx' ");		
	$donnees = fetch_command($reponse) ;

	$droit=$donnees["droit"];	
	$nom=$donnees["nom"];	
	$prenom=$donnees["prenom"];
	$tel=$donnees["telephone"];
	$mail=$donnees["mail"];
	$id=$donnees["id"];
	$idx=$donnees["idx"];
	if ($opt_aff=="")
		echo "<tr><td>  $droit </td><td>  $id  </td><td> $nom </td><td> $prenom </td><td> $tel </td><td> $mail </td>";
	else 
		echo "<tr><td> $nom </td><td> $prenom </td>";
	$nb=0;
	$r1 =command("select * from r_attachement where ref='P-$idx' ");		
	while ($d1 = fetch_command($r1) ) 
		{
		$num=$d1["num"];
		visu_doc($num,0);
		$nb++;
		}
	return ($nb);
	}


	// ------------------------------------------------------------------------- Rendez-vous --------------------------------
	function titre_rdv($user_telephone)
		{
		echo "<div class=\"CSSTableGeneratorB\" > ";
		echo "<table><tr><td width=\"10%\"> ".traduire('Date')." </td><td width=\"10%\"> ".traduire('Heure')." </td><td> ".traduire('Message envoy� par SMS au')." $user_telephone </td><td> ".traduire('Pr�avis')." </td><td> ".traduire('Etat')." </td><td> ".traduire('Auteur')." </td>";		
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
			$reponse =command("select * FROM `DD_rdv` where idx='$idx' ");
			if ($donnees = fetch_command($reponse))
				{
				$date=$donnees["date"];	
				$ligne=$donnees["ligne"];	
				$reponse =command("DELETE FROM `DD_rdv` where idx='$idx' ");
				ajout_log( $idx, traduire("Suppression RDV le")." $date : '$ligne' ", $user_idx );		
				}
			}

		echo "<table> <tr>";
		echo "<td><img src=\"images/reveil.png\" width=\"35\" height=\"35\" > <td> ";		
		echo "<td> <ul id=\"menu-bar\">";
		if ( !VerifierPortable($user_telephone) )
			{
			echo "<li> <a href=\"index.php\"  >+ ".traduire('Rendez-vous')." </a> </li></ul></td>";

			if ($user_idx==$U_idx)
				echo "<td> - ".traduire('Pour acc�der � cette fonction il faut disposer d\'un n� de t�l�phone portable')." </td> ";
			else
				echo "<td> - ".traduire('Fonction non accessible car le b�n�ficiaire ne dispose pas d\'un n� de t�l�phone portable')."</td> ";
			}
		else
			echo "<li> <a href=\"index.php?".token_ref("rdv")."\"  >+ ".traduire('Rendez-vous')." </a> </li></ul></td>";

		echo "<td> </table> ";
			
		if ( ($action=="rdv") && (VerifierPortable($user_telephone) ) )
			{
			titre_rdv($user_telephone);
			$j++;
			formulaire ("ajout_rdv");
			echo "<tr>";
			echo "<td> <input type=\"texte\" name=\"date\"  class=\"calendrier\" size=\"10\" value=\"\"> </td> ";
			echo " <td><input type=\"texte\" name=\"heure\"   size=\"5\" value=\"\"> </td>";
			echo "<td> <input type=\"texte\" name=\"ligne\"   size=\"100\" value=\"\"> </td>";
			liste_avant( "1H" );
			echo "<input type=\"hidden\" name=\"user\"  value=\"$U_idx\"> " ;
			echo "<td><input type=\"submit\" id=\"ajout_rdv\" value=\"".traduire('Ajouter')."\" ></form> </td> ";
			}
		if ($user_idx==$U_idx)
			$reponse =command("select * from  DD_rdv where user='$U_idx'  order by date desc");		
		else
			$reponse =command("select * from  DD_rdv where user='$U_idx' and auteur='$user_idx'  order by date desc");	
			
		while ($donnees = fetch_command($reponse) ) 
			{
			if ($j==0)
				titre_rdv($user_telephone);
			$date=$donnees["date"];	
			$d3= explode(" ",$date);
			$date=mef_date_fr($d3[0]);
			$heure=$d3[1];
			$avant=$donnees["avant"];	
			$etat=$donnees["etat"];	
			if ($avant=="Aucun")
				$etat="";
			$idx=$donnees["idx"];	
			$ligne=stripcslashes($donnees["ligne"]);
			$auteur=libelle_user($donnees["auteur"]);
			echo "<tr><td> $date </td><td> $heure </td><td> $ligne </td><td> $avant </td><td> $etat </td><td> $auteur </td>";
			if ( ($action=="rdv") && ($etat=="A envoyer")) 
				lien_c ("images/croixrouge.png", "rdv", param("idx","$idx" ) , traduire("Supprimer"));
			$j++;
			}
		if ($j!=0)
			echo "</table></div>";
		else
			echo "<hr>";
		}


		
	// liste des  autoris�es
	function verif_action_autorise($action)
		{
		switch ($action)
				{
				// liste des actions ne n�cessitant pas de droits
				case "":
				case "cmd_perimee":
				case "liste":
				case "fr":
				case "ro":
				case "ar":
				case "es":
				case "de":
				case "gb":
				case "ru":
				case "dx":
				case "recup_mdp":
				case "enreg_bug":
//				case "enreg_contact":				
				case "bug":
//				case "contact":				
				case "recup_dossier": 
				case "envoyer_dossier": 
				case "dossier_mail": 
				case "justificatifs": 
				case "supp_filtre_histo": 
				case "filtre_histo": 
				case "alerte_surv_mail": 
				case "surv_mail":
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
				case "detail_user2":
				case "recept_mail":
				case "supp_recept_mail":
				case "user_inactif":
				case "user_actif":
				case "user_inactif_P":
				case "user_actif_P":
				case "user_inactif_M":
				case "user_actif_M":
				case "phpinfo":
				case "visu_pages_users":
				case "visu_page":
				case "archivage_php":
				case "cmd_sql":
				case "cmd_sql_backup":
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
				case "modif_trad":
				case "modif_trad_tech":
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
//				case "visu_fichier_tmp":
				case "visu_doc":
//				case "visu_pdf":
				case "visu_image_mini":
				case "ajout_beneficiaire":
				case "verif_existe_user":
				case "verif_user":
				case "upload":
				case "modifier_user":
				case "supp_filtre":
				case "alerte_admin":
				case "raz_mdp":
				case "raz_mdp1":
				case "dde_acces" :
				case "sms_test" :
				case "sms_test_ovh" :
				case "sms_test_ovh_dd" :
				case "sms_envoi" :
				case "sms_envoi_ovh" :
				case "sms_envoi_ovh_dd" :
				case "mail_test" :
				case "mail_envoi" :
				
				// liste des actions n�cessitant des droits de administrateur	
				case "nouveau_fissa" :				

				// liste des actions n�cessitant des droits de Fonctionnel	
				
				// liste des actions n�cessitant des droits de exploitant	
				case "en_trop":
				case "integrite":
				case "authenticite":
				case "force_supervision_sms":
				case "desactive_sms2mail":
				case "active_sms2mail":
				case "liste_compte":
				case "chgt_user":
				case "mini_pdf":
				
				case "afflog":
				case "afflog_t":
				
				// liste des actions n�cessitant des droits de Formateur			
				case "init_formation": 
				case "modif_mdp_formation":				
				
				// liste des actions n�cessitant des droits d'AS uniquement
				case "collegues" :
				case "cc_activite" :
				case "cc_maj_rdv" :
				case "cc_ajout" :
				case "cc_jour" :
				case "cc_usager" :
				case "cc_usagers" :
				case "cc_accueillant" :
				case "cc_precedent" :
				case "cc_suivant" :
				case "ajout_creneau_usager" :
				case "planning_select" :
				case "planning_ajout" :
				case "supp_creneau_a_confirmer" :
				case "supp_creneau" :
				case "usager_a_modifier" :
				case "nouveau_usager" :
				case "modif_usager" :

				case "supp_filtre_usager" :
				case "usager_a_inactiver" :
				case "cc_detail_usager" :
				case "nouveau_planning" :
				case "modifier_calendrier" :
				
				
					ajout_log_jour("----------------------------------------------------------------------------------- [ Action= $action ] ");
					return($action);
				break;
				
				default : 
					erreur ("Action '$action' inconnue");
					if (isset($_SESSION['user']))
						$nom=libelle_user($_SESSION['user']);
					else 
						$nom="user non connect� (".$_SERVER["REMOTE_ADDR"].")";
					ajout_log_tech ( "Action '$action' inconnue par $nom ", "P0" );
					
					// on p�nalise l'utilisateur 
					$ip=$_SERVER["REMOTE_ADDR"];
					ajout_echec_cx ($ip);
					tempo_cx ($ip);	
					return ("");
				}
		}
		
		// -====================================================================== DEBUT de PAGE ===============================
// Connexion BdD

	$user_lang='fr';
		
	include "ctrl_pays.php";

		
	/*
	if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)!="fr") 
		{ 
		aff_logo("x");
		echo "<p>Erreur : Langue navigateur n'est pas le fran�ais";
		pied_de_page(); 
		}
*/
	
	// ------------------------------------------------------------------------------ traitement des actions sans mot de passe
	$token=variable("token");	
	if ($token!="")	
		$action=verifi_token($token,variable("action"));
	else
		$action=variable("action");		
	//$action=variable_s("action");		
	if ($token!="")	
		$action=verifi_token($token,variable("action"));
	else
		$action=variable("action");		

	// on v�rifie que l'action demand�e (champ en clair) fait bien partie de la liste officielle ==> �vite le piratage
	$action = verif_action_autorise($action);	
	
	if (isset($_SESSION['lang']))
		$user_lang=$_SESSION['lang'];
	else
		$user_lang=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		
	if ($action!="")
		{
		if (($action=="fr") || ($action=="es") || ($action=="gb")|| ($action=="de")|| ($action=="ru") || ($action=="ro")|| ($action=="ar") )
			{
			$_SESSION['lang'] = $action;
			$user_lang=$_SESSION['lang'];
			if (isset ($_SESSION['user_idx']))
				modification_langue($_SESSION['user_idx'], $user_lang );
			}	
			
		if ($action=="recup_mdp")
			{
			aff_logo();
			$idx1=variable("as");
			dde_acces(variable("user"),$idx1,"",5);
			echo "<p>".traduire("Demande enregistr�e.");
			echo "<p><br><p> ".traduire('Rappel: vous avez 5 jours pour la contacter en personne. Elle s\'assurera de votre identit�.');
			
			titre_referent("");
			$reponse =command("select * from  r_user where idx='$idx1' ");
			$d1 = fetch_command($reponse);
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
			$ip= $_SERVER["REMOTE_ADDR"];
			tempo_cx ($ip);
			$n=enreg_bug(variable("titre"),variable("descript"),variable("type"),variable("impact"),variable("qui"));
			msg_ok( traduire("Bug enregistr�")." ($n).");
			$action="bug";
			}	

		if ($action=="cmd_perimee")
			{
			echo "<p>".traduire("L'action demand�e n'est plus autoris�e (d�lais d�pass�)");
			$action="";
			}	
						
		if ($action=="alerte_admin")
			{
			aff_logo();
			echo "<p><br>";
			$motif = variable_get("motif");
			ajout_log_tech( "Signalement : $motif" , "P1");
			msg_ok( traduire("Votre signalmement a �t� tansmis � l'administrateur du site."));
			echo "<p><br>";

			pied_de_page("x");
			}	
	

		if ($action=="bug")
			{
			$date_jour=date('Y-m-d');
			aff_logo_multiple();
			debut_cadre("700");
			echo "<p><br>".traduire('Signaler un bug ou demander une �volution')." : ";
			formulaire ("enreg_bug");
			echo "<TABLE><TR><td> ".traduire('R�dacteur').":</td> ";
			if (isset($_SESSION['user']))
				$nom=libelle_user($_SESSION['user']);
			else 
				$nom="";
			echo "<td> <input type=\"text\" name=\"qui\" size=\"50\" value=\"$nom\"/></td>";
			echo "<TR> <td>".traduire('Type').": </td>";
			echo "<td><SELECT name=\"type\"  >";
			affiche_un_choix($val_init,"Bugs","Bug");
			affiche_un_choix($val_init,"Fonctionnel","Evolution");
			echo "</SELECT></td>";
			echo "<TR> <td>".traduire('Importance').":</td>";
			echo "<td><SELECT name=\"impact\"  >";
			$val_init="Normal";
			affiche_un_choix($val_init,"Urgent","Bloquant");
			affiche_un_choix($val_init,"Prioritaire","Fort");
			affiche_un_choix($val_init,"Normal");
			affiche_un_choix($val_init,"Faible");
			echo "</SELECT></td>";
			echo "<TR> <td>".traduire('Titre').": </td><td> <input type=\"text\" name=\"titre\" size=\"70\" value=\"\"/></td>";
			echo "<TR> <td>".traduire('Description').": </td><td><TEXTAREA rows=\"5\" cols=\"50\" name=\"descript\" ></TEXTAREA></td>";
			echo "<TR> <td> </td> <td><input type=\"submit\"  id=\"enreg_bug\" value=\"".traduire('Enregistrer')."\"/><p></td>";
			echo "</form> </table> ";
			fin_cadre();
			pied_de_page("x");
			}	
/*
	function enreg_contact($qui, $coordo, $descript)
		{
		// temporaire
		$message = "Auteur : $qui";	
		$message .= "<p>Coordonn�es : $coordo";
		$message .= "<p>Description : $descript";
		envoi_mail(parametre('DD_mail_fonctionnel'),"Demande 'Nous contacter' ",$message, true);
		ajout_log( "", traduire("Demandeur")." : $qui / $coordo ", "");
		}
		if ($action=="enreg_contact")
			{
			aff_logo_multiple();
			$ip= $_SERVER["REMOTE_ADDR"];
			tempo_cx ($ip);
			enreg_contact(variable("qui"),variable("coordonnees"),variable("descript"));
			msg_ok(traduire("Demande enregistr�e."));
			pied_de_page("x");
			}	
			
		if ($action=="contact")
			{
			$date_jour=date('Y-m-d');
			aff_logo_multiple();
			debut_cadre("700");
			echo "<p><br> ".traduire('Pour faire une demande, merci de remplir le formulaire suivant')." : ";
			formulaire ("enreg_contact");
			echo "<TABLE><TR><td> ".traduire('Vos nom et pr�nom')." : </td> ";
			if (isset($_SESSION['user']))
				$nom=libelle_user($_SESSION['user']);
			else 
				$nom="";
			echo "<td> <input type=\"text\" name=\"qui\" size=\"60\" value=\"$nom\"/></td>";
			echo "<TR> <td> ".traduire('Vos coordonn�es').":</td><td> <input type=\"text\" name=\"coordonnees\" size=\"60\" value=\"\"/></td>";
			echo "<TR> <td> ".traduire('Description de')." <br>".traduire('votre demande')." :</td><td><TEXTAREA rows=\"5\" cols=\"60\" name=\"descript\" ></TEXTAREA></td>";
			echo "<TR> <td> </td> <td><input type=\"submit\"  id=\"enreg_contact\" value=\"".traduire('Envoyer la demande')."\"/><br></td>";
			echo "</form> </table>  <p> ".traduire('Nous vous contacterons dans les meilleurs d�lais.')." <br><br>";
			fin_cadre();
			pied_de_page();
			}				
*/	
		if ($action=="maj_user")
			{
			if (!maj_user(variable("idx"),variable("id"),variable("pw"),variable("nom"),variable("prenom")))
				$action="finaliser_user2";
			else
				{
				$action="";
				$_SESSION['user']=variable("idx");
				$_SESSION['user_idx']=variable("idx"); // T338
				}
			}

		if ($action=="recup_dossier")
			recup_dossier();
			
		if ($action=="reinit_mdp")
			{
			$date_jour=date('Y-m-d');

			$code=variable_get("code");
			$reponse =command("select * from r_dde_acces where code='$code' and type='' and  date_dde='$date_jour'  ");
			if ( ($donnees = fetch_command($reponse) )  )
				{
				aff_logo();
				$user=$donnees["user"];
				
				$r1 =command("select * from r_user where  idx='$user'  ");
				$d1 = fetch_command($r1) ;
					
				$identifiant=$d1["id"];
				$pw=decrypt($d1["pw"]);
				
				echo "<p>".traduire('R�initialisation de votre mot de passe');
				debut_cadre("700");
				echo "<br>".traduire('Rappel format mot de passe : Au minimum 8 caract�res, au moins une majuscule, <br>une minuscule et un chiffre (caract�res sp�ciaux possible)')."<p>";
				formulaire ("changer_mdp");
				echo "<table><TR><td> ".traduire('Identifiant').": </td> ";
				echo "<td> $identifiant <input type=\"hidden\" name=\"idx\" value=\"$user\"/></td>";
				echo "<input type=\"hidden\" name=\"ancien\" value=\"$pw\"/></td>";
				echo "<TR> <td>".traduire('Nouveau mot de passe').": </td><td><input class=\"center\" type=\"password\" id=\"pwd\" name=\"n1\" value=\"\"/></td>";

				echo "<TR> <td>".traduire('Confirmation').": </td><td><input class=\"center\" type=\"password\" name=\"n2\" id=\"pwd1\" value=\"\"/></td>";
				echo "<td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> Voir saisie<td>";
				echo "<TR>  <td></td><td><input type=\"submit\"  id=\"changer_mdp\"  value=\"".traduire('Modifier')."\"/></td>";
				echo "</form></table><p>  ";
				fin_cadre();
				pied_de_page("x");
				}
			else
				erreur(traduire("Lien invalide."));
			}	

		if ($action=="changer_mdp") 
			{
			echo "<center>";					
			$action="modif_mdp";
			$ok=FALSE;
			$idx=variable('idx');
			$reponse =command("SELECT * from  r_user WHERE idx='$idx'"); 
			if ($donnees = fetch_command($reponse))
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
											command("UPDATE r_user set lecture='".encrypt($mdp)."' where id='$id'");

										$mdp=encrypt($mdp);
										aff_logo();
										command("UPDATE r_user set pw='$mdp' where id='$id'");
										msg_ok (traduire('Modification du mot de passe r�alis�e.') );
										$ok=TRUE;
										ajout_log( $id, traduire('Changement de Mot de passe') );
										$action="";
										}
									else 
										erreur (traduire("Le mot de passe de ce compte n'est pas modifiable"));
									}
								else 
									erreur (traduire("Le mot de passe n'est pas assez complexe (utiliser des Majuscules, Chiffres, caract�res sp�ciaux)"));
								}
							else 
								erreur (traduire("Le mot de passe doit �tre diff�rent de l'identifiant."));
							}
						else 
							erreur (traduire("Le mot de passe est trop court (au moins 8 caract�res)."));
						}
					else 
						erreur (traduire("Les 2 mots de passe ne sont pas identiques."));
					}
				else 
					{
					erreur(traduire("Mot de passe incorrect"));
					ajout_log( $id, traduire('Changement de Mot de passe: ancien MdP incorrect') );
					}
				}
			else 
				erreur(traduire("Identifiant incorrect."));
				
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
			$reponse = command("SELECT * from  r_user WHERE idx='$idx'"); 
			if ($donnees = fetch_command($reponse))
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
					echo "<p><br><p>".traduire('Pour finaliser la cr�ation de votre compte, merci de compl�ter les informations ci-dessous :');
					formulaire ("maj_user");
					echo "<TABLE><TR> <td></td>";
					echo "<tr><td> ".traduire('Identifiant').": </td><td><input type=\"texte\" name=\"id\"   value=\"\"> </td>";
					echo " <td>".traduire('Au minimum 8 caract�res')." </td>";
					echo "<tr><td> ".traduire('Mot de passe').": </td><td><input type=\"password\" id=\"pwd\" name=\"pw\"   value=\"\"> </td> " ;
					echo " <td>".traduire('Au minimum 8 caract�res, au moins <br>une majuscule, une minuscule et un chiffre <br>(caract�res sp�ciaux possible)')." </td>";
					echo "<tr> <td></td><td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> Voir saisie<td>";
					echo "<input type=\"hidden\" name=\"idx\"  value=\"$idx\"> " ;
					echo "<tr><td> ".traduire('Nom').": </td><td> <input type=\"texte\" name=\"nom\"   size=\"20\" value=\"$user_nom\"> </td>" ;
					echo "<tr><td> ".traduire('Pr�nom').": </td><td> <input type=\"texte\" name=\"prenom\"   size=\"20\" value=\"$user_prenom\"> </td>" ;
//					echo "<tr><td> ".traduire('T�l�phone').": </td><td><input type=\"texte\" name=\"telephone\"   size=\"15\" value=\"$user_telephone\"> </td>" ;
//					echo "<tr><td> ".traduire('Mail').": </td><td> <input type=\"texte\" name=\"mail\"   size=\"30\" value=\"$user_mail\"> </td>" ;
					echo "<td><input type=\"submit\" id=\"maj_user\" value=\"".traduire('Valider')."\" > </td> ";
					echo "</form> </table>";
					fin_cadre();
					pied_de_page("x");
					}
				else
					erreur(traduire("Compte d�j� initialis�."));
				}
			else
				erreur(traduire("Action interdite."));
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

			$reponse = command(sprintf("SELECT * from  r_user WHERE droit='' and nom='%s' and prenom='%s' and prenom_p='%s' and prenom_m='%s' and anniv='%s' and ville_nat='%s'",
																				$nom,       $prenom,          $prenom_p, 		$prenom_m, 			$anniv,      $ville_natale)	); 
			if ($donnees = fetch_command($reponse))
				{
				aff_logo();
				$id=$donnees["id"];
				echo "<br><br><br>".traduire('Rappel : votre identifiant est')." '<strong>$id<strong>'";
				$action="envoi_mdp2";
				}
			else
				{
				erreur (traduire("Les informations communiqu�es ne permettent pas de retrouver votre identifiant").".<p>");
				$action="dde_identifiant";
				}
			}			

		if ($action=="dde_identifiant")
			{
			aff_logo();
			echo "<p>".traduire('Pour r�cup�rer votre identifiant').": <p>";
			echo "<p>".traduire('Si vous �tes un acteur social, merci de contacter votre responsable.')." </a><p>";
			echo "".traduire('Si vous �tes un responsable , merci de contacter l\'administrateur de ce site (voir lien "Nous contacter" ci-dessous)')."<p>";
			debut_cadre();
			echo "<br>".traduire('Si vous �tes un b�n�ficiaire, merci de compl�ter ce formulaire')." : <p>";
			echo "<TABLE><TR>";
			formulaire ("traite_dde_identifiant");
			echo "<tr><td> ".traduire('Nom').":</td><td><input ctype=\"text\" size=\"20\" name=\"nom\" value=\"".variable("nom")."\"/></td>";
			echo "<tr><td> ".traduire('Pr�nom').": </td><td><input  type=\"text\" size=\"20\" name=\"prenom\" value=\"".variable("prenom")."\"/></td>";
			echo "<tr><td> ".traduire('Date de naissance')." :  </td><td><input  type=\"text\" size=\"20\" name=\"anniv\" value=\"".variable("anniv")."\"/></td><td>".traduire('jj/mm/aaaa')."</td>";
			echo "<tr><td> ".traduire('Ville natale').":   </td><td><input  type=\"text\" size=\"20\" name=\"ville_natale\" value=\"".variable("ville_natale")."\"/></td>";
			echo "<tr><td> ".traduire('Pr�nom de votre m�re').": </td><td><input  type=\"text\" size=\"20\" name=\"prenom_m\" value=\"".variable("prenom_m")."\"/></td>";
			echo "<tr><td> ".traduire('Pr�nom de votre p�re').": </td><td><input  type=\"text\" size=\"20\" name=\"prenom_p\" value=\"".variable("prenom_p")."\"/></td>";
			echo "<tr> <td></td><td><input type=\"submit\"  id=\"traite_dde_identifiant\"  value=\"".traduire('Valider la demande')."\"><p></td>";
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

//			$reponse = command(        "SELECT * from  r_user WHERE droit='' and nom='$nom' and prenom='$prenom' and prenom_p='$prenom_p' and prenom_m='$prenom_m' and anniv='$anniv'and ville_nat='$ville_natale'"); 
			$reponse = command(sprintf("SELECT * from  r_user WHERE droit='' and nom='%s' and prenom='%s' and prenom_p='%s' and prenom_m='%s' and anniv='%s' and ville_nat='%s'",
																				$nom,       $prenom,          $prenom_p, 		$prenom_m, 			$anniv,      $ville_natale)	); 
			if ($donnees = fetch_command($reponse))
				{
				$idx=$donnees["idx"];
				$id=$donnees["id"];
				$mdp_ancien=$donnees["pw"];
				$mdp=variable('pwd');
				$action="dde_mdp_avec_code";	// on consid�re par d�faut 		
				$date_jour=date('Y-m-d');
				$r1 =command("select * from r_dde_acces where type='' and user=$idx  and date_dde>='$date_jour' ");
				If ($d1 = fetch_command($r1) )
					{
					echo $code."-".$d1["code"];
					If ($code==$d1["code"])
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
												command("UPDATE r_user set lecture='".encrypt($mdp)."' where id='$id'");

											$mdp=encrypt($mdp);
											command("UPDATE r_user set pw='$mdp' where id='$id'");
											echo "<p><br><p>".traduire('Modification du mot de passe r�alis�e.')."<p><br><p>";
											$ok=TRUE;
											ajout_log( $idx, traduire("R�initialisation Mot de passe avec code de d�verrouillage")." $code" );
											$action="";
											}
										else 
											erreur (traduire("Le mot de passe de ce compte n'est pas modifiable"));
										}
									else 
										erreur (traduire("Le mot de passe n'est pas assez complexe (utiliser des Majuscules, Chiffres, caract�res sp�ciaux)"));
									}
								else 
									erreur (traduire( "Le mot de passe doit �tre diff�rent de l'identifiant."));
								}
							else 
								erreur (traduire("Le mot de passe est trop court (au moins 8 caract�res)."));
							}
						else 
							erreur (traduire("Code de d�verrouillage incorrect."));		
						}
					else
					erreur (traduire("Code de d�verrouillage non existant ou trop ancien"));	
					}
				else
					{
					erreur (traduire("Les informations communiqu�es ne permettent pas de traiter votre demande."));
					// Ajout log � faire
					}
			if ($action!="")
				aff_logo();
			}		

		
		if ($action=="dde_code_par_sms")
			{
			aff_logo();
			$code=rand(100000,999999);
			$date_jour=date('Y-m-d');
			$telephone=variable_get("telephone");
			$reponse =command("SELECT * from  r_user WHERE telephone='$telephone' "); 
			if (($donnees = fetch_command($reponse)) && (strlen($telephone)==10 ))
				{
				$idx=$donnees["idx"];

				$reponse =command("SELECT * from  r_dde_acces WHERE user='$idx'  and date_dde='$date_jour' "); 
				if ($donnees = fetch_command($reponse))
					{
					echo "<br><p><strong>".traduire('Un SMS ou mail a d�j� �t� envoy� aujourd\'hui.')." </strong> <p><br>";
					}
				else
					{
					$reponse =command("INSERT INTO `r_dde_acces`  VALUES ('$idx' , '$code', '$date_jour', '$idx', '$idx', '', '' ) ");
					envoi_SMS($telephone , "Code de d�verrouillage : $code ");
					echo "<br><p><strong>".traduire('Vous receverez dans quelques minutes un SMS avec le code de d�verrouillage.')."</strong> ";
					}
				$action="dde_mdp_avec_code2";
				}
			else
				{
				erreur(traduire("Numero inconnu"));
				pied_de_page("x");
				}
			}
			
		if ($action=="dde_code_par_mail")
			{
			aff_logo();
			$code=rand(100000,999999);
			$date_jour=date('Y-m-d');
			$mail=variable_get("mail");
			$reponse =command("SELECT * from  r_user WHERE mail='$mail' "); 
			if (($donnees = fetch_command($reponse)) && (VerifierAdresseMail($mail) ) )
				{
				$idx=$donnees["idx"];

				$reponse =command("SELECT * from  r_dde_acces WHERE user='$idx'  and date_dde='$date_jour' "); 
				if ($donnees = fetch_command($reponse))
					{
					echo "<br><p><strong>".traduire('Un SMS ou mail a d�j� �t� envoy� aujourd\'hui.')." </strong> <p><br>";
					}
				else
					{
					$reponse =command("INSERT INTO `r_dde_acces`  VALUES ('$idx' , '$code', '$date_jour', '$idx', '$idx', '', '' ) ");
					
					envoi_mail($mail,traduire("Code de d�verrouillage"),traduire("Suite � votre demande, votre code de d�verrouillage est")." : '$code' ");
					echo "<br><p><strong>".traduire('Vous receverez dans quelques minutes un mail avec le code de d�verrouillage.')."</strong> <p><br>";
					}
				$action="dde_mdp_avec_code2";
				}
			else
				{
				erreur(traduire("Numero inconnu"));
				pied_de_page("x");
				}
			}			
			
		if ( ($action=="dde_mdp_avec_code") || ($action=="dde_mdp_avec_code2"))
			{
			if ($action=="dde_mdp_avec_code")
				aff_logo();
			
			debut_cadre("700");
			echo "<p><br><p>".traduire('Pour r�cup�rer votre mot de passe, merci de compl�ter ce formulaire avec les informations saisies lors de la cr�ation du compte')." : <p>";
			echo "<TABLE><TR> ";
			formulaire ("valider_dde_mdp_avec_code");
			echo "<tr><td> ".traduire('Nom').":</td><td><input ctype=\"text\" size=\"20\" name=\"nom\" value=\"".variable("nom")."\"/></td>";
			echo "<tr><td> ".traduire('Pr�nom').":  </td><td><input  type=\"text\" size=\"20\" name=\"prenom\" value=\"".variable("prenom")."\"/></td>";
			echo "<tr><td> ".traduire('Date de naissance')." : </td><td><input  type=\"text\" size=\"20\" name=\"anniv\" value=\"".variable("anniv")."\"/></td><td>".traduire('jj/mm/aaaa')."</td>";
			echo "<tr><td> ".traduire('Ville natale').":  </td><td><input  type=\"text\" size=\"20\" name=\"ville_natale\" value=\"".variable("ville_natale")."\"/></td>";
			echo "<tr><td> ".traduire('Pr�nom de votre m�re').": </td><td><input  type=\"text\" size=\"20\" name=\"prenom_m\" value=\"".variable("prenom_m")."\"/></td>";
			echo "<tr><td> ".traduire('Pr�nom de votre p�re').": </td><td><input  type=\"text\" size=\"20\" name=\"prenom_p\" value=\"".variable("prenom_p")."\"/></td>";
			echo "<tr><td> ".traduire('Code d�verrouillage').": </td><td><input  type=\"password\" id=\"pwd1\" size=\"10\" name=\"code\" value=\"".variable("code")."\"/></td>";
			echo "<td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> ".traduire('Voir saisie')."<td>";
			echo "<tr><td> ".traduire('Nouveau mot de passe').":</td><td><input  type=\"password\" id=\"pwd\" size=\"12\" name=\"pwd\" value=\"".variable("pwd")."\"/></td>";
			echo "<td>".traduire('Au minimum 8 caract�res, au moins <br>une majuscule,une minuscule et un chiffre <br>(caract�res sp�ciaux possible)')." </td>";
			echo "<tr> <td></td><td><input type=\"submit\"  id=\"valider_dde_mdp_avec_code\"  value=\"".traduire('Valider la demande')."\"><p></td>";
			echo "</form> </table>";
			fin_cadre();
			pied_de_page("x");
			}	


		if (($action=="envoi_mdp") || ($action=="envoi_mdp2") )
			{
			if ($action=="envoi_mdp") // pour envoi_mp2 la variable $id est d�ja positionn� par demande identifiant
				{
				$id=variable('id');
				aff_logo();
				}
			echo "<center>";	
			$reponse = command(sprintf("SELECT * from  r_user WHERE (id='%s' or mail='%s' or telephone='%s')",$id,$id,$id) ); 
			if ($donnees = fetch_command($reponse))
				{
				if (!fetch_command($reponse)) // v�rifiction qu'il est unique
					{
					$mail=$donnees["mail"];
					$telephone=$donnees["telephone"];
					$droit=$donnees["droit"];
					$idx1=$donnees["idx"];
					$id1=$donnees["id"];
					$date_jour=date('Y-m-d');				
					
					if ( ( ( VerifierPortable($telephone)) || (VerifierAdresseMail($mail) ) ) && ($droit=="") )
						{
						echo traduire('Pour recevoir votre code de d�verrouillage : ');
						echo "<p> <table border=\"2\" ><tr> <td> <center>";
						echo "<img src=\"images/sms.png\" width=\"35\" height=\"35\" >".traduire('Soit directement en cliquant sur un choix ci-dessous')." : ";

						// si t�l�phone portable valide alors on propose de l'envoyer par SMS 
						if ( VerifierPortable($telephone))
							{
							$tel_tronque= $telephone;
							$tel_tronque[3]='.';
							$tel_tronque[4]='.';
							$tel_tronque[5]='.';
							echo "<p><a  id=\"sms\" href=\"index.php?".token_ref("dde_code_par_sms")."&telephone=".encrypt($telephone)."\"> par SMS au <strong>$tel_tronque</strong></a>";
							}
							
						if (VerifierAdresseMail($mail) )
							{
							$mail_tronque= $mail;
							for ($i=5; $i<strlen($mail)-10; $i++)
								$mail_tronque[$i]='.';
							echo "<p><a  id=\"mail\" href=\"index.php?".token_ref("dde_code_par_mail")."&mail=".encrypt($mail)."\"> Par mail � l'adresse <strong>'$mail_tronque' </strong></a>";
							}
						echo "</td></table>" ;
						echo "<p>".traduire('Soit en contactant un r�f�rent de confiance.');
						}

					
					if (( (strtolower($id1)==strtolower($id)) || (strtolower($mail)==strtolower($id))|| ($telephone==$id)) && ($droit!="")&& ($droit!="s")&& ($droit!="m")) // cas des AS et responsables
						{
						$id=$donnees["id"];
						$idx=$donnees["idx"];
						$code=rand(100000,999999);
			
						$reponse =command("select * from r_dde_acces where user='$idx1' and ddeur='$idx1' and type='' and  date_dde='$date_jour'  ");
						if (! ($donnees = fetch_command($reponse) )  )
							{
							if (compte_non_protege($id))
								{
								$sauve_lang=$user_lang;
								$reponse =command("INSERT INTO `r_dde_acces`  VALUES ('$idx' , '$code', '$date_jour', '$idx', '', '', '' ) ");
								$user_lang='fr'; // Attention : A initialiser quand le user aura m�moris� 
								
								$synth =traduire("Pour r�initialiser votre mot de passe, cliquez")." <a  id=\"lien\"  href=\"".serveur."index.php?".token_ref("reinit_mdp")."&code=".encrypt($code)."\">".traduire("ici")."</a> .";
								$synth .="<br><br>".traduire("Si le lien ne fonctionne pas, recopiez dans votre navigateur internet cette adresse : ")."<br><strong>".serveur."index.php?".token_ref("reinit_mdp")."&code=".encrypt($code);
								$synth .="</strong><p><br> ".traduire("Si vous n'�tes pas � l'origine de cette demande, cliquez")." <a  id=\"alerte\"  href=\"".serveur."index.php?".token_ref("alerte_admin")."&motif=".encrypt("reinit_mdp avec $code")."\">".traduire("ici")."</a> .";
								$dest = "$mail";
								$user_lang=$sauve_lang;
								echo "<p><br><p>".traduire("Un mail contenant un lien, valable uniquement aujourd'hui,<p> permettant de r�initialiser votre mot de passe a �t� envoy� �")." $mail. ";
								envoi_mail( $dest , traduire ("Information pour")." $id", "$synth" );		
								ajout_log( $id, traduire("Mail pour reinitialisation envoy� � l'adresse")." $mail" );
								}
							else
								erreur(traduire("D�sol� compte prot�g�"));
							}
						else
							echo "<p><br><p><br><p>".traduire('Vous avez demand� la r�initialisation de votre mot de passe mais un mail vous a d�j� �t� envoy� aujourd\'hui.')." <p><br><p><br>"; 
						}
					else
						{
						$reponse =command("select * from r_dde_acces where user='$idx1' and user<>autorise and type='' and  date_dde>='$date_jour'  ");
						if (! ($donnees = fetch_command($reponse) )  )
							{
							echo "<p><br>".traduire('Cliquez sur')." <img src=\"images/contact.png\" width=\"25\" height=\"25\" > ".traduire('correspondant � la personne que vous allez contacter pour vous aider � r�cup�rer votre mot de passe. ');

							echo traduire('Apr�s avoir cliqu� sur le lien, vous aurez 5 jours pour la contacter en personne. Elle s\'assurera de votre identit�. ');
							echo traduire('Elle vous communiquera alors le code de d�verouillage (mais elle ne connaitra pas votre mot de passe). ');
							titre_referent("","x");
							$reponse =command("select * from  r_referent where user='$idx1' ");
							while ($donnees = fetch_command($reponse) ) 
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
									$r1 =command("select * from  r_user where organisme='$organisme' and droit='S' ");
									while ($d1 = fetch_command($r1) ) 
										{
										$idx=$d1["idx"];
										$nom=$donnees["nom"];
										visu_referent_user($idx,$idx1);
										}								
									
									}
								}
							echo "</table></div>";					
							}
						else // cas o� il y  d�j� une demande en cours
							{
							echo "<p><br>Il y a dej� une demande en cours aupr�s de ";
							$ddeur=$donnees["ddeur"];
							titre_referent("","x");
							$reponse =command("select * from  r_referent where user='$idx1' and nom='$ddeur' ");
							while ($donnees = fetch_command($reponse) ) 
								{
								$organisme=stripcslashes($donnees["organisme"]);
								$idx=$donnees["idx"];
								if ($organisme!="")
									visu_referent($idx,$idx1,"x");
								}
							echo "</table></div>";						
							echo "<p><br><p> ".traduire('Contacter cette personne, elle s\'assurera de votre identit�.');
							echo "<br> ".traduire('Puis, elle vous communiquera votre code de d�verouillage (mais elle ne connaitra pas votre mot de passe). ');
							echo "<p>".traduire('Si vous n\'arrivez pas � la joindre et que vous voulez contacter un autre r�f�rent de confiance,');
							echo "<br>".traduire('attendez que le d�lai initial soit �coul� pour faire une nouvelle demande. ');
							echo "<br><p><br><p><a href=\"index.php?".token_ref("dde_mdp_avec_code")."\"> <img src=\"images/code.png\" width=\"35\" height=\"35\" >".traduire('Si vous avez d�j� reccueilli le code de d�verrouillage, cliquez ici')."</a><p><br><p><br></center>";
						
							}
						}
					pied_de_page("x");
					}
				else
					{
					erreur( traduire("Plusieurs comptes correspondent � cette r�f�rence")); 
					$action="dde_mdp2";
					}
				}
			else
				{
				erreur( traduire("Identifiant ou mail inconnu")); 
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
			echo "<TR> <td><center>".traduire('Si vous avez oubli� votre mot de passe, saisissez votre identifiant,<p> votre adresse mail ou num�ro de t�l�phone.')."</td>";
			echo "<TR> <td><center><input class=\"center\" type=\"text\" size=\"30\" name=\"id\" value=\"$id\"/>";
			echo " <input type=\"submit\"  id=\"envoi_mdp\"  value=\"".traduire('Valider')."\"><p></td>";
			echo "</form> </table>";
			fin_cadre();
			echo "<br><p><a href=\"index.php?".token_ref("dde_identifiant")."\" > <img src=\"images/identifiant.png\" width=\"35\" height=\"35\" >".traduire('Si vous avez oubli� votre identifiant, cliquez ici.')." </a>";
			echo "<br><p><br><p><a href=\"index.php?".token_ref("dde_mdp_avec_code")."\"> <img src=\"images/code.png\" width=\"35\" height=\"35\">".traduire('Si vous avez d�j� reccueilli le code de d�verrouillage, cliquez ici')."</a><p><br><p><br></center>";
			pied_de_page("x");
			}	

		if ($action=="liste") 
			{
			aff_logo();
			echo "<p>Liste des structures d�ployant 'doc-depot' : " ;
			titre_organisme();
			
			$reponse =command("select * from  r_organisme where convention<>'2' order by organisme asc");
			while ($donnees = fetch_command($reponse) ) 
				{
				$adresse=stripcslashes($donnees["adresse"]);
				$organisme=stripcslashes($donnees["organisme"]);		
				$tel=$donnees["tel"];	
				$mail=$donnees["mail"];	
				$sigle=stripcslashes($donnees["sigle"]);	
				echo "<tr><td> $organisme </td><td> $sigle </td><td> $adresse   </td><td> $tel </td><td> $mail</td>";
				}
			echo "</table></div>";				
			pied_de_page("");
			}	

// ------------------------------------------------------------------------------ FIN des actions sans identification (pas de mot de passe)		

$_SESSION['ad']=false;	

require_once 'cx.php';
	
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
	if ($action=="detail_user2")
		{
		if (is_numeric(variable_get("user")))
			{
			$_SESSION['bene']=variable_get("user");	
			$action="detail_user";
			}
		else
			$action="";	
		}

	if ($action=="")
		{
		$_SESSION['bene']=$user_idx;
		$_SESSION['user_idx']=$user_idx;
		}		

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

	if ( ($action=="user_actif_P") && ($user_droit=="R"))
		maj_droit(variable("idx"),"P");
		
	if (($action=="user_inactif_P") && ($user_droit=="R"))
		maj_droit(variable("idx"),"p");
		
	if ( ($action=="user_actif_M") && ($user_droit=="R"))
		maj_droit(variable("idx"),"M");

	if ( ($action=="user_inactif_M") && ($user_droit=="R"))
		maj_droit(variable("idx"),"m");		
		
	if ($action=="supp_upload")
		{
		$num=variable("num");
	
		$reponse =command("select * from r_attachement where  num='$num' ");
		if ($donnees = fetch_command($reponse) )
				{
				$type=$donnees["type"];		
				
				supp_attachement ($num);
				$num = substr($num,strpos($num,".")+1 );
				if ($type=="A")
					ajout_log( $user_idx, traduire("Suppression du fichier")." '$num' (".traduire('Espace partag�').")" ,$_SESSION['user']);	
				else
					ajout_log( $user_idx, traduire("Suppression du fichier")." '$num' (".traduire('Espace personnel').")" ,$_SESSION['user']);	
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
				command("delete from r_sms where idx='$user_idx' ");
				$_SESSION['pass']=false;	
				echo "<hr><p><br><p>";
				msg_ok(traduire("Suppression de compte r�alis�e!"));
				pied_de_page("x");
				}
			else
				erreur (traduire("Suppression impossible car compte prot�g�").". ");
			}
		else
			{
			erreur (traduire("Mot de passe incorrect"));
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
		nouveau_organisme(variable("organisme"), variable("tel"),variable("mail"),variable("adresse"),variable("sigle"),variable("doc"),variable("fuseau"));

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
		$idx1= nouveau_user(variable("id"),variable("pw"),variable("droit"),variable("mail"),variable("organisme"),variable("nom"),variable("prenom"),mef_date(variable("anniv")),variable("telephone"),variable("nationalite"),variable("ville_nat"),variable("adresse"),variable("recept_mail"),variable("prenom_p"),variable("prenom_m"),variable("code_lecture"),variable("nss"),variable("type_user"));
		if ( ($idx1!="") && (variable("droit")=="") )
			// par d�faut on impose le cr�ateur comme r�f�rent de  confiance
			nouveau_referent($idx1 ,$user_organisme, "Tous", "", "","","");

		if ($idx1=="")
			{
			if ($user_droit=="S") 
				$action="ajout_beneficiaire";
			else
				$action="ajout_user";			
			}
			else
				{
				msg_ok(traduire("Compte cr�� avec succ�s"));
				$action="";	
				}
		}				
				
	if (($action=="modif_user") && (($user_droit=="R") || ($user_droit=="A") ) )
		{
		modification_user(variable("idx"),variable("nom"),variable("prenom"),variable("telephone"),variable("mail"),variable("droit"),variable("organisme"));
		$action="";
		}		
		
	if ( ($action=="supp_user") &&(  ($user_droit=="A") || ($user_droit=="R") ) )
		supp_user(variable("idx"));
	


	if (($action=="modif_profil") && ( ($user_droit_org=="R") || ($user_droit_org=="A") ) )
		{
		$_SESSION['droit']=variable("profil");
		$action="";
		}

	if ($action=="modif_mdp")
		{
		aff_logo();
		
		debut_cadre("500");
		echo "<p><br>".traduire('Modification de votre mot de passe');
		echo "<p>".traduire('Au minimum 8 caract�res, au moins une majuscule, une minuscule et un chiffre (caract�res sp�ciaux possible)');
		
		echo "<TABLE><TR><td>";
		formulaire ("changer_mdp");
		if (variable('ancien')=="123456")
			{
			echo "".traduire('Identifiant').": </td><td><input class=\"center\" type=\"text\" name=\"id\" value=\"\"/>	";
			echo "<input  type=\"hidden\" name=\"ancien\" value=\"123456\"/></td>";
			}
		else
			{
			echo " <input  type=\"hidden\" name=\"idx\" value=\"$idx\"/>";
			echo "<TR> <td>".traduire('Ancien').": </td><td><input class=\"center\" id=\"pwd1\" type=\"password\" name=\"ancien\" value=\"\"/></td>";
			}
		echo "<TR> <td>".traduire('Nouveau').":</td><td><input class=\"center\" id=\"pwd2\" type=\"password\" name=\"n1\" value=\"\"/></td>";
		echo "<td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' ; ; document.getElementById('pwd2').type = this.checked ? 'text' : 'password'\"> ".traduire('Voir saisie')."<td>";

		echo "<TR> <td>".traduire('Confirmation').":</td><td><input class=\"center\" id=\"pwd\" type=\"password\" name=\"n2\" value=\"\"/></td>";
		echo "<TR>  <td></td><td><input type=\"submit\"  id=\"changer_mdp\"  value=\"".traduire('Modifier')."\"/><br><p></td>";
		echo "</form> </table> ";
		fin_cadre();
		pied_de_page("x");
		}	
				
	if(($action=="init_selenium") && ($user_droit=="A"))
		{
		echo "Init Selenium:";
		
		command("delete from r_organisme where organisme REGEXP 'SELENIUM' ");
		
		$reponse =command("select * from  r_user where nom REGEXP 'SELENIUM' or id REGEXP 'SELENIUM'  ");		
		while ($donnees = fetch_command($reponse) ) 
			{	
			$idx= $donnees["idx"];
			command("delete from r_lien where user='$idx' ");
			command("delete from r_dde_acces where user='$idx' or  ddeur='$idx' ");
			command("delete from r_referent where user='$idx' or  idx='$idx' ");
			command("delete from r_sms where idx='$idx' ");
			command("delete from log where user='$idx' or  acteur='$idx'  ");
			supp_tous_fichiers($idx);
			command("delete from r_user where idx='$idx'  ");

			echo "<br>User $idx  Ok.";
			}
		
		command("trunc table log ");
		command("trunc table z_log_t  ");
		command("drop table zz_selenium  ");	
		command("delete from fct_fissa where support='zz_selenium' ");
		echo "<br>Purge tables Ok.";
		
		echo "<p>ok.";
		exit();
		}
	

		// ===================================================================== Bloc IMAGE
		$reponse = command("SELECT * from  r_user WHERE idx='$idx'"); 
		$donnees = fetch_command($reponse);
		$user_idx=$donnees["idx"];
		$id=$donnees["id"];
		$user_nom=$donnees["nom"];
		$user_prenom=$donnees["prenom"];
		$user_droit_org=$donnees["droit"];
		$user_type_user=$donnees["type_user"];
		
		if (!isset($_SESSION['droit']))
			$user_droit=$donnees["droit"];
		else
			$user_droit=$_SESSION['droit'];
		$user_anniv=$donnees["anniv"];
		$user_telephone=$donnees["telephone"];
		$user_mail=$donnees["mail"];
		$user_nationalite=$donnees["nationalite"];
		$user_ville_nat=$donnees["ville_nat"];
		$user_adresse=stripcslashes($donnees["adresse"]);
		$user_organisme=stripcslashes($donnees["organisme"]);	
		$code_lecture=$donnees["lecture"];	
		$user_lecture=$donnees["lecture"];
		$user_lang=$donnees["langue"];		
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
			echo "<td>".traduire('Profil')." :</td><td>";
			if (!isset($_SESSION['droit']))
				liste_profil($user_droit_org,$user_droit_org);
			else
				liste_profil($user_droit_org,$_SESSION['droit']);
			echo "</td>";

			}
		echo "<td>";

		if (($donnees["droit"]=="s") || ($donnees["droit"]=="p") )
			echo "- ( <img src=\"images/inactif.png\"width=\"20\" height=\"20\"> ".traduire('Compte inactif')." ) ";
		if($user_droit=="")
			echo "- ( $user_anniv ) ";
		if (isset ($_SESSION['chgt_user']) && ($_SESSION['chgt_user']==true) )
			msg_ok("Lecture seule");
		echo "<td> <ul id=\"menu-bar\">";
		echo "<li><a href=\"index.php?".token_ref("dx")."\"  > ".traduire('D�connexion')."</a>";
		echo "<ul >";
		echo "<li><a href=\"index.php?".token_ref("modif_mdp")."\"  >".traduire('Modification mot de passe')."</a></li>";
		echo "<li><a href=\"index.php?".token_ref("histo")."\"  > ".traduire('Historique')." </a></li>";
		if ($user_droit=="")
			{
			echo "<li><a href=\"index.php?".token_ref("supp_compte_a_confirmer")."\"> ".traduire('Suppression compte')." </a></li>";
			echo "<li><a href=\"index.php?".token_ref("exporter_a_confirmer")."\"> ".traduire('Tout archiver')." </a></li>";
			}		
			
		if ($user_droit=="")
			echo " <li><a href=\"aide_b.html\" target=_blank > ".traduire('Aide')."</a></li>";
		if ($user_droit=="S")
			echo "<li><a href=\"aide_as.html\" target=_blank > ".traduire('Aide')."</a></li>";
		if ($user_droit=="R")
			echo "<li><a href=\"aide_r.html\" target=_blank > ".traduire('Aide')."</a></li>";	
			
//		echo "<ul >";
//		echo "<li><a href=\"index.php?action=faq\"  > Questions fr�quentes </a></li></ul>";
		echo "</ul></li>";
		if (isset($ligne_last_cx)) 
			if ($ligne_last_cx!="")
				echo "<br>$ligne_last_cx";

		echo "</td></table></td>";
		
		echo "<tr><td><hr>";
	
		//    ?????     etrange que ce code soi au milieu de cette zone  ????  ==>> devrait �tre AVANT
		if ($action=="modif_tel")
			{
			if (modif_tel(variable("idx"), variable("telephone"),variable("telephone2")) )
				{
				$reponse = command("SELECT * from  r_user WHERE idx='$idx'"); 
				$donnees = fetch_command($reponse);
				$user_telephone=$donnees["telephone"];
				$user_mail=$donnees["mail"];
				}
			}
		
		echo "</td><tr> ";

		if (($action=="") || ($action=="modif_tel")|| ($action=="modif_domicile"))
			{
			formulaire ("modif_tel");
			echo "<input type=\"hidden\" name=\"idx\" value=\"$idx\"> " ;
			if ($user_droit=="")
				{
				echo "<td> <img src=\"images/telephone.png\" width=\"25\" height=\"25\" > ".traduire('Tel')." :<input type=\"texte\" name=\"telephone\"   size=\"15\" value=\"$user_telephone\" onChange=\"this.form.submit();\"> " ;
				echo " - <img src=\"images/mail.png\" width=\"25\" height=\"25\" > ".traduire('Mail')." :<input type=\"texte\" name=\"telephone2\"   size=\"30\" value=\"$user_mail\" onChange=\"this.form.submit();\"> </form> </td>" ;
				
				if ((mail_valide_surv($user_mail)) && (parametre("DD_surv_mail")=="oui") )
					lien_c ("images/voir.png", "surv_mail", param("idx","$idx" ) , traduire("Surveillance de l'arriv�e mails adiministratifs"));
				}
			else
				{
				echo "<td> <img src=\"images/telephone.png\" width=\"25\" height=\"25\" > ".traduire('Tel pro')." :<input type=\"texte\" name=\"telephone\"   size=\"15\" value=\"$user_telephone\" onChange=\"this.form.submit();\"> " ;
				echo " - <img src=\"images/mail.png\" width=\"25\" height=\"25\" > ".traduire('Mail pro')." : <input type=\"texte\" name=\"telephone2\"   size=\"30\" value=\"$user_mail\" onChange=\"this.form.submit();\"> </form></td>" ;
				}		
			echo "</table> </td>";
			}
		else
			{
			if ($user_droit=="")
				{
				echo "<td> <img src=\"images/telephone.png\" width=\"25\" height=\"25\" > ".traduire('Tel').": $user_telephone " ;
				echo " - <img src=\"images/mail.png\" width=\"25\" height=\"25\" > ".traduire('Mail').": $user_mail </form></td>" ;
				if ((mail_valide_surv($user_mail)) && (parametre("DD_surv_mail")=="oui") )
					lien_c ("images/voir.png", "surv_mail", param("idx","$idx" ) , traduire("Surveillance de l'arriv�e mails adiministratifs"));
				}
			else
				{
				echo "<td> <img src=\"images/telephone.png\" width=\"25\" height=\"25\" > ".traduire('Tel pro').": $user_telephone " ;
				echo " - <img src=\"images/mail.png\" width=\"25\" height=\"25\" > ".traduire('Mail pro').": $user_mail" ;
				}		
			echo "</td></table> </td>";
			}

		echo "</td>";
		echo "<td><center> ";		
		
		if ( ($user_droit=="S") || ($user_droit=="R") || ($user_droit=="P") )  // acc�s uniqumnt aux AS et responsables
			{
			$r1 =command("select * from  r_organisme where idx='$user_organisme' ");
			$d1 = fetch_command($r1);
			$logo=$d1["logo"];
			$_SESSION['logo']=$logo;
		

				//* ----------------------------------FISSA
			$reponse = command("SELECT * from  fct_fissa WHERE organisme='$user_organisme'"); 
			if ($donnees = fetch_command($reponse))
				{
				$_SESSION['support']=$donnees["support"];
				echo "<a id=\"fissa\" href=\"fissa.php\"><img src=\"images/fissa.jpg\" width=\"70\" height=\"50\"></a>";
				echo "<a href=\"suivi.php\"><img src=\"images/suivi.jpg\" width=\"70\" height=\"50\"><a>";					
				if ($user_droit!="P") 
					{
					echo "<a href=\"rdv.php\"><img src=\"images/rdv.jpg\" width=\"70\" height=\"50\"><a>";		
					}					
				}
			/*
			//* ---------------------------------- Calendrier
			$jfissa=0;
			$reponse = command("SELECT * from  fct_calendrier WHERE organisme='$user_organisme'"); 
			if ($donnees = fetch_command($reponse))
				echo "<a href=\"index.php?action=cc_activite\"><img src=\"images/calendrier.jpg\" width=\"70\" height=\"50\"></a>";
				*/
			}
			
		// echo "<a title=\"Alerte Grand Froid/Forte Pluie\" href=\"alerte.php\"><img src=\"images/logo-alerte.jpg\" width=\"70\" height=\"50\"></a> ";
		
		if ($user_droit!="")
			{
			// si existe lien vers suivi	
			if ((isset($_SESSION['support'])) && (isset( $_SESSION['user_idx'])) )
				{
				$bdd= $_SESSION['support'];
				$uidx_fissa= $_SESSION['user_idx'];
				$reponse = command("SELECT * FROM $bdd WHERE date='0000-00-00' and pres_repas='' and activites='$uidx_fissa'  "); 
				if ($donnees = fetch_command($reponse))
					{
					$nom_slash=$donnees["nom"];
					echo "<br><a href=\"suivi.php?".token_ref("suivi")."&nom=$nom_slash\">Acc�s au Suivi individuel de $nom_slash</a>";
					}

				}		
			}
		
		echo "</center></td>";			
		if ((($user_droit=="S") || ($user_droit=="R") ) && (est_image($logo) ) )
			echo "<td> <img src=\"images/$logo\" width=\"200\" height=\"100\"  ></td> ";
			
		//-----------------------------------*/ 		
	
	
		echo "</table>";

			
		if ($user_droit=="P")
			{
			echo "<hr><p><br><p><br><p><br><p><br><p><br><p><br><p><br><p><br><p><br><p><br><p><br><p><br><p><br>";
			pied_de_page();
			}	
		
		if ($user_droit=="")	 
			// on n'affiche au b�n�ficiaire sa domiciliation que sur l'�cran d'accueil 
			if (($action=="") || ($action=="modif_tel")|| ($action=="modif_domicile"))
				{
				echo "<table><tr><td> <img src=\"images/maison.png\" width=\"25\" height=\"25\" >  ".traduire('Domiciliation postale')." : </td>";
				formulaire ("modif_domicile");
				liste_organisme($user_organisme,"1");
				
				$user_adresse=($user_adresse);
				if ($user_droit=="")
					{
					echo "<tr><td> <img src=\"images/enveloppe.png\" width=\"25\" height=\"25\" >  ".traduire('Adresse postale')." :</td>" ;
					
					if  ($user_organisme=="") 
						echo "<td> <input type=\"texte\" name=\"adresse\" id=\"ReversBE\" onfocus=\"javascript:if(this.value=='ReversBE')this.value='';\"   size=\"80\" value=\"$user_adresse\" onChange=\"this.form.submit();\"> " ;
					else
						echo "<td> $user_adresse" ;
					}
				else
					echo "<input type=\"hidden\" name=\"adresse\"  value=\"\" > " ;
				echo "<input type=\"hidden\" name=\"idx\" value=\"$idx\"> " ;
				echo "</form> </table>";
				}	
		if (($user_droit=="E") || ($user_droit=="A") || ($user_droit=="F") || ($user_droit=="T"))
			affiche_alarme();
		
		echo "<hr>";

	
		if ($action=="upload")
			traite_upload($user_idx, $code_lecture, variable ("idx") );	
		

	if (($action=="phpinfo") && ( ($user_droit=="A") || ($user_droit=="E")) )
		{
		phpinfo();
		pied_de_page();
		}


	if (($action=="archivage_php") &&  ($user_droit=="E")) 
		{
		ajout_log_tech( "Archivage PHP et SQL" , "P2");

		archivage_php();
		echo "<p> Sauvegarde Tables ";
		backup_tables(false);
		pied_de_page();
		}		
		
		if ($action=="supp_upload_a_confirmer")
			{
			echo "<hr><p>".traduire('Attention, vous avez demand� la suppression de ')."<table><tr>";
			$num=variable('num');
			visu_doc($num,"");
			echo "<tr><td> ".traduire('Confirmez-vous la suppression')." ? : </td>";
			lien_c ("images/oui.png","supp_upload",param("num",$num). param("retour",variable("retour")));
			echo "</table></div><p><p><p>";
			pied_de_page("x");
			}	
			
	if ((($action=="sms_envoi") || ($action=="sms_envoi_ovh")|| ($action=="sms_envoi_ovh_dd") ) &&  ($user_droit!=""))
		{
		$msg= stripcslashes(variable("msg"));	
		$org = stripcslashes(variable("origine"));		
		$tel= variable("tel");		
		if (strlen($msg)>10)
			{
			$msg.= " De ".$org;
			if ($action=="sms_envoi")
				{
				envoi_SMS( $tel , $msg);
				ajout_log( $idx, traduire("Envoi SMS personnel �")." : $tel", $user_idx );
				msg_ok (traduire("Envoi du SMS r�alis�"));
				}
			else
				{
				if ($action=="sms_envoi_ovh_dd")
					$origine="DOC-DEPOT";
				else 
					$origine="ADILEOS";

				envoi_SMS_operateur( $tel , $msg, $origine);
				ajout_log( $idx, traduire("Envoi SMS (via op�rateur) personnel �")." : $tel ($msg)", $user_idx );
				msg_ok (traduire("Envoi du SMS via op�rateur r�alis�"));
				}

			$action="";
			}
		else
			{
			erreur (traduire("Message trop court"));
			//$action="sms_test";			
			}
		}	
/*
	if (($action=="sms_envoi_ovh") &&  ($user_droit!=""))
		{
		$msg= stripcslashes(variable("msg"));		
		$org = stripcslashes(variable("origine"));
		$tel= variable("tel");		
		if (strlen($msg)>10)
			{
			$msg.= " Message de ".$org;

			$action="";
			}
		else
			{
			erreur (traduire("Message trop court"));
			$action="sms_test";			
			}
		}	
*/	
	if ((($action=="sms_test") || ($action=="sms_test_ovh")|| ($action=="sms_test_ovh_dd"))  &&  ($user_droit!=""))
		{
		$tel= variable("tel");
		if ($tel=="")
			$tel= variable_s("tel");

		$msg= stripcslashes(variable("msg"));
		
			echo "<center><p>".traduire('Envoi d\'un SMS')." : ";
		
			debut_cadre("500");
			echo "<br><img src=\"images/sms.png\" width=\"50\" height=\"40\" > ";
			if ($action=="sms_test") 
				formulaire ("sms_envoi");
			else			
				if ($action=="sms_test_ovh_dd") 
					formulaire ("sms_envoi_ovh_dd");
				else
					formulaire ("sms_envoi_ovh");

			echo "<TABLE><TR><td> ".traduire('Destinataire')." : </td> ";
			if ($tel=="")
				echo "<td> <input type=\"text\" name=\"tel\" size=\"13\" value=\"06\"/></td>";
			else
				echo "<td> $tel </td>".param("tel",$tel);
			echo param("origine",$user_nom." ".$user_prenom);
			echo "<TR> <td>".traduire('Texte').": </td><td><TEXTAREA rows=\"5\" cols=\"40\" onblur=\"calculeLongueur();\" onfocus=\"calculeLongueur();\" onkeydown=\"calculeLongueur();\" onkeyup=\"calculeLongueur();\"  id=\"msg\" name=\"msg\" >$msg</TEXTAREA></td>";
			echo "<TR> <td></td><td><div id=\"indic\">".traduire('220&nbsp;caract&egrave;res&nbsp;disponibles')."</div></td>";
		
			echo "<TR> <td> </td><td><input type=\"submit\"  id=\"envoi\"  value=\"".traduire('Envoyer sms sign� de ')." $user_nom $user_prenom \"/></td> ";
			echo "</form> </table> ";

			fin_cadre();
		rappel_regles_messages("sms");
		pied_de_page("x");
		}			
	

	if (($action=="mail_envoi") )
		{
		$mail = variable("mail");
		$titre = html_entity_decode(stripcslashes(variable("titre")));
		$msg = html_entity_decode(stripcslashes(variable("msg")));
		$org = stripcslashes(variable("origine"));
		$mail_org = variable("mail_org");
		if ( VerifierAdresseMail($mail))
			{
			if ( (strlen($msg)<10) ||  (strlen($titre)<8) )
				{
				erreur (traduire("Message ou titre du mail trop court"));
				$action="mail_test";			
				}
			else
				{
				envoi_mail_perso( $mail  ,$titre, $msg, $org, $mail_org);
				ajout_log( $idx, traduire("Envoi mail personnel �")."  $mail", $user_idx );
				msg_ok (traduire("Mail envoy� �")." $mail");
				$action="";
				}
			}
		else
			{
			erreur (traduire("Adresse mail incorrecte"));
			$action="mail_test";
			}
		}			

	
	if (($action=="mail_test") )
		{
		$mail= variable("mail");
		if ($mail=="")
			$mail= variable_s("mail");

		$msg= variable("msg");
		$titre= variable("titre");
		
			echo "<p><center>".traduire('Envoi d\'un Mail')." : ";		
			debut_cadre("700");
			echo "<br><img src=\"images/mail2.png\" width=\"50\" height=\"40\" > ";
			formulaire ("mail_envoi");
			echo "<TABLE><TR><td> ".traduire('Adresse mail')." : </td> ";
			if ($mail=="")
				echo "<td> <input type=\"text\" name=\"mail\" size=\"70\" value=\"$mail\"/></td>";
			else
				echo "<td> $mail</td>".param("mail",$mail);
				
			echo param("origine",$user_nom." ".$user_prenom);
			param("mail_org",$user_mail);
			echo "<TR> <td>".traduire('Titre').": </td><td><input type=\"text\" name=\"titre\" size=\"70\" value=\"$titre\"/></td>";
			echo "<TR> <td>".traduire('Texte').": </td><td><TEXTAREA rows=\"5\" cols=\"70\" name=\"msg\" >$msg</TEXTAREA></td>";
			echo "<TR> <td> </td><td><input type=\"submit\"  id=\"envoi\"  value=\"".traduire('Envoyer mail sign� de')." $user_nom $user_prenom\"/></td> ";
			echo "</form> </table> ";
			fin_cadre();
			
			rappel_regles_messages();
			
		pied_de_page("x");
		}			
		
	

		// !!!!!!!!!!!!! ZONE COMPLEXE  !!!!!!!!!!!
		if (($_SESSION['bene']!="") && ($action!="") && ($action!="dde_acces") && (($user_droit=="S") ||($user_droit=="M") ))
			{
			if (($action!="ajout_admin") 
			&& ($action!="dossier")
			&& ($action!="dossier_mail")
			&& ($action!="illicite")
			&& ($action!="creer_dossier") 
			&& ($action!="envoyer_dossier") 
			&&  ($action!="draganddrop") 
			&&  ($action!="rdv") 
			&&  ($action!="ajout_rdv")
			&&  ($action!="histo"))
				$action="detail_user";
			$user=$_SESSION['bene'];
			}
		else
			$_SESSION['bene']="";
		// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 

	if ($user_droit=="E") 
			{
			echo "<table><tr><td width=\"500\">";
			echo " <ul id=\"menu-bar\">";
			echo "<li><a href=\"index.php\"> Journaux </a><ul>";
			echo "<li><a href=\"index.php?".token_ref("afflog_t")."\"> Log technique </a></li>";
			echo "<li><a href=\"index.php?".token_ref("afflog")."\"> Log Fonctionnel</a></li>";		
			echo "<li><a href=\"tmp/log.txt\"> Aujourd'hui</a></li>";		
			echo "<li><a href=\"tmp/hier.txt\"> Hier</a></li>";		
			echo "</li></ul>";			
			echo "<li><a href=\"index.php?".token_ref("liste_compte")."\"> Liste User</a><ul>";
			echo "<li><a href=\"index.php?".token_ref("visu_pages_users")."\"> Pages </a></li></ul>";
			
			echo "<li><a href=\"index.php?".token_ref("force_supervision_sms")."\"> T�l�com</a><ul>";
			
			echo "<li><a href=\"index.php?".token_ref("force_supervision_sms")."\"> Supervision SMS � la demande </a></li>";
			echo "<li><a href=\"index.php?".token_ref("active_sms2mail")."\">  active_sms2mail </a></li>";
			echo "<li><a href=\"index.php?".token_ref("desactive_sms2mail")."\"> desactive_sms2mail </a></li>";		
			echo "<li><a href=\"index.php?".token_ref("sms_test")."\"> Envoi SMS </a></li>";		
			echo "<li><a href=\"index.php?".token_ref("sms_test_ovh")."\"> Envoi SMS OVH </a></li>";		
			echo "</li></ul>";			
			
			echo "<li> <a href=\"index.php\">CTRL</a><ul>";			

			echo "<li><a href=\"index.php?".token_ref("en_trop")."\"> Fichiers en trop </a></li>";
			echo "<li><a href=\"index.php?".token_ref("authenticite")." \"> Aunthenticit� </a></li>";
			echo "<li><a href=\"index.php?".token_ref("mini_pdf")."\"> Miniature PDF </a></li>";
			echo "<li><a href=\"index.php?".token_ref("integrite")."integrite \"> Int�grit� BdD </a></li>";
			echo "</ul><li> <a href=\"index.php?".token_ref("param_sys")."\">Param�trage</a><ul>";			

			echo "<li><a href=\"index.php?".token_ref("phpinfo")."\"> Phpinfo </a></li>";
			echo "<li><a href=\"index.php?".token_ref("archivage_php")."\"> Archivage Php+Sql </a></li>";
			echo "<li><a href=\"index.php?".token_ref("cmd_sql")."\"> Commande Sql </a></li>";
			echo "</ul></ul >";
			echo "<td></table>";

			}	
			

		if ($user_droit=="T") 
			{
			echo "<table><tr><td width=\"400\">";
			echo " <ul id=\"menu-bar\">";
			echo "<li><a href=\"index.php?".token_ref("init_formation")."\"> Init comptes </a></li>";			
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
		echo "Controle Aunthenticit� des fichiers";
		ctrl_signature(true);
		pied_de_page("x");
		}		
		
	if (($action=="mini_pdf") &&  ($user_droit=="E"))
		{
		echo "Controle existante miniature PDF";
		ctrl_mini_pdf();
		pied_de_page("x");
		}	

	if (($action=="force_supervision_sms") &&  ($user_droit=="E"))
		{
		ecrit_parametre('TECH_msg_supervision_gatewaysms', "Test SMS ");
		envoi_SMS( parametre('DD_numero_tel_sms') ,parametre('TECH_msg_supervision_gatewaysms').". ".date('H\hi',time()));
		ecrit_parametre('TECH_dernier_envoi_supervision', time() );
		echo "Supervision SMS � la demande envoy�e avec '". parametre('TECH_msg_supervision_gatewaysms')."'";
		pied_de_page("x");
		}	

	if (($action=="active_sms2mail") &&  ($user_droit=="E"))
		{
		envoi_SMS( parametre('DD_numero_tel_sms') ,'sms2mail on');
		echo "Activation SMS2MAIL envoy�e au ". parametre('DD_numero_tel_sms');
		pied_de_page("x");
		}	
		
	if (($action=="desactive_sms2mail") &&  ($user_droit=="E"))
		{
		envoi_SMS( parametre('DD_numero_tel_sms') ,'sms2mail off');
		echo "Activation SMS2MAIL envoy�e au ". parametre('DD_numero_tel_sms');
		pied_de_page("x");
		}	
	

	

		
	if (($action=="raz_mdp1") &&  ($user_droit=="E"))
		{
		$idx=variable('idx');
		$reponse =command("SELECT * from  r_user WHERE idx='$idx'"); 
		if ($donnees = fetch_command($reponse))
			{
			$id=$donnees["id"];	
			debut_cadre();
			echo "<p><br>".traduire('R�initialisation de mot de passe')." <p> ".traduire('Compte')." : $id <br><p>";
			echo "<TABLE><TR>";
			formulaire ("raz_mdp");
			echo "<TR> <td>".traduire('Nouveau mot de passe').": </td><td><input  id=\"pwd\" type=\"password\" name=\"mdp\" value=\"\"/></td>";
			echo "<td><input type=\"image\" src=\"images/oui.png\" width=\"20\" height=\"20\"  >";
			echo "<tr><td></td><td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> ".traduire('Voir saisie')."<td>";
			echo "<input type=\"hidden\" name=\"idx\" value=\"$idx\"> " ;
			echo "</form> </table> ";
			fin_cadre();
			pied_de_page("x");
			}
		}				
		
	if ( (($action=="cmd_sql") || ($action=="cmd_sql_backup") ) && ( ($user_droit=="E")) )
		{
		if  ($action=="cmd_sql_backup") 
			backup_tables(false);
		include('command_sql.php');
		pied_de_page();
		}	
		
	// permet la reinitialisation d'un mot de passe paticilier d'un compte 
	if (($action=="raz_mdp") &&  ($user_droit=="E"))
		{
		$idx=variable('idx');
		$reponse =command("SELECT * from  r_user WHERE idx='$idx'"); 
		if ($donnees = fetch_command($reponse))
			{
			$id=$donnees["id"];	
			$mdp=variable('mdp');
			echo traduire("R�initilaition du mot de passe de")." '$id' ".traduire("faite (Mdp = ")."'$mdp')";
			ajout_log_tech("R�initilaition du mot de passe de '$id' faite par exploitant. (Mdp = '$mdp')");
			$mdp=encrypt($mdp);
			$reponse =command("UPDATE r_user set pw='$mdp' where idx='$idx'");
			$action="liste_compte";
			}
		}				
	
	if ( ($action=="liste_compte") && ($user_droit=="E"))
			{
			echo "</table><div class=\"CSSTableGenerator\" > ";
			echo "<table><tr><td > ".traduire('N�')."  </td><td> ".traduire('Cr�ation')."</td><td> ".traduire('Identifiant')."</td><td> ".traduire('Nom')."</td><td> ".traduire('Pr�nom')."</td><td> ".traduire('Mail')."</td><td> ".traduire('Tel')."</td><td> ".traduire('Droit')."</td>";
			$reponse =command("select * from  r_user order by idx desc");		
			while ($donnees = fetch_command($reponse) ) 
				{
				$idx1=$donnees["idx"];	
				$creation=$donnees["creation"];
				$id=$donnees["id"];
				$nom=$donnees["nom"];
				$prenom=$donnees["prenom"];
				$mail=$donnees["mail"];
				$tel=$donnees["telephone"];
				$droit=$donnees["droit"];
				echo "<tr><td>  $idx1 </td><td> $creation </td><td>  <a href=\"index.php?".token_ref("chgt_user")."&user=$idx1\"> $id </a></td><td> $nom </td><td> $prenom </td><td> $mail </td><td> $tel </td><td> $droit </td>";
				lien_c ("images/illicite.png", "raz_mdp1", param("idx","$idx1" ) , traduire("Raz MdP"));
				}
			echo "</table></div>";
			pied_de_page("x");
			}	

				
	if ( ($action=="visu_pages_users") && ($user_droit=="E"))
			{
			echo "</table><div class=\"CSSTableGenerator\" > ";
			echo "<table><tr><td > ".traduire('Utilisateur')."  </td><td> ".traduire('Date')."</td>";
			$reponse =command("select * from  r_pages_users_debug order by tps_exec desc");		
			while ($donnees = fetch_command($reponse) ) 
				{
				$idx1=$donnees["idx_user"];	
				$tps_exec=$donnees["tps_exec"];
				echo "<tr><td>  ".libelle_user($idx1)." </td><td> <a target=_blank href=\"index.php?".token_ref("visu_page")."&user=$idx1&tps_exec=$tps_exec\"> ".date ("d/m/Y H:i's",$tps_exec)." </a></td>";
				}
			echo "</table></div>";
			pied_de_page("x");
			}	

				
	if ( ($action=="visu_page") && ($user_droit=="E"))
			{
			$idx1=variable_s("user");	
			$tps_exec=variable_s("tps_exec");
			$reponse =command("select * from  r_pages_users_debug where idx_user=$idx1 and tps_exec=$tps_exec");		
			if ($donnees = fetch_command($reponse))
				{
				ob_end_clean();
				echo "<H1>VISU PAGE ".libelle_user($idx1);
				
				$r1 =command("select * from  r_pages_users_debug where idx_user=$idx1 and tps_exec<$tps_exec order by tps_exec desc");		
				if ($d1 = fetch_command($r1))
					{
					$tps_exec1=$d1["tps_exec"];
					echo "<a href=\"index.php?".token_ref("visu_page")."&user=$idx1&tps_exec=$tps_exec1\"> ".date ("H:i's",$tps_exec1)." </a> - ";
					}
				
				echo date ("d/m/Y H:i's",$tps_exec);

				$r1 =command("select * from  r_pages_users_debug where idx_user=$idx1 and tps_exec>$tps_exec order by tps_exec asc");		
				if ($d1 = fetch_command($r1))
					{
					$tps_exec1=$d1["tps_exec"];
					echo "- <a href=\"index.php?".token_ref("visu_page")."&user=$idx1&tps_exec=$tps_exec1\"> ".date ("H:i's",$tps_exec1)."  </a>";
					}				
			
				echo "</H1><hr>";
				echo $donnees["resultat"];	
				ob_end_flush();
				exit();
				}
			}	
			
		if (($action=="afflog") &&  ($user_droit=="E"))
			{
			echo "</table> ";
			$filtre1=variable("filtre");
			formulaire ("afflog");
			echo "<table><tr> <td>".traduire('Filtre')." : <input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
			echo "</form> </td></table> ";
			
			echo "<div class=\"CSSTableGenerator\" ><table><tr><td > ".traduire('Date')." </td><td> ".traduire('IP')."</td><td> ".traduire('Action')." </td><td> ".traduire('Compte')." </td><td> ".traduire('Acteur')." </td>";
			if ($filtre1=="")
				$reponse =command("select * from  log  order by date desc limit 0,1000");		
			else
				$reponse =command("select * from  log where (date REGEXP '$filtre1' or ligne REGEXP '$filtre1' or user REGEXP '$filtre1' or acteur REGEXP '$filtre1' or ip REGEXP '$filtre1') order by date desc");		
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
			echo "</table></div>";
			pied_de_page("x");
			}
			
		if (($action=="afflog_t") && ($user_droit=="E"))
			{
			echo "</table> ";
			$filtre1=variable("filtre");
			formulaire ("afflog_t");
			echo "<table><tr> <td>".traduire('Filtre')." : <input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
			echo "</form> </td></table> ";
			
			echo "<div class=\"CSSTableGenerator\" ><table><tr><td > ".traduire('Date')."  </td><td> ".traduire('Prio')." </td><td> ".traduire('Ev�nement')." </td><td> ".traduire('Ip')." </td>";
			if ($filtre1=="")
				$reponse =command("select * from  z_log_t  order by date desc limit 0,1000");		
			else
				$reponse =command("select * from  z_log_t where (date REGEXP '$filtre1' or ligne REGEXP '$filtre1' or ip REGEXP '$filtre1'or prio REGEXP '$filtre1') order by date desc");		
			while ($donnees = fetch_command($reponse) ) 
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
			

	if (($action=="alerte_surv_mail") && ($user_droit==""))
		{
		alerte_surv_mail();
		$action="surv_mail";
		}	
				

	if (($action=="surv_mail") && ($user_droit==""))
		{
		surv_mail();
		pied_de_page("x");
		}	

	if ($action=="supp_filtre_histo") 
		{
		$filtre="";
		$action = "histo";
		}
		
	if (($action=="histo") || ($action=="filtre_histo") )
		histo_beneficiaire($user_idx, $id);

	if ( ($action=="nouveau_fissa") && ( ($user_droit=="A")  ) )
			{
			$organisme=variable("organisme");
			$support=variable("support");
			$libelle=variable("libelle");
			$acteur=variable("acteur");
			if ($acteur=="") 
				$acteur="Acteur";
			
			$beneficiaire=variable("beneficiaire");
			if ($beneficiaire=="") 
				$beneficiaire="B�n�ficiaire";
				
			if ( ($organisme!="") && ($support!="") )
				{
				$reponse =command("select * from fct_fissa where support='$support'");		
				if ($donnees = fetch_command($reponse) ) 
					erreur("Support d�j� existant");
					else
						{
						command("INSERT INTO fct_fissa VALUES ('$organisme', '$support','$libelle','$acteur', '$beneficiaire', '', '' ) ");
						$reponse =command("select * from fct_fissa ");		
						if ($donnees = fetch_command($reponse) ) 
							{
							$support_org=$donnees["support"];				
							command("CREATE TABLE $support like $support_org ");
							}
						}
					
				}
			$action="membres_organisme";
			}				

	if ( ($action=="membres_organisme") && ( ($user_droit=="A") || ($user_droit=="S")|| ($user_droit=="R") ) )
			{
			$organisme=variable_get("organisme");
			if ($organisme=="")
				$organisme=variable("organisme");
			echo libelle_organisme ($organisme); 
			echo "<div class=\"CSSTableGenerator\" ><table><tr><td > ".traduire('Droit')."  </td><td > ".traduire('Identifiant')."  </td><td> ".traduire('Nom')." </td><td> ".traduire('Pr�nom')." </td><td> ".traduire('T�l�phone')." </td><td> ".traduire('Mail')." </td>";
			$reponse =command("select * from r_lien where organisme='$organisme' ");		
			while ($donnees = fetch_command($reponse) ) 
				{
				$idx=$donnees["user"];	
				affiche_membre($idx);
				}			
				
			$reponse =command("select * from r_user where (organisme='$organisme' and (droit='S' or droit='s') )");		
			while ($donnees = fetch_command($reponse) ) 
				{
				$idx=$donnees["idx"];	
				affiche_membre($idx);
				}
			echo "</table></div>";
			
			if ($user_droit=="A") 
				{
				echo "<p><div class=\"CSSTableGenerator\" ><table><tr><td > ".traduire('Support')."  </td><td > ".traduire('Libell�')."  </td><td> ".traduire('Libell� Acteur')." </td><td> ".traduire('Libell� B�n�ficiaire')." </td>";
				
				formulaire ("nouveau_fissa");
				echo "<tr>";
				echo "<td> <input type=\"texte\" name=\"support\"   size=\"20\" value=\"\"> </td>";
				echo "<td> <input type=\"texte\" name=\"libelle\"   size=\"20\" value=\"\"> </td>";
				echo "<td> <input type=\"texte\" name=\"acteur\"   size=\"10\" value=\"\"> </td>" ;
				echo "<td> <input type=\"texte\" name=\"beneficiaire\"   size=\"25\" value=\"\"> </td>" ;
				echo "<input type=\"hidden\" name=\"organisme\"  value=\"$organisme\"> " ;
				echo "<td><input type=\"submit\" id=\"nouveau_fissa\" value=\"".traduire('Ajouter')."\" ></form> </td> ";			
				
				$reponse =command("select * from fct_fissa where organisme='$organisme' ");		
				while ($donnees = fetch_command($reponse) ) 
					{
					$support=$donnees["support"];	
					$libelle=$donnees["libelle"];	
					$acteur=$donnees["acteur"];
					$beneficiaire=$donnees["beneficiaire"];
					$mails_rapports=$donnees["mails_rapports"];
					echo "<tr><td>  $support </td><td>  '$libelle'  </td><td> '$acteur' </td><td> '$beneficiaire'</td><td> $mails_rapports </td>";
					}			
					
				echo "</table></div>";
				}
			
			pied_de_page("x");
			}	

	if ( ($action=="justificatifs") && ($user_droit=="R") )
			{
			$organisme=variable_get("organisme");
			if ($organisme=="")
				$organisme=variable("organisme");
			echo traduire ("Liste des justificatifs de")." : ".libelle_organisme ($organisme); 
			echo "<div class=\"CSSTableGenerator\" ><table><tr><td> ".traduire('Nom')." </td><td> ".traduire('Pr�nom')." </td>";
			$reponse =command("select * from r_user where (organisme='$organisme' and (droit='S' or droit='s') )");		
			while ($donnees = fetch_command($reponse) ) 
				{
				$idx=$donnees["idx"];	
				if (affiche_membre($idx,"r�duit")==0)
					echo "<td>".traduire ("Justificatif manquant")." ! </td>";
				}			
			echo "</table></div>";
			pied_de_page("x");
			}	
			
		if (($action=="renvoyer_mail") &&( ($user_droit=="R") || ($user_droit=="A")))
			{
			$idx=variable_get('idx');
			$mail=mail_user($idx);
			$body= traduire('Cr�ation de compte sur \'Doc-depot.com\':');
			$body .= "<p>".traduire("Pour accepter et finaliser la cr�ation de votre compte sur 'Doc-depot.com', merci de cliquer sur ce")." <a  id=\"lien\"  href=\"".serveur."index.php?".token_ref("finaliser_user")."&idx=".addslashes(encrypt($idx))."\">".traduire('lien')." </a> ".traduire('et compl�ter les informations manquantes.');            
			
			$body .="<br><br>".traduire("Si le lien ne fonctionne pas, recopiez dans votre navigateur internet cette adresse : ")."<br><strong>".serveur."index.php?".token_ref("finaliser_user")."&idx=".addslashes(encrypt($idx));

			
			$body .= "</strong><p>".traduire('Message de la part de')." $user_prenom $user_nom";
			$body .= "<p> <hr> <center> Copyright ADILEOS </center>";			// Envoyer mail pour demander saisie pseudo et PW
			envoi_mail($mail,traduire("Cr�ation compte"),$body);
			ajout_log( $idx, traduire("Renvoi mail de finalisation de compte")." : $mail", $user_idx );
			}

		if (($action=="modification_lecture") && ($user_droit==""))
			{
			$action="visualisation_lecture2";
			$ok=FALSE;
			$reponse = command("SELECT * from  r_user WHERE id='$id'"); 
			if ($donnees = fetch_command($reponse))
				{
				$ancienne_lecture=$donnees['lecture'];
				$mdp=variable('n1');
				if ($mdp==variable('n2') )
					{
					if (strlen($mdp)==0)
						{
						command("UPDATE r_user set lecture='$mdp' where id='$id'");
						msg_ok (traduire("Modification effectu�e: votre code de lecture est d�sactiv�."));
						$ok=TRUE;
						ajout_log( $id, traduire('Effacement code lecture') );
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
								command("UPDATE r_user set lecture='$mdp' where id='$id'");
								msg_ok( traduire("Modification code lecture r�alis�e."));
								$ok=TRUE;
								ajout_log( $id, traduire('Changement code lecture') );
								$code_lecture=$mdp;	
								$user_lecture=$mdp;
								$action="";
								}
							else 
								erreur( traduire("Le mot de passe n'est pas assez complexe (utiliser des Majuscules, Chiffres, caract�res sp�ciaux)"));
							}
						else 
							erreur(traduire("Le mot de passe est trop court (au moins 8 caract�res)."));
					}
				else 
					erreur(traduire("Les 2 mots de passe ne sont pas identiques."));
				}
			else 
				erreur(traduire("Identifiant Incorrect."));
			}
				
		if ((($action=="visualisation_lecture") || ($action=="visualisation_lecture2")) && ($user_droit==""))
			{
			$reponse = command("SELECT * from  r_user WHERE id='$id' "); 
			$donnees = fetch_command($reponse);
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
					echo "<p><br><p>".traduire('Votre code de lecture est')." : '".$cl."' <p>";
					}
				else
					echo "<p><br><p>".traduire('Votre code de lecture n\'est pas d�fini').". <p><br><p>";
				
				echo "<hr><p>".traduire('Modification de votre code de lecture des fichiers');
				echo "<TABLE><TR>";
				formulaire ("modification_lecture");
				echo "<TR> <td>".traduire('Nouveau code de lecture').":</td><td><input  id=\"pwd\" type=\"password\" name=\"n1\" value=\"\"/></td>";
				echo "<TR> <td>".traduire('Confirmation').":</td><td><input  type=\"password\" id=\"pwd1\" name=\"n2\" value=\"\"/></td>";
				echo "<td><input type=\"image\" src=\"images/oui.png\" width=\"20\" height=\"20\"  >";
				echo "<tr><td></td><td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> ".traduire('Voir saisie')."<td>";
				echo "</form> </table> ";
				fin_cadre();
				pied_de_page("x");
				}
			else
				{
				erreur (traduire("Mot de passe incorrect"));
				$action="visu_lecture";
				}

			}	
		if (($action=="visu_lecture") && ($user_droit==""))
			{
			echo "<hr><p><center>";
			debut_cadre("700");
			echo "<br><p>".traduire('Vous souhaitez visualiser ou modifier votre code secret permettant la lecture des documents')." <table><tr>";
			formulaire ("visualisation_lecture");
			echo "<td> ".traduire('Votre mot de passe').":</td><td> <input type=\"password\" id=\"pwd\" name=\"pw\"  value=\"\">  </td>";
			echo "<td><input type=\"image\" src=\"images/oui.png\" width=\"20\" height=\"20\"  >";
			echo "<tr><td></td><td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' \"> ".traduire('Voir saisie')."<p><td>";
			echo "</form> </table></div><p>";
			fin_cadre();
			pied_de_page("x");
			}	

		
		// si on arrive ici avec action=exporter c'est que l'export a echou� 
		if ( ($action=="exporter_a_confirmer")  && ($user_droit==""))
			{
			echo "<hr><center><p>";
			debut_cadre("700");
			echo "<p><br>".traduire('Pour confirmer la g�n�ration d\'un export, il vous faut saisir � nouveau votre mot de passe')." <p>";
			formulaire ("exporter","visu.php");
			echo "<table><tr><td>".traduire('Mot de passe').": <input type=\"password\" name=\"pw\" id=\"pwd\" value=\"\">  </td>";
			echo "<td><input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> ".traduire('Voir saisie')."<td>";

			echo "<tr><td>".traduire('Confirmez-vous la g�n�ration de l\'export').": </td>";
			echo "<td><br><input type=\"image\" src=\"images/oui.png\" width=\"20\" height=\"20\" title=\"Confirmer la g�n�ration de l'export\" >";
			echo "</form> </td>";
			echo "</table></div><p><p><p>";
			fin_cadre();
			echo "<p><center>".traduire('Merci de noter que le fichier g�n�r� ne sera pas prot�g� par mot de passe').". <p>";
			pied_de_page("x");
			}			
			
		if (($action=="supp_compte_a_confirmer") && ($user_droit==""))
			{
			echo "<hr><p><br><p><center>".traduire('Attention, vous avez demand� la suppression de votre compte.');
			echo "<p>".traduire('Il est vivement recommand� de faire une sauvegarde de tous vos documents, photos et notes/sms en cliquant sur')." <a href=\"index.php?".token_ref("exporter_a_confirmer")."\">".traduire('ce lien')." </a>";
			echo traduire("puisqu'en supprimant le compte l'ensemble des informations et contenu sera d�truit et que cette action est irreversible.");
			debut_cadre("700");
			echo "<p><br>".traduire('Si vous voulez confimer la suppression du compte, il faut saisir � nouveau votre mot de passe');
			formulaire ("supp_compte");
			echo "<table><tr><td></td><td>".traduire('Mot de passe').": <input type=\"password\" name=\"pw\" id=\"pwd\"  value=\"\">  </td>";
			echo "<td> <input type=\"checkbox\" onchange=\"document.getElementById('pwd').type = this.checked ? 'text' : 'password' ; document.getElementById('pwd1').type = this.checked ? 'text' : 'password' \"> ".traduire('Voir saisie')."<td>";

			echo "<tr><td></td><td> ".traduire('Confirmez-vous la suppression')." : </td>";
			echo "<td><br><input type=\"image\" src=\"images/oui.png\" width=\"20\" height=\"20\" title=\"".traduire('Confirmer la suppression')."\" >";
			echo "</form> </td>";
			echo "</table><p><p><p>";
			fin_cadre();
			pied_de_page("x");
			}

	
		if (($action=="supp_referent_a_confirmer") && ($user_droit==""))
			{
			echo "<hr><p>".traduire('Attention, vous avez demand� la suppression du r�f�rent de confiance suivant')." :<p>";
			$idx=variable('idx');
			titre_referent("");
			visu_referent($idx);
			echo "<td> ".traduire('Confirmez-vous la suppression')." :";
			lien ("images/oui.png","supp_referent",param("idx",$idx) );
			echo " </td></table></div><p><p><p>";
			pied_de_page("x");
			}

		if (($action=="supp_organisme_a_comfirmer") && ($user_droit=="A"))
			{
			echo "<hr><p>".traduire('Attention, vous avez demand� la suppression de la structure sociale suivante')." :<p>";
			$idx=variable('idx');
			titre_organisme();
			$reponse =command("select * from  r_organisme where idx='$idx' ");
			$donnees = fetch_command($reponse) ;
			$adresse=stripcslashes($donnees["adresse"]);
			$organisme=stripcslashes($donnees["organisme"]);				
			$tel=$donnees["tel"];	
			$sigle=stripcslashes($donnees["sigle"]);	
			$mail=$donnees["mail"];	
			$doc_autorise=$donnees["doc_autorise"];	
			$idx=$donnees["idx"];	
			echo "<tr><td> $organisme </td><td> $sigle   </td><td> $adresse   </td><td> $tel </td><td> $mail</td><td> $doc_autorise</td>";
			echo "<td> ".traduire('Confirmez-vous la suppression')." : </td>";
			lien_c ("images/oui.png","supp_organisme",param("idx",$idx),traduire("Confirmer la suppression") );
			echo "</table></div><p><p><p>";
			pied_de_page("x");
			}		
			
		if ( ($action=="supp_user_a_confirmer") &&(  ($user_droit=="A") || ($user_droit=="R") ) )
			{
			echo "<hr><br>".traduire('Attention, vous avez demand� la suppression du responsable ou de l\'acteur social suivant')." :<p> ";
			echo "<div class=\"CSSTableGenerator\"><table>";
			$idx=variable('idx');
	
			titre_user($user_droit);
			visu_user($idx,$user_droit);
			echo "<td> ".traduire('Confirmez-vous la suppression')." : ";
			lien ("images/oui.png","supp_user",param("idx",$idx) ,"Confirmer la suppression");
			echo "</td></table></div><p><p><p>";
			pied_de_page("x");
			}

		if ( (($action=="draganddrop") ||  ($action=="draganddrop_p")  )  &&(  ($user_droit=="") || ($user_droit=="S") ) )
			{
			$user = $_SESSION['user_idx'];

			echo "<hr>";
			affiche_titre_user($user);
			echo traduire("D�poser ci-dessous les fichiers � charger")." : <p> ";

			if  ($action=="draganddrop") 
				echo "<form action=\"upload_dd.php\" class=\"dropzone\" id=\"my-awesome-dropzone\" >";
			else
				echo "<form action=\"upload_dd_p.php\" class=\"dropzone\" id=\"my-awesome-dropzone\" >";

			echo "</form>";
			echo "<p><br><center><a href=\"index.php\" > ".traduire('Cliquez ici quand vous avez termin�').".</a><br></center>";
			pied_de_page("");
			}			
			
		if ( ($action=="modifier_user") && (($user_droit=="R") || ($user_droit=="A") ) )
			modif_user	(variable("idx"));
				
			
		if ( (($user_droit=="R") || ($user_droit=="S") ) && (($action=="") ||($action=="modif_organisme") || ($action=="modif_tel")|| ($action=="modif_domicile") ) )
			{
			echo "<table>";
			$r1 =command("select * from  r_organisme where idx='$user_organisme' ");
			$d1 = fetch_command($r1);
			$orga=stripcslashes($d1["organisme"]);
			$adresse=stripcslashes($d1["adresse"]);
			$mail=$d1["mail"];
			$telephone=$d1["tel"];
			$id_org=$d1["idx"];
			if ($user_droit=="R") 
				{
				$reponse =command("select * from r_lien where user='$user_idx'  ");
				
				while ($donnees = fetch_command($reponse) ) 
					{
					$id_org = $donnees["organisme"];
					$r1 =command("select * from r_organisme where idx='$id_org'  ");
					$d1 = fetch_command($r1);
					$orga=stripcslashes($d1["organisme"]);
					$adresse = $d1["adresse"];
					$telephone = $d1["tel"];
					$mail = $d1["mail"];
					formulaire ("modif_organisme");
					echo "<tr><td> $orga  : </td>";
					echo "<td> ".traduire('Adresse').": <input type=\"texte\" name=\"adresse\" onChange=\"this.form.submit();\" size=\"60\" value=\"$adresse\"> " ;
					echo "<td> - ".traduire('T�l�phone').": <input type=\"texte\" name=\"telephone\" onChange=\"this.form.submit();\"  size=\"15\" value=\"$telephone\"> " ;
					echo "<td> - ".traduire('Mail').": <input type=\"texte\" name=\"mail\" onChange=\"this.form.submit();\"  size=\"25\" value=\"$mail\"> " ;
					echo "<input type=\"hidden\" name=\"id\" value=\"$id_org\"> " ;
					echo "</form> ";	
					}		
				
				}
			else
				if ($action=="")
					{
					echo "<tr><td>  <img src=\"images/organisme.png\" width=\"25\" height=\"25\" ></td><td> ".traduire('Structure sociale').": $orga </td><td> / $adresse </td><td> / $telephone </td><td> / $mail </td><td> (".traduire('Resp.:').responsables_organisme($user_organisme).")</td>";
					}
			
			echo "</table>";
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

		
	if ( ($action=="modif_trad_tech") && ($user_droit=="t") )
		modif_trad_tech(variable('idx'),variable('valeur'));
		
	if ( ($action=="modif_trad") && ($user_droit=="t") )
		modif_trad(variable('idx'),variable('valeur'));

	if  ($user_droit=="t")
		traduction();	
		
	if  (($action=="param_sys") && ($user_droit=="E") )
		param_sys();
		
	if ( ($action=="modif_valeur_param") && ($user_droit=="E") )
		{
		modif_valeur_param(variable('nom'),variable('valeur'));
		param_sys();
		}
		
	
	if ( ($action=="modif_mdp_formation") && ($user_droit=="T") )
		{	
		ecrit_parametre("Formation_mdp", variable("mdp") );
		ajout_log( $user_idx, traduire("Initialisation MdP comptes de formation avec")." ".parametre("Formation_mdp") );		
		ajout_log_tech(	"Initialisation MdP comptes de formation avec ".parametre("Formation_mdp"). " par ".$user_nom."  ".$user_prenom);	

		$mdp=encrypt (parametre("Formation_mdp")) ;
		$reponse = command("UPDATE r_user set pw='$mdp' where (id REGEXP 'FORM_R') or (id REGEXP 'FORM_B') or (id REGEXP 'FORM_A')");
		msg_ok(traduire("Mot de passe des comptes de formation initialis�")." ('".parametre("Formation_mdp")."')");
		$action="";
		}	

	if ( ($action=="") && ($user_droit=="T") )
		{
		$mdp=parametre("Formation_mdp");
		formulaire ("modif_mdp_formation");
		echo traduire("Mot de passe par d�faut comptes de formation").": <input type=\"texte\" name=\"mdp\"   size=\"20\" value=\"$mdp\"> ";			
		echo "</form>";		
		}
	
	// intialisation des comptes de formation
	if ( ($action=="init_formation") && ($user_droit=="T") )
		{	
		$i=0;
		echo traduire("Initialisation comptes de formation")." :";
		
		$mdp=encrypt (parametre("Formation_mdp")) ;
		ajout_log( $user_idx, traduire("Initialisation comptes de formation")." : ".parametre("Formation_mdp") );		
		ajout_log_tech(	"Initialisation comptes de formation avec ".parametre("Formation_mdp"). " par ".$user_nom."  ".$user_prenom);	
		// Purge des toutes les tables 

		$reponse = command("Select * from r_user where (id REGEXP 'FORM_R') or (id REGEXP 'FORM_B') or (id REGEXP 'FORM_A')");
		while ($donnees = fetch_command($reponse) )
			{
			$tel = parametre('FORM_tel_rdv') ;
			$id=$donnees["id"];
			echo "<br>- $id";
			$idx=$donnees["idx"];
			command("UPDATE r_user set pw='$mdp', lecture='$mdp', mail='$id@fixeo.com', telephone='$tel' where idx='$idx' ");
			command("delete from r_sms where idx='$idx' ");
			command("delete from DD_rdv where user='$idx' or auteur='$idx' ");
			command("delete from r_dde_acces where user='$idx' or ddeur='$idx' or autorise='$idx' ");
			command("delete from log where user='$idx' or acteur='$idx'  ");
			command("delete from r_referent where user='$idx'  or organisme='$idx'  ");
			$i++;
			
			if ($donnees["droit"]=="")
				{
				ajoute_note($idx,"Num�ro d''envoi de SMS pour Doc-depot 06.98.47.43.12 ");
				$idx_rdv=inc_index("rdv");
				$date=date('Y-m-d');
				$msg = parametre('FORM_msg_rdv');
				command("INSERT INTO DD_rdv VALUES ('$idx_rdv', '$idx','$idx','$date 18H00', '$msg', '15min', 'A envoyer','' ) ");
				}
			}
		
		echo "<p>".traduire('Chaque b�n�ficiaire de la formation a pour r�f�rent tous les acteurs socicaux de la formation');
		// initialisation pour chaque b�n�ficiaire que de tous les Acteur sociaux
		$reponse = command("Select * from r_user where (id REGEXP 'FORM_A') or (id REGEXP 'FORM_R')");
		while ($donnees = fetch_command($reponse) )
			{
			$idx=$donnees["idx"];
			// recherche des ben�ficiaire � ratacher
			$r1 = command("Select * from r_user where (id REGEXP 'FORM_B')");
			while ($d1 = fetch_command($r1) )
				{
				$idx1=$d1["idx"];
				$i=inc_index("referent");					
				$ns= parametre("Formation_num_structure");
				command("INSERT INTO `r_referent`  VALUES ( '$i', '$idx1', '$ns', '$idx','', '','','')");
				}
			}	
		
		echo "<p>".traduire('Un seul document par espace et utilisateur de la formation');
		// on ne garde qu'un document par compte 
		$reponse = command("Select * from r_user where (id REGEXP 'FORM_B') or (id REGEXP 'FORM_A')");
		while ($donnees = fetch_command($reponse) )
			{
			$i=0;
			// espace perso
			$r1 =command("select * from r_attachement where ref='P-$idx' ");		
			while ($d1 = fetch_command($r1) ) 
				{
				if ($i++ !=0)
					supp_attachement ($d1["num"]);
				}	

			$i=0;				
			// espace partag�
			$r1 =command("select * from r_attachement where ref='A-$idx' ");		
			while ($d1 = fetch_command($r1) ) 
				{
				if ($i++ !=0)
					supp_attachement ($d1["num"]);
				}
			}
	
		msg_ok ("<br>".traduire('Mot de passe par d�faut')." : '".parametre("Formation_mdp")."'<br>");
		}					



		
	// -------------------------------------------------------------------------------------------------------
	// au dela les fonctions ne sont pas accesssibles pour les profils E et F
	if ( ($user_droit=="E") || ($user_droit=="F")|| ($user_droit=="T")|| ($user_droit=="t"))
		pied_de_page("x");
		
//include 'planning.php';
		
	if ( ($user_droit=="S") && ($action=="collegues") )
		{
		echo "<H4> ".traduire('Responsables vis � vis de Doc-depot.com')."</H4>";
		
		echo "<div class=\"CSSTableGenerator\"><table> ";
		titre_user("R");
		$reponse =command("select * from  r_lien where organisme='$user_organisme' ");
				
		while ($donnees = fetch_command($reponse) ) 
			{
			$idx=$donnees["user"];
			visu_user($idx,"R");
			}
		echo "</td></table></div></center>";		
		
		echo "<H4> ".traduire('Coll�gues sur Doc-depot.com')."</H4>";
		
		echo "<div class=\"CSSTableGenerator\"><table> ";
		titre_user("R");
		$reponse =command("select * from  r_user where droit='S' and organisme='$user_organisme' ");
				
		while ($donnees = fetch_command($reponse) ) 
			{
			$idx=$donnees["idx"];
			visu_user($idx,"R");
			}
		echo "</td></table></div></center>";
		pied_de_page("x");
		}
			
			
	if ( ($user_droit=="S") && ($action=="verif_user") )
			{
			echo "<table><tr><td width> <ul id=\"menu-bar\">";
			echo "<li><a href=\"index.php?".token_ref("verif_user")."\"  > ".traduire('V�rification existence')." </a></li>";
			echo "</ul></td></table>";
			
			echo "<p><center><table><tr>";
			
			$nom=mef_nom(variable('nom'));
			$prenom=mef_prenom(variable('prenom'));
			$anniv=variable('anniv');
			
			if (($nom!="") && ($prenom!="") && ($anniv!=""))
				{
				$reponse = command("select * from r_user where nom='$nom' and prenom='$prenom' and anniv='$anniv' and droit='' ");
				if ($donnees = fetch_command($reponse) )
					echo"<td ALIGN=\"CENTER\" BGCOLOR=\"lightgreen\" ><br>Il existe un compte avec ces informations. Cliquez <a href=\"index.php?".token_ref("dde_identifiant")."\">ici</a> pour r�cup�rer son compte<p>";
				else
					echo "<td ALIGN=\"CENTER\" BGCOLOR=\"yellow\" ><br>Il n'existe pas de compte avec ces informations. Cliquez <a href=\"index.php?".token_ref("ajout_beneficiaire")."\">ici</a> pour cr�er un compte<p>";
				}
			else
				if (($nom!="") || ($prenom!="") || ($anniv!=""))
					echo "<td ALIGN=\"CENTER\"  ><br>".traduire('Merci de compl�ter tous les champs').".<p>";
			echo "</td></table></center>";
			verif_existe_user();

			}
			
	if ( ($user_droit=="S") && ($action=="verif_existe_user") )
			{
			echo "<table><tr><td width> <ul id=\"menu-bar\">";
			echo "<li><a href=\"index.php?".token_ref("verif_user")."\"  > ".traduire('V�rification existence')." </a></li>";
			echo "</ul></td></table>";
			verif_existe_user();
			}

	// signalement d'un contenu illicite
	if ($action=="illicite")
			{
			$num=variable('num');
			$num = substr($num,strpos($num,".")+1 );
			ajout_log( $idx, traduire("Signalement document illicite")." : $num", $user_idx );
			ajout_log_tech( "Signalement par $user_idx  document illicite : $num" ,"P1");
			msg_ok("<Strong>".traduire('Signalement transmis � l\'administrateur').". </strong>");
			$action=variable('retour');
			}	
			
	// permet de basculer un document d'un espace � un autre
	if ( ($action=="switch")&& ($user_droit=="") )
			{
			$num=variable('num');
			$reponse =command("select * from r_attachement where  num='$num' ");
			if ($donnees = fetch_command($reponse) )
				{
				$ref=$donnees["ref"];
				if ($ref[0]=='A') $ref[0]='P'; else $ref[0]='A';
				$type=$ref[0];
				$reponse =command("update r_attachement SET ref='$ref' where num='$num' ");
				$reponse =command("update r_attachement SET type='$type' where num='$num' ");
				$num = substr($num,strpos($num,".")+1 );
				ajout_log( $idx, traduire("Basculement d'espace de")." $num", $user_idx );				
				}
			$action=variable('retour');
			}			
		
//	if (($action=="dossier") && ($user_droit=="") )
	if ($action=="dossier") 
			{
			if ($_SESSION['bene']=="")
				dossier("A-$user_idx");
			else
				dossier("A-".$_SESSION['bene']);			
			}
			
//	if ( ($action=="creer_dossier") && ($user_droit=="") )
	if ($action=="creer_dossier")
			{
			if ($_SESSION['bene']=="")
				creer_dossier("A-$user_idx");	
			else
				creer_dossier("A-".$_SESSION['bene']);		
			}	

	if ($action=="envoyer_dossier")
			{
			if ($_SESSION['bene']=="")
				$resul=envoyer_dossier("A-$user_idx");	
			else
				$resul=envoyer_dossier("A-".$_SESSION['bene']);		
			if ($resul==false)
				$action="dossier_mail";
			}	
			
	if ($action=="dossier_mail")
			{
			if ($_SESSION['bene']=="")
				dossier_mail("A-$user_idx");	
			else
				dossier_mail("A-".$_SESSION['bene']);		
			}	
			
	if ( ( ($user_droit=="S") || ($user_droit=="M") ) && ($action=="detail_user"))
			{
			affiche_titre_user($user);
			// V�rification si demande de mot de passe

			$date_jour=date('Y-m-d');
			$j=0;
			$r1 =command("select * from r_dde_acces where type='' and user=$user and ddeur=$user_idx and date_dde>='$date_jour' ");
			If ($d1 = fetch_command($r1) ) 
				{
				$date_dde=$d1["date_dde"];
				$qui=$d1["user"];
				$code=$d1["code"];
				$date_auto=$d1["date_auto"];
				$autorise=$d1["autorise"];
				
				echo "</table><table><tr> <td>".traduire('Attention: Demande de recup�ration de mot de passe')." ($date_dde) </td>";
				echo "<form method=\"POST\" action=\"index.php\">  ";
				echo "<td><input type=\"hidden\" name=\"bene\" value=\"$qui\"> " ;
				echo "<input type=\"hidden\" name=\"autorise\" value=\"$user_idx\"> " ;
				if ($code=="")
					{
					token("autorise_recup_mdp");
					echo "<input type=\"submit\"  id=\"autorise_recup_mdp\"  value=\"".traduire('Autoriser apr�s controle d\'identit�')."\"/> " ;
					}
				else
					{
					echo "<td> ".traduire('Code � communiquer <u>apr�s v�rification identit�')."</u>: '<strong>$code</strong>' </td><td> (Valable jusqu'au $date_auto) </td><td>";
					token("supp_recup_mdp");
					echo "<input type=\"submit\"  id=\"supp_recup_mdp\" value=\"".traduire('Supprimer acc�s')."\"/>" ;
					}
				echo "</form>  </td>";	
				}
			}

		if(($user_droit!="") &&  ( ((strtoupper($user_droit)!="S") && (strtoupper($user_droit)!="M")) ||  ($user_droit=="R"))  )
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
				bouton_referent(variable_get("user"));
			else
				bouton_referent($user);
				
			
		if( ($user_droit!="") && ($user_droit!="F") && ($user_droit!="E") )
			{
			if ($action=="ajout_beneficiaire")
				ajout_beneficiaire($user_idx,$user_organisme);
			else			
				if (( (strtoupper($user_droit)=="S") ||(strtoupper($user_droit)=="M") ) && ($action!="detail_user")&& ($action!="rdv")&& ($action!="ajout_rdv"))
					if (($action!="ajout_admin") && ($action!="ajout_photo")&& ($action!="ajout_organisme") && ($action!="ajout_user") )
						bouton_beneficiaire($user_idx,$user_organisme,$filtre);
			}
		// ---------------------------------------------------------------- Bloc RDV --------------------------
		if  ( ($action=="ajout_rdv") && ( ($user_droit=="S") || ($user_droit=="M") || ($user_droit=="") ) )
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
					if ($user_droit!="")
						$ligne .= "; De ".libelle_user($user_idx)." (".libelle_organisme($user_organisme).")";;
				
					command("INSERT INTO DD_rdv VALUES ('$idx', '$user1','$user_idx','$date $heure', '$ligne', '$avant', 'A envoyer', '$user_fuseau' ) ");
					ajout_log( $idx, traduire("Ajout RDV le")." $date $heure : $ligne ", $user1 );				
					}
				else
					erreur(traduire("La date doit �tre dans le futur"));
				}
			else
				erreur(traduire("Il manque des informations pour enregistrer le rendez-vous."));
			$action="rdv";
			}
			
		if ($user_droit=="") 
			if (($action!="note_sms") &&($action!="ajout_note") &&($action!="ajout_photo")&& ($action!="ajout_referent") && ($action!="ajout_admin") && ($action!="ajout_user") )
				rdv($user_idx);
				
		if ( (($user_droit=="S") || ($user_droit=="M") )  && (($action=="detail_user") || ($action=="rdv")  ) )
			{
			$user = $_SESSION['bene'];
			if ($action=="rdv")
				affiche_titre_user($user);
			rdv($user);	
			}
		//----------------------------------------------------------------------------------------------------------------

		
		if ( (($user_droit=="S") || ($user_droit=="M") ) && (($action=="detail_user") || ($action=="ajout_admin")  ) )
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
			echo "<div class=\"CSS_perso\"  ><br><center> ".traduire('Les documents et informations dans cette zone ne sont jamais visibles des r�f�rents de confiance.')." </center> ";
		
		// justificatif pour les acteurs sociaux et listage des coll�gues
		if (($user_droit!="A") && ($filtre==""))
			if (($action!="note_sms") &&($action!="ajout_note")&&($action!="rdv") && ($action!="ajout_admin") && ($action!="ajout_referent") && ($action!="ajout_organisme")  && ($action!="ajout_user"))
				{
				signet("ajout_photo");
				if (($user_droit=="") || ( ( ($user_droit=="S") ) && ($action!="detail_user") ))
					{
					bouton_upload("P-$user_idx",$user_idx);	
					if (($user_droit=="S")  && ($action!="ajout_photo"))
						{
						echo "<table><tr><td> <img src=\"images/referent.png\" width=\"35\" height=\"35\" ></td><td><ul id=\"menu-bar\">";
						echo "<li><a href=\"index.php?".token_ref("collegues")."\" > ".traduire('Mes Coll�gues')." </a></li>";
						echo "</ul></td></table><hr>";
						}
					}
				}
										
		//----------------------------------------------------------------------------- Bloc SMS et NOTES --------------
		if (($action=="ajout_note") && ($user_droit=="") )
			{
			$note= variable ('note');
			ajoute_note( $user_idx,$note );
			ajout_log( $idx, traduire("Ajout note")." : $note", $user_idx );				
			
			$action="note_sms";
			}
			
		if ($user_droit=="")
			if ( ($action!="ajout_admin")&& ($action!="rdv") &&($action!="ajout_photo") && ($action!="ajout_referent") && ($action!="ajout_organisme")  && ($action!="ajout_user"))
				affiche_sms($filtre);
		//----------------------------------------------------------------------------------------------------------------

		if (($user_droit=="") && ($action==""))
			echo "<p>.</div >";
		
		if (($action!="ajout_user")  && ($action!="ajout_organisme") )
			if ($user_droit=="A")
				bouton_affectation();	

				
		if ($user_droit!="")	
			if (($action!="ajout_affectation") &&($action!="ajout_admin") && ($action!="ajout_user") )
				if ( ($user_droit=="R") || ($user_droit=="A"))
					bouton_organisme();	
				
		pied_de_page("x");
	
		?>
	
    </body>
</html>

