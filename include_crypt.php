  <?php  

	
// cast-128 gost rijndael-128 twofish arcfour cast-256 loki97 rijndael-192 saferplus wake blowfish-compat des rijndael-256 serpent xtea blowfish enigma rc2 tripledes
// Supported modes 	cbc cfb ctr ecb ncfb nofb ofb stream 	


	$mode_cryptage= MCRYPT_RIJNDAEL_128;

	function encrypt($data) 
		{
		global  $ZZ_CLE, $mode_cryptage;
		
		$data = serialize($data);
		$td = mcrypt_module_open($mode_cryptage,"",MCRYPT_MODE_ECB,"");
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td,$ZZ_CLE,$iv);
		$data = base64_encode(mcrypt_generic($td, '!'.$data));
		mcrypt_generic_deinit($td);
		return $data;
		}


	function decrypt($data) 
		{
		global  $ZZ_CLE, $mode_cryptage;
		
		$td = mcrypt_module_open($mode_cryptage,"",MCRYPT_MODE_ECB,"");
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td,$ZZ_CLE,$iv);
		$data = mdecrypt_generic($td, base64_decode($data));
		mcrypt_generic_deinit($td);
	 
		if (substr($data,0,1) != '!')
			return false;
	 
		$data = substr($data,1,strlen($data)-1);
		return unserialize($data);
		}
		
	function encrypt_fichier($chemin_fichier,$chemin1_fichier)
		{
		 if (file_exists($chemin_fichier))
			{//verifie presence du fichier
			chmod($chemin_fichier,0777);//attribue tous droits
			$ancien = fopen($chemin_fichier, "rb");
			$nouveau = fopen($chemin1_fichier, "wb");
			$line = fread($ancien, filesize($chemin_fichier));
			fwrite ($nouveau, encrypt($line));
			fclose($ancien);
			fclose($nouveau);
			}
		 }
	 
	function decrypt_fichier($chemin1_fichier,$chemin2_fichier)
		{
		 if (file_exists($chemin1_fichier))
			{//verifie presence du fichier
			$ancien = fopen($chemin1_fichier, "rb");
			$nouveau = fopen($chemin2_fichier, "wb");
			$line = fread($ancien, filesize($chemin1_fichier));
			fwrite ($nouveau, decrypt($line));
			fclose($ancien);
			fclose($nouveau); 
			}
		}
	
	function dde_chgt_cle()
		{
		global $ZZ_CLE;
		
		echo "Demande de changement de clé de cryptage $ZZ_CLE";
		echo "<TABLE><TR><td>";
		formulaire ("chgt_cle");
		echo "<TR> <td>Ancienne clé: </td><td><input class=\"center\" type=\"password\" name=\"ancien\" value=\"\"/></td>";
		echo "<TR>  <td><input type=\"submit\"  id=\"chgt_cle\"  value=\"Modifier\"/></td>";
		echo "</form> </table> ";		
		pied_de_page("x");
		}

	function maj_cle_champ($idx, $champ)
		{
		global $ZZ_CLE_org,$ZZ_CLE_ancien,$ZZ_CLE;
		
		$ZZ_CLE=$ZZ_CLE_ancien;
		$reponse =command("","select * from r_user where idx='$idx' ");		
		$donnees = mysql_fetch_array($reponse) ;
		$val_champ=$donnees["$champ"];	
		//echo "<br>".$val_champ;
		$val_champ = decrypt($val_champ);
		if ($val_champ!="")
			{
			//echo " <br> ".$val_champ;
			$ZZ_CLE=$ZZ_CLE_org;
			$val_champ = encrypt($val_champ);
			//echo " <br> ".$val_champ;
			//$reponse =command("","upadte r_user set $champ='$val_champ' where idx='$idx' ");	
			echo "MaJ Ok. ";
			}
		else
			echo "KO (Clé incorrecte) ";
		}
		
	function chgt_cle()
		{
		global $ZZ_CLE_org,$ZZ_CLE_ancien,$ZZ_CLE;
		
		$ZZ_CLE_org = $ZZ_CLE;
		$ZZ_CLE_ancien=stripcslashes(stripcslashes(variable("ancien")));
		
		echo "Traitement changement de clé de cryptage $ZZ_CLE_ancien / $ZZ_CLE";

		$reponse =command("","select * from r_user ");		
		while ($donnees = mysql_fetch_array($reponse) ) 
			{
			$idx=$donnees["idx"];	
			echo "<br> User  $idx : ";
			maj_cle_champ($idx, "pw")	;		
			}
		pied_de_page("x");
		}
		
	?> 