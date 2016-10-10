<?php

  		require_once  'general.php';
		require_once "connex_inc.php"; 
		require_once 'exploit.php';	
		
		function liste_user($libelle, $droit)
			{
			ECHO "<hr><strong>$libelle : </strong>";
			$reponse =command("select * from  r_user where droit='$droit' and mail<>'' and not (mail like '%@fixeo.com%')");
			while ($donnees = fetch_command($reponse) )
				{
				$mail=$donnees["mail"];	
				if (VerifierAdresseMail($mail) )
					echo $mail."; ";
				}		
		 
		}
			ECHO "<center>Liste Utilisateurs</center>";
		
liste_user("Responsables","R");

liste_user("Acteur Social","S");

liste_user("Accueil","P");

liste_user("Mandataire","M");
?>