  <?php  

	include "connex_inc.php";
	include 'general.php';

	function purge_doublons($table)
		{
		echo "<p> - - - - - - - - - - - - - - - - - - - - - - - - ";
		command("drop table tmp");
		command("CREATE TABLE tmp LIKE $table ;","x"); 
		
		$reponse = command("SELECT count(*) as TOTAL FROM $table "); 
		$donnees = fetch_command($reponse) ;
		$avant=$donnees["TOTAL"];
		
		command("INSERT INTO tmp SELECT distinct * FROM $table ;","x");
		
		command("drop table $table","x");
		command("rename table tmp to $table","x");		 
		
		$reponse = command("SELECT count(*) as TOTAL FROM $table "); 
		$donnees = fetch_command($reponse) ;
		$apres=$donnees["TOTAL"];
		echo "<p> $table  : $avant ==> $apres ";		
		}
	
	command("drop table tmp","x");
	$reponse =command("select * from fct_fissa  ");
	while ($donnees = mysql_fetch_array($reponse) )
			{
			$support=$donnees["support"];
			purge_doublons($support);
			}

	?> 