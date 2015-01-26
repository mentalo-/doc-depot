  <?php  
  
    if (!isset ($_GET["version"]))
		echo "Version ?";
	else
		{  
		include 'general.php';
		include "connex_inc.php"; 
		include 'exploit.php';		
		
		$version = $_GET["version"] ;
	
		echo "<p> ------------------------------------------------------------- Installation sources LIV$version<p>";
		
		// sauvegarde des sources
		archivage_php();
		
		echo "<p>PHP:";			
			foreach(glob("LIV$version/*.php") as $v)
				{
				$d3= explode("/",$v);
				$src=$d3[1];
				echo "<br>- $src, ";
				copy ("LIV$version/$src", $src);
				}
				
		echo "<p>Images:";
			foreach(glob("LIV$version/*.jp*") as $v)
				{
				$d3= explode("/",$v);
				$src=$d3[1];
				echo "<br>- $src, ";
				copy ("LIV$version/$src", "images/$src");
				}

			foreach(glob("LIV$version/*.png") as $v)
				{
				$d3= explode("/",$v);
				$src=$d3[1];
				echo "<br>- $src, ";
				copy ("LIV$version/$src", "images/$src");
				}	

		echo "<p> ------------------------------------------------------------- Mise à jour Base de données ";
				
		echo "<p> Sauvegarde Tables ";
		backup_tables(false);
		
			
		include 'maj_bdd.php';
		
		
		}
			
	?>