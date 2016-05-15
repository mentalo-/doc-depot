  <?php  
	echo "<head>";
	echo "</head><body>";
	include "connex_inc.php";
	include 'general.php';
	include 'include_mail.php';


	ecrit_parametre('TECH_msg_supervision_gatewaysms', "Test SMS ");
	envoi_SMS( parametre('DD_numero_tel_sms') ,parametre('TECH_msg_supervision_gatewaysms').". ".date('H\hi',time()));
	ecrit_parametre('TECH_dernier_envoi_supervision', time() );
	echo "Supervision SMS à la demande envoyée avec '". parametre('TECH_msg_supervision_gatewaysms')."'";

	
	echo "</body>";

	?> 