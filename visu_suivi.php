<?php  
session_start(); 

error_reporting(E_ALL | E_STRICT);

include 'general.php';

if ( isset($_SESSION['pass']) && ($_SESSION['pass']==true) )
		{
		// methode sans passer par un fichier interm�diaire
		$fichier=variable_s('fichier');	
		$id=rand(1000000,999999999999);
		
		if (est_doc($fichier))
			{
			copy("suivi/$fichier","upload_tmp/$id.".extension_fichier($fichier));
			header("Location: upload_tmp/$id.".extension_fichier($fichier));			
			}
		else
			if (extension_fichier($fichier)=="pdf")
				{
				include "connex_inc.php";
			
				copy("suivi/$fichier","upload_tmp/$id.pdf");
				header("Location: upload_tmp/$id.pdf");			
				}		
			else
				if ( (extension_fichier($fichier)=="jpg") || (extension_fichier($fichier)=="jpeg") ) 
					{
					// D�finit le contenu de l'en-t�te - dans ce cas, image/jpeg
					header('Content-Type: image/jpeg');

					// Cr�ation d'une image vide et ajout d'un texte
					$im = imagecreatefromjpeg("suivi/$fichier");
					imagejpeg($im);

					// Lib�ration de la m�moire
					imagedestroy($im);
			
					}
				else
					if (extension_fichier($fichier)=="png")  
						{
						// D�finit le contenu de l'en-t�te - dans ce cas, image/jpeg
						header('Content-Type: image/png');

						// Cr�ation d'une image vide et ajout d'un texte
						$im = imagecreatefrompng("suivi/$fichier");
						imagepng($im);

						// Lib�ration de la m�moire
						imagedestroy($im);
						}
					else
						if (extension_fichier($fichier)=="gif")  
							{
							// D�finit le contenu de l'en-t�te - dans ce cas, image/jpeg
							header('Content-Type: image/gif');

							// Cr�ation d'une image vide et ajout d'un texte
							$im = imagecreatefromgif("suivi/$fichier" );
							imagegif($im);

							// Lib�ration de la m�moire
							imagedestroy($im);
							}				
			}

		exit();

	
	?>