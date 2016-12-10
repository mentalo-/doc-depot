<?php 
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

	function nb_message_file_envoi_sms ()
		{
		$nbmess="";
		$host=parametre('DEF_SERVEUR_MAIL_TTT'); 
		$login=parametre('DD_mail_pour_gateway_sms'); //imap login
		$password="55364963"; //imap password
		$mBox = imap_open(	$host, $login, $password); 
		if ($mBox)
			{
			$savedirpath="./tmp/" ; // attachement will save in same directory where scripts run othrwise give abs path
			$jk= new  MailAttachmentManager($host, $login, $password, $savedirpath); 
			$jk -> openMailBox();
			$nbmess = imap_num_msg($jk -> getMbox() );
			imap_close ($mBox );
			}
		return($nbmess);
		}
		
	function maj_compteur_envoi_mail()
		{
		$nb=parametre("TECH_nb_mail_envoyes")+1;
		// on met à jour le nombre de mail envoyé 
		ecrit_parametre("TECH_nb_mail_envoyes",$nb) ;

		if ($nb>0.7*parametre("DD_nbre_mail_jour_max"))
			ajout_log_tech( "Attention seuil d'envoi mails dépassé :".parametre("TECH_nb_mail_envoyes")." / ".parametre("DD_nbre_mail_jour_max"));

		}

	
		
		
	// envoi de mail sans mise en forme Doc-dépot
	function envoi_mail_perso($to,$subject,$body, $origine, $from="")
		{
		// on signe le mail avec le nom de l'emetteur
		$body = "$body <p> Mail envoyé depuis doc-depot.com par $origine";

		maj_compteur_envoi_mail();
		
		// si envoie depuis le pc de test , on ne fait que l'afficher
		if (($_SERVER['REMOTE_ADDR']=="127.0.0.1") ||(isset ($_SESSION['chgt_user']) && ($_SESSION['chgt_user']==true) ) )
			{
			echo "<table border=\"2\"> <tr> <td>$to <hr>" ;
			echo "$subject <hr>" ;
			echo "$body </td> </table>" ;
			return;
			}
		
		if ($from=="")
			$from="Doc-Depot <pas_de_reponse@doc-depot.com>";
			
		// Entete
		$headers  = "MIME-Version: 1.0 \n"; 
		$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
		$headers .="From: $from\n";
		$headers .="Reply-To: $from\n";
        $headers .='Content-Transfer-Encoding: 8bit'."\n";			
		
		// mise en forme HTML
		$body = "<html><body>$body</body></html>";	
		$subject = '=?iso-8859-1?B?'.base64_encode($subject).'?=';
		// Envoi de l'email
		
		$CR_Mail = @mail ($to, $subject, $body, $headers);

		if ($CR_Mail === FALSE)
			erreur( " Erreur envoi mail: $CR_Mail <br> \n");
		}	



	function mail2($dest, $titre, $contenu, $libelle, $mail_struct )
		{
		if (($_SERVER['REMOTE_ADDR']=="127.0.0.1") ||(isset ($_SESSION['chgt_user']) && ($_SESSION['chgt_user']==true) ))
			{
			echo "<table border=\"2\"> <tr> <td>$dest <hr>" ;
			echo "$titre <hr>" ;
			echo "$contenu </td> </table>" ;
			return;
			}		
			
		// Entete
		$headers  = "MIME-Version: 1.0 \n"; 
		$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
        $headers .='Content-Transfer-Encoding: 8bit'."\n";		
		
		$libelle = '=?iso-8859-1?B?'.base64_encode($libelle).'?='; // T354
		$headers  .= "From: FISSA $libelle <$mail_struct>" . "\r\n"; 
		
		// mise en forme HTML
		$contenu = supprime_html($contenu);
		$contenu = "<html><body>$contenu</body></html>";	
		$titre = '=?iso-8859-1?B?'.base64_encode($titre).'?=';  // T354
					
		mail ( $dest , $titre, $contenu,$headers );
		return(true);
		}	
		
	// envoi de mail avec mise en forme doc-depot
	function envoi_mail($to,$subject,$body,$masque=false)
		{
		maj_compteur_envoi_mail();
		
		$envir = parametre("TECH_identite_environnement");
			
		// Sujet du mail
		$subject = "$envir : $subject";			
		
		// Le message vers le gestionnaire est réduit (pas d'image ni slogan)
		if ($to!=parametre('DD_mail_gestinonnaire'))
			{
			// contenu du message
			$body = "<center><a href=\"".serveur."\"><img src=\"http://".serveur."images/logo.png\" width=\"150\" height=\"100\" ></a><p>
					<h3>".traduire('La Consigne Numérique Solidaire')."</h3>
					<font size=\"5\">'' ".traduire("Mon essentiel à l'abri en toute confiance")." ''</font> <p> $body";
			}

		if (($_SERVER['REMOTE_ADDR']=="127.0.0.1") ||(isset ($_SESSION['chgt_user']) && ($_SESSION['chgt_user']==true) ))
			{
			echo "<table border=\"2\"> <tr> <td>$to <hr>" ;
			echo "$subject <hr>" ;
			echo "$body </td> </table>" ;
			return;
			}
			
		// Entete
		$headers  = "MIME-Version: 1.0 \n"; 
		$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
		$headers .='From: Doc-Depot <fixeo@doc-depot.com>'."\n";
        $headers .='Reply-To: Doc-Depot <fixeo@doc-depot.com>'."\n";
        $headers .='Content-Transfer-Encoding: 8bit'."\n";			
		
		// mise en forme HTML
		$body = "<html><body>$body</body></html>";	
		$subject = '=?iso-8859-1?B?'.base64_encode($subject).'?=';
		
		// Envoi de l'email
		$CR_Mail = @mail ($to, $subject, $body, $headers);
		if (!$masque)
			{
			if ($CR_Mail === FALSE)
					erreur( "Erreur envoi mail: $CR_Mail");
				else
					msg_ok( traduire('Mail envoyé'));
			}
		}

	function envoi_mail_brut($to,$subject,$body)		
		{
		$body= supprime_html($body);
		
		maj_compteur_envoi_mail();
		ecrit_parametre("TECH_nb_sms_envoyes",parametre("TECH_nb_sms_envoyes")+1) ;

		if (($_SERVER['REMOTE_ADDR']=="127.0.0.1") || (isset ($_SESSION['chgt_user']) && ($_SESSION['chgt_user']==true) ))
			{
			echo "<table border=\"2\"> <tr> <td> SMS </td><td>" ;
			echo "$subject </td><td> " ;
			echo "$body </td> </table>" ;
			return;
			}
			
		$CR_Mail = @mail ($to, $subject, stripcslashes($body));
		
		if ($CR_Mail === FALSE)
			{
			erreur( "Erreur envoi SMS: $CR_Mail <br> \n");
			ajout_log_tech( "Erreur envoi SMS ($to / $subject / $body) : $CR_Mail  ");
			}

		}

		
	function envoi_SMS_operateur($subject,$body,$origine="ADILEOS")		
		{
		$subject=homogenise_telephone($subject) ;
		if (VerifierPortable($subject))
			{
			maj_compteur_envoi_mail();
			ecrit_parametre("TECH_nb_sms_envoyes_operateur",parametre("TECH_nb_sms_envoyes_operateur")+1) ;	
			
			$body= supprime_html($body);
			ajout_log_tech( "Envoi SMS ADILEOS au $subject : '$body' ");
			//envoi_mail_brut(parametre('DD_mail_pour_gateway_sms'),$subject,$body);
			if ( ($body!=parametre('FORM_msg_rdv')) || ($subject!=parametre('FORM_tel_rdv'))) 
				{
				if  (strlen(strstr($subject,"0692"))==10)  
					$subject="00262".substr($subject, 1);		
				else
					if ( (strlen(strstr($subject,"06"))==10) || (strlen(strstr($subject,"07"))==10) )
						$subject="0033".substr($subject, 1);		
					else
						$subject="00".substr($subject, 1);	
				
				$url= "https://www.ovh.com/cgi-bin/sms/http2sms.cgi?smsAccount=sms-cj277894-1&login=fredgont&password=fredgont&from=$origine&to=".trim($subject)."&contentType=text/json&message=".urlencode($body);
				
				$soap_do = curl_init();
				curl_setopt($soap_do, CURLOPT_URL,            $url );
				curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
				curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
				curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
				curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($soap_do, CURLOPT_POST,           false );  							
//				curl_setopt($soap_do, CURLOPT_HTTPHEADER,     array('Content-Type: text/xml; charset=utf-8' ));
				curl_setopt($soap_do, CURLOPT_HTTPHEADER,     array('Content-Type: text/json; charset=utf-8' ));

				if(!($output=curl_exec($soap_do)))
					{                
					$err = 'Curl error: ' . curl_error($soap_do);
					ajout_log_tech( "Echec envoi SMS au $subject : '$body' ; Motif $err ");
					curl_close($soap_do);
					return(false);
					}
				else
					{
					ajout_log_tech( "Retour OVH : $output ");
					$statusCode = curl_getinfo($soap_do,CURLINFO_HTTP_CODE);
					curl_close($soap_do);
					return(true);
					}
				}
				
			}
		else
			ajout_log_tech( "PAS d'envoi SMS au $subject car numéro incorrect : '$body' ");
		}
		
	function envoi_SMS($subject,$body)		
		{
		$subject=homogenise_telephone($subject) ;
		if (VerifierPortable($subject))
			{
			maj_compteur_envoi_mail();
			ecrit_parametre("TECH_nb_sms_envoyes",parametre("TECH_nb_sms_envoyes")+1) ;	
			
			$body= supprime_html($body);
			ajout_log_tech( "Envoi SMS au $subject : '$body' ");
			envoi_mail_brut(parametre('DD_mail_pour_gateway_sms'),$subject,$body);
			}
		else
			ajout_log_tech( "PAS d'envoi SMS au $subject car numéro incorrect : '$body' ");
		}	
		
	function alerte_SMS($body)		
		{
		$body = parametre("TECH_identite_environnement")." : ".$body;

		if (parametre('DD_tel_alarme1')!="")
			envoi_SMS(parametre('DD_tel_alarme1'),$body);	
		if (parametre('DD_tel_alarme2')!="")
			envoi_SMS(parametre('DD_tel_alarme2'),$body);
		}

		
function TTT_mail($aff=true)
	{
	$host=parametre('DEF_SERVEUR_MAIL_TTT'); 
	$login=parametre('DEF_ADRESSE_MAIL_TTT'); //imap login
	$password=parametre('DEF_PW_MAIL_TTT'); //imap password

	$mBox = imap_open(	$host, $login, $password); 
	
	$alarme=parametre("TECH_alarme_acces_bal");
	if (!$mBox)
		{
		if ($alarme==0)
			ecrit_parametre("TECH_alarme_acces_bal",1);
		else
			{
			if ($alarme=="") $alarme=0;
			$alarme++;
			ecrit_parametre("TECH_alarme_acces_bal",$alarme);
			if($alarme==parametre("nb_echec_alarme_acces_bal"))
				{
				ajout_log_tech( "Début alarme accès boite mail $login ","P0");
				envoi_mail(parametre('DD_mail_gestinonnaire'),"Alarme accès boite mail $login ","");
				}
			}
		echo " ==> echec Connexion boite mail";
		return;
		}
	else
		if ($alarme>=parametre("nb_echec_alarme_acces_bal"))
			{
			ajout_log_tech( "Fin alarme accès boite mail $login ","P0");
			envoi_mail(parametre('DD_mail_gestinonnaire'),"Fin alarme accès boite mail $login ","");
			ecrit_parametre("TECH_alarme_acces_bal",0);
			}
		
	$savedirpath="./tmp/" ; // attachement will save in same directory where scripts run othrwise give abs path
	$jk= new  MailAttachmentManager($host, $login, $password, $savedirpath); 
	$jk -> openMailBox();

	$nbmess = imap_num_msg($jk -> getMbox() );
	if ($aff) 
		echo "<p> #msg = ".$nbmess."<p> "; 

	for ($i=$nbmess; $i>=max($nbmess-15,1); $i--)
		{
		$header = imap_header($mBox, $i); // get first mails header
		$sujet = $header->subject;
		
		$l= $jk -> getAttachments($i, $sujet, $aff);
		if ($aff) echo " : ".$sujet;
			
		$pos = strpos($sujet, "sms2mail]");
		if ($pos == 1 )
			{
			$pos =strpos($sujet, "<")+4;
			$n= substr($sujet, $pos, 9 ); // recupération du numéro de téléphone dasn le titre du mail 
			
			$n= substr($sujet, strpos($sujet, "<")+1, strpos($sujet, ">")-strlen($sujet));
			$n=str_replace("+33","",$n);
			$n=str_replace("+262","",$n); // cas la réunion
			$n=str_replace("+590","",$n); // cas la martinique
			$n=str_replace("+594","",$n); // cas la guyane
			$n=str_replace("+596","",$n); // cas la guadeloupe
			
			if ($aff) echo " --> SMS : $n";
			if (
				(parametre('DD_numero_tel_sms')=="+33$n")  // test si cela vient de la gateway de reception
				||
				(parametre('DD_numero_tel_sms_E')=="+33$n")  // test si cela vient de la gateway d'emission
				)
				{
				$ligne = imap_fetchbody($mBox, $i, 1);
				Echo "Gatewaysms: "; 
				if (strstr($ligne,parametre('TECH_msg_supervision_gatewaysms') ) )// Cas particulier du SMS de supervision
					{
					Echo " Reception supervision gatewaysms"; 
					$delta= time()-parametre("TECH_dernier_envoi_supervision");
					ajout_log_tech( "Reception supervision gatewaysms (delais $delta sec)");
					ecrit_parametre('TECH_dernier_envoi_supervision', '' );
					if (parametre("TECH_alarme_supervision_sms")!="") 
						{
						envoi_mail(parametre('DD_mail_gestinonnaire'),"Fin alarme supervision gateway sms ","");	
						ajout_log_tech( "Fin alarme supervision gatewaysms","P0");
						}
					ecrit_parametre("TECH_alarme_supervision_sms",'') ;
					}			
				}
			else
				{
				$ligne = trim(imap_fetchbody($mBox, $i, 1));
				$pos = strpos(strtolower($ligne), "alerte");	
				$ligne=utf8_decode (quoted_printable_decode($ligne));
				ajout_log_tech( "Reception SMS de $n : '$ligne' ($pos)");
				if  (strtolower($ligne)=="stop" )
					command("delete from `cc_alerte` where tel='+33$n' ");
				else
					{
					// cas ALERTE SMS
					if  ( ($pos == 0 ) && !($pos ===false)) 
						{
						$dept = trim(substr(strtolower($ligne), 6) );
						if (($dept>1) && ($dept<100) )
							{
							$r1 = command("SELECT * FROM cc_alerte WHERE (tel='+33$n')  ");
							$d1 = fetch_command($r1);
							$date= date("Y-m-d");
							$t0=time();
							$ip= $_SERVER["REMOTE_ADDR"];

							if ($d1)
								command("UPDATE `cc_alerte` SET creation='$date' ,dept='$dept' ,sueil='',stop='' ,modif='$t0', ip='$ip' where tel='+33$n'  ");
							else
								command("INSERT INTO `cc_alerte`  VALUES ( '$date', '+33$n', '$dept', '','','','','','$ip','$t0')");
							envoi_sms_operateur("+33$n","Demande d'alerte SMS pris en compte pour 1 an. Vous pouvez arrêtez l'alerte en envoyant 'stop' au 06.98.47.43.12 (prix d'un sms non surtaxé)");
							ajout_log( "", "Enregistrement Alerte SMS par +33$n ($dept) ");
							}
						else
							envoi_sms_operateur("+33$n","Demande d'alerte SMS non pris en compte car pas d'indication de numéro de département");
						}
					else
						{				
						$cmd= "SELECT * from  r_user WHERE ((telephone='0$n') or (telephone='+33$n')  or (telephone='+262$n') or (telephone='+590$n') or (telephone='+594$n') or (telephone='+596$n')  ) and droit='' ";
						$reponse = command($cmd); 
						if ($donnees = fetch_command($reponse)) 
							{
							$id=$donnees["id"];
							$date_jour=date('Y-m-d')." ".$heure_jour=date("H\hi:s");
							$idx=$donnees["idx"];
							$ligne = imap_fetchbody($mBox, $i, 1);
							if ($donnees = fetch_command($reponse))
								{
								// cas où il y a 2 fois le même téléphone==> anormal
								ajout_log_tech ("2 fois le même numéro $n pour un bénéficiaire ! ","P1");
								}
							else
								{
								if (!strstr(strtolower($ligne), "activation"))
									{
									$ligne=utf8_decode (quoted_printable_decode($ligne));
									$ligne= addslashes2($ligne);
									
									$reponse = command("select * from  r_sms where idx='$idx' and ligne='$ligne' ");	
									if (!fetch_command($reponse))  // on vérifie que l'on a pas 2 fois le même message 
										{
										$num_seq=inc_index("notes");
										$reponse = command("INSERT INTO r_sms VALUES ('$date_jour', '$idx', '$ligne', '$num_seq' ) "); 
										}
									}
								else
									{
									recept_mail($idx,date('Y-m-d'));
									if (VerifierTelephone($n))
										envoi_SMS_operateur ("0$n","Réception de documents par mail autorisée pour la journée à $id@fixeo.com ou 0$n@fixeo.com","DOC-DEPOT");
									else
										envoi_SMS_operateur ("0$n","Réception de documents par mail autorisée pour la journée à $id@fixeo.com ","DOC-DEPOT");
									
									}
								}
							}
						}
					}
				}
			}					
		imap_delete($mBox, $i);
		imap_expunge($mBox);
		}

	}
	
/**
 * @author Florent Viel
 */
class MailAttachmentManager
{
   /**
   * Constructeur
   * @param string $host {host:port\params}BOX voir http://fr.php.net/imap_open
   * @param string $login
   * @param string $password
   * @param string $saveDirPath chemin de sauvegarde des pièces jointes
   */
   public  function __construct($host, $login, $password, $saveDirPath = './')
  {
    $this->host = $host;
    $this->login = $login;
    $this->password = $password;
    $this->saveDirPath = $savedirpath = substr($saveDirPath, -1) == "/" ? $saveDirPath : $saveDirPath."/";
  }

  /**
   * Décode le contenu du message
   * @param string $message message
   * @param integer $coding type de contenu
   * @return message décodé
   **/
   public  function getDecodeValue($message, $coding)
  {
    switch ($coding) {
      case 0: //text
      case 1: //multipart
        $message = imap_8bit($message);
        break;
      case 2: //message
        $message = imap_binary($message);
        break;
      case 3: //application
      case 5: //image
      case 6: //video
      case 7: //other
        $message = imap_base64($message);
        break;
      case 4: //audio
        $message = imap_qprint($message);
        break;
    }

    return $message;
  }

  /**
   * Ouvrir la boîte mail
   */
   public  function openMailBox()
  {
    $mbox = imap_open($this->host, $this->login, $this->password);
    if (!$mbox) {
      echo "can't connect: ".imap_last_error();
    }

    $this->mbox = $mbox;
  }

  /**
   * Ferme la boite mail en cours
   */
   public  function closeMailBox()
  {
    imap_close($this->mbox);
  }

  /**
   * Récupère les parties d'un message
   * @param object $structure structure du message
   * @return object|boolean parties du message|false en cas d'erreur
   */
   public  function getParts($structure)
  {
    return isset($structure->parts) ? $structure->parts : false;
  }

  /**
   * Tableau définissant la pièce jointe
   * @param object $part partie du message
   * @return object|boolean définition du message|false en cas d'erreur
   */
   public  function getDParameters($part)
  {
    return $part->ifdparameters ? $part->dparameters : false;
  }

    /**
   * Retourne la référence de l'hôte sans la boite mail
   * @return string {host:port\params} voir http://fr.php.net/imap_open
   */
   public  function getRef()
  {
    preg_match('#^{[^}]*}#', $this->host, $ref);
    return $ref[0];
  }

  /**
   * Retourne la liste des boites mail associées a celle ouverte
   * @param string $pattern motif de recherche
   * @return array liste des boites mail
   */
   public  function getList($pattern = '*')
  {
    return imap_list($this->mbox, $this->getRef(), $pattern);
  }

  /**
   * Récupère la contenu de la pièce jointe par rapport a sa position dans un mail donné
   * @param integer $jk numéro du mail
   * @param integer $fpos position de la pièce jointe
   * @param integer $type type de la pièce jointe
   * @return mixed data
   */
   public  function getFileData($jk, $fpos, $type)
  {
    $mege = imap_fetchbody($this->mbox, $jk, $fpos);
    $data = $this->getDecodeValue($mege,$type);

    return $data;
  }

  /**
   * Sauvegarde de la pièce jointe dans le dossier défini avec un nom unique
   * @param string $filename nom du fichier
   * @param mixed $data contenu à sauvegarder
   * @return string emplacement du fichier
   **/
   public  function saveAttachment($filename, $data)
  {
    $filepath = $this->saveDirPath.$filename;
    $tmp = explode('.', $filename);
    $ext = array_pop($tmp);
    $filename = implode('.', $tmp);
    $i=1;

    while (file_exists($filepath)) {
      $filepath = $this->saveDirPath.$filename.$i.'.'.$ext;
      $i++;
    }

    $fp = fopen($filepath, 'w');
    fputs($fp, $data);
    fclose($fp);

    return $filepath;
  }

  /**
   * Tag un message avec le flag delete
   * @param integer $jk numéro du message
   **/
   public  function tagDeleteMessage($jk)
  {
    imap_delete($this->mbox, $jk);
  }

  /**
   * Supprime les messages tagués avec le flag delete
   **/
   public  function deleteTaggedMessages()
  {
    imap_expunge($this->mbox);
  }

  /**
   * Retourne la boite mail
   * @return object boite mail
   */
   public  function getMbox()
  {
    return $this->mbox;
  }

  /**
   * Retourne le destinataire du message
   * @param integer $id numéro du mail
   * @return string mail
   */
   public  function getMessageTo($id)
  {
    $header = imap_fetchheader($this->mbox, $id);
    $header = imap_rfc822_parse_headers($header);
    return $header->to[0]->mailbox.'@'.$header->to[0]->host;
  }

  /**
   * Retourne l'emmetteur du message
   * @param integer $id numéro du mail
   * @return string mail
   */
   public  function getMessageFrom($id)
  {
    $header = imap_fetchheader($this->mbox, $id);
    $header = imap_rfc822_parse_headers($header);
    return $header->from[0]->mailbox.'@'.$header->from[0]->host;
  }


 /**
   * Récupère les pièces d'un mail donné
   * @param integer $jk numéro du mail
   * @return array type, filename, pos
   */
   public  function getAttachments($jk, $sujet,$aff=false)
  {
  	if (strlen($sujet) > 20)  
		$sujet= substr($sujet,0,17).'...' ;
		
	// pourquoi ce bloc ?
	$pos = strpos($sujet, "=?");
	if ($pos === false)
		{}
	else
		$sujet="";	
		
	$a_supprimer=0;
	if ($aff) echo "<br> - $jk";
    $structure = imap_fetchstructure($this->mbox, $jk);
    $parts = $this->getParts($structure);
    $fpos = 2;
    $attachments = array();
	
    if ($parts && count($parts)) {
	   
      for ($i = 1; $i < count($parts); $i++) {
        $part = $parts[$i];

        if ($part->ifdisposition && strtolower($part->disposition) == "attachment") {        
          $ext=$part->subtype;
          $params = $this->getDParameters($part);

          if ($params) {
            $filename = $part->dparameters[0]->value;  // attetion parfois il faut 0 (free;fr)
            $filename = imap_utf8($filename);
			if ($aff) echo " --> ".$filename. " - "; 

			if ((extension_fichier($filename)=="pdf") || (extension_fichier($filename)=="vcf") || (est_image($filename) )|| (est_doc($filename)))
				{

				$a = $this -> getMessageTo($jk);

				$header = imap_fetchheader($this->mbox, $jk);
				$header = imap_rfc822_parse_headers($header);

				$flag_MMS= strstr($header->subject,"MMS: ");

				//    message mail   à fixeo.com                      ou   reception MMS de SMS GATEWAY
				if ( (strtolower($header->to[0]->host)=="fixeo.com") || ( $flag_MMS) ) 
					{
					if ( $flag_MMS )
						$id= "0".substr ($header->subject, strpos ($header->subject,"+33")+3,9);
					else
						$id= $header->to[0]->mailbox;
						
					if ($aff)
						echo "<br> $jk/$i ($a) : $filename ";
					
					$reponse = command ("SELECT * FROM r_user WHERE ( (id like '$id' ) or telephone='$id') and droit='' "); 
					if ($donnees = fetch_command($reponse))
						{
						$droit=$donnees["droit"];
						$user=$donnees["idx"];
						$recept_mail=$donnees["recept_mail"];
						$telephone=$donnees["telephone"];

							$date_jour=date('Y-m-d');
							$_SESSION['droit']='';
							
							$from = $header->from[0]->mailbox."@".$header->from[0]->host ;			
							// vérifiction si cela ne vient pas d'un référent de confiance
							$vient_de_RC=false;
							
							// autorise tous tous RC
							$r1 = command("SELECT * FROM r_user WHERE mail='$from' and (droit='S' or droit='M' or droit='R' )"); 
							if ($d1 = fetch_command($r1))  // on a trouver un utilisateur
								{
								$vient_de_RC=true;
								$_SESSION['droit']='S';
								}
							
							// autorise les RC qui ne sont pas des AS
							$r1 = command("SELECT * FROM r_referent WHERE mail='$from' and organisme='' and user='$user' "); 
							if ($d1 = fetch_command($r1))  // on a trouver un utilisateur
								$vient_de_RC=true;

									
							$r1 = command("SELECT * FROM r_user WHERE mail='$from' and idx='$user'  "); 
							if ($d1 = fetch_command($r1))  // le mail vient du bénéficiaire lui meme
								$vient_du_beneficiaire=true;		
									
							if (($recept_mail>=$date_jour) || $vient_de_RC || $flag_MMS || $vient_du_beneficiaire)
								{
								if ($aff) echo " - Bénéficiaire $id OK!";
								$attachments[] = array('type' => $part->type, 'filename' => $filename, 'pos' => $fpos);
								
								$da= $this-> getFileData($jk, $fpos, $part->type);
								
								$this->saveAttachment($filename, $da);
								if (extension_fichier($filename)=="vcf") // si c'est une carte vcf
									{
									include ("vcard.php");
									$ligne= extrait_vcard("tmp/$filename");
									ajoute_note( $user, $ligne );
									ajout_log( $user,"Contact VCF reçu : '$ligne' ",$user);
									}
								else
									 {
									 
									if (!$flag_MMS)
										{
										if (charge_image("1","tmp/$filename",str_replace(" ","_","$filename"),$donnees["lecture"],"A-$user", $sujet, "Mail",$from, $user))
											envoi_mail($from,"Document $filename ajouté à $id.","");
										else
											envoi_mail($from,"Erreur : $filename n'a pas été ajouté à $id.","La taille doit être inférieure à 3 Mo et le format doit être du type image JPG ou PDF. ");
										}
									else // si image dasn MMS alors on stocke dans espace perso
										{

										if (charge_image("1","tmp/$filename",str_replace(" ","_","$filename"),$donnees["lecture"],"P-$user", $sujet, "MMS",$from, $user))
											{
											envoi_SMS_operateur($telephone , "MMS déposé dans votre espace personnel.","DOC-DEPOT");
											ajout_log( $user,"MMS reçu de $telephone et déposé dans espace personnel : '$filename' ",$user);
											}
										}
									}
									
								}
					

						}
				
					}
				}
          }
        }
        $fpos++;
      }

    }

    return $attachments;
  }
}		


	?>

