<?php  
	session_start(); 
	
	include "connex_inc.php";
	include 'general.php';
 	include 'include_crypt.php';
	include 'include_charge_image.php';	
	include 'exploit.php';	
	
	
	if (!empty($_FILES)) 
		{          
		$tempFile = $_FILES['file']['tmp_name'];          

		$idx=$_SESSION['user_idx'];

		$reponse = command("SELECT * from  r_user WHERE idx='$idx'"); 
		$donnees = fetch_command($reponse);
		$code_lecture=$donnees["lecture"];	
		

		charge_image("0",$tempFile,str_replace(" ","_",$_FILES['file']['name']),$code_lecture,"P-$idx", "" , "Autres", $idx, $idx);

		} 
	?>