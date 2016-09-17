<?php
	// on teste le pays d'origine
	if ($_SERVER['REMOTE_ADDR']!="127.0.0.1")
		{
		$pays= strtoupper($_SERVER['GEOIP_COUNTRY_CODE']); 
		if (($pays!="FR")&& ($pays!="")&& ($pays!="UNKNOWN") )  // on n'autorise que la france 
			{
			ajout_log_tech ( "Rejet connexion ".$_SERVER['REMOTE_ADDR']." car pays = '$pays' ", "P1" );
			aff_logo("x");
			echo "<p>".traduire('Service only available from France ');
			pied_de_page(); 
			}
		}
?>