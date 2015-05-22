  <?php  

	echo "<head>";
	echo "</head><body>";
	include "connex_inc.php";
	include 'general.php';
 	include 'include_crypt.php';
	include 'include_charge_image.php';	

	$source="tst.jpg";
	$pw=encrypt("123456");
	
function genere_fichier ($source, $pw )
	{
	
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
			exec ( "/usr/bin/convert -density 100 upload/$source upload/$source.jpg" ) ;
			$hauteur=250;
			imagethumb("upload/$source.jpg","upload_mini/$source",$hauteur);
			supp_fichier("upload/$source.jpg");
			}
		}

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
		
		
	echo "</body>";
	?> 