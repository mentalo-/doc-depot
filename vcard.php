<?PHP

function recup_info($ligne)
	{
	$r = substr ($ligne, strpos ($ligne,":")+1);
	$r = str_replace(";;",";", $r);
	if ($r[0]==';')
		$r = substr ($r, 1);
	return ($r);
	}
function extrait_vcard($file_vcard)
	{
	$synthese= "";
	
	$vcard = file($file_vcard);
    if (!$vcard) 
			retrun("");
	$l=0;
	$nom="";
	$tel="";
	$mail="";
	$adresse="";
	
	while ((!strstr(strtoupper($vcard[$l]),"END:VCARD") )&& ($l<30))
		{
		$ligne= $vcard[$l] ;
//		echo "<br>". $ligne;
		if (strstr(strtoupper($ligne),"FN:") ) // seul cas où on écrase la valeur
			$nom = recup_info($ligne);
			
		if (strstr(strtoupper($ligne),"FN:") )
			if ($nom=="")
				$nom .= recup_info($ligne);
			
		if (strstr(strtoupper($ligne),"TEL;") )
			$tel .= recup_info($ligne)." ou ";
			
		if (strstr(strtoupper($ligne),"MAIL;") )
			$mail .= recup_info($ligne)." ou ";
	
		if (strstr(strtoupper($ligne),"ADR;") )
			$adresse .= recup_info($ligne)." ou ";
			
		$l++;
		}
	$synthese = $nom;
	if ($tel!="")
		$synthese .= " - Tel: ". $tel ;
	if ($mail!="")
		$synthese .= " - Mail: ". $mail;
	if ($adresse!="")
		$synthese .= " - Adresse: ". $adresse ; 	
	$synthese .= " -";
	
	$synthese = str_replace("ou  -","-", $synthese);
	
	return($synthese);
		
	}


?>