 <?php
 
 	function mise_en_forme_date_aaaammjj( $date_jour)
		{
		$d3= explode("/",$date_jour);  
		if (isset($d3[2]))
			{
			$a=$d3[2];
			$m=$d3[1];
			$j=$d3[0];	
			return( "$a-$m-$j" );	
			}
		
		return( $date_jour );	

		}


	function command($ligne,$echo="")
		{
		if ($echo!="") echo "$ligne<br>";
		return( mysql_query($ligne) );	

		}

		function fetch_command ($reponse)
		{
		return(  mysql_fetch_array($reponse) );
		}
		
	function ajoute ($date, $nom, $type, $val, $qte="") 
			{
			if (($date!="") &&($nom!=""))
				{
				if ($type!='Visite')
					{
					$reponse =command("select * from  ZZ_mda where nom='$nom' and date='$date'  and pres_repas='$type' ");
					if ($donnees = fetch_command($reponse) ) 
						{
						if ($val != $donnees["commentaire"])
							echo "<br> Doublon  $nom : $val  <> ".$donnees["commentaire"];
						return;
						}
					}
				
				$cmd= "INSERT INTO `ZZ_mda` (`nom`, `date`, `pres_repas`, `commentaire`, `user`, `modif`, `activites`, `qte`) VALUES ('$nom','$date','$type','$val','','', '', '$qte' )";
				command( $cmd );
	
				}
			}
		

include 'connex_inc.php';

traite ("mda_2016.csv");
traite ("mda_2015.csv");
traite ("mda_2014.csv");


function traite ($source)
	{
	$fic = fopen($source, "r");

	$tab=fgetcsv($fic,3024,';','"');
	for ($i=6; $tab[$i]!=""; $i++)
		$tab_dates[$i]=$tab[$i];

	$date='1111-11-11';	
	$ligne = 0; // compteur de ligne
	while  ($tab=fgetcsv($fic,3024,';','"'))
				{
				
				$nom=strtolower ($tab[1])." ".$tab[0];
				$nom= addslashes($nom);
				//echo "<br>$nom";
				if ($tab[2]=="F")
				 $nom.=" (F)";
				$date='1111-11-11';	
				ajoute ($date, $nom, 'age' , $tab[3] );
				ajoute ($date, $nom, 'nationalite' , $tab[4] );
				
				for ($i=6; isset ($tab_dates[$i]); $i++)
					{
					if ( ($tab[$i]!="") && ($tab[$i]!="0")  )
						{
						$d3= explode("/",$tab_dates[$i]);  
						$a=$d3[2];
						$m=$d3[1];
						$j=$d3[0];	
						
						for ($k=0; $k<$tab[$i]; $k++)
							{
							$date ="$a-$m-".($j+$k);
							ajoute ($date, $nom, 'Visite' , '', '1');
							}
						}
					}
				}
	fclose($fic);
	}
		
	?> 