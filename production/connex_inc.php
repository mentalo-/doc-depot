<?PHP
	// --- ISOD --- //
	// --- Connexion base de donnйes --- //
	
		mysql_connect( "mysql51-112.perso" , "docdepot-bdd" , "E3XfTgdvHmHr" );
		mysql_select_db( "docdepot-bdd" ); 

//	mysql_connect( "sql.free.fr" , "fixeofixeo" , "553649" );
//	mysql_select_db( "fixeofixeo" ); 
//

	$cle= sprintf ("%2.29f", exp(pi()));
	$cle = strtr($cle, '0123456789',
					   '&й"\'(-и_за');
	$ZZ_CLE=$cle;
	//$ZZ_CLE=  "\"'.й(&иа\"и'\"__а\"-аиаи(и\"_-айии&\"";
?>