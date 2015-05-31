  <?php  

	include "connex_inc.php";
	include 'general.php';
	
	function traite($table, $champ, $cle)
		{
		echo "<hr>$table - $champ ($cle)<br>";
		$reponse =command("select $champ, $cle from  $table where $champ<>'' ");		
		while ($donnees = fetch_command($reponse) ) 
				{
				$valeur=$donnees[$champ];	
				$val_cle=$donnees[$cle];	
				
				$v_corrige= str_replace("\\\\\\\\","\\",$valeur);
				$v_corrige= str_replace("\\\\\\","\\",$v_corrige);
				$v_corrige= str_replace("\\\\","\\",$v_corrige);
				$v_corrige= str_replace("\\'","&apos;",$v_corrige);
				$v_corrige= str_replace("\\\"","&quot;",$v_corrige);

				if ($valeur!=$v_corrige)
					{
					echo "<br> --- $valeur === $v_corrige";
					command("UPDATE `$table` SET $champ='$v_corrige'  where $cle='$val_cle'  ");
					}
				
				}
		}
		
	traite("z_traduire","gb","idx");

	//traite("z_log_t","ligne");
	
	traite("z_bug","titre","idx");
	traite("z_bug","descript","idx");
	
	traite("r_user","nom","idx");
	traite("r_user","prenom","idx");
	traite("r_user","prenom_p","idx");
	traite("r_user","prenom_m","idx");
	traite("r_user","adresse","idx");
	traite("r_user","ville_nat","idx");
	
	traite("r_sms","ligne", "num_seq");

	traite("r_referent","nom","idx");
	traite("r_referent","prenom","idx");
	traite("r_referent","adresse","idx");

	traite("r_organisme","organisme","idx");
	traite("r_organisme","adresse","idx");	
	traite("r_organisme","sigle","idx");	
	
	traite("log","ligne","date");
	traite("dd_rdv","ligne","idx");

	traite("fct_fissa","acteur","organisme");
	traite("fct_fissa","beneficiaire","organisme");
	
	traite("cc_alerte","sueil","tel");
		
	/*
	traite("ZZ_SEC_CATH","nom");
	traite("ZZ_SEC_CATH","commentaire");
	traite("ZZ_assol","nom");
	traite("ZZ_assol","commentaire");	
	traite("ZZ_cafe115","nom");
	traite("ZZ_cafe115","commentaire");
	*/

	
	?> 