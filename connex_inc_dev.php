<?PHP
	// --- ISOD --- //
	// --- Connexion base de donn�es --- //
	
	$dbh = new PDO('mysql:host=fixeofrkud_dev.mysql.db;dbname=fixeofrkud_dev', 'fixeofrkud_dev', 'unf3mGRMKcdJ');
	if (!$dbh) {
		echo "<div id=\"logo\"> <center><a href=\"index.php\"><img src=\"images/logo.png\" width=\"200\" height=\"150\" ></a> </div> <center>";
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
