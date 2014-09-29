  <?php  

	echo "<head>";
	echo "</head><body>";
	include "connex_inc.php";
	include 'general.php';
 	include 'include_crypt.php';
	
	if ($_SERVER['REMOTE_ADDR']=="127.0.0.1")	
		{
		$id= variable_s("id");
		$idx =variable_s("idx");
		$reponse =command("","select * from  r_user where id='$id' and idx='$idx' ");
		if ($donnees = mysql_fetch_array($reponse) ) 
			{
			$pw =variable_s("pw");
			$reponse =command("","UPDATE r_user set pw='".encrypt($pw)."' where id='$id' and idx='$idx'");		
			Echo "Maj faite";
			}
		else
			Echo "Non trouvé";
		
		}
	echo "</body>";

	
	?> 