  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0trict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

    <head>
  
  <?php  

	include "connex_inc.php";
	include 'general.php';
	include 'audit_cnil.php';
	
		//
	$reponse =command("select * from fct_fissa  ");
	while ($donnees = mysql_fetch_array($reponse) )
		{
		$periode=date('Y-m',  time());
		$support=$donnees["support"];
		audit_cnil($periode, $support, false);
		}


	?>