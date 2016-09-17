  <?php  
 // traduire() : Ok (inutile)
 
 	function sens_image ( $num )
		{
		if (file_exists($num))
			{
			$size = getimagesize($num);	
			if ($size[0]>$size[1])
				return('L');
			}
		return('P');		
		}
		
 function genere_fichier ($source, $pw )
	{
	if (!file_exists("upload_chi/$source.chi"))
		return;
		
	supp_fichier("upload/$source");		
	supp_fichier("upload_mini/$source");		
	supp_fichier("upload_mini/-$source");	
	supp_fichier("upload_pdf/$source");		
	supp_fichier("upload_prot/$source");		
	
	echo "<p>Regénération source";
	decrypt_fichier("upload_chi/$source.chi","upload/$source");

	echo "<p>Regénération Miniature";	
	$sens='P';
	if ( est_image($source) )
		{
		$size = getimagesize("upload/$source");	
		$hauteur=250;
		// mode paysage, a4, etc.
		if ($size[0]>$size[1])
			$sens='L';
		else
			$hauteur=190;
		imagethumb("upload/$source","upload_mini/$source",$hauteur);
		}
	else
		{
		if ($_SERVER['REMOTE_ADDR']!="127.0.0.1")
			{
		//	$im = new Imagick("upload/$source");
		//	$im->writeImage("upload/$source.jpg");
			exec ( "/usr/bin/convert -density 100 upload/$source upload/$source.jpg" ) ;
			$hauteur=250;
			imagethumb("upload/$source.jpg","upload_mini/$source",$hauteur);
			supp_fichier("upload/$source.jpg");
			}
		}
	
	if ($_SESSION['droit']!='')
		met_point_vert($source);

	echo "<p>Ajout cadenas";			
	met_cadenas($source);	
	
	if ( est_image($source) )
		{
		if ($sens=="L")
			{
			echo "<p>Rotation image";			
			rename ("upload/$source","upload/-$source");
			rotateImage("upload/-$source","upload/$source",90);
			supp_fichier("upload/-$source");
			}
					
		require_once('fpdi/fpdf.php');
		echo "<p>Image en PDF";	
		$pdf= new  FPDF("P",'mm','A4');
					
		$pdf->Open();

		// champs facultatifs
		$pdf->SetAuthor('JM Cot');
		$pdf->SetCreator('CygnusEd 4.21 & Fpdf');
		$pdf->SetTitle('Doc-Depot');
		$pdf->SetSubject('Image');
		$pdf->SetMargins(0,0);
		$pdf->AddPage();
		$type_image=strtoupper(extension_fichier($source));
		$pdf->Image("upload/$source",0,0,210,297,$type_image);
		$pdf->Output("upload_pdf/$source.pdf");
		}
		
	echo "<p>Cryptatge PDF";	
	if ( est_image($source) )
		pdfEncrypt("upload_pdf/$source.pdf", decrypt($pw) , "upload_prot/$source.pdf","P" );
	else
		{
		pdfEncrypt("upload_pdf/$source", decrypt($pw) , "upload_prot/$source","P" );
		if( !file_exists("upload_prot/$source") ) 
			copy("upload_pdf/$source", "upload_prot/$source" );
		
		}
	}

	
	function existe_doublon($ref, $nom,$taille)
		{
		$reponse =command("select * from r_attachement where ref='$ref' ");
	 	while ($donnees = fetch_command($reponse) ) 
			{
			$num=$donnees["num"];
			$l_num=strstr($num,".");
			$l_nom=strstr($nom,".");
			if ($l_num==$l_nom)
				{
				if ($taille==filesize("upload/$num"))
						 return (true);
				}
			}	
		 return (false);
		 }
	 
 // $nom1 source du fichier source
 // $n = nom 
  function charge_image($mode,$nom1,$n,$pw,$ref, $sujet,$type, $acteur="", $user="")
		{
		global $user_idx;
	
		$sens_doc_original="P";
		
		if (!isset($user_idx))
			$user_idx="";
			
		$ext= extension_fichier($n);
		if ( strpos(  parametre("DD_alerte_extension_fichier"), $ext )!="")
			{
			alerte_SMS ("tentative de chargement de fichiers suspects '$n' par ".libelle_user($user_idx));
			ajout_log( $user, "Chargement fichier type interdit $n ",$acteur );		
			erreur("Type de fichier interdit.");
			return (false);
			}		

		if ( !est_image($n) && !est_doc($n) && !($ext=="pdf") && !($ext=="vcf") )
			{
			erreur (" Type de fichier non accepté.");
			return (false);
			}	
			
		// cas VCF 
		if ($ext=="vcf")
			{
			include ("vcard.php");
			if ($mode=="1") // chargement par mail
				{
				$ligne= extrait_vcard("$nom1");
				supp_fichier ($nom1);
				}
			else
				$ligne= extrait_vcard("$nom1");

			ajoute_note($user, $ligne);
			return(true);
			}
		
		$taille = filesize( $nom1 );
	
		if  ($taille==0) 
			{
			erreur(" Fichier vide.");
			return (false);	
			}
			
		if ( ($taille> TAILLE_FICHIER) && (!est_image($n))) 
			{
			erreur(" Fichier trop gros");
			return (false);	
			}
		
		$n= strtr($n, "'",  " " );
		if ($sujet=="")
			$sujet=$n;
		$idx=inc_index("upload");
		
		if ($mode=="1") // chargement par mail
			{
			$r = copy("$nom1","upload/$idx.$n");
			supp_fichier ($nom1);
			}
		else
			$r = move_uploaded_file($nom1,"upload/$idx.$n");

		encrypt_fichier("upload/$idx.$n","upload_chi/$idx.$n.chi");

		if ( ($taille> TAILLE_FICHIER) && (est_image($n))) 
			{
			rename("upload/$idx.$n","upload/org.$idx.$n");
			imagethumb("upload/org.$idx.$n","upload/$idx.$n",4000);
			}

		if ( est_image($n) )
				{
				$size = getimagesize("upload/$idx.$n");	
				$hauteur=250;
				// mode paysage, a4, etc.
				if ($size[0]>$size[1])
					{
					$sens='L';
					$sens_doc_original='L';
					}
				else
					{
					$sens='P';
					$hauteur=190;
					}
					
				imagethumb("upload/$idx.$n","upload_mini/$idx.$n",$hauteur);
				
				if ($_SESSION['droit']!='')
					met_point_vert("$idx.$n")	;		
					
				met_cadenas("$idx.$n");
	
				if (substr($ref,0,1)=="A")
					{
					if ($sens=="L")
						{
						rename ("upload/$idx.$n","upload/-$idx.$n");
						rotateImage("upload/-$idx.$n","upload/$idx.$n",90);
						supp_fichier("upload/-$idx.$n");
						}
						
					require_once('fpdi/fpdf.php');

					$pdf= new  FPDF("P",'mm','A4');
						
					$pdf->Open();

					// champs facultatifs
					$pdf->SetAuthor('JM Cot');
					$pdf->SetCreator('CygnusEd 4.21 & Fpdf');
					$pdf->SetTitle('Doc-Depot');
					$pdf->SetSubject('Image');

					$pdf->SetMargins(0,0);
					$pdf->AddPage();

					$type_image=strtoupper(extension_fichier($n));
					$pdf->Image("upload/$idx.$n",0,0,210,297,$type_image);
							
					$pdf->Output("upload_pdf/$idx.$n.pdf");
					
					if ($pw!="")
						pdfEncrypt("upload_pdf/$idx.$n.pdf", decrypt($pw) , "upload_prot/$idx.$n.pdf","P");
					}
				}
			else
				if (extension_fichier($n)=="pdf")
					{
					$r = copy("upload/$idx.$n","upload_pdf/$idx.$n");
					
					if ($_SERVER['REMOTE_ADDR']!="127.0.0.1")
						{
						genere_miniature_pdf("upload_pdf/$idx.$n" , "upload_mini/$idx.$n" ,250 );
						/*
						exec ( "/usr/bin/convert -density 100 upload_pdf/$idx.$n upload_mini/_$idx.$n.jpg" ) ;
						$hauteur=250;
						imagethumb("upload_mini/_$idx.$n.jpg","upload_mini/$idx.$n.jpg",$hauteur);
						supp_fichier("upload_mini/_$idx.$n.jpg");
						*/	
						if ($_SESSION['droit']!='')
							met_point_vert("$idx.$n.jpg");
						met_cadenas("$idx.$n.jpg");	
						}
					if ($pw!="")
						pdfEncrypt("upload_pdf/$idx.$n", decrypt($pw) , "upload_prot/$idx.$n","P" );
					}
				else
					if (!est_doc($n))  // si c'est un doc on garde
						 {
						 supp_fichier ("upload/$idx.$n");
						 supp_fichier ("upload_chi/$idx.$n.chi");
						 }
						 
		// on fait le traitement à la fin pour tenir compte des actions sur le fichier d'origine (redimmensionnement, rotation,etc) 

		
		if (existe_doublon($ref, "$idx.$n",filesize( "upload/$idx.$n" )))
			{
			erreur("Fichier déjà existant");
			supp_attachement ("$idx.$n");
			return (false);	
			}		
	
		if ( est_image($n) || est_doc($n) || (extension_fichier($n)=="pdf")  )
				{
				if ($r) 
					{

					$ident=$sujet;
					$date=	$date_jour=date('Y-m-d')." ".$heure_jour=date("H\hi");
					if ($ref[0]=='A') $t='A'; else $t='P';
					$u = substr($ref,strpos($ref,"-")+1 );
					$reponse = command("INSERT INTO `r_attachement`  VALUES ( '$ref', '$idx.$n', '$date', '$type', '$ident','','','','','','$acteur', '$sens_doc_original','$idx','$u','$t')");
					if ($mode=="1")
						ajout_log( $user, "Ajout fichier par Mail $n / Type : $type / $ident" ,$acteur);
					else
						ajout_log( $user, "Chargement fichier $n / Type : $type / $ident",$acteur );		
					
					$num = "$idx.$n";
					ctrl_une_signature("hash_chi", "upload_chi/".$num.".chi" , $num);
					ctrl_une_signature("hash", "upload/".$num, $num);			
					ctrl_une_signature("hash_pdf", "upload_pdf/".$num, $num);			
					ctrl_une_signature("hash_prot", "upload_prot/".$num, $num);
					ctrl_une_signature("hash_mini", "upload_mini/".$num, $num);

					return (true);
					}
				else
					erreur ("Chargement Fichier '$n' Echec");
				}
			else
				erreur (" Type de fichier non accepté.");

		return (false);	
		}

		// http://code.seebz.net/p/imagethumb/
function imagethumb( $image_src , $image_dest = NULL , $max_size = 100, $expand = FALSE, $square = FALSE )
{
	if( !file_exists($image_src) ) return FALSE;

	// Récupère les infos de l'image
	$fileinfo = getimagesize($image_src);
	if( !$fileinfo ) return FALSE;

	$width     = $fileinfo[0];
	$height    = $fileinfo[1];
	$type_mime = $fileinfo['mime'];
	$type      = str_replace('image/', '', $type_mime);

	if( !$expand && max($width, $height)<=$max_size && (!$square || ($square && $width==$height) ) )
	{
		// L'image est plus petite que max_size
		if($image_dest)
		{
			return copy($image_src, $image_dest);
		}
		else
		{
			header('Content-Type: '. $type_mime);
			return (boolean) readfile($image_src);
		}
	}

	// Calcule les nouvelles dimensions
	$ratio = $width / $height;

	if( $square )
	{
		$new_width = $new_height = $max_size;

		if( $ratio > 1 )
		{
			// Paysage
			$src_y = 0;
			$src_x = round( ($width - $height) / 2 );

			$src_w = $src_h = $height;
		}
		else
		{
			// Portrait
			$src_x = 0;
			$src_y = round( ($height - $width) / 2 );

			$src_w = $src_h = $width;
		}
	}
	else
	{
		$src_x = $src_y = 0;
		$src_w = $width;
		$src_h = $height;

		if ( $ratio > 1 )
		{
			// Paysage
			$new_width  = $max_size;
			$new_height = round( $max_size / $ratio );
		}
		else
		{
			// Portrait
			$new_height = $max_size;
			$new_width  = round( $max_size * $ratio );
		}
	}

	// Ouvre l'image originale
	$func = 'imagecreatefrom' . $type;
	if( !function_exists($func) ) return FALSE;

	$image_src = $func($image_src);
	$new_image = imagecreatetruecolor($new_width,$new_height);

	// Gestion de la transparence pour les png
	if( $type=='png' )
	{
		imagealphablending($new_image,false);
		if( function_exists('imagesavealpha') )
			imagesavealpha($new_image,true);
	}

	// Gestion de la transparence pour les gif
	elseif( $type=='gif' && imagecolortransparent($image_src)>=0 )
	{
		$transparent_index = imagecolortransparent($image_src);
		$transparent_color = imagecolorsforindex($image_src, $transparent_index);
		$transparent_index = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
		imagefill($new_image, 0, 0, $transparent_index);
		imagecolortransparent($new_image, $transparent_index);
	}

	// Redimensionnement de l'image
	imagecopyresampled(
		$new_image, $image_src,
		0, 0, $src_x, $src_y,
		$new_width, $new_height, $src_w, $src_h
	);

	// Enregistrement de l'image
	$func = 'image'. $type;
	if($image_dest)
	{
		$func($new_image, $image_dest);
	}
	else
	{
		header('Content-Type: '. $type_mime);
		$func($new_image);
	}

	// Libération de la mémoire
	imagedestroy($new_image); 

	return TRUE;
}		

// ici pasword n'est pas utilisé ==> au profit de $pw en variable globale
function pdfEncrypt ($origFile, $password,  $destFile,$sens)
	{
	//include the FPDI protection http://www.setasign.de/products/pdf-php-solutions/fpdi-protection-128/
	require_once('fpdi/FPDI_Protection.php');
		
	$pdf = new FPDI_Protection();

	// set the format of the destinaton file, in our case 6×9 inch
	$pdf->FPDF($sens,'mm','A4');

	//calculate the number of pages from the original document
	$pagecount = $pdf->setSourceFile($origFile);

	// copy all pages from the old unprotected pdf in the new one
	for ($loop = 1; $loop <= $pagecount; $loop++) 
		{
		$tplidx = $pdf->importPage($loop);
		$pdf->addPage();
		$pdf->useTemplate($tplidx);
		}
	
// protect the new pdf file, and allow no printing, copy etc and leave only reading allowed
$pdf->SetProtection(array('print'), $password, '');
$pdf->Output($destFile, 'F');

return $destFile;
}



function rotateImage($sourceFile,$destImageName,$degreeOfRotation)
	{
	  //function to rotate an image in PHP
	  //developed by Roshan Bhattara (http://roshanbh.com.np)

	  //get the detail of the image
	  $imageinfo=getimagesize($sourceFile);
	  switch($imageinfo['mime'])
	  {
	   //create the image according to the content type
	   case "image/jpg":
	   case "image/jpeg":
	   case "image/pjpeg": //for IE
				$src_img=imagecreatefromjpeg("$sourceFile");
				//rotate the image according to the spcified degree
			  $src_img = imagerotate($src_img, $degreeOfRotation, 0);
			  //output the image to a file
			  imagejpeg ($src_img,$destImageName);
				break;
		case "image/gif":
			$src_img = imagecreatefromgif("$sourceFile");
			  $src_img = imagerotate($src_img, $degreeOfRotation, 0);
			  imagegif ($src_img,$destImageName);			
					break;
		case "image/png":
			case "image/x-png": //for IE
			$src_img = imagecreatefrompng("$sourceFile");
			$src_img = imagerotate($src_img, $degreeOfRotation, 0);
			imagepng ($src_img,$destImageName);
			break;
	  }

	}

	function met_cadenas($image)
		{
		// On charge d'abord les images
		$source = imagecreatefrompng("images/cadenas.png"); // Le logo est la source
		switch ( extension_fichier($image) )
			  {    //create the image according to the content type
				case "jpg":
				case "jpeg":	$destination = imagecreatefromjpeg("upload_mini/$image"); 	break;
				case "gif":		$destination = imagecreatefromgif("upload_mini/$image"); 	break;
				case "png":		$destination = imagecreatefrompng("upload_mini/$image"); 	break;
			  }
		 
		// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
		$largeur_source = imagesx($source);
		$hauteur_source = imagesy($source);
		$largeur_destination = imagesx($destination);
		$hauteur_destination = imagesy($destination);
		 
		// On veut placer le logo en bas à droite, on calcule les coordonnées où on doit placer le logo sur la photo
		$destination_x = $largeur_destination/2 - $largeur_source/2;
		$destination_y =  $hauteur_destination/2 - $hauteur_source/2;
		 
		// On met le logo (source) dans l'image de destination (la photo)
		imagecopymerge($destination, $source, $destination_x, $destination_y, 0, 0, $largeur_source, $hauteur_source, 60);
		 
		// On affiche l'image de destination qui a été fusionnée avec le logo
		switch( extension_fichier($image) )// La photo est la destination
			  {    //create the image according to the content type
				case "jpg":
				case "jpeg":	imagejpeg($destination, "upload_mini/-$image"); 	break;
				case "gif":		imagegif($destination, "upload_mini/-$image"); 	break;
				case "png":		imagepng($destination, "upload_mini/-$image"); 	break;
			  }
		}	
	
	function met_point_vert($image)
		{
		// On charge d'abord les images
		$source = imagecreatefrompng("images/oui.png"); // Le logo est la source
		switch ( extension_fichier($image) )
			  {    //create the image according to the content type
				case "jpg":
				case "jpeg":	$destination = imagecreatefromjpeg("upload_mini/$image"); 	break;
				case "gif":		$destination = imagecreatefromgif("upload_mini/$image"); 	break;
				case "png":		$destination = imagecreatefrompng("upload_mini/$image"); 	break;
			  }
		 
		// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
		$largeur_source = imagesx($source);
		$hauteur_source = imagesy($source);
		$largeur_destination = imagesx($destination);
		$hauteur_destination = imagesy($destination);
		 
		// On veut placer le logo en bas à droite, on calcule les coordonnées où on doit placer le logo sur la photo
		$destination_x = $largeur_destination - $largeur_source;
		$destination_y =  $hauteur_destination - $hauteur_source;
		 
		// On met le logo (source) dans l'image de destination (la photo)
		imagecopymerge($destination, $source, $destination_x, $destination_y, 0, 0, $largeur_source, $hauteur_source, 60);
		 
		// On affiche l'image de destination qui a été fusionnée avec le logo
		switch( extension_fichier($image) )// La photo est la destination
			  {    //create the image according to the content type
				case "jpg":
				case "jpeg":	imagejpeg($destination, "upload_mini/_$image"); 	break;
				case "gif":		imagegif($destination, "upload_mini/_$image"); 	break;
				case "png":		imagepng($destination, "upload_mini/_$image"); 	break;
			  }
		supp_fichier ("upload_mini/$image");
		rename ("upload_mini/_$image","upload_mini/$image");
		
		}
			
	function genere_miniature_pdf($source, $dest, $hauteur=100)
		{
		exec ( "/usr/bin/convert -density 100 $source $source.jpg" ) ;  // si PDF et sur serveur OVH alors on crée une miniature
			
		// cas d'un pdf avec plusieurs pasges : ==> On n egarde que la 1ere
		if (file_exists("$source-0.jpg"))
			{
			rename ("$source-0.jpg", "$source.jpg");
			for ($i=1; $i<10; $i++)
				supp_fichier("$source-$i.jpg");
			}
		imagethumb("$source.jpg","$dest.jpg", $hauteur);
		supp_fichier("$source.jpg");
		}
	
	?> 