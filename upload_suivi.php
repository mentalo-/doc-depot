<?php  
	session_start(); 
	
	include "connex_inc.php";
	include 'general.php';
	include 'include_charge_image.php';
	
	if (!empty($_FILES)) 
		{          
		$bdd=$_SESSION['support'];	
		$nom=$_SESSION['nom_suivi'];	
		
		if (!file_exists("suivi_mini")) mkdir("suivi_mini");	// création d'un répertoire
		if (!file_exists("suivi/$bdd")) mkdir("suivi/$bdd");	// création d'un répertoire par structure
		if (!file_exists("suivi_mini/$bdd")) mkdir("suivi_mini/$bdd");	// création d'un répertoire par structure
		
		$user=$_SESSION['user'];
		$modif=time();
		$date_jour_gb=date('Y-m-d');
		
		$f=str_replace (' ','_',"$bdd/$nom-".$_FILES['file']['name']);
		
		if ((extension_fichier($f)=="pdf") ||  est_image($f)  ||  est_doc($f) )  // on ne prend en compte que les fichier doc, image et pdf
			if (!file_exists("suivi/$f")) 												// on ne recharge pas un fichier déjà existant
				{
				command("INSERT INTO `$bdd`  VALUES ( '$nom', '$date_jour_gb', '__upload', '$f' ,'$user','$modif','','')");	
				copy($_FILES['file']['tmp_name'],"suivi/$f");

				// miniature
				if ( est_image($f) )
					imagethumb("suivi/$f","suivi_mini/$f");
				else
					if (extension_fichier($f)=="pdf")								
						{
						if ($_SERVER['REMOTE_ADDR']!="127.0.0.1")
							{
							genere_miniature_pdf("suivi/$f" , "suivi_mini/$f" );
							/*
							exec ( "/usr/bin/convert -density 100 suivi/$f suivi/$f.jpg" ) ;  // si PDF et sur serveur OVH alors on crée une miniature
							
							// cas d'un pdf avec plusieurs pasges : ==> On n egarde que la 1ere
							if (file_exists("suivi/$f-0.jpg"))
								{
								rename ("suivi/$f-0.jpg", "suivi/$f.jpg");
								for ($i=1; $i<10; $i++)
									supp_fichier("suivi/$f-$i.jpg");
								}
							
							imagethumb("suivi/$f.jpg","suivi_mini/$f.jpg");
							supp_fichier("suivi/$f.jpg");
							*/
							
							}
						}

				}
		} 
	?>