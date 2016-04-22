  <?php  

	include "connex_inc.php";
	include 'general.php';
	include 'audit_cnil.php';
	
				$reponse =command("select * from fct_fissa  ");
				while ($donnees = mysql_fetch_array($reponse) )
					{
					$periode=date('Y-m',  time());
					$support=$donnees["support"];
					audit_cnil($periode, $support, false);

					}


	?>