<?php  
	session_start(); 
	
	include "connex_inc.php";
	include 'general.php';

	
	if (!empty($_FILES)) 
		{          
		$tempFile = $_FILES['file']['tmp_name'];          
	
		$bdd=$_SESSION['support'];	
		$nom=$_SESSION['nom_suivi'];	
		
		if (!file_exists("suivi/$bdd")) mkdir("suivi/$bdd");	// création d'un répertoire par structure
		
		$user=$_SESSION['user'];
		$modif=time();
		$date_jour_gb=date('Y-m-d');

		$f=str_replace (' ','_',"suivi/$bdd/$nom-".$_FILES['file']['name']);
		
		if ((extension_fichier($f)=="pdf") ||  est_image($f)  ||  est_doc($f) )  // on ne prend en compte que les fichier doc, image et pdf
			if (!file_exists("$f")) 												// on ne recharge pas un fichier déjà existant
				{
				command("INSERT INTO `$bdd`  VALUES ( '$nom', '$date_jour_gb', '__upload', '$f' ,'$user','$modif','','')");	
				copy($_FILES['file']['tmp_name'],"$f");
				if (extension_fichier($f)=="pdf")								
					{
					if ($_SERVER['REMOTE_ADDR']!="127.0.0.1")
						exec ( "/usr/bin/convert -density 100 $f $f.jpg" ) ;  // si PDF et sur serveur OVH alors on crée une miniature
					}
				}
		} 
	?>