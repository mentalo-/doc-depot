<?PHP
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