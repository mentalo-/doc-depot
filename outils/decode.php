<?php  

	
// cast-128 gost rijndael-128 twofish arcfour cast-256 loki97 rijndael-192 saferplus wake blowfish-compat des rijndael-256 serpent xtea blowfish enigma rc2 tripledes
// Supported modes 	cbc cfb ctr ecb ncfb nofb ofb stream 	

	function decrypt($data) 
		{
		global $ZZ_CLE,$mode_cryptage  ;
		
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

$mode_cryptage= MCRYPT_RIJNDAEL_128;	
Echo "Valeur à décoder :";
				echo "<form method=\"POST\" action=\"decode.php\">";
				echo "<input type=\"text\" size=\"50\" name=\"val\" value=\"\"> " ;
				echo "</form>";	
echo "<hr>"			;	
if (isset ($_POST["val"]))
	{
	$ZZ_CLE=  "\"'.é(&èà\"è'\"__à\"èèç&éçà&ééè_è_&'";
	echo $ZZ_CLE."==>".decrypt ($_POST["val"]);	
	echo "<hr>";
	$ZZ_CLE=  "\"'.é(&èà\"è'\"__à\"-àèàè(è\"_-àéèè&\"";
	echo $ZZ_CLE."==>".decrypt ($_POST["val"]);			
	}
	?>