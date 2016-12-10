<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
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

include 'header.php';	  
include 'general.php';
 
 ?>
    <head>
	<title>ADILEOS</title>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
	</head>
	<style>
				.ombre {
	-webkit-box-shadow: 15px 10px 10px 0 #A6A6A6;
box-shadow: 15px 10px 10px 0 #A6A6A6;
	}
			.arrondie {
		-moz-border-radius:20px;
		-webkit-border-radius:20px;
		border-radius:20px;
		}
	</style>
	<body>

<center>
<a href="http://adileos.jimdo.com/"  target=_blank ><img src="images/adileos.jpg" width="700" height="90" > </a> 
<br>
Association de D�veloppement et d�Int�gration de Logiciels Economiques Orient�s Social
<center>
<TABLE>
<TR> <td  width="700"> </td>

<TR> <td  width="700"> <center>
<div class=\"CSS_titre\"  >

	<TABLE>
	<TR> 
	<td> 
	<?php echo "<a href=\"alerte.php\"> "; ?>
	
	<img src="images/logo-alerte.jpg" width="150" height="100" > </a> </td>
	<td width="500" class="ombre"> 
	<center>
	<b><font size="4">Alerte SMS M�t�o</b></font>
<br><br>
	Avertir par SMS les personnes vivant dans la rue des intemp�ries impactant leur vie

	</td>
	</TABLE>
<br>
	<TABLE>
	<TR> 
	<td> 
	<?php echo "<a href=\"index.php\"> "; ?>

	<img src="images/logo.png" width="150" height="100" > </a> </td>
	<td width="500" class="ombre"> <center>
	<b><font size="4">DOC-DEPOT : La Consigne Num�rique Solidaire </b></font>
<br><br>
	Sauvegardez gratuitement de fa�on s�curis�e vos documents, photos et informations essentielles .
	</td>	
	</TABLE>

		
	<?php	
	if (file_exists ( "webmail" )) 
		{
		echo "<br><TABLE><TR> <td> ";
		echo "<a href=\"wm.php\"> "; 
		echo "<img src=\"images/webmail.jpg\" width=\"150\" height=\"100\" > </a> </td>";
		echo "<td width=\"500\" class=\"ombre\"> <center> <b><font size=\"4\">WEBMAIL </b></font>";
		echo "<br><br> Acc�der � vos mails de fa�on tr�s simple.</td></TABLE>";
		}
	?>

<p>

	<TABLE>
	<TR> 

	<td bgcolor="lightgreen"  width="700" class="arrondie">  
		<center><b>Espace d�di� aux Acteurs sociaux</b></center>
		
		<TABLE>
		<TR> 
			<td width="200" > 
			<center>
			<?php echo "<a href=\"index.php\"> "; ?>
			<img src="images/logo.png" width="101" height="75" > 
			</a>
			</td > <td > 
		<b><font size="3">DOC-DEPOT </b>: cr�er des comptes et partager les documents</b></font> <br>
</td>
		 <TR> 
<td > <center>
	<?php echo "<a href=\"fissa.php\"> "; ?>
		<img src="images/fissa.jpg" width="101" height="75" > 
		</a>
					</td > <td > 

		<b><font size="3">FISSA </b>: Simplifiez le suivi des activit�s et des b�n�ficiaires  </font> <br>
</td>
		
		<TR> 
<td > <center>
<?php echo "<a href=\"suivi.php\"> "; ?>
		<img src="images/suivi.jpg" width="101" height="75" > 
	</a>
				</td > <td > 

		<b><font size="3">Suivi personnalis� </b>: Enrichissez le suivi des b�n�ficiaires   </font> <br>
</td>

		 <TR> 
<td > <center>
	<?php echo "<a href=\"rdv.php\"> "; ?>
		<img src="images/rdv.jpg" width="101" height="75" > 
		</a>
					</td > <td > 

		<b><font size="3">Rendez-vous SMS</b>: Rappel de rendez-vous par SMS </font> <br>
</td>

		</TABLE>
		
	</td>	
	</TABLE>





</td>



</TABLE>
<?php 

		echo "<hr><center> ";

		echo "<table> <tr> <td align=\"right\" valign=\"bottom\" ></td>";
		echo "<td><a id=\"lien_conditions\" href=\"conditions_portail.html\">".traduire('Mentions l�gales')."</a>";
		echo "- <a id=\"lien_contact\" href=\"http://adileos.jimdo.com/contact\">".traduire('Nous contacter')."</a>";
		echo "- Copyright <a href=\"http://adileos.doc-depot.com\"  target=_blank >ADILEOS</a> ";
		$version= parametre("DD_version_portail") ;
		echo "- $version ";	
		echo "- <a href=\"http://adileos.jimdo.com/contact\">".traduire('Signaler un bug ou demander une �volution').".</a> </td> </table>";


 ?>

	</body>