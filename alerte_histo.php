<?php 

// ------------------------------------------------------
// DOC-DEPOT : COPYRIGTH ADILEOS - Décembre 2013/Mars 2015

session_start(); 
<?php include 'header.php';	  ?>
error_reporting(E_ALL | E_STRICT);


require_once 'general.php';
require_once 'inc_style.php';
require_once "connex_inc.php";


    echo "<head>";
	echo "<link rel=\"icon\" type=\"image/png\" href=\"images/identification.png\" />";
	echo "<title>Alerte SMS météo </title>";
    echo "<meta http-equiv=\\\"Content-Type\\\" content=\\\"text/html; charset=iso-8859-1\\\" />";
	echo "</head><body>";

	$user_lang='fr';


	// ===================================================================== Bloc IMAGE
		
		debut_cadre("800");
		echo "<tr><td></td><td><a href=\"alerte.php\" > <img id=\"logo\" src=\"images/logo-alerte.jpg\" width=\"240\" height=\"180\" ></a> </td>";
		echo "<tr><td></td><td> <h2>Liste des alertes du jour</h2> </td>";
		$date= date("Y-m-d");
		$r1 = command("SELECT * FROM cc_alerte WHERE  tel='' and creation='$date' order by dept asc ");
		echo "</table></div>";
		debut_cadre("800");
		$ordre=0;
		$c="#d4ffaa"; 
		echo "<tr><td bgcolor=\"$c\" width=\"10%\"> Département </td><td bgcolor=\"$c\"> Message </td>";
		while ($d1 = fetch_command($r1))
			{
			if ($ordre%2)
				$c="#d4ffaa"; 
			else
				$c="";
				
			$dept=$d1["dept"];
			$sueil=$d1["sueil"];
			if ($sueil=="") 
				$sueil="Pas d'alerte";
			echo "<tr><td bgcolor=\"$c\" >$dept</td><td bgcolor=\"$c\" >$sueil </td>";
			$ordre++;
			}
		echo "</table>";
		

		echo "<hr><center> ";

		echo "<table> <tr> <td align=\"right\" valign=\"bottom\" ></td>";
		echo "<td><a id=\"lien_conditions\" href=\"conditions_alerte.html\">".traduire('Conditions d\'utilisation')."</a>";
		echo "- <a id=\"lien_contact\" href=\"index.php?action=contact\">".traduire('Nous contacter')."</a>";
		echo "- Copyright <a href=\"http://adileos.doc-depot.com\">ADILEOS 2015</a>";
		$version= parametre("DD_version_portail") ;
		echo "- $version </td></table> ";	
		fermeture_bdd() ;
		?>
	
    </body>
</html>

