<?php
	include "connex_inc.php";
	include 'general.php';
	include 'include_crypt.php';
	include 'exploit.php';	  // backup _tables

	function maj_cle_champ($table, $champ)
		{
		global $ZZ_CLE_nouveau,$ZZ_CLE_ancien,$ZZ_CLE, $mode_cryptage, $mode_cryptage_nouveau, $mode_cryptage_ancien;
		
		echo "<p><table>";
		$reponse =command("select * from $table");
		while ($donnees = mysql_fetch_array($reponse))
			{
			$idx=$donnees["idx"];
			$val_champ=$donnees["$champ"];
			if ($val_champ!="")
				{
				echo "<tr> <td>$table [ $idx ]: </td><td>".$val_champ;
				$ZZ_CLE=$ZZ_CLE_ancien;
				$mode_cryptage= $mode_cryptage_ancien ;
				$val_champ = decrypt($val_champ);
				if ($val_champ!="")
					{
					echo " </td><td>==></td> <td>".$val_champ;
					$ZZ_CLE=$ZZ_CLE_nouveau;
					$mode_cryptage= $mode_cryptage_nouveau ;
					$val_champ = encrypt($val_champ);
					echo "</td> <td>==></td><td> ".$val_champ;
					command("update $table set $champ='$val_champ' where idx='$idx' ");	
					echo "</td><td>MaJ Ok.</td> ";
					}
				else
					echo "</td><td>KO (Clé incorrecte)</td> ";
				}
			}
		echo "</table>";
		}

	function maj_cle_fichier()
		{
		global $ZZ_CLE_nouveau,$ZZ_CLE_ancien,$ZZ_CLE, $mode_cryptage, $mode_cryptage_nouveau, $mode_cryptage_ancien;
		
		echo "<p><table>";
		foreach(glob('backup_chi/*.chi') as $v)
			{
			supp_fichier("backup_chi/tmp");
			echo "<tr> <td>$v :</td>";
			$ZZ_CLE=$ZZ_CLE_ancien;
			$mode_cryptage= $mode_cryptage_ancien ;
			
			decrypt_fichier("$v","backup_chi/tmp");
			if (file_exists("backup_chi/tmp"))
					{
					supp_fichier("$v");
					$ZZ_CLE=$ZZ_CLE_nouveau;
					$mode_cryptage= $mode_cryptage_nouveau ;
					encrypt_fichier("backup_chi/tmp","$v");
					echo "<td>MaJ Ok.</td> ";
					}
				else
					echo "<td>KO (Clé incorrecte)</td> ";
					
			set_time_limit(); // réinitialise le time out à chaque fichier pour éviter de ne traiter qu'une partie des fichiers
			}


		echo "</table>";
		}

		backup_tables(false, $tables = 'r_user');
		/////////////////////////////////////////////////////////////////////////////////////////////////
		// Clés initiales 
		$ZZ_CLE_ancien = "\"'.é(&èà\"è'\"__à\"èèç&éçà&ééè_è_&'";
		$mode_cryptage_ancien= MCRYPT_RIJNDAEL_128;
		//_______________________________________________________________________________________________
		// Nouvelles Clés 
		$ZZ_CLE_nouveau = '1234567890';
		$mode_cryptage_nouveau= MCRYPT_RIJNDAEL_256 ;
		/////////////////////////////////////////////////////////////////////////////////////////////////
		
		
		ajout_log_tech( "!!! Traitement changement de clé de cryptage !!!", "P0");
		echo "Traitement changement de clé de cryptage $ZZ_CLE_ancien  ==> $ZZ_CLE_nouveau (".addslashes($ZZ_CLE).")";
		maj_cle_champ('r_user', 'pw')	;
		maj_cle_champ('r_user', 'lecture')	;
		maj_cle_fichier()	;

?>
</body>