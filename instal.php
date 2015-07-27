  <?php  
  
   function installer($titre, $version, $ext, $dest ) 
		{
		echo "<p>$titre : $ext ";	
  		foreach(glob("LIV$version/*$ext") as $v)
			{
			$d3= explode("/",$v);
			$src=$d3[1];
			echo "<br>- $src, ";
			copy ("LIV$version/$src", $dest.$src);
			}
		}
			
    if (isset ($_GET["version"]))
		{  
		include 'general.php';
		include "connex_inc.php"; 
		include 'exploit.php';		
		
		$version = $_GET["version"] ;
		
		if (file_exists ("LIV$version" ))
			{
			echo "<p> ------------------------------------------------------------- Installation sources LIV$version<p>";
			
			// sauvegarde des sources
			archivage_php();
			
			installer("PHP", $version, ".php", "" );
			installer("HTML", $version, ".htm*", "" );
			installer("JS", $version, ".js", "" );
			installer("Excell", $version, ".xlsx", "" );
			installer("Images", $version, ".jp*", "images/" );
			installer("Images", $version, "*.png", "images/" );
			installer("Images", $version, "*.gif", "images/" );

			echo "<p> ------------------------------------------------------------- Mise à jour Base de données ";
					
			echo "<p> Sauvegarde Tables ";
			backup_tables(false);
				
			include 'maj_bdd.php';
			echo "<p> ====> <a href=\"http://adileos.jimdo.com/documentations/technique/\"> Mettre à jour le Journal technique </a> ";
			}
		
		}
			
	?>