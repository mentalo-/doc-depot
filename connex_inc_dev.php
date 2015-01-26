<?PHP
	// --- ISOD --- //
	// --- Connexion base de données --- //
	
		if 
			(
			(!mysql_connect( "fixeofrkud_dev.mysql.db" , "fixeofrkud_dev" , "unf3mGRMKcdJ" )) 
			or 
			(!mysql_select_db( "fixeofrkud_dev" ))
			)
			{
			echo "<div id=\"logo\"> <center><a href=\"index.php\"><img src=\"images/logo.png\" width=\"200\" height=\"150\" ></a> </div>  <center>";	
			echo "<p><b><font size=\"5\">Désolé, service saturé. Merci d'essayer de vous connecter plus tard.</b></font>";
			exit();
			}

		
	$cle= sprintf ("%2.29f", exp(pi()));
	$cle = strtr($cle, '0123456789',
					   '&é"\'(-è_çà');
	$ZZ_CLE=$cle;
	
	//$ZZ_CLE=  "\"'.é(&èà\"è'\"__à\"-àèàè(è\"_-àéèè&\"";
	$ZZ_CLE=  "\"'.é(&èà\"è'\"__à\"èèç&éçà&ééè_è_&'";
?>