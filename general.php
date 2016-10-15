<?PHP
// traduire() : Ok

include 'param.php';
include 'bdd.php';
require_once 'include_crypt.php';

	
	
	
FUNCTION poste_local()
	 {
	 return($_SERVER['REMOTE_ADDR']=="127.0.0.1");
	 }
	 
	function token_ref($action)
		{
		$t=time();
		return( "token=".encrypt("$action-#-$t")); 
		}	
		
	function token($action, $user='#')
		{
		$t=time();
		$token=addslashes(encrypt("$action-$user-$t"));
		echo "<input type=\"hidden\" name=\"token\" value=\"$token\"> " ;
		}	
	function token_return($action, $user='#')
		{
		$t=time();
		$token=addslashes(encrypt("$action-$user-$t"));
		return("<input type=\"hidden\" name=\"token\" value=\"$token\"> ") ;
		}
		
	function verifi_token($token,$action,$user="")
		{
		$token=decrypt($token);
		if ($token!="")
			{
			$d3= explode("-",$token);
			$u=$d3[1];
			$action_token=$d3[0];
			$time_token=$d3[2];
			//echo "Token : $action_token $u $time_token";
			// si commande publiques on ne vérifie que la durée de validité 
			if ( ($action_token=="finaliser_user") || ($action_token=="recup_dossier") || ($action_token=="reinit_mdp") ) // pour ces commandes, la validité est de 7 jours
				{
				if ( (time()- $time_token) > parametre("DD_duree_vie_dossier")*24*3600) 
					{
					//erreur("Commande périmèe");
					return ("cmd_perimee");
					}	
				}
			else
				{
				if ( ($u!="#") && ($user!="") && ($u!=$user))
					{
					erreur("Commande non valide pour vous ($user)");
					return ("");
					}
				
				if ( (time()- $time_token) > 1.5*TIME_OUT) 
					{
					//erreur("Commande périmèe");
					return ("cmd_perimee");
					}
				}
		
			
			/*if (($action!="") && ( $action != $action_token) )
				{
				erreur("Token incorrect $action != $action_token ");
				return ("");
				}	
			*/	
			

			return ($action_token); 
			}
		else
			{
			erreur("Token incorrect");
			return ("");
			}
		}
		
		//====================================================================================================================
			
	function jour($t)
		{
		return (($t+4) % 7);
		}
		
	function libelle_jour($i)
		{
		switch ($i)
			{
			case 1 : return("Lundi"); break;
			case 2 : return("Mardi"); break;
			case 3 : return("Mercredi"); break;
			case 4 : return("Jeudi"); break;
			case 5 : return("Vendredi"); break;
			case 6 : return("Samedi"); break;
			case 0 : return("Dimanche"); break;
			}
		}
		
	function liste_avant( $val_init , $mode="", $autre="")
		{
		echo "<td><SELECT name=\"avant\" $mode >";
		affiche_un_choix($val_init,"Aucun", traduire("Pas de SMS"));
		affiche_un_choix($val_init,"1H");
//		affiche_un_choix($val_init,"4H");
		affiche_un_choix($val_init,"La veille", traduire("La veille soir"));
//		affiche_un_choix($val_init,"24H");
		if ($autre!="")
			affiche_un_choix($val_init,$autre);
		echo "</SELECT></td>";
		}			

	require_once "connex_inc.php";
	$traductions_fr_fr =( strtolower(parametre("DD_traductions_fr_fr","oui"))=="non"); 	
	
	function traduire($ligne)
		{
		global $user_lang, $traductions_fr_fr;
				
		if (($user_lang!="fr") && ($user_lang!="gb") && ($user_lang!="de") &&  ($user_lang!="es") && ($user_lang!="ru"))
			$user_lang="fr";
		
		if ($traductions_fr_fr)
			{
			$l=addslashes($ligne);
			$r1 =command("select * from  z_traduire where original='$l' ");
			if (!($d1=fetch_command($r1))) 
				{
				if ($l!="")
					{
					$idx=inc_index('traduire');
					command("insert into z_traduire VALUES ($idx,'$l','','','','','','$l','') ");
					}
				}			
			else
				{
				//echo 
				$l=stripcslashes($d1[$user_lang]); // T358
				if ($l!="")	
					$ligne=$l;
				}
			}
		return($ligne);
		}	

	function libelle_user($idx)
		{
		if (is_numeric($idx))
			{
			$r1 =command("select * from  r_user where idx='$idx' ");
			$d1 = fetch_command($r1);
			return($d1["nom"]." ".$d1["prenom"]);
			}
		else return($idx);
		}	

				
	function libelle_organisme($organisme)
		{
		$r1 =command("select * from  r_organisme where idx='$organisme' ");
		$d1 = fetch_command($r1);
		return(stripcslashes($d1["organisme"]));
		}		
	function adresse_organisme($organisme)
		{
		$r1 =command("select * from  r_organisme where idx='$organisme' ");
		$d1 = fetch_command($r1);
		return(stripcslashes($d1["adresse"]));
		}		
	function telephone_organisme($organisme)
		{
		$r1 =command("select * from  r_organisme where idx='$organisme' ");
		$d1 = fetch_command($r1);
		return(stripcslashes($d1["tel"]));
		}			
	function mail_organisme($organisme)
		{
		$r1 =command("select * from  r_organisme where idx='$organisme' ");
		$d1 = fetch_command($r1);
		return(stripcslashes($d1["mail"]));
		}			
				
	function libelle_organisme_du_user($idx	)
		{
		$l="";
		if (is_numeric($idx))
			{
			$r1 =command("select * from  r_user where idx='$idx' ");
			if($d1 = fetch_command($r1))
				if ( ($d1["droit"]=="S") || ($d1["droit"]=="s") || ($d1["droit"]=="R")|| ($d1["droit"]=="r") ) 
					$l= libelle_organisme($d1["organisme"]);
			}
		return($l);
		}

	function supp_acces($ddeur,$bene,$autorise)
		{
		$date_jour=date('Y-m-d');

		$reponse =command("UPDATE r_dde_acces set code='' , date_auto='' where user='$bene' and ddeur='$ddeur' and autorise='$autorise' and type='A' ");
		ajout_log( $bene, traduire("Fin d'autorisation d'accès au compte de")." ".libelle_user($bene).traduire("par")." ".libelle_user($autorise)." ".traduire("à")." ".libelle_user($ddeur) );
		ajout_log( $autorise, traduire("Fin d'autorisation d'accès au compte de")." ".libelle_user($bene)." ".traduire("par")." ".libelle_user($autorise)." ".traduire("à")." ".libelle_user($ddeur), $ddeur);
		}
		
	function purge_dde_acces()
		{
		$date_jour=date('Y-m-d');

		$r1 =command("select * from r_dde_acces where type='A' and code<>'' and code<>'????' and date_dde<'$date_jour' ");
		while ($d1 = fetch_command($r1) ) 
			{
			$bene=$d1["user"];
			$autorise=$d1["autorise"];
			$ddeur=$d1["ddeur"];
			supp_acces($ddeur,$bene,$autorise);
			}
		}
		
	function ajoute_note($user, $ligne)
		{
		$ligne = stripcslashes($ligne);
		$num_seq=inc_index("notes");
		$date_jour=date('Y-m-d')." ".$heure_jour=date("H\hi:s");
		if ($ligne!="")
			{
			$r1 =command("select * from r_sms where idx='$user' and ligne='$ligne'  ");
			if (! fetch_command($r1) ) 
				command("INSERT INTO r_sms VALUES ('$date_jour' , '$user', '$ligne', '$num_seq' ) ");
			}
		}
		
	// =========================================================== 
	
	// entete formulaire
	// formulaire ("");
	function formulaire ($action, $source="")
		{
		global $user;
		
		if ($source=="")
			$source=$_SERVER['PHP_SELF'] ;

		commentaire_html("Formulaire $action");
		echo "<form method=\"POST\" action=\"$source\">";
	//	echo "<input type=\"hidden\" name=\"action\" value=\"$action\"> " ;
		if (isset($user))
			token($action, "$user");
		else
			token($action,"#");
		}
// ----------------------------------------------------------------- Mise en forme

	function VerifierAdresseMail($adresse)  
		{  
		return (filter_var(trim($adresse), FILTER_VALIDATE_EMAIL));  
		}
	
	function homogenise_telephone($telephone)  
		{
		$telephone = str_replace (" ","", $telephone);
		$telephone = str_replace ("-","", $telephone);
		$telephone = str_replace (".","", $telephone);
		return(trim($telephone));
		}
	
	function VerifierTelephone($telephone)  
		{ 
		$telephone=homogenise_telephone($telephone) ;
	
		if ( ($telephone[0]!='0')  && ($telephone[0]!='+'))
 		 return false;  
		 
		 // cas france en 0 + 10 chiffres
		if ( ($telephone[0]=='0')  && ($telephone[1]!='0')  &&  (strlen($telephone)!=10))
 		 return false;  
		 
		 // cas format international
		if (($telephone[0]=='+') ||  ($telephone[0]=='0')  && ($telephone[1]=='0') )
			if (strlen($telephone)<8)
				return false;  	
			else  // cas france en '+33' + 9 chiffres
				if ( ($telephone[1]=='3') && ($telephone[2]=='3') && (strlen($telephone)!=12))
					return false; 				
	  return true;
	  }

	function VerifierPortable($telephone)  
		{  
		$telephone=trim($telephone);

		if (
			( (strlen(strstr($telephone,"06"))!=10) && (strlen(strstr($telephone,"07"))!=10) )
		&&
			( (strlen(strstr($telephone,"+336"))!=12) && (strlen(strstr($telephone,"+337"))!=12) )
			)
									
			return false; 		
		else		
		  return true;
	  }	  
	  
	Function erreur($texte)
		{
		echo "<div id=\"msg_erreur\"><center><FONT color=\"#ff0000\" >".traduire('Erreur')." : $texte</FONT><br></center></div >";
		ajout_log_jour("Msg erreur : $texte");
		}	
	
	Function msg_ok($texte)
		{
		echo "<div id=\"msg_ok\" class=\"CSS_msg_ok\"><center>$texte<br></center></div >";
		}	
		
	function remplace_carcateres($nom)
		{
		return (
			strtr($nom, 'çàéèêëùñ<>#()-+=&²_°".,;!?%$[|]\'',
						'caeeeeun                        ')
			);
		}
	
	Function mef_texte_a_afficher($nom)
		{
		$nom = str_replace ("<","&lsaquo;", $nom);
		$nom = str_replace (">","&rsaquo;", $nom);
		
		return($nom);
		}	
	
	Function mef_nom($nom)
		{
		$nom=strtolower($nom);
		$nom=remplace_carcateres($nom);
		$nom=strtoupper($nom);
		return($nom);
		}	
		
	Function mef_prenom($prenom)
		{
		$prenom=strtolower($prenom);
		$prenom=remplace_carcateres($prenom);
		return(ucfirst($prenom));
		}

	
	Function mef_date($date)
		{
		if (substr_count($date,"/")!=2)
			return("");

		$d3= explode("/",$date);
		$a=$d3[2];
		$m=$d3[1];
		$j=$d3[0];
		
		if ( ($j>31) || ($j<1) || ($m<1) || ($m>12))
			return("");
		
		if (($a>13) && ($a<100)) $a=1900+$a;
		if ($a<=13) $a=2000+$a;
		if ( ($a>2020) || ($a<1900) )
			return("");		
			
		return(sprintf("%02d/%02d/%4d",$j,$m,$a));
		}

		function mef_date_fr($date)
			{
			if (substr_count($date,"-")!=2)
				return("");

			$d3= explode("-",$date);
			$a=$d3[0];
			$m=$d3[1];
			$j=$d3[2];
			
			if ( ($j>31) || ($j<1) || ($m<1) || ($m>12))
				return("");
			
			if (($a>13) && ($a<100)) $a=1900+$a;
			if ($a<=13) $a=2000+$a;
			if ( ($a>2020) || ($a<1900) )
				return("");		
				
			return(sprintf("%02d/%02d/%4d",$j,$m,$a));
			}

		function mef_date_BdD($date)
			{
			if (substr_count($date,"/")!=2)
				return("");

			$d3= explode("/",$date);
			$a=$d3[2];
			$m=$d3[1];
			$j=$d3[0];
			
			if ( ($j>31) || ($j<1) || ($m<1) || ($m>12))
				return("");
			
			if (($a>13) && ($a<100)) $a=1900+$a;
			if ($a<=13) $a=2000+$a;
			if ( ($a>2020) || ($a<1900) )
				return("");		
				
			return(sprintf("%04d-%02d-%02d",$a,$m,$j));
			}			

			
	function mise_en_forme_date_aaaammjj( $date_jour)
		{
		$d3= explode("/",$date_jour);  
		if (isset($d3[2]))
			$a=$d3[2];
		else
			$a=date("Y");
		if ($a<100) $a+=2000;
		$m=$d3[1];
		$j=$d3[0];	
		if (($j<1) || ($j>31) || ($m<1) || ($m>12) )
			return("");
		
		return( "$a-$m-$j" );
		}
			
		function mef_heure_BdD($date)
			{
			$date=strtolower($date);
			
			if (substr_count($date,"h")==1)
				{
				$d3= explode("h",$date);
				$m=$d3[1];
				$h=$d3[0];
				}
			else
				{
				$h= $date;
				$m=0;
				}			
			
			if (  ($m<0) || ($m>=60))
				$m=0;
				
			if (  ($h<0) || ($h>23))
				$m=8;		
				
			return(sprintf("%02dh%02d",$h,$m));
			}		
			
	function supprime_html($body)
		{
		$body= str_replace("&lt;","<",$body);
		$body= str_replace("&gt;",">",$body);
		$body= str_replace("&quot;","\"",$body);
		$body= str_replace("&apos;","'",$body);
		return($body);
		}
		
	function filtre_xss($var)
		{
		$var= str_replace("<","&lt;",$var);
		$var= str_replace(">","&gt;",$var);
		$var= str_replace("\"","&quot;",$var);
//		$var= str_replace("\\","&#92;",$var);
		$var= str_replace("'","&apos;",$var);

		if ((stristr($var,"<script")===FALSE) 
			&& (stristr($var,"<object")===FALSE)
			&& (stristr($var,"<applet")===FALSE)
			&& (stristr($var,"<embed")===FALSE)
			)
			return($var);
	
		return("");
		}

	function est_image($num)
		{
		switch ( extension_fichier( $num ))
			 {
			case "jpg" :
			case "jpeg" :
			case "png" :
			case "gif": 
						return(true);
		default :
			return(false);
			}
		}	

	function est_doc($num)
		{
		switch ( extension_fichier( $num ))
			 {
			case "doc" :
			case "docx" :	
			case "odt" :
			
						return(true);
			default :
			return(false);
			}
		}		
	function supp_fichier($nom)
		{
		ajout_log_jour("supp_fichier($nom)");
		if (file_exists($nom))
				unlink ($nom);
		}
	
	Function commentaire_html($texte)
		{
		echo "\r\n<!-- $texte -->\r\n";
		ajout_log_jour($texte);
		}
	
	Function signet($texte)
		{
		commentaire_html("Signet : $texte");
		echo "<a name=\"$texte\"></a> ";
		}
	
		
	function extension_fichier( $num )
		{
		$tabfile = explode('.',$num);
		// extension = le dernier element
		return (strtolower($tabfile[sizeof($tabfile)-1] ));
		}	
	

	function visu_session($nom)
		{
		if (isset($_SESSION[$nom]))
			echo "<br> ['$nom'] : '".$_SESSION[$nom]."'";
		}
	$trace_variables="";
	function trace_variable( $libelle , $valeur )
		{
		global $trace_variables;
		if ($libelle!="token")
			$trace_variables .= "<br> $libelle = '$valeur'";
		return ($valeur);
		}
		
	// recupére la variable d'abord en POST puis en GET
	function variable_s($libelle)
		{
		if (isset ($_POST[$libelle]))
			return(trace_variable( $libelle ,filtre_xss($_POST[$libelle])));
		if (isset ($_GET[$libelle]))
			return(trace_variable( $libelle ,filtre_xss($_GET[$libelle])));
		return("");
		}
	
	// recupére la variable d'abord en POST ou seulement "action" en GET	
	function variable($libelle)
		{
		if (isset ($_POST[$libelle]))
			{
			$tmp = addslashes( mysql_real_escape_string(filtre_xss($_POST[$libelle]))); 
			if ($libelle=="filtre")
				{
				$tmp= str_replace("(","",$tmp);
				$tmp= str_replace(")","",$tmp);
				$tmp= str_replace("*","",$tmp);
				$tmp= str_replace("%","",$tmp);
				$tmp= str_replace("^","",$tmp);
				}
			return(trace_variable( $libelle ,$tmp));	
			}

		if (isset ($_GET[$libelle]))
			if (($libelle=="action") || ($libelle=="token") )
				return(trace_variable( $libelle ,mysql_real_escape_string(filtre_xss($_GET[$libelle]))));

		return("");
		}

		
	function variable_cryptee($libelle)
		{
		if (isset ($_POST[$libelle]))
//			return(addslashes(mysql_real_escape_string(decrypt(filtre_xss(strtr($_POST[$libelle] ' ',  '+'))))));
			return(trace_variable( $libelle ,mysql_real_escape_string(decrypt(filtre_xss($_POST[$libelle])))));
			
		if (isset ($_GET[$libelle]))
			return(trace_variable( $libelle ,mysql_real_escape_string(decrypt(filtre_xss(strtr($_GET[$libelle], ' ',  '+'))))));
			
		return("");
		}		
		
	function variable_get($libelle)
		{
		if (isset ($_GET[$libelle]))
			{	
			if ($libelle=="action")
				return(trace_variable( $libelle ,mysql_real_escape_string(filtre_xss($_GET[$libelle]))));
			else
				{
				return(trace_variable( $libelle ,mysql_real_escape_string(decrypt(filtre_xss(strtr($_GET[$libelle], ' ',  '+'))))));
				}
			}
		return("");
		}	
	
	function variable_get_ltd($libelle, $validite='' )
		{
		if (isset ($_GET[$libelle]))
			{	
			if ($libelle=="action")
				return(trace_variable( $libelle ,mysql_real_escape_string(filtre_xss($_GET[$libelle]))));
			else
				{
				return(trace_variable( $libelle ,mysql_real_escape_string(decrypt_ltd(filtre_xss(strtr($_GET[$libelle], ' ',  '+')),$validite))));
				}
			}
		return("");
		}

	function inc_index($valeur)
		{
		$p="TECH_sequence_$valeur";
		$idx= parametre($p)+1; 
		ecrit_parametre($p,$idx);
		return ($idx);
		}
	
			
	function param ($var,$val)
		{
		return "<input type=\"hidden\" name=\"$var\" value=\"$val\">";
		}
	
	function lien($image, $action, $param, $title="", $size="20", $blank="", $sans_lien="", $arrondi =false )
		{
		global $user;
		
		commentaire_html(" $image / $action / $blank");
		
		if ($arrondi)
			$format_arrondi="class=\"arrondie\"";
		else
			$format_arrondi="";			
		
		if ($blank!="") 
			$blank = "target=_blank";
		
		if ($sans_lien=="")
			{
			$source= "index.php";
			if ( ($action=="visu_image") || ($action=="visu_image_mini")|| ($action=="visu_fichier")|| ($action=="visu_doc") )
				$source= "visu.php";
			if  ($action=="visu_suivi") 
				$source= "visu_suivi.php";			
			if  ($action=="visu_dossier") 
				$source= "visu_dossier.php";
			if  ($action=="supp_filtre_suivi") 
				$source= "suivi.php";
				
			echo "<form method=\"POST\" action=\"$source\" $blank >";

			if ($size=="")
				echo "<input type=\"image\" src=\"$image\" title=\"".traduire($title)."\" $format_arrondi>";
			else
				echo "<input type=\"image\" src=\"$image\" width=\"$size\" height=\"$size\" title=\"".traduire($title)."\" $format_arrondi>";
			token($action,$user);
//			echo "<input type=\"hidden\" name=\"action\" value=\"$action\">";
			echo "$param</form>";
			}
		else
			{
			if ($size=="")
				echo "<input type=\"image\" src=\"$image\" title=\"".traduire($title)."\" $format_arrondi>";
			else
				echo "<input type=\"image\" src=\"$image\" width=\"$size\" height=\"$size\" title=\"".traduire($title)."\" $format_arrondi >";
			}

		}

	function aff_logo($titre="")
		{
		echo "<div id=\"logo\"> <center>";
		echo "<a href=\"portail.php\"><img src=\"images/adileos.jpg\" width=\"150\" height=\"22\" ></a> ";
		echo "<a href=\"index.php\"><img src=\"images/logo.png\" width=\"30\" height=\"22\" ></a> ";
		if (file_exists ( "webmail" )) 
			echo "<a href=\"wm.php\"><img src=\"images/webmail.jpg\" width=\"30\" height=\"22\" ></a> ";
		echo "<a href=\"fissa.php\"><img src=\"images/fissa.jpg\" width=\"30\" height=\"22\" ></a> ";	
		echo "<a href=\"suivi.php\"><img src=\"images/suivi.jpg\" width=\"30\" height=\"22\" ></a> ";	
		echo "<a href=\"rdv.php\"><img src=\"images/rdv.jpg\" width=\"30\" height=\"22\" ></a> ";	
		echo "<a href=\"alerte.php\"><img src=\"images/logo-alerte.jpg\" width=\"30\" height=\"22\" ></a> ";	

		echo "</div>  <center>";	
	
		if (strpos($_SERVER['PHP_SELF'],'wm')!==false)
			{
			echo "<div id=\"logo\"> <center><a href=\"wm.php\"><img src=\"images/webmail.jpg\" width=\"200\" height=\"150\" ></a> </div>  <center>";	
			if ($titre!="")
				{
				echo "<h3>".traduire("Accèder à vos mails de façon très simple.")."</h3>";
				echo "<p><i><b><p>";
				}
			}		
		else			
		if (strpos($_SERVER['PHP_SELF'],'suivi')!==false)
			{
			echo "<div id=\"logo\"> <center><a href=\"suivi.php\"><img src=\"images/suivi.jpg\" width=\"200\" height=\"150\" ></a> </div>  <center>";	
			if ($titre!="")
				{
				echo "<h3>".traduire("Enrichissez le suivi des bénéficiaires ")."</h3>";
				echo "<p><i><b><p>";
				}
			}		
		else		
		if (strpos($_SERVER['PHP_SELF'],'rdv')!==false)
			{
			echo "<div id=\"logo\"> <center><a href=\"rdv.php\"><img src=\"images/rdv.jpg\" width=\"200\" height=\"150\" ></a> </div>  <center>";	
			if ($titre!="")
				{
				echo "<h3>".traduire("Rappel de rendez-vous par SMS")."</h3>";
				echo "<p><i><b>".traduire("Augmentez l'efficacité de vos rendez-vous auprès des bénéficiaires")."<p>";
				}
			}		
		else
		if (strpos($_SERVER['PHP_SELF'],'fissa')!==false)
			{
			echo "<div id=\"logo\"> <center><a href=\"fissa.php\"><img src=\"images/fissa.jpg\" width=\"200\" height=\"150\" ></a> </div>  <center>";	
			if ($titre!="")
				{
				echo "<h3>".traduire('Simplifiez le suivi des activités et des bénéficiaires')."</h3>";
				echo "<p><i><b><p>";
				}
			}		
		else
			{
			echo "<div id=\"logo\"> <center><a href=\"index.php\"><img src=\"images/logo.png\" width=\"200\" height=\"150\" ></a> </div>  <center>";	
			if ($titre!="")
				{
				echo "<h3>".traduire('La Consigne Numérique Solidaire')."</h3>";
				echo "<p><i><b><font size=\"5\">'' ".traduire('Mon essentiel à l\'abri en toute confiance')." '' .</b></i></font>";
				echo "<p>".traduire('Sauvegardez gratuitement de façon sécurisée vos documents, photos et informations essentielles .');
				}
			}
		}	
	
	function aff_logo_multiple()
		{
		echo "<div id=\"logo\"> <center>";
		echo "<a href=\"http://adileos.jimdo.com/\"><img src=\"images/adileos.jpg\" width=\"700\" height=\"90\" >"; 	
		echo "<br>";
		echo "<a href=\"index.php\"><img src=\"images/logo.png\" width=\"150\" height=\"100\" ></a>";
		echo "<a href=\"fissa.php\"><img src=\"images/fissa.jpg\" width=\"150\" height=\"100\" ></a>";
		echo "<a href=\"rdv.php\"><img src=\"images/rdv.jpg\" width=\"150\" height=\"100\" ></a>";
		echo "<a href=\"suivi.php\"><img src=\"images/suivi.jpg\" width=\"150\" height=\"100\" ></a>";
		echo "<a href=\"alerte.php\"><img src=\"images/logo-alerte.jpg\" width=\"150\" height=\"100\" ></a>";
		echo "</div>  <center>";	
		}	
		

	function lien_c($image, $action, $param , $title="", $size="20")
		{
		echo "<td width=\"20\">";
		lien($image, $action, $param , $title, $size);
		echo "</td>";
		}
	
	function ajout_log($id, $ligne, $acteur="")
		{
		$date_log=date('Y-m-d');	
		$heure_jour=date("H\hi.s");	
		$ip= $_SERVER["REMOTE_ADDR"];
		$ligne=addslashes($ligne);
		
		if (!is_numeric($id))
			{
			$reponse = command("select * FROM `r_user`  where id='$id' ");
			$donnees = fetch_command($reponse)	;	
			$id= $donnees["idx"];	
			}	

		if ($acteur=="")
			$acteur=$id;
			
		$reponse = command("INSERT INTO `log`  VALUES ('$date_log $heure_jour' , '$id', '$ligne', '$acteur','$ip' ) ");
		}

	function ajout_log_tech( $ligne, $prio="P2")
		{
		ajout_log_jour("Fct: ajout_log_tech($ligne,$prio)");
		$date_log=date('Y-m-d');	
		$heure_jour=date("H\hi.s");	
		$ip= $_SERVER["REMOTE_ADDR"];
		$ligne=addslashes($ligne);
		$reponse = command("INSERT INTO `z_log_t`  VALUES ('$date_log $heure_jour' , '$ligne', '$ip', '$prio' ) ");
		}		

	function ajout_log_jour( $ligne)
		{
		$date_log=date('Y-m-d');	
		$heure_jour=date("H\hi.s");	
		$ligne=addslashes($ligne);
		
		$nom='tmp/log.txt';
		$f_log = fopen($nom, 'a+');		
		fputs($f_log, $date_log." ".$heure_jour." : ".$ligne."\r\n"); 
		fclose($f_log);
		}	
		
	function purge_log ()
		{
		$date=date('Y-m-d',  mktime(0,0,0 , date("m")-3, date("d"), date ("Y")));
		$r1 = command("DELETE FROM log WHERE ligne like '%connexion%' and date<$date  ");
		$date=date('Y-m-d',  mktime(0,0,0 , date("m")-1, date("d"), date ("Y")));
		$r1 = command("DELETE FROM z_log_t WHERE date<$date  ");		
		}
				

	// ----------------------------------------------------------------- Tempo connexion

	
	function maj_last_cx($idx)
		{
		$date_log=date('Y-m-d');	
		$heure_jour=date("H\hi.s");
		command("UPDATE r_user set last_cx='$date_log $heure_jour' where idx='$idx' ");	
		}
		
	function maj_last_hash_ctrl($idx)
		{
		$date_log=date('Y-m-d');	
		$heure_jour=date("H\hi.s");
		command("UPDATE r_user set last_hash_ctrl='$date_log $heure_jour' where idx='$idx' ");	
		}
		
	function ajout_echec_cx ($id)
		{
		$delta=parametre("DD_incremente_tempo",4);
		$reponse = command("select * from cx where id='$id' ");
		if ($donnees = fetch_command($reponse) )
			{
			$tempo=min( 120, $donnees["tempo"]+$delta);
			
			$tmax= parametre("DD_seuil_tempo_cx_max") ;
			if ( ($tempo >= $tmax ) && ($tempo < $tmax+$delta ) )
				alerte_sms("Seuil nbre connexion dépassé pour $id");
				
			$reponse = command("UPDATE cx SET tempo='$tempo' where id='$id' ") ;
			
			$nb_echec= parametre("nbre_echec_sur_periode");
			ecrit_parametre("nbre_echec_sur_periode", $nb_echec+1 );
			
			if (parametre("DD_nbre_echec_max_par_periode")==$nb_echec)
				alerte_sms("Dépassement nombre d'échec de connexion par période");
			}
		else
			$reponse = command("INSERT INTO `cx` VALUES ( '$id', '$delta') ");
		}
		
	function supp_echec_cx ($id)
		{
		$reponse = command("delete from cx where id='$id' ");
		}
		
	function tempo_cx ($id)
		{
		$reponse = command("select * from cx where id='$id' ");
		if ($donnees = fetch_command($reponse) )
			if ($_SERVER['REMOTE_ADDR']!="127.0.0.1")
				sleep ($donnees["tempo"]);		
		}

	function decremente_echec_cx ($pas=1)
		{
		$r1 = command("select * from cx ");
			while ($donnees = fetch_command($r1) )
				{
				$tempo=$donnees["tempo"]-$pas;
				$id=$donnees["id"];
				if ($tempo<=0)
					mysql_query("DELETE FROM cx where id='$id' ") ;
				else
					mysql_query("UPDATE  cx SET tempo='$tempo' where id='$id' ") ;
				}
		}

	function compte_non_protege($id)
		{
		return ( ( $id!="Form_B" ) && ( $id!="Form_R" ) && ( $id!="Form_A1" ) && ( $id!="Form_A2" ) );
		}
	
	function liste_profil( $user_droit_org, $val_init)
		{
		echo "<form method=\"post\" >";
		//ECHO "<input  type=\"hidden\" name=\"action\" value=\"modif_profil\"/> ";
		token("modif_profil");
		echo "<SELECT name=\"profil\" onChange=\"this.form.submit();\"  >";
		if ($user_droit_org=="R")
			{
			affiche_un_choix($val_init,"S","Acteur social");
			affiche_un_choix($val_init,"R","Responsable");
			}

		if ($user_droit_org=="A")
			{
			affiche_un_choix($val_init,"A","Administrateur");
			affiche_un_choix($val_init,"E","Exploitant");
			affiche_un_choix($val_init,"F","Fonctionnel");
			affiche_un_choix($val_init,"T","Formateur");
			affiche_un_choix($val_init,"t","Traducteur");
			affiche_un_choix($val_init,"s","AS inactif");
			affiche_un_choix($val_init,"R","Responsable");
			affiche_un_choix($val_init,"S","Acteur social");
			affiche_un_choix($val_init,"","Bénéficiaire");
			}
		echo "</SELECT></form>";
		}
		
	function debut_cadre($taille="500")
		{
		echo "<center><TABLE><TR> <td></td><td  width=\"$taille\"> <center><div class=\"CSS_titre\"  >";
		}

	function fin_cadre()
		{
		echo "</td><td></td></TABLE>";
		}
		
	function formate_telephone($tel)
		{
		global $user_droit;

		if ((VerifierPortable($tel)) && ($user_droit!="M") )
			{
			$src =$_SERVER['PHP_SELF'];
			$m = "<form method=\"POST\" action=\"index.php\">$tel ";
			$m .= "<input type=\"image\" src=\"images/sms.png\" width=\"20\" height=\"20\" title=\"".traduire('Envoyer un SMS')."\">";
			$m .= "<input type=\"hidden\" name=\"tel\" value=\"$tel\">";
			if (strpos($_SERVER['PHP_SELF'],'index.php')!==false)
				$m .= token_return("sms_test_ovh_dd")."</form>";
			else
				$m .= token_return("sms_test_ovh")."</form>";
			
			$tel =$m;
			}
		return  ($tel) ;			
		}

	function formate_mail($mail)
		{
		if (VerifierAdresseMail($mail))	
			{
			$m = "<form method=\"POST\" action=\"index.php\">$mail ";
			$m .= "<input type=\"image\" src=\"images/mail2.png\" width=\"20\" height=\"20\"  title=\"".traduire('Envoyer un Mail')."\">";
			$m .= "<input type=\"hidden\" name=\"mail\" value=\"$mail\">";
			$m .= token_return("mail_test")."</form>";
			//$m .= "<input type=\"hidden\" name=\"action\" value=\"mail_test\"></form>";
			$mail =$m;
			}
			
		return  ($mail) ;
		}		
		
	function pied_de_page($r="")
		{
		global $user_lang , $trace_variables;
		
		if ($r!="")
			echo "<center><p><br><a id=\"accueil\" href=\"index.php\">".traduire('Retour à la page d\'accueil.')."</a>"; 

		echo "<br>";
		echo "<hr><center> ";

		echo "<table> <tr> <td align=\"right\" valign=\"bottom\" ></td>";
		if ( ($_SERVER["SCRIPT_NAME"]=="/doc-depot/index.php")	 || ($_SERVER["SCRIPT_NAME"]=="/index.php")	)
			{
			if ($user_lang!="fr")
				echo "<td><a href=\"index.php?".token_ref("fr")."\" ><img width=\"25\" border=\"0\" height=\"18\" title=\"français\" alt=\"français\" src=\"images/flag_fr.png\"/></a></td><td> | </td>";
			if ($user_lang!="gb")
				echo "<td><a href=\"index.php?".token_ref("gb")."\" ><img width=\"25\" border=\"0\" height=\"18\" title=\"english\" alt=\"anglais\" src=\"images/flag_gb.png\"/></a></td><td> | </td>";
			/*
			if ($user_lang!="de")
					echo "<td><a href=\"index.php?".token_ref("de")."\" ><img width=\"25\" border=\"0\" height=\"18\" title=\"allemand\" alt=\"allemand\" src=\"images/flag_de.png\"/></a></td><td> | </td>";
			if ($user_lang!="es")
					echo "<td><a href=\"index.php?".token_ref("es")."\" ><img width=\"25\" border=\"0\" height=\"18\" title=\"espagnol\" alt=\"espagnol\" src=\"images/flag_es.png\"/></a></td><td> | </td>";
			if ($user_lang!="ru")
					echo "<td><a href=\"index.php?".token_ref("ru")."\" ><img width=\"25\" border=\"0\" height=\"18\" title=\"russe\" alt=\"russe\" src=\"images/flag_ru.png\"/></a></td><td> | </td>";
			*/
			echo "<td><a id=\"lien_conditions\" href=\"conditions.html\">".traduire('Conditions d\'utilisation')."</a>";
			}
		else
			echo "<td><a id=\"lien_conditions\" href=\"conditions_frs.html\">".traduire('Conditions d\'utilisation')."</a>";

		echo "- <a id=\"lien_contact\" href=\"http://adileos.jimdo.com/contact\">".traduire('Nous contacter')."</a>";
		echo "- Copyright <a href=\"http://adileos.doc-depot.com\"  target=_blank >ADILEOS</a> ";
		$version= parametre("DD_version_portail") ;
		if ($_SERVER['REMOTE_ADDR']=="127.0.0.1")
			echo "- <a href=\"version.htm\"target=_blank > $version </a>";	
		else
			echo "- $version ";	

		echo "- <a href=\"http://adileos.jimdo.com/contact\">".traduire('Signaler un bug ou demander une évolution').".</a> </td> </table>";

		ECHO "</center><p align=\"left\">";
		if (($_SERVER['REMOTE_ADDR']=="127.0.0.1") || ( parametre("TECH_identite_environnement")=="PP") )
			{
			 if (variable("token")!="")
				{
				$token=decrypt(variable("token"));
				if ($token!="")
					{
					$d3= explode("-",$token);
					$u=$d3[1];
					$action_token=$d3[0];
					$time_token=$d3[2];
					echo "<hr><p align=\"left\"> Token : $action_token / $u / ".date ( 'Y-m-d H\hi.s',$time_token);
					}
				}
			echo "<p align=\"left\"> $trace_variables";
				
			echo "<p align=\"left\"> Session";
			visu_session("user");
			 visu_session("user_idx");
			 visu_session("lang");
			 visu_session("bene");
			 visu_session("chgt_user");
			 visu_session("logo");
			 visu_session("profil");
			 visu_session("droit");
			 visu_session("filtre");
			 visu_session("LAST_ACTIVITY");
			 visu_session("support");
			 
			}
		
		
		//echo "<p>LG:".strlen(ob_get_contents() );
		
		if (isset($_SESSION["user"]))
			$idx=$_SESSION["user"];
		else
			$idx="";
			$t=time();
			
		if ( strtolower(parametre("DD_MODE_Debug"))=="oui")
			command (sprintf("INSERT INTO `r_pages_users_debug` VALUES ( '%s', '%s', '%s') ",$idx,$t, mysql_real_escape_string(ob_get_contents()) ));
		/*
		$f=fopen ("tmp/$idx-$t.htm","w");
		fwrite($f, ob_get_contents());
		fclose($f);
		*/
		ob_end_flush();
		fermeture_bdd() ;
		
		exit();
		}
		
	function  addslashes2($memo)
		{
		 return(addslashes($memo));
		}
	
	function affiche_un_choix($val_init, $val, $libelle="")
		{
		if ($libelle=="")
			$libelle=$val;
			
		$libelle=traduire($libelle);
			
		if (( $val_init!=$val) || ($val_init=="") || ($val==""))
				echo "<OPTION  VALUE=\"$val\"> $libelle </OPTION>";
			else
				echo "<OPTION  VALUE=\"$val\" selected> $libelle </OPTION>";
		}
	function rappel_regles_messages($sms="")
		{
		echo "<center><p>".traduire("Il est interdit d’envoyer des messages à caractère injurieux, insultants, dénigrants, diffamatoires, dégradants ou susceptibles de porter atteinte à la vie privée des personnes ou à leur dignité, relatifs à la race, l’origine nationale, les mœurs, la religion, les opinions politiques, les origines sociales, l’âge ou le handicap ;")." : ";
		if ($sms!="")
			echo "<center><br>".traduire("Envoi du lundi au samedi entre 8h et 20h (hors jours fériés) ");
		}

		
		
	function verification_acces( $nom_fichier )
		{
		$u2=$_SESSION['user'];	
		if (!verification_acces2( $nom_fichier ))
			{
			ajout_log_tech( "Tentative d'accès au fichier $nom_fichier non autorisé à ".libelle_user($u2) ,"P0");
			}
		else
			ajout_log_tech( "Vérification ok accès acès à $nom_fichier par ".libelle_user($u2) ,"P2");
	
		}
	function verification_acces2( $nom_fichier )
		{
		$u2=$_SESSION['user'];		// Acteur
		
		$reponse =command("select * from  r_attachement where num='$nom_fichier' ");	
		if ($donnees = fetch_command($reponse) ) 
			{
			if (strtolower($nom_fichier)!=strtolower($donnees['num']))
				{
				ajout_log_tech( "Tentative d'accès à un fichier inexistant '$nom_fichier' par ".libelle_user($u2) ,"P0");
				return (false);	
				}
			if ($donnees['user']==$u2) // cas on c'est un doc du bénéficiaire
				return (true);	
			else
				{
				$u1=$_SESSION['user_idx'];	 // cas on c'est le RC qui accede au doc du bénéficiaire
				$reponse =command("select * from  r_referent where (nom='$u1' and  user='$u2') or  (nom='$u2' and  user='$u1')  ");	
				if ($donnees = fetch_command($reponse) ) 
					return (true);		
				else
					{
					// il faut aussi tester le cas ou le bénéficiaire à désigné 'tous'  d'un structure
					$reponse =command("select * from  r_user where  idx='$u2'");
					$donnees = fetch_command($reponse);
					$org = $donnees['organisme'];
					
					$reponse =command("select * from  r_referent where organisme='$org' and  user='$u1' and nom='Tous' ");	
					if ($donnees = fetch_command($reponse) ) 
						return (true);		
					}
				}
			} 
		else
			{
			ajout_log_tech( "Tentative d'accès à un fichier inexistant $nom_fichier par ".libelle_user($u2) ,"P0");
			// le fichier n'existe pas 
			}	
		return (false);	
		}

		
	// ===============================================================================================
	// transforme la liste d'activité stockées en table en une liste affichable avec lien pour supprimer
	function mef_activites($act,$nom,$date)
		{
		$ret="";
		$d3=explode('#-#',$act);
		$i=0;
		while (isset($d3[$i]))
			{
			if ($d3[$i]!="")
				{
				$a=str_replace ('(A)','',$d3[$i]);
				$ret.=$a;
				$ret.="<a title=\"Suppresion $a\"  href=\"fissa.php?".token_ref("supp_activite")."&idx=$i&nom=$nom&date_jour=$date\"> <img src=\"images/croixrouge.png\"width=\"15\" height=\"15\"><a>";
				$ret.="; ";
				}
			
			$i++;
			}
		return $ret;
		}
		

	function liste_participants_activite( $act, $date )
		{
		global $bdd;

		$ret="";
		
		$reponse = command("SELECT * FROM $bdd WHERE date='$date' and (activites like '%$act%') group by nom ASC "); 
		while ($donnees = fetch_command($reponse) )
			$ret.=stripcslashes($donnees["nom"]).", ";
	
		return($ret);
		}


		
?>