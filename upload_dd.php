  <?php  
	session_start(); 
	
	include "connex_inc.php";
	include 'general.php';
 	include 'include_crypt.php';
	include 'include_charge_image.php';	
	
	
	if (!empty($_FILES)) 
		{          
		$tempFile = $_FILES['file']['tmp_name'];          

		$idx=$_SESSION['user_idx'];
		$acteur=$_SESSION['acteur'];
		
		$reponse = mysql_query("SELECT * from  r_user WHERE idx='$idx'"); 
		if ($donnees = mysql_fetch_array($reponse))
			{
			$code_lecture=$donnees["lecture"];	
			charge_image("0",$tempFile,$_FILES['file']['name'],$code_lecture,"A-$idx", "" , "Autres", $acteur, $idx);
			}
		else
		 // on demande de charger un document pour un compte qui n'existe pas 
		 ajout_log_tech("Incohérence: Tentative d'ajout de fichier sur compte $idx qui n'existe pas par $acteur !");

		} 
	?> 