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
// DOC-DEPOT : COPYRIGTH ADILEOS - D�cembre 2013/Mars 2015

session_start(); 

error_reporting(E_ALL | E_STRICT);

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
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<?php include 'header.php';	  ?>

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
	echo "<title>Alerte SMS Canicule </title>";
    echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
	echo "</head><body>";

	$user_lang='fr';
	$format_date = "d/m/Y";
	$token=variable("token");	
	if ($token!="")	
		$action=verifi_token($token,variable("action"));
	else
		$action=variable("action");		
	//$action=variable_s("action");	
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
				ajout_log( $_SESSION['user'], traduire('D�connexion') );
		$_SESSION['pass_ad']=false;// et hop le mot de passe... poubelle !
		echo "<div id=\"msg_dx\">".traduire('Vous �tes d�connect�!')."</div><br>";
		}


	$user_telephone=variable("tel");
	$user_dept=variable("dept");		
	
	// ===================================================================== Bloc IMAGE

		debut_cadre("800");
		echo "<tr><td><a href=\"alerte_canicule.php\" > <img id=\"logo\" src=\"images/canicule.jpg\" width=\"240\" height=\"180\" ></a> </td>";
		echo "<td><center><h2>".traduire('Soyez alert� en cas de risque de canicule').".</h2>";
		echo traduire("Vous poss�dez un t�l�phone portable, alors en remplissant le formulaire ci-dessous, <br>vous recevrez gratuitement, pendant un an, un SMS s'il est pr�vu dans les 3 jours suivants des temp�ratures tr�s �lev�es.");
		echo " <p>( Temp�rature Max en journ�e > � ".parametre("DD_seuil_canicule")."�C  et la temp�rature nocturne > � ".parametre("DD_seuil_canicule_nuit")."�C ) <p>";
		echo "<br><p><br>";
	
		echo "<FONT color=\"orange\">".traduire("Attention, la fonctionalit� n'est ouverte que pour le Nord-Pas-de-Calais et ce � titre exp�rimental.")."</FONT> </center></td>";
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
				erreur (traduire("Format de t�l�phone incorrect"));
			else
//				if (($dept<1) || ($dept>99) )
				{
				$list_dept=array (59,62);
				if ( !in_array($dept,$list_dept ) ) 
					erreur (traduire("Num�ro de d�partement incorrect"));
				else
					{
					$r1 = command("SELECT * FROM cc_alerte_canicule WHERE  tel='$telephone' ");
					$d1 = fetch_command($r1);
					$date= date($format_date );
					$t0=time();
					$ip= $_SERVER["REMOTE_ADDR"];

					if ($d1)
						command("UPDATE `cc_alerte_canicule` SET dept='$dept' ,sueil='',stop='' ,modif='$t0', ip='$ip' where tel='$telephone'  ");
					else
						command("INSERT INTO `cc_alerte_canicule`  VALUES ( '$date', '$telephone', '$dept', '','','','','','$ip','$t0')");
					msg_ok(traduire("Mise � jour r�alis�e."));
					ajout_log( "", "Inscription Alerte canicule $telephone ($dept) via web ");

					}
				}
			}
		echo "<table>";
		
		formulaire ("saisie","alerte_canicule.php");
		echo "<tr><td> ".traduire('T�l�phone')." : </td><td><input type=\"texte\" name=\"tel\"   size=\"15\" value=\"$user_telephone\" > " ;
		echo "<tr><td> ".traduire('D�partement')." : </td><td><input type=\"texte\" name=\"dept\"   size=\"3\" value=\"$user_dept\" > " ;
		echo "<tr><td> </td><td><input type=\"submit\"  id=\"nouveau\"  value=\"".traduire('Valider')."\" > </td> ";
		echo "</form></table> ";
		fin_cadre();
		
		echo "<p>".traduire("Autre m�thode: depuis le t�l�phone devant recevoir les alertes, envoyez un SMS au 06.98.47.43.12 (num�ro non surtax�)<br>contenant 'canicule' suivi du num�ro de d�partement o� vous �tes<br>(Par exemple \"canicule 62\" si vous �tes sur Calais)");
		
		

		echo "<br><p><br><p><br><p><br><p><br>";
		echo "<hr><center> ";

		echo "<table> <tr> <td align=\"right\" valign=\"bottom\" ></td>";
		echo "<td><a id=\"lien_conditions\" href=\"conditions_alerte.html\">".traduire('Conditions d\'utilisation')."</a>";
		echo "- <a id=\"lien_contact\" href=\"http://adileos.jimdo.com/contact\">".traduire('Nous contacter')."</a>";
		echo "- Copyright <a href=\"http://adileos.doc-depot.com\">ADILEOS 2015</a> ";
		$version= parametre("DD_version_portail") ;
		echo "- $version ";	
		fermeture_bdd() ;
		?>
	
    </body>
</html>

