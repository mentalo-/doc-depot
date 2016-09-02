<?PHP
// traduire() : Ok

function parametre($nom, $par_defaut="")
	{
	$reponse =command("select * from  DD_param where nom='$nom' ");
	if ($donnees = fetch_command($reponse) ) 
		return ($donnees["valeur"]);
	else
		command("INSERT INTO DD_param VALUES ('$nom', '$par_defaut' ) ");
	return ("");
	}

function ecrit_parametre($nom, $valeur)
	{
	$reponse =command("select * from  DD_param where nom='$nom' ");
	if ($donnees = fetch_command($reponse) ) 
		command("update DD_param set valeur='$valeur' where nom='$nom' ");
	else
		command("INSERT INTO DD_param VALUES ('$nom', '$valeur' ) ");
	}
	
// zone définitions --------------------------------------
if ($_SERVER['REMOTE_ADDR']!="127.0.0.1")
	define('serveur',$_SERVER['SERVER_NAME'].'/');
else
	define('serveur','');

define('MAX_FICHIER','60');
define('TIME_OUT','1800');
define('TIME_OUT_BENE','300');
define('TAILLE_FICHIER','8000000');
define('TAILLE_FICHIER_dropzone','8');

		function param_sys()
			{
			$filtre1=variable("filtre");
			$nom_a_modifier=variable_get("nom");

			formulaire ("param_sys");
			echo "<table><tr><td > Filtre : <input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
			echo "</form><td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td></table> ";

			if ($filtre1!="")
				$filtre=" where (nom REGEXP '$filtre1' or valeur REGEXP '$filtre1') ";
			else
				$filtre="";

			echo "</table><div class=\"CSSTableGenerator\" > ";
			echo "<table><tr><td > ".traduire('Nom')."  </td><td > ".traduire('Valeur')."  </td>";
			$reponse =command("select * from  DD_param $filtre order by nom ASC");		
			while ($donnees = fetch_command($reponse) ) 
				{
				$nom=$donnees["nom"];	
				$valeur=$donnees["valeur"];
				echo "<tr><td><a href=\"index.php?action=param_sys&nom=".encrypt($nom)."\"> $nom <a></td>";

				if ( $nom_a_modifier!=$nom) 
					{
					echo "<td> $valeur ";
					if ((strlen($valeur)==10) && ($valeur[0]=='1') && (is_numeric($valeur))  )
							echo " (".date ("d/m/Y H:i",$valeur)." )";
					echo "</td>";
					}
				else
					echo "<td><form method=\"post\" > <input  type=\"hidden\" name=\"action\" value=\"modif_valeur_param\"/> ".param('nom',"$nom")."<input type=\"text\" name=\"valeur\" value=\"$valeur\" onChange=\"this.form.submit();\" >  </form> </td>";
				}
			echo "</table></div>";
			pied_de_page("x");
			}

		
		function modif_valeur_param($nom, $valeur)
			{
			$valeur= addslashes($valeur);
			$reponse =command("update DD_param SET valeur = '$valeur' where nom='$nom' ");
			}

			
		function traduction()
			{
			global $user_lang;
			
			$filtre1=variable("filtre");
			$nom_a_modifier=variable_get("nom");

			echo "<table><tr> ";
			echo "<td><a href=\"index.php?".token_ref("gb")."\" ><img width=\"25\" border=\"0\" height=\"18\" title=\"anglais\" alt=\"anglais\" src=\"images/flag_gb.png\"/></a></td><td> | </td>";
			echo "<td><a href=\"index.php?".token_ref("de")."\" ><img width=\"25\" border=\"0\" height=\"18\" title=\"allemand\" alt=\"allemand\" src=\"images/flag_de.png\"/></a></td><td> | </td>";
			echo "<td><a href=\"index.php?".token_ref("es")."\" ><img width=\"25\" border=\"0\" height=\"18\" title=\"espagnol\" alt=\"espagnol\" src=\"images/flag_es.png\"/></a></td><td> | </td>";
			echo "<td><a href=\"index.php?".token_ref("ru")."\" ><img width=\"25\" border=\"0\" height=\"18\" title=\"russe\" alt=\"russe\" src=\"images/flag_ru.png\"/></a></td><td> | </td>";

			Echo "<td>Langue = $user_lang </td></table><hr>";
			
			formulaire ("trad");
			echo "<table><tr><td > Filtre : <input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
			echo "</form><td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td></table> ";

			if ($filtre1!="")
				$filtre=" where (fr REGEXP '$filtre1' or $user_lang REGEXP '$filtre1') ";
			else
				$filtre="";

			echo "</table><div class=\"CSSTableGenerator\" > ";
			echo "<table><tr><td >  </td><td > ".traduire('Nom')."  </td><td > ".traduire('Valeur')."  </td>";
			$i=0;
			$reponse =command("select * from  z_traduire $filtre order by commentaire ASC, original ASC");		
			while ($donnees = fetch_command($reponse) ) 
				{
				$nom=$donnees["original"];	
				$valeur=stripcslashes($donnees["$user_lang"]);
				$idx=$donnees["idx"];	
				$commentaire=$donnees["commentaire"];	
				echo "<tr>";
				$idx2=$i-8;
				if ($commentaire=="technique")
					$chk= " checked " ;
				else
					$chk= ""  ;
				
				echo "<td width=\"10\">";
				formulaire ("modif_trad_tech", "index.php#$idx2");
				echo param("idx","$idx");
				echo "<input type=\"checkbox\" name=\"valeur\" onChange=\"this.form.submit();\" $chk >  </form> </td> ";
								
				echo "<td id=\"$idx\" width=\"30%\"> $nom ";
				
				$cible_g=$user_lang;
				if ($user_lang=="gb")
					$cible_g="en";
				

				echo "<a id=\"$i\" href=\"https://translate.google.com/?hl=fr#fr/$cible_g/$nom\"  target=_blank>$user_lang</a></td>";
				

				if ($commentaire!="technique")
					{
					echo "<td width=\"70%\">");
					formulaire ("modif_trad", "index.php#$idx2");
					echo param('idx',"$idx").param('filtre',"$filtre1");
					echo "<input type=\"text\" name=\"valeur\" id=\"$idx\" value=\"".$valeur."\" size=\"100\" onChange=\"this.form.submit();\" >  </form> </td>";
					}
				else 
					echo "<td > </td>";

				$i++;
				}
			echo "</table></div>";
			pied_de_page("x");
			}

		
		function modif_trad($idx, $valeur)
			{
			global $user_lang;

			//$valeur= addslashes($valeur); // T358
			//if ($user_lang!="fr")
				$reponse =command("update z_traduire SET $user_lang = '$valeur' where idx='$idx' ");

			}		
		
		function modif_trad_tech($idx, $valeur)
			{
			global $user_lang;

			$reponse =command("select * from  z_traduire where idx='$idx'");		
			if ($donnees = fetch_command($reponse) ) 
				{
				$commentaire=$donnees["commentaire"];	
				if ($commentaire=="technique")
					$reponse =command("update z_traduire SET commentaire = '' where idx='$idx' ");
				else
					$reponse =command("update z_traduire SET commentaire = 'technique' where idx='$idx' ");
				}

			}
?>