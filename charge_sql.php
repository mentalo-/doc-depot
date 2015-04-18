  <?php  
  
    if (isset ($_GET["f"]))
		{  
		include 'general.php';
		include "connex_inc.php"; 
		include 'exploit.php';		
			
		$filename = $_GET["f"] ;
		echo "<p> Fichier -> '$filename' <br>";
		
		$nbc=0;
		$templine = '';
		$lines = file($filename); // Read entire file
		foreach ($lines as $line)
			{

			if (substr($line, 0, 2) == '--' || $line == '')   // Skip all comments ;
				$templine = '';
				else
				$templine .= $line;
				
			if (substr(trim($line), -1, 1) == ';')
				{
				$nbc++;
				mysql_query($templine) or print('Error: '.mysql_error() . '<br>$templine');
				$templine = '';
				}
				
			}
		echo "<hr> $nbc Requêtes exécutées.";

		}
	?>