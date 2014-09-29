
 <?php

function 	appel_url($url, $test)
	{

	$timestart=microtime(true);
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	$result = curl_exec($curl);
	$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
	/*Initialisation de la ressource curl*/
	$curl = curl_init();
	/*On indique à curl quelle url on souhaite télécharger*/
	curl_setopt($curl, CURLOPT_URL, $url);
	/*On indique à curl de nous retourner le contenu de la requête plutôt que de l'afficher*/
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	/*On indique à curl de ne pas retourner les headers http de la réponse dans la chaine de retour*/
	curl_setopt($curl, CURLOPT_HEADER, false);
	/*On execute la requete*/
	$output = curl_exec($curl);
	/*On a une erreur alors on la lève*/
	
	If ($statusCode==200)
		echo " Ok ";
	else
		echo " Ko (erreur :".$statusCode .") ";

	$timeend=microtime(true);
	$time=$timeend-$timestart;
	 
	//Afficher le temps de chargement
	$page_load_time = round($time*1000, 0);

	echo "($page_load_time ms)" ;
	If ( ($statusCode==200) && ($test!=""))
		{
		if (strpos ($output,$test)!=0)
			{}
		else
		 echo  " Absence '$test' "; 
		 
		 echo "<p> $output";
		 }

	/*On ferme la ressource*/ 
	curl_close($curl);
		}

    echo "<head>";
	echo "<link rel=\"icon\" type=\"image/png\" href=\"images/identification.png\" />";
	echo "<title>Appel_TTT</title>";
	echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"15\">";
	echo "</head><body>";
/*	
	echo "<p> Logo : ";
	$timestart=microtime(true);
	if ( @file_get_contents ("http://doc-depot.com/images/logo.png" ) == false )
		echo " inaccessible;";
	else
			echo "Ok";

	$timeend=microtime(true);
	$time=$timeend-$timestart;
	 
	//Afficher le temps de chargement
	$page_load_time = round($time*1000, 0);

	echo " ($page_load_time ms)" ;	
	
	echo "<p> URL : ";
	appel_url("http://doc-depot.com","Si vous avez oublié votre mot de passe, cliquez");
*/
	echo "<p> URL : ";
	appel_url("http://www.doc-depot.com/TTT_mail2.php","");
	
	

	echo "</body>";
	?> 
