<?php 

			include "../connex_inc.php";


$SQL_C = "SELECT * FROM r_user "; 
$result_C = mysql_query($SQL_C)or die(mysql_error()); 

$str=""; 
$i=0; 
//Creation du menu déroulant 
while ($val_C = mysql_fetch_array($result_C,MYSQL_ASSOC)) 
{ 
 foreach($val_C as $key => $value){ 
  if($key!="id"){ 
   $values[$val_C['idx']][$key]=$value; 
//tableau $values 
//id unique : premiere dimension du tableau l'id de la commande 
//deuxième : les élément de la base de donnée correspondant à l'id 
//On crée le tableau qui ressemblera à : 
// $values['1']['type'] 
//$values[1]['description'] 
//$values[1]['etat'] 
//... 
  } 
 } 
//Contient les option du menu déroulant qu'on affichera plus loin 
 $str.="<option value='".$val_C['id']."'>".$val_C['id']."</option>"; 
} 


//Création des div contenant les éléments de la base de donnée 
$str_div=""; 
foreach($values as $key => $value){ 
  
 //On cache toutes les div 
 //La fonction affiche() fera apparaitre la div correspondante au click 
 $str_div.="<div id='".$key."' style='display:none'>"; 
 $str_div.="<ul>"; 
 foreach($value as $key_value => $rslt){ 
  $str_div.="<li>".$key_value." : ".$rslt."</li>"; 
 } 
 $str_div.="</ul>"; 
 $str_div.="</div>"; 
} 

?> 
<!-- Page HTML --> 
<p>Choisissez un appel d'offre: </p> 
<!-- événement onchange de la balise select définit sur la fonction affiche() --> 
<select id='select' name='select' onchange='affiche()'> 
<option value='0'>-----choisir-----</option> 
<?php 
 //$str contient la chaine de caractère html des différentes options du menu déroulant 
 echo $str; 
?> 
</select> 
<?php 
 //$str_div contient la chaine de caractère html des différentes div 
 echo $str_div; 
?> 

<script type="text/javascript"> 
var last_div=""; 
function affiche(){ 
 var id=document.getElementById("select").value; 
 //Si un div est déjà affichée on l'a cache 
 if(last_div!=""){ 
  document.getElementById(last_div).style.display="none"; 
 } 
 last_div=id; 
//On affiche le contenu 
 document.getElementById(id).style.display="block"; 
} 
</script> 