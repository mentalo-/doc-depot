<?PHP

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
			echo "<table><tr><td > Nom  </td><td > Valeur  </td>";
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

?>