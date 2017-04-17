	
    <?php 
// traduire() : Ok

	function maj_compteur_envoi_mail()
		{
		$nb=parametre("TECH_nb_mail_envoyes")+1;
		// on met � jour le nombre de mail envoy� 
		ecrit_parametre("TECH_nb_mail_envoyes",$nb) ;

		if ($nb>0.7*parametre("DD_nbre_mail_jour_max"))
			ajout_log_tech( "Attention seuil d'envoi mails d�pass� :".parametre("TECH_nb_mail_envoyes")." / ".parametre("DD_nbre_mail_jour_max"));

		}
		
	// envoi de mail sans mise en forme Doc-d�pot
	function envoi_mail_perso($to,$subject,$body, $origine, $from="")
		{
		// on signe le mail avec le nom de l'emetteur
		$body = "$body <p> Mail envoy� depuis doc-depot.com par $origine";

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
		
	// envoi de mail avec mise en forme doc-depot
	function envoi_mail($to,$subject,$body,$masque=false)
		{
		maj_compteur_envoi_mail();
		
		$envir = parametre("TECH_identite_environnement");
			
		// Sujet du mail
		$subject = "$envir : $subject";			
		
		// Le message vers le gestionnaire est r�duit (pas d'image ni slogan)
		if ($to!=parametre('DD_mail_gestinonnaire'))
			{
			// contenu du message
			$body = "<center><a href=\"https://doc-depot.com\"><img src=\"http://doc-depot.com/images/logo.png\" width=\"150\" height=\"100\" ></a><p>
					<h3>".traduire('La Consigne Num�rique Solidaire')."</h3>
					<font size=\"5\">'' ".traduire("Mon essentiel � l'abri en toute confiance")." ''</font> <p> $body";
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
					msg_ok( traduire('Mail envoy�'));
			}
		}

	function envoi_mail_brut($to,$subject,$body)		
		{

		maj_compteur_envoi_mail();
		ecrit_parametre("TECH_nb_sms_envoyes",parametre("TECH_nb_sms_envoyes")+1) ;

		if (($_SERVER['REMOTE_ADDR']=="127.0.0.1") ||(isset ($_SESSION['chgt_user']) && ($_SESSION['chgt_user']==true) ))
			{
			echo "<table border=\"2\"> <tr> <td> SMS </td><td>" ;
			echo "$subject </td><td> " ;
			echo "$body </td> </table>" ;
			return;
			}
			
		$CR_Mail = @mail ($to, $subject, $body);
		if ($CR_Mail === FALSE)
			erreur( "Erreur envoi SMS: $CR_Mail <br> \n");

		}

		
	function envoi_SMS($subject,$body)		
		{
		if (VerifierPortable($subject))
			{
			ajout_log_tech( "Envoi SMS au $subject : '$body' ");
			if ( ($body!=parametre('FORM_msg_rdv')) || ($subject!=parametre('FORM_tel_rdv'))) 
				envoi_mail_brut(parametre('DD_mail_pour_gateway_sms'),$subject,$body);
			}
		else
			ajout_log_tech( "PAS d'envoi SMS au $subject car num�ro incorrect : '$body' ");
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
				ajout_log_tech( "D�but alarme acc�s boite mail $login ","P0");
				envoi_mail(parametre('DD_mail_gestinonnaire'),"Alarme acc�s boite mail $login ","");
				}
			}
		echo " ==> echec Connexion boite mail";
		return;
		}
	else
		if ($alarme>=parametre("nb_echec_alarme_acces_bal"))
			{
			ajout_log_tech( "Fin alarme acc�s boite mail $login ","P0");
			envoi_mail(parametre('DD_mail_gestinonnaire'),"Fin alarme acc�s boite mail $login ","");
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
		if (strlen($sujet) > 20)  
			substr($sujet,0,17).'...' ;
		$pos = strpos($sujet, "=?");
		if ($pos === false)
			{}
		else
			$sujet="";	

		$l= $jk -> getAttachments($i, $sujet, $aff);
		if ($aff) echo " : ".$sujet;
			
		$pos = strpos($sujet, "sms2mail]");
		if ($pos == 1 )
			{
			$pos =strpos($sujet, "<")+4;
			$n= substr($sujet, $pos, 9 ); // recup�ration du num�ro de t�l�phone dasn le titre du mail 
			
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
						envoi_mail(parametre('DD_mail_gestinonnaire'),"Fin alarme supervision gateway sms ","");
					ecrit_parametre("TECH_alarme_supervision_sms",'') ;
					}			
				}
			else
				{
				$ligne = trim(imap_fetchbody($mBox, $i, 1));
				$pos = strpos(strtolower($ligne), "alerte");	
				
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
							$format_date = "d/m/Y";
							$date= date($format_date );
							$t0=time();
							$ip= $_SERVER["REMOTE_ADDR"];

							if ($d1)
								command("UPDATE `cc_alerte` SET dept='$dept' ,sueil='-5',stop='' ,modif='$t0', ip='$ip' where tel='+33$n'  ");
							else
								command("INSERT INTO `cc_alerte`  VALUES ( '$date', '+33$n', '$dept', '-5','','','','','$ip','$t0')");
							}
						else
							envoi_sms("+33$n","Demande d'alerte SMS non pris en compte car pas d'indication de num�ro de d�partement");
						}
					else
						{				
						$cmd= "SELECT * from  r_user WHERE ((telephone='0$n') or (telephone='+33$n')  ) and droit='' ";
						$reponse = command($cmd); 
						if ($donnees = fetch_command($reponse)) 
							{
							$date_jour=date('Y-m-d')." ".$heure_jour=date("H\hi:s");
							$idx=$donnees["idx"];
							$ligne = imap_fetchbody($mBox, $i, 1);
							
							if ($donnees = fetch_command($reponse))
								{
								// cas o� il y a 2 fois le m�me t�l�phone==> anormal
								ajout_log_tech ("2 fois le m�me num�ro $n pour un b�n�ficiaire ! ","P1");
								}
							else
								{
								if (!strstr(strtolower($ligne), "activation"))
									{
									$cmd= "INSERT INTO r_sms VALUES ('$date_jour', '$idx', '$ligne' ) ";
									$reponse = command($cmd); 
									}
								else
									recept_mail($idx,date('Y-m-d'));
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
   * @param string $saveDirPath chemin de sauvegarde des pi�ces jointes
   */
   public  function __construct($host, $login, $password, $saveDirPath = './')
  {
    $this->host = $host;
    $this->login = $login;
    $this->password = $password;
    $this->saveDirPath = $savedirpath = substr($saveDirPath, -1) == "/" ? $saveDirPath : $saveDirPath."/";
  }

  /**
   * D�code le contenu du message
   * @param string $message message
   * @param integer $coding type de contenu
   * @return message d�cod�
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
   * Ouvrir la bo�te mail
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
   * R�cup�re les parties d'un message
   * @param object $structure structure du message
   * @return object|boolean parties du message|false en cas d'erreur
   */
   public  function getParts($structure)
  {
    return isset($structure->parts) ? $structure->parts : false;
  }

  /**
   * Tableau d�finissant la pi�ce jointe
   * @param object $part partie du message
   * @return object|boolean d�finition du message|false en cas d'erreur
   */
   public  function getDParameters($part)
  {
    return $part->ifdparameters ? $part->dparameters : false;
  }

    /**
   * Retourne la r�f�rence de l'h�te sans la boite mail
   * @return string {host:port\params} voir http://fr.php.net/imap_open
   */
   public  function getRef()
  {
    preg_match('#^{[^}]*}#', $this->host, $ref);
    return $ref[0];
  }

  /**
   * Retourne la liste des boites mail associ�es a celle ouverte
   * @param string $pattern motif de recherche
   * @return array liste des boites mail
   */
   public  function getList($pattern = '*')
  {
    return imap_list($this->mbox, $this->getRef(), $pattern);
  }

  /**
   * R�cup�re la contenu de la pi�ce jointe par rapport a sa position dans un mail donn�
   * @param integer $jk num�ro du mail
   * @param integer $fpos position de la pi�ce jointe
   * @param integer $type type de la pi�ce jointe
   * @return mixed data
   */
   public  function getFileData($jk, $fpos, $type)
  {
    $mege = imap_fetchbody($this->mbox, $jk, $fpos);
    $data = $this->getDecodeValue($mege,$type);

    return $data;
  }

  /**
   * Sauvegarde de la pi�ce jointe dans le dossier d�fini avec un nom unique
   * @param string $filename nom du fichier
   * @param mixed $data contenu � sauvegarder
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
   * @param integer $jk num�ro du message
   **/
   public  function tagDeleteMessage($jk)
  {
    imap_delete($this->mbox, $jk);
  }

  /**
   * Supprime les messages tagu�s avec le flag delete
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
   * @param integer $id num�ro du mail
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
   * @param integer $id num�ro du mail
   * @return string mail
   */
   public  function getMessageFrom($id)
  {
    $header = imap_fetchheader($this->mbox, $id);
    $header = imap_rfc822_parse_headers($header);
    return $header->from[0]->mailbox.'@'.$header->from[0]->host;
  }


 /**
   * R�cup�re les pi�ces d'un mail donn�
   * @param integer $jk num�ro du mail
   * @return array type, filename, pos
   */
   public  function getAttachments($jk, $sujet,$aff=false)
  {
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

				//    message mail   � fixeo.com                      ou   reception MMS de SMS GATEWAY
				if ( (strtolower($header->to[0]->host)=="fixeo.com") || ( $flag_MMS) ) 
					{
					if ( $flag_MMS )
						$id= "0".substr ($header->subject, strpos ($header->subject,"+33")+3,9);
					else
						$id= $header->to[0]->mailbox;
						
					if ($aff)
						echo "<br> $jk/$i ($a) : $filename ";
					
					$reponse = command ("SELECT * FROM r_user WHERE ( id='$id' or telephone='$id') and droit='' "); 
					if ($donnees = fetch_command($reponse))
						{
						echo " x0 ";
						$droit=$donnees["droit"];
						$user=$donnees["idx"];
						$recept_mail=$donnees["recept_mail"];
						$telephone=$donnees["telephone"];

							$date_jour=date('Y-m-d');
							
							$from = $header->from[0]->mailbox."@".$header->from[0]->host ;			
							// v�rifiction si cela ne vient pas d'un r�f�rent de confiance
							$vient_de_RC=false;
							
							$r1 = command("SELECT * FROM r_user WHERE mail='$from')"); 
							if ($d1 = fetch_command($r1))  // on a trouver un utilisateur
								if ($d1["droit"]=='S') // c'est bien un Acteur Social
									{
									$rc_idx=$d1["idx"];
									$r2 = command( "SELECT * FROM r_referent WHERE user='$user' and nom='$rc_idx' and organisme='' "); 
									if ($d2 = fetch_command($r2))  
										$vient_de_RC=true;
									}
							
							if (($recept_mail>=$date_jour) || $vient_de_RC || $flag_MMS )
								{
								if ($aff) echo " - B�n�ficiaire $id OK!";
								$attachments[] = array('type' => $part->type, 'filename' => $filename, 'pos' => $fpos);
								
								$da= $this-> getFileData($jk, $fpos, $part->type);
								
								$this->saveAttachment($filename, $da);
								if (extension_fichier($filename)=="vcf") // si c'est une carte vcf
									{
									include ("vcard.php");
									$ligne= extrait_vcard("tmp/$filename");
									ajoute_note( $user, $ligne );
									ajout_log( $user,"Contact VCF re�u : '$ligne' ",$user);
									}
								else
									 {		
									if (!$flag_MMS)
										{
										if (charge_image("1","tmp/$filename","$filename",$donnees["lecture"],"A-$user", $sujet, "Mail",$from, $user))
											envoi_mail($from,"Document $filename ajout� � $id.","");
										else
											envoi_mail($from,"Erreur : $filename n'a pas �t� ajout� � $id.","La taille doit �tre inf�rieure � 3 Mo et le format doit �tre du type image (JPG) ou PDF. ");
										}
									else // si image dasn MMS alors on stocke dans espace perso
										{

										if (charge_image("1","tmp/$filename","$filename",$donnees["lecture"],"P-$user", $sujet, "MMS",$from, $user))
											{
											envoi_SMS($telephone , "MMS d�pos� dans votre espace personnel.");
											ajout_log( $user,"MMS re�u de $telephone et d�pos� dans espace personnel : '$filename' ",$user);
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

