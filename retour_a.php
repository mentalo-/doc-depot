  <?php  
  
    if (!isset ($_GET["version"]))
		echo "Version ?";
	else
		{  
		include 'general.php';
		include "connex_inc.php"; 
		include 'exploit.php';		
			
		$version = $_GET["version"] ;

		echo "<p> ------------------------------------------------------------- Restoration sources $version<p>";
		
	
		echo "<p>PHP:";			
			foreach(glob("$version/*.php") as $v)
				{
				$d3= explode("/",$v);
				$src=$d3[1];
				echo "<br>- $src, ";
				copy ($v, $src);
				}
				

		echo "<p> ------------------------------------------------------------- Restoration Base de données ";
				
		$version=parametre("DD_version_bdd");
		echo "<p> Version actuelle -> $version <br>";
				
			foreach(glob("$version/*.sql") as $v)
				{
				$f2 = fopen($v,"rb"); 
				$l="";
 				  // On execute les requête écrite dans le fichier jusqu'en feof
				  $ligne = fgets($f2, 4096);
				 while (!feof($f2)) {
						if (strlen($ligne)>1)
							{
							echo "<br>-".strlen($ligne);
							$l.=$ligne;
							if ($ligne[strlen($ligne)-2]==';')
								{
								echo $l;
								//$result=mysql_query( $ligne );
								//if( !result ) 
									  // Traitement de l'erreur sur le DELETE ou INSERT								
								$l="";
								}
							}

					  $ligne = fgets($f2, 4096);
					  }
			 
				  fclose($f2); 
				}
		
		}
			
	?>