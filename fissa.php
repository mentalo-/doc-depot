<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0trict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">


    <head>
	 <?php
		if (isset ($_GET["action"])) $action=$_GET["action"]; else  	$action="";	
		if ((isset ($_GET["nom"])) && ($action=="suivi"))
			{
			$nom = $_GET["nom"];
			echo "<title> $nom </title>";
			}
	     else
			echo "<title> FISSA </title>";
		?> 
		
		
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
 <?php
include 'calendrier.php';
include 'general.php';
include 'inc_style.php';
    echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
?> 		    
		</head>
		<body>
	
       <?php 
	   
	   
		$user_lang='fr';
		
		$nom=array();
		$liste_nom=array();
		$pres_repas=array();
		$qte=array();
		$commentaire=array();
		$nb_usager=100;
	
	$bdd = variable_s("support");
	if ($bdd!="") 
		$_SESSION['support']=$bdd;
	else
//		$bdd=$_SESSION['support'];
			$bdd='effectif';
		
	// ConnexiondD
	include "connex_inc.php";
	
	$cmd= "SELECT * FROM fct_fissa WHERE support='$bdd' ";
	$reponse = mysql_query($cmd); 
	$donnees = mysql_fetch_array($reponse);
	
	$beneficiaire=$donnees["beneficiaire"];
	if ($beneficiaire=="") $beneficiaire="Bénéficiaires";
		
	$acteur=$donnees["acteur"];
	if ($acteur=="") $acteur="Accueillants";
	
	$libelle=$donnees["libelle"];

	/*
	$logo = variable_s("logo");
	if ($logo!="") 
		$_SESSION['logo']=$logo;
	else
		$logo=$_SESSION['logo'];	
		*/
	$logo="";
	

// ---------------------------------------------------------------------------------------	
	function charge_date($d)
		{
		global $imax, $nom, $pres_repas, $qte, $nb_usager,$commentaire, $synth,$bdd;

		for($i=0;$i<$nb_usager;$i++) 
			{
			$nom[$i]="";
			$pres_repas[$i]="";
			$commentaire[$i]="";
			}
		$i=0; 
		
		$reponse = mysql_query("SELECT DISTINCT * FROM $bdd WHERE date='$d' and pres_repas!='Suivi' "); 
		while (($donnees = mysql_fetch_array($reponse) ) && ($i<10000))
			if ($donnees["nom"]!="Synth")
				{
				$nom[$i]=$donnees["nom"];

				$pres_repas[$i]=$donnees["pres_repas"];
				$qte[$i]=$donnees["qte"];
				$commentaire[$i]=stripcslashes($donnees["commentaire"]);
				$i++; 		
				}
			else
				$synth=$donnees["commentaire"];
		$imax=$i;

		}
	
	function charge_nom()
		{
		global $jmax, $liste_nom, $bdd;
		
		for($i=0;$i<200;$i++) $liste_nom[$i]="";
		$j=0; 		
		
		$reponse = mysql_query("select nom from $bdd group by nom ASC");
			
		while (($donnees = mysql_fetch_array($reponse) ) && ($j<10000))
			if ($donnees["nom"]!="Synth")
				{
				$liste_nom[$j]=$donnees["nom"];
				$j++; 		
				}
		$jmax=$j;
		}	
	
	// =========================================================== procedures générales

	
function nouveau( $d, $nom, $pres,$com ,$memo,$qte="1")
		{
		global $bdd;
			
		if ($nom!="")
			{
			$r1 = mysql_query("SELECT DISTINCT count(*) FROM $bdd WHERE date='0000-00-00' and nom='$nom' ");
			$r2=mysql_fetch_row($r1); 
			if ($r2[0]==0)
				{
				$cmd = "INSERT INTO `$bdd`  VALUES ( '$nom', '', '','','$qte')";
				$reponse = mysql_query($cmd);
				}
				
			$r1 = mysql_query("SELECT  count(*) FROM $bdd WHERE date='$d' and nom='$nom' ");
			$r2=mysql_fetch_row($r1); 
			$r=$r2[0];	
			$com= addslashes2($com);
			$nom= addslashes2($nom);
			$memo= addslashes2($memo);
			if ($r==0)
				{
				$cmd = "INSERT INTO `$bdd`  VALUES ( '$nom', '$d', '$pres','$com','$qte')";
				$reponse = mysql_query($cmd);
				}
			else
				{
				$cmd = "UPDATE $bdd set commentaire='$memo' where nom='$nom' and date='0000-00-00' ";
				$reponse = mysql_query($cmd);				
				
				if ( $pres!="Erreur saisie")
					$cmd="UPDATE $bdd SET commentaire='$com', pres_repas='$pres' , qte='$qte' WHERE nom='$nom' AND date='$d'" ;
				else
					$cmd="DELETE FROM $bdd  WHERE nom='$nom' AND date='$d'" ;
							
				$reponse = mysql_query($cmd);
				}
			}
		}

function chgt_nom(  $nom, $nouveau)
		{
		global $bdd;
		
		if ($nom!="")
			{
			$nouveau= addslashes2($nouveau);
			$cmd="UPDATE $bdd SET nom='$nouveau' WHERE nom='$nom' " ;
			$reponse = mysql_query($cmd);
			}
		}	

function liste_presence( $val_init , $nom ="")
		{
		global $acteur;
		
		echo "</td> <td> ";
		echo "<SELECT name=\"presence\"  onChange=\"this.form.submit();\">";
		if ((!strstr($nom,"(A)")) && (!strstr($nom,"(B)")) && (!strstr($nom,"(S)")) )		
			{
			affiche_un_choix($val_init,"Visite");
			affiche_un_choix($val_init,"Visite+Repas");
			affiche_un_choix($val_init,"Refusé");
			}
		else
			{
			if ((!strstr($nom,"(B)")) && (!strstr($nom,"(S)")) )	
				affiche_un_choix($val_init,"Activité");	
			else
				affiche_un_choix($val_init,"$acteur");
			}
		affiche_un_choix($val_init,"Pour info");

		affiche_un_choix($val_init,"Erreur saisie");
		echo "</SELECT>";
		}
		
	function liste_type($val_init ="" )
		{
		echo "<SELECT name=\"type\"  onChange=\"this.form.submit();\">";
		affiche_un_choix($val_init,"Bénéficiaire");
		affiche_un_choix($val_init,"Bénéficiaire femme");
		affiche_un_choix($val_init,"Bénévole");
		affiche_un_choix($val_init,"Salarié");
		affiche_un_choix($val_init,"Activité");
		echo "</SELECT>";
		}

function choix_qte( $val_init )
		{
		echo "<SELECT name=\"qte\"  onChange=\"this.form.submit();\">";
		for ($i=1; $i<16; $i++)
			affiche_un_choix($val_init,"$i");
		echo "</SELECT>";
		}
	function statistic()
		{
		global $bdd;
		
		$i=0; 
		echo "<BR> ";
		$reponse = mysql_query("SELECT  * FROM $bdd order by date ASC"); 
		while (($donnees = mysql_fetch_array($reponse) ) && ($i<10000))
			if ($donnees["nom"]!="Synth")
				if ((strstr($donnees["pres_repas"],"Visite")) || (strstr($donnees["pres_repas"],"Refusé")) )
					{
					$nom=$donnees["nom"];
					$pres_repas=$donnees["pres_repas"];
					$date=$donnees["date"];
					echo "$date;$nom;$pres_repas;<p>";
					$i++; 		
					}
		}

		
	function presents($date)
		{
		global $bdd;

		$t="";
		$i=0; 
		$reponse = mysql_query("SELECT * FROM $bdd WHERE date='$date' and nom <>'Synth' and ( pres_repas='Visite+Repas' or pres_repas='Visite' or pres_repas='Refusé' ) group by nom ASC"); 
		while (($donnees = mysql_fetch_array($reponse) ) && ($i<10000))
				{
				$nom=$donnees["nom"];
				$t = $t."$nom; ";

				$i++; 		
				}
		return $t;
		}

	function accueillants($date)
		{
		global $bdd,$acteur;

		$t="";
		$i=0; 
		$reponse = mysql_query("SELECT * FROM $bdd WHERE date='$date' and nom <>'Synth' and (pres_repas='$acteur' ) group by nom ASC"); 
		while (($donnees = mysql_fetch_array($reponse) ) && ($i<10000))
				{
				$nom=$donnees["nom"];
				$t = $t."$nom; ";
				//echo "$nom; ";
				$i++; 		
				}
		return $t;
		}
		
	function proposition($profil)
		{
		global  $date_jour, $bdd, $acteur;
		
		$l= date('Y-m-d',  mktime(0,0,0 , date("m")-3, date("d"), date ("Y")));
		
		$reponse = mysql_query("SELECT *,count(*) as TOTAL FROM $bdd where date>'$l' and nom<>'Synth'  group by nom order by nom limit 40 "); 
		
		while ($donnees = mysql_fetch_array($reponse) ) 
				{
				$n=$donnees["nom"];
				$r2 = mysql_query("SELECT * FROM $bdd WHERE date='$date_jour' and nom='$n' "); 
				if (! mysql_fetch_array($r2))
					{
					if ($profil!="")
						{
						if (strpos($n,"$profil")!==FALSE)
							{
							if ( (strpos($n,"(B)")!==FALSE) ||(strpos($n,"(S)")!==FALSE) )
								echo "<a href=\"fissa.php?action=nouveau&qte=1&date_jour=$date_jour&nom=$n&memo=&presence=Acteur+Social&commentaire=\">$n</a>; " ;
						if ( (strpos($n,"(A)")!==FALSE))
								echo "<a href=\"fissa.php?action=nouveau&qte=1&date_jour=$date_jour&nom=$n&memo=&presence=Activité&commentaire=\">$n</a>; " ;
							}
						}
					else
						if ( (strstr($n,"(B)")===FALSE) &&(strstr($n,"(S)")===FALSE) && (strstr($n,"(A)")===FALSE))
							echo "<a href=\"fissa.php?action=nouveau&qte=1&date_jour=$date_jour&nom=$n&memo=&presence=Visite&commentaire=\">$n</a>; " ;
					
					}

				}
		


		}

	function rapport($date)
		{
		global $nb_usager,$nom,$commentaire,$imax, $pres_repas, $qte, $bdd, $acteur, $beneficiaire, $libelle;
		
		$i=0; 
				
		echo "<form method=\"GET\" action=\"fissa.php\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"mail\"> " ;
		echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date\">";
		echo "<input type=\"submit\" value=\">>> Envoi Mail Synthèse et Rapport d'activité >>>\" >";	
			
		echo "<table border=\"0\" >";	
		echo "<tr> <td width=\"600\"> ";

		$r1 = mysql_query("SELECT DISTINCT pres_repas,count(*) FROM $bdd WHERE date='$date' and nom <>'Synth'  ");
		$r2=mysql_fetch_row($r1); 
		$r=$r2[0];
		/*
		$r1 = mysql_query("SELECT DISTINCT count(*) FROM $bdd WHERE date='$date' and nom <>'Synth' and pres_repas='Visite+Repas' ");
		$r2=mysql_fetch_row($r1); 
		$r3=$r2[0];
		*/
		$txt="FISSA : Rapport d'activité du $date concernant '$libelle' <br>";
		echo "<BR>$txt";

//		echo "<BR><BR><b>$beneficiaire : </b>";	
//		echo presents($date);
	
		echo "<BR><b>Synthèse : </b><BR>";
		$i=0; 
		$reponse = mysql_query("SELECT * FROM $bdd WHERE date='$date' and nom='Synth' "); 
		while (($donnees = mysql_fetch_array($reponse) ) && ($i<10000))
				{
				$c=$donnees["commentaire"];
				$c=nl2br (stripcslashes($c));
				echo "$c ";
				$i++; 		
				}
				
		echo "<BR><BR><b>$acteur : </b>";
		echo accueillants($date);
				
		echo "<BR><BR><b> Détails : </b> ";		
		for($i=0;$i<$imax;$i++)
			if (($nom[$i]!="") /* &&  ($commentaire[$i]!="") */ )
				if (strpos($nom[$i],"(B)")===FALSE)
					if (strpos($nom[$i],"(S)")===FALSE)
							{
							echo "<BR> ";
							$valeur=$nom[$i];
							echo "- $valeur ";
							
							$valeur=$pres_repas[$i];
							echo "($valeur) : ";

							$valeur=$qte[$i];
							//echo "( X $valeur) : ";
							$valeur=$commentaire[$i];
							echo " $valeur";
							}
				
		echo "<BR><BR> </td></table>";

		}

function date_getMicroTime() 
	{
	list($usec, $sec) = explode(' ', microtime());
	return ((float) $usec + (float) $sec);
	}

	function mail2($dest, $titre, $contenu, $headers)
		{
		$total = 0;
		$n=0;
		while(($total < 1.5) && ($n<20))
			{
			$start = date_getMicroTime();
			for($i = 0 ; $i < 999999 ; $i++) 1; // Temps de pause entre chaque tentative, carleep() n'est pas dispour Free.fr
				mail ( $dest , $titre, $contenu,$headers );
			$total = round(date_getMicroTime() - $start, 3);
			$n++;
			}
		echo " ($n) ";
		return ($n<20);
		}
	
	
	function envoi_mail($date)
		{
		global $nb_usager,$nom,$commentaire,$imax, $pres_repas, $bdd, $libelle;
		
		$reponse = command("","SELECT * FROM fct_fissa WHERE support='$bdd' "); 
		if ($donnees = mysql_fetch_array($reponse) )
			{
			$idx=$donnees["organisme"];
			
			$reponse = command("","SELECT * FROM r_organisme WHERE idx='$idx' "); 
			if ($donnees = mysql_fetch_array($reponse) )
				$mail_struct=$donnees["mail"];
			
			$dest="";
			$reponse = mysql_query("SELECT * FROM r_user WHERE droit='R' and organisme='$idx' "); 
			while ($donnees = mysql_fetch_array($reponse) ) 
				$dest.=$donnees["mail"].";";				
			}
			
		$headers  = 'MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8'. "\r\n";
		$headers  .= "From: FISSA $libelle <$mail_struct>" . "\r\n"; 

		Echo "Envoi mails de la journée du $date";
		$i=0; 
				
		$r1 = mysql_query("SELECT DISTINCT count(*) FROM $bdd WHERE date='$date' and nom <>'Synth' and ( pres_repas='Visite+Repas' or pres_repas='Visite' or pres_repas='Refusé' ) ");
		$r2=mysql_fetch_row($r1); 
		$r=$r2[0];
		
		$r1 = mysql_query("SELECT DISTINCT count(*) FROM $bdd WHERE date='$date' and nom <>'Synth' and pres_repas='Visite+Repas' ");
		$r2=mysql_fetch_row($r1); 
		$r3=$r2[0];
		
		$synth ="$libelle : aujourd'hui, $date, $r personnes accueillies, dont $r3 repas.";
		
		Echo "<BR><BR>- Envoi Synthèse $bdd à $dest : ";
		
		if  (mail2 ( $dest , "FISSA $libelle (synthèse) : $date ", "$synth", $headers   )) echo "OK"; else echo "Echec";
		
		$txt= $synth. "\n\nPrésents : ";	
		$txt= $txt . presents($date);
		
		$txt= $txt ."\n\nAccueillants :" ;
		$txt= $txt . accueillants($date);
		
		$i=0; 
		$txt=$txt ."\n\nSynthèse : ";
		$reponse = mysql_query("SELECT * FROM $bdd WHERE date='$date' and nom='Synth' "); 
		while (($donnees = mysql_fetch_array($reponse) ) && ($i<10000))
				{
				$c=$donnees["commentaire"];
				$txt = $txt." $c" ;
				$i++; 		
				}
		
		$txt = $txt. "\n\n Détails :  ";		
		for($i=0;$i<$imax;$i++)
			if (($nom[$i]!="") &&  ($commentaire[$i]!=""))
				if (!strstr($pres_repas[$i],"(B)"))
					if (!strstr($pres_repas[$i],"(S)"))
							{
							$valeur=$nom[$i];
							$txt = $txt. "\n\n- $valeur :  ";
							$valeur=$commentaire[$i];
							$txt = $txt. " $valeur ; ";
							}

		Echo "<BR><BR>- Envoi rapport detaillé à $dest : ";
		if  (mail2 ( $dest , "FISSA $libelle (détails) : $date", "$txt", $headers   )) echo "OK"; else echo "Echec";
		
		$date_jour=date('Y-m-d');
		nouveau($date,"Mail", "Mail","Envoyé le $date_jour","");
		}
		
	function affiche_memo()
		{
		global $bdd;

		$date_jour=date('Y-m-d');
		$i=0; 
		$reponse = mysql_query("SELECT * FROM $bdd WHERE commentaire<>'' and date='0000-00-00' order by nom DESC "); 
		while (($donnees = mysql_fetch_array($reponse) ) && ($i<10000))
				{
				if ($i==0)
					echo "<b>Memo: </b> ";
				$c=$donnees["commentaire"];
				$n=$donnees["nom"];
				echo "<BR><a href=\"fissa.php?action=suivi&nom=$n&date_jour=$date_jour\" target=_blank> <b>$n</b> </a> : $c";
				$i++; 		
				}
		}
		
	function histo($nom,$detail)
		{
		global $bdd;

		$i=0; 
		if ($detail=="")
			$reponse = mysql_query("SELECT * FROM $bdd WHERE nom='$nom' and date<>'0000-00-00' and pres_repas<>'pda' order by date DESC "); 
		else
			$reponse = mysql_query("SELECT * FROM $bdd WHERE nom='$nom' and date<>'0000-00-00' and pres_repas='Suivi' order by date DESC "); 
		
		while (($donnees = mysql_fetch_array($reponse) ) && ($i<10000))
				{
				$c=$donnees["commentaire"];
				$d=$donnees["date"];
				$p=$donnees["pres_repas"];
				$c=nl2br ($c);
				if ($p=="Suivi") 
					echo "<BR><b>$d ($p) : $c </b>"; 
				else
					echo "<BR>$d ($p) : $c ";
				$i++; 		
				}
		
			}
			

	// -======================================================================aisie


	
/*
if(isset($_POST['pass']))
	{
	$id=$_POST['id'];
	$reponse = mysql_query("SELECT * FROM user WHERE id='$id'"); 
	$donnees = mysql_fetch_array($reponse);
	$mot_de_passe=$donnees["pw"];
	$date_log=date('Y-m-d');	
	// verifioni la variable = mot de passe...
		if ($_POST['pass']==$mot_de_passe) 
			{
			$_SESSION['pass']=true;	 
			}
			else
			{
			echo '<p class="center"tyle="font-size:16px;font-weight:bold;color:red">Mot de passe incorrect !</span><br />'. $_SESSION['pass']=false;
			}

	}
	
if ( !isset($_SESSION['pass']) ||($_SESSION['pass']==false) )
	//i pas de valeur pass en session on affiche le formulaire...
	{
	echo "<img src=\"cafe115.jpg\" width=\"250\" height=\"100\" >  ";	
	echo "<p><TABLE><TR> <td><form class=\"center\" action=\"#\" method=\"post\"> Identifiant: </td><td><input class=\"center\" type=\"text\" name=\"id\" value=\"\"/></td>";
	echo "<TR> <td>Mot de passe: </td><td><input class=\"center\" type=\"password\" name=\"pass\" value=\"\"/><input type=\"submit\" value=\"Go\"/></td>";
	echo "</form> </table> ";
	echo "<p><a href=\"fissa.php?action=dde_mdp\" > (Si vous avez oublié votre mot de passe, cliquez ici )</a><p>";
	exit;
	} // mot de pass invalide =>TOP la page ne'affiche pas ! 
	
*/	
	if (isset ($_GET["memo"])) $memo=$_GET["memo"]; else  	$memo="";	
	
	if (!isset ($_GET["date_jour"]))
		$date_jour=date('Y-m-d');
	else 
		$date_jour=$_GET["date_jour"];
	
	if (isset ($_GET["action"]))
		{
		$action=$_GET["action"];
		
		if ($action=="nouveau")
			{
			$nom1= variable_s("nom");
			$type= variable_s("type");
			
			if ($type=="Bénéficiaire femme")
				$nom1 .= " (F)";
			if ($type=="Bénévole")
				$nom1 .= " (B)";
			if ($type=="Salarié")
				$nom1 .= " (S)";
			if ($type=="Activité")
				$nom1 .= " (A)";		
			
			
			nouveau($date_jour,$nom1, $_GET["presence"],$_GET["commentaire"],$memo);
			}

		if ($action=="chgt_nom")
			chgt_nom($_GET["nom"],$_GET["nouveau"]);
			
		if ($action=="rapport")
			{
			charge_date($date_jour);
			rapport($date_jour);
			}
		
		if ($action=="mail")
			{
			charge_date($date_jour);
			envoi_mail($date_jour);
			}

		}
	else
		$action="";

	$date_log=date('Y-m-d')." ".date("H\hi.s");
	
	charge_nom();	
	switch ($action)
	{
	case "cnil":
				echo "<form method=\"GET\" action=\"fissa.php\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"cnil\"> " ;
				Echo "CNIL : Informations sur accueilli ";
				echo "<table border=\"0\" >";	
				echo "<tr> <td> ";	
				echo "<SELECT name=nom>";
				echo "<OPTION  VALUE=\"\">  </OPTION>";
				for ($j=0;$j<$jmax;$j++)
					{
					$sel=$liste_nom[$j];
					if ($sel!= "Mail") 
						echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
					}
				echo "</SELECT>";
				echo "</table >";	
				echo "<input type=\"submit\" value=\"Selectionner\" > </td> </form>  ";
					
				if (isset ($_GET["nom"])) $nom=$_GET["nom"]; else  	$nom="";	
				if ($nom!="")
					{
					echo "<table border=\"0\" >";	
					echo "<tr> <td> Identifiant : </td> <td> $nom </td> ";	
					echo "</table >";					

					$reponse = mysql_query("SELECT * FROM $bdd WHERE nom='$nom' and date<>'0000-00-00' order by date DESC "); 
					while ($donnees = mysql_fetch_array($reponse) ) 
							{
							$d=$donnees["date"];
							$p=$donnees["pres_repas"];
							echo "<BR>$d : $p ";
							}
					}
					
				break;
				
	case "rapport":
	case "mail":
				echo "<p><a href=\"javascript:window.close();\">Fermer la fenêtre</a>"; 
				break;
				
	case "suivi":
	case "accompagnement":
	case "pda":
	
			// =====================================================================locelection
			if (isset ($_GET["pda"])) $pda=$_GET["pda"]; else $pda="";
			if (isset ($_GET["nom"])) $nom=$_GET["nom"]; else $nom="";
			if (isset ($_GET["com"])) $com=$_GET["com"]; else $com="";			
			if ($nom!="")
				{
				if ($nom!="Synth")
					{
					echo "<form method=\"GET\" action=\"fissa.php\">";
					echo "<input type=\"hidden\" name=\"action\" value=\"$action\"> " ;
					echo "<input type=\"hidden\" name=\"nom\" value=\"$nom\"> " ;
					echo "<tr> <td> ";
					echo "<input type=\"text\" name=\"date_jour\" size=\"10\" value=\"$date_jour\" class=\"calendrier\"></td>";
					echo "<td>"; 
					echo "<input type=\"submit\" value=\"Selectionner\" > </td> </form>  ";
					
					echo "<p>Suivi et Accompagnement de <b> $nom </b>";	
				
					if ($action!="accompagnement")
						echo "<a href=\"fissa.php?action=accompagnement&nom=$nom&date_jour=$date_jour\" > ( N'afficher que l'accompagnement )</a><p>";
					else
						echo "<a href=\"fissa.php?action=suivi&nom=$nom&date_jour=$date_jour\" > ( Afficher tout l'historique )</a><p>";
					
					}
				else
					echo "<p> Historique des faits marquants <p>";	
		
				if ($com=="")
					{
					$reponse = mysql_query("SELECT * FROM $bdd WHERE date='$date_jour' and nom='$nom' and pres_repas='Suivi' "); 
					if ($donnees = mysql_fetch_array($reponse))
						$commentaire=stripcslashes($donnees["commentaire"]);
					else
						$commentaire="";
					}
				else
					{
					$com=addslashes2($com);
					$reponse = mysql_query("SELECT * FROM $bdd WHERE date='$date_jour' and nom='$nom' and pres_repas='Suivi' "); 
					
					if ($donnees = mysql_fetch_array($reponse))
						$reponse = mysql_query("UPDATE $bdd set commentaire='$com' where nom='$nom' and date='$date_jour' and pres_repas='Suivi' ");
					else
						$reponse = mysql_query("INSERT INTO `$bdd`  VALUES ( '$nom', '$date_jour', 'Suivi','$com','1')");					
					$commentaire=$com;
					}

				if ($pda=="")
					{
					$reponse = mysql_query("SELECT * FROM $bdd WHERE nom='$nom' and pres_repas='pda' "); 
					if ($donnees = mysql_fetch_array($reponse))
						$pda=stripcslashes($donnees["commentaire"]);
					else
						$pda="";
					}
				else
					{
					$pda=addslashes2($pda);
					$reponse = mysql_query("SELECT * FROM $bdd WHERE nom='$nom' and pres_repas='pda' "); 
					
					if ($donnees = mysql_fetch_array($reponse))
						$reponse = mysql_query("UPDATE $bdd set commentaire='$pda' where nom='$nom' and pres_repas='pda' ");
					else
						$reponse = mysql_query("INSERT INTO `$bdd`  VALUES ( '$nom', '$date_jour', 'pda','$pda','1')");					
					$pda=stripcslashes($pda);
					}					
				
				if ($nom!="Synth")
					{
					echo "<P> <table border=\"2\" >";
					echo "<tr> <td> ";
					echo "<form method=\"GET\" action=\"fissa.php\">";
					echo "<input type=\"hidden\" name=\"action\" value=\"suivi\"> " ;
					echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
					echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
					echo "<TEXTAREA rows=\"4\" cols=\"110\" name=\"com\">$commentaire</TEXTAREA>";
					echo "</td> <td>";
					echo "<input type=\"submit\" value=\"MaJ Suivi\" >  ";
					echo "</form> ";
					echo "<tr> <td> ";
					echo "<form method=\"GET\" action=\"fissa.php\">";
					echo "<input type=\"hidden\" name=\"action\" value=\"pda\"> " ;
					echo "<input type=\"hidden\" name=\"nom\"  value=\"$nom\">";
					echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
					echo "<TEXTAREA rows=\"4\" cols=\"110\" name=\"pda\">$pda</TEXTAREA>";
					echo "</td> <td>";
					echo "<input type=\"submit\" value=\"MaJ Plan d'action\" >  ";
					echo "</form> ";
					echo "</table>  ";
					}
				echo "<a href=\"javascript:window.close();\">Fermer la fenêtre</a>"; 
				if  (($action=="suivi") || ($action=="pda"))
					histo($nom,"");
				else
					histo($nom,"accompagnement");
				break;
				}
			
	default:
			// =====================================================================loc IMAGE
			echo "<table border=\"0\" >";	
			echo "<tr> <td> <a href=\"index.php\"> <img src=\"images/logo.png\" width=\"200\" height=\"100\"  > </a> </td> ";		

			charge_date($date_jour);

			echo "<td><table> <tr><td>  </td><td>  </td><td>  </td><td>  </td><td>  </td>";
			echo "<td><a href=\"\"> <img src=\"images/fissa.jpg\" width=\"200\" height=\"40\"> $libelle <a> </td> <tr> <td> ";

			$i=0;
			// =====================================================================loc DATE
			echo "<td> ";
			$a=substr($date_jour,0,4);
			$m=substr($date_jour,5,2);
			$j=substr($date_jour,8,2);
			$veille=date('Y-m-d',  mktime(0,0,0 , $m, $j-1, $a));
			echo " <a href=\"fissa.php?action=date&date_jour=$veille\"> < </a> </td> <td> ";
			echo "<form method=\"GET\" action=\"fissa.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"date\"> " ;	
			echo "<input type=\"text\" name=\"date_jour\" size=\"10\" value=\"$date_jour\" class=\"calendrier\" >";
			echo "</td> <td> ";
			$jsuivant=date('Y-m-d',  mktime(0,0,0 , $m, $j+1, $a));
			echo " <a href=\"fissa.php?action=date&date_jour=$jsuivant\"> > </a> </td> <td> ";
			echo "<input type=\"submit\" value=\"Valider\" >  ";
			echo " </form> ";
			
			// =====================================================================loc RAPPORT
			echo "</td> <td width=\"450\"> ";
		
			echo "<ul id=\"menu-bar\">";
			echo "<li><a href=\"fissa.php?date_jour=$date_jour&action=rapport\" target=_blank>Rapport du $date_jour </a></li>";
		//	echo "<li><a href=\"stat.php\" target=_blank>Statistiques</a>";
			echo "<li><a href=\"index.php?action=dx\">Deconnexion</a>";
			echo "</ul> </td>";
			
			echo "</td> </table> ";
		
			if ($logo!="")
				echo "<td> <a href=\"fissa.php\"> <img src=\"images/$logo\" width=\"200\" height=\"100\"  > </a> </td>";
			
			echo "  </table> <P> ";
			
			// =====================================================================loc Liste présents
			echo "<table border=\"2\" >";
			
			// =====================================================================loc AJOUTER
			echo "<tr> <td width=\"1000\"> Ajout rapide: ";
			proposition("");
			echo "</td><tr> <td > $acteur: ";
			proposition("(S)");	
			proposition("(B)");				
			echo "</td><tr> <td > Activités: ";
			proposition("(A)");					
			echo "  </table> <P> ";
			
			affiche_memo();
			echo "<table border=\"2\" >";
			
			// =====================================================================loc AJOUTER
			echo "<form method=\"GET\" action=\"fissa.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
			echo "<input type=\"hidden\" name=\"femme\" value=\"\"> " ;
			echo "<input type=\"hidden\" name=\"memo\" value=\"\"> " ;	
			echo "<input type=\"hidden\" name=\"commentaire\" value=\"\"> " ;
			echo "<input type=\"hidden\" name=\"presence\" value=\"Visite\"> " ;	
			echo "<input type=\"hidden\" name=\"qte\" value=\"1\"> " ;	
			echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
			echo "<tr> <td> ";
			echo "<SELECT name=nom>";
			echo "<OPTION  VALUE=\"\">  </OPTION>";
			for ($j=0;$j<$jmax;$j++)
				{
				$sel=$liste_nom[$j];
				if ($sel!= "Mail") 
					echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
				}
			echo "</SELECT>";
			echo "</td> <td>"; 
			echo "<input type=\"submit\" value=\"Ajouter\" >  ";
			echo "</td>";
//			<td></td>";
			echo " </form> ";	
			// =====================================================================loc NOUVEAU
			echo "<td></td> <td><form method=\"GET\" action=\"fissa.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
			echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
			echo "<input type=\"hidden\" name=\"memo\" value=\"\"> " ;	
			echo "<input type=\"hidden\" name=\"qte\" value=\"1\"> " ;
			echo "<input type=\"text\" name=\"nom\" size=\"30\" value=\"\">";	
			liste_type();
			echo "<input type=\"hidden\" name=\"commentaire\" value=\"\"> " ;	
			echo "<input type=\"hidden\" name=\"presence\" value=\"Visite\"> " ;
			echo "<input type=\"submit\" value=\"Créer Nouveau\" >  ";
			echo "</td></form> ";	

			echo "<tr> <td bgcolor=\"#FFCC66\"> Prénom / Nom </td> <td bgcolor=\"#FFCC66\"> Evénement </td>";
			//<td bgcolor=\"#FFCC66\"> Qte </td>
			echo "<td bgcolor=\"#FFCC66\"> Memo </td><td bgcolor=\"#FFCC66\"> Commentaire </td>";		
			for($i=0;$i<$imax;$i++)
				if ($nom[$i]!="")
					{
					echo "<tr> <td> ";
					echo "<form method=\"GET\" action=\"fissa.php\">";
					echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
					echo "<input type=\"hidden\" name=\"femme\" value=\"\"> " ;
					echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
					$nom1=$nom[$i];
				
					echo "<input type=\"hidden\" name=\"nom\" size=\"20\" value=\"$nom1\">";
					echo "<a href=\"fissa.php?action=suivi&nom=$nom1&date_jour=$date_jour\" target=_blank> <b>$nom1</b> </a></td>";
					$valeur=$pres_repas[$i];
					if (($nom1!= "Mail") && ($valeur!="Atelier") )
						liste_presence($valeur, $nom[$i]);
					else
						echo "</td> <input type=\"hidden\" name=\"presence\" value=\"$valeur\"> <td>";
					echo "</td> <td>";
					$valeur =$qte[$i];
					if ($valeur=="")
						$valeur="1";
					//choix_qte( $valeur );
					//echo " </td> <td>";
					$reponse = mysql_query("SELECT * FROM $bdd where date='0000-00-00' and nom='$nom1' "); 
					if ($donnees = mysql_fetch_array($reponse) )
						$n=$donnees["commentaire"];	
					else
					  $n="";
					  
					echo "<TEXTAREA rows=\"1\" cols=\"20\" name=\"memo\" onChange=\"this.form.submit();\">$n</TEXTAREA>";
						
					echo "</td> <td> ";

					$valeur=$commentaire[$i];
					echo "<TEXTAREA rows=\"1\" cols=\"60\" name=\"commentaire\"onChange=\"this.form.submit();\" >$valeur</TEXTAREA>";
					echo " </form> ";
					echo "</td>";
					}

			// =====================================================================locYNTHESE
			
			echo "<P> <table border=\"2\" >";
			echo "<tr> <td> ";
			echo "<form method=\"GET\" action=\"fissa.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"nouveau\"> " ;
			echo "<input type=\"hidden\" name=\"nom\"  value=\"Synth\">";
			echo "<input type=\"hidden\" name=\"date_jour\"  value=\"$date_jour\">";
			echo "<input type=\"hidden\" name=\"femme\" value=\"\"> " ;
			echo "<input type=\"hidden\" name=\"qte\" value=\"1\"> " ;
			echo "<input type=\"hidden\" name=\"presence\"  value=\"Synth\">";
			if ($synth=="") 
				$synth="Faits marquants:\n ";
			else 
				$synth=stripcslashes($synth);
			echo "<TEXTAREA rows=\"8\" cols=\"110\" name=\"commentaire\">$synth</TEXTAREA>";
			echo "</td> <td>";
			echo "<input type=\"submit\" value=\"Maj Synthése\" >";
			echo "<p><p><a href=\"fissa.php?action=suivi&nom=Synth\" target=_blank> Historique </a>";
			echo "</form> ";
			echo "</table>  ";


			// =====================================================================loc CHANGEMENT NOM
			echo "<P> <table border=\"0\" ><tr> <td>";
			echo "<form method=\"GET\" action=\"fissa.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"chgt_nom\"> " ;
			echo "<SELECT name=nom>";
			echo "<OPTION  VALUE=\"\">  </OPTION>";
			for ($j=0;$j<$jmax;$j++)
				{
				$sel=$liste_nom[$j];
				if ($sel!= "Mail") 
					echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
				}
			echo "</SELECT>";
			echo "</td> <td> à tranformer en </td> <td>";
			echo "<input type=\"text\" name=\"nouveau\" size=\"20\" value=\"\">";	
			echo "<input type=\"submit\" value=\"MaJ nom\" >  ";
			echo " </form> ";
			echo "</table>  ";
			
				// =====================================================================loc Histo
			echo "<P> <table border=\"0\" ><tr> <td>";
			echo "<form method=\"GET\" action=\"fissa.php\" target=_blank>";
			echo "<input type=\"hidden\" name=\"action\" value=\"suivi\"> " ;
			echo "<SELECT name=nom>";
			echo "<OPTION  VALUE=\"\">  </OPTION>";
			for ($j=0;$j<$jmax;$j++)
				{
				$sel=$liste_nom[$j];
				if ($sel!= "Mail") 
					echo "<OPTION  VALUE=\"$sel\"> $sel </OPTION>";
				}
			echo "</SELECT>";
			echo "<input type=\"submit\" value=\"Historique\" >  ";
			echo "</form> ";
			echo "</table>  ";
			break;
	}
	
	pied_de_page();
		?>
	
    </body>
</html>
