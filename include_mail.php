	
    <?php 

		
	function envoi_mail($to,$subject,$body,$masque=false)
		{
		// Sujet du mail
		$subject = "DOC-DEPOT : $subject";
		  
		// Le message
		if ($to!=parametre('DD_mail_gestinonnaire'))
			$body = "<center><a href=\"http://doc-depot.com\"><img src=\"http://doc-depot.com/images/logo.png\" width=\"150\" height=\"100\" ></a><p>
					<font size=\"5\">'' Mon essentiel à l'abri en toute confiance ''</font> <p> $body";
		
		ecrit_parametre("TECH_nb_mail_envoyes",parametre("TECH_nb_mail_envoyes")+1) ;
		
		if (($_SERVER['REMOTE_ADDR']=="127.0.0.1"))
			{
			echo "<table border=\"2\"> <tr> <td>$to <hr>" ;
			echo "$subject <hr>" ;
			echo "$body </td> </table>" ;
			return;
			}
		
		$headers  = "MIME-Version: 1.0 \n"; 
		$headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
		$headers .='From: Doc-Depot <fixeo@doc-depot.com>'."\n";
        $headers .='Reply-To: Doc-Depot <fixeo@doc-depot.com>'."\n";
        $headers .='Content-Transfer-Encoding: 8bit'."\n";			
    
		$body = "<html><body>$body</body></html>";	
		$subject = '=?iso-8859-1?B?'.base64_encode($subject).'?=';
		// Envoi de l'email
		$CR_Mail = @mail ($to, $subject, $body, $headers);
		if (!$masque)
			{
			if ($CR_Mail === FALSE)
					erreur( " Erreur envoi mail: $CR_Mail <br> \n");
				else
					echo " Mail envoyé<br> \n";
			}

		}

	function envoi_mail_brut($to,$subject,$body)		
		{
				ecrit_parametre("TECH_nb_sms_envoyes",parametre("TECH_nb_sms_envoyes")+1) ;

		if (($_SERVER['REMOTE_ADDR']=="127.0.0.1"))
			{
			echo "<table border=\"2\"> <tr> <td> SMS </td><td>" ;
			echo "$subject </td><td> " ;
			echo "$body </td> </table>" ;
			return;
			}
			
		$CR_Mail = @mail ($to, $subject, $body);
		if ($CR_Mail === FALSE)
			erreur( " Erreur envoi mail: $CR_Mail <br> \n");

		}

		
	function envoi_SMS($subject,$body)		
		{
		ajout_log_tech( "Envoi SMS au $subject : '$body' ");
		envoi_mail_brut(parametre('DD_mail_pour_gateway_sms'),$subject,$body);
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
		if ($alarme=="")
			{
			ecrit_parametre("TECH_alarme_acces_bal",time());
			ajout_log_tech( "Début alarme accès boite mail $login ","P0");
			envoi_mail(parametre('DD_mail_gestinonnaire'),"Début alarme accès boite mail $login ","");
			}
		echo " ==> echec Connexion boite mail";
		return;
		}
	else
		if ($alarme!="")
			{
			ajout_log_tech( "Fin alarme accès boite mail $login ","P0");
			envoi_mail(parametre('DD_mail_gestinonnaire'),"Fin alarme accès boite mail $login ","");
			ecrit_parametre("TECH_alarme_acces_bal","");
			}
		
	$savedirpath="./tmp/" ; // attachement will save in same directory where scripts run othrwise give abs path
	$jk= new  MailAttachmentManager($host, $login, $password, $savedirpath); 
	$jk -> openMailBox();

	$nbmess = imap_num_msg($jk -> getMbox() );
	if ($aff) 
		echo "<p> ".$nbmess."<p> "; 

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
			$n= substr($sujet, $pos, 9 ); // recupération du numéro de téléphone dasn le titre du mail 
			
			if ($aff) echo " --> SMS : $n";
			if (parametre('DD_numero_tel_sms ')=="+33$n")  // test si cela vient de la gateway
				{
				$ligne = imap_fetchbody($mBox, $i, 1);
				Echo "Gatewaysms: "; 
				if (strstr($ligne,parametre('TECH_msg_supervision_gatewaysms') ) )// Cas particulier du SMS de supervision
					{
					Echo " Reception supervision gatewaysms"; 
					$delta= time()-parametre("TECH_dernier_envoi_supervision");
					ajout_log_tech( "Reception supervision gatewaysms (delais $delta sec)");
					ecrit_parametre('TECH_dernier_envoi_supervision', '' );
					}			
				}
			else
				{
				$cmd= "SELECT * from  r_user WHERE ((telephone='0$n') or (telephone='+33$n')  ) and droit='' ";
				$reponse = mysql_query($cmd); 
				if ($donnees = mysql_fetch_array($reponse)) 
						{
						$date_jour=date('Y-m-d')." ".$heure_jour=date("H\hi:s");
						$idx=$donnees["idx"];
						$ligne = imap_fetchbody($mBox, $i, 1);
						
						if ($donnees = mysql_fetch_array($reponse))
							{
							// cas où il y a 2 fois le même téléphone==> anormal
							ajout_log_tech ("2 fois le même numéro $n pour un bénéficiaire ! ","P1");
							}
						else
							{
							if (!strstr(strtolower($ligne), "activation"))
								{
								$cmd= "INSERT INTO r_sms VALUES ('$date_jour', '$idx', '$ligne' ) ";
								$reponse = mysql_query($cmd); 
								}
							else
								recept_mail($idx,date('Y-m-d'));
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
					
					$reponse = command ("","SELECT * FROM r_user WHERE ( id='$id' or telephone='$id') and droit='' "); 
					if ($donnees = mysql_fetch_array($reponse))
						{
						echo " x0 ";
						$droit=$donnees["droit"];
						$user=$donnees["idx"];
						$recept_mail=$donnees["recept_mail"];
						$telephone=$donnees["telephone"];

							$date_jour=date('Y-m-d');
							
							$from = $header->from[0]->mailbox."@".$header->from[0]->host ;			
							// vérifiction si cela ne vient pas d'un référent de confiance
							$vient_de_RC=false;
							
							$r1 = mysql_query("SELECT * FROM r_user WHERE mail='$from')"); 
							if ($d1 = mysql_fetch_array($r1))  // on a trouver un utilisateur
								if ($d1["droit"]=='S') // c'est bien un Acteur Social
									{
									$rc_idx=$d1["idx"];
									$r2 = mysql_query("SELECT * FROM r_referent WHERE user='$user' and nom='$rc_idx' and organisme='' "); 
									if ($d2 = mysql_fetch_array($r2))  
										$vient_de_RC=true;
									}
							
							if (($recept_mail>=$date_jour) || $vient_de_RC || $flag_MMS )
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
									}
								else
									 {		
									if (!$flag_MMS)
										{
										if (charge_image("1","tmp/$filename","$filename",$donnees["lecture"],"A-$user", $sujet, "Mail",$from, $user))
											envoi_mail($from,"Document $filename ajouté à $id.","");
										else
											envoi_mail($from,"Erreur : $filename n'a pas été ajouté à $id.","La taille doit être inférieure à 3 Mo et le format doit être du type image (JPG) ou PDF. ");
										}
									else // si image dasn MMS alors on stocke dans espace perso
										{

										if (charge_image("1","tmp/$filename","$filename",$donnees["lecture"],"P-$user", $sujet, "MMS",$from, $user))
											envoi_SMS($telephone , "MMS déposé dans votre espace personnel.");
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

