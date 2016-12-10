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
	
	require_once "connex_inc.php";
	require_once 'general.php';
 	require_once 'include_crypt.php';
	require_once 'include_charge_image.php';	
	require_once 'exploit.php';	
	
	
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