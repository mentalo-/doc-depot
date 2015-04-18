<?php 

// ------------------------------------------------------
// DOC-DEPOT : COPYRIGTH ADILEOS - Décembre 2013/Mars 2015

session_start(); 

error_reporting(E_ALL | E_STRICT);



// { Début - Première partie
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
// } Fin - Première partie

// { Début - Seconde partie
if(isset($_SESSION['sauvegarde']))
	{
    $_POST = $_SESSION['sauvegarde'] ;
    $_FILES = $_SESSION['sauvegardeFILES'] ;
    unset($_SESSION['sauvegarde'], $_SESSION['sauvegardeFILES']);
	}
// } Fin - Seconde partie

?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<script src="http://code.jquery.com/jquery-1.5.1.min.js"></script> 

<script language="JavaScript">
		$(document).ready(function() { $('#msg_ok').delay(3000).fadeOut();});
		
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


 <?php
require_once 'general.php';
require_once 'inc_style.php';
require_once "connex_inc.php";


    echo "<head>";
	echo "<link rel=\"icon\" type=\"image/png\" href=\"images/identification.png\" />";
	echo "<title>Doc-Depot.com </title>";
    echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
	echo "</head><body>";

	$user_lang='fr';
	$format_date = "d/m/Y";
	$action=variable("action");
	// ------------------------------------------------------------------------------ traitement des actions sans mot de passe
	
	if (isset($_SESSION['lang']))
		$user_lang=$_SESSION['lang'];
	else
		$user_lang=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		
	if (($action=="fr") || ($action=="es") || ($action=="gb")|| ($action=="de")|| ($action=="ru") )
		{
		$_SESSION['lang'] = $action;
		$user_lang=$_SESSION['lang'];
		if (isset ($_SESSION['user_idx']))
			modification_langue($_SESSION['user_idx'], $user_lang );
		}	

	if ($action=="dx") 
		{
		if ( (isset ($_SESSION['pass_ad'])) && ($_SESSION['pass_ad']==TRUE)) 
				ajout_log( $_SESSION['user'], traduire('Déconnexion') );
		$_SESSION['pass_ad']=false;// et hop le mot de passe... poubelle !
		echo "<div id=\"msg_dx\">".traduire('Vous êtes déconnecté!')."</div><br>";
		}


	$user_telephone=variable("tel");
	$user_dept=variable("dept");		
	
	// ===================================================================== Bloc IMAGE

		debut_cadre("800");
		echo "<tr><td><a href=\"alerte.php\" > <img id=\"logo\" src=\"images//logo-alerte.jpg\" width=\"240\" height=\"180\" ></a> </td>";
		echo "<td><center><h2>".traduire('Soyez alerté en cas de risque de grand-froid ou de forte pluie').".</h2>";
		echo traduire("Vous possédez un téléphone portable, alors en remplissant le formulaire ci-dessous, vous recevrez gratuitement, pendant un an, un SMS s'il est prévu dans les 3 jours suivants :");
		echo "<br>".traduire('précipitation importantes ou des températures ressenties très basses.');
	
		echo "<h3>".traduire("Ce service est strictement réservé aux personnes qui \"vivent\" dans la rue.")."</h3>";
		echo "<FONT color=\"orange\">".traduire("Attention, la fonctionalité n'est ouverte que pour la région parisienne et ce à titre expérimental.")."</FONT> </center></td>";
		fin_cadre();
		
		debut_cadre("300");
		if ($action=="saisie")
			{
			$telephone=variable("tel");
			$dept=variable("dept");	

			if ($telephone[0]=='0')
				$telephone="+33".substr($telephone,1);
			
			$plus="";
			if ($telephone[0]=='+')
				$plus='+';
			$telephone = $plus.preg_replace('`[^0-9]`', '', $telephone);
			if (($telephone=="") || (!VerifierPortable($telephone)) )
				erreur (traduire("Format de téléphone incorrect"));
			else
//				if (($dept<1) || ($dept>99) )
				{
				$list_dept=array (78,75,91,92,93,94,95);
				if ( !in_array($dept,$list_dept ) ) 
					erreur (traduire("Numéro de département incorrect"));
				else
					{
					$r1 = command("SELECT * FROM cc_alerte WHERE  tel='$telephone' ");
					$d1 = fetch_command($r1);
					$date= date($format_date );
					$t0=time();
					$ip= $_SERVER["REMOTE_ADDR"];

					if ($d1)
						command("UPDATE `cc_alerte` SET dept='$dept' ,sueil='',stop='' ,modif='$t0', ip='$ip' where tel='$telephone'  ");
					else
						command("INSERT INTO `cc_alerte`  VALUES ( '$date', '$telephone', '$dept', '$sueil','','','','','$ip','$t0')");
					msg_ok(traduire("Mise à jour réalisée."));
					}
				}
			}
		echo "<table>";
		
		formulaire ("saisie","alerte.php");
		echo "<tr><td> ".traduire('Téléphone')." : </td><td><input type=\"texte\" name=\"tel\"   size=\"15\" value=\"$user_telephone\" > " ;
		echo "<tr><td> ".traduire('Département')." : </td><td><input type=\"texte\" name=\"dept\"   size=\"3\" value=\"$user_dept\" > " ;
		echo "<tr><td> </td><td><input type=\"submit\"  id=\"nouveau\"  value=\"".traduire('Valider')."\" > </td> ";
		echo "</form></table> ";
		fin_cadre();
		
		echo "<p>".traduire("Autre méthode: envoyez un SMS au 06.98.47.43.12 (numéro non surtaxé) contenant 'alerte' suivi du numéro de département où vous êtes.<br>(Par exemple \"alerte 75\" si vous êtes sur Paris)");
		
		echo "<center><table border=\"1\"><tr><td>";
		debut_cadre("500");
		echo "<tr><td><a href=\"index.php\" > <img id=\"logo\" src=\"images//logo.png\" width=\"140\" height=\"100\" ></a> </td>";
		echo "<td><center><h4>".traduire("La Consigne Numérique Solidaire")."</h4>";
		echo "</center>".traduire("Si vous souhaitez sauvegarder une copie de vos documents, photos et informations essentielles, vous pouvez utiliser \"doc-depot\" gratuitement. Consultez la liste des structures permettant de vous créer un compte en cliquant")." <a href=\"index.php?action=liste\" >".traduire("ici")."<a></td>";
		fin_cadre();	
		echo "</td></table>";


		echo "<br>";
		echo "<hr><center> ";

		echo "<table> <tr> <td align=\"right\" valign=\"bottom\" ></td>";
		echo "<td><a id=\"lien_conditions\" href=\"conditions_alerte.html\">".traduire('Conditions d\'utilisation')."</a>";
		echo "- <a id=\"lien_contact\" href=\"index.php?action=contact\">".traduire('Nous contacter')."</a>";
		echo "- Copyright <a href=\"http://adileos.doc-depot.com\">ADILEOS 2014</a> ";
		$version= parametre("DD_version_portail") ;
		echo "- $version ";	
		fermeture_bdd() ;
		?>
	
    </body>
</html>

