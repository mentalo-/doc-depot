<?php  

	
// cast-128 gost rijndael-128 twofish arcfour cast-256 loki97 rijndael-192 saferplus wake blowfish-compat des rijndael-256 serpent xtea blowfish enigma rc2 tripledes
// Supported modes 	cbc cfb ctr ecb ncfb nofb ofb stream 	


	$mode_cryptage= MCRYPT_RIJNDAEL_256;

	function encrypt_ltd($data) 
		{
		$data=time()."-".$data;
		return (encrypt($data));
		}
	

	function decrypt_ltd($data, $to = '') 
		{
		$data=decrypt($data);
		$data=substr($data,11); // récupération de la partie utile
		
		// on vérifie que la requette n'est pas périmée si une durée de validité a été communiquée 
		//si c'est cas on retourne une chaine vide
		if ($to!='')
			{
			$t=substr($data,0,10);
			if ($t<time()-$to)
				return('');
			}
			
		return ($data);
		}
		
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
		
?>
