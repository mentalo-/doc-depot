<?php  
include 'general.php';

// methode sans passer par un fichier interm�diaire
$fichier=variable_s('fichier');	
$id=rand(1000000,999999999999);
include "connex_inc.php";

$d3= explode("-",$fichier);
$bene=$d3[0];
$dossier=$d3[1];

ajout_log($bene, traduire("Acc�s au dossier")." $dossier ".traduire('en lecture'), $_SERVER["REMOTE_ADDR"]);
copy("dossiers/$fichier.pdf","upload_tmp/$id.pdf");
header("Location: upload_tmp/$id.pdf");			

?>