<?php  
///////////////////////////////////////////////////////////////////////
//   This file is part of doc-depot.
//
//   doc-depot is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
//
//   doc-depot is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
//
//   You should have received a copy of the GNU General Public License along with doc-depot.  If not, see <http://www.gnu.org/licenses/>.
///////////////////////////////////////////////////////////////////////

session_start(); 

error_reporting(E_ALL | E_STRICT);

include 'general.php';
$fichier=variable_s('fichier');	
if ( isset($_SESSION['pass']) && ($_SESSION['pass']==true) )
	if (strpos($fichier, $_SESSION['support'])!=0) 
		{
		ajout_log_tech ( "Acces fichier interne illicite '$fichier' par user =".$_SESSION['user_idx']." sur base ".$_SESSION['support'], "P0" );
		}
	else
		{
		// methode sans passer par un fichier intermédiaire
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
					// Définit le contenu de l'en-tête - dans ce cas, image/jpeg
					header('Content-Type: image/jpeg');

					// Création d'une image vide et ajout d'un texte
					$im = imagecreatefromjpeg("suivi/$fichier");
					imagejpeg($im);

					// Libération de la mémoire
					imagedestroy($im);
			
					}
				else
					if (extension_fichier($fichier)=="png")  
						{
						// Définit le contenu de l'en-tête - dans ce cas, image/jpeg
						header('Content-Type: image/png');

						// Création d'une image vide et ajout d'un texte
						$im = imagecreatefrompng("suivi/$fichier");
						imagepng($im);

						// Libération de la mémoire
						imagedestroy($im);
						}
					else
						if (extension_fichier($fichier)=="gif")  
							{
							// Définit le contenu de l'en-tête - dans ce cas, image/jpeg
							header('Content-Type: image/gif');

							// Création d'une image vide et ajout d'un texte
							$im = imagecreatefromgif("suivi/$fichier" );
							imagegif($im);

							// Libération de la mémoire
							imagedestroy($im);
							}				
			}

		exit();

	
	?>