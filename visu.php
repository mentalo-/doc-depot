<?php  
session_start(); 

error_reporting(E_ALL | E_STRICT);

include 'general.php';

if ( isset($_SESSION['pass']) && ($_SESSION['pass']==true) )
	switch (variable_s('action'))
		{
		case "exporter":	
			include "connex_inc.php";
			include 'include_crypt.php';
			
			$user_idx=$_SESSION['user_idx'];
			$reponse = command("SELECT * from  r_user WHERE idx='$user_idx'"); 
			$donnees = fetch_command($reponse);
			$id=$donnees["id"];
			if (encrypt(variable("pw"))!=$donnees["pw"]) 
				{
				erreur (traduire("Mot de passe Incorrect").". ");
				break; 
				}
			
			$id=rand(1000000,999999999999);
			$zip = new ZipArchive(); 
			$j=1;
			if ($zip->open("dir_zip/$id.zip", ZipArchive::CREATE) === true)
				{
				$reponse =command("select * from r_attachement where  ref='A-$user_idx' or ref='P-$user_idx' ");
				while (($donnees = fetch_command($reponse) ) && ($j<100))
					{
					$f=$donnees["num"];
					 if(!$zip->addFile('upload/'.$f, $f))
						{
						  echo 'Impossible d&#039;ajouter &quot;'.$f.'&quot;.<br/>';
						}
					$j++; 	
					}
				
				// Ajout d’un fichier avec Notes et SMS
				$zip->addFile('SMS-et-notes.htm');
				$txt="<table>";
				// Ajout direct.
				$reponse =command("select * from  r_sms where (idx='$user_idx') order by date desc");		
				while ($donnees = fetch_command($reponse) ) 
					{
					$date=$donnees["date"];	
					$ligne=stripcslashes($donnees["ligne"]);
					$txt.= "<tr> <td>$date </td><td> $ligne </td>";
					}
				$txt.= "</table>";
				$zip->addFromString('SMS-et-notes.htm',$txt );

				
				// Ajout d’un fichier avec Notes et SMS
				$zip->addFile('historique.htm');
				$txt="<table><tr><td> ".traduire('Date').":   </td><td> ".traduire('Evénement').":</td><td> ".traduire('Acteur').":</td>";

				$reponse =command("select * from  log where (user='$user_idx' ) or (acteur='$user_idx' or acteur='$id') order by date DESC ");		
				while ($donnees = fetch_command($reponse) ) 
					{
					$date=$donnees["date"];	
					$ligne=stripcslashes($donnees["ligne"]);
					$acteur=$donnees["acteur"];
					$ip=$donnees["ip"];
					if (is_numeric($donnees["user"]))
						$user=libelle_user($donnees["user"]);

					if (($acteur!="") && (is_numeric($acteur) ) )
						$acteur=libelle_user($acteur);
					$txt.= "<tr><td title=\"$ip\">  $date  </td><td> $ligne </td><td> $acteur </td>";
					}
	
				$txt.= "</table>";
				$zip->addFromString('historique.htm',$txt );				
				
				$zip->close();
				
				// méthode d'origine
				//header("Location: dir_zip/$id.zip");
				ajout_log($user_idx, traduire("Génération d'un fichier de sauvegarde (fonction export)"),$user_idx);
				header('Content-Transfer-Encoding: binary'); //Transfert en binaire (fichier).
				header('Content-Disposition: attachment; filename="Archive.zip"'); //Nom du fichier.
				header('Content-Length: '.filesize("dir_zip/$id.zip")); 
				readfile("dir_zip/$id.zip");

				ajout_log($_SESSION['user'], traduire("Export des fichiers et données du compte"),$_SESSION['user']);
				}
			break;
		
		// methode sans passer par un fichier intermédiaire
		case "visu_fichier_sans_intermediaire":
			// Connexion BdD
			include "connex_inc.php";

			$fichier=variable_s('num');

			verification_acces( $fichier );

			ajout_log_tech( "Visu_fichier $fichier - ".variable_s('code'));
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment; filename="downloaded.pdf"');
			if (variable_s('code')!="")
				{
				if ( est_image($fichier))
					readfile("upload_prot/$fichier.pdf");
				else
					readfile("upload_prot/$fichier");
				}
			else
				readfile("upload/$fichier");

			$bene= $_SESSION['bene'];
			if ($bene=="") 
				$bene=$_SESSION['user'];
			
			$fichier = substr($fichier,strpos($fichier,".")+1 );

			ajout_log($bene, traduire("Accès au fichier")." $fichier ".traduire('en lecture'),$_SESSION['user']);

			break;		
			
		case "visu_fichier":
			// Connexion BdD
			include "connex_inc.php";
			
			$id=rand(1000000,999999999999);
			$fichier=variable_s('num');

			verification_acces( $fichier );

			ajout_log_tech( "Visu_fichier $fichier - ".variable_s('code'));
			if (variable_s('code')!="")
				{
				if ( est_image($fichier))
					copy("upload_prot/$fichier.pdf","upload_tmp/$id.pdf");
				else
					copy("upload_prot/$fichier","upload_tmp/$id.pdf");
				}
			else
				copy("upload/$fichier","upload_tmp/$id.pdf");
			 
			header("Location: upload_tmp/$id.pdf");			
			$bene= $_SESSION['bene'];
			if ($bene=="") 
				$bene=$_SESSION['user'];
			
			$fichier = substr($fichier,strpos($fichier,".")+1 );

			ajout_log($bene, traduire("Accès au fichier")." $fichier ".traduire('en lecture'),$_SESSION['user']);

			break;

		case "visu_doc":
			// Connexion BdD
			include "connex_inc.php";

			$id=rand(1000,9999);
			$fichier=variable_s('num');

			verification_acces( $fichier );

			copy("upload/$fichier","upload_tmp/$id.$fichier");

			header("Location: upload_tmp/$id.$fichier");			
			$bene= $_SESSION['bene'];
			if ($bene=="") 
				$bene=$_SESSION['user'];
			
			ajout_log($bene, traduire("Accès au fichier")." $fichier ".traduire("en lecture"),$_SESSION['user']);
			pied_de_page();
			break;
			
		case "visu_image_mini":
		
			switch( extension_fichier(variable_s("nom")) ) 
				{
				case "jpg": case "jpeg" :
					// Définit le contenu de l'en-tête - dans ce cas, image/jpeg
					header('Content-Type: image/jpeg');
					$im = imagecreatefromjpeg( "upload_mini/".variable_s("nom"));
					imagejpeg($im);

					// Libération de la mémoire
					imagedestroy($im);
					exit();
					break;	

				case "png": 
					// Définit le contenu de l'en-tête - dans ce cas, image/jpeg
					header('Content-Type: image/png');
					$im = imagecreatefrompng( "upload_mini/".variable_s("nom"));
					imagepng($im);

					// Libération de la mémoire
					imagedestroy($im);
					exit();
					break;
					
				case "gif": 
					// Définit le contenu de l'en-tête - dans ce cas, image/jpeg
					header('Content-Type: image/gif');
					$im = imagecreatefromgif( "upload_mini/".variable_s("nom"));
					imagegif($im);

					// Libération de la mémoire
					imagedestroy($im);
					exit();
					break;
				}
	
		case "visu_image":
			switch( extension_fichier(variable_s("nom")) ) 
				{
				case "jpg": case "jpeg" :
					// Définit le contenu de l'en-tête - dans ce cas, image/jpeg
					header('Content-Type: image/jpeg');

					// Création d'une image vide et ajout d'un texte
					$im = imagecreatefromjpeg("upload/".variable_s("nom") );
					imagejpeg($im);

					// Libération de la mémoire
					imagedestroy($im);
					exit();
					break;
				case "png": 
					// Définit le contenu de l'en-tête - dans ce cas, image/jpeg
					header('Content-Type: image/png');

					// Création d'une image vide et ajout d'un texte
					$im = imagecreatefrompng("upload/".variable_s("nom") );
					imagepng($im);

					// Libération de la mémoire
					imagedestroy($im);
					exit();
					break;	

				case "gif": 
					// Définit le contenu de l'en-tête - dans ce cas, image/jpeg
					header('Content-Type: image/gif');

					// Création d'une image vide et ajout d'un texte
					$im = imagecreatefromgif("upload/".variable_s("nom") );
					imagegif($im);

					// Libération de la mémoire
					imagedestroy($im);
					exit();
					break;				
				}

		default : break;
		}

	
	?>