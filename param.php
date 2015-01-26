<?PHP
// traduire() : Ok

function parametre($nom, $par_defaut="")
	{
	$reponse =command("","select * from  DD_param where nom='$nom' ");
	if ($donnees = mysql_fetch_array($reponse) ) 
		return ($donnees["valeur"]);
	else
		command("","INSERT INTO DD_param VALUES ('$nom', '$par_defaut' ) ");
	return ("");
	}

function ecrit_parametre($nom, $valeur)
	{
	$reponse =command("","select * from  DD_param where nom='$nom' ");
	if ($donnees = mysql_fetch_array($reponse) ) 
		$reponse =command("","update DD_param set valeur='$valeur' where nom='$nom' ");
	else
		command("","INSERT INTO DD_param VALUES ('$nom', '$valeur' ) ");	}
	
// zone définitions --------------------------------------
if ($_SERVER['REMOTE_ADDR']!="127.0.0.1")
	define('serveur','https://doc-depot.com/');
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
			$reponse =command("","select * from  DD_param $filtre order by nom ASC");		
			while ($donnees = mysql_fetch_array($reponse) ) 
				{
				$nom=$donnees["nom"];	
				$valeur=$donnees["valeur"];
				echo "<tr><td><a href=\"index.php?action=param_sys&nom=".encrypt($nom)."\"> $nom <a></td>";

				if ( $nom_a_modifier!=$nom) 
					echo "<td> $valeur </td>";
				else
					echo "<td><form method=\"post\" > <input  type=\"hidden\" name=\"action\" value=\"modif_valeur_param\"/> ".param('nom',"$nom")."<input type=\"text\" name=\"valeur\" value=\"$valeur\" onChange=\"this.form.submit();\" >  </form> </td>";
				}
			echo "</table></div>";
			pied_de_page("x");
			}

		
		function modif_valeur_param($nom, $valeur)
			{
			$valeur= addslashes($valeur);
			$reponse =command("","update DD_param SET valeur = '$valeur' where nom='$nom' ");
			}

			
		function traduction()
			{
			global $user_lang;
			
			$filtre1=variable("filtre");
			$nom_a_modifier=variable_get("nom");

			if ($user_lang=="fr") $user_lang="gb";
			echo "<table><tr> ";
			echo "<td><a href=\"index.php?action=gb\" ><img width=\"25\" border=\"0\" height=\"18\" title=\"anglais\" alt=\"anglais\" src=\"images/flag_gb.png\"/></a></td><td> | </td>";
			echo "<td><a href=\"index.php?action=de\" ><img width=\"25\" border=\"0\" height=\"18\" title=\"allemand\" alt=\"allemand\" src=\"images/flag_de.png\"/></a></td><td> | </td>";
			echo "<td><a href=\"index.php?action=es\" ><img width=\"25\" border=\"0\" height=\"18\" title=\"espagnol\" alt=\"espagnol\" src=\"images/flag_es.png\"/></a></td><td> | </td>";
			echo "<td><a href=\"index.php?action=ru\" ><img width=\"25\" border=\"0\" height=\"18\" title=\"russe\" alt=\"russe\" src=\"images/flag_ru.png\"/></a></td><td> | </td>";

			Echo "<td>Langue = $user_lang </td></table><hr>";
			
			formulaire ("trad");
			echo "<table><tr><td > Filtre : <input type=\"text\" name=\"filtre\" size=\"20\" value=\"$filtre1\" onChange=\"this.form.submit();\"> ";
			echo "</form><td><img src=\"images/loupe.png\"width=\"20\" height=\"20\">  </td></table> ";

			if ($filtre1!="")
				$filtre=" where (fr REGEXP '$filtre1' or $user_lang REGEXP '$filtre1') ";
			else
				$filtre="";

			echo "</table><div class=\"CSSTableGenerator\" > ";
			echo "<table><tr><td > ".traduire('Nom')."  </td><td > ".traduire('Valeur')."  </td>";
			$reponse =command("","select * from  z_traduire $filtre order by fr ASC");		
			while ($donnees = mysql_fetch_array($reponse) ) 
				{
				$nom=$donnees["fr"];	
				$valeur=$donnees["$user_lang"];
				$idx=$donnees["idx"];	
				echo "<tr><td width=\"30%\"> $idx - $nom ";
				
				$cible_g=$user_lang;
				if ($user_lang=="gb")
					$cible_g="en";
					
				echo "<a id=\"E$idx\" href=\"https://translate.google.com/?hl=fr#fr/$cible_g/$nom\"  target=_blank>$user_lang</a>";
					
				echo "</td><td width=\"70%\"><form method=\"post\" > <input  type=\"hidden\"  name=\"action\" value=\"modif_trad\"/> ".param('idx',"$idx").param('filtre',"$filtre1")."<input type=\"text\" name=\"valeur\" id=\"S$idx\" value=\"$valeur\" size=\"100\" onChange=\"this.form.submit();\" >  </form> </td>";
				}
			echo "</table></div>";
			pied_de_page("x");
			}

		
		function modif_trad($idx, $valeur)
			{
			global $user_lang;
			
			$valeur= addslashes($valeur);
			if ($user_lang!="fr")
				$reponse =command("","update z_traduire SET $user_lang = '$valeur' where idx='$idx' ");

			}
?>