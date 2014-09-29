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

		$reponse = mysql_query("SELECT * from  r_user WHERE idx='$idx'"); 
		$donnees = mysql_fetch_array($reponse);
		$code_lecture=$donnees["lecture"];	
		

		charge_image("0",$tempFile,$_FILES['file']['name'],$code_lecture,"P-$idx", "" , "Autres", $idx, $idx);

		} 
	?> 