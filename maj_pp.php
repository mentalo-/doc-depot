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
			
			$mail=substr_replace($mail, '@fixeo.com', strpos($mail,'@'));
			command("UPDATE `$table` SET mail='pp_$mail'  where idx='$idx' ");
			}
		}
	
	Echo "Initialisation environnement Pré-Production : ";
	
	if (strpos($_SERVER['SERVER_NAME'],"pp.fixeo.fr")==false)
		echo "<p> Erreur : Environnement non PP !!!";
	else
		{
		Echo "<p>- Paramètres : ";
		// initialise les variables spcifique Pré production
			ecrit_parametre("TECH_identite_environnement ", "PP");
			ecrit_parametre("DEF_ADRESSE_MAIL_TTT", "pp.docdepot.mail@gmail.com");
			ecrit_parametre("DEF_SERVEUR_MAIL_TTT", "{imap.gmail.com:993/imap/ssl/novalidate-cert}");
			ecrit_parametre("DEF_PW_MAIL_TTT", "55364963");
			ecrit_parametre("DD_numero_tel_sms ", "0698354401");
		Echo "terminé";	
		
		Echo "<p>- Adresses mail : ";
		// pour éviter d'envoyer des mails à tort aux vrais utilisateurs
		// chaque adresse mail est préfixées par PP
		traite ("r_user");
		traite ("r_referent");
		traite ("r_organisme");
		//	traite ("cc_user");
		Echo "terminé";

		Echo "<p>- Diffusion Fissa : ";
		command("UPDATE `$table` SET mails_rapports='pp@fixeo.com' , mails_rapport_detaille='pp@fixeo.com'  ");
		Echo "terminé";	
		
		Echo "<p>- Logo : ";
		copy ("images/logo_pp.png","images/logo.png");
		Echo "terminé";	
		}
	?> 