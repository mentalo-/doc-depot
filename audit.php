<?php include 'header.php';	  ?>
  <center>
<a href="http://adileos.jimdo.com/"  target=_blank ><img src="images/adileos.jpg" width="700" height="90" > </a> 
<br>
Association de Développement et d’Intégration de Logiciels Economiques Orientés Social
<center>
  
  <?php  

	include 'audit_cnil_mots.php';
	include 'general.php';

	function homogene ( $commentaire)
		{
	//	$commentaire = strtr($commentaire, 'Êáàâäãåçéèêëíìîïñóòôöõúùûüýÿ-_()<>;.,"?/!’«”“‘', 'Eaaaaaaceeeeiiiinooooouuuuyy                  ');
		$commentaire = strtr($commentaire, "'", " ");
		$commentaire = str_replace ("&apos"," ", $commentaire);
		$commentaire = str_replace ("   "," ", $commentaire);
		$commentaire = str_replace ("  "," ", $commentaire);
		$commentaire = str_replace ("\n"," ", $commentaire);
		$commentaire = str_replace ("\r"," ", $commentaire);
		return($commentaire);
		}
		
	function dd_strstr($commentaire,$recherche)
		 {
		 global $nb_tst;
		 
		$nb_tst++;
		return(stristr($commentaire,$recherche));
		 }
	

	function test1( $m)
		{
		global $autorises, $commentaire;

		if 	(dd_strstr($commentaire,$m) )
			$commentaire=str_ireplace ($m, "<B><span style=\"background-color:#ffff66;\" >&nbsp;$m&nbsp;</span></b>", $commentaire);
		}
		
	function test( $m)
		{
		global $autorises, $commentaire;
					
		if 	(dd_strstr($commentaire," ".$m))
			{
			test1(" ".$m."e ");
			test1(" ".$m."es ");
			test1(" ".$m."s ");
			test1(" ".$m."r ");
			test1(" $m ");		
			}
			
		}
	
	function test_approx( $org, $approx,$m)
		{
		if 	(dd_strstr($m,$org))
			test(str_replace ($org,$approx, $m) );	
		}
		
		

		$nb_tst=0;
		
		for ($i=0; isset($mots[$i]); $i++)
				$mots[$i] = trim( strtr ($mots[$i], 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ-()<>;.', 'aaaaaaceeeeiiiinooooouuuuyy       ') ) ;	
	
		$ncolor=0;			
		$nb=0;

		if (($ncolor++ %2 )==0) $color="#ffffff" ; else $color="#d4ffaa" ; 	

		$commentaire = variable_s("commentaire");		
			
		echo "<p><br>Texte à analyser :";
		echo "<form method=\"POST\" action=\"audit.php\">";
		echo "<TEXTAREA rows=\"6\" cols=\"80\" name=\"commentaire\" onChange=\"this.form.submit();\" >$commentaire</TEXTAREA>";
		echo "<input type=\"submit\" value=\"Analyser\" >  ";
		echo "</form> ";
				
	
		$commentaire = homogene ( " ".$commentaire." ");
		$org=$commentaire ;		
		
		for ($i=0; isset($mots[$i]); $i++)
				{
				$m=$mots[$i];
				
				test( $m);
				
				if (strlen($m)>6)
					{
					test_approx("eux","euse", $m);	
					test_approx("eau","eaux", $m);		
					test_approx("eur","euse", $m);
					test_approx("iste","isme", $m);			
					test_approx("xuel","xuelle", $m);
					test_approx("gie","gue", $m);
					test_approx("if","ive", $m);	
					
						// test ortographe approximatif
					test_approx("ph","f", $m) ;	
					test_approx("tt","t", $m);
					test_approx("ll","l", $m);
					test_approx("rr","r", $m);
					test_approx("mm","m", $m);
					test_approx("pp","p", $m);
					test_approx("th","t", $m);					
					test_approx("y","i", $m);			
					test_approx("o","0", $m);			
					test_approx("au","o", $m);
					test_approx("mp","np", $m);			
					test_approx("mb","nb", $m);			
					test_approx("en","an", $m);			
					test_approx("an","en", $m);		
					}
				test_approx(" ","", $m);	
				}
					


//			$cumul.="$i - $nb_tst <table border=\"2\"><tr> ";



if ($org!="  ")
	{
	if ($org!=$commentaire)	
		{
		$entete=  "<p>Bonjour,<p>Veuillez trouver ci-dessous les saisies qui nécessitent votre regard. ";
		$entete.=  " Certains mots ou expressions utilisés sont susceptibles de ne pas respecter les directives de la CNIL.";
		$entete.=  "<p>Ce système de détection, en phase expérimentale, peut ne pas avoir détecté certains mots ou expressions inappropriés, n'hésitez pas à nous faire des retours via le lien <a id=\"lien\"  href=\"https://www.doc-depot.com/index.php?action=contact\">contact de FISSA</a><p>";				
		$entete.=  "<p>Rappel CNIL: les informations personnelles enregistrées doivent être «adéquates, pertinentes et non excessives au regard des finalités pour lesquelles elles sont collectées (article 6-3°).";
		$entete.=  "<br>En principe, les données sensibles (information concernant l’origine raciale ou ethnique, les opinions politiques, philosophiques ou religieuses, l’appartenance syndicale, la santé ou la vie sexuelle) ne peuvent être recueillies et exploitées qu’avec le consentement explicite des personnes. ";

		$cumul="<table border=\"2\"><tr> <td bgcolor=\"$color\">  $entete </td> ";
		//$cumul.= "<td bgcolor=\"$color\">  $org </td> ";
		$cumul.= "<tr> <td bgcolor=\"$color\">  $commentaire </td> ";
		$cumul.="</table> ";
		}
 else
 		{
		
		$entete=  "<p>Bonjour,<p> ";
		$entete.=  "Le texte saisi ne présente pas, a priori, de mots ou d'expressions susceptibles de ne pas respecter les directives de la CNIL.";
		$entete.=  "<p>Ce système de détection, en phase expérimentale, peut ne pas avoir détecté certains mots ou expressions inappropriés, n'hésitez pas à nous faire des retours via le lien <a id=\"lien\"  href=\"https://www.doc-depot.com/index.php?action=contact\">contact de FISSA</a><p>";				
		$entete.=  "<p>Rappel CNIL: les informations personnelles enregistrées doivent être «adéquates, pertinentes et non excessives au regard des finalités pour lesquelles elles sont collectées (article 6-3°).";
		$entete.=  "<br>En principe, les données sensibles (information concernant l’origine raciale ou ethnique, les opinions politiques, philosophiques ou religieuses, l’appartenance syndicale, la santé ou la vie sexuelle) ne peuvent être recueillies et exploitées qu’avec le consentement explicite des personnes. ";

		$cumul="<table border=\"2\"><tr> <td bgcolor=\"$color\">  $entete </td> ";
		//$cumul.= "<td bgcolor=\"$color\">  $org </td> ";
		$cumul.= "<tr> <td bgcolor=\"$color\">  $commentaire </td> ";
		$cumul.="</table> ";
		}
			echo $cumul;
	}
				


	?>