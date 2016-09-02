<?php  
	session_start(); 
	
	require_once "connex_inc.php";
	require_once 'general.php';
 	require_once 'include_crypt.php';
	require_once 'include_charge_image.php';	
	require_once 'exploit.php';	
	
	
	if (!empty($_FILES)) 
		{          
		$tempFile = $_FILES['file']['tmp_name'];          

		$idx=$_SESSION['user_idx'];
		$acteur=$_SESSION['acteur'];
		
		$reponse = command("SELECT * from  r_user WHERE idx='$idx'"); 
		if ($donnees = fetch_command($reponse))
			{
			$code_lecture=$donnees["lecture"];	
			charge_image("0",$tempFile,str_replace(" ","_",$_FILES['file']['name']),$code_lecture,"A-$idx", "" , "Autres", $acteur, $idx);
			}
		else
		 // on demande de charger un document pour un compte qui n'existe pas 
		 ajout_log_tech("Incohérence: Tentative d'ajout de fichier sur compte $idx qui n'existe pas par $acteur !");

		} 
	?>