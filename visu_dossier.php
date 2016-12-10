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

include 'general.php';

// methode sans passer par un fichier intermédiaire
$fichier=variable_s('fichier');	
$id=rand(1000000,999999999999);
include "connex_inc.php";

$d3= explode("-",$fichier);
$bene=$d3[0];
$dossier=$d3[1];

ajout_log($bene, traduire("Accès au dossier")." $dossier ".traduire('en lecture'), $_SERVER["REMOTE_ADDR"]);
copy("dossiers/$fichier.pdf","upload_tmp/$id.pdf");
header("Location: upload_tmp/$id.pdf");			

?>