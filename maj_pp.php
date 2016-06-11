  <?php  

	include "connex_inc.php";
	include 'general.php';
	
	function traite ($table)
		{
		$reponse =command("select *  from  $table where mail<>'' ");		
		while ($donnees = fetch_command($reponse) ) 
			{
			$mail=$donnees["mail"];	
			$idx=$donnees["idx"];
			if ($mail!="")
				{
				$d3= explode("@",$mail);  
				if ( (isset($d3[0])) && (isset($d3[1])))
					{
					$mail= $d3[0].'@fixeo.com';
					command("UPDATE `$table` SET mail='$mail'  where idx='$idx' ");
					}
				}
			}
		}

	function traite_fissa ($table)
		{
		$reponse =command("select *  from  $table where pres_repas like 'mail' and date ='1111-11-11'");		
		while ($donnees = fetch_command($reponse) ) 
			{
			$mail=$donnees["commentaire"];	
			$nom=$donnees["nom"];	
			if ($mail!="")
				{
				$d3= explode("@",$mail);  
				if ( (isset($d3[0])) && (isset($d3[1])))
					{
					$mail= $d3[0].'@fixeo.com';
					command("UPDATE `$table` SET commentaire='$mail'  where nom= '$nom' and  pres_repas like 'mail' and date ='1111-11-11'");
					}
				}
			}
		}		
	Echo "Initialisation environnement pour test: ";

	if ( (strpos($_SERVER['SERVER_NAME'],"pp.fixeo.fr")!=false) &&  (strpos($_SERVER['SERVER_NAME'],"127.0.0.1")!=false) )  
		{
		echo "Non applicable sur cet environnement !";
		exit ();
		}
	
	if (strpos($_SERVER['SERVER_NAME'],"pp.fixeo.fr")==false)
		{
		Echo "<p>- Paramètres : ";
		// initialise les variables spcifique Pré production
			ecrit_parametre("TECH_identite_environnement ", "PP");
			ecrit_parametre("DEF_ADRESSE_MAIL_TTT", "pp.docdepot.mail@gmail.com");
			ecrit_parametre("DEF_SERVEUR_MAIL_TTT", "{imap.gmail.com:993/imap/ssl/novalidate-cert}");
			ecrit_parametre("DEF_PW_MAIL_TTT", "55364963");
			ecrit_parametre("DD_numero_tel_sms ", "0698354401");
		Echo "terminé";	
		}
		
		Echo "<p>- Adresses mail : ";
		// pour éviter d'envoyer des mails à tort aux vrais utilisateurs
		// chaque adresse mail est préfixées par PP
		traite ("r_user");
		traite ("r_referent");
		traite ("r_organisme");
		
		$reponse =command("select * from fct_fissa  ");
		while ($donnees = mysql_fetch_array($reponse) )
			{

			$support=$donnees["support"];
			traite_fissa($support);
			Echo "<p>- Fissa : $support";			
		
			}	
		command("UPDATE `fct_fissa` SET mails_rapports='pp@fixeo.com' , mails_rapport_detaille='pp@fixeo.com'  ");	
		
		Echo "<p>- Logo : ";
		if (strpos($_SERVER['SERVER_NAME'],"pp.fixeo.fr")==false)
			copy ("images/logo_dev.png","images/logo.png");
		else
			copy ("images/logo_pp.png","images/logo.png");
		Echo "terminé";	
		
	?> 