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
				
			foreach(glob("$version/*.sql") as $filename)
				{
				echo "<hr> Fichier -> '$filename' <br>";
				
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
		
		}
		


/*


    //ENTER THE RELEVANT INFO BELOW
    $mysqlDatabaseName ='online_admission_form';
    $mysqlUserName ='root';
    $mysqlPassword ='';
    $mysqlHostName ='localhost';
    $mysqlImportFilename ='db-backup-1360387884-770ac5920c7155e73215540b30ed1c18.sql';
	
    //DONT EDIT BELOW THIS LINE
    //Export the database and output the status to the page
    $command='mysql -h' .$mysqlHostName .' -u' .$mysqlUserName .' -p' .$mysqlPassword .' ' .$mysqlDatabaseName .' < ' .$mysqlImportFilename;
    exec($command,$output=array(),$worked);
    switch($worked){
    case 0: echo 'Import file <b>' .$mysqlImportFilename .'</b> successfully imported to database <b>' .$mysqlDatabaseName .'</b>';    break;
    case 1:  echo 'There was an error during import. Please make sure the import file is saved in the same folder as this script and check your values:<br/><br/><table><tr><td>MySQL Database Name:</td><td><b>' .$mysqlDatabaseName .'</b></td></tr><tr><td>MySQL User Name:</td><td><b>' .$mysqlUserName .'</b></td></tr><tr><td>MySQL Password:</td><td><b>NOTSHOWN</b></td></tr><tr><td>MySQL Host Name:</td><td><b>' .$mysqlHostName .'</b></td></tr><tr><td>MySQL Import Filename:</td><td><b>' .$mysqlImportFilename .'</b></td></tr></table>';     break;
    }


*/
	?>