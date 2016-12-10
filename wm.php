<?php///////////////////////////////////////////////////////////////////////
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

		?>

