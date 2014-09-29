<?PHP
	// --- ISOD --- //
	// --- Connexion base de donnйes --- //
	
		mysql_connect( "localhost" , "root" , "" );
		mysql_select_db( "doc-depot" ); 

//	mysql_connect( "sql.free.fr" , "fixeofixeo" , "553649" );
//	mysql_select_db( "fixeofixeo" ); 
//

	$cle= sprintf ("%2.29f", exp(pi()));
	$cle = strtr($cle, '0123456789',
					   '&й"\'(-и_за');
	$ZZ_CLE=$cle;
//	$ZZ_CLE=  "\"'.й(&иа\"и'\"__а\"-аиаи(и\"_-айии&\"";
?>