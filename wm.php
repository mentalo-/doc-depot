<?php session_start();

include 'general.php';

	$format_date = "d/m/Y";
	$user_lang='fr';
	
	// ConnexiondD
	include "connex_inc.php";
	
	$action="wm";
	
	require_once 'cx.php';

	if ( isset($_SESSION['pass']) && ($_SESSION['pass']==false) && ($_SESSION['droit']=="")  )
		{
		include_once __DIR__.'/webmail/libraries/afterlogic/api.php';
		
//		if (class_exists('CApi') && CApi::IsValid() &&(isset($_SESSION['adresse_mail'])) &&(isset($_SESSION['mdp_mail'])) )
		if (class_exists('CApi') && CApi::IsValid()  )
			{	
			//$mail=$_SESSION['adresse_mail'];
			//$pw=$_SESSION['mdp_mail'];

			$mail='bg@doc-depot.com';
			$pw='BG_123456';			
			
			header('Location: ../webmail/index.php?sso&hash='.CApi::GenerateSsoToken($mail, $pw));
			}
			else
			{
				echo 'AfterLogic API isn\'t available';
			}
		}
	else
		// ce n'est pas un bénéficiaire on va sur doc-depot
		header('Location: ../index.php');



