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

require_once 'include_crypt.php';
require_once 'exploit.php';

	function ttt_echec_cx ($id,$mot_de_passe="")
		{
		global $droit;

		if (
			( (strpos( $_SERVER['PHP_SELF'],"index.php")===FALSE)  && (strpos( $_SERVER['PHP_SELF'],"wm.php")===FALSE) )
			&&
			 (  ($droit=="")  || ($droit=="M")  || ($droit=="A")  || ($droit=="E") )
			 )
			 {
			erreur(traduire("Vous n'avez pas les droits pour d'accéder à ce module. Pour accèder à Doc-depot, cliquez")." <a  id=\"lien\"  href=\"".serveur."index.php\">".traduire("ici")."</a>") ;
			$_SESSION['pass']=false;
			}
			else
			{
			tempo_cx ($id);
			$ip= $_SERVER["REMOTE_ADDR"];
			tempo_cx ($ip);	
			erreur(traduire("Mot de passe incorrect")." !!"). 
			$_SESSION['pass']=false;
			ajout_log( $id,traduire("Echec Connexion")."  $id ");
			ajout_log_tech( traduire("Echec Connexion")."  $id / $mot_de_passe / ".$_POST['pass']);
			ajout_echec_cx ($id);
			ajout_echec_cx ($ip);
			}
		}
		
	
	if ($action=="dx") 
		{
		if ( (isset ($_SESSION['pass'])) && ($_SESSION['pass']==TRUE)  ) 
				ajout_log( $_SESSION['user_idx'], traduire('Déconnexion') );
		$_SESSION['pass']=false;// et hop le mot de passe... poubelle !
		$_SESSION['chgt_user']=false;
		echo "<div id=\"msg_dx\">".traduire('Vous êtes déconnecté!')."</div><br>";
		}

// ---------------------------------------on récupére les information de la personne connectée
if (isset($_POST['pass'])) // mot de passe défini
	{
	$id_post=variable('id');
	$reponse = command(sprintf("SELECT * from  r_user WHERE id='%s' ",$id_post ) ); 
		if (!($donnees = fetch_command($reponse)))
			{
			ttt_echec_cx (sprintf("%s",$id_post));
			//ajout_log_tech("User inconnu ");
			ajout_log_tech("User inconnu '$id_post' - '".$_POST['id']);
			ajout_log_tech("Mot de passe : '".$_POST['pass']."'");			
			//ajout_log_tech("Source : ".$_SERVER['PHP_SELF'] );			
			}
		else
			{
			$mot_de_passe=addslashes($donnees["pw"]);	
			$droit=$donnees["droit"];	
			$id=$donnees["id"];
			$date_log=date('Y-m-d');	
			$heure_jour=date("H\hi.s");	
			$_SESSION['bene']="";
			//ajout_log_tech("User connu ");

			ajout_log_tech("'$id' - '".$_POST['id']."' - '$id_post'");
			ajout_log_tech("'".decrypt($mot_de_passe )."' - '".$_POST['pass']."'");			
			ajout_log_tech("'$mot_de_passe' - '".encrypt(addslashes($_POST['pass']))."' - '".encrypt($_POST['pass'])."'" );
			ajout_log_tech("Droit : '$droit' " );			
			ajout_log_tech("Source : ".$_SERVER['PHP_SELF'] );

			// verifion si la variable = mot de passe...
			if ( ( 
				(encrypt(addslashes($_POST['pass']))==$mot_de_passe) // on vérifie le mot de passe 
				||			
				(strtolower($_POST['pass'])==strtolower(decrypt($mot_de_passe)) ) // on vérifie le mot de passe en minuscule
				||
				// cas particulier en mode poste de développement on vérifie aussu un mot de passe en clair 
				(($_POST['pass']==$mot_de_passe) && ($_SERVER['REMOTE_ADDR']=="127.0.0.1")	 )
				) 
				&& ( !strstr($donnees["droit"] ,"-" ) )  // ceux qui sont désactivé ne peuvent pas accéder
				&& ( strtolower($id)==strtolower($id_post))  // sécurité : on s'assure que l'id lu est bien celui demandé (et non in contournement de la requette
					&& ( ( ($droit!="")  && ($droit!="M") && ($droit!="A") && ($droit!="E") ) || (strpos( $_SERVER['PHP_SELF'],"index.php")>0)  || (strpos( $_SERVER['PHP_SELF'],"wm.php")>0)  )  // ceux qui sont désactivé ne peuvent pas accéder
					)
					{
					supp_echec_cx ($_POST['id']);
					$ip= $_SERVER["REMOTE_ADDR"];
					supp_echec_cx ($ip);
					$_SESSION['pass']=true;	 
					$idx=$donnees["idx"];
					$_SESSION['user']=$idx;	 
					ajout_log( $idx, traduire('Connexion') );
					if (decrypt($mot_de_passe)=="123456") 
						{
						$action="modif_mdp";
						$identifiant=$id;
						}
						
					$ancien_droit="";
					if (isset($_SESSION['droit']))
						$ancien_droit=$_SESSION['droit'];
						
					$_SESSION['user_idx']=$donnees["idx"];
					$_SESSION['droit']= $donnees["droit"];
					$user_fuseau= $donnees["fuseau"];
					$_SESSION['filtre']= "";
					$_SESSION['ad']=false;	
					$_SESSION['chgt_user']=false;
					
					if (($donnees["droit"]=="A") && ($ancien_droit!="A"))
						envoi_mail( parametre('DD_mail_gestinonnaire') , "Connexion administrateur : ".$donnees["nom"]." ".$donnees["prenom"], "IP : $ip" );
						
					if (($donnees["droit"]=="E") && ($ancien_droit!="E"))
						envoi_mail( parametre('DD_mail_gestinonnaire') , "Connexion exploitant : ".$donnees["nom"]." ".$donnees["prenom"], "IP : $ip" );		
					
					// supprime les demandes de recupération de mot de passe encore actif 
					$reponse =command("UPDATE r_dde_acces set type='-' where user='$idx' and type='' and date_dde>='$date_log' ");
					$label = libelle_user($idx);
					$last_cx = "";
					$reponse =command("select * from  log where ( user='$idx' or user='$id'or user='$label'  ) and  (ligne Like '%Connexion%') and  (not (ligne Like '%Déconnexion%')) and ( not (ligne Like '%Echec Connexion%'))  order by date DESC ");		
					if ($donnees = fetch_command($reponse))// c'est la connexion actuelle
						if ($donnees = fetch_command($reponse) )// c'est la connexion précédente
							{
							$last_cx=$donnees["date"];
							if ($last_cx!="")
								{
								maj_last_cx($idx);
								$ligne_last_cx = traduire('Dernière connexion')." :<br> $last_cx. ";
								
								$reponse =command("select * from  log where ( user='$idx' or user='$id'  or user='$label' ) and  (ligne Like '%Echec Connexion%') order by date DESC ");		
								$donnees = fetch_command($reponse); 
								$last_echec_cx=$donnees["date"];	
								if ($last_echec_cx>$last_cx)
									echo traduire("Depuis votre derniére connexion, il y a eu tentative de connexion à votre compte, merci de consulter votre")." <a href=\"index.php?".token_ref("histo")."\"  >".traduire('historique')."</a> ";
								}
							}
					if ($_SESSION['droit']=="")
						ctrl_signature_user( $idx );
					
					// verification que les dates CG n'ont pas changé depuis la derniere connexion
					// si c'est le cas on en infome l'utilisateur
					$date_cg=parametre("DD_date_cg");			
					if ($last_cx<$date_cg)
						echo traduire("Les conditions générales de 'doc-depot.com' ont changé, merci d'en prendre connaissance en cliquant")." <a href=\"conditions.html\"  >".traduire('ici')."</a> ";
				
					}
				else
					ttt_echec_cx ($id,$mot_de_passe." - ". encrypt(addslashes($_POST['pass'])));
			}
		}
	

	// permet à l'exploitant de prendre le role de n'importe quel utilisateur mais sans mise à jour SQL
	if ( ($action=="chgt_user") && ($_SESSION['droit']=="E"))
		{
		$_SESSION['user']=variable_s('user');
		unset($_SESSION['droit']);
		$_SESSION['chgt_user']=true;
		
		$idx=$_SESSION['user'];
		$reponse = command("SELECT * from  r_user WHERE idx='$idx'"); 
		$donnees = fetch_command($reponse);
		$_SESSION['droit']=$donnees["droit"];
		$action="";
		}
		
	// ------------------------------------ on collecte les infos utiles du user connceté
	if ( (isset($_SESSION['user'])) && (is_numeric($_SESSION['user'])) )
		{
		$idx=$_SESSION['user'];
		$reponse = command("SELECT * from  r_user WHERE idx='$idx'"); 
		$donnees = fetch_command($reponse);
		$user_idx=$donnees["idx"];
		$_SESSION['acteur']=$user_idx; // utilisé par le upload en mode drag and drop 
		$id=$donnees["id"];
		$pw=$donnees["pw"];
		$user_nom=$donnees["nom"];
		$user_prenom=$donnees["prenom"];
		$user_droit_org=$donnees["droit"];
	
		if 	( ( ($user_droit_org=="") || ($user_droit_org=="M") || ($user_droit_org=="A") || ($user_droit_org=="F") || ($user_droit_org=="E") || ($user_droit_org=="T") || ($user_droit_org=="t") )  
			&& strpos( $_SERVER['PHP_SELF'],"index.php")===FALSE 
			&& strpos( $_SERVER['PHP_SELF'],"wm.php")===FALSE  
				)
			{
			$_SESSION['pass']=false;// et hop le mot de passe... poubelle !
			$_SESSION['chgt_user']=false;
			}
		else
			{
			if (!isset($_SESSION['droit']))
				$user_droit=$donnees["droit"];
			else
				$user_droit=$_SESSION['droit'];
			$user_fuseau= $donnees["fuseau"];
			$user_type_user=$donnees["type_user"];
			$user_anniv=$donnees["anniv"];
			$user_telephone=$donnees["telephone"];
			$user_mail=$donnees["mail"];
			$user_lecture=$donnees["lecture"];
			$user_nationalite=$donnees["nationalite"];
			$user_ville_nat=$donnees["ville_nat"];
			$user_adresse=stripcslashes($donnees["adresse"]);
			$user_organisme=stripcslashes($donnees["organisme"]);
			$user_lang=$donnees["langue"];
			$reponse = command("SELECT * from  fct_fissa WHERE organisme='$user_organisme'"); 
			if ($donnees = fetch_command($reponse))
				{
				$_SESSION['support']=$donnees["support"];
				$bdd=$_SESSION['support'];
				}
			$r1 =command("select * from  r_organisme where idx='$user_organisme' ");
			$d1 = fetch_command($r1);
			$logo=$d1["logo"];
			$_SESSION['logo']=$logo;
			}
		}
	else 
		unset($_SESSION['user']);

	if( strpos(parametre("DD_Ip_bannis",""),$_SERVER["REMOTE_ADDR"]) !==false )
		{
		erreur(traduire("Mot de passe incorrect")." !!"). 
		$_SESSION['pass']=false;
		ajout_log_tech( traduire("Refus Connexion ip banni")." : ".$_SERVER["REMOTE_ADDR"]);
		}

	if ( !isset($_SESSION['pass']) ||($_SESSION['pass']==false) || !(isset($_SESSION['user'])) || ($_SESSION['user']=="") )
		// si pas de valeur pass en session on affiche le formulaire...
		{
		aff_logo("x");
		debut_cadre();
		echo "<br><TABLE><TR> <td> <img src=\"images/identification.png\"  width=\"50\" height=\"50\"  > </td> <td>";
		echo "<TABLE><TR> <td><form class=\"center\"  method=\"post\"> ".traduire('Identifiant').": </td><td><input required type=\"text\" name=\"id\" value=\"\"/></td>";
		echo "<TR> <td>".traduire('Mot de passe').": </td><td><input required  id=\"pwd2\"  type=\"password\" name=\"pass\"  autocomplete=\"off\" value=\"\"/>";
		echo "<td><input type=\"checkbox\" onchange=\"document.getElementById('pwd2').type = this.checked ? 'text' : 'password'\"> ".traduire('Voir saisie')."<td>";
		//echo "<input  type=\"hidden\" name=\"action\" value=\"\"/></td>";
		token("");
		echo "<TR> <td></td><td><input type=\"submit\" value=\"".traduire('Se connecter')."\"/><p></td>";
		echo "</form> </table> </table> ";
		fin_cadre();
		echo "<br>";
		$msg_tech=parametre("DD_msg_1ere_page");
		if ($msg_tech!="")
			echo $msg_tech;
		echo "<p><br><a href=\"index.php?".token_ref("dde_mdp")."\" > <img src=\"images/oubli.png\" width=\"35\" height=\"35\" > ".traduire('Si vous avez oublié votre mot de passe ou identifiant, cliquez ici.')." </a><p><p>";
		echo "<p><br></center></div>";
		pied_de_page();
		} 

?>