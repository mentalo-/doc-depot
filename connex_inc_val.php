<?PHP
	// --- ISOD --- //
	// --- Connexion base de donn�es --- //
	
		if 
			(
			(!mysql_connect( "fixeofrkud_val.mysql.db" , "fixeofrkud_val" , "VgEZxd5a6DUU" )) 
			or 
			(!mysql_select_db( "fixeofrkud_val" ))
			)
			{
			echo "<div id=\"logo\"> <center><a href=\"index.php\"><img src=\"images/logo.png\" width=\"200\" height=\"150\" ></a> </div>  <center>";	
			echo "<p><b><font size=\"5\">D�sol�, service satur�. Merci d'essayer de vous connecter plus tard.</b></font>";
			exit();
			}

		
	$cle= sprintf ("%2.29f", exp(pi()));
	$cle = strtr($cle, '0123456789',
					   '&�"\'(-�_��');
	$ZZ_CLE=$cle;
	
	//$ZZ_CLE=  "\"'.�(&��\"�'\"__�\"-����(�\"_-����&\"";
	$ZZ_CLE=  "\"'.�(&��\"�'\"__�\"���&���&���_�_&'";
?>