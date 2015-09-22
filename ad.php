<?php 

// ------------------------------------------------------
// DOC-DEPOT : COPYRIGTH ADILEOS - Décembre 2013/Mars 2015

session_start(); 

error_reporting(E_ALL | E_STRICT);

include 'general.php';
include 'inc_style_cal.php';

	$to=TIME_OUT_BENE;

	if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $to )) 
		$_SESSION['pass_ad']=false;
		
	$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp


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
require_once "connex_inc.php";
require_once 'include_crypt.php';


    echo "<head>";
	echo "<link rel=\"icon\" type=\"image/png\" href=\"images/identification.png\" />";
	echo "<title>Doc-Depot.com </title>";
    echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
	
	$refr=TIME_OUT+10;
	echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"$refr\">";

	echo "</head><body>";

	$user_lang='fr';

	// ------------------------------------------------------------------------------ traitement des actions sans mot de passe
	$action=variable("action");	

	
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

// ------------------------------------------------------------------------------ FIN des actions sans identification (pas de mot de passe)		


// ---------------------------------------on récupére les information de la personne connectée
if (isset($_POST['id']))
	{
	$organisme=variable('org');
	$id=variable('id');
	$reponse = command("SELECT * from  r_organisme WHERE  organisme='$organisme' "); 
	$_SESSION['pass_ad']=false;

	if ($donnees = fetch_command($reponse))
		{
		$idx_org= $donnees["idx"];
		$reponse = command("SELECT * from  cc_user WHERE (tel='$id' or mail='$id' or nom='$id' or prenom='$id' ) and organisme='$idx_org' "); 
		if ( $donnees = fetch_command($reponse) ) 
			{
			$idx_usager=$donnees["idx"];
			$user_type=$donnees["type"];	
			if ( !( $donnees = fetch_command($reponse) )  )
				{			
				$_SESSION['pass_ad']=true;	 
				$_SESSION['user']=$idx_usager;	 
				$_SESSION['ad']=true;	 
				$_SESSION["mois"]=0;
				}
			else
				erreur(traduire("Plusieurs personnes correspondent à cet identifiant")." !!");
			}
		else
			erreur(traduire("Identifiant inconnu")." !!");
		}
	else
		erreur(traduire("Structure inconnue")." !!"); 

	}

	if ( !isset($_SESSION['pass_ad']) ||($_SESSION['pass_ad']==false) || !(isset($_SESSION['user'])) || ($_SESSION['user']=="") )
		// si pas de valeur pass en session on affiche le formulaire...
		{
		echo "<div id=\"logo\"> <br><p><center><a href=\"ad.php\"><img src=\"images/calendrier.jpg\" width=\"200\" height=\"150\" ></a> </div>  <center>";	
		echo "<p><br><p><p>";
		debut_cadre();
		echo "<br><p><TABLE><TR> <td>";
		
		if (isset($idx_org) ) 
			$libelle_org=libelle_organisme($idx_org);
		else
			$libelle_org="";
		echo "<TABLE><TR> <td><form class=\"center\"  method=\"post\"> ".traduire('Structure').": </td><td><input required type=\"text\" name=\"org\" value=\"$libelle_org\"/></td>";
		echo "<TR> <td>".traduire('Identifiant').": </td><td><input required  id=\"id\"  type=\"text\" name=\"id\"  autocomplete=\"off\" value=\"\"/>";
		echo "<input  type=\"hidden\" name=\"action\" value=\"\"/></td>";
		echo "<TR> <td></td><td><input type=\"submit\" value=\"".traduire('Se connecter')."\"/><p></td>";
		echo "</form> </table> </table> ";
		fin_cadre();
		echo "<br><p><br><p><p>";
		echo "<p><br></center></div>";
		pied_de_page();
		} 
		
	// ------------------------------------ on collecte les infos utiles du user connceté
	if (isset($_SESSION['user']))
		{
		$idx_usager=$_SESSION['user'];
		$reponse = command("SELECT * from  cc_user WHERE idx='$idx_usager'  "); 
		$donnees = fetch_command($reponse);

		$user_nom=$donnees['prenom'];
		$user_prenom=$donnees['nom'];
		$user_telephone=$donnees['tel'];
		$user_mail=$donnees['mail'];
		$user_droit=$donnees['type'];
		$user_organisme=$donnees['organisme'];
		}

	$action=variable("action");
		
	// ===================================================================== Bloc IMAGE
		echo "<table border=\"0\" >";	
		echo "<tr> <td> ";		
		echo "<a href=\"ad.php\" > ";
		echo "<img id=\"logo\" src=\"images//calendrier.jpg\" width=\"140\" height=\"100\" ></a> </td> ";	
		if ($_SERVER['REMOTE_ADDR']=="127.0.0.1")
			echo "<td> <table><tr> <td align=\"center\" > <table><tr><td align=\"center\" bgcolor=\"lightgreen\" ><b>$user_nom - $user_prenom </b></td>";
		else
			echo "<td> <table><tr><td align=\"center\"> <table><tr><td align=\"center\" ><b>$user_nom - $user_prenom </b></td>";
			
		echo "</table></td>";
		
		echo "<tr><td><hr>";

		if ($action=="modif_tel")
			{
			if (modif_tel(variable("idx"), variable("telephone"),variable("telephone2")) )
				{
				$user_telephone=variable("telephone");
				$user_mail=variable("telephone2");	
				}
			}
		
		echo "</td><tr> ";

		if (($action=="") || ($action=="modif_tel"))
			{
			formulaire ("modif_tel","ad.php");
			echo "<input type=\"hidden\" name=\"idx\" value=\"$idx_usager\"> " ;
			echo "<td> <img src=\"images/telephone.png\" width=\"25\" height=\"25\" > ".traduire('Tel')." :<input type=\"texte\" name=\"telephone\"   size=\"15\" value=\"$user_telephone\" onChange=\"this.form.submit();\"> " ;
			echo " - <img src=\"images/mail.png\" width=\"25\" height=\"25\" > ".traduire('Mail')." :<input type=\"texte\" name=\"telephone2\"   size=\"30\" value=\"$user_mail\" onChange=\"this.form.submit();\"> " ;
			echo "</form></table> </td>";
			}
		else
			{
			echo "<td> <img src=\"images/telephone.png\" width=\"25\" height=\"25\" > ".traduire('Tel').": $user_telephone " ;
			echo " - <img src=\"images/mail.png\" width=\"25\" height=\"25\" > ".traduire('Mail').": $user_mail" ;
			echo "</table> </td>";
			}
	
		echo "<td> <ul id=\"menu-bar\">";
		echo "<li><a href=\"ad.php?action=dx\"  > ".traduire('Déconnexion')."</a>";
		echo "</ul >";
		echo "</td>";
	
		echo "</table>";
		echo "<hr>";
		
		if ($action=="") 
			$action="cc_activite";


		include 'planning2.php';			
				
		pied_de_page("x");
	
		?>
	
    </body>
</html>

